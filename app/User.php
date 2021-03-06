<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * Class User
 * @package App
 *
 * @property array $fillable
 * @property array $hidden
 * @property array $casts
 *
 * @property integer $id
 * @property string $name
 * @property string $email
 * @property string $email_verified_at
 * @property string $password
 * @property string $remember_token
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Department[] $departments
 */
class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'email', 'password'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = ['remember_token'];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Relation vit table 'departments' via junction table 'department_user'
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function departments()
    {
        return $this->belongsToMany('App\Department');
    }

    /**
     * Validation rules for Department instances
     *
     * @param User|null $model
     * @return array
     */
    public static function rules($model = null) :array
    {
        if ($model instanceof self) {
            $nameUnique = '|unique:users,name,' . $model->id;
            $emailUnique = '|unique:users,email,' . $model->id;
        } else {
            $nameUnique = '|unique:users';
            $emailUnique = '|unique:users';
        }
        return [
            'name' => 'required|between:2,127|regex:/^[абвгдеёжзийклмнопрстуфхцчшщъыьэюяяАБВГДЕЁЖЗИЙКЛМНОПРСТУФХЦЧШЩЪЫЬЭЮЯЯА-Яa-zA-Z\s\.]+$/' . $nameUnique,
            'email' => 'required|email' . $emailUnique,
            'password' => 'required|alpha_dash|min:8'
        ];
    }
}
