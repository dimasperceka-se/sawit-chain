<div class="main-content" >
<div class="container-fluid">
    <h1>Selamat datang di Palmoiltrace</h1>
</div>
<script type="text/javascript">
    $('#page_title, #breadcrumb_title').text('<?php echo $titlet ?>');
$('#first-breadcrumb').text('<?php echo $breadcrumb_1 ?>');
$('#second-breadcrumb').text('<?php echo $breadcrumb_2 ?>');
    var varjs =
    {
        "config": {
            "base_url": "<?=base_url()?>/",
            "default_currency": "IDR",
            "extjs_version": "<?=$ver?>"
        }
    }
    ;
    <?$key = array_keys($action);
    for ($i=0;$i<sizeof($action);$i++) {?>
        var m_<?=$key[$i]?> = <?=($action[$key[$i]]===true?'true':($action[$key[$i]]===false?'false':"'".$action[$key[$i]]."'"))?>;
        <?}?>
    $(function(){
        var default_module = '<?php echo $default_module ?>';

        if (default_module != '') {
            link(default_module);
        }
    })
</script>
</div>