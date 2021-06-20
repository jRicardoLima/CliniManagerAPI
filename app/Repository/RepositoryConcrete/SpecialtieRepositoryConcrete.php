<?php


namespace App\Repository\RepositoryConcrete;

use App\Models\FactoriesModels\ModelsFactory;
use App\Models\Specialtie;
use App\Repository\IRepository;
use App\Repository\MediatorRepository\INotifer;
use App\Repository\Serializable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SpecialtieRepositoryConcrete implements IRepository,INotifer,Serializable
{

    protected $model = null;
    private $getDataRelations = false;
    private $isJoinRelation = [];
    public function __construct()
    {

        $this->model = App::make(ModelsFactory::class, ['className' => Specialtie::class]);
    }

    public function findId($id, bool $uuid = false, bool $join = false, bool $serialize = false)
    {
        $query = $this->model;

        if ($join) {
            $this->getDataRelations = true;
            //$query = $query->with(['employeeRelationPivot']);
        }
        if (!$uuid) {
            $query = $query->where('id','=', $id)
                ->where('organization_id', '=', auth()->user()->organization_id)
                ->first();
        } else {
            $query = $query->where('uuid','=', $id)
                           ->where('organization_id', '=', auth()->user()->organization_id)
                           ->first();
        }

        if ($serialize) {
            return $this->serialize($query, null, '');
        }
        return $query;
    }

    public function findAll(bool $join = false, bool $serialize = false)
    {
        $query = $this->model;

        if($join){
            $this->getDataRelations = true;
        }
        $query = $query->where('organization_id', '=',auth()->user()->organization_id);

        if ($serialize) {
            return $this->serialize($query->get(),'');
        }
        return $query->get();
    }

    public function save(object $obj, bool $returnObject = false)
    {
        $specialtie = $this->model;

        $specialtie->uuid = Str::uuid();
        $specialtie->name = $obj->name;
        $specialtie->register_syndicate = isset($obj->syndicate_code) && $obj->syndicate_code != null ? $obj->syndicate_code : null;
        $specialtie->organization_id = auth()->user()->organization_id;
        $ret = $specialtie->save();

        if (isset($obj->linkEmployee) && count($obj->linkEmployee) > 0) {
            $ids = RequestAllCustom($obj->linkEmployee, function ($item, $key) {
               return $item['id'];
            });
            $specialtie->employeeRelationPivot()->sync($ids);
        }

        if ($returnObject) {
            return $specialtie;
        } else {
            return $ret;
        }
    }

    public function update($id, object $data)
    {
        $specialtie = $this->findId($id);

        $specialtie->name = $data->name;
        $specialtie->register_syndicate = isset($data->syndicate_code) && $data->syndicate_code != null ? $data->syndicate_code : null;

        $ret = $specialtie->save();
        if (isset($data->linkEmployee)) {
            if(count($data->linkEmployee) > 0){
                $ids = RequestAllCustom($data->linkEmployee, function ($item, $key) {

                      foreach ($item as $keyy => $value){
                          if($keyy == 'employee_id' || $keyy == 'id'){
                              return $value;
                          }
                      }
                });
                $specialtie->employeeRelationPivot()->sync($ids);
            }else{
                $specialtie->employeeRelationPivot()->sync([]);
            }
        }
        if ($ret) {
            return true;
        }
        return false;
    }

    public function remove($id, bool $forceDelete = false)
    {
        $specialtie = $this->findId($id,false,false,false);

        if (!$forceDelete) {
            $specialtie->employeeRelationPivot()->sync([]);
            return $specialtie->delete();
        }

        $specialtie->employeeRelationPivot()->sync([]);
        return $specialtie->forceDelete();
    }

    public function get(array $conditions, array $coluns = [], bool $join = false, bool $first = false, bool $serialize = false)
    {
        $query = $this->model;

        if (count($coluns) > 0) {
            $query = $query->addSelect($coluns);
        }

        if ($join) {
            $this->getDataRelations = true;
            $query = $query->with(['employeeRelationPivot']);
        }

        if (array_key_exists('id', $conditions)) {
            $query = $query->where('specialties.id', '=', $conditions['id']);
        }
        if (array_key_exists('name', $conditions)) {
            $query = $query->where('specialties.name','like','%'.$conditions['name'].'%');
        }
        if (array_key_exists('syndicate_code', $conditions)) {
            $query = $query->where('specialties.register_syndicate', '=', $conditions['syndicate_code']);
        }
        if(array_key_exists('cod_employee',$conditions)){
            $query = $query->join('employee_specialties','employee_specialties.specialtie_id','=','specialties.id')
                           ->addSelect('specialties.*')
                           ->where('employee_specialties.employee_id','=',$conditions['cod_employee']);
            $this->isJoinBuilder(true,['employee_specialties']);
        }

        if ($first) {
            if ($serialize) {
                return $this->serialize($query->first(), '', true);
            }
            return $query->first();
        }
        if ($serialize) {
            return $this->serialize($query->get(), '',);
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
        if (!$first) {
            $dataSpecialties = new Collection();

            foreach ($data as $key => $value) {
                $specialties = new \stdClass();
                $specialties->id = isset($value->specialtie_id) ? $value->specialtie_id : $value->id;
                $specialties->uuid = $value->uuid;
                $specialties->name = $value->name;
                $specialties->register_syndicate = $value->register_syndicate;
                $specialties->deleted_at = $value->deleted_at;
                $specialties->created_at = $value->created_at;
                $specialties->updated_at = $value->updated_at;

                if($this->getDataRelations){
                    $dataEmployees= $this->getDataRelationMany($value->employeeRelationPivot,$value);
                    $specialties->employees = $dataEmployees;
                }
                $dataSpecialties->add($specialties);
            }
            return $this->typeReturnSerialize($type,$dataSpecialties);
        }
        $specialtie = new \stdClass();
        $specialtie->id = $data->id;
        $specialtie->uuid = $data->uuid;
        $specialtie->name = $data->name;
        $specialtie->register_syndicate = $data->register_syndicate;
        $specialtie->deleted_at = $data->deleted_at;
        $specialtie->created_at = $data->created_at;
        $specialtie->updated_at = $data->updated_at;

        if($this->getDataRelations){
            $specialtie->employees = $this->getDataRelationMany($data->employeeRelationPivot,$data);
        }
        return $this->typeReturnSerialize($type,$specialtie);
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
        return $this->isJoinRelation = ['isJoin' => true,'joins' => $joins];
    }

    private function getDataRelationMany($relation,$value){
        $data = [];
        if(count($this->isJoinRelation) && $this->isJoinRelation['isJoin']){
            if($this->isJoinRelation['joins'][0] == 'employee_specialties'){
                $result = $this->getDataPivotEmployeeSpecialties($value->id);

                if($result != null && count($result) > 0){
                    foreach ($result as $item){
                        $data[] = [
                            'employee_id' =>  $item->id,
                            'employee_uuid' =>  $item->uuid,
                            'employee_name' =>  $item->name,
                            'employee_birth_date' =>  $item->birth_date,
                            'employee_salary' =>  $item->salary,
                            'employee_type' =>  $item->type,
                            'employee_professional_register' =>  $item->professional_register,
                            'employee_photo' =>  $item->photo
                        ];
                    }
                }
                return $data;
            }
        }
        if($relation !== null){
            foreach ($relation as $item){
                if($item->pivot->specialtie_id == $value->id){
                    $data[] = [
                        'employee_id' =>  $item->id,
                        'employee_uuid' =>  $item->uuid,
                        'employee_name' =>  $item->name,
                        'employee_birth_date' =>  $item->birth_date,
                        'employee_salary' =>  $item->salary,
                        'employee_type' =>  $item->type,
                        'employee_professional_register' =>  $item->professional_register,
                        'employee_photo' =>  $item->photo
                    ];
                }
            }
        }
        return $data;
    }
    private function controlRelations(string $nameRelation,$param){
        switch (strtolower($nameRelation)){

            case 'employeespecialtiesrelation':

                if(isset($param->specialtie_id)){
                    return $this->getDataPivotEmployeeSpecialties($param->specialtie_id);
                }
                return $this->getDataPivotEmployeeSpecialties($param->id);
            default:
                return null;
        }
    }

    private function getDataPivotEmployeeSpecialties($idSpecialtie){
        $query = "SELECT employee.* FROM employee_specialties,employee, specialties
                 WHERE employee_specialties.employee_id = employee.id
                 AND employee_specialties.specialtie_id = specialties.id
                 AND employee_specialties.specialtie_id = :id";

        return DB::select($query,['id' => $idSpecialtie]);

    }
}
