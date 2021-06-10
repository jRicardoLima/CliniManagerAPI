<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Profile extends Model
{
    use HasFactory,SoftDeletes;

    protected $table = 'profiles';
    protected $primaryKey = 'id';

    protected $fillable = [
      'uuid',
      'name',
      'organization_id'
    ];

    protected $hidden = [
        'id'
    ];

    //Relations

    public function organizationRelation()
    {
        return $this->belongsTo(Organization::class,'organization_id','id');
    }

    public function userRelation()
    {
        return $this->hasOne(User::class,'profile_id','id');
    }
}
