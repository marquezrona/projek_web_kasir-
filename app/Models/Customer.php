<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Customer extends Model
{
    protected $fillable = [
        'name',
        'phone',
    ];

    public function nfcCard(): HasOne
    {
        return $this->hasOne(NfcCard::class);
    }
}
