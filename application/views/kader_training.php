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
        <div class="row-fluid">
            <div class="box gradient" style="margin:0 7px 25px 7px">
                    <div class="title">
                        <div class="row-fluid">
                            <div class="span4 right_offset" style="float: right; height: 45px; text-align: right;">
                                <h5 style="width:calc(100% - 32px);margin-top: 2px;">
                                    <span id="judul">
                                    </span>
                                </h5>
                                <div class="options_arrow pull-right" style="margin-top: -26px;">
                                    <div class="dropdown pull-right">
                                        <a class="dropdown-toggle " id="dLabel" role="button" data-toggle="dropdown" data-target="#" href="/page.html">
                                            <i class=" icon-caret-down"></i>
                                        </a>
                                        <?php echo $this->load->view('list_region', $action, TRUE); ?>  
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- End .row-fluid -->
                    </div>
                    <!-- End .title -->
            </div>
            <!-- End .box -->
        </div>
        <div class="row-fluid">
            <div class="content" style="height:165px;margin:0 auto;display:table;width:100%">
                <!--<ul class="row-fluid fluid general_statistics hidden-phone">-->
                <ul class="row-fluid fluid general_statistics">
                    <!--<li class="box gradient span3" style="display:inline;width:19%;margin-bottom:0">-->
                    <li class="gradient span3">
                     <a>
                        <div class="icon">
                            <img src="<?php echo base_url()?>img/general/GAP.png">
                            <img class="hover" src="<?php echo base_url()?>img/general/green/GAP.png">
                        </div>
                        <div class="heading" id="gap"><?php echo number_format($gap,0,'.',',') ?></div>
                        <div class="desc">
                            <?php echo lang('Peserta GAP') ?>
                        </div>
                    </a>
                </li>
                <!--<li class="box gradient span3" style="display:inline;width:19%;margin-bottom:0">-->
                <li class="gradient span3">
                    <a>
                        <div class="icon">
                            <img src="<?php echo base_url()?>img/general/GNP.png">
                            <img class="hover" src="<?php echo base_url()?>img/general/green/GNP.png">
                        </div>
                        <div class="heading" id="gnp"><?php echo number_format($gnp,0,'.',',') ?></div>
                        <div class="desc">
                            <?php echo lang('Peserta GNP') ?>
                        </div>
                    </a>
                </li>
                <!--<li class="box gradient span3" style="display:inline;width:19%;margin-bottom:0">-->
                <li class="gradient span3">
                    <a>
                        <div class="icon">
                            <img src="<?php echo base_url()?>img/general/GFP.png">
                            <img class="hover" src="<?php echo base_url()?>img/general/green/GFP.png">
                        </div>
                        <div class="heading" id="gfp"><?php echo number_format($gfp,0,'.',',') ?></div>
                        <div class="desc">
                            <?php echo lang('Peserta GFP') ?>
                        </div>
                    </a>
                </li>
            </ul>
        </div>          
        <div class="content" style="height:1330px">
            <div class="span6" style="margin:0">
                <div class="box gradient">
                    <div class="content row-fluid" style="background-color:#FFFFFF">
                        <div id="chart_gap"></div>
                    </div>
                    <!-- End .content -->
                </div>
                <!-- End .box -->
            </div>
            <!-- End .span6 -->
            <div class="span6" style="margin:0">
                <div class="box gradient">
                    <div class="content row-fluid" style="background-color:#FFFFFF">
                        <div id="tahun_gap"></div>
                    </div>
                    <!-- End .content -->
                </div>
                <!-- End .box -->
            </div>
            <!-- End .span6 -->
            <div class="span6" style="margin:0">
                <div class="box gradient">
                    <div class="content row-fluid" style="background-color:#FFFFFF">
                        <div id="chart_gnp"></div>
                    </div>
                    <!-- End .content -->
                </div>
                <!-- End .box -->
            </div>
            <!-- End .span6 -->
            <div class="span6" style="margin:0">
                <div class="box gradient">
                    <div class="content row-fluid" style="background-color:#FFFFFF">
                        <div id="tahun_gnp"></div>
                    </div>
                    <!-- End .content -->
                </div>
                <!-- End .box -->
            </div>
            <!-- End .span6 -->   
            <div class="span6" style="margin:0">
                <div class="box gradient">
                    <div class="content row-fluid" style="background-color:#FFFFFF">
                        <div id="chart_gfp"></div>
                    </div>
                    <!-- End .content -->
                </div>
                <!-- End .box -->
            </div>
            <!-- End .span6 -->
            <div class="span6" style="margin:0">
                <div class="box gradient">
                    <div class="content row-fluid" style="background-color:#FFFFFF">
                        <div id="tahun_gfp"></div>
                    </div>
                    <!-- End .content -->
                </div>
                <!-- End .box -->
            </div>
            <!-- End .span6 -->            
        </div>
        <!-- End .box -->
    </div>
    <!-- End .row-fluid -->
    <?}
    if ($style!='') {?>
    <style type="text/css">
        <?php echo $style?>
    </style>
    <?}?>
    <?php if ($js): ?>  
        <script type="text/javascript" src="<?php echo base_url()?>js/modules/<?php echo $js?>.js"></script>
    <?php endif ?>