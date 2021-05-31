<?php


namespace App\Repository\RepositoryConcrete;


use App\Models\BussinessUnit;
use App\Models\FactoriesModels\ModelsFactory;
use App\Repository\IRepository;
use App\Repository\MediatorRepository\DispatchNotifier;
use App\Repository\MediatorRepository\INotifer;
use App\Repository\Serializable;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;
use stdClass;

class BussinessUnitRepositoryConcrete implements IRepository,INotifer,Serializable
{
    protected  $model = null;

    public function __construct()
    {
        $this->model = App::make(ModelsFactory::class,['className' => BussinessUnit::class]);
    }

    public function findId($id, bool $uuid = false,bool $join = false,bool $serialize = false)
    {
        if(!$uuid){
            return $this->model->where('id',$id)
                               ->where('organization_id','=',auth()->user()->organization_id)
                               ->with('addressRelation')
                               ->first();
        } else {
            return $this->model->where('uuid',$id)
                               ->where('organization_id','=',auth()->user()->organization_id)
                               ->with('addressRelation')
                               ->first();
        }
    }

    public function findAll(bool $join = false,bool $serialize = false)
    {
        $ret = $this->model->where('organization_id','=',auth()->user()->organization_id)->with('addressRelation')->get();
        if($serialize){
         return $this->serialize($ret);
        } else {
            return $ret;
        }
    }

    public function save(object $obj, bool $returnObject = false)
    {
        $bussiness = $this->model;

        $bussiness->uuid = Str::uuid();
        $bussiness->company_name = $obj->company_name;
        $bussiness->fantasy_name = $obj->fantasy_name;
        $bussiness->cpf_cnpj = $obj->cpf_cnpj;

        $bussiness->address_id = $this->notifier('saveAddress',$obj)->id;
        $bussiness->organization_id = auth()->user()->organization_id;

        $ret = $bussiness->save();

        if($returnObject){
            return $bussiness;
        } else {
            return $ret;
        }
    }

    public function update($id, object $data)
    {
        $bussiness = $this->findId($id);

        $bussiness->company_name = $data->company_name;
        $bussiness->fantasy_name = $data->fantasy_name;
        $bussiness->cpf_cnpj = $data->cpf_cnpj;
        $ret = $this->notifier('updateaddress',$data);

        if($ret){
           return $bussiness->save();
        } else {
            return false;
        }
    }

    public function remove($id, bool $forceDelete = false)
    {
        if(!$forceDelete){
            $bussiness = $this->findId($id);

            $param = new stdClass();
            $param->id = $bussiness->address_id;
            $param->forceDelete = false;

            $ret = $this->notifier('deleteaddress',$param);
            if($ret){
                return $bussiness->delete();
            } else{
                return false;
            }

        } else {
            $bussiness = $this->findId($id);

            $param = new stdClass();
            $param->id = $bussiness->address_id;
            $param->forceDelete = true;

            $ret = $this->notifier('deleteaddress',$param);

            if($ret){
                return $bussiness->forceDelete();
            } else {
                return false;
            }
        }
    }

    public function get(array $conditions, array $coluns = [], bool $join = false, bool $first = false,bool $serialize = false)
    {
        $query = $this->model;

        if(count($coluns) > 0){
            $query = $query->addSelect($coluns);
        }

       if($join){
           $query = $query->with(['addressRelation','employeeRelation']);
       }

       if(array_key_exists('id',$conditions)){
           $query = $query->where('bussiness_units.id','=',$conditions['id']);
       }
       if(array_key_exists('company_name',$conditions)){
           $query = $query->where('bussiness_units.company_name','like','%'.$conditions['company_name'].'%');
       }
       if(array_key_exists('fantasy_name',$conditions)){
           $query = $query->where('bussiness_units.fantasy_name','like','%'.$conditions['fantasy_name'].'%');
       }
       if(array_key_exists('cpf_cnpj',$conditions)){
           $query = $query->where('bussiness_units.cpf_cnpj','=',$conditions['cpf_cnpj']);
       }
       if(array_key_exists('created_at_ini',$conditions) && array_key_exists('created_at_end',$conditions)){
            $query = $query->whereBetween('bussiness_units.created_at',[$conditions['created_at_ini'],$conditions['created_at_end']]);
       }
       $query = $query->where('bussiness_units.organization_id','=',auth()->user()->organization_id);

       if($first){
           if($serialize){
               return $this->serialize($query->first());
           }
           return $query->first();
       } else {
           if($serialize){
               return $this->serialize($query->get());
           }
           return $query->get();
       }
    }

    public function getModel()
    {
        return $this->model;
    }

    public function notifier(string $methodNotifier,$param = null)
    {
        $serviceDispatch = App::make(DispatchNotifier::class,['classNotified' => AddressRepositoryConcrete::class,'classNotifier' => BussinessUnitRepositoryConcrete::class]);
        if(is_null($param)){
            throw new Exception('Param is Null');
        }
        switch (strtolower($methodNotifier)){
            case 'saveaddress':
                $id = $serviceDispatch->dispatchSaveAddress($param);
                return $id;
            case 'updateaddress':

                $ret = $serviceDispatch->dispacthUpdateAddress($param->address_id,$param);
                return $ret;
            case 'deleteaddress':
                $ret = $serviceDispatch->dispatchDeleteAddress($param);
                return $ret;
            default:
                throw new Exception('Method not found');
        }
    }

    public function thisNotifier()
    {
       return $this;
    }

    public function serialize($data,string $type = 'json',bool $first = false)
    {
        $dataBussiness = new Collection();
        foreach ($data as $key => $value){
            $bussinessSerialize = new stdClass();
            $bussinessSerialize->id = $value->id;
            $bussinessSerialize->uuid = $value->uuid;
            $bussinessSerialize->company_name = $value->company_name;
            $bussinessSerialize->fantasy_name = $value->fantasy_name;
            $bussinessSerialize->cpf_cnpj = $value->cpf_cnpj;
            $bussinessSerialize->address_id = $value->address_id;
            $bussinessSerialize->address_uuid = $value->addressRelation->uuid;
            $bussinessSerialize->country = $value->addressRelation->contry;
            $bussinessSerialize->state = $value->addressRelation->state;
            $bussinessSerialize->city = $value->addressRelation->city;
            $bussinessSerialize->zipcode = $value->addressRelation->zipcode;
            $bussinessSerialize->neighborhood = $value->addressRelation->neighborhood;
            $bussinessSerialize->street = $value->addressRelation->street;
            $bussinessSerialize->number = $value->addressRelation->number;
            $bussinessSerialize->telphone = $value->addressRelation->telphone;
            $bussinessSerialize->celphone = $value->addressRelation->celphone;
            $bussinessSerialize->email = $value->addressRelation->email;
            $bussinessSerialize->address_updated_at = $value->addressRelation->updated_at;
            $bussinessSerialize->address_created_at = $value->addressRelation->created_at;
            $bussinessSerialize->address_deleted_at = $value->addressRelation->deleted_at;
            $bussinessSerialize->created_at = $value->created_at;
            $bussinessSerialize->deleted_at = $value->deleted_at;
            $bussinessSerialize->updated_at = $value->updated_at;
            $dataBussiness->add($bussinessSerialize);
        }

        if($type == '' || $type == null){
            return $dataBussiness;
        } else if($type == 'md64'){
            return base64_encode($bussinessSerialize);
        } else if ($type == 'json'){
            return $dataBussiness->jsonSerialize();
        }
    }
}
