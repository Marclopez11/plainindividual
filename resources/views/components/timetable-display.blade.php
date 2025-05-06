<div class="w-full mb-4">
    @if($timetable)
        <h4 class="text-md font-semibold text-gray-700 mb-2">{{ $timetable->name }}</h4>

        <!-- Legend -->
        <div class="flex flex-wrap gap-3 mb-3">
            <div class="flex items-center">
                <div class="w-4 h-4 bg-purple-200 rounded-sm mr-1"></div>
                <span class="text-xs">{{ __('Codocència') }}</span>
            </div>
            <div class="flex items-center">
                <div class="w-4 h-4 bg-yellow-200 rounded-sm mr-1"></div>
                <span class="text-xs">{{ __('Desdoblament') }}</span>
            </div>
            <div class="flex items-center">
                <div class="w-4 h-4 bg-orange-200 rounded-sm mr-1"></div>
                <span class="text-xs">{{ __('Desdoblament + Codocència') }}</span>
            </div>
            <div class="flex items-center">
                <div class="w-4 h-4 bg-blue-100 rounded-sm mr-1"></div>
                <span class="text-xs">{{ __('Pati/Descans') }}</span>
            </div>
        </div>

        <!-- Timetable Display -->
        <div class="overflow-x-auto">
            <table class="w-full border-collapse border border-gray-300">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="border border-gray-300 p-2 w-24"></th>
                        <th class="border border-gray-300 p-2 font-bold">{{ __('DILLUNS') }}</th>
                        <th class="border border-gray-300 p-2 font-bold">{{ __('DIMARTS') }}</th>
                        <th class="border border-gray-300 p-2 font-bold">{{ __('DIMECRES') }}</th>
                        <th class="border border-gray-300 p-2 font-bold">{{ __('DIJOUS') }}</th>
                        <th class="border border-gray-300 p-2 font-bold">{{ __('DIVENDRES') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @php
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

                        // Sort time slots by start time
                        uksort($timeSlots, function($a, $b) use ($timeSlots) {
                            return strtotime($timeSlots[$a]['start']) - strtotime($timeSlots[$b]['start']);
                        });

                        $days = ['DILLUNS', 'DIMARTS', 'DIMECRES', 'DIJOUS', 'DIVENDRES'];
                    @endphp

                    @foreach($timeSlots as $timeKey => $timeData)
                        <tr>
                            <td class="border border-gray-300 p-2 text-center text-sm">
                                <div>{{ $timeData['start'] }}</div>
                                <div>{{ $timeData['end'] }}</div>
                            </td>

                            @foreach($days as $day)
                                @php
                                    $cellClass = 'bg-white';
                                    $slot = $timeData['slots'][$day] ?? null;

                                    if ($slot) {
                                        switch ($slot->type) {
                                            case 'codocencia':
                                                $cellClass = 'bg-purple-200';
                                                break;
                                            case 'desdoblament':
                                                $cellClass = 'bg-yellow-200';
                                                break;
                                            case 'desdoblament_codocencia':
                                                $cellClass = 'bg-orange-200';
                                                break;
                                            case 'pati':
                                                $cellClass = 'bg-blue-100';
                                                break;
                                        }
                                    }
                                @endphp

                                <td class="border border-gray-300 p-2 {{ $cellClass }} align-top">
                                    @if($slot)
                                        <div class="font-medium text-sm">{{ $slot->subject }}</div>
                                        @if($slot->notes)
                                            <div class="text-xs text-gray-500 mt-1">{{ $slot->notes }}</div>
                                        @endif
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="bg-yellow-50 p-4 rounded-md border border-yellow-200 text-yellow-700">
            {{ __('No hi ha cap horari definit.') }}
        </div>
    @endif
</div>
