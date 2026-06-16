
$(function() {
    $.get(m_api+'/mill/get_mill_profile?PID='+m_PID+'&year='+m_year, function(data) {
        if (data) {
            var td = '';
            var jml = 0;
            $.each(data.supplier, function(index, val) {
                td += '<tr><td>'+val.MRoleNames+'</td><td>'+val.jml_supplier+'</td><td>'+val.ttp+' %</td></tr>';

                jml += parseInt(val.jml_supplier);
            });
            $("#summary_ttp").html(data.total_ttp);
            $('#datacategorysupplier').html(td);
            $('#box4').html(jml);
            $('#box5').html(data.mapped.mapped_farmer);
            $('#jml_kebun').html(data.mapped.jml_kebun);
            $('#luas_kebun').html(data.mapped.luas_kebun);
            $('#jml_kebun_pemasok').html(data.pemasok.jml_kebun_sme);
            $('#luas_kebun_pemasok').html(data.pemasok.luas_kebun_sme);
            $('#MillDisplayID').html(data.basic.MillDisplayID);
            $('#MillName').html(data.basic.MillName);
            $('#Year').html(data.basic.Year);
            $('#Status').html(data.basic.Status);
            $('#staffNr').html(data.basic.staffNr);
            $('#Province').html(data.basic.Province);
            $('#District').html(data.basic.District);
            $('#Subdistrict').html(data.basic.SubDistrict);
            $('#Village').html(data.basic.Village);
            $('#ProductionCapacity').html(data.basic.Capacity);
            $('#Latitude').html(data.basic.Latitude);
            $('#Longitude').html(data.basic.Longitude);
            $('#imageMill').html('<img style="height:150px" src="'+m_url+'/'+data.basic.Logo+'"/>');
            $("#approved_by").html(data.approvedby);
        }
    });

    
    $.get(m_api+'/dashboard/year_list/', function(data) {
        if (data) {
            var li = '<li><a href="#" class="list_year" data-id="" data-name="'+m_year>+'">'+m_year>+'</a></li>';
                $('#year').append(li);
            $.each(data, function(index, val) {
                var li = '<li><a href="#" class="list_year" data-id="'+val.id+'" data-name="'+val.name+'">'+val.name+'</a></li>';
                $('#year').append(li);
            });
            // set previously selected
            if (m_year) {
                $('.list_year[data-id="'+m_year+'"]').click();
            }
        }
    });
});