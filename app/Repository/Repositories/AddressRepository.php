<?php


namespace App\Repository\Repositories;


use App\Repository\IRepository;
use App\Repository\Repository;
use App\Repository\RepositoryConcrete\AddressRepositoryConcrete;
use Illuminate\Container\Container;

class AddressRepository extends Repository
{

    public function createFactory(): IRepository
    {
        return new AddressRepositoryConcrete();
    }
}
