<?php

namespace App\Http\Controllers;

use App\Models\Facility;
use App\Models\FacilityReservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Notifications\FacilityReservationStatusNotification;
use App\Models\AccessLog;

class FacilityReservationController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $role = auth()->user()->role ?? null;
            if (!in_array(strtolower($role), ['administrator'])) {
                abort(403, 'Only Administrators can approve or deny facility reservations.');
            }
            return $next($request);
        })->only(['approve', 'deny']);
    }

    public function index()
    {
        // Show all reservations for the current user or all if admin/legal
        $reservations = FacilityReservation::with(['facility', 'reserver', 'approver'])->latest()->get();
        return view('facility_reservations.index', compact('reservations'));
    }

    public function create()
    {
        $facilities = Facility::where('status', 'available')->get();
        return view('facility_reservations.create', compact('facilities'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'facility_id' => 'required|exists:facilities,id',
            'start_time' => 'required|date|after:now',
            'end_time' => 'required|date|after:start_time',
            'purpose' => 'nullable|string'
        ]);

        FacilityReservation::create([
            'facility_id' => $request->facility_id,
            'reserved_by' => Auth::id(),
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'purpose' => $request->purpose,
            'status' => 'pending'
        ]);

        return redirect()->route('facility_reservations.index')->with('success', 'Reservation request submitted for approval!');
    }

    public function show($id)
    {
        $reservation = FacilityReservation::with(['facility', 'reserver', 'approver'])->findOrFail($id);
        return view('facility_reservations.show', compact('reservation'));
    }

    public function approve($id)
    {
        if (strtolower(Auth::user()->role) !== 'administrator') {
            abort(403, 'Only Administrators can approve reservations.');
        }
        $reservation = FacilityReservation::findOrFail($id);
        if ($reservation->status !== 'pending') {
            return redirect()->back()->with('error', 'This reservation has already been processed.');
        }
        $reservation->update([
            'status' => 'approved',
            'approved_by' => Auth::id(),
            'remarks' => request('remarks')
        ]);
        // Notify reserver
        $reservation->reserver->notify(new FacilityReservationStatusNotification($reservation));
        // Log action
        AccessLog::create([
            'user_id' => Auth::id(),
            'action' => 'approve_facility_reservation',
            'description' => 'Approved facility reservation ID ' . $reservation->id,
            'ip_address' => request()->ip()
        ]);
        return redirect()->route('facility_reservations.index')->with('success', 'Reservation approved!');
    }

    public function deny($id)
    {
        if (strtolower(Auth::user()->role) !== 'administrator') {
            abort(403, 'Only Administrators can deny reservations.');
        }
        $reservation = FacilityReservation::findOrFail($id);
        if ($reservation->status !== 'pending') {
            return redirect()->back()->with('error', 'This reservation has already been processed.');
        }
        $reservation->update([
            'status' => 'denied',
            'approved_by' => Auth::id(),
            'remarks' => request('remarks')
        ]);
        // Notify reserver
        $reservation->reserver->notify(new FacilityReservationStatusNotification($reservation));
        // Log action
        AccessLog::create([
            'user_id' => Auth::id(),
            'action' => 'deny_facility_reservation',
            'description' => 'Denied facility reservation ID ' . $reservation->id,
            'ip_address' => request()->ip()
        ]);
        return redirect()->route('facility_reservations.index')->with('success', 'Reservation denied.');
    }

    public function destroy($id)
    {
        $reservation = FacilityReservation::findOrFail($id);
        $reservation->delete();
        return redirect()->route('facility_reservations.index')->with('success', 'Reservation deleted.');
    }
}
