<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Supplier extends Model
{
    use HasFactory,SoftDeletes;

    protected $table = 'suppliers';
    protected $primaryKey = 'id';

    protected $fillable = [
        'uuid',
        'cpf_cnpj',
        'company_name',
        'fantasy_name',
        'address_id',
        'organization_id'
    ];

    protected $hidden =[
        'organization_id'
    ];

    //Relations
    public function addressRelation()
    {
        return $this->hasOne(Address::class,'id','address_id');
    }

    public function organizationRelation()
    {
        return $this->belongsTo(Organization::class,'organization_id','id');
    }

    public function productRelationPivot()
    {
        return $this->belongsToMany(Product::class,'products_suppliers',
                                   'supplier_id','product_id');
    }
}
