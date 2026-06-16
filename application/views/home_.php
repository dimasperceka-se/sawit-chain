<script language="javascript" type="text/javascript" src="<?=base_url()?>js/plugins/bootstrap-datepicker.js"></script>
<?php
if ($js!='') {?>
   <script>
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
   </script>
   <div id="ext-content"></div>
      <div id='row-fluid' style="display:none">
        <div class="page-head xs-pt-10 xs-pb-10">
            <div class="row">
                <div class="col-md-2"><h2><?php echo lang('Filter') ?></h2></div>
                <div class="col-md-10">
                    <div class="btn-group btn-hspace pull-right">
                        <input type="text" id="datepicker1" class="form-control"  style="display:inline-block; width: 105px; height: 38px;" value="<?php echo $tgl['awal'] ?>">
                        &nbsp;&nbsp;<?php echo lang('sampai') ?>&nbsp;&nbsp;
                        <input type="text" id="datepicker2" class="form-control"  style="display:inline-block; width: 105px; height: 38px;" value="<?php echo $tgl['akhir'] ?>">&nbsp;&nbsp;
                        <button class="btn btn-default pull-right" data-original-title=".btn .btn-info" data-placement="top" rel="tooltip" onClick="setRange()"><?php echo lang('Cari') ?></button>
                    </div>
                </div>
            </div>
        </div>

        <div class="main-content" >
            <div class="row">
                <div class="col-md-3">
                    <div class="widget widget-tile hvr-fade">
                        <div class="data-info col-md-8">
                            <div class="value" id="box1"></div>
                            <div class="desc">
                                <?php echo lang('Pembelian Dari Petani') ?>
                            </div>
                        </div>
                        <div class="icon col-md-4"><img src="<?=base_url()?>img/general/traceability.png"></div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="widget widget-tile hvr-fade">
                        <div class="data-info col-md-8">
                            <div class="value" id="box2"></div>
                            <div class="desc">
                                <?php echo lang('Penjualan Ke Warehouse') ?>
                            </div>
                        </div>
                        <div class="icon col-md-4"><img src="<?=base_url()?>img/general/traceability.png"></div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="widget widget-tile hvr-fade">
                        <div class="data-info col-md-8">
                            <div class="value" id="box3"></div>
                            <div class="desc">
                                <?php echo lang('Number of Farmer Sales') ?>
                            </div>
                        </div>
                        <div class="icon col-md-4"><img src="<?=base_url()?>img/general/cpg2.png"></div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="widget widget-tile hvr-fade">
                        <div class="data-info col-md-8">
                            <div class="value" id="box4"></div>
                            <div class="desc">
                                <?php echo lang('Number of Transaction') ?>
                            </div>
                        </div>
                        <div class="icon col-md-4"><img src="<?=base_url()?>img/general/cpg2.png"></div>
                    </div>
                </div>
            </div>          
            <div class="row">
              <div class="col-md-6 xs-mt-20">
                 <div class="box gradient">
                   <div class="content row-fluid" style="background-color:#FFFFFF">
                     <div id="pie1"></div>
                   </div>
                   <!-- End .content -->
                 </div>
                 <!-- End .box -->
              </div>
              <div class="col-md-6 xs-mt-20">
                 <div class="box gradient">
                   <div class="content row-fluid" style="background-color:#FFFFFF">
                     <div id="pie2"></div>
                   </div>
                   <!-- End .content -->
                 </div>
                 <!-- End .box -->
              </div>
          </div>
        </div>
        <!-- End .box -->
      </div>
      <!-- End .row-fluid -->
<?}
if ($style!='') {?>
<style type="text/css">
<?=$style?>
</style>
<?}?>
<script type="text/javascript" src="<?=base_url()?>js/modules/<?=$js?>.js"></script>
<script>
$('#datepicker1').datepicker({
 format: 'yyyy-mm-dd'
});
$('#datepicker2').datepicker({
 format: 'yyyy-mm-dd'
});
function setRange_() {
   var awal = $('#datepicker1').val();
   var akhir = $('#datepicker2').val();
   if (awal!='' && akhir!='') {
      link('<?=site_url('home/home/main')?>/<?=$url_param?>?petani=<?=$petani?>&awal='+awal+'&akhir='+akhir);
   }
}
</script>
