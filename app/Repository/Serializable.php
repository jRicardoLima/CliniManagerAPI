<?php


namespace App\Repository;


interface Serializable
{
    public function serialize(mixed $data, string $type = 'json');
}
