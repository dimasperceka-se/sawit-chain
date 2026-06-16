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
    <div id='row-fluid' style="display:none">
        <div class="page-head xs-pt-10 xs-pb-10">
            <div class="row">
                <div class="col-md-9">
                    <button id="btnRegenDashboard" type="button" class="btn btn-primary">Regenerate Dashboard</button>
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
                            <div class="value" id="box_gap"><?php echo number_format($gap,0,'.',',') ?></div>
                            <div class="desc">
                                <?php echo lang('Registered GAP Participants') ?>
                            </div>
                        </div>
                        <div class="icon col-md-4"><img src="<?php echo base_url()?>img/general/GAP.png"></div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="widget widget-tile hvr-fade">
                        <div class="data-info col-md-8">
                            <div class="value" id="box_gap_female"><?php echo number_format($gap,0,'.',',') ?></div>
                            <div class="desc">
                                <?php echo lang('Female GAP Participants') ?>
                            </div>
                        </div>
                        <div class="icon col-md-4"><img src="<?php echo base_url()?>img/general/perempuan.png"></div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="widget widget-tile hvr-fade">
                        <div class="data-info col-md-8">
                            <div class="value" id="box_attended"><?php echo number_format($gap,0,'.',',') ?></div>
                            <div class="desc">
                                <?php echo lang('Total Attended GAP Participant Training Days') ?>
                            </div>
                        </div>
                        <div class="icon col-md-4"><img src="<?php echo base_url()?>img/general/GAP.png"></div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="widget widget-tile hvr-fade">
                        <div class="data-info col-md-8">
                            <div class="value" id="box_attendance_percent"><?php echo number_format($gap,0,'.',',') ?></div>
                            <div class="desc">
                                <?php echo lang('Participant Attendance in GAP Trainings (%)') ?>
                            </div>
                        </div>
                        <div class="icon col-md-4"><img src="<?php echo base_url()?>img/general/GAP.png"></div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="widget widget-tile hvr-fade">
                        <div class="data-info col-md-8">
                            <div class="value" id="box_mt_70"><?php echo number_format($gap,0,'.',',') ?></div>
                            <div class="desc">
                                <?php echo lang('Participant Attending at least 70% of GAP Training Sessions') ?>
                            </div>
                        </div>
                        <div class="icon col-md-4"><img src="<?php echo base_url()?>img/general/GAP.png"></div>
                    </div>
                </div>
                <!-- <div class="col-md-3">
                    <div class="widget widget-tile hvr-fade">
                        <div class="data-info col-md-8">
                            <div class="value" id="box_gnp"><?php echo number_format($gnp,0,'.',',') ?></div>
                            <div class="desc">
                                <?php echo lang('Peserta GNP') ?>
                            </div>
                        </div>
                        <div class="icon col-md-4"><img src="<?php echo base_url()?>img/general/GNP.png"></div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="widget widget-tile hvr-fade">
                        <div class="data-info col-md-8">
                            <div class="value" id="box_gfp"><?php echo number_format($gfp,0,'.',',') ?></div>
                            <div class="desc">
                                <?php echo lang('Peserta GFP') ?>
                            </div>
                        </div>
                        <div class="icon col-md-4"><img src="<?php echo base_url()?>img/general/GFP.png"></div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="widget widget-tile hvr-fade">
                        <div class="data-info col-md-8">
                            <div class="value" id="box_gdp"></div>
                            <div class="desc">
                                <?php echo lang('Peserta GDP') ?>
                            </div>
                        </div>
                        <div class="icon col-md-4"><img src="<?php echo base_url()?>img/general/GSP.png"></div>
                    </div>
                </div> -->
            </div>     
        </div>     
        <div class="row">
            <div class="col-md-6 xs-mt-20">
                <div class="box gradient">
                    <div class="content row-fluid" style="background-color:#FFFFFF">
                        <div id="chart_gap"></div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 xs-mt-20">
                <div class="box gradient">
                    <div class="content row-fluid" style="background-color:#FFFFFF">
                        <div id="chart_gap_tahun"></div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 xs-mt-20">
                <div class="box gradient">
                    <div class="content row-fluid" style="background-color:#FFFFFF">
                        <div id="chart_attendance_session"></div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 xs-mt-20">
                <div class="box gradient">
                    <div class="content row-fluid" style="background-color:#FFFFFF">
                        <div id="chart_attendance"></div>
                    </div>
                </div>
            </div>
            <!-- <div class="col-md-6 xs-mt-20">
                <div class="box gradient">
                    <div class="content row-fluid" style="background-color:#FFFFFF">
                        <div id="chart_gnp"></div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 xs-mt-20">
                <div class="box gradient">
                    <div class="content row-fluid" style="background-color:#FFFFFF">
                        <div id="chart_gnp_tahun"></div>
                    </div>
                </div>
            </div>   
            <div class="col-md-6 xs-mt-20">
                <div class="box gradient">
                    <div class="content row-fluid" style="background-color:#FFFFFF">
                        <div id="chart_gfp"></div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 xs-mt-20">
                <div class="box gradient">
                    <div class="content row-fluid" style="background-color:#FFFFFF">
                        <div id="chart_gfp_tahun"></div>
                    </div>
                </div>
            </div> 
            <div class="col-md-6 xs-mt-20">
                <div class="box gradient">
                    <div class="content row-fluid" style="background-color:#FFFFFF">
                        <div id="chart_gdp"></div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 xs-mt-20">
                <div class="box gradient">
                    <div class="content row-fluid" style="background-color:#FFFFFF">
                        <div id="chart_gdp_tahun"></div>
                    </div>
                </div>
            </div> -->            
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