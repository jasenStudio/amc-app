<?php

namespace App\Filters;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class UserFilter
{
    public function __construct(
        public string $search = '',
        public string $role = '',
    ) {}

    /**
     * @return Builder<User>
     */
    public function apply(): Builder
    {
        $query = User::query()->orderByDesc('id');

        if ($this->search !== '') {
            $term = '%'.$this->search.'%';
            $query->where(function (Builder $q) use ($term): void {
                $q->where('name', 'like', $term)
                    ->orWhere('email', 'like', $term);
            });
        }

        if ($this->role !== '') {
            $query->where('role', $this->role);
        }

        return $query;
    }
}
