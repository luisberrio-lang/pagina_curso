<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void {
    if (Schema::hasTable('courses') && !Schema::hasColumn('courses', 'price_previous')) {
      Schema::table('courses', function (Blueprint $table) {
        $table->decimal('price_previous', 10, 2)->nullable()->after('price_anual');
      });
    }
  }

  public function down(): void {
    if (Schema::hasTable('courses') && Schema::hasColumn('courses', 'price_previous')) {
      Schema::table('courses', function (Blueprint $table) {
        $table->dropColumn('price_previous');
      });
    }
  }
};
