<?php
/******************************************
 *  Author : n1colius.lau@gmail.com   
 *  Created On : Fri Nov 15 2019
 *  File : cdn.php
 *******************************************/
//Get BaseURL
$config['http'] = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == "on") ? "https" : "http");
$root = $config['http']."://".$_SERVER['HTTP_HOST'];
$root .= str_replace(basename($_SERVER['SCRIPT_NAME']),"",$_SERVER['SCRIPT_NAME']);
$root = rtrim($root,'/');

// $config['SpcCDN'] = 'https://d1crcv4bd5roru.cloudfront.net/cdn';

//untuk fallback, tidak pakai aws s3
$config['SpcCDN'] = $root;