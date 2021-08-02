<?php


namespace App\Exceptions;

interface IAppException
{
    public function setLog(string $messageLog,string $levelLog,string $channel = 'systemLog');
}
