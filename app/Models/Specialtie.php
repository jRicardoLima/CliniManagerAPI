<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Specialtie extends Model
{
    use HasFactory,SoftDeletes;

    protected $table = 'specialties';
    protected $primaryKey = 'id';

    protected $fillable = ['uuid','name','register_syndicate'];

    protected $hidden = ['organization_id'];

    //Relations
    public function employeeRelationPivot()
    {
        return $this->belongsToMany(Employee::class,'employee_specialties',
                                    'specialtie_id','employee_id');
    }

    public function healthProcedurePivot()
    {
        return $this->belongsToMany(HealthProcedure::class,'health_procedure_specialties',
                                   'specialtie_id','health_procedure_id');
    }

    public function organizationRelation()
    {
        return $this->belongsTo(Organization::class,'organization_id','id');
    }

}
