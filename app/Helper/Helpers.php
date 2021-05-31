<?php

use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

function convertData($value, $format = 'd/m/Y'){
    return \Carbon\Carbon::parse($value)->format($format);
}

function formatMoneyToBr($value,$decimals = 2,$decimalSeparator = '.',$thousandsSeparator = ','){
    return number_format($value,$decimals,$decimalSeparator,$thousandsSeparator);
}

function filterRequestAll($arrayData){
    $collection = collect($arrayData);

    $filtered = $collection->filter(fn($value,$key) =>  $value != null);

    return $filtered->toArray();
}

function decodeBase64ToImage($file,$newFile){
    if(!is_null($file) && $file != ''){
        $data = explode(',',$file);
        $decode = base64_decode($data[1]);
        $size = getimagesizefromstring($decode);

        if(empty($size) || strpos($size['mime'], 'image/') !== 0){
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
