<?php


namespace App\Repository;


interface IRepository
{
    public function findId($id,bool $uuid = false,bool $join = false,bool $serialize = false);
    public function findAll(bool $join = false,bool $serialize = false,int $limit = 0);
    public function save(object $obj, bool $returnObject = false);
    public function saveInLoop(object $obj,bool $returnObject = false);
    public function update($id,object $data);
    public function remove($id,bool $forceDelete = false);
    public function get(array $conditions,array $coluns = [],bool $join = false,bool $first = false,bool $serialize = false,int $limit = 0);
    public function getModel();
}
