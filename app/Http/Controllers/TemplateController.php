<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Gate;

class TemplateController extends Controller
{
    /**
     * Constructor - Aplicar middleware de autenticación
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Mostrar el formulario para subir una plantilla.
     */
    public function index()
    {
        // Verificar si el usuario tiene permisos de administrador
        if (!auth()->user()->hasTeamRole(auth()->user()->currentTeam, 'admin')) {
            abort(403, 'No tienes permisos para acceder a esta página.');
        }

        // Comprobar si la plantilla existe
        $templateExists = Storage::disk('local')->exists('templates/plantilla_plan_soporte.docx');
        $templateLastUpdated = null;

        if ($templateExists) {
            $templateLastUpdated = Storage::disk('local')->lastModified('templates/plantilla_plan_soporte.docx');
            $templateLastUpdated = date('d/m/Y H:i:s', $templateLastUpdated);
        }

        return view('templates.index', compact('templateExists', 'templateLastUpdated'));
    }

    /**
     * Procesar la subida de una plantilla.
     */
    public function store(Request $request)
    {
        // Verificar si el usuario tiene permisos de administrador
        if (!auth()->user()->hasTeamRole(auth()->user()->currentTeam, 'admin')) {
            abort(403, 'No tienes permisos para gestionar plantillas.');
        }

        $request->validate([
            'template_file' => 'required|file|mimes:docx|max:10240', // Max 10MB, solo formato docx
        ], [
            'template_file.required' => 'Debes seleccionar un archivo',
            'template_file.mimes' => 'El archivo debe ser un documento de Word (.docx)',
            'template_file.max' => 'El tamaño máximo permitido es de 10MB',
        ]);

        if ($request->hasFile('template_file') && $request->file('template_file')->isValid()) {
            // Almacenar el archivo en el directorio de plantillas (templates)
            $file = $request->file('template_file');

            // Almacenar como plantilla_plan_soporte.docx
            $file->storeAs('templates', 'plantilla_plan_soporte.docx', 'local');

            return redirect()->route('templates.index')
                ->with('success', 'Plantilla actualizada correctamente.');
        }

        return redirect()->route('templates.index')
            ->with('error', 'Error al subir la plantilla.');
    }

    /**
     * Descargar la plantilla actual.
     */
    public function download()
    {
        // Verificar si el usuario tiene permisos
        if (!auth()->user()->hasTeamRole(auth()->user()->currentTeam, 'admin')) {
            abort(403, 'No tienes permisos para descargar la plantilla.');
        }

        $templatePath = storage_path('app/templates/plantilla_plan_soporte.docx');

        if (!file_exists($templatePath)) {
            return redirect()->route('templates.index')
                ->with('error', 'La plantilla no existe.');
        }

        return response()->download($templatePath, 'plantilla_plan_soporte.docx');
    }

    /**
     * Eliminar la plantilla actual.
     */
    public function destroy()
    {
        // Verificar si el usuario tiene permisos de administrador
        if (!auth()->user()->hasTeamRole(auth()->user()->currentTeam, 'admin')) {
            abort(403, 'No tienes permisos para eliminar plantillas.');
        }

        if (Storage::disk('local')->exists('templates/plantilla_plan_soporte.docx')) {
            Storage::disk('local')->delete('templates/plantilla_plan_soporte.docx');
            return redirect()->route('templates.index')
                ->with('success', 'Plantilla eliminada correctamente.');
        }

        return redirect()->route('templates.index')
            ->with('error', 'La plantilla no existe.');
    }

    /**
     * Proporcionar una guía sobre los marcadores de posición disponibles.
     */
    public function placeholders()
    {
        // Verificar si el usuario tiene permisos
        if (!auth()->user()->hasTeamRole(auth()->user()->currentTeam, 'admin')) {
            abort(403, 'No tienes permisos para acceder a esta información.');
        }

        // Lista de marcadores disponibles para la plantilla
        $placeholders = [
            'nombre_estudiante' => 'Nombre del estudiante',
            'fecha_nacimiento' => 'Fecha de nacimiento (dd/mm/yyyy)',
            'nombre_tutores' => 'Nombre de los tutores legales',
            'lugar_nacimiento' => 'Lugar de nacimiento',
            'telefono' => 'Teléfono de contacto',
            'direccion' => 'Dirección',
            'lengua_habitual' => 'Lengua de uso habitual',
            'otras_lenguas' => 'Otras lenguas que conoce',
            'curso' => 'Curso actual',
            'grupo' => 'Grupo',
            'tutor' => 'Tutor/a',
            'fecha_incorporacion_centro' => 'Fecha de incorporación al centro',
            'fecha_llegada_catalunya' => 'Fecha de llegada a Cataluña',
            'fecha_incorporacion_sistema' => 'Fecha de incorporación al sistema educativo catalán',
            'centros_anteriores' => 'Centros donde ha estado matriculado anteriormente',
            'escolarizacion_previa' => 'Escolarización previa',
            'retencion_curso' => 'Retención de curso',
            'otra_informacion' => 'Otra información de interés',
            'motivado_informe_reconeixement' => 'X si está motivado por Informe de reconeixement',
            'motivado_avaluacio_psicopedagogica' => 'X si está motivado por Avaluació psicopedagògica',
            'motivado_avaluacio_inicial_nouvingut' => 'X si está motivado por Avaluació inicial nouvingut',
            'motivado_avaluacio_origen_estranger_aula' => 'X si está motivado por Avaluació origen estranger (aula)',
            'motivado_avaluacio_origen_estranger_tardana' => 'X si está motivado por Avaluació origen estranger (tardana)',
            'motivado_decisio_comissio' => 'X si está motivado por Decisió comissió',
            'motivado_altres' => 'X si está motivado por Altres',
            'justificacion_other' => 'Texto para Altres justificación',
            'commission_proponent' => 'Proponente de la comisión (EAP/tutor/etc)',
            'commission_motivation' => 'Motivación de la comisión',
            'brief_justification' => 'Justificación breve del PSI',
        ];

        return view('templates.placeholders', compact('placeholders'));
    }

