<?php

namespace App\Filters;

use App\Models\Service;
use Illuminate\Database\Eloquent\Builder;

class ServiceFilter
{
    public function __construct(
        public string $search = '',
        public string $status = '',
        public string $featured = '',
    ) {}

    /**
     * @return Builder<Service>
     */
    public function apply(): Builder
    {
        $query = Service::query()
            ->with(['coverImage'])
            ->orderByDesc('id');

        if ($this->search !== '') {
            $term = '%'.$this->search.'%';
            $query->where(function (Builder $q) use ($term): void {
                $q->where('title', 'like', $term)
                    ->orWhere('description', 'like', $term);
            });
        }

        if ($this->status !== '') {
            $query->where('status', $this->status);
        }

        if ($this->featured === 'yes') {
            $query->where('featured', true);
        } elseif ($this->featured === 'no') {
            $query->where('featured', false);
        }

        return $query;
    }
}
