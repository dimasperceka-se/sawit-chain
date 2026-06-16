<?php
if ($js!='') {
   $ver = $this->config->item('extjs_version')?>
   <script>
      document.getElementById('titlet').innerHTML = '<?=$titlet?>';
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
   <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
   <script type="text/javascript" src="<?=base_url()?>js/chart/highcharts.js"></script>
   <script type="text/javascript" src="<?=base_url()?>js/chart/modules/exporting.js"></script>
   <script type="text/javascript" src="<?=base_url()?>js/chart/modules/no-data-to-display.js"></script>
   <div id="ext-content"></div>
      <div class="row-fluid">
        <div class="box gradient">
          <div class="title">
            <div class="row-fluid">
              <div class="span6" style="height:45px">
                <h4>
                <i class=" icon-bar-chart"></i><span id="judul"><!--($prov=='12'?'Sumatera Utara':-->
                <?=($prov==''?lang('All Province'):($prov=='11'?'Aceh':
                  ($prov=='76'?'West Sulawesi':($prov=='73'?'South Sulawesi':
                  ($prov=='72'?'Central Sulawesi':($prov=='74'?'Southeast Sulawesi':($prov=='13'?'West Sumatra':'')))))))?>
                  </span>
                </h4>
              </div>
              <!-- End .span6 -->
              <div class="span6 to_hide right_offset" style="height:45px">
                <div class="btn-toolbar">
                  <div class="options_arrow pull-right">
                    <div class="dropdown pull-right">
                      <a class="dropdown-toggle " id="dLabel" role="button" data-toggle="dropdown" data-target="#" href="/page.html">
                      <i class=" icon-caret-down"></i>
                      </a>
                      <ul class="dropdown-menu " role="menu" aria-labelledby="dLabel" id="dLabeli">
                        <?if ($prov!='') echo '<li><a href="'.base_url().index_page().'/home/home">'.$kab.lang('Semua').'</a></li>';
                        if ($prov=='' OR ($prov=='11' and $action['kab']!='')) 
                           echo '<li><a href="'.base_url().index_page().'/home/home/index/11">Aceh</a></li>';
                        if ($prov=='' OR ($prov=='76' and $action['kab']!='')) 
                           echo '<li><a href="'.base_url().index_page().'/home/home/index/76">Sulawesi Barat</a></li>';
                        /*if ($prov=='' OR ($prov=='' OR $prov=='12') 
                           echo '<li><a href="'.base_url().index_page().'/home/home/index/'.($prov!=''?'':'12').'">'.($prov!=''?'Semua':'Sulawesi Utara').'</a></li>';*/
                        if ($prov=='' OR ($prov=='73' and $action['kab']!='')) 
                           echo '<li><a href="'.base_url().index_page().'/home/home/index/73">Sulawesi Selatan</a></li>';
                        if ($prov=='' OR ($prov=='72' and $action['kab']!='')) 
                           echo '<li><a href="'.base_url().index_page().'/home/home/index/72">Sulawesi Tengah</a></li>';
                        if ($prov=='' OR ($prov=='74' and $action['kab']!='')) 
                           echo '<li><a href="'.base_url().index_page().'/home/home/index/74">Sulawesi Tenggara</a></li>';
                        if ($prov=='' OR ($prov=='13' and $action['kab']!='')) 
                           echo '<li><a href="'.base_url().index_page().'/home/home/index/13">Sumatra Barat</a></li>';?>
                      </ul>
                    </div>
                  </div>
                </div>
              </div>
              <!-- End .span6 -->
            </div>
            <!-- End .row-fluid -->
          </div>
          <!-- End .title -->
          <div class="content" style="height:2380px">
               <ul class="row-fluid fluid general_statistics hidden-phone">
                <li class="box gradient span3">
                <a>
                <div class="icon">
                  <img src="<?=base_url()?>img/general/cpg2.png">
                  <img class="hover" src="<?=base_url()?>img/general/green/cpg.png">
                </div>
                <div class="heading" id="cpg"></div>
                <div class="desc">
                   <?=lang('CPG')?>
                </div>
                </a>
                </li>
                <li class="box gradient span3">
                <a>
                <div class="icon">
                  <img src="<?=base_url()?>img/general/petani2.png">
                  <img class="hover" src="<?=base_url()?>img/general/green/petani.png">
                </div>
                <div class="heading" id="farmer"></div>
                <div class="desc">
                  <?=lang('Petani Kakao')?>
                </div>
                </a>
                </li>
                <li class="box gradient span3">
                <a>
                <div class="icon">
                  <img src="<?=base_url()?>img/general/pohon2.png">
                  <img class="hover" src="<?=base_url()?>img/general/green/pohon.png">
                </div>
                <div class="heading" id="pohon"></div>
                <div class="desc">
                  Pohon Kakao
                </div>
                </a>
                </li>
                <li class="box gradient span3">
                <a>
                <div class="icon">
                  <img src="<?=base_url()?>img/general/lahan2.png">
                  <img class="hover" src="<?=base_url()?>img/general/green/lahan.png">
                </div>
                <div class="heading" id="luas"></div>
                <div class="desc">
                  Luas Lahan (HA)
                </div>
                </a>
                </li>
                <li class="box gradient span3">
                <a>
                <div class="icon">
                  <img src="<?=base_url()?>img/general/produksi.png">
                  <img class="hover" src="<?=base_url()?>img/general/green/produksi.png">
                </div>
                <div class="heading" id="total"></div>
                <div class="desc">
                  Produksi (TON)
                </div>
                </a>
                </li>
                <li class="box gradient span3" style="width:16%">
                <a>
                <div class="icon">
                  <img src="<?=base_url()?>img/general/productivity.png">
                  <img class="hover" src="<?=base_url()?>img/general/green/productivity.png">
                </div>
                <div class="heading" id="productivity"></div>
                <div class="desc">
                  Produktivitas (Kg/Ha/Thn)
                </div>
                </a>
                </li>
                <li class="box gradient span3">
                <a>
                <div class="icon">
                  <img src="<?=base_url()?>img/general/nutrisi2.png">
                  <img class="hover" src="<?=base_url()?>img/general/green/nutrisi.png">
                </div>
                <div class="heading" id="training"></div>
                <div class="desc">
                  PESERTA GNP
                </div>
                </a>
                </li>
              </ul>
                
            <div class="span6" style="margin-left:0px">
                <div class="box gradient">
                  <div class="content row-fluid" style="background-color:#FFFFFF">
                  <div id="pie1"></div>
                  </div>
                  <!-- End .content -->
                </div>
                <!-- End .box -->
              </div>
              <!-- End .span6 -->
            <div class="span6" style="margin-left:25px">
                <div class="box gradient">
                  <div class="content row-fluid" style="background-color:#FFFFFF">
                  <div id="pie2"></div>
                  </div>
                  <!-- End .content -->
                </div>
                <!-- End .box -->
              </div>
              <!-- End .span6 -->
            <div class="span6" style="margin-left:0px;">
                <div class="box gradient">
                  <div class="content row-fluid" style="background-color:#FFFFFF">
                  <div id="pie3"></div>
                  </div>
                  <!-- End .content -->
                </div>
                <!-- End .box -->
              </div>
              <!-- End .span6 -->

            <div class="span6" style="margin-left:25px;">
                <div class="box gradient">
                  <div class="content row-fluid" style="background-color:#FFFFFF">
                  <div id="pie4"></div>
                  </div>
                  <!-- End .content -->
                </div>
                <!-- End .box -->
              </div>
              <!-- End .span6 -->
            <div class="span6" style="margin-left:0px;">
                <div class="box gradient">
                  <div class="content row-fluid" style="background-color:#FFFFFF">
                  <div id="pie5"></div>
                  </div>
                  <!-- End .content -->
                </div>
                <!-- End .box -->
              </div>
              <!-- End .span6 -->
            <div class="span6" style="margin-left:25px;">
                <div class="box gradient">
                  <div class="content row-fluid" style="background-color:#FFFFFF">
                  <div id="pie6"></div>
                  </div>
                  <!-- End .content -->
                </div>
                <!-- End .box -->
              </div>
              <!-- End .span6 -->
              
            <div class="span6" style="margin-left:0px;">
                <div class="box gradient">
                  <div class="content row-fluid" style="background-color:#FFFFFF">
                  <div id="pie7"></div>
                  </div>
                  <!-- End .content -->
                </div>
                <!-- End .box -->
              </div>
              <!-- End .span6 -->
            <div class="span6" style="margin-left:25px;">
                <div class="box gradient">
                  <div class="content row-fluid" style="background-color:#FFFFFF">
                  <div id="pie8"></div>
                  </div>
                  <!-- End .content -->
                </div>
                <!-- End .box -->
              </div>
              <!-- End .span6 -->

            <div class="span6" style="margin-left:0px;">
                <div class="box gradient">
                  <div class="content row-fluid" style="background-color:#FFFFFF">
                  <div id="pie9"></div>
                  </div>
                  <!-- End .content -->
                </div>
                <!-- End .box -->
              </div>
              <!-- End .span6 -->
            <div class="span6" style="margin-left:25px;">
                <div class="box gradient">
                  <div class="content row-fluid" style="background-color:#FFFFFF">
                  <div id="pie10"></div>
                  </div>
                  <!-- End .content -->
                </div>
                <!-- End .box -->
              </div>
              <!-- End .span6 -->
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
<?if ($daer!=''){?>
   <script>
   var jj = '<li><a href="<?=base_url().index_page()?>/home/home"><?=lang('Semua')?></a></li>';
   var judul = 'Semua';
   for (var i=0;i<s[7].length;i++) {
      jj += '<li><a href="<?=base_url().index_page()?>/home/home/index/a/'+s[7][i]['id']+'">'+
         s[7][i]['label']+'</a></li>';
      if (s[7][i]['id']=='<?=$private?>') judul = s[7][i]['label']
   }
   document.getElementById('dLabeli').innerHTML = jj;
   document.getElementById('judul').innerHTML = judul;
   </script>
<?}?>
