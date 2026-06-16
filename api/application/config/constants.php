<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| File and Directory Modes
|--------------------------------------------------------------------------
|
| These prefs are used when checking and setting modes when working
| with the file system.  The defaults are fine on servers with proper
| security, but you may wish (or even need) to change the values in
| certain environments (Apache running a separate process for each
| user, PHP under CGI with Apache suEXEC, etc.).  Octal values should
| always be used to set the mode correctly.
|
*/
define('FILE_READ_MODE', 0644);
define('FILE_WRITE_MODE', 0666);
define('DIR_READ_MODE', 0755);
define('DIR_WRITE_MODE', 0777);

/*
|--------------------------------------------------------------------------
| File Stream Modes
|--------------------------------------------------------------------------
|
| These modes are used when working with fopen()/popen()
|
*/

define('FOPEN_READ',							'rb');
define('FOPEN_READ_WRITE',						'r+b');
define('FOPEN_WRITE_CREATE_DESTRUCTIVE',		'wb'); // truncates existing file data, use with care
define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE',	'w+b'); // truncates existing file data, use with care
define('FOPEN_WRITE_CREATE',					'ab');
define('FOPEN_READ_WRITE_CREATE',				'a+b');
define('FOPEN_WRITE_CREATE_STRICT',				'xb');
define('FOPEN_READ_WRITE_CREATE_STRICT',		'x+b');

//Untuk Keperluan Aplikasi
define('REPLANTED_FINANCE_HA_FUNDING', 7500);


/*
|--------------------------------------------------------------------------
| Folder Untuk AWS S3
|--------------------------------------------------------------------------
|
| Berikut adalah constant untuk folder di AWS S3 untuk Palmoil Web, agar rapi dikumpulkan disini semua
|
*/

//Module Farmer
define('AWSS3_FARMER_PATH','members/farmer');
define('AWSS3_FARMER_SIGNATURE_PATH','members/farmer/signature');
define('AWSS3_FARMER_KTP_PATH','members/farmer/ktp');
define('AWSS3_FARMER_PLOT_PATH','plot/farmer');
define('AWSS3_FARMER_SURVEY_CERT_PATH','plot/cert');
define('AWSS3_FARMER_PLOT_DOC_OWNED_PATH','plot/farmer/owned_doc');
define('AWSS3_FARMER_PLOT_SOIL_EROTION_PATH','plot/farmer/soil_erotion');
define('AWSS3_FARMER_PLOT_SOIL_ACC_PATH','plot/farmer/soil_accumulation');
define('AWSS3_FARMER_PLOT_CONTRACT_PATH','plot/farmer/contract');

//Module Partner
define('AWSS3_LOGO_PARTNER','partner/logo');

//Module Mill
define('AWSS3_MILL_LOGO_PATH','mill/logo');
define('AWSS3_MILL_LOCATION_PATH','mill/location');
define('AWSS3_MILL_PLOT_PATH','plot/mill');
define('AWSS3_CMSDOC_PATH','cms/document/file'); //type: documents, fullpathDB: documents/cms/document/file/namafilenya
//Module Refinery
define('AWSS3_REFINERY_LOGO_PATH','refinery/logo');
define('AWSS3_REFINERY_LOCATION_PATH','refinery/location');

//Module SME
define('AWSS3_SME_PATH','members/sme');
define('AWSS3_SME_LOGO_PATH','members/sme/logo');
define('AWSS3_SME_PLOT_PATH','plot/sme');
define('AWSS3_WH_SME_PLOT_PATH','warehouse/sme');

//Module IMS
define('AWSS3_IMS_MASTER_FILES_PATH','ims/files');
define('AWSS3_CERT_PROG_PATH','certification/program');
define('AWSS3_CERT_BODY_PATH','certification/body');
define('AWSS3_CERT_CONTRACT_PATH','certification/contract');

define('AWSS3_APPLICANT_PHOTO_PATH','applicant/photo');
define('AWSS3_APPLICANT_CONTRACT_PATH','applicant/contract');
define('AWSS3_APPLICANT_SIGNATURE_PATH','applicant/signature');

//Coaching
define('AWSS3_COACHING_PHOTO_PATH','coaching/photo');
define('AWSS3_COACHING_SIGNATURE_PATH','coaching/signature');

//Training
define('AWSS3_UPLOAD_FARMER_TRAINING_PATH','training/farmer/upload_attandaance');
define('AWSS3_TRAINING_FARMER_PHOTO_PATH','training/farmer/photo');
define('AWSS3_TRAINING_FARMER_TTD_PATH','training/farmer/signature');
define('AWSS3_TRAINING_IMAGE_PATH','training/files');
define('AWSS3_TRAINING_FILE_PATH','training/docs');

//Module News
define('AWSS3_NEWS_IMAGE','cms/news/images');

//Module Staff
define('AWSS3_STAFF_PHOTO','staff/photo');

/* End of file constants.php */
/* Location: ./application/config/constants.php */