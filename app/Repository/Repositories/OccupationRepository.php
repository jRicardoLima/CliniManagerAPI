<?php


namespace App\Repository\Repositories;


use App\Repository\IRepository;
use App\Repository\Repository;
use App\Repository\RepositoryConcrete\OcuppationRepositoryConcrete;
use Illuminate\Container\Container;

class OccupationRepository extends Repository
{

    public function createFactory(): IRepository
    {
        return new OcuppationRepositoryConcrete();
    }
}
