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
        $address->contry = $obj->country;
        $address->state = $obj->state;
        $address->zipcode = $obj->zipcode;
        $address->city = $obj->city;
        $address->neighborhood = $obj->neighborhood;
        $address->street = $obj->street;
        $address->number = $obj->number;
        $address->telphone = $obj->telphone;
        $address->celphone = $obj->celphone;
        $address->email = $obj->email;
        $address->observation = $obj->observation;
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

        $address->contry = $data->country;
        $address->state = $data->state;
        $address->zipcode = $data->zipcode;
        $address->city = $data->city;
        $address->neighborhood = $data->neighborhood;
        $address->street = $data->street;
        $address->number = $data->number;
        $address->telphone = $data->telphone;
        $address->celphone = $data->celphone;
        $address->email = $data->email;
        $address->observation = $data->observation;

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
