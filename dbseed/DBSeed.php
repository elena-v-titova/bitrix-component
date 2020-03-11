<?php

use Bitrix\Main\Loader;
use Bitrix\Iblock\IblockTable;
use Bitrix\Highloadblock\HighloadBlockTable as HLBT;

class DBSeed
{
    private $db;
    private $iblockName;
    private $hlblockName;

    public function __construct($db, $ibName, $hlbName)
    {
        $this->db = $db;
        $this->iblockName = $ibName;
        $this->hlblockName = $hlbName;
    }

    public function createProductsAndReviews()
    {
        for ($i = 1; $i <= 20; $i++) {
            $product_id = $this->addIBlockElement("Товар$i", "Описание товара$i");

            $review_count = rand(0, 4);
            for ($j = 1; $j <= $review_count; $j++) {
                $this->addHBlockElement($product_id, "Отзыв$j о товаре$product_id");
            }
        }
    }

    public function addIBlockElement($name, $descr)
    {
        if (Loader::includeModule('iblock')) {
            $ibel = new CIBlockElement;

            $arFields = Array(
                "IBLOCK_ID"      => $this->getIBID(),
                "NAME"           => $name,
                "ACTIVE"         => "Y",
                "DETAIL_TEXT"    => $descr,
            );

            $product_id = $ibel->Add($arFields);
            if (!$product_id) {
                ShowError("Iblock not added");
                exit;
            }

            return $product_id;
        }
    }

    public function getIBID()
    {
        Loader::includeModule('iblock');
        $iblock = IblockTable::getList(array(
            'filter' => array('CODE' => $this->iblockName),
        ))->fetch();

        if (!$iblock) {
            ShowError("Iblock not found");
            exit;
        }

        return $iblock["ID"];
    }

    public function addHBlockElement($product_id, $review_text)
    {
        if (Loader::IncludeModule('highloadblock')) {
            $arFields = array(
                'UF_ACTIVE' => "Y",
                'UF_NAME' => "Имя$product_id",
                'UF_SURNAME' => "Фамилия$product_id",
                'UF_TEXT' => $review_text,
                'UF_PRODUCT' => $product_id
            );

            $hlblock = HLBT::getList(array(
                'filter' => array('TABLE_NAME' => $this->hlblockName),
            ))->fetch();
            $entity = HLBT::compileEntity($hlblock);
            $hlblockClass = $entity->getDataClass();
            $hlblockEl = $hlblockClass::add($arFields);
            if (!$hlblockEl->isSuccess()) {
                ShowError("HLBlock not added");
                exit;
            }
        }
    }

}

?>

