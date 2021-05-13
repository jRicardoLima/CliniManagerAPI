<?php


namespace App\Repository\MediatorRepository;


interface INotifer
{
    public function notifier(string $methodNotifier,mixed $param = null);
    public function thisNotifier();
}
