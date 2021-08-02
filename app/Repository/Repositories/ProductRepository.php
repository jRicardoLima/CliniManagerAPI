<?php


namespace App\Repository\Repositories;


use App\Repository\IRepository;
use App\Repository\Repository;
use App\Repository\RepositoryConcrete\ProductRepositoryConcrete;
use Illuminate\Container\Container;

class ProductRepository extends Repository
{

    public function createFactory(): IRepository
    {
        return new ProductRepositoryConcrete();
    }
}
