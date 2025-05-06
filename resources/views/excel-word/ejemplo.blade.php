<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Ejemplo de Estructura Excel') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('Ejemplo de Estructura del Excel') }}</h3>

                <p class="mb-4">Para que el sistema pueda extraer correctamente la información, debes estructurar tu archivo Excel como se muestra a continuación:</p>

                <div class="mb-6">
                    <h4 class="text-md font-medium text-gray-800 mb-2">Estructura de datos en Excel</h4>
                    <p class="mb-2">Las celdas importantes y su contenido son:</p>

                    <div class="overflow-x-auto mb-6">
                        <table class="min-w-full divide-y divide-gray-200 border border-gray-300">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-r border-gray-300">
                                        Celda
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Contenido
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <!-- Datos personales -->
                                <tr>
                                    <td colspan="2" class="px-6 py-2 bg-gray-100 font-medium text-gray-900">
                                        DATOS PERSONALES
                                    </td>
                                </tr>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 border-r border-gray-300">
                                        C6
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        Nom de l'alumne/a
                                    </td>
                                </tr>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 border-r border-gray-300">
                                        E6
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        Data de naixement
                                    </td>
                                </tr>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 border-r border-gray-300">
                                        C7
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        Lloc de naixement
                                    </td>
                                </tr>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 border-r border-gray-300">
                                        E7
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        Nom dels tutors i/o tutores legals
                                    </td>
                                </tr>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 border-r border-gray-300">
                                        C8
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        Adreça
                                    </td>
                                </tr>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 border-r border-gray-300">
                                        E8
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        Telèfon
                                    </td>
                                </tr>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 border-r border-gray-300">
                                        C9
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        Llengua d'ús habitual
                                    </td>
                                </tr>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 border-r border-gray-300">
                                        E9
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        Altres llengües que coneix
                                    </td>
                                </tr>
                                <!-- Datos escolares -->
                                <tr>
                                    <td colspan="2" class="px-6 py-2 bg-gray-100 font-medium text-gray-900">
                                        DADES ESCOLARS
                                    </td>
                                </tr>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 border-r border-gray-300">
                                        C13
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        Curs
                                    </td>
                                </tr>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 border-r border-gray-300">
                                        E13
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        Grup
                                    </td>
                                </tr>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 border-r border-gray-300">
                                        C14
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        Tutor/a
                                    </td>
                                </tr>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 border-r border-gray-300">
                                        E14
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        Data d'arribada a Catalunya
                                    </td>
                                </tr>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 border-r border-gray-300">
                                        C15
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        Data d'incorporació al centre
                                    </td>
                                </tr>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 border-r border-gray-300">
                                        E15
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        Data d'incorporació al sistema educatiu català
                                    </td>
                                </tr>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 border-r border-gray-300">
                                        C16
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        Centres on ha estat matriculat anteriorment
                                    </td>
                                </tr>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 border-r border-gray-300">
                                        E16
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        Escolarització prèvia
                                    </td>
                                </tr>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 border-r border-gray-300">
                                        C17
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        Retenció de curs
                                    </td>
                                </tr>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 border-r border-gray-300">
                                        C18
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        Altres informacions d'interès
                                    </td>
                                </tr>
                                <!-- Sección de Justificación -->
                                <tr>
                                    <td colspan="2" class="px-6 py-2 bg-gray-100 font-medium text-gray-900">
                                        JUSTIFICACIÓ (MOTIVAT PER)
                                    </td>
                                </tr>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 border-r border-gray-300">
                                        I7
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        Checkbox: Informe de reconeixement de有必要支持教育支持的特殊需要
                                        <span class="ml-2 text-xs text-indigo-600">(Aparecerá como ☑ si está marcado, ☐ si no lo está)</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 border-r border-gray-300">
                                        I8
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        Checkbox: Avaluació psicopedagògica
                                        <span class="ml-2 text-xs text-indigo-600">(Aparecerá como ☑ si está marcado, ☐ si no lo está)</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 border-r border-gray-300">
                                        I9
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        Checkbox: Resultat de l'avaluació inicial de l'alumne/a nouvingut
                                        <span class="ml-2 text-xs text-indigo-600">(Aparecerá como ☑ si está marcado, ☐ si no lo está)</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 border-r border-gray-300">
                                        I10
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        Checkbox: Avaluació de l'alumne/a d'origen estranger que ja no assisteix a l'aula d'acollida
                                        <span class="ml-2 text-xs text-indigo-600">(Aparecerá como ☑ si está marcado, ☐ si no lo está)</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 border-r border-gray-300">
                                        I11
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        Checkbox: Avaluació de l'alumne/a d'origen estranger amb necessitats educatives derivades
                                        <span class="ml-2 text-xs text-indigo-600">(Aparecerá como ☑ si está marcado, ☐ si no lo está)</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 border-r border-gray-300">
                                        I12
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        Checkbox: Decisió de la comissió d'atenció educativa inclusiva (CAEI)
                                        <span class="ml-2 text-xs text-indigo-600">(Aparecerá como ☑ si está marcado, ☐ si no lo está)</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 border-r border-gray-300">
                                        J12
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        Texto: Decisió de la CAD a proposta de ... (EAP/tutor/docent/família...) motivada per ...
                                    </td>
                                </tr>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 border-r border-gray-300">
                                        I13
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        Checkbox: Altres
                                        <span class="ml-2 text-xs text-indigo-600">(Aparecerá como ☑ si está marcado, ☐ si no lo está)</span>
                                    </td>
                                </tr>
                                <!-- Nuevos campos añadidos -->
                                <tr>
                                    <td colspan="2" class="px-6 py-2 bg-gray-100 font-medium text-gray-900">
                                        DATOS ADICIONALES DEL ESTUDIANTE
                                    </td>
                                </tr>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 border-r border-gray-300">
                                        I16 - J16
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        Competencias y capacidades del alumno
                                        <span class="ml-2 text-xs text-indigo-600">(${competencies_alumne_capabilities})</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 border-r border-gray-300">
                                        J18
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        Puntos fuertes de aprendizaje
                                        <span class="ml-2 text-xs text-indigo-600">(${learning_strong_points})</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 border-r border-gray-300">
                                        J19
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        Puntos a mejorar en el aprendizaje
                                        <span class="ml-2 text-xs text-indigo-600">(${learning_improvement_points})</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 border-r border-gray-300">
                                        J22
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        Intereses del estudiante
                                        <span class="ml-2 text-xs text-indigo-600">(${student_interests})</span>
                                    </td>
                                </tr>
                                <!-- Nueva sección para brief_justification -->
                                <tr>
                                    <td colspan="2" class="px-6 py-2 bg-gray-100 font-medium text-gray-900">
                                        JUSTIFICACIÓN BREVE (HOJA "PORTADA - CONCRECIÓ DEL PI")
                                    </td>
                                </tr>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 border-r border-gray-300">
                                        C15:F15
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        Justificación breve del plan
                                        <span class="ml-2 text-xs text-indigo-600">(${brief_justification})</span>
                                    </td>
                                </tr>
                                <!-- Nueva sección para objetivos y criterios -->
                                <tr>
                                    <td colspan="2" class="px-6 py-2 bg-gray-100 font-medium text-gray-900">
                                        OBJETIVOS Y CRITERIOS (HOJA "CE_ÀREES/MATÈRIES")
                                    </td>
                                </tr>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 border-r border-gray-300">
                                        E4:E25
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        Objetivos
                                        <span class="ml-2 text-xs text-indigo-600">(${objetivo#1} hasta ${objetivo#21})</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 border-r border-gray-300">
                                        F4:F25
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        Criterios de evaluación
                                        <span class="ml-2 text-xs text-indigo-600">(${criterio#1} hasta ${criterio#21})</span>
                                    </td>
                                </tr>
                                <!-- Nueva sección para objetivos y criterios transversales -->
                                <tr>
                                    <td colspan="2" class="px-6 py-2 bg-gray-100 font-medium text-gray-900">
                                        OBJETIVOS Y CRITERIOS TRANSVERSALES (HOJA "CE_TRANSVERSALS")
                                    </td>
                                </tr>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 border-r border-gray-300">
                                        E4:E25
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        Objetivos transversales
                                        <span class="ml-2 text-xs text-indigo-600">(${transversal_objective#1} hasta ${transversal_objective#21})</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 border-r border-gray-300">
                                        F4:F25
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        Criterios de evaluación transversales
                                        <span class="ml-2 text-xs text-indigo-600">(${transversal_criteria#1} hasta ${transversal_criteria#21})</span>
                                    </td>
                                </tr>
                                <!-- Nueva sección para la tabla Sabers -->
                                <tr>
                                    <td colspan="2" class="px-6 py-2 bg-gray-100 font-medium text-gray-900">
                                        TABLA SABERS
                                    </td>
                                </tr>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 border-r border-gray-300">
                                        B4:B35
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        Área/Materia
                                        <span class="ml-2 text-xs text-indigo-600">(${area_materia#1} hasta ${area_materia#18})</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 border-r border-gray-300">
                                        C4:C35
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        Bloque de saberes
                                        <span class="ml-2 text-xs text-indigo-600">(${bloc_sabers#1} hasta ${bloc_sabers#18})</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 border-r border-gray-300">
                                        D4:D35
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        Saber
                                        <span class="ml-2 text-xs text-indigo-600">(${saber#1} hasta ${saber#18})</span>
                                    </td>
                                </tr>
                                <!-- Nueva sección para tabla de reuniones de seguimiento -->
                                <tr>
                                    <td colspan="2" class="px-6 py-2 bg-gray-100 font-medium text-gray-900">
                                        TABLA DE REUNIONES DE SEGUIMIENTO (HOJA "DESENVOLUPAMENT")
                                    </td>
                                </tr>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 border-r border-gray-300">
                                        B5:E[n]
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        Tabla dinámica de reuniones (se adapta al número de filas)
                                        <span class="ml-2 text-xs text-indigo-600">(${reunio_data#1}, ${reunio_agents#1}, ${reunio_temes#1}, ${reunio_acords#1}, etc.)</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 border-r border-gray-300">
                                        B5:B[n]
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        Fecha de la reunión (Data)
                                    </td>
                                </tr>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 border-r border-gray-300">
                                        C5:C[n]
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        Participantes (Agents participants)
                                    </td>
                                </tr>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 border-r border-gray-300">
                                        D5:D[n]
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        Temas tratados (Temes tractats)
                                    </td>
                                </tr>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 border-r border-gray-300">
                                        E5:E[n]
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        Acuerdos (Acords)
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="bg-blue-50 p-4 rounded-md mb-6">
                    <h4 class="text-sm font-medium text-blue-700 mb-2">{{ __('Nota') }}</h4>
                    <p class="text-sm text-blue-600">
                        {{ __('Para obtener mejores resultados, asegúrate de que tu archivo Excel contenga los datos en las celdas exactamente como se muestra arriba. El sistema extraerá la información de estas celdas específicas.') }}
                    </p>
                </div>

                <div class="bg-indigo-50 p-4 rounded-md mb-6">
                    <h4 class="text-sm font-medium text-indigo-700 mb-2">{{ __('Guía para tablas dinámicas en Word') }}</h4>
                    <p class="text-sm text-indigo-600 mb-2">
                        {{ __('Para que la tabla de reuniones de seguimiento funcione correctamente, debes preparar tu plantilla Word de esta manera:') }}
                    </p>
                    <ul class="list-disc list-inside text-sm text-indigo-600 space-y-1 ml-4">
                        <li>{{ __('En la tabla de "REUNIONS DE SEGUIMENT I ACORDS AMB L\'ALUMNE/A, EL PARE, LA MARE O EL TUTOR O TUTORA LEGAL"') }}</li>
                        <li>{{ __('Debes colocar los marcadores exactamente así:') }}</li>
                    </ul>
                    <div class="bg-indigo-100 p-3 mt-2 rounded text-xs overflow-x-auto">
                        <table class="border-collapse border border-indigo-300 w-full">
                            <tr class="bg-indigo-200">
                                <th class="border border-indigo-300 p-1">Data</th>
                                <th class="border border-indigo-300 p-1">Agents participants</th>
                                <th class="border border-indigo-300 p-1">Temes tractats</th>
                                <th class="border border-indigo-300 p-1">Acords</th>
                            </tr>
                            <tr>
                                <td class="border border-indigo-300 p-1">${reunio_data#1}</td>
                                <td class="border border-indigo-300 p-1">${reunio_agents#1}</td>
                                <td class="border border-indigo-300 p-1">${reunio_temes#1}</td>
                                <td class="border border-indigo-300 p-1">${reunio_acords#1}</td>
                            </tr>
                        </table>
                    </div>
                    <p class="text-sm text-indigo-600 mt-3">
                        {{ __('IMPORTANTE: Los marcadores deben ser exactamente como se muestra, incluyendo el símbolo # y el número 1.') }}
                    </p>
                    <p class="text-sm text-indigo-600 mt-2">
                        {{ __('No necesitas crear más filas. El sistema duplicará automáticamente esta fila según las entradas que encuentre en el Excel.') }}
                    </p>
                </div>

                <div class="flex justify-end">
                    <a href="{{ route('excel-word.index') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 focus:outline-none focus:border-indigo-700 focus:ring focus:ring-indigo-200 active:bg-indigo-600 transition">
                        {{ __('Volver') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
