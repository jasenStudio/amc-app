<?php

namespace App\Support;

use App\Models\Post;
use App\Models\Project;
use App\Models\Service;
use Illuminate\Support\Facades\Schema;

/**
 * Resolve whether the public home sections have content worth rendering.
 *
 * These checks power the visibility of the featured sections and their
 * navigation links (navbar, footer, 404). Using `exists()` keeps the queries
 * cheap since only a COUNT is executed. Results are memoized per request so
 * the three queries run at most once, even when several views consult them.
 */
final class PublicSectionAvailability
{
    private ?bool $hasProjects = null;

    private ?bool $hasServices = null;

    private ?bool $hasPosts = null;

    public function hasFeaturedProjects(): bool
    {
        return $this->hasProjects ??= Schema::hasTable('projects')
            && Project::query()->active()->featured()->exists();
    }

    public function hasFeaturedServices(): bool
    {
        return $this->hasServices ??= Schema::hasTable('services')
            && Service::query()->active()->featured()->exists();
    }

    public function hasFeaturedPosts(): bool
    {
        return $this->hasPosts ??= Schema::hasTable('posts')
            && Post::query()->published()->featured()->exists();
    }
}
