// if (m_prov!='') dataDistrict(m_data,'garden',m_petani); 

var pie_yield        = new Array();
var pie_land_ownership   = new Array();
var pie_land_owner   = new Array();
var pie_land_cert    = new Array();

var ajaxDataRenderer = function(url) {
   $('#wrapper').addClass('cover');
   var s = new Array();
   $.ajax({
        type: "GET",
        url: url,
        data: {prov: m_prov,kab: m_kab,priv: m_priv,daer: m_daer,petani:m_petani,tahun:m_tahun,survey:m_survey,partner:m_partner},
        contentType: "application/json; charset=utf-8",
        dataType: "json",
        async: false,
        success: function(r) {
            $('#box11').html(number_format(r['all'][0]['garden'],0,'.',','));
            $('#box1').html(number_format(r['all'][0]['area'],0,'.',','));
            $('#box2').html(number_format(r['all'][0]['produksi'],0,'.',','));
            $('#box4').html(number_format(r['all'][0]['kebun1'],1,'.',','));

            $('#box3').html(number_format(r['all'][0]['rerata'],2,'.',','));
            $('#box5').html(number_format(r['all'][0]['tanaman_cacao'],0,'.',','));
            $('#box7').html(number_format(r['all'][0]['tanaman_lain'],0,'.',','));
            $('#box_broken_tree').html(number_format(r['all'][0]['tanaman_rusak'],0,'.',','));

            $('#box8').html(number_format(r['all'][0]['rerata_hektar'],0,'.',','));
            $('#box_produktifitas').html(number_format(r['all'][0]['produktifitas'],0,'.',','));
            $('#box_produktifitas_pohon').html(number_format(r['all'][0]['produktifitas_pohon'],2,'.',','));
            $('#box_tree_age').html(number_format(r['all'][0]['rerata_umur'],1,'.',','));

            $('#wrapper').removeClass('cover');

            var groups = r['group'];
            //s9,s1,s2,pie_yield
            //[s61, s62],[s101, s102],s3,[s41, s42]
            //s5,chart_avg_tree,chart_tree_age,pie_land_ownership
            //pie_land_owner,pie_land_cert
            var s9 = new Array();
            var s1 = new Array();
            var s2 = new Array();
            var pie_yield = new Array();
            //===============
            var s61 = new Array();
            var s62 = new Array();
            var s101 = new Array();
            var s102 = new Array();
            var s3 = new Array();
            var s41 = new Array();
            var s42 = new Array();
            //================
            var cat_avg_tree         = [];
            var chart_avg_tree         = [];
            chart_avg_tree[0]          = {};
            chart_avg_tree[0]['name']  = lang('Average Tree');
            chart_avg_tree[0]['data']  = [];
            var cat_tree_age                    = new Array();
            var chart_tree_age                  = new Array();
            chart_tree_age[0]          = {};
            chart_tree_age[0]['name']  = lang('Average Farm Age');
            chart_tree_age[0]['data']  = [];
            

            for (var i=0;i<groups.length;i++) {
               s9[i] = new Array();
               s1[i] = new Array();
               s2[i] = new Array();
               s9[i][0] = s1[i][0] = s2[i][0] = s61[i] = s101[i] = s41[i] = cat_avg_tree[i] = cat_tree_age[i] = lang(groups[i]['label']);
               s9[i][0] = lang(groups[i]['label']);
               s9[i][1] = parseInt(groups[i]['kebun']);
               s1[i][1] = parseInt(groups[i]['luas_kebun']);
               s2[i][1] = parseInt(groups[i]['produksi'])/1000;
               s62[i] = parseInt(groups[i]['produktifitas']);
               s102[i] = parseFloat(groups[i]['produktifitas_menghasilkan'],1);
               s42[i] = parseFloat(groups[i]['rerata_ukuran']);
               chart_avg_tree[0]['data'][i] = parseFloat(groups[i]['rerata_pohon']);
               chart_tree_age[0]['data'][i] = parseFloat(groups[i]['rerata_umur']);
            }
            var keys_produksi = [
                // {'key':'Yield500', 'label':lang('Below 500')},
                {'key':"<=350Farmer", 'label': lang("Below 350")},
                {'key':">350And<=500Farmer", 'label': lang("Between 350 And 500")},
                {'key':">500And<=750Farmer", 'label': lang("Between 500 And 750")},
                {'key':">750And<=1000Farmer", 'label': lang("Between 750 And 1000")},
                {'key':">1000And<=1500Farmer", 'label': lang("Between 1000 And 1500")},
                // {'key':">1500And<=2000Farmer", 'label': lang("Between 1500 And 2000")},
                {'key':">1500Farmer", 'label': lang("Above 1500")},
                // {'key':'Yield500', 'label':lang('Below 500')},
                // {'key':'Yield1000', 'label':lang('Between 500 and 1000')},
                // {'key':'Yield2000', 'label':lang('Between 1000 and 2000')},
                // {'key':'YieldAbove2000', 'label':lang('Above 2000')}
            ]
            for (var i = keys_produksi.length - 1; i >= 0; i--) {                
               pie_yield[i]       = [];
               pie_yield[i][0] = lang(keys_produksi[i].label);
               pie_yield[i][1] = parseInt(r['all'][0][keys_produksi[i].key]);
            };
            //console.log(pie_yield)
            var keys_ukuran = [
                {'key':'Marginal', 'label':lang('Marginal (Less than 0.3 ha)')},
                {'key':'Micro', 'label':lang('Micro (Between 0.3 and 0.6 Ha)')},
                {'key':'Small', 'label':lang('Small (Between 0.6 and 1 Ha)')},
                {'key':'Medium', 'label':lang('Medium (Between 1 and 2 Ha)')},
                {'key':'Large', 'label':lang('Large (More than 2 Ha)')},
            ]
            for (var i = keys_ukuran.length - 1; i >= 0; i--) {
               s3[i]       = [];
               s3[i][0] = lang(keys_ukuran[i].label);
               s3[i][1] = parseInt(r['all'][0][keys_ukuran[i].key]);
            };
            var s5 = new Array();
            var keys_komposisi = [
                {'key':'PohonTBM', 'label':lang('TBM')},
                {'key':'PohonTM', 'label':lang('TM')},
                {'key':'RehabTree', 'label':lang('TR')},
                {'key':'PohonLain', 'label':lang('Tanaman Lain')}
            ]
            for (var i = keys_komposisi.length - 1; i >= 0; i--) {                
               s5[i]       = [];
               s5[i][0] = lang(keys_komposisi[i].label);
               s5[i][1] = parseInt(r['all'][0][keys_komposisi[i].key]);
            };
            var pie_land_ownership = new Array();
            var keys_owner = [
                {'key':'Owner', 'label':lang('Pemilik Penggaran')},
                {'key':'CropShare', 'label':lang('Petani Bagi Hasil')},
                {'key':'Rent', 'label':lang('Petani Penyewa')},
                {'key':'Other', 'label':lang('Lainnya')}
            ]
            for (var i = keys_owner.length - 1; i >= 0; i--) {                
               pie_land_ownership[i]       = [];
               pie_land_ownership[i][0] = lang(keys_owner[i].label);
               pie_land_ownership[i][1] = parseInt(r['all'][0][keys_owner[i].key]);
            };
            //console.log(pie_land_ownership)
            var pie_land_owner = new Array();
            var keys_owne = [
                {'key':'FarmerHimHerself', 'label':lang('Farmer Him/Herself')},
                {'key':'FamilyMember', 'label':lang('Family Member')},
                {'key':'OtherPerson', 'label':lang('Other Person')},
                {'key':'DoNotKnow', 'label':lang('Do Not Know')},
            ]
            for (var i = keys_owne.length - 1; i >= 0; i--) {                
               pie_land_owner[i]       = [];
               pie_land_owner[i][0] = lang(keys_owne[i].label);
               pie_land_owner[i][1] = parseInt(r['all'][0][keys_owne[i].key]);
            };
            var pie_land_cert = new Array();
            var keys_cert = [
                {'key':'NotarialDeepBpn', 'label':lang('Notaris Deed/BPN')},
                {'key':'SkktCamat', 'label':lang('SKKT/Camat')},
                {'key':'VillageLurah', 'label':lang('Village/Lurah')},
                {'key':'NoLandCertificate', 'label':lang('No Land Certificate')}
            ]
            for (var i = keys_cert.length - 1; i >= 0; i--) {                
               pie_land_cert[i]       = [];
               pie_land_cert[i][0] = lang(keys_cert[i].label);
               pie_land_cert[i][1] = parseInt(r['all'][0][keys_cert[i].key]);
            };
            
            s = [s1, s2, s3, [s41, s42], s5, [s61, s62], null, null, s9, [s101,s102]];
            //    0   1   2   31    32    4   51    52   6     7
            
            // s[77] = r['district'];
            var pie_farm_size = [];
            pie_farm_size[0] = [lang('Small (Less than 1 ha)'),parseInt(r['all'][0]['Small'])+parseInt(r['all'][0]['Marginal'])+parseInt(r['all'][0]['Micro'])];
            pie_farm_size[1] = [lang('Medium (Between 1 ha and 2 ha)'),parseInt(r['all'][0]['Medium'])];
            pie_farm_size[2] = [lang('Large (More than 2 ha)'),parseInt(r['all'][0]['Large'])];
            var pie_farm_mgt = [];
            pie_farm_mgt[0] = [lang('Unprofessional (Less than 500 kg/ha)'), parseInt(r['all'][0]['Unprofessional'])];
            pie_farm_mgt[1] = [lang('Progressing (Between 500 kg/ha and 1,000 kg/Ha)'), parseInt(r['all'][0]['Progressing'])];
            pie_farm_mgt[2] = [lang('Professional (More than 1,000 kg/ha)'), parseInt(r['all'][0]['Professional'])];

            s['chart_tree_age']  = chart_tree_age;
            s['cat_tree_age']    = cat_tree_age;
            s['chart_avg_tree']  = chart_avg_tree;
            s['cat_avg_tree']    = cat_avg_tree;
            s['pie_yield']    = pie_yield;
            s['pie_land_ownership']    = pie_land_ownership;
            s['pie_land_owner']    = pie_land_owner;
            s['pie_land_cert']    = pie_land_cert;
            s['pie_farm_size']    = pie_farm_size;
            s['pie_farm_mgt']    = pie_farm_mgt;

            document.getElementById('row-fluid').style.display='';
            
         }
   });
   return s; 
};

