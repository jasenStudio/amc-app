@php use LakM\Commenter\GuestModeRateLimiter; @endphp

<div @logout.window="$wire.$refresh()">
    <div class="lakm_commenter w-full" method="POST">
        <x-honeypot wire:model="honeyPostData" />

        @if ($guestEmailVerified)
            <div x-data="{ show: true }">
                <x-commenter::modal>
                    <div class="text-green-600 text-lg text-center font-bold p-4">Your email verified successfully!</div>
                </x-commenter::modal>
            </div>
        @endif
        @if ($model->guestModeEnabled() && !$this->secureGuestMode->enabled())
            <div class="flex flex-col gap-x-8 sm:flex-row">
                <div class="flex w-full flex-col">
                    <x-commenter::input wire:model="name" :shouldDisable="$limitExceeded" placeholder="{{ __('Comment as') }}" />
                    <div class="min-h-6">
                        @if ($errors->has('name'))
                            <span class="align-top text-xs text-red-500 sm:text-sm">
                                {{ __($errors->first('name')) }}
                            </span>
                        @endif
                    </div>
                </div>
                @if (config('commenter.guest_mode.email_enabled'))
                    <div class="flex w-full flex-col">
                        <x-commenter::input wire:model="email" :shouldDisable="$limitExceeded" type="email"
                            placeholder="{{ __('Email') }}" />
                        <div class="min-h-6">
                            @if ($errors->has('email'))
                                <span class="align-top text-xs text-red-500 sm:text-sm">
                                    {{ __($errors->first('email')) }}
                                </span>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        @endif

        <div>
            <livewire:comments-editor wire:model="text" :$editorId :$guestModeEnabled :$disableEditor />
        </div>

        <div class="min-h-6">
            <div x-cloak x-data="message(@js($editorId))" @comment-created.window="show($event.detail.id)">
                <span x-show="showMsg" x-transition class="align-top text-xs text-green-500 sm:text-sm">
                    @if ($approvalRequired)
                        {{ __('Comment created and will be displayed once approved.') }}
                    @else
                        {{ __('Comment created.') }}
                    @endif
                </span>
            </div>
            <div>
                @if ($errors->has('text'))
                    <span class="align-top text-xs text-red-500 sm:text-sm">{{ __($errors->first('text')) }}</span>
                @endif
            </div>
        </div>
        @if (!$limitExceeded)
            @if (!$this->guestModeEnabled && $loginRequired)
                <div>
                    <span x-data="{ pageUrl: window.location.href }">
                        {{ __('Please') }}
                        <x-commenter::link wire:click.prevent="redirectToLogin(pageUrl)"
                            class="font-bold text-blue-600">
                            {{ __('login') }}
                        </x-commenter::link>
                        {{ __('to comment !') }}
                    </span>
                </div>
            @elseif($verifyLinkSent)
                <span class="text-green-400">Verify link was sent to your email address</span>
            @elseif(!$this->secureGuestMode->allowed())
                <div x-data="{ showEmailField: false, pageUrl: window.location.href }" id="verify-email-button">
                    <span x-show="!showEmailField" x-transition>
                        {{ __('Please') }}
                        <x-commenter::link @click="showEmailField=true" class="font-bold text-blue-600" type="button">
                            {{ __('verify your email') }}
                        </x-commenter::link>
                        {{ __('to comment !') }}
                    </span>

                    <div x-show="showEmailField" x-transition class="flex flex-col gap-y-2">
                        <div class="flex flex-col gap-x-8 sm:flex-row">
                            <div class="flex w-full flex-col">
                                <x-commenter::input wire:model="name" placeholder="{{ __('Comment as') }}" />
                                <div class="min-h-6">
                                    @if ($errors->has('name'))
                                        <span class="align-top text-xs text-red-500 sm:text-sm">
                                            {{ __($errors->first('name')) }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                            <div class="flex w-full flex-col">
                                <x-commenter::input wire:model="email" type="email"
                                    placeholder="{{ __('Email') }}" />
                                <div class="min-h-6">
                                    @if ($errors->has('email'))
                                        <span class="align-top text-xs text-red-500 sm:text-sm">
                                            {{ __($errors->first('email')) }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        @if (!$rateLimitExceeded)
                            <div wire:click="sendVerifyLink(pageUrl)">
                                <x-commenter::button size="sm" loadingTarget="sendVerifyLink">
                                    Send Link
                                </x-commenter::button>
                            </div>
                        @else
                            <div x-cloak x-data="countdown(@js(GuestModeRateLimiter::$decaySeconds))"
                                @counter-finished.window="$wire.set('rateLimitExceeded', false)">
                                <span x-init="start" class="text-red-600">Max limit exceeded
                                    ({{ GuestModeRateLimiter::$maxAttempts }}) try again in: <span
                                        x-text="count"></span></span>
                            </div>
                        @endif
                    </div>
                </div>
            @else
                <div class="flex items-start gap-3 mb-4">
                    <input type="checkbox" wire:model="privacy_accepted" id="comment-privacy-accepted" required
                        class="mt-1 h-4 w-4 shrink-0 rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                    <label for="comment-privacy-accepted" class="text-xs leading-relaxed text-zinc-600">
                        Acepto la <a href="{{ route('privacy.policy') }}" target="_blank" rel="noopener noreferrer"
                            class="text-blue-600 underline underline-offset-2 hover:text-blue-800">Política de Privacidad y Tratamiento de Datos Personales</a>.
                        Autorizo el uso de mis datos exclusivamente para gestionar mi solicitud o suscripción de acuerdo con la Ley 1581 de 2012.
                    </label>
                </div>
                @error('privacy_accepted')
                    <span class="align-top text-xs text-red-500 sm:text-sm mb-4 block">{{ __('Debes aceptar la política de privacidad para continuar') }}</span>
                @enderror

                <x-commenter::button wire:click="create" loadingTarget="create" class="w-full sm:w-auto">
                    {{ __('Create') }}
                </x-commenter::button>
            @endif
        @else
            <div>
                <span class="text-red-500">
                    {{ __('Allowed comment limit') }} ({{ $model->getCommentLimit() }}) {{ __('exceeded !') }}
                </span>
            </div>
        @endif
    </div>
</div>
