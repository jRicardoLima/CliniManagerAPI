<?php


namespace App\Repository\RepositoryConcrete;


use App\Models\Employee;
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

class EmployeeRepositoryConcrete implements IRepository,INotifer,Serializable
{

    protected $model = null;

    public function __construct()
    {
        $this->model = App::make(ModelsFactory::class,['className' => Employee::class]);
    }

    public function notifier(string $methodNotifier,$param = null)
    {
        $addressDisptach = App::make(DispatchNotifier::class,['classNotified'=> AddressRepositoryConcrete::class,'classNotifier' => EmployeeRepositoryConcrete::class]);

        if(is_null($param)){
            throw new Exception('Param is null');
        }

        switch (strtolower($methodNotifier)){

            case 'saveaddress':
                 return $addressDisptach->dispatchSaveAddress($param);
            case 'updateaddress':
                return $addressDisptach->dispacthUpdateAddress($param->address_id,$param);
            case 'deleteaddress':
                return $addressDisptach->dispatchDeleteAddress($param);
            default:
                throw new Exception('Method no found');
        }
    }

    public function thisNotifier()
    {
        return $this;
    }

    public function findId($id, bool $uuid = false,bool $join = false, bool $serialize = false)
    {
        $query = $this->model;
        if($serialize){
            $join = true;
        }
        if($join){
            $query = $query->with(['bussinessRelation','userRelation','specialtieRelationPivot']);
        }

        if(!$uuid){
            $query = $query->where('id',$id)
                         ->where('organization_id','=',auth()->user()->organization_id)
                         ->with('addressRelation')
                         ->first();
        } else {
            $query = $query->where('uuid',$id)
                         ->where('organization_id','=',auth()->user()->organization_id)
                         ->with('addressRelation')
                         ->first();
        }
        if($serialize){
            return $this->serialize($query,null,true);
        } else {
            return $query;
        }
    }

    public function findAll(bool $join = false, bool $serialize = false)
    {
        $query = $this->model;

        if($serialize){
            $join = true;
        }

        if($join){
            $query = $query->with(['bussinessRelation','userRelation','specialtieRelationPivot']);
        }
        $query = $query->with('addressRelation')
                       ->where('organization_id','=',auth()->user()->organization_id);

        if($serialize){
            return $this->serialize($query->get(),'');
        } else {
            return $query;
        }
    }

    public function save(object $obj, bool $returnObject = false)
    {
        $employee = $this->model;

        $employee->uuid = Str::uuid();
        $employee->name = $obj->name;
        $employee->birth_date = $obj->birth_date;
        $employee->cpf_cnpj = $obj->cpf_cnpj;
        $employee->type = $obj->type;
        $employee->salary = (isset($obj->salary) && $obj->salary != null) ? formatMoneyToSql($obj->salary) : null;
        $employee->professional_register = (isset($obj->professional_register) && $obj->professional_register != null) ? $obj->professional_register : null;
        $employee->photo = (isset($obj->file) && $obj->file != null) ? $obj->file : null;
        $employee->occupation_id = $obj->occupation_id;
        $employee->address_id = $this->notifier('saveaddress',$obj)->id;
        $employee->bussiness_id = $obj->bussiness_id;
        $employee->organization_id = auth()->user()->organization_id;

        $ret = $employee->save();
        if($returnObject){
            return $employee;
        } else{
            return $ret;
        }


    }

    public function update($id, object $data)
    {
        $employee = $this->findId($id);

        $employee->name = $data->name;
        $employee->birth_date = convertDataToSql($data->birth_date);
        $employee->cpf_cnpj = $data->cpf_cnpj;
        $employee->type = $data->type;
        $employee->salary = (isset($data->salary) && $data->salary != null) ? formatMoneyToSql($data->salary) : null;
        $employee->professional_register = (isset($data->professional_register) && $data->professional_register != null) ? $data->professional_register : null;
        if(isset($data->file) && $data->file != null){
            $employee->photo = $data->file;
        }
        $employee->occupation_id = $data->occupation_id;
        $ret = $this->notifier('updateaddress',$data);
        $employee->bussiness_id = $data->bussiness_id;

        if($ret){
           return $employee->save();
        } else {
            return false;
        }


    }

    public function remove($id, bool $forceDelete = false)
    {
        if(!$forceDelete){
            $employee = $this->findId($id);

            $param = new stdClass();
            $param->id = $employee->address_id;
            $param->forceDelete = false;

            $ret = $this->notifier('deleteaddress',$param);

            if($ret){
                return $employee->delete();
            } else {
                return false;
            }
        } else {
            $employee = $this->findId($id);

            $param = new stdClass();
            $param->id = $employee->address_id;
            $param->forceDelete = true;

            $ret = $this->notifier('deleteaddress',$param);

            if($ret){
                $employee->forceDelete();
            } else {
                return false;
            }
        }
    }

