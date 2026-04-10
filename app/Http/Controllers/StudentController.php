<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function dashboard()
    {
        // You can also fetch database data here later and pass it to the view
        return view('student.dashboard');
    }

    public function profile(){
        return view('student.profile');
    }
    

    
}
