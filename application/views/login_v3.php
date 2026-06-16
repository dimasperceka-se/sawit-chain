<?php
/******************************************
 *  Author : n1colius.lau@gmail.com   
 *  Created On : Thu Jul 16 2020
 *  File : login_v3.php
 *******************************************/
?>


<?php if (!$this->session->userdata('cookieAccepted')) : ?>
    <div style="display:none;" id="privacyAlert" class="alert alert-warning alert-dismissible fixed-top" role="alert">
        <p><i class="fa fa-exclamation-circle" style="font-size: 16px;"></i> &nbsp; This website uses cookies to improve our services. By using this site, you agree on its use. For more information please check our <a style="color: #222; text-decoration: underline" href="<?= base_url() ?>term-and-condition" target="_blank">Privacy policy, Term and Conditions</a> page.</p>
        <button type="button" class="btn btn-sm btn-secondary btn-pill" onClick="acceptCookie()">Accept</button>
    </div>
<?php endif; ?>


<div id="loginRow" class="row mt-10">
    <div class="col-xs-10 col-sm-7 col-md-5 col-lg-4 login-box">
        <a href="#" class="logo" onclick="return false;">
            <img class="img-reponsive" src="<?php echo base_url() ?>assets/images/logo.png" alt="PalmoilTrace" />
        </a>

        <div class="panel">
            <div class="panel-heading bg-theme-color text-uppercase">
                <h5><i class="fa fa-user"></i> Sign In</h5>
            </div>
            <div class="panel-body border-top-theme-color">
                
                <div style="display:none;text-align: left;" class="alert alert-danger" id="boxFailed"></div><br style="clear:both;" />

                <form onsubmit="FormLoginSubmit()" id="FormMainLogin" action="<?php echo site_url('system/login/log_in') ?>" method="post">
                    <div class="form-group mb-lg">
                        <label>Username</label>
                        <div class="input-group input-group-icon">
                            <input name="username" id="username" type="text" autocomplete="off" class="form-control input-lg" tabindex="1" />
                            <span class="input-group-addon">
                                <span class="icon icon-lg">
                                    <i class="fa fa-user"></i>
                                </span>
                            </span>
                        </div>
                    </div>

                    <div class="form-group mb-lg">
                        <label class="pull-left">Password</label>
                        <!-- <div class="clearfix">
                            <a href="<?php echo site_url('system/login/forgot') ?>" class="pull-right" style="color:#299246!important" tabindex="4">Lost Password?</a>
                        </div> -->
                        <div class="input-group input-group-icon">
                            <input name="password" id="password" type="password" autocomplete="off" class="form-control input-lg" tabindex="2" />
                            <span class="input-group-addon">
                                <span class="icon icon-lg">
                                    <i class="fa fa-lock"></i>
                                </span>
                            </span>
                        </div>
                    </div>

                    <div class="col-sm-12 text-center" style="padding:0px;">
                        <button type="submit" id="btnSubmit" class="btn btn-success btn-lg col-sm-12">Sign In</button>
                    </div>
                    <div class="col-sm-12 text-center" style="padding: 5px;text-align:center !important;">
                        <a href="<?php echo site_url('system/login/forgot_pass') ?>" style="color:#61557A; font-size: 11px;margin-top:23px;" tabindex="4">Lost Password</a> | <a href="<?php echo site_url('system/login/forgot_verification_code') ?>" style="color:#61557A; font-size: 11px;margin-top:23px;" tabindex="4">Submit Lost Password Verification Code</a>
                        &nbsp;&nbsp;<img id="ajaxImgLoad" style="margin-top:10px;display:none;" src="<?php echo base_url() ?>images/dg/ajax.gif" />
                    </div>

                </form>

            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function(){
    //form submit
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
            validationMsg.push(lang('Username is required'));
        }

        if(document.getElementById("password").value == "") {
            validationStatus = false;
            validationMsg.push(lang('Password is required'));
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
            $.ajax({
                type: "POST",
                url: '<?php echo $api_url?>'+'/page/front_user_login',
                data: {
                    username: document.getElementById("username").value,
                    passwd: document.getElementById("password").value
                },
                success: function (r) {
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
                        $('#boxFailed').html(lang('Login process failed, Please try again later'));
                        $("#boxFailed").show();
                    }
                }
            });
        }
    });
});
</script>

<div class="art" style="margin-top:-7px;">
    <img class="img-responsive theme-wallpaper" src="<?php echo base_url() ?>assets/images/wallpaper_palmoil_v2.jpg" alt="" srcset="">
    <h4><strong>ABOUT <span class="theme-color">PALMOIL</span><span style="color:#47A447;">TRACE</span></strong></h4>
    <p>The PalmOilTrace Supply Chain and Farm Management Information System includes web and mobile applications for transparent sustainability program management and product traceability to and from farms to factories and consumers.​</p>
</div>

<?php echo $this->load->view('login/footer_col3_v2', NULL, TRUE); ?>