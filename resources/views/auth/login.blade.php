<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-6">
        @csrf

        <!-- Email Address -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="mt-1 block w-full bg-surface-2 border-border text-text placeholder-muted focus:border-primary focus:ring-primary" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="you@example.com" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div>
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" class="mt-1 block w-full bg-surface-2 border-border text-text placeholder-muted focus:border-primary focus:ring-primary"
                            type="password"
                            name="password"
                            required autocomplete="current-password"
                            placeholder="••••••••" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="flex items-center justify-between">
            <label for="remember" class="flex items-center gap-2">
                <input id="remember" type="checkbox" name="remember" class="rounded border-border bg-surface-2 text-primary focus:ring-primary focus:ring-offset-0">
                <span class="text-sm text-muted">{{ __('Remember me') }}</span>
            </label>

            @if (Route::has('password.request'))
                <a class="text-sm text-primary hover:underline" href="{{ route('password.request') }}">
                    {{ __('Forgot password?') }}
                </a>
            @endif
        </div>

        <!-- Submit Button -->
        <button type="submit" class="btn btn-primary w-full">
            {{ __('Log in') }}
        </button>

        <!-- Demo Accounts Info -->
        <div class="card bg-surface-2/50 p-4 text-sm">
            <p class="font-medium text-text mb-2">Demo Accounts:</p>
            <ul class="space-y-1 text-muted">
                <li>• superadmin@example.com / password</li>
                <li>• admin@example.com / password</li>
                <li>• staff@example.com / password</li>
            </ul>
        </div>
    </form>
</x-guest-layout>
