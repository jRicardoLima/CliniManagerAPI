<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HealthProcedure extends Model
{
    use HasFactory,SoftDeletes;

    protected $table = "health_procedures";
    protected $primaryKey = "id";

    protected $fillable = [
        'uuid',
        'name',
        'register_syndicate'
    ];

    protected $hidden = [
        'organization_id'
    ];

    //Relations

    public function organizationRelation()
    {
        return $this->belongsTo(Organization::class,'organization_id','id');
    }

    public function specialtieRelationPivot()
    {
        return $this->belongsToMany(Specialtie::class,'health_procedure_specialties',
                                    'health_procedure_id','specialtie_id');
    }
}
