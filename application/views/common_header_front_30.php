<?php
$baseurlcheck = rtrim(trim(base_url()),'/');
$title        = "Koltiva | KoltiTrace Palm Oil";

if($this->config->item('url_gar') == $baseurlcheck) {
    $title = "GAR | KoltiTrace Palm Oil";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title><?= $title ?></title>
    <link href="https://dptwplzs7m8x9.cloudfront.net/web/logo/koltiva_k_green_square.png" rel="shortcut icon">

    <meta name="keywords" content="PalmOilTrace" />
    <meta name="description" content="PalmOilTrace">

    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700,800|Shadows+Into+Light" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="<?php echo base_url() ?>assets/vendor/bootstrap/css/bootstrap.css" />
    <link rel="stylesheet" href="<?php echo base_url() ?>assets/vendor/font-awesome/css/font-awesome.css" />

    <link rel="stylesheet" href="<?php echo base_url() ?>assets/stylesheets/koltiva.3.0.css">

    <script src="<?php echo base_url() ?>assets/vendor/jquery/jquery.js"></script>
    <script src="<?php echo base_url() ?>assets/vendor/bootstrap/js/bootstrap.js"></script>
    <script src="<?php echo base_url() ?>js/functions.js" type="text/javascript"></script>
</head>
<body>
