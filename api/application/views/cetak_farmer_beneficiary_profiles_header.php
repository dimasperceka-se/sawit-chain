<?php
/**
 * @Author: nikolius
 * @Date:   2016-04-13 15:42:31
 */
?>
<!DOCTYPE  html>
<html lang="en" xmlns="http://www.w3.org/1999/html">
<head>
   <meta charset="utf-8">
   <title>Farmer</title>
   <style type="text/css">
      @media print {
         @page {
            /*margin: 0.5cm 0.5cm 0cm 0.5cm;*/
            padding: 0cm;
            overflow:hidden;
         }

         #page
         {
            z-index: 1;
            width: 688px !important;
            height: 950px !important;
            background-image: none;
            border-style: none;
            border-color: #000000;
            #background-color: transparent;
            background: white;
            #margin-top:0px;
            #margin-left: auto;
            #margin-right: auto;
            margin:0 auto;
            #padding: 18px 11px 83px 12px;
            padding-top:20px;
            padding-left:110px;
            overflow:hidden;
         }

         .page-break {
            display: block;
            page-break-before: always;
         }

         .page-break-after {
            display: block;
            page-break-after: always;
         }
      }

      page[size="A4"] {
         background: white;
         width: 19cm;
         height: auto;
         display: block;
         margin: 0 auto;
         margin-bottom: 0.5cm;
         box-shadow: 0 0 0.5cm rgba(0,0,0,0.5);
      }

   </style>

   <!-- CSS -->
   <link rel="stylesheet" type="text/css" href="<?php echo base_url() ?>assets/css/print_beneficiary/site_global.css"/>
   <link rel="stylesheet" type="text/css" href="<?php echo base_url() ?>assets/css/print_beneficiary/index.css" />

   <script type="text/javascript">
      if(typeof Muse == "undefined") window.Muse = {}; window.Muse.assets = {"required":["jquery-1.8.3.min.js", "museutils.js", "jquery.watch.js", "index.css"], "outOfDate":[]};
   </script>

   <script type="text/javascript">
      document.documentElement.className += ' js';
      var __adobewebfontsappname__ = "muse";
   </script>

   <!-- JS includes -->
  <script type="text/javascript">
      document.write('\x3Cscript src="' + (document.location.protocol == 'https:' ? 'https:' : 'http:') + '//webfonts.creativecloud.com/varela-round:n4:default.js" type="text/javascript">\x3C/script>');
   </script>

   <script src="<?php echo base_url();?>assets/js/print_beneficiary/jquery-1.8.3.min.js" type="text/javascript"></script>
   <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyACXVwWCJen2OZeCAEYdRxP_HEh7CkxOvs"></script>
   <script src="<?php echo base_url() ?>assets/js/gmap3.js"></script>
</head>
<body>