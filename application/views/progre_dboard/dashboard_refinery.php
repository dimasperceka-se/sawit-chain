<script language="javascript" type="text/javascript" src="<?=base_url()?>assets/plugins/datepicker/js/bootstrap-datepicker.js"></script>
<?php

/**
 * @Author: muhammad hidayaturrohman
 * @Date:   2020-11-25
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

    <?$key = array_keys($action);
    for ($i = 0; $i < sizeof($action); $i++) {?>
    var m_<?php echo $key[$i]?> = <?php echo ($action[$key[$i]] === true ? 'true' : ($action[$key[$i]] === false ? 'false' : "'" . $action[$key[$i]] . "'"))?>;
    <?}?>
</script>
<div id="ext-content"></div>

<div id='row-fluid'>
    <div class="page-head xs-pt-10 xs-pb-10">
        <div class="row">
            <div class="col-md-2"><h2><?php echo lang('Filter') ?></h2></div>
            <div class="col-md-10">
                <div class="pull-right xs-mr-50">&nbsp;</div>
                <div class="btn-group btn-hspace pull-right">
                    <div class="btn-group btn-hspace pull-right">
                        <button class="btn btn-default pull-right" data-original-title=".btn .btn-info" data-placement="top" rel="tooltip" onClick="setRange()"><?php echo lang('Cari') ?></button>
                    </div>
                    <div class="btn-group btn-hspace pull-right">
                    <input type="text" id="startdate" class="form-control"  style="display:inline-block; width: 105px; height: 38px;" value="<?php echo !empty($startdate)?$startdate:date('Y-01-01') ?>">
                        &nbsp;&nbsp;<?php echo lang('sampai') ?>&nbsp;&nbsp;
                    <input type="text" id="enddate" class="form-control"  style="display:inline-block; width: 105px; height: 38px;" value="<?php echo !empty($enddate)?$enddate:date('Y-m-d') ?>">&nbsp;&nbsp;
                    </div>
                    <div class="btn-group btn-hspace pull-right">
                        <p style="margin-top:4px;padding:3px"><p>
                    </div>
                    <div class="btn-group btn-hspace pull-right">
                        <button class="btn btn-default dropdown-toggle" data-toggle="dropdown" type="button" aria-expanded="false"><span id="currentMill"><?php echo lang('All Mill') ?></span>&nbsp;<span class="caret"></span></button>
                        <ul class="dropdown-menu" role="menu" id="Mill">
                        </ul>
                    </div>         
                </div>
            </div>
        </div>
    </div>

    <div class="main-content" >
        <br />

        <div class="row">

            <div class="col-md-3">
                <div class="widget widget-tile hvr-fade">
                    <div class="data-info col-md-8">
                        <div class="value" id="number_off_total_input_oil">0</div>
                        <div class="desc">
                            <?php echo lang('TOTAL INPUT OIL (MT)')?>
                        </div>
                    </div>
                    <div class="icon col-md-4"><img src="<?php echo base_url()?>img/general/refinery/icon-palmoil-oil-input.png"></div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="widget widget-tile hvr-fade">
                    <div class="data-info col-md-8">
                        <div class="value" id="number_of_total_cpo">0</div>
                        <div class="desc">
                            <?php echo lang('TOTAL CPO (MT)')?>
                        </div>
                    </div>
                    <div class="icon col-md-4"><img src="<?php echo base_url()?>img/general/refinery/icon-palmoil-total-cpo.png"></div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="widget widget-tile hvr-fade">
                    <div class="data-info col-md-8">
                        <div class="value" id="number_of_total_pko">0</div>
                        <div class="desc">
                            <?php echo lang('TOTAL PK (MT)')?>
                        </div>
                    </div>
                    <div class="icon col-md-4"><img src="<?php echo base_url()?>img/general/refinery/icon-palmoil-total-pko.png"></div>
                </div>
            </div>

            <div class="col-md-3">
                <div class="widget widget-download-list">
                    <div class="widget-head widget-tile">
                        <div class="col-md-8">
                            <div class="value" id="number_of_transaction">0</div>
                            <div class="desc">
                                <?php echo lang('NUMBER OF RECEPTION') ?>
                            </div>
                        </div>
                        <div class="widget-icon col-md-4"><img src="<?php echo base_url()?>img/general/refinery/icon-palmoil-transaction.png"></div>
                    </div>
                    <ul class="widget-list colapsed" id="transaction_details">
                    
                    </ul>
                </div>
            </div>

        </div>

        <br /><br />

        <div class="row">

            <div class="col-md-6 xs-mt-20">
                <div class="box gradient">
                    <div class="content row-fluid" style="border:1px solid lightgray;">
                        <div id="number_gauge_cpo_delivered"></div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 xs-mt-20">
                <div class="box gradient">
                    <div class="content row-fluid" style="border:1px solid lightgray;">
                        <div id="number_gauge_pko_delivered"></div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 xs-mt-20">
                <div class="box gradient">
                    <div class="content row-fluid" style="border:1px solid lightgray;">
                        <div id="number_off_refinery_transaction"></div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 xs-mt-20">
                <div class="box gradient">
                    <div class="content row-fluid" style="border:1px solid lightgray;">
                        <div id="number_off_oil_transaction"></div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 xs-mt-20">
                <div class="box gradient">
                    <div class="content row-fluid" style="border:1px solid lightgray;">
                        <div id="number_off_refinery_transaction"></div>
                    </div>
                </div>
            </div>

            <div class="col-md-6 xs-mt-20">
                <div class="box gradient">
                    <div class="content row-fluid" style="border:1px solid lightgray;">
                        <div id="number_off_oil_input"></div>
                    </div>
                </div>
            </div>

            <div class="col-md-12 xs-mt-20">
                <div class="box gradient">
                    <div class="content row-fluid" style="border:1px solid lightgray;">
                        <div id="number_oil_line_production"></div>
                    </div>
                </div>
            </div>

        </div>

    </div>

    <p class="dashDateGen" style="margin:15px 0 25px 15px;text-align:left;font-style:italic;font-weight:bold;"></p>
</div>

<script type="text/javascript" src="<?=base_url()?>js/modules/<?=$js?>.js"></script>

<script>
$(function() {
    $.get(m_api+'/dashboard/group_mill_refinery/', function(data) {
        if (data) {
            $.each(data, function(index, val) {
                var li = '<li><a href="#" class="mill" data-id="'+val.id+'" data-name="'+val.name+'">'+val.name+'</a></li>';
                $('#Mill').append(li);
            });
        }
    });

    $('#Mill').on('click', '.mill', function(event) {
        event.preventDefault();
        var start   = $('#datepicker1').val();
        var end     = $('#datepicker2').val();

        //get MillName
        var name    = $(this).data('name');
        console.log(name);
        $('#currentMill').text($(this).data('name'));

        //get IdMill
        m_mill   = $(this).data('id');
       
        $('#mill').empty();
        $.get(m_api+'/dashboard/mill_list_refinery?MillID=' + m_mill, function(data) {
            if (data) {
                var li = '<li><a href="#" class="mill" data-id="" data-name="'+lang('All Mill')+'">'+lang('All Mill')+'</a></li>';
                    $('#mill').append(li);
                $.each(data, function(index, val) {
                    var li = '<li><a href="#" class="mill" data-id="'+val.id+'" data-name="'+val.name+'">'+val.name+'</a></li>';
                    $('#mill').append(li);
                });
            }
        });
    });
});

var checkin = $('#startdate').datepicker({
    format: 'yyyy-mm-dd'
}).on('changeDate', function(ev) {
    if (ev.date.valueOf() > checkout.date.valueOf()) {
        var newDate = new Date(ev.date)
        newDate.setDate(newDate.getDate() + 1);
        checkout.setValue(newDate);
    }
}).data('datepicker');

var checkout = $('#enddate').datepicker({
    format: 'yyyy-mm-dd',
    onRender: function(date) {
        return date.valueOf() <= checkin.date.valueOf() ? 'disabled' : '';
    }
}).on('changeDate', function(ev) {
}).data('datepicker');

function setRange() {
    ajaxDataRenderer(m_data);
}         

</script>