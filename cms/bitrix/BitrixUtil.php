<?php
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
CModule::IncludeModule("iblock");



class BitrixUtil {

    public static function deleteAllSections(){//удаляет все разделы и соответственно элементы
        Loger::setCurrentSafe('gnx');
        Loger::info('Удаляем все разделы');
        $arFilter = array('DEPTH_LEVEL' => 1);
        self::deleteSections($arFilter);

        Loger::setPrevious();
    }

    public static function deleteSectionsByIblock($iblock_id){
        Loger::setCurrentSafe('gnx');
        Loger::info('Удаляем все разделы');

        $arFilter = array('DEPTH_LEVEL' => 1, 'IBLOCK_ID'=>$iblock_id);
        self::deleteSections($arFilter);

        Loger::setPrevious();
    }

    public static function deleteAllElements(){//удаляет все элементы
        Loger::setCurrentSafe('gnx');
        Loger::info('Удаляем все элементы');

        $arFilter=array();
        self::deleteElements($arFilter);

        Loger::setPrevious();
    }

    public static function deleteElementsByIblock($iblock_id){
        Loger::setCurrentSafe('gnx');
        Loger::info('Удаляем все элементы инфоблока с id='.$iblock_id);

        $arFilter = array('IBLOCK_ID' => $iblock_id);
        self::deleteElements($arFilter);

        Loger::setPrevious();
    }


    //private
    private static function deleteElements($arFilter){
        global $DB;

        Loger::setCurrentSafe('gnx');
        Loger::info('Начинаем удаление инфоблоков');

        $arSelect = Array("ID");


        Loger::debug('Получаем все элементы, которые следует удалить');
        $res = CIBlockElement::GetList(Array(), $arFilter, false, Array(), $arSelect);
        $count=$res->SelectedRowsCount();
        Loger::info("Пытаемся удалить $count элементов");

        while($ar_fields = $res->GetNext()){
            $id=$ar_fields['ID'];
            Loger::debug('Пытаемся удалить элемент с id='.$id);

            if(!CIBlockElement::Delete($id)){
                Loger::error('Ошибка удаления элемента');
                $DB->Rollback();
            }
            else{
                $DB->Commit();
                Loger::debug('Элемент успешно удален');
            }
        }

        Loger::setPrevious();
    }

    private static function deleteSections($arFilter){//функция для удаления разделов. ее вызывают все публичные
        global $DB;

        Loger::setCurrentSafe('gnx');
        Loger::info('Начинаем удаление разделов');


        $arSelect=array('ID', 'NAME');//для удаления достаточно ID
        $rsSect = CIBlockSection::GetList(array(),$arFilter, false, $arSelect);
        while ($arSect = $rsSect->GetNext()){
            $id=$arSect['ID'];
            $name=$arSect['NAME'];
            Loger::debug("Пытаемся удалить раздел $name ($id) ");

            $DB->StartTransaction();
            if(!CIBlockSection::Delete($id)){
                Loger::error('Ошибка удаления раздела');
                $DB->Rollback();
            }
            else{
                $DB->Commit();
                Loger::debug('Раздел $name успешно удален');
            }

        }

        Loger::setPrevious();

    }

    public static function ldPath($path=''){//local default path
        return '/local/templates/.default'.$path;
    }

    public static function bdPath($path=''){//bitrix default path
        return '/bitrix/templates/.default'.$path='';
    }

    public static function tPath($path=''){//current template path
        return SITE_TEMPLATE_PATH.$path='';
    }

    function getPageLevel($page=''){//глубина страницы в url
        global $APPLICATION;
        if(!$page){
            $page=$APPLICATION->GetCurPage();
        }

        return count(explode('/', $page))-1;
    }





} 