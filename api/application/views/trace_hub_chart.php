 
 <style>
   #pie1{ height:400px; border:1px solid #ccc; width:47%; margin-right:29px; }
   #pie2 { height:400px; border:1px solid #ccc;  }
   #series1 { height:400px; border:1px solid #ccc; width:47%; margin-right:29px;  }
   #series2 { height:400px; border:1px solid #ccc;  }
 </style>
<div class="col-md-12" >
   <div class="row">
	<div id="pie1" class="col-md-6 xs-mt-20">0</div> 
	<div id="pie2" class="col-md-6 xs-mt-20">0</div>
   </div>
</div>
 
<div class="col-md-12" >
   <div class="row">
	<div id="series1" class="col-md-6 xs-mt-20">0</div> 
	<div id="series2" class="col-md-6 xs-mt-20">0</div>
   </div>
</div>

<script>
    $( document ).ready(function() {
        Highcharts.setOptions({
			colors: ['#72635E','#FFBC65','#99884C','#7F5E33','#CC7C14','#402706','#FFC80C','#FF4F0C']
		 });
		Highcharts.chart('pie1', { 
			chart: {
			  type: 'pie', 
		   },
		   credits: {
			enabled: false
		   }, 
			 tooltip: {
			  pointFormat: '{series.name}: <b>{point.y:,.1f}</b>'
			 },
			 title: {
			  text: 'Production (t)'
			 },
			 subtitle: {
			  text: ''
			 },
			 xAxis: { 
			  labels: {
			   style: {
				fontSize: '10px',
				fontFamily: 'Verdana, sans-serif'
			   }
			  }
			 },
			 legend: {
			  enabled: true
			 },
			 plotOptions: {
			   pie: {
				 allowPointSelect: true,
				 cursor: 'pointer',
				 dataLabels: {
				   enabled: false
				 },
				 showInLegend: true
			   }
			 },
			 series: [{ 
			   'name' :'jumlah', 
			   'data':<?php echo $pie1_series;?>
			   
		   }] 
		});
		
		
		
		Highcharts.chart('pie2', { 
			chart: {
			  type: 'pie', 
		   },
		   credits: {
			enabled: false
		   }, 
			 tooltip: {
			  pointFormat: '{series.name}: <b>{point.y:,.1f}</b>'
			 },
			 title: {
			  text: 'Traceable Sales'
			 },
			 subtitle: {
			  text: ''
			 },
			 xAxis: { 
			  labels: {
			   style: {
				fontSize: '10px',
				fontFamily: 'Verdana, sans-serif'
			   }
			  }
			 },
			 legend: {
			  enabled: true
			 },
			 plotOptions: {
			   pie: {
				 allowPointSelect: true,
				 cursor: 'pointer',
				 dataLabels: {
				   enabled: false
				 },
				 showInLegend: true
			   }
			 },
			 series: [{ 
			   'name' :'jumlah', 
			   'data':<?php echo $pie2_series;?>
			   
		   }] 
		});
		
		
		Highcharts.chart('series1', { 
			title: {
				text: 'Number Of Farmer Transaction per MONTH'
			},

			subtitle: {
				text: ''
			},

			yAxis: {
				title: {
					text: 'Number'
				}
			},
			legend: {
				layout: 'vertical',
				align: 'c',
				verticalAlign: 'bottom',
			},
			xAxis: {
				categories: [<?php echo @$CatFarmers; ?>]
			},
			plotOptions: {
				series: {
					label: {
						connectorAllowed: false
					}
				}
			},

			series: <?php echo @$farmers; ?>,

			responsive: {
				rules: [{
					condition: {
						maxWidth: 500
					},
					chartOptions: {
						legend: {
							layout: 'horizontal',
							align: 'center',
							verticalAlign: 'bottom'
						}
					}
				}]
			}

		});
		
		
		Highcharts.chart('series2', { 
			title: {
				text: 'Sales Trend per MONTH'
			},

			subtitle: {
				text: ''
			},

			yAxis: {
				title: {
					text: 'Price'
				}
			},
			legend: {
				layout: 'vertical',
				align: 'c',
				verticalAlign: 'bottom',
			},
			xAxis: {
				categories: [<?php echo  $CatSales; ?>]
			},
			plotOptions: {
				series: {
					label: {
						connectorAllowed: false
					}
				}
			},

			series: <?php echo @$Sales; ?>,

			responsive: {
				rules: [{
					condition: {
						maxWidth: 500
					},
					chartOptions: {
						legend: {
							layout: 'horizontal',
							align: 'center',
							verticalAlign: 'bottom'
						}
					}
				}]
			}

		});
		 
});
</script>