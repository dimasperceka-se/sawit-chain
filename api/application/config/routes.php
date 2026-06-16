<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	http://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There area two reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router what URI segments to use if those provided
| in the URL cannot be matched to a valid route.
|
*/

$route['default_controller'] = "welcome";
$route['404_override'] = '';

/**
 * Route untuk mobile monitoring
 */
$route['authentication/login']                  = 'mobile_auth/login';
$route['authentication/logout']                 = 'mobile_auth/logout';

//activity location
$route['location/get-by-coordinate']            = 'mobile_monitoring/get_by_coordinate';
$route['location/get-by-district']              = 'mobile_monitoring/get_by_district';
$route['monitoring/get-data']                   = 'mobile_monitoring/fetch_monitoring';
$route['monitoring/sync-data']                  = 'mobile_monitoring/sync_monitoring';
$route['monitoring/upload-data']                = 'mobile_monitoring/sync_upload';

/**
 * Route untuk mobile traceability
 */
$route['auth-traceability/login']               = 'mobile_auth/login';
$route['auth-traceability/check-token']         = 'mobile_auth/checktoken';
$route['auth-traceability/logout']              = 'mobile_auth/logout';

$route['traceability/get-farmer']               = 'mobile_traceability/get_farmer';
$route['traceability/get-price']                = 'mobile_traceability/get_price';
$route['traceability/get-quota']                = 'mobile_traceability/check_quota';
$route['traceability/submit-batch']             = 'mobile_traceability/submit_batch';
$route['traceability/submit-transaction']       = 'mobile_traceability/submit_transaction';
$route['traceability/add-garden/(:any)']        = 'mobile_traceability/add_garden/$1';


$route['traceability/download-batch/(:num)']    = 'sync/getbatch/$1';
$route['traceability/get-destination/(:num)']   = 'mobile_traceability/get_destination/$1';

//unit testing
$route['ciunit']                                = "ciunit_controller/index";
$route['ciunit/(:any)']                         = "ciunit_controller/index/$1";

/* Traceability */
/* Mobile */
//Login
$route['auth-traceabilityx/login'] = 'traceability_api/mobile_auth/login';

//Login cognito
$route['auth-traceabilityx/v2/login'] = 'traceability_api/mobile_auth/v2';
$route['auth-traceabilityx/v2/login-app'] = 'traceability_api/mobile_auth/login_app';

//Login new version farmgate
$route['auth-traceabilityx/new/smelogin'] = 'traceability_api/mobile_auth/new';

//reference sme after login
$route['reference-fa-checkin/v2/sme'] = 'traceability_api/mobile_auth/sme';

//get delivery
$route['traceabilityx/get-delivery'] = 'traceability_api/delivery/fetch_api';

//post delivery
$route['traceabilityx/submit-delivery'] = 'traceability_api/delivery/submit_api';

//delete delivery
$route['traceabilityx/delete-delivery-pick'] = 'traceability_api/delivery/delete_api';

//delete batch
$route['traceabilityx/delete-batch'] = 'traceability_api/batching/delete_supplychain_batch_api';

//get reception
$route['traceabilityx/get-reception'] = 'traceability_api/reception/fetch_api';

//post recetption
$route['traceabilityx/submit-reception'] = 'traceability_api/reception/submit_api';

//post update reception
$route['traceabilityx/accept-delivery'] = 'traceability_api/reception/submit_accept_delivery';

//post sme
$route['traceabilityx/submit-sme'] = 'traceability_api/sme/add_sme';

//get sme
$route['traceabilityx/get-sme'] = 'traceability_api/sme/fetch_api';

//Transaction
$route['traceabilityx/farmer'] = 'traceability_api/farmer/fetch'; //Params -> PartnerID/SupplychainID
$route['traceabilityx/get-transaction'] = 'traceability_api/transaction/fetch';
$route['traceabilityx/submit-transaction'] = 'traceability_api/supplychain/submit_transaction';
$route['traceabilityx/submit-farmer'] = 'traceability_api/farmer/submit';
$route['traceabilityx/submit-plantation'] = 'traceability_api/farmer/submit_plot';

$route['traceabilityx/get-plantation'] = 'traceability_api/mobile_auth/plantation';

//Certification
$route['traceabilityx/get-certification'] = 'traceability_api/farmer/fetch_cert'; //Params -> PartnerID/SupplychainID

