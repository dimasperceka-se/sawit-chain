
function number_format (number, decimals, dec_point, thousands_sep) {
  number = (number + '').replace(/[^0-9+\-Ee.]/g, '');
  var n = !isFinite(+number) ? 0 : +number,
    prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
    sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep,
    dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
    s = '',
    toFixedFix = function (n, prec) {
      var k = Math.pow(10, prec);
      return '' + Math.round(n * k) / k;
    };
  // Fix for IE parseFloat(0.55).toFixed(0) = 0;
  s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
  if (s[0].length > 3) {
    s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
  }
  if ((s[1] || '').length < prec) {
    s[1] = s[1] || '';
    s[1] += new Array(prec - s[1].length + 1).join('0');
  }
  return s.join(dec);
}
var dataDistrict = function(url) {
   $.ajax({
        type: "GET",
        url: m_district,
        data: {prov: m_prov},
        contentType: "application/json; charset=utf-8",
        dataType: "json",
        async: false,
        success: function(r) {
            var html_d=jud='';
            for (var i=0;i<r.length;i++) {
               html_d += '<li><a href="'+m_url+'/home/home/index/'+m_prov+'/0/'+r[i]['id']+'">'+r[i]['label']+'</a></li>';
               if (r[i]['id']==m_kab) jud = r[i]['label']
            }
            document.getElementById('dLabeli').innerHTML = document.getElementById('dLabeli').innerHTML + html_d;
            if (jud!='') document.getElementById('judul').innerHTML = jud;
         }
   })
}
if (m_prov!='') dataDistrict(m_data); 
var ajaxDataRenderer = function(url) {
   var s = new Array();
   $.ajax({
        type: "GET",
        url: url,
        data: {prov: m_prov,kab: m_kab,priv: m_priv,daer: m_daer},
        contentType: "application/json; charset=utf-8",
        dataType: "json",
        async: false,
        success: function(r) {
            var s1 = new Array();
            var cpg = r['cpg'];
            var s8 = r['district'];
            var total_cpg = 0;
            for (var i=0;i<cpg.length;i++) {
               total_cpg += parseInt(cpg[i]['total']);
               s1[i] = new Array();
               s1[i][0] = cpg[i]['label'];
               s1[i][1] = parseInt(cpg[i]['total']);
            }
            $('#cpg').html(number_format(total_cpg,0,',',','));

            var s2 = new Array();
            var farmer = r['farmer'];
            var total_farmer = 0;
            for (var i=0;i<farmer.length;i++) {
               total_farmer += parseInt(farmer[i]['total']);
               s2[i] = new Array();
               s2[i][0] = farmer[i]['label'];
               s2[i][1] = parseInt(farmer[i]['total']);
            }
            $('#farmer').html(number_format(total_farmer,0,',',','));
         
            var s3 = new Array();
            var luas = r['luas'];
            var total_luas = 0;
            for (var i=0;i<luas.length;i++) {
               total_luas += parseInt(luas[i]['total']);
               s3[i] = new Array();
               s3[i][0] = luas[i]['label'];
               s3[i][1] = parseInt(luas[i]['total']);
            }
            $('#luas').html(number_format(total_luas,0,',',','));
         
            var s4 = new Array();
            var pohon = r['pohon'];
            var total_pohon = 0;
            for (var i=0;i<pohon.length;i++) {
               s4[i] = new Array();
               s4[i][0] = pohon[i]['label'];
               s4[i][1] = parseInt(pohon[i]['total']);
               total_pohon += parseInt(pohon[i]['total']);
            }
            $('#pohon').html(number_format(total_pohon,0,',',','));
         
            var s5 = new Array();
            var s9 = new Array();
            var s13 = new Array();
            var total = r['total'];
            var total_total = 0;
            for (var i=0;i<total.length;i++) {
               total_total += parseInt(total[i]['total']);
               s5[i] = new Array();
               s9[i] = new Array();
               s13[i] = total[i]['label'];
               s5[i][1] = parseInt(total[i]['produktivitas']);
               s9[i][0] = total[i]['label'];
               s9[i][1] = parseInt(total[i]['total']/1000);
            }
            $('#total').html(number_format(parseInt(total_total/1000),0,',',','));
            $('#productivity').html(number_format(r['total_productivity'][0]['produktivitas'],0,',',','));
         
            var s6 = new Array();
            var s7 = new Array();
            var training = r['training'];
            var total_training = 0;
            for (var i=0;i<training.length;i++) {
               total_training += parseInt(training[i]['total']);
               s7[i] = training[i]['label'];
               s6[i] = new Array();
               s6[i][0] = training[i]['label'];
               s6[i][1] = parseInt(training[i]['total']);
            }
            $('#training').html(number_format(total_training,0,',',','));

            var s11 = new Array();
            var avg = r['avg'];
            var keys = Object.keys(avg[0]);
            for (var i=0;i<keys.length;i++) {
               s11[i] = new Array();
               s11[i][0] = keys[i];
               s11[i][1] = parseFloat(avg[0][keys[i]]);
            }
            var s12 = new Array();
            var train = r['train'];
            for (var i=0;i<train.length;i++) {
               s12[i] = new Array();
               s12[i][0] = train[i]['label'];
               s12[i][1] = parseFloat(train[i]['total']);
            }
            
            var s14 = new Array();
            var farm = r['farm'];
            var keys = Object.keys(farm[0]);
            for (var i=0;i<keys.length;i++) {
               s14[i] = new Array();
               s14[i][0] = keys[i];
               s14[i][1] = parseFloat(farm[0][keys[i]]);
            }

            var s15 = new Array();
            var s16 = new Array();
            var farm_size = r['farm_size'];
            for (var i=0;i<farm_size.length;i++) {
               s15[i] = farm_size[i]['label'];
               s16[i] = parseFloat(farm_size[i]['avg']);
            }

            var s17 = new Array();
            var s18 = new Array();
            var s19 = new Array();
            var farmer_gender = r['farmer_gender'];
            for (var i=0;i<farmer_gender.length;i++) {
               s17[i] = farmer_gender[i]['label'];
               s18[i] = parseFloat(farmer_gender[i]['male']);
               s19[i] = parseFloat(farmer_gender[i]['female']);
            }
            s = [s1, s2, s3, s4, s5, s6, s7, s8, s9, s11,s12, s13, s14, s15, s16, s17, s18, s19]
            //    0   1   2   3   4   5   6   7   8   9   10   11  12   13    14    15 16    17
         }
   });
   return s; 
};
//if (s[5].length>0) plot(s[5],'pie6', 'Training Circle', 'bar');

