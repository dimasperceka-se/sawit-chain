/*
* @Author: nikolius
* @Date:   2017-09-15 15:40:25
* @Last Modified by:   nikolius
* @Last Modified time: 2017-11-27 10:54:53
*/
$(document).on('change', '#fprovince', function(e) {
    //load district
    $.ajax({
        type: "GET",
        url: m_api+'/common/combo_district_access',
        contentType: "application/json; charset=utf-8",
        dataType: "json",
        async: false,
        data: {ProvinceID: e.target.value},
        success: function (data) {
            if (data.length > 0) {
                $('#fdistrict').find('option').remove().end().append('<option value="all_district">'+lang('All District')+'</option>');
                $.each(data, function(index, val) {
                    $('#fdistrict').append('<option value="'+val.id+'">'+val.label+'</option>');
                });
            } else {
                //tidak ada datanya
                $('#fdistrict').find('option').remove().end().append('<option value="all_district">'+lang('All District')+'</option>');
            }
        },
        error: function(data) {
            //tidak ada datanya
            $('#fdistrict').find('option').remove().end().append('<option value="all_district">'+lang('All District')+'</option>');
        }
    });
});

function runSearch() {
    let ftype = $("#ftype").val();
    let fprovince = $("#fprovince").val();
    let fdistrict = $("#fdistrict").val();

    $.ajax({
        type: "GET",
        url: m_data,
        data: {prov: fprovince,kab: fdistrict,ftype: ftype},
        contentType: "application/json; charset=utf-8",
        dataType: "json",
        async: false,
        success: function(r) {
            console.log(r);

            //prep variable ================ (begin)
            var farmer_baseline = r.dataDisplay.farmer_baseline;
            if(farmer_baseline != "-") farmer_baseline = number_format(farmer_baseline,0,'.',',');

            var farmer_postline = r.dataDisplay.farmer_postline;
            if(farmer_postline != "-") farmer_postline = number_format(farmer_postline,0,'.',',');

            var plantation_baseline = r.dataDisplay.plantation_baseline;
            if(plantation_baseline != "-") plantation_baseline = number_format(plantation_baseline,0,'.',',');

            var plantation_postline = r.dataDisplay.plantation_postline;
            if(plantation_postline != "-") plantation_postline = number_format(plantation_postline,0,'.',',');

            var productivity_baseline = r.dataDisplay.productivity_baseline;
            if(productivity_baseline != "-") productivity_baseline = number_format(productivity_baseline,1,'.',',');

            var productivity_postline = r.dataDisplay.productivity_postline;
            if(productivity_postline != "-") productivity_postline = number_format(productivity_postline,1,'.',',');

            var productivity_per_tree_baseline = r.dataDisplay.productivity_per_tree_baseline;
            if(productivity_per_tree_baseline != "-") productivity_per_tree_baseline = number_format(productivity_per_tree_baseline,0,'.',',');

            var productivity_per_tree_postline = r.dataDisplay.productivity_per_tree_postline;
            if(productivity_per_tree_postline != "-") productivity_per_tree_postline = number_format(productivity_per_tree_postline,0,'.',',');
            //prep variable ================ (begin)

            //data display
            $('#box_farmer_baseline').html(farmer_baseline);
            $('#box_farmer_postline').html(farmer_postline);
            $('#box_plantation_baseline').html(plantation_baseline);
            $('#box_plantation_postline').html(plantation_postline);
            $('#box_productivity_baseline').html(productivity_baseline);
            $('#box_productivity_postline').html(productivity_postline);
            $('#box_productivity_per_tree_baseline').html(productivity_per_tree_baseline);
            $('#box_productivity_per_tree_postline').html(productivity_per_tree_postline);

            //console.log(r.dataChart.YearNameCate);
            arrReturn.YearNameCate = r.dataChart.YearNameCate;
            arrReturn.GardenYearData = r.dataChart.GardenYearData;
            arrReturn.BarChartProductivity = r.dataChart.BarChartProductivity;


            //bar_garden_per_year ================================================================ (begin)
            var dataYearCate = [];
            $.each(arrReturn.YearNameCate.name, function(key, value) {
                dataYearCate[key] = {name: value, categories: arrReturn.YearNameCate.categories};
            });

            var barGardenYear = [];
            $.each(arrReturn.GardenYearData, function(key, value) {
                var dataBasePost = [];

                var increObj = 0;
                for(var objBasePost in value) {
                    if(objBasePost == "label") continue;
                    dataBasePost[increObj] = parseInt(value[objBasePost]);
                    increObj++;
                }

                barGardenYear[key] = {name: value.label, data: dataBasePost};
            });
            //console.log(barGardenYear);

            //define
            column(barGardenYear,'bar_garden_per_year', lang('Oil Palm Plantation Surveys'), lang('Surveys/Year'), null,dataYearCate,'normal',0,true,-90);
            //console.log(barGardenYear);
            //console.log(dataYearCate);
            //bar_garden_per_year ================================================================ (end)

            //prep variable
            var BarRegion = [];
            var ProdBase = [];
            var ProdPost = [];
            var TreeProdBase = [];
            var TreeProdPost = [];
            $.each(arrReturn.BarChartProductivity, function(key, value) {
                BarRegion[key] = [value.label];
                ProdBase[key] = parseFloat(value.productivity_baseline);
                ProdPost[key] = parseFloat(value.productivity_postline);
                TreeProdBase[key] = parseFloat(value.productivity_per_tree_baseline);
                TreeProdPost[key] = parseFloat(value.productivity_per_tree_postline);
            });

            //bar_average_productivity && bar_average_tree_productivity ====================================================== (begin)
            column([{name: lang('Baseline'),data: ProdBase, stack:'Baseline'},{name: lang('Post-Line'),data: ProdPost, stack:'Post-Line'}], 'bar_average_productivity',lang('Average Oil Palm Plantation Yield'), lang('Mt/Ha/Year'), ['#95130b','#99884C'], BarRegion,'normal',1,true);

            column([{name: lang('Baseline'),data: TreeProdBase, stack:'Baseline'},{name: lang('Post-Line'),data: TreeProdPost, stack:'Post-Line'}], 'bar_average_tree_productivity',lang('Average Oil Palm Tree Yield'), lang('Kg/Ha/Year'), ['#95130b','#99884C'], BarRegion,'normal',1,true);
            //bar_average_productivity && bar_average_tree_productivity ====================================================== (end)

            $('#wrapper').removeClass('cover');
            document.getElementById('row-fluid').style.display='';
            $(".dashDateGen").html('Generated on '+r.dataDisplay.DateGenerated);
        }
    });
}

