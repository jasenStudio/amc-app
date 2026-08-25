<?php

namespace App\Filters;

use App\Models\Project;
use Illuminate\Database\Eloquent\Builder;

class ProjectFilter
{
    public function __construct(
        public string $search = '',
        public string $status = '',
        public string $featured = '',
        public string $location = '',
    ) {}

    /**
     * @return Builder<Project>
     */
    public function apply(): Builder
    {
        $query = Project::query()
            ->with(['coverImage'])
            ->orderByDesc('id');

        if ($this->search !== '') {
            $term = '%'.$this->search.'%';
            $query->where(function (Builder $q) use ($term): void {
                $q->where('title', 'like', $term)
                    ->orWhere('client', 'like', $term)
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

        if ($this->location !== '') {
            $query->where('location', 'like', '%'.$this->location.'%');
        }

        return $query;
    }
}
