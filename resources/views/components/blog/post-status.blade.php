<flux:card>
    <div class="space-y-4">
        <flux:select wire:model="status" :label="__('Status')" :label:badge="__('required_field')" data-test="post-status">
            <flux:select.option value="draft">{{ __('Draft') }}</flux:select.option>
            <flux:select.option value="published">{{ __('Published') }}</flux:select.option>
        </flux:select>

        <flux:input wire:model.live.blur="published_at" :label="__('Published at')" type="datetime-local" />

        <flux:switch wire:model="featured" :label="__('Featured on home')" />

        <flux:input wire:model.number="order" :label="__('Order')" type="number"
            min="0" />
    </div>
</flux:card>
