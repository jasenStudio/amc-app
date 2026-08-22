<?php

namespace App\Livewire\Tags;

use App\Models\Tag;
use Flux\Flux as FluxFacade;
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

    public ?int $confirmingDeletion = null;

    public function delete(int $tagId): void
    {
        $tag = Tag::findOrFail($tagId);
        $tag->delete();

        $this->confirmingDeletion = null;

        FluxFacade::toast(variant: 'success', text: __('Tag deleted.'));
    }

    public function render(): View
    {
        $query = Tag::query()
            ->withCount('posts')
            ->orderBy('name');

        if ($this->search !== '') {
            $query->where('name', 'like', '%'.$this->search.'%');
        }

        return view('livewire.tags.tags-index', [
            'tags' => $query->paginate(15),
        ]);
    }
}
