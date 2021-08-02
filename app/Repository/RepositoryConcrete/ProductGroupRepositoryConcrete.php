<?php


namespace App\Repository\RepositoryConcrete;

use App\Models\FactoriesModels\ModelsFactory;
use App\Models\ProductGroup;
use App\Repository\IRepository;
use App\Repository\MediatorRepository\INotifer;
use App\Repository\Serializable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;

class ProductGroupRepositoryConcrete implements IRepository, INotifer, Serializable
{
    protected $model = null;
    private $getDataRelations = false;
    private $isJoinRelation = [];
    public function __construct()
    {
        $this->model = App::make(ModelsFactory::class,['className' => ProductGroup::class]);
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
            $query = $query->where('uuid','=',$id)
                           ->where('organization_id','=',auth()->user()->organization_id)
                           ->first();
        }

        if($serialize){
            return $this->serialize($query,null,true);
        }
        return $query;
    }

    public function findAll(bool $join = false, bool $serialize = false, int $limit = 0)
    {
        $query = $this->model;
        if($join){
            $this->getDataRelations = true;
        }
        $query = $query->where('organization_id',auth()->user()->organization_id);

        if($limit > 0){
            $query = $query->limit($limit);
        }

        if($serialize){
            return $this->serialize($query->get(),'');
        }
        return $query->get();
    }

    public function save(object $obj, bool $returnObject = false)
    {
        $productGroup = $this->model;

        $productGroup->uuid = Str::uuid();
        $productGroup->name = $obj->name;
        $productGroup->organization_id = auth()->user()->organization_id;

        $ret = $productGroup->save();

        if($returnObject){
            return $productGroup;
        }
        return $ret;
    }

    public function update($id, object $data)
    {
        $productGroup = $this->findId($id);

        $productGroup->name = $data->name;

        return $productGroup->save();

    }

    public function remove($id, bool $forceDelete = false)
    {
        $productGroup = $this->findId($id);

        if(!$forceDelete){
            return $productGroup->delete();
        }
        return $productGroup->forceDelete();
    }

    public function get(array $conditions, array $coluns = [], bool $join = false, bool $first = false, bool $serialize = false,int $limit = 0)
    {
       $query = $this->model;

       if(count($coluns)){
           $query = $query->addSelect($coluns);
       }

       if($join){
           $this->getDataRelations = true;
       }

       if(array_key_exists('id',$conditions)){
           $query = $query->where('products_groups.id','=',$conditions['id']);
       }
       if(array_key_exists('name',$conditions)){
           $query = $query->where('products_groups.name','like','%'.$conditions['name'].'%');
       }
       $query = $query->where('products_groups.organization_id','=',auth()->user()->organization_id);
       if($limit > 0){
           $query = $query->limit($limit);
       }
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

        if(!$first){
            $dataProductGroup = new Collection();
            foreach ($data as $key => $value) {
                $productGroup = new \stdClass();

                $productGroup->id = $value->id;
                $productGroup->uuid = $value->uuid;
                $productGroup->name = $value->name;

                $dataProductGroup->add($productGroup);
            }
            return $this->returnTypeSerialize(null,$dataProductGroup);
        }
        $productGroup = new \stdClass();

        $productGroup->id = $data->id;
        $productGroup->uuid = $data->uuid;
        $productGroup->name = $data->name;

        return $this->returnTypeSerialize(null,$productGroup);
    }

    private function returnTypeSerialize($type,$data)
    {
        if($type == null || $type == ''){
            return $data;
        }
        return json_encode($data);
    }

    public function saveInLoop(object $obj, bool $returnObject = false)
    {
        // TODO: Implement saveInLoop() method.
    }
}
