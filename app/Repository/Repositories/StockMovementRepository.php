<?php


namespace App\Repository\Repositories;


use App\Repository\IRepository;
use App\Repository\Repository;
use App\Repository\RepositoryConcrete\StockMovementRepositoryConcrete;
use Illuminate\Container\Container;

class StockMovementRepository extends Repository
{

    public function createFactory(): IRepository
    {
        return new StockMovementRepositoryConcrete();
    }
}
