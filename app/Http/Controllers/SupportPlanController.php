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
