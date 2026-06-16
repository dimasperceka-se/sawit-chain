    <script language="javascript" type="text/javascript" src="<?=base_url()?>assets/plugins/datepicker/js/bootstrap-datepicker.js"></script>
    <style type="text/css">
    .datepicker table {
        min-width: 220px;
    }
    </style>
    <div class="pull-right xs-mr-50">&nbsp;</div>
    <div class="btn-group btn-hspace pull-right" id="selectYear" class="hidden">
        <input type="text" id="datepicker1" class="form-control" style="width: 60px; height: 38px;" value="<?php echo !empty($tahun)?$tahun:date('Y') ?>">
    </div>
    <div class="btn-group btn-hspace pull-right">
        <button class="btn btn-default dropdown-toggle" data-toggle="dropdown" type="button" aria-expanded="false"><span id="typeTitle"></span>&nbsp;<span class="caret"></span></button>
        <ul class="dropdown-menu" role="menu" id="dLabeli">
            <li><a onClick="changeType(0); setRange(); return false" href="<?php echo current_url()?>"><?php echo lang('All Farmers') ?></a></li>
            <li><a onClick="changeType(1); setRange(); return false" href="<?php echo current_url()?>?petani=1"><?php echo lang('Certified Farmers') ?></a></li>
        </ul>
    </div>
    <script type="text/javascript">
        $('#datepicker1').datepicker({
            format: 'yyyy ',
            viewMode: 2,
            minViewMode: 2
        }).on('changeDate', function(ev){
            $(this).datepicker('hide');
            setTimeout(function() {
                setRange();
            }, 100);
        });
        function changeType (type) {
            if (type == 1) {
                $('#selectYear').removeClass('hidden');
                $('#typeTitle').text(lang('Certified Farmers'));
            } else {
                $('#selectYear').addClass('hidden');
                $('#typeTitle').text(lang('All Farmers'));
            }
            m_petani = type;
        } 
        function setRange() {
            if (typeof(m_survey) === 'undefined') {
                m_survey = null;
            }
            var tahun = $('#datepicker1').val();
            link('<?php echo current_url() ?>?petani='+m_petani+'&tahun='+tahun+'&survey='+m_survey);
        }  
        $(function() {                                    
            changeType(m_petani);     
            $('#datepicker1').on('change', function(event) {
                // console.log($(this).val());
            });                           
        });
    </script>