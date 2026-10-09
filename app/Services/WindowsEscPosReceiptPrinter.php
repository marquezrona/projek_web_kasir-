<?php

namespace App\Services;

use App\Contracts\ReceiptPrinter;
use App\Models\Sale;
use App\Models\StoreSetting;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\Printer;

class WindowsEscPosReceiptPrinter implements ReceiptPrinter
{
    public function print(Sale $sale, StoreSetting $store): void
    {
        $printerName = config('services.receipt.printer');

        $printer = new Printer(
            new WindowsPrintConnector($printerName)
        );

        // =========================
        // HEADER
        // =========================
        $printer->setJustification(Printer::JUSTIFY_CENTER);

        // Nama toko lebih besar dan tebal

        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->setEmphasis(true);
        $printer->setTextSize(1, 1); // Ukuran normal
        $printer->text($store->store_name . "\n");

        $printer->setEmphasis(false);
        $printer->text($store->store_address . "\n");
        $printer->text(str_repeat('-', 32) . "\n");
        // =========================
        // INFORMASI TRANSAKSI
        // =========================
        $printer->setJustification(Printer::JUSTIFY_LEFT);

        $printer->text(sprintf(
            "%-15s %15s\n",
            "No. Transaksi :",
            $sale->invoice
        ));

        $printer->text(
            sprintf(
                "%-15s %15s\n",
                "Tanggal       :",
                $sale->created_at->format('d-m-Y H:i')
            )
        );

        $printer->text(
            sprintf(
                "%-15s %15s\n",
                "Pelanggan     :",
                $sale->customer_type
            )
        );

        $printer->text(str_repeat('-', 42) . "\n");


        // =========================
        // DAFTAR BARANG
        // =========================
        foreach ($sale->items as $item) {

            $printer->text($item->product_name . "\n");

            $harga = number_format(
                (float) $item->price,
                0,
                ',',
                '.'
            );

            $subtotal = number_format(
                (float) $item->subtotal,
                0,
                ',',
                '.'
            );

            $kiri = $item->quantity . " x Rp " . $harga;
            $kanan = "Rp " . $subtotal;

            $spasi = max(1, 41 - strlen($kiri) - strlen($kanan));

            $printer->text(
                $kiri . str_repeat(" ", $spasi) . $kanan . "\n"
            );
        }


        // =========================
        // TOTAL
        // =========================
        $printer->text(str_repeat('-', 42) . "\n");
        $printSummary = function (string $label, float $amount) use ($printer) {

            $label = str_pad($label, 32, ' ', STR_PAD_RIGHT);

            $printer->text(
                $label .
                    'Rp ' .
                    number_format($amount, 0, ',', '.') .
                    "\n"
            );
        };

        $printSummary('Subtotal', (float) $sale->subtotal);

        if ((float) $sale->discount_amount > 0) {
            $printSummary('Diskon', (float) $sale->discount_amount);
        }

        if ((float) $sale->tax > 0) {
            $printSummary('Pajak', (float) $sale->tax);
        }

        if ((float) $sale->other_fee > 0) {
            $printSummary('Biaya lain', (float) $sale->other_fee);
        }

        $printer->setEmphasis(true);
        $printSummary('TOTAL', (float) $sale->total);
        $printer->setEmphasis(false);

        $printSummary('Bayar', (float) $sale->paid);
        $printSummary('Kembali', (float) $sale->change);

        // =========================
        // FOOTER
        // =========================
        $printer->text(str_repeat('-', 42) . "\n");

        $printer->setJustification(Printer::JUSTIFY_CENTER);

        $printer->text("Terima kasih\n");
        $printer->text("Atas kunjungan Anda\n");

        $printer->feed(1);

        $printer->cut();
        $printer->close();
    }
}
