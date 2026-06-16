
<?php
if ($js != '') {

//cek apakah dashboard filter region aktif
$uri4 = $this->uri->segment(4);
    ?>
<script>
    $('#page_title, #breadcrumb_title').text('<?php echo $titlet ?>');
    <?php if($uri4 ==""){?>
        $('#first-breadcrumb').text('<?php echo $breadcrumb_1 ?>');
        $('#second-breadcrumb').text('<?php echo $breadcrumb_2 ?>');
    <?php }?>

    <?$key = array_keys($action);
    for ($i = 0; $i < sizeof($action); $i++) {?>
        var m_<?=$key[$i]?> = <?=($action[$key[$i]] === true ? 'true' : ($action[$key[$i]] === false ? 'false' : "'" . $action[$key[$i]] . "'"))?>;
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
                    <!--<button id="btnRegenDashboard" type="button" class="btn btn-primary">Regenerate Dashboard</button>-->
                </div>
                <div class="col-md-3">
                    <?php echo $this->load->view('list_region', $action, true); ?>
                    <span style="margin:4px 12px 0px 0px;float:right;font-size:19px;"><?php echo lang('Filter')?> : </span>
                </div>
            </div>
        </div>
        <br />

        <div class="main-content">
            <div class="row">
                <div class="col-md-3">
                    <div class="widget widget-tile hvr-fade">
                        <div class="data-info col-md-8">
                            <div class="value" id="box_total_farmer">0</div>
                            <div class="desc">
                                <?=lang('Total Farmers')?>
                            </div>
                        </div>
                        <div class="icon col-md-4"><img src="<?=base_url()?>img/general/petani2.png"></div>
                    </div>
                </div>

                <div class="col-md-3" style="display: none;">
                    <div class="widget widget-tile hvr-fade">
                        <div class="data-info col-md-8">
                            <div class="value" id="box_male_member">0</div>
                            <div class="desc">
                                <?=lang('Male Farmers')?> (%)
                            </div>
                        </div>
                        <div class="icon col-md-4"><img src="<?=base_url()?>img/general/petani2.png"></div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="widget widget-tile hvr-fade">
                        <div class="data-info col-md-8">
                            <div class="value" id="box_female_member">0</div>
                            <div class="desc">
                                <?=lang('Female Farmers')?> (%)
                            </div>
                        </div>
                        <div class="icon col-md-4"><img src="<?=base_url()?>img/general/perempuan.png"></div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="widget widget-tile hvr-fade">
                        <div class="data-info col-md-8">
                            <div class="value" id="box_average_age">0 years</div>
                            <div class="desc">
                                <?php echo lang('Average Farmer\'s Age (Years)') ?>
                            </div>
                        </div>
                        <div class="icon col-md-4"><img src="<?=base_url()?>img/general/usia.png"></div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="widget widget-tile hvr-fade">
                        <div class="data-info col-md-8">
                            <div class="value" id="box_completed_primary_school"></div>
                            <div class="desc">
                                <?php echo lang('Farmers Completed Primary School') ?> (%)
                            </div>
                        </div>
                        <div class="icon col-md-4"><img src="<?=base_url()?>img/general/lulus_sd.png"></div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="widget widget-tile hvr-fade">
                        <div class="data-info col-md-8">
                            <div class="value" id="box_ppi_index_125"></div>
                            <div class="desc">
                                <?php echo lang('Farmers living below $ 1.25 per day (%)') ?>
                            </div>
                        </div>
                        <div class="icon col-md-4"><img src="<?=base_url()?>img/general/bellow_1.25.png"></div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="widget widget-tile hvr-fade">
                        <div class="data-info col-md-8">
                            <div class="value" id="box_ppi_index_25"></div>
                            <div class="desc">
                                <?php echo lang('Farmers living below $ 2.5 per day (%)') ?>
                            </div>
                        </div>
                        <div class="icon col-md-4"><img src="<?=base_url()?>img/general/bellow_1.25.png"></div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="widget widget-tile hvr-fade">
                        <div class="data-info col-md-8">
                            <div class="value" id="box_below_35_age"></div>
                            <div class="desc">
                                <?php echo lang('Young Farmers Below 35 Years Old') ?> (%)
                            </div>
                        </div>
                        <div class="icon col-md-4"><img src="<?=base_url()?>img/general/usia.png"></div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="widget widget-tile hvr-fade">
                        <div class="data-info col-md-8">
                            <div class="value" id="box_average_hh_member"></div>
                            <div class="desc">
                                <?php echo lang('Average Household Members') ?>
                            </div>
                        </div>
                        <div class="icon col-md-4"><img src="<?=base_url()?>img/general/cpg2.png"></div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="widget widget-tile hvr-fade">
                        <div class="data-info col-md-8">
                            <div class="value" id="box_province"></div>
                            <div class="desc">
                                <?php echo lang('Provinsi') ?>
                            </div>
                        </div>
                        <div class="icon col-md-4"><img src="<?=base_url()?>img/general/provinsi.png"></div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="widget widget-tile hvr-fade">
                        <div class="data-info col-md-8">
                            <div class="value" id="box_district"></div>
                            <div class="desc">
                                <?php echo lang('Kabupaten') ?>
                            </div>
                        </div>
                        <div class="icon col-md-4"><img src="<?=base_url()?>img/general/kabupaten.png"></div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="widget widget-tile hvr-fade">
                        <div class="data-info col-md-8">
                            <div class="value" id="box_subdistrict"></div>
                            <div class="desc">
                                <?php echo lang('Sub-Districts') ?>
                            </div>
                        </div>
                        <div class="icon col-md-4"><img src="<?=base_url()?>img/general/kecamatan.png"></div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="widget widget-tile hvr-fade">
                        <div class="data-info col-md-8">
                            <div class="value" id="box_village"></div>
                            <div class="desc">
                                <?php echo lang('Village') ?>
                            </div>
                        </div>
                        <div class="icon col-md-4"><img src="<?=base_url()?>img/general/desa.png"></div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="widget widget-tile hvr-fade">
                        <div class="data-info col-md-8">
                            <div class="value" id="box_farmer_group"></div>
                            <div class="desc">
                                <?php echo lang('Farmer Groups') ?>
                            </div>
                        </div>
                        <div class="icon col-md-4"><img src="<?=base_url()?>img/general/cpg2.png"></div>
                    </div>
                </div>
                
                <?php 
                    if($action['PartnerID'] == "14") {
                        $CssDisplayWagsDisNone = 'display:none;';
                    }else{
                        $CssDisplayWagsDisNone = '';
                    }
                ?>
                <div style="<?php echo $CssDisplayWagsDisNone; ?>" class="col-md-3">
                    <div class="widget widget-tile hvr-fade">
                        <div class="data-info col-md-8">
                            <div class="value" id="box_cooperative"></div>
                            <div class="desc">
                                <?php echo lang('Cooperatives') ?>
                            </div>
                        </div>
                        <div class="icon col-md-4"><img src="<?=base_url()?>img/general/cpg2.png"></div>
                    </div>
                </div>

                <div style="<?php echo $CssDisplayWagsDisNone; ?>" class="col-md-3">
                    <div class="widget widget-tile hvr-fade">
                        <div class="data-info col-md-8">
                            <div class="value" id="box_gapoktan"></div>
                            <div class="desc">
                                <?php echo lang('Gapoktan') ?>
                            </div>
                        </div>
                        <div class="icon col-md-4"><img src="<?=base_url()?>img/general/cpg2.png"></div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="widget widget-tile hvr-fade">
                        <div class="data-info col-md-8">
                            <div class="value" id="box_farmer_own_hp"></div>
                            <div class="desc">
                                <?php echo lang('Farmers owning a Handphone')?>
                            </div>
                        </div>
                        <div class="icon col-md-4"><img src="<?php echo base_url()?>img/general/farmer-handphone.png"></div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="widget widget-tile hvr-fade">
                        <div class="data-info col-md-8">
                            <div class="value" id="box_hp_smart"></div>
                            <div class="desc">
                                <?php echo lang('Farmers owning a Smartphone or with access to a Smartphone')?>
                            </div>
                        </div>
                        <div class="icon col-md-4"><img src="<?php echo base_url()?>img/general/farmer-smartphone.png"></div>
                    </div>
                </div>

            </div>

            <div class="row">
                <div class="col-md-6 xs-mt-20">
                    <div class="box gradient">
                        <div class="content row-fluid" style="border:1px solid lightgray;">
                            <div id="pie_hh_member_count"></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 xs-mt-20">
                    <div class="box gradient">
                        <div class="content row-fluid" style="border:1px solid lightgray;">
                            <div id="bar_gender_per_daerah"></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 xs-mt-20">
                    <div class="box gradient">
                        <div class="content row-fluid" style="border:1px solid lightgray;">
                            <div id="bar_member_age"></div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 xs-mt-20">
                    <div class="box gradient">
                        <div class="content row-fluid" style="border:1px solid lightgray;">
                            <div id="pie_age_class"></div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 xs-mt-20">
                    <div class="box gradient">
                        <div class="content row-fluid" style="border:1px solid lightgray;">
                            <div id="pie_education"></div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 xs-mt-20">
                    <div class="box gradient">
                        <div class="content row-fluid" style="border:1px solid lightgray;">
                            <div id="pie_marital_status"></div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 xs-mt-20">
                    <div class="box gradient">
                        <div class="content row-fluid" style="border:1px solid lightgray;">
                            <div id="bar_average_hh_members"></div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 xs-mt-20">
                    <div class="box gradient">
                        <div class="content row-fluid" style="border:1px solid lightgray;">
                            <div id="bar_poverty_level"></div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 xs-mt-20">
                    <div class="box gradient">
                        <div class="content row-fluid" style="border:1px solid lightgray;">
                            <div id="column_farmer_group"></div>
                        </div>
                    </div>
                </div>
                
                <div style="<?php echo $CssDisplayWagsDisNone?>" class="col-md-6 xs-mt-20">
                    <div class="box gradient">
                        <div class="content row-fluid" style="border:1px solid lightgray;">
                            <div id="column_cooperative"></div>
                        </div>
                    </div>
                </div>

                <div style="<?php echo $CssDisplayWagsDisNone?>" class="col-md-6 xs-mt-20">
                    <div class="box gradient">
                        <div class="content row-fluid" style="border:1px solid lightgray;">
                            <div id="column_gapoktan"></div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 xs-mt-20">
                    <div class="box gradient">
                        <div class="content row-fluid" style="border:1px solid lightgray;">
                            <div id="pie_handphone"></div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 xs-mt-20">
                    <div class="box gradient">
                        <div class="content row-fluid" style="border:1px solid lightgray;">
                            <div id="pie_hp_smartphone"></div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        <!-- End .row-fluid -->

        <p class="dashDateGen" style="margin:15px 0 25px 15px;text-align:left;font-style:italic;font-weight:bold;"></p>

    </div>

    <?}
if ($style != '') {?>
    <style type="text/css">
        <?=$style?>
    </style>
    <?}?>
    <script type="text/javascript" src="<?=base_url()?>js/modules/<?=$js?>.js"></script>