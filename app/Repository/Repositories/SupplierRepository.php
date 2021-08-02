<?php


namespace App\Repository\Repositories;


use App\Repository\IRepository;
use App\Repository\Repository;
use App\Repository\RepositoryConcrete\SupplierRepositoryConcrete;
use Illuminate\Container\Container;

class SupplierRepository extends Repository
{

    public function createFactory(): IRepository
    {
       return new SupplierRepositoryConcrete();
    }
}
