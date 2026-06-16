<!DOCTYPE  html>
<html lang="en" xmlns="http://www.w3.org/1999/html">
<head>
    <meta charset="utf-8">
    <title>Farmer</title>
    <style type="text/css">
        body {
            margin: 0;
            padding: 0;
            font-family: Tahoma, Verdana, Helvetica, Arial;
            font-size: 14px;
            color: #000000;
            background-color: #ffffff;
        }
        * {
            box-sizing: border-box;
            -moz-box-sizing: border-box;
        }
        .page {
            width: 21cm;
            height:27.5cm;
            padding-top: 1cm;
            padding-bottom: 1cm;
            padding-right: 1cm;
            padding-left: 1cm;
            margin: 0.2cm auto;
            border: 1px #D3D3D3 solid;
            border-radius: 5px;
            background: white;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
            text-align: justify;
        }

        @page {
            size: A4 ;
            margin: 0;
        }

        @media print {
            .page {
                margin: initial;
                border: initial;
                border-radius: initial;
                width: initial;
                min-height: initial;
                box-shadow: initial;
                background: initial;
                background-color: initial;

                padding-bottom:0;
            }
           .header{border:90px #cccccc;}
        }

        textarea {
            font-family: Tahoma, Verdana, Helvetica, Arial;
            padding: 0;
        }
        a:link, a:visited { color: #0066CC; text-decoration: none}
        a:active, a:hover { color: #008800; text-decoration: underline}

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
            height: 25px;   /* 'padding-top' + 'height' must be equal to the 'background image height' */
            padding-top: 42px;
            padding-left: 30px;
            background: url(images/templatemo_top_bg.gif) no-repeat bottom;
        }
        #templatemo_header {
            clear: left;
            padding-top: 2px;
            height: 50px;
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
            float: left;
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
            font-size: 8px;
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
            margin-right: 8px;
            margin-bottom: 8px;
            border: 1px solid #000000;
        }
        .section_box {
            margin: 8px;
            padding: 8px;
            border: 1px dashed #ffffff;
            background: #FFFFFF;
            border: 1px solid #000000;
        }
        .section_box2 {
            clear: left;
            margin-top: 8px;
            background: #ffffff;
            color: #000000;
            font-weight: bold;
            border: 1px solid #000000;
        }
        .section_box3 {
            clear: left;
            margin-top: 8px;
            background: #ffffff;
            color: #000000;
            border: 1px;
        }
        .text_area {
            padding: 0px 2px 0px 2px;
            font-size: 8px;
        }
        .publish_date {
            clear: both;
            margin-top: 8px;
            color: #999999;
            font-size: 8px;
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
            padding-left: 8px;
            background: #cccccc;
            font-size: 12px;
            font-weight: bold;
            color: #000000;
          border-bottom: 1px solid #000000;
          text-align:left;
        }
        .post_title {
            padding: 2px;
            padding-left: 8px;
            background: #cccccc;
            font-size: 12px;
            font-weight: bold;
            color: #000000;
          border-bottom: 1px solid #000000;
          text-align:left;
        }
        .templatemo_menu {
            list-style-type: none;
            margin: 8px;
            margin-top: 0px;
            padding: 0px;
            width: 195px;
        }
        .templatemo_menu li a{
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
          border:1px solid #000000;
          font-family: Tahoma, Verdana, Helvetica, Arial;
          color: #000000;
          font-size: 8px;
        }
        .box13 {
            border:1px solid #000000;
            font-size: 8px;
        }
        .box_disabled {
            border:1px solid #000000;
            background-color:#CCCCCC;
        }
        .font11 {
          font-size: 8px;
        }
        .font12 {
            font-size: 8px;
        }
        .font13 {
            font-size: 8px;
        }
        .fontred {
            font-size: 12px;
            color:#F00;
        }
        * html .templatemo_menu li a{
            width: 190px;
        }
        .templatemo_menu li a:visited, .templatemo_menu li a:active{
            color: #000000;
        }
        .templatemo_menu li a:hover{
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
        input{
            border: 1px solid #000000;
            background-color: #FFF;
            font-size: 8px;
            padding: 0;
        }
        .page-break {
            page-break-before: always;
        }
    </style>
</head>
<body>