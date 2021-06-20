<?php


namespace App\Repository;


abstract class Repository
{
    abstract public function createFactory() : IRepository;
}
