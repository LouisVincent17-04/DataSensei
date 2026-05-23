<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Institution;
use App\Models\Module;

class SuperAdminController extends Controller
{
    //
    public function dashboard()
    {
        $users = User::orderBy('created_at', 'desc')->take(10)->get();
        $institutions = Institution::orderBy('id', 'desc')->take(5)->get();
        return view('superadmin.dashboard', compact('users', 'institutions'));
    }
}
