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
                        <!-- <div class="btn-group btn-hspace pull-right">
                            <button class="btn btn-default dropdown-toggle" data-toggle="dropdown" type="button" aria-expanded="false"><span id="currentDO"><?php echo lang('All SME') ?></span>&nbsp;<span class="caret"></span></button>
                            <ul class="dropdown-menu" role="menu" id="doList">
                            </ul>
                        </div> -->
                        <div class="btn-group btn-hspace pull-right">
                            <button class="btn btn-default dropdown-toggle" data-toggle="dropdown" type="button" aria-expanded="false"><span id="currentMill"><?php echo lang('All Mill') ?></span>&nbsp;<span class="caret"></span></button>
                            <ul class="dropdown-menu" role="menu" id="millList" style="max-height:300px;overflow:auto">
                            </ul>
                        </div>
                        <!-- <div class="btn-group btn-hspace pull-right">
                            <button class="btn btn-default dropdown-toggle" data-toggle="dropdown" type="button" aria-expanded="false"><span id="currentProvince"><?php echo lang('All Province') ?></span>&nbsp;<span class="caret"></span></button>
                            <ul class="dropdown-menu" role="menu" id="provinceList">
                            </ul>
                        </div>-->
                    </div>
                </div>
            </div>
        </div>
        <div class="main-content" >
            <!-- Bagian ini untuk counter -->
            <div class="row">            
                <div class="col-md-3">
                    <div class="widget widget-tile hvr-fade">
                        <div class="data-info col-md-8">
                            <div class="value" id="box1"></div>
                            <div class="desc">
                            <?php echo lang('Number of DO') ?>
                            </div>
                        </div>
                        <div class="icon col-md-4"><img src="<?php echo base_url()?>img/general/TRADER.PNG" alt=""></div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="widget widget-tile hvr-fade">
                        <div class="data-info col-md-8">
                            <div class="value" id="box2"></div>
                            <div class="desc">
                                <?php echo lang('Number of SME (Agent/Dealer)') ?>
                            </div>
                        </div>
                        <div class="icon col-md-4"><img src="<?php echo base_url()?>img/general/TRADER.PNG" alt=""></div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="widget widget-tile hvr-fade">
                        <div class="data-info col-md-8">
                            <div class="value" id="box3"></div>
                            <div class="desc">
                                <?php echo lang('Number of Batch') ?>
                            </div>
                        </div>
                        <div class="icon col-md-4"><img src="<?php echo base_url()?>img/general/TRADER.png" alt=""></div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="widget widget-tile hvr-fade">
                        <div class="data-info col-md-8">
                            <div class="value" id="box4"></div>
                            <div class="desc">
                                <?php echo lang('Number of Farmer') ?>
                            </div>
                        </div>
                        <div class="icon col-md-4"><img src="<?php echo base_url()?>img/general/petani2.png" alt=""></div>
                    </div>
                </div>
                <!-- End 4 counter pertama --> 
                <!-- Start 4 counter kedua -->
                <div class="col-md-3">
                    <div class="widget widget-tile hvr-fade">
                        <div class="data-info col-md-8">
                            <div class="value" id="box5"></div>
                            <div class="desc">
                            <?php echo lang('Number of Transactions') ?>
                            </div>
                        </div>
                        <div class="icon col-md-4"><img src="<?php echo base_url()?>img/general/TRADER.png" alt=""></div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="widget widget-tile hvr-fade">
                        <div class="data-info col-md-8">
                            <div class="value" id="box6"></div>
                            <div class="desc">
                                <?php echo lang('Number of Plot') ?>
                            </div>
                        </div>
                        <div class="icon col-md-4"><img src="<?php echo base_url()?>img/general/master-gnp-participant.png" alt=""></div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="widget widget-tile hvr-fade">
                        <div class="data-info col-md-8">
                            <div class="value" id="box7"></div>
                            <div class="desc">
                                <?php echo lang('Number of Production (MT)') ?>
                            </div>
                        </div>
                        <div class="icon col-md-4"><img src="<?php echo base_url()?>img/general/WAREHOUSE.PNG" alt=""></div>
                    </div>
                </div>
                <!-- End 4 counter kedua -->
            </div>
            <!-- Bagian ini untuk grafik -->           
            <div class="row">
                <div class="col-md-6 xs-mt-20">
                    <div class="box gradient">
                        <div class="content row-fluid" style="background-color:#FFFFFF">
                            <div id="potential_annual"></div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6 xs-mt-20">
                    <div class="box gradient">
                        <div class="content row-fluid" style="background-color:#FFFFFF">
                            <div id="traceable_volume"></div>
                        </div>
                    </div>
                </div>

                <div class="col-md-12 xs-mt-20">
                    <div class="box gradient">
                        <div class="content row-fluid" style="background-color:#FFFFFF">
                            <div id="jumlah_penjualan"></div>
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

            var sessionPrivateStaff = "<?php echo $_SESSION['role'] == 'Private'; ?>";
            var sessionMillStaff    = "<?php echo $_SESSION['role'] == 'Mill'; ?>";
            
            var checkin = $('#datepicker1').datepicker({
                format: 'yyyy-mm-dd'
            }).on('changeDate', function(ev) {
                if (ev.date.valueOf() > checkout.date.valueOf()) {
                    var newDate = new Date(ev.date)
                    newDate.setDate(newDate.getDate() + 1);
                    checkout.setValue(newDate);
                }
            }).data('datepicker');
            var checkout = $('#datepicker2').datepicker({
                format: 'yyyy-mm-dd',
                onRender: function(date) {
                    return date.valueOf() <= checkin.date.valueOf() ? 'disabled' : '';
                }
            }).on('changeDate', function(ev) {
            }).data('datepicker');
            
            $(function() {
                $.get(m_api+'/dashboard/region_session/', function(data) {
                    if (data) {
                        
                        //if login as private and mill stafff
                        if(sessionPrivateStaff != '1' && sessionMillStaff != '1'){
                            var li = '<li><a href="#" class="list_province" data-id="" data-name="'+lang('All Province')+'">'+lang('All Province')+'</a></li>';
                            $('#provinceList').append(li);

                            $.each(data, function(index, val) {
                                $.each(val, function(i, v) {
                                    var li = '<li><a href="#" class="list_province" data-id="'+v.id+'" data-name="'+v.name+'">'+v.name+'</a></li>';
                                    $('#provinceList').append(li);
                                });
                            });
                        }
                        
                        // set previously selected
                        if (localStorage.getItem("prov")) {
                            $('.list_province[data-id="'+localStorage.getItem("prov")+'"]').click();
                        }
                    }
                });

                $('#provinceList').on('click', '.list_province', function(event) {
                    event.preventDefault();
                    var start   = $('#datepicker1').val();
                    var end     = $('#datepicker2').val();
                    $('#currentProvince').text($(this).data('name'));
                    m_prov = $(this).data('id');
                    localStorage.setItem("prov", m_prov);

                    /// Mill ///
                    $('#millList li').remove();
                    var li1 = '<li><a href="#" class="list_mill" data-id="" data-name="'+lang('All Mill')+'">'+lang('All Mill')+'</a></li>';
                    $('#millList').append(li1);
                    $('#currentMill').text(lang('All Mill'));

                    /// DO ///
                    $('#doList li').remove();  
                    var li2 = '<li><a href="#" class="list_do" data-id="" data-name="'+lang('All SME')+'">'+lang('All SME')+'</a></li>';
                    $('#doList').append(li2);
                    $('#currentDO').text(lang('All SME'));

                    $.get(m_api+'/dashboard/mill_list/?search&province='+$(this).data('id')+'&start='+start+'&end='+end, function(data) {

                        if (data) {
                            $.each(data, function(index, val) {
                                var li = '<li><a href="#" class="list_mill" data-id="'+val.id+'" data-name="'+val.name+'">'+val.name+'</a></li>';
                                $('#millList').append(li);
                            });

                            if (m_mill) {
                                $('.list_mill[data-id="'+m_mill+'"]').click();
                            }
                        }
                    });
                });

                $.get(m_api+'/dashboard/mill_list/', function(data) {
                    if (data) {
                     
                        //if login as private or mill staff
                        if(sessionPrivateStaff != '1' || sessionMillStaff != '1'){
                            var li = '<li><a href="#" class="list_mill" data-id="" data-name="'+lang('All Mill')+'">'+lang('All Mill')+'</a></li>';
                            $('#millList').append(li);
                        }

                        $.each(data, function(index, val) {
                            var li = '<li><a href="#" class="list_mill" data-id="'+val.id+'" data-name="'+val.name+'">'+val.name+'</a></li>';
                            $('#millList').append(li);
                        });

                        // set previously selected
                        if (m_mill) {
                            $('.list_mill[data-id="'+m_mill+'"]').click();
                        }
                        if(sessionPrivateStaff != '1' || sessionMillStaff != '1'){
                            var li2 = '<li><a href="#" class="list_mill" data-id="other" data-name="'+lang('Other Mill')+'">'+lang('Other Mill')+'</a></li>';
                            $('#millList').append(li2);
                        }
                    }
                });

                $('#millList').on('click', '.list_mill', function(event) {
                    event.preventDefault();
                    var start   = $('#datepicker1').val();
                    var end     = $('#datepicker2').val();
                    $('#currentMill').text($(this).data('name'));
                    m_mill = $(this).data('id');

                    $('#doList li').remove();                    
                    var li = '<li><a href="#" class="list_do" data-id="" data-name="'+lang('All SME')+'">'+lang('All SME')+'</a></li>';
                    $('#doList').append(li);
                    $('#currentDO').text(lang('All SME'));
                    
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
                });
            });
            
            function setRange() {
                var awal    = $('#datepicker1').val();
                var akhir   = $('#datepicker2').val();
                if (awal!=='' && akhir!=='') {
                    url = "<?php echo site_url('home/dash_traceability')?>";
                    link(url+m_kab+'/'+m_kec+'/'+m_desa+'?search=&awal='+awal+'&akhir='+akhir+'&traceability_partner='+m_traceability_partner+'&mill='+m_mill+'&do='+m_do+'&agent='+m_agent+'&prov='+m_prov);
                }
            }
        </script>