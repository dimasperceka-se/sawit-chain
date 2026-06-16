<?php if (!empty($filter_region)): ?>
   <div id="divCommonContentRegion" class="page-head xs-pt-10 xs-pb-10">
      <div class="row">
        <div class="col-md-9">&nbsp;</div>
        <div class="col-md-3" style="border:1px solid #F0F0F0; padding : 5px; border-radius: 22px;">
         <div class="col-md-12">          
            <div class="btn-group btn-hspace pull-right">
            <button class="btn btn-default dropdown-toggle" style="width:100px; border: 0px !important;" data-toggle="dropdown" type="button" aria-expanded="false"><span id="judul"><?php echo lang($current_region) ?></span>&nbsp;<span class="caret"></span></button>
               <ul class="dropdown-menu" role="menu" id="dLabeli" >
                  <?php if (!empty($action['ProvinceID'])): ?>
                     <li><a href="<?php echo current_url() ?>" onclick="link(this.href); return false"><?php echo lang('All Provinces') ?></a></li>
                     <li class="divider"></li>
                     <?php if (!empty($action['DistrictID'])): ?>
                        <li><a href="<?php echo current_url()."?prov={$action['ProvinceID']}" ?>" onclick="link(this.href); return false"><?php echo lang('All Districts in ').lang($action['Province']) ?></a></li>
                        <li class="divider"></li>
                        <?php if (!empty($action['SubDistrictID'])): ?>
                           <li><a href="<?php echo current_url()."?prov={$action['ProvinceID']}&dist={$action['DistrictID']}" ?>" onclick="link(this.href); return false"><?php echo lang('All Sub Districts in ').lang($action['District']) ?></a></li>
                           <li class="divider"></li>
                        <?php endif ?>
                     <?php endif ?>
                  <?php endif ?>
                  <?php foreach ($filter_region as $key => $value): ?>
                     <?php
                        if (empty($action['ProvinceID'])) {
                           $prov       = $value['id'];
                           $dist       = '';
                           $subdist    = '';
                        } else {
                           $prov = $action['ProvinceID'];
                           if (empty($action['DistrictID'])) {
                              $dist       = $value['id'];
                              $subdist    = '';
                           } else {
                              if ($mentokDistrict == true){
                                 $dist       = $value['id'];
                                 $subdist    = '';
                              } else {
                                 $dist       = $action['DistrictID'];
                                 $subdist    = $value['id'];
                              }
                           }
                        }
                     ?>
                     <li><a href="<?php echo current_url()."?prov={$prov}&dist={$dist}&subdist={$subdist}" ?>" onclick="link(this.href); return false"><?php echo lang($value['label']) ?></a></li>
                  <?php endforeach ?>
               </ul>
            </div>
            <span style="font-size: 18px;float: right;margin-right:15px; margin-top:5px;margin-left: 0px;"><?php echo lang('Filter Region');?>:</span>
         </div>
      </div>
      </div>
   </div>
<?php endif ?>
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
   <script src="<?php echo base_url() ?>js/screenfull.min.js"></script>
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