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
                    <flux:input wire:model.live.debounce.250ms="title" :label="__('Title')" required autofocus data-test="post-title" />
                </div>

                <div class="space-y-2">
                    <flux:input wire:model="slug" :label="__('Slug')" required data-test="post-slug" />
                    <p class="text-xs text-zinc-500 dark:text-zinc-400">{{ __('Used in the public URL. Only letters, numbers and dashes.') }}</p>
                </div>

                <div
                    wire:ignore
                    wire:key="post-body-editor"
                    class="space-y-2"
                    x-data="tiptapEditor({
                        endpoint: '{{ route('blog.images.store') }}',
                        csrf: document.querySelector('meta[name=\'csrf-token\']').content,
                        initial: @js($body),
                    })"
                    x-init="mount($el, $wire)"
                >
                    <flux:label>{{ __('Body') }}</flux:label>

                    <div data-tiptap-target="toolbar" class="flex flex-wrap gap-1 rounded-t-lg border border-b-0 border-zinc-200 bg-zinc-50 p-1 dark:border-zinc-700 dark:bg-zinc-800">
                        <button type="button" data-cmd="bold" :class="{ 'is-active': isBold }" :aria-pressed="isBold" class="rounded px-2 py-1 text-sm font-bold hover:bg-white dark:hover:bg-zinc-700" aria-label="Bold">B</button>
                        <button type="button" data-cmd="italic" :class="{ 'is-active': isItalic }" :aria-pressed="isItalic" class="rounded px-2 py-1 text-sm italic hover:bg-white dark:hover:bg-zinc-700" aria-label="Italic">I</button>
                        <button type="button" data-cmd="h2" :class="{ 'is-active': isH2 }" :aria-pressed="isH2" class="rounded px-2 py-1 text-xs font-semibold hover:bg-white dark:hover:bg-zinc-700" aria-label="Heading 2">H2</button>
                        <button type="button" data-cmd="h3" :class="{ 'is-active': isH3 }" :aria-pressed="isH3" class="rounded px-2 py-1 text-xs font-semibold hover:bg-white dark:hover:bg-zinc-700" aria-label="Heading 3">H3</button>
                        <button type="button" data-cmd="ul" :class="{ 'is-active': isBulletList }" :aria-pressed="isBulletList" class="rounded px-2 py-1 text-sm hover:bg-white dark:hover:bg-zinc-700" aria-label="Bullet list">•≡</button>
                        <button type="button" data-cmd="ol" :class="{ 'is-active': isOrderedList }" :aria-pressed="isOrderedList" class="rounded px-2 py-1 text-sm hover:bg-white dark:hover:bg-zinc-700" aria-label="Numbered list">1.</button>
                        <button type="button" data-cmd="quote" :class="{ 'is-active': isBlockquote }" :aria-pressed="isBlockquote" class="rounded px-2 py-1 text-sm italic hover:bg-white dark:hover:bg-zinc-700" aria-label="Quote">&ldquo;</button>
                        <button type="button" data-cmd="link" :class="{ 'is-active': isLink }" :aria-pressed="isLink" class="rounded px-2 py-1 text-sm hover:bg-white dark:hover:bg-zinc-700" aria-label="Link">⌘L</button>
                        <button type="button" data-cmd="image" class="rounded px-2 py-1 text-sm hover:bg-white dark:hover:bg-zinc-700" aria-label="Image">🖼</button>
                    </div>

                    <div data-tiptap-target="editor" class="rounded-b-lg border border-zinc-200 bg-white p-4 dark:border-zinc-700 dark:bg-zinc-900"></div>

                    @error('body')<p class="text-xs text-red-500">{{ $message }}</p>@enderror
                </div>

                {{-- Hidden field synced by TipTap --}}
                <input type="hidden" wire:model="body" />

                <div class="space-y-2">
                    <flux:textarea wire:model="excerpt" :label="__('Excerpt')" :placeholder="__('Short summary used in listings…')" rows="3" data-test="post-excerpt" />
                    <p class="text-xs text-zinc-500 dark:text-zinc-400">{{ strlen($excerpt) }} / 500</p>
                </div>

                <flux:fieldset :legend="__('Tags')">
                    <div class="space-y-3">
                        <div class="flex flex-wrap gap-2">
                            @forelse ($all_tags as $tag)
                                <label class="inline-flex items-center gap-2 rounded-md border border-zinc-200 bg-white px-2 py-1 text-sm dark:border-zinc-700 dark:bg-zinc-800">
                                    <input
                                        type="checkbox"
                                        value="{{ $tag->id }}"
                                        wire:model="tag_ids"
                                        class="rounded border-zinc-300 text-amc-blue focus:ring-amc-blue"
                                        data-test="tag-{{ $tag->id }}"
                                    />
                                    <span>{{ $tag->name }}</span>
                                </label>
                            @empty
                                <p class="text-sm text-zinc-500">{{ __('No tags yet. Add the first one below.') }}</p>
                            @endforelse
                        </div>

                        <div class="flex items-end gap-2">
                            <flux:input wire:model="new_tag_name" :label="__('New tag')" :placeholder="__('Tag name')" class="flex-1" data-test="new-tag-name" />
                        </div>
                    </div>
                </flux:fieldset>

                <details class="rounded-lg border border-zinc-200 dark:border-zinc-700">
                    <summary class="cursor-pointer px-4 py-3 text-sm font-medium">{{ __('SEO') }}</summary>
                    <div class="space-y-3 p-4">
                        <flux:input wire:model="seo_title" :label="__('SEO title')" :description="__('Falls to the visible title when empty.')" />
                        <flux:textarea wire:model="seo_description" :label="__('SEO description')" :description="__('Falls to the excerpt when empty.')" rows="3" />
                        <flux:input wire:model="seo_image" :label="__('SEO image URL')" :description="__('Falls to the cover image when empty.')" />
                    </div>
                </details>
            </div>

            <aside class="space-y-6">
                <flux:card>
                    <div class="space-y-4">
                        <flux:select wire:model="status" :label="__('Status')" data-test="post-status">
                            <flux:select.option value="draft">{{ __('Draft') }}</flux:select.option>
                            <flux:select.option value="published">{{ __('Published') }}</flux:select.option>
                        </flux:select>

                        <flux:input wire:model="published_at" :label="__('Published at')" type="datetime-local" />

                        <flux:switch wire:model="featured" :label="__('Featured on home')" />

                        <flux:input wire:model.number="order" :label="__('Order')" type="number" min="0" />
                    </div>
                </flux:card>

                <flux:card>
                    <div class="space-y-3">
                        <flux:label>{{ __('Cover image') }}</flux:label>

                        @if ($post?->cover_image_thumb && ! $cover_upload && ! $should_remove_cover)
                            <img src="{{ asset('storage/'.$post->cover_image_thumb) }}" alt="" class="size-full rounded-md object-cover" data-test="current-cover">
                            <flux:button type="button" variant="ghost" icon="trash" wire:click="removeCover" data-test="remove-cover">
                                {{ __('Remove cover') }}
                            </flux:button>
                        @endif

                        <flux:input wire:model="cover_upload" type="file" accept="image/png,image/jpeg,image/webp" :label="__('Upload new')" data-test="cover-upload" />

                        @if ($cover_upload)
                            <p class="text-xs text-zinc-500">{{ __('New cover will replace the current one on save.') }}</p>
                        @endif
                    </div>
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