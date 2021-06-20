<?php


namespace App\Repository\RepositoryConcrete;


use App\Models\FactoriesModels\ModelsFactory;
use App\Models\Occupation;
use App\Repository\IRepository;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;

class OcuppationRepositoryConcrete implements IRepository
{
    protected $model = null;

    public function __construct()
    {
        $this->model = App::make(ModelsFactory::class,['className' => Occupation::class]);
    }

    public function findId($id,bool $uuid = false,bool $join = false,bool $serialize = false)
    {
        if(!$uuid){
            return $this->model->where('id', $id)
                                ->where('organization_id','=',auth()->user()->organization_id)
                                ->first();
        } else {
            return $this->model->where('uuid',$id)
                                ->where('organization_id','=',auth()->user()->organization_id)
                                ->first();
        }
    }

    public function findAll(bool $join = false,bool $serialize = false)
    {
        return $this->model->where('organization_id','=',auth()->user()->organization_id)->get();
    }

    public function save(object $obj, bool $returnObject = false)
    {
        $occupation = $this->model;

        $occupation->uuid = Str::uuid();
        $occupation->name = $obj->name;
        $occupation->organization_id = auth()->user()->organization_id;

       $ret = $occupation->save();

        if($returnObject){
            return $occupation;
        } else {
            return $ret;
        }
    }

    public function update($id,object $data)
    {
        $occ = $this->findId($id);

        $occ->name = $data->name;
        return $occ->save();
    }

    public function remove($id,bool $forceDelete = false)
    {
        if(!$forceDelete){
            $occ = $this->findId($id);
            return $occ->delete();
        } else {
            $occ = $this->findId($id);
            return $occ->forceDelete();
        }
    }

    public function get(array $conditions, array $coluns = [], bool $join = false,bool $first = false,bool $serialize = false)
    {
        $query = $this->model;

        if(count($coluns) > 0){
            $query = $query->addSelect($coluns);
        }
        if(array_key_exists('created_at_ini',$conditions) && array_key_exists('created_at_end',$conditions)){
               $query = $query->whereBetween('created_at',[$conditions['created_at_ini'],$conditions['created_at_end']]);
        }
        if(array_key_exists('name',$conditions)){
           $query = $query->where('name','=',$conditions['name']);
        }
        $query = $query->where('organization_id','=',auth()->user()->organization_id);
        if($first){
          return $query->first();
        } else{
            return $query->get();
        }
    }

    public function getModel()
    {
        return $this->model;
    }
}
