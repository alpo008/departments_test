<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

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
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) :Response
    {
        $code = 200;
        $data = [];
        $post = $request->post();
        $validator = Validator::make($post, User::rules(), User::messages());
        if ($validator->fails()) {
            $code = 400;
            $data = $validator->errors();
        } else {
            $model = new User($post);
            $model->password = bcrypt($model->password);
            if ($model->save()) {
                $data = $model->getAttributes();
                if (!empty($data['password'])) {
                    unset($data['password']);
                } else {
                    $code = 400;
                }
            }
        }
        return response(compact('code', 'data'))
            ->header('Content-Type', 'application/json');
    }

    /**
     * Display the specified resource.
     *
     * @param  string  $id
     * @return \Illuminate\Http\Response
     */
    public function show(string $id) :Response
    {
        if (!$user = User::find($id)) {
            $user = [];
        }
        return response(compact('user'))
            ->header('Content-Type', 'application/json');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, string $id) :Response
    {
        $model = User::find($id);
        if ($model instanceof User) {
            $code = 200;
            $data = [];
            $model->fill($request->post());
            $dirtyAttributes = $model->getDirty();
            $rules = array_filter(User::rules(), function ($key) use($dirtyAttributes) {
                return array_key_exists($key, $dirtyAttributes);
            }, ARRAY_FILTER_USE_KEY);
            $validator = Validator::make($dirtyAttributes, $rules, User::messages());
            if ($validator->fails()) {
                $code = 400;
                $data = $validator->errors();
            } else {
                if ($model->isDirty('password')) {
                    $model->password = bcrypt($model->password);
                }
                if ($model->save()) {
                    $data = $model->getAttributes();
                    if (!empty($data['password'])) {
                        unset($data['password']);
                    } else {
                        $code = 400;
                    }
                }
            }
        } else {
            return response(compact(404, []))
                ->header('Content-Type', 'application/json')
                ->setStatusCode(404);
        }
        return response(compact('code', 'data'))
            ->header('Content-Type', 'application/json');
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
