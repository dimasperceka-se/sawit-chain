<?php
/**
 * @Author: nikolius
 * @Date:   2017-10-18 16:06:45
 * @Last Modified by:   nikolius
 * @Last Modified time: 2017-10-18 16:57:03
 */
echo $templateHeader;
?>

<div class="panel panel-sign" style="margin-bottom:135px">
    <div class="panel-title-sign mt-xl text-right">
        <h2 class="title text-uppercase text-bold m-none" style="background-color:#95130b!important"><i class="fa fa-user mr-xs"></i> Registering Staff</h2>
    </div>

    <div class="panel-body" style="border-top-color:#95130b!important;padding:20px 20px 8px 20px !important">

    <?php if($proses == true){?>

        <div class="alert alert-success">
            Successfully registering your account, please wait while redirecting
        </div>

        <div align="center">
            <progress id="progressbar" value="0" max="100"></progress>
        </div>

        <script type="text/javascript">
        $(document).ready(function(){
            var progressbar = $('#progressbar'),max = progressbar.attr('max'),time = (1000/max)*5,value = progressbar.val();

            var loading = function() {
                value += 1;
                addValue = progressbar.val(value);

                $('.progress-value').html(value + '%');

                if (value == max) {
                    clearInterval(animate);

                    //redirect masuk login
                    window.location.href = '<?php echo base_url();?>';
                }
            };

            var animate = setInterval(function() {
                loading();
            }, time);
        });
        </script>

    <?php } else {?>
        <div class="alert alert-danger">
            <strong>Register Failed</strong>
            <br /><br />
            Please try again in a few minutes
        </div>
    <?php }?>

    </div>
</div>

<?php echo $templateFooter;?>