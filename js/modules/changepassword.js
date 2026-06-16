$(function(){

    //ganti check password (begin)
    $(document).on( "blur", "#oldpassword", function(e) {
        var nilaiNya = String($(this).val());
        if(nilaiNya == ""){
            $("#notoldpassword").show();
        }else{
            $("#notoldpassword").hide();
        }
    });

    $(document).on( "blur", "#newpassword", function(e) {
        var nilaiNya = String($(this).val());
        var regexRule=  /^(?=.*\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[^a-zA-Z0-9])(?!.*\s).{8,14}$/;

        if(nilaiNya.match(regexRule)){
            $("#notnewpassword").hide();
        }else{
            $("#notnewpassword").show();
        }
    });

    $(document).on( "blur", "#newpassword_confirm", function(e) {
        var passNya = String($("#newpassword").val());
        var passConfNya = String($(this).val());

        if(passNya == passConfNya){
            $("#notnewpassword_confirm").hide();
        }else{
            $("#notnewpassword_confirm").show();
        }
    });
    //ganti check password (end)

	$('#save_password').off('click').on('click', function(event) {
		event.preventDefault();
		var validForm = true;

        Ext.MessageBox.show({
            msg: 'Loading, please wait...',
            progressText: 'Saving...',
            width:300,
            wait:true,
            waitConfig: {interval:200},
            icon:'ext-mb-download', //custom class in msg-box.html
            iconHeight: 50,
            animateTarget: 'mb7'
        });

        $('#form-password .form-control').each(function(){
            if($(this).val() == ""){
                validForm = false;
            }
        });

        $("#form-password .parsley-errors-list").each(function(){
            if($(this).is(":visible")){
                validForm = false;
            }
        });

        if(validForm == true){
            $.post(m_password, $('#form-password').serialize(), function(data, textStatus, xhr) {
                Ext.MessageBox.hide();
                if (data == true) {
                    $('input').val('');
                    Ext.MessageBox.show({
                        title: 'Information',
                        msg: lang('Password Updated'),
                        buttons: Ext.MessageBox.OK,
                        animateTarget: 'mb9',
                        icon: 'ext-mb-success'
                    });
                } else {
                    Ext.MessageBox.show({
                        title: 'Warning',
                        msg: lang(data),
                        buttons: Ext.MessageBox.OK,
                        animateTarget: 'mb9',
                        icon: 'ext-mb-info'
                    });
                };
            });
        }else{
            Ext.MessageBox.hide();
            Ext.MessageBox.show({
                title: 'Information',
                msg: lang('Form not valid yet!'),
                buttons: Ext.MessageBox.OK,
                animateTarget: 'mb9',
                icon: 'ext-mb-info'
            });
        }
	});
    //ganti password (end)

	$('#save_profile').off('click').on('click', function(event) {
		event.preventDefault();
		$.post(m_profile, $('#form-profile').serialize(), function(data, textStatus, xhr) {
			if (data) {
				Ext.MessageBox.alert('Success', lang('Profile Updated'));
				$('#form-bp2').modal('hide');
                window.location.reload(true);
			} else {
				Ext.MessageBox.alert('Warning', lang('Failed to update profile'));
			};
		});
	});
	loadProfile();
})

function loadProfile () {
	$.get(m_profile, function(data) {
		$('option[value="'+data.UserLanguage+'"]').attr('selected', 'selected');;
		$('option[value="'+data.UserNotification+'"]').attr('selected', 'selected');;
	});
}