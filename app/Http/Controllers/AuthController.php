<?php 

namespace App\Http\Controllers; // DEFINE THE NAMESPACE FOR THIS CONTROLLER CLASS

use Illuminate\Http\Request; // IMPORT THE REQUEST CLASS TO HANDLE HTTP REQUESTS
use Illuminate\Support\Facades\Auth; // IMPORT THE AUTH FACADE FOR HANDLING AUTHENTICATION LOGIC
use App\Models\User; // IMPORT THE USER MODEL TO INTERACT WITH THE USERS DATABASE TABLE

class AuthController extends Controller // DEFINE THE AUTHCONTROLLER CLASS WHICH EXTENDS LARAVEL'S BASE CONTROLLER
{ // OPEN THE CLASS BODY
  public function showLogin(Request $request) // DEFINE A METHOD TO SHOW THE LOGIN FORM
    { // OPEN THE METHOD BODY

        if (Auth::check()) { // CHECK IF A USER IS ALREADY LOGGED INTO THE APPLICATION
            
            $user = Auth::user(); // GRAB THE INSTANCE OF THE CURRENTLY LOGGED-IN USER

            // REDIRECT THE USER TO THEIR SPECIFIC DASHBOARD BASED ON THEIR ROLE
            if ($user->role == User::ROLE_SUPERADMIN) {
                return redirect('/superadmin/dashboard'); 
            }

            if ($user->role == User::ROLE_ADMIN) {
                return redirect('/admin/dashboard'); 
            }

            if ($user->role == User::ROLE_INSTRUCTOR) {
                return redirect('/instructor/dashboard'); 
            }
            
            if ($user->role == User::ROLE_STUDENT) {
                return redirect('/student/dashboard'); 
            }

            return redirect('/'); // FALLBACK REDIRECT IF ROLE IS UNKNOWN
        } // CLOSE THE AUTH CHECK IF STATEMENT

        // IF THE USER IS NOT LOGGED IN, REACHING THIS LINE WILL RENDER THE LOGIN VIEW
        return view('auth.login'); 
        
    } // CLOSE THE METHOD BODY

    public function login(Request $request) // DEFINE THE LOGIN METHOD THAT ACCEPTS THE INCOMING HTTP REQUEST
    { // OPEN THE LOGIN METHOD BODY
        $request->validate([ // BEGIN VALIDATION OF THE INCOMING FORM DATA
            'email' => 'required|email', // ENSURE THE EMAIL FIELD IS NOT EMPTY AND IS A VALID EMAIL FORMAT
            'password' => 'required', // ENSURE THE PASSWORD FIELD IS NOT EMPTY
        ]); // CLOSE THE VALIDATION RULES ARRAY

        if (Auth::attempt($request->only('email', 'password'))) { // CHECK DATABASE IF EMAIL AND PASSWORD MATCH; IF YES, LOG THEM IN

            $request->session()->regenerate(); // REGENERATE THE SESSION ID TO SECURE AGAINST SESSION FIXATION ATTACKS

            $user = Auth::user(); // GRAB THE INSTANCE OF THE CURRENTLY LOGGED-IN USER

            if ($user->role == User::ROLE_SUPERADMIN) { // CHECK IF THE LOGGED-IN USER HAS THE SUPERADMIN ROLE
                return redirect('/superadmin/dashboard'); // RENDER AND RETURN THE SUPERADMIN DASHBOARD VIEW
            } // CLOSE THE SUPERADMIN ROLE CHECK

            if ($user->role == User::ROLE_ADMIN) { // CHECK IF THE LOGGED-IN USER HAS THE ADMIN ROLE
                return redirect('/admin/dashboard'); // RENDER AND RETURN THE ADMIN DASHBOARD VIEW
            } // CLOSE THE ADMIN ROLE CHECK

            if ($user->role == User::ROLE_INSTRUCTOR) { // CHECK IF THE LOGGED-IN USER HAS THE INSTRUCTOR ROLE
                return redirect('/instructor/dashboard'); // RENDER AND RETURN THE INSTRUCTOR DASHBOARD VIEW
            } // CLOSE THE INSTRUCTOR ROLE CHECK
            
            if ($user->role == User::ROLE_STUDENT) { // CHECK IF THE LOGGED-IN USER HAS THE STUDENT ROLE
                return redirect('/student/dashboard'); // RENDER AND RETURN THE STUDENT DASHBOARD VIEW
            } // CLOSE THE STUDENT ROLE CHECK

            return redirect('/auth/login'); // FALLBACK: RETURN TO LOGIN VIEW IF THE USER HAS NO RECOGNIZED ROLE
        } // CLOSE THE IF STATEMENT FOR SUCCESSFUL AUTHENTICATION

        return back()->withErrors([ // IF LOGIN FAILED, REDIRECT THE USER BACK TO THE LOGIN PAGE WITH ERRORS
            'email' => 'Invalid credentials' // PASS THIS SPECIFIC ERROR MESSAGE FOR THE EMAIL FIELD
        ])->withInput(); // KEEP THE OLD INPUT DATA (LIKE EMAIL) SO THE USER DOES NOT HAVE TO RETYPE IT
    } // CLOSE THE LOGIN METHOD

