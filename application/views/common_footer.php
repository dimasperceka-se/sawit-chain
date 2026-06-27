
    </div>

    <div id="page-info" class="md-modal colored-header md-effect-1">
        <div class="md-content">
            <div class="modal-header">
                <button type="button" data-dismiss="modal" aria-hidden="true" class="close md-close"><i class="icon s7-close"></i></button>
                <h3 class="modal-title"><?php echo lang('Page Info') ?></h3>
            </div>
            <div class="modal-body" id="page-info-content">

            </div>
            <div class="modal-footer">
                <button type="button" data-dismiss="modal" class="btn btn-default md-close">Close</button>
            </div>
        </div>
    </div>
    <div class="md-overlay"></div>

    <nav class="am-right-sidebar">
        <div class="sb-content">
            <?php
                $fotoProfile = "";
                if($_SESSION['Photo_staff'] != ""){
                    $fotoProfile = $this->config->item('api_base_url').'images/staff/'.$_SESSION['Photo_staff'];
                }else{
                    if($_SESSION['Gender'] == "f"){
                        $fotoProfile = $this->config->item('api_base_url').'images/default_photo/female-business.jpg';
                    }else{
                        $fotoProfile = $this->config->item('api_base_url').'images/default_photo/male-business.jpg';
                    }
                }
            ?>
            <div class="user-info"><img src="<?php echo $fotoProfile;?>"><span class="name"><?php echo $_SESSION['realname'] ?><span class="status"></span></span><span class="position"><?php echo $_SESSION['group'] ?></span></div>
            <div class="tab-navigation">
                <ul role="tablist" class="nav nav-tabs nav-justified">
                    <li role="presentation" class="active"><a href="#tab1" aria-controls="home" role="tab" data-toggle="tab"> <span class="icon s7-smile"></span></a></li>
                    <li role="presentation"><a href="#tab2" aria-controls="settings" role="tab" data-toggle="tab"> <span class="icon s7-ticket"></span></a></li>
                    <li style="display:none;" role="presentation"><a href="#tab4" aria-controls="profile" role="tab" data-toggle="tab"> <span class="icon s7-chat"></span></a></li>
                    <!-- <li role="presentation" class="active"><a href="#tab3" aria-controls="messages" role="tab" data-toggle="tab"> <span class="icon s7-help2"></span></a></li> -->
                </ul>
            </div>
            <div class="tab-panel">
                <div class="tab-content">
                    <div id="tab1" role="tabpanel" class="tab-pane announcement active am-scroller nano">
                        <div class="nano-content">
                            <div class="content">
                                <h2><?php echo lang('Announcement') ?></h2>
                                <ul id="announcement">
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div id="tab2" role="tabpanel" class="tab-pane announcement active am-scroller nano">
                        <div class="nano-content">
                            <div class="content">
                                <h2><?php echo lang('Documents') ?></h2>
                                <ul id="document">
                                </ul>
                            </div>
                        </div>
                    </div>
                    <!-- <div id="tab3" role="tabpanel" class="tab-pane faqs am-scroller nano">
                        <div class="nano-content">
                            <div class="content">
                                <h2>FAQs</h2>

                                <div id="accordion" role="tablist" aria-multiselectable="true" class="panel-group accordion">
                                    <div class="panel">
                                        <div role="tab" class="panel-heading">
                                            <h4 class="panel-title"><a data-toggle="collapse" data-parent="#accordion" href="#faq1" aria-expanded="true" aria-controls="collapseOne">
                                                <div class="icon"><span class="s7-angle-down"></span></div>
                                                <span class="title">Under Error 352</span></a></h4>
                                        </div>
                                        <div id="faq1" role="tabpanel" aria-labelledby="headingOne" class="panel-collapse collapse in">
                                            <div class="panel-body">Suspendisse nec leo tortor rhoncus tincidunt. Duis sit amet rutrum elit.</div>
                                        </div>
                                    </div>
                                    <div class="panel">
                                        <div role="tab" class="panel-heading">
                                            <h4 class="panel-title"><a data-toggle="collapse" data-parent="#accordion" href="#faq2" aria-expanded="false" aria-controls="collapseTwo" class="collapsed">
                                                <div class="icon"><span class="s7-angle-down"></span></div>
                                                <span class="title">Failure platform</span></a></h4>
                                        </div>
                                        <div id="faq2" role="tabpanel" aria-labelledby="headingTwo" class="panel-collapse collapse">
                                            <div class="panel-body">Suspendisse nec leo tortor rhoncus tincidunt. Duis sit amet rutrum elit.</div>
                                        </div>
                                    </div>
                                    <div class="panel">
                                        <div role="tab" class="panel-heading">
                                            <h4 class="panel-title"><a data-toggle="collapse" data-parent="#accordion" href="#faq3" aria-expanded="false" aria-controls="collapseThree" class="collapsed">
                                                <div class="icon"><span class="s7-angle-down"></span></div>
                                                <span class="title">Error 404</span></a></h4>
                                        </div>
                                        <div id="faq3" role="tabpanel" aria-labelledby="headingThree" class="panel-collapse collapse">
                                            <div class="panel-body">Suspendisse nec leo tortor rhoncus tincidunt. Duis sit amet rutrum elit.</div>
                                        </div>
                                    </div>
                                    <div class="panel">
                                        <div role="tab" class="panel-heading">
                                            <h4 class="panel-title"><a data-toggle="collapse" data-parent="#accordion" href="#faq4" aria-expanded="false" aria-controls="collapseThree" class="collapsed">
                                                <div class="icon"><span class="s7-angle-down"></span></div>
                                                <span class="title">New workstation</span></a></h4>
                                        </div>
                                        <div id="faq4" role="tabpanel" aria-labelledby="headingThree" class="panel-collapse collapse">
                                            <div class="panel-body">Suspendisse nec leo tortor rhoncus tincidunt. Duis sit amet rutrum elit.</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="search">
                            <input type="text" placeholder="Search..." name="q"><span class="s7-search"></span>
                        </div>
                    </div> -->
                    <div id="tab4" role="tabpanel" class="tab-pane ticket am-scroller nano" style="display:none;">
                        <div class="nano-content">
                            <div class="content">
                                <h2><?php echo lang('Feedback') ?></h2>

                                <form id="formMainFeedback">
                                    <div class="form-group send-ticket">
                                        <input id="titleFeedback" type="text" placeholder="<?php echo lang('Title') ?>" class="form-control">
                                    </div>
                                    <div class="form-group send-ticket">
                                        <textarea id="contentFeedback" rows="3" placeholder="<?php echo lang('Write Here') ?>..." class="form-control"></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-primary btn-lg"><?php echo lang('Submit') ?></button>
                                </form>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>
