<?php

/**
 * @Author: Aprianto 
 */
//cek apakah dashboard filter region aktif
$uri4 = $this->uri->segment(4);
?>

<script>
    $('#page_title, #breadcrumb_title').text('<?php echo $titlet ?>');
    <?php if($uri4 ==""){?>
        $('#first-breadcrumb').text('<?php echo $breadcrumb_1 ?>');
        $('#second-breadcrumb').text('<?php echo $breadcrumb_2 ?>');
    <?php }?>
 
</script>

  
<div id="content">
    <iframe src="<?php echo base_url() . 'map/merangin/sinarmas_merangin';?>"  frameborder="0" width="100%"  marginheight="0"  marginwidth="0" scrolling="no" style="height:1324px; padding:5px;" onload="resizeIframe(this)"></iframe>
</div>
 