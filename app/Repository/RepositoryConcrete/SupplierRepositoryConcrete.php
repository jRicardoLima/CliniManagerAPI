<?php


namespace App\Repository\RepositoryConcrete;


use App\Models\FactoriesModels\ModelsFactory;
use App\Models\Supplier;
use App\Repository\IRepository;
use App\Repository\MediatorRepository\DispatchNotifier;
use App\Repository\MediatorRepository\INotifer;
use App\Repository\Serializable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;
use function PHPUnit\Framework\exactly;

class SupplierRepositoryConcrete implements INotifer, Serializable, IRepository
{
    protected $model = null;
    private $getDataRelations = false;
    private $isJoinRelation = [];

    public function __construct()
    {
        $this->model = App::make(ModelsFactory::class,['className' => Supplier::class]);
    }

    public function notifier(string $methodNotifier, $param = null)
    {
       $serviceDispatchAddress = App::make(DispatchNotifier::class,['classNotified' => AddressRepositoryConcrete::class,'classNotifier' => SupplierRepositoryConcrete::class]);

       if(is_null($param)){
           throw new \Exception('Param is Null');
       }

       switch (strtolower($methodNotifier)){
           case 'saveaddress': return $serviceDispatchAddress->dispatchSaveAddress($param);

           case 'updateaddress': return $serviceDispatchAddress->dispacthUpdateAddress($param->address_id,$param);

           case 'deleteaddress': return $serviceDispatchAddress->dispatchDeleteAddress($param);

           default: throw new \Exception('Notifier method not found');
       }
    }

    public function thisNotifier()
    {
        return $this;
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
                           ->with('addressRelation')
                           ->first();
        } else {
            $query = $query->where('uuid','=',$id)
                           ->where('organization_id','=',auth()->user()->organization_id)
                           ->with('addressRelation')
                           ->first();
        }

        if($serialize){
            return $this->serialize($query,null,true);
        }
        return $query;
    }

    public function findAll(bool $join = false, bool $serialize = false)
    {
        if($join){
            $this->getDataRelations = true;
        }

        $ret = $this->model->where('organization_id',auth()->user()->organization_id)->with('addressRelation')->get();

        if($serialize){
            return $this->serialize($ret,'');
        }
        return $ret;
    }

    public function save(object $obj, bool $returnObject = false)
    {
        $supplier = $this->model;

        $supplier->uuid = Str::uuid();
        $supplier->cpf_cnpj = $obj->cpf_cnpj;
        $supplier->company_name = $obj->company_name;
        $supplier->fantasy_name = isset($obj->fantasy_name) && $obj->fantasy_name !== null ? $obj->fantasy_name : null;
        $supplier->address_id = $this->notifier('saveaddress',$obj)->id;
        $supplier->organization_id = auth()->user()->organization_id;

        $ret = $supplier->save();

        if($returnObject){
            return $supplier;
        }
        return $ret;
    }

    public function update($id, object $data)
    {
        $supplier = $this->findId($id);

        $supplier->cpf_cnpj = $data->cpf_cnpj;
        $supplier->company_name = $data->company_name;
        $supplier->fantasy_name = isset($data->fantasy_name) && $data->fantasy_name !== null ? $data->fantasy_name : null;
        $ret = $this->notifier('updateAddress',$data);

        if($ret){
            return $supplier->save();
        }
        return false;
    }

    public function remove($id, bool $forceDelete = false)
    {
        $supplier = $this->findId($id);
        $param = new \stdClass();

        $param->id = $supplier->address_id;

        if(!$forceDelete){
           $param->forceDelete = false;

           $ret = $this->notifier('deleteaddress',$param);

           if($ret){
               return $supplier->delete();
           }
           return false;
        }
        $param->forceDelete = true;

        $ret = $this->notifier('deleteaddress',$param);

        if($ret){
            return $supplier->forceDelete();
        }
        return false;
    }

    public function get(array $conditions, array $coluns = [], bool $join = false, bool $first = false, bool $serialize = false)
    {
        $query = $this->model;

        if(count($coluns) > 0){
            $query = $query->addSelect($coluns);
        }

        if ($join){
            $this->getDataRelations = true;
        }

        if(array_key_exists('id',$conditions)){
            $query = $query->where('suppliers.id','=',$conditions['id']);
        }
        if(array_key_exists('company_name',$conditions)){
            $query = $query->where('suppliers.company_name','like','%'.$conditions['company_name'].'%');
        }
        if(array_key_exists('fantasy_name',$conditions)){
            $query = $query->where('suppliers.fantasy_name','like','%'.$conditions['fantasy_name'].'%');
        }
        if(array_key_exists('cpf_cnpj',$conditions)){
            $query = $query->where('suppliers.cpf_cnpj','=',$conditions['cpf_cnpj']);
        }
        $query = $query->where('suppliers.organization_id','=',auth()->user()->organization_id);

        if($first){
            if($serialize){
                return $this->serialize($query->firtst(),'',true);
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

    public function serialize($data, string $type = 'json', bool $first = false)
    {
        if(!$first){
            $dataSuppliers = new Collection();

            foreach ($data as $key => $value){
                $suppliers = new \stdClass();

                $suppliers->id = $value->id;
                $suppliers->uuid = $value->uuid;
                $suppliers->company_name = $value->company_name;
                $suppliers->fantasy_name = $value->fantasy_name;
                $suppliers->cpf_cnpj = $value->cpf_cnpj;
                $suppliers->address_id = $value->address_id;
                $suppliers->address_uuid = $value->addressRelation->uuid;
                $suppliers->country = $value->addressRelation->contry;
                $suppliers->state = $value->addressRelation->state;
                $suppliers->city = $value->addressRelation->city;
                $suppliers->zipcode = $value->addressRelation->zipcode;
                $suppliers->neighborhood = $value->addressRelation->neighborhood;
                $suppliers->street = $value->addressRelation->street;
                $suppliers->number = $value->addressRelation->number;
                $suppliers->telphone = $value->addressRelation->telphone;
                $suppliers->celphone = $value->addressRelation->celphone;
                $suppliers->email = $value->addressRelation->email;
                $suppliers->observation = $value->addressRelation->observation;

                $dataSuppliers->add($suppliers);
            }
            return $this->returnTypeSerialize($type,$dataSuppliers);
        }
        $supplier = new \stdClass();
        $supplier->id = $data->id;
        $supplier->uuid = $data->uuid;
        $supplier->company_name = $data->company_name;
        $supplier->fantasy_name = $data->fanstasy_name;;
        $supplier->cpf_cnpj = $data->cpf_cnpj;
        $supplier->address_id = $data->address_id;
        $supplier->address_uuid = $data->addressRelation->uuid;
        $supplier->country = $data->addressRelation->contry;
        $supplier->state = $data->addressRelation->state;
        $supplier->city = $data->addressRelation->city;
        $supplier->zipcode = $data->addressRelation->zipcode;
        $supplier->neighborhood = $data->addressRelation->neighborhood;
        $supplier->street = $data->addressRelation->street;
        $supplier->number = $data->addressRelation->number;
        $supplier->telphone = $data->addressRelation->telphone;
        $supplier->celphone = $data->addressRelation->celphone;
        $supplier->email = $data->addressRelation->email;
        $supplier->observation = $data->addressRelation->observation;

        return $this->returnTypeSerialize($type,$supplier);
    }

    private function returnTypeSerialize($type,$data){
        if($type == null || $type == ''){
            return $data;
        }
        return json_encode($data);
    }
}
