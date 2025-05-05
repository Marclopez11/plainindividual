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
            'professionals' => 'nullable|array',
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
            'professionals' => 'nullable|array',
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

        try {
            // Ruta a la plantilla Word
            $templatePath = storage_path('app/templates/plantilla_plan_soporte.docx');

            // Verificar si existe la plantilla
            if (!file_exists($templatePath)) {
                throw new \Exception('La plantilla de Word no se encontró.');
            }

            // Procesar variables básicas
            $variables = $this->prepareTemplateVariables($supportPlan);

            // Procesar los datos de áreas, bloques y saberes
            $variables = array_merge($variables, $this->prepareSaberVariables($supportPlan));

            // Usar el enfoque directo para procesar la plantilla
            $tempFile = $this->processDocxTemplate($templatePath, $variables);

            // Generar el nombre del archivo
            $filename = 'plan_de_soporte_' . $supportPlan->student_name . '_' . date('Y-m-d') . '.docx';
            $filename = str_replace(' ', '_', $filename); // Reemplazar espacios con guiones bajos

            // Descargar el archivo
            return response()->download($tempFile, $filename)->deleteFileAfterSend(true);

        } catch (\Exception $e) {
            // Si hay un problema con la plantilla, intentar regenerarla
            try {
                // Crear una copia nueva de la plantilla
                $templateBackup = storage_path('app/templates/plantilla_plan_soporte.docx.bak');

                // Si existe una copia de seguridad, intentar usarla
                if (file_exists($templateBackup)) {
                    // Restaurar la plantilla desde la copia de seguridad
                    copy($templateBackup, storage_path('app/templates/plantilla_plan_soporte.docx'));

                    // Reintentar con la plantilla restaurada
                    $variables = $this->prepareTemplateVariables($supportPlan);
                    $variables = array_merge($variables, $this->prepareSaberVariables($supportPlan));
                    $tempFile = $this->processDocxTemplate(storage_path('app/templates/plantilla_plan_soporte.docx'), $variables);

                    return response()->download($tempFile, $filename)->deleteFileAfterSend(true);
                }

                // Si no existe copia de seguridad, redirigir con mensaje de error
                return redirect()->back()->with('error', 'No se pudo generar el documento Word. La plantilla parece estar dañada y no se encontró una copia de seguridad: ' . $e->getMessage());
            } catch (\Exception $innerException) {
                // Si todos los intentos fallan, mostrar un mensaje de error claro
                return redirect()->back()->with('error',
                    'Error al generar el documento Word: ' . $e->getMessage() .
                    '<br>El problema puede ser con la plantilla Word. Verifique que el archivo existe y no está dañado, o contacte al administrador.');
            }
        }
    }

    /**
     * Preparar variables para la plantilla
     */
    private function prepareTemplateVariables($supportPlan)
    {
        $variables = [];

        // Datos personales
        $variables['nombre_estudiante'] = $supportPlan->student_name ?? '';
        $variables['fecha_nacimiento'] = $supportPlan->birth_date ? $supportPlan->birth_date->format('d/m/Y') : '';
        $variables['nombre_tutores'] = $supportPlan->tutor_name ?? '';
        $variables['lugar_nacimiento'] = $supportPlan->birth_place ?? '';
        $variables['telefono'] = $supportPlan->phone ?? '';
        $variables['direccion'] = $supportPlan->address ?? '';
        $variables['lengua_habitual'] = $supportPlan->usual_language ?? '';
        $variables['otras_lenguas'] = $supportPlan->other_languages ?? '';

        // Datos escolares
        $variables['curso'] = $supportPlan->course ?? '';
        $variables['grupo'] = $supportPlan->group ?? '';
        $variables['tutor'] = $supportPlan->teacher_name ?? '';
        $variables['fecha_incorporacion_centro'] = $supportPlan->school_incorporation_date ? $supportPlan->school_incorporation_date->format('d/m/Y') : '';
        $variables['fecha_llegada_catalunya'] = $supportPlan->catalonia_arrival_date ? $supportPlan->catalonia_arrival_date->format('d/m/Y') : '';
        $variables['fecha_incorporacion_sistema'] = $supportPlan->educational_system_date ? $supportPlan->educational_system_date->format('d/m/Y') : '';
        $variables['centros_anteriores'] = $supportPlan->previous_schools ?? '';
        $variables['escolarizacion_previa'] = $supportPlan->previous_schooling ?? '';
        $variables['retencion_curso'] = $supportPlan->course_retention ?? '';
        $variables['otra_informacion'] = $supportPlan->other_data ?? '';

        // Datos de competencias, condiciones personales e intereses
        $variables['competencies_alumne_capabilities'] = $supportPlan->competencies_alumne_capabilities ?? '';
        $variables['learning_strong_points'] = $supportPlan->learning_strong_points ?? '';
        $variables['learning_improvement_points'] = $supportPlan->learning_improvement_points ?? '';
        $variables['student_interests'] = $supportPlan->student_interests ?? '';
        $variables['brief_justification'] = $supportPlan->brief_justification ?? '';

        // Marcadores de checkbox
        $checkedBox = '☑';
        $uncheckedBox = '☐';

        // Datos de justificación
        $justificationReasons = $supportPlan->justification_reasons ?? [];
        if (!is_array($justificationReasons)) {
            $justificationReasons = [];
        }

        $variables['motivado_informe_reconeixement'] = in_array('informe_reconeixement', $justificationReasons) ? $checkedBox : $uncheckedBox;
        $variables['motivado_avaluacio_psicopedagogica'] = in_array('avaluacio_psicopedagogica', $justificationReasons) ? $checkedBox : $uncheckedBox;
        $variables['motivado_avaluacio_inicial_nouvingut'] = in_array('avaluacio_inicial_nouvingut', $justificationReasons) ? $checkedBox : $uncheckedBox;
        $variables['motivado_avaluacio_origen_estranger_aula'] = in_array('avaluacio_origen_estranger_aula', $justificationReasons) ? $checkedBox : $uncheckedBox;
        $variables['motivado_avaluacio_origen_estranger_tardana'] = in_array('avaluacio_origen_estranger_tardana', $justificationReasons) ? $checkedBox : $uncheckedBox;
        $variables['motivado_decisio_comissio'] = in_array('decisio_comissio', $justificationReasons) ? $checkedBox : $uncheckedBox;
        $variables['motivado_altres'] = in_array('altres', $justificationReasons) ? $checkedBox : $uncheckedBox;

        $variables['justificacion_other'] = $supportPlan->justification_other ?? '';
        $variables['commission_proponent'] = $supportPlan->commission_proponent ?? '';
        $variables['commission_motivation'] = $supportPlan->commission_motivation ?? '';

        // Datos de professionals i serveis
        $professionals = $supportPlan->professionals ?? [];
        if (!is_array($professionals)) {
            $professionals = [];
        }

        // Marcadores de professionals
        $profesionalMarkers = [
            'tutor_responsable', 'tutor_aula_acollida', 'suport_intensiu', 'aula_integral',
            'mestre_educacio_especial', 'assessor_llengua', 'altres_professionals',
            'equip_assessorament', 'serveis_socials', 'centre_salut_mental',
            'centres_recursos', 'suports_externs', 'activitats_extraescolars',
            'beques_ajuts', 'altres_serveis'
        ];

        foreach ($profesionalMarkers as $marker) {
            $variables['professional_' . $marker] = in_array($marker, $professionals) ? $checkedBox : $uncheckedBox;
        }

        // Procesar arrays transversales
        $variables = array_merge($variables, $this->prepareTransversalVariables($supportPlan));
        $variables = array_merge($variables, $this->prepareLearningVariables($supportPlan));

        return $variables;
    }

    /**
     * Preparar variables para áreas, bloques y saberes
     */
    private function prepareSaberVariables($supportPlan)
    {
        $variables = [];

        $areaMateria = $supportPlan->area_materia ?? [];
        $blocSabers = $supportPlan->bloc_sabers ?? [];
        $saberItems = $supportPlan->saber ?? [];

        if (!is_array($areaMateria)) $areaMateria = [];
        if (!is_array($blocSabers)) $blocSabers = [];
        if (!is_array($saberItems)) $saberItems = [];

        // Crear un array de filas que combina los tres arrays
        $saberRows = [];
        $maxSaberIdx = max(count($areaMateria), count($blocSabers), count($saberItems));

        for ($i = 0; $i < $maxSaberIdx; $i++) {
            $saberRows[] = [
                'area' => $areaMateria[$i] ?? '',
                'bloc' => $blocSabers[$i] ?? '',
                'saber' => $saberItems[$i] ?? ''
            ];
        }

        // Agregar variables para cada fila
        $maxSabers = 18; // Máximo de filas en la plantilla

        for ($i = 1; $i <= $maxSabers; $i++) {
            $idx = $i - 1;

            $variables['area_materia#' . $i] = ($idx < count($saberRows)) ? $saberRows[$idx]['area'] : "";
            $variables['bloc_sabers#' . $i] = ($idx < count($saberRows)) ? $saberRows[$idx]['bloc'] : "";
            $variables['saber#' . $i] = ($idx < count($saberRows)) ? $saberRows[$idx]['saber'] : "";
        }

        return $variables;
    }

    /**
     * Preparar variables para competencias transversales
     */
    private function prepareTransversalVariables($supportPlan)
    {
        $variables = [];

        $transversalObjectives = $supportPlan->transversal_objectives ?? [];
        $transversalCriteria = $supportPlan->transversal_criteria ?? [];

        if (!is_array($transversalObjectives)) $transversalObjectives = [];
        if (!is_array($transversalCriteria)) $transversalCriteria = [];

        $filteredRows = [];
        foreach ($transversalObjectives as $key => $objective) {
            if (!empty(trim($objective)) || !empty(trim($transversalCriteria[$key] ?? ''))) {
                $filteredRows[] = [
                    'objective' => $objective,
                    'criteria' => $transversalCriteria[$key] ?? ''
                ];
            }
        }

        $rowCount = count($filteredRows);
        $maxRows = 14;

        for ($i = 1; $i <= $maxRows; $i++) {
            $value1 = ($i <= $rowCount) ? $filteredRows[$i-1]['objective'] : "";
            $value2 = ($i <= $rowCount) ? $filteredRows[$i-1]['criteria'] : "";

            // Asignar variables para diferentes formatos posibles
            $variables['objetivo_transversal#' . $i] = $value1;
            $variables['criterio_transversal#' . $i] = $value2;
            $variables['transversal_objective#' . $i] = $value1;
            $variables['transversal_criteria#' . $i] = $value2;
        }

        return $variables;
    }

    /**
     * Preparar variables para objetivos de aprendizaje
     */
    private function prepareLearningVariables($supportPlan)
    {
        $variables = [];

        $learningObjectives = $supportPlan->learning_objectives ?? [];
        $evaluationCriteria = $supportPlan->evaluation_criteria ?? [];

        if (!is_array($learningObjectives)) $learningObjectives = [];
        if (!is_array($evaluationCriteria)) $evaluationCriteria = [];

        $filteredRows = [];
        foreach ($learningObjectives as $key => $objective) {
            if (!empty(trim($objective)) || !empty(trim($evaluationCriteria[$key] ?? ''))) {
                $filteredRows[] = [
                    'objective' => $objective,
                    'criteria' => $evaluationCriteria[$key] ?? ''
                ];
            }
        }

        $rowCount = count($filteredRows);
        $maxRows = 22;

        for ($i = 1; $i <= $maxRows; $i++) {
            $value1 = ($i <= $rowCount) ? $filteredRows[$i-1]['objective'] : "";
            $value2 = ($i <= $rowCount) ? $filteredRows[$i-1]['criteria'] : "";

            // Asignar variables para diferentes formatos posibles
            $variables['objetivo#' . $i] = $value1;
            $variables['criterio#' . $i] = $value2;
            $variables['learning_objective#' . $i] = $value1;
            $variables['evaluation_criteria#' . $i] = $value2;
        }

        return $variables;
    }

    /**
     * Método para manejar directamente el reemplazo de variables en archivos docx
     * Este enfoque es más fiable para plantillas que pueden tener formatos específicos
     * @param string $templatePath Ruta al archivo de plantilla
     * @param array $variables Variables a reemplazar [clave => valor]
     * @return string Ruta al archivo temporal con los reemplazos
     */
    private function processDocxTemplate($templatePath, $variables)
    {
        // Crear un archivo temporal para el documento procesado
        $tempFile = tempnam(sys_get_temp_dir(), 'docx_processed');
        copy($templatePath, $tempFile);

        try {
            // Abrir el archivo como un archivo zip
            $zip = new \ZipArchive();
            if ($zip->open($tempFile) !== true) {
                throw new \Exception("No se pudo abrir el archivo de plantilla como ZIP");
            }

            // El contenido principal del documento está en word/document.xml
            $contentXml = $zip->getFromName('word/document.xml');
            if ($contentXml === false) {
                throw new \Exception("No se pudo leer el contenido del documento");
            }

            // Reemplazar variables en el XML
            foreach ($variables as $key => $value) {
                // Escapar caracteres especiales en XML
                $value = htmlspecialchars($value, ENT_XML1, 'UTF-8');

                // Reemplazar en el formato ${variable}
                $contentXml = str_replace('${'.$key.'}', $value, $contentXml);

                // Si la variable tiene un formato numerado como ${variable#1}, ${variable#2}, etc.
                if (strpos($key, '#') !== false) {
                    // La clave ya incluye el índice, así que hacemos un reemplazo directo
                    $contentXml = str_replace('${'.$key.'}', $value, $contentXml);
                }
            }

            // Actualizar el archivo XML en el ZIP
            $zip->addFromString('word/document.xml', $contentXml);

            // Comprobar también archivos de encabezado y pie de página que pueden contener variables
            $headerFiles = $this->getZipFilesByPattern($zip, 'word/header*.xml');
            $footerFiles = $this->getZipFilesByPattern($zip, 'word/footer*.xml');

            // Procesar archivos de encabezado
            foreach ($headerFiles as $headerFile) {
                $headerXml = $zip->getFromName($headerFile);
                if ($headerXml !== false) {
                    foreach ($variables as $key => $value) {
                        $value = htmlspecialchars($value, ENT_XML1, 'UTF-8');
                        $headerXml = str_replace('${'.$key.'}', $value, $headerXml);
                        if (strpos($key, '#') !== false) {
                            $headerXml = str_replace('${'.$key.'}', $value, $headerXml);
                        }
                    }
                    $zip->addFromString($headerFile, $headerXml);
                }
            }

            // Procesar archivos de pie de página
            foreach ($footerFiles as $footerFile) {
                $footerXml = $zip->getFromName($footerFile);
                if ($footerXml !== false) {
                    foreach ($variables as $key => $value) {
                        $value = htmlspecialchars($value, ENT_XML1, 'UTF-8');
                        $footerXml = str_replace('${'.$key.'}', $value, $footerXml);
                        if (strpos($key, '#') !== false) {
                            $footerXml = str_replace('${'.$key.'}', $value, $footerXml);
                        }
                    }
                    $zip->addFromString($footerFile, $footerXml);
                }
            }

            // Cerrar el archivo zip
            $zip->close();

            return $tempFile;
        } catch (\Exception $e) {
            // Si ocurre un error, eliminar el archivo temporal y relanzar la excepción
            if (file_exists($tempFile)) {
                unlink($tempFile);
            }
            throw $e;
        }
    }

    /**
     * Obtener archivos del ZIP que coinciden con un patrón
     */
    private function getZipFilesByPattern($zip, $pattern)
    {
        $matchingFiles = [];

        for ($i = 0; $i < $zip->numFiles; $i++) {
            $filename = $zip->getNameIndex($i);
            if (fnmatch($pattern, $filename)) {
                $matchingFiles[] = $filename;
            }
        }

        return $matchingFiles;
    }
}
