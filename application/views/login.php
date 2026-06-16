<?php
$baseurlcheck = rtrim(trim(base_url()),'/');
$title        = "SawitChain";
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
        <meta name="author" content="okler.net">

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

        <!-- Cookies Div -->
        <script src="<?php echo base_url() ?>js/plugins/modernizr.custom.32549.js"></script>
        <style type="text/css">
            @-webkit-keyframes slideDown {
                0%, 100% { -webkit-transform: translateY(-50px); }
                10%, 90% { -webkit-transform: translateY(0px); }
            }
            @-moz-keyframes slideDown {
                0%, 100% { -moz-transform: translateY(-50px); }
                10%, 90% { -moz-transform: translateY(0px); }
            }

            #CookiesDiv {
                position: absolute;
                z-index: 101;
                color:black;
                top: 0;
                left: 0;
                right: 0;
                background: #fde073;
                text-align: center;
                line-height: 2.5;
                overflow: hidden;
                -webkit-box-shadow: 0 0 5px black;
                -moz-box-shadow:    0 0 5px black;
                box-shadow:         0 0 5px black;
            }

            .cssanimations.csstransforms #CookiesDiv {
                -webkit-transform: translateY(-50px);
                -webkit-animation: slideDown 30s linear 0s infinite forwards;

                -moz-transform:    translateY(-50px);
                -moz-animation:    slideDown 30s linear 0s infinite forwards;
            }
        </style>
    </head>
    <body>

        <div id="CookiesDiv"><img style="margin-top:-3px;" src="<?php echo base_url() ?>images/icons/silk/information.png" width="16" />&nbsp;&nbsp;We updated our privacy policies for this site, you can check in our privacy policies <a id="PrivacyClicked" href="#">page</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a title="close" href="#" id="CookiesDivClose"><img style="margin-top:-3px;" src="<?php echo base_url() ?>images/icons/silk/decline.png" width="16" /></a></div>

        <!-- start: page -->
        <section class="body-sign">
            <div class="center-sign" style="padding-top:0px!important">
                <a href="#" class="logo pull-left" onclick="return false;">
                    <img src="<?php echo base_url() ?>assets/images/logo.png" height="54" alt="Palmoiltrace" />
                </a>

                <div class="panel panel-sign">
                    <div class="panel-title-sign mt-xl text-right">
                        <h2 class="title text-uppercase text-bold m-none" style="background-color:#95130b!important"><i class="fa fa-user mr-xs"></i> Sign In</h2>
                    </div>
                    <div class="panel-body" style="border-top-color:#95130b!important;padding:20px!important">
                        <?php if (!empty($msg)): ?>
                        <div class="alert alert-danger">
                            <?php echo $msg; ?>
                        </div>
                        <?php endif ?>
                        <form action="<?php echo site_url('system/login/log_in') ?>" method="post">
                            <div class="form-group mb-lg">
                                <label>Username</label>
                                <div class="input-group input-group-icon">
                                    <input name="username" type="text" class="form-control input-lg" tabindex="1" />
                                    <span class="input-group-addon">
                                        <span class="icon icon-lg">
                                            <i class="fa fa-user"></i>
                                        </span>
                                    </span>
                                </div>
                            </div>

                            <div class="form-group mb-lg">
                                <div class="clearfix">
                                    <label class="pull-left">Password</label>
                                    <a href="<?php echo site_url('system/login/forgot') ?>" class="pull-right" style="color:#95130b!important" tabindex="4">Lost Password?</a>
                                </div>
                                <div class="input-group input-group-icon">
                                    <input name="password" type="password" class="form-control input-lg" tabindex="2" />
                                    <span class="input-group-addon">
                                        <span class="icon icon-lg">
                                            <i class="fa fa-lock"></i>
                                        </span>
                                    </span>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-12 text-right">
                                    <button type="submit" class="btn btn-success btn-lg hidden-xs col-sm-12" tabindex="3">Sign In</button>
                                    <button type="submit" class="btn btn-success btn-block btn-lg visible-xs mt-lg col-sm-12">Sign In</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </section>
        <!-- end: page -->

        <?php
        echo $this->load->view('login/footer_col3_v2', NULL, TRUE);
        ?>

        <!-- Vendor -->
        <script src="<?php echo base_url() ?>assets/vendor/jquery/jquery.js"></script>
        <script src="<?php echo base_url() ?>assets/vendor/jquery-browser-mobile/jquery.browser.mobile.js"></script>
        <script src="<?php echo base_url() ?>assets/vendor/bootstrap/js/bootstrap.js"></script>
        <!-- <script src="<?php echo base_url() ?>assets/vendor/nanoscroller/nanoscroller.js"></script> -->
        <!-- <script src="<?php echo base_url() ?>assets/vendor/bootstrap-datepicker/js/bootstrap-datepicker.js"></script> -->
        <!-- <script src="<?php echo base_url() ?>assets/vendor/magnific-popup/magnific-popup.js"></script> -->
        <!-- <script src="<?php echo base_url() ?>assets/vendor/jquery-placeholder/jquery.placeholder.js"></script> -->

        <!-- Theme Base, Components and Settings -->
        <!-- <script src="<?php echo base_url() ?>assets/javascripts/theme.js"></script> -->

        <!-- Theme Custom -->
        <!-- <script src="<?php echo base_url() ?>assets/javascripts/theme.custom.js"></script> -->

        <!-- Theme Initialization Files -->
        <!-- <script src="<?php echo base_url() ?>assets/javascripts/theme.init.js"></script> -->

        <!-- Cookies Div -->
        <script>
            close = document.getElementById("CookiesDivClose");
            close.addEventListener('click', function() {
                note = document.getElementById("CookiesDiv");
                note.style.display = 'none';
            }, false);

            PrivacyClicked = document.getElementById("PrivacyClicked");
            PrivacyClicked.addEventListener('click', function() {
                //Add Cookies
                document.cookie = "policyclick=sudah; path=/";
                window.open('','_blank');

                note = document.getElementById("CookiesDiv");
                note.style.display = 'none';
            }, false);

            function getCookie(cname) {
                var name = cname + "=";
                var decodedCookie = decodeURIComponent(document.cookie);
                var ca = decodedCookie.split(';');
                for(var i = 0; i <ca.length; i++) {
                    var c = ca[i];
                    while (c.charAt(0) == ' ') {
                        c = c.substring(1);
                    }
                    if (c.indexOf(name) == 0) {
                        return c.substring(name.length, c.length);
                    }
                }
                return "";
            }


            //Cek Cookies
            var CookiesPolicy = getCookie('policyclick');
            if(CookiesPolicy == 'sudah'){
                note = document.getElementById("CookiesDiv");
                note.style.display = 'none';
            }
        </script>
    </body>
</html>