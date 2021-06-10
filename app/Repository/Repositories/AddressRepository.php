<?php


namespace App\Repository\Repositories;


use App\Repository\IRepository;
use App\Repository\Repository;
use App\Repository\RepositoryConcrete\AddressRepositoryConcrete;

class AddressRepository extends Repository
{

    public function createFactory(): IRepository
    {
        return new AddressRepositoryConcrete();
    }
}
