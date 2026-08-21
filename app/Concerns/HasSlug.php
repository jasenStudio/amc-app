<?php

namespace App\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * @template TModel of Model
 */
trait HasSlug
{
    /**
     * Boot the trait: generate a unique slug from the source attribute on save.
     * Models using this trait MUST declare `protected string $slugSource`
     * (and optionally `protected string $slugField = 'slug'`).
     */
    public static function bootHasSlug(): void
    {
        static::saving(function (Model $model): void {
            /** @var TModel $model */
            if (! $model->isDirty($model->slugSource) && ! $model->isDirty($model->slugField)) {
                return;
            }

            $model->{$model->slugField} = $model->generateUniqueSlug(
                $model->{$model->slugSource}
            );
        });
    }

    /**
     * Build a unique slug from the given value, excluding this model.
     */
    protected function generateUniqueSlug(string $value): string
    {
        $base = Str::slug($value) ?: 'n-a';
        $slug = $base;
        $suffix = 1;

        while ($this->slugExists($slug)) {
            $slug = $base.'-'.$suffix++;
        }

        return $slug;
    }

    /**
     * Check if the given slug is already taken by another record.
     */
    protected function slugExists(string $slug): bool
    {
        return static::query()
            ->where($this->slugField, $slug)
            ->where($this->getKeyName(), '!=', $this->getKey() ?? 0)
            ->exists();
    }

    /**
     * Find a model by its slug.
     *
     * @param  Builder<TModel>  $query
     * @return Builder<TModel>
     */
    public function scopeForSlug(Builder $query, string $slug): Builder
    {
        return $query->where($this->slugField, $slug);
    }
}
