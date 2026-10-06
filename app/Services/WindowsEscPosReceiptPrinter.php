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
        $printer->setEmphasis(true);

        $printer->text($store->store_name . "\n");

        $printer->setEmphasis(false);
        $printer->text($store->store_address . "\n");

        $printer->text(str_repeat('-', 32) . "\n");


        // =========================
        // INFORMASI TRANSAKSI
        // =========================
        $printer->setJustification(Printer::JUSTIFY_LEFT);

        $printer->text("No. Transaksi : " . $sale->invoice . "\n");
        $printer->text(
            "Tanggal       : " .
            $sale->created_at->format('d-m-Y H:i') .
            "\n"
        );

        $printer->text(
            "Pelanggan     : " .
            $sale->customer_type .
            "\n"
        );

        $printer->text(str_repeat('-', 32) . "\n");


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

            $printer->text(
                $item->quantity .
                " x Rp " .
                $harga .
                "     Rp " .
                $subtotal .
                "\n"
            );
        }


        // =========================
        // TOTAL
        // =========================
        $printer->text(str_repeat('-', 32) . "\n");

        $printer->text(
            "Subtotal      Rp " .
            number_format(
                (float) $sale->subtotal,
                0,
                ',',
                '.'
            ) .
            "\n"
        );

        if ((float) $sale->discount_amount > 0) {
            $printer->text(
                "Diskon         Rp " .
                number_format(
                    (float) $sale->discount_amount,
                    0,
                    ',',
                    '.'
                ) .
                "\n"
            );
        }

        if ((float) $sale->tax > 0) {
            $printer->text(
                "Pajak          Rp " .
                number_format(
                    (float) $sale->tax,
                    0,
                    ',',
                    '.'
                ) .
                "\n"
            );
        }

        if ((float) $sale->other_fee > 0) {
            $printer->text(
                "Biaya lain     Rp " .
                number_format(
                    (float) $sale->other_fee,
                    0,
                    ',',
                    '.'
                ) .
                "\n"
            );
        }

        $printer->setEmphasis(true);

        $printer->text(
            "TOTAL          Rp " .
            number_format(
                (float) $sale->total,
                0,
                ',',
                '.'
            ) .
            "\n"
        );

        $printer->setEmphasis(false);

        $printer->text(
            "Bayar          Rp " .
            number_format(
                (float) $sale->paid,
                0,
                ',',
                '.'
            ) .
            "\n"
        );

        $printer->text(
            "Kembali        Rp " .
            number_format(
                (float) $sale->change,
                0,
                ',',
                '.'
            ) .
            "\n"
        );


        // =========================
        // FOOTER
        // =========================
        $printer->text(str_repeat('-', 32) . "\n");

        $printer->setJustification(Printer::JUSTIFY_CENTER);

        $printer->text("Terima kasih\n");
        $printer->text("Atas kunjungan Anda\n");

        $printer->feed(2);

        $printer->cut();
        $printer->close();
    }
}