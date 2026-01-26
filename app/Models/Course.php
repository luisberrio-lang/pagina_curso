<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Course extends Model
{
  protected $fillable = [
    'area_id','title','slug','short_desc','cover_path',
    'is_published','is_featured','sort_order',
    'description','audience','learning','benefits','includes','requirements','syllabus',
    // ✅ Usaremos SOLO este como pago único:
    'price_anual',
    'price_previous',
    'whatsapp_message'
  ];

  protected $casts = [
    'is_published' => 'boolean',
    'is_featured'  => 'boolean',
    'sort_order'   => 'integer',

    'learning'     => 'array',
    'benefits'     => 'array',
    'includes'     => 'array',
    'requirements' => 'array',
    'syllabus'     => 'array',

    'price_anual'  => 'decimal:2',
    'price_previous' => 'decimal:2',
  ];

  protected static function booted(): void
  {
    static::saving(function (self $course) {
      if (!$course->slug) $course->slug = Str::slug($course->title);
    });
  }

  public function area(): BelongsTo
  {
    return $this->belongsTo(Area::class);
  }

  public function images(): HasMany
  {
    return $this->hasMany(CourseImage::class)->orderBy('sort_order')->orderBy('id');
  }

  public function coverUrl(): ?string
  {
    return $this->cover_path ? asset('storage/'.$this->cover_path) : null;
  }

  // ✅ Precio único
  public function pricesArray(): array
  {
    return $this->price_anual !== null
      ? ['Pago único (Acceso de por vida)' => (float)$this->price_anual]
      : [];
  }

  public function startingPrice(): ?float
  {
    return $this->price_anual !== null ? (float)$this->price_anual : null;
  }

  public function whatsappText(): string
  {
    $base = $this->whatsapp_message
      ?: 'Hola, me interesa el curso [CURSO] del área [ÁREA]. ¿Me brinda información para adquirirlo?';

    return str_replace(['[CURSO]','[ÁREA]'], [$this->title, $this->area?->name ?? ''], $base);
  }
}
