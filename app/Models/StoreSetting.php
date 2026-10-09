<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class StoreSetting extends Model
{
    protected $fillable = [
        'store_name',
        'store_address',
        'logo_path',
    ];

    public static function current(): self
    {
        return static::query()->firstOrCreate(
            ['id' => 1],
            [
                'store_name' => 'Toko Sembako Jazzel',
                'store_address' => 'Sabu Raijua - Nusa Tenggara Timur',
            ],
        );
    }

    public function getLogoUrlAttribute(): string
    {
        return $this->logo_path
            ? Storage::disk('public')->url($this->logo_path)
            : asset('assets/img/jazzel-monogram.png');
    }
}
