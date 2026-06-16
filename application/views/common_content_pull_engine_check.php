<div class="main-content" >
   <?php if (!empty($js_file)): ?>
      <?php foreach ($js_file as $key => $js): ?>
         <script type="text/javascript" src="<?php echo $js?>"></script>      
      <?php endforeach ?>
   <?php endif ?>
<?php
if ($js!='') {?>
   <script>
      $('#page_title, #breadcrumb_title').text('<?php echo $titlet ?>');
      $('#first-breadcrumb').text('<?php echo $breadcrumb_1 ?>');
      $('#second-breadcrumb').text('<?php echo $breadcrumb_2 ?>');
      <?$key = array_keys($action);
      for ($i=0;$i<sizeof($action);$i++) {?>
      var m_<?php echo $key[$i]?> = <?php echo ($action[$key[$i]]===true?'true':($action[$key[$i]]===false?'false':"'".$action[$key[$i]]."'"))?>;
      <?}?>

   if(Ext.getCmp('win')) Ext.getCmp('win').destroy();
   if(Ext.getCmp('access_win')) Ext.getCmp('access_win').destroy();
   if(Ext.getCmp('winTraining')) Ext.getCmp('winTraining').destroy();
   if(Ext.getCmp('winCompostPenjualan')) Ext.getCmp('winCompostPenjualan').destroy();
   if(Ext.getCmp('winNurseyPenjualan')) Ext.getCmp('winNurseyPenjualan').destroy();
   if(Ext.getCmp('winNurseyTrader')) Ext.getCmp('winNurseyTrader').destroy();
   if(Ext.getCmp('print')) Ext.getCmp('print').destroy();

   if(Ext.getCmp('winHarvest')) Ext.getCmp('winHarvest').destroy();
   if(Ext.getCmp('winAntara')) Ext.getCmp('winAntara').destroy();
   if(Ext.getCmp('winGarden')) Ext.getCmp('winGarden').destroy();
   if(Ext.getCmp('winDetail')) Ext.getCmp('winDetail').destroy();
   if(Ext.getCmp('winSaving')) Ext.getCmp('winSaving').destroy();
   if(Ext.getCmp('winFarmer')) Ext.getCmp('winFarmer').destroy();
   if(Ext.getCmp('winPpi')) Ext.getCmp('winPpi').destroy();
   if(Ext.getCmp('winPpi2012')) Ext.getCmp('winPpi2012').destroy();
   if(Ext.getCmp('winNutrisi')) Ext.getCmp('winNutrisi').destroy();
   if(Ext.getCmp('winCompostPenjualan')) Ext.getCmp('winCompostPenjualan').destroy();
   if(Ext.getCmp('winNurseyPenjualan')) Ext.getCmp('winNurseyPenjualan').destroy();
   if(Ext.getCmp('winAff')) Ext.getCmp('winAff').destroy();
   if(Ext.getCmp('certwin')) Ext.getCmp('certwin').destroy();
   if(Ext.getCmp('duafgwin')) Ext.getCmp('duafgwin').destroy();
   if(Ext.getCmp('summary')) Ext.getCmp('summary').destroy();

   if(Ext.getCmp('winplay')) Ext.getCmp('winplay').destroy();

   if(Ext.getCmp('winDistrict')) Ext.getCmp('win').destroy();
   if(Ext.getCmp('winpar')) Ext.getCmp('winpar').destroy();



   </script>
   <script type="text/javascript" src="<?php echo base_url()?>js/modules/<?php echo $js?>.js"></script>

   <!-- js additional (begin) -->
   <?php
   if($js_additional != ""){
        $arrTmp = explode(',',$js_additional);
        foreach ($arrTmp as $key => $value) {
            echo '<script type="text/javascript" src="'.base_url().'js/modules/'.$value.'.js"></script>';
        }
   }
   ?>
   <!-- js additional (end) -->

   <div id="ext-content"></div>
   <div id="et-content" style="min-width: 310px; height: 400px; margin: 0 auto"></div>
<?}
if ($style!='') {?>
   <style type="text/css">
   <?php echo $style?>
   </style>
<?}?>
</div>