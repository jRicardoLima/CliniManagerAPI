<?php


namespace App\Repository;


use Illuminate\Container\Container;
use ReflectionClass;

class RepositoryFactory
{
  protected $model = null;

  public function __construct(IRepository $model)
  {
    $this->model = $model;
  }

    public function factory()
    {
        try{
            $reflection = new ReflectionClass($this->model);
            $name = $reflection->getName();
            return new $name();
        }catch (\Exception $exc){
            return $exc->getMessage();
        }

    }
}
