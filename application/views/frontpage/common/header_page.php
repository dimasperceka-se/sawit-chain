<?php
/**
 * @Author: nikolius
 * @Date:   2017-10-17 10:01:19
 * @Last Modified by:   nikolius
 * @Last Modified time: 2017-10-17 17:56:09
 */
$baseurlcheck = rtrim(trim(base_url()),'/');
$title        = "Koltiva | KoltiTrace Palm Oil";

if($this->config->item('url_gar') == $baseurlcheck) {
    $title = "GAR | KoltiTrace Palm Oil";
}
?>
<!DOCTYPE html>
<html class="fixed">
    <head>
        <title><?= $title ?></title>
        <link href="<?php echo base_url();?>img/favicon.png" rel="shortcut icon">
        <!-- Basic -->
        <meta charset="UTF-8">

        <meta name="keywords" content="Palmoiltrace" />
        <meta name="description" content="Palmoiltrace">
        <meta name="author" content="nikolius.lau">

        <!-- Mobile Metas -->
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />

        <!-- Web Fonts  -->
        <link href="<?php echo $this->config->item('http'); ?>://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700,800|Shadows+Into+Light" rel="stylesheet" type="text/css">

        <!-- Vendor CSS -->
        <link rel="stylesheet" href="<?php echo base_url() ?>assets/vendor/bootstrap/css/bootstrap.css" />
        <link rel="stylesheet" href="<?php echo base_url() ?>assets/vendor/font-awesome/css/font-awesome.css" />
        <!-- <link rel="stylesheet" href="<?php echo base_url() ?>assets/vendor/magnific-popup/magnific-popup.css" /> -->
        <!-- <link rel="stylesheet" href="<?php echo base_url() ?>assets/vendor/bootstrap-datepicker/css/datepicker3.css" /> -->

        <!-- Theme CSS -->
        <link rel="stylesheet" href="<?php echo base_url() ?>assets/stylesheets/theme.css" />

        <!-- Skin CSS -->
        <link rel="stylesheet" href="<?php echo base_url() ?>assets/stylesheets/skins/default.css" />

        <!-- Theme Custom CSS -->
        <link rel="stylesheet" href="<?php echo base_url() ?>assets/stylesheets/theme-custom.css">

        <!-- Head Libs -->
        <!-- <script src="<?php echo base_url() ?>assets/vendor/modernizr/modernizr.js"></script> -->

        <!-- Vendor -->
        <script src="<?php echo base_url() ?>assets/vendor/jquery/jquery.js"></script>
        <script src="<?php echo base_url() ?>assets/js/moment.js"></script>
        <script src="<?php echo base_url() ?>assets/vendor/jquery-browser-mobile/jquery.browser.mobile.js"></script>
        <script src="<?php echo base_url() ?>assets/vendor/bootstrap/js/bootstrap.js"></script>

        <!-- datepicker -->
        <link rel="stylesheet" href="<?php echo base_url() ?>assets/plugins/Pikaday/css/pikaday.css">
        <script src="<?php echo base_url() ?>assets/plugins/Pikaday/pikaday.js"></script>

        <!-- Form Validation -->
        <script src="<?php echo base_url() ?>assets/plugins/jquery.validate.min.js"></script>

        <script type="text/javascript">
        function scrollToAnchor(aid){
            var aTag = $("a[name='"+ aid +"']");
            $('html,body').animate({scrollTop: aTag.offset().top},'slow');
        }
        </script>
    </head>
    <body>

    <!-- start: page -->
    <section class="body-sign">
        <div class="center-sign" style="padding-top:15px!important">
            <a href="#" class="logo pull-left" onclick="return false;">
                <img src="<?php echo base_url() ?>assets/images/logo.png" height="54" alt="Palmoiltrace" />
            </a>
