<?php

namespace App\Http\Controllers;

use App\Models\Citizen;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CitizenController extends Controller
{
    public function index()
    {
        $citizens = Citizen::withCount('complaints')->latest()->paginate(10);

        return view('citizens.index', compact('citizens'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('citizens.form', ['citizen' => new Citizen]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        Citizen::create($this->validated($request));

        return redirect()->route('citizens.index')->with('success', __('app.flash.citizen_added'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Citizen $citizen)
    {
        $citizen->load(['complaints.category', 'notifications']);

        return view('citizens.show', compact('citizen'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Citizen $citizen)
    {
        return view('citizens.form', compact('citizen'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Citizen $citizen)
    {
        $citizen->update($this->validated($request, $citizen));

        return redirect()->route('citizens.index')->with('success', __('app.flash.citizen_updated'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Citizen $citizen)
    {
        if ($citizen->complaints()->exists()) {
            return back()->with('error', __('app.flash.citizen_has_requests'));
        }
        $citizen->delete();

        return back()->with('success', __('app.flash.citizen_deleted'));
    }

    private function validated(Request $request, ?Citizen $citizen = null): array
    {
        return $request->validate([
            'cin' => ['required', 'max:30', Rule::unique('citizens')->ignore($citizen)],
            'first_name' => ['required', 'max:100'],
            'last_name' => ['required', 'max:100'],
            'birth_date' => ['nullable', 'date', 'before:today'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'max:30'],
            'address' => ['nullable', 'max:1000'],
        ]);
    }
}
