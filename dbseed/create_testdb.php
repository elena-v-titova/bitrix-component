<?php
require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_before.php');
require_once("DBSeed.php");

$dbseed = new DBSeed($DB, "products", "reviews");

$dbseed->createProductsAndReviews();

echo("Элементы инфоблока и highload-блока созданы");

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_after.php");

