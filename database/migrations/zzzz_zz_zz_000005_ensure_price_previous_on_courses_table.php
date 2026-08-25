<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('courses') || Schema::hasColumn('courses', 'price_previous')) {
            return;
        }

        $hasAnnualPrice = Schema::hasColumn('courses', 'price_anual');

        Schema::table('courses', function (Blueprint $table) use ($hasAnnualPrice) {
            $column = $table->decimal('price_previous', 10, 2)->nullable();

            if ($hasAnnualPrice) {
                $column->after('price_anual');
            }
        });
    }

    public function down(): void
    {
        // Intencionalmente no destructiva: preserva precios existentes.
    }
};
