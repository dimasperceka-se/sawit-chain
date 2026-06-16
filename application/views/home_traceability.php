<script language="javascript" type="text/javascript" src="<?php echo base_url()?>js/plugins/bootstrap-datepicker.js"></script>
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
                    <?php //echo $this->load->view('list_region_traceability', $action, TRUE); ?>
                    <div class="pull-right xs-mr-50">&nbsp;</div>
                    <div class="btn-group btn-hspace pull-right">
                        <input type="text" id="datepicker1" class="form-control"  style="display:inline-block; width: 105px; height: 38px;" value="<?php echo $tgl['awal'] ?>">
                        &nbsp;&nbsp;<?php echo lang('sampai') ?>&nbsp;&nbsp;
                        <input type="text" id="datepicker2" class="form-control"  style="display:inline-block; width: 105px; height: 38px;" value="<?php echo $tgl['akhir'] ?>">&nbsp;&nbsp;
                        <button class="btn btn-default pull-right" data-original-title=".btn .btn-info" data-placement="top" rel="tooltip" onClick="setRange()"><?php echo lang('Cari') ?></button>
                        &nbsp;&nbsp;
                        <div class="btn-group btn-hspace pull-right">
                            <button class="btn btn-default dropdown-toggle" data-toggle="dropdown" type="button" aria-expanded="false"><span id="currentAgent"><?php echo lang('All SME') ?></span>&nbsp;<span class="caret"></span></button>
                            <ul class="dropdown-menu" role="menu" id="agentList">
                            </ul>
                        </div>
                        <div class="btn-group btn-hspace pull-right">
                            <button class="btn btn-default dropdown-toggle" data-toggle="dropdown" type="button" aria-expanded="false"><span id="currentDO"><?php echo lang('All DO') ?></span>&nbsp;<span class="caret"></span></button>
                            <ul class="dropdown-menu" role="menu" id="doList">
                            </ul>
                        </div>
                        <div class="btn-group btn-hspace pull-right">
                            <button class="btn btn-default dropdown-toggle" data-toggle="dropdown" type="button" aria-expanded="false"><span id="currentMill"><?php echo lang('All Mill') ?></span>&nbsp;<span class="caret"></span></button>
                            <ul class="dropdown-menu" role="menu" id="millList">
                            </ul>
                        </div>
                        
                    </div>
                </div>
            </div>
        </div>
        <div class="main-content" >
            <div class="row">
                <!-- <div class="col-md-3">
                    <div class="widget widget-tile hvr-fade">
                        <div class="data-info col-md-8">
                            <div class="value" id="box1"></div>
                            <div class="desc">
                                <?php echo lang('Cocoa Production Groups (CPG)') ?>
                            </div>
                        </div>
                        <div class="icon col-md-4"><img src="<?php echo base_url()?>img/general/cpg2.png" alt=""></div>
                    </div>
                </div> -->
                <!-- <div class="col-md-3">
                    <div class="widget widget-tile hvr-fade">
                        <div class="data-info col-md-8">
                            <div class="value" id="box2"></div>
                            <div class="desc">
                                <?php echo lang('Cocoa Farmers') ?>
                            </div>
                        </div>
                        <div class="icon col-md-4"><img src="<?php echo base_url()?>img/general/petani2.png" alt=""></div>
                    </div>
                </div> -->
                <!-- <div class="col-md-3">
                    <div class="widget widget-tile hvr-fade">
                        <div class="data-info col-md-8">
                            <div class="value" id="box3"></div>
                            <div class="desc">
                                <?php echo lang('Land Area (Ha)') ?>
                            </div>
                        </div>
                        <div class="icon col-md-4"><img src="<?php echo base_url()?>img/general/lahan2.png" alt=""></div>
                    </div>
                </div> -->
                <!-- <div class="col-md-3">
                    <div class="widget widget-tile hvr-fade">
                        <div class="data-info col-md-8">
                            <div class="value" id="box4"></div>
                            <div class="desc">
                                <?php echo lang('Production (MT)') ?>
                            </div>
                        </div>
                        <div class="icon col-md-4"><img src="<?php echo base_url()?>img/general/produksi.png" alt=""></div>
                    </div>
                </div> -->
                <div class="col-md-3">
                    <div class="widget widget-tile hvr-fade">
                        <div class="data-info col-md-8">
                            <div class="value" id="box5"></div>
                            <div class="desc">
                                <?php echo lang('Traceable Sales (MT)') ?>
                            </div>
                        </div>
                        <div class="icon col-md-4"><img src="<?php echo base_url()?>img/general/traceability.png" alt=""></div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="widget widget-tile hvr-fade">
                        <div class="data-info col-md-8">
                            <div class="value" id="box22"></div>
                            <div class="desc">
                                <?php echo lang('Number of Transactions') ?>
                            </div>
                        </div>
                        <div class="icon col-md-4"><img src="<?php echo base_url()?>img/general/productivity.png" alt=""></div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="widget widget-tile hvr-fade">
                        <div class="data-info col-md-8">
                            <div class="value" id="box21"></div>
                            <div class="desc">
                                <?php echo lang('Number of Farmer Sales') ?>
                            </div>
                        </div>
                        <div class="icon col-md-4"><img src="<?php echo base_url()?>img/general/cpg2.png" alt=""></div>
                    </div>
                </div>
                <!--<div class="col-md-3">
                    <div class="widget widget-tile hvr-fade">
                        <div class="data-info col-md-8">
                            <div class="value" id="box_cert_sales"></div>
                            <div class="desc">
                                <?php echo lang('Certified Traceable Sales (MT)') ?>
                            </div>
                        </div>
                        <div class="icon col-md-4"><img src="<?php echo base_url()?>img/general/traceability.png" alt=""></div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="widget widget-tile hvr-fade">
                        <div class="data-info col-md-8">
                            <div class="value" id="box_cert_farmer_sales"></div>
                            <div class="desc">
                                <?php echo lang('Number of Certified Farmer Sales') ?>
                            </div>
                        </div>
                        <div class="icon col-md-4"><img src="<?php echo base_url()?>img/general/cpg2.png" alt=""></div>
                    </div>
                </div>-->
                
                <div class="col-md-3">
                    <div class="widget widget-tile hvr-fade">
                        <div class="data-info col-md-8">
                            <div class="value" id="box23"></div>
                            <div class="desc">
                                <?php echo lang('Number of SME Sales') ?>
                            </div>
                        </div>
                        <div class="icon col-md-4"><img src="<?php echo base_url()?>img/general/TRADER.PNG" alt=""></div>
                    </div>
                </div>
                <div class="col-md-3" style="display:none;">
                    <div class="widget widget-tile hvr-fade">
                        <div class="data-info col-md-8">
                            <div class="value" id="box24"></div>
                            <div class="desc">
                                <?php echo lang('Farmer Organizations') ?>
                            </div>
                        </div>
                        <div class="icon col-md-4"><img src="<?php echo base_url()?>img/general/SCE.PNG" alt=""></div>
                    </div>
                </div>
                <!--<div class="col-md-3">
                    <div class="widget widget-tile hvr-fade">
                        <div class="data-info col-md-8">
                            <div class="value" id="box25"></div>
                            <div class="desc">
                                <?php echo lang('Mill') ?>
                            </div>
                        </div>
                        <div class="icon col-md-4"><img src="<?php echo base_url()?>img/general/WAREHOUSE.PNG" alt=""></div>
                    </div>
                </div>-->
            </div>          
            <div class="row">
                <div class="col-md-6 xs-mt-20" style="display:none;">
                    <div class="box gradient">
                        <div class="content row-fluid" style="background-color:#FFFFFF">
                            <div id="pie1"></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 xs-mt-20"> <!-- 1 -->
                    <div class="box gradient">
                        <div class="content row-fluid" style="background-color:#FFFFFF">
                            <div id="pie2"></div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6 xs-mt-20"> <!-- 5 -->
                    <div class="box gradient">
                        <div class="content row-fluid" style="background-color:#FFFFFF">
                            <div id="chart_farmer"></div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 xs-mt-20" style="display:none;">
                    <div class="box gradient">
                        <div class="content row-fluid" style="background-color:#FFFFFF">
                            <div id="pie21"></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 xs-mt-20" style="display:none;">
                    <div class="box gradient">
                        <div class="content row-fluid" style="background-color:#FFFFFF">
                            <div id="pie22"></div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 xs-mt-20"> <!-- 2 -->
                    <div class="box gradient">
                        <div class="content row-fluid" style="background-color:#FFFFFF">
                            <div id="pie31"></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 xs-mt-20"> <!-- 3 -->
                    <div class="box gradient">
                        <div class="content row-fluid" style="background-color:#FFFFFF">
                            <div id="pie32"></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 xs-mt-20"> <!-- 4 -->
                    <div class="box gradient">
                        <div class="content row-fluid" style="background-color:#FFFFFF">
                            <div id="chart_sales"></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 xs-mt-20" style="display:none;">
                    <div class="box gradient">
                        <div class="content row-fluid" style="background-color:#FFFFFF">
                            <div id="chart_certified_farmer"></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 xs-mt-20" style="display:none;">
                    <div class="box gradient">
                        <div class="content row-fluid" style="background-color:#FFFFFF">
                            <div id="chart_certified_production"></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 xs-mt-20" style="display:none;">
                    <div class="box gradient">
                        <div class="content row-fluid" style="background-color:#FFFFFF">
                            <div id="chart_uncertified_farmer"></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 xs-mt-20" style="display:none;">
                    <div class="box gradient">
                        <div class="content row-fluid" style="background-color:#FFFFFF">
                            <div id="chart_uncertified_production"></div>
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
        <script type="text/javascript" src="<?php echo base_url()?>js/modules/<?php echo $js?>.js"></script>
        <script>
            var checkin = $('#datepicker1').datepicker({
                format: 'yyyy-mm-dd'
            }).on('changeDate', function(ev) {
                if (ev.date.valueOf() > checkout.date.valueOf()) {
                    var newDate = new Date(ev.date)
                    newDate.setDate(newDate.getDate() + 1);
                    checkout.setValue(newDate);
                }
                // checkin.hide();
                // $('#enddate')[0].focus();
            }).data('datepicker');
            var checkout = $('#datepicker2').datepicker({
                format: 'yyyy-mm-dd',
                onRender: function(date) {
                    return date.valueOf() <= checkin.date.valueOf() ? 'disabled' : '';
                }
            }).on('changeDate', function(ev) {
                // checkout.hide();
            }).data('datepicker');
            
            $(function() {
                $.get(m_api+'/dashboard/mill_list/', function(data) {
                    if (data) {
                        var li = '<li><a href="#" class="list_mill" data-id="" data-name="'+lang('All Mill')+'">'+lang('All Mill')+'</a></li>';
                            $('#millList').append(li);
                        $.each(data, function(index, val) {
                            var li = '<li><a href="#" class="list_mill" data-id="'+val.id+'" data-name="'+val.name+'">'+val.name+'</a></li>';
                            $('#millList').append(li);
                        });
                        // set previously selected
                        if (m_mill) {
                            $('.list_mill[data-id="'+m_mill+'"]').click();
                        }
                    }
                });
                
                $('#millList').on('click', '.list_mill', function(event) {
                    event.preventDefault();
                    var start   = $('#datepicker1').val();
                    var end     = $('#datepicker2').val();
                    $('#currentMill').text($(this).data('name'));
                    m_mill = $(this).data('id');
                    ///DO///
                    $('#doList li').remove();                    
                    var li = '<li><a href="#" class="list_do" data-id="" data-name="'+lang('All DO')+'">'+lang('All DO')+'</a></li>';
                    $('#doList').append(li);
                    $('#currentDO').text(lang('All DO'));
                    //m_do = '';
                    ///Agent///
                    $('#agentList li').remove();                    
                    var li = '<li><a href="#" class="list_agent" data-id="" data-name="'+lang('All SME')+'">'+lang('All SME')+'</a></li>';
                    $('#agentList').append(li);
                    $('#currentAgent').text(lang('All SME'));
                    //m_agent = '';
                    ///
                    $.get(m_api+'/dashboard/do_list/?search&mill='+$(this).data('id')+'&start='+start+'&end='+end, function(data) {
                        if (data) {
                            $.each(data, function(index, val) {
                                var li = '<li><a href="#" class="list_do" data-id="'+val.id+'" data-name="'+val.name+'">'+val.name+'</a></li>';
                                $('#doList').append(li);
                            });
                            if (m_do) {
                                $('.list_do[data-id="'+m_do+'"]').click();
                            }
                        }
                    });
                });
                
                $('#doList').on('click', '.list_do', function(event) {
                    event.preventDefault();
                    var start   = $('#datepicker1').val();
                    var end     = $('#datepicker2').val();
                    $('#currentDO').text($(this).data('name'));
                    m_do = $(this).data('id');
                    ///Agent///
                    $('#agentList li').remove();                    
                    var li = '<li><a href="#" class="list_agent" data-id="" data-name="'+lang('All SME')+'">'+lang('All SME')+'</a></li>';
                    $('#agentList').append(li);
                    $('#currentAgent').text(lang('All SME'));
                    //m_agent = '';
                    ///
                    $.get(m_api+'/dashboard/agent_list/?search&mill='+m_mill+'&do='+$(this).data('id')+'&start='+start+'&end='+end, function(data) {
                        if (data) {
                            $.each(data, function(index, val) {
                                var li = '<li><a href="#" class="list_agent" data-id="'+val.id+'" data-name="'+val.name+'">'+val.name+'</a></li>';
                                $('#agentList').append(li);
                            });
                            if (m_agent) {
                                $('.list_agent[data-id="'+m_agent+'"]').click();
                            }
                        }
                    });
                });
                
                $('#agentList').on('click', '.list_agent', function(event) {
                    event.preventDefault();
                    var start   = $('#datepicker1').val();
                    var end     = $('#datepicker2').val();
                    $('#currentAgent').text($(this).data('name'));
                    m_agent = $(this).data('id');
                });
            });
            
            function setRange() {
                var awal    = $('#datepicker1').val();
                var akhir   = $('#datepicker2').val();
                if (awal!=='' && akhir!=='') {
                    url = "<?php echo site_url('home/dash_traceability')?>";
                    link(url+m_kab+'/'+m_kec+'/'+m_desa+'?search=&awal='+awal+'&akhir='+akhir+'&traceability_partner='+m_traceability_partner+'&mill='+m_mill+'&do='+m_do+'&agent='+m_agent);
                }
            }
        </script>