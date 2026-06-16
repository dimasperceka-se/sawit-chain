    $(function(){
        $('#btn-submit').off('click').on('click', function(event) {
            event.preventDefault();
            $.post(m_data, $('#form-profile').serialize(), function(data, textStatus, xhr) {
                if (data) {
                	Ext.MessageBox.alert('Success', lang('Profile Updated'));
                } else {
                	Ext.MessageBox.alert('Warning', lang('Failed to update profile'));                	
                };
            });
        });
        loadProfile();
    })

    function loadProfile () {
    	$.get(m_data, function(data) {
    		$('option[value="'+data.UserLanguage+'"]').attr('selected', 'selected');;
    		$('option[value="'+data.UserNotification+'"]').attr('selected', 'selected');;
    	});
    }