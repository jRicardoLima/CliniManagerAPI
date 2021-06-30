<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory,SoftDeletes;

    protected $table = 'products';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'name',
        'maxQuantity',
        'minQuantity',
        'unit_measurement',
        'origin',
        'ncm',
        'cest',
        'cfop',
        'barcode',
        'xped',
        'st_icms',
        'description',
        'product_group_id',
        'organization_id'
    ];

    protected $hidden = [
      'organization_id'
    ];

    //Relations

    public function organizationRelation()
    {
        return $this->belongsTo(Organization::class,'organization_id','id');
    }

    public function productGroupRelation()
    {
        return $this->belongsTo(ProductGroup::class,'product_group_id','id');
    }

    public function supplierRelationPivot()
    {
        return $this->belongsToMany(Supplier::class,'products_suppliers',
                                   'product_id','supplier_id');
    }


}
