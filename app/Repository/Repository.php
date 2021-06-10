<?php


namespace App\Repository;


abstract class Repository
{
    public abstract function createFactory() : IRepository;
}
