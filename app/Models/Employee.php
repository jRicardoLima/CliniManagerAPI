<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
    use HasFactory,SoftDeletes;

    protected $table = 'employee';
    protected $primaryKey = 'id';

    protected $fillable = [
        'uuid',
        'name',
        'birt_date',
        'type',
        'salary',
        'occupation_id',
        'address_id',
        'bussiness_id',
        'organization_id'
    ];

    protected $hidden = [
        'id'
    ];

    //Relations
    public function userRelation()
    {
        return $this->hasOne(User::class,'employee_id','id');
    }

    public function organizationRelation()
    {
        return $this->belongsTo(Organization::class,'organization_id','id');
    }


    //Accessors and Mutators
    public function getBirthDateAttribute($value)
    {
       return  convertData($value);
    }

    public function getSalaryAttribute($value)
    {
        return formatMoneyToBr($value);
    }

    public function getTypeAttribute($value)
    {
        if($value === 'health_professional'){
            return 'Profissional de saúde';
        } else {
            return 'Funcionario padrão';
        }
    }

    public function setTypeAttribute($value)
    {
        if($value === 'Profissional de saúde'){
            $this->attributes['type'] = 'health_professional';
        } else {
            $this->attributes['type'] = 'standard_employee';
        }
    }


}
