<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Project;
use App\Models\Service;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Schema;

class SitemapController extends Controller
{
    public function __invoke(): Response
    {
        $deployDate = '2026-08-26T00:00:00+00:00';

        $urls = collect([
            [
                'loc' => route('home'),
                'lastmod' => $deployDate,
                'changefreq' => 'weekly',
                'priority' => '1.0',
            ],
            [
                'loc' => route('services'),
                'lastmod' => $deployDate,
                'changefreq' => 'weekly',
                'priority' => '0.9',
            ],
            [
                'loc' => route('projects'),
                'lastmod' => $deployDate,
                'changefreq' => 'weekly',
                'priority' => '0.8',
            ],
            [
                'loc' => route('blog'),
                'lastmod' => $deployDate,
                'changefreq' => 'weekly',
                'priority' => '0.8',
            ],
            [
                'loc' => route('about.vision'),
                'lastmod' => $deployDate,
                'changefreq' => 'monthly',
                'priority' => '0.5',
            ],
            [
                'loc' => route('about.mission'),
                'lastmod' => $deployDate,
                'changefreq' => 'monthly',
                'priority' => '0.5',
            ],
            [
                'loc' => route('privacy.policy'),
                'lastmod' => $deployDate,
                'changefreq' => 'yearly',
                'priority' => '0.3',
            ],
        ]);

        if (Schema::hasTable('services')) {
            Service::query()->active()->ordered()->get(['slug', 'updated_at'])->each(function (Service $service) use (&$urls): void {
                $urls->push([
                    'loc' => route('services.show', $service->slug),
                    'lastmod' => $service->updated_at?->toAtomString() ?? now()->toAtomString(),
                    'changefreq' => 'monthly',
                    'priority' => '0.7',
                ]);
            });
        }

        if (Schema::hasTable('projects')) {
            Project::query()->active()->ordered()->get(['slug', 'updated_at'])->each(function (Project $project) use (&$urls): void {
                $urls->push([
                    'loc' => route('projects.show', $project->slug),
                    'lastmod' => $project->updated_at?->toAtomString() ?? now()->toAtomString(),
                    'changefreq' => 'monthly',
                    'priority' => '0.6',
                ]);
            });
        }

        if (Schema::hasTable('posts')) {
            Post::query()->published()->ordered()->get(['slug', 'updated_at', 'published_at'])->each(function (Post $post) use (&$urls): void {
                $urls->push([
                    'loc' => route('blog.show', $post->slug),
                    'lastmod' => ($post->updated_at ?? $post->published_at)?->toAtomString() ?? now()->toAtomString(),
                    'changefreq' => 'weekly',
                    'priority' => '0.6',
                ]);
            });
        }

        return response()
            ->view('sitemap', ['urls' => $urls])
            ->header('Content-Type', 'application/xml; charset=UTF-8');
    }
}
