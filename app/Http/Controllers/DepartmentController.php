<?php

namespace App\Http\Controllers;

use App\Department;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class DepartmentController extends Controller
{
    /**
     * Default start page for pagination on index
     */
    const DEFAULT_START_PAGE = 1;

    /**
     * Default number of entries per page on index
     */
    const DEFAULT_ENTRIES_PER_PAGE = 4;

    /**
     * Returns a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) :Response
    {
        $page = $request->query('page') ?? self::DEFAULT_START_PAGE;
        $offset = ($page - 1) * self::DEFAULT_ENTRIES_PER_PAGE;
        $totalEntries = Department::count();
        $totalPages = (int) ceil($totalEntries / self::DEFAULT_ENTRIES_PER_PAGE);
        $departments = Department::with('users')
            ->offset($offset)
            ->limit(self::DEFAULT_ENTRIES_PER_PAGE)
            ->get();
        $departments = $departments ?? [];
        return response(compact('departments', 'page', 'totalPages'))
            ->header('Content-Type', 'application/json');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //TODO
    }

    /**
     * Returns the specified resource.
     *
     * @param  string  $id
     * @return \Illuminate\Http\Response
     */
    public function show(string $id) :Response
    {
        if (!$department = Department::with('users')->find($id)) {
            $department = [];
        }
        if (!$allUsers = User::all()) {
            $allUsers = [];
        }
        return response(compact('department', 'allUsers'))
            ->header('Content-Type', 'application/json');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Department  $department
     * @return \Illuminate\Http\Response
     */
    public function edit(Department $department)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, string $id)
    {
        //TODO
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Department  $department
     * @return \Illuminate\Http\Response
     */
    public function destroy(Department $department)
    {
        //
    }
}
