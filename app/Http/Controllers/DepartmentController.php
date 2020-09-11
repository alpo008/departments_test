<?php

namespace App\Http\Controllers;

use App\Department;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
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
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) :Response
    {
        $code = 200;
        $data = [];
        $post = $this->getPost($request);
        $postFields = $post['postFields'] ?? [];
        $users = $post['users'] ?? [];
        $fileName = $post['fileName'] ?? '';
        $logo = $postFields['logo'] ?? null;
        $logoSrcPath = config('app.department.logoSrcPath', self::DEFAULT_LOGO_SRC_PATH);
        $validator = Validator::make($postFields, Department::rules());
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
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, string $id) :Response
    {
        $model = Department::find($id);
        if ($model instanceof Department) {
            $code = 200;
            $data = [];
            $post = $this->getPost($request);
            $postFields = $post['postFields'] ?? [];
            $users = $post['users'] ?? [];
            $fileName = $post['fileName'] ?? '';
            $logo = $postFields['logo'] ?? null;
            $oldFile = null;
            if (!empty($model->logo)) {
                $parts = explode('/', $model->logo);
                $oldFile = array_pop($parts);
            }
            $logoSrcPath = config('app.department.logoSrcPath', self::DEFAULT_LOGO_SRC_PATH);
            $validator = Validator::make($postFields, Department::rules($model));
            if ($validator->fails()) {
                $code = 400;
                $data = $validator->errors();
            } else {
                $model->name = $postFields['name'];
                $model->description = $postFields['description'];

                DB::beginTransaction();

                if ($commit = $model->save()) {
                    if ($logo instanceof UploadedFile && !empty($fileName)) {
                        if ($this->saveFile($logo, $fileName)) {
                            $model->logo = $logoSrcPath . $fileName;
                            $commit = $model->save();
                        }
                        if (!empty($oldFile)) {
                            $this->deleteFile($oldFile);
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
     * @param  string  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(string $id) :Response
    {
        $model = Department::find($id);
        if ($model instanceof Department) {
            $code = 200;
            if (!empty($model->logo)) {
                $parts = explode('/', $model->logo);
                $logoFile = array_pop($parts);
            }
            DB::beginTransaction();
            $model->users()->detach();
            try {
                $commit = $model->delete();
            } catch (\Exception $e) {
                $commit = false;
            }
            if (!$commit) {
                DB::rollBack();
                $code = 400;
            } else {
                DB::commit();
                if (!empty($logoFile)) {
                    $this->deleteFile($logoFile);
                }
            }
            return response(compact('code'))
                ->header('Content-Type', 'application/json');
        } else {
            return response(compact(404, []))
                ->header('Content-Type', 'application/json')
                ->setStatusCode(404);
        }
    }

    /**
     * Saving uploaded file
     *
     * @param UploadedFile $file
     * @param string $fileName
     * @return bool
     */
    protected function saveFile(UploadedFile $file, string $fileName) :bool
    {
        $logoSavePath = config('app.department.logoSavePath', self::DEFAULT_LOGO_SAVE_PATH);
        return $file->storeAs($logoSavePath, $fileName);
    }

    /**
     * Deleting uploaded file
     *
     * @param string $fileName
     * @return bool
     */
    protected function deleteFile(string $fileName) :bool
    {
        $logoSavePath = config('app.department.logoSavePath', self::DEFAULT_LOGO_SAVE_PATH);
        try {
            $result = unlink(storage_path(
                'app' . DIRECTORY_SEPARATOR .$logoSavePath . DIRECTORY_SEPARATOR . $fileName)
            );
        } catch (\Exception $e) {
            $result= false;
        }
        return $result;
    }

    /**
     * Retrieving data from create / update request
     *
     * @param Request $request
     * @return array
     */
    protected function getPost(Request $request) :array
    {
        $postFields = [];
        $fileName = '';
        if ($department = $request->post('department')) {
            $postFields = json_decode($department, true);
            if (!empty($postFields['logo']) && is_string($postFields['logo'])) {
                $postFields['logo'] = null;
            }
        }
        if ($users = $request->post('users')) {
            $users = json_decode($users);
        } else {
            $users = [];
        }
        if ($logo = $request->file('logo')) {
            $postFields['logo'] = $logo;
            $fileName = (string)Str::uuid() . '.' . $logo->getClientOriginalExtension();
        }
        return compact('postFields', 'users', 'fileName');
    }
}
