<?php


namespace App\Exceptions\ExceptionsFactory;


use App\Exceptions\AppException;
use Exception;

class FactoryException extends AppException
{
    public function __construct($message, $code = 0, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
