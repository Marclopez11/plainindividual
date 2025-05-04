<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Detalls del Pla de Suport Individualitzat') }}
            </h2>
            <div class="flex space-x-2">
                <a href="{{ route('support-plans.exportToWord', $supportPlan->id) }}" class="inline-flex items-center px-4 py-2 bg-blue-100 border border-transparent rounded-md font-semibold text-xs text-blue-800 uppercase tracking-widest hover:bg-blue-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    {{ __('Exportar a Word') }}
                </a>
              {{--   <a href="{{ route('support-plans.exportToExcel', $supportPlan->id) }}" class="inline-flex items-center px-4 py-2 bg-green-100 border border-transparent rounded-md font-semibold text-xs text-green-800 uppercase tracking-widest hover:bg-green-200 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    {{ __('Exportar a Excel') }}
                        </a> --}}
                    </div>
                </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                <table class="w-full border-collapse border border-gray-800 mb-8" style="table-layout: fixed;">
                    <!-- DADES PERSONALS -->
                    <tr>
                        <td colspan="4" class="p-3 font-bold text-white text-xl" style="background-color: #6ab0e6; border: 1px solid #000;">
                            DADES PERSONALS
                        </td>
                    </tr>

                    <!-- Primera fila: Nom de l'alumne/a - Data de naixement -->
                    <tr>
                        <td class="p-2 font-bold border border-gray-800" style="width: 25%;">Nom de l'alumne/a:</td>
                        <td class="p-2 border border-gray-800" style="width: 25%;">
                            {{ $supportPlan->student_name }}
                        </td>
                        <td class="p-2 font-bold border border-gray-800" style="width: 25%;">Data de naixement:</td>
                        <td class="p-2 border border-gray-800" style="width: 25%;">
                            {{ $supportPlan->birth_date ? $supportPlan->birth_date->format('d/m/Y') : '' }}
                        </td>
                    </tr>

                    <!-- Segunda fila: Lloc de naixement - Nom dels tutors -->
                    <tr>
                        <td class="p-2 font-bold border border-gray-800">Lloc de naixement:</td>
                        <td class="p-2 border border-gray-800">
                            {{ $supportPlan->birth_place }}
                        </td>
                        <td class="p-2 font-bold border border-gray-800">Nom dels tutors i/o tutores legals:</td>
                        <td class="p-2 border border-gray-800">
                            {{ $supportPlan->tutor_name }}
                        </td>
                    </tr>

                    <!-- Tercera fila: Adreça - Telèfon -->
                    <tr>
                        <td class="p-2 font-bold border border-gray-800">Adreça:</td>
                        <td class="p-2 border border-gray-800">
                            {{ $supportPlan->address }}
                        </td>
                        <td class="p-2 font-bold border border-gray-800">Telèfon:</td>
                        <td class="p-2 border border-gray-800">
                            {{ $supportPlan->phone }}
                        </td>
                    </tr>

                    <!-- Cuarta fila: Llengua d'ús habitual - Altres llengües -->
                    <tr>
                        <td class="p-2 font-bold border border-gray-800">Llengua d'ús habitual:</td>
                        <td class="p-2 border border-gray-800">
                            {{ $supportPlan->usual_language }}
                        </td>
                        <td class="p-2 font-bold border border-gray-800">Altres llengües que coneix:</td>
                        <td class="p-2 border border-gray-800">
                            {{ $supportPlan->other_languages }}
                        </td>
                    </tr>

                    <!-- Fila vacía -->
                    <tr>
                        <td colspan="4" class="p-1 border border-gray-800 h-6"></td>
                    </tr>

                    <!-- DADES ESCOLARS -->
                    <tr>
                        <td colspan="4" class="p-3 font-bold text-white text-xl" style="background-color: #6ab0e6; border: 1px solid #000;">
                            DADES ESCOLARS
                        </td>
                    </tr>

                    <!-- Primera fila de datos escolares: Curs - Grup -->
                    <tr>
                        <td class="p-2 font-bold border border-gray-800">Curs:</td>
                        <td class="p-2 border border-gray-800">
                            {{ $supportPlan->course }}
                        </td>
                        <td class="p-2 font-bold border border-gray-800">Grup:</td>
                        <td class="p-2 border border-gray-800">
                            {{ $supportPlan->group }}
                        </td>
                    </tr>

                    <!-- Segunda fila de datos escolares: Tutor - Data d'arribada -->
                    <tr>
                        <td class="p-2 font-bold border border-gray-800">Tutor/a:</td>
                        <td class="p-2 border border-gray-800">
                            {{ $supportPlan->teacher_name }}
                        </td>
                        <td class="p-2 font-bold border border-gray-800">Data d'arribada a Catalunya:</td>
                        <td class="p-2 border border-gray-800">
                            {{ $supportPlan->catalonia_arrival_date ? $supportPlan->catalonia_arrival_date->format('d/m/Y') : '' }}
                        </td>
                    </tr>

                    <!-- Tercera fila de datos escolares: Incorporació centre - Incorporació sistema -->
                    <tr>
                        <td class="p-2 font-bold border border-gray-800">Data d'incorporació al centre:</td>
                        <td class="p-2 border border-gray-800">
                            {{ $supportPlan->school_incorporation_date ? $supportPlan->school_incorporation_date->format('d/m/Y') : '' }}
                        </td>
                        <td class="p-2 font-bold border border-gray-800">Data d'incorporació al sistema educatiu català:</td>
                        <td class="p-2 border border-gray-800">
                            {{ $supportPlan->educational_system_date ? $supportPlan->educational_system_date->format('d/m/Y') : '' }}
                        </td>
                    </tr>

                    <!-- Cuarta fila de datos escolares: Centres anteriors - Escolarització -->
                    <tr>
                        <td class="p-2 font-bold border border-gray-800">Centres on ha estat matriculat anteriorment:</td>
                        <td class="p-2 border border-gray-800">
                            {{ $supportPlan->previous_schools }}
                        </td>
                        <td class="p-2 font-bold border border-gray-800">Escolarització prèvia:</td>
                        <td class="p-2 border border-gray-800">
                            {{ $supportPlan->previous_schooling }}
                        </td>
                    </tr>

                    <!-- Quinta fila: Retenció de curs (fila completa) -->
                    <tr>
                        <td class="p-2 font-bold border border-gray-800">Retenció de curs:</td>
                        <td class="p-2 border border-gray-800" colspan="3">
                            {{ $supportPlan->course_retention }}
                        </td>
                    </tr>

                    <!-- Sexta fila: Altres informacions (fila completa) -->
                    <tr>
                        <td class="p-2 font-bold border border-gray-800">Altres informacions d'interès:</td>
                        <td class="p-2 border border-gray-800" colspan="3">
                            {{ $supportPlan->other_data }}
                        </td>
                    </tr>
                </table>

                <div class="flex justify-end space-x-3 mt-6">
                    <a href="{{ route('support-plans.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 focus:bg-gray-300 active:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        {{ __('Volver') }}
                    </a>

                    <a href="{{ route('support-plans.edit', $supportPlan->id) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        {{ __('Editar') }}
                    </a>

                    <form action="{{ route('support-plans.destroy', $supportPlan->id) }}" method="POST" class="inline-block">
                            @csrf
                            @method('DELETE')
                        <button type="submit" onclick="return confirm('¿Estás seguro de que deseas eliminar este plan de soporte?');" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-800 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                            {{ __('Eliminar') }}
                        </button>
                        </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