//TPH
$route['traceabilityx/get-collecting'] = 'traceability_api/collecting/fetch';
$route['traceabilityx/submit-collecting'] = 'traceability_api/collecting/submit';

//Pengiriman
$route['traceabilityx/get-batch'] = 'traceability_api/trans_batch/fetch';
$route['traceabilityx/get-spcode'] = 'traceability_api/supplychain/list_spb';
$route['traceabilityx/submit-batch'] = 'traceability_api/supplychain/submit_batch';

//Penerimaan
$route['traceabilityx/get-batch-penerimaan'] = 'traceability_api/trans_batch_penerimaan/fetch';
$route['traceabilityx/submit-batch-penerimaan'] = 'traceability_api/trans_batch_penerimaan/submit';

//Inbox
$route['traceabilityx/get-notification'] = 'traceability_api/notification/fetch';

$route['traceabilityx/get-relation'] = 'traceability_api/mobile_auth/relation';

//Referensi
$route['reference/farming-type'] = 'traceability_api/farming_type/fetch';
$route['reference/transport'] = 'traceability_api/transport/fetch';
$route['reference/transport-save'] = 'traceability_api/transport/submit';
$route['reference/transport-del/(:num)'] = 'traceability_api/transport/del/$1';

$route['reference/batch-status'] = 'traceability_api/batch_status/fetch';
$route['reference/batch-status-save'] = 'traceability_api/batch_status/submit';
$route['reference/batch-status-del/(:num)'] = 'traceability_api/batch_status/del/$1';

$route['reference/batch-type'] = 'traceability_api/batch_type/fetch';
$route['reference/batch-type-save'] = 'traceability_api/batch_type/submit';
$route['reference/batch-type-del/(:num)'] = 'traceability_api/batch_type/del/$1';

$route['reference/supplychain-org'] = 'traceability_api/Supplychain_org/fetch';
$route['reference/supplychain-org-save'] = 'traceability_api/Supplychain_org/submit';
$route['reference/supplychain-org-del/(:num)'] = 'traceability_api/Supplychain_org/del/$1';
$route['reference/supplychain-org-obj/(:num)'] = 'traceability_api/Supplychain_org/obj/$1';
$route['reference/supplychain'] = 'traceability_api/Supplychain_org/sid';
$route['reference/supplychain-org-partner'] = 'traceability_api/Supplychain_org/partner';

$route['reference/supplychain-org-rel'] = 'traceability_api/Supplychain_org_rel/fetch';
$route['reference/supplychain-org-rel-save'] = 'traceability_api/Supplychain_org_rel/submit';
$route['reference/supplychain-org-rel-del/(:num)'] = 'traceability_api/Supplychain_org_rel/del/$1';

$route['reference/supplychain-package'] = 'traceability_api/Supplychain_package/fetch';
$route['reference/supplychain-package-save'] = 'traceability_api/Supplychain_package/submit';
$route['reference/supplychain-package-del/(:num)'] = 'traceability_api/Supplychain_package/del/$1';

$route['reference/supplychain-quality'] = 'traceability_api/Supplychain_quality/fetch';
$route['reference/supplychain-quality-save'] = 'traceability_api/Supplychain_quality/submit';
$route['reference/supplychain-quality-del/(:num)'] = 'traceability_api/Supplychain_quality/del/$1';

$route['reference/supplychain-quality-value'] = 'traceability_api/Supplychain_quality_value/fetch';
$route['reference/supplychain-quality-value-quality'] = 'traceability_api/Supplychain_quality_value/quality';
$route['reference/supplychain-quality-value-save'] = 'traceability_api/Supplychain_quality_value/submit';
$route['reference/supplychain-quality-value-del/(:num)'] = 'traceability_api/Supplychain_quality_value/del/$1';

$route['reference/supplychain-price'] = 'traceability_api/Supplychain_price/fetch';
$route['reference/supplychain-price-save'] = 'traceability_api/Supplychain_price/submit';
$route['reference/supplychain-price-del/(:num)'] = 'traceability_api/Supplychain_price/del/$1';

$route['traceabilityx/get-report-purchase-transaction'] = 'traceability_api/web_transaction/fetch_api_purchase_report';

$route['traceabilityx/get-report-sales-transaction'] = 'traceability_api/web_transaction/fetch_api_sales_report';


/* Web */
$route['web-traceability/check-role-transaction'] = 'traceability_api/web_transaction/check_role_transaction';
//Transaction
$route['web-traceability/main-grid'] = 'traceability_api/web_transaction/fetch';
$route['web-traceability/main-submit'] = 'traceability_api/web_transaction/submit';

