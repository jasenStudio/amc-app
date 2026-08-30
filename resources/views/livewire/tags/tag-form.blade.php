<section class="space-y-6 flex flex-col justify-center items-center max-w-lg mx-auto mt-10 lg:mt-20">
    <div class="flex items-center justify-between self-start">
        <div>
            <flux:heading size="xl">{{ $tag ? __('edit_tag') : __('new_tag') }}</flux:heading>
            <flux:subheading>
                {{ $tag ? __('update_tag_description') : __('create_tag_description') }}
            </flux:subheading>
        </div>

        @if ($tag)
            <flux:button icon="trash" variant="ghost" size="sm" wire:click="$set('confirmingDeletion', true)"
                data-test="delete-tag">
                {{ __('Delete') }}
            </flux:button>
        @endif
    </div>

    <form wire:submit="save" class="space-y-6 w-full">
        <x-dashboard.form-error-summary />

        <div class="grid gap-6 lg:grid-cols-3">
            <div class="space-y-6 lg:col-span-6">
                <div class="space-y-2">
                    <flux:input wire:model.live.debounce.500ms="name" :label="__('Name')" autofocus
                        :label:badge="__('required_field')" data-test="tag-name" />
                </div>

                <div class="space-y-2">
                    <div class="flex items-end gap-2">
                        <div class="flex-1">
                            <flux:input wire:model.live.blur="slug" :label="__('Slug')"
                                :label:badge="__('required_field')" :description="__('tag_url_validation_message')"
                                data-test="tag-slug" />
                        </div>
                        @if ($tag)
                            <flux:button type="button" variant="ghost" size="sm" wire:click="regenerateSlug"
                                class="mb-0.5 whitespace-nowrap" data-test="regenerate-slug">
                                {{ __('Regenerate') }}
                            </flux:button>
                        @endif
                    </div>
                </div>

                <div class="space-y-4">
                    <div class="flex items-center justify-end gap-3">
                        <flux:button :href="route('dashboard.tags.index')" wire:navigate variant="filled">
                            {{ __('Cancel') }}
                        </flux:button>
                        <flux:button variant="primary" type="submit" data-test="save-tag">
                            {{ __('save_tag') }}
                        </flux:button>
                    </div>

                    @if ($tag)
                        <flux:separator />

                        <div class="text-sm text-zinc-300 ">
                            Aparece en:
                            <span class="capitalize">

                                @if ($posts_count > 0)
                                    {{ trans_choice(':count post|:count posts', $posts_count) }}
                                @else
                                    {{ __('no_posts_using_tag') }}
                                @endif
                            </span>
                        </div>
                    @endif
                </div>
            </div>


        </div>
    </form>

    @if ($tag)
        <flux:modal name="confirm-tag-deletion" wire:model="confirmingDeletion" focusable class="max-w-lg">
            <div class="space-y-6">
                <div>
                    <flux:heading size="lg">{{ __('delete_tag_confirmation_title') }}</flux:heading>
                    <flux:subheading>
                        {{ __('delete_tag_confirmation_description') }}
                    </flux:subheading>
                </div>

                <div class="flex justify-end space-x-2 rtl:space-x-reverse">
                    <flux:modal.close>
                        <flux:button variant="filled">{{ __('Cancel') }}</flux:button>
                    </flux:modal.close>

                    <flux:button variant="danger" wire:click="delete" data-test="confirm-delete-tag">
                        {{ __('delete_tag') }}
                    </flux:button>
                </div>
            </div>
        </flux:modal>
    @endif
</section>
