<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StockMovement extends Model
{
    use HasFactory,SoftDeletes;

    protected $primaryKey = 'id';
    protected $table = 'stock_movements';

    protected $fillable = [
      'uuid',
      'type',
      'quantity_moved',
      'unitary_amount',
      'total_amount',
      'date_movement',
      'pin',
      'product_id',
      'bussiness_unit_id',
      'supplier_id',
      'organization_id'
    ];

    protected $hidden = [
      'organization_id'
    ];


    //Accessors and Mutators
    public function getCreatedAtAttribute($value)
    {
        return convertData($value);
    }

    public function getUpdatedAtAttribute($value)
    {
        return convertData($value);
    }

    public function getUnitaryAmountAttribute($value)
    {
        if($value != null){
            return formatMoneyToBr($value);
        }

    }

    public function getTotalAmountAttribute($value)
    {
        return formatMoneyToBr($value);
    }

    public function getQuantityMovedAttribute($value)
    {
        return formatMoneyToBr($value);
    }

    public function getTypeAttribute($value)
    {
        if($value == 'input'){
            return 'Entrada';
        } else {
            return 'Saída';
        }
    }

    public function getDateMovementAttribute($value)
    {
        return convertData($value);
    }

    //Relations
    public function productRelation()
    {
        return $this->belongsTo(Product::class,'product_id','id');
    }

    public function bussinessUnitRelation()
    {
        return $this->belongsTo(BussinessUnit::class,'bussiness_unit_id','id');
    }

    public function supplierRelation()
    {
        return $this->belongsTo(Supplier::class,'supplier_id','id');
    }

    public function organizationRelation()
    {
        return $this->belongsTo(Organization::class,'organization_id','id');
    }
    //LOGIC
    public function generatePin()
    {
        $number = rand(0,1000);
        $date = new \DateTime();
        return $number.$date;
    }
}
