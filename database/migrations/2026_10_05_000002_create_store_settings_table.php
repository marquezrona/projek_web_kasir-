<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('store_settings', function (Blueprint $table) {
            $table->id();
            $table->string('store_name', 150);
            $table->string('store_address')->default('');
            $table->string('logo_path')->nullable();
            $table->timestamps();
        });

        DB::table('store_settings')->insert([
            'id' => 1,
            'store_name' => 'Toko Sembako Jazzel',
            'store_address' => 'Sabu Raijua - Nusa Tenggara Timur',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('store_settings');
    }
};
