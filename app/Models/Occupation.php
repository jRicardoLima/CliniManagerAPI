<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Occupation extends Model
{
    use HasFactory,SoftDeletes;

    protected $table = 'occupations';
    protected $primaryKey = 'id';

    protected $fillable = [
        'uuid',
        'name',
        'organization_id'
    ];

    protected $hidden = [
      'organization_id'
    ];


    //Relations
    public function employee_relation()
    {
        return $this->hasOne(Employee::class,'occupation_id','id');
    }

    //Accessors and Mutators
    public function getCreatedAtAttribute($value)
    {
        if($value != null && $value !=""){
            return convertData($value);
        }
    }

    public function getUpdatedAtAttribute($value)
    {
        if($value != null && $value != ""){
            return convertData($value);
        }
    }

    public function getDeletedAtAttribute($value)
    {
        if($value != null && $value != ""){
            return convertData($value);
        }
    }


}
