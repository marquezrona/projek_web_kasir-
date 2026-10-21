<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    protected $table = 'buku';

    protected $fillable = [
        'judul',
        'deskripsi',
        'status',
    ];
}
