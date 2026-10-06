<?php

namespace App\Http\Controllers\Cashier;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Sale;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class SaleController extends Controller
{
    public function index(Request $request)
    {
        $validated = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'period' => ['nullable', 'in:all,day,week,month,year'],
        ]);
        $search = trim($validated['search'] ?? '');
        $period = $validated['period'] ?? 'all';

        $sales = Sale::query()
            ->with('items')
            ->where('cashier_id', $request->user()->id)
            ->when($period !== 'all', function ($query) use ($period) {
                $now = now();
                $start = match ($period) {
                    'day' => $now->copy()->startOfDay(),
                    'week' => $now->copy()->startOfWeek(Carbon::MONDAY),
                    'month' => $now->copy()->startOfMonth(),
                    'year' => $now->copy()->startOfYear(),
                };
                $end = match ($period) {
                    'day' => $now->copy()->endOfDay(),
                    'week' => $now->copy()->endOfWeek(Carbon::SUNDAY),
                    'month' => $now->copy()->endOfMonth(),
                    'year' => $now->copy()->endOfYear(),
                };

                $query->whereBetween('created_at', [$start, $end]);
            })
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('invoice', 'like', "%{$search}%")
                        ->orWhere('customer_type', 'like', "%{$search}%")
                        ->orWhere('customer_contact', 'like', "%{$search}%")
                        ->orWhere('payment_method', 'like', "%{$search}%")
                        ->orWhereHas('items', function ($query) use ($search) {
                            $query->where('product_name', 'like', "%{$search}%");
                        });
                });
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('cashier.riwayat', compact('sales', 'search', 'period'));
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'integer', 'distinct', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'customer_type' => ['required', 'in:Umum,Pelanggan Member,Member VIP'],
            'customer_contact' => ['nullable', 'string', 'max:50'],
            'discount_percent' => ['required', 'numeric', 'min:0', 'max:100'],
            'discount_amount' => ['required', 'numeric', 'min:0'],
            'tax' => ['required', 'numeric', 'min:0'],
            'other_fee' => ['required', 'numeric', 'min:0'],
            'paid' => ['required', 'numeric', 'min:0'],
            'payment_method' => ['required', 'in:tunai,qris,transfer'],
        ]);

        $sale = DB::transaction(function () use ($data, $request): Sale {
            $quantities = collect($data['items'])->keyBy('product_id');
            $products = Product::query()
                ->whereIn('id', $quantities->keys())
                ->where('is_active', true)
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            if ($products->count() !== $quantities->count()) {
                throw ValidationException::withMessages([
                    'items' => 'Satu atau lebih barang sudah tidak tersedia.',
                ]);
            }

            $lineItems = [];
            $subtotal = 0;

            foreach ($quantities as $productId => $item) {
                $product = $products->get($productId);
                $quantity = (int) $item['quantity'];

                if ($product->stock < $quantity) {
                    throw ValidationException::withMessages([
                        'items' => "Stok {$product->name} tidak mencukupi.",
                    ]);
                }

                $price = (float) $product->price;
                $lineSubtotal = round($price * $quantity, 2);
                $subtotal += $lineSubtotal;
                $lineItems[] = [
                    'product' => $product,
                    'quantity' => $quantity,
                    'price' => $price,
                    'subtotal' => $lineSubtotal,
                ];
            }

            $discountPercent = (float) $data['discount_percent'];
            $discountAmount = round($subtotal * $discountPercent / 100, 2)
                + (float) $data['discount_amount'];

            if ($discountAmount > $subtotal) {
                throw ValidationException::withMessages([
                    'discount_amount' => 'Total diskon tidak boleh melebihi subtotal.',
                ]);
            }

            $tax = (float) $data['tax'];
            $otherFee = (float) $data['other_fee'];
            $total = round($subtotal - $discountAmount + $tax + $otherFee, 2);
            $paid = (float) $data['paid'];

            if ($paid < $total) {
                throw ValidationException::withMessages([
                    'paid' => 'Uang pembayaran masih kurang dari total transaksi.',
                ]);
            }

            $sale = Sale::query()->create([
                'invoice' => 'INV-'.now()->format('Ymd').'-'.Str::upper(Str::random(8)),
                'cashier_id' => $request->user()->id,
                'cashier_name' => $request->user()->name,
                'customer_type' => $data['customer_type'],
                'customer_contact' => $data['customer_contact'] ?? null,
                'subtotal' => $subtotal,
                'discount_percent' => $discountPercent,
                'discount_amount' => $discountAmount,
                'tax' => $tax,
                'other_fee' => $otherFee,
                'total' => $total,
                'paid' => $paid,
                'change' => round($paid - $total, 2),
                'payment_method' => $data['payment_method'],
            ]);

            foreach ($lineItems as $lineItem) {
                $product = $lineItem['product'];

                $sale->items()->create([
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'price' => $lineItem['price'],
                    'quantity' => $lineItem['quantity'],
                    'subtotal' => $lineItem['subtotal'],
                ]);

                $product->decrement('stock', $lineItem['quantity']);
            }

            return $sale;
        });

return response()->json([
    'message' => 'Transaksi berhasil disimpan.',
    'sale_id' => $sale->id,
    'invoice' => $sale->invoice,
    'total' => $sale->total,
    'change' => $sale->change,
    'history_url' => route('cashier.riwayat'),
], 201); 
    }
}
