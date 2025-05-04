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

                        <!-- Sexta fila: Altres informacions (fila completa) -->
                        <tr>
                            <td class="p-2 font-bold border border-gray-800">Altres informacions d'interès:</td>
                            <td class="p-2 border border-gray-800" colspan="3">
                                <textarea name="other_data" rows="3" class="w-full px-2 py-1 border-0 border-b border-gray-300 bg-gray-50/50 shadow-sm focus:border-indigo-500 focus:bg-white focus:shadow-md focus:ring-0 transition-all duration-200">{{ old('other_data', $supportPlan->other_data) }}</textarea>
                                @error('other_data')<span class="text-red-500 text-xs">{{ $message }}</span>@enderror
                            </td>
                        </tr>
                    </table>

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
</x-app-layout>
