<?php


namespace App\Repository\MediatorRepository;


use Exception;

final class DispatchNotifier extends ManagerMediator
{
    private $class = null;
    private $notifier = null;

    public function __construct(INotified $repository,INotifer $notifier)
    {
        try {
            parent::__construct($repository,$notifier);
            $this->class = $this->classNotified();
            $this->notifier = $this->classNotifier();
        }catch (Exception $e){
            throw new Exception('Repository is null or not exits');
        }
    }

    public function dispatchSaveAddress(object $obj)
    {
        return $this->class->save($obj,true);
    }
}
