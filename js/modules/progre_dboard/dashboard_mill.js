function gauge(div, title, data){
    var gaugeOptions = {
        chart: {
            type: 'solidgauge',
            renderTo: div
        },

        title: {
            text: title
        },

        pane: {
            center: ['50%', '80%'],
            size: '140%',
            startAngle: -90,
            endAngle: 90,
            background: {
                backgroundColor: (Highcharts.theme && Highcharts.theme.background2) || '#535353',
                innerRadius: '60%',
                outerRadius: '100%',
                shape: 'arc'
            }
        },

        tooltip: {
            enabled: true,
            followPointer: true,
            valueDecimals: 3,
        },

        plotOptions: {
            solidgauge: {
                dataLabels: {
                    y: 5,
                    borderWidth: 0,
                    useHTML: true
                }
            }
        }
    };

    var percent = (data[0].data/data[0].max);
    
    data[0].percent_value = (data[0].data > 0 && data[0].max > 0) ? percent : 0;
    var max_label = 0;
        max_label = number_format(data[0].max,3,'.',',');
    new Highcharts.Chart(Highcharts.merge(gaugeOptions, {
        yAxis: [
        {
            stops: [
                [0.1, '#95130b'], // green
                [0.5, '#95130b'], // yellow
                [0.9, '#95130b'] // red
            ],
            min: 0,
            max: data[0].max,
            lineWidth: 0,
            minorTickInterval: 1,
            tickPixelInterval: 1,
            tickAmount: 1,
            tickWidth: 1,
            title: {
                text: "0",
                y: 140,
                x: -185,
            },
            
            showFirstLabel:false,
            showLastLabel:false,
        }, {
            stops: [
                [0.1, '#6BCD0A'], // green
                [0.5, '#6BCD0A'], // yellow
                [0.9, '#6BCD0A'] // red
            ],
            min: 0,
            max: data[0].max,
            lineWidth: 0,
            minorTickInterval: 1,
            tickPixelInterval: 1,
            tickAmount: 1,
            tickWidth: 1,
            title: {
                text: max_label,
                y: 140,
                x: 190,
            },
            showFirstLabel:false,
            showLastLabel:false,
        }],
        
        series: [{
            name: data[0].name,
            data: [10],
            yAxis: 0,
            dataLabels: {
                x: 0,
                allowOverlap: true,
                y: -50,
                format: '<div style="text-align:center"><span style="font-size:24px;color:' +
                ((Highcharts.theme && Highcharts.theme.contrastTextColor) || '#666666') + '">'+number_format(data[0].percent_value,3,'.',',')+' %</span><br/>'
            },
            innerRadius:'60%',
            radius: '100%',
            tooltip: {
                pointFormat: '<span style="color:{point.color}">\u25CF</span> {series.name}: <b>'+number_format(data[0].data,3,'.',',')+'</b><br/>',
            }
        }]

    }));
}
var ajaxDataRenderer = function(url) {
    var arrReturn = {};
    $('#wrapper').addClass('cover');

    var groupMill = $('#groupMill').val();
    var mill =  $('#mill').val();
    var startdate = $('#startdate').val();
    var enddate = $('#enddate').val();

    $.ajax({
        type: "GET",
        url: url,
        data: {mill: mill, groupMill: groupMill, startdate: startdate, enddate: enddate},
        contentType: "application/json; charset=utf-8",
        dataType: "json",
        async: false,
        success: function(r) {
            console.log(r);
            if(r.validation.pko == 'empty'){
                document.getElementById("box_pko").style.display = "none";
                document.getElementById("gauge_pko_delivered").style.display = "none";
            } 

            if(r.validation.cpo == 'empty'){
                document.getElementById("box_cpo").style.display = "none";
                document.getElementById("gauge_cpo_delivered").style.display = "none";
            } 

            $('#number_total_cpo').html(number_format(r.box.number_total_cpo,2,'.',','));
            $('#number_total_pko').html(number_format(r.box.number_total_pko,2,'.',','));
            
            //data display
            $('#number_off_ffb_input').html(number_format(r.box.number_of_ffb_input,2,'.',','));
            $('#number_traceable_farmer').html(number_format(r.box.number_traceable_farmer,0,'.',','));
            $('#number_of_transaction').html(number_format(r.box.number_of_transaction,0,'.',','));
            $('#number_processing_result').html(number_format(r.box.number_processing_result,2,'.',','));
            $('#number_of_dispatch').html(number_format(r.box.number_of_dispatch,0,'.',','));
            $('#number_average_of_production').html(number_format(r.box.number_average_of_production,2,'.',','));
            
            //data gauge chart
            gauge_single('number_gauge_cpo_delivered', lang('CPO delivered'), [{max: 2000, data: r.dataDisplay.number_gauge_total_cpo, name: lang('CPO delivered')}]);
            
            gauge_single('number_gauge_pko_delivered', lang('PK delivered'), [{max: 2000, data: r.dataDisplay.number_gauge_total_pko, name: lang('PK delivered')}]);

            //pie chart cpo with data
            $(document).ready(function() {
                var chart = {
                    plotBackgroundColor: null,
                    plotBorderWidth: null,
                    plotShadow: false
                };
                var title = {
                    text: 'CPO'   
                };      
                var tooltip = {
                    pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
                };
                var plotOptions = {
                    pie: {
                        colors: [
                            '#7CB5EC',
                            '#95130b'
                        ],
                        allowPointSelect: true,
                        cursor: 'pointer',
                        
                        dataLabels: {
                            enabled: false           
                        },
                        
                        showInLegend: true
                    }
                };
                var series = [{
                    type: 'pie',
                    name: 'Browser share',
                    data: [
                        ['Traceable', r.pie.number_traceability_cpo],
                        ['Non-Traceable', r.pie.number_nontraceability_cpo],
                    ]
                }];     

                var json = {};   
                json.chart = chart; 
                json.title = title;     
                json.tooltip = tooltip;  
                json.series = series;
                json.plotOptions = plotOptions;

                $('#number_bar_cpo').highcharts(json);  
                
            });
            //end pie chart cpo with data

            //pie chart pko with data
            $(document).ready(function() {
                var chart = {
                    plotBackgroundColor: null,
                    plotBorderWidth: null,
                    plotShadow: false
                };
                var title = {
                    text: lang('Transaction Traceability')   
                };      
                var tooltip = {
                    pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
                };
                var plotOptions = {
                    pie: {
                        colors: [
                            '#7CB5EC',
                            '#95130b'
                        ],
                        allowPointSelect: true,
                        cursor: 'pointer',
                        
                        dataLabels: {
                            enabled: false           
                        },
                        
                        showInLegend: true
                    }
                };
                var series = [{
                    type: 'pie',
                    name: 'Browser share',
                    data: [
                        ['Traceable', r.pie.number_traceability_pko],
                        ['Non-Traceable', r.pie.number_nontraceability_pko]
                    ]
                }];     
                
                var json = {};   
                json.chart = chart; 
                json.title = title;     
                json.tooltip = tooltip;  
                json.series = series;
                json.plotOptions = plotOptions;

                $('#number_bar_pko').highcharts(json);  

            });
            //end pie chart pko with data

            //line chart oil with data
            $(document).ready(function() {
                var title = {
                    text: lang('Oil Production')   
                };
                var subtitle = {
                };
                var xAxis = {
                    categories: ['January', 'February', 'March', 'April', 'May', 'June',
                        'July', 'August', 'September', 'October', 'November', 'December']
                };
                var yAxis = {
                    title: {
                    },
                    plotLines: [{
                        value: 0,
                        width: 1,
                        color: '#808080'
                    }]
                };   
                var tooltip = {
                    valueSuffix: '',
                    pointFormat: "Value: {point.y:.2f}"
                }
                var legend = {
                    layout: 'horizontal',
                    align: 'center',
                    verticalAlign: 'bottom',
                    borderWidth: 0
                };

                var labelNamePk = 'PK';
                var labelNameCpo = 'CPO';

                if(r.validation.pko == 'empty'){
                    console.log('series');
                    var series =  [{
                        name: labelNameCpo,
                        data: [r.line.number_cpo_january
                            ,r.line.number_cpo_february
                            ,r.line.number_cpo_march
                            ,r.line.number_cpo_april
                            ,r.line.number_cpo_may
                            ,r.line.number_cpo_june
                            ,r.line.number_cpo_july
                            ,r.line.number_cpo_august
                            ,r.line.number_cpo_september
                            ,r.line.number_cpo_october
                            ,r.line.number_cpo_november
                            ,r.line.number_cpo_december
                        ],
                        color: "#95130b"   
                    }];
                }

                if(r.validation.cpo == 'empty'){
                    var series =  [{
                        name: labelNamePk,
                        data: [r.line.number_pko_january
                            ,r.line.number_pko_february
                            ,r.line.number_pko_march
                            ,r.line.number_pko_april
                            ,r.line.number_pko_may
                            ,r.line.number_pko_june
                            ,r.line.number_pko_july
                            ,r.line.number_pko_august
                            ,r.line.number_pko_september
                            ,r.line.number_pko_october
                            ,r.line.number_pko_november
                            ,r.line.number_pko_december
                            ],
                        color: "#7CB5EC"
                    }];   
                }

                if(r.validation.cpo == 'notempty' && r.validation.pko == 'notempty'){
                    var series =  [{
                        name: labelNameCpo,
                        data: [r.line.number_cpo_january
                            ,r.line.number_cpo_february
                            ,r.line.number_cpo_march
                            ,r.line.number_cpo_april
                            ,r.line.number_cpo_may
                            ,r.line.number_cpo_june
                            ,r.line.number_cpo_july
                            ,r.line.number_cpo_august
                            ,r.line.number_cpo_september
                            ,r.line.number_cpo_october
                            ,r.line.number_cpo_november
                            ,r.line.number_cpo_december
                        ],
                        color: "#95130b"   
                    },{
                        name: labelNamePk,
                        data: [r.line.number_pko_january
                                ,r.line.number_pko_february
                                ,r.line.number_pko_march
                                ,r.line.number_pko_april
                                ,r.line.number_pko_may
                                ,r.line.number_pko_june
                                ,r.line.number_pko_july
                                ,r.line.number_pko_august
                                ,r.line.number_pko_september
                                ,r.line.number_pko_october
                                ,r.line.number_pko_november
                                ,r.line.number_pko_december
                                ],
                            color: "#7CB5EC"
                    }];
                }
                
                var json = {};
                json.title = title;
                json.subtitle = subtitle;
                json.xAxis = xAxis;
                json.yAxis = yAxis;
                json.tooltip = tooltip;
                json.legend = legend;
                json.series = series;
                
                $('#number_oil_line_production').highcharts(json);
            });
            //end line chart oil with data

            $('#wrapper').removeClass('cover');
            document.getElementById('row-fluid').style.display='';
            $(".dashDateGen").html('Generated on '+r.dataDisplay.DateGenerated);
        }
    });
};

var arrReturn = ajaxDataRenderer(m_data);

