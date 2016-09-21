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
    private static $strategy;

    public static function setStrategy($strategyName){

        if(is_object(self::$strategy)){//если уже есть стратегия, то удаляем объекты
            self::$strategy->free(self::$containers);
        }

        $className=$strategyName."LogerStrategy";

        //Debug::dump($className);

        self::$strategy=new $className();



    }

    public static function addContainer($containerName){

        if(self::$containers[$containerName]==NULL){//если такого имени не существует
            self::$containers[$containerName]=self::$strategy->addContainer($containerName);
        }

    }

    public static function write($container_name, $data){
        self::$strategy->write(self::$containers, $container_name, $data);
    }

    public static function get($container_name){
        return self::$strategy->get(self::$containers, $container_name);

    }
}

abstract class LogerStrategy{
    abstract public function addContainer($containerName);
    abstract public function write(&$container, $container_name, $data);
    abstract public function get($container, $container_name);
    abstract public function free($container);
}


class ArrayLogerStrategy extends LogerStrategy{
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

class FileLogerStrategy extends LogerStrategy{
    public function addContainer($containerName){

    }
    public function write(&$container, $container_name, $data){

    }
    public function get($container, $container_name){

    }

    public function free($container){

    }
}