<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Area extends Model
{
    protected $fillable = [
        'name','slug','description','sort_order','is_default'
    ];

    protected $casts = [
        'sort_order' => 'integer',
        'is_default' => 'boolean',
    ];

    public function courses(): HasMany
    {
        return $this->hasMany(Course::class);
    }

    // Para rutas públicas por slug (programas/cursos/area/{area:slug})
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    public static function defaultArea(): ?self
    {
        return self::query()
            ->where('is_default', true)
            ->ordered()
            ->first()
            ?? self::query()->ordered()->first();
    }
}