    public function register(Request $request) // DEFINE THE REGISTER METHOD THAT ACCEPTS THE INCOMING HTTP REQUEST
    { // OPEN THE REGISTER METHOD BODY
        $request->validate([ // BEGIN VALIDATION OF THE REGISTRATION FORM DATA
            'name' => 'required|string|max:255', // ENSURE NAME IS PROVIDED, IS A STRING, AND IS UNDER 255 CHARACTERS
            'email' => 'required|email|unique:users,email', // ENSURE EMAIL IS VALID AND DOES NOT ALREADY EXIST IN THE USERS TABLE
            'password' => 'required|min:6|confirmed' // ENSURE PASSWORD IS AT LEAST 6 CHARS AND MATCHES THE PASSWORD_CONFIRMATION FIELD
        ]); // CLOSE THE VALIDATION RULES ARRAY

        $user = User::create([ // CREATE A NEW RECORD IN THE USERS TABLE AND STORE IT IN THE $USER VARIABLE
            'name' => $request->name, // SET THE DATABASE NAME COLUMN TO THE PROVIDED NAME INPUT
            'email' => $request->email, // SET THE DATABASE EMAIL COLUMN TO THE PROVIDED EMAIL INPUT
            'password' => $request->password, // SET THE DATABASE PASSWORD COLUMN TO THE PROVIDED PASSWORD (LARAVEL HASHES THIS AUTOMATICALLY IN THE MODEL)
            'role' => User::ROLE_STUDENT // AUTOMATICALLY ASSIGN THE DEFAULT STUDENT ROLE TO THE NEW USER
        ]); // CLOSE THE USER CREATION ARRAY

        Auth::login($user); // AUTOMATICALLY LOG IN THE NEWLY CREATED USER SO THEY DON'T HAVE TO LOGIN MANUALLY

        return redirect('/student/dashboard')->with('success', 'Welcome!'); // REDIRECT THE USER TO THEIR DASHBOARD WITH A SUCCESS FLASH MESSAGE
    } // CLOSE THE REGISTER METHOD

    public function logout(Request $request) // DEFINE THE LOGOUT METHOD THAT ACCEPTS THE INCOMING HTTP REQUEST
    { // OPEN THE LOGOUT METHOD BODY
        Auth::logout(); // LOG THE CURRENT USER OUT OF THE APPLICATION

        $request->session()->invalidate(); // INVALIDATE THE CURRENT SESSION TO CLEAR ALL DATA
        $request->session()->regenerateToken(); // REGENERATE THE CSRF TOKEN TO PROTECT AGAINST CROSS-SITE REQUEST FORGERY

        return redirect('/login'); // REDIRECT THE USER BACK TO THE LOGIN PAGE AFTER LOGGING OUT
    } // CLOSE THE LOGOUT METHOD
} // CLOSE THE AUTHCONTROLLER CLASS