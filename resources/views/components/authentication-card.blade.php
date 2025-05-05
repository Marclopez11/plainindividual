<div class="min-h-screen flex flex-col md:flex-row">
    <!-- Left side - Decorative side with gradient background and content -->
    <div class="hidden md:flex md:w-1/2 bg-gradient-to-br from-blue-500 via-indigo-600 to-purple-700 p-10 flex-col justify-center items-center text-white">
        <div class="max-w-md mx-auto">
            <div class="mb-8">
        {{ $logo }}
            </div>
            <h1 class="text-4xl font-bold mb-6">Plan de Soporte Individualizado</h1>
            <p class="text-xl mb-8">Una herramienta para crear y gestionar planes de apoyo personalizados para cada estudiante.</p>
            <div class="hidden md:block">
                <svg class="w-full max-w-sm opacity-30" viewBox="0 0 500 500" xmlns="http://www.w3.org/2000/svg">
                    <path fill="currentColor" d="M488.6 250.2c0 11.8-1.8 23.2-5.2 34.2s-8.2 21.3-14.3 30.4-13.3 17.4-21.7 24.3-17.6 12.4-27.7 16.3-20.8 6.9-32.1 8.9-23 3-34.7 3-24.4-1-36.1-3-23.9-4.9-35.1-8.9-21.5-9.4-30.9-16.3-17.9-15.2-24.8-24.3-12.4-19.4-16.5-30.4-6.2-22.4-6.2-34.2 1.8-23.2 5.2-34.2 8.2-21.3 14.3-30.4 13.3-17.4 21.7-24.3 17.6-12.4 27.7-16.3 20.8-6.9 32.1-8.9 23-3 34.7-3 24.4 1 36.1 3 23.9 4.9 35.1 8.9 21.5 9.4 30.9 16.3 17.9 15.2 24.8 24.3 12.4 19.4 16.5 30.4 6.1 22.4 6.1 34.2zm-59.2-107.6c20.8 12.2 37.3 30.4 48.8 54.8s17.2 49.1 17.2 74c0 24.9-5.7 49.6-17.2 74s-28 42.6-48.8 54.8-42.5 18.3-65 18.3-44.2-6.1-65-18.3-37.3-30.4-48.8-54.8-17.2-49.1-17.2-74c0-24.9 5.7-49.6 17.2-74s28-42.6 48.8-54.8 42.5-18.3 65-18.3 44.2 6.1 65 18.3z"/>
                </svg>
            </div>
        </div>
    </div>

    <!-- Right side - Form side -->
    <div class="w-full md:w-1/2 flex flex-col justify-center items-center bg-white p-6">
        <div class="w-full max-w-md">
            <div class="md:hidden flex justify-center mb-6">
                {{ $logo }}
            </div>

            <div class="bg-white shadow-xl rounded-lg p-8 border border-gray-100">
        {{ $slot }}
            </div>
        </div>
    </div>
</div>
