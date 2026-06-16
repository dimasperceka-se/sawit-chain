<!DOCTYPE html>
<?php
    $baseurlcheck = rtrim(trim(base_url()),'/');
    $title        = "SawitChain";
    $logo = '
    <div class="mx-auto object-center object-cover w-1/2 mb-8">
        <img src="' . base_url('assets/new/img/sawitchain-full-logo.png') . '" alt="SawitChain" class="w-full h-auto" />
    </div>';
    $background = base_url('assets/new/img/sawitchain-bg.jpeg');
?>

<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="icon" href="<?php echo base_url('assets/new/img/sawitchain-favicon.png'); ?>" />
    <title><?= $title ?></title>
    <link rel="stylesheet" href="<?php echo base_url('assets/new/css/pe-icon-7-stroke.css'); ?>" />
    <link rel="stylesheet" href="<?php echo base_url('assets/new/css/helper.css'); ?>" />
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/tailwindcss/2.2.15/tailwind.min.css"
      integrity="sha512-braXHF1tCeb8MzPktmUHhrjZBSZknHvjmkUdkAbeqtIrWwCchhcpUeAf2Sq3yIq1Q1x5PlroafjceOUbIE3Q5g=="
      crossorigin="anonymous"
      referrerpolicy="no-referrer"
    />
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.13.0/css/all.min.css"
    />
    <link rel="stylesheet" href="<?php echo base_url('assets/new/css/styles.css'); ?>" />
    <script src="<?php echo base_url('assets/vendor/jquery/jquery.js') ?>" type="text/javascript"></script>
    <script src="<?php echo base_url('assets/new/js/feather.min.js'); ?>"></script>
  </head>

  <body>
    <div class="w-full login-screen relative">
      <img
        src="<?php echo $background ?>"
        alt="Nestle"
        class="
          absolute
          object-cover object-left
          lg:object-center
          w-full
          h-full
          img-bg
        "
      />
      <div class="container mx-auto relative py-6 lg:py-16 h-full">
        <div class="grid-cols-1 block lg:hidden">
        <div class="mx-auto object-center object-cover w-2/4 mb-12">
            <img src="<?php echo base_url('assets/new/img/sawitchain-full-logo.png'); ?>" alt="SawitChain" class="w-full h-auto">
        </div>

        </div>
        <div class="flex justify-between">
          <div class="card-login">
            <div class="grid-cols-1 hidden lg:block">
              <?=$logo?>
            </div>
            <div class="
                    bg-red-100
                    border
                    border-red-400
                    text-red-700
                    px-4 py-3
                    rounded
                    relative"
                id="boxFailed" 
                style="display: none;" 
                role="alert"
            >
            </div>

            <div class="grid-cols-1">
                <div class="flex">
                    <div class="flex-col flex ml-auto mr-auto items-center w-full">
                        <form action="#" class="mt-2 flex flex-col w-full">
                            <label class="block mb-2 font-bold text-sm sm:text-base">Username or Email</label>
                            <div class="
                                    relative
                                    text-gray-400
                                    focus-within:text-gray-600
                                    block
                                    mb-4
                                    input-style
                                ">
                                <span class="
                                    pointer-events-none
                                    absolute
                                    top-1/2
                                    transform
                                    -translate-y-1/2
                                    left-3
                                    ">
                                    <!-- <i data-feather="user"></i> -->
                                    <i class="pe-7s-mail new-icon"></i>
                                </span>

                                <input type="text" name="email" id="username" placeholder="Email / Username" class="
                                    form-input
                                    py-3
                                    px-4
                                    appearance-none
                                    w-full
                                    block
                                    focus:outline-none
                                    " />
                            </div>
                            <label class="block mb-2 font-bold text-sm sm:text-base">Password</label>
                            <div class="
                                    relative
                                    text-gray-400
                                    focus-within:text-gray-600
                                    block
                                    input-style
                                ">
                                <span class="
                                    pointer-events-none
                                    absolute
                                    top-1/2
                                    transform
                                    -translate-y-1/2
                                    left-3
                                    ">
                                    <!-- <i data-feather="lock"></i> -->
                                    <i class="pe-7s-lock new-icon"></i>
                                </span>

                                <input type="password" name="password" id="password" placeholder="Password" class="
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
                                        <i class="pe-7s-look look-off new-icon"></i>
                                    </span>

                                    <!-- <i data-feather="eye"></i>
                                    <i data-feather="eye-off"></i> -->
                                </span>
                            </div>
                            <br>
                            <button class="
                                    py-4
                                    mb-4
                                    btn-login-bg
                                    text-center
                                    px-17
                                    md:px-12 md:py-4
                                    text-white
                                    leading-tight
                                    text-xl
                                    md:text-base
                                "
                                id="btnSubmit">
                                <span 
                                    style="display: inline-flex;">
                                    Login<pre> </pre>
                                    <img 
                                        id="ajaxImgLoad" 
                                        style="display: none; height: 1em;" 
                                        src="<?php echo base_url() ?>images/dg/ajax-loader.gif"
                                    />
                                </span>
                            </button>
                            <p class="
                                    text-sm
                                    md:text-base
                                    text-center
                                    lg:text-left
                                    agreement
                                ">
                                By signing in, you agree with our
                                <a href="#" target="_blank" class="terms underline">Terms</a> and
                                <a href="#" target="_blank" class="terms underline">Privacy Policy</a>
                            </p>
                        </form>
                    </div>
                </div>
            </div>

            </div>
        </div>
        <div
          class="
            left-2/4
            transform
            -translate-x-1/2
            absolute
            lg:left-0 lg:-translate-x-0
            bottom-0
            mb-6
            lg:mb-16
          "
        >
          <a href="#" target="_blank">
            
          </a>
        </div>
      </div>
      <div class="grid grid-cols-1 hidden lg:block">
        <div class="footer">
          <p class="text-white text-center">
            @ <?=date("Y")?> SawitChain. All rights reserved.
          </p>
        </div>
      </div>
    </div>
  </body>
    <script>
        $(document).ready(function() {
            const eye = document.querySelector(".open");
            const eyeoff = document.querySelector(".look-off");
            const passwordField = document.querySelector("input[type=password]");
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

                            if(r.process == 'login_success') {
                                window.location = '<?php echo base_url()?>';
                            }

                            if(r.process == 'tor_required') {
                                window.location = '<?php echo base_url()?>'+'system/login_proc/after_login_tor';
                            }

                            if(r.process == 'new_password_required') {
                                window.location = '<?php echo base_url()?>'+'system/login_proc/passwd_challenge';
                            }
                            
                        },
                        error: function(r){
                            $("#ajaxImgLoad").hide();
                            $(".welcome-back").hide();
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

        });
    </script>
</html>
