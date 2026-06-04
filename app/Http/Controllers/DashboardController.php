<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
use App\Models\ComplaintCategory;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __invoke(Request $request)
    {
        $query = Complaint::query();
        if ($request->user()->role === 'citizen') {
            $query->where('citizen_id', $request->user()->citizen?->id);
        }
        $statusCounts = (clone $query)->selectRaw('status, count(*) as total')->groupBy('status')->pluck('total', 'status');
        $categoryCounts = ComplaintCategory::withCount(['complaints' => fn ($q) => $request->user()->role === 'citizen' ? $q->where('citizen_id', $request->user()->citizen?->id) : $q])->get();

        return view('dashboard', [
            'complaintCount' => (clone $query)->count(),
            'resolvedCount' => (clone $query)->where('status', 'resolved')->count(),
            'processingCount' => (clone $query)->where('status', 'processing')->count(),
            'urgentCount' => (clone $query)->where('priority', 'urgent')->count(),
            'statusCounts' => $statusCounts,
            'categoryCounts' => $categoryCounts,
            'recentComplaints' => (clone $query)->with(['citizen', 'category', 'service'])->latest()->take(6)->get(),
        ]);
    }
}
