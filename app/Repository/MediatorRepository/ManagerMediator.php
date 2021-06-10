<?php


namespace App\Repository\MediatorRepository;


use Exception;

abstract class ManagerMediator
{
    private $repository;
    private $notifier;
   protected function __construct(INotified $repository,INotifer $notifer)
   {
       try {
           $this->repository = (new $repository)->notified();
           $this->notifier = (new $notifer)->thisNotifier();
       }catch(Exception $e){
           throw new Exception('Repository is null or not exists');
       }
   }

    protected function classNotified()
    {
        return $this->repository;
    }

    protected function classNotifier()
    {
        return $this->notifier;
    }
}
