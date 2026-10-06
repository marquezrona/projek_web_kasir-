<?php

namespace App\Contracts;

use App\Models\Sale;
use App\Models\StoreSetting;

interface ReceiptPrinter
{
    public function print(Sale $sale, StoreSetting $store): void;
}
