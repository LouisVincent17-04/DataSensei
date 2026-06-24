<?php

namespace App\Http\Controllers;

use App\Services\AdminDashboardService;
use Illuminate\View\View;

class AdminReportController extends Controller
{
    public function index(AdminDashboardService $analytics): View
    {
        return view('admin.reports.index', [
            'reports' => $analytics->reports(),
        ]);
    }
}
