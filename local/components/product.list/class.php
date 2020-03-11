<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

use \Bitrix\Highloadblock\HighloadBlockTable as HLBT;

if (!\Bitrix\Main\Loader::includeModule('iblock')) {
    ShowError(Loc::getMessage('IBLOCK_MODULE_NOT_INSTALLED'));
    return;
}

if (!\Bitrix\Main\Loader::includeModule('highloadblock')) {
    ShowError(Loc::getMessage('IBLOCK_MODULE_NOT_INSTALLED'));
    return;
}

class ProductsComponent extends CBitrixComponent
{
    public function executeComponent()
    {
        $this->getProductList();
        $this->getProductReviews();
        $this->sortProductList();
        $this->paginateProductList();

        $this->includeComponentTemplate();
    }

    private function paginateProductList()
    {
        $countOnPage = $this->arParams["PRODUCTS_COUNT"];
        $page = empty($_GET['PAGEN_1']) ? 1 : intval($_GET['PAGEN_1']);
        $elements = $this->arResult["PRODUCT_LIST"];
        $elementsPage = array_slice($elements, ($page-1) * $countOnPage, $countOnPage);

        $navResult = new CDBResult();
        $navResult->NavPageCount = ceil(count($elements) / $countOnPage);
        $navResult->NavPageNomer = $page;
        $navResult->NavNum = 1;
        $navResult->NavPageSize = $countOnPage;
        $navResult->NavRecordCount = count($elements);

        $this->arResult["PRODUCT_LIST"] = $elementsPage;

        global $APPLICATION;
        $APPLICATION->IncludeComponent(
            'bitrix:system.pagenavigation',
            'modern',
            array(
                'NAV_RESULT' => $navResult,
        ));
    }

    private function getProductList()
    {
        $query = \Bitrix\Iblock\ElementTable::getList(array(
            'select' => array('ID', 'NAME', 'DETAIL_TEXT', 'TIMESTAMP_X'),
            'filter' => array('IBLOCK_ID' => $this->arParams["IBLOCK_ID"]),
            'order' => array('TIMESTAMP_X' => 'ASC'),
            'cache' => array(
                'ttl' => 600,
            )
        ));

        while ($product = $query->fetch()) {
            $this->arResult['PRODUCT_LIST'][$product['ID']]['product'] = $product;
        }
    }

    private function getProductReviews()
    {
        $hlblock = HLBT::getList(array(
            'filter' => array('ID' => $this->arParams["HLBLOCK_ID"]),
        ))->fetch();
        if ($hlblock) {
            $entity = HLBT::compileEntity($hlblock);
            $hlblockClass = $entity->getDataClass();
            $query = $hlblockClass::getList(array(
                'filter' => array(
                    'UF_ACTIVE' => 1,
                ),
                'select' => array("*"),
                'cache' => array(
                    'ttl' => 600,
                )
            ));

            while ($review = $query->fetch()) {
                $this->arResult['PRODUCT_LIST'][$review['UF_PRODUCT']]['reviews'][] = $review;
            }
        }
    }

    private function sortProductList()
    {
        uasort($this->arResult['PRODUCT_LIST'], function($a, $b) {
            $ca = count($a['reviews']);
            $cb = count($b['reviews']);
            if ($ca == $cb) {
                if ($a['product']['TIMESTAMP_X'] == $b['product']['TIMESTAMP_X']) {
                    return 0;
                }
                return $a['product']['TIMESTAMP_X'] < $b['product']['TIMESTAMP_X'] ? 1 : -1;
            }
            return ($ca < $cb) ? 1 : -1;
        });
    }
}

?>

