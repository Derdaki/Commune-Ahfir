<?php

namespace App\Http\Controllers;

use App\Models\AdministrativeRequest;
use App\Models\Citizen;
use App\Models\Employee;
use App\Models\MunicipalService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __invoke(Request $request)
    {
        $statusCounts = AdministrativeRequest::query()
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        return view('dashboard', [
            'citizenCount' => Citizen::count(),
            'employeeCount' => Employee::where('active', true)->count(),
            'serviceCount' => MunicipalService::where('active', true)->count(),
            'requestCount' => AdministrativeRequest::count(),
            'statusCounts' => $statusCounts,
            'recentRequests' => AdministrativeRequest::with(['citizen', 'service'])->latest('submitted_at')->take(6)->get(),
        ]);
    }
}
