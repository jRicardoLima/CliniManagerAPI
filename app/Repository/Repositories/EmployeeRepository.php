<?php


namespace App\Repository\Repositories;


use App\Repository\IRepository;
use App\Repository\Repository;
use App\Repository\RepositoryConcrete\EmployeeRepositoryConcrete;
use Illuminate\Container\Container;

class EmployeeRepository extends Repository
{

    public function createFactory(): IRepository
    {
        return new EmployeeRepositoryConcrete();
    }
}
