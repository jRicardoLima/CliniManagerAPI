<?php


namespace App\Repository\Repositories;


use App\Repository\IRepository;
use App\Repository\Repository;
use App\Repository\RepositoryConcrete\SpecialtieRepositoryConcrete;
use Illuminate\Container\Container;

class SpecialtieRepository extends Repository
{

    public function createFactory(): IRepository
    {
       return new SpecialtieRepositoryConcrete();
    }
}
