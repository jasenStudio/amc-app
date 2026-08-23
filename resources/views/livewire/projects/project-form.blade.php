<section class="w-full space-y-6">
    <div>
        <flux:heading size="xl">{{ $project ? __('Edit project') : __('New project') }}</flux:heading>
        <flux:subheading>
            {{ $project ? __('Update the project content and metadata.') : __('Create a new project.') }}
        </flux:subheading>
    </div>

    <form wire:submit="save" class="space-y-6">
        <div class="grid gap-6 lg:grid-cols-3">
            <div class="space-y-6 lg:col-span-2">
                <div class="space-y-2">
                    <flux:input wire:model.live.debounce.500ms="title" :label="__('Title')" required autofocus
                        data-test="project-title" />
                </div>

                <div class="space-y-2">
                    <div class="flex items-end gap-2">
                        <div class="flex-1">
                            <flux:input wire:model="slug" :label="__('Slug')" required data-test="project-slug" />
                        </div>
                        @if ($project)
                            <flux:button type="button" variant="ghost" size="sm" wire:click="regenerateSlug"
                                class="mb-0.5 whitespace-nowrap" data-test="regenerate-slug">
                                {{ __('Regenerate') }}
                            </flux:button>
                        @endif
                    </div>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400">
                        {{ __('Used in the public URL. Only letters, numbers and dashes.') }}</p>
                </div>

                <div class="space-y-2">
                    <flux:textarea wire:model="description" :label="__('Description')" required rows="6"
                        data-test="project-description" />
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="space-y-2">
                        <flux:input wire:model="client" :label="__('Client')" required data-test="project-client" />
                    </div>
                    <div class="space-y-2">
                        <flux:input wire:model="location" :label="__('Location')" data-test="project-location" />
                    </div>
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="space-y-2">
                        <flux:input wire:model="date" :label="__('Project date')" type="date" required
                            data-test="project-date" />
                    </div>
                    <div class="space-y-2">
                        <flux:input wire:model="order" :label="__('Order')" type="number" min="0"
                            data-test="project-order" />
                    </div>
                </div>

                <div class="space-y-2">
                    <flux:textarea wire:model="excerpt" :label="__('Excerpt')"
                        :placeholder="__('Short summary used in listings…')" rows="3" data-test="project-excerpt" />
                    <p class="text-xs text-zinc-500 dark:text-zinc-400">{{ strlen($excerpt) }} / 500</p>
                </div>

                <div class="space-y-2">
                    <flux:input wire:model="video_url" :label="__('Video URL')" type="url"
                        placeholder="https://www.youtube.com/watch?v=..." data-test="project-video-url" />
                    <p class="text-xs text-zinc-500 dark:text-zinc-400">
                        {{ __('Paste a YouTube or Vimeo link (optional).') }}</p>
                </div>

                <details class="rounded-lg border border-zinc-200 dark:border-zinc-700">
                    <summary class="cursor-pointer px-4 py-3 text-sm font-medium">{{ __('SEO') }}</summary>
                    <div class="space-y-3 p-4">
                        <flux:input wire:model="title_seo" :label="__('SEO title')"
                            :description="__('Leave empty to use the public title.')" />
                    </div>
                </details>

                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <flux:heading size="sm">{{ __('Gallery') }}</flux:heading>
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
                                            <flux:badge color="amber" size="sm">{{ __('Cover') }}</flux:badge>
                                        </div>
                                    @endif

                                    <div class="absolute inset-0 flex items-center justify-center gap-1 bg-black/50 opacity-0 group-hover:opacity-100 transition rounded-lg">
                                        @if (! $image['is_cover'])
                                            <flux:button type="button" size="xs" variant="primary"
                                                wire:click="setCoverImage({{ $index }})"
                                                title="{{ __('Set as cover') }}">
                                                <flux:icon name="star" class="size-4" />
                                            </flux:button>
                                        @endif
                                        @if ($index > 0)
                                            <flux:button type="button" size="xs" variant="filled"
                                                wire:click="moveGalleryImageUp({{ $index }})"
                                                title="{{ __('Move up') }}">
                                                <flux:icon name="arrow-up" class="size-4" />
                                            </flux:button>
                                        @endif
                                        @if ($index < count($galleryImages) - 1)
                                            <flux:button type="button" size="xs" variant="filled"
                                                wire:click="moveGalleryImageDown({{ $index }})"
                                                title="{{ __('Move down') }}">
                                                <flux:icon name="arrow-down" class="size-4" />
                                            </flux:button>
                                        @endif
                                        <flux:button type="button" size="xs" variant="danger"
                                            wire:click="removeGalleryImage({{ $index }})"
                                            title="{{ __('Remove') }}">
                                            <flux:icon name="trash" class="size-4" />
                                        </flux:button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="rounded-lg border-2 border-dashed border-zinc-300 dark:border-zinc-600 p-8 text-center">
                            <flux:text class="text-zinc-500">{{ __('No images uploaded yet.') }}</flux:text>
                        </div>
                    @endif
                </div>
            </div>

            <aside class="space-y-6">
                <flux:card>
                    <flux:select wire:model="status" :label="__('Status')" data-test="project-status">
                        <flux:select.option value="active">{{ __('Active') }}</flux:select.option>
                        <flux:select.option value="inactive">{{ __('Inactive') }}</flux:select.option>
                    </flux:select>

                    <div class="mt-4">
                        <flux:checkbox wire:model="featured" :label="__('Featured')" />
                    </div>
                </flux:card>

                <div class="flex items-center justify-end gap-3">
                    <flux:button :href="route('dashboard.projects.index')" wire:navigate variant="filled">
                        {{ __('Cancel') }}
                    </flux:button>
                    <flux:button variant="primary" type="submit" data-test="save-project">
                        {{ __('Save project') }}
                    </flux:button>
                </div>
            </aside>
        </div>
    </form>
</section>
