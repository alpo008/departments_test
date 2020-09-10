<?php

namespace App\Http\Controllers;

use App\Department;
use App\User;
use Faker\Provider\Image;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class DepartmentController extends Controller
{
    /**
     * Default start page for pagination on index
     */
    const DEFAULT_START_PAGE = 1;

    /**
     * Default number of entries per page on index
     */
    const DEFAULT_ENTRIES_PER_PAGE = 5;

    /**
     * Default path to render logo image
     */
    const DEFAULT_LOGO_SRC_PATH = '/storage/logo/';

    /**
     * Default path to store logo image
     */
    const DEFAULT_LOGO_SAVE_PATH = 'public/logo';

    /**
     * Returns a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) :Response
    {
        $page = $request->query('page') ?? self::DEFAULT_START_PAGE;
        $entriesPerPage = config('app.department.entriesPerPage', self::DEFAULT_ENTRIES_PER_PAGE);
        $offset = ($page - 1) * $entriesPerPage;
        $totalEntries = Department::count();
        $totalPages = (int) ceil($totalEntries / $entriesPerPage);
        $departments = Department::with('users')
            ->offset($offset)
            ->limit($entriesPerPage)
            ->orderBy('updated_at', 'desc')
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
        $code = 200;
        $data = [];
        $postFields = [];
        $logoSrcPath = config('app.department.logoSrcPath', self::DEFAULT_LOGO_SRC_PATH);
        if ($department = $request->post('department')) {
            $postFields = json_decode($department, true);
        }
        if ($users = $request->post('users')) {
            $users = json_decode($users);
        }
        if ($logo = $request->file('logo')) {
            $postFields['logo'] = $logo;
            $fileName = (string)Str::uuid() . '.' . $logo->getClientOriginalExtension();
        }
        $validator = Validator::make($postFields, Department::rules(), Department::messages());
        if ($validator->fails()) {
            $code = 400;
            $data = $validator->errors();
        } else {
            $postFields['logo'] = !empty($fileName) ? $logoSrcPath . $fileName : null;

            DB::beginTransaction();

            $model = new Department($postFields);
            if ($commit = $model->save()) {
                if ($logo instanceof UploadedFile && !empty($fileName)) {
                    if (!$this->saveFile($logo, $fileName)) {
                        $model->logo = null;
                        $commit = $model->save();
                    }
                }
            }
            try {
                $model->users()->sync($users);
            } catch (\Exception $e) {
                $commit = false;
            }
            if ($commit) {
                DB::commit();
                $data = $model->attributesToArray();
            } else {
                DB::rollBack();
            }
        }
        return response(compact('code', 'data'))
            ->header('Content-Type', 'application/json');
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

    /**
     * Saving uploaded file
     *
     * @param UploadedFile $file
     * @param string $fileName
     * @return bool
     */
    protected function saveFile(UploadedFile $file, string $fileName) {
        $logoSavePath = config('app.department.logoSavePath', self::DEFAULT_LOGO_SAVE_PATH);
        return $file->storeAs($logoSavePath, $fileName);
    }
}
