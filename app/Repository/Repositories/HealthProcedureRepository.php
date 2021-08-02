<?php


namespace App\Repository\Repositories;


use App\Repository\IRepository;
use App\Repository\Repository;
use App\Repository\RepositoryConcrete\HealthProcedureRepositoryConcrete;
use Illuminate\Container\Container;

class HealthProcedureRepository extends Repository
{

    public function createFactory(): IRepository
    {
        return new HealthProcedureRepositoryConcrete();
    }
}
