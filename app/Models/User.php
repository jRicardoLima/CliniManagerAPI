<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable,HasApiTokens,SoftDeletes;

    protected $table = 'users';
    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'uuid',
        'user_name',
        'password',
        'employee_id',
        'profile_id',
        'organization_id'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    //RELATIONS
    public function organizationRelation()
    {
        return $this->belongsTo(Organization::class,'organization_id','id');
    }

    public function employeeRelation()
    {
        return $this->hasOne(Employee::class,'employee_id','id');
    }

    public function profileRelation()
    {
        return $this->belongsTo(Profile::class,'profile_id','id');
    }
}
