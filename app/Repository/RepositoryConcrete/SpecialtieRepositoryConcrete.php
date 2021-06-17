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
    protected $getDataPivot = false;
    public function __construct()
    {
        $this->model = App::make(ModelsFactory::class, ['className' => Specialtie::class]);
    }

    public function findId($id, bool $uuid = false, bool $join = false, bool $serialize = false)
    {
        $query = $this->model;

        if ($join) {
            $this->getDataPivot = true;
            $query = $query->with(['employeeRelationPivot']);
        }
        if (!$uuid) {
            $query = $query->where('id', $id)
                ->where('organization_id', '=', auth()->user()->organization_id)
                ->first();
        } else {
            $query = $query->where('uuid', $id)
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
            $this->getDataPivot = true;
        }
        $query = $query->where('organization_id', '=',auth()->user()->organization_id);

        if ($serialize) {
            return $this->serialize($query->get(),'');
        }
        return $query;
    }

    public function save(object $obj, bool $returnObject = false)
    {
        $specialtie = $this->model;

        $specialtie->uuid = Str::uuid();
        $specialtie->name = $obj->name;
        $specialtie->register_syndicate = (isset($obj->syndicate_code) && $obj->syndicate_code != null) ? $obj->syndicate_code : null;
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
        $specialtie->register_syndicate = (isset($data->syndicate_code) && $data->syndicate_code != null) ? $data->syndicate_code : null;

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
        if (!$forceDelete) {
            $specialtie = $this->findId($id,false,false,false);

            if($specialtie->employeeRelationPivot != null){
                $specialtie->employeeRelationPivot()->sync([]);
            }

            return $specialtie->delete();
        }
        $specialtie = $this->findId($id);

        if($specialtie->employeeRelationPivot != null){
            $specialtie->employeeRelationPivot()->sync([]);
        }


        return $specialtie->forceDelete();
    }

    public function get(array $conditions, array $coluns = [], bool $join = false, bool $first = false, bool $serialize = false)
    {
        $query = $this->model;

        if (count($coluns) > 0) {
            $query = $query->addSelect($coluns);
        }

        if ($join) {
            $this->getDataPivot = true;
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
                           ->where('employee_specialties.employee_id','=',$conditions['cod_employee']);
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
        $manyRelation = new Collection();

        if (!$first) {
            $dataSpecialties = new Collection();

            foreach ($data as $key => $value) {
                $specialties = new \stdClass();
                $employeeData = [];
                $specialties->id = (isset($value->specialtie_id)) ? $value->specialtie_id : $value->id;
                $specialties->uuid = $value->uuid;
                $specialties->name = $value->name;
                $specialties->register_syndicate = $value->register_syndicate;
                $specialties->deleted_at = $value->deleted_at;
                $specialties->created_at = $value->created_at;
                $specialties->updated_at = $value->updated_at;

                if($this->getDataPivot){

                    $employeeData = $this->controlRelations('employeespecialtiesrelation',$value);
                }
                $specialties->employees = $employeeData;
                $dataSpecialties->add($specialties);
            }

            if ($type == '' || $type == null) {
                return $dataSpecialties;
            } else if ($type == 'md64') {
                return base64_encode($dataSpecialties);
            } else if ($type == 'json') {
                return $dataSpecialties->jsonSerialize();
            }
        }
        $specialtie = new \stdClass();
        $specialtie->id = $value->id;
        $specialtie->uuid = $value->uuid;
        $specialtie->name = $value->name;
        $specialtie->register_syndicate = $value->register_syndicate;
        $specialtie->deleted_at = $value->deleted_at;
        $specialtie->created_at = $value->created_at;
        $specialtie->updated_at = $value->updated_at;

        if ($specialtie->employeeRelationPivot != null) {
            foreach ($specialtie->employeeRelationPivot as $employee) {
                $relation = [
                    'employee_id' => $employee->id,
                    'employee_uuid' => $employee->uuid,
                    'employee_name' => $employee->name,
                    'employee_birth_date' => $employee->birth_date,
                    'employee_salary' => $employee->salary,
                    'employee_type' => $employee->type,
                    'employee_professional_register' => $employee->professional_register,
                    'employee_photo' => $employee->photo
                ];
                $manyRelation->add($relation);
            }
            $specialtie->employees = $manyRelation;
        }
        if ($type == '' || $type == null) {
            return $specialtie;
        } else if ($type == 'md64') {
            return base64_encode($specialtie);
        } else if ($type == 'json') {
            return json_encode($specialtie);
        }
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

        $employeeData =  DB::select($query,['id' => $idSpecialtie]);
        $ret = [];
        if ($employeeData != null && count($employeeData) > 0) {
            foreach ($employeeData as $employee) {
                $ret[] = [
                    'employee_id' => $employee->id,
                    'employee_uuid' => $employee->uuid,
                    'employee_name' => $employee->name,
                    'employee_birth_date' => $employee->birth_date,
                    'employee_salary' => $employee->salary,
                    'employee_type' => $employee->type,
                    'employee_professional_register' => $employee->professional_register,
                    'employee_photo' => $employee->photo
                ];
            }
        }
        return $ret;
    }
}
