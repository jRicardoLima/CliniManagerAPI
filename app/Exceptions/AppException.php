<?php


namespace App\Exceptions;


 use Exception;
 use Illuminate\Support\Facades\Log;

 class AppException extends Exception implements IAppException
{
   protected  const ERROR = 'error';
   protected  const EMERGENCY = 'emergency';
   protected  const ALERT = 'alert';
   protected  const CRITICAL = 'critical';
   protected  const WARNING = 'warning';
   protected  const NOTICE = 'notice';
   protected  const INFO = 'info';

     public function __construct($message,$code = 0, Exception $previous = null)
     {
        parent::__construct($message,$code,$previous);
     }
     public function setLog(string $messageLog,string $levelLog, string $channel = 'systemLog')
     {
         switch ($levelLog){
            case 'error':
               Log::channel($channel)->error($messageLog);
            break;
            case 'emergency':
                Log::channel($channel)->emergency($messageLog);
            break;
            case 'alert':
                Log::channel($channel)->alert($messageLog);
            break;
            case 'critical':
                Log::channel($channel)->critical($messageLog);
            break;
            case 'warning':
                 Log::channel($channel)->warning($messageLog);
            break;
            case 'NOTICE':
                 Log::channel($channel)->notice($messageLog);
            break;
            case 'INFO':
                 Log::channel($channel)->info($messageLog);
            break;
            default:
                 Log::channel($channel)->info($messageLog);
         }

     }
 }
