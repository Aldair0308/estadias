<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CalendarController extends Controller
{
    public function index()
    {
        return view('calendar.index');
    }

    public function getEvents()
    {
        // Return calendar events data
        return response()->json([]);
    }

    public function storeEvent(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'start' => 'required|date',
            'end' => 'nullable|date'
        ]);

        $event = \App\Models\CalendarEvent::create([
            'user_id' => auth()->id(),
            'title' => $validated['title'],
            'start_date' => $validated['start'],
            'end_date' => $validated['end'] ?? null
        ]);

        return response()->json(['success' => true, 'event' => $event]);
    }










    public function updateEvent(Request $request, $id)
    {
        $event = \App\Models\CalendarEvent::findOrFail($id);
        
        // Verificar permisos
        if ($event->user_id !== auth()->id()) {
            return response()->json(['success' => false, 'message' => 'No autorizado'], 403);
        }
        
        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'start' => 'sometimes|date',
            'end' => 'nullable|date',
            'color' => 'nullable|string',
            'description' => 'nullable|string',
            'allDay' => 'boolean'
        ]);
        
        $event->update([
            'title' => $validated['title'] ?? $event->title,
            'start_date' => $validated['start'] ?? $event->start_date,
            'end_date' => $validated['end'] ?? $event->end_date,
            'color' => $validated['color'] ?? $event->color,
            'description' => $validated['description'] ?? $event->description,
            'all_day' => $validated['allDay'] ?? $event->all_day
        ]);
        
        return response()->json(['success' => true]);
    }


}