//sms 
$route['web-traceability/main-grid-sms'] = 'traceability_api/web_transaction/fetch_sms';

$route['web-traceability/main-grid-report-pembelian'] = 'traceability_api/web_transaction/fetch_purchases_report';

$route['web-traceability/main-grid-report-penjualan'] = 'traceability_api/web_transaction/fetch_sales_report';

$route['web-traceability/farmer'] = 'traceability_api/web_transaction/farmer';

$route['web-traceability/new-farmer'] = 'traceability_api/web_transaction/farmer_combo';

$route['web-traceability/plantation'] = 'traceability_api/web_transaction/plantation';

$route['web-traceability/plantationnew'] = 'traceability_api/web_transaction/plantation_new';

$route['web-traceability/plantationtc'] = 'traceability_api/web_transaction/plantation_tc';

$route['web-traceability/plantationtcnew'] = 'traceability_api/web_transaction/plantation_tc_new';
$route['web-traceability/sellermill'] = 'traceability_api/web_transaction/mill_seller';
$route['web-traceability/sellerdo'] = 'traceability_api/web_transaction/do_seller';
$route['web-traceability/selleragent'] = 'traceability_api/web_transaction/agent_seller';
$route['web-traceability/package-type'] = 'traceability_api/web_transaction/package_type';

$route['web-traceability/quality-grid'] = 'traceability_api/web_transaction/qualityGrid';
$route['web-traceability/quality'] = 'traceability_api/web_transaction/quality';
$route['web-traceability/quality-value'] = 'traceability_api/web_transaction/quality_value';
$route['web-traceability/cetak-kuitansi/(:num)/(:num)'] = 'traceability_api/web_transaction/report_cetak_kuitansi/$1/$2';

//Pengiriman
$route['web-traceability/pengiriman-main-grid'] = 'traceability_api/web_pengiriman/fetch';
$route['web-traceability/pengiriman-transaction-grid'] = 'traceability_api/web_pengiriman/transaction';

$route['web-traceability/pengiriman-transaction-grid-detail'] = 'traceability_api/delivery/grid_transaction_detail';

$route['web-traceability/pengiriman-submit'] = 'traceability_api/web_pengiriman/submit';
$route['web-traceability/get-transaction-window'] = 'traceability_api/web_pengiriman/transaction_window';
$route['web-traceability/destination'] = 'traceability_api/web_pengiriman/destination';
$route['web-traceability/spbcode'] = 'traceability_api/web_pengiriman/spcode';
$route['web-traceability/submit-transaction'] = 'traceability_api/web_pengiriman/saveTransaksi';
$route['web-traceability/delete-transaction'] = 'traceability_api/web_pengiriman/delete_transaction';
$route['web-traceability/close-pengiriman'] = 'traceability_api/web_pengiriman/close_batch';
$route['web-traceability/sent-pengiriman'] = 'traceability_api/web_pengiriman/sent_batch';
$route['web-traceability/get-do'] = 'traceability_api/web_pengiriman/get_do';
$route['web-traceability/cetak-suratjalan/(:num)/(:num)'] = 'traceability_api/web_pengiriman/cetak_suratjalan/$1/$2';

//Penerimaan
$route['web-traceability/penerimaan-main-grid'] = 'traceability_api/web_penerimaan/fetch';
$route['web-traceability/penerimaan-submit'] = 'traceability_api/web_penerimaan/submit';
$route['web-traceability/data-edit-penerimaan'] = 'traceability_api/web_penerimaan/data_edit';
$route['web-traceability/cetak-penerimaankuitansi/(:num)'] = 'traceability_api/web_penerimaan/report_cetak_kuitansi/$1';

//*/
/* End of file routes.php */
/* Location: ./application/config/routes.php */

$route['new-auth-traceability/login'] = 'traceability_api/mobile_auth/login';
$route['new-traceability/farmer'] = 'traceability_api/farmer/fetch';


/**
 * Route untuk Farmer Apps Mobile
 */

// registrasi dari incognito
$route['farmer-apps/cek-farmer'] = 'mobile_farmer_apps/cekFarmer'; //lamda use basic auth
$route['farmer-apps/reg-cognito'] = 'mobile_farmer_apps/regUserCognito'; //lamda use basic auth
$route['farmer-apps/farmer_check_by_phone'] = 'mobile_farmer_apps/farmer_check_by_phone'; //verification

