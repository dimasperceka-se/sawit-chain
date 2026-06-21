<?php
$baseurlcheck = rtrim(trim(base_url()),'/');
$title        = "SawitChain | Traceability System for Palm Oil";

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

    </head>
    <body>
        <!-- start: page -->
        <section class="body-sign">
            <div class="center-sign" style="padding-top:0px!important">
                <a href="#" class="logo pull-left" onclick="return false;">
                    <img src="<?php echo base_url() ?>assets/images/logo.png" height="54" alt="Palmoiltrace" />
                </a>

                <div class="panel panel-sign">
                    <div class="panel-title-sign mt-xl text-right">
                        <h2 class="title text-uppercase text-bold m-none" style="background-color:#95130b!important"><i class="fa fa-user mr-xs"></i> Recover Password</h2>
                    </div>
                    <div class="panel-body" style="border-top-color:#95130b!important;padding:20px 20px 8px 20px !important">
                        <div class="alert alert-info">
                            <p class="m-none text-semibold h6">Enter your username below and we will send you reset instructions to your registered email</p>
                        </div>

                        <?php if (!empty($msg)): ?>
                            <div class="alert alert-<?php echo $type ?>">
                                <?php echo $msg; ?>
                            </div>
                        <?php endif ?>

                        <?php if($isPost == 1){?>
                        <?php if (validation_errors()): ?>
                            <div class="alert alert-danger">
                                <?php echo validation_errors(); ?>
                            </div>
                        <?php endif ?>
                        <?php }?>

                        <form action="<?php echo site_url('system/login/forgot') ?>" method="post">
                            <div class="form-group mb-lg">
                                <label>Username</label>
                                <div class="input-group input-group-icon">
                                    <input name="username" type="text" maxlength="50" class="form-control input-lg" />
                                    <span class="input-group-addon">
                                        <span class="icon icon-lg">
                                            <i class="fa fa-envelope"></i>
                                        </span>
                                    </span>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-12 text-right">
                                    <button type="submit" class="btn btn-success btn-lg hidden-xs col-sm-12">Send Request</button>
                                    <input type="hidden" name="isPost" value="1" />
                                </div>
                            </div>
                            <p class="text-center mt-lg">Remembered? <a href="<?php echo site_url('system/login') ?>" style="color:#95130b!important">Sign In!</a>
                        </form>
                    </div>
                </div>
            </div>
        </section>
        <!-- end: page -->

        <?php
        echo $this->load->view('login/footer_latest', NULL, TRUE);
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

    </body>
</html>