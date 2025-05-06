<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Desenvolupament') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('Ejemplo de Estructura de desenvolupament') }}</h3>

                <p class="mb-4">Per que el sistema pugui extreure correctament la informació de desenvolupament, has de estructurar el teu fitxer Excel com es mostra a continuació:</p>

                <div class="mb-6">
                    <h4 class="text-md font-medium text-gray-800 mb-2">Estructura de dades de desenvolupament en Excel</h4>
                    <p class="mb-2">Les cel·les importants i el seu contingut són:</p>

                    <div class="overflow-x-auto mb-6">
                        <table class="min-w-full divide-y divide-gray-200 border border-gray-300">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-r border-gray-300">
                                        Data
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-r border-gray-300">
                                        Agents que intervenen
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-r border-gray-300">
                                        Temes tractats
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Acords i compromisos
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <!-- Exemple de 10 files -->
                                @for ($i = 1; $i <= 10; $i++)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 border-r border-gray-300">
                                            B{{ $i + 4 }}
                                            <div class="text-xs text-gray-500">
                                                ${desenvolupament_data#{{ $i }}}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 border-r border-gray-300">
                                            C{{ $i + 4 }}
                                            <div class="text-xs text-gray-500">
                                                ${desenvolupament_agents#{{ $i }}}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 border-r border-gray-300">
                                            D{{ $i + 4 }}
                                            <div class="text-xs text-gray-500">
                                                ${desenvolupament_temes#{{ $i }}}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            E{{ $i + 4 }}
                                            <div class="text-xs text-gray-500">
                                                ${desenvolupament_acords#{{ $i }}}
                                            </div>
                                        </td>
                                    </tr>
                                @endfor
                            </tbody>
                        </table>
                    </div>

                    <div class="bg-blue-50 p-4 rounded-md mb-6">
                        <h4 class="text-sm font-medium text-blue-700 mb-2">{{ __('Nota sobre el format') }}</h4>
                        <ul class="list-disc list-inside text-sm text-blue-600 space-y-1">
                            <li>Les dates han d'estar en format dd/mm/yyyy</li>
                            <li>Pots afegir fins a 10 reunions de desenvolupament</li>
                            <li>Cada cel·la pot contenir text descriptiu sense límit específic</li>
                            <li>És important mantenir la estructura de columnes (Data, Agents, Temes, Acords)</li>
                        </ul>
                    </div>
                </div>

                <div class="flex justify-end">
                    <a href="{{ route('excel-word.index') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 focus:outline-none focus:border-indigo-700 focus:ring focus:ring-indigo-200 active:bg-indigo-600 transition">
                        {{ __('Volver') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
