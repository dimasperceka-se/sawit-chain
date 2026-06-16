if (m_prov!='') dataDistrict(m_data,'traceability'); 
var ajaxDataRenderer = function(url) {
   $('#wrapper').addClass('cover');
   var s = new Array();
   $.ajax({
        type: "GET",
        url: url,
        data: {prov: m_prov,kab: m_kab,priv: m_priv,daer: m_daer,awal: m_awal,akhir: m_akhir,orgid: m_orgid,partner:m_partner},
        contentType: "application/json; charset=utf-8",
        dataType: "json",
        async: false,
        success: function(r) {
           var box1 = box2 = box3 = box4 = 0;
            
            var data1 = r['penjualan'];
            var s2 = new Array();
            for (var i=0;i<data1.length;i++) {
               box2 += parseInt(data1[i]['total'])/1000;
               box1 += parseInt(data1[i]['total_bruto'])/1000;
               s2[i] = new Array();
               s2[i][0] = lang(data1[i]['label']);
               s2[i][1] = parseInt(data1[i]['total'])/1000;
            }
            var data2 = r['transaction'];
            var s1 = new Array();
            for (var i=0;i<data2.length;i++) {
               box3 += parseInt(data2[i]['total_farmer']);
               box4 += parseInt(data2[i]['total']);
               s1[i] = new Array();
               s1[i][0] = lang(data2[i]['label']);
               s1[i][1] = parseInt(data2[i]['total_farmer']);
            }

            s = [s1,s2]

            s[7] = r['district'];

            $('#box1').html(number_format(box1,0,'.',','));
            $('#box2').html(number_format(box2,0,'.',','));
            $('#box3').html(number_format(box3,0,'.',','));
            $('#box4').html(number_format(box4,0,'.',','));
            $('#wrapper').removeClass('cover');
            document.getElementById('row-fluid').style.display='';
         }
   });
   return s; 
};

var s = ajaxDataRenderer(m_data); 

plot(s[0],'pie1', lang('Total Petani'),'2',lang('Jumlah'));
plot(s[1],'pie2', lang('Traceable Sales (MT)'),'2',lang('Jumlah'),1);
