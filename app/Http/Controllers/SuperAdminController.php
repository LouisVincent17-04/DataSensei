<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Institution;
use App\Models\Module;
use App\Models\ModuleLibraryItem;

class SuperAdminController extends Controller
{
    //
    public function dashboard()
    {
        $users = User::orderByDesc('created_at')->take(5)->get();
        $institutions = Institution::withCount([
                'users as student_count' => fn ($query) => $query->where('role', User::ROLE_USER),
                'users as admin_count' => fn ($query) => $query->where('role', User::ROLE_INSTITUTION_ADMIN),
            ])
            ->orderByDesc('student_count')
            ->orderBy('name')
            ->take(5)
            ->get();

        $stats = [
            'students' => User::where('role', User::ROLE_USER)->count(),
            'admins' => User::whereIn('role', [User::ROLE_ADMIN, User::ROLE_SUPERADMIN, User::ROLE_INSTITUTION_ADMIN])->count(),
            'institutions' => Institution::count(),
            'active_institutions' => Institution::where('status', 'active')->count(),
            'modules' => ModuleLibraryItem::where('is_active', true)->count() ?: Module::count(),
            'disabled_accounts' => User::where('status', 'disabled')->count(),
            'new_registrations' => User::whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()])->count(),
        ];

        return view('superadmin.dashboard', compact('users', 'institutions', 'stats'));
    }
}
