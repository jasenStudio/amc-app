<?php

namespace App\Livewire\Services;

use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.dashboard')]
#[Title('Services')]
class ServicesIndex extends Component
{
    public function render(): View
    {
        return view('livewire.services.services-index');
    }
}
