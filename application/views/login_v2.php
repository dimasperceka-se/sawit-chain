<?php
$baseurlcheck = rtrim(trim(base_url()),'/');
$title        = "SawitChain";

if($this->config->item('url_gar') == $baseurlcheck) {
    $title = "GAR | KoltiTrace Palm Oil";
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="keywords" content="Palmoiltrace" />
    <meta name="description" content="Palmoil Trace Application">
    <meta name="author" content="Koltiva">

    <title><?= $title ?></title>
    
    <link href="<?php echo base_url();?>img/favicon.png" rel="shortcut icon">

    <!-- Web Font, Open Sans -->
    <link href="<?php echo $this->config->item('http'); ?>://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700,800|Shadows+Into+Light" rel="stylesheet" type="text/css">

    <!-- Vendor CSS -->
    <link rel="stylesheet" href="<?php echo base_url() ?>assets/vendor/bootstrap/css/bootstrap.css" />
    <link rel="stylesheet" href="<?php echo base_url() ?>assets/vendor/font-awesome/css/font-awesome.css" />

    <!-- Theme CSS -->
    <link rel="stylesheet" href="<?php echo base_url() ?>assets/stylesheets/theme.css" />
    <!-- Skin CSS -->
    <link rel="stylesheet" href="<?php echo base_url() ?>assets/stylesheets/skins/default.css" />
    <!-- Theme Custom CSS -->
    <link rel="stylesheet" href="<?php echo base_url() ?>assets/stylesheets/theme-custom.css">
    <!-- Custom Modernizr -->
    <script src="<?php echo base_url() ?>js/plugins/modernizr.custom.32549.js"></script>

    <link rel="stylesheet" href="<?php echo base_url() ?>assets/stylesheets/koltiva.css">

    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

    <style>
        /* Adjust this value for each Product */
        .theme-color{
            color: #95130b;
        }
        .bg-theme-color{
            background-color: #95130b;
            color: #fafafa;
        }
        .btn-theme-color{
            background-color: #95130b;
            color: #fafafa;
            filter: brightness(85%);
        }
        .btn-theme-color:hover,
        .btn-theme-color:active,
        .btn-theme-color:focus,
        .btn-theme-color:visited{
            filter: brightness(100%);
            color: #fff;
        }
        .border-top-theme-color{
            border-color: #95130b !important;
        }
    </style>
  </head>
  <body class="login-page">
    
    <?php if(!$this->session->userdata('cookieAccepted')): ?>
    <div style="display:none;" id="privacyAlert" class="alert alert-warning alert-dismissible fixed-top" role="alert">
      <p><i class="fa fa-exclamation-circle" style="font-size: 16px;"></i> &nbsp; This website uses cookies to improve our services. By using this site, you agree on its use. For more information please check our <a style="color: #222; text-decoration: underline" href="<?=base_url()?>term-and-condition" target="_blank">Privacy policy, Term and Conditions</a> page.</p>
      <button type="button" class="btn btn-sm btn-secondary btn-pill" onClick="acceptCookie()">Accept</button>
    </div>
    <?php endif; ?>

    <div id="loginRow" class="row mt-10">
        <div class="col-xs-12 col-sm-7 col-md-5 col-lg-4 login-box">
            <a href="#" class="logo" onclick="return false;">
                <img class="img-reponsive" src="<?php echo base_url() ?>assets/images/logo.png" alt="PalmoilTrace" />
            </a>
    
            <div class="panel">
                <div class="panel-heading bg-theme-color text-uppercase">
                    <h5><i class="fa fa-user"></i> Sign In</h5>
                </div>
                <div class="panel-body border-top-theme-color">
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
                                <a href="<?php echo site_url('system/login/forgot') ?>" class="pull-right" style="color:#72635E!important">Lost Password?</a>
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
                        <div class="form-group">
                            <button type="submit" class="btn btn-theme-color btn-block btn-lg">Sign In</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <div class="art" style="margin-top:-7px;">
        <img class="img-responsive theme-wallpaper" src="<?php echo base_url() ?>assets/images/wallpaper_palmoil_v2.jpg" alt="" srcset="">        
        <h5 style="margin-bottom: 7px;"><strong>ABOUT <span class="theme-color">PALMOIL</span><span style="color:#47A447;">TRACE</span></strong></h5>
        <p>
            The PalmOilTrace Supply Chain and Farm Management Information System includes web and mobile applications for transparent sustainability program management and product traceability to and from farms to factories and consumers.​ <br><br>
            Environmental, social, and economic sustainability starts with traceability - reducing risks through multilayered global supply chains and connecting smallholders, traders, processing industry, input suppliers and banks in the PalmOilTrace end-to-end traceability platform.
        </p>
    </div>

    <?php echo $this->load->view('login/footer_col3_v2', NULL, TRUE); ?>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script src="<?php echo base_url() ?>assets/vendor/bootstrap/js/bootstrap.min.js"></script>
    <script>
        function acceptCookie(){
            $.ajax({
                url: '<?php echo base_url() ?>system/login/accept_cookie',
                success: function(){
                    $('#privacyAlert').alert('close');
                }
            });
        }

        $(function () {
            localStorage.clear();
            $('[data-toggle="tooltip"]').tooltip();
        });
    </script>
  </body>
</html>