<div class="grid grid-cols-1">
    <div class="card-new-password mx-auto">
        <!-- <img src="<?php echo base_url('assets/new/img/icon-newpassword.svg'); ?>" alt="Verification Code"
            class="mx-auto icon-title" /> -->
        <div class="mx-auto icon-title object-center object-cover w-1/4 mb-8">
            <svg width="75" height="74" fill="none" xmlns="http://www.w3.org/2000/svg">
                <g clip-path="url(#a)">
                    <path fill="#fff" d="M.5 0h74v74H.5z"/>
                    <circle cx="37.46" cy="36.96" r="36.96" fill="#DFEDAE"/>
                    <rect x="7.5" y="21.5" width="50" height="34" rx="6.5" fill="#fff" stroke="#192740"/>
                    <path d="m45.94 32.133-.212-4.03a2 2 0 0 1 .583-1.52l19.17-19.169a2 2 0 0 1 2.828 0l4.242 4.243a2 2 0 0 1 0 2.828l-19.169 19.17a2 2 0 0 1-1.52.582l-4.03-.212a2 2 0 0 1-1.892-1.892z" fill="#7F8FA4" stroke="#192740" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="m17.722 32.305-1.787-.516.323-.99 1.77.685-.089-2.015h1.049l-.088 2.05 1.74-.673.317 1.008-1.817.515 1.172 1.559-.85.62-1.054-1.687-1.043 1.635-.855-.598 1.212-1.593zm8.069 0-1.787-.516.322-.99 1.77.685-.088-2.015h1.049l-.088 2.05 1.74-.673.316 1.008-1.816.515 1.172 1.559-.85.62-1.054-1.687-1.043 1.635-.856-.598 1.213-1.593zm8.069 0-1.788-.516.323-.99 1.77.685-.089-2.015h1.05l-.089 2.05 1.74-.673.317 1.008-1.816.515 1.172 1.559-.85.62-1.055-1.687-1.043 1.635-.855-.598 1.213-1.593z" fill="#000"/>
                    <path d="M16.5 41h30" stroke="#192740" stroke-linecap="round" stroke-linejoin="round"/>
                </g>
                <defs>
                    <clipPath id="a">
                        <path fill="#fff" transform="translate(.5)" d="M0 0h74v74H0z"/>
                    </clipPath>
                </defs>
            </svg>
        </div>
        <h1 class="text-center">Create Your Password</h1>
        <p class="text-center mb-9 desc">
            Please enter a new password
        </p>
        <div class="card flex align items-center alert-wrong" id="boxFailed">
        </div>
        <div class="card flex align items-center alert-success" id="boxSuccess" style="<?php if ($_SESSION['message']) echo 'display:flex;'; ?>">
            <?php if ($_SESSION['message']) echo $_SESSION['message']; ?>
        </div>
        <div class="flex">
            <div class="flex-col flex ml-auto mr-auto items-center w-full">
                <form action="#" id="form-input" class="mt-2 flex flex-col w-full">
                    <label class="block mb-2 font-bold text-sm sm:text-base" for="forms-labelOverInputCode">New
                        Password</label>
                    <div class="
                      relative
                      text-gray-400
                      focus-within:text-gray-600
                      block
                      input-style
                    "
                    id="form-input-password">
                        <span class="
                        pointer-events-none
                        absolute
                        top-1/2
                        transform
                        -translate-y-1/2
                        left-3
                      ">
                            <i class="pe-7s-lock new-icon"></i>
                        </span>
                        <input type="password" name="password" id="new_password"
                            placeholder="Type at least 8 characters..."
                            onkeyup="validationPassword(this.value)" class="
                        form-input
                        py-3
                        px-4
                        appearance-none
                        w-full
                        block
                        focus:outline-none
                      " />
                        <span class="
                        pointer-events-auto
                        cursor-pointer
                        absolute
                        top-1/2
                        transform
                        -translate-y-1/2
                        right-3
                      ">
                            <span><img src="<?php echo base_url('assets/new/img/look-off.svg'); ?>" alt="" class="new-icon open" /></span>
                            <span>
                                <i class="pe-7s-look look-off new-icon hidden"></i>
                            </span>
                        </span>
                    </div>
                    <div class="relative mt-3 hidden" id="container-progress">
                        <div class="
                        overflow-hidden
                        h-2
                        text-xs
                        flex
                        rounded
                        bg-purple-200
                      ">
                            <div id="progress-bar-pass" class="
                          shadow-none
                          flex flex-col
                          text-center
                          whitespace-nowrap
                          text-white
                          justify-center
                          bg-red
                        "></div>
                        </div>
                        <p class="mt-2" id="password_strength_text"></p>
                    </div>
                    <!-- <p class="alert-previous-password">
                        <i class="pe-7s-close"></i>
                        You coudn’t use the same previous password
                    </p> -->
                    <label class="block mb-2 mt-4 font-bold text-sm sm:text-base" for="forms-labelOverInputCode">Confirm
                        New Password</label>
                    <div class="
                      relative
                      text-gray-400
                      focus-within:text-gray-600
                      block
                      input-style
                    "
                    id="form-input-confirm-password">
                        <span class="
                        pointer-events-none
                        absolute
                        top-1/2
                        transform
                        -translate-y-1/2
                        left-3
                      ">
                            <i class="pe-7s-lock new-icon"></i>
                        </span>
                        <input type="password" name="password" id="confirm_password"
                            placeholder="Type at least 8 characters..."
                            onkeyup="validatePasswordConfirm()" class="
                        form-input
                        py-3
                        px-4
                        appearance-none
                        w-full
                        block
                        focus:outline-none
                      " />
                        <span class="
                        pointer-events-auto
                        cursor-pointer
                        absolute
                        top-1/2
                        transform
                        -translate-y-1/2
                        right-3
                      ">
                            <span><img src="<?php echo base_url('assets/new/img/look-off.svg'); ?>" alt="" class="new-icon2 open2" /></span>
                            <span>
                                <i class="pe-7s-look look-off2 new-icon2 hidden"></i>
                            </span>
                        </span>
                    </div>
                    <p id="valPasswordConfirm"></p>
                    <p>&nbsp;</p>
                    <label class="block mb-2 font-bold text-sm sm:text-base" for="forms-labelOverInputCode">Code</label>
                    <div class="
                      relative
                      text-gray-400
                      focus-within:text-gray-600
                      block
                      input-style
                    "
                    id="form-input-code">
                        <input pattern="/^[0-9]/" onKeyPress="if(this.value.length==6) return false;" maxlength="6"
                            type="number" name="code" id="code" placeholder="6 digit code..."
                            onkeydown="validationCode()" onkeyup="validationCode()" class="
                        form-input
                        py-3
                        px-4
                        appearance-none
                        w-full
                        block
                        focus:outline-none
                      " />
                    </div>
                    <p id="valCode"></p>
                    <button class="
                      py-4
                      mt-4
                      mb-4
                      btn-login-bg
                      text-center
                      px-17
                      md:px-12 md:py-4
                      text-white
                      leading-tight
                      text-xl
                      md:text-base
                    " type="submit" id="btnSubmit">
                        <span style="display: inline-flex;">
                            Confirm Code
                            <pre> </pre>
                            <img id="ajaxImgLoad" style="height: 1em;"
                                src="<?php echo base_url() ?>images/dg/ajax-loader.gif" />
                        </span>
                    </button>
                    <div class="flex justify-between">
                        <a href="#" class="help"> Help Center </a>
                        <p class="return">
                            Didn’t Receive Code? <a
                                href="<?php echo site_url('system/login/forgot_pass'); ?>">Resend</a>
                        </p>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
    function error(e) {
        e.add('error');
    }

    function rmError (e) {
        e.remove('error');
    }

    function validationCode() {
        let form = document.getElementById('form-input-code');
        let code = document.getElementById('code').value;
        let text = document.getElementById('valCode');
        let pattern = /^(?=.*\d)(?=.*[0-9])(?=.*[0-9])[0-9]{6,}$/;

        if (code.match(pattern)) {
            form.classList.add('valid');
            form.classList.remove('invalid');
            rmError(form.classList);
            text.innerHTML = "";
            text.style.color = '';
        } else {
            form.classList.remove('valid');
            form.classList.add('invalid');
            error(form.classList);
            text.innerHTML = "Invalid Code. Please re-check the code to your email.";
            text.style.color = '#d02630';
            text.style.fontSize = '14px';
            text.style.marginTop = '8px';
        }

        if (code == '') {
            form.classList.remove('valid');
            form.classList.add('invalid');
            error(form.classList);
            text.innerHTML = "Invalid Code. Please re-check the code to your email.";
            text.style.color = '#d02630';
        }
    }

    function validatePasswordConfirm() {
        let form = document.getElementById('form-input-confirm-password');
        let text = document.getElementById('valPasswordConfirm');
        var password = document.getElementById("new_password").value;
        var confirmPassword = document.getElementById("confirm_password").value;
        if (password != confirmPassword) {
            form.classList.add('invalid');
            form.classList.remove('valid');
            error(form.classList);
            text.innerHTML = "The specified password must be identical";
            text.style.color = '#d02630';
            text.style.fontSize = '14px';
            text.style.marginTop = '8px';
        } else {
            form.classList.remove('invalid');
            form.classList.add('valid');
            rmError(form.classList);
            text.innerHTML = "";
            text.style.color = '';
        }
        if (confirmPassword == '') {
            form.classList.remove('valid');
            form.classList.add('invalid');
            error(form.classList)
            text.innerHTML = "The specified password must be identical";
            text.style.color = '#d02630';
        }
    }
    
    function validationPassword(password) {
        var password_strength = document.getElementById("password_strength_text");
        var loading = document.getElementById("progress-bar-pass");
        var container_loading = document.getElementById("container-progress");
        //if textBox is empty
        if(password.length==0){
            password_strength.innerHTML = "";
            return;
        }
        //Regular Expressions
        var regex = new Array();
        regex.push("[A-Z]"); //For Uppercase Alphabet
        regex.push("[a-z]"); //For Lowercase Alphabet
        regex.push("[0-9]"); //For Numeric Digits
        regex.push("[$@$!%*#?&]"); //For Special Characters

        var passed = 0;

        //Validation for each Regular Expression
        for (var i = 0; i < regex.length; i++) {
            if((new RegExp (regex[i])).test(password)){
                passed++;
            }
        }

        //Validation for Length of Password
        if(password.length < 8){
            passed = 2;
        }

        //Display of Status
        var color = "";
        var passwordStrength = "";
        loading.style.background = "";
        container_loading.style.display = "none";
        
        switch(passed){
            case 1:
                container_loading.style.display = "none"
                break;
            case 2:
                passwordStrength = "Password too Short";
                password_strength.style.fontSize = "14px";
                loading.style.width = "30%";
                loading.style.background = "#d02630";
                container_loading.style.display = "block";
                color = "#d02630";
                break;
            case 3:
                passwordStrength = "Medium  Password";
                password_strength.style.fontSize = "14px";
                loading.style.width = "65%";
                loading.style.background = "#f77d2b";
                container_loading.style.display = "block";
                color = "#f77d2b";
                break;
            case 4:
                passwordStrength = "Strong  Password";
                password_strength.style.fontSize = "14px";
                loading.style.width = "100%";
                loading.style.background = "#2bbe72";
                container_loading.style.display = "block"
                color = "#2bbe72";
                break;
        }
        password_strength.innerHTML = passwordStrength;
        password_strength.style.color = color;
    }

    $(document).ready(function () {

        const eye = document.querySelector(".open");
        const eyeoff = document.querySelector(".look-off");
        const eyeConfirm = document.querySelector(".open2");
        const eyeoffConfirm = document.querySelector(".look-off2");
        const passwordField = document.getElementById("new_password");
        const passwordFieldConfirm = document.getElementById("confirm_password");
        eye.addEventListener("click", () => {
            eye.style.display = "none";
            eyeoff.style.display = "block";
            passwordField.type = "text";
        });
        eyeoff.addEventListener("click", () => {
            eyeoff.style.display = "none";
            eye.style.display = "block";
            passwordField.type = "password";
        });
        eyeConfirm.addEventListener("click", () => {
            eyeConfirm.style.display = "none";
            eyeoffConfirm.style.display = "block";
            passwordFieldConfirm.type = "text";
        });
        eyeoffConfirm.addEventListener("click", () => {
            eyeoffConfirm.style.display = "none";
            eyeConfirm.style.display = "block";
            passwordFieldConfirm.type = "password";
        });

        $(document).on('click', '#btnSubmit', function (e) {
            e.preventDefault();
            $("#ajaxImgLoad").show();

            $("#boxFailed").html('');
            $("#boxFailed").hide();
            $("#boxSuccess").html('');
            $("#boxSuccess").hide();

            let validationStatus = true;
            let validationMsg = [];
            let validationMsgDisplay = '';
            let password = document.getElementById("new_password").value;
            let confirmPassword = document.getElementById("confirm_password").value;
            let code = document.getElementById('code').value;
            let formPassword = document.getElementById('form-input-password');
            let formPasswordConfirm = document.getElementById('form-input-confirm-password');
            let textPasswordConfirm = document.getElementById('valPasswordConfirm');
            let formCode = document.getElementById('form-input-code');
            let textCode = document.getElementById('valCode');
            let pattern = /^(?=.*\d)(?=.*[0-9])(?=.*[0-9])[0-9]{6,}$/;

            //Regular Expressions
            var regex = new Array();
            regex.push("[A-Z]"); //For Uppercase Alphabet
            regex.push("[a-z]"); //For Lowercase Alphabet
            regex.push("[0-9]"); //For Numeric Digits
            regex.push("[$@$!%*#?&]"); //For Special Characters

            var passed = 0;

            //Validation for each Regular Expression
            for (var i = 0; i < regex.length; i++) {
                if((new RegExp (regex[i])).test(password)){
                    passed++;
                }
            }

            //Validation for Length of Password
            if(password.length < 8){
                passed = 2;
            }

            switch(passed){
                case 1:
                    validationStatus = false;
                    validationMsgDisplay = 'Password must be strong';
                    break;
                case 2:
                    validationStatus = false;
                    validationMsgDisplay = 'Password must be strong';
                    break;
                case 3:
                    validationStatus = false;
                    validationMsgDisplay = 'Password must be strong';
                    break;
                case 4:
                    validationStatus = true;
                    break;
            }

            if (password == '') {
                validationStatus = false;
                formPassword.classList.remove('valid');
                formPassword.classList.add('invalid');
                error(formPassword.classList);
                validationMsgDisplay = 'Password is required';
            } else {
                formPassword.classList.remove('invalid');
                formPassword.classList.add('valid');
                rmError(formPassword.classList);
            }

            if (password != confirmPassword) {
                validationStatus = false;
                formPasswordConfirm.classList.add('invalid');
                formPasswordConfirm.classList.remove('valid');
                error(formPasswordConfirm.classList);
                textPasswordConfirm.innerHTML = "The specified password must be identical";
                textPasswordConfirm.style.color = '#d02630';
                textPasswordConfirm.style.fontSize = '14px';
                textPasswordConfirm.style.marginTop = '8px';
            }

            if (confirmPassword == '') {
                validationStatus = false;
                formPasswordConfirm.classList.remove('valid');
                formPasswordConfirm.classList.add('invalid');
                error(formPasswordConfirm.classList);
                textPasswordConfirm.innerHTML = "The specified password must be identical";
                textPasswordConfirm.style.color = '#d02630';
            }

            if (!code.match(pattern)) {
                validationStatus = false;
                formCode.classList.remove('valid');
                formCode.classList.add('invalid');
                error(formCode.classList);
                textCode.innerHTML = "Invalid Code. Please re-check the code to your email.";
                textCode.style.color = '#d02630';
                textCode.style.fontSize = '14px';
                textCode.style.marginTop = '8px';
            }

            if (code == '') {
                validationStatus = false;
                formCode.classList.remove('valid');
                formCode.classList.add('invalid');
                error(formCode.classList);
                textCode.innerHTML = "Invalid Code. Please re-check the code to your email.";
                textCode.style.color = '#d02630';
            }

            if (validationStatus == false) {
                if (validationMsgDisplay != '') {
                    validationMsgDisplay = '<img src="/assets/new/img/alert-circle.svg" alt="Alert" /><p>' + validationMsgDisplay + '</p>';

                    $('#boxFailed').html(validationMsgDisplay);
                    $("#boxFailed").css("display", "flex");
                }
                $("#ajaxImgLoad").hide();
            } else {
                $.ajax({
                    type: "POST",
                    url: '<?php echo $api_url?>' + '/page/front_forgot_pass_confirm',
                    data: {
                        passwd: document.getElementById("new_password").value,
                        verification_code: document.getElementById("code").value,
                    },
                    success: function (r) {
                        $("#ajaxImgLoad").hide();
                        window.location = r.page;
                        // window.location = "/system/login/done";
                    },
                    error: function (r) {
                        $("#ajaxImgLoad").hide();
                        $(".welcome-back").hide();
                        try {
                            let objR = JSON.parse(r.responseText);
                            $('#boxFailed').html(objR.message);
                            $("#boxFailed").show();
                        } catch (err) {
                            $('#boxFailed').html('Process failed, Please try again later');
                            $("#boxFailed").show();
                        }
                    }
                });
            }
        });

    });
</script>