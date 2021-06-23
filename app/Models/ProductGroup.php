<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductGroup extends Model
{
    use HasFactory,SoftDeletes;

    protected $table = 'products_groups';
    protected $primaryKey = 'id';

    protected $fillable = [
      'uuid',
      'name',
      'organization_id',
    ];

    protected $hidden = [
        'organization_id',
    ];

    //Relations

    public function organizationRelation()
    {
        return $this->belongsTo(Organization::class,'organization_id','id');
    }
}
