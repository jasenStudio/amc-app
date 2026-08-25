<?php

namespace App\Livewire\Services;

use App\Enums\ActiveStatus;
use App\Filters\ServiceFilter;
use App\Livewire\Concerns\WithFilters;
use App\Models\Service;
use Flux\Flux as FluxFacade;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.dashboard')]
#[Title('Services')]
class ServicesIndex extends Component
{
    use WithFilters, WithPagination;

    #[Url(except: '')]
    public string $search = '';

    #[Url(except: '')]
    public string $status = '';

    #[Url(except: '')]
    public string $featured = '';

    public ?int $confirmingDeletion = null;

    public function delete(int $serviceId): void
    {
        $service = Service::query()->findOrFail($serviceId);

        Gate::authorize('delete', $service);

        $service->delete();
        $this->confirmingDeletion = null;

        FluxFacade::toast(variant: 'success', text: __('Service deleted.'));
    }

    public function canUpdate(Service $service): bool
    {
        return Gate::allows('update', $service);
    }

    public function canDelete(Service $service): bool
    {
        return Gate::allows('delete', $service);
    }

    public function render(): View
    {
        $filter = new ServiceFilter($this->search, $this->status, $this->featured);

        return view('livewire.services.services-index', [
            'services' => $filter->apply()->paginate(15),
            'statuses' => [ActiveStatus::Active, ActiveStatus::Inactive],
        ]);
    }
}
