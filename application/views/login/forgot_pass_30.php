<div class="row-fluid">
    <div class="col-md-12 right-panel">
        <img src="<?php echo base_url() ?>assets/images/logo_palmoil.png" alt="PalmOilTrace" width="250">
        
        <div class="form-content width-small"><!-- Form Container for Flex -->

            <div class="alert alert-danger" id="boxFailed"></div>
            <div class="alert alert-success" id="boxSuccess"><?php if($frontProcessMsg != "") echo $frontProcessMsg;?></div>

            <div class="row form-info text-center">
                <div class="col-12">
                    <img id="ajaxImgLoad" style="display: none;" src="<?php echo base_url() ?>images/dg/ajax.gif" />
                    <h4><strong>Need help with your Password?</strong></h4>
                    <p><?php echo lang('Enter the username you use for PalmOilTrace, and we\'ll help you to reset your password.');?></p>
                </div>
            </div>
            
            <form class="login-form" action="#" method="post">
                <div class="form-group" style="margin-bottom: 0;">
                    <div class="input-group input-group-icon">
                        <input id="username" name="username" type="text" class="form-control" 
                               placeholder="Username" tabindex="1" required>
                        <span class="input-group-addon">
                            <i class="fa fa-user"></i>
                        </span>
                    </div>
                </div>

                <div class="text-center">
                    <p><small><?php echo lang('If you forgot your username, <br> please contact our <a href="mailto:support@koltiva.com">Customer Support Center</a>');?></small></p>
                </div>

                <div class="horizontal-divider"><span style="color: #555555">or</span></div>
                <p class="text-center" style="color: #555555">
                    <small>Have / Submit <a href="/system/login/forgot_verification_code" class="primary-color" style="font-weight: bold;">verification code?</a></small>
                </p>


                <div class="form-group mt-40">
                    <button type="submit" id="btnSubmit" class="btn btn-block st-button text-uppercase">
                        <?php echo lang('Request Verification Code'); ?>
                    </button>
                </div>

                <div class="form-text text-center mt-40">
                    <i class="fa fa-long-arrow-left"></i> 
                    <a href="<?php echo site_url('system/login/'); ?>">
                        Return to Login Page
                    </a>
                </div>
            </form>
        </div><!-- End of Form Content -->

        <div class="forgot-footer bg-gray">
            <ul class="list-inline">
                <li><span>@<?php echo date('Y'); ?> Koltiva AG and All It's Affiliates</span></li>
                <li><a href="https://koltiva.com/privacy-policy.php" target="_blank" rel="noopener noreferrer">Privacy Policies</a></li>
                <li><a href="#">Terms of Use</a></li>
            </ul>
        </div>
    </div>
</div>

<script>
    $(function(){
        $('#boxFailed').hide();
        $('#boxSuccess').hide();

        $(document).on('click', '#btnSubmit', function(e) {
            e.preventDefault();
            $("#ajaxImgLoad").show();

            $("#boxFailed").html('');
            $("#boxFailed").hide();
            $("#boxSuccess").html('');
            $("#boxSuccess").hide();
            // document.getElementById("boxContent").style.height = "250px";

            let validationStatus = true;
            let validationMsg = [];
            let validationMsgDisplay;

            if(document.getElementById("username").value == "") {
                validationStatus = false;
                validationMsg.push(lang('Username is required'));
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
                // document.getElementById("boxContent").style.height = "330px";
            } else {
                $.ajax({
                    type: "POST",
                    url: '<?php echo $api_url?>'+'/page/front_req_forgot_password',
                    data: {
                        username: document.getElementById("username").value
                    },
                    success: function (r) {
                        $("#ajaxImgLoad").hide();
                        //console.log(r);

                        $('#boxSuccess').html(r.message);
                        $("#boxSuccess").show();
                        // document.getElementById("boxContent").style.height = "330px";
                        document.getElementById("username").value = "";
                    },
                    error: function(r){
                        $("#ajaxImgLoad").hide();
                        try {
                            let objR = JSON.parse(r.responseText);
                            $('#boxFailed').html(objR.message);
                            $("#boxFailed").show();
                            // document.getElementById("boxContent").style.height = "330px";
                        }
                        catch(err) {
                            $('#boxFailed').html(lang('Process failed, Please try again later'));
                            $("#boxFailed").show();
                            // document.getElementById("boxContent").style.height = "330px";
                        }
                    }
                });
            }
        });
    });
</script>