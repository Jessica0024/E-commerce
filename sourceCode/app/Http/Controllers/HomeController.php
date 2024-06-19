<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Gate;


use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // Use the auth middleware to require authentication for this controller
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        // Use Gate facade to check if the user is an admin

        // If the user is an admin, redirect them to the products index page
        if (Gate::allows('isAdmin')) {
            return redirect()->route('products.index');
        } 
        
        // If the user is not an admin, redirect them to the products show by category page
        else 
        {
            return redirect()->route('products.showByCategory');
        }
    }
}
