<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Editar Plan de Soporte Individualizado') }}
            </h2>
            <span class="inline-flex px-3 py-1 bg-indigo-100 text-indigo-800 rounded-full text-sm font-medium">
                {{ $supportPlan->student_name }}
            </span>
                </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <form action="{{ route('support-plans.update', $supportPlan->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                    <!-- Campos obligatorios y selección de equipo -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                        <div>
                            <label for="name" class="block font-medium text-sm text-gray-700 mb-1">Nombre del plan <span class="text-red-500">*</span></label>
                            <input type="text" name="name" id="name" value="{{ old('name', $supportPlan->name) }}" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                            @error('name')<span class="text-red-500 text-xs mt-1">{{ $message }}</span>@enderror
                        </div>

                        <div>
                            <label for="team_id" class="block font-medium text-sm text-gray-700 mb-1">Asignar a equipo</label>
                            <select name="team_id" id="team_id" class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                <option value="">-- Seleccione un equipo (opcional) --</option>
                                @foreach(isset($userTeams) ? $userTeams : [] as $team)
                                    <option value="{{ $team->id }}" {{ (old('team_id', $supportPlan->team_id) == $team->id) ? 'selected' : '' }}>{{ $team->name }}</option>
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
                                    <input type="text" name="student_name" value="{{ old('student_name', $supportPlan->student_name) }}" class="w-full px-2 py-1 border-0 border-b border-gray-300 bg-gray-50/50 shadow-sm focus:border-indigo-500 focus:bg-white focus:shadow-md focus:ring-0 transition-all duration-200" required>
                                    @error('student_name')<span class="text-red-500 text-xs">{{ $message }}</span>@enderror
                                </td>
                                <td class="p-2 font-bold border border-gray-800" style="width: 25%;">Data de naixement:</td>
                                <td class="p-2 border border-gray-800" style="width: 25%;">
                                    <input type="date" name="birth_date" value="{{ old('birth_date', $supportPlan->birth_date?->format('Y-m-d')) }}" class="w-full px-2 py-1 border-0 border-b border-gray-300 bg-gray-50/50 shadow-sm focus:border-indigo-500 focus:bg-white focus:shadow-md focus:ring-0 transition-all duration-200">
                                    @error('birth_date')<span class="text-red-500 text-xs">{{ $message }}</span>@enderror
                                </td>
                            </tr>

                            <!-- Segunda fila: Lloc de naixement - Nom dels tutors -->
                            <tr>
                                <td class="p-2 font-bold border border-gray-800">Lloc de naixement:</td>
                                <td class="p-2 border border-gray-800">
                                    <input type="text" name="birth_place" value="{{ old('birth_place', $supportPlan->birth_place) }}" class="w-full px-2 py-1 border-0 border-b border-gray-300 bg-gray-50/50 shadow-sm focus:border-indigo-500 focus:bg-white focus:shadow-md focus:ring-0 transition-all duration-200">
                                    @error('birth_place')<span class="text-red-500 text-xs">{{ $message }}</span>@enderror
                                </td>
                                <td class="p-2 font-bold border border-gray-800">Nom dels tutors i/o tutores legals:</td>
                                <td class="p-2 border border-gray-800">
                                    <input type="text" name="tutor_name" value="{{ old('tutor_name', $supportPlan->tutor_name) }}" class="w-full px-2 py-1 border-0 border-b border-gray-300 bg-gray-50/50 shadow-sm focus:border-indigo-500 focus:bg-white focus:shadow-md focus:ring-0 transition-all duration-200">
                                    @error('tutor_name')<span class="text-red-500 text-xs">{{ $message }}</span>@enderror
                                </td>
                            </tr>

                            <!-- Tercera fila: Adreça - Telèfon -->
                            <tr>
                                <td class="p-2 font-bold border border-gray-800">Adreça:</td>
                                <td class="p-2 border border-gray-800">
                                    <input type="text" name="address" value="{{ old('address', $supportPlan->address) }}" class="w-full px-2 py-1 border-0 border-b border-gray-300 bg-gray-50/50 shadow-sm focus:border-indigo-500 focus:bg-white focus:shadow-md focus:ring-0 transition-all duration-200">
                                    @error('address')<span class="text-red-500 text-xs">{{ $message }}</span>@enderror
                                </td>
                                <td class="p-2 font-bold border border-gray-800">Telèfon:</td>
                                <td class="p-2 border border-gray-800">
                                    <input type="text" name="phone" value="{{ old('phone', $supportPlan->phone) }}" class="w-full px-2 py-1 border-0 border-b border-gray-300 bg-gray-50/50 shadow-sm focus:border-indigo-500 focus:bg-white focus:shadow-md focus:ring-0 transition-all duration-200">
                                    @error('phone')<span class="text-red-500 text-xs">{{ $message }}</span>@enderror
                                </td>
                            </tr>

                            <!-- Cuarta fila: Llengua d'ús habitual - Altres llengües -->
                            <tr>
                                <td class="p-2 font-bold border border-gray-800">Llengua d'ús habitual:</td>
                                <td class="p-2 border border-gray-800">
                                    <input type="text" name="usual_language" value="{{ old('usual_language', $supportPlan->usual_language) }}" class="w-full px-2 py-1 border-0 border-b border-gray-300 bg-gray-50/50 shadow-sm focus:border-indigo-500 focus:bg-white focus:shadow-md focus:ring-0 transition-all duration-200">
                                    @error('usual_language')<span class="text-red-500 text-xs">{{ $message }}</span>@enderror
                                </td>
                                <td class="p-2 font-bold border border-gray-800">Altres llengües que coneix:</td>
                                <td class="p-2 border border-gray-800">
                                    <input type="text" name="other_languages" value="{{ old('other_languages', $supportPlan->other_languages) }}" class="w-full px-2 py-1 border-0 border-b border-gray-300 bg-gray-50/50 shadow-sm focus:border-indigo-500 focus:bg-white focus:shadow-md focus:ring-0 transition-all duration-200">
                                    @error('other_languages')<span class="text-red-500 text-xs">{{ $message }}</span>@enderror
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
                                    <input type="text" name="course" value="{{ old('course', $supportPlan->course) }}" class="w-full px-2 py-1 border-0 border-b border-gray-300 bg-gray-50/50 shadow-sm focus:border-indigo-500 focus:bg-white focus:shadow-md focus:ring-0 transition-all duration-200">
                                    @error('course')<span class="text-red-500 text-xs">{{ $message }}</span>@enderror
                                </td>
                                <td class="p-2 font-bold border border-gray-800">Grup:</td>
                                <td class="p-2 border border-gray-800">
                                    <input type="text" name="group" value="{{ old('group', $supportPlan->group) }}" class="w-full px-2 py-1 border-0 border-b border-gray-300 bg-gray-50/50 shadow-sm focus:border-indigo-500 focus:bg-white focus:shadow-md focus:ring-0 transition-all duration-200">
                                    @error('group')<span class="text-red-500 text-xs">{{ $message }}</span>@enderror
                                </td>
                            </tr>

                            <!-- Segunda fila de datos escolares: Tutor - Data d'arribada -->
                            <tr>
                                <td class="p-2 font-bold border border-gray-800">Tutor/a:</td>
                                <td class="p-2 border border-gray-800">
                                    <input type="text" name="teacher_name" value="{{ old('teacher_name', $supportPlan->teacher_name) }}" class="w-full px-2 py-1 border-0 border-b border-gray-300 bg-gray-50/50 shadow-sm focus:border-indigo-500 focus:bg-white focus:shadow-md focus:ring-0 transition-all duration-200">
                                    @error('teacher_name')<span class="text-red-500 text-xs">{{ $message }}</span>@enderror
                                </td>
                                <td class="p-2 font-bold border border-gray-800">Data d'arribada a Catalunya:</td>
                                <td class="p-2 border border-gray-800">
                                    <input type="date" name="catalonia_arrival_date" value="{{ old('catalonia_arrival_date', $supportPlan->catalonia_arrival_date?->format('Y-m-d')) }}" class="w-full px-2 py-1 border-0 border-b border-gray-300 bg-gray-50/50 shadow-sm focus:border-indigo-500 focus:bg-white focus:shadow-md focus:ring-0 transition-all duration-200">
                                    @error('catalonia_arrival_date')<span class="text-red-500 text-xs">{{ $message }}</span>@enderror
                                </td>
                            </tr>

                            <!-- Tercera fila de datos escolares: Incorporació centre - Incorporació sistema -->
                            <tr>
                                <td class="p-2 font-bold border border-gray-800">Data d'incorporació al centre:</td>
                                <td class="p-2 border border-gray-800">
                                    <input type="date" name="school_incorporation_date" value="{{ old('school_incorporation_date', $supportPlan->school_incorporation_date?->format('Y-m-d')) }}" class="w-full px-2 py-1 border-0 border-b border-gray-300 bg-gray-50/50 shadow-sm focus:border-indigo-500 focus:bg-white focus:shadow-md focus:ring-0 transition-all duration-200">
                                    @error('school_incorporation_date')<span class="text-red-500 text-xs">{{ $message }}</span>@enderror
                                </td>
                                <td class="p-2 font-bold border border-gray-800">Data d'incorporació al sistema educatiu català:</td>
                                <td class="p-2 border border-gray-800">
                                    <input type="date" name="educational_system_date" value="{{ old('educational_system_date', $supportPlan->educational_system_date?->format('Y-m-d')) }}" class="w-full px-2 py-1 border-0 border-b border-gray-300 bg-gray-50/50 shadow-sm focus:border-indigo-500 focus:bg-white focus:shadow-md focus:ring-0 transition-all duration-200">
                                    @error('educational_system_date')<span class="text-red-500 text-xs">{{ $message }}</span>@enderror
                                </td>
                            </tr>

                            <!-- Cuarta fila de datos escolares: Centres anteriors - Escolarització -->
                            <tr>
                                <td class="p-2 font-bold border border-gray-800">Centres on ha estat matriculat anteriorment:</td>
                                <td class="p-2 border border-gray-800">
                                    <input type="text" name="previous_schools" value="{{ old('previous_schools', $supportPlan->previous_schools) }}" class="w-full px-2 py-1 border-0 border-b border-gray-300 bg-gray-50/50 shadow-sm focus:border-indigo-500 focus:bg-white focus:shadow-md focus:ring-0 transition-all duration-200">
                                    @error('previous_schools')<span class="text-red-500 text-xs">{{ $message }}</span>@enderror
                                </td>
                                <td class="p-2 font-bold border border-gray-800">Escolarització prèvia:</td>
                                <td class="p-2 border border-gray-800">
                                    <input type="text" name="previous_schooling" value="{{ old('previous_schooling', $supportPlan->previous_schooling) }}" class="w-full px-2 py-1 border-0 border-b border-gray-300 bg-gray-50/50 shadow-sm focus:border-indigo-500 focus:bg-white focus:shadow-md focus:ring-0 transition-all duration-200">
                                    @error('previous_schooling')<span class="text-red-500 text-xs">{{ $message }}</span>@enderror
                                </td>
                            </tr>

                            <!-- Quinta fila: Retenció de curs (fila completa) -->
                            <tr>
                                <td class="p-2 font-bold border border-gray-800">Retenció de curs:</td>
                                <td class="p-2 border border-gray-800" colspan="3">
                                    <input type="text" name="course_retention" value="{{ old('course_retention', $supportPlan->course_retention) }}" class="w-full px-2 py-1 border-0 border-b border-gray-300 bg-gray-50/50 shadow-sm focus:border-indigo-500 focus:bg-white focus:shadow-md focus:ring-0 transition-all duration-200">
                                    @error('course_retention')<span class="text-red-500 text-xs">{{ $message }}</span>@enderror
                                </td>
                            </tr>

                            <!-- Sexta fila: Altres informacions d'interès -->
                            <tr>
                                <td class="p-2 font-bold border border-gray-800">Altres informacions d'interès:</td>
                                <td class="p-2 border border-gray-800" colspan="3">
                                    <textarea name="other_data" rows="3" class="w-full px-2 py-1 border-0 border-b border-gray-300 bg-gray-50/50 shadow-sm focus:border-indigo-500 focus:bg-white focus:shadow-md focus:ring-0 transition-all duration-200">{{ old('other_data', $supportPlan->other_data) }}</textarea>
                                    @error('other_data')<span class="text-red-500 text-xs">{{ $message }}</span>@enderror
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
                                                        {{ in_array('informe_reconeixement', old('justification_reasons', $supportPlan->justification_reasons ?? [])) ? 'checked' : '' }}
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
                                                        {{ in_array('avaluacio_psicopedagogica', old('justification_reasons', $supportPlan->justification_reasons ?? [])) ? 'checked' : '' }}
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
                                                        {{ in_array('avaluacio_inicial_nouvingut', old('justification_reasons', $supportPlan->justification_reasons ?? [])) ? 'checked' : '' }}
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
                                                        {{ in_array('avaluacio_origen_estranger_aula', old('justification_reasons', $supportPlan->justification_reasons ?? [])) ? 'checked' : '' }}
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
                                                        {{ in_array('avaluacio_origen_estranger_tardana', old('justification_reasons', $supportPlan->justification_reasons ?? [])) ? 'checked' : '' }}
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
                                                        {{ in_array('decisio_comissio', old('justification_reasons', $supportPlan->justification_reasons ?? [])) ? 'checked' : '' }}
                                                        class="absolute w-5 h-5 opacity-0 cursor-pointer">
                                                    <div class="w-5 h-5 border border-gray-400 flex items-center justify-center bg-white">
                                                        <svg class="hidden w-4 h-4 text-blue-600 check-indicator" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                        </svg>
                                                    </div>
                                                </div>
                                                <label>Decisió de la comissió d'atenció educativa inclusiva (CAEI) a proposta de</label>
                                                <input type="text" name="commission_proponent" placeholder="EAP/tutor/docent/família..." class="ml-1 px-2 py-1 border border-gray-300 rounded-md w-auto"
                                                    value="{{ old('commission_proponent', $supportPlan->commission_proponent ?? '') }}">
                                                <label class="ml-1">motivada per</label>
                                                <input type="text" name="commission_motivation" class="ml-1 px-2 py-1 border border-gray-300 rounded-md w-1/3"
                                                    value="{{ old('commission_motivation', $supportPlan->commission_motivation ?? '') }}">
                        </div>

                                            <div class="flex items-start">
                                                <div class="relative flex items-center mt-1 mr-2">
                                                    <input type="checkbox" name="justification_reasons[]" value="altres"
                                                        {{ in_array('altres', old('justification_reasons', $supportPlan->justification_reasons ?? [])) ? 'checked' : '' }}
                                                        class="absolute w-5 h-5 opacity-0 cursor-pointer">
                                                    <div class="w-5 h-5 border border-gray-400 flex items-center justify-center bg-white">
                                                        <svg class="hidden w-4 h-4 text-blue-600 check-indicator" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                        </svg>
                                </div>
                            </div>
                                                <label>Altres:</label>
                                                <input type="text" name="justification_other" class="ml-2 px-2 py-1 border border-gray-300 rounded-md w-4/5"
                                                    value="{{ old('justification_other', $supportPlan->justification_other ?? '') }}">
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
                                            <textarea name="brief_justification" rows="4" class="w-full px-2 py-1 border border-gray-300 rounded-md">{{ old('brief_justification', $supportPlan->brief_justification ?? '') }}</textarea>
                                </div>
                            </div>
                                </td>
                            </tr>
                        </table>

                        <!-- NIVELL ACTUAL DE COMPETÈNCIES -->
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
                                        <textarea name="competencies_alumne_capabilities" rows="3" class="w-full px-2 py-1 border border-gray-300 rounded-md" placeholder="-">{{ old('competencies_alumne_capabilities', $supportPlan->competencies_alumne_capabilities ?? '') }}</textarea>
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
">-{{ old('learning_strong_points', $supportPlan->learning_strong_points ?? '') }}</textarea>
                        </div>

                                    <div class="mb-4">
                                        <p class="font-bold mb-2">Punts de millora:</p>
                                        <textarea name="learning_improvement_points" rows="3" class="w-full px-2 py-1 border border-gray-300 rounded-md" placeholder="- Ritme de treball
