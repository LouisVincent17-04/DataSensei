<?php

namespace App\Http\Controllers;

use App\Services\AdminDashboardService;
use Illuminate\View\View;

class AdminController extends Controller
{
    public function dashboard(AdminDashboardService $analytics): View
    {
        return view('admin.dashboard', [
            'user' => auth()->user(),
            'analytics' => $analytics->overview(),
        ]);
    }
}
