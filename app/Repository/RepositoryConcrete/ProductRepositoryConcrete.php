<?php


namespace App\Repository\RepositoryConcrete;

use App\Models\FactoriesModels\ModelsFactory;
use App\Models\Product;
use App\Repository\IRepository;
use App\Repository\MediatorRepository\INotifer;
use App\Repository\Serializable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductRepositoryConcrete implements IRepository, Serializable, INotifer
{
    private $model = null;
    private $getDataRelations = false;
    private $isJoinRelation = [];

    public function __construct()
    {
        $this->model = App::make(ModelsFactory::class,['className' => Product::class]);
    }

    public function findId($id, bool $uuid = false, bool $join = false, bool $serialize = false)
    {
        $query = $this->model;

        if($join){
            $this->getDataRelations = true;
        }
        if(!$uuid){
            $query = $query->where('id','=',$id)
                           ->where('organization_id','=',auth()->user()->organization_id)
                           ->first();

        } else {
            $query = $query->where('uuid','=',$uuid)
                           ->where('organization_id','=',auth()->user()->organization_id)
                           ->first();
        }
        if($serialize){
            return $this->serialize($query,'',true);
        }
        return $query;
    }

    public function findAll(bool $join = false, bool $serialize = false)
    {
        $query = $this->model;

        if($join){
            $this->getDataRelations = true;
        }
        $query = $query->where('organization_id','=',auth()->user()->organization_id)
                       ->limit(400)
                       ->orderBy('products.created_at','ASC');
        if($serialize){
            return $this->serialize($query->get(),'','');
        }
        return $query;
    }

    public function save(object $obj, bool $returnObject = false)
    {
        $product = $this->model;

        $product->uuid = Str::uuid();
        $product->name = $obj->name;
        $product->maxQuantity = isset($obj->maxQuantity) && $obj->maxQuantity != null ? $obj->maxQuantity : null;
        $product->minQuantity = isset($obj->minQuantity) && $obj->minQuantity != null ? $obj->minQuantity : null;
        $product->unit_measurement = isset($obj->unit_measurement) && $obj->unit_measurement != null ? $obj->unit_measurement : null;
        $product->origin = isset($obj->origin) && $obj->origin != null ? $obj->origin : null;
        $product->ncm = isset($obj->ncm) && $obj->ncm != null ? $obj->ncm : null;
        $product->cest = isset($obj->cest) && $obj->cest != null ? $obj->cest : null;
        $product->cfop = isset($obj->cfop) && $obj->cfop != null ? $obj->cfop : null;
        $product->barcode = isset($obj->barcode) && $obj->barcode != null ? $obj->barcode : null;
        $product->xped = isset($obj->xped) && $obj->xped != null ? $obj->xped : null;
        $product->st_icms = isset($obj->st_icms) && $obj->st_icms != null ? $obj->st_icms : null;
        $product->description = isset($obj->description) && $obj->description != null ? $obj->description : null;
        $product->product_group_id = isset($obj->product_group) && $obj->product_group != null ? $obj->product_group : null;
        $product->organization_id = auth()->user()->organization_id;

        $ret = $product->save();

        if(isset($obj->linkSuppliers) && count($obj->linkSuppliers) > 0){
            $ids = RequestAllCustom($obj->linkSuppliers,function($item,$key){
                return $item['id'];
            });
            $product->supplierRelationPivot()->sync($ids);
        }

        if($returnObject){
            return $product;
        }
        return $ret;

    }

    public function update($id, object $data)
    {
        $product = $this->findId($id);

        $product->name = $data->name;
        $product->maxQuantity = isset($data->maxQuantity) && $data->maxQuantity != null ? $data->maxQuantity : null;
        $product->minQuantity = isset($data->minQuantity) && $data->minQuantity != null ? $data->minQuantity : null;
        $product->unit_measurement = isset($data->unit_measurement) && $data->unit_measurement != null ? $data->unit_measurement : null;
        $product->origin = isset($data->origin) && $data->origin != null ? $data->origin : null;
        $product->ncm = isset($data->ncm) && $data->ncm != null ? $data->ncm : null;
        $product->cest = isset($data->cest) && $data->cest != null ? $data->cest : null;
        $product->cfop = isset($data->cfop) && $data->cfop != null ? $data->cfop : null;
        $product->barcode = isset($data->barcode) && $data->barcode != null ? $data->barcode : null;
        $product->xped = isset($data->xped) && $data->xped != null ? $data->xped : null;
        $product->st_icms = isset($data->st_icms) && $data->st_icms != null ? $data->st_icms : null;
        $product->description = isset($data->description) && $data->description != null ? $data->description : null;
        $product->product_group_id = isset($data->product_group_id) && $data->product_group_id != null ? $data->product_group_id : null;
        $product->organization_id = auth()->user()->organization_id;

        $ret = $product->save();

        if(isset($data->suppliers)){
            if(count($data->suppliers) > 0){

                $ids = RequestAllCustom($data->suppliers,function($item,$key){
                        foreach ($item as $keyy => $value){
                            if($keyy == 'supplier_id' || $keyy == 'id'){
                                return $value;
                            }
                        }
                });
                $product->supplierRelationPivot()->sync($ids);
            } else {
                $product->supplierRelationPivot->sync([]);
            }
        }

        if($ret){
            return true;
        } else {
            return false;
        }

    }

    public function remove($id, bool $forceDelete = false)
    {
        $product = $this->findId($id);

        if(!$forceDelete){
            $product->supplierRelationPivot()->sync([]);
            return $product->delete();
        }
        $product->supplierRelationPivot()->sync([]);
        return $product->forceDelete();
    }

    public function get(array $conditions, array $coluns = [], bool $join = false, bool $first = false, bool $serialize = false)
    {
        $query = $this->model;

        if(count($coluns) > 0){
            $query = $query->addSelect($coluns);
        }

        if($join){
            $this->getDataRelations = true;
        }

        if(array_key_exists('id',$conditions)){
            $query = $query->where('products.id','=',$conditions['id']);
        }
        if(array_key_exists('name',$conditions)){
            $query = $query->where('products.name','like','%'.$conditions['name'].'%');
        }
        if(array_key_exists('id_suppliers',$conditions)){
            $query = $query->join('products_suppliers','products_suppliers.product_id','=','products.id')
                           ->addSelect('products.*')
                           ->where('products_suppliers.supplier_id','=',$conditions['id_suppliers']);
            $this->isJoinBuilder(true,['products_suppliers']);
        }
        if(array_key_exists('name_suppliers',$conditions)){
                $query = $query->join('products_suppliers','products_suppliers.product_id','=','products.id')
                               ->addSelect('products.*')
                               ->whereExists(function($query) use($conditions){
                                   $query->select(DB::raw(1))
                                          ->from('suppliers')
                                          ->whereColumn('products_suppliers.supplier_id','=','suppliers.id')
                                          ->where(function($query) use($conditions){
                                              $query->where('suppliers.company_name','like','%'.$conditions['name_suppliers'].'%')
                                                    ->orWhere('suppliers.fantasy_name','like','%'.$conditions['name_suppliers'].'%');
                                          });
                               });
            $this->isJoinBuilder(true,['products_suppliers']);
        }
        if(array_key_exists('id_group',$conditions)){
            $query = $query->where('products.product_group_id','=',$conditions['id_group']);
        }
        if(array_key_exists('group_name',$conditions)){
            $query = $query->join('products_groups','products_groups.id','=','products.product_group_id')
                           ->addSelect('products.*')
                           ->where('products_groups.name','like','%'.$conditions['group_name'].'%');
        }
        $query = $query->where('products.organization_id','=',auth()->user()->organization_id)
                       ->limit(400)
                       ->orderBy('products.created_at','ASC');

        if($first){
            if($serialize){
                return $this->serialize($query->first(),'',true);
            }
            return $query->first();
        }
        if($serialize){

            return $this->serialize($query->get(),'');
        }
        return $query->get();
    }

    public function getModel()
    {
        return $this;
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
        if(!$first){
            $dataProduct = new Collection();

            foreach ($data as $key => $value){
                $product = new \stdClass();

                $product->id = $value->id;
                $product->uuid = $value->uuid;
                $product->name = $value->name;
                $product->maxQuantity = $value->maxQuantity;
                $product->minQuantity = $value->minQuantity;
                $product->unit_measurement = $value->unit_measurement;
                $product->origin = $value->origin;
                $product->ncm = $value->ncm;
                $product->cest = $value->cest;
                $product->cfop = $value->cfop;
                $product->barcode = $value->barcode;
                $product->xped = $value->xped;
                $product->st_icms = $value->st_icms;
                $product->description = $value->description;
                $product->created_at = $value->created_at;
                $product->updated_at = $value->updated_at;
                $product->deleted_at = $value->deleted_at;

                if($this->getDataRelations){

                    if($value->productGroupRelation != null){
                        $product->product_group_id = $value->productGroupRelation->id;
                        $product->product_group_uuid = $value->productGroupRelation->uuid;
                        $product->product_group_name = $value->productGroupRelation->name;
                        $product->product_group_created_at = $value->productGroupRelation->created_at;
                        $product->product_group_updated_at = $value->productGroupRelation->updated_at;
                        $product->product_group_deleted_at = $value->productGroupRelation->deleted_at;
                    }
                    $dataSuppliers = $this->getDataRelationMany($value->supplierRelationPivot,$value);

                    $product->suppliers = $dataSuppliers;
                }
                $dataProduct->add($product);
            }
            return $this->typeReturnSerialize($type,$dataProduct);
        }

        $product = new \stdClass();

        $product->id = $data->id;
        $product->uuid = $data->uuid;
        $product->name = $data->name;
        $product->maxQuantity = $data->maxQuantity;
        $product->minQuantity = $data->minQuantity;
        $product->unit_measurement = $data->unit_measurement;
        $product->origin = $data->origin;
        $product->ncm = $data->ncm;
        $product->cest = $data->cest;
        $product->cfop = $data->cfop;
        $product->barcode = $data->barcode;
        $product->xped = $data->xped;
        $product->st_icms = $data->st_icms;
        $product->description = $data->description;
        $product->created_at = $data->created_at;
        $product->updated_at = $data->updated_at;
        $product->deleted_at = $data->deleted_at;

        if($this->getDataRelations){
            if($data->productGroupRelation != null){
                $product->product_group_id = $data->productGroupRelation()->id;
                $product->product_group_uuid = $data->productGroupRelation()->uuid;
                $product->product_group_name = $data->productGroupRelation()->name;
                $product->product_group_created_at = $data->productGroupRelation()->created_at;
                $product->product_group_updated_at = $data->productGroupRelation()->updated_at;
                $product->product_group_deleted_at = $data->productGroupRelation()->deleted_at;
            }
            $dataSuppliers = $this->getDataRelationMany($data->supplierRelationPivot,$data);

            $product->suppliers = $dataSuppliers;
        }
        return $this->typeReturnSerialize($type,$product);

    }
    public function typeReturnSerialize($type,$data)
    {
        if($type == null || $type == '' ){
            return $data;
        }
        return json_encode($data);
    }
    private function isJoinBuilder(bool $join,array $joins)
    {
        return $this->isJoinRelation = ['isJoin' => $join,'joins' => $joins];
    }

    private function getDataRelationMany($relation,$value)
    {
        $data = [];

        if($relation != null){
            foreach ($relation as $item){
                if($item->pivot->product_id == $value->id){
                    $data[] = [
                            'supplier_id' => $item->id,
                            'supplier_uuid' => $item->uuid,
                            'supplier_name' => $item->name,
                            'supplier_cpf_cnpj' => $item->cpf_cnpj,
                            'supplier_company_name' => $item->company_name,
                            'supplier_fantasy_name' => $item->fantasy_name,
                    ];
                }
            }
        }
        return $data;
    }

    private function getDataPivotProductsSuppliers($idProduct)
    {
        $query = "SELECT suppliers.* FROM  products_suppliers,products,suppliers
                   WHERE products_suppliers.product_id = products.id
                   AND products_suppliers.supplier_id = suppliers.id
                   AND products_suppliers.supplier_id = :id";

        return DB::select($query,['id' => $idProduct]);
    }
}