</div>
<script src="<?php echo base_url() ?>assets/lib/jquery.nanoscroller/javascripts/jquery.nanoscroller.min.js" type="text/javascript"></script>
<script src="<?php echo base_url() ?>assets/js/main.js" type="text/javascript"></script>
<script src="<?php echo base_url() ?>assets/lib/bootstrap/dist/js/bootstrap.min.js" type="text/javascript"></script>
<script src="<?php echo base_url() ?>assets/lib/datetimepicker/js/bootstrap-datetimepicker.min.js" type="text/javascript"></script>
<script src="<?php echo base_url() ?>js/add.js" type="text/javascript"></script>
<script src="<?php echo base_url() ?>js/functions.js" type="text/javascript"></script>
<script src="<?php echo base_url() ?>js/plugins/print-lib.js" type="text/javascript"></script>


<!--for CKEditor  -->
<script src="<?php echo base_url() ?>assets/ckeditor/ckeditor.js"></script>
<!-- END CKEditor -->


<!--
<script src="<?php echo base_url() ?>assets/lib/jquery-flot/jquery.flot.js" type="text/javascript"></script>
<script src="<?php echo base_url() ?>assets/lib/jquery-flot/jquery.flot.pie.js" type="text/javascript"></script>
<script src="<?php echo base_url() ?>assets/lib/jquery-flot/jquery.flot.resize.js" type="text/javascript"></script>
<script src="<?php echo base_url() ?>assets/lib/jquery-flot/plugins/jquery.flot.orderBars.js" type="text/javascript"></script>
<script src="<?php echo base_url() ?>assets/lib/jquery-flot/plugins/curvedLines.js" type="text/javascript"></script>
<script src="<?php echo base_url() ?>assets/lib/jquery.sparkline/jquery.sparkline.min.js" type="text/javascript"></script>
<script src="<?php echo base_url() ?>assets/lib/jquery-ui/jquery-ui.min.js" type="text/javascript"></script>
<script src="<?php echo base_url() ?>assets/lib/jquery.vectormap/jquery-jvectormap-1.2.2.min.js" type="text/javascript"></script>
<script src="<?php echo base_url() ?>assets/lib/jquery.vectormap/maps/jquery-jvectormap-us-merc-en.js" type="text/javascript"></script>
<script src="<?php echo base_url() ?>assets/lib/jquery.vectormap/maps/jquery-jvectormap-world-mill-en.js" type="text/javascript"></script>
<script src="<?php echo base_url() ?>assets/lib/jquery.vectormap/maps/jquery-jvectormap-uk-mill-en.js" type="text/javascript"></script>
<script src="<?php echo base_url() ?>assets/lib/jquery.vectormap/maps/jquery-jvectormap-fr-merc-en.js" type="text/javascript"></script>
<script src="<?php echo base_url() ?>assets/lib/jquery.vectormap/maps/jquery-jvectormap-us-il-chicago-mill-en.js" type="text/javascript"></script>
<script src="<?php echo base_url() ?>assets/lib/jquery.vectormap/maps/jquery-jvectormap-au-mill-en.js" type="text/javascript"></script>
<script src="<?php echo base_url() ?>assets/lib/jquery.vectormap/maps/jquery-jvectormap-in-mill-en.js" type="text/javascript"></script>
<script src="<?php echo base_url() ?>assets/lib/jquery.vectormap/maps/jquery-jvectormap-map.js" type="text/javascript"></script>
<script src="<?php echo base_url() ?>assets/lib/jquery.vectormap/maps/jquery-jvectormap-ca-lcc-en.js" type="text/javascript"></script>
<script src="<?php echo base_url() ?>assets/lib/countup/countUp.min.js" type="text/javascript"></script>
<script src="<?php echo base_url() ?>assets/lib/chartjs/Chart.min.js" type="text/javascript"></script>
<script src="<?php echo base_url() ?>assets/js/app-dashboard.js" type="text/javascript"></script>
-->
<script type='text/javascript'>
    var url_browser = '<?php echo base_url() ?>api/EmailTemplate/browse';
    var url_upload = '<?php echo base_url() ?>api/EmailTemplate/editor_upload';

    (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
    (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
    m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
    })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

    ga('create', 'UA-46635047-1', 'cocoatrace.com');
    ga('send', 'pageview');

    $(window).load(function() {
     $('#loading').fadeOut();
    });

    $(document).ready(function() {
        $('body').css('display', 'none');
        $('body').fadeIn(500);


        $("#logo a, #sidebar_menu a:not(.accordion-toggle), a.ajx").click(function() {
        //event.preventDefault();
        //newLocation = this.href;
        //$('body').fadeOut(500, newpage);
        });
        function newpage() {
        window.location = newLocation;
        }
        setTimeout(window.loadGoogleMaps(), 100);

        //getNotifHeader();
        loadAnnouncement();
        loadDocument();

    });

