<?php
/******************************************
 *  Author : n1colius.lau@gmail.com   
 *  Created On : Thu Jul 16 2020
 *  File : common_header_front.php
 *******************************************/
$baseurlcheck = rtrim(trim(base_url()),'/');
$title        = "SawitChain | Traceability System for Palm Oil";

if($this->config->item('url_gar') == $baseurlcheck) {
    $title = "GAR | KoltiTrace Palm Oil";
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="keywords" content="Palmoiltrace" />
    <meta name="description" content="Palmoil Trace Application">
    <meta name="author" content="SawitChain">

    <title><?= $title ?></title>

    <link href="/assets/new/img/sawitchain-full-logo.png" rel="shortcut icon">

    <!-- Web Font, Open Sans -->
    <link href="<?php echo $this->config->item('http'); ?>://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700,800|Shadows+Into+Light" rel="stylesheet" type="text/css">

    <!-- Vendor CSS -->
    <link rel="stylesheet" href="<?php echo base_url() ?>assets/vendor/bootstrap/css/bootstrap.css" />
    <link rel="stylesheet" href="<?php echo base_url() ?>assets/vendor/font-awesome/css/font-awesome.css" />

    <!-- Theme CSS -->
    <link rel="stylesheet" href="<?php echo base_url() ?>assets/stylesheets/theme.css" />
    <!-- Skin CSS -->
    <link rel="stylesheet" href="<?php echo base_url() ?>assets/stylesheets/skins/default.css" />
    <!-- Theme Custom CSS -->
    <link rel="stylesheet" href="<?php echo base_url() ?>assets/stylesheets/theme-custom.css">
    <!-- Custom Modernizr -->
    <script src="<?php echo base_url() ?>js/plugins/modernizr.custom.32549.js"></script>

    <link rel="stylesheet" href="<?php echo base_url() ?>assets/stylesheets/koltiva.css">

    <script src="<?php echo base_url() ?>js/jquery-1.11.1.min.js"></script>
    <script src="<?php echo base_url() ?>js/functions.js" type="text/javascript"></script>

    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
        <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

    <style>
        /* Adjust this value for each Product */
        .theme-color {
            color: #95130b;
        }

        .bg-theme-color {
            background-color: #95130b;
            color: #fafafa;
        }

        .btn-theme-color {
            background-color: #95130b;
            color: #fafafa;
            filter: brightness(85%);
        }

        .btn-theme-color:hover,
        .btn-theme-color:active,
        .btn-theme-color:focus,
        .btn-theme-color:visited {
            filter: brightness(100%);
            color: #fff;
        }

        .border-top-theme-color {
            border-color: #95130b !important;
        }
    </style>
</head>

<body class="login-page">