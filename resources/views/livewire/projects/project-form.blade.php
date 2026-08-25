<section class="w-full space-y-6">
    <div>
        <flux:heading size="xl">{{ $project ? __('edit_project') : __('new_project') }}</flux:heading>
        <flux:subheading>
            {{ $project ? __('update_project_content_and_metadata') : __('create_new_project') }}
        </flux:subheading>
    </div>

    <form wire:submit="save" class="space-y-6">
        <div class="grid gap-6 lg:grid-cols-3">
            <div class="space-y-6 lg:col-span-2">
                <div class="space-y-2">
                    <flux:input wire:model.live.debounce.500ms="title" :label="__('title')" required autofocus
                        data-test="project-title" />
                </div>

                <div class="space-y-2">
                    <div class="flex items-end gap-2">
                        <div class="flex-1">
                            <flux:input wire:model="slug" :label="__('slug')" required data-test="project-slug" />
                        </div>
                        @if ($project)
                            <flux:button type="button" variant="ghost" size="sm" wire:click="regenerateSlug"
                                class="mb-0.5 whitespace-nowrap" data-test="regenerate-slug">
                                {{ __('regenerate') }}
                            </flux:button>
                        @endif
                    </div>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400">
                        {{ __('slug_hint') }}</p>
                </div>

                <div class="space-y-2">
                    <flux:textarea wire:model="description" :label="__('description')" required rows="6"
                        data-test="project-description" />
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="space-y-2">
                        <flux:input wire:model="client" :label="__('client')" required data-test="project-client" />
                    </div>
                    <div class="space-y-2">
                        <flux:input wire:model="location" :label="__('location')" data-test="project-location" />
                    </div>
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="space-y-2">
                        <flux:input wire:model="date" :label="__('project_date')" type="date" required
                            data-test="project-date" />
                    </div>
                    <div class="space-y-2">
                        <flux:input wire:model="order" :label="__('order')" type="number" min="0"
                            data-test="project-order" />
                    </div>
                </div>

                <div class="space-y-2">
                    <flux:textarea wire:model="excerpt" :label="__('excerpt')"
                        :placeholder="__('excerpt_placeholder')" rows="3" data-test="project-excerpt" />
                    <p class="text-xs text-zinc-500 dark:text-zinc-400">{{ strlen($excerpt) }} / 500</p>
                </div>

                <div class="space-y-2">
                    <flux:input wire:model="video_url" :label="__('video_url')" type="url"
                        placeholder="https://www.youtube.com/watch?v=..." data-test="project-video-url" />
                    <p class="text-xs text-zinc-500 dark:text-zinc-400">
                        {{ __('video_url_hint') }}</p>
                </div>

                <details class="rounded-lg border border-zinc-200 dark:border-zinc-700">
                    <summary class="cursor-pointer px-4 py-3 text-sm font-medium">{{ __('seo') }}</summary>
                    <div class="space-y-3 p-4">
                        <flux:input wire:model="title_seo" :label="__('seo_title')"
                            :description="__('leave_empty_to_use_the_public_title')" />
                    </div>
                </details>

                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <flux:heading size="sm">{{ __('gallery') }}</flux:heading>
                        <livewire:ui.gallery-uploader path="projects/webp" :slug-hint="$slug" />
                    </div>

                    @if (count($galleryImages) > 0)
                        <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4">
                            @foreach ($galleryImages as $index => $image)
                                <div class="relative group">
                                    <img src="{{ \App\Support\ImageUrl::public($image['path']) }}"
                                        alt="" class="aspect-square w-full rounded-lg object-cover">

                                    @if ($image['is_cover'])
                                        <div class="absolute top-2 left-2">
                                            <flux:badge color="amber" size="sm">{{ __('cover') }}</flux:badge>
                                        </div>
                                    @endif

                                    <div class="absolute inset-0 flex items-center justify-center gap-1 bg-black/50 opacity-0 group-hover:opacity-100 transition rounded-lg">
                                        @if (! $image['is_cover'])
                                            <flux:button type="button" size="xs" variant="primary"
                                                wire:click="setCoverImage({{ $index }})"
                                                title="{{ __('set_as_cover') }}">
                                                <flux:icon name="star" class="size-4" />
                                            </flux:button>
                                        @endif
                                        @if ($index > 0)
                                            <flux:button type="button" size="xs" variant="filled"
                                                wire:click="moveGalleryImageUp({{ $index }})"
                                                title="{{ __('move_up') }}">
                                                <flux:icon name="arrow-up" class="size-4" />
                                            </flux:button>
                                        @endif
                                        @if ($index < count($galleryImages) - 1)
                                            <flux:button type="button" size="xs" variant="filled"
                                                wire:click="moveGalleryImageDown({{ $index }})"
                                                title="{{ __('move_down') }}">
                                                <flux:icon name="arrow-down" class="size-4" />
                                            </flux:button>
                                        @endif
                                        <flux:button type="button" size="xs" variant="danger"
                                            wire:click="removeGalleryImage({{ $index }})"
                                            title="{{ __('remove') }}">
                                            <flux:icon name="trash" class="size-4" />
                                        </flux:button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="rounded-lg border-2 border-dashed border-zinc-300 dark:border-zinc-600 p-8 text-center">
                            <flux:text class="text-zinc-500">{{ __('no_images_uploaded_yet') }}</flux:text>
                        </div>
                    @endif
                </div>
            </div>

            <aside class="space-y-6">
                <flux:card>
                    <flux:select wire:model="status" :label="__('status')" data-test="project-status">
                        <flux:select.option value="active">{{ __('active') }}</flux:select.option>
                        <flux:select.option value="inactive">{{ __('inactive') }}</flux:select.option>
                    </flux:select>

                    <div class="mt-4">
                        <flux:checkbox wire:model="featured" :label="__('featured')" />
                    </div>
                </flux:card>

                <div class="flex items-center justify-end gap-3">
                    <flux:button :href="route('dashboard.projects.index')" wire:navigate variant="filled">
                        {{ __('cancel') }}
                    </flux:button>
                    <flux:button variant="primary" type="submit" data-test="save-project">
                        {{ __('save_project') }}
                    </flux:button>
                </div>
            </aside>
        </div>
    </form>
</section>
