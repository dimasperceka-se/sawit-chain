<?php
/**
 * @Author: nikolius
 * @Date:   2017-05-12 11:07:09
 */
?>
<script>
    $('#page_title, #breadcrumb_title').text('<?php echo $titlet ?>');
    $('#first-breadcrumb').text('<?php echo $breadcrumb_1 ?>');
    $('#second-breadcrumb').text('<?php echo $breadcrumb_2 ?>');
    <?$key = array_keys($action);
    for ($i = 0; $i < sizeof($action); $i++) {?>
    var m_<?=$key[$i]?> = <?=($action[$key[$i]] === true ? 'true' : ($action[$key[$i]] === false ? 'false' : "'" . $action[$key[$i]] . "'"))?>;
    <?}?>
</script>
<div id="ext-content"></div>

<div id='row-fluid'>

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
                        <div class="value" id="box1">0.00</div>
                        <div class="desc">
                            <?=lang('Acreages of land provided by the local government to the programme')?>
                        </div>
                    </div>
                    <div class="icon col-md-4"><img src="<?=base_url()?>img/general/GAP.png"></div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="widget widget-tile hvr-fade">
                    <div class="data-info col-md-8">
                        <div class="value" id="box1">0.00</div>
                        <div class="desc">
                            <?=lang('Increase in revenues')?>
                        </div>
                    </div>
                    <div class="icon col-md-4"><img src="<?=base_url()?>img/general/GAP.png"></div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="widget widget-tile hvr-fade">
                    <div class="data-info col-md-8">
                        <div class="value" id="box1">0.00</div>
                        <div class="desc">
                            <?=lang('Usage of wood for other purposes than distillation')?>
                        </div>
                    </div>
                    <div class="icon col-md-4"><img src="<?=base_url()?>img/general/GAP.png"></div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="widget widget-tile hvr-fade">
                    <div class="data-info col-md-8">
                        <div class="value" id="box1">0.00</div>
                        <div class="desc">
                            <?=lang('Infrastructure improvement as a side effect to the programme')?>
                        </div>
                    </div>
                    <div class="icon col-md-4"><img src="<?=base_url()?>img/general/GAP.png"></div>
                </div>
            </div>

        </div>

        <div class="row">

            <div class="col-md-3">
                <div class="widget widget-tile hvr-fade">
                    <div class="data-info col-md-8">
                        <div class="value" id="box1">0.00</div>
                        <div class="desc">
                            <?=lang('Other reforestation activity or community activy in the same area')?>
                        </div>
                    </div>
                    <div class="icon col-md-4"><img src="<?=base_url()?>img/general/GAP.png"></div>
                </div>
            </div>

        </div>

    </div>

</div>