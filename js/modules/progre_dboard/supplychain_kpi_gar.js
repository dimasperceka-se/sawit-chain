/*
 * @Author: gitandi
 * @Date:   2019-06-27 12:50:17
 * @Last Modified by:   gitandi
 * @Last Modified time: 2019-06-27 12:50:17
 */
function runSearch() {
    $('#wrapper').addClass('cover');
    var fprovince = $("#fprovince").val();
    var fdistrict = $("#fdistrict").val();
    // console.log(url);

    $.ajax({
        type: "GET",
        url: m_data,
        data: {prov: fprovince, kab: fdistrict},
        contentType: "application/json; charset=utf-8",
        dataType: "json",
        async: false,
        success: function (r) {
            var dataForest = [];
            var html_forest = '';
            var total_forest = 0;
//            box_plantation_mapped
            //console.log(r);

            //data display
            $('#box_mill').html(number_format(r.data.jml_mill, 0, ',', '.'));
            $('#mill_province').html(number_format(r.data.mill_prov, 0, ',', '.'));
            $('#mill_district').html(number_format(r.data.mill_dis, 0, ',', '.'));

            var supplybase = parseInt(r.data.jml_inti) + parseInt(r.data.jml_plasma) + parseInt(r.data.jml_external) + parseInt(r.data.dealer) + parseInt(r.data.jml_smallholder);
            $('#box_supplybase').html(number_format(supplybase, 0, ',', '.'));
            $('#inti').html(lang('Count') + ': ' + number_format(r.data.jml_inti, 0, ',', '.') + ', ' +lang('Garden') + ': ' + number_format(r.data.garden_inti, 2, ',', '.') + ' Ha');
//            $('#garden_inti').html(number_format(r.data.garden_inti, 0, '.', ','));
            $('#plasma').html(lang('Count') + ': ' + number_format(r.data.jml_plasma, 0, ',', '.') + ', ' +lang('Garden') + ': ' + number_format(r.data.garden_plasma, 2, ',', '.') + ' Ha');
//            $('#garden_plasma').html(number_format(r.data.garden_plasma, 0, '.', ','));
            $('#external').html(lang('Count') + ': ' + number_format(r.data.jml_external, 0, ',', '.') + ', ' +lang('Garden') + ': ' + number_format(r.data.garden_external, 2, ',', '.') + ' Ha');
//            $('#garden_external').html(number_format(r.data.garden_external, 0, '.', ','));
            $('#dealer').html(lang('Count') + ': ' + number_format(r.data.dealer, 0, ',', '.'));
            $('#smallholder').html(lang('Count') + ': ' + number_format(r.data.jml_smallholder, 0, ',', '.') + ', ' +lang('Garden') + ': ' + number_format(r.data.garden_smallholder, 2, ',', '.') + ' Ha');
//            $('#garden_smallholder').html(number_format(r.data.garden_smallholder, 0, '.', ','));

            var farmer = parseInt(r.data.dav) + parseInt(r.data.jml_smallholder);
            $('#box_farmer').html(number_format(farmer, 0, ',', '.'));
            $('#dav').html(lang('Count') + ': ' + number_format(r.data.dav, 0, ',', '.') + ', ' +lang('Garden') + ': ' + number_format(r.data.garden_dav, 2, ',', '.') + ' Ha');
            $('#farmer_smallholder').html(lang('Count') + ': ' + number_format(r.data.jml_smallholder, 0, ',', '.') + ', ' +lang('Garden') + ': ' + number_format(r.data.garden_smallholder, 2, ',', '.') + ' Ha');

            var untraceble = parseInt(r.data.farmer_registered) - parseInt(r.data.farmer_traceble);
            $('#box_farmer_registered').html(number_format(r.data.farmer_registered, 0, ',', '.'));
            $('#Traceble').html(lang('Count') + ': ' + number_format(r.data.farmer_traceble, 0, ',', '.') + ', ' +lang('Garden') + ': ' + number_format(r.data.garden_traceble, 2, ',', '.') + ' Ha');
            $('#Untraceble').html(lang('Count') + ': ' + number_format(untraceble, 0, ',', '.') + ', ' +lang('Garden') + ': ' + number_format(r.data.garden_untraceble, 2, ',', '.') + ' Ha');

            dataForest = r.dataForest;
            dataForest.forEach(function (item, index) {
                total_forest += parseInt(item.GardenCount);
                var value = lang('Count') + ': ' + number_format(item.GardenCount, 0, ',', '.') + ', ' +lang('Garden') + ': ' + number_format(item.AreaHa, 2, ',', '.') + ' Ha';
                html_forest += '<li><a class="link_forest" data-type="' + item.AreaName + '" href="#"><span class="label">' + lang(item.AreaName) + '</span><span class="value" id="' + item.AreaName + '">' + value + '</span> </a></li>';
            });
            $('#box_forest').html(number_format(total_forest, 0, ',', '.'));
            $('#list_forest').html(html_forest);

            $('#wrapper').removeClass('cover');
            document.getElementById('row-fluid').style.display = '';
            $(".dashDateGen").html('Generated on ' + r.data.DateGenerated);
        }
    });

}

$(document).on('change', '#fprovince', function (e) {
    //load district
    $.ajax({
        type: "GET",
        url: m_api + '/dashboard/region',
        contentType: "application/json; charset=utf-8",
        dataType: "json",
        async: false,
        data: {prov: e.target.value, daer: m_daer},
        success: function (data) {
            if (data.data) {
                $('#fdistrict').find('option').remove().end().append('<option value="all_district">' + lang('All District') + '</option>');
                $.each(data.data, function (index, val) {
                    $('#fdistrict').append('<option value="' + val.id + '">' + val.name + '</option>');
                });
            } else {
                //tidak ada datanya
                $('#fdistrict').find('option').remove().end().append('<option value="all_district">' + lang('All District') + '</option>');
            }
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

$(function () {
    //Load combo province pertama kali
    $.ajax({
        type: "GET",
        url: m_api + '/dashboard/region',
        contentType: "application/json; charset=utf-8",
        dataType: "json",
        async: false,
        data: {daer: m_daer},
        success: function (data) {
            if (data.data) {
                $('#fprovince').find('option').remove().end().append('<option value="all_province">' + lang('All Province') + '</option>');
                $.each(data.data, function (index, val) {
                    $('#fprovince').append('<option value="' + val.id + '">' + val.name + '</option>');
                });
            }
        }
    });

    //Langsung jalankan search pertama kali
    runSearch();
});