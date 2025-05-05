<div class="w-full border-collapse mb-8">
    <div x-data="timetableEditor()" x-init="initialize()" class="w-full">
        <div class="border border-gray-300 rounded-md p-4 bg-white shadow-sm">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">{{ __('Horari Escolar') }}</h3>

            <!-- Timetable Controls -->
            <div class="flex flex-wrap gap-4 mb-4">
                <div class="flex-grow">
                    <label for="timetable_name" class="block text-sm font-medium text-gray-700 mb-1">{{ __('Nom de l\'horari') }}</label>
                    <input type="text" name="timetable_name" id="timetable_name" x-model="timetableName"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                </div>

                <div class="flex items-end space-x-2">
                    <button type="button" @click="addTimeSlot()"
                        class="px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition">
                        {{ __('+ Afegir franja') }}
                    </button>

                    <button type="button" @click="confirmResetTimetable()"
                        class="px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition">
                        {{ __('Reiniciar') }}
                    </button>

                    <div class="relative" x-data="{ open: false }">
                        <button type="button" @click="open = !open"
                            class="px-4 py-2 bg-blue-100 border border-transparent rounded-md font-semibold text-xs text-blue-800 uppercase tracking-widest hover:bg-blue-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition">
                            {{ __('Plantilles') }}
                        </button>
                        <div x-show="open" @click.away="open = false"
                            class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg z-10 border border-gray-200">
                            <div class="py-1">
                                <button type="button" @click="loadTemplate('primary'); open = false"
                                    class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    {{ __('Primària (9:00-17:00)') }}
                                </button>
                                <button type="button" @click="loadTemplate('secondary'); open = false"
                                    class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    {{ __('Secundària (8:00-14:30)') }}
                                </button>
                                <button type="button" @click="loadTemplate('custom'); open = false"
                                    class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    {{ __('Personalitzat (buit)') }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Color legend -->
            <div class="flex flex-wrap gap-3 mb-4">
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

            <!-- Timetable -->
            <div class="overflow-x-auto">
                <table class="w-full border-collapse border border-gray-300">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="border border-gray-300 p-2 w-24"></th>
                            <template x-for="day in days" :key="day">
                                <th class="border border-gray-300 p-2 font-bold" x-text="day"></th>
                            </template>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="(timeSlot, timeIndex) in timeSlots" :key="timeIndex">
                            <tr>
                                <td class="border border-gray-300 p-2 text-center">
                                    <div class="flex flex-col">
                                        <input type="text" x-model="timeSlot.start"
                                            class="w-full text-center text-sm px-1 py-1 border border-gray-300 rounded-md mb-1"
                                            placeholder="Inici">
                                        <input type="text" x-model="timeSlot.end"
                                            class="w-full text-center text-sm px-1 py-1 border border-gray-300 rounded-md"
                                            placeholder="Fi">
                                    </div>
                                    <div class="mt-1">
                                        <button type="button" @click.stop.prevent="removeTimeSlot(timeIndex)"
                                            class="text-xs text-red-600 hover:text-red-800">
                                            {{ __('Eliminar') }}
                                        </button>
                                    </div>
                                </td>
                                <template x-for="day in days" :key="day">
                                    <td class="border border-gray-300 p-0 align-top">
                                        <div x-data="{
                                                editing: false,
                                                subject: getSlot(day, timeIndex)?.subject || '',
                                                type: getSlot(day, timeIndex)?.type || 'regular',
                                                notes: getSlot(day, timeIndex)?.notes || ''
                                            }"
                                            x-init="
                                                $watch('editing', value => {
                                                    if (value) {
                                                        subject = getSlot(day, timeIndex)?.subject || '';
                                                        type = getSlot(day, timeIndex)?.type || 'regular';
                                                        notes = getSlot(day, timeIndex)?.notes || '';
                                                    }
                                                })
                                            "
                                            :class="getCellClass(getSlot(day, timeIndex))"
                                            class="h-full min-h-[80px] p-2 text-sm transition-colors duration-200 cursor-pointer hover:bg-gray-50"
                                            @click="editing = true">

                                            <!-- Display subject when not editing -->
                                            <div x-show="!editing" class="h-full flex flex-col justify-between">
                                                <div x-text="getSlot(day, timeIndex)?.subject || ''" class="font-medium"></div>
                                                <div x-show="getSlot(day, timeIndex)?.notes" class="text-xs text-gray-500 mt-1"
                                                    x-text="getSlot(day, timeIndex)?.notes"></div>
                                            </div>

                                            <!-- Edit form -->
                                            <div x-show="editing" class="h-full" @click.stop @click.away="saveSlot(day, timeIndex, subject, type, notes); editing = false">
                                                <div class="flex flex-col h-full">
                                                    <input type="text" x-model="subject" placeholder="Assignatura"
                                                        class="w-full px-2 py-1 text-sm border border-gray-300 rounded-md mb-1"
                                                        @click.stop>

                                                    <select x-model="type"
                                                        class="w-full px-2 py-1 text-xs border border-gray-300 rounded-md mb-1"
                                                        @click.stop>
                                                        <option value="regular">Regular</option>
                                                        <option value="codocencia">Codocència</option>
                                                        <option value="desdoblament">Desdoblament</option>
                                                        <option value="desdoblament_codocencia">Desdoblament + Codocència</option>
                                                        <option value="pati">Pati/Descans</option>
                                                    </select>

                                                    <textarea x-model="notes" placeholder="Notes"
                                                        class="w-full px-2 py-1 text-xs border border-gray-300 rounded-md mb-1 flex-grow"
                                                        @click.stop></textarea>

                                                    <div class="flex space-x-1 mt-auto">
                                                        <button type="button"
                                                            @click.stop="saveSlot(day, timeIndex, subject, type, notes); editing = false"
                                                            class="flex-1 px-2 py-1 text-xs bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                                                            {{ __('Desar') }}
                                                        </button>
                                                        <button type="button"
                                                            @click.stop="editing = false"
                                                            class="flex-1 px-2 py-1 text-xs bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300">
                                                            {{ __('Cancel·lar') }}
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </template>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

            <!-- JSON output field (hidden) -->
            <input type="hidden" name="timetable_data" x-model="JSON.stringify(buildTimetableData())">
        </div>
    </div>

    <!-- Modals -->
    <div x-data="{
            showModal: false,
            modalType: null,
            timeIndexToDelete: null,
            modalTitle: '',
            modalMessage: ''
        }"
        x-show="showModal"
        @show-confirmation-modal.window="
            showModal = true;
            modalType = $event.detail.type;
            timeIndexToDelete = $event.detail.timeIndex;
            modalTitle = $event.detail.title;
            modalMessage = $event.detail.message;
        "
        @keydown.escape.window="showModal = false"
        x-cloak
        class="fixed inset-0 z-50 overflow-y-auto">

        <!-- Modal Backdrop -->
        <div class="fixed inset-0 bg-black opacity-30" @click="showModal = false"></div>

        <!-- Modal Content -->
        <div class="flex items-center justify-center min-h-screen">
            <div class="bg-white rounded-lg shadow-xl p-6 max-w-md mx-auto z-10 relative">
                <div class="mb-4">
                    <h3 class="text-lg font-medium text-gray-900" x-text="modalTitle"></h3>
                    <p class="mt-2 text-sm text-gray-500" x-text="modalMessage"></p>
                </div>
                <div class="flex justify-end space-x-3">
                    <button @click="showModal = false" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300">
                        {{ __('Cancel·lar') }}
                    </button>
                    <button
                        @click="
                            if (modalType === 'delete-timeslot') {
                                const indexToDelete = timeIndexToDelete;
                                $dispatch('confirm-delete-timeslot', {timeIndex: indexToDelete});
                            }
                            else if (modalType === 'reset-timetable') {
                                $dispatch('confirm-reset-timetable');
                            }
                            showModal = false;
                        "
                        class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                        {{ __('Eliminar') }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    <style>
        [x-cloak] { display: none !important; }
    </style>

    <script>
        function timetableEditor() {
            return {
                days: ['DILLUNS', 'DIMARTS', 'DIMECRES', 'DIJOUS', 'DIVENDRES'],
                timeSlots: [],
                slots: [],
                timetableName: 'Horari Escolar',

                initialize() {
                    const existingData = document.querySelector('input[name="timetable_data"]')?.value;
                    this.timetableName = document.querySelector('input[name="timetable_name"]')?.value || 'Horari Escolar';

                    if (existingData && existingData !== '' && existingData !== 'null') {
                        try {
                            const data = JSON.parse(existingData);
                            this.loadExistingData(data);
                        } catch (e) {
                            console.error('Error loading timetable data:', e);
                            this.loadTemplate('custom'); // Load empty template as fallback
                        }
                    } else {
                        this.loadTemplate('custom'); // Load empty template for new timetables
                    }

                    // Event listeners for confirmation actions
                    this.$el.addEventListener('confirm-delete-timeslot', (e) => {
                        const timeIndex = e.detail.timeIndex;
                        this.removeTimeSlot(timeIndex);
                    });

                    this.$el.addEventListener('confirm-reset-timetable', () => {
                        this.doResetTimetable();
                    });
                },

                loadExistingData(data) {
                    this.timetableName = data.name || this.timetableName;
                    this.timeSlots = data.timeSlots || [];
                    this.slots = data.slots || [];

                    // If no time slots were loaded, create at least one
                    if (this.timeSlots.length === 0) {
                        this.addTimeSlot();
                    }
                },

                loadTemplate(template) {
                    this.timeSlots = [];
                    this.slots = [];

                    if (template === 'primary') {
                        // Primary school template (9:00-17:00)
                        this.timeSlots = [
                            { start: '9:00', end: '9:30' },
                            { start: '9:30', end: '11:00' },
                            { start: '11:00', end: '11:30' },
                            { start: '11:30', end: '13:00' },
                            { start: '13:00', end: '15:00' },
                            { start: '15:00', end: '15:30' },
                            { start: '15:30', end: '17:00' }
                        ];

                        // Add some default slots
                        this.saveSlot('DILLUNS', 0, 'Assemblea', 'regular');
                        this.saveSlot('DIMARTS', 0, 'Assemblea', 'regular');
                        this.saveSlot('DIMECRES', 0, 'Assemblea', 'regular');
                        this.saveSlot('DIJOUS', 0, 'Assemblea', 'regular');
                        this.saveSlot('DIVENDRES', 0, 'Assemblea', 'regular');

                        this.saveSlot('DILLUNS', 1, 'Projecte', 'codocencia');
                        this.saveSlot('DIMARTS', 1, 'Català', 'regular');
                        this.saveSlot('DIMECRES', 1, 'Matemàtiques', 'desdoblament');
                        this.saveSlot('DIJOUS', 1, 'Educació física', 'regular');
                        this.saveSlot('DIVENDRES', 1, 'Música', 'regular');

                        this.saveSlot('DILLUNS', 2, 'Pati', 'pati');
                        this.saveSlot('DIMARTS', 2, 'Pati', 'pati');
                        this.saveSlot('DIMECRES', 2, 'Pati', 'pati');
                        this.saveSlot('DIJOUS', 2, 'Pati', 'pati');
                        this.saveSlot('DIVENDRES', 2, 'Pati', 'pati');

                    } else if (template === 'secondary') {
                        // Secondary school template (8:00-14:30)
                        this.timeSlots = [
                            { start: '8:00', end: '9:00' },
                            { start: '9:00', end: '10:00' },
                            { start: '10:00', end: '11:00' },
                            { start: '11:00', end: '11:30' },
                            { start: '11:30', end: '12:30' },
                            { start: '12:30', end: '13:30' },
                            { start: '13:30', end: '14:30' }
                        ];

                        // Add default slot for recess
                        this.saveSlot('DILLUNS', 3, 'Pati', 'pati');
                        this.saveSlot('DIMARTS', 3, 'Pati', 'pati');
                        this.saveSlot('DIMECRES', 3, 'Pati', 'pati');
                        this.saveSlot('DIJOUS', 3, 'Pati', 'pati');
                        this.saveSlot('DIVENDRES', 3, 'Pati', 'pati');

                    } else {
                        // Custom/empty template
                        this.addTimeSlot(); // Add one empty time slot
                    }
                },

                addTimeSlot() {
                    this.timeSlots.push({ start: '', end: '' });
                },

                confirmDeleteTimeSlot(timeIndex) {
                    this.$dispatch('show-confirmation-modal', {
                        type: 'delete-timeslot',
                        timeIndex: timeIndex,
                        title: 'Eliminar franja horària',
                        message: 'Està segur que vol eliminar aquesta franja horària? S\'eliminaran totes les assignatures associades a aquesta franja.'
                    });
                },

                confirmResetTimetable() {
                    this.$dispatch('show-confirmation-modal', {
                        type: 'reset-timetable',
                        title: 'Reiniciar l\'horari',
                        message: 'Està segur que vol reiniciar l\'horari? Es perdran totes les dades.'
                    });
                },

                removeTimeSlot(index) {
                    console.log('Removing time slot at index:', index);
                    // Store the current slots that need to be kept
                    const remainingSlots = this.slots.filter(slot => slot.timeIndex !== index);

                    // Update timeIndex for remaining slots
                    remainingSlots.forEach(slot => {
                        if (slot.timeIndex > index) {
                            slot.timeIndex--;
                        }
                    });

                    // Remove the time slot
                    this.timeSlots.splice(index, 1);

                    // Update the slots array with the modified data
                    this.slots = remainingSlots;

                    // Ensure there's at least one time slot
                    if (this.timeSlots.length === 0) {
                        this.addTimeSlot();
                    }
                },

                doResetTimetable() {
                    this.timeSlots = [];
                    this.slots = [];
                    this.addTimeSlot();
                },

                getSlot(day, timeIndex) {
                    return this.slots.find(slot => slot.day === day && slot.timeIndex === timeIndex);
                },

                saveSlot(day, timeIndex, subject, type, notes = '') {
                    const existingSlotIndex = this.slots.findIndex(slot => slot.day === day && slot.timeIndex === timeIndex);

                    if (existingSlotIndex !== -1) {
                        // Update existing slot
                        if (subject.trim() === '') {
                            // Remove slot if subject is empty
                            this.slots.splice(existingSlotIndex, 1);
                        } else {
                            this.slots[existingSlotIndex].subject = subject;
                            this.slots[existingSlotIndex].type = type;
                            this.slots[existingSlotIndex].notes = notes;
                        }
                    } else if (subject.trim() !== '') {
                        // Create new slot
                        this.slots.push({
                            day,
                            timeIndex,
                            subject,
                            type,
                            notes
                        });
                    }
                },

                getCellClass(slot) {
                    if (!slot) return 'bg-white';

                    switch (slot.type) {
                        case 'codocencia': return 'bg-purple-200';
                        case 'desdoblament': return 'bg-yellow-200';
                        case 'desdoblament_codocencia': return 'bg-orange-200';
                        case 'pati': return 'bg-blue-100';
                        default: return 'bg-white';
                    }
                },

                buildTimetableData() {
                    // Convert the timetable data to a format suitable for saving
                    const formattedSlots = this.slots.map(slot => {
                        const timeSlot = this.timeSlots[slot.timeIndex];
                        return {
                            day: slot.day,
                            time_start: timeSlot.start,
                            time_end: timeSlot.end,
                            subject: slot.subject,
                            type: slot.type,
                            notes: slot.notes
                        };
                    });

                    return {
                        name: this.timetableName,
                        timeSlots: this.timeSlots,
                        slots: this.slots,
                        formattedSlots // This will be used for saving to the database
                    };
                }
            };
        }
    </script>
</div>
