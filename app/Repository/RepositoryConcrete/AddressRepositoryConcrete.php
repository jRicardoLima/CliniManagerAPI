<?php


namespace App\Repository\RepositoryConcrete;


use App\Models\Address;
use App\Repository\IRepository;
use App\Repository\MediatorRepository\INotified;
use Illuminate\Support\Str;

class AddressRepositoryConcrete implements IRepository,INotified
{
    protected $model;

    public function __construct()
    {
        $this->model = new Address();
    }

    public function findId($id, bool $uuid = false,bool $join = false,bool $serialize = false)
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

    public function findAll(bool $join = false,bool $serialize = false)
    {
        return $this->model->where('organization_id','=',auth()->user()->organization_id);
    }

    public function save(object $obj, bool $returnObject = false)
    {

        $address = $this->model;

        $address->uuid = Str::uuid();
        $address->contry = (isset($obj->country) && $obj->country != null) ? $obj->country : null;
        $address->state = $obj->state;
        $address->zipcode = (isset($obj->zipcode) && $obj->zipcode != null) ? $obj->zipcode : null;
        $address->city = $obj->city;
        $address->neighborhood = $obj->neighborhood;
        $address->street = $obj->street;
        $address->number = (isset($obj->number) && $obj->number != null) ? $obj->number : null;
        $address->telphone = (isset($obj->telphone) && $obj->telphone != null) ? $obj->telphone : null;
        $address->celphone = (isset($obj->celphone) && $obj->celphone != null) ? $obj->celphone : null;
        $address->email = (isset($obj->email) && $obj->email != null) ? $obj->email : null;
        $address->observation = (isset($obj->observation) && $obj->observation != null) ? $obj->observation : null;
        $address->organization_id = auth()->user()->organization_id;

        $ret = $address->save();
        if($returnObject){
            return $address;
        } else {
            return $ret;
        }

    }

    public function update($id, object $data)
    {
        $address = $this->findId($id);

        $address->contry = (isset($data->country) && $data->country != null) ? $data->country : null;
        $address->state = (isset($data->state) && $data->state != null) ? $data->state : null;
        $address->zipcode = (isset($data->zipcode) && $data->zipcode != null) ? $data->zipcode : null;
        $address->city = $data->city;
        $address->neighborhood = $data->neighborhood;
        $address->street = $data->street;
        $address->number = (isset($data->number) && $data->number != null) ? $data->number : null;
        $address->telphone = (isset($data->telphone) && $data->telphone != null) ? $data->telphone : null;
        $address->celphone = (isset($data->celphone) && $data->celphone != null) ? $data->celphone : null;
        $address->email = (isset($data->email) && $data->email != null) ? $data->email : null;
        $address->observation = (isset($data->observation) && $data->observation != null) ? $data->observation : null;

        return $address->save();
    }

    public function remove($id, bool $forceDelete = false)
    {
        if(!$forceDelete){
            $address = $this->findId($id);

            return $address->delete();
        } else {
            $address = $this->findId($id);

            return $address->forceDelete();
        }
    }

    public function get(array $conditions, array $coluns = [], bool $join = false, bool $first = false,bool $serialize = false)
    {
        $query = $this->model();

        if(count($coluns) > 0){
            $query = $query->addSelect($coluns);
        }

        if(array_key_exists('country',$conditions)){
            $query = $query->where('country', '=', $conditions['country']);
        }
        if(array_key_exists('state',$conditions)){
            $query = $query->where('state','=',$conditions['state']);
        }
        if(array_key_exists('zipcode',$conditions)){
            $query = $query->where('zipcode','=',$conditions['zipcode']);
        }
        if(array_key_exists('city',$conditions)){
            $query = $query->where('city','=',$conditions['city']);
        }
        if(array_key_exists('neighborhood ',$conditions)){
            $query = $query->where('neighborhood ','=',$conditions['neighborhood']);
        }
        if(array_key_exists('street',$conditions)){
            $query = $query->where('street','=',$conditions['street']);
        }
        if(array_key_exists('number',$conditions)){
            $query = $query->where('number','=',$conditions['number']);
        }
        if(array_key_exists('telphone',$conditions)){
            $query = $query->where('telphone','=',$conditions['telphone']);
        }
        if(array_key_exists('celphone',$conditions)){
            $query = $query->where('celphone','=',$conditions['celphone']);
        }
        if(array_key_exists('email',$conditions)){
            $query = $query->where('email','=',$conditions['email']);
        }
        if(array_key_exists('observation',$conditions)){
            $query = $query->where('observation','=',$conditions['observation']);
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

    public function notified()
    {
        return $this;
    }

}
