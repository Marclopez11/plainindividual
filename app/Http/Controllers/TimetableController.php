<?php

namespace App\Http\Controllers;

use App\Models\SupportPlan;
use App\Models\Timetable;
use App\Models\TimetableSlot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TimetableController extends Controller
{
    /**
     * Store a newly created timetable.
     */
    public function store(Request $request, SupportPlan $supportPlan)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'configuration' => 'nullable|array',
        ]);

        $timetable = $supportPlan->timetables()->create($validated);

        // Process slot data if it exists
        if ($request->has('slots') && is_array($request->slots)) {
            foreach ($request->slots as $slot) {
                $timetable->slots()->create([
                    'day' => $slot['day'],
                    'time_start' => $slot['time_start'],
                    'time_end' => $slot['time_end'],
                    'subject' => $slot['subject'],
                    'type' => $slot['type'] ?? null,
                    'notes' => $slot['notes'] ?? null,
                ]);
            }
        }

        return response()->json([
            'timetable' => $timetable->load('slots'),
            'message' => 'Timetable created successfully'
        ]);
    }

    /**
     * Update the specified timetable.
     */
    public function update(Request $request, Timetable $timetable)
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'configuration' => 'nullable|array',
        ]);

        $timetable->update($validated);

        // Handle slot updates if needed
        if ($request->has('slots') && is_array($request->slots)) {
            // Remove existing slots
            $timetable->slots()->delete();

            // Create new slots
            foreach ($request->slots as $slot) {
                $timetable->slots()->create([
                    'day' => $slot['day'],
                    'time_start' => $slot['time_start'],
                    'time_end' => $slot['time_end'],
                    'subject' => $slot['subject'],
                    'type' => $slot['type'] ?? null,
                    'notes' => $slot['notes'] ?? null,
                ]);
            }
        }

        return response()->json([
            'timetable' => $timetable->load('slots'),
            'message' => 'Timetable updated successfully'
        ]);
    }

    /**
     * Remove the specified timetable.
     */
    public function destroy(Timetable $timetable)
    {
        $timetable->slots()->delete();
        $timetable->delete();

        return response()->json([
            'message' => 'Timetable deleted successfully'
        ]);
    }

    /**
     * Get a specific timetable with its slots.
     */
    public function show(Timetable $timetable)
    {
        return response()->json([
            'timetable' => $timetable->load('slots'),
        ]);
    }

    /**
     * List all timetables for a support plan.
     */
    public function index(SupportPlan $supportPlan)
    {
        return response()->json([
            'timetables' => $supportPlan->timetables()->with('slots')->get(),
        ]);
    }
}
