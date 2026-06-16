<?php
/**
 * @Author: nikolius
 * @Date:   2017-03-02 10:57:05
 */

$baseurlcheck = rtrim(trim(base_url()),'/');
$title        = "Koltiva | KoltiTrace Palm Oil";

if($this->config->item('url_gar') == $baseurlcheck) {
    $title = "GAR | KoltiTrace Palm Oil";
}
?>
<!doctype html>
<html class="fixed">
    <head>
        <title><?= $title ?></title>
        <link href="<?php echo base_url();?>img/favicon.png" rel="shortcut icon">
        <!-- Basic -->
        <meta charset="UTF-8">

        <meta name="keywords" content="Palmoiltrace" />
        <meta name="description" content="Palmoiltrace">
        <meta name="author" content="okler.net">

        <!-- Mobile Metas -->
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />

        <!-- Web Fonts  -->
        <link href="<?php echo $this->config->item('http'); ?>://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700,800|Shadows+Into+Light" rel="stylesheet" type="text/css">

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

        <!-- Head Libs -->
        <!-- <script src="<?php echo base_url() ?>assets/vendor/modernizr/modernizr.js"></script> -->

    </head>
    <body>
        <!-- start: page -->
        <section class="body-sign">
            <div class="center-sign" style="padding-top:0px!important">
                <a href="#" class="logo pull-left" onclick="return false;">
                    <img src="<?php echo base_url() ?>assets/images/logo.png" height="54" alt="Palmoiltrace" />
                </a>

                <div class="panel panel-sign" style="margin-bottom:150px; width:750px;">
                    <div class="panel-title-sign mt-xl text-right">
                        <h2 class="title text-uppercase text-bold m-none" style="background-color:#95130b!important"><i class="fa fa-user mr-xs"></i> Term of Reference</h2>
                    </div>
                    <div class="panel-body" style="border-top-color:#95130b!important;padding:20px!important;">
                        <div style="height:500px;overflow-y: scroll;overflow-x: hidden;">
                            <h3>These Terms of Use are effective on 11 April, 2019.</h3>

                            <p>By accessing or using the PalmoilTrace Data Management service, made available by Koltiva, you agree to be bound by these terms of use ("Terms of Use"). The Service is owned and controlled by Koltiva. These Terms of Use affect your legal rights and obligations.</p>

                            <h5>Basic Term</h5>

                            <ol>
                                <li>You may not post violent, inappropriate, discriminatory, unlawful, infringing, hateful, or suggestive photos or other content via the Service.</li>
                                <li>You are responsible for any activity that occurs through your account and you agree you will not sell, transfer, license or assign your account, connections, username, or any account rights. You also represent that all information you provide or provided to PalmoilTrace application upon registration and at all other times will be true, accurate, current and complete and you agree to update your information as necessary to maintain its truth and accuracy.</li>
                                <li>You agree that you will not solicit, collect or use the login credentials of other PalmoilTrace application users.</li>
                                <li>You are responsible for keeping your password secret and secure. Do not share your password with anyone. The PalmoilTrace application has no responsibilities for consequents resulting from the sharing of passwords.</li>
                                <li>You must not defame, stalk, bully, abuse, harass, threaten, impersonate or intimidate people or entities.</li>
                                <li>You may not use the Service for any illegal or unauthorized purpose. You agree to comply with all laws, rules and regulations of Indonesia applicable to your use of the Service and your Content (defined below), including but not limited to, copyright laws.</li>
                                <li>You are solely responsible for your conduct and any data, text, files, information, usernames, images, graphics, photos, profiles, audio and video clips, sounds, musical works, works of authorship, applications, links and other content or materials (collectively, "Content") that you submit, post or display on or via the Service.</li>
                                <li>You must not create or submit unwanted email, comments, likes or other forms of commercial or harassing communications (a/k/a "spam") to any users.</li>
                                <li>You must not interfere or disrupt the Service or servers or networks connected to the Service, including by transmitting any worms, viruses, spyware, malware or any other code of a destructive or disruptive nature. You may not inject content or code or otherwise alter or interfere with the way any PalmoilTrace application page is rendered or displayed in a user's browser or device.</li>
                                <li>You must not create accounts with the Service through unauthorized means, including but not limited to, by using an automated device, script, bot, spider, crawler or scraper.</li>
                                <li>You must not attempt to restrict another user from using or enjoying the Service and you must not encourage or facilitate violations of these Terms of Use or any other PalmoilTrace application terms.</li>
                                <li>Violation of these Terms of Use may, in PalmoilTrace application’s sole discretion, result in termination of your PalmoilTrace application account. You understand and agree that PalmoilTrace application cannot and will not be responsible for the Content posted on the Service and you use the Service at your own risk. If you violate the letter or spirit of these Terms of Use, or otherwise create risk or possible legal exposure for PalmoilTrace application, we can stop providing all or part of the Service to you.</li>
                            </ol>

                            <h5>General Conditions</h5>
                            <ol>
                                <li>We reserve the right, in our sole discretion, to change these Terms of Use ("Updated Terms") from time to time. Unless we make a change for legal or administrative reasons, we will provide reasonable advance notice before the Updated Terms become effective. You agree that we may notify you of the Updated Terms by posting them on the Service, and that your use of the Service after the effective date of the Updated Terms (or engaging in such other conduct as we may reasonably specify) constitutes your agreement to the Updated Terms. Therefore, you should review these Terms of Use and any Updated Terms before using the Service. The Updated Terms will be effective as of the time of posting, or such later date as may be specified in the Updated Terms, and will apply to your use of the Service from that point forward. These Terms of Use will govern any disputes arising before the effective date of the Updated Terms.</li>
                                <li>We reserve the right to refuse access to the Service to anyone for any reason at any time.</li>
                                <li>We reserve the right to force forfeiture of any username for any reason.</li>
                                <li>We may, but have no obligation to, remove, edit, block, and/or monitor Content or  accounts containing Content that we determine in our sole discretion violates these Terms of Use.</li>
                                <li>You are solely responsible for your interaction with other users of the Service, whether  online or offline. You agree that PalmoilTrace application is not responsible or liable for the conduct of any user. PalmoilTrace application reserves the right, but has no obligation, to monitor or become involved in disputes between you and other users. Exercise common sense and your best judgment when interacting with others, including when you submit or post Content or any personal or other information.</li>
                                <li>There may be links from the Service, or from communications you receive from the Service, to third-party web sites or features. There may also be links to third-party web sites or features in images or comments within the Service. The Service also includes third-party content that we do not control, maintain or endorse. Functionality on the Service may also permit interactions between the Service and a third-party web site or feature, including applications that connect the Service or your profile on the Service with a third-party web site or feature. For example, the Service may include a feature that enables you to share Content from the Service or your Content with a third party, which may be publicly posted on that third party's service or application. Using this functionality typically requires you to login to your account on the third-party service and you do so at your own risk. PalmoilTrace application does not control any of these third- party web services or any of their content. You expressly acknowledge and agree that PalmoilTrace application is in no way responsible or liable for any such third-party services or features. YOUR CORRESPONDENCE AND BUSINESS DEALINGS WITH THIRD PARTIES FOUND THROUGH THE SERVICE ARE SOLELY BETWEEN YOU AND THE THIRD PARTY. You may choose, at your sole and absolute discretion and risk, to use applications that connect the Service or your profile on the Service with a third-party service (each, an "Application") and such Application may interact with, connect to or gather and/or pull information from and to your Service profile. By using such Applications, you acknowledge and agree to the following: (i) if you use an Application to share information, you are consenting to information about your profile on the Service being shared (ii) your use of an Application may cause personally identifying information to be publicly disclosed and/or associated with you, even if PalmoilTrace application has not itself provided such information and (iii) your use of an Application is at your own option and risk, and you will hold the PalmoilTrace application Parties (defined below) harmless for activity related to the Application.</li>
                                <li>You agree that you are responsible for all data charges you incur through use of the Service.</li>
                                <li>We prohibit crawling, scraping, caching or otherwise accessing any content on the Service via automated means, including but not limited to, user profiles and photos (except as may be the result of standard search engine protocols or technologies used by a search engine with PalmoilTrace application’s express consent).</li>
                            </ol>

                            <br />
                            <p>The effective date of these Terms of Use is April 11, 2019. These Terms of Use were written in English (US). To the extent any translated version of these Terms of Use conflicts with the English version, the English version controls.</p>

                        </div>

                        <div class="row">
                            <?php if($ViewOnly == 'no') { ?>
                                <div class="col-sm-12 text-right">
                                    <button style="display: inline-block;vertical-align: top;" id="btnAgree" class="btn btn-primary btn-md center-block" Style="width: 100px;">I Agree</button>
                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                    <button style="display: inline-block;vertical-align: top;" id="btnNotAgree" class="btn btn-danger btn-md center-block" Style="width: 100px;">I Do Not Agree</button>
                                </div>
                            <?php }else{ ?>
                                <!-- View Only Mas Nikolius -->
                            <?php } ?>
                        </div>

                    </div>

                </div>
            </div>
        </section>
        <!-- end: page -->

        <div class="navbar navbar-fixed-bottom">
            <p style="color:#51B451;float: none;font-size:0.9em;margin-bottom:5px!important;margin-top:25px!important;" class="navbar-text text-center">
                <b>&nbsp; &copy; 2013 - <?php echo date('Y')?> PT. Koltiva, All rights reserved</b>
            </p>
        </div>

        <!-- Vendor -->
        <script src="<?php echo base_url() ?>assets/vendor/jquery/jquery.js"></script>
        <script src="<?php echo base_url() ?>assets/vendor/jquery-browser-mobile/jquery.browser.mobile.js"></script>
        <script src="<?php echo base_url() ?>assets/vendor/bootstrap/js/bootstrap.js"></script>

        <script type="text/javascript">
            $(document).ready(function(){
                $(document).on( "click", "#btnNotAgree", function(e) {
                    window.location = '<?php echo base_url()?>';
                });

                <?php if($tor_register_staff == "1"){?>
                    $(document).on( "click", "#btnAgree", function(e) {
                        window.location = '<?php echo base_url()?>system/register/submit_register/';
                    });
                <?php }else{ ?>
                    $(document).on( "click", "#btnAgree", function(e) {
                        window.location = '<?php echo base_url()?>system/login/changepass_first/';
                    });
                <?php }?>
            });
        </script>
    </body>
</html>