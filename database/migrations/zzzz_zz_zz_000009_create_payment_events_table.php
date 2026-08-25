<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('payment_events')) {
            return;
        }

        Schema::create('payment_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_id')->constrained()->restrictOnDelete();
            $table->string('provider', 30)->default('izipay');
            $table->char('event_key', 64)->unique();
            $table->char('payload_hash', 64);
            $table->string('provider_code', 20)->nullable();
            $table->string('processing_status', 20)->index();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
            $table->index(['payment_id', 'created_at']);
        });
    }

    public function down(): void
    {
        // No destructiva: los eventos sustentan idempotencia y auditoría.
    }
};
