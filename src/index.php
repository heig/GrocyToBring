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
        echo "Skipping Bring for $p->name because it's partly in stock \n";
    }elseif($grocySkipPartlyInStockCustom == 1 && $grocy->checkHideFromBring($p->id, getenv('HIDEPARTLYFROMBRING'))){
        echo "Skipping Bring for $p->name because it's partly in stock (custom setting) \n";
    }elseif($p->done == 1){
        echo "Skipping Bring for $p->name because it's marked as done \n";
    }else{
        $product_details = $grocy->getProductEntity($p->id);
		$product_store = $grocy->getProductStore($p->id);
        $bringlist = $grocy->getBringUUID($product_details->shopping_location_id, getenv('BRINGUUIDFIELD'), $bringuuid);
	    $purchase_quantity = (ceil($p->amount_missing / $product_store->qu_conversion_factor_purchase_to_stock));
		if ($purchase_quantity > 1){
			$purchase_unit_name = $grocy->quantities[$product_details->qu_id_purchase]["name_plural"];
		}else{
			$purchase_unit_name = $grocy->quantities[$product_details->qu_id_purchase]["name"];
		};
        if(empty($bring->saveItem($bringlist, clean($p->name), $purchase_quantity.' '.$purchase_unit_name))){
            echo "Added $purchase_quantity $purchase_unit_name ".clean($p->name)." to bring \n";
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
