<?php

namespace App\Livewire\Projects;

use App\Enums\ActiveStatus;
use App\Filters\ProjectFilter;
use App\Livewire\Concerns\WithFilters;
use App\Models\Project;
use Flux\Flux as FluxFacade;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.dashboard')]
#[Title('Projects')]
class ProjectsIndex extends Component
{
    use WithFilters, WithPagination;

    #[Url(except: '')]
    public string $search = '';

    #[Url(except: '')]
    public string $status = '';

    #[Url(except: '')]
    public string $featured = '';

    #[Url(except: '')]
    public string $location = '';

    public ?int $confirmingDeletion = null;

    public function delete(int $projectId): void
    {
        $project = Project::query()->findOrFail($projectId);

        Gate::authorize('delete', $project);

        $project->delete();
        $this->confirmingDeletion = null;

        FluxFacade::toast(variant: 'success', text: __('Project deleted.'));
    }

    public function canUpdate(Project $project): bool
    {
        return Gate::allows('update', $project);
    }

    public function canDelete(Project $project): bool
    {
        return Gate::allows('delete', $project);
    }

    public function render(): View
    {
        $filter = new ProjectFilter($this->search, $this->status, $this->featured, $this->location);

        return view('livewire.projects.projects-index', [
            'projects' => $filter->apply()->paginate(15),
            'statuses' => [ActiveStatus::Active, ActiveStatus::Inactive],
        ]);
    }
}