    /**
     * Descargar una plantilla de ejemplo con marcadores de posición.
     */
    public function downloadSample()
    {
        // Verificar si el usuario tiene permisos
        if (!auth()->user()->hasTeamRole(auth()->user()->currentTeam, 'admin')) {
            abort(403, 'No tienes permisos para descargar la plantilla de ejemplo.');
        }

        // Crear una plantilla de ejemplo usando PhpWord
        $phpWord = new \PhpOffice\PhpWord\PhpWord();
        $section = $phpWord->addSection();

        // Título
        $titleStyle = [
            'bold' => true,
            'size' => 16,
            'name' => 'Arial',
        ];
        $section->addText('Plantilla de ejemplo - Plan de Soporte Individualizado', $titleStyle, ['alignment' => 'center']);
        $section->addTextBreak(1);

        // Introducción
        $section->addText('Esta es una plantilla de ejemplo que muestra cómo usar los marcadores de posición. Reemplaza los marcadores en tu plantilla con el formato ${nombre_marcador}.', ['size' => 11]);
        $section->addTextBreak(1);

        // Tabla de datos personales
        $table = $section->addTable(['borderSize' => 1, 'borderColor' => '000000']);

        // Encabezado
        $table->addRow();
        $table->addCell(9000, ['bgColor' => '6ab0e6'])->addText('DATOS PERSONALES', ['bold' => true, 'color' => 'FFFFFF']);

        // Datos del estudiante
        $table->addRow();
        $table->addCell(4500)->addText('Nombre del estudiante:', ['bold' => true]);
        $table->addCell(4500)->addText('${nombre_estudiante}');

        $table->addRow();
        $table->addCell(4500)->addText('Fecha de nacimiento:', ['bold' => true]);
        $table->addCell(4500)->addText('${fecha_nacimiento}');

        $table->addRow();
        $table->addCell(4500)->addText('Tutores legales:', ['bold' => true]);
        $table->addCell(4500)->addText('${nombre_tutores}');

        // Más datos personales
        $table->addRow();
        $table->addCell(4500)->addText('Lugar de nacimiento:', ['bold' => true]);
        $table->addCell(4500)->addText('${lugar_nacimiento}');

        $table->addRow();
        $table->addCell(4500)->addText('Teléfono:', ['bold' => true]);
        $table->addCell(4500)->addText('${telefono}');

        $table->addRow();
        $table->addCell(4500)->addText('Dirección:', ['bold' => true]);
        $table->addCell(4500)->addText('${direccion}');

        $table->addRow();
        $table->addCell(4500)->addText('Lengua habitual:', ['bold' => true]);
        $table->addCell(4500)->addText('${lengua_habitual}');

        $table->addRow();
        $table->addCell(4500)->addText('Otras lenguas:', ['bold' => true]);
        $table->addCell(4500)->addText('${otras_lenguas}');

        $section->addTextBreak(1);

        // Tabla de datos escolares
        $table = $section->addTable(['borderSize' => 1, 'borderColor' => '000000']);

        // Encabezado
        $table->addRow();
        $table->addCell(9000, ['bgColor' => '6ab0e6'])->addText('DATOS ESCOLARES', ['bold' => true, 'color' => 'FFFFFF']);

        // Datos escolares
        $table->addRow();
        $table->addCell(4500)->addText('Curso:', ['bold' => true]);
        $table->addCell(4500)->addText('${curso}');

        $table->addRow();
        $table->addCell(4500)->addText('Grupo:', ['bold' => true]);
        $table->addCell(4500)->addText('${grupo}');

        $table->addRow();
        $table->addCell(4500)->addText('Tutor/a:', ['bold' => true]);
        $table->addCell(4500)->addText('${tutor}');

        $table->addRow();
        $table->addCell(4500)->addText('Incorporación al centro:', ['bold' => true]);
        $table->addCell(4500)->addText('${fecha_incorporacion_centro}');

        $table->addRow();
        $table->addCell(4500)->addText('Llegada a Cataluña:', ['bold' => true]);
        $table->addCell(4500)->addText('${fecha_llegada_catalunya}');

        $table->addRow();
        $table->addCell(4500)->addText('Incorporación sistema educativo:', ['bold' => true]);
        $table->addCell(4500)->addText('${fecha_incorporacion_sistema}');

        $table->addRow();
        $table->addCell(4500)->addText('Centros anteriores:', ['bold' => true]);
        $table->addCell(4500)->addText('${centros_anteriores}');

        $table->addRow();
        $table->addCell(4500)->addText('Escolarización previa:', ['bold' => true]);
        $table->addCell(4500)->addText('${escolarizacion_previa}');

        $table->addRow();
        $table->addCell(4500)->addText('Retención de curso:', ['bold' => true]);
        $table->addCell(4500)->addText('${retencion_curso}');

        $table->addRow();
        $table->addCell(4500)->addText('Otra información:', ['bold' => true]);
        $table->addCell(4500)->addText('${otra_informacion}');

        // Generar el documento
        $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
        $tempFile = tempnam(sys_get_temp_dir(), 'plantilla_ejemplo');
        $objWriter->save($tempFile);

        return response()->download($tempFile, 'plantilla_ejemplo.docx')->deleteFileAfterSend(true);
    }
}
