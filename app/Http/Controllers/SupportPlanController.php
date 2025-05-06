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
use PhpOffice\PhpWord\SimpleType\TblWidth;
use PhpOffice\PhpWord\SimpleType\Jc;
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
            'timetable_data' => 'nullable|string',
            'timetable_name' => 'nullable|string|max:255',
            
            // Reuniones con familia
            'reunion_familia_data' => 'nullable|array',
            'reunion_familia_data.*' => 'nullable|date',
            'reunion_familia_agents' => 'nullable|array',
            'reunion_familia_agents.*' => 'nullable|string',
            'reunion_familia_temes' => 'nullable|array',
            'reunion_familia_temes.*' => 'nullable|string',
            'reunion_familia_acords' => 'nullable|array',
            'reunion_familia_acords.*' => 'nullable|string',
            
            // Reuniones con profesionales
            'reunion_professionals_data' => 'nullable|array',
            'reunion_professionals_data.*' => 'nullable|date',
            'reunion_professionals_agents' => 'nullable|array',
            'reunion_professionals_agents.*' => 'nullable|string',
            'reunion_professionals_temes' => 'nullable|array',
            'reunion_professionals_temes.*' => 'nullable|string',
            'reunion_professionals_acords' => 'nullable|array',
            'reunion_professionals_acords.*' => 'nullable|string',
            
            // Acuerdos
            'acords_data' => 'nullable|array',
            'acords_data.*' => 'nullable|date',
            'acords_agents' => 'nullable|array',
            'acords_agents.*' => 'nullable|string',
            'acords_tipus' => 'nullable|array',
            'acords_tipus.*' => 'nullable|array',
            'acords_observacions' => 'nullable|array',
            'acords_observacions.*' => 'nullable|string',
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

        // Procesar reuniones con familia
        $reunionsFamilia = [];
        if ($request->has('reunion_familia_data')) {
            foreach ($request->reunion_familia_data as $index => $data) {
                $reunionsFamilia[] = [
                    'data' => $data,
                    'agents' => $request->reunion_familia_agents[$index] ?? '',
                    'temes' => $request->reunion_familia_temes[$index] ?? '',
                    'acords' => $request->reunion_familia_acords[$index] ?? '',
                ];
            }
        }
        $validated['reunion_familia'] = $reunionsFamilia;

        // Procesar reuniones con profesionales
        $reunionsProfessionals = [];
        if ($request->has('reunion_professionals_data')) {
            foreach ($request->reunion_professionals_data as $index => $data) {
                $reunionsProfessionals[] = [
                    'data' => $data,
                    'agents' => $request->reunion_professionals_agents[$index] ?? '',
                    'temes' => $request->reunion_professionals_temes[$index] ?? '',
                    'acords' => $request->reunion_professionals_acords[$index] ?? '',
                ];
            }
        }
        $validated['reunion_professionals'] = $reunionsProfessionals;

        // Procesar acuerdos
        $acords = [];
        if ($request->has('acords_data')) {
            foreach ($request->acords_data as $index => $data) {
                $tipus = $request->input("acords_tipus_{$index}", []);
                $acords[] = [
                    'data' => $data,
                    'agents' => $request->acords_agents[$index] ?? '',
                    'tipus' => $tipus,
                    'observacions' => $request->acords_observacions[$index] ?? '',
                ];
            }
        }
        $validated['acords'] = $acords;

        $supportPlan = SupportPlan::create($validated);

        // Handle timetable data if present
        if (!empty($request->timetable_data)) {
            $timetableData = json_decode($request->timetable_data, true);

            if ($timetableData && isset($timetableData['formattedSlots'])) {
                // Create timetable
                $timetable = $supportPlan->timetables()->create([
                    'name' => $request->timetable_name ?? 'Horari Escolar',
                    'configuration' => [
                        'timeSlots' => $timetableData['timeSlots'] ?? [],
                        'slots' => $timetableData['slots'] ?? []
                    ]
                ]);

                // Create slots
                foreach ($timetableData['formattedSlots'] as $slotData) {
                    $timetable->slots()->create([
                        'day' => $slotData['day'],
                        'time_start' => $slotData['time_start'],
                        'time_end' => $slotData['time_end'],
                        'subject' => $slotData['subject'],
                        'type' => $slotData['type'] ?? 'regular',
                        'notes' => $slotData['notes'] ?? null
                    ]);
                }
            }
        }

        $supportPlan->save();

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

        // Validar los datos
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
            'timetable_data' => 'nullable|string',
            'timetable_name' => 'nullable|string|max:255',
        ]);

        $user = Auth::user();

        // Verify team membership if team is being changed
        if (!empty($validated['team_id']) && $validated['team_id'] != $supportPlan->team_id) {
            $team = Team::find($validated['team_id']);
            if (!$team->hasUser($user) && $team->owner_id !== $user->id) {
                return redirect()->back()->with('error', 'No tienes permiso para asignar planes a este equipo.');
            }
        }

        // Procesar reuniones con familia
        $reunionsFamilia = [];
        if ($request->has('reunion_familia_data')) {
            foreach ($request->reunion_familia_data as $index => $data) {
                $reunionsFamilia[] = [
                    'data' => $data,
                    'agents' => $request->reunion_familia_agents[$index] ?? '',
                    'temes' => $request->reunion_familia_temes[$index] ?? '',
                    'acords' => $request->reunion_familia_acords[$index] ?? '',
                ];
            }
        }

        // Procesar reuniones con profesionales
        $reunionsProfessionals = [];
        if ($request->has('reunion_professionals_data')) {
            foreach ($request->reunion_professionals_data as $index => $data) {
                $reunionsProfessionals[] = [
                    'data' => $data,
                    'agents' => $request->reunion_professionals_agents[$index] ?? '',
                    'temes' => $request->reunion_professionals_temes[$index] ?? '',
                    'acords' => $request->reunion_professionals_acords[$index] ?? '',
                ];
            }
        }

        // Procesar acuerdos
        $acords = [];
        if ($request->has('acords_data')) {
            foreach ($request->acords_data as $index => $data) {
                $tipus = $request->input("acords_tipus_{$index}", []);
                $acords[] = [
                    'data' => $data,
                    'agents' => $request->acords_agents[$index] ?? '',
                    'tipus' => $tipus,
                    'observacions' => $request->acords_observacions[$index] ?? '',
                ];
            }
        }

        // Actualizar el plan de soporte con todos los datos
        $supportPlan->update(array_merge($validated, [
            'reunion_familia' => $reunionsFamilia,
            'reunion_professionals' => $reunionsProfessionals,
            'acords' => $acords
        ]));

        // Handle timetable data if present
        if (!empty($request->timetable_data)) {
            $timetableData = json_decode($request->timetable_data, true);

            if ($timetableData && isset($timetableData['formattedSlots'])) {
                // Get existing timetable or create a new one
                $timetable = $supportPlan->timetables()->first();

                if (!$timetable) {
                    $timetable = $supportPlan->timetables()->create([
                        'name' => $request->timetable_name ?? 'Horario',
                        'data' => $timetableData
                    ]);
                } else {
                    $timetable->update([
                        'name' => $request->timetable_name ?? $timetable->name,
                        'data' => $timetableData
                    ]);
                }
            }
        }

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

            // Crear un nuevo documento PHPWord para la tabla
            $phpWord = new PhpWord();
            $section = $phpWord->addSection();
            
            // Añadir espacio antes de la tabla
            $section->addTextBreak(2);
            
            // Crear la tabla del horario
            $table = $section->addTable([
                'borderSize' => 6,
                'borderColor' => '000000',
                'width' => 100,
                'unit' => TblWidth::PERCENT,
                'alignment' => Jc::CENTER,
                'cellMargin' => 80
            ]);

            // Obtener el horario
            $timetable = $supportPlan->timetables()->first();
            
            if ($timetable) {
                // Definir anchos de columna (en twips)
                $columnWidths = [
                    2000,  // HORA
                    3000,  // DILLUNS
                    3000,  // DIMARTS
                    3000,  // DIMECRES
                    3000,  // DIJOUS
                    3000   // DIVENDRES
                ];

                // Añadir encabezados
                $table->addRow(400); // Altura de fila específica
                $headers = ['HORA', 'DILLUNS', 'DIMARTS', 'DIMECRES', 'DIJOUS', 'DIVENDRES'];
                foreach ($headers as $index => $header) {
                    $cell = $table->addCell($columnWidths[$index], [
                        'bgColor' => '6ab0e6',
                        'valign' => 'center',
                        'vMerge' => 'restart',
                        'spacing' => 80,
                        'spaceAfter' => 80,
                        'spaceBefore' => 80
                    ]);
                    $cell->addText($header, [
                        'bold' => true,
                        'color' => 'FFFFFF',
                        'size' => 10
                    ], [
                        'alignment' => 'center',
                        'spaceBefore' => 50,
                        'spaceAfter' => 50
                    ]);
                }

                // Organizar slots por tiempo
                $timeSlots = [];
                foreach ($timetable->slots as $slot) {
                    $timeKey = $slot->time_start . '-' . $slot->time_end;
                    if (!isset($timeSlots[$timeKey])) {
                        $timeSlots[$timeKey] = [
                            'start' => $slot->time_start,
                            'end' => $slot->time_end,
                            'slots' => []
                        ];
                    }
                    $timeSlots[$timeKey]['slots'][$slot->day] = $slot;
                }

                // Ordenar por hora de inicio
                uksort($timeSlots, function($a, $b) use ($timeSlots) {
                    return strtotime($timeSlots[$a]['start']) - strtotime($timeSlots[$b]['start']);
                });

                $days = ['DILLUNS', 'DIMARTS', 'DIMECRES', 'DIJOUS', 'DIVENDRES'];

                // Generar filas
                foreach ($timeSlots as $timeData) {
                    $table->addRow(350); // Altura específica para las filas de contenido
                    
                    // Columna de hora
                    $cell = $table->addCell($columnWidths[0], [
                        'valign' => 'center',
                        'spacing' => 50,
                        'spaceAfter' => 50,
                        'spaceBefore' => 50
                    ]);
                    $cell->addText(
                        $timeData['start'] . ' - ' . $timeData['end'],
                        ['size' => 9],
                        ['alignment' => 'center']
                    );

                    // Columnas de días
                    foreach ($days as $index => $day) {
                        $slot = $timeData['slots'][$day] ?? null;
                        $bgColor = 'FFFFFF'; // Default white

                        if ($slot) {
                            switch ($slot->type) {
                                case 'codocencia':
                                    $bgColor = 'E9D5FF';
                                    break;
                                case 'desdoblament':
                                    $bgColor = 'FEF9C3';
                                    break;
                                case 'desdoblament_codocencia':
                                    $bgColor = 'FFEDD5';
                                    break;
                                case 'pati':
                                    $bgColor = 'DBEAFE';
                                    break;
                            }
                        }

                        $cell = $table->addCell($columnWidths[$index + 1], [
                            'bgColor' => $bgColor,
                            'valign' => 'center',
                            'spacing' => 50,
                            'spaceAfter' => 50,
                            'spaceBefore' => 50
                        ]);

                        if ($slot) {
                            $cell->addText(
                                $slot->subject,
                                ['size' => 9],
                                ['alignment' => 'center']
                            );
                            if ($slot->notes) {
                                $cell->addText(
                                    $slot->notes,
                                    ['size' => 8, 'italic' => true],
                                    ['alignment' => 'center']
                                );
                            }
                        }
                    }
                }
            } else {
                $table->addRow();
                $cell = $table->addCell(11500, [
                    'gridSpan' => 6,
                    'valign' => 'center'
                ]);
                $cell->addText('No hi ha cap horari definit.', [], ['alignment' => 'center']);
            }

            // Guardar la tabla temporalmente
            $tempTableFile = tempnam(sys_get_temp_dir(), 'table_');
            $objWriter = IOFactory::createWriter($phpWord, 'Word2007');
            $objWriter->save($tempTableFile);

            // Leer el contenido de la tabla
            $zipTable = new \ZipArchive();
            $zipTable->open($tempTableFile);
            $tableXml = $zipTable->getFromName('word/document.xml');
            $zipTable->close();
            unlink($tempTableFile);

            // Extraer solo el XML de la tabla
            preg_match('/<w:tbl>.*<\/w:tbl>/s', $tableXml, $matches);
            $tableOnlyXml = $matches[0];

            // Preparar variables para la plantilla
                    $variables = $this->prepareTemplateVariables($supportPlan);
                    $variables = array_merge($variables, $this->prepareSaberVariables($supportPlan));

            // Crear una copia temporal de la plantilla
            $tempFile = tempnam(sys_get_temp_dir(), 'docx_');
            copy($templatePath, $tempFile);

            // Abrir la plantilla
            $zip = new \ZipArchive();
            if ($zip->open($tempFile) === true) {
                // Leer el contenido del documento
                $documentXml = $zip->getFromName('word/document.xml');
                $originalXml = $documentXml; // Guardar una copia del XML original

                // Reemplazar todas las variables excepto la tabla
                foreach ($variables as $key => $value) {
                    if ($key !== 'horari_escolar') {
                        $value = htmlspecialchars($value, ENT_XML1, 'UTF-8');
                        $documentXml = str_replace('${'.$key.'}', $value, $documentXml);
                    }
                }

                // Buscar el párrafo exacto que contiene el marcador
                if (preg_match('/<w:p\b[^>]*>(?:(?!<w:p\b|<\/w:p>).)*\${horari_escolar}(?:(?!<w:p\b|<\/w:p>).)*<\/w:p>/s', $documentXml, $matches)) {
                    $paragraphToReplace = $matches[0];
                    // Reemplazar solo ese párrafo específico con la tabla
                    $documentXml = str_replace($paragraphToReplace, $tableOnlyXml, $documentXml);
                } else {
                    // Si no encontramos el párrafo exacto, intentar un reemplazo directo más seguro
                    $documentXml = str_replace('${horari_escolar}', $tableOnlyXml, $documentXml);
                }

                // Verificar que el documento no esté vacío o corrupto
                if (empty($documentXml) || !str_contains($documentXml, '<w:document')) {
                    // Restaurar el XML original si algo salió mal
                    $documentXml = $originalXml;
                    throw new \Exception('El documento resultante está vacío o corrupto.');
                }

                // Verificar que el contenido importante se mantuvo
                if (!str_contains($documentXml, '<w:document')) {
                    $documentXml = $originalXml;
                    throw new \Exception('Se perdió contenido importante del documento durante el proceso.');
                }

                // Actualizar el documento
                $zip->addFromString('word/document.xml', $documentXml);
                $zip->close();
            }

            // Generar el nombre del archivo
            $filename = 'plan_de_soporte_' . $supportPlan->student_name . '_' . date('Y-m-d') . '.docx';
            $filename = str_replace(' ', '_', $filename);

            // Descargar el archivo
            return response()->download($tempFile, $filename)->deleteFileAfterSend(true);

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error al generar el documento Word: ' . $e->getMessage());
        }
    }

    /**
     * Preparar variables para la plantilla
     */
    private function prepareTemplateVariables($supportPlan)
    {
        $variables = [
            'nombre_estudiante' => $supportPlan->student_name,
            'fecha_nacimiento' => $supportPlan->birth_date ? $supportPlan->birth_date->format('d/m/Y') : '',
            'nombre_tutores' => $supportPlan->tutor_name,
            'lugar_nacimiento' => $supportPlan->birth_place,
            'telefono' => $supportPlan->phone,
            'direccion' => $supportPlan->address,
            'lengua_habitual' => $supportPlan->usual_language,
            'otras_lenguas' => $supportPlan->other_languages,
            'curso' => $supportPlan->course,
            'grupo' => $supportPlan->group,
            'tutor' => $supportPlan->teacher_name,
            'fecha_incorporacion_centro' => $supportPlan->school_incorporation_date ? $supportPlan->school_incorporation_date->format('d/m/Y') : '',
            'fecha_llegada_catalunya' => $supportPlan->catalonia_arrival_date ? $supportPlan->catalonia_arrival_date->format('d/m/Y') : '',
            'fecha_incorporacion_sistema' => $supportPlan->educational_system_date ? $supportPlan->educational_system_date->format('d/m/Y') : '',
            'centros_anteriores' => $supportPlan->previous_schools,
            'escolarizacion_previa' => $supportPlan->previous_schooling,
            'retencion_curso' => $supportPlan->course_retention,
            'otra_informacion' => $supportPlan->other_data,
            
            // Justificación
            'motivado_informe_reconeixement' => in_array('informe_reconeixement', $supportPlan->justification_reasons ?? []) ? '☒' : '☐',
            'motivado_avaluacio_psicopedagogica' => in_array('avaluacio_psicopedagogica', $supportPlan->justification_reasons ?? []) ? '☒' : '☐',
            'motivado_avaluacio_inicial_nouvingut' => in_array('avaluacio_inicial_nouvingut', $supportPlan->justification_reasons ?? []) ? '☒' : '☐',
            'motivado_avaluacio_origen_estranger_aula' => in_array('avaluacio_origen_estranger_aula', $supportPlan->justification_reasons ?? []) ? '☒' : '☐',
            'motivado_avaluacio_origen_estranger_tardana' => in_array('avaluacio_origen_estranger_tardana', $supportPlan->justification_reasons ?? []) ? '☒' : '☐',
            'motivado_decisio_comissio' => in_array('decisio_comissio', $supportPlan->justification_reasons ?? []) ? '☒' : '☐',
            'motivado_altres' => in_array('altres', $supportPlan->justification_reasons ?? []) ? '☒' : '☐',
            'justificacion_other' => $supportPlan->justification_other,
            'commission_proponent' => $supportPlan->commission_proponent,
            'commission_motivation' => $supportPlan->commission_motivation,
            'brief_justification' => $supportPlan->brief_justification,

            // Competencias y aprendizaje
            'competencies_alumne_capabilities' => trim($supportPlan->competencies_alumne_capabilities) ?: '',
            'learning_strong_points' => trim($supportPlan->learning_strong_points) ?: '',
            'learning_improvement_points' => trim($supportPlan->learning_improvement_points) ?: '',
            'student_interests' => trim($supportPlan->student_interests) ?: '',

            // Profesionales
            'professional_tutor_responsable' => in_array('tutor_responsable', $supportPlan->professionals ?? []) ? '☒' : '☐',
            'professional_tutor_aula_acollida' => in_array('tutor_aula_acollida', $supportPlan->professionals ?? []) ? '☒' : '☐',
            'professional_suport_intensiu' => in_array('suport_intensiu', $supportPlan->professionals ?? []) ? '☒' : '☐',
            'professional_aula_integral' => in_array('aula_integral', $supportPlan->professionals ?? []) ? '☒' : '☐',
            'professional_mestre_educacio_especial' => in_array('mestre_educacio_especial', $supportPlan->professionals ?? []) ? '☒' : '☐',
            'professional_assessor_llengua' => in_array('assessor_llengua', $supportPlan->professionals ?? []) ? '☒' : '☐',
            'professional_altres_professionals' => in_array('altres_professionals', $supportPlan->professionals ?? []) ? '☒' : '☐',
            'professional_equip_assessorament' => in_array('equip_assessorament', $supportPlan->professionals ?? []) ? '☒' : '☐',
            'professional_serveis_socials' => in_array('serveis_socials', $supportPlan->professionals ?? []) ? '☒' : '☐',
            'professional_centre_salut_mental' => in_array('centre_salut_mental', $supportPlan->professionals ?? []) ? '☒' : '☐',
            'professional_centres_recursos' => in_array('centres_recursos', $supportPlan->professionals ?? []) ? '☒' : '☐',
            'professional_suports_externs' => in_array('suports_externs', $supportPlan->professionals ?? []) ? '☒' : '☐',
            'professional_activitats_extraescolars' => in_array('activitats_extraescolars', $supportPlan->professionals ?? []) ? '☒' : '☐',
            'professional_beques_ajuts' => in_array('beques_ajuts', $supportPlan->professionals ?? []) ? '☒' : '☐',
            'professional_altres_serveis' => in_array('altres_serveis', $supportPlan->professionals ?? []) ? '☒' : '☐',

            'horari_escolar' => $this->generateTimetableHtml($supportPlan->timetables()->first()),

            // Reuniones de seguimiento con familia
            'reunion_familia_data_1' => $supportPlan->reunion_familia_data_1 ?? '',
            'reunion_familia_agents_1' => $supportPlan->reunion_familia_agents_1 ?? '',
            'reunion_familia_temes_1' => $supportPlan->reunion_familia_temes_1 ?? '',
            'reunion_familia_acords_1' => $supportPlan->reunion_familia_acords_1 ?? '',
            
            // Reuniones de seguimiento con profesionales
            'reunion_professionals_data_1' => $supportPlan->reunion_professionals_data_1 ?? '',
            'reunion_professionals_agents_1' => $supportPlan->reunion_professionals_agents_1 ?? '',
            'reunion_professionals_temes_1' => $supportPlan->reunion_professionals_temes_1 ?? '',
            'reunion_professionals_acords_1' => $supportPlan->reunion_professionals_acords_1 ?? '',
            
            // Acuerdos sobre continuidad
            'acords_data_1' => $supportPlan->acords_data_1 ?? '',
            'acords_agents_1' => $supportPlan->acords_agents_1 ?? '',
            'acords_acord_1' => $supportPlan->acords_acord_1 ?? '',
            'acords_observacions_1' => $supportPlan->acords_observacions_1 ?? '',
            'acords_continuitat_1' => in_array('continuitat', $supportPlan->acords_tipus_1 ?? []) ? '☒' : '☐',
            'acords_revisio_1' => in_array('revisio', $supportPlan->acords_tipus_1 ?? []) ? '☒' : '☐',
            'acords_finalitzacio_1' => in_array('finalitzacio', $supportPlan->acords_tipus_1 ?? []) ? '☒' : '☐',

            // Generar tabla de reuniones con familia
            'reunions_familia_table' => $this->generateReunionsTable($supportPlan->reunion_familia),
            'reunions_professionals_table' => $this->generateReunionsTable($supportPlan->reunion_professionals),
            'acords_table' => $this->generateAcordsTable($supportPlan->acords),
        ];

        // Generar variables dinámicas para objetivos y criterios
        for ($i = 1; $i <= 22; $i++) {
            $variables["objetivo#$i"] = $supportPlan->{"objetivo_$i"} ?? '';
            $variables["criterio#$i"] = $supportPlan->{"criterio_$i"} ?? '';
        }

        // Generar variables dinámicas para objetivos y criterios transversales
        for ($i = 1; $i <= 14; $i++) {
            $variables["transversal_objective#$i"] = $supportPlan->{"transversal_objective_$i"} ?? '';
            $variables["transversal_criteria#$i"] = $supportPlan->{"transversal_criteria_$i"} ?? '';
        }

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

    /**
     * Genera el HTML para la tabla de horario
     */
    private function generateTimetableHtml($timetable)
    {
        if (!$timetable) {
            return '<w:p><w:r><w:t>No hi ha cap horari definit.</w:t></w:r></w:p>';
        }

        $html = '<w:tbl>
            <w:tblPr>
                <w:tblStyle w:val="TableGrid"/>
                <w:tblW w:w="10000" w:type="dxa"/>
                <w:tblBorders>
                    <w:top w:val="single" w:sz="4" w:space="0" w:color="000000"/>
                    <w:left w:val="single" w:sz="4" w:space="0" w:color="000000"/>
                    <w:bottom w:val="single" w:sz="4" w:space="0" w:color="000000"/>
                    <w:right w:val="single" w:sz="4" w:space="0" w:color="000000"/>
                    <w:insideH w:val="single" w:sz="4" w:space="0" w:color="000000"/>
                    <w:insideV w:val="single" w:sz="4" w:space="0" w:color="000000"/>
                </w:tblBorders>
            </w:tblPr>
            <w:tblGrid>
                <w:gridCol w:w="1500"/>
                <w:gridCol w:w="1700"/>
                <w:gridCol w:w="1700"/>
                <w:gridCol w:w="1700"/>
                <w:gridCol w:w="1700"/>
                <w:gridCol w:w="1700"/>
            </w:tblGrid>';

        // Encabezado
        $html .= '<w:tr>
            <w:tc><w:p><w:r><w:t>HORA</w:t></w:r></w:p></w:tc>
            <w:tc><w:p><w:r><w:t>DILLUNS</w:t></w:r></w:p></w:tc>
            <w:tc><w:p><w:r><w:t>DIMARTS</w:t></w:r></w:p></w:tc>
            <w:tc><w:p><w:r><w:t>DIMECRES</w:t></w:r></w:p></w:tc>
            <w:tc><w:p><w:r><w:t>DIJOUS</w:t></w:r></w:p></w:tc>
            <w:tc><w:p><w:r><w:t>DIVENDRES</w:t></w:r></w:p></w:tc>
            </w:tr>';

        // Organizar slots por tiempo
        $timeSlots = [];
        foreach ($timetable->slots as $slot) {
            $timeKey = $slot->time_start . '-' . $slot->time_end;
            if (!isset($timeSlots[$timeKey])) {
                $timeSlots[$timeKey] = [
                    'start' => $slot->time_start,
                    'end' => $slot->time_end,
                    'slots' => []
                ];
            }
            $timeSlots[$timeKey]['slots'][$slot->day] = $slot;
        }

        // Ordenar por hora de inicio
        uksort($timeSlots, function($a, $b) use ($timeSlots) {
            return strtotime($timeSlots[$a]['start']) - strtotime($timeSlots[$b]['start']);
        });

        $days = ['DILLUNS', 'DIMARTS', 'DIMECRES', 'DIJOUS', 'DIVENDRES'];

        // Generar filas
        foreach ($timeSlots as $timeData) {
            $html .= '<w:tr>';
            
            // Columna de hora
            $html .= '<w:tc><w:p><w:r><w:t>' . htmlspecialchars($timeData['start'] . ' - ' . $timeData['end']) . '</w:t></w:r></w:p></w:tc>';

            // Columnas de días
            foreach ($days as $day) {
                $slot = $timeData['slots'][$day] ?? null;
                $bgColor = 'FFFFFF'; // Default white

                if ($slot) {
                    switch ($slot->type) {
                        case 'codocencia':
                            $bgColor = 'E9D5FF'; // purple-200
                            break;
                        case 'desdoblament':
                            $bgColor = 'FEF9C3'; // yellow-200
                            break;
                        case 'desdoblament_codocencia':
                            $bgColor = 'FFEDD5'; // orange-200
                            break;
                        case 'pati':
                            $bgColor = 'DBEAFE'; // blue-100
                            break;
                    }
                }

                $html .= '<w:tc>
                    <w:tcPr><w:shd w:fill="' . $bgColor . '"/></w:tcPr>
                    <w:p><w:r><w:t>' . ($slot ? htmlspecialchars($slot->subject) : '') . '</w:t></w:r></w:p>';

                if ($slot && $slot->notes) {
                    $html .= '<w:p><w:r><w:rPr><w:sz w:val="16"/></w:rPr><w:t>' . htmlspecialchars($slot->notes) . '</w:t></w:r></w:p>';
                }

                $html .= '</w:tc>';
            }

            $html .= '</w:tr>';
        }

        $html .= '</w:tbl>';

        return $html;
    }

    /**
     * Genera el HTML para la tabla de reuniones
     */
    private function generateReunionsTable($reunions)
    {
        if (empty($reunions) || !is_array($reunions)) {
            return '';
        }

        $table = '<w:tbl>
            <w:tblPr>
                <w:tblStyle w:val="TableGrid"/>
                <w:tblW w:w="10000" w:type="dxa"/>
                <w:tblBorders>
                    <w:top w:val="single" w:sz="4" w:space="0" w:color="000000"/>
                    <w:left w:val="single" w:sz="4" w:space="0" w:color="000000"/>
                    <w:bottom w:val="single" w:sz="4" w:space="0" w:color="000000"/>
                    <w:right w:val="single" w:sz="4" w:space="0" w:color="000000"/>
                    <w:insideH w:val="single" w:sz="4" w:space="0" w:color="000000"/>
                    <w:insideV w:val="single" w:sz="4" w:space="0" w:color="000000"/>
                </w:tblBorders>
                <w:tblLook w:val="04A0" w:firstRow="1" w:lastRow="0" w:firstColumn="1" w:lastColumn="0" w:noHBand="0" w:noVBand="1"/>
            </w:tblPr>
            <w:tblGrid>
                <w:gridCol w:w="1500"/>
                <w:gridCol w:w="2500"/>
                <w:gridCol w:w="3000"/>
                <w:gridCol w:w="3000"/>
            </w:tblGrid>';

        // Header row
        $table .= '<w:tr>
            <w:tc>
                <w:tcPr>
                    <w:shd w:fill="6AB0E6" w:val="clear"/>
                </w:tcPr>
                <w:p>
                    <w:pPr><w:jc w:val="center"/></w:pPr>
                    <w:r><w:rPr><w:b/><w:color w:val="FFFFFF"/></w:rPr><w:t>Data</w:t></w:r>
                </w:p>
            </w:tc>
            <w:tc>
                <w:tcPr>
                    <w:shd w:fill="6AB0E6" w:val="clear"/>
                </w:tcPr>
                <w:p>
                    <w:pPr><w:jc w:val="center"/></w:pPr>
                    <w:r><w:rPr><w:b/><w:color w:val="FFFFFF"/></w:rPr><w:t>Agents participants</w:t></w:r>
                </w:p>
            </w:tc>
            <w:tc>
                <w:tcPr>
                    <w:shd w:fill="6AB0E6" w:val="clear"/>
                </w:tcPr>
                <w:p>
                    <w:pPr><w:jc w:val="center"/></w:pPr>
                    <w:r><w:rPr><w:b/><w:color w:val="FFFFFF"/></w:rPr><w:t>Temes tractats</w:t></w:r>
                </w:p>
            </w:tc>
            <w:tc>
                <w:tcPr>
                    <w:shd w:fill="6AB0E6" w:val="clear"/>
                </w:tcPr>
                <w:p>
                    <w:pPr><w:jc w:val="center"/></w:pPr>
                    <w:r><w:rPr><w:b/><w:color w:val="FFFFFF"/></w:rPr><w:t>Acords</w:t></w:r>
                </w:p>
            </w:tc>
        </w:tr>';

        // Data rows
        foreach ($reunions as $reunio) {
            $table .= '<w:tr>
                <w:tc>
                    <w:p>
                        <w:pPr><w:jc w:val="center"/></w:pPr>
                        <w:r><w:t>' . htmlspecialchars($reunio['data'] ?? '', ENT_XML1, 'UTF-8') . '</w:t></w:r>
                    </w:p>
                </w:tc>
                <w:tc>
                    <w:p>
                        <w:pPr><w:jc w:val="left"/></w:pPr>
                        <w:r><w:t>' . htmlspecialchars($reunio['agents'] ?? '', ENT_XML1, 'UTF-8') . '</w:t></w:r>
                    </w:p>
                </w:tc>
                <w:tc>
                    <w:p>
                        <w:pPr><w:jc w:val="left"/></w:pPr>
                        <w:r><w:t>' . htmlspecialchars($reunio['temes'] ?? '', ENT_XML1, 'UTF-8') . '</w:t></w:r>
                    </w:p>
                </w:tc>
                <w:tc>
                    <w:p>
                        <w:pPr><w:jc w:val="left"/></w:pPr>
                        <w:r><w:t>' . htmlspecialchars($reunio['acords'] ?? '', ENT_XML1, 'UTF-8') . '</w:t></w:r>
                    </w:p>
                </w:tc>
            </w:tr>';
        }

        $table .= '</w:tbl>';
        return $table;
    }

    /**
     * Genera el HTML para la tabla de acuerdos
     */
    private function generateAcordsTable($acords)
    {
        if (empty($acords) || !is_array($acords)) {
            return '';
        }

        $table = '<w:tbl>
            <w:tblPr>
                <w:tblStyle w:val="TableGrid"/>
                <w:tblW w:w="10000" w:type="dxa"/>
                <w:tblBorders>
                    <w:top w:val="single" w:sz="4" w:space="0" w:color="000000"/>
                    <w:left w:val="single" w:sz="4" w:space="0" w:color="000000"/>
                    <w:bottom w:val="single" w:sz="4" w:space="0" w:color="000000"/>
                    <w:right w:val="single" w:sz="4" w:space="0" w:color="000000"/>
                    <w:insideH w:val="single" w:sz="4" w:space="0" w:color="000000"/>
                    <w:insideV w:val="single" w:sz="4" w:space="0" w:color="000000"/>
                </w:tblBorders>
                <w:tblLook w:val="04A0" w:firstRow="1" w:lastRow="0" w:firstColumn="1" w:lastColumn="0" w:noHBand="0" w:noVBand="1"/>
            </w:tblPr>
            <w:tblGrid>
                <w:gridCol w:w="1500"/>
                <w:gridCol w:w="2500"/>
                <w:gridCol w:w="3000"/>
                <w:gridCol w:w="3000"/>
            </w:tblGrid>';

        // Header row
        $table .= '<w:tr>
            <w:tc>
                <w:tcPr>
                    <w:shd w:fill="6AB0E6" w:val="clear"/>
                </w:tcPr>
                <w:p>
                    <w:pPr><w:jc w:val="center"/></w:pPr>
                    <w:r><w:rPr><w:b/><w:color w:val="FFFFFF"/></w:rPr><w:t>Data</w:t></w:r>
                </w:p>
            </w:tc>
            <w:tc>
                <w:tcPr>
                    <w:shd w:fill="6AB0E6" w:val="clear"/>
                </w:tcPr>
                <w:p>
                    <w:pPr><w:jc w:val="center"/></w:pPr>
                    <w:r><w:rPr><w:b/><w:color w:val="FFFFFF"/></w:rPr><w:t>Agents participants</w:t></w:r>
                </w:p>
            </w:tc>
            <w:tc>
                <w:tcPr>
                    <w:shd w:fill="6AB0E6" w:val="clear"/>
                </w:tcPr>
                <w:p>
                    <w:pPr><w:jc w:val="center"/></w:pPr>
                    <w:r><w:rPr><w:b/><w:color w:val="FFFFFF"/></w:rPr><w:t>Acord</w:t></w:r>
                </w:p>
            </w:tc>
            <w:tc>
                <w:tcPr>
                    <w:shd w:fill="6AB0E6" w:val="clear"/>
                </w:tcPr>
                <w:p>
                    <w:pPr><w:jc w:val="center"/></w:pPr>
                    <w:r><w:rPr><w:b/><w:color w:val="FFFFFF"/></w:rPr><w:t>Observacions</w:t></w:r>
                </w:p>
            </w:tc>
        </w:tr>';

        // Data rows
        foreach ($acords as $acord) {
            $tipus = '';
            if (isset($acord['tipus']) && is_array($acord['tipus'])) {
                $tipus = implode(', ', array_map('ucfirst', $acord['tipus']));
            }

            $table .= '<w:tr>
                <w:tc>
                    <w:p>
                        <w:pPr><w:jc w:val="center"/></w:pPr>
                        <w:r><w:t>' . htmlspecialchars($acord['data'] ?? '', ENT_XML1, 'UTF-8') . '</w:t></w:r>
                    </w:p>
                </w:tc>
                <w:tc>
                    <w:p>
                        <w:pPr><w:jc w:val="left"/></w:pPr>
                        <w:r><w:t>' . htmlspecialchars($acord['agents'] ?? '', ENT_XML1, 'UTF-8') . '</w:t></w:r>
                    </w:p>
                </w:tc>
                <w:tc>
                    <w:p>
                        <w:pPr><w:jc w:val="left"/></w:pPr>
                        <w:r><w:t>' . htmlspecialchars($tipus, ENT_XML1, 'UTF-8') . '</w:t></w:r>
                    </w:p>
                </w:tc>
                <w:tc>
                    <w:p>
                        <w:pPr><w:jc w:val="left"/></w:pPr>
                        <w:r><w:t>' . htmlspecialchars($acord['observacions'] ?? '', ENT_XML1, 'UTF-8') . '</w:t></w:r>
                    </w:p>
                </w:tc>
            </w:tr>';
        }

        $table .= '</w:tbl>';
        return $table;
    }
}
