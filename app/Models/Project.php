<?php

namespace App\Models;

use App\Concerns\HasSlug;
use App\Enums\ActiveStatus;
use App\Support\ImageUrl;
use Database\Factories\ProjectFactory;
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
 * @property string $client
 * @property string|null $location
 * @property Carbon $date
 * @property ActiveStatus $status
 * @property bool $featured
 * @property int $order
 * @property string|null $video_url
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 */
class Project extends Model
{
    /**
     * @use HasFactory<ProjectFactory>
     * @use HasSlug<Project>
     */
    use HasFactory, HasSlug, SoftDeletes;

    protected static function booted(): void
    {
        static::forceDeleted(function (Project $project): void {
            $project->images()->get()->each->delete();
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
        'client',
        'location',
        'date',
        'status',
        'featured',
        'order',
        'video_url',
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
            'date' => 'date',
            'featured' => 'boolean',
            'order' => 'integer',
        ];
    }

    /**
     * @return HasMany<ProjectImage, $this>
     */
    public function images(): HasMany
    {
        return $this->hasMany(ProjectImage::class)->orderBy('order');
    }

    /**
     * @return HasOne<ProjectImage, $this>
     */
    public function coverImage(): HasOne
    {
        return $this->hasOne(ProjectImage::class)->where('is_cover', true);
    }

    /**
     * @param  Builder<Project>  $query
     * @return Builder<Project>
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', ActiveStatus::Active->value);
    }

    /**
     * @param  Builder<Project>  $query
     * @return Builder<Project>
     */
    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('featured', true);
    }

    /**
     * @param  Builder<Project>  $query
     * @return Builder<Project>
     */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query
            ->orderBy('order')
            ->orderByDesc('date')
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
