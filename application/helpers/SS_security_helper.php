<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * Fetch the IP Address
 *
 * @return	string
 * added by trinurika
 * update 2016-01-14
 */
if (!function_exists('ip_address')) {

    function ip_address() {
        if (!empty($_SERVER["HTTP_CLIENT_IP"])) {
            $ip = $_SERVER["HTTP_CLIENT_IP"];
        } elseif (!empty($_SERVER["HTTP_X_FORWARDED_FOR"])) {
            $ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
        } else {
            $ip = $_SERVER["REMOTE_ADDR"];
        }

        return $ip;
    }

}

/**
 * User Agent
 *
 * @return	string
 * added by trinurika
 * update 2016-01-14
 */
if (!function_exists('user_agent')) {

    function user_agent() {
        $user_agent = (!isset($_SERVER['HTTP_USER_AGENT'])) ? FALSE : $_SERVER['HTTP_USER_AGENT'];

        return $user_agent;
    }

}