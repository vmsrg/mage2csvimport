<?php  
function clearPrice($price,$decimals=''){
    $price = str_replace('.', '', $price);
    $price = str_replace(',', '.', $price);
    $price = str_replace('&euro;.', '', $price);
    $price = str_replace('&#8364;', '', $price);
    $price = str_replace('€', '', $price);
    $price = str_replace(' ', '', $price);
    $price = str_replace(' ', '', $price);
    
    return floatval(trim($price));
}

function clearText($result){
    $result = html_entity_decode($result);
    $result = htmlspecialchars_decode($result);
    $result = str_replace('&amp;', '&', $result);
    $result = str_replace('&quot;', '"', $result);
    // $result = str_replace('&#039;', '\'', $result);
    $result = preg_replace_callback("/(&#[0-9]+;)/", function($m) { return mb_convert_encoding($m[1], "UTF-8", "HTML-ENTITIES"); }, $result); 

    $result = preg_replace('/\s+/', ' ', $result);

    return $result;
}

function br2nl($str) {
    $str = preg_replace("/(\r\n|\n|\r)/", "", $str);
    return  preg_replace('#<br\s*/?>#i', "\n", $str);
    /* return preg_replace("/<br\s*\/?>/i", "\r\n", $str);*/
}
?>