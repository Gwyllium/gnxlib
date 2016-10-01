<?php

/*уровни логирования
DEBUG - отладочные сообщения наиболее подробные
INFO - менее подробные отладочные сообщения - например вызов функции
WARNING - некритичные ошибки, часть из которых можно исправить
ERROR - ошибка, после которой система может продолжить работу.
FATAL - значительная ошибка, которая препятствует нормальному выполнению. Например, инициализация важной библиотеки
*/

define('GLOGER_NONE', 0);
define('GLOGER_DEBUG', 1);
define('GLOGER_INFO', 2);
define('GLOGER_WARNING',3 );
define('GLOGER_ERROR',4 );
define('GLOGER_FATAL',5 );

//загнать эту функцию в отдельный класс
function udate($format = 'u', $utimestamp = null) {
    if (is_null($utimestamp))
        $utimestamp = microtime(true);

    $timestamp = floor($utimestamp);
    $milliseconds = round(($utimestamp - $timestamp) * 1000000);

    return date(preg_replace('`(?<!\\\\)u`', $milliseconds, $format), $timestamp);
}


class Loger{
    private static $containers;//список контейнеров для сообщений. это могут быть файлы, массивы и т.п.

    private static $outputStrategy;
    private static $logLevel;//по этому уровню определяем что логировать
    private static $currentContainerName;
    private static $prevContainerName;



    public static function setOutputStrategy($strategyName){
        $className=$strategyName."LogerStrategy";
        self::$outputStrategy=new $className();
    }

    public static function addContainer($containerName){
        if(self::$containers[$containerName]==NULL){//если такого имени не существует
            self::$containers[$containerName]=array();
        }
    }

    public static function getCurrent(){
        return self::$currentContainerName;
    }

    public static function setCurrent($name){
        if(self::$containers[$name]==NULL){
            self::$containers[$name]=array();
        }
        self::$currentContainerName=$name;
    }

    public static function setCurrentSafe($name){//выбирает текущий контейнер, но сохраняет предыдущий
        if(empty(self::$currentContainerName)){
            self::$currentContainerName=$name;
            self::$prevContainerName=$name;
        }
        else{
            self::$prevContainerName=self::$currentContainerName;
            self::$currentContainerName=$name;
        }



    }

    public static function setPrevious(){
        self::$currentContainerName=self::$prevContainerName;
        self::$prevContainerName='';
    }



    public static function setLevel($level){
        self::$logLevel=$level;
    }

    public static function write($container_name, $data, $level=GLOGER_DEBUG){

        if(self::$logLevel<=$level){
            self::$containers[$container_name][udate('d.m.Y H:i:s.u')]=new LogerMessage($data, $level);
        }
    }



    public static function get($container_name){
        $dataLog=self::$containers[$container_name];
        return self::$outputStrategy->get($dataLog);
    }

    public static function getAll(){
        return self::$containers;
    }

    public static function debug($data, $container_name=""){
        if(empty($container_name)){
            $container_name=self::$currentContainerName;
        }
        self::write($container_name, $data, GLOGER_DEBUG);
    }

    public static function info($data, $container_name=""){
        if(empty($container_name)){
            $container_name=self::$currentContainerName;
        }
        self::write($container_name, $data, GLOGER_INFO);
    }



    public static function warning($data, $container_name=""){
        if(empty($container_name)){
            $container_name=self::$currentContainerName;

        }
        self::write($container_name, $data, GLOGER_WARNING);
    }

    public static function error($data, $container_name=""){
        if(empty($container_name)){
            $container_name=self::$currentContainerName;
        }
        self::write($container_name, $data, GLOGER_ERROR);
    }

    public static function fatal($data, $container_name=""){
        if(empty($container_name)){
            $container_name=self::$currentContainerName;
        }
        self::write($container_name, $data, GLOGER_FATAL);
    }




}

class LogerMessage{

    private $message;
    private $level;

    public function __construct($message, $level){
        $this->message=$message;
        $this->level=$level;
    }

    public function  getMessage(){
        return $this->message;
    }

    public function  getLevel(){
        return $this->level;
    }



}




abstract class OutputLogerStrategy{
    abstract public function get($dataLog);
}

class HtmlLogerStrategy extends OutputLogerStrategy{
    public function get($dataLog){

        $colors=array(
            '#483D8B','#2E8B57', '#DAA520', '#FF7F50', '#FF0000'
        );



        $htmlString="<div style='color:#FFFFFF; width:80%; '>";

        $counter=0;
        foreach ($dataLog as $time=>$message) {

            $strNumber=($counter%2);

            $level=$message->getLevel();
            $string=$message->getMessage();

            $color_number=$level-1;

            $htmlString.="<div style='background: $colors[$color_number]; min-height: 30px'>";

            $htmlString.="<div style='width: 20%; padding: 5px; display: inline-block'>$time</div>";
            $htmlString.="<div style='display: inline-block; padding: 5px;'>$string</div>";

            $htmlString.="</div>";

            $counter++;
            //end foreach $dataLog
        }

        $htmlString.="</div>";

        return $htmlString;
    }
}