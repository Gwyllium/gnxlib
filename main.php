<?php

//функции для автоматической загрузки

spl_autoload_register(function ($class) {
    $filepath = rsearch(__DIR__, $class.".php");

    if($filepath){
        require_once($filepath);
    }


});




function rsearch($folder, $pattern) {
    $iti = new RecursiveDirectoryIterator($folder);
    foreach(new RecursiveIteratorIterator($iti) as $file){
        if(strpos($file , $pattern) !== false){
            return $file;
        }
    }
    return false;
}