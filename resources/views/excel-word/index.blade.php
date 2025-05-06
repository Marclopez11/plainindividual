<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Convertir Excel a Word') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('Generar Word desde Excel') }}</h3>

                <p class="mb-4">{{ __('Selecciona tu archivo Excel para generar un documento Word automáticamente.') }}</p>

                <form action="{{ route('excel-word.process') }}" method="POST" enctype="multipart/form-data" class="mb-6" id="excelForm">
                    @csrf

                    @if (session('error'))
                        <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-red-500" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-red-700">
                                        {{ session('error') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if (session('success'))
                        <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-green-500" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-green-700">
                                        {{ session('success') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="mt-4 bg-gray-50 rounded-md p-6">
                        <div class="mb-4">
                            <label for="excel_file" class="block text-sm font-medium text-gray-700 mb-2">{{ __('Archivo Excel (.xlsx, .xls)') }}</label>
                            <input type="file" name="excel_file" id="excel_file" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-medium file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 focus:outline-none" required>
                            <p class="mt-1 text-xs text-gray-500">{{ __('Selecciona un archivo Excel con los datos en las celdas específicas requeridas') }}</p>
                        </div>

                        <div class="bg-gray-50 p-4 rounded-md">
                            <h4 class="text-sm font-medium text-gray-700 mb-2">{{ __('Información importante') }}</h4>
                            <ul class="list-disc list-inside text-sm text-gray-600 space-y-1">
                                <li>{{ __('El sistema leerá celdas específicas de tu Excel (C6-C9, E6-E9, C13-C18, E13-E16)') }}</li>
                                <li>{{ __('También puede leer celdas de justificación (I7-I13, J12)') }}</li>
                                <li>{{ __('Nuevos campos añadidos: I16-J16 (competencias), J18 (puntos fuertes), J19 (puntos a mejorar), J22 (intereses)') }}</li>
                                <li>{{ __('Justificación breve desde la hoja "Portada - Concreció del PI" (celdas C15:F15)') }}</li>
                                <li>{{ __('Objetivos y criterios de evaluación desde la hoja "CE_Àrees/Matèries" (celdas E4:E25 y F4:F25)') }}</li>
                                <li>{{ __('Objetivos y criterios transversales desde la hoja "CE_Transversals" (celdas E4:E25 y F4:F25)') }}</li>
                                <li>{{ __('Datos de la tabla Sabers: Área/Materia (B4:B35), Bloque de saberes (C4:C35) y Saber (D4:D35)') }}</li>
                                <li>{{ __('Asegúrate de que los datos estén ubicados en las celdas correctas') }}</li>
                                <li>{{ __('Se generará un documento Word personalizado con tus datos') }}</li>
                            </ul>
                        </div>

                        <div class="bg-yellow-50 p-4 rounded-md mt-4">
                            <h4 class="text-sm font-medium text-yellow-700 mb-2">{{ __('Nota sobre el procesamiento') }}</h4>
                            <p class="text-sm text-yellow-600">
                                {{ __('El procesamiento de archivos Excel complejos puede tardar hasta 5 minutos. Por favor, sé paciente y no cierres la ventana mientras se procesa tu archivo.') }}
                            </p>
                        </div>

                        <div class="mt-6 flex items-center">
                            <button type="submit" id="submitBtn" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 focus:outline-none focus:border-indigo-700 focus:ring focus:ring-indigo-200 active:bg-indigo-600 transition">
                                {{ __('Procesar Excel') }}
                            </button>
                            <a href="{{ route('excel-word.ejemplo') }}" class="ml-4 inline-flex items-center text-sm text-indigo-600 hover:text-indigo-900 focus:outline-none transition">
                                <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                {{ __('Ver ejemplo de estructura Excel') }}
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Loading Modal -->
    <div id="loadingModal" class="fixed inset-0 flex items-center justify-center z-50 hidden">
        <div class="absolute inset-0 bg-black opacity-50"></div>
        <div class="bg-white p-8 rounded-lg shadow-xl z-10 max-w-md mx-auto relative">
            <div class="flex flex-col items-center">
                <div class="animate-spin rounded-full h-16 w-16 border-t-4 border-b-4 border-indigo-600 mb-6"></div>
                <h3 class="text-xl font-medium text-gray-900 mb-3">{{ __('Procesando Excel') }}</h3>
                <p class="text-gray-600 text-center mb-4">{{ __('Estamos generando tu documento Word. Por favor, espera un momento...') }}</p>
                <div class="w-full bg-gray-200 rounded-full h-2.5 mb-1">
                    <div id="progressBar" class="bg-indigo-600 h-2.5 rounded-full w-0 transition-all duration-300"></div>
                </div>
                <p class="text-xs text-gray-500 mt-1" id="loadingText">{{ __('Extrayendo datos del Excel...') }}</p>
                <p class="text-xs text-gray-400 mt-3 hidden" id="closeModalText">{{ __('Puedes cerrar esta ventana manualmente cuando se inicie la descarga') }}</p>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('excelForm');
            const loadingModal = document.getElementById('loadingModal');
            const progressBar = document.getElementById('progressBar');
            const loadingText = document.getElementById('loadingText');
            const closeModalText = document.getElementById('closeModalText');
            const loadingSteps = [
                '{{ __("Extrayendo datos del Excel...") }}',
                '{{ __("Procesando la información...") }}',
                '{{ __("Aplicando formato al documento Word...") }}',
                '{{ __("Finalizando el documento...") }}',
                '{{ __("Preparando la descarga...") }}'
            ];
            let currentStep = 0;
            let progressInterval;
            let downloadStarted = false;

            // Agregar botón para cerrar manualmente el modal
            const modalContent = document.querySelector('#loadingModal .bg-white');
            if (modalContent) {
                const closeButton = document.createElement('button');
                closeButton.className = 'absolute top-2 right-2 p-1 rounded-full hover:bg-gray-200 focus:outline-none';
                closeButton.innerHTML = '<svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>';
                closeButton.addEventListener('click', hideLoadingModal);
                modalContent.appendChild(closeButton);
            }

            form.addEventListener('submit', function(e) {
                const fileInput = document.getElementById('excel_file');

                if (fileInput.files.length > 0) {
                    // Show the loading modal
                    loadingModal.classList.remove('hidden');
                    downloadStarted = false;
                    closeModalText.classList.add('hidden');

                    // Simulate progress with loading steps
                    simulateProgress();
                }
            });

            function simulateProgress() {
                let progress = 0;

                progressInterval = setInterval(function() {
                    progress += Math.floor(Math.random() * 10) + 1;
                    if (progress >= 100) {
                        progress = 100;
                        loadingText.textContent = '{{ __("¡Descarga iniciada!") }}';

                        // Marcar como descarga iniciada
                        downloadStarted = true;

                        // Mostrar texto para cerrar manualmente
                        closeModalText.classList.remove('hidden');

                        // Programar cierre automático después de un tiempo
                        setTimeout(function() {
                            hideLoadingModal();
                        }, 5000);
                    }

                    // Update progress bar
                    progressBar.style.width = progress + '%';

                    // Update loading text periodically
                    if (progress > currentStep * 20 && currentStep < loadingSteps.length) {
                        loadingText.textContent = loadingSteps[currentStep];
                        currentStep++;
                    }
                }, 500);
            }

            function hideLoadingModal() {
                // Ocultar el modal
                loadingModal.classList.add('hidden');
                // Resetear la barra de progreso y el texto
                setTimeout(function() {
                    progressBar.style.width = '0%';
                    loadingText.textContent = loadingSteps[0];
                    closeModalText.classList.add('hidden');
                    currentStep = 0;
                }, 500);

                // Limpiar el intervalo si existe
                if (progressInterval) {
                    clearInterval(progressInterval);
                    progressInterval = null;
                }
                downloadStarted = false;
            }

            // Detectar cuando se completa la descarga y ocultar el modal
            window.addEventListener('focus', function() {
                // Si el modal está visible y la descarga se ha iniciado
                if (!loadingModal.classList.contains('hidden') && downloadStarted) {
                    hideLoadingModal();
                }
            });

            // También detectar navegación o escape para ocultar el modal
            document.addEventListener('keydown', function(event) {
                if (event.key === 'Escape' && !loadingModal.classList.contains('hidden')) {
                    hideLoadingModal();
                }
            });
        });
    </script>
</x-app-layout>
