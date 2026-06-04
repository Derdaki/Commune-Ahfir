<?php

namespace App\Http\Controllers;

use App\Models\MunicipalService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class MunicipalServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $services = MunicipalService::withCount(['employees', 'requests'])->orderBy('name')->paginate(10);

        return view('services.index', compact('services'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('services.form', ['service' => new MunicipalService]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        MunicipalService::create($this->validated($request));

        return redirect()->route('services.index')->with('success', __('app.flash.service_added'));
    }

    /**
     * Display the specified resource.
     */
    public function show(MunicipalService $municipalService)
    {
        return redirect()->route('services.edit', $municipalService);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MunicipalService $municipalService)
    {
        return view('services.form', ['service' => $municipalService]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MunicipalService $municipalService)
    {
        $municipalService->update($this->validated($request, $municipalService));

        return redirect()->route('services.index')->with('success', __('app.flash.service_updated'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MunicipalService $municipalService)
    {
        if ($municipalService->employees()->exists() || $municipalService->requests()->exists()) {
            return back()->with('error', __('app.flash.service_in_use'));
        }
        $municipalService->delete();

        return back()->with('success', __('app.flash.service_deleted'));
    }

    private function validated(Request $request, ?MunicipalService $service = null): array
    {
        return $request->validate([
            'name' => ['required', 'max:150', Rule::unique('municipal_services')->ignore($service)],
            'code' => ['required', 'max:30', Rule::unique('municipal_services')->ignore($service)],
            'description' => ['nullable', 'max:1000'],
            'active' => ['nullable', 'boolean'],
        ]) + ['active' => $request->boolean('active')];
    }
}
