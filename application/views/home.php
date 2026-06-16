  <?php
  if ($js!='') {?>
  <script>
    $('#page_title, #breadcrumb_title').text('<?php echo $titlet ?>');
$('#first-breadcrumb').text('<?php echo $breadcrumb_1 ?>');
$('#second-breadcrumb').text('<?php echo $breadcrumb_2 ?>');
    var varjs =
    {
        "config": {
            "base_url": "<?php echo base_url()?>/",
            "default_currency": "IDR",
            "extjs_version": "<?php echo $ver?>"
        }
    }
    ;
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
                    <?php echo $this->load->view('list_region', $action, TRUE); ?>
                </div>
            </div>
        </div>
        <div class="main-content" >
            <div class="row">
                    <div class="col-md-3">
                        <div class="widget widget-tile hvr-fade">
                            <div class="data-info col-md-8">
                                <div class="value" id="cpg"></div>
                                <div class="desc">
                                    <?php echo lang('Kelompok Produksi Kakao (CPG)') ?>
                                </div>
                            </div>
                            <div class="icon col-md-4"><img src="<?php echo base_url()?>img/general/cpg2.png"></div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="widget widget-tile hvr-fade">
                            <div class="data-info col-md-8">
                                <div class="value" id="farmer"></div>
                                <div class="desc">
                                    <?php echo lang('Petani Kakao') ?>
                                </div>
                            </div>
                            <div class="icon col-md-4"><img src="<?php echo base_url()?>img/general/petani2.png"/></div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="widget widget-tile hvr-fade">
                            <div class="data-info col-md-8">
                                <div class="value" id="usia_petani"></div>
                                <div class="desc">
                                    <?php echo lang('Rata-Rata Usia Petani (Tahun)') ?>
                                </div>
                            </div>
                            <div class="icon col-md-4"><img src="<?php echo base_url()?>img/general/usia.png"></div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="widget widget-tile hvr-fade">
                            <div class="data-info col-md-8">
                                <div class="value" id="petani_perempuan"></div>
                                <div class="desc">
                                    <?php echo lang('PETANI PEREMPUAN (%)') ?>
                                </div>
                            </div>
                            <div class="icon col-md-4"><img src="<?php echo base_url()?>img/general/perempuan.png"></div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="widget widget-tile hvr-fade">
                            <div class="data-info col-md-8">
                                <div class="value" id="jumlah_kebun"></div>
                                <div class="desc">
                                    <?php echo lang('Jumlah Kebun Kakao') ?>
                                </div>
                            </div>
                            <div class="icon col-md-4"><img src="<?php echo base_url()?>img/general/garden.png"></div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="widget widget-tile hvr-fade">
                            <div class="data-info col-md-8">
                                <div class="value" id="ukuran_kebun"></div>
                                <div class="desc">
                                    <?php echo lang('Rata - Rata Ukuran Kebun (Ha)') ?>
                                </div>
                            </div>
                            <div class="icon col-md-4"><img src="<?php echo base_url()?>img/general/avg_farm_size.png"></div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="widget widget-tile hvr-fade">
                            <div class="data-info col-md-8">
                                <div class="value" id="pohon"></div>
                                <div class="desc">
                                    <?php echo lang('Tanaman Kakao') ?>
                                </div>
                            </div>
                            <div class="icon col-md-4"><img src="<?php echo base_url()?>img/general/pohon2.png"></div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="widget widget-tile hvr-fade">
                            <div class="data-info col-md-8">
                                <div class="value" id="luas"></div>
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
                                <div class="value" id="produksi"></div>
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
                                <div class="value" id="produktifitas"></div>
                                <div class="desc">
                                    <?php echo lang('Produktivitas (Kg/Ha/Thn)') ?>
                                </div>
                            </div>
                            <div class="icon col-md-4"><img src="<?php echo base_url()?>img/general/productivity.png"></div>
                        </div>
                    </div>
                        <!-- <div class="col-md-3">
                        <div class="widget widget-tile hvr-fade">
                            <div class="data-info col-md-8">
                                <div class="value" id="box31"></div>
                                    <div class="desc">
                                        <?php echo lang('Organisasi Petani') ?>
                                    </div>
                                </div>
                                <div class="icon col-md-4"><img src="<?php echo base_url()?>img/general/koperasi.PNG"></div>
                            </div>
                        </div> -->
                        <div class="col-md-3">
                        <div class="widget widget-tile hvr-fade">
                            <div class="data-info col-md-8">
                                <div class="value" id="produktifitas_pohon"></div>
                                    <div class="desc">
                                        <?php echo lang('Produktivitas Pohon (Kg/Pohon/Tahun)') ?>
                                    </div>
                                </div>
                                <div class="icon col-md-4"><img src="<?php echo base_url()?>img/general/tree_productivity.png"></div>
                            </div>
                        </div>
                        <div class="col-md-3">
                        <div class="widget widget-tile hvr-fade">
                            <div class="data-info col-md-8">
                                <div class="value" id="petani_sertifikasi"></div>
                                    <div class="desc">
                                        <?php echo lang('Jumlah Petani Terrsertifikasi') ?>
                                    </div>
                                </div>
                                <div class="icon col-md-4"><img src="<?php echo base_url()?>img/general/farmer.png"></div>
                            </div>
                        </div>
                        <div class="col-md-3">
                        <div class="widget widget-tile hvr-fade">
                            <div class="data-info col-md-8">
                                <div class="value" id="luas_sertifikasi"></div>
                                    <div class="desc">
                                        <?php echo lang('Luas Lahan Tersertifikasi (Ha)') ?>
                                    </div>
                                </div>
                                <div class="icon col-md-4"><img src="<?php echo base_url()?>img/general/certified_area.png"></div>
                            </div>
                        </div>
                        <div class="col-md-3">
                        <div class="widget widget-tile hvr-fade">
                            <div class="data-info col-md-8">
                                <div class="value" id="produksi_sertifikasi"></div>
                                    <div class="desc">
                                        <?php echo lang('Produksi Kakao Tersertifikasi (Ton)') ?>
                                    </div>
                                </div>
                                <div class="icon col-md-4"><img src="<?php echo base_url()?>img/general/certified_production.png"></div>
                            </div>
                        </div>
                        <div class="col-md-3">
                        <div class="widget widget-tile hvr-fade">
                            <div class="data-info col-md-8">
                                <div class="value" id="training_gnp"></div>
                                    <div class="desc">
                                        <?php echo lang('PESERTA GNP') ?>
                                    </div>
                                </div>
                                <div class="icon col-md-4"><img src="<?php echo base_url()?>img/general/nutrisi2.png"></div>
                            </div>
                        </div>
                        
                        <div class="col-md-3">
                        <div class="widget widget-tile hvr-fade">
                            <div class="data-info col-md-8">
                                <div class="value" id="training_gfp"></div>
                                    <div class="desc">
                                        <?php echo lang('Peserta GFP') ?>
                                    </div>
                                </div>
                                <div class="icon col-md-4"><img src="<?php echo base_url()?>img/general/finance_gfp.png"></div>
                            </div>
                        </div>
                </div>
                <div class="row">
                    <div class="col-md-6 xs-mt-20">
                        <div class="box gradient">
                            <div class="content row-fluid" style="background-color:#FFFFFF">
                                <div id="chart_farm_size"></div>
                            </div>
                            <!-- End .content -->
                        </div>
                        <!-- End .box -->
                    </div>
                    <!-- End .span6 -->
                    <div class="col-md-6 xs-mt-20">
                        <div class="box gradient">
                            <div class="content row-fluid" style="background-color:#FFFFFF">
                                <div id="chart_farm_mgt"></div>
                            </div>
                            <!-- End .content -->
                        </div>
                        <!-- End .box -->
                    </div>
                    <!-- End .span6 -->
                </div>
            </div>
            <!-- End .row-fluid -->
        </div>
        <?}
        if ($style!='') {?>
        <style type="text/css">
            <?php echo $style?>
        </style>
        <?}?>
        <script type="text/javascript" src="<?php echo base_url()?>js/modules/<?php echo $js?>.js"></script>

