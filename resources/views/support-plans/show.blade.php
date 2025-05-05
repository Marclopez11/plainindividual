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
                                        @php
                                            $justificationReasons = $supportPlan->justification_reasons ?? [];
                                            if (!is_array($justificationReasons)) {
                                                $justificationReasons = [];
                                            }
                                        @endphp
                                        <div class="flex items-start">
                                            <input type="checkbox" disabled {{ in_array('informe_reconeixement', $justificationReasons) ? 'checked' : '' }} class="mt-1 mr-2">
                                            <span>Informe de reconeixement de necessitat de suport educatiu</span>
                            </div>
                                        <div class="flex items-start">
                                            <input type="checkbox" disabled {{ in_array('avaluacio_psicopedagogica', $justificationReasons) ? 'checked' : '' }} class="mt-1 mr-2">
                                            <span>Avaluació psicopedagògica</span>
                            </div>
                                        <div class="flex items-start">
                                            <input type="checkbox" disabled {{ in_array('avaluacio_inicial_nouvingut', $justificationReasons) ? 'checked' : '' }} class="mt-1 mr-2">
                                            <span>Resultat de l'avaluació inicial de l'alumne/a nouvingut<sup>2</sup></span>
                        </div>
                                        <div class="flex items-start">
                                            <input type="checkbox" disabled {{ in_array('avaluacio_origen_estranger_aula', $justificationReasons) ? 'checked' : '' }} class="mt-1 mr-2">
                                            <span>Avaluació de l'alumne/a d'origen estranger que ja no assisteix a l'aula d'acollida però que rep suport a l'aula ordinària.</span>
                        </div>
                                        <div class="flex items-start">
                                            <input type="checkbox" disabled {{ in_array('avaluacio_origen_estranger_tardana', $justificationReasons) ? 'checked' : '' }} class="mt-1 mr-2">
                                            <span>Avaluació de l'alumne/a d'origen estranger amb necessitats educatives derivades de la incorporació tardana al sistema educatiu.</span>
                    </div>
                                        <div class="flex items-start">
                                            <input type="checkbox" disabled {{ in_array('decisio_comissio', $justificationReasons) ? 'checked' : '' }} class="mt-1 mr-2">
                                            <span>Decisió de la comissió d'atenció educativa inclusiva (CAEI) a proposta de</span>
                                            <span class="mx-1 font-semibold">{{ $supportPlan->commission_proponent ?? '' }}</span>
                                            <span>motivada per</span>
                                            <span class="ml-1 font-semibold">{{ $supportPlan->commission_motivation ?? '' }}</span>
                        </div>
                                        <div class="flex items-start">
                                            <input type="checkbox" disabled {{ in_array('altres', $justificationReasons) ? 'checked' : '' }} class="mt-1 mr-2">
                                            <span>Altres: {{ $supportPlan->justification_other ?? '' }}</span>
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
                                        {{ $supportPlan->brief_justification ?? '' }}
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
                                    <div class="p-2 bg-gray-50 rounded min-h-[80px]">
                                        {{ $supportPlan->competencies_alumne_capabilities ?? '-' }}
                        </div>
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
                                    <div class="p-2 bg-gray-50 rounded min-h-[80px]">
                                        {{ $supportPlan->learning_strong_points ?? '-' }}
                        </div>
                    </div>

                                <div class="mb-4">
                                    <p class="font-bold mb-2">Punts de millora:</p>
                                    <div class="p-2 bg-gray-50 rounded min-h-[80px]">
                                        {{ $supportPlan->learning_improvement_points ?? '-' }}
                        </div>
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
                                {{ $supportPlan->student_interests }}
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

                        @php
                            $transversalObjectives = $supportPlan->transversal_objectives ?? [];
                            $transversalCriteria = $supportPlan->transversal_criteria ?? [];
                            if (!is_array($transversalObjectives)) $transversalObjectives = [];
                            if (!is_array($transversalCriteria)) $transversalCriteria = [];
                            $maxRows = max(count($transversalObjectives), count($transversalCriteria));
                        @endphp

                        @if($maxRows > 0)
                            @for($i = 0; $i < $maxRows; $i++)
                                @if(!empty($transversalObjectives[$i] ?? '') || !empty($transversalCriteria[$i] ?? ''))
                                    <tr>
                                        <td class="p-2 border border-gray-800">
                                            {!! nl2br(e($transversalObjectives[$i] ?? '')) !!}
                                        </td>
                                        <td class="p-2 border border-gray-800">
                                            {!! nl2br(e($transversalCriteria[$i] ?? '')) !!}
                                        </td>
                                    </tr>
                                @endif
                            @endfor
                        @else
                            <tr>
                                <td colspan="2" class="p-2 border border-gray-800 text-center text-gray-500">
                                    No s'han definit competències transversals
                                </td>
                            </tr>
                        @endif
                    </table>

                    <!-- CONCRECIÓ DE LES COMPETÈNCIES DE LES ÀREES DEL PSI -->
                    <table class="w-full border-collapse border border-gray-800 mb-8" style="table-layout: fixed;">
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

                        @php
                            $learningObjectives = $supportPlan->learning_objectives ?? [];
                            $evaluationCriteria = $supportPlan->evaluation_criteria ?? [];
                            if (!is_array($learningObjectives)) $learningObjectives = [];
                            if (!is_array($evaluationCriteria)) $evaluationCriteria = [];
                            $maxRows = max(count($learningObjectives), count($evaluationCriteria));
                        @endphp

                        @if($maxRows > 0)
                            @for($i = 0; $i < $maxRows; $i++)
                                @if(!empty($learningObjectives[$i] ?? '') || !empty($evaluationCriteria[$i] ?? ''))
                                    <tr>
                                        <td class="p-2 border border-gray-800">
                                            {!! nl2br(e($learningObjectives[$i] ?? '')) !!}
                                        </td>
                                        <td class="p-2 border border-gray-800">
                                            {!! nl2br(e($evaluationCriteria[$i] ?? '')) !!}
                                        </td>
                                    </tr>
                                @endif
                            @endfor
                        @else
                            <tr>
                                <td colspan="2" class="p-2 border border-gray-800 text-center text-gray-500">
                                    No s'han definit competències de les àrees
                                </td>
                            </tr>
                        @endif
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
                            $areaMateria = $supportPlan->area_materia ?? [];
                            $blocSabers = $supportPlan->bloc_sabers ?? [];
                            $saberItems = $supportPlan->saber ?? [];

                            if (!is_array($areaMateria)) $areaMateria = [];
                            if (!is_array($blocSabers)) $blocSabers = [];
                            if (!is_array($saberItems)) $saberItems = [];

                            $maxRows = max(count($areaMateria), count($blocSabers), count($saberItems));
                        @endphp

                        @if($maxRows > 0)
                            @for($i = 0; $i < $maxRows; $i++)
                                @if(!empty($areaMateria[$i] ?? '') || !empty($blocSabers[$i] ?? '') || !empty($saberItems[$i] ?? ''))
                                    <tr>
                                        <td class="p-2 border border-gray-800">
                                            {{ $areaMateria[$i] ?? '' }}
                                        </td>
                                        <td class="p-2 border border-gray-800">
                                            {{ $blocSabers[$i] ?? '' }}
                                        </td>
                                        <td class="p-2 border border-gray-800">
                                            {!! nl2br(e($saberItems[$i] ?? '')) !!}
                                        </td>
                                    </tr>
                                @endif
                            @endfor
                        @else
                            <tr>
                                <td colspan="3" class="p-2 border border-gray-800 text-center text-gray-500">
                                    No s'han definit àrees, blocs de sabers i sabers
                                </td>
                            </tr>
                        @endif
                    </table>
                </div>

                <!-- Excel Format (Hidden by default) -->
                <div id="excel-format-content" class="hidden">
                    <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 mb-8" role="alert">
                        <p class="font-bold">Format Excel</p>
                        <p>Els camps per al format Excel estaran disponibles pròximament.</p>
                    </div>
                </div>

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
    </script>
</x-app-layout>
