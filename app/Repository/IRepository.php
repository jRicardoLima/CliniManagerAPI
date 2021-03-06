<?php


namespace App\Repository;


interface IRepository
{
    public function findId($id,bool $uuid = false);
    public function findAll();
    public function save(object $obj, bool $returnObject = false);
    public function update($id,object $data);
    public function remove($id,bool $forceDelete = false);
    public function get(array $conditions,array $coluns = [],bool $join = false,bool $first = false);
    public function getModel();
}
