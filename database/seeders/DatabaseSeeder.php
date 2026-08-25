<?php

namespace Database\Seeders;

use App\Models\Area;
use App\Services\AdminEnvironmentSynchronizer;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
  public function run(): void
  {
    // Admin por ENV (si existe)
    if (config('admin.name') && config('admin.email') && config('admin.password')) {
      app(AdminEnvironmentSynchronizer::class)->sync();
    }

    // Áreas base (si no hay)
    if (Area::count() === 0) {
      Area::create([
        'name' => 'Ingeniería Eléctrica',
        'slug' => 'ingenieria-electrica',
        'description' => 'Programas y cursos orientados a electricidad, potencia, diseño y software.',
        'sort_order' => 0,
        'is_default' => true,
      ]);
      Area::create([
        'name' => 'Ingeniería Civil',
        'slug' => 'ingenieria-civil',
        'description' => 'Cursos por especialidad civil, herramientas y materiales.',
        'sort_order' => 1,
      ]);
      Area::create([
        'name' => 'Arquitectura',
        'slug' => 'arquitectura',
        'description' => 'Diseño, modelado, flujos BIM y recursos.',
        'sort_order' => 2,
      ]);
    }
  }
}
