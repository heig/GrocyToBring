<?php

// Version: 1.4

require_once('BringApi.php');
require_once('GrocyApi.php');

$bringuuid = getenv('BRINGUUID');
$grocyURL = getenv('GROCYURL');
$grocyApiKey = getenv('GROCYAPIKEY');
$source = getenv('SOURCE');

$grocySkipPartlyInStock = getenv('GROCYSKIPPARTLYINSTOCK');
$grocySkipPartlyInStockCustom = getenv('GROCYSKIPPARTLYINSTOCKCUSTOM');


$bring = new BringApi('',"$bringuuid",false);

//echo $bring->getItems();


$grocy = new GrocyApi($grocyURL, $grocyApiKey);

if($source == "shoppinglist"){
    $missing_products = $grocy->getShoppingListItmes();

}else{
    $missing_products = $grocy->getVolatileProducts('0')->missing_products;

}


foreach($missing_products as $p){
    if($grocy->checkHideFromBring($p->id, getenv('HIDEFROMBRING'))){
        echo "Skipping Bring for $p->name because it's hidden from Bring \n";
    }elseif($grocySkipPartlyInStock == 1 && $p->is_partly_in_stock == 1){
        echo "Skipping Bring for $p->name because is partly in stock \n";
    }elseif($grocySkipPartlyInStockCustom == 1 && $grocy->checkHideFromBring($p->id, getenv('HIDEPARTLYFROMBRING'))){
        echo "Skipping Bring for $p->name because is partly in stock (custom setting) \n";
    }else{
        if(empty($bring->saveItem(clean($p->name), $p->amount_missing.' '.getenv('UNIT')))){
            echo "Added ".clean($p->name)." to bring \n";
        }else{
            echo "Error adding $p->name to bring \n";
        };
    }

}

function clean($string) {
    $percentReplace = getenv('PERCENTREPLACE');

   if(preg_match('/%/', $string)){
    return str_replace( array("%"), " $percentReplace", $string);
   }else{
    return str_replace( array("#", "'", ";"), '', $string);
   }
}
