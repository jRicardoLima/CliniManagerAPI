<?php


namespace App\Repository\RepositoryConcrete;


use App\Models\BussinessUnit;
use App\Models\FactoriesModels\ModelsFactory;
use App\Repository\IRepository;
use App\Repository\MediatorRepository\DispatchNotifier;
use App\Repository\MediatorRepository\INotifer;
use Exception;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;

class BussinessUnitRepositoryConcrete implements IRepository,INotifer
{
    protected  $model = null;

    public function __construct()
    {
        $this->model = App::make(ModelsFactory::class,['className' => BussinessUnit::class]);
    }

    public function findId($id, bool $uuid = false)
    {
        if(!$uuid){
            return $this->model->where('id',$id)
                               ->where('organization_id','=',auth()->user()->organization_id)
                               ->first();
        } else {
            return $this->model->where('uuid',$id)
                               ->where('organization_id','=',auth()->user()->organization_id)
                               ->first();
        }
    }

    public function findAll()
    {
        return $this->model->where('organization_id','=',auth()->user()->organization_id)->get();
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

    }

    public function remove($id, bool $forceDelete = false)
    {
        if(!$forceDelete){
            $bussiness = $this->findId($id);
            return $bussiness->delete();
        } else {
            $bussiness = $this->findId($id);
            return $bussiness->forceDelete();
        }
    }

    public function get(array $conditions, array $coluns = [], bool $join = false, bool $first = false)
    {
        $query = $this->model;

        if(count($coluns) > 0){
            $query = $query->addSelect($coluns);
        }

       if($join){
           $query = $query->join('address_id','=','addresses.id');
       }

       if(array_key_exists('companyName',$conditions)){
           $query = $query->where('company_name','=',$conditions['companyName']);
       }
       if(array_key_exists('fantasyName',$conditions)){
           $query = $query->where('fantasy_name','=',$conditions['fantasyName']);
       }
       if(array_key_exists('cpfCnpj',$conditions)){
           $query = $query->where('cpf_cnpj','=',$conditions['cpfCnpj']);
       }
       if(array_key_exists('created_at_ini',$conditions) && array_key_exists('created_at_end',$conditions)){
            $query = $query->whereBetween('created_at',[$conditions['created_at_ini'],$conditions['created_at_end']]);
       }
       $query = $query->where('organization_id','=',auth()->user()->organization_id);

       if($first){
           return $query->first();
       } else {
           return $query->get();
       }
    }

    public function getModel()
    {
        return $this->model;
    }

    public function notifier(string $methodNotifier,mixed $param = null)
    {
        $serviceDispatch = App::make(DispatchNotifier::class,['classNotified' => AddressRepositoryConcrete::class,'classNotifier' => BussinessUnitRepositoryConcrete::class]);
        if(is_null($param)){
            throw new Exception('Param is Null');
        }
        switch (strtolower($methodNotifier)){
            case 'saveaddress':
                $id = $serviceDispatch->dispatchSaveAddress($param);
                return $id;
            default:
                throw new Exception('Method not found');
        }
    }

    public function thisNotifier()
    {
       return $this;
    }
}
