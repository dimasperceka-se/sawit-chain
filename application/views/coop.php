<?php
if ($js!='') {
    ?>
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
                    <div class="col-md-10" style="display:none">
                        <?php echo $this->load->view('list_coop', $action, TRUE); ?>  
                    </div>
                </div>
            </div>
        <div class="main-content" >
            <div class="row">
                <div class="col-md-3">
                    <div class="widget widget-tile hvr-fade">
                        <div class="data-info col-md-8">
                            <div class="value" id="registered_member"></div>
                            <div class="desc">
                                <?php echo lang('Anggota Terdaftar') ?>
                            </div>
                        </div>
                        <div class="icon col-md-4"><img src="<?php echo base_url()?>img/general/finance_gfp.png"></div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="widget widget-tile hvr-fade">
                        <div class="data-info col-md-8">
                            <div class="value" id="candidate_member"></div>
                            <div class="desc">
                                <?php echo lang('Anggota Kandidat') ?>
                            </div>
                        </div>
                        <div class="icon col-md-4"><img src="<?php echo base_url()?>img/general/finance_gfp.png"></div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="widget widget-tile hvr-fade">
                        <div class="data-info col-md-8">
                            <div class="value" id="active_member"></div>
                            <div class="desc">
                                <?php echo lang('Anggota Aktif') ?>
                            </div>
                        </div>
                        <div class="icon col-md-4"><img src="<?php echo base_url()?>img/general/finance_gfp.png"></div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="widget widget-tile hvr-fade">
                        <div class="data-info col-md-8">
                            <div class="value" id="inactive_member"></div>
                            <div class="desc">
                                <?php echo lang('Anggota Tidak Aktif') ?>
                            </div>
                        </div>
                        <div class="icon col-md-4"><img src="<?php echo base_url()?>img/general/finance_gfp.png"></div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="widget widget-tile hvr-fade">
                        <div class="data-info col-md-8">
                            <div class="value" id="member_scpp"></div>
                            <div class="desc">
                                <?php echo lang('Anggota SCPP') ?>
                            </div>
                        </div>
                        <div class="icon col-md-4"><img src="<?php echo base_url()?>img/general/finance_gfp.png"></div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="widget widget-tile hvr-fade">
                        <div class="data-info col-md-8">
                            <div class="value" id="member_nonscpp"></div>
                            <div class="desc">
                                <?php echo lang('Anggota Non SCPP') ?>
                            </div>
                        </div>
                        <div class="icon col-md-4"><img src="<?php echo base_url()?>img/general/finance_gfp.png"></div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="widget widget-tile hvr-fade">
                        <div class="data-info col-md-8">
                            <div class="value" id="member_loan"></div>
                            <div class="desc">
                                <?php echo lang('Jumlah Anggota Meminjam') ?>
                            </div>
                        </div>
                        <div class="icon col-md-4"><img src="<?php echo base_url()?>img/general/finance_gfp.png"></div>
                    </div>
                </div>
                 <div class="col-md-3">
                    <div class="widget widget-tile hvr-fade">
                        <div class="data-info col-md-8">
                            <div class="value" id="member_due_saving_wajib"></div>
                            <div class="desc">
                                <?php echo lang('Jumlah Anggota Menunggak Simpanan Wajib') ?>
                            </div>
                        </div>
                        <div class="icon col-md-4"><img src="<?php echo base_url()?>img/general/finance_gfp.png"></div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="widget widget-tile hvr-fade">
                        <div class="data-info col-md-8">
                            <div class="value" id="total_loan"></div>
                            <div class="desc">
                                <?php echo lang('Jumlah Pinjaman') ?>
                            </div>
                        </div>
                        <div class="icon col-md-4"><img src="<?php echo base_url()?>img/general/finance_saving.png"></div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="widget widget-tile hvr-fade">
                        <div class="data-info col-md-8">
                            <div class="value" id="total_loan_oustanding"></div>
                            <div class="desc">
                                <?php echo lang('Jumlah Pinjaman Outstanding') ?>
                            </div>
                        </div>
                        <div class="icon col-md-4"><img src="<?php echo base_url()?>img/general/finance_saving.png"></div>
                    </div>
                </div>
                 <div class="col-md-3">
                    <div class="widget widget-tile hvr-fade">
                        <div class="data-info col-md-8">
                            <div class="value" id="total_loan_interest"></div>
                            <div class="desc">
                                <?php echo lang('Jumlah Bunga Pinjaman') ?>
                            </div>
                        </div>
                        <div class="icon col-md-4"><img src="<?php echo base_url()?>img/general/finance_saving.png"></div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="widget widget-tile hvr-fade">
                        <div class="data-info col-md-8">
                            <div class="value" id="total_loan_paid"></div>
                            <div class="desc">
                                <?php echo lang('Jumlah Pinjaman Terbayar') ?>
                            </div>
                        </div>
                        <div class="icon col-md-4"><img src="<?php echo base_url()?>img/general/finance_saving.png"></div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="widget widget-tile hvr-fade">
                        <div class="data-info col-md-8">
                            <div class="value" id="total_saving_account"></div>
                            <div class="desc">
                                <?php echo lang('Jumlah Akun Tabungan') ?>
                            </div>
                        </div>
                        <div class="icon col-md-4"><img src="<?php echo base_url()?>img/general/finance_loan.png"></div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="widget widget-tile hvr-fade">
                        <div class="data-info col-md-8">
                            <div class="value" id="saving_pokok"></div>
                            <div class="desc">
                                <?php echo lang('Jumlah Simpanan Pokok') ?>
                            </div>
                        </div>
                        <div class="icon col-md-4"><img src="<?php echo base_url()?>img/general/finance_gfp_baseline.png"></div>
                    </div>
                </div>                                  
                <div class="col-md-3">
                    <div class="widget widget-tile hvr-fade">
                        <div class="data-info col-md-8">
                            <div class="value" id="saving_wajib"></div>
                            <div class="desc">
                                <?php echo lang('Jumlah Simpanan Wajib') ?>
                            </div>
                        </div>
                        <div class="icon col-md-4"><img src="<?php echo base_url()?>img/general/finance_account.png"></div>
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
            <?}
            if ($style!='') {?>
            <style type="text/css">
                <?php echo $style?>
            </style>
            <?}?>
            <?php if ($js): ?>  
                <script type="text/javascript" src="<?php echo base_url()?>js/modules/<?php echo $js?>.js"></script>
            <?php endif ?>
