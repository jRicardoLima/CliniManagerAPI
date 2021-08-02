<?php


namespace App\Repository;


use Illuminate\Container\Container;

abstract class Repository
{
    abstract public function createFactory() : IRepository;
}