var ajaxDataRenderer = function(url) {
    var arrReturn = {};
    $('#wrapper').addClass('cover');

    //load combo propinsi
    $.ajax({
        type: "GET",
        url: m_api+'/common/combo_propinsi_access',
        contentType: "application/json; charset=utf-8",
        dataType: "json",
        async: false,
        success: function (data) {
            if (data.length > 0) {
                $('#fprovince').find('option').remove().end().append('<option value="all_province">'+lang('All Province')+'</option>');
                $.each(data, function(index, val) {
                    $('#fprovince').append('<option value="'+val.id+'">'+val.label+'</option>');
                });
            }
        }
    });

    let ftype = $("#ftype").val();
    let fprovince = $("#fprovince").val();
    let fdistrict = $("#fdistrict").val();

    $.ajax({
        type: "GET",
        url: url,
        data: {prov: fprovince,kab: fdistrict,ftype: ftype},
        contentType: "application/json; charset=utf-8",
        dataType: "json",
        async: false,
        success: function(r) {
            console.log(r);

            //prep variable ================ (begin)
            var farmer_baseline = r.dataDisplay.farmer_baseline;
            if(farmer_baseline != "-") farmer_baseline = number_format(farmer_baseline,0,'.',',');

            var farmer_postline = r.dataDisplay.farmer_postline;
            if(farmer_postline != "-") farmer_postline = number_format(farmer_postline,0,'.',',');

            var plantation_baseline = r.dataDisplay.plantation_baseline;
            if(plantation_baseline != "-") plantation_baseline = number_format(plantation_baseline,0,'.',',');

            var plantation_postline = r.dataDisplay.plantation_postline;
            if(plantation_postline != "-") plantation_postline = number_format(plantation_postline,0,'.',',');

            var productivity_baseline = r.dataDisplay.productivity_baseline;
            if(productivity_baseline != "-") productivity_baseline = number_format(productivity_baseline,1,'.',',');

            var productivity_postline = r.dataDisplay.productivity_postline;
            if(productivity_postline != "-") productivity_postline = number_format(productivity_postline,1,'.',',');

            var productivity_per_tree_baseline = r.dataDisplay.productivity_per_tree_baseline;
            if(productivity_per_tree_baseline != "-") productivity_per_tree_baseline = number_format(productivity_per_tree_baseline,0,'.',',');

            var productivity_per_tree_postline = r.dataDisplay.productivity_per_tree_postline;
            if(productivity_per_tree_postline != "-") productivity_per_tree_postline = number_format(productivity_per_tree_postline,0,'.',',');
            //prep variable ================ (begin)

            //data display
            $('#box_farmer_baseline').html(farmer_baseline);
            $('#box_farmer_postline').html(farmer_postline);
            $('#box_plantation_baseline').html(plantation_baseline);
            $('#box_plantation_postline').html(plantation_postline);
            $('#box_productivity_baseline').html(productivity_baseline);
            $('#box_productivity_postline').html(productivity_postline);
            $('#box_productivity_per_tree_baseline').html(productivity_per_tree_baseline);
            $('#box_productivity_per_tree_postline').html(productivity_per_tree_postline);

            //console.log(r.dataChart.YearNameCate);
            arrReturn.YearNameCate = r.dataChart.YearNameCate;
            arrReturn.GardenYearData = r.dataChart.GardenYearData;
            arrReturn.BarChartProductivity = r.dataChart.BarChartProductivity;


            //bar_garden_per_year ================================================================ (begin)
            var dataYearCate = [];
            $.each(arrReturn.YearNameCate.name, function(key, value) {
                dataYearCate[key] = {name: value, categories: arrReturn.YearNameCate.categories};
            });

            var barGardenYear = [];
            $.each(arrReturn.GardenYearData, function(key, value) {
                var dataBasePost = [];

                var increObj = 0;
                for(var objBasePost in value) {
                    if(objBasePost == "label") continue;
                    dataBasePost[increObj] = parseInt(value[objBasePost]);
                    increObj++;
                }

                barGardenYear[key] = {name: value.label, data: dataBasePost};
            });
            //console.log(barGardenYear);

            //define
            column(barGardenYear,'bar_garden_per_year', lang('Oil Palm Plantation Surveys'), lang('Surveys/Year'), null,dataYearCate,'normal',0,true,-90);
            //console.log(barGardenYear);
            //console.log(dataYearCate);
            //bar_garden_per_year ================================================================ (end)

            //prep variable
            var BarRegion = [];
            var ProdBase = [];
            var ProdPost = [];
            var TreeProdBase = [];
            var TreeProdPost = [];
            $.each(arrReturn.BarChartProductivity, function(key, value) {
                BarRegion[key] = [value.label];
                ProdBase[key] = parseFloat(value.productivity_baseline);
                ProdPost[key] = parseFloat(value.productivity_postline);
                TreeProdBase[key] = parseFloat(value.productivity_per_tree_baseline);
                TreeProdPost[key] = parseFloat(value.productivity_per_tree_postline);
            });

            //bar_average_productivity && bar_average_tree_productivity ====================================================== (begin)
            column([{name: lang('Baseline'),data: ProdBase, stack:'Baseline'},{name: lang('Post-Line'),data: ProdPost, stack:'Post-Line'}], 'bar_average_productivity',lang('Average Oil Palm Plantation Yield'), lang('Mt/Ha/Year'), ['#95130b','#99884C'], BarRegion,'normal',1,true);

            column([{name: lang('Baseline'),data: TreeProdBase, stack:'Baseline'},{name: lang('Post-Line'),data: TreeProdPost, stack:'Post-Line'}], 'bar_average_tree_productivity',lang('Average Oil Palm Tree Yield'), lang('Kg/Ha/Year'), ['#95130b','#99884C'], BarRegion,'normal',1,true);
            //bar_average_productivity && bar_average_tree_productivity ====================================================== (end)

            $('#wrapper').removeClass('cover');
            document.getElementById('row-fluid').style.display='';
            $(".dashDateGen").html('Generated on '+r.dataDisplay.DateGenerated);
        }
    });

    //return arrReturn;
};

var arrReturn = ajaxDataRenderer(m_data);