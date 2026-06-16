// if (m_prov!='') dataDistrict(m_data,'certification'); 
var ajaxDataRenderer = function(url) {
   $('#wrapper').addClass('cover');
   var s = new Array();
   $.ajax({
        type: "GET",
        url: url,
        data: {prov: m_prov,kab: m_kab,priv: m_priv,daer: m_daer,partner:m_partner,startdate:m_startdate,enddate:m_enddate},
        contentType: "application/json; charset=utf-8",
        dataType: "json",
        async: false,
        success: function(r) {
            var box1 = box11 = box2 = box3 = box4 = box41 = total = 0;
            
            var s11 = new Array();
            var s12 = new Array();
            var s13 = new Array();
            var s14 = new Array();
            var s15 = new Array();
            var data1 = r['data'];
            for (var i=0;i<data1.length;i++) {
               s11[i] = lang(data1[i]['label']);
               s12[i] = parseFloat(data1[i]['UTZ_farmer']);
               s13[i] = parseFloat(data1[i]['Rainforest_farmer']);
               s14[i] = parseFloat(data1[i]['Fair_farmer']);
               s15[i] = parseFloat(data1[i]['Organic_farmer']);
               box1 += parseInt(data1[i]['farmer'])
            }

            var s51 = new Array();
            var s52 = new Array();
            var s53 = new Array();
            var s54 = new Array();
            var s55 = new Array();
            var data5 = r['data'];
            for (var i=0;i<data5.length;i++) {
               s51[i] = lang(data5[i]['label']);
               s52[i] = parseFloat(data5[i]['UTZ_garden']);
               s53[i] = parseFloat(data5[i]['Rainforest_garden']);
               s54[i] = parseFloat(data5[i]['Fair_garden']);
               s55[i] = parseFloat(data5[i]['Organic_garden']);
               box11 += parseFloat(data5[i]['garden'])
            }

            var s21 = new Array();
            var s22 = new Array();
            var s23 = new Array();
            var data2 = r['data'];
            var avg_female = 0;
            var count = 0;
            for (var i=0;i<data2.length;i++) {
               s21[i] = lang(data2[i]['label']);
               s22[i] = parseFloat(data2[i]['male']);
               s23[i] = parseFloat(data2[i]['female']);
               box2 += parseFloat(data2[i]['female']);
               total += parseFloat(data2[i]['male'])+parseFloat(data2[i]['female']);
               female = parseFloat(data2[i]['female'])/(parseFloat(data2[i]['male'])+parseFloat(data2[i]['female']));
               if (female) {
                  avg_female += female;
                  count++;
               }
            }
            // box2 = box2/total*100;
            box2 = avg_female/data2.length*100;

            var s31 = new Array();
            var s32 = new Array();
            var data3 = r['data'];
            for (var i=0;i<data3.length;i++) {
               s31[i] = lang(data3[i]['label']);
               s32[i] = parseFloat(data3[i]['ha']);
               box3 += parseFloat(data3[i]['ha']);
            }

            var s41 = new Array();
            var s42 = new Array();
            var data4 = r['data'];
            total = 0;
            count = 0;
            var avg_productivity = 0;
            for (var i=0;i<data4.length;i++) {
               s41[i] = lang(data4[i]['label']);
               s42[i] = parseFloat(data4[i]['production'])/1000;
               box41 += parseFloat(data4[i]['production'])/1000;
               total += parseFloat(data4[i]['ha']);
               prod = parseFloat(data4[i]['production'])/parseFloat(data4[i]['ha']);
               if (prod) {
                  avg_productivity += prod;
                  count++;
               }
            }
            // box4 = box41/total*1000;
            box4 = avg_productivity/data4.length;

            var s61 = new Array();
            var s62 = new Array();
            var data6 = r['data'];
            for (var i=0;i<data6.length;i++) {
               s61[i] = lang(data6[i]['label']);
               s62[i] = parseFloat(data6[i]['production'])/parseFloat(data6[i]['ha']);
            }

            var s71 = new Array();
            var s72 = new Array();
            var s73 = new Array();
            var s74 = new Array();
            var data7 = r['data'];
            for (var i=0;i<data7.length;i++) {
               s71[i] = lang(data7[i]['label']);
               s72[i] = parseFloat(data7[i]['trader']);
               s73[i] = parseFloat(data7[i]['koperasi']);
               s74[i] = parseFloat(data7[i]['warehouse']);
            }

            var s91 = new Array();
            var s92 = new Array();
            var data9 = r['data'];
            for (var i=0;i<data9.length;i++) {
               s91[i] = lang(data9[i]['label']);
               s92[i] = parseFloat(data9[i]['ha'])/parseFloat(data9[i]['garden']);
            }

            if (size = r['data']) {
               var total_size = count_farm = total_ha = count_tree = avg_size = avg_tree = 0;
               count_size = 0;
               count_trees = 0;
               $.each(size, function(index, val) {
                  total_size     += parseFloat(val.ha);
                  count_farm     += parseFloat(val.garden);
                  total_ha       += parseFloat(val.ha);
                  count_tree     += parseFloat(val.tree);
                  farm_size = parseFloat(val.ha)/parseFloat(val.garden);
                  if (farm_size) {
                     avg_size += farm_size;
                     count_size++;
                  }
                  tree = parseFloat(val.tree)/parseFloat(val.ha);
                  if (tree) {
                     avg_tree += tree;
                     count_trees++;
                  }
               });
               // $('#box_avg_farm_size').html(number_format(total_size/count_farm,2,'.',','));
               $('#box_avg_farm_size').html(number_format(avg_size/count_size,2,'.',','));
               // $('#box_avg_cocoa_tree').html(number_format(count_tree/total_ha,0,'.',','));
               $('#box_avg_cocoa_tree').html(number_format(avg_tree/count_trees,0,'.',','));
            };

            s = [[s11,s12,s13,s14,s15], [s21,s22,s23], [s31, s32], [s41, s42], [s51,s52,s53,s54,s55], [s61,s62], [s71,s72,s73,s74],
               r['district'], [s91,s92]]
            //    0   1                2           3        4        

            $('#box1').html(number_format(box1,0,'.',','));
            $('#box11').html(number_format(box11,0,'.',','));
            $('#box2').html(number_format(box2,1,'.',','));
            $('#box3').html(number_format(box3,0,'.',','));
            $('#box4').html(number_format(box4,0,'.',','));
            $('#box41').html(number_format(box41,0,'.',','));
            $('#wrapper').removeClass('cover');
            document.getElementById('row-fluid').style.display='';
         }
   });
   return s; 
};

