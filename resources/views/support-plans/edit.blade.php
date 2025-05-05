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

                            @php
                                $areaMateria = old('area_materia', $supportPlan->area_materia ?? []);
                                $blocSabers = old('bloc_sabers', $supportPlan->bloc_sabers ?? []);
                                $saberItems = old('saber', $supportPlan->saber ?? []);

                                // Ensure we have at least 2 rows (filled or empty)
                                if (count($areaMateria) < 2) {
                                    $areaMateria = array_pad($areaMateria, 2, '');
                                }
                                if (count($blocSabers) < 2) {
                                    $blocSabers = array_pad($blocSabers, 2, '');
                                }
                                if (count($saberItems) < 2) {
                                    $saberItems = array_pad($saberItems, 2, '');
                                }

                                $sabersRowCount = max(count($areaMateria), count($blocSabers), count($saberItems));
                            @endphp

                            @for ($i = 0; $i < $sabersRowCount; $i++)
                                <tr>
                                    <td class="p-2 border border-gray-800">
                                        <input type="text" name="area_materia[]" value="{{ $areaMateria[$i] ?? '' }}" class="w-full px-2 py-1 border border-gray-300 rounded-md">
                                    </td>
                                    <td class="p-2 border border-gray-800">
                                        <input type="text" name="bloc_sabers[]" value="{{ $blocSabers[$i] ?? '' }}" class="w-full px-2 py-1 border border-gray-300 rounded-md">
                                    </td>
                                    <td class="p-2 border border-gray-800">
                                        <textarea name="saber[]" rows="2" class="w-full px-2 py-1 border border-gray-300 rounded-md">{{ $saberItems[$i] ?? '' }}</textarea>
                                    </td>
                                </tr>
                            @endfor

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
                                    <span id="sabers-row-counter" class="ml-2 text-sm text-gray-500">{{ $sabersRowCount }}/18 files</span>
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
                        </div>

                    <!-- Excel Format (Hidden by default) -->
                    <div id="excel-format-content" class="hidden">
                        <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 mb-8" role="alert">
                            <p class="font-bold">Format Excel</p>
                            <p>Els camps per al format Excel estaran disponibles pròximament.</p>
                            </div>
                        </div>

                    <div class="flex justify-end mt-4 space-x-3">
                        <a href="{{ route('support-plans.show', $supportPlan->id) }}" class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 focus:bg-gray-300 active:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition">
                            {{ __('Cancelar') }}
                        </a>
                        <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-800 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition">
                            {{ __('Actualizar Plan') }}
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

            // Àrees, bloc de sabers i sabers
            const addSabersButton = document.getElementById('add-sabers-row');
            const sabersContainer = document.getElementById('sabers-container');
            const sabersRowCounter = document.getElementById('sabers-row-counter');
            let sabersRowCount = {{ $sabersRowCount ?? 2 }}; // Initial row count from PHP
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