/*
 * GOOGLE MAPS
 * description: Append google maps to head dynamically
 */

var gMapsLoaded = false;
window.gMapsCallback = function() {
    gMapsLoaded = true;
    $(window).trigger('gMapsLoaded');
}
window.loadGoogleMaps = function(callback) {
    if (gMapsLoaded)
        return window.gMapsCallback();
    var script_tag = document.createElement('script');
    script_tag.setAttribute("type", "text/javascript");
    script_tag.setAttribute("src", "https://maps.googleapis.com/maps/api/js?key=AIzaSyCoaZKDW1hV9HLw9hcO_rLmiQx0Z5P2M3g&callback=gMapsCallback");
    (document.getElementsByTagName("head")[0] || document.documentElement).appendChild(script_tag);
    if (callback) {
        callback();
    }
}
/* ~ END: GOOGLE MAPS */

/*
 * LOAD SCRIPTS
 * Usage:
 * Define function = myPrettyCode ()...
 * loadScript("js/my_lovely_script.js", myPrettyCode);
 */

var jsArray = {};

function loadScript(scriptName, callback) {

    if (!jsArray[scriptName]) {
        jsArray[scriptName] = true;

        // adding the script tag to the head as suggested before
        var body = document.getElementsByTagName('body')[0];
        var script = document.createElement('script');
        script.type = 'text/javascript';
        script.src = scriptName;

        // then bind the event to the callback function
        // there are several events for cross browser compatibility
        //script.onreadystatechange = callback;
        script.onload = callback;

        // fire the loading
        body.appendChild(script);

    } else if (callback) {// changed else to else if(callback)
        // console.log("JS file already added!");
        //execute function
        callback();
    }

}

