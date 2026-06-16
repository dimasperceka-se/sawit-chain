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
          width: 21cm;
          height: 29.7cm;
          display: block;
          margin: 0 auto;
          margin-bottom: 0.5cm;
          box-shadow: 0 0 0.5cm rgba(0,0,0,0.5);
        }
        @media print {
          body, page[size="A4"] {
            margin: 0;
            box-shadow: 0;
          }
        }

        body {
            margin: 0;
            padding: 0;
            font-family: Tahoma, Verdana, Helvetica, Arial;
            font-size: 11px;
            color: #000000;
            background-color: #ffffff;
        }

        textarea {
            font-family: Tahoma, Verdana, Helvetica, Arial;
            padding: 0;
        }

        a:link, a:visited {
            color: #0066CC;
            text-decoration: none
        }

        a:active, a:hover {
            color: #008800;
            text-decoration: underline
        }

        #templatemo_container_wrapper {
            /*background: url(images/templatemo_side_bg.gif) repeat-x;*/
            background: #ffffff;
        }

        #templatemo_container {
            margin: 0px auto;
            /*background: url(images/templatemo_content_bg.gif);*/
            background: #FFFFFF;
        }

        #templatemo_top {
            clear: left;
            height: 25px; /* 'padding-top' + 'height' must be equal to the 'background image height' */
            padding-top: 42px;
            padding-left: 30px;
            background: url(images/templatemo_top_bg.gif) no-repeat bottom;
        }

        #templatemo_header {
            clear: left;
            padding-top: 2px;
            height: 70px;
            text-align: center;
            font-weight: bold;
            font-size: 20px;
            color: #000000;
            /*background: url(images/templatemo_header_bg.gif) no-repeat;*/
        }

        #inner_header {
            height: 30px;
            background: url(images/templatemo_header.jpg) no-repeat center center;
        }

        #templatemo_left_column {
            clear: left;
            /*float: left;*/
            width: 100%;
        }

        #templatemo_right_column {
            float: right;
            width: 216px;
            padding-right: 15px;
        }

        #templatemo_footer {
            clear: both;
            /*padding-top: 18px;*/
            height: 15px;
            text-align: center;
            font-size: 11px;
            /*background: url(images/templatemo_footer_bg.gif) no-repeat;*/
            color: #ffffff;
        }

        #templatemo_footer a {
            color: #666666;
        }

        #templatemo_site_title {
            padding-top: 65px;
            font-weight: bold;
            font-size: 28px;
            color: #000000;
        }

        #templatemo_site_slogan {
            padding-top: 14px;
            font-weight: bold;
            font-size: 13px;
            color: #AAFFFF;
        }

        .templatemo_spacer {
            clear: left;
            height: 18px;
        }

        .templatemo_pic {
            float: left;
            margin-right: 10px;
            margin-bottom: 10px;
            border: 1px solid #000000;
        }

        .section_box {
            margin: 10px;
            padding: 10px;
            border: 1px dashed #ffffff;
            background: #FFFFFF;
            border: 1px solid #000000;
        }

        .section_box2 {
            clear: left;
            margin-top: 10px;
            background: #ffffff;
            color: #000000;
            font-weight: bold;
            border: 1px solid #000000;
        }

        .section_box3 {
            clear: left;
            margin-top: 10px;
            background: #ffffff;
            color: #000000;
            border: 1px;
        }

        .text_area {
            padding: 0px 2px 0px 2px;
        }

        .publish_date {
            clear: both;
            margin-top: 10px;
            color: #999999;
            font-size: 11px;
            font-weight: bold;
        }

        .title {
            padding-bottom: 12px;
            font-size: 18px;
            font-weight: bold;
            color: #000000;
        }

        .subtitle {
            padding-bottom: 6px;
            font-size: 14px;
            font-weight: bold;
            color: #666666;
        }

        .post_title_main {
            padding: 2px;
            padding-left: 10px;
            background: #cccccc;
            font-size: 12px;
            font-weight: bold;
            color: #000000;
            border-bottom: 1px solid #000000;
            text-align: left;
        }

        .post_title {
            padding: 2px;
            padding-left: 10px;
            background: #cccccc;
            font-size: 12px;
            font-weight: bold;
            color: #000000;
            border-bottom: 1px solid #000000;
            text-align: left;
        }

        .templatemo_menu {
            list-style-type: none;
            margin: 10px;
            margin-top: 0px;
            padding: 0px;
            width: 195px;
        }

        .templatemo_menu li a {
            background: #F4F4F4 url(images/button_default.gif) no-repeat;
            font-size: 12px;
            font-weight: bold;
            color: #000000;
            display: block;
            width: auto;
            margin-bottom: 2px;
            padding: 5px;
            padding-left: 12px;
            text-decoration: none;
        }

        .box {
            border: 1px solid #000000;
            font-family: Tahoma, Verdana, Helvetica, Arial;
            color: #000000;
            font-size: 11px;
        }

        .box13 {
            border: 1px solid #000000;
            font-size: 11px;
        }

        .box_disabled {
            border: 1px solid #000000;
            background-color: #CCCCCC;
        }

        .font11 {
            font-size: 11px;
        }

        .font12 {
            font-size: 11px;
        }

        .font13 {
            font-size: 11px;
        }

        .fontred {
            font-size: 12px;
            color: #F00;
        }

        * html .templatemo_menu li a {
            width: 190px;
        }

        .templatemo_menu li a:visited, .templatemo_menu li a:active {
            color: #000000;
        }

        .templatemo_menu li a:hover {
            background: #EEEEEE url(images/button_active.gif) no-repeat;
            color: #FF3333;
        }

        #templatemo_container_wrapper #templatemo_container #templatemo_left_column .text_area .section_box2 .post_title_main strong td {
            color: #000000;
        }

        #templatemo_container_wrapper #templatemo_container #templatemo_left_column .text_area .section_box2 .post_title_main {
            color: #000000;
        }

        div {
            color: #000000;
        }

        font_red {
            color: #F00;
        }

        input {
            border: 1px solid #000000;
            background-color: #FFF
        }

        .table_bordered {
            border-collapse: collapse;
            border-spacing: 0;
        }
        .table_bordered td {
            border: 1px solid #000000;
            padding: 4px;
        }
        .table_noborder {
            border-collapse: collapse;
            border-spacing: 0;
        }
        .table_noborder td {
            border: 0px solid #000000;
            padding: 4px;
        }
        .no_border {
            border: 0px none !important;
        }
        .table_map {
            font-size: 110%;
        }
    </style>
    <script src="<?php echo base_url() ?>assets/js/jquery.min.js"></script>
    <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyACXVwWCJen2OZeCAEYdRxP_HEh7CkxOvs"></script>
    <script src="<?php echo base_url() ?>assets/js/gmap3.js"></script>
</head>
<body>