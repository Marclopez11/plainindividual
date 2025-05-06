<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpSpreadsheet\IOFactory as SpreadsheetIOFactory;
use PhpOffice\PhpWord\IOFactory as WordIOFactory;
use PhpOffice\PhpWord\TemplateProcessor;

class ExcelWordController extends Controller
{
    /**
     * Mostrar el formulario de subida de Excel
     */
    public function index()
    {
        return view('excel-word.index');
    }

    /**
     * Procesar el archivo Excel y generar la plantilla Word
     */
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
            return redirect()->back()->with('error', 'No se ha subido ningún archivo');
        }

        try {
            // Verificar que existe la plantilla Word
            $templatePath = storage_path('app/templates/plantilla_plan_soporte_excel.docx');
            if (!file_exists($templatePath)) {
                Log::error('Plantilla Word no encontrada: ' . $templatePath);
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
                return redirect()->back()->with('error', 'No se encontraron los datos requeridos en el Excel');
            }

            // Generar documento Word directamente para descargar
            return $this->generateAndDownloadWordDocument($data);

        } catch (\PhpOffice\PhpSpreadsheet\Reader\Exception $e) {
            Log::error('Error al leer el archivo Excel: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error al leer el archivo Excel. Asegúrate de que el formato es correcto.');
        } catch (\PhpOffice\PhpWord\Exception\Exception $e) {
            Log::error('Error al procesar la plantilla Word: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error al procesar la plantilla Word: ' . $e->getMessage());
        } catch (\Exception $e) {
            Log::error('Error general al procesar el archivo: ' . $e->getMessage());
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
            // Log para depuración
            Log::info("Hoja encontrada en el Excel: " . $sheet->getTitle());
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
                Log::warning("No se pudo leer la celda $cellReference: " . $e->getMessage());
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
                Log::info("Justificación breve extraída: " . $data['brief_justification']);
            } else {
                $data['brief_justification'] = '';
                Log::warning("No se encontró la hoja 'Portada - Concreció del PI'");
            }
        } catch (\Exception $e) {
            $data['brief_justification'] = '';
            Log::warning("Error al extraer la justificación breve: " . $e->getMessage());
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
                        Log::info("Objetivo #{$index} extraído: " . $cellValue);
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
                        Log::info("Criterio #{$index} extraído: " . $cellValue);
                    }
                }

                Log::info("Objetivos y criterios extraídos correctamente de la hoja CE_Àrees/Matèries");
            } else {
                Log::warning("No se encontró la hoja 'CE_Àrees/Matèries'");
                // Inicializar las variables con cadenas vacías para evitar errores en la plantilla
                for ($i = 1; $i <= 22; $i++) {
                    // Modificar los nombres de las variables para incluir el símbolo #
                    $data['objetivo#' . $i] = '';
                    $data['criterio#' . $i] = '';
                }
            }
        } catch (\Exception $e) {
            Log::warning("Error al extraer objetivos y criterios: " . $e->getMessage());
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
                Log::info("Hoja CE_Transversals encontrada: " . $ceTransversalsSheet->getTitle());

                // Extraer objetivos transversales de E4:E25
                for ($row = 4; $row <= 25; $row++) {
                    $cellValue = $ceTransversalsSheet->getCell('E' . $row)->getValue() ?? '';
                    // Índice para la plantilla (empieza en 1 aunque la primera celda es E4)
                    $index = $row - 3;
                    // Asignar al formato de variable correcto
                    $data['transversal_objective#' . $index] = $cellValue;

                    if (!empty($cellValue)) {
                        Log::info("Objetivo transversal #{$index} extraído: " . $cellValue);
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
                        Log::info("Criterio transversal #{$index} extraído: " . $cellValue);
                    }
                }

                Log::info("Objetivos y criterios transversales extraídos correctamente de la hoja CE_Transversals");
            } else {
                Log::warning("No se encontró la hoja 'CE_Transversals'");
                // Inicializar las variables con cadenas vacías para evitar errores en la plantilla
                for ($i = 1; $i <= 22; $i++) {
                    $data['transversal_objective#' . $i] = '';
                    $data['transversal_criteria#' . $i] = '';
                }
            }
        } catch (\Exception $e) {
            Log::warning("Error al extraer objetivos y criterios transversales: " . $e->getMessage());
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
                Log::info("No se encontró una hoja específica para Sabers, intentando usar la hoja activa");
                $sabersSheet = $worksheet;
            }

            if ($sabersSheet) {
                Log::info("Hoja para tabla Sabers encontrada: " . $sabersSheet->getTitle());

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

                        Log::info("Fila #{$counter} de Sabers extraída: Área={$areaValue}, Bloc={$blocValue}, Saber={$saberValue}");

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

                Log::info("Datos de la tabla Sabers extraídos correctamente: " . ($counter - 1) . " filas con contenido");
            } else {
                Log::warning("No se pudo encontrar una hoja adecuada para la tabla Sabers");
                // Inicializar todas las variables con cadenas vacías
                for ($i = 1; $i <= 18; $i++) {
                    $data['area_materia#' . $i] = '';
                    $data['bloc_sabers#' . $i] = '';
                    $data['saber#' . $i] = '';
                }
            }
        } catch (\Exception $e) {
            Log::warning("Error al extraer datos de la tabla Sabers: " . $e->getMessage());
            // Inicializar las variables con cadenas vacías
            for ($i = 1; $i <= 18; $i++) {
                $data['area_materia#' . $i] = '';
                $data['bloc_sabers#' . $i] = '';
                $data['saber#' . $i] = '';
            }
        }

        // Intentar extraer datos de la tabla de reuniones de seguimiento en la hoja "Desenvolupament"
        try {
            $patternsDesen = ['Desenvolupament', 'Desarrollo', 'Reunions', 'Reuniones', 'Seguiment', 'Seguimiento'];
            $reunionesSheet = $findSheetByPattern($patternsDesen);

            if ($reunionesSheet) {
                Log::info("Hoja para tabla de reuniones encontrada: " . $reunionesSheet->getTitle());

                // Array para almacenar las filas de la tabla
                $reunionesRows = [];
                $maxRow = 30; // Límite máximo de filas a buscar

                // Extraer datos de la tabla comenzando en B5, C5, D5, E5
                for ($row = 5; $row <= $maxRow; $row++) {
                    // Extraer valores de cada columna
                    $dataValue = $reunionesSheet->getCell('B' . $row)->getValue() ?? '';
                    $agentsValue = $reunionesSheet->getCell('C' . $row)->getValue() ?? '';
                    $temesValue = $reunionesSheet->getCell('D' . $row)->getValue() ?? '';
                    $acordsValue = $reunionesSheet->getCell('E' . $row)->getValue() ?? '';

                    // Verificar si la fila tiene algún contenido
                    if (!empty($dataValue) || !empty($agentsValue) || !empty($temesValue) || !empty($acordsValue)) {
                        // Guardar la fila completa
                        $reunionesRows[] = [
                            'data' => $dataValue,
                            'agents' => $agentsValue,
                            'temes' => $temesValue,
                            'acords' => $acordsValue
                        ];

                        Log::info("Fila de reunión extraída: Data={$dataValue}, Agents={$agentsValue}, Temes={$temesValue}, Acords={$acordsValue}");
                    } else {
                        // Si encontramos una fila vacía después de la fila 5, podemos detenernos
                        // a menos que haya más filas no vacías después
                        if ($row > 5 && empty($reunionesRows)) {
                            // No se encontró ninguna fila con datos
                            break;
                        } elseif ($row > 5) {
                            // Verificar si hay más filas con contenido después
                            $hasMoreContent = false;
                            for ($checkRow = $row + 1; $checkRow <= $row + 3 && $checkRow <= $maxRow; $checkRow++) {
                                $checkDataValue = $reunionesSheet->getCell('B' . $checkRow)->getValue() ?? '';
                                $checkAgentsValue = $reunionesSheet->getCell('C' . $checkRow)->getValue() ?? '';
                                $checkTemesValue = $reunionesSheet->getCell('D' . $checkRow)->getValue() ?? '';
                                $checkAcordsValue = $reunionesSheet->getCell('E' . $checkRow)->getValue() ?? '';

                                if (!empty($checkDataValue) || !empty($checkAgentsValue) || !empty($checkTemesValue) || !empty($checkAcordsValue)) {
                                    $hasMoreContent = true;
                                    break;
                                }
                            }

                            if (!$hasMoreContent) {
                                // No hay más contenido después de esta fila vacía
                                break;
                            }
                            // Si hay más contenido, continuamos el bucle
                        }
                    }
                }

                // Guardar el número de filas para usarlo en la plantilla
                $data['reuniones_count'] = count($reunionesRows);

                // Guardar cada fila en variables individuales para compatibilidad
                for ($i = 0; $i < count($reunionesRows); $i++) {
                    $index = $i + 1;
                    $data['reunio_data#' . $index] = $reunionesRows[$i]['data'];
                    $data['reunio_agents#' . $index] = $reunionesRows[$i]['agents'];
                    $data['reunio_temes#' . $index] = $reunionesRows[$i]['temes'];
                    $data['reunio_acords#' . $index] = $reunionesRows[$i]['acords'];
                }

                // Guardar el array completo para uso posterior (en caso de necesitar uso de tabla dinámica)
                $data['reuniones_rows'] = $reunionesRows;

                Log::info("Datos de la tabla de reuniones extraídos correctamente: " . count($reunionesRows) . " filas con contenido");
            } else {
                Log::warning("No se encontró la hoja 'Desenvolupament' o similar para la tabla de reuniones");
                $data['reuniones_count'] = 0;
                $data['reuniones_rows'] = [];
            }
        } catch (\Exception $e) {
            Log::warning("Error al extraer datos de la tabla de reuniones: " . $e->getMessage());
            $data['reuniones_count'] = 0;
            $data['reuniones_rows'] = [];
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
                Log::warning("No se pudo leer la celda $cell: " . $e->getMessage());
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
            Log::warning("No se pudo leer la celda J12: " . $e->getMessage());
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

        // Log de datos extraídos para debugging
        Log::info('Datos extraídos del Excel (cantidad): ' . count($data));

        return $data;
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
                $cellValue = trim($worksheet->getCellByColumnAndRow($col, $row)->getValue());
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
                $cellValue = trim($worksheet->getCellByColumnAndRow($col, $row)->getValue());
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
                $cellValue = $worksheet->getCellByColumnAndRow($col, $row)->getValue();
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

    /**
     * Generar documento Word y enviarlo directamente para descarga
     */
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

        // Log de todas las variables para debug
        Log::info('Variables disponibles para la plantilla: ' . implode(', ', array_keys($data)));

        // Procesar la tabla de reuniones si existe
        $this->processReunionesTable($templateProcessor, $data);

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
                        Log::warning("Valor truncado para la variable $key (excede 32000 caracteres)");
                    }

                    try {
                        // Asegurarse de que el valor sea una cadena para evitar problemas
                        $value = !is_null($value) ? (string)$value : '';

                        // Reemplazar la variable en la plantilla
                        $templateProcessor->setValue($key, $value);
                        Log::debug("Variable reemplazada correctamente: {$key}");
                    } catch (\Exception $e) {
                        Log::warning("Error al establecer valor para $key: " . $e->getMessage());
                        // Intentar con un valor vacío si hay error
                        try {
                            $templateProcessor->setValue($key, '');
                        } catch (\Exception $innerE) {
                            Log::error("No se pudo establecer un valor vacío para $key: " . $innerE->getMessage());
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
                    Log::error("Error al procesar la tabla $templateKey: " . $e->getMessage());
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

        // Crear una respuesta HTTP con el documento
        $response = response('', 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
            'Cache-Control' => 'max-age=0',
        ]);

        // Leer el contenido del archivo temporal y colocarlo en la respuesta
        $response->setContent(file_get_contents($tempFile));

        // Eliminar el archivo temporal
        unlink($tempFile);

        return $response;
    }

    /**
     * Insertar datos de tabla en la plantilla Word
     */
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
                Log::warning("No se pudo clonar el bloque $blockName. Posiblemente no existe en la plantilla: " . $e->getMessage());

                // Intenta clonar filas si el bloque no funciona
                try {
                    $templateProcessor->cloneRowAndSetValues($blockName, $replacements);
                } catch (\Exception $e2) {
                    Log::error("Error al clonar filas para $blockName: " . $e2->getMessage());
                    throw $e2;
                }
            }
        }
    }

    /**
     * Procesa la tabla de reuniones para generar una tabla dinámica en el documento Word
     */
    private function processReunionesTable($templateProcessor, $data)
    {
        // Verificar si tenemos datos de reuniones
        if (!isset($data['reuniones_count']) || $data['reuniones_count'] <= 0) {
            Log::info("No hay datos de reuniones para procesar");
            return;
        }

        try {
            Log::info("Procesando tabla de reuniones con " . $data['reuniones_count'] . " filas");

            // Para evitar el problema de #1#1, usaremos un enfoque diferente
            // Primero, extraemos las filas que necesitamos
            $reunionesCount = $data['reuniones_count'];

            // Si tenemos al menos una fila, procedemos
            if ($reunionesCount > 0) {
                try {
                    // Primero, guardamos los valores originales de la primera fila
                    $originalData = $data['reunio_data#1'] ?? '';
                    $originalAgents = $data['reunio_agents#1'] ?? '';
                    $originalTemes = $data['reunio_temes#1'] ?? '';
                    $originalAcords = $data['reunio_acords#1'] ?? '';

                    Log::info("Datos originales para la primera fila guardados");

                    // Ahora, en lugar de usar cloneRow, añadiremos filas a la tabla manualmente
                    // usando variables temporales con nombres sin números (para evitar el problema #1#1)

                    // Para la primera fila, usamos los datos directamente
                    $templateProcessor->setValue('reunio_data#1', $originalData);
                    $templateProcessor->setValue('reunio_agents#1', $originalAgents);
                    $templateProcessor->setValue('reunio_temes#1', $originalTemes);
                    $templateProcessor->setValue('reunio_acords#1', $originalAcords);

                    Log::info("Primera fila procesada correctamente");

                    // Si hay más de una fila, necesitamos un enfoque diferente
                    if ($reunionesCount > 1) {
                        // Usamos el método cloneRow, pero DESPUÉS tendremos que corregir los nombres de las variables
                        if (method_exists($templateProcessor, 'cloneRow')) {
                            // Intentar clonar la fila (esto generará variables con #1#2, #1#3, etc.)
                            $templateProcessor->cloneRow('reunio_data#1', $reunionesCount - 1);
                            Log::info("Filas clonadas: " . ($reunionesCount - 1));

                            // Ahora necesitamos corregir las variables generadas
                            for ($i = 2; $i <= $reunionesCount; $i++) {
                                // Las variables generadas tendrán el formato #1#X donde X es la posición
                                $generatedSuffix = '#1#' . ($i - 1); // PhpWord numera desde 1 las filas clonadas

                                // Reemplazamos estas variables con los datos correctos
                                $templateProcessor->setValue('reunio_data' . $generatedSuffix, $data['reunio_data#' . $i] ?? '');
                                $templateProcessor->setValue('reunio_agents' . $generatedSuffix, $data['reunio_agents#' . $i] ?? '');
                                $templateProcessor->setValue('reunio_temes' . $generatedSuffix, $data['reunio_temes#' . $i] ?? '');
                                $templateProcessor->setValue('reunio_acords' . $generatedSuffix, $data['reunio_acords#' . $i] ?? '');

                                Log::info("Corrección aplicada para fila $i con sufijo $generatedSuffix");
                            }

                            Log::info("Todas las filas procesadas correctamente");
                        } else {
                            // Si cloneRow no está disponible, informamos del problema
                            Log::warning("El método cloneRow no está disponible. No se pueden añadir filas adicionales.");
                        }
                    }
                } catch (\Exception $e) {
                    Log::warning("Error al clonar filas de la tabla: " . $e->getMessage());
                    // En caso de error, simplemente mostramos la primera fila si tenemos datos
                    $this->replaceFirstRowOnly($templateProcessor, $data);
                }
            } else {
                // Si no hay datos, simplemente limpiamos la primera fila
                $this->clearFirstRow($templateProcessor);
            }
        } catch (\Exception $e) {
            Log::error("Error general al procesar la tabla de reuniones: " . $e->getMessage());
            // En caso de error, simplemente mostramos la primera fila si tenemos datos
            $this->replaceFirstRowOnly($templateProcessor, $data);
        }
    }

    /**
     * Reemplaza solo la primera fila de la tabla con datos
     */
    private function replaceFirstRowOnly($templateProcessor, $data)
    {
        Log::info("Reemplazando solo la primera fila con datos disponibles");

        try {
            // Reemplazar solo la primera fila
            $templateProcessor->setValue('reunio_data#1', $data['reunio_data#1'] ?? '');
            $templateProcessor->setValue('reunio_agents#1', $data['reunio_agents#1'] ?? '');
            $templateProcessor->setValue('reunio_temes#1', $data['reunio_temes#1'] ?? '');
            $templateProcessor->setValue('reunio_acords#1', $data['reunio_acords#1'] ?? '');

            Log::info("Primera fila reemplazada correctamente");
        } catch (\Exception $e) {
            Log::warning("Error al reemplazar la primera fila: " . $e->getMessage());
        }
    }

    /**
     * Limpia la primera fila de la tabla
     */
    private function clearFirstRow($templateProcessor)
    {
        Log::info("Limpiando la primera fila de la tabla (no hay datos)");

        try {
            $templateProcessor->setValue('reunio_data#1', '');
            $templateProcessor->setValue('reunio_agents#1', '');
            $templateProcessor->setValue('reunio_temes#1', '');
            $templateProcessor->setValue('reunio_acords#1', '');

            Log::info("Primera fila limpiada correctamente");
        } catch (\Exception $e) {
            Log::warning("Error al limpiar la primera fila: " . $e->getMessage());
        }
    }

    /**
     * Reemplaza las variables individuales de reuniones en el documento Word
     */
    private function replaceReunionesVariables($templateProcessor, $data)
    {
        Log::info("Reemplazando variables individuales de reuniones");

        try {
            // Reemplazar variables individuales para cada fila
            for ($i = 1; $i <= 10; $i++) { // Limitamos a 10 filas por seguridad
                $templateProcessor->setValue('reunio_data#' . $i, $data['reunio_data#' . $i] ?? '');
                $templateProcessor->setValue('reunio_agents#' . $i, $data['reunio_agents#' . $i] ?? '');
                $templateProcessor->setValue('reunio_temes#' . $i, $data['reunio_temes#' . $i] ?? '');
                $templateProcessor->setValue('reunio_acords#' . $i, $data['reunio_acords#' . $i] ?? '');
            }
        } catch (\Exception $e) {
            Log::warning("Error al reemplazar variables individuales de reuniones: " . $e->getMessage());
        }
    }

    /**
     * Descargar el documento Word generado
     */
    public function download($filename)
    {
        $path = Storage::disk('public')->path('word_generated/' . $filename);

        if (!file_exists($path)) {
            abort(404, 'Archivo no encontrado');
        }

        $headers = [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        return response()->download($path, $filename, $headers);
    }
}
