<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('courses') && !Schema::hasColumn('courses', 'price_one_time')) {
            Schema::table('courses', function (Blueprint $table) {
                $table->decimal('price_one_time', 10, 2)->nullable()->after('price_anual');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('courses') && Schema::hasColumn('courses', 'price_one_time')) {
            Schema::table('courses', function (Blueprint $table) {
                $table->dropColumn('price_one_time');
            });
        }
    }
};
