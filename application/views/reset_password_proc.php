<?php
/**
 * @Author: nikolius
 * @Date:   2017-02-08 13:56:53
 */

$baseurlcheck = rtrim(trim(base_url()),'/');
$title        = "Koltiva | KoltiTrace Palm Oil";

if($this->config->item('url_gar') == $baseurlcheck) {
    $title = "GAR | KoltiTrace Palm Oil";
}
?>
<!doctype html>
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

        <!-- Vendor -->
        <script src="<?php echo base_url() ?>assets/vendor/jquery/jquery.js"></script>
        <script src="<?php echo base_url() ?>assets/vendor/jquery-browser-mobile/jquery.browser.mobile.js"></script>
        <script src="<?php echo base_url() ?>assets/vendor/bootstrap/js/bootstrap.js"></script>
    </head>
    <body>

        <section class="body-sign">
            <div class="center-sign" style="padding-top:0px!important">
                <a href="#" class="logo pull-left" onclick="return false;">
                    <img src="<?php echo base_url() ?>assets/images/logo.png" height="54" alt="Palmoiltrace" />
                </a>

                <div class="panel panel-sign">
                    <div class="panel panel-sign">
                    <div class="panel-title-sign mt-xl text-right">
                        <h2 class="title text-uppercase text-bold m-none" style="background-color:#95130b!important"><i class="fa fa-user mr-xs"></i> Reset Password</h2>
                    </div>

                    <div class="panel-body" style="border-top-color:#95130b!important;padding:20px 20px 8px 20px !important">

                        <?php if($prosesAll == true){?>
                        <div class="alert alert-success">
                            Successfully reset your password, please wait while redirecting
                        </div>

                        <div align="center">
                            <progress id="progressbar" value="0" max="100"></progress>
                        </div>

                        <script type="text/javascript">
                        $(document).ready(function(){
                            var progressbar = $('#progressbar'),max = progressbar.attr('max'),time = (1000/max)*5,value = progressbar.val();

                            var loading = function() {
                                value += 1;
                                addValue = progressbar.val(value);

                                $('.progress-value').html(value + '%');

                                if (value == max) {
                                    clearInterval(animate);

                                    //redirect masuk login
                                    window.location.href = '<?php echo base_url();?>';
                                }
                            };

                            var animate = setInterval(function() {
                                loading();
                            }, time);
                        });
                        </script>

                        <?php }else{?>

                        <div class="alert alert-danger">
                            <strong>Reset password failed</strong>
                            <br /><br />
                            <?php echo $pesanGagal;?>
                        </div>
                        <p class="text-center mt-lg">Back to <a href="<?php echo site_url('system/login') ?>" style="color:#95130b!important">login page</a>

                        <?php }?>
                    </div>
                </div>
            </div>
        </section>
        <!-- end: page -->


        <?php
        echo $this->load->view('login/footer_latest', NULL, TRUE);
        ?>

    </body>
</html>