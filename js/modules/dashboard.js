// if (m_prov!='') dataDistrict(m_data,'index'); 
var ajaxDataRenderer = function(url) {
   var s = new Array();
    $('#wrapper').addClass('cover');
    $.ajax({
        type: "GET",
        url: url,
        data: {prov: m_prov,kab: m_kab,priv: m_priv,daer: m_daer,partner:m_partner},
        contentType: "application/json; charset=utf-8",
        dataType: "json",
        async: false,
        success: function(r) {

            $('#cpg').html(number_format(r['cpg'],0,',',','));
            $('#farmer').html(number_format(r['farmer'],0,',',','));
            $('#luas').html(number_format(r['luas'],0,',',','));
            $('#pohon').html(number_format(r['pohon']/1000,0,',',','));
            $('#produksi').html(number_format(r['produksi']/1000,0,',',','));
            $('#produktifitas').html(number_format(r['produktifitas'],0,',',','));
            $('#usia_petani').html(number_format(r['usia'],1,'.','.'));
            $('#ukuran_kebun').html(number_format(r['rerata'],2,'.','.'));
            $('#petani_perempuan').html(number_format(r['perempuan']*100,1,'.','.'));
            $('#training_gnp').html(number_format(r['gnp'],0,',',','));
            $('#training_gfp').html(number_format(r['gfp'],0,',',','));

            $('#produktifitas_pohon').html(number_format(r['produktifitas_pohon'],2,'.','.'));
            $('#petani_sertifikasi').html(number_format(r['certified'],0,',',','));
            $('#luas_sertifikasi').html(number_format(r['luas_sertifikasi'],0,',',','));
            $('#produksi_sertifikasi').html(number_format(r['produksi_sertifikasi']/1000,0,',',','));
            $('#jumlah_kebun').html(number_format(r['garden'],0,',',','));
            
            // if (r['ketiga'][0])
            //     $('#box31').html(number_format(r['ketiga'][0]['total'],0,',',','));

            // pie farm size classification
            var keys_farm_size = [
                {'key' : 'small', 'label' : lang('Small < 1 ha')},
                {'key' : 'medium', 'label' : lang('Medium >= 1 ha and < 2 ha')},
                {'key' : 'large', 'label' : lang('Large >= 2 ha')},
            ]
            var pie_farm_size = [];
            for (var i = keys_farm_size.length - 1; i >= 0; i--) {                
                pie_farm_size[i]       = [];
                pie_farm_size[i][0]    = keys_farm_size[i].label;
                pie_farm_size[i][1]    = 0;
            };
            // pie farm management classification
            var keys_farm_mgt = [
                {'key' : 'unprofessional', 'label' : lang('Unprofessional < 500 kg/ha')},
                {'key' : 'progressing', 'label' : lang('Progressing >= 500 kg/ha and < 1,000 kg/Ha ')},
                {'key' : 'professional', 'label' : lang('Professional >= 1,000 kg/ha')},
            ]
            var pie_farm_mgt = [];
            for (var i = keys_farm_mgt.length - 1; i >= 0; i--) {                
                pie_farm_mgt[i]       = [];
                pie_farm_mgt[i][0]    = keys_farm_mgt[i].label;
                pie_farm_mgt[i][1]    = 0;
            };

            $.each(keys_farm_size, function(idx, key) {
                pie_farm_size[idx][1] += parseInt(r[key.key]);
            });

            $.each(keys_farm_mgt, function(idx, key) {
                pie_farm_mgt[idx][1] += parseInt(r[key.key]);
            });
            s['pie_farm_size'] = pie_farm_size;
            s['pie_farm_mgt'] = pie_farm_mgt;

            // s[7] = r['district'];
            $('#row-fluid').show();
            $('#wrapper').removeClass('cover');
         }
   });
   return s
};
// $( window ).load(function() {
$( document ).ready(function() {
   var s = ajaxDataRenderer(m_data);
   plot(s.pie_farm_size,'chart_farm_size', lang('Farm Size Classifications'),'1',lang('Jumlah'));
   plot(s.pie_farm_mgt,'chart_farm_mgt', lang('Farm Management Classification'),'1',lang('Jumlah'));
    // console.log( "ready!" );
});
// console.log(s.pie_farm_size);

