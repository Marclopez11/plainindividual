<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Crear Plan de Soporte Individualizado') }}
            </h2>
                </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <form action="{{ route('support-plans.store') }}" method="POST">
                        @csrf

                    <!-- Campos obligatorios y selección de equipo -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                        <div>
                            <label for="name" class="block font-medium text-sm text-gray-700 mb-1">Nombre del plan <span class="text-red-500">*</span></label>
                            <input type="text" name="name" id="name" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                            @error('name')<span class="text-red-500 text-xs mt-1">{{ $message }}</span>@enderror
                        </div>

                        <div>
                            <label for="team_id" class="block font-medium text-sm text-gray-700 mb-1">Asignar a equipo</label>
                            <select name="team_id" id="team_id" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                <option value="">-- Seleccione un equipo (opcional) --</option>
                                @foreach(isset($userTeams) ? $userTeams : [] as $team)
                                    <option value="{{ $team->id }}">{{ $team->name }}</option>
                                @endforeach
                            </select>
                            @error('team_id')<span class="text-red-500 text-xs mt-1">{{ $message }}</span>@enderror
                            </div>
                                </div>

                    <!-- Selector de formato -->
                    <div class="mb-6">
                        <label class="block font-medium text-sm text-gray-700 mb-1">Format del pla</label>
                        <div class="flex space-x-4">
                            <button type="button" class="px-4 py-2 bg-blue-100 border border-transparent rounded-md font-semibold text-xs text-blue-800 uppercase tracking-widest hover:bg-blue-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition active:bg-blue-300" id="format-word" onclick="switchFormat('word')">
                                Format Word
                            </button>
                            <button type="button" class="px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition" id="format-excel" onclick="switchFormat('excel')">
                                Format Excel
                            </button>
                            </div>
                        </div>

                    <div id="word-format-content">
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
                                    <input type="text" name="student_name" class="w-full px-2 py-1 border-0 border-b border-gray-300 bg-gray-50/50 shadow-sm focus:border-indigo-500 focus:bg-white focus:shadow-md focus:ring-0 transition-all duration-200" required>
                                </td>
                                <td class="p-2 font-bold border border-gray-800" style="width: 25%;">Data de naixement:</td>
                                <td class="p-2 border border-gray-800" style="width: 25%;">
                                    <input type="date" name="birth_date" class="w-full px-2 py-1 border-0 border-b border-gray-300 bg-gray-50/50 shadow-sm focus:border-indigo-500 focus:bg-white focus:shadow-md focus:ring-0 transition-all duration-200">
                                </td>
                            </tr>

                            <!-- Segunda fila: Lloc de naixement - Nom dels tutors -->
                            <tr>
                                <td class="p-2 font-bold border border-gray-800">Lloc de naixement:</td>
                                <td class="p-2 border border-gray-800">
                                    <input type="text" name="birth_place" class="w-full px-2 py-1 border-0 border-b border-gray-300 bg-gray-50/50 shadow-sm focus:border-indigo-500 focus:bg-white focus:shadow-md focus:ring-0 transition-all duration-200">
                                </td>
                                <td class="p-2 font-bold border border-gray-800">Nom dels tutors i/o tutores legals:</td>
                                <td class="p-2 border border-gray-800">
                                    <input type="text" name="tutor_name" class="w-full px-2 py-1 border-0 border-b border-gray-300 bg-gray-50/50 shadow-sm focus:border-indigo-500 focus:bg-white focus:shadow-md focus:ring-0 transition-all duration-200">
                                </td>
                            </tr>

                            <!-- Tercera fila: Adreça - Telèfon -->
                            <tr>
                                <td class="p-2 font-bold border border-gray-800">Adreça:</td>
                                <td class="p-2 border border-gray-800">
                                    <input type="text" name="address" class="w-full px-2 py-1 border-0 border-b border-gray-300 bg-gray-50/50 shadow-sm focus:border-indigo-500 focus:bg-white focus:shadow-md focus:ring-0 transition-all duration-200">
                                </td>
                                <td class="p-2 font-bold border border-gray-800">Telèfon:</td>
                                <td class="p-2 border border-gray-800">
                                    <input type="text" name="phone" class="w-full px-2 py-1 border-0 border-b border-gray-300 bg-gray-50/50 shadow-sm focus:border-indigo-500 focus:bg-white focus:shadow-md focus:ring-0 transition-all duration-200">
                                </td>
                            </tr>

                            <!-- Cuarta fila: Llengua d'ús habitual - Altres llengües -->
                            <tr>
                                <td class="p-2 font-bold border border-gray-800">Llengua d'ús habitual:</td>
                                <td class="p-2 border border-gray-800">
                                    <input type="text" name="usual_language" class="w-full px-2 py-1 border-0 border-b border-gray-300 bg-gray-50/50 shadow-sm focus:border-indigo-500 focus:bg-white focus:shadow-md focus:ring-0 transition-all duration-200">
                                </td>
                                <td class="p-2 font-bold border border-gray-800">Altres llengües que coneix:</td>
                                <td class="p-2 border border-gray-800">
                                    <input type="text" name="other_languages" class="w-full px-2 py-1 border-0 border-b border-gray-300 bg-gray-50/50 shadow-sm focus:border-indigo-500 focus:bg-white focus:shadow-md focus:ring-0 transition-all duration-200">
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
                                    <input type="text" name="course" class="w-full px-2 py-1 border-0 border-b border-gray-300 bg-gray-50/50 shadow-sm focus:border-indigo-500 focus:bg-white focus:shadow-md focus:ring-0 transition-all duration-200">
                                </td>
                                <td class="p-2 font-bold border border-gray-800">Grup:</td>
                                <td class="p-2 border border-gray-800">
                                    <input type="text" name="group" class="w-full px-2 py-1 border-0 border-b border-gray-300 bg-gray-50/50 shadow-sm focus:border-indigo-500 focus:bg-white focus:shadow-md focus:ring-0 transition-all duration-200">
                                </td>
                            </tr>

                            <!-- Segunda fila de datos escolares: Tutor - Data d'arribada -->
                            <tr>
                                <td class="p-2 font-bold border border-gray-800">Tutor/a:</td>
                                <td class="p-2 border border-gray-800">
                                    <input type="text" name="teacher_name" class="w-full px-2 py-1 border-0 border-b border-gray-300 bg-gray-50/50 shadow-sm focus:border-indigo-500 focus:bg-white focus:shadow-md focus:ring-0 transition-all duration-200">
                                </td>
                                <td class="p-2 font-bold border border-gray-800">Data d'arribada a Catalunya:</td>
                                <td class="p-2 border border-gray-800">
                                    <input type="date" name="catalonia_arrival_date" class="w-full px-2 py-1 border-0 border-b border-gray-300 bg-gray-50/50 shadow-sm focus:border-indigo-500 focus:bg-white focus:shadow-md focus:ring-0 transition-all duration-200">
                                </td>
                            </tr>

                            <!-- Tercera fila de datos escolares: Incorporació centre - Incorporació sistema -->
                            <tr>
                                <td class="p-2 font-bold border border-gray-800">Data d'incorporació al centre:</td>
                                <td class="p-2 border border-gray-800">
                                    <input type="date" name="school_incorporation_date" class="w-full px-2 py-1 border-0 border-b border-gray-300 bg-gray-50/50 shadow-sm focus:border-indigo-500 focus:bg-white focus:shadow-md focus:ring-0 transition-all duration-200">
                                </td>
                                <td class="p-2 font-bold border border-gray-800">Data d'incorporació al sistema educatiu català:</td>
                                <td class="p-2 border border-gray-800">
                                    <input type="date" name="educational_system_date" class="w-full px-2 py-1 border-0 border-b border-gray-300 bg-gray-50/50 shadow-sm focus:border-indigo-500 focus:bg-white focus:shadow-md focus:ring-0 transition-all duration-200">
                                </td>
                            </tr>

                            <!-- Cuarta fila de datos escolares: Centres anteriores - Escolarització -->
                            <tr>
                                <td class="p-2 font-bold border border-gray-800">Centres on ha estat matriculat anteriorment:</td>
                                <td class="p-2 border border-gray-800">
                                    <input type="text" name="previous_schools" class="w-full px-2 py-1 border-0 border-b border-gray-300 bg-gray-50/50 shadow-sm focus:border-indigo-500 focus:bg-white focus:shadow-md focus:ring-0 transition-all duration-200">
                                </td>
                                <td class="p-2 font-bold border border-gray-800">Escolarització prèvia:</td>
                                <td class="p-2 border border-gray-800">
                                    <input type="text" name="previous_schooling" class="w-full px-2 py-1 border-0 border-b border-gray-300 bg-gray-50/50 shadow-sm focus:border-indigo-500 focus:bg-white focus:shadow-md focus:ring-0 transition-all duration-200">
                                </td>
                            </tr>

                            <!-- Quinta fila: Retenció de curs (fila completa) -->
                            <tr>
                                <td class="p-2 font-bold border border-gray-800">Retenció de curs:</td>
                                <td class="p-2 border border-gray-800" colspan="3">
                                    <input type="text" name="course_retention" class="w-full px-2 py-1 border-0 border-b border-gray-300 bg-gray-50/50 shadow-sm focus:border-indigo-500 focus:bg-white focus:shadow-md focus:ring-0 transition-all duration-200">
                                </td>
                            </tr>

                            <!-- Sexta fila: Altres informacions d'interès -->
                            <tr>
                                <td class="p-2 font-bold border border-gray-800">Altres informacions d'interès:</td>
                                <td class="p-2 border border-gray-800" colspan="3">
                                    <textarea name="other_data" rows="3" class="w-full px-2 py-1 border-0 border-b border-gray-300 bg-gray-50/50 shadow-sm focus:border-indigo-500 focus:bg-white focus:shadow-md focus:ring-0 transition-all duration-200"></textarea>
                                </td>
                            </tr>
                        </table>

                        <!-- JUSTIFICACIÓ -->
                        <table class="w-full border-collapse border border-gray-800 mb-8" style="table-layout: fixed;">
                            <tr>
                                <td colspan="1" class="p-3 font-bold text-white text-xl" style="background-color: #6ab0e6; border: 1px solid #000;">
                                    JUSTIFICACIÓ
                                </td>
                            </tr>
                            <tr>
                                <td class="p-3 border border-gray-800">
                                    <div class="mb-2">
                                        <p class="font-bold mb-3">Motivat per:<sup>1</sup></p>
                                        <div class="space-y-2">
                                            <div class="flex items-start">
                                                <div class="relative flex items-center mt-1 mr-2">
                                                    <input type="checkbox" name="justification_reasons[]" value="informe_reconeixement"
                                                        class="absolute w-5 h-5 opacity-0 cursor-pointer">
                                                    <div class="w-5 h-5 border border-gray-400 flex items-center justify-center bg-white">
                                                        <svg class="hidden w-4 h-4 text-blue-600 check-indicator" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                        </svg>
                                                    </div>
                                                </div>
                                                <label>Informe de reconeixement de necessitat de suport educatiu</label>
                                            </div>
                                            <div class="flex items-start">
                                                <div class="relative flex items-center mt-1 mr-2">
                                                    <input type="checkbox" name="justification_reasons[]" value="avaluacio_psicopedagogica"
                                                        class="absolute w-5 h-5 opacity-0 cursor-pointer">
                                                    <div class="w-5 h-5 border border-gray-400 flex items-center justify-center bg-white">
                                                        <svg class="hidden w-4 h-4 text-blue-600 check-indicator" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                        </svg>
                                                    </div>
                                                </div>
                                                <label>Avaluació psicopedagògica</label>
                                            </div>
                                            <div class="flex items-start">
                                                <div class="relative flex items-center mt-1 mr-2">
                                                    <input type="checkbox" name="justification_reasons[]" value="avaluacio_inicial_nouvingut"
                                                        class="absolute w-5 h-5 opacity-0 cursor-pointer">
                                                    <div class="w-5 h-5 border border-gray-400 flex items-center justify-center bg-white">
                                                        <svg class="hidden w-4 h-4 text-blue-600 check-indicator" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                        </svg>
                                </div>
                            </div>
                                                <label>Resultat de l'avaluació inicial de l'alumne/a nouvingut<sup>2</sup></label>
                                            </div>
                                            <div class="flex items-start">
                                                <div class="relative flex items-center mt-1 mr-2">
                                                    <input type="checkbox" name="justification_reasons[]" value="avaluacio_origen_estranger_aula"
                                                        class="absolute w-5 h-5 opacity-0 cursor-pointer">
                                                    <div class="w-5 h-5 border border-gray-400 flex items-center justify-center bg-white">
                                                        <svg class="hidden w-4 h-4 text-blue-600 check-indicator" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                        </svg>
                                </div>
                            </div>
                                                <label>Avaluació de l'alumne/a d'origen estranger que ja no assisteix a l'aula d'acollida però que rep suport a l'aula ordinària.</label>
                                            </div>
                                            <div class="flex items-start">
                                                <div class="relative flex items-center mt-1 mr-2">
                                                    <input type="checkbox" name="justification_reasons[]" value="avaluacio_origen_estranger_tardana"
                                                        class="absolute w-5 h-5 opacity-0 cursor-pointer">
                                                    <div class="w-5 h-5 border border-gray-400 flex items-center justify-center bg-white">
                                                        <svg class="hidden w-4 h-4 text-blue-600 check-indicator" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                        </svg>
                                </div>
                                                </div>
                                                <label>Avaluació de l'alumne/a d'origen estranger amb necessitats educatives derivades de la incorporació tardana al sistema educatiu.</label>
                                            </div>
                                            <div class="flex items-start">
                                                <div class="relative flex items-center mt-1 mr-2">
                                                    <input type="checkbox" name="justification_reasons[]" value="decisio_comissio"
                                                        class="absolute w-5 h-5 opacity-0 cursor-pointer">
                                                    <div class="w-5 h-5 border border-gray-400 flex items-center justify-center bg-white">
                                                        <svg class="hidden w-4 h-4 text-blue-600 check-indicator" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                        </svg>
                            </div>
                        </div>
                                                <label>Decisió de la comissió d'atenció educativa inclusiva (CAEI)</label>
                                                <input type="text" name="commission_proponent" placeholder="EAP/tutor/docent/família..." class="ml-1 px-2 py-1 border border-gray-300 rounded-md w-auto">
                                                <label class="ml-1">a proposta de</label>
                                                <input type="text" name="commission_motivation" class="ml-1 px-2 py-1 border border-gray-300 rounded-md w-1/3">
                        </div>
                                            <div class="flex items-start">
                                                <div class="relative flex items-center mt-1 mr-2">
                                                    <input type="checkbox" name="justification_reasons[]" value="altres"
                                                        class="absolute w-5 h-5 opacity-0 cursor-pointer">
                                                    <div class="w-5 h-5 border border-gray-400 flex items-center justify-center bg-white">
                                                        <svg class="hidden w-4 h-4 text-blue-600 check-indicator" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                        </svg>
                                </div>
                            </div>
                                                <label>Altres:</label>
                                                <input type="text" name="justification_other" class="ml-2 px-2 py-1 border border-gray-300 rounded-md w-4/5">
                                </div>
                            </div>
                        </div>
                                </td>
                            </tr>
                            <tr>
                                <td class="border border-gray-800">
                                    <div class="grid grid-cols-5">
                                        <div class="p-3 font-bold text-white text-xl col-span-1" style="background-color: #6ab0e6; border: 1px solid #000;">
                                            Breu justificació del PSI
                                        </div>
                                        <div class="p-3 border border-gray-800 col-span-4">
                                            <textarea name="brief_justification" rows="4" class="w-full px-2 py-1 border border-gray-300 rounded-md"></textarea>
                                </div>
                            </div>
                                </td>
                            </tr>
                        </table>

                        <!-- NIVELL ACTUAL DE COMPETÈNCIES (Word Format) -->
                        <table class="w-full border-collapse border border-gray-800 mb-8" style="table-layout: fixed;">
                            <tr>
                                <td colspan="1" class="p-3 font-bold text-white text-xl" style="background-color: #6ab0e6; border: 1px solid #000;">
                                    Nivell actual de competències
                                </td>
                            </tr>
                            <tr>
                                <td class="p-3 border border-gray-800">
                                    <div class="mb-4">
                                        <p class="font-bold mb-2">En Alumne és capaç de:</p>
                                        <textarea name="competencies_alumne_capabilities" rows="3" class="w-full px-2 py-1 border border-gray-300 rounded-md" placeholder="-"></textarea>
                                </div>
                                </td>
                            </tr>
                        </table>

                        <table class="w-full border-collapse border border-gray-800 mb-8" style="table-layout: fixed;">
                            <tr>
                                <td colspan="1" class="p-3 font-bold text-white text-xl" style="background-color: #6ab0e6; border: 1px solid #000;">
                                    Condicions personals i estil d'aprenentatge
                                </td>
                            </tr>
                            <tr>
                                <td class="p-3 border border-gray-800">
                                    <div class="mb-4">
                                        <p class="font-bold mb-2">Punts forts:</p>
                                        <textarea name="learning_strong_points" rows="3" class="w-full px-2 py-1 border border-gray-300 rounded-md" placeholder="- Segueix les dinàmiques més habituals de l'aula.
