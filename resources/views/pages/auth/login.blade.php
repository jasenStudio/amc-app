<x-layouts::auth :title="__('Log in')">
    <div class="flex flex-col gap-6">
        <x-auth-header :title="__('Inicia sesión con tu cuenta')" :description="__('Ingresa tu correo y constraseña abajo para inciar sesión')" />

        <!-- Session Status -->
        <x-auth-session-status class="text-center" :status="session('status')" />


        <form method="POST" action="{{ route('login.store') }}" class="flex flex-col gap-6">
            @csrf

            <!-- Email Address -->
            <flux:input name="email" :label="__('Correo electronico')" :value="old('email')" type="email" required
                autofocus autocomplete="email" placeholder="email@example.com" />

            <!-- Password -->
            <div class="relative">
                <flux:input name="password" :label="__('Contraseña')" type="password" required
                    autocomplete="current-password" :placeholder="__('Contrasena')" viewable />

                @if (Route::has('password.request'))
                    <flux:link class="absolute top-0 text-sm inset-e-0" :href="route('password.request')" wire:navigate>
                        {{ __('Forgot your password?') }}
                    </flux:link>
                @endif
            </div>

            <!-- Remember Me -->
            <flux:checkbox name="remember" :label="__('Recuerdame')" :checked="old('remember')" />

            <div class="flex items-center justify-end">
                <flux:button variant="primary" type="submit" class="w-full" data-test="login-button">
                    {{ __('Iniciar Sesion') }}
                </flux:button>
            </div>
        </form>

    </div>
</x-layouts::auth>
