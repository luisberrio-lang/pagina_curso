<?php

namespace Database\Seeders;

use App\Models\Area;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
  public function run(): void
  {
    // Admin por ENV (si existe)
    if (env('ADMIN_EMAIL') && env('ADMIN_PASSWORD')) {
      User::firstOrCreate(
        ['email' => env('ADMIN_EMAIL')],
        [
          'name' => env('ADMIN_NAME', 'Administrador'),
          'password' => Hash::make(env('ADMIN_PASSWORD')),
          'is_admin' => true,
        ]
      );
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
