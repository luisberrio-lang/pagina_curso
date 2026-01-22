<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void {
    Schema::create('courses', function (Blueprint $table) {
      $table->id();
      $table->foreignId('area_id')->constrained()->cascadeOnDelete();

      $table->string('title');
      $table->string('slug')->unique();
      $table->string('short_desc', 255)->nullable();

      $table->string('cover_path')->nullable();

      $table->boolean('is_published')->default(false);
      $table->boolean('is_featured')->default(false);
      $table->integer('sort_order')->default(0);

      // Contenido
      $table->longText('description')->nullable();
      $table->longText('audience')->nullable(); // para quién es

      $table->json('learning')->nullable();     // qué aprenderás
      $table->json('benefits')->nullable();
      $table->json('includes')->nullable();     // extras
      $table->json('requirements')->nullable();

      // Temario: [{"title":"Módulo 1","topics":["..",".."]}, ...]
      $table->json('syllabus')->nullable();

      // Precios (SOLO se muestran en Programas/Cursos)
      $table->decimal('price_monthly', 10, 2)->nullable();
      $table->decimal('price_bimestral', 10, 2)->nullable();
      $table->decimal('price_trimestral', 10, 2)->nullable();
      $table->decimal('price_semestral', 10, 2)->nullable();
      $table->decimal('price_anual', 10, 2)->nullable();
      $table->decimal('price_one_time', 10, 2)->nullable();

      // WhatsApp (opcional override; si está vacío se genera automático)
      $table->text('whatsapp_message')->nullable();

      $table->timestamps();
    });
  }

  public function down(): void {
    Schema::dropIfExists('courses');
  }
};
