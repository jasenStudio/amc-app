<?php

namespace App\Livewire\Concerns;

use Livewire\WithPagination;

/**
 * @mixin WithPagination
 */
trait WithFilters
{
    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatus(): void
    {
        $this->resetPage();
    }

    public function updatingFeatured(): void
    {
        $this->resetPage();
    }

    public function updatingTag(): void
    {
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->reset(['search', 'status', 'featured', 'tag']);
        $this->resetPage();
    }
}
