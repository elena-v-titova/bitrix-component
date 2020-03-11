<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

use Bitrix\Highloadblock;

if (\Bitrix\Main\Loader::includeModule("iblock")) {
    $dbIBlock = CIBlock::GetList(
        array("sort" => "asc"),
        array("ACTIVE" => "Y")
    );
    while ($iblock = $dbIBlock->Fetch()) {
        $arIblock[$iblock["ID"]] = "[".$iblock["ID"]."] ".$iblock["NAME"];
    }
}

if (\Bitrix\Main\Loader::includeModule("highloadblock")) {
    $dbHLBlock = \Bitrix\Highloadblock\HighloadBlockTable::GetList();
    while ($hlblock = $dbHLBlock->Fetch()) {
        $arHLBlock[$hlblock["ID"]] = "[".$hlblock["ID"]."] ".$hlblock["NAME"];
    }
}

$arComponentParameters = array(
    "PARAMETERS" => array(
        "PRODUCTS_COUNT" => array(
            "PARENT" => "BASE",
            "NAME" => GetMessage("PRODUCTS_COUNT_NAME"),
            "TYPE" => "STRING",
            "DEFAULT" => "5"
        ),
        "IBLOCK_ID" => array(
            "PARENT" => "SETTINGS",
            "NAME" => GetMessage("IBLOCKID_NAME"),
            "TYPE" => "LIST",
            "VALUES" => $arIblock,
        ),
        "HLBLOCK_ID" => array(
            "PARENT" => "SETTINGS",
            "NAME" => GetMessage("HLBLOCKID_NAME"),
            "TYPE" => "LIST",
            "VALUES" => $arHLBlock,
        ),
    )
);

?>

