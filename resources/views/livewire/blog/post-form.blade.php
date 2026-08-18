<section class="w-full space-y-6">
    <div>
        <flux:heading size="xl">{{ $post ? __('Edit post') : __('New post') }}</flux:heading>
        <flux:subheading>
            {{ $post ? __('Update the post content and metadata.') : __('Create a new blog post.') }}
        </flux:subheading>
    </div>

    <form wire:submit="save" class="space-y-6">
        <div class="grid gap-6 lg:grid-cols-3">
            <div class="space-y-6 lg:col-span-2">
                <div class="space-y-2">
                    <flux:input wire:model.live.debounce.500ms="title" :label="__('Title')" required autofocus
                        data-test="post-title" />
                </div>

                <div class="space-y-2">
                    <div class="flex items-end gap-2">
                        <div class="flex-1">
                            <flux:input wire:model="slug" :label="__('Slug')" required data-test="post-slug" />
                        </div>
                        @if ($post)
                            <flux:button type="button" variant="ghost" size="sm" wire:click="regenerateSlug"
                                class="mb-0.5 whitespace-nowrap" data-test="regenerate-slug">
                                {{ __('Regenerate') }}
                            </flux:button>
                        @endif
                    </div>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400">
                        {{ __('Used in the public URL. Only letters, numbers and dashes.') }}</p>
                </div>

                <x-blog.post-editor :body="$body" :slug="$slug" />

                <div class="space-y-2">
                    <flux:textarea wire:model="excerpt" :label="__('Excerpt')"
                        :placeholder="__('Short summary used in listings…')" rows="3"
                        data-test="post-excerpt" />
                    <p class="text-xs text-zinc-500 dark:text-zinc-400">{{ strlen($excerpt) }} / 500</p>
                </div>

                <x-blog.post-tags :all-tags="$all_tags" />

                <x-blog.post-seo />
            </div>

            <aside class="space-y-6">
                <x-blog.post-status />

                <flux:card>
                    <livewire:ui.image-uploader
                        :endpoint="route('dashboard.images.store')"
                        path="blog/webp"
                        :slug-hint="$slug"
                        :existing-thumb-url="$coverImageThumb"
                        :existing-full-url="$coverImageFull"
                        @image-uploaded="onImageUploaded"
                        @image-removed="onImageRemoved"
                    />
                </flux:card>

                <div class="flex items-center justify-end gap-3">
                    <flux:button :href="route('blog.index')" wire:navigate variant="filled">
                        {{ __('Cancel') }}
                    </flux:button>
                    <flux:button variant="primary" type="submit" data-test="save-post">
                        {{ __('Save post') }}
                    </flux:button>
                </div>
            </aside>
        </div>
    </form>
</section>
