<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('order_items')) {
            return;
        }

        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->restrictOnDelete();
            $table->foreignId('course_id')->nullable()->constrained()->nullOnDelete();
            $table->string('course_title');
            $table->string('course_slug');
            $table->decimal('unit_price', 10, 2);
            $table->char('currency', 3)->default('PEN');
            $table->unsignedSmallInteger('quantity')->default(1);
            $table->decimal('line_total', 10, 2);
            $table->timestamps();
            $table->unique(['order_id', 'course_id']);
        });
    }

    public function down(): void
    {
        // Intencionalmente no destructiva para preservar snapshots históricos.
    }
};
