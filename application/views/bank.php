
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
                    <?php echo $this->load->view('list_region', $action, TRUE); ?>
                </div>
            </div>
        </div>
        <div class="row-fluid">

        <div class="main-content" >
        <div class="row">
            <!-- <div class="col-md-3">
                <div class="widget widget-tile hvr-fade">
                    <div class="data-info col-md-8">
                        <div class="value" id="box_no_loan"></div>
                        <div class="desc">
                            <?php echo lang('Number of Fitted the Criteria')?>
                        </div>
                    </div>
                    <div class="icon col-md-4"><img src="<?php echo base_url()?>img/general/finance_loan.png"></div>
                </div>
            </div> -->
            <div class="col-md-3">
                <div class="widget widget-tile hvr-fade">
                    <div class="data-info col-md-8">
                        <div class="value" id="box_farmer_loan_pass_lt10"></div>
                        <div class="desc">
                            <?php echo lang('Farmers who fit the criteria and lived 10 km from Bank')?>
                        </div>
                    </div>
                    <div class="icon col-md-4"><img src="<?php echo base_url()?>img/general/finance_loan.png"></div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="widget widget-tile hvr-fade">
                    <div class="data-info col-md-8">
                        <div class="value" id="box_farmer_loan_pass_mt10"></div>
                        <div class="desc">
                            <?php echo lang('Farmers who fit the criteria and lived outside 10 km from Bank')?>
                        </div>
                    </div>
                    <div class="icon col-md-4"><img src="<?php echo base_url()?>img/general/finance_loan.png"></div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="widget widget-tile hvr-fade">
                    <div class="data-info col-md-8">
                        <div class="value" id="box_approved"></div>
                        <div class="desc">
                            <?php echo lang('Number of Approved Loan')?>
                        </div>
                    </div>
                    <div class="icon col-md-4"><img src="<?php echo base_url()?>img/general/loan-approved.png"></div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="widget widget-tile hvr-fade">
                    <div class="data-info col-md-8">
                        <div class="value" id="box_rejected"></div>
                        <div class="desc">
                            <?php echo lang('Number of Rejected Loan')?>
                        </div>
                    </div>
                    <div class="icon col-md-4"><img src="<?php echo base_url()?>img/general/loan-rejected.png"></div>
                </div>
            </div>
            <!-- <div class="col-md-3">
                <div class="widget widget-tile hvr-fade">
                    <div class="data-info col-md-8">
                        <div class="value" id="box_amount"></div>
                        <div class="desc">
                            <?php echo lang('Total Amount of Approved Loan') ?>
                        </div>
                    </div>
                    <div class="icon col-md-4"><img src="<?php echo base_url()?>img/general/finance_gfp.png"></div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="widget widget-tile hvr-fade">
                    <div class="data-info col-md-8">
                        <div class="value" id="box_distance_bank"></div>
                        <div class="desc">
                            <?php echo lang('Farmer who Lived 10 km from Banks') ?>
                        </div>
                    </div>
                    <div class="icon col-md-4"><img src="<?php echo base_url()?>img/general/kabupaten.png"></div>
                </div>
            </div> -->
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
