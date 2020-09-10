<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

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
     * @return array
     */
    public static function rules() :array
    {
        return [
            'name' => 'unique:departments|required|between:2,127|regex:/^[абвгдеёжзийклмнопрстуфхцчшщъыьэюяяАБВГДЕЁЖЗИЙКЛМНОПРСТУФХЦЧШЩЪЫЬЭЮЯЯА-Яa-zA-Z\s\d_-]+$/',
            'description' => 'required|between:5,65535',
            'logo' => 'file|mimes:jpeg,gif,png|max:1024'
        ];
    }

    /**
     * Validation errors messages
     *
     * @return array
     */
    public static function messages()
    {
        return [
            'required' => __('This field is required'),
            'between' => __('Number of characters should be between :min and :max'),
            'max' => __('Max allowed size is :max KB'),
            'regex' => __('Unacceptable symbols'),
            'file' => __('File upload failed'),
            'mimes' => __('Wrong mime-type of the uploaded file')
        ];
    }
}
