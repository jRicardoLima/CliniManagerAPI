<?php


namespace App\Repository\Repositories;


use App\Repository\IRepository;
use App\Repository\Repository;
use App\Repository\RepositoryConcrete\SpecialtieRepositoryConcrete;

class SpecialtieRepository extends Repository
{

    public function createFactory(): IRepository
    {
       return new SpecialtieRepositoryConcrete();
    }
}
