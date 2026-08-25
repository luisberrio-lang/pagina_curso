<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('payments')) {
            return;
        }

        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->restrictOnDelete();
            $table->string('provider', 30)->default('izipay');
            $table->string('payment_number', 40)->unique();
            $table->string('public_token', 64)->unique();
            $table->string('provider_transaction_id', 40)->unique();
            $table->string('provider_reference', 100)->nullable()->index();
            $table->string('provider_code', 20)->nullable();
            $table->unsignedSmallInteger('attempt');
            $table->decimal('base_amount', 10, 2);
            $table->char('base_currency', 3);
            $table->string('status', 20)->default('pending')->index();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('failed_at')->nullable();
            $table->timestamps();
            $table->unique(['order_id', 'attempt']);
            $table->index(['order_id', 'status']);
        });
    }

    public function down(): void
    {
        // No destructiva: los intentos de pago forman parte del historial comercial.
    }
};
