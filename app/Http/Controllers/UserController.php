<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class UserController extends Controller
{
    /**
     * Default start page for pagination on index
     */
    const DEFAULT_START_PAGE = 1;

    /**
     * Default number of entries per page on index
     */
    const DEFAULT_ENTRIES_PER_PAGE = 10;

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request  $request) :Response
    {
        $page = $request->query('page') ?? self::DEFAULT_START_PAGE;
        $entriesPerPage = config('app.user.entriesPerPage', self::DEFAULT_ENTRIES_PER_PAGE);
        $offset = ($page - 1) * $entriesPerPage;
        $totalEntries = User::count();
        $totalPages = (int) ceil($totalEntries / $entriesPerPage);
        $users = User::orderBy('updated_at', 'desc')
            ->offset($offset)
            ->limit($entriesPerPage)
            ->get();
        $users = $users ?? [];
        return response(compact('users', 'page', 'totalPages'))
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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (!$user = User::find($id)) {
            $user = [];
        }
        return response(compact('user'))
            ->header('Content-Type', 'application/json');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
