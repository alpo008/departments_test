<?php

namespace App\Http\Controllers;


use App\Department;
use App\User;
use Carbon\Carbon;

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
     * Shows the application statistics.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $usersTotal = User::count();
        $unlinkedUsers = $users = User::whereNotIn('id', function ($query) {
            $query->select('user_id')
                ->distinct()
                ->from('department_user');
        })->count();
        $departmentsTotal = Department::count();
        $departmentsWithUsers = Department::join('department_user', 'department_user.department_id', '=', 'departments.id')
            ->distinct('departments.id')
            ->count();
        $usersLastUpdate = User::max('updated_at');
        $departmentsLastUpdate = Department::max('updated_at');
        $lastUpdate = max($usersLastUpdate, $departmentsLastUpdate);
        $lastUpdate = Carbon::createFromFormat('Y-m-d H:i:s', $lastUpdate)
            ->format('d.m.Y H:i:s');
        return view('home', compact(
            'usersTotal',
            'unlinkedUsers',
            'departmentsTotal',
            'departmentsWithUsers',
            'lastUpdate'
        ));
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

    /**
     * Protects of redirecting to 404 in case of vue-page reload.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function addUser()
    {
        return view('home', ['vueComponent' => 'add-user']);
    }

    /**
     * Protects of redirecting to 404 in case of vue-page reload.
     *
     * @param string $id
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function editUser(string $id)
    {
        return view('home', ['vueComponent' => 'edit-user']);
    }
}