function plot(data, div, judul, format) {

   var pf = '{series.name}: <b>{point.percentage:.2f}%</b>';
   if (format=='1') pf = '{series.name}: <b>{point.y}</b>'; 
   new Highcharts.Chart({
         chart: {
             renderTo: div,
             plotBackgroundColor: null,
             plotBorderWidth: null,
             plotShadow: false
         },
         colors: ['#3B5323','#446B1E','#4E8419','#589C14','#61B50F','#6BCD0A','#75E605','#7FFF00'],
         //colors: ['#397D02','#567E3A','#A6D785','#687E5A','#8AA37B','#476A34','#93DB70','#4CBB17','#49E20E','#55AE3A'],
         title: {
             text: judul
         },
         tooltip: {
            pointFormat: pf
         },
         plotOptions: {
              pie: {
                 allowPointSelect: true,
                 cursor: 'pointer',
                 dataLabels: {
                     enabled: true,
                     formatter: function () {
                         if (format=='1') return number_format(this.percentage,1,',',',')+'%';
                         else return number_format(this.y,0,',',',');
                     }
                 },
                 showInLegend: true
             }
         },
         series: [{
             type: 'pie',
             name: judul,
             data: data
         }]
     });
}
var s = ajaxDataRenderer(m_data); 
plot(s[0],'pie1', lang('Cocoa Production Groups'));
plot(s[1],'pie2', lang('Smallholder Cocoa Farmers'));
plot(s[2],'pie3', lang('Total Cocoa Garden Land Area (Ha)'));
plot(s[8],'pie4', lang('Total Annual Cocoa Production (MT)'));

