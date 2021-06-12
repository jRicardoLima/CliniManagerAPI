<?php


namespace App\Repository;


interface Serializable
{
    public function serialize($data, string $type = 'json',bool $first = false);
}
