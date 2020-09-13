<?php

namespace App\Http\Controllers;

use App\Department;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

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
    const DEFAULT_LOGO_SAVE_PATH = 'app/public/logo/';

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
        $post = $request->post();
        $errors = $this->validateRequest($post);

        if (count($errors)) {
            $code = 400;
        } else {
            $model = new Department($request->post('department'));
            $data = $this->saveModel($model, $request->post('users'), $request->post('logoFile'));
        }

        return response(compact('code', 'data', 'errors'))
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
            $post = $request->json()->all();
            $errors = $this->validateRequest($post, $model);

            if (count($errors)) {
                $code = 400;
            } else {
                $department = $post['department'] ?? [];
                $users = $post['users'] ?? [];
                $logoFile = $post['logoFile'] ?? [];
                if (!empty($department['name'])) {
                    $model->name = $department['name'];
                }
                if (!empty($department['description'])) {
                    $model->description = $department['description'];
                }
                $data = $this->saveModel($model, $users, $logoFile);
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
     * Saving model, updating relations with users and store logo if exists
     *
     * @param Department $model
     * @param array $users
     * @param array $logoFile
     * @return array
     */
    protected function saveModel(Department $model, array $users, array $logoFile)
    {
        DB::beginTransaction();
        $commit = $model->save();
        if (!empty($users) && is_array($users)) {
            try {
                $model->users()->sync($users);
            } catch (\Exception $e) {
                $commit = false;
            }
        }
        if ($commit) {
            DB::commit();
            $data = $model->attributesToArray();
            if (!empty($logoFile)) {
                $this->handleLogoFile($logoFile, $model);
            }
        } else {
            DB::rollBack();
        }
        return $data ?? [];
    }

    /**
     * Validates request and returns errors array if any
     *
     * @param array $post
     * @param Department|null $model
     * @return array
     */
    protected function validateRequest(array $post, $model = null) :array
    {
        $departmentErrors = [];
        $fileErrors = [];
        $department = $post['department'] ?? [];
        $validator = Validator::make($department, Department::rules($model));
        if ($validator->fails()) {
            $departmentErrors = $validator->errors()->messages();
        }
        if (!empty($post['logoFile'])) {
            $validator = Validator::make($post['logoFile'], Department::fileRules());
            if ($validator->fails()) {
                $fileErrors = $validator->errors()->messages();
            }
        }
        return array_merge($departmentErrors, $fileErrors);
    }

    /**
     * Saving or updating logo file
     *
     * @param array $logoFile
     * @param Department $model
     */
    protected function handleLogoFile(array $logoFile, Department $model) :void
    {
        $logoSavePath = config('app.department.logoSavePath', self::DEFAULT_LOGO_SAVE_PATH);
        $logoSrcPath = config('app.department.logoSrcPath', self::DEFAULT_LOGO_SRC_PATH);
        if (!!$model->logo) {
            $oldLogoFile = str_replace($logoSrcPath, '', $model->logo);
        }
        if (!empty($logoFile['ext']) && !empty($logoFile['body'])) {
            $data = preg_replace('#^data:image/\w+;base64,#i', '', $logoFile['body']);
            $fileName = uniqid() . '.' . $logoFile['ext'];
            if (file_put_contents(storage_path($logoSavePath) . $fileName, base64_decode($data))) {
                $model->logo = $logoSrcPath . $fileName;
                if ($model->save() && !empty($oldLogoFile)) {
                    $this->deleteFile($oldLogoFile);
                }
            }
        }
    }


    /**
     * Deleting logo file
     *
     * @param string $fileName
     * @return bool
     */
    protected function deleteFile(string $fileName) :bool
    {
        $logoSavePath = config('app.department.logoSavePath', self::DEFAULT_LOGO_SAVE_PATH);
        try {
            $result = unlink(storage_path($logoSavePath . $fileName)
            );
        } catch (\Exception $e) {
            $result= false;
        }
        return $result;
    }
}
