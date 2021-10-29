<?php

require_once('BringApi.php');
require_once('GrocyApi.php');

$email = getenv('EMAIL');
$password = getenv('PASSWORD');

$bring = new BringApi("$email","$password",true);

$lists = json_decode($bring->loadLists());
echo "Below are your bring lists with their UUID: \n";
foreach ($lists->lists as $l){
    echo $l->name ." // UUID: ".$l->listUuid."\n";
}
