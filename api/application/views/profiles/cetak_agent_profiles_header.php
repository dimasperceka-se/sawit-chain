<?php
/**
 * @Author: nikolius
 * @Date:   2017-12-29 16:28:31
 * @Last Modified by:   nikolius
 * @Last Modified time: 2017-12-29 16:34:41
 */
$baseurlnya = base_url();
?>
<html lang="en" xmlns="http://www.w3.org/1999/html" moznomarginboxes mozdisallowselectionprint>
<head>
   <meta charset="utf-8"/>
   <title><?php echo $titleNya;?></title>

   <link rel="stylesheet" type="text/css" href="<?php echo $baseurlnya ?>assets/css/profiles/agent-profiles.css"/>
   <link rel="stylesheet" type="text/css" href="<?php echo $baseurlnya ?>assets/css/profiles/agent-profiles-media.css" media="print"/>

   <script src="<?php echo $baseurlnya;?>assets/js/print_beneficiary/jquery-1.8.3.min.js" type="text/javascript"></script>
   <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyACXVwWCJen2OZeCAEYdRxP_HEh7CkxOvs"></script>
   <script src="<?php echo $baseurlnya;?>assets/js/gmap3.js"></script>
</head>
<body>