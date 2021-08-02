<?php

namespace App\Models;

use App\Repository\Repositories\SupplierRepository;
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

    public function specialtieRelation()
    {
        return $this->hasOne(Specialtie::class,'organization_id','id');
    }

    public function healthProcedure()
    {
        return $this->hasOne(healthProcedure::class,'organization_id','id');
    }

    public function supplierRelation()
    {
        return $this->hasOne(Supplier::class,'organization_id','id');
    }

    public function productGroupRelation()
    {
        return $this->hasOne(ProductGroup::class,'organization_id','id');
    }

    public function productRelation()
    {
        return $this->hasOne(Product::class,'organization_id','id');
    }

    public function stockMovementRelation()
    {
        return $this->hasOne(StockMovement::class,'organization_id','id');
    }
}