/* ~ END: LOAD SCRIPTS */
</script>
<!--<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyACXVwWCJen2OZeCAEYdRxP_HEh7CkxOvs&sensor=false"></script>-->

<script language="javascript" type="text/javascript" src="<?php echo base_url() ?>js/gmap3.js"></script>
<script type="text/javascript">
   $(document).ready(function () {
        //initialize the javascript
        App.init();
        // App.dashboard();

        //ambil data notifikasi header
        var interval = <?php echo (!$this->config->item('NotifInterval') || $this->config->item('NotifInterval') == '')?0:$this->config->item('NotifInterval')?>;
        if(interval*1>1)
        {
            setInterval(function() {
               getNotifHeader();
          }, interval*1);
        }

      //form feedback
      $(document).on( "submit", "#formMainFeedback", function(e) {
         e.preventDefault();

         //cek validasi form
         var titleFeedback = $("#titleFeedback").val();
         var contentFeedback = $("#contentFeedback").val();
         if(titleFeedback==""){
            Ext.MessageBox.show({
               title: 'Notifications',
               msg: 'Title is empty',
               buttons: Ext.MessageBox.OK,
               animateTarget: 'mb9',
               icon: 'ext-mb-warning'
            });
            return false;
         }
         if(contentFeedback==""){
            Ext.MessageBox.show({
               title: 'Notifications',
               msg: 'Content is empty',
               buttons: Ext.MessageBox.OK,
               animateTarget: 'mb9',
               icon: 'ext-mb-warning'
            });
            return false;
         }

         Ext.MessageBox.show({
            msg: 'Submitting your data, please wait...',
            progressText: 'Saving...',
            width:300,
            wait:true,
            waitConfig: {interval:200},
            icon:'ext-mb-download', //custom class in msg-box.html
            animateTarget: 'mb7'
         });

         Ext.Ajax.request({
            waitMsg: 'Please wait...',
            url: m_api+'/cms_feedback/insert_feedback',
            method : 'POST',
            params: {
               titleFeedback: titleFeedback,
               contentFeedback: contentFeedback
            },
            success: function(response, opts){
               Ext.MessageBox.hide();
               $("#titleFeedback").val('');
               $("#contentFeedback").val('');

               Ext.MessageBox.show({
                  title: 'Notifications',
                  msg: 'Feedback sent successfully!',
                  buttons: Ext.MessageBox.OK,
                  animateTarget: 'mb9',
                  icon: 'ext-mb-info'
               });
            },
            failure: function(response, opts){
               Ext.MessageBox.hide();
               Ext.MessageBox.show({
                  title: 'Notifications',
                  msg: 'Failed to send feedback!',
                  buttons: Ext.MessageBox.OK,
                  animateTarget: 'mb9',
                  icon: 'ext-mb-error'
               });
            }
         });

      });

   });
</script>


</body>
</html>