<?php
/**
 * @Author: nikolius
 * @Date:   2016-12-13 10:52:13
 */
$baseurlnya = base_url();
?>
<html lang="en" xmlns="http://www.w3.org/1999/html" moznomarginboxes mozdisallowselectionprint>
<head>
    <meta charset="utf-8"/>
    <title>Nursery Profile</title>

    <link rel="stylesheet" type="text/css" href="<?php echo $baseurlnya ?>assets/css/nursery_summary/nursery-summary.css"/>
    <link rel="stylesheet" type="text/css" href="<?php echo $baseurlnya ?>assets/css/nursery_summary/nursery-summary-media.css" media="print"/>

    <script src="<?php echo $baseurlnya; ?>assets/js/print_beneficiary/jquery-1.8.3.min.js" type="text/javascript"></script>
    <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyACXVwWCJen2OZeCAEYdRxP_HEh7CkxOvs"></script>
    <script src="<?php echo $baseurlnya; ?>assets/js/gmap3.js"></script>

    <style type="text/css">
    .gm-style-cc:last-child {
        display: none !important;
    }
    a[title="Report errors in the road map or imagery to Google"] {
        display: none !important;
    }
    a[href="https://www.google.com/intl/en-US_US/help/terms_maps.html"] {
        display: none !important;
    }
    </style>
</head>
<body>