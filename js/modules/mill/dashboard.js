
$(function() {
    $.get(m_api+'/mill/get_mill_dashboard?PID='+m_PID, function(data) {
        if (data) {
            var jml = 0;
            var details = '';
            $.each(data.supplier, function(index, val) {
                jml += parseInt(val.jml_supplier);

                details += '<li>'
                +'<a class="link_mill" data-type="total_unmapped_garden_area" href="#">'
                +'<span class="label">'+val.MRoleNames+'</span>'
                +'<span class="value" id="total_unmapped_garden_area_val">'+val.jml_supplier+'</span>'
                +'</a>'
                +'</li>';
            });
            var sup_detail = '';
            $.each(data.supplier_detail, function(index, val) {
                sup_detail += '<tr>'
                +'<td>'+val.SupplierID+'</td>'
                +'<td>'+val.SPBCode+'</td>'
                +'<td>'+val.SupplierName+'</td>'
                +'<td>'+val.Alias+'</td>'
                +'<td>'+val.SupplierType+'</td>'
                +'<td>'+val.Village+'</td>'
                +'<td>'+val.SubDistrict+'</td>'
                +'<td>'+val.District+'</td>'
                +'<td>'+val.Latitude+'</td>'
                +'<td>'+val.Longitude+'</td>'
                +'<td>'+val.LuasKebun+'</td>'
                +'<td>'+val.TotalFarmer+'</td>'
                +'<td>'+val.TotalKebun+'</td>'
                +'</tr>';
            });
            $('#supplier_detail_list').html(sup_detail);
            $('#supplier_detail_dropdown').html(details);
            $('#supplier_detail').html(jml);
            $('#registered_farmer').html(data.farmer.registered_farmer);
            $('#mapped_farmer_val').html(data.farmer.mapped_farmer);
            $('#unmapped_farmer_val').html(data.farmer.unmapped_farmer);
            $('#total_mapped_garden_val').html(data.farmer.garden_mapped);
            $('#total_unmapped_garden_val').html(data.farmer.garden_unmapped);
            $('#total_mapped_garden_area_val').html(data.farmer.garden_area_mapped);
            $('#total_unmapped_garden_area_val').html(data.farmer.garden_area_unmapped);
        }
    });
    
    get_mill_transaction(m_PID,m_awal,m_akhir);

    $.get(m_api+'/dashboard/year_list/', function(data) {
        if (data) {
            $.each(data, function(index, val) {
                var selected = '';
                if(val.id == m_awal){
                    selected = 'selected';
                }
                var li = '<option '+selected+' val="'+val.id+'">'+val.name+'</option>';
                $('#start_date').append(li);
            });

            $.each(data, function(index, val) {
                var selected = '';
                if(val.id == m_akhir){
                    selected = 'selected';
                }
                var li2 = '<option '+selected+' val="'+val.id+'">'+val.name+'</option>';
                $('#end_date').append(li2);
            });
        }
    });
});

$('.widget-download-list .widget-head').on('click', function (event) {
    event.preventDefault();
    /* Act on the event */
    $list = $($(this).parent().find('.widget-list')[0]);
    if ($list.hasClass('expanded')) {
        $list.removeClass('expanded');
        $list.addClass('colapsed');
    } else {
        $list.addClass('expanded');
        $list.removeClass('colapsed');
    }
});

function get_mill_transaction(PID,start,end){
    $.get(m_api+'/mill/get_mill_transaction?PID='+PID+'&start='+start+'&end='+start, function(data) {
        if (data) {
            var datatable = '';
            $.each(data.transaction, function(index, val) {
                datatable += '<tr><td>'+val.MRoleNames+'</td><td>'+val.JmlTransaksi+'</td><td>'+val.JmlKebun+'</td><td>'+val.LuasKebun+'</td><td>'+val.Tonase+'</td></tr>';
            });
            $("#supplier_transaction_data").html(datatable);            

            new Highcharts.Chart({
                chart: {
                    renderTo:'volume_chart',
                    type: 'line'
                },
                title: {
                    text: lang('Volume(Ton) Supplybase Bulanan')
                },
                xAxis: {
                    categories: data.header
                },
                yAxis: {
                    title: {
                        text: lang('Ton')
                    }
                },
                plotOptions: {
                    line: {
                        dataLabels: {
                            enabled: true
                        },
                        enableMouseTracking: false
                    }
                },
                tooltip: {
                    // pointFormat: '{series.name} : <b>{point.y:,.0f}</b> kg<br>',
                    // shared:true,
                    // split:false,
                    // crosshairs: true
                    formatter: function () {
                        var s = "<table style='border:1px solid #666;padding:5px'>"
                
                        $.each(this.points, function () {
                            s += "<tr><td><li style='list-style-type:square;font-size:15pt;color:"+this.series.color+";padding-left:10px;border-bottom: 1px solid #666;padding-bottom:5px'></li>"+
                                "</td><td style=\"padding:5px;border:1px solid #666\">"+this.series.name+": </td>" +
                                "<td style=\"padding:5px;border:1px solid #666\"><b>"+this.y+"</b></td></tr>";
                        });
                        s += "</table>"
                        return s;
                    },
                    shared: true,
                    useHTML: true
                },
                series: data.grafik
            });
        }
    });
}

function setRange(){
    var start_date =    $("#start_date").val();
    var end_date =      $("#start_date").val();

    get_mill_transaction(m_PID,start_date,end_date);
}