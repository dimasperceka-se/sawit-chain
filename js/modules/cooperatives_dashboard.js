// if (m_prov!='') dataDistrict(m_data,'finance');

var ajaxDataRenderer = function(url) {
    $('#wrapper').addClass('cover');
    var chart = [];
    $.ajax({
        type: "GET",
        url: url,
        data: {CoopID:m_coop_id},
        contentType: "application/json; charset=utf-8",
        dataType: "json",
        async: false,
        success: function(r) {

        	// var active_member = active_member_gender = saving_pokok = saving_wajib = member_scpp = member_nonscpp = 0;

            $('#registered_member').html(number_format(r['registered_member'], 0, '.', ','));
            $('#candidate_member').html(number_format(r['candidate_member'], 0, '.', ','));
			$('#active_member').html(number_format(r['active_member'], 0, '.', ','));
            $('#inactive_member').html(number_format(r['inactive_member'], 0, '.', ','));
			$('#active_member_male').html(number_format(r['persen_male'], 0, '.', ',')+'%');
			$('#active_member_female').html(number_format(r['persen_female'], 0, '.', ',')+'%');
			$('#saving_pokok').html(number_format(r['saving_pokok'], 1, '.', ','));
			$('#saving_wajib').html(number_format(r['saving_wajib'], 1, '.', ','));
			$('#member_scpp').html(number_format(r['member_scpp'], 0, '.', ','));
			$('#member_nonscpp').html(number_format(r['member_nonscpp'], 0, '.', ','));
            $('#member_loan').html(number_format(r['member_loan'], 0, '.', ','));
            $('#member_due_saving_wajib').html(number_format(r['member_due_saving_wajib'], 0, '.', ','));            
            $('#avgAge').html(number_format(r['avgAge'], 0, '.', ','));
            $('#total_saving_account').html(number_format(r['total_saving_account'], 0, '.', ','));
            $('#total_loan').html(r['total_loan']);
            $('#total_loan_interest').html(r['total_loan_interest']);
            $('#total_loan_outstanding').html(r['total_loan_outstanding']);
            $('#total_loan_paid').html(r['total_loan_paid']);


            $('#wrapper').removeClass('cover');
            document.getElementById('row-fluid').style.display='';
            // console.log(r);

            //todo: jika semua value 0, hide the chart
            /*column([{name: lang('Jumlah Member'),data: [r.bar1.val[0]*1,r.bar1.val[1]*1]}], 'pie1', lang('Anggota berdasarkan Status'), 'Jumlah Member', ['#3B5323'], [r.bar1.label[0],r.bar1.label[1]])
            column([{name: lang('Jumlah Member'),data: [r.bar2.val[0]*1,r.bar2.val[1]*1]}], 'pie2', lang('Anggota berdasarkan Anggota Berdasarkan Member type '), 'Jumlah Member', ['#3B5323'], [r.bar2.label[0],r.bar2.label[1]])
            column([{name: lang('Jumlah Member'),data: [r.bar3.val[0]*1,r.bar3.val[1]*1]}], 'pie3', lang('Anggota berdasarkan Anggota Berdasarkan Gender'), 'Jumlah Member', ['#3B5323'], [r.bar3.label[0],r.bar3.label[1]])
            column([{name: lang('Jumlah Member'),data: [r.bar4.val[0]*1,r.bar4.val[1]*1]}], 'pie4', lang('Anggota berdasarkan Anggota Berdasarkan Occupation'), 'Jumlah Member', ['#3B5323'], [r.bar4.label[0],r.bar4.label[1]])
            column([{name: lang('Jumlah Member'),data: [r.bar5.val[0]*1,r.bar5.val[1]*1]}], 'pie5', lang('Anggota berdasarkan Anggota Berdasarkan Status Pernikahan'), 'Jumlah Member', ['#3B5323'], [r.bar5.label[0],r.bar5.label[1]])

             $('#pie5').highcharts({
                    title: {
                        text: 'Pergerakan Kas Per bulan',
                        x: -20 //center
                    },
                    subtitle: {
                        text: 'Tahun 2016',
                        x: -20
                    },
                    xAxis: {
                        categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun',
                            'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']
                    },
                    yAxis: {
                        title: {
                            text: 'Jumlah (Rp)'
                        },
                        plotLines: [{
                            value: 0,
                            width: 1,
                            color: '#808080'
                        }]
                    },
                    legend: {
                        layout: 'vertical',
                        align: 'right',
                        verticalAlign: 'middle',
                        borderWidth: 0
                    },
                    series: [{
                        name: 'Kas',
                        data: [r.bar6.val[0]*1,r.bar6.val[1]*1,r.bar6.val[2]*1,r.bar6.val[3]*1,r.bar6.val[4]*1,r.bar6.val[5]*1,r.bar6.val[6]*1,r.bar6.val[7]*1,r.bar6.val[8]*1,r.bar6.val[9]*1,r.bar6.val[10]*1,r.bar6.val[11]*1,]
                    }]
                });

                $('#pie6').highcharts({
                    title: {
                        text: 'Laba/Rugi Per bulan',
                        x: -20 //center
                    },
                    subtitle: {
                        text: 'Tahun 2016',
                        x: -20
                    },
                    xAxis: {
                        categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun',
                            'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec']
                    },
                    yAxis: {
                        title: {
                            text: 'Saldo (Rp)'
                        },
                        plotLines: [{
                            value: 0,
                            width: 1,
                            color: '#808080'
                        }]
                    },
                    legend: {
                        layout: 'vertical',
                        align: 'right',
                        verticalAlign: 'middle',
                        borderWidth: 0
                    },
                    series: [{
                        name: 'Pendapatan',
                        data: [r.bar7.val[0]*1,r.bar7.val[1]*1,r.bar7.val[2]*1,r.bar7.val[3]*1,r.bar7.val[4]*1,r.bar7.val[5]*1,r.bar7.val[6]*1,r.bar7.val[7]*1,r.bar7.val[8]*1,r.bar7.val[9]*1,r.bar7.val[10]*1,r.bar7.val[11]*1]
                    },
                    {
                        name: 'Biaya',
                        data: [r.bar8.val[0]*1,r.bar8.val[1]*1,r.bar8.val[2]*1,r.bar8.val[3]*1,r.bar8.val[4]*1,r.bar8.val[5]*1,r.bar8.val[6]*1,r.bar8.val[7]*1,r.bar8.val[8]*1,r.bar8.val[9]*1,r.bar8.val[10]*1,r.bar8.val[11]*1]
                    }]
                }); */
        }
    });
    return chart;
};

var chart = ajaxDataRenderer(m_data);
// console.log(chart)
// var isi = [4239.282, 2665.438];
// var label = ["South Sulawesi", "Southeast Sulawesi"];
// column([{name: lang('Produksi'),data: isi}], 'pie1', lang('Produksi Kakao Tersertifikasi'), 'Ton', ['#3B5323'], label)

// column([
//     {name: lang('UTZ'),data: [4]},
//     // {name: lang('Rainforest'),data: [4]},
//     // {name: lang('Fair Trade'),data: [5]},
//     {name: lang('Organic'),data: [6]}],
//      'pie1', lang('Sertifikasi Petani'), lang('Farmers'), ['#3B5323','#4E8419','#61B50F','#75E605'], [2,3],'normal',0,false)
