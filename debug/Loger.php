<?php

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
    private static $inputStrategy;
    private static $outputStrategy;

    public static function setInputStrategy($strategyName){

        if(is_object(self::$inputStrategy)){//если уже есть стратегия, то удаляем объекты
            self::$inputStrategy->free(self::$containers);
        }

        $className=$strategyName."LogerStrategy";

        //Debug::dump($className);

        self::$inputStrategy=new $className();
    }

    public static function setOutputStrategy($strategyName){
        $className=$strategyName."LogerStrategy";
        self::$outputStrategy=new $className();
    }

    public static function addContainer($containerName){

        if(self::$containers[$containerName]==NULL){//если такого имени не существует
            self::$containers[$containerName]=self::$inputStrategy->addContainer($containerName);
        }

    }

    //public static function

    public static function write($container_name, $data){
        self::$inputStrategy->write(self::$containers, $container_name, $data);
    }

    public static function get($container_name){
        $dataLog=self::$inputStrategy->get(self::$containers, $container_name);
        return self::$outputStrategy->get($dataLog);

    }


}

class LogerMessage{
    private $time;
    private $message;
    private $level;

    public function __construct($time, $message, $level){
        $this->time=$time;
        $this->message=$message;
        $this->level=$level;
    }
}

abstract class InputLogerStrategy{
    abstract public function addContainer($containerName);
    abstract public function write(&$container, $container_name, $data);
    abstract public function get($container, $container_name);
    abstract public function free($container);
}


class ArrayLogerStrategy extends InputLogerStrategy{
    public function addContainer($containerName){
        return array();
    }
    public function write(&$container, $container_name, $data){

        $dt=new DateTime();

        $container[$container_name][udate('d.m.Y H:i:s.u')]=$data;
    }
    public function get($container, $container_name){
        return $container[$container_name];

    }

    public function free($container){

    }
}

class FileLogerStrategy extends InputLogerStrategy{
    public function addContainer($containerName){

    }
    public function write(&$container, $container_name, $data){

    }
    public function get($container, $container_name){

    }

    public function free($container){

    }
}



abstract class OutputLogerStrategy{
    abstract public function get($dataLog);
}

class HtmlLogerStrategy extends OutputLogerStrategy{
    public function get($dataLog){

        $colors=array(
            '#6487dc', '#4682B4'
        );



        $htmlString="<div style='color:#FFFFFF; width:80%; '>";

        $counter=0;
        foreach ($dataLog as $time=>$string) {

            $strNumber=($counter%2);

            $htmlString.="<div style='background: $colors[$strNumber]; min-height: 30px'>";

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