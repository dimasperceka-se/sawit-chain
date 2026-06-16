<?php
/******************************************
 *  Author : n1colius.lau@gmail.com   
 *  Created On : Thu Jul 16 2020
 *  File : common_footer_front.php
 *******************************************/
?>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script src="<?php echo base_url() ?>assets/vendor/bootstrap/js/bootstrap.min.js"></script>
    <script>
        function acceptCookie() {
            $.ajax({
                url: '<?php echo base_url() ?>system/login/accept_cookie',
                success: function() {
                    $('#privacyAlert').alert('close');
                }
            });
        }

        $(function() {
            localStorage.clear();
            $('[data-toggle="tooltip"]').tooltip();
        });

        function FormLoginSubmit(){
            //Clear Local Storage
            localStorage.clear();
            return true;
        }
    </script>
</body>

</html>