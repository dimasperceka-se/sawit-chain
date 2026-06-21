<?php
/******************************************
 *  Author : n1colius.lau@gmail.com   
 *  Created On : Tue Nov 12 2019
 *  File : common_header_front.php
 *******************************************/
?>
<!doctype html>
<html class="fixed">
    <head>
        <title>SawitChain</title>
        <link rel="icon" href="<?php echo base_url('assets/new/img/sawitchain-logo.png'); ?>" />
        <!-- Basic -->
        <meta charset="UTF-8">

        <meta name="keywords" content="SawitChain" />
        <meta name="description" content="SawitChain">

        <!-- Mobile Metas -->
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />

        <link href="https://fonts.googleapis.com/css?family=Abel&display=swap" rel="stylesheet">

        <!-- Vendor CSS -->
        <link rel="stylesheet" href="<?php echo base_url() ?>assets/vendor/bootstrap/css/bootstrap.css" />
        <link rel="stylesheet" href="<?php echo base_url() ?>assets/vendor/font-awesome/css/font-awesome.css" />
        <!-- <link rel="stylesheet" href="<?php echo base_url() ?>assets/vendor/magnific-popup/magnific-popup.css" /> -->
        <!-- <link rel="stylesheet" href="<?php echo base_url() ?>assets/vendor/bootstrap-datepicker/css/datepicker3.css" /> -->

        <!-- Theme CSS -->
        <link rel="stylesheet" href="<?php echo base_url() ?>assets/stylesheets/theme.css" />

        <!-- Skin CSS -->
        <link rel="stylesheet" href="<?php echo base_url() ?>assets/stylesheets/skins/default.css" />

        <!-- Theme Custom CSS -->
        <link rel="stylesheet" href="<?php echo base_url() ?>assets/stylesheets/theme-custom.css">
        <link rel="stylesheet" href="<?php echo base_url() ?>assets/stylesheets/koltiva.css">
        <!-- Head Libs -->
        <!-- <script src="<?php echo base_url() ?>assets/vendor/modernizr/modernizr.js"></script> -->

        <!-- Cookies Div -->
        <script src="<?php echo base_url() ?>js/plugins/modernizr.custom.32549.js"></script>

        <!-- Vendor -->
        <script src="<?php echo base_url() ?>assets/vendor/jquery/jquery.js"></script>
        <script src="<?php echo base_url() ?>assets/vendor/jquery-browser-mobile/jquery.browser.mobile.js"></script>
        <script src="<?php echo base_url() ?>assets/vendor/bootstrap/js/bootstrap.js"></script>
        <script src="<?php echo base_url() ?>js/functions.js" type="text/javascript"></script> <!-- except -->
        <link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet">

        <style type="text/css">
            body {
                font-family: 'Roboto';
            }

            @-webkit-keyframes slideDown {
                0%, 100% { -webkit-transform: translateY(-50px); }
                10%, 90% { -webkit-transform: translateY(0px); }
            }
            @-moz-keyframes slideDown {
                0%, 100% { -moz-transform: translateY(-50px); }
                10%, 90% { -moz-transform: translateY(0px); }
            }

            #CookiesDiv {
                position: absolute;
                z-index: 101;
                color:black;
                top: 0;
                left: 0;
                right: 0;
                background: #fde073;
                text-align: center;
                line-height: 2.5;
                overflow: hidden;
                -webkit-box-shadow: 0 0 5px black;
                -moz-box-shadow:    0 0 5px black;
                box-shadow:         0 0 5px black;
            }

            .cssanimations.csstransforms #CookiesDiv {
                -webkit-transform: translateY(-50px);
                -webkit-animation: slideDown 30s linear 0s infinite forwards;

                -moz-transform:    translateY(-50px);
                -moz-animation:    slideDown 30s linear 0s infinite forwards;
            }
        </style>
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
    </head>
    <body>
        <!-- <?php if(!$this->session->userdata('cookieAccepted')): ?>
        <div id="privacyAlert" class="alert alert-warning alert-dismissible fixed-top" role="alert">
        <p><i class="fa fa-exclamation-circle" style="font-size: 16px;"></i> &nbsp; This website uses cookies to improve our services. By using this site, you agree on its use. For more information please check our <a style="color: #222; text-decoration: underline" href="https://koltiva.com/privacy-policy.php" target="_blank">Privacy Policy</a> page.</p>
        <button type="button" class="btn btn-sm btn-secondary btn-pill" onClick="acceptCookie()">Accept</button>
        </div>
        <?php endif; ?> -->
    
        <!-- start: page -->
        <section class="body-sign">
            <div class="center-sign" style="padding-top:30px!important">
                <a href="#" class="logo pull-left" onclick="return false;">
                <svg width="331" height="61" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <g clip-path="url(#a)">
                        <path d="M186.962 15.337c.271-.002.537.069.77.206.239.138.442.332.592.564.163.244.246.533.239.826v4.68a1.623 1.623 0 0 1-1.601 1.6h-9.711v31.033c.001.223-.04.443-.12.65a1.49 1.49 0 0 1-.325.507 1.52 1.52 0 0 1-.502.325 1.717 1.717 0 0 1-.655.12h-4.679a1.531 1.531 0 0 1-1.123-.478 1.548 1.548 0 0 1-.478-1.128V23.214h-9.654a1.613 1.613 0 0 1-1.601-1.601v-4.68a1.491 1.491 0 0 1 .478-1.152 1.59 1.59 0 0 1 1.128-.444h27.242zM226.23 58.395a1.439 1.439 0 0 1 .148 1.654 1.51 1.51 0 0 1-1.434.89h-5.883a1.466 1.466 0 0 1-1.243-.65l-12.096-16.05h-3.346v10.008a1.683 1.683 0 0 1-.21.803 1.44 1.44 0 0 1-.588.588 1.68 1.68 0 0 1-.803.21h-4.679a1.671 1.671 0 0 1-.798-.21 1.433 1.433 0 0 1-.593-.588 1.67 1.67 0 0 1-.206-.803V16.934a1.517 1.517 0 0 1 .478-1.152c.301-.29.705-.45 1.123-.444h12.427a14.494 14.494 0 0 1 10.391 4.172 13.545 13.545 0 0 1 4.354 10.161 13.966 13.966 0 0 1-2.309 7.963 14.525 14.525 0 0 1-6.28 5.42l11.547 15.341zm-17.411-22.033a6.577 6.577 0 0 0 0-13.148h-6.443v13.148h6.443zM265.316 58.92a1.588 1.588 0 0 1-.241 1.43 1.599 1.599 0 0 1-1.298.645h-4.913a1.468 1.468 0 0 1-1.539-1.066L246.6 27.71l-9.057 27.066a1.437 1.437 0 0 1-1.482 1.066h-4.975a1.599 1.599 0 0 1-1.59-1.331 1.587 1.587 0 0 1 .051-.744l12.555-37.385a1.433 1.433 0 0 1 1.482-1.065h6.041a1.53 1.53 0 0 1 1.539 1.065l14.152 42.537zM298.304 48.029c.312.304.493.716.507 1.151a1.39 1.39 0 0 1-.507 1.157 19.519 19.519 0 0 1-9.081 4.975c-3.384.832-6.927.761-10.276-.205a20.202 20.202 0 0 1-8.913-5.3 20.12 20.12 0 0 1-5.21-19.28 20.191 20.191 0 0 1 5.095-9.034 19.73 19.73 0 0 1 8.852-5.386 20.185 20.185 0 0 1 10.275-.297 19.746 19.746 0 0 1 9.143 4.875 1.587 1.587 0 0 1 .399 1.85c-.093.207-.229.39-.399.54l-3.498 3.198a1.365 1.365 0 0 1-1.004.41 1.565 1.565 0 0 1-1.066-.41 11.899 11.899 0 0 0-8.708-3.021 12.388 12.388 0 0 0-11.843 12.379 11.854 11.854 0 0 0 3.465 8.555 12.388 12.388 0 0 0 17.144.683 1.711 1.711 0 0 1 1.066-.353 1.363 1.363 0 0 1 1.008.41l3.551 3.103zM328.51 23.214h-15.872v8.411h10.777a1.524 1.524 0 0 1 1.157.478c.29.304.45.709.444 1.128v4.66c.012.213-.021.427-.098.626a1.51 1.51 0 0 1-1.503.97h-10.777v8.475h15.872a1.626 1.626 0 0 1 1.601 1.596v4.679a1.672 1.672 0 0 1-.21.803c-.135.25-.341.455-.593.588a1.648 1.648 0 0 1-.798.21h-22.148a1.68 1.68 0 0 1-.803-.21 1.462 1.462 0 0 1-.592-.588 1.67 1.67 0 0 1-.206-.803V16.933a1.516 1.516 0 0 1 .478-1.151c.303-.292.708-.451 1.128-.445h22.148c.418-.006.822.154 1.123.445a1.485 1.485 0 0 1 .478 1.151v4.68a1.624 1.624 0 0 1-1.601 1.6h-.005z" fill="#2BBE72"/>
                        <path d="M45.367 58.53a1.477 1.477 0 0 1 .086 1.62 1.434 1.434 0 0 1-1.382.85h-5.63a1.477 1.477 0 0 1-1.295-.703L22.808 39.478v14.879c.002.22-.037.439-.115.645a1.516 1.516 0 0 1-.325.478c-.142.142-.313.25-.502.32a1.671 1.671 0 0 1-.645.12h-4.646c-.277 0-.55-.071-.793-.206a1.434 1.434 0 0 1-.588-.588 1.635 1.635 0 0 1-.206-.793V27.377a1.434 1.434 0 0 1 .478-1.09 1.597 1.597 0 0 1 1.119-.44h4.645c.416-.004.817.153 1.119.44a1.434 1.434 0 0 1 .478 1.09v4.78l10.763-15.82a1.52 1.52 0 0 1 1.295-.646h5.645a1.467 1.467 0 0 1 1.386.822 1.529 1.529 0 0 1-.091 1.649L29.829 35.846l15.538 22.683z" fill="#814C36"/>
                        <path d="M61.067 55.886a19.595 19.595 0 0 1-7.81-1.591 20.423 20.423 0 0 1-6.414-4.263 20.28 20.28 0 1 1 14.233 5.854h-.01zm0-7.824c2.159.01 4.28-.558 6.146-1.644a11.915 11.915 0 0 0 4.47-4.473 12.513 12.513 0 0 0 1.677-6.113c-.001-1.627-.326-3.237-.956-4.736a11.848 11.848 0 0 0-2.677-3.967 12.576 12.576 0 0 0-3.967-2.648 12.322 12.322 0 0 0-7.116-.708 12.23 12.23 0 0 0-9.645 9.645 12.164 12.164 0 0 0 .702 7.088c.913 2.26 2.5 4.185 4.546 5.51a12.125 12.125 0 0 0 6.82 2.047zM111.237 48.124a1.435 1.435 0 0 1 1.085.478c.29.3.45.702.444 1.119v4.645c-.002.278-.073.55-.205.794a1.483 1.483 0 0 1-1.324.793H88.654a1.606 1.606 0 0 1-1.591-1.587v-37.06a1.615 1.615 0 0 1 1.591-1.591H93.3a1.615 1.615 0 0 1 1.586 1.591v30.818h16.351zM135.526 15.715c.268 0 .532.07.765.205.242.138.45.33.606.56.16.243.242.53.235.822v4.65a1.605 1.605 0 0 1-1.587 1.587h-9.64v30.818a1.566 1.566 0 0 1-.445 1.113 1.482 1.482 0 0 1-1.147.478h-4.669a1.607 1.607 0 0 1-1.587-1.587V23.553h-9.588a1.614 1.614 0 0 1-1.586-1.587v-4.65a1.505 1.505 0 0 1 .478-1.147 1.576 1.576 0 0 1 1.113-.44l27.052-.014zM142.991 17.306a1.615 1.615 0 0 1 1.592-1.591h4.645a1.609 1.609 0 0 1 1.587 1.591v37.05c0 .278-.071.551-.205.794a1.505 1.505 0 0 1-.56.588c-.25.142-.534.213-.822.205h-4.645a1.608 1.608 0 0 1-1.592-1.586v-37.05z" fill="#814C36"/>
                        <path d="M18.844 19.652c-2.147 0-4.29-.192-6.404-.573-3.981-.827-6.801-3.193-8.751-6.692C1.777 9.004.769 5.323.023 1.547-.15.674.323-.038 1.189-.005c4.913.196 9.779.679 14.166 3.207a11.949 11.949 0 0 1 5.291 6.457 32.214 32.214 0 0 1 1.759 8.321c.1 1.143-.273 1.51-1.434 1.578-.358 0-.712.071-1.07.086-.359.014-.689.01-1.057.01z" fill="#2BBE72"/>
                    </g>
                    <defs>
                        <clipPath id="a">
                            <path fill="#fff" d="M0 0h330.111v61H0z"/>
                        </clipPath>
                    </defs>
                </svg>
                </a>

                <div class="panel panel-sign" style="width:750px;">
                    <div class="panel-title-sign mt-xl text-right">
                        <h2 class="title text-uppercase text-bold m-none" style="background-color:#2bbe72!important"><i class="fa fa-user mr-xs"></i> Term of Reference</h2>
                    </div>
                    <div class="panel-body" style="border-top-color:#2bbe72!important;padding:20px!important;">

                        <div class="alert alert-danger" id="boxFailed" style="display:none;"></div>

                        <div style="height:600px;overflow-y: scroll;overflow-x: hidden;">

                            <h3>Terms of Use are effective on 01 January, 2021.</h3>

                            <p>These terms are binding and begin to apply to You (Customers or Invited Users) when You start accessing the SawitChain Application from SawitChain AG and its affiliate PT SawitChain (The "SawitChain" or "Us”, “We”, “Our”).</p>

                            <p>By registering to use the SawitChain SawitChain Application, You declare that You have read and understood these Terms & Conditions, and consider to have the authority to act on behalf of anyone registered to use Our SawitChain Application.</p>

                            <p>SawitChain has the right to change these Terms & Conditions anytime, and it will take effect when the new or revised Terms & Conditions are attached to the SawitChain Application. SawitChain will attempt to announce changes to You via email or notification through the SawitChain Application. Because it is quite possible that these Terms & Conditions will change from time after time, it is Your duty to ensure that You have read, understood and agreed to the latest Terms & Conditions available on the SawitChain Application.</p>

                            <h5>Using SawitChain Application</h5>
                            <p>
                                This SawitChain Application is owned, and provided by SawitChain
                                and is made to provide You with the following:
                            </p>
                            <ul>
                                <li>Manage all suppliers, traders, processors, and processing in SawitChain </li>
                                <li>Manage all commodities and trading commodities across the globe</li>
                                <li>Manage all sustainability project across all sourcing country</li>
                            </ul>

                            <h5>Availability of Our Services</h5>
                            <p>
                                Unless required by applicable laws and regulations, SawitChain does
                                not provide any warranty, expressed or implied, and makes no
                                representations regarding the SawitChain Application. Specially,
                                even though the SawitChain Application is well implemented,
                                SawitChain does not declare and guarantee that:
                            </p>
                            <ul>
                                <li>SawitChain Application is free of viruses and other damage; or</li>
                                <li>User access to the SawitChain Application can be used onward or undisturbed, because the use of this SawitChain Application also depends on cellular signals and also the condition of the hardware.</li>
                            </ul>

                            <h5>Your Obligation</h5>
                            <ol>
                                <li>General Obligation
                                    <p>
                                        You must make sure that You only use the SawitChain
                                        Application for Your business needs that is right and
                                        legally, with the Terms and Conditions and notifications
                                        announced by SawitChain or the conditions listed in the
                                        SawitChain Application. The account that You have can only
                                        be run by yourself, and if You want Your account to be run
                                        by someone else, there must be prior approval by SawitChain.
                                        You pledge and release SawitChain and other interested parties
                                        in connection with any liability, loss, cost and damage
                                        suffered by SawitChain and other interested parties as a direct
                                        or indirect result of Your actions inconsistently with or
                                        violating part of these Terms & Conditions. SawitChain has the
                                        right (at its sole discretion) to obtain exclusive defense
                                        and control over everything related to Your compensation, in
                                        which case You must assist and work with SawitChain to carry
                                        out the defense.
                                    </p>
                                </li>
                                <li>Access term
                                    <p>
                                        You must ensure that all the usernames and passwords needed
                                        to access the SawitChain Application are stored securely and
                                        confidentially. You must immediately notify SawitChain of Your
                                        unauthorized use of your password, or other security
                                        violations, and SawitChain will reset your password, and You
                                        must take all other actions that SawitChain considers important
                                        enough to maintain or improve the security of the computer
                                        system and the SawitChain network, and Your access to our
                                        SawitChain Application.
                                        <br />
                                        As a condition of these Terms, when accessing and using the
                                        SawitChain Application, you must:
                                        <ul>
                                            <li>Do not try to weaken the security or integrity of the SawitChain Application system or the SawitChain network;</li>
                                            <li>Do not use or abuse the SawitChain Application in any way that can interfere with the ability of other users to use the SawitChain Application;</li>
                                            <li>Not trying to get unauthorized access to any material other than those that have been stated clearly that you have obtained permission to access it;</li>
                                            <li>Not sending, or entering into the SawitChain Application: any file that can damage other people's computer tools or software;</li>
                                            <li>Do not try to change, copy, emulate, and disassemble data or system of SawitChain Application.</li>
                                        </ul>
                                    </p>
                                </li>
                                <li>Usage Limits
                                    <p>The use of the SawitChain Application can be restricted according to the access rights and responsibilities of each user and You can see the restrictions on the SawitChain Application user interface.</p>
                                </li>
                                <li>Responsibility for Compensation
                                    <p>You release SawitChain from all: claims, claims, costs of loss, damage and loss arising as a result of your violation of the Terms and Conditions set forth in this Agreement, or any obligations that You may have to SawitChain.</p>
                                </li>
                            </ol> 
                            <h5>Confidentiality and Privacy</h5>
                            <ul>
                                <li>Confidentiality
                                    <p>Each party WILL NOT, without written consent from the other party, disclose or provide Confidential Information to anyone, or use it for their own benefit, other than as referred to in these Conditions. The obligations of each party in this provision will survive even if one of the parties has not cooperated again.
                                        <br>
                                        The provisions of the article do not apply to information that:
                                    </p>
                                    <ul>
                                        <li>It has become public knowledge other than because of violations of this provision;</li>
                                        <li>Received from third parties who legally obtained it, and did not have an obligation to limit disclosure;</li>
                                        <li>Develop by SawitChain itself.</li>
                                    </ul>
                                </li>
                                <li>Privacy
                                    <p>SawitChain owns and maintains a privacy policy that explains and defines the parties obligations to respect personal information. You are advised to read our privacy policy first, and you will be deemed to have agreed to the policy when you agree to these terms & conditions.</p>
                                </li>
                            </ul>

                            <h5>Intellectual Property Rights</h5>
                            <p>Copyright and any Intellectual Property Rights that attached to this SawitChain Application are owned by SawitChain.</p>
                            <p>Ownership and all Intellectual Property Rights contained in Data is still your property. It is strongly recommended that you keep a copy of all data that you input into the SawitChain Application. SawitChain adheres to the policy and undergoes the best procedures to prevent data loss, including daily system routines for backing up data, but makes no guarantees that there will never be data loss.</p>
                            <p>As regulated in the applicable laws and in Indonesia regulations that connection with Intellectual Property Rights, no part of the SawitChain Application is permitted to be reproduced, copied, published, made or transmitted in any form in any way without the consent of SawitChain. Failure not to follow this article will result in you being subject to civil and criminal sanctions in accordance with applicable laws and regulations.</p>

                            <h5>Guarantee and Recognition</h5>
                            <ol>
                                <li>You acknowledge that you have the authority to use the SawitChain Application, and to access information and Data that you input into the SawitChain Application, including any information or Data entered into the SawitChain Application by anyone you have authorized to use the SawitChain Application.</li>
                                <li>You are also authorized to access information and data that has been processes, which is provided to you through of the use of SawitChain Application.</li>
                                <li>SawitChain is not responsible to anyone other than you, and there is no intention in this Agreement to benefit anyone other than you. If you use the SawitChain Application on behalf of or to benefit someone other than you (as well as a legal entity or not, or others), you agree that:
                                <ul>
                                    <li>You are responsible for ensuring that you have the right to do so;</li>
                                    <li>You are responsible for authorizing to anyone that you provide the access for information or data, and you agree that SawitChain does not have the responsibility to provide access to the information or data to anyone without your authorization, and may show any request to obtain information to You are to be served;</li>
                                    <li>You release SawitChain from any claim or loss related to: SawitChain's refusal to provide access to your information or data to anyone in accordance with this provision; Provision of information or data by SawitChain to anyone based on your authorization.</li>
                                </ul>
                                </li>
                                <li>To determine that our SawitChain Application meets your needs and can be used in accordance with the objectives is your own responsibility.</li>
                            </ol>
                            <h5>Others General Provisions</h5>
                            <ol>
                                <li>You cannot transfer the rights to another person without the written consent of SawitChain.</li>
                                <li>If there is a conflict between the two parties, it will try to be resolved by discussion first to reach an agreement. If such an agreement is not reached, then both parties agree to resolve the issue through legal procedures.</li>
                                <li>Any notification given based on these Terms by one party to another must be made in writing via email and will be deemed to have been given at the time the transmission was made. Notification to SawitChain must be sent to support@sawitchain.com or to another e-mail address notified to you by SawitChain. Notifications to you will be sent to the email address you provided when making your access to our SawitChain Application.</li>
                            </ol>
                        </div>

                        <br /><br />
                        <div class="row">
                            <div class="col-sm-12 text-right">
                                <button style="display: inline-block;vertical-align: top;" id="btnAgree" class="btn btn-primary btn-md center-block" Style="width: 100px;">I Agree</button>
                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                <button style="display: inline-block;vertical-align: top;" id="btnNotAgree" class="btn btn-danger btn-md center-block" Style="width: 100px;">I Do Not Agree</button>
                                &nbsp;&nbsp;&nbsp;&nbsp;<img id="ajaxImgLoad" style="margin-top:8px;display:none;" src="<?php echo base_url() ?>images/dg/ajax.gif" />
                            </div>
                        </div>

                    </div>

                </div>
            </div>
            <input type="hidden" name="UserId" id="UserId" value="<?php echo $UserId?>" />
        </section>
        <!-- end: page -->

        <div class="forgot-footer bg-gray">
            <ul class="list-inline">
                <li><span>@<?php echo date('Y'); ?> SawitChain and All Its Affiliates</span></li>
                <li><a href="https://sawitchain.com/privacy-policy.php" target="_blank" rel="noopener noreferrer">Privacy Policies</a></li>
                <li><a href="#">Terms of Use</a></li>
            </ul>
        </div>

        <script>
        $(document).ready(function(){
            $(document).on( "click", "#btnAgree", function(e) {
                $('#ajaxImgLoad').show();
                
                //ajax tor
                $.ajax({
                    type: "POST",
                    url: '<?php echo $api_url?>'+'/page/front_user_tor',
                    data: {
                        UserId: document.getElementById("UserId").value
                    },
                    success: function (r) {
                        //console.log(r);
                        window.location = '<?php echo base_url()?>';
                        $("#ajaxImgLoad").hide();
                    },
                    error: function(r){
                        try {
                            let objR = JSON.parse(r.responseText);
                            $('#boxFailed').html(objR.message);
                            $("#boxFailed").show();
                        }
                        catch(err) {
                            $('#boxFailed').html(lang('Submit failed, Please try again later'));
                            $("#boxFailed").show();
                        }
                        $("#ajaxImgLoad").hide();
                    }
                });
            });

            $(document).on( "click", "#btnNotAgree", function(e) {
                $('#ajaxImgLoad').show();
                window.location = '<?php echo base_url()?>'+'/system/login/logout';
            });
        });
        </script>