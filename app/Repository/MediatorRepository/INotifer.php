<?php


namespace App\Repository\MediatorRepository;


interface INotifer
{
    public function notifier(string $methodNotifier,$param = null);
    public function thisNotifier();
}
