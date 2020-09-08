<?php

namespace App\Http\Controllers;

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
        $this->middleware('auth');
    }

    /**
     * Shows the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home');
    }

    /**
     * Shows the departments index page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function departments()
    {
        return view('home', ['vueComponent' => 'departments']);
    }

    /**
     * Shows the users index page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function users()
    {
        return view('home', ['vueComponent' => 'users']);
    }
}
