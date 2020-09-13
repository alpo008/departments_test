<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;

/**
 * Class Department
 * @package App
 *
 * @property array $fillable
 *
 * @property integer $id
 * @property string $name
 * @property string $description
 * @property string $logo
 * @property string $created_at
 * @property string $updated_at
 *
 * @property User[] $users
 */
class Department extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'description', 'logo',
    ];

    /**
     * Relation vit table 'users' via junction table 'department_user'
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany('App\User');
    }

    /**
     * Validation rules for Department instances
     *
     * @param Department|null $model
     * @return array
     */
    public static function rules($model = null) :array
    {
        if ($model instanceof self) {
            $nameUnique = '|unique:departments,name,' . $model->id;
        } else {
            $nameUnique = '|unique:departments';
        }
        return [
            'name' => 'required|between:2,127|regex:/^[абвгдеёжзийклмнопрстуфхцчшщъыьэюяяАБВГДЕЁЖЗИЙКЛМНОПРСТУФХЦЧШЩЪЫЬЭЮЯЯА-Яa-zA-Z\s\d_-]+$/' . $nameUnique,
            'description' => 'required|between:5,65535',
        ];
    }

    /**
     * Validation rules for Department instances
     *
     * @return array
     */
    public static function fileRules() :array
    {
        return [
            'mimeType' => ['nullable', Rule::in(['image/jpeg', 'image/png', 'image/gif'])],
            'ext' => ['nullable', Rule::in(['jpeg', 'jpg', 'png', 'gif', 'JPEG', 'JPG', 'PNG', 'GIF'])],
            'size' => 'nullable|integer|max:1024'
        ];
    }
}
