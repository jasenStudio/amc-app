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

    public function updatingRole(): void
    {
        $this->resetPage();
    }

    public function updatingLocation(): void
    {
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $filters = ['search', 'status', 'featured', 'tag', 'role', 'location'];
        $existing = array_filter($filters, fn (string $f) => property_exists($this, $f));
        $this->reset($existing);
        $this->resetPage();
    }
}
