<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Organization extends Model
{
    use HasFactory,SoftDeletes;

    protected $table = 'organizations';
    protected $primaryKey = 'id';

    protected $fillable = [
        'uuid',
        'cpf_cnpj',
        'license',
        'qtd_user'
    ];

    protected $hidden = [
            'id'
    ];

    //Relations
    public function userRelation()
    {
        return $this->hasOne(User::class,'organization_id','id');
    }

    public function employeeRelation()
    {
        return $this->hasOne(Employee::class,'organization_id','id');
    }

    public function profileRelation()
    {
        return $this->hasOne(Profile::class,'organization_id','id');
    }
}
