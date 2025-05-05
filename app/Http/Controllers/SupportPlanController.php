<?php

namespace App\Http\Controllers;

use App\Models\SupportPlan;
use App\Models\Team;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\SupportPlanExport;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\Style\Font;
use PhpOffice\PhpWord\Style\Paragraph;
use PhpOffice\PhpWord\Style\Table;
use PhpOffice\PhpWord\Style\Color;
use PhpOffice\PhpWord\Style\Alignment;
use PhpOffice\PhpWord\Style\Spacing;
use PhpOffice\PhpWord\Style\Border;
use Illuminate\Support\Facades\Gate;
use PhpOffice\PhpWord\TemplateProcessor;

class SupportPlanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // This should already be handled by the auth middleware
        // but adding an extra check to be safe
        if (!$user) {
            return redirect()->route('login');
        }

        $userTeams = $user->ownedTeams->merge($user->teams);
        $teamIds = $userTeams->pluck('id');

        // Filter by team if a team_id is provided in the request
        if ($request->has('team_id') && in_array($request->team_id, $teamIds->toArray())) {
            $supportPlans = SupportPlan::where('team_id', $request->team_id)
                ->orderBy('created_at', 'desc')
                ->paginate(10);
            $currentTeam = Team::find($request->team_id);
        } else {
            // Show plans from all teams the user belongs to
            $supportPlans = SupportPlan::whereIn('team_id', $teamIds)
                ->orWhere('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->paginate(10);
            $currentTeam = null;
        }

        return view('support-plans.index', compact('supportPlans', 'userTeams', 'currentTeam'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = Auth::user();

        // This should already be handled by the auth middleware
        // but adding an extra check to be safe
        if (!$user) {
            return redirect()->route('login');
        }

        $userTeams = $user->ownedTeams->merge($user->teams);

        return view('support-plans.create', compact('userTeams'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'student_name' => 'required|string|max:255',
            'birth_date' => 'nullable|date',
            'birth_place' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
            'educational_level' => 'nullable|string|max:255',
            'course' => 'nullable|string|max:255',
            'group' => 'nullable|string|max:255',
            'school_year' => 'nullable|string|max:255',
            'tutor_name' => 'nullable|string|max:255',
            'assessment_team' => 'nullable|string|max:255',
            'teacher_name' => 'nullable|string|max:255',
            'revision_date' => 'nullable|date',
            'elaboration_date' => 'nullable|date',
            'family_agreement_date' => 'nullable|date',
            'school_incorporation_date' => 'nullable|date',
            'catalonia_arrival_date' => 'nullable|date',
            'educational_system_date' => 'nullable|date',
            'previous_schools' => 'nullable|string',
            'previous_schooling' => 'nullable|string',
            'course_retention' => 'nullable|string|max:255',
            'other_data' => 'nullable|string',
            'usual_language' => 'nullable|string|max:255',
            'other_languages' => 'nullable|string|max:255',
            'justification_reasons' => 'nullable|array',
            'justification_other' => 'nullable|string|max:255',
            'commission_proponent' => 'nullable|string|max:255',
            'commission_motivation' => 'nullable|string',
            'brief_justification' => 'nullable|string',
            'competencies_alumne_capabilities' => 'nullable|string',
            'learning_strong_points' => 'nullable|string',
            'learning_improvement_points' => 'nullable|string',
            'student_interests' => 'nullable|string',
            'transversal_objectives' => 'nullable|array',
            'transversal_criteria' => 'nullable|array',
            'learning_objectives' => 'nullable|array',
            'evaluation_criteria' => 'nullable|array',
            'area_materia' => 'nullable|array',
            'bloc_sabers' => 'nullable|array',
            'saber' => 'nullable|array',
            'team_id' => 'nullable|exists:teams,id',
        ]);

        $user = Auth::user();

        // Verify team membership if a team is selected
        if (!empty($validated['team_id'])) {
            $team = Team::find($validated['team_id']);
            if (!$team->hasUser($user) && $team->owner_id !== $user->id) {
                return redirect()->back()->with('error', 'No tienes permiso para asignar planes a este equipo.');
            }
        }

        // Add user_id to the validated data
        $validated['user_id'] = $user->id;

        $supportPlan = SupportPlan::create($validated);

        return redirect()->route('support-plans.show', $supportPlan->id)
            ->with('success', 'Plan de soporte creado correctamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $supportPlan = SupportPlan::findOrFail($id);

        if (Gate::denies('view', $supportPlan)) {
            abort(403, 'No tienes permiso para ver este plan de soporte.');
        }

        return view('support-plans.show', compact('supportPlan'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $supportPlan = SupportPlan::findOrFail($id);

        if (Gate::denies('update', $supportPlan)) {
            abort(403, 'No tienes permiso para editar este plan de soporte.');
        }

        $user = Auth::user();

        // This should already be handled by the auth middleware
        // but adding an extra check to be safe
        if (!$user) {
            return redirect()->route('login');
        }

        $userTeams = $user->ownedTeams->merge($user->teams);

        return view('support-plans.edit', compact('supportPlan', 'userTeams'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $supportPlan = SupportPlan::findOrFail($id);

        if (Gate::denies('update', $supportPlan)) {
            abort(403, 'No tienes permiso para actualizar este plan de soporte.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'student_name' => 'required|string|max:255',
            'birth_date' => 'nullable|date',
            'birth_place' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
            'educational_level' => 'nullable|string|max:255',
            'course' => 'nullable|string|max:255',
            'group' => 'nullable|string|max:255',
            'school_year' => 'nullable|string|max:255',
            'tutor_name' => 'nullable|string|max:255',
            'assessment_team' => 'nullable|string|max:255',
            'teacher_name' => 'nullable|string|max:255',
            'revision_date' => 'nullable|date',
            'elaboration_date' => 'nullable|date',
            'family_agreement_date' => 'nullable|date',
            'school_incorporation_date' => 'nullable|date',
            'catalonia_arrival_date' => 'nullable|date',
            'educational_system_date' => 'nullable|date',
            'previous_schools' => 'nullable|string',
            'previous_schooling' => 'nullable|string',
            'course_retention' => 'nullable|string|max:255',
            'other_data' => 'nullable|string',
            'usual_language' => 'nullable|string|max:255',
            'other_languages' => 'nullable|string|max:255',
            'justification_reasons' => 'nullable|array',
            'justification_other' => 'nullable|string|max:255',
            'commission_proponent' => 'nullable|string|max:255',
            'commission_motivation' => 'nullable|string',
            'brief_justification' => 'nullable|string',
            'competencies_alumne_capabilities' => 'nullable|string',
            'learning_strong_points' => 'nullable|string',
            'learning_improvement_points' => 'nullable|string',
            'student_interests' => 'nullable|string',
            'transversal_objectives' => 'nullable|array',
            'transversal_criteria' => 'nullable|array',
            'learning_objectives' => 'nullable|array',
            'evaluation_criteria' => 'nullable|array',
            'area_materia' => 'nullable|array',
            'bloc_sabers' => 'nullable|array',
            'saber' => 'nullable|array',
            'team_id' => 'nullable|exists:teams,id',
        ]);

        $user = Auth::user();

        // Verify team membership if team is being changed
        if (!empty($validated['team_id']) && $validated['team_id'] != $supportPlan->team_id) {
            $team = Team::find($validated['team_id']);
            if (!$team->hasUser($user) && $team->owner_id !== $user->id) {
                return redirect()->back()->with('error', 'No tienes permiso para asignar planes a este equipo.');
            }
        }

        $supportPlan->update($validated);

        return redirect()->route('support-plans.show', $supportPlan->id)
            ->with('success', 'Plan de soporte actualizado correctamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $supportPlan = SupportPlan::findOrFail($id);

        if (Gate::denies('delete', $supportPlan)) {
            abort(403, 'No tienes permiso para eliminar este plan de soporte.');
        }

        $supportPlan->delete();

        return redirect()->route('support-plans.index')
            ->with('success', 'Plan de soporte eliminado correctamente.');
    }

    /**
     * Export the support plan to Excel.
     */
    public function exportToExcel(string $id)
    {
        $supportPlan = SupportPlan::findOrFail($id);

        if (Gate::denies('view', $supportPlan)) {
            abort(403, 'No tienes permiso para exportar este plan de soporte.');
        }

        return Excel::download(new SupportPlanExport($supportPlan), 'plan_de_soporte.xlsx');
    }

    /**
     * Export the support plan to Word.
     */
    public function exportToWord(string $id)
    {
        $supportPlan = SupportPlan::findOrFail($id);

        if (Gate::denies('view', $supportPlan)) {
            abort(403, 'No tienes permiso para exportar este plan de soporte.');
        }

        // Ruta a la plantilla Word (debe existir en esta ubicación)
        $templatePath = storage_path('app/templates/plantilla_plan_soporte.docx');

        // Verificar si existe la plantilla
        if (!file_exists($templatePath)) {
            return redirect()->back()->with('error', 'La plantilla de Word no se encontró. Por favor, contacte con el administrador.');
        }

        // Crear un objeto de procesador de plantillas
        $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor($templatePath);

        // Definir valores a sustituir en la plantilla
        // Los marcadores en la plantilla deben tener el formato ${nombre_variable}
        $templateProcessor->setValue('nombre_estudiante', $supportPlan->student_name ?? '');
        $templateProcessor->setValue('fecha_nacimiento', $supportPlan->birth_date ? $supportPlan->birth_date->format('d/m/Y') : '');
        $templateProcessor->setValue('nombre_tutores', $supportPlan->tutor_name ?? '');
        $templateProcessor->setValue('lugar_nacimiento', $supportPlan->birth_place ?? '');
        $templateProcessor->setValue('telefono', $supportPlan->phone ?? '');
        $templateProcessor->setValue('direccion', $supportPlan->address ?? '');
        $templateProcessor->setValue('lengua_habitual', $supportPlan->usual_language ?? '');
        $templateProcessor->setValue('otras_lenguas', $supportPlan->other_languages ?? '');

        // Datos escolares
        $templateProcessor->setValue('curso', $supportPlan->course ?? '');
        $templateProcessor->setValue('grupo', $supportPlan->group ?? '');
        $templateProcessor->setValue('tutor', $supportPlan->teacher_name ?? '');
        $templateProcessor->setValue('fecha_incorporacion_centro', $supportPlan->school_incorporation_date ? $supportPlan->school_incorporation_date->format('d/m/Y') : '');
        $templateProcessor->setValue('fecha_llegada_catalunya', $supportPlan->catalonia_arrival_date ? $supportPlan->catalonia_arrival_date->format('d/m/Y') : '');
        $templateProcessor->setValue('fecha_incorporacion_sistema', $supportPlan->educational_system_date ? $supportPlan->educational_system_date->format('d/m/Y') : '');
        $templateProcessor->setValue('centros_anteriores', $supportPlan->previous_schools ?? '');
        $templateProcessor->setValue('escolarizacion_previa', $supportPlan->previous_schooling ?? '');
        $templateProcessor->setValue('retencion_curso', $supportPlan->course_retention ?? '');
        $templateProcessor->setValue('otra_informacion', $supportPlan->other_data ?? '');

        // Datos de justificación
        $justificationReasons = $supportPlan->justification_reasons ?? [];
        if (!is_array($justificationReasons)) {
            $justificationReasons = [];
        }

        // Utilizamos diferentes opciones de símbolos de casilla para garantizar compatibilidad
        // Opción 1: Unicode checkbox symbols - ☑ (checked) and ☐ (unchecked)
        // Opción 2: Wingdings font symbols - þ (checked) and ¨ (unchecked)
        // Opción 3: Simple symbols - [X] and [ ]

        // Seleccionamos la Opción 1 que suele tener mejor compatibilidad:
        $checkedBox = '☑';
        $uncheckedBox = '☐';

        // En caso de problemas con los símbolos Unicode, puedes cambiar a:
        // $checkedBox = '[X]';
        // $uncheckedBox = '[ ]';

        $templateProcessor->setValue('motivado_informe_reconeixement', in_array('informe_reconeixement', $justificationReasons) ? $checkedBox : $uncheckedBox);
        $templateProcessor->setValue('motivado_avaluacio_psicopedagogica', in_array('avaluacio_psicopedagogica', $justificationReasons) ? $checkedBox : $uncheckedBox);
        $templateProcessor->setValue('motivado_avaluacio_inicial_nouvingut', in_array('avaluacio_inicial_nouvingut', $justificationReasons) ? $checkedBox : $uncheckedBox);
        $templateProcessor->setValue('motivado_avaluacio_origen_estranger_aula', in_array('avaluacio_origen_estranger_aula', $justificationReasons) ? $checkedBox : $uncheckedBox);
        $templateProcessor->setValue('motivado_avaluacio_origen_estranger_tardana', in_array('avaluacio_origen_estranger_tardana', $justificationReasons) ? $checkedBox : $uncheckedBox);
        $templateProcessor->setValue('motivado_decisio_comissio', in_array('decisio_comissio', $justificationReasons) ? $checkedBox : $uncheckedBox);
        $templateProcessor->setValue('motivado_altres', in_array('altres', $justificationReasons) ? $checkedBox : $uncheckedBox);

        $templateProcessor->setValue('justificacion_other', $supportPlan->justification_other ?? '');
        $templateProcessor->setValue('commission_proponent', $supportPlan->commission_proponent ?? '');
        $templateProcessor->setValue('commission_motivation', $supportPlan->commission_motivation ?? '');
        $templateProcessor->setValue('brief_justification', $supportPlan->brief_justification ?? '');

        // Datos de competencias, condiciones personales e intereses
        $templateProcessor->setValue('competencies_alumne_capabilities', $supportPlan->competencies_alumne_capabilities ?? '');
        $templateProcessor->setValue('learning_strong_points', $supportPlan->learning_strong_points ?? '');
        $templateProcessor->setValue('learning_improvement_points', $supportPlan->learning_improvement_points ?? '');
        $templateProcessor->setValue('student_interests', $supportPlan->student_interests ?? '');

        // Procesar competencias transversales
        $transversalObjectives = $supportPlan->transversal_objectives ?? [];
        $transversalCriteria = $supportPlan->transversal_criteria ?? [];

        if (!is_array($transversalObjectives)) {
            $transversalObjectives = [];
        }

        if (!is_array($transversalCriteria)) {
            $transversalCriteria = [];
        }

        // Filtrar filas vacías
        $filteredTransversalRows = [];
        foreach ($transversalObjectives as $key => $objective) {
            if (!empty(trim($objective)) || !empty(trim($transversalCriteria[$key] ?? ''))) {
                $filteredTransversalRows[] = [
                    'objective' => $objective,
                    'criteria' => $transversalCriteria[$key] ?? ''
                ];
            }
        }

        $transversalCount = count($filteredTransversalRows);

        // Establecer valores directamente para competencias transversales
        // Insertar hasta 14 filas (máximo definido para competencias transversales)
        $maxTransversalRows = 14;

        // Manejar los marcadores de objetivo_transversal y criterio_transversal
        for ($i = 1; $i <= $maxTransversalRows; $i++) {
            // Si hay datos para esta fila, insertarlos
            if ($i <= $transversalCount) {
                $templateProcessor->setValue("objetivo_transversal#{$i}", $filteredTransversalRows[$i-1]['objective']);
                $templateProcessor->setValue("criterio_transversal#{$i}", $filteredTransversalRows[$i-1]['criteria']);
            }
            // Si no hay datos, dejar en blanco
            else {
                $templateProcessor->setValue("objetivo_transversal#{$i}", "");
                $templateProcessor->setValue("criterio_transversal#{$i}", "");
            }
        }

        // También manejar los marcadores de transversal_objective y transversal_criteria
        for ($i = 1; $i <= $maxTransversalRows; $i++) {
            // Si hay datos para esta fila, insertarlos
            if ($i <= $transversalCount) {
                $templateProcessor->setValue("transversal_objective#{$i}", $filteredTransversalRows[$i-1]['objective']);
                $templateProcessor->setValue("transversal_criteria#{$i}", $filteredTransversalRows[$i-1]['criteria']);
            }
            // Si no hay datos, dejar en blanco
            else {
                $templateProcessor->setValue("transversal_objective#{$i}", "");
                $templateProcessor->setValue("transversal_criteria#{$i}", "");
            }
        }

        // Procesar objetivos de aprendizaje y criterios de evaluación
        $learningObjectives = $supportPlan->learning_objectives ?? [];
        $evaluationCriteria = $supportPlan->evaluation_criteria ?? [];

        if (!is_array($learningObjectives)) {
            $learningObjectives = [];
        }

        if (!is_array($evaluationCriteria)) {
            $evaluationCriteria = [];
        }

        // Filtrar filas vacías
        $filteredLearningRows = [];
        foreach ($learningObjectives as $key => $objective) {
            if (!empty(trim($objective)) || !empty(trim($evaluationCriteria[$key] ?? ''))) {
                $filteredLearningRows[] = [
                    'objective' => $objective,
                    'criteria' => $evaluationCriteria[$key] ?? ''
                ];
            }
        }

        $learningCount = count($filteredLearningRows);

        // Establecer valores directamente para objetivos de aprendizaje
        // Insertar hasta 22 filas para asegurarnos de cubrir todos los marcadores
        $maxLearningRows = 22;
        for ($i = 1; $i <= $maxLearningRows; $i++) {
            // Si hay datos para esta fila, insertarlos
            if ($i <= $learningCount) {
                $templateProcessor->setValue("objetivo#{$i}", $filteredLearningRows[$i-1]['objective']);
                $templateProcessor->setValue("criterio#{$i}", $filteredLearningRows[$i-1]['criteria']);
            }
            // Si no hay datos, dejar en blanco
            else {
                $templateProcessor->setValue("objetivo#{$i}", "");
                $templateProcessor->setValue("criterio#{$i}", "");
            }

            // También manejar los marcadores learning_objective y evaluation_criteria por si acaso
            try {
                $templateProcessor->setValue("learning_objective#{$i}", $i <= $learningCount ? $filteredLearningRows[$i-1]['objective'] : "");
                $templateProcessor->setValue("evaluation_criteria#{$i}", $i <= $learningCount ? $filteredLearningRows[$i-1]['criteria'] : "");
            } catch (\Exception $e) {
                // Ignorar errores, estos marcadores podrían no existir
            }
        }

        // Procesar áreas, bloques de saberes y saberes
        $areaMateria = $supportPlan->area_materia ?? [];
        $blocSabers = $supportPlan->bloc_sabers ?? [];
        $saberItems = $supportPlan->saber ?? [];

        if (!is_array($areaMateria)) {
            $areaMateria = [];
        }

        if (!is_array($blocSabers)) {
            $blocSabers = [];
        }

        if (!is_array($saberItems)) {
            $saberItems = [];
        }

        // Crear un array de filas que combina los tres arrays
        $saberRows = [];
        $maxSaberIdx = max(count($areaMateria), count($blocSabers), count($saberItems));

        for ($i = 0; $i < $maxSaberIdx; $i++) {
            if (!empty(trim($areaMateria[$i] ?? '')) || !empty(trim($blocSabers[$i] ?? '')) || !empty(trim($saberItems[$i] ?? ''))) {
                $saberRows[] = [
                    'area' => $areaMateria[$i] ?? '',
                    'bloc' => $blocSabers[$i] ?? '',
                    'saber' => $saberItems[$i] ?? ''
                ];
            }
        }

        // Establecer valores para la tabla de áreas, bloques y saberes
        // Intentar con distintos nombres de marcadores
        try {
            // Intentar crear una tabla
            if (count($saberRows) > 0) {
                $tableContent = '<table style="width:100%; border-collapse: collapse;">';
                $tableContent .= '<tr style="background-color: #6ab0e6; color: white;">';
                $tableContent .= '<th style="border: 1px solid #000; padding: 5px; width: 30%;">Àrea o Matèria</th>';
                $tableContent .= '<th style="border: 1px solid #000; padding: 5px; width: 30%;">Bloc de sabers</th>';
                $tableContent .= '<th style="border: 1px solid #000; padding: 5px; width: 40%;">Saber</th>';
                $tableContent .= '</tr>';

                foreach ($saberRows as $row) {
                    $tableContent .= '<tr>';
                    $tableContent .= '<td style="border: 1px solid #000; padding: 5px;">' . $row['area'] . '</td>';
                    $tableContent .= '<td style="border: 1px solid #000; padding: 5px;">' . $row['bloc'] . '</td>';
                    $tableContent .= '<td style="border: 1px solid #000; padding: 5px;">' . $row['saber'] . '</td>';
                    $tableContent .= '</tr>';
                }

                $tableContent .= '</table>';

                // Intentar con diferentes marcadores para la tabla de saberes
                foreach (['tabla_saberes', 'taula_sabers', 'sabers_table'] as $marker) {
                    try {
                        $templateProcessor->setValue($marker, $tableContent);
                        break;
                    } catch (\Exception $e) {
                        continue;
                    }
                }
            }
        } catch (\Exception $e) {
            // Si falla la creación de la tabla HTML, intentar con filas individuales
            $maxSabers = 25; // Máximo de filas en la plantilla

            for ($i = 1; $i <= $maxSabers; $i++) {
                $idx = $i - 1;
                // Si hay datos para esta fila, insertarlos
                if ($idx < count($saberRows)) {
                    try {
                        $templateProcessor->setValue("area_materia#{$i}", $saberRows[$idx]['area']);
                        $templateProcessor->setValue("bloc_sabers#{$i}", $saberRows[$idx]['bloc']);
                        $templateProcessor->setValue("saber#{$i}", $saberRows[$idx]['saber']);
                    } catch (\Exception $e) {
                        // Intentar con nombres alternativos
                        try {
                            $templateProcessor->setValue("area#{$i}", $saberRows[$idx]['area']);
                            $templateProcessor->setValue("bloc#{$i}", $saberRows[$idx]['bloc']);
                            $templateProcessor->setValue("saber_item#{$i}", $saberRows[$idx]['saber']);
                        } catch (\Exception $e) {
                            continue;
                        }
                    }
                }
                // Si no hay datos, dejar en blanco
                else {
                    try {
                        $templateProcessor->setValue("area_materia#{$i}", "");
                        $templateProcessor->setValue("bloc_sabers#{$i}", "");
                        $templateProcessor->setValue("saber#{$i}", "");
                    } catch (\Exception $e) {
                        // Intentar con nombres alternativos
                        try {
                            $templateProcessor->setValue("area#{$i}", "");
                            $templateProcessor->setValue("bloc#{$i}", "");
                            $templateProcessor->setValue("saber_item#{$i}", "");
                        } catch (\Exception $e) {
                            continue;
                        }
                    }
                }
            }
        }

        // Generar el nombre del archivo
        $filename = 'plan_de_soporte_' . $supportPlan->student_name . '_' . date('Y-m-d') . '.docx';
        $filename = str_replace(' ', '_', $filename); // Reemplazar espacios con guiones bajos

        // Guardar el documento en un archivo temporal
        $tempFile = tempnam(sys_get_temp_dir(), 'plan_soporte');
        $templateProcessor->saveAs($tempFile);

        // Descargar el archivo
        return response()->download($tempFile, $filename)->deleteFileAfterSend(true);
    }
}
