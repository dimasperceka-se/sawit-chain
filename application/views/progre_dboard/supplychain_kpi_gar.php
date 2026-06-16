<?php
/**
 * @Author: gitandi
 * @Date:   2019-06-27 12:50:17
 * @Last Modified by:   gitandi
 * @Last Modified time: 2019-06-27 12:50:17
 */
//cek apakah dashboard filter region aktif
$uri4 = $this->uri->segment(4);
?>
<script>
    $('#page_title, #breadcrumb_title').text('<?php echo $titlet ?>');
<?php if ($uri4 == "") { ?>
        $('#first-breadcrumb').text('<?php echo $breadcrumb_1 ?>');
        $('#second-breadcrumb').text('<?php echo $breadcrumb_2 ?>');
<?php } ?>

<?php
$key = array_keys($action);
for ($i = 0; $i < sizeof($action); $i++) {
    ?>
        var m_<?php echo $key[$i] ?> = <?php echo ($action[$key[$i]] === true ? 'true' : ($action[$key[$i]] === false ? 'false' : "'" . $action[$key[$i]] . "'")) ?>;
<?php } ?>
</script>
<div id="ext-content"></div>

<div id='row-fluid'>

    <div class="page-head xs-pt-10 xs-pb-10">
        <div class="row">
            <div class="col-md-2"><h2><?php echo lang('Filter') ?></h2></div>
            <div class="col-md-10">
                <div class="btn-group btn-hspace pull-right">
                    <button style="height:27px;padding: 0px 4px !important;" class="btn btn-default pull-right" data-original-title=".btn .btn-info" data-placement="top" rel="tooltip" onClick="runSearch()"><?php echo lang('Cari') ?></button>
                </div>

                <div class="btn-group btn-hspace pull-right">
                    <select class="form-control DashCombo" name="fdistrict" id="fdistrict">
                        <option value="all_district"><?php echo lang('All District'); ?></option>
                    </select>
                </div>

                <div class="btn-group btn-hspace pull-right">
                    <select class="form-control DashCombo" name="fprovince" id="fprovince">
                        <option value="all_province"><?php echo lang('All Province'); ?></option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <div class="main-content" >
        <br/>

        <div class="row">
            <div class="col-md-12" style="padding: 0">

                <div class="col-md-3">
                    <div class="widget widget-download-list">
                        <div class="widget-head widget-tile">
                            <!--<div class="data-info col-md-8">-->
                            <div class="col-md-8">
                                <div class="value" id="box_mill"></div>
                                <div class="desc">
                                    <?php echo lang('Mill ') ?>
                                </div>
                            </div>
                            <div class="widget-icon col-md-4"><img src="<?php echo base_url() ?>img/general/Oil-Palm-Plantations.png"></div>
                            <!--</div>-->
                        </div>
                        <ul class="widget-list colapsed">
                            <li><a class="link_mill" data-type="province" href="#"><span class="label"><?php echo lang('Province') ?></span><span class="value" id="mill_province"></span> </a></li>
                            <li><a class="link_mill" data-type="distrcit" href="#"><span class="label"><?php echo lang('District') ?></span><span class="value" id="mill_district"></span> </a></li>
                            <!--<li><a class="link_farmer" data-type="" href="#" id="btn_detail_farmer"><span class="label"><?php echo lang('Detail') ?></span><span class="icon s7-download"></span></a></li>-->
                        </ul>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="widget widget-download-list">
                        <div class="widget-head">
                            <div class="col-md-8">
                                <div class="value" id="box_supplybase"></div>
                                <div class="desc">
                                    <?php echo lang('Supply Base') ?>
                                </div>
                            </div>
                            <div class="widget-icon col-md-4"><img src="<?php echo base_url() ?>img/general/trader.png"></div>
                        </div>
                        <ul class="widget-list colapsed">
                            <li><a class="link_Inti" data-type="Inti" href="#">
                                    <span class="label"><?php echo lang('Kebun Inti') ?></span>
                                    <!--<span class="value" id="garden_inti" style="padding-left: 50px;">-->
                                    </span><span class="value" id="inti"></span> 
                                </a>
                            </li>
                            <li>
                                <a class="link_Plasma" data-type="Plasma" href="#">
                                    <span class="label"><?php echo lang('Plasma') ?></span>
                                    <!--<span class="value" id="garden_plasma" style="padding-left: 50px;"></span>-->
                                    <span class="value" id="plasma"></span> </a></li>
                            <li>
                                <a class="link_External" data-type="External" href="#">
                                    <span class="label"><?php echo lang('External Estates') ?></span>
                                    <!--<span class="value" id="garden_external" style="padding-left: 50px;">-->
                                    </span><span class="value" id="external"></span> 
                                </a>
                            </li>
                            <li>
                                <a class="link_Dealer" data-type="Dealer" href="#">
                                    <span class="label"><?php echo lang('Dealer') ?></span>
                                    <span class="value" id="dealer"></span> 
                                </a>
                            </li>
                            <li>
                                <a class="link_Smallholder" data-type="Smallholder" href="#">
                                    <span class="label"><?php echo lang('Direct Smallholder') ?></span>
                                    <!--<span class="value" id="garden_smallholder" style="padding-left: 50px;"></span>-->
                                    <span class="value" id="smallholder"></span> 
                                </a>
                            </li>
                            <!--<li><a class="link_garden" data-type="" href="#" id="btn_detail_garden"><span class="label"><?php echo lang('Detail') ?></span><span class="icon s7-download"></span></a></li>-->
                        </ul>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="widget widget-download-list">
                        <div class="widget-head">
                            <!--<div class="data-info col-md-8">-->
                            <div class="col-md-8">
                                <div class="value" id="box_farmer"></div>
                                <div class="desc">
                                    <?php echo lang('Farmer') ?>
                                </div>
                            </div>
                            <div class="widget-icon col-md-4"><img src="<?php echo base_url() ?>img/general/petani2.png"></div>
                            <!--</div>-->
                        </div>
                        <ul class="widget-list colapsed">
                            <li><a class="link_dav" data-type="`dav" href="#"><span class="label"><?php echo lang('Dealer/Agent/Vendor') ?></span><span class="value" id="dav"></span> </a></li>
                            <li><a class="link_Smallholder" data-type="Smallholder" href="#"><span class="label"><?php echo lang('Direct Smallholder') ?></span><span class="value" id="farmer_smallholder"></span> </a></li>
                            <!--<li><a class="link_garden_mapped" data-type="" href="#" id="btn_detail_garden_mapped"><span class="label"><?php echo lang('Detail') ?></span><span class="icon s7-download"></span></a></li>-->
                        </ul>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="widget widget-download-list">
                        <div class="widget-head">
                            <div class="col-md-8">
                                <div class="value" id="box_farmer_registered"></div>
                                <div class="desc">
                                    <?php echo lang('Farmer Registered') ?>
                                </div>
                            </div>
                            <div class="widget-icon col-md-4"><img src="<?php echo base_url() ?>img/general/petani2.png"></div>
                        </div>
                        <ul class="widget-list colapsed">
                            <li><a class="link_ftraceble" data-type="Traceble" href="#"><span class="label"><?php echo lang('Traceable Farmer') ?></span><span class="value" id="Traceble"></span> </a></li>
                            <li><a class="link_funtraceble" data-type="Untraceble" href="#"><span class="label"><?php echo lang('Untraceable Farmer') ?></span><span class="value" id="Untraceble"></span> </a></li>
                            <!--<li><a class="link_garden" data-type="" href="#" id="btn_detail_garden"><span class="label"><?php echo lang('Detail') ?></span><span class="icon s7-download"></span></a></li>-->
                        </ul>
                    </div>
                </div>
                
            </div>


            <div class="col-md-12" style="padding: 0">

                <div class="col-md-3">
                    <div class="widget widget-download-list">
                        <div class="widget-head">
                            <div class="col-md-8">
                                <div class="value" id="box_forest"></div>
                                <div class="desc">
                                    <?php echo lang('Status Kawasan Hutan') ?>
                                </div>
                            </div>
                            <div class="widget-icon col-md-4"><img src="<?php echo base_url() ?>img/general/Oil-Palm-Plantations.png"></div>
                        </div>
                        <ul id="list_forest" class="widget-list colapsed">
                        </ul>
                    </div>
                </div>
                
            </div>

        </div>

        <br /><br />

    </div>

    <p class="dashDateGen" style="margin:15px 0 25px 15px;text-align:left;font-style:italic;font-weight:bold;"></p>
</div>

<script type="text/javascript" src="<?= base_url() ?>js/modules/<?= $js ?>.js"></script>