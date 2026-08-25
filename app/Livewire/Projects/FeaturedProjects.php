<?php

namespace App\Livewire\Projects;

use App\Models\Project;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Schema;
use Livewire\Component;

class FeaturedProjects extends Component
{
    public int $limit = 4;

    public function render(): View
    {
        $projects = collect();

        if (Schema::hasTable('projects')) {
            $projects = Project::query()
                ->active()
                ->featured()
                ->ordered()
                ->with(['coverImage'])
                ->limit($this->limit)
                ->get();
        }

        return view('livewire.projects.featured-projects', [
            'projects' => $projects,
        ]);
    }
}
