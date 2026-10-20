<?php

namespace App\Http\Controllers\Cashier;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\NfcCard;
use App\Models\NfcCardTransaction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class NfcCustomerController extends Controller
{
    public function index(): View
    {
        $customers = Customer::query()
            ->with('nfcCard')
            ->latest()
            ->paginate(20);

        return view('cashier.pelanggan', compact('customers'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->merge([
            'card_uid' => strtoupper((string) preg_replace('/\s+/', '', trim((string) $request->input('card_uid', '')))),
        ]);
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:30'],
            'card_uid' => ['required', 'string', 'max:100', 'regex:/^[A-Z0-9:_-]+$/', 'unique:nfc_cards,uid'],
        ]);
        DB::transaction(function () use ($data): void {
            $customer = Customer::query()->create([
                'name' => trim($data['name']),
                'phone' => $data['phone'] ? trim($data['phone']) : null,
            ]);

            $customer->nfcCard()->create([
                'uid' => $data['card_uid'],
                'balance' => 0,
            ]);
        });

        return to_route('cashier.pelanggan')->with('status', 'Pelanggan dan kartu NFC berhasil ditambahkan.');
    }

    public function topUp(Request $request, Customer $customer): RedirectResponse
    {
        $data = $request->validate([
            'amount' => ['required', 'numeric', 'min:1', 'max:9999999999.99'],
            'payment_method' => ['required', 'in:tunai,qris,transfer'],
        ]);

        DB::transaction(function () use ($data, $request, $customer): void {
            $card = NfcCard::query()
                ->where('customer_id', $customer->id)
                ->lockForUpdate()
                ->first();

            if (! $card) {
                throw ValidationException::withMessages([
                    'customer' => 'Pelanggan ini belum memiliki kartu NFC.',
                ]);
            }

            $amount = round((float) $data['amount'], 2);
            if ((float) $card->balance + $amount > 9999999999.99) {
                throw ValidationException::withMessages([
                    'amount' => 'Saldo kartu melebihi batas maksimum.',
                ]);
            }

            $card->increment('balance', $amount);

            NfcCardTransaction::query()->create([
                'nfc_card_id' => $card->id,
                'cashier_id' => $request->user()->id,
                'type' => 'top_up',
                'amount' => $amount,
                'payment_method' => $data['payment_method'],
            ]);
        });

        return to_route('cashier.pelanggan')->with('status', 'Saldo kartu '.$customer->name.' berhasil ditambahkan.');
    }
}
