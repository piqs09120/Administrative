<?php

namespace App\Http\Controllers;

use App\Models\Facility;
use Illuminate\Http\Request;

class FacilitiesController extends Controller
{
    public function __construct()
    {
        // Role restrictions removed - all users can now manage facilities
    }

    public function index()
    {
        $facilities = Facility::withCount('reservations')->latest()->get();
        return view('facilities.index', compact('facilities'));
    }

    public function create()
    {
        return view('facilities.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:available,unavailable'
        ]);

        Facility::create($request->all());
        return redirect()->route('facilities.index')->with('success', 'Facility created successfully!');
    }

    public function show($id)
    {
        $facility = Facility::with('reservations.reserver')->findOrFail($id);
        return view('facilities.show', compact('facility'));
    }

    public function showAjax($id)
    {
        $facility = Facility::with(['reservations' => function ($q) {
            $q->with('reserver')->orderByDesc('start_time')->take(5);
        }])->findOrFail($id);

        return response()->json([
            'success' => true,
            'facility' => [
                'id' => $facility->id,
                'name' => $facility->name,
                'location' => $facility->location,
                'description' => $facility->description,
                'status' => $facility->status,
                'reservations_count' => $facility->reservations->count(),
                'updated_at' => optional($facility->updated_at)->format('M d, Y H:i'),
                'recent_reservations' => $facility->reservations->map(function ($r) {
                    return [
                        'id' => $r->id,
                        'reserver' => $r->reserver->name ?? 'Unknown',
                        'start_time' => optional($r->start_time)->format('M d, H:i'),
                        'end_time' => optional($r->end_time)->format('H:i'),
                        'status' => $r->status,
                    ];
                })->values(),
            ],
        ]);
    }

    public function edit($id)
    {
        $facility = Facility::findOrFail($id);
        return view('facilities.edit', compact('facility'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:available,unavailable'
        ]);

        $facility = Facility::findOrFail($id);
        $facility->update($request->all());
        return redirect()->route('facilities.show', $id)->with('success', 'Facility updated successfully!');
    }

    public function destroy($id)
    {
        try {
            $facility = Facility::withCount('reservations')->findOrFail($id);
            
            // Check if facility is occupied
            if ($facility->status === 'occupied') {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete facility that is currently occupied.'
                ], 422);
            }
            
            // Check if facility has reservations
            if ($facility->reservations_count > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete facility that has active reservations.'
                ], 422);
            }
            
            $facility->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Facility deleted successfully!'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred. Please refresh the page and try again.'
            ], 500);
        }
    }
} 