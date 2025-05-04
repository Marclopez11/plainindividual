<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            <x-authentication-card-logo />
        </x-slot>

        <div class="text-center mb-8">
            <h2 class="text-3xl font-bold text-gray-800">¡Bienvenido!</h2>
            <p class="text-gray-600 mt-2">Inicia sesión para acceder a tu cuenta</p>
        </div>

        <x-validation-errors class="mb-4 bg-red-50 p-3 rounded-lg text-sm" />

        @session('status')
            <div class="mb-4 p-3 bg-green-50 text-green-700 rounded-lg font-medium text-sm">
                {{ $value }}
            </div>
        @endsession

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="space-y-5">
                <div>
                    <x-label for="email" value="{{ __('Correo electrónico') }}" class="text-gray-700 font-semibold" />
                    <div class="mt-1 relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <x-input id="email" class="block w-full pl-10 border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 rounded-md shadow-sm transition" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="tu@email.com" />
                    </div>
                </div>

                <div>
                    <div class="flex items-center justify-between">
                        <x-label for="password" value="{{ __('Contraseña') }}" class="text-gray-700 font-semibold" />
                        @if (Route::has('password.request'))
                            <a class="text-sm text-blue-600 hover:text-blue-800 font-medium" href="{{ route('password.request') }}">
                                {{ __('¿Olvidaste la contraseña?') }}
                            </a>
                        @endif
                    </div>
                    <div class="mt-1 relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                            </svg>
                        </div>
                        <x-input id="password" class="block w-full pl-10 border-gray-300 focus:border-blue-500 focus:ring focus:ring-blue-200 focus:ring-opacity-50 rounded-md shadow-sm transition" type="password" name="password" required autocomplete="current-password" placeholder="••••••••" />
                    </div>
                </div>

                <div class="flex items-center">
                    <label for="remember_me" class="flex items-center">
                        <x-checkbox id="remember_me" name="remember" class="text-blue-600 focus:ring-blue-500" />
                        <span class="ml-2 text-sm text-gray-600">{{ __('Recordar sesión') }}</span>
                    </label>
                </div>

                <div>
                    <x-button class="w-full justify-center py-3 bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 focus:ring-blue-500 transition">
                        {{ __('Iniciar sesión') }}
                    </x-button>
                </div>
            </div>
        </form>

        <div class="mt-6 text-center text-sm text-gray-600">
            ¿No tienes una cuenta?
            <a href="{{ route('register') }}" class="font-medium text-blue-600 hover:text-blue-800">
                Regístrate aquí
            </a>
        </div>
    </x-authentication-card>
</x-guest-layout>
