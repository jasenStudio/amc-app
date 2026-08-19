<section class="w-full space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <flux:heading size="xl">{{ __('Blog') }}</flux:heading>
            <flux:subheading>{{ __('Manage posts.') }}</flux:subheading>
        </div>
        <flux:button variant="primary" :href="route('blog.create')" wire:navigate icon="plus" data-test="new-post">
            {{ __('New post') }}
        </flux:button>

    </div>
    <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
        <flux:input wire:model.live.debounce.300ms="search" :label="__('Search')" type="search"
            placeholder="{{ __('Title or tag…') }}" />

        <flux:select wire:model.live="status" :label="__('Status')" placeholder="{{ __('Any') }}">
            @foreach ($statuses as $s)
                <flux:select.option :value="$s->value">{{ ucfirst($s->value) }}</flux:select.option>
            @endforeach
        </flux:select>

        <flux:select wire:model.live="featured" :label="__('Featured')">
            <flux:select.option value="">{{ __('Any') }}</flux:select.option>
            <flux:select.option value="yes">{{ __('Featured only') }}</flux:select.option>
            <flux:select.option value="no">{{ __('Not featured') }}</flux:select.option>
        </flux:select>

        <flux:select wire:model.live="tag" :label="__('Tag')" placeholder="{{ __('Any') }}">
            @foreach ($tags as $t)
                <flux:select.option :value="$t->slug">{{ $t->name }}</flux:select.option>
            @endforeach
        </flux:select>
    </div>

    <flux:table :paginate="$posts">
        <flux:table.columns>
            <flux:table.column>{{ __('Cover') }}</flux:table.column>
            <flux:table.column>{{ __('Title') }}</flux:table.column>
            <flux:table.column>{{ __('Status') }}</flux:table.column>
            <flux:table.column align="center">{{ __('Featured') }}</flux:table.column>
            <flux:table.column align="end">{{ __('Order') }}</flux:table.column>
            <flux:table.column>{{ __('Author') }}</flux:table.column>
            <flux:table.column align="end">{{ __('Actions') }}</flux:table.column>
        </flux:table.columns>

        <flux:table.rows>
            @forelse ($posts as $post)
                <flux:table.row :key="$post->id">
                    <flux:table.cell>
                        @if ($post->cover_image_thumb_url)
                            <img src="{{ $post->cover_image_thumb_url }}" alt=""
                                class="size-10 rounded object-cover">
                        @else
                            <div class="size-10 rounded bg-zinc-100 dark:bg-zinc-700"></div>
                        @endif
                    </flux:table.cell>

                    <flux:table.cell variant="strong">
                        <div class="max-w-xs truncate">{{ $post->title }}</div>
                        @if ($post->tags->isNotEmpty())
                            <div class="mt-1 flex flex-wrap gap-1">
                                @foreach ($post->tags as $tag)
                                    <flux:badge size="sm" color="zinc">{{ $tag->name }}</flux:badge>
                                @endforeach
                            </div>
                        @endif
                    </flux:table.cell>

                    <flux:table.cell>
                        <flux:badge :color="$post->status->value === 'published' ? 'green' : 'zinc'" size="sm">
                            {{ ucfirst($post->status->value) }}
                        </flux:badge>
                    </flux:table.cell>

                    <flux:table.cell align="center">
                        @if ($post->featured)
                            <flux:icon name="star" class="size-4 text-amber-500 inline" />
                        @else
                            <flux:icon name="star" class="size-4 text-zinc-300 inline" variant="outline" />
                        @endif
                    </flux:table.cell>

                    <flux:table.cell align="end">{{ $post->order }}</flux:table.cell>

                    <flux:table.cell>{{ $post->author->name }}</flux:table.cell>

                    <flux:table.cell align="end">
                        <div class="flex justify-end gap-2">
                            @if ($this->canUpdate($post))
                                <flux:button size="sm" :href="route('blog.edit', $post)" wire:navigate
                                    variant="ghost" icon="pencil" data-test="edit-post-{{ $post->id }}">
                                    {{ __('Edit') }}
                                </flux:button>
                            @endif
                            @if ($this->canDelete($post))
                                <flux:button size="sm" variant="ghost" icon="trash"
                                    data-test="delete-post-{{ $post->id }}"
                                    wire:click="$set('confirmingDeletion', {{ $post->id }})">
                                    {{ __('Delete') }}
                                </flux:button>
                            @endif
                        </div>
                    </flux:table.cell>
                </flux:table.row>
            @empty
                <flux:table.row>
                    <flux:table.cell colspan="7" class="text-center text-zinc-500">
                        {{ __('No posts found.') }}
                    </flux:table.cell>
                </flux:table.row>
            @endforelse
        </flux:table.rows>
    </flux:table>

    <flux:modal name="confirm-post-deletion" wire:model="confirmingDeletion" focusable class="max-w-lg">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">{{ __('Delete this post?') }}</flux:heading>
                <flux:subheading>
                    {{ __('The post will be hidden from the public site and can be restored later by an admin.') }}
                </flux:subheading>
            </div>

            <div class="flex justify-end space-x-2 rtl:space-x-reverse">
                <flux:modal.close>
                    <flux:button variant="filled">{{ __('Cancel') }}</flux:button>
                </flux:modal.close>

                <flux:button variant="danger" wire:click="delete({{ (int) $confirmingDeletion }})"
                    data-test="confirm-delete-post">
                    {{ __('Delete post') }}
                </flux:button>
            </div>
        </div>
    </flux:modal>


</section>
