<?php
$baseurlcheck = rtrim(trim(base_url()),'/');
$title        = "Koltiva | KoltiTrace Palm Oil";

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
    
    <!-- <?php if(!$this->session->userdata('cookieAccepted')): ?>
    <div id="privacyAlert" class="alert alert-warning alert-dismissible fixed-top" role="alert">
      <p><i class="fa fa-exclamation-circle" style="font-size: 16px;"></i> &nbsp; This website uses cookies to improve our services. By using this site, you agree on its use. For more information please check our <a style="color: #222; text-decoration: underline" href="/privacy-policy.php" target="_blank">Privacy Policy</a> page.</p>
      <button type="button" class="btn btn-sm btn-secondary btn-pill" onClick="acceptCookie()">Accept</button>
    </div>
    <?php endif; ?> -->
    
    <div id="loginRow" class="row mt-10">
        <div class="col-xs-12 col-sm-7 col-md-6 col-lg-5 login-box">
            <a href="#" class="logo" onclick="return false;">
                <img class="img-reponsive" src="<?php echo base_url() ?>assets/images/logo.png" alt="CoconutTrace" />
            </a>
    
            <div class="panel">
                <div class="panel-heading bg-theme-color text-uppercase">
                    <h5><i class="fa fa-key"></i> Recovery</h5>
                </div>
                <div class="panel-body border-top-theme-color">
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

                        <div class="form-group">
                            <button type="submit" class="btn btn-theme-color btn-block btn-lg">Send Request</button>
                            <input type="hidden" name="isPost" value="1" />
                        </div>
                        <p class="text-center mt-lg">Remembered? <a href="<?php echo site_url('system/login') ?>" style="color:##72635E!important">Sign In!</a>
                    </form>
                </div>
            </div>

        </div>
    </div>
    
    <div class="art">
        <img class="img-responsive theme-wallpaper" src="<?php echo base_url() ?>assets/images/wallpaper_palmoil_v2.jpg" alt="" srcset="">        
        <h4><strong>ABOUT <span class="theme-color">PALMOIL</span><span style="color:#47A447;">TRACE</span></strong></h4>
        <p>PalmoilTrace is a web and mobile application that focuses on palm traceability and sustainability program management. With PalmoilTrace, palm and palm oil companies are able to verify supply chain sustainability and fair working practices from independent palm smallholders farms, through the trade, to its processing facilities.</p>
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
    </script>
  </body>
</html>