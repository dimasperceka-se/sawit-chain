var ajaxDataRenderer = function(url) {
    var arrReturn = {};
    $('#wrapper').addClass('cover');

    var idMill = m_mill;
    var mill = $('#currentMill').val();
    var startdate = $('#startdate').val();
    var enddate = $('#enddate').val();
    
    console.log(idMill);
    console.log(mill);

    $.ajax({
        type: "GET",
        url: url,
        data: { 
                idMill: idMill, 
                mill: mill, 
                startdate: startdate, 
                enddate: enddate
            },
        contentType: "application/json; charset=utf-8",
        dataType: "json",
        async: false,
        success: function(r) {
            console.log(r);
            //data display
            $('#number_off_total_input_oil').html(number_format(r.box.number_off_total_input_oil,3,'.',','));
            $('#number_of_total_cpo').html(number_format(r.box.number_of_total_cpo,3,'.',','));
            $('#number_of_total_pko').html(number_format(r.box.number_of_total_pko,3,'.',','));
            $('#number_of_transaction').html(number_format(r.box.number_of_transaction,0,'.',','));

            if(r.box.number_of_transaction != '0'){
                var details = '<li>'
                +'<a class="link_mill" data-type="total_mill" href="#">'
                +'<span class="label">'+r.box.name+'</span>'
                +'<span class="value" id="total_mill_val">'+r.box.number_of_transaction+'</span>'
                +'</a>'
                +'</li>';
                
                $('#transaction_details').html(details);
            } else {
                var childMill = r.dataDisplay.dataMillName;

                var details = '<li>'
                +'<a class="link_mill" data-type="total_mill" href="#">'
                +'<span class="label">'+childMill+'</span>'
                +'<span class="value" id="total_mill_val">'+r.box.number_of_transaction+'</span>'
                +'</a>'
                +'</li>';
                
                $('#transaction_details').html(details);
            }
            
            //line chart Refinery Per Month
            $(document).ready(function() {
                var title = {
                    text: lang('Number Of Refinery Transaction Per Month')   
                };
                var subtitle = {
                    // text: 'Source: worldClimate.com'
                };
                var xAxis = {
                    categories: ['January', 'February', 'March', 'April', 'May', 'June',
                        'July', 'August', 'September', 'October', 'November', 'December']
                };
                var yAxis = {
                    title: {
                        // text: 'Temperature (\xB0C)'
                    },
                    plotLines: [{
                        value: 0,
                        width: 1,
                        color: '#808080'
                    }]
                };   
                var tooltip = {
                    valueSuffix: '',
                    pointFormat: "Value: {point.y:.1f}"
                }
                var legend = {
                    layout: 'horizontal',
                    align: 'center',
                    verticalAlign: 'bottom',
                    borderWidth: 0
                };
                var series =  [{        
                        name: 'REFINERY',
                        data: [r.line.number_refinery_january
                            ,r.line.number_refinery_february
                            ,r.line.number_refinery_march
                            ,r.line.number_refinery_april
                            ,r.line.number_refinery_may
                            ,r.line.number_refinery_june
                            ,r.line.number_refinery_july
                            ,r.line.number_refinery_august
                            ,r.line.number_refinery_september
                            ,r.line.number_refinery_october
                            ,r.line.number_refinery_november
                            ,r.line.number_refinery_december
                        ],
                        color: "#95130b"
                    }
                ];
            
                var json = {};
                json.title = title;
                json.subtitle = subtitle;
                json.xAxis = xAxis;
                json.yAxis = yAxis;
                json.tooltip = tooltip;
                json.legend = legend;
                json.series = series;
                
                $('#number_off_refinery_transaction').highcharts(json);
            
            });
            //end line chart Refinery Per Month

            //line chart oil Input Per Month
            $(document).ready(function() {
                var title = {
                    text: lang('Number Of Oil Input Per Month')   
                };
                var subtitle = {
                    // text: 'Source: worldClimate.com'
                };
                var xAxis = {
                    categories: ['January', 'February', 'March', 'April', 'May', 'June',
                        'July', 'August', 'September', 'October', 'November', 'December']
                };
                var yAxis = {
                    title: {
                        // text: 'Temperature (\xB0C)'
                    },
                    plotLines: [{
                        value: 0,
                        width: 1,
                        color: '#808080'
                    }]
                };   
                var tooltip = {
                    valueSuffix: ''
                }
                var legend = {
                    layout: 'horizontal',
                    align: 'center',
                    verticalAlign: 'bottom',
                    borderWidth: 0
                };
                var series =  [{        
                        name: 'OIL',
                        data: [r.line.number_oil_january
                            ,r.line.number_oil_february
                            ,r.line.number_oil_march
                            ,r.line.number_oil_april
                            ,r.line.number_oil_may
                            ,r.line.number_oil_june
                            ,r.line.number_oil_july
                            ,r.line.number_oil_august
                            ,r.line.number_oil_september
                            ,r.line.number_oil_october
                            ,r.line.number_oil_november
                            ,r.line.number_oil_december
                        ],
                        color: "#95130b"
                    }
                ];
            
                var json = {};
                json.title = title;
                json.subtitle = subtitle;
                json.xAxis = xAxis;
                json.yAxis = yAxis;
                json.tooltip = tooltip;
                json.legend = legend;
                json.series = series;
                
                $('#number_off_oil_transaction').highcharts(json);
            
            });
            //end line chart Refinery Per Month
           
            //line chart oil with data
            $(document).ready(function() {
                var title = {
                    text: lang('Oil Production')   
                };
                var subtitle = {
                    // text: 'Source: worldClimate.com'
                };
                var xAxis = {
                    categories: ['January', 'February', 'March', 'April', 'May', 'June',
                        'July', 'August', 'September', 'October', 'November', 'December']
                };
                var yAxis = {
                    title: {
                        // text: 'Temperature (\xB0C)'
                    },
                    plotLines: [{
                        value: 0,
                        width: 1,
                        color: '#808080'
                    }]
                };   
                var tooltip = {
                    valueSuffix: ''
                }
                var legend = {
                    layout: 'horizontal',
                    align: 'center',
                    verticalAlign: 'bottom',
                    borderWidth: 0
                };
                var series =  [{
                        name: 'PKO',
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
                    }, 
                    {
                        name: 'CPO',
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
                    }
                ];
            
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

