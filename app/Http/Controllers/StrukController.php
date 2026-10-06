<?php

namespace App\Http\Controllers;

use App\Contracts\ReceiptPrinter;
use App\Models\Sale;
use App\Models\StoreSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Throwable;

class StrukController extends Controller
{
    public function printReceipt(
        Request $request,
        Sale $sale,
        ReceiptPrinter $receiptPrinter,
    ): RedirectResponse {
        $sale = Sale::query()
            ->where('cashier_id', $request->user()->id)
            ->with('items')
            ->findOrFail($sale->id);

        try {
            $receiptPrinter->print($sale, StoreSetting::current());
        } catch (Throwable $exception) {
            Log::error('Gagal mencetak struk transaksi.', [
                'sale_id' => $sale->id,
                'exception' => $exception,
            ]);

            if ($request->wantsJson()) {
                return response()->json([
                    'message' => 'Struk gagal dicetak. Periksa printer dan konfigurasi nama printer.',
                ], 500);
            }

            return back()->withErrors([
                'receipt' => 'Struk gagal dicetak. Periksa printer dan konfigurasi nama printer.',
            ]);
        }

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Struk transaksi '.$sale->invoice.' berhasil dicetak.',
            ]);
        }

        return back()->with('status', 'Struk transaksi '.$sale->invoice.' berhasil dicetak.');
    }
}
