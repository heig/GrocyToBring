<?php

require_once('BringApi.php');
require_once('GrocyApi.php');

$bringuuid = getenv('BRINGUUID');
$grocyURL = getenv('GROCYURL');
$grocyApiKey = getenv('GROCYAPIKEY');

$bring = new BringApi('',"$bringuuid",false);

//echo $bring->getItems();

$grocy = new GrocyApi($grocyURL, $grocyApiKey);
$missing_products = $grocy->getVolatileProducts('0')->missing_products;

foreach($missing_products as $p){
    if(empty($bring->saveItem($p->name, ''))){
        echo "Added $p->name to bring \n";
    }else{
        echo "Error adding $p->name to bring \n";
    };

}