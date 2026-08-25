<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('orders')) {
            return;
        }

        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number', 32)->unique();
            $table->string('public_token', 64)->unique();
            $table->char('checkout_token_hash', 64)->unique();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('first_name', 100);
            $table->string('last_name', 100);
            $table->string('email');
            $table->string('phone', 30);
            $table->string('document_type', 20)->nullable();
            $table->string('document_number', 30)->nullable();
            $table->decimal('subtotal', 10, 2);
            $table->decimal('total', 10, 2);
            $table->char('currency', 3)->default('PEN');
            $table->string('status', 20)->default('pending')->index();
            $table->string('payment_status', 20)->default('pending')->index();
            $table->timestamps();
            $table->index(['email', 'created_at']);
        });
    }

    public function down(): void
    {
        // Intencionalmente no destructiva para preservar órdenes históricas.
    }
};
