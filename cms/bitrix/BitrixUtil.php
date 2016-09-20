<?php
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
CModule::IncludeModule("iblock");



class BitrixUtil {

    public static function deleteAllSections(){//удаляет все разделы и соответственно элементы

        global $DB;

        $arFilter = array('DEPTH_LEVEL' => 1); // выберет потомков без учета активности
        $arSelect=array("ID");//для удаления достаточно ID
        $rsSect = CIBlockSection::GetList(array(),$arFilter, false, $arSelect);
        while ($arSect = $rsSect->GetNext()){
            //die('ok');
            $id=$arSect['ID'];

            $DB->StartTransaction();
            if(!CIBlockSection::Delete($id)){

                $DB->Rollback();
            }
            else{
                $DB->Commit();
            }

        }

    }

    public static function deleteAllElements(){//удаляет все элементы
        global $DB;

        $arSelect = Array("ID");
        $res = CIBlockElement::GetList(Array(), array(), false, Array(), $arSelect);



        while($ar_fields = $res->GetNext()){
            $id=$ar_fields['ID'];
            if(!CIBlockElement::Delete($id)){

                $DB->Rollback();
            }
            else{
                $DB->Commit();
            }

        }
    }

} 