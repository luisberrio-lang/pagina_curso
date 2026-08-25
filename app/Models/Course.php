<?php

namespace App\Models;

use App\Support\Money;
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

  public function orderItems(): HasMany
  {
    return $this->hasMany(OrderItem::class);
  }

  public function coverUrl(): ?string
  {
    return $this->cover_path ? asset('storage/'.$this->cover_path) : null;
  }

  // ✅ Precio único
  public function currentPrice(): ?string
  {
    return $this->hasCommercialPrice() ? (string) $this->price_anual : null;
  }

  public function previousPrice(): ?string
  {
    return $this->price_previous !== null && (string) $this->price_previous !== ''
      ? (string) $this->price_previous
      : null;
  }

  public function currency(): string
  {
    return Money::currencyCode();
  }

  public function formattedCurrentPrice(): ?string
  {
    return Money::format($this->currentPrice(), $this->currency());
  }

  public function formattedPreviousPrice(): ?string
  {
    return Money::format($this->previousPrice(), $this->currency());
  }

  public function discountPercentage(): ?int
  {
    return Money::discountPercentage($this->currentPrice(), $this->previousPrice());
  }

  public function hasCommercialPrice(): bool
  {
    return Money::isPositive($this->price_anual);
  }

  public function commercialData(): array
  {
    return [
      'id' => $this->getKey(),
      'name' => $this->title,
      'price' => $this->currentPrice(),
      'currency' => $this->currency(),
      'is_published' => $this->is_published,
      'reference' => $this->slug,
      'image' => $this->coverUrl(),
    ];
  }

  public function whatsappText(): string
  {
    $base = $this->whatsapp_message
      ?: 'Hola, me interesa el curso [CURSO] del área [ÁREA]. ¿Me brinda información para adquirirlo?';

    return str_replace(['[CURSO]','[ÁREA]'], [$this->title, $this->area?->name ?? ''], $base);
  }
}
