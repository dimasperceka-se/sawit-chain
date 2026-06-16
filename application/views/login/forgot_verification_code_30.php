<div class="row-fluid">

    <div class="col-xs-12 right-panel">
        <img src="<?php echo base_url() ?>assets/images/logo_palmoil.png" alt="PalmOilTrace" width="200">
        
        <div class="form-content width-small"><!-- Form Container for Flex -->

            <div class="alert alert-danger" id="boxFailed"></div>

            
            <div class="row form-info text-center">
                <div class="col-12">
                    <img id="ajaxImgLoad" style="display: none;" src="<?php echo base_url() ?>images/dg/ajax.gif" />
                    <h4><strong>Change your Password</strong></h4>
                    <p><?php echo lang('To reset your password, please input your username and the verification code you received in your email.');?></p>
                </div>
            </div>

            <form class="login-form" action="#" method="post">
                
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
                    <div class="input-group input-group-icon">
                        <input id="verification_code" name="verification_code" type="text" class="form-control" 
                            placeholder="Verification Code" tabindex="2" required>
                        <span class="input-group-addon">
                            <i class="fa fa-lock"></i>
                        </span>
                    </div>
                </div>

                <div class="form-group" style="margin-bottom: 5px;">
                    <div class="input-group">
                        <input id="password" name="password" type="password" class="form-control" 
                                placeholder="New Password" tabindex="3" required onkeyup="checkPassStr(this.value)">
                        <span class="input-group-btn">
                            <button class="togglePassword btn btn-default" type="button">
                                <i class="fa fa-eye-slash"></i>
                            </button>
                        </span>
                    </div>
                </div>

                <div class="text-small">
                    <p>Your password must fit this criteria:</p>
                    <ul class="password-rules">
                        <li id="minChar" class="fail">Contains <strong>8 Characters</strong></li>
                        <li id="charAllowed" class="fail">Contains lowercase letter <strong>[a-z]</strong>, uppercase letter <strong>[A-Z]</strong>, numbers <strong>[0-9]</strong>, and special characters <strong>[!_@#$%^&*]</strong></li>
                        <li id="noSpace" class="fail">Cannot contains <strong>(space)</strong></li>
                    </ul>
                </div>
                
                <div class="form-group">
                    <div class="input-group">
                        <input id="password_re" name="password_re" type="password" class="form-control" 
                            placeholder="Confirm New Password" tabindex="4" required>
                        <span class="input-group-btn">
                            <button class="togglePassword btn btn-default" type="button">
                                <i class="fa fa-eye-slash"></i>
                            </button>
                        </span>
                    </div>
                </div>

                <div class="form-group" style="margin-bottom: 60px;">
                    <button type="submit" id="btnSubmit" class="btn btn-block st-button text-uppercase">
                        Verify and Continue
                    </button>
                </div>
            </form>
            
        </div><!-- End of Form Content -->
        <!-- <br/>
        <br/>
        <br/>
        <br/>
        <br/> -->
        <div class="forgot-footer bg-gray">
            <ul class="list-inline">
                <li><span>@<?php echo date('Y'); ?> Koltiva and All Its Affiliates</span></li>
                <li><a href="https://koltiva.com/privacy-policy.php" target="_blank" rel="noopener noreferrer">Privacy Policies</a></li>
                <li><a href="#">Terms of Use</a></li>
            </ul>
        </div>
    </div>
</div>

<script>
    $(function(){
        $('#boxFailed').hide();

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

            if(document.getElementById("password_re").value == "") {
                validationStatus = false;
                validationMsg.push(lang('Retype password is required'));
            }

            if(document.getElementById("password").value != document.getElementById("password_re").value) {
                validationStatus = false;
                validationMsg.push(lang('Password not match'));
            }

            if(document.getElementById("verification_code").value == "") {
                validationStatus = false;
                validationMsg.push(lang('Verification code is required'));
            }

            //Check Pass String
            if(checkPassStr(document.getElementById("password").value) == false){
                validationStatus = false;
                validationMsg.push(lang('Password doesn\'t match criteria'));
            }

            if(validationStatus == false) {
                validationMsgDisplay = '<ul>';
                for (var index = 0; index < validationMsg.length; index++) {
                    validationMsgDisplay += '<li>'+validationMsg[index]+'</li>';
                }
                validationMsgDisplay += '</ul>';

                $('#boxFailed').html(validationMsgDisplay);
                $("#boxFailed").show();
                $("#ajaxImgLoad").hide();
            } else {
                $.ajax({
                    type: "POST",
                    url: '<?php echo $api_url?>'+'/page/front_forgot_pass_confirm',
                    data: {
                        username: document.getElementById("username").value,
                        passwd: document.getElementById("password").value,
                        verification_code: document.getElementById("verification_code").value,
                    },
                    success: function (r) {
                        $("#ajaxImgLoad").hide();
                        //console.log(r);

                        //langsung login
                        window.location = '<?php echo base_url()?>';
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

            let elem = document.querySelector('.forgot-footer');

            switch(validationMsg.length) {
              case 5:
                elem.style.margin = '-20px';
                break;
              case 4:
                elem.style.margin = '-15px';
                break;
              case 3:
                elem.style.margin = '-10px';
                break;
              case 2:
                elem.style.margin = '-5px';
                break;
              case 1:
                elem.style.margin = '0px';
                break;
              default:
                elem.style.removeProperty('margin');
            }
        });
    });

    $(".togglePassword").click(function(){
        var x = document.getElementById("password"),
            y = document.getElementById("password_re");

        if (x.type === "password" && y.type === "password") {
            x.type = "text";
            y.type = "text";
            $('.togglePassword > i').removeClass('fa-eye-slash');
            $('.togglePasswordRe > i').removeClass('fa-eye-slash');
            $('.togglePassword > i').addClass('fa-eye');
            $('.togglePasswordRe > i').addClass('fa-eye');
        } else {
            x.type = "password";
            y.type = "password";
            $('.togglePassword > i').removeClass('fa-eye');
            $('.togglePasswordRe > i').removeClass('fa-eye');
            $('.togglePassword > i').addClass('fa-eye-slash');
            $('.togglePasswordRe > i').addClass('fa-eye-slash');
        }
    });

    function checkPassStr(pass){
        var status = false,
            txtMinChar = $('#minChar'),
            txtCharAllowed = $('#charAllowed'),
            txtNoSpace = $('#noSpace');

        if(pass != "") {
            if(pass.length >= 8){
                txtMinChar.removeClass('fail');
                txtMinChar.addClass('pass');
                status = true;
            }else{
                txtMinChar.removeClass('pass');
                txtMinChar.addClass('fail');
                status = false;
            }

            reChar = /^(?=.*\d)(?=.*[A-Z])(?=.*[a-z])(?=.*[0-9])(?=.*[\!\_\@\#\$\%\^\&\*\?]).{8,}$/;
            if(reChar.test(pass)){
                txtCharAllowed.removeClass('fail');
                txtCharAllowed.addClass('pass');
                status = true;
            }else{
                txtCharAllowed.removeClass('pass');
                txtCharAllowed.addClass('fail');
                status = false;
            }

            if(pass.length >= 3 && pass.indexOf(' ') <= 0){
                txtNoSpace.removeClass('fail');
                txtNoSpace.addClass('pass');
                status = true;
            }else{
                txtNoSpace.removeClass('pass');
                txtNoSpace.addClass('fail');
                status = false;
            }
        }else{
            txtMinChar.removeClass('pass');
            txtCharAllowed.removeClass('pass');
            txtNoSpace.removeClass('pass');
            txtMinChar.addClass('fail');
            txtCharAllowed.addClass('fail');
            txtNoSpace.addClass('fail');
            status = false;
        }

        return status;
    }
</script>
