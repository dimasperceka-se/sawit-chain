<?php
/******************************************
 *  Author : n1colius.lau@gmail.com   
 *  Created On : Thu Oct 10 2019
 *  File : awscognito.php
 *******************************************/
defined('BASEPATH') OR exit('No direct script access allowed');

$config['awscog'] = [
    'credentials' => [
        'key' => 'AKIAXV2QEJE4IP55ZX3Z',
        'secret' => '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',
    ],
    'region' => 'ap-southeast-1',
    'version' => 'latest',

    // Silence the AWS SDK "PHP 7.4 deprecated" notice on local Herd (we run PHP 7.4).
    'suppress_php_deprecation_warning' => true,

    'app_client_id' => '1lc490sstumu3q8h14fpgvo1ot', //Applykan sesuai userpool yg mau dituju
    'app_client_secret' => 'ifnih61qrr2sbb58q2ou33qi9gl55n4stlpef8q5ko10kd2fgq2', //Applykan sesuai userpool yg mau dituju
    'user_pool_id' => 'ap-southeast-1_1D5YvMFMC' //Applykan sesuai userpool yg mau dituju

    // 'app_client_id' => '7dakqt32eb0d3e9j9kuo3gld2v', //Applykan sesuai userpool yg mau dituju
    // 'app_client_secret' => '1v00rmi2752nprifjjhqonutph6gs79n8em6d9docbpapthk8c1k', //Applykan sesuai userpool yg mau dituju
    // 'user_pool_id' => 'ap-southeast-1_5q9gitVml' //Applykan sesuai userpool yg mau dituju
];

