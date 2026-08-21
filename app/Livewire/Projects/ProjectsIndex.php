<?php

namespace App\Livewire\Projects;

use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.dashboard')]
#[Title('Projects')]
class ProjectsIndex extends Component
{
    public function render(): View
    {
        return view('livewire.projects.projects-index');
    }
}
