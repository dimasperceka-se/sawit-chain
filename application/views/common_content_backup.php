<?$ver = 'ext-4.2.0.663'?>
<link rel="stylesheet" type="text/css" href="<?=base_url()?>js/<?=$ver?>/examples/shared/example.css" />
<link rel="stylesheet" type="text/css" href="<?=base_url()?>js/<?=$ver?>/examples/writer/writer.css" />

<!-- GC -->

<script type="text/javascript" src="<?=base_url()?>js/<?=$ver?>/examples/shared/include-ext.js"></script>
<script type="text/javascript" src="<?=base_url()?>js/<?=$ver?>/examples/shared/options-toolbar.js"></script>
<script type="text/javascript" src="<?=base_url()?>js/<?=$ver?>/examples/shared/examples.js"></script>
<script>
   <?$key = array_keys($action);
   for ($i=0;$i<sizeof($action);$i++) {?>
   var m_<?=$key[$i]?> = '<?=$action[$key[$i]]?>';
   <?}?>
</script>
<script type="text/javascript" src="<?=base_url().'js/modules/'.$js?>"></script>
