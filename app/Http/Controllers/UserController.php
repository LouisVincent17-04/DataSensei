<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    CONST ROLE_SUPERADMIN = 4;
    CONST ROLE_ADMIN = 3;
    CONST ROLE_INSTRUCTOR = 2;
    CONST ROLE_STUDENT = 1;
   
    public function changePassword(){

        if( Auth::user()->role == self::ROLE_STUDENT){
            return view('student.change_password');
        }
        else if( Auth::user()->role == self::ROLE_INSTRUCTOR){
            return view('instructor.change_password');
        }
        else if( Auth::user()->role == self::ROLE_ADMIN){
            return view('admin.change_password');
        }
        else if( Auth::user()->role == self::ROLE_SUPERADMIN){
            return view('superadmin.change_password');
        }
    }
    
}