var s = ajaxDataRenderer(m_data); 

column([{name: lang('Produksi'),data: s[3][1]}], 'pie4', lang('Produksi Kakao Tersertifikasi'), 'Ton', ['#3B5323'], s[3][0])
column([{name: lang('Luas'),data: s[2][1]}], 'pie3', lang('Luas Lahan Tersertifikasi'), 'Ha', ['#3B5323'], s[2][0])
column([{name: lang('UTZ'),data: s[0][1]},{name: lang('Rainforest'),data: s[0][2]},{name: lang('Fair Trade'),data: s[0][3]},
   {name: lang('Organic'),data: s[0][4]}], 'pie1', lang('Sertifikasi Petani'), lang('Farmers'), ['#3B5323','#4E8419','#61B50F','#75E605'], s[0][0],
   'normal',0,true)
column([{name: lang('Laki-laki'),data: s[1][1]},{name: lang('Perempuan'),data: s[1][2]}], 'pie2', lang('Jenis Kelamin Sertifikasi Petani'), 
   '%', ['#3B5323','#589C14'], s[1][0],'percent',0,true)

column([{name: lang('UTZ'),data: s[4][1]},{name: lang('Rainforest'),data: s[4][2]},{name: lang('Fair Trade'),data: s[4][3]},
   {name: lang('Organic'),data: s[4][4]}], 'pie11', lang('Sertifikasi Kebun Kakao'), lang('Gardens'), ['#3B5323','#4E8419','#61B50F','#75E605'], s[4][0],
   'normal',0,true)
column([{name: lang('Produktivitas'),data: s[5][1]}], 'pie41', lang('Rata Rata Produktivitas Kebun Tersertifikasi'), lang('(KG/Ha/Tahun)'), 
   ['#3B5323'], s[5][0])
column([{name: lang('Trader'),data: s[6][1]},{name: lang('Organisasi Petani'),data: s[6][2]},{name: lang('Warehouse'),data: s[6][3]}], 
   'pie5', lang('Pemegang Sertifikat'), lang('Farmers'), ['#3B5323','#4E8419','#61B50F','#75E605'], s[6][0],'normal',0,true)
column([{name: lang('Rata Rata Ukuran Kebun'),data: s[8][1]}], 'pie6', lang('Rata Rata Ukuran Kebun'), lang('Ha/Farmers'), 
   ['#3B5323'], s[8][0], 'normal', 1)
