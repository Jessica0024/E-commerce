<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // Middleware that allows only guests to access the login page
        $this->middleware('guest')->except('logout');
    }

    public function login(Request $request)
    {
        // Validates the input data
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Checks if the 'remember me' checkbox is checked
        $remember = $request->input('remember') ? true : false;

        // Gets the user's credentials
        $credentials = $request->only('email', 'password');

        // Attempts to authenticate the user
        Auth::attempt($credentials, $remember);

        // Redirects the user back with an error message if authentication fails
        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }
}
