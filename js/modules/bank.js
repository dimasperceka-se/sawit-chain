// if (m_prov!='') dataDistrict(m_data,'demographic',m_petani); 
var ajaxDataRenderer = function(url) {
    var s = [];
    $('#wrapper').addClass('cover');
    $.ajax({
        type: "GET",
        url: url,
        data: {prov: m_prov,kab: m_kab,priv: m_priv,daer: m_daer,petani:m_petani,tahun:m_tahun,partner:m_partner},
        contentType: "application/json; charset=utf-8",
        dataType: "json",
        async: false,
        success: function(result) {
            var farmer_loan_pass_lt10 = 0;
            var farmer_loan_pass_mt10 = 0;
            if (farmer = result.farmer) {
                $.each(farmer, function(index, val) {
                    farmer_loan_pass_lt10 += parseInt(val.farmer_loan_pass_lt10);
                    farmer_loan_pass_mt10 += parseInt(val.farmer_loan_pass_mt10);
                });
            }
            $('#box_farmer_loan_pass_lt10').html(number_format(farmer_loan_pass_lt10,0,'.',','));
            $('#box_farmer_loan_pass_mt10').html(number_format(farmer_loan_pass_mt10,0,'.',','));

            var approved = 0, rejected = 0, amount = 0;
            if (loan = result.loan) {
                $.each(loan, function(index, val) {
                    approved    += parseInt(val.approved) + parseInt(val.finished);
                    rejected    += parseInt(val.rejected);
                    amount      += parseFloat(val.total_amount);
                });
            }
            $('#box_approved').html(number_format(approved,0,'.',','));
            $('#box_rejected').html(number_format(rejected,0,'.',','));
            $('#box_amount').html(number_format(amount,2,'.',','));

            var distance_bank = 0;
            if (distance = result.distance) {
                $.each(distance, function(index, val) {
                    distance_bank += parseInt(val.farmer);
                });
            }
            $('#box_distance_bank').html(number_format(distance_bank,0,'.',','));

            $('#wrapper').removeClass('cover');
            $('#row-fluid').css('display', '');
        }
    });
return s; 
};

var s = ajaxDataRenderer(m_data); 