    public function get(array $conditions, array $coluns = [], bool $join = false, bool $first = false, bool $serialize = false)
    {
        $query = $this->model;

        if(count($coluns) > 0){
            $query = $query->addSelect($coluns);
        }

        if($join){
            $query = $query->with(['bussinessRelation','userRelation','specialtieRelationPivot']);
        }

        if(array_key_exists('id',$conditions)){
            $query = $query->where('employee.id','=',$conditions['id']);
        }
        if(array_key_exists('name',$conditions)){
            $query = $query->where('employee.name','like','%'.$conditions['name'].'%');
        }
        if(array_key_exists('birthDate',$conditions)){
            $query = $query->where('employee.birth_date','=',$conditions['birthDate']);
        }
        if(array_key_exists('cpf_cnpj',$conditions)){
            $query = $query->where('employee.cpf_cnpj','=',$conditions['cpf_cnpj']);
        }
        if(array_key_exists('type',$conditions)){
            $query = $query->where('employee.type','=',$conditions['type']);
        }
        if(array_key_exists('salary',$conditions)){
            $query = $query->where('employee.salary','=',$conditions['salary']);
        }
        if(array_key_exists('professional_register',$conditions)){
            $query = $query->where('employee.professional_register','=',$conditions['professional_register']);
        }
        if(array_key_exists('photo',$conditions)){
            $query = $query->where('employee.photo','=',$conditions['photo']);
        }
        if(array_key_exists('bussiness_id',$conditions)){
            $query = $query->where('employee.bussiness_id','=',$conditions['bussiness_id']);
        }
        if(array_key_exists('occupation_id',$conditions)){
            $query = $query->where('employee.occupation_id', '=',$conditions['occupation_id']);
        }

        if($first){
            if($serialize){
                return $this->serialize($query->first(),'',true);
            }
            return $query->first();
        } else {
            if($serialize){
                return $this->serialize($query->get(),'');
            }
            return $query->get();
        }
    }

    public function getModel()
    {
        return $this->model;
    }

