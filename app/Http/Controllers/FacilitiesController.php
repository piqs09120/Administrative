<?php

namespace App\Http\Controllers;

use App\Models\Facility;
use Illuminate\Http\Request;

class FacilitiesController extends Controller
{
    public function index()
    {
        $facilities = Facility::latest()->get();
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
        $facility = Facility::findOrFail($id);
        $facility->delete();
        return redirect()->route('facilities.index')->with('success', 'Facility deleted successfully!');
    }
} 