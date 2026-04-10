<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ChallengesController extends Controller
{
    //
    public function challenges() {
        return view('student.challenges');
    }
}
