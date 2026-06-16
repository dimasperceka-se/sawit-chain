<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 *
 * @param type $type
 * @return string {COUNTRYCODE}-{DISTRICTCODE}-{NUMBER}
 */
if ( ! function_exists('convert_language'))
{
    function convert_language($data, $language) {
        if($data){
            foreach(@$data as $key => $value){
                if(is_object($value)){
                    foreach($value as $k => $v){
                        /*
                        if(is_numeric($v)){
                            if($k != 'latitude' && $k != 'longitude')
                            $v = number_format($v, 0);
                        }
                        */
                        // if(!$v){
                        //     $v = "";
                        // }
                        if($v == "0" OR $v == ""){
                            if(is_numeric($v)){
                                if($k != 'latitude' && $k != 'longitude')
                                $v = number_format($v, 0);
                            }else{
                                $v = "";
                            }
                        }
                        $value->$k = array('label' => $k, 'value' => $v);
                    }
                }else{
                    if(!$value){
                            $value = "";
                    }
                    $data->$key = array('label' => $key, 'value' => $value);
                }
            }
            return $data;
        }
        return array();
    }
    //digunakan untuk transalasi di response transacti di FC
    function convert_languagetransaction($data, $language) {
        foreach(@$data as $key => $value){
            if(is_object($value)){
                foreach($value as $k => $v){
                    /*
                    if(is_numeric($v)){
                        if($k != 'latitude' && $k != 'longitude')
                        $v = number_format($v, 0);
                    }
                    */
                    if(!$v){
                        $v = null;
                    }
                    $value->$k = array('label' => $k, 'value' => $v); //translasi
                }
            }else{
                if(!$value){
                    $value = "";
                }
                $data->$key = array('label' => $key, 'value' => $value); //translasi
            }
        }
        return $data;
    }
}
//Added by yusuf.sutana@koltiva.com ==== (End)

if ( ! function_exists('load_lang'))
{
    function load_lang() {
        $CI = & get_instance();
        switch(@$_SESSION['language']) {
            case 'Indonesia':
                $CI->load->language('general', 'indonesia');
            break;
            case 'English':
                $CI->load->language('general', 'english');
            break;
            default:
                $CI->load->language('general', 'english');
            break;
        }
    }
}

if ( ! function_exists('nonconvert_language'))
{
    function nonconvert_language($data, $language) {
        foreach(@$data as $key => $value){
            if(is_object($value)){
                foreach($value as $k => $v){
                    /*
                    if(is_numeric($v)){
                        if($k != 'latitude' && $k != 'longitude')
                        $v = number_format($v, 0);
                    }
                    */
                    if(!$v){
                        $v = "";
                    }
                    $value->$k = array('label' => $k, 'value' => $v); //Non translasi
                }
            }else{
                $data->$key = array('label' => $key, 'value' => $value); //Non translasi
            }
        }
        return $data;
    }
}