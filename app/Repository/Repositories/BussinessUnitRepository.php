<?php


namespace App\Repository\Repositories;


use App\Repository\IRepository;
use App\Repository\Repository;
use App\Repository\RepositoryConcrete\BussinessUnitRepositoryConcrete;

class BussinessUnitRepository extends Repository
{

    public function createFactory(): IRepository
    {
        return new BussinessUnitRepositoryConcrete();
    }
}
