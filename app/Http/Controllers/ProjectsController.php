<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Support\VideoEmbed;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ProjectsController extends Controller
{
    public function index(): View
    {
        $projects = collect();

        if (Schema::hasTable('projects')) {
            $projects = Project::query()
                ->active()
                ->ordered()
                ->with(['coverImage'])
                ->paginate(12)
                ->appends(request()->query());
        }

        return view('pages::projects.index', [
            'projects' => $projects,
        ]);
    }

    public function show(string $slug): View
    {
        $project = Project::query()
            ->active()
            ->with(['images'])
            ->where('slug', $slug)
            ->first();

        if ($project === null) {
            throw new NotFoundHttpException;
        }

        $videoThumbnail = null;
        $videoEmbedUrl = null;

        if ($project->video_url) {
            $videoThumbnail = VideoEmbed::thumbnailUrl($project->video_url);
            $videoEmbedUrl = VideoEmbed::embedUrl($project->video_url);
        }

        return view('pages::projects.show', [
            'project' => $project,
            'videoThumbnail' => $videoThumbnail,
            'videoEmbedUrl' => $videoEmbedUrl,
        ]);
    }
}
