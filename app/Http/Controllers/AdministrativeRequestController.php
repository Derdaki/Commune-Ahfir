<?php

namespace App\Http\Controllers;

use App\Models\AdministrativeRequest;
use App\Models\Citizen;
use App\Models\CitizenNotification;
use App\Models\Employee;
use App\Models\MunicipalService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class AdministrativeRequestController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $query = AdministrativeRequest::with(['citizen', 'service', 'employee'])->latest('submitted_at');
        if (request('status')) {
            $query->where('status', request('status'));
        }
        $requests = $query->paginate(10)->withQueryString();

        return view('requests.index', ['requests' => $requests, 'statuses' => AdministrativeRequest::statusLabels()]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('requests.form', $this->formData(new AdministrativeRequest));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['reference'] = 'AHF-'.now()->format('Ymd').'-'.Str::upper(Str::random(5));
        $data['submitted_at'] = now();
        $administrativeRequest = AdministrativeRequest::create($data);
        $this->notify($administrativeRequest, __('app.flash.request_added'), __('app.notifications.request_status_message', ['reference' => $administrativeRequest->reference, 'status' => __('app.status.pending')]));

        return redirect()->route('requests.index')->with('success', __('app.flash.request_added'));
    }

    /**
     * Display the specified resource.
     */
    public function show(AdministrativeRequest $administrativeRequest)
    {
        $administrativeRequest->load(['citizen', 'service', 'employee', 'notifications']);

        return view('requests.show', ['administrativeRequest' => $administrativeRequest, 'statuses' => AdministrativeRequest::statusLabels()]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(AdministrativeRequest $administrativeRequest)
    {
        return view('requests.form', $this->formData($administrativeRequest));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, AdministrativeRequest $administrativeRequest)
    {
        $oldStatus = $administrativeRequest->status;
        $data = $this->validated($request);
        if (in_array($data['status'], ['approved', 'rejected'], true)) {
            $data['processed_at'] = now();
        } else {
            $data['processed_at'] = null;
        }
        $administrativeRequest->update($data);
        if ($oldStatus !== $administrativeRequest->status) {
            $label = AdministrativeRequest::statusLabels()[$administrativeRequest->status];
            $this->notify($administrativeRequest, __('app.notifications.request_updated'), __('app.notifications.request_status_message', ['reference' => $administrativeRequest->reference, 'status' => $label]));
        }

        return redirect()->route('requests.show', $administrativeRequest)->with('success', __('app.flash.request_updated'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AdministrativeRequest $administrativeRequest)
    {
        $administrativeRequest->delete();

        return redirect()->route('requests.index')->with('success', __('app.flash.request_deleted'));
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'citizen_id' => ['required', 'exists:citizens,id'],
            'municipal_service_id' => ['required', 'exists:municipal_services,id'],
            'employee_id' => ['nullable', 'exists:employees,id'],
            'type' => ['required', 'max:150'],
            'description' => ['nullable', 'max:2000'],
            'status' => ['required', Rule::in(array_keys(AdministrativeRequest::STATUSES))],
            'admin_notes' => ['nullable', 'max:2000'],
        ]);
    }

    private function formData(AdministrativeRequest $administrativeRequest): array
    {
        return [
            'administrativeRequest' => $administrativeRequest,
            'citizens' => Citizen::orderBy('last_name')->get(),
            'services' => MunicipalService::where('active', true)->orderBy('name')->get(),
            'employees' => Employee::where('active', true)->orderBy('last_name')->get(),
            'statuses' => AdministrativeRequest::statusLabels(),
        ];
    }

    private function notify(AdministrativeRequest $request, string $title, string $message): void
    {
        CitizenNotification::create([
            'citizen_id' => $request->citizen_id,
            'administrative_request_id' => $request->id,
            'title' => $title,
            'message' => $message,
        ]);
    }
}
