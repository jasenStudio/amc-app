<details class="rounded-lg border border-zinc-200 dark:border-zinc-700">
    <summary class="cursor-pointer px-4 py-3 text-sm font-medium">{{ __('SEO') }}</summary>
    <div class="space-y-3 p-4">
        <flux:input wire:model="seo_title" :label="__('SEO title')"
            :description="__('Falls to the visible title when empty.')" />
        <flux:textarea wire:model="seo_description" :label="__('SEO description')"
            :description="__('Falls to the excerpt when empty.')" rows="3" />
        <flux:input wire:model="seo_image" :label="__('SEO image URL')"
            :description="__('Falls to the cover image when empty.')" />
    </div>
</details>
