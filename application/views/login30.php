<?php 
$frontLang = 0; //count front language, 0 will display only "English"
?>

<?php if (!$this->session->userdata('cookieAccepted')) : ?>
    <div style="display:none;" id="privacyAlert" class="alert alert-warning alert-dismissible fixed-top" role="alert">
        <p><i class="fa fa-exclamation-circle" style="font-size: 16px;"></i> &nbsp; This website uses cookies to improve our services. By using this site, you agree on its use. For more information please check our <a style="color: #222; text-decoration: underline" href="<?= base_url() ?>term-and-condition" target="_blank">Privacy policy, Term and Conditions</a> page.</p>
        <button type="button" class="btn btn-sm btn-secondary btn-pill" onClick="acceptCookie()">Accept</button>
    </div>
<?php endif; ?>

<div class="row-fluid">
    <div class="col-md-8 left-panel">
        <div class="container">
            
            <div class="welcome">
                <div class="row">
                    <div class="col-md-10">
                        <h4>Welcome to</h4>
                        <h1 class="product-name">Palmoil<span>Trace</span></h1>
                        <br/>
                    </div>
                    <div class="col-md-2 text-right">
                        <!-- <button class="btn btn-default btn-xs dropdown-toggle lang-button" type="button">
                            <i class="fa fa-globe"></i> English
                        </button> -->
                    </div>
                </div>

                <div id="textCarousel" class="carousel slide" data-ride="carousel">
                    <div class="carousel-inner" role="listbox">
                        <div class="item active">
                            <strong>PalmOilTrace, </strong>The PalmOilTrace Supply Chain and Farm Management Information System includes web and mobile applications for transparent sustainability program management and product traceability to and from farms to factories and consumers.​
                        </div>
                        <div class="item">
                            With <strong>PalmOilTrace</strong>, Environmental, social, and economic sustainability starts with traceability - reducing risks through multilayered global supply chains and connecting smallholders, traders, processing industry, input suppliers and banks in the PalmOilTrace end-to-end traceability platform.
                        </div>
                    </div>
                    <ol class="carousel-indicators ktv-carousel-indicators">
                        <li data-target="#textCarousel" data-slide-to="0" class="active"></li>
                        <li data-target="#textCarousel" data-slide-to="1"></li>
                    </ol>
                </div>
            </div>

            

            <img src="<?php echo base_url() ?>assets/images/illustration_palmoil.svg" alt="PalmOil Illustration" class="center-walpaper" width="700" height="300">

            <div class="welcome-footer row">
                <div class="col-md-4">
                    <img src="https://dptwplzs7m8x9.cloudfront.net/web/koltiva-logo-beyond-traceability-right_rgb.svg" alt="Koltiva" width="200">
                </div>
                <div class="col-md-3">
                </div>
                <div class="col-md-5 welcome-footer-links text-right">
                    <ul class="list-inline ">
                        <li><a href="https://www.koltiva.com/#aboutus" target="_blank" rel="noopener noreferrer">About Us</a></li>
                        <li><a href="https://www.koltiva.com/#contact" target="_blank" rel="noopener noreferrer">Contact Us</a></li>
                        <li><a href="https://www.koltiva.com#services" target="_blank" rel="noopener noreferrer">Services</a></li>
                        <li class="link-icon"><a href="https://www.linkedin.com/company/pt-koltiva/" target="_blank" rel="noopener noreferrer"><i class="fa fa-linkedin"></i></a></li>
                        <li class="link-icon"><a href="https://www.youtube.com/channel/UCEEMooggYyT1lQHRl-jJReQ" target="_blank" rel="noopener noreferrer"><i class="fa fa-youtube-play"></i></a></li>
                    </ul>
                </div>
            </div>
            
        </div>
    </div>
    <div class="col-md-4 right-panel">
        <div class="flex">
            <div class="side-logo"></div>
            <div class="logo">
                <img src="<?php echo base_url() ?>assets/images/logo.png" class="img-responsive" alt="PalmOilTrace">
            </div>
            <div class="side-logo"></div>
        </div>
        <br />
        <br />
        <br />
        <div class="form-content"><!-- Form Container for Flex -->

            <?php if (!empty($msg)): ?>
                <div class="alert alert-danger">
                    <?php echo $msg; ?>
                </div>
            <?php endif; ?>

            <div class="alert alert-danger" id="boxFailed"></div>
            <div class="alert alert-success" id="boxSuccess"><?php if($frontProcessMsg != "") echo $frontProcessMsg;?></div>

            <div class="row form-info">
                <div class="col-md-9">
                    <h4 class="primary-color"><strong>Safe and Secure</strong><img id="ajaxImgLoad" style="display: none;" src="<?php echo base_url() ?>images/dg/ajax.gif" /></h4>
                    <p>Sign in to your Account!</p>
                </div>
            </div>

            <form class="login-form" method="post">
                <div class="form-group">
                    <div class="input-group input-group-icon">
                        <input id="username" name="username" type="text" class="form-control" 
                               placeholder="Username" tabindex="1" required>
                        <span class="input-group-addon">
                            <i class="fa fa-user"></i>
                        </span>
                    </div>
                </div>
                <div class="form-group">
                    <div class="input-group">
                        <input id="password" name="password" type="password" class="form-control" 
                               placeholder="Password" tabindex="2" required>
                        <span class="input-group-btn">
                            <button id="togglePassword" class="btn btn-default" type="button">
                                <i class="fa fa-eye-slash"></i>
                            </button>
                        </span>
                    </div>
                    <div class="row" >
                        <!-- <div class="col-sm-12 text-center" style="font-size: 11px; padding: 5px !important">
                            <a href="<?php //echo site_url('system/login/forgot_pass') ?>" style="color:#148c32 !important" tabindex="4">Lost Password</a>| <a href="<?php //echo site_url('system/login/forgot_verification_code') ?>" style="color:#148c32 !important" tabindex="4">Submit Lost Password Verification Code</a>
                        </div> -->
                        <div class="form-text text-left col-md-6">
                        </div>
                        <div class="form-text text-right col-md-6">
                            <a href="<?php echo site_url('system/login/forgot_pass') ?>" style="color:#148c32 !important">Lost your password?</a>
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <button type="submit" id="btnSubmit" class="btn btn-block st-button text-uppercase">Login</button>
                </div>
                <div class="form-text text-center mt-40">
                    <ul class="list-inline">
                        <li><a href="<?php echo site_url('system/login/privacy_policy') ?>" target="_blank" rel="noopener noreferrer" style="color:#000000 !important"><u>Privacy Policies</u></a></li>
                        <li><a href="<?php echo site_url('system/login/term_of_use') ?>" target="_blank" rel="noopener noreferrer" style="color:#000000 !important"><u>Terms of Use</u></a></li>
                    </ul>
                </div>
            </form>
        </div><!-- End of Form Content -->
        <div class="login-footer bg-primary">
            &copy; <?php echo date('Y'); ?> Koltiva AG and All Its Affiliates
        </div>
    </div>
