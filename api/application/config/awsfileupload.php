<?php
/******************************************
 *  Author : fashahd@gmail.com  
 *  Created On : Tue March 03 2020
 *  File : awsfileupload.php
 *******************************************/
defined('BASEPATH') OR exit('No direct script access allowed');

$config['s3']['version'] = 'latest';
$config['s3']['region'] = 'ap-southeast-1';
$config['s3']['suppress_php_deprecation_warning'] = true;

$config['s3']['credentials']['key'] = 'AKIAXV2QEJE4PXNCQMWK';
$config['s3']['credentials']['secret'] = '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';
$config['s3']['suppress_php_deprecation_warning'] = true;

$config['s3Bucket'] = 'palmoiltrace';