<?php
function generateOneLetter($length = 1) {
    $characters = 'BEGJKPSTUY';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

function debugPrintArr($arr){
    echo "<pre>";
    print_r($arr);
    echo "</pre>";
}
?>