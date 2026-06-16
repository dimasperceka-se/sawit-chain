<?php
/******************************************
 *  Author : n1colius.lau@gmail.com   
 *  Created On : Thu Jul 16 2020
 *  File : passwd_challenge.php
 *******************************************/
?>
<div id="loginRow" class="row mt-10">
    <div class="col-xs-10 col-sm-7 col-md-5 col-lg-3 login-box">
        <a href="#" class="logo" onclick="return false;">
            <img class="img-reponsive" src="<?php echo base_url() ?>assets/images/logo.png" alt="PalmoilTrace" />
        </a>

        <div class="panel">
            <div class="panel-heading bg-theme-color text-uppercase">
                <h5><i class="fa fa-user"></i> Sign In</h5>
            </div>
            <div class="panel-body border-top-theme-color">
                
                <div style="text-align: left;" class="alert alert-danger" id="boxFailed"></div>
                <br style="clear:both;" />

                <div class="alert alert-info">
                    <p class="m-none text-semibold h6"><?php echo lang('Please update your password');?></p>
                    <p class="m-none text-semibold h6"><?php echo lang('Your password must fit these criteria');?></p>
                    <ul>
                        <li><?php echo lang('Minimal 8 characters')?></li>
                        <li><?php echo lang('Contains lowercase [a-z], uppercase [A-Z], numbers [0-9], and special characters [!@#$%^&*()[]{}]')?></li>
                        <li><?php echo lang('Cannot contains + or - or (space)') ?></li>
                    </ul>
                </div>

                <form action="<?php echo site_url('system/login_proc/after_passwd_challenge') ?>" method="post">
                    <div class="form-group mb-lg">
                        <label><?php echo lang('New Password');?></label>
                        <div class="input-group input-group-icon">
                            <input name="new_password" id="new_password" type="password" maxlength="50" class="form-control input-lg" />
                            <span class="input-group-addon">
                                <span class="icon icon-lg">
                                    <i class="fa fa-envelope"></i>
                                </span>
                            </span>
                        </div>
                    </div>

                    <div class="form-group mb-lg">
                        <label><?php echo lang('New Password Confirmation');?></label>
                        <div class="input-group input-group-icon">
                            <input name="new_password_re" id="new_password_re" type="password" maxlength="50" class="form-control input-lg" />
                            <span class="input-group-addon">
                                <span class="icon icon-lg">
                                    <i class="fa fa-envelope"></i>
                                </span>
                            </span>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-11 text-right">
                            <button type="submit" id="btnSubmit" class="btn btn-success btn-lg hidden-xs col-sm-12"><?php echo lang('Submit');?></button>
                            <input type="hidden" name="UserIdProc" id="UserIdProc" value="<?php echo $UserId?>" />
                        </div>
                        <div class="col-sm-1 text-right">
                            <img id="ajaxImgLoad" style="margin-top:10px;display:none;" src="<?php echo base_url() ?>images/dg/ajax.gif" />
                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function(){
    $('#boxFailed').hide();

    $(document).on('click', '#btnSubmit', function(e) {
        e.preventDefault();
        $("#ajaxImgLoad").show();

        $("#boxFailed").html('');
        $("#boxFailed").hide();

        let validationStatus = true;
        let validationMsg = [];
        let validationMsgDisplay;

        if(document.getElementById("new_password").value == "") {
            validationStatus = false;
            validationMsg.push(lang('Password is required'));
        }

        if(document.getElementById("new_password_re").value == "") {
            validationStatus = false;
            validationMsg.push(lang('Password Confirmation is required'));
        }

        if(document.getElementById("new_password").value != document.getElementById("new_password_re").value) {
            validationStatus = false;
            validationMsg.push(lang('Password did not match'));
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
                url: '<?php echo $api_url?>'+'/page/front_passwd_challenge',
                data: {
                    passwd: document.getElementById("new_password").value,
                    UserIdProc: document.getElementById("UserIdProc").value
                },
                success: function (r) {
                    $("#ajaxImgLoad").hide();

                    if(r.process == 'user_confirmed') {
                        window.location = '<?php echo base_url()?>';
                    }

                    if(r.process == 'tor_required') {
                        window.location = '<?php echo site_url('system/login_proc/after_login_tor')?>';
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
                        $('#boxFailed').html(lang('Process failed, Please try again later'));
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
    <p>PalmoilTrace is a web and mobile application that focuses on palm traceability and sustainability program management. With PalmoilTrace, palm and palm oil companies are able to verify supply chain sustainability and fair working practices from independent palm smallholders farms, through the trade, to its processing facilities.</p>
</div>