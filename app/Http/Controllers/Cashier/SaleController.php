<?php

namespace App\Http\Controllers\Cashier;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\NfcCard;
use App\Models\NfcCardTransaction;
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
    public function nfcBalance(Request $request): JsonResponse
    {
        $this->normalizeCardUid($request);
        $data = $request->validate([
            'card_uid' => ['required', 'string', 'max:100', 'regex:/^[A-Z0-9:_-]+$/'],
        ]);

        $card = NfcCard::query()->where('uid', $data['card_uid'])->first();

        return response()->json([
            'card_uid' => $data['card_uid'],
            'registered' => $card?->customer !== null,
            'balance' => $card?->balance ?? '0.00',
            'member_name' => $card?->customer?->name,
            'member_id' => $card?->customer_id,
        ]);
    }

    public function index(Request $request)
    {
        $validated = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'period' => ['nullable', 'in:all,day,week,month,year'],
            'date' => ['nullable', 'date_format:Y-m-d'],
            'month' => ['nullable', 'integer', 'between:1,12'],
            'year' => ['nullable', 'integer', 'between:1900,9999'],
        ]);
        $search = trim($validated['search'] ?? '');
        $period = $validated['period'] ?? 'all';
        $date = $validated['date'] ?? '';
        $month = $validated['month'] ?? '';
        $year = $validated['year'] ?? '';
        $hasCalendarFilter = $date !== '' || $month !== '' || $year !== '';

        $sales = Sale::query()
            ->with(['items', 'customer'])
            ->where('cashier_id', $request->user()->id)
            ->when($period !== 'all' && ! $hasCalendarFilter, function ($query) use ($period) {
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
            ->when($date !== '', fn ($query) => $query->whereDate('created_at', $date))
            ->when($month !== '', fn ($query) => $query->whereMonth('created_at', $month))
            ->when($year !== '', fn ($query) => $query->whereYear('created_at', $year))
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

        return view('cashier.riwayat', compact('sales', 'search', 'period', 'date', 'month', 'year'));
    }

    public function store(Request $request): JsonResponse
    {
        $this->normalizeCardUid($request);
        $data = $request->validate([
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'integer', 'distinct', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'discount_percent' => ['required', 'numeric', 'min:0', 'max:100'],
            'discount_amount' => ['required', 'numeric', 'min:0'],
            'tax' => ['required', 'numeric', 'min:0'],
            'other_fee' => ['required', 'numeric', 'min:0'],
            'paid' => ['required', 'numeric', 'min:0'],
            'payment_method' => ['required', 'in:tunai,qris,transfer,nfc'],
            'card_uid' => ['required_if:payment_method,nfc', 'nullable', 'string', 'max:100', 'regex:/^[A-Z0-9:_-]+$/'],
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
            $nfcCard = null;
            $member = null;
            $paid = (float) $data['paid'];

            if ($data['payment_method'] === 'nfc') {
                $nfcCard = NfcCard::query()
                    ->where('uid', $data['card_uid'])
                    ->lockForUpdate()
                    ->first();

                if (! $nfcCard) {
                    throw ValidationException::withMessages([
                        'card_uid' => 'Kartu NFC belum terdaftar. Isi saldo kartu terlebih dahulu.',
                    ]);
                }

                if (! $nfcCard->customer_id) {
                    throw ValidationException::withMessages([
                        'card_uid' => 'Kartu NFC belum terhubung ke pelanggan. Daftarkan pelanggan melalui menu Pelanggan NFC.',
                    ]);
                }

                $cardMember = Customer::query()->findOrFail($nfcCard->customer_id);

                $member = $cardMember;

                if ((float) $nfcCard->balance < $total) {
                    throw ValidationException::withMessages([
                        'card_uid' => 'Saldo kartu NFC tidak mencukupi.',
                    ]);
                }

                $paid = $total;
            }

            if ($data['payment_method'] !== 'nfc' && $paid < $total) {
                throw ValidationException::withMessages([
                    'paid' => 'Uang pembayaran masih kurang dari total transaksi.',
                ]);
            }

            $sale = Sale::query()->create([
                'invoice' => 'INV-'.now()->format('Ymd').'-'.Str::upper(Str::random(8)),
                'cashier_id' => $request->user()->id,
                'customer_id' => $member?->id,
                'cashier_name' => $request->user()->name,
                'customer_type' => $member ? 'Member' : 'Umum',
                'customer_contact' => $member?->phone,
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

            if ($nfcCard) {
                $nfcCard->decrement('balance', $total);
                NfcCardTransaction::query()->create([
                    'nfc_card_id' => $nfcCard->id,
                    'cashier_id' => $request->user()->id,
                    'sale_id' => $sale->id,
                    'type' => 'purchase',
                    'amount' => $total,
                    'payment_method' => 'nfc',
                ]);
            }

            return $sale;
        });

        $cardBalance = $data['payment_method'] === 'nfc'
            ? NfcCard::query()->where('uid', $data['card_uid'])->value('balance')
            : null;

        return response()->json([
            'message' => 'Transaksi berhasil disimpan.',
            'sale_id' => $sale->id,
            'invoice' => $sale->invoice,
            'total' => $sale->total,
            'change' => $sale->change,
            'card_balance' => $cardBalance,
            'history_url' => route('cashier.riwayat'),
        ], 201);
    }

    private function normalizeCardUid(Request $request): void
    {
        $uid = (string) $request->input('card_uid', '');
        $uid = strtoupper((string) preg_replace('/\s+/', '', trim($uid)));

        $request->merge(['card_uid' => $uid]);
    }
}
