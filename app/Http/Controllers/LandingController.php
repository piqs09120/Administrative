<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LandingController extends Controller
{
    /**
     * Display the landing page
     */
    public function index()
    {
        return view('landing.index');
    }

    /**
     * Display the visitor management landing page
     */
    public function visitorManagement()
    {
        return view('visitor_management_landing');
    }

    /**
     * Display the facilities reservation landing page
     */
    public function facilitiesReservation()
    {
        $facilities = \App\Models\Facility::all();
        return view('facilities_reservation_landing', compact('facilities'));
    }
}
