<div x-data="{ submitting: @entangle('submitting') }" class="rounded-lg bg-white p-8 shadow-lg">
    @if ($submitted)
        <div class="text-center">
            <p class="text-lg font-semibold text-amc-blue">{{ __('Thank you! We will contact you soon.') }}</p>
        </div>
    @else
        <form
            @submit.prevent="
                submitting = true;
                grecaptcha.ready(() => {
                    grecaptcha.execute('{{ config('services.recaptcha.site_key') }}', { action: 'contact' })
                        .then(token => $wire.submit(token))
                        .catch(() => { submitting = false; });
                });
            "
            class="space-y-6"
        >
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                <div>
                    <label for="name" class="block text-xs font-semibold uppercase tracking-[0.2em] text-amc-gray-text">
                        {{ __('Full name') }}
                    </label>
                    <input wire:model="name" type="text" id="name"
                        class="mt-2 w-full border-b border-slate-300 bg-transparent py-2 text-base text-amc-blue focus:border-amc-orange focus:outline-none">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="company" class="block text-xs font-semibold uppercase tracking-[0.2em] text-amc-gray-text">
                        {{ __('Company / Sector') }}
                    </label>
                    <input wire:model="company" type="text" id="company"
                        class="mt-2 w-full border-b border-slate-300 bg-transparent py-2 text-base text-amc-blue focus:border-amc-orange focus:outline-none">
                    @error('company')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label for="service_id" class="block text-xs font-semibold uppercase tracking-[0.2em] text-amc-gray-text">
                    {{ __('Service of interest') }}
                </label>
                <select wire:model="service_id" id="service_id"
                    class="mt-2 w-full border-b border-slate-300 bg-transparent py-2 text-base text-amc-blue focus:border-amc-orange focus:outline-none">
                    <option value="">-- {{ __('Select a service') }} --</option>
                    @foreach ($services as $service)
                        <option value="{{ $service->id }}">{{ $service->title }}</option>
                    @endforeach
                </select>
                @error('service_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="message" class="block text-xs font-semibold uppercase tracking-[0.2em] text-amc-gray-text">
                    {{ __('Message') }}
                </label>
                <textarea wire:model="message" id="message" rows="5"
                    class="mt-2 w-full border border-slate-300 bg-amc-gray-bg p-3 text-base text-amc-blue focus:border-amc-orange focus:outline-none"></textarea>
                @error('message')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit"
                :disabled="submitting"
                class="w-full bg-amc-blue py-4 text-sm font-semibold uppercase tracking-[0.2em] text-white transition hover:bg-amc-blue-dark disabled:cursor-not-allowed disabled:opacity-50">
                <span x-show="!submitting">{{ __('Send request') }}</span>
                <span x-show="submitting">{{ __('Sending...') }}</span>
            </button>
        </form>
    @endif

    @push('scripts')
        <script src="https://www.google.com/recaptcha/api.js?render={{ config('services.recaptcha.site_key') }}"></script>
    @endpush
</div>