    public function serialize($data, string $type = 'json',bool $first = false)
    {
        $manyRelation = [];
        if(!$first){
            $dataEmployee = new Collection();

            foreach ($data as $key => $value){
                $employee = new stdClass();

                $employee->id = $value->id;
                $employee->uuid = $value->uuid;
                $employee->name = $value->name;
                $employee->birth_date = $value->birth_date;
                $employee->cpf_cnpj = $value->cpf_cnpj;
                $employee->type = $value->type;
                $employee->salary = $value->salary;
                $employee->professional_register = $value->professional_register;
                $employee->photo = $value->photo;
                $employee->created_at = $value->created_at;
                $employee->updated_at = $value->updated_at;
                $employee->deleted_at = $value->deleted_at;

                $employee->address_id = $value->address_id;
                $employee->address_uuid = $value->addressRelation->address_uuid;
                $employee->country = $value->addressRelation->contry;
                $employee->state = $value->addressRelation->state;
                $employee->city = $value->addressRelation->city;
                $employee->zipcode = $value->addressRelation->zipcode;
                $employee->neighborhood = $value->addressRelation->neighborhood;
                $employee->street = $value->addressRelation->street;
                $employee->number = $value->addressRelation->number;
                $employee->telphone = $value->addressRelation->telphone;
                $employee->celphone = $value->addressRelation->celphone;
                $employee->email = $value->addressRelation->email;
                $employee->address_created_at = $value->addressRelation->created_at;
                $employee->address_updated_at = $value->addressRelation->updated_at;
                $employee->address_deleted_at = $value->addressRelation->deleted_at;

                $employee->bussiness_id = $value->bussiness_id;
                $employee->bussiness_uuid = $value->bussinessRelation->uuid;
                $employee->company_name = $value->bussinessRelation->company_name;
                $employee->fantasy_name = $value->bussinessRelation->fantasy_name;
                $employee->bussiness_cpf_cnpj = $value->bussinessRelation->cpf_cnpj;
                $employee->bussiness_created_at = $value->bussinessRelation->created_at;
                $employee->bussienss_updated_at = $value->bussinessRelation->update_at;
                $employee->bussiness_deleted_at = $value->bussinessRelation->deleted_at;

                $employee->occupation_id = $value->occupation_id;
                $employee->occupation_name = $value->occupationRelation->name;

                if($value->userRelation != null){
                    $employee->user_id = $value->userRelation->id;
                    $employee->user_name = $value->userRelation->user_name;
                    $employee->user_created_at = $value->userRelation->createda_at;
                    $employee->user_updated_at = $value->userRelation->updated_at;
                    $employee->user_deleted_at = $value->userRelation->deleted_at;
                }

                if($value->specialtieRelationPivot != null){
                    foreach ($value->specialtieRelationPivot as $specialtie){
                        $manyRelation[] = [
                            'id' => $specialtie->id,
                            'uuid' => $specialtie->uuid,
                            'specialtie_name' => $specialtie->name,
                            'register_syndicate' => $specialtie->register_syndicate,
                            'specialtie_deleted_at' => $specialtie->deleted_at,
                            'specialtie_created_at' => $specialtie->created_at,
                            'specialtie_updated_at' => $specialtie->updated_at

                        ];
                    }
                    $employee->specialties = $manyRelation;
                }
                $dataEmployee->add($employee);
            }
            if($type == '' || $type == null){
                return $dataEmployee;
            } else if($type == 'md64') {
                return base64_encode($dataEmployee);
            } else if($type == 'json') {
                return $dataEmployee->jsonSerialize();
            }
        } else {
            $employee = new stdClass();
            $employee->id =$data->id;
            $employee->uuid =$data->uuid;
            $employee->name = $data->name;
            $employee->birth_date = $data->birth_date;
            $employee->cpf_cnpj = $data->cpf_cnpj;
            $employee->type = $data->type;
            $employee->salary = $data->salary;
            $employee->professional_register = $data->professional_register;
            $employee->photo = $data->photo;
            $employee->created_at = $data->created_at;
            $employee->updated_at = $data->updated_at;
            $employee->deleted_at = $data->deleted_at;

            $employee->address_id = $data->address_id;
            $employee->address_uuid = $data->addressRealtion->address_uuid;
            $employee->country = $data->addressRealtion->contry;
            $employee->state = $data->addressRealtion->state;
            $employee->city = $data->addressRealtion->city;
            $employee->zipcode = $data->addressRealtion->zipcode;
            $employee->neighborhood = $data->addressRealtion->neighborhood;
            $employee->street = $data->addressRealtion->street;
            $employee->number = $data->addressRealtion->number;
            $employee->telphone = $data->addressRealtion->telphone;
            $employee->celphone = $data->addressRealtion->celphone;
            $employee->email = $data->addressRealtion->email;
            $employee->address_created_at = $data->addressRealtion->created_at;
            $employee->address_updated_at = $data->addressRealtion->updated_at;
            $employee->address_deleted_at = $data->addressRealtion->deleted_at;

            $employee->bussiness_id = $data->bussiness_id;
            $employee->bussiness_uuid = $data->bussinessRelation->uuid;
            $employee->company_name = $data->bussinessRelation->company_name;
            $employee->fantasy_name = $data->bussinessRelation->fantasy_name;
            $employee->bussiness_cpf_cnpj = $data->bussinessRelation->cpf_cnpj;
            $employee->bussiness_created_at = $data->bussinessRelation->created_at;
            $employee->bussienss_updated_at = $data->bussinessRelation->update_at;
            $employee->bussiness_deleted_at = $data->bussinessRelation->deleted_at;

            if($data->userRelation != null){
                $employee->user_id = $data->userRelation->id;
                $employee->user_name = $data->userRelation->user_name;
                $employee->user_created_at = $data->userRelation->createda_at;
                $employee->user_updated_at = $data->userRelation->updated_at;
                $employee->user_deleted_at = $data->userRelation->deleted_at;
            }

            if($data->specialtieRelationPivot != null){
                foreach ($data->specialtieRelationPivot as $specialtie){
                    $manyRelation[] = [
                        'id' => $specialtie->id,
                        'uuid' => $specialtie->uuid,
                        'specialtie_name' => $specialtie->name,
                        'register_syndicate' => $specialtie->register_syndicate,
                        'specialtie_deleted_at' => $specialtie->deleted_at,
                        'specialtie_created_at' => $specialtie->created_at,
                        'specialtie_updated_at' => $specialtie->updated_at

                    ];
                }
                $employee->specialties = $manyRelation;
            }

            if($type == '' || $type == null){
                return $employee;
            } else if($type == 'md64') {
                return base64_encode($employee);
            } else if($type == 'json') {
                return json_encode($employee);
            }
        }
    }
}
