<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Address extends Model
{
    use HasFactory,SoftDeletes;

    protected $table = 'adresses';
    protected $primaryKey = 'id';

    protected $fillable = [
        'uuid',
        'contry',
        'state',
        'zipcode',
        'city',
        'neighborhood',
        'street',
        'number',
        'telphone',
        'celphone',
        'email',
        'observation',
        'organization_id'
    ];

    protected $hidden = [
        'organization_id'
    ];

    //Accessors and Mutators

    public function getCreatedAtAttribute($value)
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

    public function getUpdatedAtAttribute($value)
    {
        if($value != null && $value != ""){
            return convertData($value);
        }
    }

    //Relations
    public function bussinessUnitRelation()
    {
        return $this->belongsTo(BussinessUnit::class,'address_id','id');
    }

    public function employeeRelation()
    {
        return $this->belongsTo(Employee::class,'address_id','id');
    }
}
