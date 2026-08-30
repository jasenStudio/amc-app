<?php

namespace App\Providers;

use App\Enums\UserRole;
use App\Models\User;
use App\Support\PublicSectionAvailability;
use Carbon\CarbonImmutable;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(PublicSectionAvailability::class);
    }

    public function boot(): void
    {
        $this->configureDefaults();
        $this->configureAuthorization();
        $this->sharePublicSectionAvailability();
    }

    protected function sharePublicSectionAvailability(): void
    {
        View::composer('*', function ($view): void {
            $availability = $this->app->make(PublicSectionAvailability::class);

            $view->with([
                'hasFeaturedProjects' => $availability->hasFeaturedProjects(),
                'hasFeaturedServices' => $availability->hasFeaturedServices(),
                'hasFeaturedPosts' => $availability->hasFeaturedPosts(),
            ]);
        });
    }

    protected function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );

        Password::defaults(fn (): ?Password => app()->isProduction()
            ? Password::min(12)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised()
            : null,
        );
    }

    protected function configureAuthorization(): void
    {
        Gate::before(function (User $user, string $ability): ?bool {
            if ($user->role === UserRole::Pending) {
                return false;
            }

            return null;
        });

        Gate::define('admin', fn (User $user): bool => in_array($user->role, [UserRole::SuperAdmin, UserRole::Admin], true));

        Gate::define('manage-posts', fn (User $user): bool => in_array($user->role, [UserRole::SuperAdmin, UserRole::Admin, UserRole::Editor], true));

        RateLimiter::for('blog-inline-images', fn (Request $request) => Limit::perMinute(30)->by($request->user()?->id ?: $request->ip()));

        RateLimiter::for('dashboard-images', fn (Request $request) => Limit::perMinute(15)->by($request->user()?->id ?: $request->ip()));
    }
}