$route['farmer-apps/update-fcm'] = 'mobile_farmer_apps/updateFCM';
$route['cognito/login'] = 'mobile_farmer_apps/login_aws';

$route['farmer-apps/profile'] = 'mobile_farmer_apps/farmer_profile';
$route['farmer-apps/login'] = 'mobile_farmer_apps/login';
$route['farmer-apps/garden'] = 'mobile_farmer_apps/farmer_garden';
$route['farmer-apps/transaction-detail'] = 'mobile_farmer_apps/farmer_transaction';
$route['farmer-apps/transaction'] = 'mobile_farmer_apps/farmer_transaction';
$route['farmer-apps/transaction-detail-summary'] = 'mobile_farmer_apps/farmer_transaction_detail_summary';
$route['farmer-apps/trader'] = 'mobile_farmer_apps/trader';
$route['farmer-apps/trainings'] = 'mobile_farmer_apps/training';
$route['farmer-apps/certification'] = 'mobile_farmer_apps/certification';
$route['farmer-apps/certification-ics-history'] = 'mobile_farmer_apps/certification_ics_history';
$route['farmer-apps/notification'] = 'mobile_farmer_apps/farmer_notification';
$route['farmer-apps/agent'] = 'mobile_farmer_apps/farmer_agent';
$route['farmer-apps/field-agent'] = 'mobile_farmer_apps/field_agent';
$route['farmer-apps/news'] = 'mobile_farmer_apps/news';
$route['farmer-apps/news-detail'] = 'mobile_farmer_apps/news_detail';
$route['farmer-apps/video'] = 'mobile_farmer_apps/video';
$route['farmer-apps/training'] = 'mobile_farmer_apps/farmer_training';
$route['farmer-apps/partner'] = 'mobile_farmer_apps/farmer_trader';
$route['farmer-apps/manual'] = 'mobile_farmer_apps/farmer_manual';
$route['farmer-apps/seedlings'] = 'mobile_farmer_apps/seedlings';
$route['farmer-apps/register'] = 'mobile_farmer_apps/registration';
$route['farmer-apps/confirm-transaction'] = 'mobile_farmer_apps/confirm_transaction';
$route['farmer-apps/collector-quota'] = 'mobile_farmer_apps/collector_quota';
$route['farmer-apps/farmer-incentive'] = 'mobile_farmer_apps/FarmerIncentive';
$route['farmer-apps/daily-price'] = 'mobile_farmer_apps/DailyPrice';
$route['farmer-apps/farmer-premium'] = 'mobile_farmer_apps/farmer_premium';
$route['farmer-apps/Kiosk'] = 'mobile_farmer_apps/Kiosk';
$route['farmer-apps/assign-dealer'] = 'grower/combo_dealer_Assign_mobile';
$route['farmer-apps/check_registered_phone'] = 'mobile_farmer_apps/check_registered_phone';
$route['farmer-apps/check-phone-num-registration'] = 'mobile_farmer_apps/check_phone_num_registration';
$route['farmer-apps/check-staffid'] = 'mobile_farmer_apps/check_staffid';

$route['farmer-apps/send-otp'] = 'mobile_farmer_apps/send_otp';
$route['farmer-apps/validation-otp'] = 'mobile_farmer_apps/validation_otp';
$route['farmer-apps/reset-password'] = 'mobile_farmer_apps/send_reset_password';
$route['farmer-apps/confirm-reset-password'] = 'mobile_farmer_apps/confirm_reset_password';
$route['farmer-apps/change-phone-cognito'] = 'mobile_farmer_apps/change_phone_cognito';

$route['farmer-apps/analytics'] = 'mobile_farmer_apps/analytics';

$route['cms/fetch-video/list'] = 'video/list';
$route['farmer-apps/farmer_check_by_farmer_id'] = 'mobile_farmer_apps/farmer_check_by_farmer_id';
$route['farmer-apps/read-notification'] = 'mobile_farmer_apps/read_notification';

$route['farmer-apps/change-phone-fromcentral'] = 'mobile_farmer_apps/change_phone_fromcentral';

/* route new from cms */

$route['farmer-apps-new/news']   = 'mobile_farmer_apps_new/news_new';
$route['farmer-apps-new/video']  = 'mobile_farmer_apps_new/video_new';
$route['farmer-apps-new/manual'] = 'mobile_farmer_apps_new/farmer_manual_new';