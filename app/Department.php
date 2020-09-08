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
}
