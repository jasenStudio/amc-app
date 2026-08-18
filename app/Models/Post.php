<?php

namespace App\Models;

use App\Actions\Images\ConvertImageToWebp;
use App\Concerns\HasSlug;
use App\PostStatus;
use Database\Factories\PostFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $title
 * @property string $slug
 * @property string|null $excerpt
 * @property string $body
 * @property string|null $cover_image
 * @property string|null $cover_image_thumb
 * @property PostStatus $status
 * @property Carbon|null $published_at
 * @property int $author_id
 * @property bool $featured
 * @property int $order
 * @property string|null $seo_title
 * @property string|null $seo_description
 * @property string|null $seo_image
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 */
class Post extends Model
{
    /**
     * @use HasFactory<PostFactory>
     * @use HasSlug<Post>
     */
    use HasFactory, HasSlug, SoftDeletes;

    /**
     * Boot model hooks: clean up image assets only on permanent deletion.
     * Soft delete preserves files so the post can be restored later.
     */
    protected static function booted(): void
    {
        static::forceDeleted(function (Post $post): void {
            $thumb = (string) ($post->cover_image_thumb ?? '');
            $full = (string) ($post->cover_image ?? '');

            if ($thumb === '' && $full === '') {
                return;
            }

            app(ConvertImageToWebp::class)->delete($thumb, $full);
        });
    }

    protected string $slugSource = 'title';

    protected string $slugField = 'slug';

    protected $fillable = [
        'title',
        'slug',
        'excerpt',
        'body',
        'cover_image',
        'cover_image_thumb',
        'status',
        'published_at',
        'author_id',
        'featured',
        'order',
        'seo_title',
        'seo_description',
        'seo_image',
    ];

    protected $attributes = [
        'status' => 'draft',
        'featured' => false,
        'order' => 0,
    ];

    protected function casts(): array
    {
        return [
            'status' => PostStatus::class,
            'published_at' => 'datetime',
            'featured' => 'boolean',
            'order' => 'integer',
        ];
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    /**
     * @return BelongsToMany<Tag, $this>
     */
    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    /**
     * @param  Builder<Post>  $query
     * @return Builder<Post>
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query
            ->where('status', PostStatus::Published->value)
            ->where(function (Builder $query): void {
                $query->whereNull('published_at')->orWhere('published_at', '<=', now());
            });
    }

    /**
     * @param  Builder<Post>  $query
     * @return Builder<Post>
     */
    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('featured', true);
    }

    /**
     * @param  Builder<Post>  $query
     * @return Builder<Post>
     */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query
            ->orderBy('order')
            ->orderByDesc('published_at')
            ->orderByDesc('id');
    }

    public function seoTitle(): string
    {
        return $this->seo_title ?: $this->title;
    }

    public function seoDescription(): string
    {
        return $this->seo_description ?: ($this->excerpt ?? '');
    }

    public function seoImage(): ?string
    {
        return $this->seo_image ?: $this->cover_image;
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
