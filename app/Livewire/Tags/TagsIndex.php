<?php

namespace App\Livewire\Tags;

use App\Models\Tag;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('layouts.dashboard')]
#[Title('Tags')]
class TagsIndex extends Component
{
    #[Url(except: '')]
    public string $search = '';

    public function render(): View
    {
        $query = Tag::query()->orderBy('name');

        if ($this->search !== '') {
            $query->where('name', 'like', '%' . $this->search . '%');
        }

        return view('livewire.tags.tags-index', [
            'tags' => $query->paginate(15),
        ]);
    }
}
