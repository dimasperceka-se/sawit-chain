<?php

/**
 * @Author: nikolius
 * @Date:   2017-09-22 14:34:52
 * @Last Modified by:   nikolius
 * @Last Modified time: 2017-09-22 17:50:32
 */
//cek apakah dashboard filter region aktif
$uri4 = $this->uri->segment(4);
?>
<script>
    $('#page_title, #breadcrumb_title').text('<?php echo $titlet ?>');
    <?php if($uri4 ==""){?>
        $('#first-breadcrumb').text('<?php echo $breadcrumb_1 ?>');
        $('#second-breadcrumb').text('<?php echo $breadcrumb_2 ?>');
    <?php }?>

    //untuk combobox agar tampil tanpa lihat status
    <?php $action['region_status'] = "not_active"; ?>

    <?$key = array_keys($action);
    for ($i = 0; $i < sizeof($action); $i++) {?>
    var m_<?php echo $key[$i]?> = <?php echo ($action[$key[$i]] === true ? 'true' : ($action[$key[$i]] === false ? 'false' : "'" . $action[$key[$i]] . "'"))?>;
    <?}?>
</script>

<div id="ext-content"></div>

<div id='row-fluid'>

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

    <div class="main-content" >
        <div class="row">

            <div class="col-md-6 xs-mt-20">
                <div class="box gradient">
                    <div class="content row-fluid" style="border:1px solid lightgray;">
                        <div id="bar_chart_count_training"></div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 xs-mt-20">
                <div class="box gradient">
                    <div class="content row-fluid" style="border:1px solid lightgray;">
                        <div id="bar_chart_count_training_peryear"></div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 xs-mt-20">
                <div class="box gradient">
                    <div class="content row-fluid" style="border:1px solid lightgray;">
                        <div id="bar_chart_training_kategori"></div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 xs-mt-20">
                <div class="box gradient">
                    <div class="content row-fluid" style="border:1px solid lightgray;">
                        <div id="pie_chart_training_kategori"></div>
                    </div>
                </div>
            </div>

        </div>

    </div>

    <p class="dashDateGen" style="margin:15px 0 25px 15px;text-align:left;font-style:italic;font-weight:bold;"></p>
</div>

<script type="text/javascript" src="<?=base_url()?>js/modules/<?=$js?>.js"></script>