<?php
/******************************************
 *  Author : n1colius.lau@gmail.com
 *  Created On : Fri May 22 2020
 *  File : translation_helper.php
 *******************************************/
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if (!function_exists('getDirContents')) {
    function getDirContents($dir, &$results = array()){
        $files = scandir($dir);

        foreach($files as $key => $value){
            $path = realpath($dir.DIRECTORY_SEPARATOR.$value);
            if(!is_dir($path)) {
                $results[] = $path;
            } else if($value != "." && $value != "..") {
                getDirContents($path, $results);
                $results[] = $path;
            }
        }

        return $results;
    }
}

if (!function_exists('GetFileExt')) {
    function GetFileExt($filename) {
        $arrTemp = explode(".", $filename);
        $ext = strtolower(array_values(array_slice($arrTemp, -1))[0]);
        return $ext;
    }
}