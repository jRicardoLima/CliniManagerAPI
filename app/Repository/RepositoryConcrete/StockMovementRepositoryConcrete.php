<?php


namespace App\Repository\RepositoryConcrete;


use App\Models\FactoriesModels\ModelsFactory;
use App\Models\StockMovement;
use App\Repository\IRepository;
use App\Repository\MediatorRepository\INotifer;
use App\Repository\Serializable;
use Illuminate\Container\Container;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class StockMovementRepositoryConcrete implements IRepository, INotifer, Serializable
{
    protected $model;
    private $getDataRelations = false;
    private $isJoinRelation = [];
    public function __construct()
    {
        $this->model = App::make(ModelsFactory::class,['className' => StockMovement::class]);
    }

    public function findId($id, bool $uuid = false, bool $join = false, bool $serialize = false)
    {
       $query  = $this->model;

       if($join){
           $query = $query->with(['productRelation','bussinessUnitRelation','supplierRelation']);
           $this->getDataRelations = true;
       }
       if(!$uuid){
           $query = $query->where('id','=',$id)
                          ->where('organization_id','=',auth()->user()->organization_id)
                          ->first();
       } else {
           $query = $query->where('uuid','=',$id)
                          ->where('organization_id','=',auth()->user()->organization_id)
                          ->first();
       }
       if($serialize){
           return $this->serialize($query,'',true);
       }
       return $query;
    }

    public function findAll(bool $join = false, bool $serialize = false,int $limit = 0)
    {
        $query = $this->model;

        if($join){
            $this->getDataRelations = true;
        }
        $query = $query->where('organization_id','=',auth()->user()->organization_id);

        if($limit > 0){
            $query = $query->limit($limit);
        }
        $query = $query->orderBy('stock_movements.created_at','ASC');
        if($serialize){
            return $query->get()->toJson();
        }
        return $query->get();
    }

    public function save(object $obj, bool $returnObject = false)
    {
        $stockMovement = $this->model;
        $stockMovement->uuid = Str::uuid();
        $stockMovement->type = ($obj->type == 'Entrada') ? 'input' : 'output';
        $stockMovement->quantity_moved = $obj->quantity_moved;
        $stockMovement->unitary_amount = $obj->unitary_amount;
        $stockMovement->total_amount = $obj->quantity_moved * $obj->unitary_amount;
        $stockMovement->date_movement = $obj->date_movement;
        $stockMovement->pin = null;
        $stockMovement->product_id = $obj->product_id;
        $stockMovement->bussiness_unit_id = $obj->bussiness_unit_id;
        $stockMovement->supplier_id = $obj->supplier_id;
        $stockMovement->organization_id  = auth()->user()->organization_id;

        $ret = $stockMovement->save();

        if($returnObject){
            return $stockMovement;
        }
        return $ret;
    }

    public function saveInLoop(object $obj,bool $returnObject = false)
    {
        $stockMovement = App::make(ModelsFactory::class,['className' => StockMovement::class]);
        $stockMovement->uuid = Str::uuid();
        $stockMovement->type = ($obj->type == 'Entrada') ? 'input' : 'output';
        $stockMovement->quantity_moved = $obj->quantity_moved;
        $stockMovement->unitary_amount = isset($obj->unitary_amount) && $obj->unitary_amount != null ? $obj->unitary_amount : null;
        $stockMovement->total_amount = (isset($obj->quantity_moved) && $obj->quantity_moved != null  && isset($obj->unitary_amount) && $obj->unitary_amount != null)
                                       ? $obj->quantity_moved * $obj->unitary_amount : 0.0;
        $stockMovement->date_movement = $obj->date_movement;
        $stockMovement->pin = null;
        $stockMovement->product_id = $obj->product_id;
        $stockMovement->bussiness_unit_id = $obj->bussiness_unit_id;
        $stockMovement->supplier_id = isset($obj->supplier_id)  && $obj->supplier_id != null ? $obj->supplier_id : null;
        $stockMovement->organization_id  = auth()->user()->organization_id;

        $ret = $stockMovement->save();

        if($returnObject){
            return $stockMovement;
        }
        return $ret;
    }

    public function update($id, object $data)
    {
        $stockMovement = $this->findId($id);

        $stockMovement->type = $data->type;
        $stockMovement->quantity_moved = $data->quantity_moved;
        $stockMovement->unitary_amount = $data->unitary_amount;
        $stockMovement->total_amount = $data->quantity_moved * $data->unitary_amount;
        $stockMovement->product_id = $data->product_id;
        $stockMovement->bussiness_unit_id = $data->bussiness_unit_id;
        $stockMovement->supplier_id = $data->supplier_id;

        $ret = $stockMovement->save();

        return $ret;
    }

    public function remove($id, bool $forceDelete = false)
    {
        $stockMovement = $this->findId($id);

        if(!$forceDelete){
           return $stockMovement->delete();
        }
        return $stockMovement->forceDelete();
    }

    public function get(array $conditions, array $coluns = [], bool $join = false, bool $first = false, bool $serialize = false,int $limit = 0)
    {
        $query = $this->model;

        if(count($coluns) > 0){
            $query = $query->addSelect($coluns);
        }

        if($join){
            $query = $query->with(['productRelation','bussinessUnitRelation','supplierRelation']);
            $this->getDataRelations = true;
        }

        if(array_key_exists('id',$conditions)){
            $query = $query->where('stock_movements.id','=',$conditions['id']);
        }
        if(array_key_exists('type_stock',$conditions)){
            $query = $query->where('stock_movements.type','=',$conditions['type_stock']);
        }
        if(array_key_exists('created_at_ini',$conditions) &&
          array_key_exists('created_at_end',$conditions)){
            $query = $query->whereBetween('stock_movements.created_at',[$conditions['created_at_ini'],$conditions['created_at_end']]);
        }
        if(array_key_exists('date_movement_ini',$conditions) &&
           array_key_exists('date_movement_end',$conditions)){
            $query = $query->whereBetween('stock_movements.date_movement',[$conditions['date_movement_ini'],$conditions['date_movement_end']]);
        }
        if(array_key_exists('updated_at_ini',$conditions) &&
          array_key_exists('updated_at_end',$conditions)){
            $query = $query->whereBetween('stock_movements.updated_at',[$conditions['updated_at_ini'], $conditions['updated_at_end']]);
        }
        if(array_key_exists('product_id',$conditions)){
            $query = $query->where('stock_movements.product_id','=',$conditions['product_id']);
        }
        if(array_key_exists('product_name',$conditions)){
            $query = $query->join('products','products.id','=','stock_movements.product_id')
                           ->addSelect('stock_movements.*')
                           ->where('products.name','like','%'.$conditions['product_name'].'%');
        }

        if(array_key_exists('bussiness_unit_id',$conditions)){
            $query = $query->where('stock_movements.bussiness_unit_id','=',$conditions['bussiness_unit_id']);
        }

        if(array_key_exists('supplier_id',$conditions)){
            $query = $query->where('stock_movements.supplier_id','=',$conditions['supplier_id']);
        }
        if(array_key_exists('actualStock',$conditions)){

        }

        $query = $query->where('stock_movements.organization_id','=',auth()->user()->organization_id);

        if($limit > 0){
            $query = $query->limit($limit);
        }
        $query = $query->orderBy('stock_movements.created_at','ASC');

        if($first){
            if($serialize){
                return $query->first()->toJson();
            }
            return $query->first();
        }
        if($serialize){
            return $query->get()->toJson();
        }
        return $query->get();
    }

    public function getModel()
    {
       return $this->model;
    }

    public function notifier(string $methodNotifier, $param = null)
    {
        // TODO: Implement notifier() method.
    }

    public function thisNotifier()
    {
        return $this;
    }

    public function serialize($data, string $type = 'json', bool $first = false)
    {

    }

    private function createTempTableStock($data)
    {
        $query = "CREATE TEMPORARY TABLE actual_stock(
                  product_id INTEGER UNIQUE NOT_NULL,
                  product_name VARCHAR NOT NULL,
                  quantity_actual DECIMAL(10,4) NOT NULL,
                  avarege_price DECIMAL(10,4) NOT NULL,
                  total_amount DECIMAL(10,4) NOT NULL
                 )";

        DB::statement($query);
    }

    private function calcStock()
    {
        $query = $this->model;

       $query = $query->select(DB::raw('DISTINCT stock_movements.product_id'))
                      ->addSelect('stock_movements.*')
                      ->join('products','products.id','=','stock_movements.product_id')
                      ->avg('stock_movements.unitary_amount')
                      ->sum('stock_movements.quantity_moved');
    }

}
