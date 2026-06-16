<?php
/**
 * @Author: nikolius
 * @Date:   2017-03-02 12:03:44
 */
$baseurlcheck = rtrim(trim(base_url()),'/');
$title        = "Koltiva | KoltiTrace Palm Oil";

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

        <style type="text/css">
            #formReset .has-error .control-label,
            #formReset .has-error .help-block,
            #formReset .has-error .form-control-feedback {
                color: #f39c12;
            }

            #formReset .has-success .control-label,
            #formReset .has-success .help-block,
            #formReset .has-success .form-control-feedback {
                color: #18bc9c;
            }
        </style>
    </head>
    <body>
        <!-- start: page -->
        <section class="body-sign">
            <div class="center-sign" style="padding-top:0px!important">
                <a href="#" class="logo pull-left" onclick="return false;">
                    <img src="<?php echo base_url() ?>assets/images/logo.png" height="54" alt="Palmoiltrace" />
                </a>

                <div class="panel panel-sign">
                    <div class="panel panel-sign">
                    <div class="panel-title-sign mt-xl text-right">
                        <h2 class="title text-uppercase text-bold m-none" style="background-color:#95130b!important"><i class="fa fa-user mr-xs"></i> Change Password</h2>
                    </div>

                    <div class="panel-body" style="border-top-color:#95130b!important;padding:20px 20px 8px 20px !important">

                        <div class="alert alert-info">
                            <p style="margin-bottom:10px;" class="m-none text-semibold h6">This is your first time log in, you must change your password.<br />Your password must fit these criteria</p><br />
                            <ul>
                                <li>Minimal 8 characters</li>
                                <li>Maximal 14 characters</li>
                                <li>Contains lowercase [a-z], uppercase [A-Z], numbers [0-9], and special characters [!@#$%^&*[](){}]</li>
                            </ul>
                        </div>

                        <form id="formReset" action="<?php echo site_url('system/login/changepass_first_proc') ?>" method="post">
                            <!-- <div id="divGroupNewPass" class="form-group mb-lg has-error"> -->
                            <div id="divGroupNewPass" class="form-group mb-lg">
                                <label>New Password</label>
                                <div class="input-group input-group-icon">
                                    <input name="newPass" id="newPass" type="password" class="form-control input-lg" />
                                    <span class="input-group-addon">
                                        <span class="icon icon-lg">
                                            <i id="iconNewPass" class="formReset-input fa fa-lock"></i>
                                            <!--<i class="fa fa-check"></i>-->
                                        </span>
                                    </span>
                                </div>
                            </div>

                            <!-- <div id="divGroupNewPassConf" class="form-group mb-lg has-success"> -->
                            <div id="divGroupNewPassConf" class="form-group mb-lg">
                                <label>Retype Password</label>
                                <div class="input-group input-group-icon">
                                    <input name="newPassConf" id="newPassConf" type="password" class="form-control input-lg" />
                                    <span class="input-group-addon">
                                        <span class="icon icon-lg">
                                            <i id="iconNewPassConf" class="formReset-input fa fa-lock"></i>
                                            <!--<i class="fa fa-times"></i>-->
                                        </span>
                                    </span>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-sm-12 text-right">
                                    <button id="formReset-btnSubmit" disabled="" type="submit" class="btn btn-success btn-lg hidden-xs col-sm-12">Change Password & Login</button>
                                </div>
                            </div>
                            <br />

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

        <script type="text/javascript">

            $(document).on( "blur", "#newPass", function(e) {
                var passNya = String($(this).val());
                var regexRule=  /^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[^a-zA-Z0-9])(?!.*\s).{8,14}$/;

                if(passNya.match(regexRule)){
                    $("#divGroupNewPass").attr('class','form-group mb-lg has-success');
                    $("#iconNewPass").attr('class','formReset-input fa fa-check');
                }else{
                    $("#divGroupNewPass").attr('class','form-group mb-lg has-error');
                    $("#iconNewPass").attr('class','formReset-input fa fa-times');
                }

                cekSubmitButton();
            });

            $(document).on( "blur", "#newPassConf", function(e) {
                var passNya = String($("#newPass").val());
                var passConfNya = String($(this).val());

                if(passNya == passConfNya){
                    $("#divGroupNewPassConf").attr('class','form-group mb-lg has-success');
                    $("#iconNewPassConf").attr('class','fa fa-check');

                }else{
                    $("#divGroupNewPassConf").attr('class','form-group mb-lg has-error');
                    $("#iconNewPassConf").attr('class','fa fa-times');
                }

                cekSubmitButton();
            });

            function cekSubmitButton() {
                var validForm = true;
                $('.form-group').each(function() {
                    if($(this).hasClass('has-error')){
                        validForm = false;
                    }
                });

                $('.form-control').each(function(){
                    if($(this).val() == ""){
                        validForm = false;
                    }
                });

                if(validForm){
                    $("#formReset-btnSubmit").removeAttr('disabled');
                }else{
                    $("#formReset-btnSubmit").attr('disabled','');
                }
            }
        </script>
    </body>
</html>