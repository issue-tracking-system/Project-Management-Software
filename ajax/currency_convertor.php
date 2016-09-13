<?php

function convertCurrency($amount, $from, $to) {
    $url = "https://www.google.com/finance/converter?a=$amount&from=$from&to=$to";
    $data = file_get_contents($url);
    preg_match("/<span class=bld>(.*)<\/span>/", $data, $converted);
    $converted = preg_replace("/[^0-9.]/", "", $converted[1]);
    return round($converted, 2);
}

$valutes = array();
$new_value = convertCurrency($_POST['sum'], $_POST['from'], $_POST['to']);

echo $new_value;
