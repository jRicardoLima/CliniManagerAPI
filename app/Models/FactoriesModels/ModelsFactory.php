<?php


namespace App\Models\FactoriesModels;


use Exception;
use Illuminate\Database\Eloquent\Model;
use ReflectionClass;

class ModelsFactory
{
    protected $model = null;

    public function __construct(Model $model)
    {
        //if($model != null){
            $this->model = $model;
        //}
       // throw new Exception('model null or not exists');
    }

    public function factory()
    {
        try {
            $reflection = new ReflectionClass($this->model);
            $name = $reflection->getName();
            return new $name;
        } catch (Exception $e){
            return $e->getMessage();
        }
    }
}
