<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nfc_cards', function (Blueprint $table) {
            $table->id();
            $table->string('uid', 100)->unique();
            $table->decimal('balance', 12, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('nfc_card_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('nfc_card_id')->constrained()->cascadeOnDelete();
            $table->foreignId('cashier_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('sale_id')->nullable()->constrained()->nullOnDelete();
            $table->string('type', 20);
            $table->decimal('amount', 12, 2);
            $table->string('payment_method', 30)->nullable();
            $table->timestamps();

            $table->index(['nfc_card_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nfc_card_transactions');
        Schema::dropIfExists('nfc_cards');
    }
};
