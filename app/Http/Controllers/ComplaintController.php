<?php

namespace App\Http\Controllers;

use App\Models\Citizen;
use App\Models\CitizenNotification;
use App\Models\Complaint;
use App\Models\ComplaintCategory;
use App\Models\ComplaintHistory;
use App\Models\Employee;
use App\Models\MunicipalService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ComplaintController extends Controller
{
    public function index(Request $request)
    {
        $complaints = $this->filteredQuery($request)->paginate(12)->withQueryString();

        return view('complaints.index', $this->filters() + compact('complaints'));
    }

    public function create(Request $request)
    {
        return view('complaints.form', $this->formData(new Complaint));
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        if ($request->user()->role === 'citizen') {
            $data['citizen_id'] = $request->user()->citizen->id;
            $data['status'] = 'new';
            $data['channel'] = 'web';
            $data['employee_id'] = null;
        }
        $data['reference'] = 'REC-'.now()->format('Ym').'-'.Str::upper(Str::random(6));
        $complaint = Complaint::create($data);
        $this->history($complaint, 'created', null, 'new', $request->input('history_comment'));
        $this->notify($complaint, __('complaints.notifications.created_title'), __('complaints.notifications.created_message', ['reference' => $complaint->reference]));

        return redirect()->route('complaints.show', $complaint)->with('success', __('complaints.flash.created'));
    }

    public function show(Request $request, Complaint $complaint)
    {
        $this->authorizeAccess($request, $complaint);
        $complaint->load(['citizen', 'category', 'service', 'employee', 'histories.user', 'notifications']);

        return view('complaints.show', compact('complaint'));
    }

    public function edit(Request $request, Complaint $complaint)
    {
        abort_if($request->user()->role === 'citizen', 403);

        return view('complaints.form', $this->formData($complaint));
    }

    public function update(Request $request, Complaint $complaint)
    {
        abort_if($request->user()->role === 'citizen', 403);
        $oldStatus = $complaint->status;
        $oldService = $complaint->municipal_service_id;
        $data = $this->validated($request);
        $data['resolved_at'] = $data['status'] === 'resolved' ? now() : null;
        $complaint->update($data);
        $action = $oldStatus !== $complaint->status ? 'status_changed' : ($oldService !== $complaint->municipal_service_id ? 'assigned' : 'updated');
        $this->history($complaint, $action, $oldStatus, $complaint->status, $request->input('history_comment'));
        if ($oldStatus !== $complaint->status) {
            $this->notify($complaint, __('complaints.notifications.updated_title'), __('complaints.notifications.updated_message', ['reference' => $complaint->reference, 'status' => __("complaints.status.{$complaint->status}")]));
        }

        return redirect()->route('complaints.show', $complaint)->with('success', __('complaints.flash.updated'));
    }

    public function destroy(Request $request, Complaint $complaint)
    {
        abort_unless($request->user()->role === 'admin', 403);
        $complaint->delete();

        return redirect()->route('complaints.index')->with('success', __('complaints.flash.deleted'));
    }

    public function filteredQuery(Request $request): Builder
    {
        $query = Complaint::with(['citizen', 'category', 'service', 'employee'])->latest();
        if ($request->user()->role === 'citizen') {
            $query->where('citizen_id', $request->user()->citizen?->id);
        }
        foreach (['status', 'priority', 'complaint_category_id', 'municipal_service_id'] as $field) {
            $query->when($request->filled($field), fn (Builder $q) => $q->where($field, $request->input($field)));
        }
        $query->when($request->filled('date_from'), fn (Builder $q) => $q->whereDate('created_at', '>=', $request->date('date_from')))
            ->when($request->filled('date_to'), fn (Builder $q) => $q->whereDate('created_at', '<=', $request->date('date_to')))
            ->when($request->filled('search'), fn (Builder $q) => $q->where(fn (Builder $sub) => $sub->where('reference', 'like', '%'.$request->search.'%')->orWhere('subject', 'like', '%'.$request->search.'%')->orWhere('description', 'like', '%'.$request->search.'%')));

        return $query;
    }

    private function validated(Request $request): array
    {
        $rules = [
            'citizen_id' => [$request->user()->role === 'citizen' ? 'nullable' : 'required', 'exists:citizens,id'],
            'complaint_category_id' => ['required', 'exists:complaint_categories,id'],
            'municipal_service_id' => ['nullable', 'exists:municipal_services,id'],
            'employee_id' => ['nullable', 'exists:employees,id'],
            'subject' => ['required', 'string', 'max:180'],
            'description' => ['required', 'string', 'max:5000'],
            'location' => ['nullable', 'string', 'max:255'],
            'priority' => ['required', Rule::in(Complaint::PRIORITIES)],
            'status' => ['required', Rule::in(Complaint::STATUSES)],
            'channel' => ['required', Rule::in(['web', 'desk', 'phone', 'email'])],
            'resolution' => ['nullable', 'string', 'max:3000'],
            'history_comment' => ['nullable', 'string', 'max:1000'],
        ];

        return $request->validate($rules);
    }

    private function formData(Complaint $complaint): array
    {
        return $this->filters() + ['complaint' => $complaint, 'citizens' => Citizen::orderBy('last_name')->get(), 'employees' => Employee::where('active', true)->orderBy('last_name')->get()];
    }

    private function filters(): array
    {
        return ['categories' => ComplaintCategory::where('active', true)->get(), 'services' => MunicipalService::where('active', true)->orderBy('name')->get()];
    }

    private function authorizeAccess(Request $request, Complaint $complaint): void
    {
        if ($request->user()->role === 'citizen') {
            abort_unless($complaint->citizen_id === $request->user()->citizen?->id, 403);
        }
    }

    private function history(Complaint $complaint, string $action, ?string $old, ?string $new, ?string $comment): void
    {
        ComplaintHistory::create(['complaint_id' => $complaint->id, 'user_id' => auth()->id(), 'action' => $action, 'old_status' => $old, 'new_status' => $new, 'comment' => $comment]);
    }

    private function notify(Complaint $complaint, string $title, string $message): void
    {
        CitizenNotification::create(['citizen_id' => $complaint->citizen_id, 'complaint_id' => $complaint->id, 'title' => $title, 'message' => $message]);
    }
}
