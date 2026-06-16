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
                            <ul class="dropdown-menu" role="menu" id="millList" style="max-height: 300px;overflow: auto;">
                            </ul>
                        </div>
                        <div class="btn-group btn-hspace pull-right">
                            <button class="btn btn-default dropdown-toggle" data-toggle="dropdown" type="button" aria-expanded="false"><span id="currentType"><?php echo lang('All Type') ?></span>&nbsp;<span class="caret"></span></button>
                            <ul class="dropdown-menu" role="menu" id="typeList">
                                <li><a href="#" class="list_type" data-id="" data-name="<?=lang('All Type')?>"><?=lang('All Type')?></a></li>
                                <li><a href="#" class="list_type" data-id="farmer" data-name="<?=lang('Farmer')?>"><?=lang('Farmer')?></a></li>
                                <li><a href="#" class="list_type" data-id="garden" data-name="<?=lang('Garden')?>"><?=lang('Garden')?></a></li>
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
            <!-- Bagian ini untuk grafik -->           
            <div class="row">
                <div class="col-md-12 xs-mt-20">
                    <div class="box gradient">
                        <div class="content row-fluid" style="background-color:#FFFFFF;">
                            <div id="achievment_fa" style="min-height:800px"></div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End .box -->
        </div>
        <!-- End .row-fluid -->
        <?php }
        if ($style!='') {?>
        <style type="text/css">
            <?php echo $style?>
        </style>
        <?php }?>
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

                $.get(m_api+'/dashboard/mill_list_fa/', function(data) {
                    if (data) {
                     
                        //if login as private or mill staff
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
                        
                        if (m_type) {
                            $('.list_type[data-id="'+m_type+'"]').click();
                        }
                    }
                });

                $('#millList').on('click', '.list_mill', function(event) {
                    event.preventDefault();
                    var start   = $('#datepicker1').val();
                    var end     = $('#datepicker2').val();
                    $('#currentMill').text($(this).data('name'));
                    m_mill = $(this).data('id');
                });

                $('#typeList').on('click', '.list_type', function(event) {
                    console.log($(this).data('name'));
                    event.preventDefault();
                    var start   = $('#datepicker1').val();
                    var end     = $('#datepicker2').val();
                    $('#currentType').text($(this).data('name'));
                    m_type = $(this).data('id');
                });
            });
            
            function setRange() {
                var awal    = $('#datepicker1').val();
                var akhir   = $('#datepicker2').val();
                if (awal!=='' && akhir!=='') {
                    url = "<?php echo site_url('progre_dboard/dashboard_fa')?>";
                    link(url+'?search=&awal='+awal+'&akhir='+akhir+'&mill='+m_mill+'&type='+m_type);
                }
            }
        </script>