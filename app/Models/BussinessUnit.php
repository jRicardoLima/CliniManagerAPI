<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BussinessUnit extends Model
{
    use HasFactory,SoftDeletes;

    protected $table = 'bussiness_units';
    protected $primaryKey = 'id';

    protected $fillable = [
        'uuid',
        'company_name',
        'fantasy_name',
        'cpf_cnpj',
        'address_id',
        'organization_id'
    ];

    protected $hidden = [
        'organization_id'
    ];

    //Accessors and Mutators

    public function getCreatedAtAttribute($value)
    {
        if($value != null && $value !=""){
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
        if($value != null && $value !=""){
            return convertData($value);
        }
    }

    //Relations
    public function addressRelation()
    {
        return $this->hasOne(Address::class,'id','address_id');
    }
}
