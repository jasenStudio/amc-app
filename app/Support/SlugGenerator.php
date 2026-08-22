<?php

namespace App\Support;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class SlugGenerator
{
    public static function unique(string $modelClass, string $title, ?int $ignoreId = null): string
    {
        $base = Str::slug($title) ?: 'n-a';
        $slug = $base;
        $suffix = 1;

        $usesSoftDeletes = in_array(SoftDeletes::class, class_uses_recursive($modelClass), true);

        $exists = static::slugExists($modelClass, $slug, $ignoreId, $usesSoftDeletes);

        while ($exists) {
            $slug = $base.'-'.$suffix++;
            $exists = static::slugExists($modelClass, $slug, $ignoreId, $usesSoftDeletes);
        }

        return $slug;
    }

    private static function slugExists(string $modelClass, string $slug, ?int $ignoreId, bool $usesSoftDeletes): bool
    {
        $query = $modelClass::query()
            ->where('slug', $slug);

        if ($ignoreId !== null) {
            $query->where('id', '!=', $ignoreId);
        }

        if ($usesSoftDeletes) {
            $query->withTrashed();
        }

        return $query->exists();
    }
}
