<?php


namespace App\Repository\RepositoryConcrete;


use App\Models\FactoriesModels\ModelsFactory;
use App\Models\Specialtie;
use App\Repository\IRepository;
use App\Repository\MediatorRepository\INotifer;
use App\Repository\Serializable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;

class SpecialtieRepositoryConcrete implements IRepository,INotifer,Serializable
{

    protected $model = null;

    public function __construct()
    {
        $this->model = App::make(ModelsFactory::class, ['className' => Specialtie::class]);
    }

    public function findId($id, bool $uuid = false, bool $join = false, bool $serialize = false)
    {
        $query = $this->model;

        if ($serialize) {
            $join = true;
        }
        if ($join) {
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

        if ($serialize) {
            $join = true;
        }

        if ($join) {
            $query = $query->with(['employeeRelationPivot']);
        }
        $query = $query->where('organization_id', '=', auth()->user()->organization_id);

        if ($serialize) {
            return $this->serialize($query);
        }
        return $query;
    }

    public function save(object $obj, bool $returnObject = false)
    {
        $specialtie = $this->model;

        $specialtie->uuid = Str::uuid();
        $specialtie->name = $obj->name;
        $specialtie->register_syndicate = (isset($obj->register_syndicate) && $obj->register_syndicate != null) ? $obj->register_syndicate : null;

        $ret = $specialtie->save();

        if (isset($obj->healthProfessionals) && count($obj->healthProfessionals) > 0) {
            $ids = RequestAllCustom($obj->heathProfessionals, function ($item, $key) {
                if ($key == 'id') {
                    return $item;
                }
            });
            $specialtie->employeeRelationPivot()->sync([$ids]);
        }

        if ($returnObject) {
            return $specialtie;
        } else {
            return $ret;
        }
    }

    public function update($id, object $data)
    {
        $specialtie = $this->model;

        $specialtie->name = $data->name;
        $specialtie->register_syndicate = (isset($data->register_syndicate) && $data->register_syndicate != null) ? $data->register_syndicate : null;

        $ret = $specialtie->save();

        if (isset($data->healthProfessional)) {
            $ids = RequestAllCustom($data->heathProfessionals, function ($item, $key) {
                if ($key == 'id') {
                    return $item;
                }
            });
            $specialtie->employeeRelationPivot()->sync([$ids]);
        }

        if ($ret) {
            return true;
        }
        return false;

    }

    public function remove($id, bool $forceDelete = false)
    {
        if (!$forceDelete) {
            $specialtie = $this->findId($id);

            $specialtie->employeeRelationPivot()->sync([]);

            return $specialtie->delete();
        }
        $specialtie = $this->findId($id);

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
            $query = $query->with(['employeeRelationPivot']);
        }

        if (array_key_exists('id', $conditions)) {
            $query = $query->where('specialties.id', '=', $conditions['id']);
        }
        if (array_key_exists('name', $conditions)) {
            $query = $query->where('specialties.name', 'like', '%' . $conditions['name'] . '%');
        }
        if (array_key_exists('register_syndicate', $conditions)) {
            $query = $query->where('specialties.register_syndicate', '=', $conditions['register_syndicate']);
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

                $specialties->id = $value->id;
                $specialties->uuid = $value->uuid;
                $specialties->name = $value->name;
                $specialties->register_syndicate = $value->register_syndicate;
                $specialties->deleted_at = $value->deleted_at;
                $specialties->created_at = $value->created_at;
                $specialties->updated_at = $value->updated_at;

                if ($specialties->employeeRelationPivot != null) {
                    foreach ($specialties->employeeRelationPivot as $employee) {
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
                    $specialties->employees = $manyRelation;
                }
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
}
