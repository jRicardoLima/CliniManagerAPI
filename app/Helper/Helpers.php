<?php

use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

function convertData($value, $format = 'd/m/Y'){
    return \Carbon\Carbon::parse($value)->format($format);
}
function convertDataToSql($value){
   $date = explode('/',$value);

   return $date[2]."-".$date[1]."-".$date[0];
}

function formatMoneyToBr($value,$decimals = 2,$decimalSeparator = '.',$thousandsSeparator = ','){
    return number_format($value,$decimals,$decimalSeparator,$thousandsSeparator);
}
function formatMoneyToSql($value){
   $value = str_replace(',','',$value);
   return number_format($value,2,'.','');
}

function filterRequestAll($arrayData,bool $filter = true,?Closure $closure = null){
    $collection = collect($arrayData);

    if($filter){
       if ($closure != null){
           return $collection->filter($closure)->toArray();
       }
       return $collection->filter(fn($value,$key) =>  $value != null && $value != 'none')->toArray();

    }
    return $collection->toArray();
}

function RequestAllCustom($arrayData,Closure $closure,$method = 'map',bool $toArray = true){

    $collection = collect($arrayData);

    switch (strtolower($method)){

        case 'map':
            if($toArray){
                return $collection->map($closure)->toArray();
            }
            return $collection->map($closure);
        default:
            throw new Exception('method RequestAll not found');
    }
}

function clearValues($value,$target,$newValue){
    return str_replace($target,$newValue,$value);
}

function decodeBase64ToImage($file,$newFile){
    if(!is_null($file) && $file != ''){
        $data = explode(',',$file);
        $decode = base64_decode($data[1]);
        $size = getimagesizefromstring($decode);

        if($size == '' || strpos($size['mime'], 'image/') !== 0){
            throw new ValidationException(['Imagem não é valida']);
        }

        $ext = substr($size['mime'],6);
        if(!in_array($ext,['png','gif','jpeg'])){
            throw new ValidationException(['Imagem não é valida']);
        }

        $imgFile = $newFile.'.'.$ext;
        return ['image' => $imgFile,'raw' => $decode];
    } else {
        throw new ValidationException(['File is null']);
    }
}
