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
    </script>
    <div id="ext-content"></div>
    <div id='row-fluid' style="display:none">
        <div class="page-head xs-pt-10 xs-pb-10">
            <div class="row">
                <div class="col-md-2"><h2><?php echo lang('Filter') ?></h2></div>
                <div class="col-md-10">
                    <?php echo $this->load->view('list_region_cargill', $action, TRUE); ?>
                </div>
            </div>
        </div>
        <div class="main-content" >
            <div class="row">
                <div class="col-md-3">
                    <div id="accordion1" class="panel-group accordion">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <div class="panel-title">
                                    <a data-toggle="collapse" data-parent="#accordion1" href="#collapseOne" style="display: inline-block;">
                                        <i class="icon s7-angle-down"></i>
                                        <div class="value" id="farmer"></div>
                                        <?php echo lang('Petani') ?>
                                    </a>
                                    
                                    <img src="<?php echo base_url()?>img/general/petani2.png" style="    
float: right;
padding: 17px 10px;
width: 25%;"/>
                                </div>
                            </div>
                            <div id="collapseOne" class="panel-collapse collapse">
                                <div class="panel-body">
                                    
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                    <div class="col-md-3">
                        <div class="widget widget-tile hvr-fade">
                            <div class="data-info col-md-8">
                                <!-- <div class="value" id="farmer"></div> -->
                                <div class="desc">
                                    <?php echo lang('Petani') ?>
                                </div>
                            </div>
                            <div class="icon col-md-4"><img src="<?php echo base_url()?>img/general/petani2.png"/></div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="widget widget-tile hvr-fade">
                            <div class="data-info col-md-8">
                                <div class="value" id="production"></div>
                                <div class="desc">
                                    <?php echo lang('Produksi (TON)') ?>
                                </div>
                            </div>
                            <div class="icon col-md-4"><img src="<?php echo base_url()?>img/general/produksi.png"></div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="widget widget-tile hvr-fade">
                            <div class="data-info col-md-8">
                                <div class="value" id="area"></div>
                                <div class="desc">
                                    <?php echo lang('Luas Lahan (HA)') ?>
                                </div>
                            </div>
                            <div class="icon col-md-4"><img src="<?php echo base_url()?>img/general/land_area.png"></div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="widget widget-tile hvr-fade">
                            <div class="data-info col-md-8">
                                <div class="value" id="productivity"></div>
                                <div class="desc">
                                    <?php echo lang('Produktivitas (Kg/Ha/Thn)') ?>
                                </div>
                            </div>
                            <div class="icon col-md-4"><img src="<?php echo base_url()?>img/general/productivity.png"></div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="widget widget-tile hvr-fade">
                            <div class="data-info col-md-8">
                                <div class="value" id="nursery"></div>
                                <div class="desc">
                                    <?php echo lang('Nursery') ?>
                                </div>
                            </div>
                            <div class="icon col-md-4"><img src="<?php echo base_url()?>img/general/nutrisi2.png"></div>
                        </div>
                        </div>
                </div>
            </div>
        </div>
        <?}
        if ($style!='') {?>
        <style type="text/css">
            <?php echo $style?>
        </style>
        <?}?>
        <script type="text/javascript" src="<?php echo base_url()?>js/modules/<?php echo $js?>.js"></script>

