<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\MunicipalService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $employees = Employee::with('service')->latest()->paginate(10);

        return view('employees.index', compact('employees'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('employees.form', ['employee' => new Employee, 'services' => MunicipalService::where('active', true)->orderBy('name')->get()]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        Employee::create($this->validated($request));

        return redirect()->route('employees.index')->with('success', __('app.flash.employee_added'));
    }

    /**
     * Display the specified resource.
     */
    public function show(Employee $employee)
    {
        return redirect()->route('employees.edit', $employee);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Employee $employee)
    {
        return view('employees.form', ['employee' => $employee, 'services' => MunicipalService::orderBy('name')->get()]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Employee $employee)
    {
        $employee->update($this->validated($request, $employee));

        return redirect()->route('employees.index')->with('success', __('app.flash.employee_updated'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Employee $employee)
    {
        $employee->delete();

        return back()->with('success', __('app.flash.employee_deleted'));
    }

    private function validated(Request $request, ?Employee $employee = null): array
    {
        return $request->validate([
            'municipal_service_id' => ['required', 'exists:municipal_services,id'],
            'employee_number' => ['required', 'max:30', Rule::unique('employees')->ignore($employee)],
            'first_name' => ['required', 'max:100'],
            'last_name' => ['required', 'max:100'],
            'position' => ['required', 'max:150'],
            'email' => ['required', 'email', Rule::unique('employees')->ignore($employee)],
            'phone' => ['nullable', 'max:30'],
            'hire_date' => ['nullable', 'date'],
            'active' => ['nullable', 'boolean'],
        ]) + ['active' => $request->boolean('active')];
    }
}
