<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('payment_links')) {
            return;
        }

        Schema::create('payment_links', function (Blueprint $table) {
            $table->id();
            $table->string('reference', 32)->unique();
            $table->char('token_hash', 64)->unique();
            $table->text('token_ciphertext');
            $table->string('customer_name', 150);
            $table->string('customer_email');
            $table->string('customer_phone', 30)->nullable();
            $table->string('concept');
            $table->decimal('amount', 10, 2);
            $table->char('currency', 3)->default('PEN');
            $table->string('status', 20)->default('pending')->index();
            $table->timestamp('expires_at')->nullable()->index();
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->text('internal_note')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        // No destructiva: los enlaces forman parte del historial comercial.
    }
};
