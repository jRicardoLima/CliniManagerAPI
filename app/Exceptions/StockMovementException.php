<?php


namespace App\Exceptions\StockMovementExceptions;


use App\Exceptions\AppException;
use Exception;

class StockMovementException extends AppException
{
    public function __construct($message, $code = 0, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

}
