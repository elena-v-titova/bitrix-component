# Компонент Bitrix "Список товаров"

*local/* - компонент Bitrix
*dbseed/* - наполнение БД тестовым набором данных

## Установка компонента

Скопировать каталог *local/* в проект Bitrix. Компонент можно подключить:

    <?$APPLICATION->IncludeComponent(
       "product.list",
       "",
       array(
          "PRODUCTS_COUNT" => "6",
          "IBLOCK_ID" => "6",
          "HLBLOCK_ID" => "2"
       ),
       false
    );

## Генерация тестового набора БД

Скопировать каталог *dbseed/* в проект Bitrix.

Предполагается, что в БД уже есть сущности *Товар* (IBLOCK=products) и *Отзыв* (HLBLOCK=reviews).

Для гененрации тестового набора данных выполнить http://*site*/dbseed/create_testdb.php.