-"></textarea>
                        </div>

                                    <div class="mb-4">
                                        <p class="font-bold mb-2">Punts de millora:</p>
                                        <textarea name="learning_improvement_points" rows="3" class="w-full px-2 py-1 border border-gray-300 rounded-md" placeholder="- Ritme de treball
-"></textarea>
                                </div>
                                </td>
                            </tr>
                        </table>

                        <table class="w-full border-collapse border border-gray-800 mb-8" style="table-layout: fixed;">
                            <tr>
                                <td colspan="1" class="p-3 font-bold text-white text-xl" style="background-color: #6ab0e6; border: 1px solid #000;">
                                    Interessos i motivacions de l'alumne/a
                                </td>
                            </tr>
                            <tr>
                                <td class="p-3 border border-gray-800">
                                    <textarea name="student_interests" rows="3" class="w-full px-2 py-1 border border-gray-300 rounded-md" placeholder="-"></textarea>
                                </td>
                            </tr>
                        </table>

                        <!-- ÀREES, BLOC DE SABERS I SABERS -->
                        <table class="w-full border-collapse border border-gray-800 mb-8" style="table-layout: fixed;">
                            <tr>
                                <td colspan="3" class="p-3 font-bold text-white text-xl" style="background-color: #6ab0e6; border: 1px solid #000;">
                                    ÀREES, BLOC DE SABERS I SABERS
                                </td>
                            </tr>
                            <tr class="bg-blue-100">
                                <td class="p-3 border border-gray-800 font-bold" style="width: 30%;">Àrea o Matèria</td>
                                <td class="p-3 border border-gray-800 font-bold" style="width: 30%;">Bloc de sabers</td>
                                <td class="p-3 border border-gray-800 font-bold" style="width: 40%;">Saber</td>
                            </tr>

                            <!-- Se muestran siempre 2 filas iniciales -->
                            <tr>
                                <td class="p-2 border border-gray-800">
                                    <input type="text" name="area_materia[]" class="w-full px-2 py-1 border border-gray-300 rounded-md">
                                </td>
                                <td class="p-2 border border-gray-800">
                                    <input type="text" name="bloc_sabers[]" class="w-full px-2 py-1 border border-gray-300 rounded-md">
                                </td>
                                <td class="p-2 border border-gray-800">
                                    <textarea name="saber[]" rows="2" class="w-full px-2 py-1 border border-gray-300 rounded-md"></textarea>
                                </td>
                            </tr>
                            <tr>
                                <td class="p-2 border border-gray-800">
                                    <input type="text" name="area_materia[]" class="w-full px-2 py-1 border border-gray-300 rounded-md">
                                </td>
                                <td class="p-2 border border-gray-800">
                                    <input type="text" name="bloc_sabers[]" class="w-full px-2 py-1 border border-gray-300 rounded-md">
                                </td>
                                <td class="p-2 border border-gray-800">
                                    <textarea name="saber[]" rows="2" class="w-full px-2 py-1 border border-gray-300 rounded-md"></textarea>
                                </td>
                            </tr>

                            <tr id="sabers-container">
                                <!-- Aquí se insertarán nuevas filas mediante JavaScript -->
                            </tr>
                            <tr>
                                <td colspan="3" class="p-2 border border-gray-800">
                                    <button type="button" id="add-sabers-row" class="inline-flex items-center px-3 py-1 bg-blue-100 border border-blue-300 rounded-md font-medium text-xs text-blue-800 hover:bg-blue-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                        </svg>
                                        Afegir més files
                                    </button>
                                    <span id="sabers-row-counter" class="ml-2 text-sm text-gray-500">2/18 files</span>
                                </td>
                            </tr>
                        </table>

                        <!-- PROFESSIONALS I SERVEIS QUE HI INTERVENEN -->
                        <table class="w-full border-collapse border border-gray-800 mb-8" style="table-layout: fixed;">
                            <tr>
                                <td colspan="1" class="p-3 font-bold text-white text-xl" style="background-color: #6ab0e6; border: 1px solid #000;">
                                    PROFESSIONALS I SERVEIS QUE HI INTERVENEN
                                </td>
                            </tr>
                            <tr>
                                <td class="p-3 border border-gray-800">
                                    <div class="space-y-2">
                                        <div class="flex items-start">
                                            <div class="relative flex items-center mt-1 mr-2">
                                                <input type="checkbox" name="professionals[]" value="tutor_responsable"
                                                    class="absolute w-5 h-5 opacity-0 cursor-pointer">
                                                <div class="w-5 h-5 border border-gray-400 flex items-center justify-center bg-white">
                                                    <svg class="hidden w-4 h-4 text-blue-600 check-indicator" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                    </svg>
                                                </div>
                                            </div>
                                            <label>Tutor/a (responsable de coordinar l'elaboració del PI)</label>
                                        </div>
                                        <div class="flex items-start">
                                            <div class="relative flex items-center mt-1 mr-2">
                                                <input type="checkbox" name="professionals[]" value="tutor_aula_acollida"
                                                    class="absolute w-5 h-5 opacity-0 cursor-pointer">
                                                <div class="w-5 h-5 border border-gray-400 flex items-center justify-center bg-white">
                                                    <svg class="hidden w-4 h-4 text-blue-600 check-indicator" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                    </svg>
                                                </div>
                                            </div>
                                            <label>Tutor/a aula d'acollida:</label>
                                        </div>
                                        <div class="flex items-start">
                                            <div class="relative flex items-center mt-1 mr-2">
                                                <input type="checkbox" name="professionals[]" value="suport_intensiu"
                                                    class="absolute w-5 h-5 opacity-0 cursor-pointer">
                                                <div class="w-5 h-5 border border-gray-400 flex items-center justify-center bg-white">
                                                    <svg class="hidden w-4 h-4 text-blue-600 check-indicator" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                    </svg>
                                                </div>
                                            </div>
                                            <label>Suport intensiu a l'escolarització inclusiva (SIEI):</label>
                                        </div>
                                        <div class="flex items-start">
                                            <div class="relative flex items-center mt-1 mr-2">
                                                <input type="checkbox" name="professionals[]" value="aula_integral"
                                                    class="absolute w-5 h-5 opacity-0 cursor-pointer">
                                                <div class="w-5 h-5 border border-gray-400 flex items-center justify-center bg-white">
                                                    <svg class="hidden w-4 h-4 text-blue-600 check-indicator" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                    </svg>
                                                </div>
                                            </div>
                                            <label>Aula integral de suport (AIS):</label>
                                        </div>
                                        <div class="flex items-start">
                                            <div class="relative flex items-center mt-1 mr-2">
                                                <input type="checkbox" name="professionals[]" value="mestre_educacio_especial"
                                                    class="absolute w-5 h-5 opacity-0 cursor-pointer">
                                                <div class="w-5 h-5 border border-gray-400 flex items-center justify-center bg-white">
                                                    <svg class="hidden w-4 h-4 text-blue-600 check-indicator" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                    </svg>
                                                </div>
                                            </div>
                                            <label>Mestre/a d'educació especial:</label>
                                        </div>
                                        <div class="flex items-start">
                                            <div class="relative flex items-center mt-1 mr-2">
                                                <input type="checkbox" name="professionals[]" value="assessor_llengua"
                                                    class="absolute w-5 h-5 opacity-0 cursor-pointer">
                                                <div class="w-5 h-5 border border-gray-400 flex items-center justify-center bg-white">
                                                    <svg class="hidden w-4 h-4 text-blue-600 check-indicator" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                    </svg>
                                                </div>
                                            </div>
                                            <label>Assessor/a de llengua i cohesió social (LIC):</label>
                                        </div>
                                        <div class="flex items-start">
                                            <div class="relative flex items-center mt-1 mr-2">
                                                <input type="checkbox" name="professionals[]" value="altres_professionals"
                                                    class="absolute w-5 h-5 opacity-0 cursor-pointer">
                                                <div class="w-5 h-5 border border-gray-400 flex items-center justify-center bg-white">
                                                    <svg class="hidden w-4 h-4 text-blue-600 check-indicator" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                    </svg>
                                                </div>
                                            </div>
                                            <label>Altres professionals (educador/a, tècnic/a d'integració social (TIS), monitors per a l'educació inclusiva a l'escola...):</label>
                                        </div>
                                        <div class="flex items-start">
                                            <div class="relative flex items-center mt-1 mr-2">
                                                <input type="checkbox" name="professionals[]" value="equip_assessorament"
                                                    class="absolute w-5 h-5 opacity-0 cursor-pointer">
                                                <div class="w-5 h-5 border border-gray-400 flex items-center justify-center bg-white">
                                                    <svg class="hidden w-4 h-4 text-blue-600 check-indicator" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                    </svg>
                                                </div>
                                            </div>
                                            <label>Equip d'assessorament psicopedagògic (EAP) / treballador/a social:</label>
                                        </div>
                                        <div class="flex items-start">
                                            <div class="relative flex items-center mt-1 mr-2">
                                                <input type="checkbox" name="professionals[]" value="serveis_socials"
                                                    class="absolute w-5 h-5 opacity-0 cursor-pointer">
                                                <div class="w-5 h-5 border border-gray-400 flex items-center justify-center bg-white">
                                                    <svg class="hidden w-4 h-4 text-blue-600 check-indicator" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                    </svg>
                                                </div>
                                            </div>
                                            <label>Serveis socials:</label>
                                        </div>
                                        <div class="flex items-start">
                                            <div class="relative flex items-center mt-1 mr-2">
                                                <input type="checkbox" name="professionals[]" value="centre_salut_mental"
                                                    class="absolute w-5 h-5 opacity-0 cursor-pointer">
                                                <div class="w-5 h-5 border border-gray-400 flex items-center justify-center bg-white">
                                                    <svg class="hidden w-4 h-4 text-blue-600 check-indicator" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                    </svg>
                                                </div>
                                            </div>
                                            <label>Centre de salut mental infantil i juvenil (CSMIJ):</label>
                                        </div>
                                        <div class="flex items-start">
                                            <div class="relative flex items-center mt-1 mr-2">
                                                <input type="checkbox" name="professionals[]" value="centres_recursos"
                                                    class="absolute w-5 h-5 opacity-0 cursor-pointer">
                                                <div class="w-5 h-5 border border-gray-400 flex items-center justify-center bg-white">
                                                    <svg class="hidden w-4 h-4 text-blue-600 check-indicator" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                    </svg>
                                                </div>
                                            </div>
                                            <label>Centre de recursos educatius per a deficients auditius (CREDA), centre de recursos educatius per a deficients visuals (CREDV), centres d'educació especial com a centres proveïdors de serveis i recursos (CEEPSIR), fisioterapeuta...:</label>
                                        </div>
                                        <div class="flex items-start">
                                            <div class="relative flex items-center mt-1 mr-2">
                                                <input type="checkbox" name="professionals[]" value="suports_externs"
                                                    class="absolute w-5 h-5 opacity-0 cursor-pointer">
                                                <div class="w-5 h-5 border border-gray-400 flex items-center justify-center bg-white">
                                                    <svg class="hidden w-4 h-4 text-blue-600 check-indicator" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                    </svg>
                                                </div>
                                            </div>
                                            <label>Suports externs (centres de psicopedagogia, reforç escolar, activitats del pla educatiu d'entorn...):</label>
                                        </div>
                                        <div class="flex items-start">
                                            <div class="relative flex items-center mt-1 mr-2">
                                                <input type="checkbox" name="professionals[]" value="activitats_extraescolars"
                                                    class="absolute w-5 h-5 opacity-0 cursor-pointer">
                                                <div class="w-5 h-5 border border-gray-400 flex items-center justify-center bg-white">
                                                    <svg class="hidden w-4 h-4 text-blue-600 check-indicator" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                    </svg>
                                                </div>
                                            </div>
                                            <label>Activitats extraescolars:</label>
                                        </div>
                                        <div class="flex items-start">
                                            <div class="relative flex items-center mt-1 mr-2">
                                                <input type="checkbox" name="professionals[]" value="beques_ajuts"
                                                    class="absolute w-5 h-5 opacity-0 cursor-pointer">
                                                <div class="w-5 h-5 border border-gray-400 flex items-center justify-center bg-white">
                                                    <svg class="hidden w-4 h-4 text-blue-600 check-indicator" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                    </svg>
                                                </div>
                                            </div>
                                            <label>Beques/Ajuts:</label>
                                        </div>
                                        <div class="flex items-start">
                                            <div class="relative flex items-center mt-1 mr-2">
                                                <input type="checkbox" name="professionals[]" value="altres_serveis"
                                                    class="absolute w-5 h-5 opacity-0 cursor-pointer">
                                                <div class="w-5 h-5 border border-gray-400 flex items-center justify-center bg-white">
                                                    <svg class="hidden w-4 h-4 text-blue-600 check-indicator" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                    </svg>
                                </div>
                            </div>
                                            <label>Altres serveis:</label>
                                </div>
                            </div>
                                </td>
                            </tr>
                        </table>

                        <!-- HORARI ESCOLAR -->
                        <div class="rounded-lg border border-gray-300 shadow-sm mb-8 p-4">
                            <h3 class="text-lg font-semibold mb-4">{{ __('Horari Escolar') }}</h3>

                            <!-- Hidden inputs for timetable data -->
                            <input type="hidden" name="timetable_data" value="">
                            <input type="hidden" name="timetable_name" value="Horari Escolar">

                            @include('components.timetable-editor')
                        </div>

                        <!--OBJECTIUS I CRITERIS D'AVALUACIÓ TRANSVERSALS-->
                        <table class="w-full border-collapse border border-gray-800 mb-8" style="table-layout: fixed;">
                            <tr>
                                <td colspan="1" class="p-3 font-bold text-white text-xl" style="background-color: #6ab0e6; border: 1px solid #000;">
                                    OBJECTIUS I CRITERIS D'AVALUACIÓ TRANSVERSALS
                                </td>
                            </tr>
                            <tr>
                                <td class="p-3 border border-gray-800">
                                    <div class="mb-4">
                                        <p class="font-bold mb-2">Objectius:</p>
                                        <textarea name="transversal_objectives[]" rows="4" class="w-full px-2 py-1 border border-gray-300 rounded-md"></textarea>
                                    </div>
                                    <div class="mb-4">
                                        <p class="font-bold mb-2">Criteris:</p>
                                        <textarea name="transversal_criteria[]" rows="4" class="w-full px-2 py-1 border border-gray-300 rounded-md"></textarea>
                                    </div>
                                </td>
                            </tr>
                        </table>
                                </div>

                    <!-- Excel Format (Hidden by default) -->
                    <div id="excel-format-content" class="hidden">
                        <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 mb-8" role="alert">
                            <p class="font-bold">Format Excel</p>
                            <p>Els camps per al format Excel estaran disponibles pròximament.</p>
                            </div>
                        </div>

                    <div class="flex justify-end mt-4">
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition">
                            {{ __('Guardar Plan') }}
                        </button>
                        </div>
                    </form>
            </div>
        </div>
    </div>

    <script>
        function switchFormat(format) {
            const wordContent = document.getElementById('word-format-content');
            const excelContent = document.getElementById('excel-format-content');
            const wordButton = document.getElementById('format-word');
            const excelButton = document.getElementById('format-excel');

            if (format === 'word') {
                wordContent.classList.remove('hidden');
                excelContent.classList.add('hidden');
                wordButton.classList.add('bg-blue-100', 'text-blue-800');
                wordButton.classList.remove('bg-gray-200', 'text-gray-700');
                excelButton.classList.add('bg-gray-200', 'text-gray-700');
                excelButton.classList.remove('bg-blue-100', 'text-blue-800');
            } else {
                wordContent.classList.add('hidden');
                excelContent.classList.remove('hidden');
                wordButton.classList.add('bg-gray-200', 'text-gray-700');
                wordButton.classList.remove('bg-blue-100', 'text-blue-800');
                excelButton.classList.add('bg-blue-100', 'text-blue-800');
                excelButton.classList.remove('bg-gray-200', 'text-gray-700');
            }
        }

        // Add function to handle adding new rows to competencies tables
        document.addEventListener('DOMContentLoaded', function() {
            // Handle custom checkboxes
            const customCheckboxes = document.querySelectorAll('.relative input[type="checkbox"]');

            // Initialize checkboxes state
            customCheckboxes.forEach(function(checkbox) {
                const checkIndicator = checkbox.nextElementSibling.querySelector('.check-indicator');
                if (checkbox.checked) {
                    checkIndicator.classList.remove('hidden');
                } else {
                    checkIndicator.classList.add('hidden');
                }
            });

            // Add event listeners to each checkbox
            customCheckboxes.forEach(function(checkbox) {
                checkbox.addEventListener('change', function() {
                    const checkIndicator = this.nextElementSibling.querySelector('.check-indicator');
                    if (this.checked) {
                        checkIndicator.classList.remove('hidden');
                    } else {
                        checkIndicator.classList.add('hidden');
                    }
                });
            });

            // Competencias de áreas
            const addButton = document.getElementById('add-objective-row');
            const container = document.getElementById('objectives-container');
            const rowCounter = document.getElementById('row-counter');
            let rowCount = 2; // Initial row count
            const maxRows = 20; // Maximum number of rows allowed

            if (addButton) {
                addButton.addEventListener('click', function() {
                    if (rowCount < maxRows) {
                        const newRow = document.createElement('tr');

                        newRow.innerHTML = `
                            <td class="p-2 border border-gray-800 align-top">
                                <textarea name="learning_objectives[]" rows="4" class="w-full px-2 py-1 border border-gray-300 rounded-md"></textarea>
                            </td>
                            <td class="p-2 border border-gray-800 align-top">
                                <textarea name="evaluation_criteria[]" rows="4" class="w-full px-2 py-1 border border-gray-300 rounded-md"></textarea>
                            </td>
                        `;

                        container.insertAdjacentElement('beforebegin', newRow);
                        rowCount++;

                        // Update the counter
                        rowCounter.textContent = `${rowCount}/${maxRows} files`;

                        // Disable button when max rows reached
                        if (rowCount >= maxRows) {
                            addButton.disabled = true;
                            addButton.classList.add('opacity-50', 'cursor-not-allowed');
                        }
                    }
                });
            }

            // Competencias transversales
            const addTransversalButton = document.getElementById('add-transversal-row');
            const transversalContainer = document.getElementById('transversal-container');
            const transversalRowCounter = document.getElementById('transversal-row-counter');
            let transversalRowCount = 2; // Initial row count
            const maxTransversalRows = 14; // Maximum number of rows allowed

            if (addTransversalButton) {
                addTransversalButton.addEventListener('click', function() {
                    if (transversalRowCount < maxTransversalRows) {
                        const newRow = document.createElement('tr');

                        newRow.innerHTML = `
                            <td class="p-2 border border-gray-800 align-top">
                                <textarea name="transversal_objectives[]" rows="4" class="w-full px-2 py-1 border border-gray-300 rounded-md"></textarea>
                            </td>
                            <td class="p-2 border border-gray-800 align-top">
                                <textarea name="transversal_criteria[]" rows="4" class="w-full px-2 py-1 border border-gray-300 rounded-md"></textarea>
                            </td>
                        `;

                        transversalContainer.insertAdjacentElement('beforebegin', newRow);
                        transversalRowCount++;

                        // Update the counter
                        transversalRowCounter.textContent = `${transversalRowCount}/${maxTransversalRows} files`;

                        // Disable button when max rows reached
                        if (transversalRowCount >= maxTransversalRows) {
                            addTransversalButton.disabled = true;
                            addTransversalButton.classList.add('opacity-50', 'cursor-not-allowed');
                        }
                    }
                });
            }

            // Àrees, bloc de sabers i sabers
            const addSabersButton = document.getElementById('add-sabers-row');
            const sabersContainer = document.getElementById('sabers-container');
            const sabersRowCounter = document.getElementById('sabers-row-counter');
            let sabersRowCount = 2; // Initial row count
            const maxSabersRows = 18; // Maximum number of rows allowed

            if (addSabersButton) {
                addSabersButton.addEventListener('click', function() {
                    if (sabersRowCount < maxSabersRows) {
                        const newRow = document.createElement('tr');

                        newRow.innerHTML = `
                            <td class="p-2 border border-gray-800">
                                <input type="text" name="area_materia[]" class="w-full px-2 py-1 border border-gray-300 rounded-md">
                            </td>
                            <td class="p-2 border border-gray-800">
                                <input type="text" name="bloc_sabers[]" class="w-full px-2 py-1 border border-gray-300 rounded-md">
                            </td>
                            <td class="p-2 border border-gray-800">
                                <textarea name="saber[]" rows="2" class="w-full px-2 py-1 border border-gray-300 rounded-md"></textarea>
                            </td>
                        `;

                        sabersContainer.insertAdjacentElement('beforebegin', newRow);
                        sabersRowCount++;

                        // Update the counter
                        sabersRowCounter.textContent = `${sabersRowCount}/${maxSabersRows} files`;

                        // Disable button when max rows reached
                        if (sabersRowCount >= maxSabersRows) {
                            addSabersButton.disabled = true;
                            addSabersButton.classList.add('opacity-50', 'cursor-not-allowed');
                        }
                    }
                });
            }
        });
    </script>
</x-app-layout>
