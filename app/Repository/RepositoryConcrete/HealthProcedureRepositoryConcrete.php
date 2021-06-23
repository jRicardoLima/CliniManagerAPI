<?php


namespace App\Repository\RepositoryConcrete;

use App\Models\FactoriesModels\ModelsFactory;
use App\Models\HealthProcedure;
use App\Repository\IRepository;
use App\Repository\MediatorRepository\INotifer;
use App\Repository\Serializable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class HealthProcedureRepositoryConcrete implements IRepository, Serializable, INotifer
{
    protected $model = null;
    private $getDataRelations = false;
    private $isJoinRelation = [];

    public function __construct()
    {
        $this->model = App::make(ModelsFactory::class,['className' => HealthProcedure::class]);
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

    public function findAll(bool $join = false, bool $serialize = false)
    {
        $query = $this->model;

        if($join){
            $this->getDataRelations = true;
        }

        $query = $query->where('organization_id',auth()->user()->organization_id);

        if($serialize){
            return $this->serialize($query->get(),'');
        }
        return $query->get();
    }

    public function save(object $obj, bool $returnObject = false)
    {
        $healthProcedure = $this->model;

        $healthProcedure->uuid = Str::uuid();
        $healthProcedure->name = $obj->name;
        $healthProcedure->register_syndicate = isset($obj->syndicate_code) && $obj->syndicate_code != null ? $obj->syndicate_code : null;
        $healthProcedure->organization_id = auth()->user()->organization_id;

        $ret = $healthProcedure->save();

        if(isset($obj->linkSpecialties) && count($obj->linkSpecialties) > 0){
            $ids = RequestAllCustom($obj->linkSpecialties,function($item,$key){
               return $item['id'];
            });
            $healthProcedure->specialtieRelationPivot()->sync($ids);
        }

        if($returnObject){
            return $healthProcedure;
        }
        return $ret;
    }

    public function update($id, object $data)
    {
        $healthProcedure = $this->findId($id);

        $healthProcedure->name = $data->name;
        $healthProcedure->register_syndicate = isset($data->syndicate_code) && $data->syndicate_code != null ? $data->syndicate_code : null;

        $ret = $healthProcedure->save();
        if(isset($data->linkSpecialtie)){
            if(count($data->linkSpecialtie) > 0){
                $ids = RequestAllCustom($data->linkSpecialtie,function($item,$key){

                        foreach ($item as $keyy => $value){
                            if($keyy == 'id' || $keyy == 'specialtie_id'){
                                return $value;
                            }
                        }
                });

                $healthProcedure->specialtieRelationPivot()->sync($ids);
            } else {
                $healthProcedure->specialtieRelationPivot()->sync([]);
            }
        }
        if($ret){
            return true;
        }

        return false;
    }

    public function remove($id, bool $forceDelete = false)
    {
        $healthProcedure = $this->findId($id);

        if(!$forceDelete){
            $healthProcedure->specialtieRelationPivot()->sync([]);
            return $healthProcedure->delete();
        }
        $healthProcedure->specialtieRelationPivot()->sync([]);
        return $healthProcedure->forceDelete();
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
            $query = $query->where('health_procedures.id','=',$conditions['id']);
        }
        if(array_key_exists('name',$conditions)){
            $query = $query->where('health_procedures.name','like','%'.$conditions['name'].'%');
        }
        if(array_key_exists('syndicate_code',$conditions)){
            $query = $query->where('health_procedures.register_syndicate','=',$conditions['syndicate_code']);
        }
        if(array_key_exists('cod_specialtie',$conditions)){
            $query = $query->join('health_procedure_specialties','health_procedure_specialties.health_procedure_id','=','health_procedures.id')
                           ->addSelect('health_procedures.*')
                           ->where('health_procedure_specialties.specialtie_id','=',$conditions['cod_specialtie']);
            $this->isJoinBuilder(true,['health_procedure_specialties']);
        }
        $query = $query->where('health_procedures.organization_id','=',auth()->user()->organization_id);
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
            $dataHealthProcedures = new Collection();

            foreach ($data as $key => $value){
                $healthProcedure = new \stdClass();

                $healthProcedure->id = $value->id;
                $healthProcedure->name = $value->name;
                $healthProcedure->register_syndicate = $value->register_syndicate;
                $healthProcedure->created_at = $value->created_at;
                $healthProcedure->updated_at = $value->updated_at;
                $healthProcedure->deleted_at = $value->deleted_at;

                if($this->getDataRelations){
                    $dataSpecialties = $this->getDataRelationMany($value->specialtieRelationPivot,$value);
                    $healthProcedure->specialties = $dataSpecialties;
                }
                $dataHealthProcedures->add($healthProcedure);
            }
            return $this->typeReturnSerialize($type,$dataHealthProcedures);
        }
        $healthProcedure = new \stdClass();

        $healthProcedure->id = $data->health_procedure_id ?? $data->id;
        $healthProcedure->uuid = $data->uuid;
        $healthProcedure->name = $data->name;
        $healthProcedure->register_syndicate = $data->register_syndicate;
        $healthProcedure->created_at = $data->created_at;
        $healthProcedure->updated_at = $data->updated_at;
        $healthProcedure->deleted_at = $data->deleted_at;

        if($this->getDataRelations){
            $healthProcedure->specialties = $this->getDataRelationMany($data->specialtieRelationPivot,$healthProcedure->id);
        }
        return $this->typeReturnSerialize($type,$healthProcedure);
    }

    private function typeReturnSerialize($type,$data){
        if($type == null || $type == ''){
            return $data;
        }
        return json_encode($data);
    }
    private function getDataRelationMany($relation,$value)
    {
        $data = [];
        if(count($this->isJoinRelation) > 0 && $this->isJoinRelation['isJoin']){
            if($this->isJoinRelation['joins'][0] == 'health_procedure_specialties'){
                    $results = $this->getDataHealthProcedureSpecialtiesPivot($value->id);
                    if($results != null && count($results) > 0){
                        foreach ($results as $item){
                            $data[] = [
                                'specialtie_id' => $item->id,
                                'specialtie_name' => $item->name,
                                'specialtie_register_syndicate' => $item->register_syndicate
                            ];
                        }
                    }
                    return $data;
            }
        }
        if($relation != null){
            foreach ($relation as $item){
                if($item->pivot->health_procedure_id == $value->id){
                   $data[] = [
                       'specialtie_id' => $item->id,
                       'specialtie_name' => $item->name,
                       'specialtie_register_syndicate' => $item->register_syndicate
                   ];
                }
            }
        }
        return $data;
    }
    private function isJoinBuilder(bool $isJoin,array $joins)
    {
        return $this->isJoinRelation = ['isJoin' => $isJoin,'joins' => $joins];
    }
    public function getDataHealthProcedureSpecialtiesPivot($idHealthProcedure)
    {
        $query = "SELECT specialties.* FROM health_procedure_specialties,specialties,health_procedures
                 WHERE health_procedure_specialties.specialtie_id = specialties.id
                 AND health_procedure_specialties.health_procedure_id = health_procedures.id
                 AND health_procedures.id = :id";

        return DB::select($query,['id' => $idHealthProcedure]);
    }
}
