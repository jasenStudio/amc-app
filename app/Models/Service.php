<?php

namespace App\Models;

use App\Concerns\HasSlug;
use App\Enums\ActiveStatus;
use App\Support\ImageUrl;
use Database\Factories\ServiceFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * @property int $id
 * @property string $title
 * @property string|null $title_seo
 * @property string $slug
 * @property string $description
 * @property string|null $excerpt
 * @property float|null $price
 * @property ActiveStatus $status
 * @property bool $featured
 * @property int $order
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 */
class Service extends Model
{
    /**
     * @use HasFactory<ServiceFactory>
     * @use HasSlug<Service>
     */
    use HasFactory, HasSlug, SoftDeletes;

    protected static function booted(): void
    {
        static::forceDeleted(function (Service $service): void {
            $service->images()->get()->each->delete();
        });
    }

    protected string $slugSource = 'title';

    protected string $slugField = 'slug';

    protected $fillable = [
        'title',
        'title_seo',
        'slug',
        'description',
        'excerpt',
        'price',
        'status',
        'featured',
        'order',
    ];

    protected $attributes = [
        'status' => 'active',
        'featured' => false,
        'order' => 0,
    ];

    protected function casts(): array
    {
        return [
            'status' => ActiveStatus::class,
            'price' => 'decimal:2',
            'featured' => 'boolean',
            'order' => 'integer',
        ];
    }

    /**
     * @return HasMany<ServiceImage, $this>
     */
    public function images(): HasMany
    {
        return $this->hasMany(ServiceImage::class)->orderBy('order');
    }

    /**
     * @return HasOne<ServiceImage, $this>
     */
    public function coverImage(): HasOne
    {
        return $this->hasOne(ServiceImage::class)->where('is_cover', true);
    }

    /**
     * @param  Builder<Service>  $query
     * @return Builder<Service>
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', ActiveStatus::Active->value);
    }

    /**
     * @param  Builder<Service>  $query
     * @return Builder<Service>
     */
    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('featured', true);
    }

    /**
     * @param  Builder<Service>  $query
     * @return Builder<Service>
     */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query
            ->orderBy('order')
            ->orderByDesc('id');
    }

    public function seoTitle(): string
    {
        return $this->title_seo ?: $this->title;
    }

    public function seoDescription(): string
    {
        return $this->excerpt ?: Str::limit(strip_tags($this->description), 160);
    }

    public function seoImage(): ?string
    {
        return $this->coverImage?->image_path;
    }

    public function getCoverImageUrlAttribute(): ?string
    {
        return ImageUrl::public($this->coverImage?->image_path);
    }

    protected function slugExists(string $slug): bool
    {
        return static::query()
            ->where($this->slugField, $slug)
            ->where($this->getKeyName(), '!=', $this->getKey() ?? 0)
            ->withTrashed()
            ->exists();
    }
}
