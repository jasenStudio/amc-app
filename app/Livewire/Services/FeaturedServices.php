<?php

namespace App\Livewire\Services;

use App\Models\Service;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Schema;
use Livewire\Component;

class FeaturedServices extends Component
{
    public int $limit = 6;

    public function render(): View
    {
        $services = collect();

        if (Schema::hasTable('services')) {
            $services = Service::query()
                ->active()
                ->featured()
                ->ordered()
                ->with(['coverImage'])
                ->limit($this->limit)
                ->get();
        }

        return view('livewire.services.featured-services', [
            'services' => $services,
        ]);
    }
}
