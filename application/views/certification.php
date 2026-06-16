    <script language="javascript" type="text/javascript" src="<?=base_url()?>assets/plugins/datepicker/js/bootstrap-datepicker.js"></script>
<?php
if ($js!='') {?>
<script>
    $('#page_title, #breadcrumb_title').text('<?php echo $titlet ?>');
$('#first-breadcrumb').text('<?php echo $breadcrumb_1 ?>');
$('#second-breadcrumb').text('<?php echo $breadcrumb_2 ?>');
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
                    <?php echo $this->load->view('list_region', $action, TRUE); ?>
                    <div class="pull-right xs-mr-50">&nbsp;</div>
                    <div class="btn-group btn-hspace pull-right">
                        <input type="text" id="startdate" class="form-control"  style="display:inline-block; width: 105px; height: 38px;" value="<?php echo !empty($startdate)?$startdate:date('Y-01-01') ?>">
                        &nbsp;&nbsp;<?php echo lang('sampai') ?>&nbsp;&nbsp;
                        <input type="text" id="enddate" class="form-control"  style="display:inline-block; width: 105px; height: 38px;" value="<?php echo !empty($enddate)?$enddate:date('Y-m-d') ?>">&nbsp;&nbsp;
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
                                <?=lang('Petani Tersertifikasi')?>
                            </div>
                        </div>
                        <div class="icon col-md-4"><img src="<?=base_url()?>img/general/certified_farmer.png"></div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="widget widget-tile hvr-fade">
                        <div class="data-info col-md-8">
                            <div class="value" id="box2"></div>
                            <div class="desc">
                                <?=lang('Petani Perempuan Tersertifikasi')?> (%)
                            </div>
                        </div>
                        <div class="icon col-md-4"><img src="<?=base_url()?>img/general/certified_female_farmer.png"></div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="widget widget-tile hvr-fade">
                        <div class="data-info col-md-8">
                            <div class="value" id="box11"></div>
                            <div class="desc">
                                <?=lang('Jumlah Kebun Kakao Tersertifikasi')?>
                            </div>
                        </div>
                        <div class="icon col-md-4"><img src="<?=base_url()?>img/general/certified_garden.png"></div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="widget widget-tile hvr-fade">
                        <div class="data-info col-md-8">
                            <div class="value" id="box3"></div>
                            <div class="desc">
                                <?php echo lang('Luas Lahan Tersertifikasi (Ha)') ?>
                            </div>
                        </div>
                        <div class="icon col-md-4"><img src="<?=base_url()?>img/general/certified_area.png"></div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="widget widget-tile hvr-fade">
                        <div class="data-info col-md-8">
                            <div class="value" id="box4"></div>
                            <div class="desc">
                                <?php echo lang('Produktivitas (KG/HA/THN)') ?>
                            </div>
                        </div>
                        <div class="icon col-md-4"><img src="<?=base_url()?>img/general/certified_productivity.png"></div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="widget widget-tile hvr-fade">
                        <div class="data-info col-md-8">
                            <div class="value" id="box41"></div>
                            <div class="desc">
                                <?php echo lang('Produksi Tersertifikasi (TON)') ?>
                            </div>
                        </div>
                        <div class="icon col-md-4"><img src="<?=base_url()?>img/general/sertifikasi.png"></div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="widget widget-tile hvr-fade">
                        <div class="data-info col-md-8">
                            <div class="value" id="box_avg_farm_size"></div>
                            <div class="desc">
                                <?php echo lang('Rata-Rata Ukuran Kebun Tersertifikasi per Petani (HA)') ?>
                            </div>
                        </div>
                        <div class="icon col-md-4"><img src="<?=base_url()?>img/general/certified_productivity.png"></div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="widget widget-tile hvr-fade">
                        <div class="data-info col-md-8">
                            <div class="value" id="box_avg_cocoa_tree"></div>
                            <div class="desc">
                                <?php echo lang('Rata - Rata Pohon Kakao per Hektar Tersertifikasi') ?>
                            </div>
                        </div>
                        <div class="icon col-md-4"><img src="<?=base_url()?>img/general/sertifikasi.png"></div>
                    </div>
                </div>
            </div>          
            <div class="row">
                <div class="col-md-6 xs-mt-20">
                    <div class="box gradient">
                        <div class="content row-fluid" style="background-color:#FFFFFF">
                            <div id="pie1"></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 xs-mt-20">
                    <div class="box gradient">
                        <div class="content row-fluid" style="background-color:#FFFFFF">
                            <div id="pie2"></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 xs-mt-20">
                    <div class="box gradient">
                        <div class="content row-fluid" style="background-color:#FFFFFF">
                            <div id="pie11"></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 xs-mt-20">
                    <div class="box gradient">
                        <div class="content row-fluid" style="background-color:#FFFFFF">
                            <div id="pie3"></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 xs-mt-20">
                    <div class="box gradient">
                        <div class="content row-fluid" style="background-color:#FFFFFF">
                            <div id="pie4"></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 xs-mt-20">
                    <div class="box gradient">
                        <div class="content row-fluid" style="background-color:#FFFFFF">
                            <div id="pie41"></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 xs-mt-20">
                    <div class="box gradient">
                        <div class="content row-fluid" style="background-color:#FFFFFF">
                            <div id="pie5"></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 xs-mt-20">
                    <div class="box gradient">
                        <div class="content row-fluid" style="background-color:#FFFFFF">
                            <div id="pie6"></div>
                        </div>
                    </div>
                </div>
            </div>
                <!-- End .box -->
        </div>
    </div>
            <!-- End .row-fluid -->
        <?}
        if ($style!='') {?>
        <style type="text/css">
            <?=$style?>
        </style>
        <?}?>
        <script type="text/javascript" src="<?=base_url()?>js/modules/<?=$js?>.js"></script>
        <script type="text/javascript">
            // $('#datepicker1').datepicker({
            //     format: 'yyyy ',
            //     viewMode: 2,
            //     minViewMode: 2
            // });  
            var checkin = $('#startdate').datepicker({
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
            var checkout = $('#enddate').datepicker({
                format: 'yyyy-mm-dd',
                onRender: function(date) {
                    return date.valueOf() <= checkin.date.valueOf() ? 'disabled' : '';
                }
            }).on('changeDate', function(ev) {
                // checkout.hide();
            }).data('datepicker');
            function setRange() {
                var startdate = $('#startdate').val();
                var enddate = $('#enddate').val();
                link('<?=site_url('home/home/certification')?>/<?=$url_param?>?startdate='+startdate+'&enddate='+enddate);
            }       
        </script>