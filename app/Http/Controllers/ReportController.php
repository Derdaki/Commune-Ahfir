<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function pdf(Request $request, ComplaintController $controller)
    {
        $complaints = $controller->filteredQuery($request)->get();

        return Pdf::loadView('complaints.report', compact('complaints'))->setPaper('a4', 'landscape')->download('reclamations-'.now()->format('Ymd').'.pdf');
    }

    public function excel(Request $request, ComplaintController $controller)
    {
        $complaints = $controller->filteredQuery($request)->get();
        $html = view('complaints.report', compact('complaints'))->render();

        return response("\xEF\xBB\xBF".$html, 200, ['Content-Type' => 'application/vnd.ms-excel; charset=UTF-8', 'Content-Disposition' => 'attachment; filename="reclamations-'.now()->format('Ymd').'.xls"']);
    }
}
