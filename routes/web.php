<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Site\HomeController;
use App\Http\Controllers\Site\CatalogController;
use App\Http\Controllers\Site\PriceController;
use App\Http\Controllers\Site\FaqController;

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\AreaController;
use App\Http\Controllers\Admin\CourseController;
use App\Http\Controllers\Admin\CourseImageController;

// Sitio público
Route::get('/', [HomeController::class, 'index'])->name('home');

Route::prefix('programas')->group(function () {
  Route::get('cursos', [CatalogController::class, 'index'])->name('courses.index');
  Route::get('cursos/area/{area:slug}', [CatalogController::class, 'index'])->name('courses.byArea');
  Route::get('cursos/curso/{course:slug}', [CatalogController::class, 'show'])->name('courses.show');
});

Route::get('/precio', [PriceController::class, 'index'])->name('price');
Route::get('/faq', [FaqController::class, 'index'])->name('faq');

/**
 * ✅ Ruta que Breeze espera: /dashboard
 * Admin => admin.dashboard
 * Cliente => home
 */
Route::middleware('auth')->get('/dashboard', function () {
  return auth()->user()?->is_admin
    ? redirect()->route('admin.dashboard')
    : redirect()->route('home');
})->name('dashboard');

/**
 * ✅ Admin dashboard real
 */
Route::middleware(['auth','admin'])
  ->prefix('admin/dashboard')
  ->name('admin.')
  ->group(function () {

    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::post('/areas/{area}/default', [AreaController::class, 'setDefault'])->name('areas.default');
    Route::resource('areas', AreaController::class)->except(['show']);

    Route::resource('courses', CourseController::class)->except(['show']);

    Route::post('courses/{course}/images', [CourseImageController::class, 'store'])->name('courses.images.store');
    Route::delete('courses/{course}/images/{image}', [CourseImageController::class, 'destroy'])->name('courses.images.destroy');
    Route::post('courses/{course}/images/{image}/up', [CourseImageController::class, 'moveUp'])->name('courses.images.up');
    Route::post('courses/{course}/images/{image}/down', [CourseImageController::class, 'moveDown'])->name('courses.images.down');
  });

require __DIR__.'/auth.php';