">-{{ old('learning_improvement_points', $supportPlan->learning_improvement_points ?? '') }}</textarea>
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
                                    <textarea name="student_interests" rows="3" class="w-full px-2 py-1 border border-gray-300 rounded-md">{{ old('student_interests', $supportPlan->student_interests) }}</textarea>
                                </td>
                            </tr>
                        </table>

                        <!-- CONCRECIÓ DE LES COMPETÈNCIES TRANSVERSALS DEL PSI -->
                        <table class="w-full border-collapse border border-gray-800 mb-8" style="table-layout: fixed;">
                            <tr>
                                <td colspan="2" class="p-3 font-bold text-white text-xl" style="background-color: #6ab0e6; border: 1px solid #000;">
                                    CONCRECIÓ DE LES COMPETÈNCIES TRANSVERSALS DEL PSI
                                </td>
                            </tr>
                            <tr>
                                <td class="p-3 border border-gray-800 w-1/2 bg-blue-100">
                                    <div class="flex items-center">
                                        <span class="font-bold mr-1">Objectiu d'aprenentatge</span>
                                        <span class="text-sm text-gray-500">(7)</span>
                                    </div>
                                    <div class="text-sm mb-2">Què volem que aprengui l'alumnat i per a què?</div>
                                    <div class="text-sm mb-2">CAPACITAT + SABER + FINALITAT</div>
                                </td>
                                <td class="p-3 border border-gray-800 w-1/2 bg-blue-100">
                                    <div class="flex items-center">
                                        <span class="font-bold mr-1">Criteri d'avaluació</span>
                                        <span class="text-sm text-gray-500">(8)</span>
                                    </div>
                                    <div class="text-sm mb-2">Com sabem que ho ha après?</div>
                                    <div class="text-sm mb-2">ACCIÓ + SABER + CONTEXT</div>
                                </td>
                            </tr>
                            <tbody id="transversal-container">
                                @php
                                    $transversalObjectives = old('transversal_objectives', $supportPlan->transversal_objectives ?? []);
                                    $transversalCriteria = old('transversal_criteria', $supportPlan->transversal_criteria ?? []);
                                    
                                    if (!is_array($transversalObjectives)) {
                                        $transversalObjectives = [''];
                                    }
                                    if (!is_array($transversalCriteria)) {
                                        $transversalCriteria = [''];
                                    }
                                    
                                    $transversalRowCount = max(count($transversalObjectives), count($transversalCriteria));
                                @endphp

                                @for($i = 0; $i < $transversalRowCount; $i++)
                                    <tr class="transversal-row">
                                        <td class="p-2 border border-gray-800">
                                            <div class="flex items-center gap-2">
                                                <textarea name="transversal_objectives[]" rows="3" class="w-full px-2 py-1 border border-gray-300 rounded-md">{{ $transversalObjectives[$i] ?? '' }}</textarea>
                                            </div>
                                        </td>
                                        <td class="p-2 border border-gray-800">
                                            <div class="flex items-center gap-2">
                                                <textarea name="transversal_criteria[]" rows="3" class="w-full px-2 py-1 border border-gray-300 rounded-md">{{ $transversalCriteria[$i] ?? '' }}</textarea>
                                                <button type="button" class="delete-row text-red-600 hover:text-red-800 flex-shrink-0">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                    </svg>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endfor
                            </tbody>
                            <tr>
                                <td colspan="2" class="p-2 border border-gray-800">
                                    <button type="button" id="add-transversal-row" class="inline-flex items-center px-3 py-1 bg-blue-100 border border-blue-300 rounded-md font-medium text-xs text-blue-800 hover:bg-blue-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                        </svg>
                                        Afegir més files
                                    </button>
                                    <span id="transversal-row-counter" class="ml-2 text-sm text-gray-500">{{ $transversalRowCount }}/10 files</span>
                                </td>
                            </tr>
                        </table>

                        <!-- ÀREES, BLOC DE SABERS I SABERS -->
                        <table id="sabers-table" class="w-full border-collapse border border-gray-800 mb-8" style="table-layout: fixed;">
                            <tr>
                                <td colspan="4" class="p-3 font-bold text-white text-xl" style="background-color: #6ab0e6; border: 1px solid #000;">
                                    ÀREES, BLOC DE SABERS I SABERS
                                </td>
                            </tr>
                            <tr class="bg-blue-100">
                                <td class="p-3 border border-gray-800 font-bold" style="width: 30%;">Àrea o Matèria</td>
                                <td class="p-3 border border-gray-800 font-bold" style="width: 30%;">Bloc de sabers</td>
                                <td class="p-3 border border-gray-800 font-bold" style="width: 35%;">Saber</td>
                                <td class="p-3 border border-gray-800 font-bold" style="width: 5%;"></td>
                            </tr>

                            @php
                                $areaMateria = old('area_materia', $supportPlan->area_materia ?? []);
                                $blocSabers = old('bloc_sabers', $supportPlan->bloc_sabers ?? []);
                                $saberItems = old('saber', $supportPlan->saber ?? []);

                                if (empty($areaMateria)) {
                                    $areaMateria = [''];
                                }
                                if (empty($blocSabers)) {
                                    $blocSabers = [''];
                                }
                                if (empty($saberItems)) {
                                    $saberItems = [''];
                                }

                                $sabersRowCount = max(count($areaMateria), count($blocSabers), count($saberItems));
                            @endphp

                            <tbody id="sabers-container">
                            @for ($i = 0; $i < $sabersRowCount; $i++)
                                <tr class="saber-row">
                                    <td class="p-2 border border-gray-800">
                                        <input type="text" name="area_materia[]" value="{{ $areaMateria[$i] ?? '' }}" class="w-full px-2 py-1 border border-gray-300 rounded-md">
                                    </td>
                                    <td class="p-2 border border-gray-800">
                                        <input type="text" name="bloc_sabers[]" value="{{ $blocSabers[$i] ?? '' }}" class="w-full px-2 py-1 border border-gray-300 rounded-md">
                                    </td>
                                    <td class="p-2 border border-gray-800">
                                        <textarea name="saber[]" rows="2" class="w-full px-2 py-1 border border-gray-300 rounded-md">{{ $saberItems[$i] ?? '' }}</textarea>
                                    </td>
                                    <td class="p-2 border border-gray-800 text-center">
                                        <button type="button" class="delete-row text-red-600 hover:text-red-800">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                        </button>
                                    </td>
                                </tr>
                            @endfor
                            </tbody>

                            <tr>
                                <td colspan="4" class="p-2 border border-gray-800">
                                    <button type="button" id="add-sabers-row" class="inline-flex items-center px-3 py-1 bg-blue-100 border border-blue-300 rounded-md font-medium text-xs text-blue-800 hover:bg-blue-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                        </svg>
                                        Afegir més files
                                    </button>
                                    <span id="sabers-row-counter" class="ml-2 text-sm text-gray-500">{{ $sabersRowCount }}/18 files</span>
                                </td>
                            </tr>
                        </table>

                        <!-- CONCRECIÓ DE LES COMPETÈNCIES DE LES ÀREES DEL PSI -->
                        <table id="learning-table" class="w-full border-collapse border border-gray-800 mb-8" style="table-layout: fixed;">
                            <tr>
                                <td colspan="2" class="p-3 font-bold text-white text-xl" style="background-color: #6ab0e6; border: 1px solid #000;">
                                    CONCRECIÓ DE LES COMPETÈNCIES DE LES ÀREES DEL PSI
                                </td>
                            </tr>
                            <tr>
                                <td class="p-3 border border-gray-800 w-1/2 bg-blue-100">
                                    <div class="flex items-center">
                                        <span class="font-bold mr-1">Objectiu d'aprenentatge</span>
                                        <span class="text-sm text-gray-500">(7)</span>
                                    </div>
                                    <div class="text-sm mb-2">Què volem que aprengui l'alumnat i per a què?</div>
                                    <div class="text-sm mb-2">CAPACITAT + SABER + FINALITAT</div>
                                </td>
                                <td class="p-3 border border-gray-800 w-1/2 bg-blue-100">
                                    <div class="flex items-center">
                                        <span class="font-bold mr-1">Criteri d'avaluació</span>
                                        <span class="text-sm text-gray-500">(8)</span>
                                    </div>
                                    <div class="text-sm mb-2">Com sabem que ho ha après?</div>
                                    <div class="text-sm mb-2">ACCIÓ + SABER + CONTEXT</div>
                                </td>
                            </tr>

                            <tbody id="learning-container">
                            @php
                                $learningObjectives = old('learning_objectives', $supportPlan->learning_objectives ?? []);
                                $evaluationCriteria = old('evaluation_criteria', $supportPlan->evaluation_criteria ?? []);
                                
                                if (empty($learningObjectives)) {
                                    $learningObjectives = [''];
                                }
                                if (empty($evaluationCriteria)) {
                                    $evaluationCriteria = [''];
                                }
                                
                                $learningRowCount = max(count($learningObjectives), count($evaluationCriteria));
                            @endphp

                            @for ($i = 0; $i < $learningRowCount; $i++)
                                <tr class="learning-row">
                                    <td class="p-2 border border-gray-800">
                                        <div class="flex items-center gap-2">
                                            <textarea name="learning_objectives[]" rows="3" class="w-full px-2 py-1 border border-gray-300 rounded-md">{{ $learningObjectives[$i] ?? '' }}</textarea>
                                        </div>
                                    </td>
                                    <td class="p-2 border border-gray-800">
                                        <div class="flex items-center gap-2">
                                            <textarea name="evaluation_criteria[]" rows="3" class="w-full px-2 py-1 border border-gray-300 rounded-md">{{ $evaluationCriteria[$i] ?? '' }}</textarea>
                                            <button type="button" class="delete-row text-red-600 hover:text-red-800 flex-shrink-0">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endfor
                            </tbody>

                            <tr>
                                <td colspan="2" class="p-2 border border-gray-800">
                                    <button type="button" id="add-learning-row" class="inline-flex items-center px-3 py-1 bg-blue-100 border border-blue-300 rounded-md font-medium text-xs text-blue-800 hover:bg-blue-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                        </svg>
                                        Afegir més files
                                    </button>
                                    <span id="learning-row-counter" class="ml-2 text-sm text-gray-500">{{ $learningRowCount }}/10 files</span>
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
                                        @php
                                            $professionals = old('professionals', $supportPlan->professionals ?? []);
                                            if (!is_array($professionals)) {
                                                $professionals = [];
                                            }
                                        @endphp

                                        <div class="flex items-start">
                                            <div class="relative flex items-center mt-1 mr-2">
                                                <input type="checkbox" name="professionals[]" value="tutor_responsable"
                                                    {{ in_array('tutor_responsable', $professionals) ? 'checked' : '' }}
                                                    class="absolute w-5 h-5 opacity-0 cursor-pointer">
                                                <div class="w-5 h-5 border border-gray-400 flex items-center justify-center bg-white">
                                                    <svg class="hidden w-4 h-4 text-blue-600 check-indicator" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                    </svg>
                                                </div>
                                            </div>
                                            <span>Tutor/a (responsable de coordinar l'elaboració del PI)</span>
                                        </div>

                                        <div class="flex items-start">
                                            <div class="relative flex items-center mt-1 mr-2">
                                                <input type="checkbox" name="professionals[]" value="tutor_aula_acollida"
                                                    {{ in_array('tutor_aula_acollida', $professionals) ? 'checked' : '' }}
                                                    class="absolute w-5 h-5 opacity-0 cursor-pointer">
                                                <div class="w-5 h-5 border border-gray-400 flex items-center justify-center bg-white">
                                                    <svg class="hidden w-4 h-4 text-blue-600 check-indicator" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                    </svg>
                                                </div>
                                            </div>
                                            <span>Tutor/a aula d'acollida:</span>
                                        </div>

                                        <div class="flex items-start">
                                            <div class="relative flex items-center mt-1 mr-2">
                                                <input type="checkbox" name="professionals[]" value="suport_intensiu"
                                                    {{ in_array('suport_intensiu', $professionals) ? 'checked' : '' }}
                                                    class="absolute w-5 h-5 opacity-0 cursor-pointer">
                                                <div class="w-5 h-5 border border-gray-400 flex items-center justify-center bg-white">
                                                    <svg class="hidden w-4 h-4 text-blue-600 check-indicator" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                    </svg>
                                                </div>
                                            </div>
                                            <span>Suport intensiu a l'escolarització inclusiva (SIEI):</span>
                                        </div>

                                        <div class="flex items-start">
                                            <div class="relative flex items-center mt-1 mr-2">
                                                <input type="checkbox" name="professionals[]" value="aula_integral"
                                                    {{ in_array('aula_integral', $professionals) ? 'checked' : '' }}
                                                    class="absolute w-5 h-5 opacity-0 cursor-pointer">
                                                <div class="w-5 h-5 border border-gray-400 flex items-center justify-center bg-white">
                                                    <svg class="hidden w-4 h-4 text-blue-600 check-indicator" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                    </svg>
                                                </div>
                                            </div>
                                            <span>Aula integral de suport (AIS):</span>
                                        </div>

                                        <div class="flex items-start">
                                            <div class="relative flex items-center mt-1 mr-2">
                                                <input type="checkbox" name="professionals[]" value="mestre_educacio_especial"
                                                    {{ in_array('mestre_educacio_especial', $professionals) ? 'checked' : '' }}
                                                    class="absolute w-5 h-5 opacity-0 cursor-pointer">
                                                <div class="w-5 h-5 border border-gray-400 flex items-center justify-center bg-white">
                                                    <svg class="hidden w-4 h-4 text-blue-600 check-indicator" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                    </svg>
                                                </div>
                                            </div>
                                            <span>Mestre/a d'educació especial:</span>
                                        </div>

                                        <div class="flex items-start">
                                            <div class="relative flex items-center mt-1 mr-2">
                                                <input type="checkbox" name="professionals[]" value="assessor_llengua"
                                                    {{ in_array('assessor_llengua', $professionals) ? 'checked' : '' }}
                                                    class="absolute w-5 h-5 opacity-0 cursor-pointer">
                                                <div class="w-5 h-5 border border-gray-400 flex items-center justify-center bg-white">
                                                    <svg class="hidden w-4 h-4 text-blue-600 check-indicator" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                    </svg>
                                                </div>
                                            </div>
                                            <span>Assessor/a de llengua i cohesió social (LIC):</span>
                                        </div>

                                        <div class="flex items-start">
                                            <div class="relative flex items-center mt-1 mr-2">
                                                <input type="checkbox" name="professionals[]" value="altres_professionals"
                                                    {{ in_array('altres_professionals', $professionals) ? 'checked' : '' }}
                                                    class="absolute w-5 h-5 opacity-0 cursor-pointer">
                                                <div class="w-5 h-5 border border-gray-400 flex items-center justify-center bg-white">
                                                    <svg class="hidden w-4 h-4 text-blue-600 check-indicator" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                    </svg>
                                                </div>
                                            </div>
                                            <span>Altres professionals (educador/a, tècnic/a d'integració social (TIS), monitors per a l'educació inclusiva a l'escola...):</span>
                                        </div>

                                        <div class="flex items-start">
                                            <div class="relative flex items-center mt-1 mr-2">
                                                <input type="checkbox" name="professionals[]" value="equip_assessorament"
                                                    {{ in_array('equip_assessorament', $professionals) ? 'checked' : '' }}
                                                    class="absolute w-5 h-5 opacity-0 cursor-pointer">
                                                <div class="w-5 h-5 border border-gray-400 flex items-center justify-center bg-white">
                                                    <svg class="hidden w-4 h-4 text-blue-600 check-indicator" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                    </svg>
                                                </div>
                                            </div>
                                            <span>Equip d'assessorament psicopedagògic (EAP) / treballador/a social:</span>
                                        </div>

                                        <div class="flex items-start">
                                            <div class="relative flex items-center mt-1 mr-2">
                                                <input type="checkbox" name="professionals[]" value="serveis_socials"
                                                    {{ in_array('serveis_socials', $professionals) ? 'checked' : '' }}
                                                    class="absolute w-5 h-5 opacity-0 cursor-pointer">
                                                <div class="w-5 h-5 border border-gray-400 flex items-center justify-center bg-white">
                                                    <svg class="hidden w-4 h-4 text-blue-600 check-indicator" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                    </svg>
                                                </div>
                                            </div>
                                            <span>Serveis socials:</span>
                                        </div>

                                        <div class="flex items-start">
                                            <div class="relative flex items-center mt-1 mr-2">
                                                <input type="checkbox" name="professionals[]" value="centre_salut_mental"
                                                    {{ in_array('centre_salut_mental', $professionals) ? 'checked' : '' }}
                                                    class="absolute w-5 h-5 opacity-0 cursor-pointer">
                                                <div class="w-5 h-5 border border-gray-400 flex items-center justify-center bg-white">
                                                    <svg class="hidden w-4 h-4 text-blue-600 check-indicator" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                    </svg>
                                                </div>
                                            </div>
                                            <span>Centre de salut mental infantil i juvenil (CSMIJ):</span>
                                        </div>

                                        <div class="flex items-start">
                                            <div class="relative flex items-center mt-1 mr-2">
                                                <input type="checkbox" name="professionals[]" value="centres_recursos"
                                                    {{ in_array('centres_recursos', $professionals) ? 'checked' : '' }}
                                                    class="absolute w-5 h-5 opacity-0 cursor-pointer">
                                                <div class="w-5 h-5 border border-gray-400 flex items-center justify-center bg-white">
                                                    <svg class="hidden w-4 h-4 text-blue-600 check-indicator" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                    </svg>
                                                </div>
                                            </div>
                                            <span>Centre de recursos educatius per a deficients auditius (CREDA), centre de recursos educatius per a deficients visuals (CREDV), centres d'educació especial com a centres proveïdors de serveis i recursos (CEEPSIR), fisioterapeuta...:</span>
                                        </div>

                                        <div class="flex items-start">
                                            <div class="relative flex items-center mt-1 mr-2">
                                                <input type="checkbox" name="professionals[]" value="suports_externs"
                                                    {{ in_array('suports_externs', $professionals) ? 'checked' : '' }}
                                                    class="absolute w-5 h-5 opacity-0 cursor-pointer">
                                                <div class="w-5 h-5 border border-gray-400 flex items-center justify-center bg-white">
                                                    <svg class="hidden w-4 h-4 text-blue-600 check-indicator" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                    </svg>
                                                </div>
                                            </div>
                                            <span>Suports externs (centres de psicopedagogia, reforç escolar, activitats del pla educatiu d'entorn...):</span>
                                        </div>

                                        <div class="flex items-start">
                                            <div class="relative flex items-center mt-1 mr-2">
                                                <input type="checkbox" name="professionals[]" value="activitats_extraescolars"
                                                    {{ in_array('activitats_extraescolars', $professionals) ? 'checked' : '' }}
                                                    class="absolute w-5 h-5 opacity-0 cursor-pointer">
                                                <div class="w-5 h-5 border border-gray-400 flex items-center justify-center bg-white">
                                                    <svg class="hidden w-4 h-4 text-blue-600 check-indicator" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                    </svg>
                                                </div>
                                            </div>
                                            <span>Activitats extraescolars:</span>
                                        </div>

                                        <div class="flex items-start">
                                            <div class="relative flex items-center mt-1 mr-2">
                                                <input type="checkbox" name="professionals[]" value="beques_ajuts"
                                                    {{ in_array('beques_ajuts', $professionals) ? 'checked' : '' }}
                                                    class="absolute w-5 h-5 opacity-0 cursor-pointer">
                                                <div class="w-5 h-5 border border-gray-400 flex items-center justify-center bg-white">
                                                    <svg class="hidden w-4 h-4 text-blue-600 check-indicator" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                    </svg>
                                                </div>
                                            </div>
                                            <span>Beques/Ajuts:</span>
                            </div>

                                        <div class="flex items-start">
                                            <div class="relative flex items-center mt-1 mr-2">
                                                <input type="checkbox" name="professionals[]" value="altres_serveis"
                                                    {{ in_array('altres_serveis', $professionals) ? 'checked' : '' }}
                                                    class="absolute w-5 h-5 opacity-0 cursor-pointer">
                                                <div class="w-5 h-5 border border-gray-400 flex items-center justify-center bg-white">
                                                    <svg class="hidden w-4 h-4 text-blue-600 check-indicator" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                                    </svg>
                                </div>
                            </div>
                                            <span>Altres serveis:</span>
                                </div>
                            </div>
                                </td>
                            </tr>
                        </table>

                        <!-- HORARI ESCOLAR -->
                        <div class="rounded-lg border border-gray-300 shadow-sm mb-8 p-4">
                            <h3 class="text-lg font-semibold mb-4">{{ __('Horari Escolar') }}</h3>

                            @php
                                $timetable = $supportPlan->timetables()->with('slots')->first();
                                $timetableData = null;

                                if ($timetable) {
                                    // Convert timetable data to the format expected by the editor
                                    $timeSlots = [];
                                    $slots = [];

                                    // Group slots by time
                                    $timeMap = [];
                                    foreach ($timetable->slots as $slot) {
                                        $timeKey = $slot->time_start . '-' . $slot->time_end;
                                        if (!isset($timeMap[$timeKey])) {
                                            $timeMap[$timeKey] = count($timeSlots);
                                            $timeSlots[] = [
                                                'start' => $slot->time_start,
                                                'end' => $slot->time_end
                                            ];
                                        }

                                        $timeIndex = $timeMap[$timeKey];
                                        $slots[] = [
                                            'day' => $slot->day,
                                            'timeIndex' => $timeIndex,
                                            'subject' => $slot->subject,
                                            'type' => $slot->type ?: 'regular',
                                            'notes' => $slot->notes ?: ''
                                        ];
                                    }

                                    $formattedSlots = $timetable->slots->map(function($slot) {
                                        return [
                                            'day' => $slot->day,
                                            'time_start' => $slot->time_start,
                                            'time_end' => $slot->time_end,
                                            'subject' => $slot->subject,
                                            'type' => $slot->type ?: 'regular',
                                            'notes' => $slot->notes ?: ''
                                        ];
                                    })->toArray();

                                    $timetableData = [
                                        'name' => $timetable->name,
                                        'timeSlots' => $timeSlots,
                                        'slots' => $slots,
                                        'formattedSlots' => $formattedSlots
                                    ];

                                    // Convert to JSON for the hidden input
                                    $timetableDataJson = json_encode($timetableData);
                                }
                            @endphp

                            <!-- Hidden input to store the timetable data -->
                            <input type="hidden" name="timetable_data" value="{{ $timetableDataJson ?? '' }}">
                            <input type="hidden" name="timetable_name" value="{{ $timetable->name ?? 'Horari Escolar' }}">

                            @include('components.timetable-editor')
                        </div>

                        <!-- REUNIONS DE SEGUIMENT -->
                        <div class="space-y-8 mt-8">
                            <!-- REUNIONS DE SEGUIMENT I ACORDS AMB L'ALUMNE/A -->
                            <table class="w-full border-collapse border border-gray-800 mb-8" style="table-layout: fixed;" id="reunions-familia-table">
                                <tr>
                                    <td colspan="4" class="p-3 font-bold text-white text-xl" style="background-color: #6ab0e6; border: 1px solid #000;">
                                        REUNIONS DE SEGUIMENT I ACORDS AMB L'ALUMNE/A, EL PARE, LA MARE O EL TUTOR O TUTORA LEGAL
                                    </td>
                                </tr>
                                <tr class="bg-blue-100">
                                    <td class="p-2 font-bold border border-gray-800" style="width: 15%;">Data</td>
                                    <td class="p-2 font-bold border border-gray-800" style="width: 25%;">Agents participants</td>
                                    <td class="p-2 font-bold border border-gray-800" style="width: 30%;">Temes tractats</td>
                                    <td class="p-2 font-bold border border-gray-800" style="width: 30%;">Acords</td>
                                </tr>
                                <tbody id="reunions-familia-container">
                                    @if(isset($supportPlan->reunion_familia) && is_array($supportPlan->reunion_familia))
                                        @foreach($supportPlan->reunion_familia as $index => $reunio)
                                            <tr class="reunion-familia-row">
                                                <td class="p-2 border border-gray-800">
                                                    <input type="date" name="reunion_familia_data[]" value="{{ $reunio['data'] ?? '' }}" class="w-full px-2 py-1 border border-gray-300 rounded-md">
                                                </td>
                                                <td class="p-2 border border-gray-800">
                                                    <textarea name="reunion_familia_agents[]" rows="2" class="w-full px-2 py-1 border border-gray-300 rounded-md">{{ $reunio['agents'] ?? '' }}</textarea>
                                                </td>
                                                <td class="p-2 border border-gray-800">
                                                    <textarea name="reunion_familia_temes[]" rows="2" class="w-full px-2 py-1 border border-gray-300 rounded-md">{{ $reunio['temes'] ?? '' }}</textarea>
                                                </td>
                                                <td class="p-2 border border-gray-800">
                                                    <div class="flex items-start space-x-2">
                                                        <textarea name="reunion_familia_acords[]" rows="2" class="flex-grow px-2 py-1 border border-gray-300 rounded-md">{{ $reunio['acords'] ?? '' }}</textarea>
                                                        <button type="button" class="delete-row text-red-600 hover:text-red-800 flex-shrink-0 mt-1">
                                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                            </svg>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr class="reunion-familia-row">
                                            <td class="p-2 border border-gray-800">
                                                <input type="date" name="reunion_familia_data[]" class="w-full px-2 py-1 border border-gray-300 rounded-md">
                                            </td>
                                            <td class="p-2 border border-gray-800">
                                                <textarea name="reunion_familia_agents[]" rows="2" class="w-full px-2 py-1 border border-gray-300 rounded-md"></textarea>
                                            </td>
                                            <td class="p-2 border border-gray-800">
                                                <textarea name="reunion_familia_temes[]" rows="2" class="w-full px-2 py-1 border border-gray-300 rounded-md"></textarea>
                                            </td>
                                            <td class="p-2 border border-gray-800">
                                                <div class="flex items-start space-x-2">
                                                    <textarea name="reunion_familia_acords[]" rows="2" class="flex-grow px-2 py-1 border border-gray-300 rounded-md"></textarea>
                                                    <button type="button" class="delete-row text-red-600 hover:text-red-800 flex-shrink-0 mt-1">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                        </svg>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endif
                                </tbody>
                                <tr>
                                    <td colspan="4" class="p-2 border border-gray-800">
                                        <button type="button" id="add-reunio-familia" class="inline-flex items-center px-3 py-1 bg-blue-100 border border-blue-300 rounded-md font-medium text-xs text-blue-800 hover:bg-blue-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                            </svg>
                                            Afegir més files
                                        </button>
                                        <span id="reunions-familia-counter" class="ml-2 text-sm text-gray-500">1/10 files</span>
                                    </td>
                                </tr>
                            </table>

                            <!-- REUNIONS DE SEGUIMENT AMB ELS PROFESSIONALS -->
                            <table class="w-full border-collapse border border-gray-800 mb-8" style="table-layout: fixed;" id="reunions-professionals-table">
                                <tr>
                                    <td colspan="4" class="p-3 font-bold text-white text-xl" style="background-color: #6ab0e6; border: 1px solid #000;">
                                        REUNIONS DE SEGUIMENT AMB ELS PROFESSIONALS
                                    </td>
                                </tr>
                                <tr class="bg-blue-100">
                                    <td class="p-2 font-bold border border-gray-800" style="width: 15%;">Data</td>
                                    <td class="p-2 font-bold border border-gray-800" style="width: 25%;">Agents participants</td>
                                    <td class="p-2 font-bold border border-gray-800" style="width: 30%;">Temes tractats</td>
                                    <td class="p-2 font-bold border border-gray-800" style="width: 30%;">Acords</td>
                                </tr>
                                <tbody id="reunions-professionals-container">
                                    @if(isset($supportPlan->reunion_professionals) && is_array($supportPlan->reunion_professionals))
                                        @foreach($supportPlan->reunion_professionals as $index => $reunio)
                                            <tr class="reunion-professionals-row">
                                                <td class="p-2 border border-gray-800">
                                                    <input type="date" name="reunion_professionals_data[]" value="{{ $reunio['data'] ?? '' }}" class="w-full px-2 py-1 border border-gray-300 rounded-md">
                                                </td>
                                                <td class="p-2 border border-gray-800">
                                                    <textarea name="reunion_professionals_agents[]" rows="2" class="w-full px-2 py-1 border border-gray-300 rounded-md">{{ $reunio['agents'] ?? '' }}</textarea>
                                                </td>
                                                <td class="p-2 border border-gray-800">
                                                    <textarea name="reunion_professionals_temes[]" rows="2" class="w-full px-2 py-1 border border-gray-300 rounded-md">{{ $reunio['temes'] ?? '' }}</textarea>
                                                </td>
                                                <td class="p-2 border border-gray-800">
                                                    <div class="flex items-start space-x-2">
                                                        <textarea name="reunion_professionals_acords[]" rows="2" class="flex-grow px-2 py-1 border border-gray-300 rounded-md">{{ $reunio['acords'] ?? '' }}</textarea>
                                                        <button type="button" class="delete-row text-red-600 hover:text-red-800 flex-shrink-0 mt-1">
                                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                            </svg>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr class="reunion-professionals-row">
                                            <td class="p-2 border border-gray-800">
                                                <input type="date" name="reunion_professionals_data[]" class="w-full px-2 py-1 border border-gray-300 rounded-md">
                                            </td>
                                            <td class="p-2 border border-gray-800">
                                                <textarea name="reunion_professionals_agents[]" rows="2" class="w-full px-2 py-1 border border-gray-300 rounded-md"></textarea>
                                            </td>
                                            <td class="p-2 border border-gray-800">
                                                <textarea name="reunion_professionals_temes[]" rows="2" class="w-full px-2 py-1 border border-gray-300 rounded-md"></textarea>
                                            </td>
                                            <td class="p-2 border border-gray-800">
                                                <div class="flex items-start space-x-2">
                                                    <textarea name="reunion_professionals_acords[]" rows="2" class="flex-grow px-2 py-1 border border-gray-300 rounded-md"></textarea>
                                                    <button type="button" class="delete-row text-red-600 hover:text-red-800 flex-shrink-0 mt-1">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                        </svg>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endif
                                </tbody>
                                <tr>
                                    <td colspan="4" class="p-2 border border-gray-800">
                                        <button type="button" id="add-reunio-professionals" class="inline-flex items-center px-3 py-1 bg-blue-100 border border-blue-300 rounded-md font-medium text-xs text-blue-800 hover:bg-blue-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                            </svg>
                                            Afegir més files
                                        </button>
                                        <span id="reunions-professionals-counter" class="ml-2 text-sm text-gray-500">1/10 files</span>
                                    </td>
                                </tr>
                            </table>

                            <!-- ACORDS SOBRE LA CONTINUÏTAT -->
                            <table class="w-full border-collapse border border-gray-800 mb-8" style="table-layout: fixed;" id="acords-table">
                                <tr>
                                    <td colspan="4" class="p-3 font-bold text-white text-xl" style="background-color: #6ab0e6; border: 1px solid #000;">
                                        ACORDS SOBRE LA CONTINUÏTAT DEL PLA DE SUPORT INDIVIDUALITZAT
                                    </td>
                                </tr>
                                <tr class="bg-blue-100">
                                    <td class="p-2 font-bold border border-gray-800" style="width: 15%;">Data</td>
                                    <td class="p-2 font-bold border border-gray-800" style="width: 25%;">Agents participants</td>
                                    <td class="p-2 font-bold border border-gray-800" style="width: 30%;">Acord</td>
                                    <td class="p-2 font-bold border border-gray-800" style="width: 30%;">Observacions</td>
                                </tr>
                                <tbody id="acords-container">
                                    @if(isset($supportPlan->acords) && is_array($supportPlan->acords))
                                        @foreach($supportPlan->acords as $index => $acord)
                                            <tr class="acord-row">
                                                <td class="p-2 border border-gray-800">
                                                    <input type="date" name="acords_data[]" value="{{ $acord['data'] ?? '' }}" class="w-full px-2 py-1 border border-gray-300 rounded-md">
                                                </td>
                                                <td class="p-2 border border-gray-800">
                                                    <textarea name="acords_agents[]" rows="2" class="w-full px-2 py-1 border border-gray-300 rounded-md">{{ $acord['agents'] ?? '' }}</textarea>
                                                </td>
                                                <td class="p-2 border border-gray-800">
                                                    <div class="space-y-2">
                                                        @php
                                                            $tipus = is_array($acord['tipus'] ?? null) ? $acord['tipus'] : [];
                                                        @endphp
                                                        <div class="flex items-center">
                                                            <input type="checkbox" name="acords_tipus_{{ $loop->index }}[]" value="continuitat" {{ in_array('continuitat', $tipus) ? 'checked' : '' }} class="mr-2">
                                                            <span>Continuïtat</span>
                                                        </div>
                                                        <div class="flex items-center">
                                                            <input type="checkbox" name="acords_tipus_{{ $loop->index }}[]" value="modificacio" {{ in_array('modificacio', $tipus) ? 'checked' : '' }} class="mr-2">
                                                            <span>Modificació</span>
                                                        </div>
                                                        <div class="flex items-center">
                                                            <input type="checkbox" name="acords_tipus_{{ $loop->index }}[]" value="finalitzacio" {{ in_array('finalitzacio', $tipus) ? 'checked' : '' }} class="mr-2">
                                                            <span>Finalització</span>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="p-2 border border-gray-800">
                                                    <div class="flex items-start space-x-2">
                                                        <textarea name="acords_observacions[]" rows="2" class="flex-grow px-2 py-1 border border-gray-300 rounded-md">{{ $acord['observacions'] ?? '' }}</textarea>
                                                        <button type="button" class="delete-row text-red-600 hover:text-red-800 flex-shrink-0 mt-1">
                                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                            </svg>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr class="acord-row">
                                            <td class="p-2 border border-gray-800">
                                                <input type="date" name="acords_data[]" class="w-full px-2 py-1 border border-gray-300 rounded-md">
                                            </td>
                                            <td class="p-2 border border-gray-800">
                                                <textarea name="acords_agents[]" rows="2" class="w-full px-2 py-1 border border-gray-300 rounded-md"></textarea>
                                            </td>
                                            <td class="p-2 border border-gray-800">
                                                <div class="space-y-2">
                                                    <div class="flex items-center">
                                                        <input type="checkbox" name="acords_tipus_0[]" value="continuitat" class="mr-2">
                                                        <span>Continuïtat</span>
                                                    </div>
                                                    <div class="flex items-center">
                                                        <input type="checkbox" name="acords_tipus_0[]" value="modificacio" class="mr-2">
                                                        <span>Modificació</span>
                                                    </div>
                                                    <div class="flex items-center">
                                                        <input type="checkbox" name="acords_tipus_0[]" value="finalitzacio" class="mr-2">
                                                        <span>Finalització</span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="p-2 border border-gray-800">
                                                <div class="flex items-start space-x-2">
                                                    <textarea name="acords_observacions[]" rows="2" class="flex-grow px-2 py-1 border border-gray-300 rounded-md"></textarea>
                                                    <button type="button" class="delete-row text-red-600 hover:text-red-800 flex-shrink-0 mt-1">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                        </svg>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endif
                                </tbody>
                                <tr>
                                    <td colspan="4" class="p-2 border border-gray-800">
                                        <button type="button" id="add-acord" class="inline-flex items-center px-3 py-1 bg-blue-100 border border-blue-300 rounded-md font-medium text-xs text-blue-800 hover:bg-blue-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                            </svg>
                                            Afegir més files
                                        </button>
                                        <span id="acords-counter" class="ml-2 text-sm text-gray-500">1/10 files</span>
                                    </td>
                                </tr>
                            </table>
                        </div>

                        <div class="flex justify-end mt-4">
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition">
                                {{ __('Guardar Cambios') }}
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

        document.addEventListener('DOMContentLoaded', function() {
            function setupTable(tableId, rowClass, containerId, addButtonId, counterSpanId, maxRows) {
                const table = document.getElementById(tableId);
                if (!table) return null;

                const container = document.getElementById(containerId);
                const addButton = document.getElementById(addButtonId);
                const counterSpan = document.getElementById(counterSpanId);
                let rowCount = document.querySelectorAll('.' + rowClass).length;

                // Función para actualizar el contador
                function updateRowCount() {
                    if (counterSpan) {
                        counterSpan.textContent = rowCount + '/' + maxRows + ' files';
                    }
                    if (addButton) {
                        addButton.style.display = rowCount >= maxRows ? 'none' : 'inline-flex';
                    }
                }

                // Manejar eliminación de filas
                table.addEventListener('click', function(e) {
                    const deleteButton = e.target.closest('.delete-row');
                    if (deleteButton && rowCount > 1) {
                        const row = deleteButton.closest('tr');
                        row.remove();
                        rowCount--;
                        updateRowCount();
                    }
                });

                // Manejar adición de filas
                if (addButton) {
                    addButton.addEventListener('click', function() {
                        if (rowCount < maxRows) {
                            const lastRow = container.querySelector('.' + rowClass + ':last-child');
                            if (lastRow) {
                                const newRow = lastRow.cloneNode(true);
                                // Limpiar valores de los inputs
                                newRow.querySelectorAll('input, textarea').forEach(input => {
                                    input.value = '';
                                });
                                
                                // Si es la tabla de learning, actualizar el número
                                if (tableId === 'learning-table') {
                                    const spans = newRow.querySelectorAll('.font-bold');
                                    spans.forEach(span => {
                                        span.textContent = (rowCount + 1) + '.';
                                    });
                                }
                                
                                container.appendChild(newRow);
                                rowCount++;
                                updateRowCount();
                            }
                        }
                    });
                }

                // Inicializar contador y botones de eliminar
                updateRowCount();

                return {
                    updateRowCount: updateRowCount
                };
            }

            // Configurar tabla de competencias transversales
            const transversal = setupTable(
                'transversal-table',
                'transversal-row',
                'transversal-container',
                'add-transversal-row',
                'transversal-row-counter',
                10
            );

            // Configurar tabla de sabers
            const sabers = setupTable(
                'sabers-table',
                'saber-row',
                'sabers-container',
                'add-sabers-row',
                'sabers-row-counter',
                18
            );

            // Configurar tabla de competencias de áreas
            const learning = setupTable(
                'learning-table',
                'learning-row',
                'learning-container',
                'add-learning-row',
                'learning-row-counter',
                10
            );

            // Configurar tabla de reuniones con familia
            const reunionsFamily = setupTable(
                'reunions-familia-table',
                'reunion-familia-row',
                'reunions-familia-container',
                'add-reunion-familia-row',
                'reunions-familia-counter',
                10
            );

            // Configurar tabla de reuniones con profesionales
            const reunionsProfessionals = setupTable(
                'reunions-professionals-table',
                'reunion-professionals-row',
                'reunions-professionals-container',
                'add-reunion-professionals-row',
                'reunions-professionals-counter',
                10
            );

            // Configurar tabla de acuerdos
            const acords = setupTable(
                'acords-table',
                'acord-row',
                'acords-container',
                'add-acord-row',
                'acords-counter',
                10
            );
        });

        // Función para eliminar filas
        document.addEventListener('click', function(e) {
            if (e.target.closest('.delete-row')) {
                e.target.closest('tr').remove();
            }
        });
    </script>
</x-app-layout>