plot(s[9],'pie7', 'Average Cocoa Farm Size (%)','1');
//plot(s[10],'pie8', 'Training Participant');
plot(s[12],'pie8', 'Farm Composition (%)','1');
//if (s[4].length>0) plot(s[4],'pie5', 'Average Farm Productivity (Kg/Ha/Year)');
//if (s[3].length>0) plot(s[3],'pie4', 'Pohon Kakao');
//console.log(s[4]);
new Highcharts.Chart({
   chart: {
      renderTo: 'pie5',
      type: 'column'
   },
   colors: ['#3B5323','#446B1E','#4E8419','#589C14','#61B50F','#6BCD0A','#75E605','#7FFF00'],
   //'#397D02','#567E3A','#A6D785','#687E5A','#8AA37B','#476A34','#93DB70','#4CBB17','#49E20E','#55AE3A'],
   title: {
       text: 'Average Farm Productivity'
   },
   xAxis: {
       categories: s[11],
       labels: {
           rotation: -45,
           align: 'right',
           style: {
               fontSize: '13px',
               fontFamily: 'Verdana, sans-serif'
           }
       }
   },
   yAxis: {
      title: {
            text: "Kg/Ha/Year"
        }
   },
   legend: {
       enabled: false
   },
   series: [{
       name: 'Productivity',
       data: s[4],
       dataLabels: {
           enabled: true,
           rotation: -90,
           color: '#FFFFFF',
           align: 'right',
           x: 4,
           y: 10,
           style: {
               fontSize: '13px',
               fontFamily: 'Verdana, sans-serif',
               textShadow: '0 0 3px black'
           }
       }
   }]
});

plot(s[5],'pie6', 'Household Members Trained in Nutrition');
/*new Highcharts.Chart({
   chart: {
      renderTo: 'pie6',
      type: 'column'
   },
   title: {
       text: 'Household Members Trained in Nutrition'
   },
   xAxis: {
       categories: s[6],
       labels: {
           rotation: -45,
           align: 'right',
           style: {
               fontSize: '13px',
               fontFamily: 'Verdana, sans-serif'
           }
       }
   },
   legend: {
       enabled: false
   },
   series: [{
       name: 'Training',
       data: s[5],
       dataLabels: {
           enabled: true,
           rotation: -90,
           color: '#FFFFFF',
           align: 'right',
           x: 4,
           y: 10,
           style: {
               fontSize: '13px',
               fontFamily: 'Verdana, sans-serif',
               textShadow: '0 0 3px black'
           }
       }
   }]
});*/

new Highcharts.Chart({
   chart: {
      renderTo: 'pie9',
      type: 'column'
   },
   colors: ['#3B5323','#446B1E','#4E8419','#589C14','#61B50F','#6BCD0A','#75E605','#7FFF00'],
   //'#397D02','#567E3A','#A6D785','#687E5A','#8AA37B','#476A34','#93DB70','#4CBB17','#49E20E','#55AE3A'],
   title: {
       text: 'Average Farm Size (Ha)'
   },
   xAxis: {
       categories: s[13],
       labels: {
           rotation: -45,
           align: 'right',
           style: {
               fontSize: '13px',
               fontFamily: 'Verdana, sans-serif'
           }
       }
   },
   yAxis: {
      title: {
            text: "Size (Ha)"
        }
   },
   legend: {
       enabled: false
   },
   series: [{
       name: 'Province',
       data: s[14],
       dataLabels: {
           enabled: true,
           rotation: -90,
           color: '#FFFFFF',
           align: 'right',
           x: 4,
           y: 10,
           style: {
               fontSize: '13px',
               fontFamily: 'Verdana, sans-serif',
               textShadow: '0 0 3px black'
           }
       }
   }]
});  

new Highcharts.Chart({
   chart: {
      renderTo: 'pie10',
      type: 'column'
   },
   colors: ['#3B5323','#446B1E','#4E8419','#589C14','#61B50F','#6BCD0A','#75E605','#7FFF00'],
   //'#397D02','#567E3A','#A6D785','#687E5A','#8AA37B','#476A34','#93DB70','#4CBB17','#49E20E','#55AE3A'],
   title: {
       text: 'Farmer by Gender'
   },
   xAxis: {
       categories: s[15],
       labels: {
           rotation: -45,
           align: 'right',
           style: {
               fontSize: '13px',
               fontFamily: 'Verdana, sans-serif'
           }
       }
   },
   yAxis: {
      title: {
            text: "Jumlah"
        }
   },
   tooltip: {
       formatter: function() {
           return '<b>'+ this.x +'</b><br/>'+
               this.series.name +': '+ this.y +'<br/>'+
               'Total: '+ this.point.stackTotal;
       }
   },
   plotOptions: {
       column: {
           stacking: 'normal',
           dataLabels: {
               enabled: true,
               color: (Highcharts.theme && Highcharts.theme.dataLabelsColor) || 'white',
               style: {
                   textShadow: '0 0 3px black, 0 0 3px black'
               }
           }
       }
   },
   series: [{
       name: 'Male',
       data: s[16]
   },{
       name: 'Female',
       data: s[17]
   }]
});  
