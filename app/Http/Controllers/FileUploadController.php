<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\IOFactory as SpreadsheetIOFactory;
use PhpOffice\PhpWord\IOFactory as WordIOFactory;
use PhpOffice\PhpWord\TemplateProcessor;

class FileUploadController extends Controller
{
    /**
     * Mostrar el formulario para subir una plantilla.
     */
    public function index()
    {
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
     * Descargar la plantilla actual.
     */
    public function download()
    {
        $templatePath = storage_path('app/templates/plantilla_pla_individualitzat.xlsx');

        if (!file_exists($templatePath)) {
            return redirect()->route('welcome')
                ->with('error', 'La plantilla no existe.');
        }

        return response()->download($templatePath, 'plantilla_pla_individualitzat.xlsx');
    }


    public function process(Request $request)
    {
        // Aumentar el tiempo límite de ejecución a 300 segundos (5 minutos)
        ini_set('max_execution_time', 300);
        set_time_limit(300);

        // Aumentar el límite de memoria si es necesario
        ini_set('memory_limit', '512M');

        $request->validate([
            'excel_file' => 'required|file|mimes:xlsx,xls,csv|max:10240',
        ]);

        if (!$request->hasFile('excel_file')) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['error' => 'No se ha subido ningún archivo'], 400);
            }
            return redirect()->back()->with('error', 'No se ha subido ningún archivo');
        }

        try {
            // Verificar que existe la plantilla Word
            $templatePath = storage_path('app/templates/plantilla_plan_soporte_excel.docx');
            if (!file_exists($templatePath)) {
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json(['error' => 'La plantilla Word no se encuentra en el sistema. Por favor, contacte al administrador.'], 404);
                }
                return redirect()->back()->with('error', 'La plantilla Word no se encuentra en el sistema. Por favor, contacte al administrador.');
            }


            // Guardar archivo Excel temporalmente
            $file = $request->file('excel_file');
            $filePath = $file->getRealPath();

            // Configurar opciones de lectura para optimizar rendimiento
            $readerOptions = [
                'readDataOnly' => true, // Solo leer datos, ignorar estilos
                'readEmptyCells' => false // No leer celdas vacías
            ];

            // Leer archivo Excel con opciones optimizadas
            $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReaderForFile($filePath);

            // Aplicar opciones si el lector lo soporta
            if (method_exists($reader, 'setReadDataOnly')) {
                $reader->setReadDataOnly(true);
            }

            if (method_exists($reader, 'setReadEmptyCells')) {
                $reader->setReadEmptyCells(false);
            }

            $spreadsheet = $reader->load($filePath);
            $worksheet = $spreadsheet->getActiveSheet();

            // Buscar celdas específicas
            $data = $this->extractDataFromExcel($worksheet);

            if (empty($data)) {
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json(['error' => 'No se encontraron los datos requeridos en el Excel'], 400);
                }
                return redirect()->back()->with('error', 'No se encontraron los datos requeridos en el Excel');
            }

            // Generar documento Word directamente para descargar
            return $this->generateAndDownloadWordDocument($data);

        } catch (\PhpOffice\PhpSpreadsheet\Reader\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['error' => 'Error al leer el archivo Excel. Asegúrate de que el formato es correcto.'], 400);
            }
            return redirect()->back()->with('error', 'Error al leer el archivo Excel. Asegúrate de que el formato es correcto.');
        } catch (\PhpOffice\PhpWord\Exception\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['error' => 'Error al procesar la plantilla Word: ' . $e->getMessage()], 500);
            }
            return redirect()->back()->with('error', 'Error al procesar la plantilla Word: ' . $e->getMessage());
        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['error' => 'Error al procesar el archivo: ' . $e->getMessage()], 500);
            }
            return redirect()->back()->with('error', 'Error al procesar el archivo: ' . $e->getMessage());
        }
    }

    /**
     * Extraer los datos específicos del Excel
     */
    private function extractDataFromExcel($worksheet)
    {
        // Incrementar el tiempo límite para esta operación específica
        ini_set('max_execution_time', 300);

        $data = [];

        // Mapeo de celdas específicas a variables en la plantilla Word
        $cellMappings = [
            // Datos personales
            'C6' => 'nombre_estudiante',
            'E6' => 'fecha_nacimiento',
            'C7' => 'lugar_nacimiento',
            'E7' => 'nombre_tutores',
            'C8' => 'direccion',
            'E8' => 'telefono',
            'C9' => 'lengua_habitual',
            'E9' => 'otras_lenguas',

            // Datos escolares
            'C13' => 'curso',
            'E13' => 'grupo',
            'C14' => 'tutor',
            'E14' => 'fecha_llegada_catalunya',
            'C15' => 'fecha_incorporacion_centro',
            'E15' => 'fecha_incorporacion_sistema',
            'C16' => 'centros_anteriores',
            'E16' => 'escolarizacion_previa',
            'C17' => 'retencion_curso',
            'C18' => 'otra_informacion',

            // Nuevos mapeos
            'I16' => 'competencies_alumne_capabilities',
            'J18' => 'learning_strong_points',
            'J19' => 'learning_improvement_points',
            'J22' => 'student_interests'
        ];

        // Optimización: limitar las llamadas a getParent()->getAllSheets()
        $allSheets = $worksheet->getParent()->getAllSheets();
        $sheetMap = [];

        // Crear un mapa de nombres de hojas para búsqueda más eficiente
        foreach ($allSheets as $sheet) {
            $sheetMap[$sheet->getTitle()] = $sheet;
        }

        // Función para encontrar una hoja por nombre aproximado
        $findSheetByPattern = function ($patterns) use ($sheetMap) {
            foreach ($patterns as $pattern) {
                foreach ($sheetMap as $title => $sheet) {
                    if (stripos($title, $pattern) !== false) {
                        return $sheet;
                    }
                }
            }
            return null;
        };

        // Extraer valores de celdas específicas
        foreach ($cellMappings as $cellReference => $wordVariable) {
            try {
                $value = $worksheet->getCell($cellReference)->getValue();

                // Si es una fecha, formatearla adecuadamente
                if (in_array($wordVariable, ['fecha_nacimiento', 'fecha_llegada_catalunya', 'fecha_incorporacion_centro', 'fecha_incorporacion_sistema']) && $value instanceof \DateTimeInterface) {
                    $value = $value->format('d/m/Y');
                }

                $data[$wordVariable] = $value ?? '';
            } catch (\Exception $e) {
                // Si hay un error al leer la celda, dejar el valor en blanco
                $data[$wordVariable] = '';
            }
        }

        // Intentar extraer la justificación breve usando la función de búsqueda
        try {
            $patternsConcrecio = ['Portada - Concreció', 'Portada - Concrecio', 'Concreció del PI', 'Concrecio del PI', 'PI'];
            $concrecioSheet = $findSheetByPattern($patternsConcrecio);

            if ($concrecioSheet) {
                // Intentar leer el rango C15:F15
                $briefJustification = '';

                // Leer cada celda del rango
                for ($col = 'C'; $col <= 'F'; $col++) {
                    $cellValue = $concrecioSheet->getCell($col . '15')->getValue() ?? '';
                    if (!empty($cellValue)) {
                        $briefJustification .= $cellValue . ' ';
                    }
                }

                $data['brief_justification'] = trim($briefJustification);
            } else {
                $data['brief_justification'] = '';
            }
        } catch (\Exception $e) {
            $data['brief_justification'] = '';
        }

        // Intentar extraer objetivos y criterios usando la función de búsqueda
        try {
            $patternsAreas = ['CE_Àrees', 'CE_Arees', 'Areas', 'Àrees', 'Matèries', 'Materies'];
            $ceAreasSheet = $findSheetByPattern($patternsAreas);

            if ($ceAreasSheet) {
                // Extraer objetivos de E4:E25
                for ($row = 4; $row <= 25; $row++) {
                    $cellValue = $ceAreasSheet->getCell('E' . $row)->getValue() ?? '';
                    // Índice para la plantilla (empieza en 1 aunque la primera celda es E4)
                    $index = $row - 3;
                    // Modificar el nombre de la variable para incluir el símbolo #
                    $data['objetivo#' . $index] = $cellValue;

                    if (!empty($cellValue)) {
                    }
                }

                // Extraer criterios de F4:F25
                for ($row = 4; $row <= 25; $row++) {
                    $cellValue = $ceAreasSheet->getCell('F' . $row)->getValue() ?? '';
                    // Índice para la plantilla (empieza en 1 aunque la primera celda es F4)
                    $index = $row - 3;
                    // Modificar el nombre de la variable para incluir el símbolo #
                    $data['criterio#' . $index] = $cellValue;

                    if (!empty($cellValue)) {
                    }
                }

            } else {
                // Inicializar las variables con cadenas vacías para evitar errores en la plantilla
                for ($i = 1; $i <= 22; $i++) {
                    // Modificar los nombres de las variables para incluir el símbolo #
                    $data['objetivo#' . $i] = '';
                    $data['criterio#' . $i] = '';
                }
            }
        } catch (\Exception $e) {
            // Inicializar las variables con cadenas vacías
            for ($i = 1; $i <= 22; $i++) {
                // Modificar los nombres de las variables para incluir el símbolo #
                $data['objetivo#' . $i] = '';
                $data['criterio#' . $i] = '';
            }
        }

        // Intentar extraer objetivos y criterios transversales de la hoja "CE_Transversals"
        try {
            $patternsTransversals = ['CE_Transversals', 'Transversals', 'Transversales', 'Transversal'];
            $ceTransversalsSheet = $findSheetByPattern($patternsTransversals);

            if ($ceTransversalsSheet) {

                // Extraer objetivos transversales de E4:E25
                for ($row = 4; $row <= 25; $row++) {
                    $cellValue = $ceTransversalsSheet->getCell('E' . $row)->getValue() ?? '';
                    // Índice para la plantilla (empieza en 1 aunque la primera celda es E4)
                    $index = $row - 3;
                    // Asignar al formato de variable correcto
                    $data['transversal_objective#' . $index] = $cellValue;

                    if (!empty($cellValue)) {
                        }
                }

                // Extraer criterios transversales de F4:F25
                for ($row = 4; $row <= 25; $row++) {
                    $cellValue = $ceTransversalsSheet->getCell('F' . $row)->getValue() ?? '';
                    // Índice para la plantilla (empieza en 1 aunque la primera celda es F4)
                    $index = $row - 3;
                    // Asignar al formato de variable correcto
                    $data['transversal_criteria#' . $index] = $cellValue;

                    if (!empty($cellValue)) {
                    }
                }

            } else {
                // Inicializar las variables con cadenas vacías para evitar errores en la plantilla
                for ($i = 1; $i <= 22; $i++) {
                    $data['transversal_objective#' . $i] = '';
                    $data['transversal_criteria#' . $i] = '';
                }
            }
        } catch (\Exception $e) {
            // Inicializar las variables con cadenas vacías
            for ($i = 1; $i <= 22; $i++) {
                $data['transversal_objective#' . $i] = '';
                $data['transversal_criteria#' . $i] = '';
            }
        }

        // Intentar extraer datos de la tabla Sabers (área/materia, bloc_sabers y saber)
        try {
            $patternsSabers = ['Sabers', 'Saberes', 'Tabla Sabers', 'Taula Sabers', 'Competencies', 'Competències'];
            $sabersSheet = $findSheetByPattern($patternsSabers);

            if (!$sabersSheet) {
                // Si no se encuentra una hoja específica, intentar usar la hoja activa
                $sabersSheet = $worksheet;
            }

            if ($sabersSheet) {

                // Contador para asignar índices secuenciales (1-18)
                $counter = 1;

                // Extraer datos de la tabla B4:D35
                for ($row = 4; $row <= 35 && $counter <= 18; $row++) {
                    // Verificar si hay contenido en la fila (al menos en una de las columnas)
                    $areaValue = $sabersSheet->getCell('B' . $row)->getValue() ?? '';
                    $blocValue = $sabersSheet->getCell('C' . $row)->getValue() ?? '';
                    $saberValue = $sabersSheet->getCell('D' . $row)->getValue() ?? '';

                    // Si hay algún contenido, extraer los valores
                    if (!empty($areaValue) || !empty($blocValue) || !empty($saberValue)) {
                        // Asignar los valores a las variables con el formato correcto
                        $data['area_materia#' . $counter] = $areaValue;
                        $data['bloc_sabers#' . $counter] = $blocValue;
                        $data['saber#' . $counter] = $saberValue;


                        // Incrementar el contador solo si se encontró contenido
                        $counter++;
                    }
                }

                // Asegurarse de que todas las variables hasta 18 están inicializadas
                for ($i = $counter; $i <= 18; $i++) {
                    $data['area_materia#' . $i] = '';
                    $data['bloc_sabers#' . $i] = '';
                    $data['saber#' . $i] = '';
                }

            } else {
                // Inicializar todas las variables con cadenas vacías
                for ($i = 1; $i <= 18; $i++) {
                    $data['area_materia#' . $i] = '';
                    $data['bloc_sabers#' . $i] = '';
                    $data['saber#' . $i] = '';
                }
            }
        } catch (\Exception $e) {
            // Inicializar las variables con cadenas vacías
            for ($i = 1; $i <= 18; $i++) {
                $data['area_materia#' . $i] = '';
                $data['bloc_sabers#' . $i] = '';
                $data['saber#' . $i] = '';
            }
        }

        // Intentar extraer datos de la hoja de desenvolupament
        try {
            $patternsDesenvolupament = ['Desenvolupament', 'Desarrollo', 'Desenvolupament del PI', 'Desarrollo del PI'];
            $desenvolupamentSheet = $findSheetByPattern($patternsDesenvolupament);

            if (!$desenvolupamentSheet) {
                $desenvolupamentSheet = $worksheet;
            }

            if ($desenvolupamentSheet) {

                // Extraer datos de les reunions de desenvolupament (B5:E14 per tenir 10 files)
                for ($i = 1; $i <= 10; $i++) {
                    $row = $i + 4; // Empezamos en la fila 5 (índex + 4)

                    // Extraer els valors de cada columna
                    $dataValue = trim($desenvolupamentSheet->getCell('B' . $row)->getValue() ?? '');
                    $agentsValue = trim($desenvolupamentSheet->getCell('C' . $row)->getValue() ?? '');
                    $temesValue = trim($desenvolupamentSheet->getCell('D' . $row)->getValue() ?? '');
                    $acordsValue = trim($desenvolupamentSheet->getCell('E' . $row)->getValue() ?? '');

                    // Asignar els valors a les variables corresponents
                    $data['desenvolupament_data#' . $i] = $dataValue;
                    $data['desenvolupament_agents#' . $i] = $agentsValue;
                    $data['desenvolupament_temes#' . $i] = $temesValue;
                    $data['desenvolupament_acords#' . $i] = $acordsValue;

                    if (!empty($dataValue) || !empty($agentsValue) || !empty($temesValue) || !empty($acordsValue)) {
                    }
                }

            } else {
                // Inicialitzar totes les variables amb cadenes buides
                for ($i = 1; $i <= 10; $i++) {
                    $data['desenvolupament_data#' . $i] = '';
                    $data['desenvolupament_agents#' . $i] = '';
                    $data['desenvolupament_temes#' . $i] = '';
                    $data['desenvolupament_acords#' . $i] = '';
                }
            }
        } catch (\Exception $e) {
            // Inicialitzar les variables amb cadenes buides
            for ($i = 1; $i <= 10; $i++) {
                $data['desenvolupament_data#' . $i] = '';
                $data['desenvolupament_agents#' . $i] = '';
                $data['desenvolupament_temes#' . $i] = '';
                $data['desenvolupament_acords#' . $i] = '';
            }
        }

        // Intentar extraer datos de la hoja de Seguiment i avaluació
        try {
            $patternsSeguiment = ['Seguiment i avaluació', 'Seguiment', 'Seguimiento y evaluación', 'Seguimiento'];
            $seguimentSheet = $findSheetByPattern($patternsSeguiment);

            if (!$seguimentSheet) {
                $seguimentSheet = $worksheet;
            }

            if ($seguimentSheet) {

                // Extraer dades de les reunions de seguiment (B5:E14)
                for ($i = 1; $i <= 10; $i++) {
                    $row = $i + 4; // Empezamos en la fila 5 (índex + 4)

                    // Extraer els valors de cada columna
                    $dataValue = trim($seguimentSheet->getCell('B' . $row)->getValue() ?? '');
                    $agentsValue = trim($seguimentSheet->getCell('C' . $row)->getValue() ?? '');
                    $temesValue = trim($seguimentSheet->getCell('D' . $row)->getValue() ?? '');
                    $acordsValue = trim($seguimentSheet->getCell('E' . $row)->getValue() ?? '');

                    // Asignar els valors a les variables corresponents amb el prefix reunio_s_
                    $data['reunio_s_data#' . $i] = $dataValue;
                    $data['reunio_s_agents#' . $i] = $agentsValue;
                    $data['reunio_s_temes#' . $i] = $temesValue;
                    $data['reunio_s_acords#' . $i] = $acordsValue;

                    if (!empty($dataValue) || !empty($agentsValue) || !empty($temesValue) || !empty($acordsValue)) {
                    }
                }

            } else {
                // Inicialitzar totes les variables amb cadenes buides
                for ($i = 1; $i <= 10; $i++) {
                    $data['reunio_s_data#' . $i] = '';
                    $data['reunio_s_agents#' . $i] = '';
                    $data['reunio_s_temes#' . $i] = '';
                    $data['reunio_s_acords#' . $i] = '';
                }
            }
        } catch (\Exception $e) {
            // Inicialitzar les variables amb cadenes buides
            for ($i = 1; $i <= 10; $i++) {
                $data['reunio_s_data#' . $i] = '';
                $data['reunio_s_agents#' . $i] = '';
                $data['reunio_s_temes#' . $i] = '';
                $data['reunio_s_acords#' . $i] = '';
            }
        }

        // Extraer los checkboxes de justificación
        $checkboxMapping = [
            'I7' => ['field' => 'motivado_informe_reconeixement', 'desc_field' => 'motivado_informe_reconeixement_desc'],
            'I8' => ['field' => 'motivado_avaluacio_psicopedagogica', 'desc_field' => 'motivado_avaluacio_psicopedagogica_desc'],
            'I9' => ['field' => 'motivado_avaluacio_inicial_nouvingut', 'desc_field' => 'motivado_avaluacio_inicial_nouvingut_desc'],
            'I10' => ['field' => 'motivado_avaluacio_origen_estranger_aula', 'desc_field' => 'motivado_avaluacio_origen_estranger_aula_desc'],
            'I11' => ['field' => 'motivado_avaluacio_origen_estranger_tardana', 'desc_field' => 'motivado_avaluacio_origen_estranger_tardana_desc'],
            'I12' => ['field' => 'motivado_decisio_comissio', 'desc_field' => 'motivado_decisio_comissio_desc'],
            'I13' => ['field' => 'motivado_altres', 'desc_field' => 'motivado_altres_desc']
        ];

        // Valores de texto para las descripciones
        $descTexts = [
            'I7' => 'Informe de reconeixement de necessitats específiques de suport educatiu',
            'I8' => 'Avaluació psicopedagògica',
            'I9' => 'Resultat de l\'avaluació inicial de l\'alumne/a nouvingut',
            'I10' => 'Avaluació de l\'alumne/a d\'origen estranger que ja no assisteix a l\'aula d\'acollida però que rep suport a l\'aula ordinària',
            'I11' => 'Avaluació de l\'alumne/a d\'origen estranger amb necessitats educatives derivades de la incorporació tardana al sistema educatiu',
            'I12' => 'Decisió de la comissió d\'atenció educativa inclusiva (CAEI)',
            'I13' => 'Altres'
        ];

        // Procesar cada checkbox
        foreach ($checkboxMapping as $cell => $fields) {
            try {
                // Verificar si la celda está marcada (true/false)
                $isChecked = (bool)$worksheet->getCell($cell)->getValue();

                // Guardar el estado del checkbox (símbol Unicode o vacío)
                $data[$fields['field']] = $isChecked ? '☑' : '☐';

                // Guardar la descripción del checkbox
                $data[$fields['desc_field']] = $descTexts[$cell];
            } catch (\Exception $e) {
                $data[$fields['field']] = '';
                $data[$fields['desc_field']] = $descTexts[$cell];
            }
        }

        // Extraer información específica para la comisión (celda J12)
        try {
            $comisionValue = $worksheet->getCell('J12')->getValue() ?: '';

            // Guardar el texto completo de la celda J12 para la variable motivado_decisio_comissio_desc
            $data['motivado_decisio_comissio_desc'] = $comisionValue;

            // Si hay valor en la celda J12, intentamos extraer propuesta y motivación
            if (!empty($comisionValue)) {
                // Intentar separar la cadena por la palabra "motivada per"
                if (strpos($comisionValue, 'motivada per') !== false) {
                    $parts = explode('motivada per', $comisionValue);

                    // Extraer la parte del proponente (suponiendo formato "... a proposta de X")
                    $proponentPart = trim($parts[0]);
                    if (strpos($proponentPart, 'a proposta de') !== false) {
                        $proponentParts = explode('a proposta de', $proponentPart);
                        $data['commission_proponent'] = trim(end($proponentParts));
                    } else {
                        $data['commission_proponent'] = $proponentPart;
                    }

                    // Extraer la motivación
                    $data['commission_motivation'] = isset($parts[1]) ? trim($parts[1]) : '';
                } else {
                    // Si no sigue el formato esperado, guardamos todo el valor
                    $data['commission_proponent'] = $comisionValue;
                    $data['commission_motivation'] = '';
                }
            } else {
                $data['commission_proponent'] = '';
                $data['commission_motivation'] = '';
            }
        } catch (\Exception $e) {
            $data['commission_proponent'] = '';
            $data['commission_motivation'] = '';
        }

        // Buscar tablas específicas si existen
        $tables = [
            'Necesidades Educativas' => 'tabla_necesidades',
            'Apoyos Actuales' => 'tabla_apoyos',
            // Añadir otras tablas que puedan estar en el Excel
        ];

        foreach ($tables as $tableTitle => $variableName) {
            if ($this->hasTable($worksheet, $tableTitle)) {
                $tableData = $this->extractTable($worksheet, $tableTitle);
                $data[$variableName] = $tableData;
            }
        }


        return $data;
    }

    private function generateAndDownloadWordDocument($data)
    {
        // Aumentar el tiempo límite para esta operación
        ini_set('max_execution_time', 300);

        // Ruta a la plantilla
        $templatePath = storage_path('app/templates/plantilla_plan_soporte_excel.docx');

        // Verificar que existe la plantilla
        if (!file_exists($templatePath)) {
            throw new \Exception('La plantilla no se encuentra en la ruta: ' . $templatePath);
        }

        // Usar la plantilla existente
        $templateProcessor = new TemplateProcessor($templatePath);

        // Dividir el proceso de reemplazo en bloques para evitar sobrecarga
        $dataChunks = array_chunk($data, 50, true);

        foreach ($dataChunks as $chunk) {
            // Reemplazar variables de texto en la plantilla
            foreach ($chunk as $key => $value) {
                // Si el valor es un array, asumimos que es una tabla y la procesamos separadamente
                if (!is_array($value)) {
                    // Truncar valores muy largos para evitar problemas de memoria
                    if (is_string($value) && strlen($value) > 32000) {
                        $value = substr($value, 0, 32000) . '...';
                    }

                    try {
                        // Asegurarse de que el valor sea una cadena para evitar problemas
                        $value = !is_null($value) ? (string)$value : '';

                        // Reemplazar la variable en la plantilla
                        $templateProcessor->setValue($key, $value);
                    } catch (\Exception $e) {
                        // Intentar con un valor vacío si hay error
                        try {
                            $templateProcessor->setValue($key, '');
                        } catch (\Exception $innerE) {
                        }
                    }
                }
            }
        }

        // Procesar tablas si existen en la plantilla
        $tableMappings = [
            'tabla_necesidades' => 'necesidades_educativas',
            'tabla_apoyos' => 'apoyos_actuales',
            // Añadir otras tablas según sea necesario
        ];

        foreach ($tableMappings as $dataKey => $templateKey) {
            if (isset($data[$dataKey]) && is_array($data[$dataKey]) && !empty($data[$dataKey])) {
                try {
                    $this->insertTableData($templateProcessor, $templateKey, $data[$dataKey]);
                } catch (\Exception $e) {
                    // Continuar con otras tablas si hay error
                }
            }
        }

        // Nombre de archivo basado en el nombre del estudiante si está disponible
        $fileName = isset($data['nombre_estudiante']) && !empty($data['nombre_estudiante'])
            ? 'plan_soporte_' . preg_replace('/[^a-zA-Z0-9]/', '_', $data['nombre_estudiante']) . '.docx'
            : 'plan_soporte.docx';

        // Guardar el documento en memoria para descarga
        $tempFile = tempnam(sys_get_temp_dir(), 'word_');
        $templateProcessor->saveAs($tempFile);

        // Leer el contenido del archivo
        $fileContent = file_get_contents($tempFile);

        // Eliminar el archivo temporal
        unlink($tempFile);

        // Crear una respuesta HTTP con el documento y los headers adecuados
        return response($fileContent)
            ->header('Content-Type', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document')
            ->header('Content-Disposition', 'attachment; filename="' . $fileName . '"')
            ->header('Content-Length', strlen($fileContent))
            ->header('Cache-Control', 'no-cache, no-store, max-age=0, must-revalidate')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }

     private function insertTableData($templateProcessor, $blockName, $tableData)
    {
        // Verificar si la tabla tiene datos
        if (empty($tableData)) {
            return;
        }

        // Estructura de datos para el template
        $replacements = [];

        // Para cada fila de la tabla
        foreach ($tableData as $row) {
            // Mapear columnas a variables de la plantilla
            $rowData = [];

            // Ajustar según la estructura de tu tabla
            if (isset($row[0])) $rowData['columna1'] = $row[0];
            if (isset($row[1])) $rowData['columna2'] = $row[1];
            if (isset($row[2])) $rowData['columna3'] = $row[2];
            // Añadir más columnas según sea necesario

            $replacements[] = $rowData;
        }

        // Reemplazar el bloque con los datos de la tabla
        if (!empty($replacements)) {
            try {
                $templateProcessor->cloneBlock($blockName, 0, true, false, $replacements);
            } catch (\Exception $e) {

                // Intenta clonar filas si el bloque no funciona
                try {
                    $templateProcessor->cloneRowAndSetValues($blockName, $replacements);
                } catch (\Exception $e2) {
                    throw $e2;
                }
            }
        }
    }



    /**
     * Verifica si existe una tabla con el título especificado
     */
    private function hasTable($worksheet, $tableTitle)
    {
        $highestRow = $worksheet->getHighestRow();
        $highestColumn = $worksheet->getHighestColumn();
        $highestColumnIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestColumn);

        for ($row = 1; $row <= $highestRow; $row++) {
            for ($col = 1; $col <= $highestColumnIndex; $col++) {
                $cellCoordinate = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col - 1) . $row;
                $cellValue = trim($worksheet->getCell($cellCoordinate)->getValue());
                if (strpos($cellValue, $tableTitle) !== false) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Extrae una tabla completa a partir de su título
     */
    private function extractTable($worksheet, $tableTitle)
    {
        $table = [];
        $tableFound = false;
        $tableEndFound = false;
        $startRow = 0;

        $highestRow = $worksheet->getHighestRow();
        $highestColumn = $worksheet->getHighestColumn();
        $highestColumnIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($highestColumn);

        // Buscar el inicio de la tabla
        for ($row = 1; $row <= $highestRow && !$tableFound; $row++) {
            for ($col = 1; $col <= $highestColumnIndex && !$tableFound; $col++) {
                $cellCoordinate = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col - 1) . $row;
                $cellValue = trim($worksheet->getCell($cellCoordinate)->getValue());
                if (strpos($cellValue, $tableTitle) !== false) {
                    $tableFound = true;
                    $startRow = $row + 1; // Asumimos que la tabla comienza en la siguiente fila
                    break;
                }
            }
        }

        if (!$tableFound) {
            return $table;
        }

        // Extrae la tabla hasta encontrar una fila vacía
        for ($row = $startRow; $row <= $highestRow && !$tableEndFound; $row++) {
            $rowData = [];
            $rowIsEmpty = true;

            for ($col = 1; $col <= $highestColumnIndex; $col++) {
                $cellCoordinate = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col - 1) . $row;
                $cellValue = $worksheet->getCell($cellCoordinate)->getValue();
                $rowData[] = $cellValue;

                if (!empty($cellValue)) {
                    $rowIsEmpty = false;
                }
            }

            if ($rowIsEmpty) {
                $tableEndFound = true;
            } else {
                $table[] = $rowData;
            }
        }

        return $table;
    }

}