var s = ajaxDataRenderer(m_data); 

plot(s[8],'pie11', lang('Jumlah Kebun Kakao'),'2',lang('Jumlah'));
plot(s[0],'pie1', lang('Total Luas Lahan Kebun Kakao (Ha)'),'2',lang('Luas'));
plot(s[1],'pie2', lang('Total Produksi Kakao Per Tahun (MT)'),'2',lang('Total'));
plot(s.pie_yield,'chart_yield', lang('Yield Categories')+' ('+lang('Kg/Ha/Year')+')','1',lang('Jumlah'));

column([{name: lang('Rata-rata'),data: s[5][1]}], 'chart_produktifitas', lang('Rata-Rata Produktivitas Kebun'), lang('Kg/Ha/Tahun'), ['#3B5323'], s[5][0])
column([{name: 'Produktivitas',data: s[9][1]}], 'chart_produktifitas_pohon', lang('Rata-Rata Produktivitas Tanaman Menghasilkan'), lang('Kg/TM/Tahun'), ['#3B5323'], 
   s[9][0],'normal', 2)
plot(s[2],'pie3', lang('Rata-Rata Ukuran Kebun Kakao (%)'),'1',lang('Jumlah'),1);
column([{name: lang('Ukuran'),data: s[3][1]}], 'pie4', lang('Rata-Rata Ukuran Kebun (Ha)'), lang('Ukuran (Ha)'), ['#3B5323'], s[3][0], 
   'normal', 1)

plot(s[4],'pie5', lang('Komposisi Kebun (%)'),'1',lang('Jumlah'));
column_one(s.chart_avg_tree, 'chart_avg_tree', lang('Average Number of Cacao Trees per Hectare'), lang('Trees/Ha'), null, s.cat_avg_tree,'normal', 0);
column_one(s.chart_tree_age, 'chart_tree_age', lang('Average Farm Age'), lang('Year'), null, s.cat_tree_age,'normal', 1);
plot(s.pie_land_ownership,'chart_land_ownership', lang('Land Ownership'),'1',lang('Jumlah'));

plot(s.pie_land_owner,'chart_land_owner', lang('Owner of the land'),'1',lang('Jumlah'));
plot(s.pie_land_cert,'chart_land_certificate', lang('Land Certificate'),'1',lang('Jumlah'));

plot(s.pie_farm_size,'chart_farm_size', lang('Farm Size Classifications'),'1',lang('Jumlah'));
plot(s.pie_farm_mgt,'chart_farm_mgt', lang('Farm Management Classifications'),'1',lang('Jumlah'));

