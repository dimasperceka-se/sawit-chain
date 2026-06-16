<?php
/**
 * @Author: nikolius
 * @Date:   2016-06-03 17:11:28
 */
$baseurlnya = base_url();
// $baseurlnya = str_replace('http://','https://',$baseurlnya);
?>
<html lang="en" xmlns="http://www.w3.org/1999/html" moznomarginboxes mozdisallowselectionprint>
<head>
   <meta charset="utf-8"/>
   <title><?php echo $titleNya;?></title>

   <link rel="stylesheet" type="text/css" href="<?php echo $baseurlnya ?>assets/css/print_beneficiary/print_beneficiary.css"/>
   <link rel="stylesheet" type="text/css" href="<?php echo $baseurlnya ?>assets/css/print_beneficiary/print_beneficiary-media.css?1" media="print"/>

   <script src="<?php echo $baseurlnya;?>assets/js/print_beneficiary/jquery-1.8.3.min.js" type="text/javascript"></script>
   <!--<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyACXVwWCJen2OZeCAEYdRxP_HEh7CkxOvs"></script>-->
   <!--<script src="<?php echo $baseurlnya;?>assets/js/gmap3.js"></script>-->
</head>
<body>