</div>
<script>
    $(function(){
        $("#boxSuccess").html('');
        $("#boxSuccess").hide();
        $("#boxFailed").html('');
        $("#boxFailed").hide();

        $(document).on('click', '#btnSubmit', function(e) {
            e.preventDefault();
            $("#ajaxImgLoad").show();

            $("#boxFailed").html('');
            $("#boxFailed").hide();

            let validationStatus = true;
            let validationMsg = [];
            let validationMsgDisplay;

            if(document.getElementById("username").value == "") {
                validationStatus = false;
                validationMsg.push('Username is required');
            }
            
            if(document.getElementById("password").value == "") {
                validationStatus = false;
                validationMsg.push('Password is required');
            }

            if(validationStatus == false) {
                validationMsgDisplay = '<ul>';
                for (var index = 0; index < validationMsg.length; index++) {
                    validationMsgDisplay += '<li>'+validationMsg[index]+'</li>'
                }
                validationMsgDisplay += '</ul>';

                $('#boxFailed').html(validationMsgDisplay);
                $("#boxFailed").show();
                $("#ajaxImgLoad").hide();
            } else {

                var data = new FormData();
                data.append('username', document.getElementById("username").value);
                data.append('passwd', document.getElementById("password").value);


                var opts = {
                    url: '<?php echo $api_url?>'+'/page/front_user_login',
                    data: data,
                    cache: false,
                    contentType: false,
                    processData: false,
                    method: 'POST',
                    type: 'POST',
                    success: function(r){
                        $("#ajaxImgLoad").hide();

                        localStorage.setItem('ctapp_duplikatemail', 0);
                        localStorage.setItem('ctapp_duplikatphonenumber', 0);

                        //Cek pesan duplikat email or phone number dulu ========= (begin)
                        if(r.CekDuplikatEmail == 1) {
                            localStorage.setItem('ctapp_duplikatemail', 1);
                        }

                        if(r.CekDuplikatPhonenumber == 1) {
                            localStorage.setItem('ctapp_duplikatphonenumber', 1);
                        }
                        //Cek pesan duplikat email or phone number dulu ========= (end)

                        if(r.process == 'login_success') {
                            window.location = '<?php echo site_url()?>';
                        }

                        if(r.process == 'tor_required') {
                            window.location = '<?php echo site_url('system/login_proc/after_login_tor')?>';
                        }

                        if(r.process == 'new_password_required') {
                            window.location = '<?php echo site_url('system/login_proc/passwd_challenge')?>';
                        }
                        
                    },
                    error: function(r){
                        $("#ajaxImgLoad").hide();
                        try {
                            let objR = JSON.parse(r.responseText);
                            $('#boxFailed').html(objR.message);
                            $("#boxFailed").show();
                        }
                        catch(err) {
                            $('#boxFailed').html('Login process failed, Please try again later');
                            $("#boxFailed").show();
                        }
                    }
                };

                jQuery.ajax(opts);
            }
        });

        $('#textCarousel').carousel({
            interval: 10000,
            pause: "hover",
            keyboard: true,
        });
    });
    $("#togglePassword").click(function(){
        var x = document.getElementById("password");
        if (x.type === "password") {
            x.type = "text";
            $('#togglePassword > i').removeClass('fa-eye-slash');
            $('#togglePassword > i').addClass('fa-eye');
        } else {
            x.type = "password";
            $('#togglePassword > i').removeClass('fa-eye');
            $('#togglePassword > i').addClass('fa-eye-slash');
        }
    });
</script>