<?php
$baseurlcheck = rtrim(trim(base_url()),'/');
$title        = "SawitChain | Traceability System for Palm Oil";

if($this->config->item('url_gar') == $baseurlcheck) {
    $title = "GAR | KoltiTrace Palm Oil";
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title><?= $title ?></title>
    <link href="<?php echo base_url(); ?>img/favicon.png" rel="shortcut icon">
    <link href="<?php echo base_url(); ?>css/bootstrap_3.3.0.min.css" rel="stylesheet">
    <link href="<?php echo base_url(); ?>css/custom-login.css" rel="stylesheet">

    <style>
    body{
        background-color: #444;
    }

    #bg {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
    }

    @media screen and (min-width: 1200px) {
        #bg img {
            width: 100%;
        }
    }
    @media screen and (min-height: 800px) {
        #bg img {
            height: 100%;
        }
    }

    .vertical-offset-100{
        padding-top:100px;
    }

    .panel {
        background-color: rgba(245, 245, 245, 0.85);
        padding: 0 20px;
    }
    .col-centered{
        float: none;
        margin: 0 auto;
    }
    </style>

    <!-- <script src="<?php echo base_url(); ?>js/jquery-1.11.1.min.js"></script> -->
</head>

<body>
    <div id="bg"><img src="<?php echo base_url() ?>img/background/7.jpg" alt=""></div>
    <div class="container">
        <div class="row vertical-offset-100">
            <div class="col-md-5 col-centered">
                <div class="panel panel-default">
                    <!-- <div class="panel-heading">
                        <h3 class="panel-title">Please sign in</h3>
                    </div> -->
                    <div class="panel-body">
                        <div class="row">
                            <img src="<?php echo base_url(); ?>img/logo_horizontal.png" alt="" style="display: block; margin: 20px auto;">
                        </div>

                        <?php if (!empty($msg)): ?>
                        <div class="alert alert-<?php echo $type ?>">
                            <?php echo $msg; ?>
                        </div>
                        <?php endif ?>

                            <fieldset>
                                <div class="form-group">
                                    <div class="col-md-12 control">
                                        <div style="padding-top:15px; font-size:85%" class="text-center" >
                                        <a href="<?php echo site_url('system/login') ?>" style="color:#333">
                                            Back to login form
                                        </a>
                                        </div>
                                    </div>
                                </div>
                            </fieldset>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="navbar navbar-fixed-bottom">
        <div class="container">
            <p class="navbar-text text-center" style="color:#fff;float: none;font-size:0.9em;">
                <b>&nbsp; &copy; <?php echo date("Y"); ?> PT. SawitChain, All rights reserved</b>
            </p>
        </div>
    </div>
</body>

</html>
