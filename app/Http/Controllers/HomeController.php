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

    /**
     * Protects of redirecting to 404 in case of vue-page reload.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function addDepartment()
    {
        return view('home', ['vueComponent' => 'add-department']);
    }

    /**
     * Protects of redirecting to 404 in case of vue-page reload.
     *
     * @param string $id
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function editDepartment(string $id)
    {
        return view('home', ['vueComponent' => 'edit-department']);
    }
}
