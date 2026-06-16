<style>
    .Filter {
    width: 47px;
    font-family: Roboto;
    font-size: 20px;
    font-weight: 300;
    font-stretch: normal;
    font-style: normal;
    line-height: normal;
    letter-spacing: 0.6px;
    text-align: left;
    color: #000;
}
</style>
<div class="page-head">
    <div class="row"> 
            <span class="Filter">
            Filter
            </span>
             <div class="btn-group btn-hspace pull-right">
                <button class="btn btn-default pull-right" style="border-radius: 8px;" data-original-title=".btn .btn-info" data-placement="top" rel="tooltip" onClick="setFilter()"><?php echo lang('Cari') ?></button>
            </div>
            <div class="btn-group btn-hspace pull-right">
            <button class="btn btn-default dropdown-toggle" style="display:inline-block; width: 150px; height: 38px; border-radius: 8px;" data-toggle="dropdown" type="button" aria-expanded="false"><span id="currentVillage"><?php echo lang('Village') ?></span>&nbsp;<span class="caret"></span></button>
            <ul class="dropdown-menu" role="menu" id="VillageList">
            </ul>
            </div>
            <div class="btn-group btn-hspace pull-right">
            <button class="btn btn-default dropdown-toggle" style="display:inline-block; width: 150px; height: 38px; border-radius: 8px;" data-toggle="dropdown" type="button" aria-expanded="false"><span id="currentSubDistrict"><?php echo lang('SubDistrict') ?></span>&nbsp;<span class="caret"></span></button>
            <ul class="dropdown-menu" role="menu" id="SubDistrictList">
            </ul>
            </div>
            <div class="btn-group btn-hspace pull-right">
            <button class="btn btn-default dropdown-toggle" style="display:inline-block; width: 150px; height: 38px; border-radius: 8px;" data-toggle="dropdown" type="button" aria-expanded="false"><span id="currentDistrict"><?php echo lang('District') ?></span>&nbsp;<span class="caret"></span></button>
            <ul class="dropdown-menu" role="menu" id="DistrictList">
            </ul>
            </div>
            
            <div class="btn-group btn-hspace pull-right">
               <input type="text" id="datepicker1" class="form-control" style="display:inline-block; width: 105px; height: 38px; border-radius: 8px;" value="<?php echo $awal?>">
                &nbsp;&nbsp;<?php echo lang('sampai') ?>&nbsp;&nbsp;
                <input type="text" id="datepicker2" class="form-control" style="display:inline-block; width: 105px; height: 38px; border-radius: 8px;" value="<?php echo $akhir ?>">&nbsp;&nbsp;
            </div>
            
    </div>
</div>
<script language="javascript" type="text/javascript" src="<?php echo base_url()?>js/plugins/bootstrap-datepicker.js"></script>
<script>  
    var checkin = $('#datepicker1').datepicker({
    format: 'yyyy-mm-dd'
    }).on('changeDate', function(ev) {
    if (ev.date.valueOf() > checkout.date.valueOf()) {
        var newDate = new Date(ev.date)
        newDate.setDate(newDate.getDate() + 1);
        checkout.setValue(newDate);
    }
    }).data('datepicker');

    var checkout =  $('#datepicker2').datepicker({
    format: 'yyyy-mm-dd',
    onRender: function(date) {
        return date.valueOf() <= checkin.date.valueOf() ? 'disabled' : '';
    }
    }).on('changeDate', function(ev) {}).data('datepicker');
</script>

<script>
 $(function() {
    $.get(m_api+'/dashboard/district_session/', function(data) {
        if (data) {
            var li = '<li><a href="#" class="list_district" data-id="" data-name="'+lang('All District')+'">'+lang('All District')+'</a></li>';
                    $('#DistrictList').append(li);
            $.each(data, function(index, val) {
                $.each(val, function(i, v) {
                    var li = '<li><a href="#" class="list_district" data-id="'+v.id+'" data-name="'+v.name+'">'+v.name+'</a></li>';
                    $('#DistrictList').append(li);
                });
            });
            
            // set previously selected
            if (localStorage.getItem("dis")) {
                $('.list_district[data-id="'+localStorage.getItem("dis")+'"]').click();
            }
        }
    });
    
    $('#DistrictList').on('click', '.list_district', function(event) {
        event.preventDefault();
        var start   = $('#datepicker1').val();
        var end     = $('#datepicker2').val();
        $('#currentDistrict').text($(this).data('name'));
        m_district = $(this).data('id');
        var m_subdistrict = '';
        var m_village = '';
        
        localStorage.setItem("dis", m_district);
        localStorage.setItem("sub", m_subdistrict);
        localStorage.setItem("vil", m_village);

        $.get(m_api+'/dashboard/subdistrict_session/?id='+$(this).data('id'), function(data) {
            if (data) {
                var li = '<li><a href="#" class="list_sub_district" data-id="" data-name="'+lang('All SubDistrict')+'">'+lang('All SubDistrict')+'</a></li>';
                    $('#SubDistrictList').append(li);
                $.each(data, function(index, val) {
                    $.each(val, function(i, v) {
                        var li = '<li><a href="#" class="list_sub_district" data-id="'+v.id+'" data-name="'+v.name+'">'+v.name+'</a></li>';
                        $('#SubDistrictList').append(li);
                    } );
                });

                // set previously selected
                if (localStorage.getItem("dis")) {
                    $('.list_sub_district[data-id="'+localStorage.getItem("dis")+'"]').click();
                }
            }
        });

        $('#SubDistrictList').on('click', '.list_sub_district', function(event) {
            event.preventDefault();
            var start   = $('#datepicker1').val();
            var end     = $('#datepicker2').val();
            $('#currentSubDistrict').text($(this).data('name'));
            var m_subdistrict = $(this).data('id');
            localStorage.setItem("sub", m_subdistrict);
            var m_village = '';
            localStorage.setItem("vil", m_village);
            
            $.get(m_api+'/dashboard/village_session/?id='+$(this).data('id'), function(data) {
                if (data) {
                    var li = '<li><a href="#" class="list_village" data-id="" data-name="'+lang('All Village')+'">'+lang('All Village')+'</a></li>';
                    $('#VillageList').append(li);
                    $.each(data, function(index, val) {
                        $.each(val, function(i, v) {
                            var li = '<li><a href="#" class="list_village" data-id="'+v.id+'" data-name="'+v.name+'">'+v.name+'</a></li>';
                            $('#VillageList').append(li);
                        } );
                    });

                    $('#VillageList').on('click', '.list_village', function(event) {
                        event.preventDefault();
                        var start   = $('#datepicker1').val();
                        var end     = $('#datepicker2').val();
                        $('#currentVillage').text($(this).data('name'));
                        var m_village = $(this).data('id');
                        localStorage.setItem("vil", m_village);

                        // set previously selected
                        if (localStorage.getItem("dis")) {
                            $('.list_village[data-id="'+localStorage.getItem("dis")+'"]').click();
                        }
                    });
                }
            });
        });
    });
});
</script>