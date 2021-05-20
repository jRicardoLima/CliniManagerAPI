<?php

function convertData($value,$format = 'd/m/Y'){
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
