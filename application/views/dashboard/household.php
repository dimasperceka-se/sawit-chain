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
        
    //generate dashboard
    $("#btnRegenDashboard").click(function(e) {
        e.preventDefault();
        link(m_path+'?_addparam=&regen=go');
        return false;
    });
</script>
    <div id="ext-content"></div>
    <div id='row-fluid'>
        <div class="page-head xs-pt-10 xs-pb-10">
            <div class="row">
                <div class="col-md-9">
                    <button style="display:none;" id="btnRegenDashboard" type="button" class="btn btn-primary">Regenerate Dashboard</button>
                </div>
                <div class="col-md-3">
                    <?php echo $this->load->view('list_region', $action, true); ?>
                    <span style="margin:4px 12px 0px 0px;float:right;font-size:19px;"><?php echo lang('Filter')?> : </span>
                </div>
            </div>
        </div>

        <div class="main-content" >
            <div class="row">
                <div class="col-md-3">
                    <div class="widget widget-tile hvr-fade">
                        <div class="data-info col-md-8">
                            <div class="value" id="box_farmer"><?php echo number_format($gap,0,'.',',') ?></div>
                            <div class="desc">
                                <?php echo lang('Petani') ?>
                            </div>
                        </div>
                        <div class="icon col-md-4"><img src="<?php echo base_url()?>img/general/petani2.png"></div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="widget widget-tile hvr-fade">
                        <div class="data-info col-md-8">
                            <div class="value" id="box_family"><?php echo number_format($gnp,0,'.',',') ?></div>
                            <div class="desc">
                                <?php echo lang('Family Members') ?>
                            </div>
                        </div>
                        <div class="icon col-md-4"><img src="<?php echo base_url()?>img/general/cpg2.png"></div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="widget widget-tile hvr-fade">
                        <div class="data-info col-md-8">
                            <div class="value" id="box_female_family_members"></div>
                            <div class="desc">
                                <?php echo lang('Female Family Members (%)') ?>
                            </div>
                        </div>
                        <div class="icon col-md-4"><img src="<?php echo base_url()?>img/general/perempuan.png"></div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="widget widget-tile hvr-fade">
                        <div class="data-info col-md-8">
                            <div class="value" id="box_school"><?php echo number_format($gfp,0,'.',',') ?></div>
                            <div class="desc">
                                <?php echo lang('Family Members at School') ?>
                            </div>
                        </div>
                        <div class="icon col-md-4"><img src="<?php echo base_url()?>img/general/lulus_sd.png"></div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="widget widget-tile hvr-fade">
                        <div class="data-info col-md-8">
                            <div class="value" id="box_family_working"></div>
                            <div class="desc">
                                <?php echo lang('Family Members working on the Farm') ?>
                            </div>
                        </div>
                        <div class="icon col-md-4"><img src="<?php echo base_url()?>img/general/GAP.png"></div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="widget widget-tile hvr-fade">
                        <div class="data-info col-md-8">
                            <div class="value" id="box_working"></div>
                            <div class="desc">
                                <?php echo lang('Workers') ?>
                            </div>
                        </div>
                        <div class="icon col-md-4"><img src="<?php echo base_url()?>img/general/GAP.png"></div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="widget widget-tile hvr-fade">
                        <div class="data-info col-md-8">
                            <div class="value" id="box_female_worker"></div>
                            <div class="desc">
                                <?php echo lang('Female Workers (%)') ?>
                            </div>
                        </div>
                        <div class="icon col-md-4"><img src="<?php echo base_url()?>img/general/GAP.png"></div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="widget widget-tile hvr-fade">
                        <div class="data-info col-md-8">
                            <div class="value" id="box_worker_use_ppe"></div>
                            <div class="desc">
                                <?php echo lang('Workers using PPE (%)') ?>
                            </div>
                        </div>
                        <div class="icon col-md-4"><img src="<?php echo base_url()?>img/general/GAP.png"></div>
                    </div>
                </div>

            </div>
            
            <div class="row">
                <div class="col-md-6 xs-mt-20">
                    <div class="box gradient">
                        <div class="content row-fluid" style="border:1px solid lightgray;">
                            <div id="chart_farmer"></div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 xs-mt-20">
                    <div class="box gradient">
                        <div class="content row-fluid" style="border:1px solid lightgray;">
                            <div id="chart_child"></div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 xs-mt-20">
                    <div class="box gradient">
                        <div class="content row-fluid" style="border:1px solid lightgray;">
                            <div id="chart_school"></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 xs-mt-20">
                    <div class="box gradient">
                        <div class="content row-fluid" style="border:1px solid lightgray;">
                            <div id="chart_family_working"></div>
                        </div>
                    </div>
                </div>   

                <div class="col-md-6 xs-mt-20">
                    <div class="box gradient">
                        <div class="content row-fluid" style="border:1px solid lightgray;">
                            <div id="pie_family_activity_type"></div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 xs-mt-20">
                    <div class="box gradient">
                        <div class="content row-fluid" style="border:1px solid lightgray;">
                            <div id="chart_working"></div>
                        </div>
                    </div>
                </div>   

                <div class="col-md-6 xs-mt-20">
                    <div class="box gradient">
                        <div class="content row-fluid" style="border:1px solid lightgray;">
                            <div id="pie_working_hour"></div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 xs-mt-20">
                    <div class="box gradient">
                        <div class="content row-fluid" style="border:1px solid lightgray;">
                            <div id="pie_worker_activity_type"></div>
                        </div>
                    </div>
                </div>

            </div>    
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