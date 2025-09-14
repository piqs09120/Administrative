<?php

namespace App\Http\Controllers;

use App\Models\Facility;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FacilitiesController extends Controller
{
    public function __construct()
    {
        // Role restrictions removed - all users can now manage facilities
    }

    public function index(Request $request)
    {
        $query = Facility::withCount('reservations');
        
        // Apply filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        if ($request->filled('facility_type')) {
            $query->where('facility_type', $request->facility_type);
        }
        
        if ($request->filled('location')) {
            $query->where('location', $request->location);
        }
        
        if ($request->filled('min_capacity')) {
            $query->where('capacity', '>=', $request->min_capacity);
        }
        
        if ($request->filled('amenities')) {
            $amenities = is_array($request->amenities) ? $request->amenities : [$request->amenities];
            foreach ($amenities as $amenity) {
                $query->where('amenities', 'like', "%{$amenity}%");
            }
        }
        
        $facilities = $query->latest()->get();
        
        // Return JSON for AJAX requests
        if ($request->ajax()) {
            return response()->json([
                'facilities' => $facilities->map(function($facility) {
                    return [
                        'id' => $facility->id,
                        'name' => $facility->name,
                        'location' => $facility->location,
                        'description' => $facility->description,
                        'status' => $facility->status,
                        'capacity' => $facility->capacity,
                        'amenities' => $facility->amenities,
                        'rating' => $facility->rating,
                        'facility_type' => $facility->facility_type,
                        'reservations_count' => $facility->reservations_count,
                        'hourly_rate' => $facility->hourly_rate,
                        'operating_hours' => [
                            'start' => $facility->operating_hours_start,
                            'end' => $facility->operating_hours_end
                        ]
                    ];
                })
            ]);
        }
        
        // Get active tab from request parameter
        $validTabs = ['directory', 'monitoring', 'equipment'];
        $tabParam = $request->get('tab');
        $activeTab = in_array($tabParam, $validTabs) ? $tabParam : 'directory';
        
        return view('facilities.index', compact('facilities', 'activeTab'));
    }

    public function calendar(Request $request)
    {
        $facilityId = $request->get('facility_id');
        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->endOfMonth()->format('Y-m-d'));
        
        $query = Facility::with(['reservations' => function($q) use ($startDate, $endDate) {
            $q->whereBetween('start_time', [$startDate, $endDate])
              ->whereIn('status', ['approved', 'pending']);
        }]);
        
        if ($facilityId) {
            $query->where('id', $facilityId);
        }
        
        $facilities = $query->get();
        
        $calendarData = [];
        foreach ($facilities as $facility) {
            $calendarData[$facility->id] = [
                'name' => $facility->name,
                'reservations' => $facility->reservations->map(function($reservation) {
                    return [
                        'id' => $reservation->id,
                        'start_time' => $reservation->start_time->format('Y-m-d H:i:s'),
                        'end_time' => $reservation->end_time->format('Y-m-d H:i:s'),
                        'purpose' => $reservation->purpose,
                        'status' => $reservation->status,
                        'reserver' => $reservation->reserver->name ?? 'Unknown'
                    ];
                })
            ];
        }
        
        return response()->json($calendarData);
    }

    public function checkAvailability(Request $request)
    {
        $request->validate([
            'facility_id' => 'required|exists:facilities,id',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time'
        ]);
        
        $facility = Facility::findOrFail($request->facility_id);
        
        // Check for conflicting reservations
        $conflicts = \App\Models\FacilityReservation::where('facility_id', $request->facility_id)
            ->whereIn('status', ['approved', 'pending'])
            ->where(function($query) use ($request) {
                $query->where(function($q) use ($request) {
                    $q->where('start_time', '<=', $request->start_time)
                      ->where('end_time', '>', $request->start_time);
                })->orWhere(function($q) use ($request) {
                    $q->where('start_time', '<', $request->end_time)
                      ->where('end_time', '>=', $request->end_time);
                })->orWhere(function($q) use ($request) {
                    $q->where('start_time', '>=', $request->start_time)
                      ->where('end_time', '<=', $request->end_time);
                });
            })
            ->with('reserver')
            ->get();
        
        return response()->json([
            'available' => $conflicts->isEmpty(),
            'conflicts' => $conflicts->map(function($conflict) {
                return [
                    'id' => $conflict->id,
                    'start_time' => $conflict->start_time->format('Y-m-d H:i:s'),
                    'end_time' => $conflict->end_time->format('Y-m-d H:i:s'),
                    'purpose' => $conflict->purpose,
                    'reserver' => $conflict->reserver->name ?? 'Unknown'
                ];
            })
        ]);
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
            'status' => 'required|in:available,unavailable,occupied',
            'capacity' => 'nullable|integer|min:1',
            'amenities' => 'nullable|string',
            'facility_type' => 'nullable|string|max:100',
            'hourly_rate' => 'nullable|numeric|min:0',
            'operating_hours_start' => 'nullable|date_format:H:i',
            'operating_hours_end' => 'nullable|date_format:H:i|after:operating_hours_start',
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpg,jpeg,png,webp|max:4096'
        ]);

        $data = $request->only([
            'name', 'location', 'description', 'status', 'capacity', 
            'amenities', 'facility_type', 'hourly_rate', 
            'operating_hours_start', 'operating_hours_end'
        ]);

        // Handle image uploads
        if ($request->hasFile('images')) {
            $imagePaths = [];
            foreach ($request->file('images') as $image) {
                $path = $image->store('facilities/images', 'public');
                $imagePaths[] = $path;
            }
            $data['images'] = $imagePaths;
        }

        $facility = Facility::create($data);
        
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
            'status' => 'required|in:available,unavailable,occupied',
            'cover_image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
            'remove_image' => 'nullable|boolean'
        ]);

        $facility = Facility::findOrFail($id);
        $facility->update($request->only(['name','location','description','status']));

        // Remove existing image if requested
        if ($request->boolean('remove_image')) {
            foreach (['jpg','jpeg','png','webp'] as $ext) {
                $path = "facilities/{$id}/cover.$ext";
                if (Storage::disk('public')->exists($path)) {
                    Storage::disk('public')->delete($path);
                }
            }
        }

        // Upload new cover image if provided
        if ($request->hasFile('cover_image')) {
            $file = $request->file('cover_image');
            $ext = strtolower($file->getClientOriginalExtension() ?: 'jpg');
            if (!in_array($ext, ['jpg','jpeg','png','webp'])) { $ext = 'jpg'; }

            $dir = "facilities/{$id}";
            Storage::disk('public')->makeDirectory($dir);

            // Clean previous cover files
            foreach (['jpg','jpeg','png','webp'] as $e) {
                $p = "$dir/cover.$e";
                if (Storage::disk('public')->exists($p)) { Storage::disk('public')->delete($p); }
            }

            $file->storeAs($dir, "cover.$ext", 'public');
        }
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

            // Re-check using ACTIVE reservations only (pending/approved)
            // Consider only upcoming/ongoing reservations as blocking
            $activeReservations = \App\Models\FacilityReservation::where('facility_id', $facility->id)
                ->whereIn('status', ['pending', 'approved'])
                ->where(function($q){
                    $q->whereNull('end_time')
                      ->orWhere('end_time', '>', now());
                })
                ->count();
            if ($activeReservations > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete facility that has active reservations.'
                ], 422);
            }

            // Remove any stored cover images (new and legacy paths)
            foreach (['jpg','jpeg','png','webp'] as $ext) {
                $storageRel = "facilities/{$id}/cover.$ext";
                if (Storage::disk('public')->exists($storageRel)) {
                    Storage::disk('public')->delete($storageRel);
                }
                $legacyRel = public_path("facilities/{$id}/cover.$ext");
                if (file_exists($legacyRel)) {
                    @unlink($legacyRel);
                }
            }

            // Attempt to remove the (now possibly empty) directory on storage disk
            $dir = "facilities/{$id}";
            if (Storage::disk('public')->exists($dir)) {
                try { Storage::disk('public')->deleteDirectory($dir); } catch (\Throwable $t) {}
            }

            $facility->delete();

            return response()->json([
                'success' => true,
                'message' => 'Facility deleted successfully!'
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Facility not found.'
            ], 404);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'An unexpected error occurred. Please refresh the page and try again.'
            ], 500);
        }
    }
} 