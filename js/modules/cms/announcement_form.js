$(document).ready(function() {
    $('#breadcrumb_title').text(lang('CMS Announcement'));
    $('#first-breadcrumb').text(lang('Announcement'));
    $('#second-breadcrumb').text(lang('Add Announcement'));

    //initialize the javascript
    $('#announcement-description').summernote({
        fontNames: ["Helvetica", "sans-serif", "Arial", "Arial Black", "Comic Sans MS", "Courier New"],
        fontNamesIgnoreCheck: ["Helvetica", "sans-serif", "Arial", "Arial Black", "Comic Sans MS", "Courier New"],
        height: 400,
        placeholder: 'Type news content here...',
        disableResizeEditor: true,
        toolbar: [
            ['style', ['style']],
            ['font', ['bold', 'italic', 'underline', 'strikethrough', 'superscript', 'subscript', 'clear']],
            ['fontname', ['fontnames']],
            ['fontsize', ['fontsize']],
            ['color', ['color']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['height', ['height']],
            ['table', ['table']],
            ['videos', ['videos']],
            ['insert', ['link', 'picture', 'hr', 'video']],
            ['view', ['fullscreen' /*, 'codeview' */ ]], // remove codeview button 
            ['help', ['help']]
        ],
        onChange: function() {
            //$("#stage_"+i).val($("#description_"+i).code());
        }
    });

    $('.note-editing-area').addClass('validate');

    $lang = $('#nf-language-switcher').val();
    if(m_announ_id != 0){
        $.get(m_api_url+'/announcement/detail?AnnID=' + m_announ_id + '&Language=' + $lang , function(ret) {
            populate('#Announcement-Form', ret.data);
            $('#announcement-description').summernote('code', ret.data.Content);
            $('#announcement-status').val(ret.data.StatusType);
            $('#announcement-status option[value='+ret.data.StatusType+']').attr('selected','selected');
            $('#announcement-status').select2('val', ret.data.StatusType);
            
            var partners = [];
            if(ret.data.PartnerIDImplode != null){
                var expPartners = ret.data.PartnerIDImplode.split(',');
                for(var i = 0; i < expPartners.length ; i++){
                    partners.push(expPartners[i]);
                }
            }
            $('#partner-access').select2('val', partners);
            $roles = [];
            if(ret.data.RoleAccessFarmer != null){
                $roles.push('RoleAccessFarmer');
            }
            if(ret.data.RoleAccessStaff != null){
                $roles.push('RoleAccessStaff');
            }
            if(ret.data.RoleAccessTrader != null){
                $roles.push('RoleAccessTrader');
            }
            if(ret.data.RoleAccessRetailer != null){
                $roles.push('RoleAccessRetailer');
            }
            $('#role-access').select2('val', $roles);
            if(ret.data.StatusType == 'Public'){
                $(".partner-access").removeClass('show');
            } else {
                $(".partner-access").addClass('show');
            }
        });

        $('#nf-language-switcher').change(function(){
            $lang = $(this).val();
            $.get(m_api_url+'/announcement/detail?AnnID=' + m_announ_id + '&Language=' + $lang , function(ret) {
                populate('#Announcement-Form', ret.data);
                $('#announcement-description').summernote('code', '');
                $('#announcement-description').summernote('pasteHTML', ret.data.Content);
                $('#announcement-status').val(ret.data.StatusType);
                $('#announcement-status option[value='+ret.data.StatusType+']').attr('selected','selected');
                $('#announcement-status').select2('val', ret.data.StatusType);
                if(ret.data.StatusType == 'Public'){
                    $(".partner-access").removeClass('show');
                } else {
                    $(".partner-access").addClass('show');
                }
            });
        });

    } else {
        $('input[name=PostedBy]').val(m_author);
    }

    $('#nf-language-switcher').select2({
        width: '100%',
        placeholder: "Select Language"
    });

    $('#announcement-status').select2({
        width: '100%',
        placeholder: "Select status"
    });

    if(m_announ_id == 0){
        $("#announcement-status").select2("val", "Public");
    }

    $('#announcement-status').on('select2:select', function (e) {
        var data = e.params.data;
        if(data.text == 'Private'){
            $(".partner-access").addClass('show');
        } else {
            $(".partner-access").removeClass('show');
        }
    });

    $('#partner-access').select2({
        width: '100%',
        multiple:true,
        placeholder: "Select Partner Access"
    });

    $('#role-access').select2({
        width: '100%',
        multiple:true,
        placeholder: "Select Role Access"
    });

    $("input[id^='upload_file']").each(function() {
        var id = parseInt(this.id.replace("upload_file", ""));
        $("#upload_file" + id).change(function() {
            if ($("#upload_file" + id).val() != "") {
                $("#moreImageUploadLink").show();
            }
        });
    });

    var upload_number = 2;
    $('#attachMore').click(function() {
        var moreUploadTag = '';
        moreUploadTag += '<div class="row"><div class="col-md-8">';
        moreUploadTag += '<div class="attachment-file"><input class="form-control" type="file" class="attachment-files" onchange="ValidateSize(this)" id="upload_file' + upload_number + '" name="upload_file' + upload_number + '"/></div>';
        moreUploadTag += '</div><div class="col-md-4" style="margin-top:15px;"><a href="javascript:del_file(' + upload_number + ')" style="cursor:pointer;text-decoration:underline;margin-left:20px;" onclick="return delConfirm()">delete</a></div></div>';
        $('<dl class="rm-t rm-b" id="delete_file' + upload_number + '">' + moreUploadTag + '</dl>').fadeIn('slow').appendTo('#moreImageUpload');
        upload_number++;
    });
});

$('#savePublish').on('click', function(){

    var formData = new FormData();

    $lang = $('#nf-language-switcher').val();

    //Form data
    var form_data = $('form#Announcement-Form').serializeArray();
    $.each(form_data, function (key, input) {
        //formData.append(input.name, input.value);
        if(input.name == 'Content'){
            formData.append(input.name, htmlentities.encode(input.value));
        } else {
            formData.append(input.name, input.value);
        }
    });

    formData.append('submit','publish');

    if(validateFormInput()){
        // process the form
        $.ajax({
            type        : 'POST',
            url          :  m_api_url+'/announcement/detail',
            data         :  formData,
            processData: false,
            contentType: false
        }).done(function(data) {
            // here we will handle errors and validation messages
            if(data.success == true){
                link(baseUrl + 'cms/announcement?page=' + m_current_page + '&language=' + $lang);
            }
        });
    }
});

$('#saveUnPublish').on('click', function(){

    var formData = new FormData();

    $lang = $('#nf-language-switcher').val();

    //Form data
    var form_data = $('form#Announcement-Form').serializeArray();
    $.each(form_data, function (key, input) {
        //formData.append(input.name, input.value);
        if(input.name == 'Content'){
            formData.append(input.name, htmlentities.encode(input.value));
        } else {
            formData.append(input.name, input.value);
        }
    });

    formData.append('submit','unpublish');

    if(validateFormInput()){
        // process the form
        $.ajax({
            type        : 'POST',
            url          :  m_api_url+'/announcement/detail',
            data         :  formData,
            processData: false,
            contentType: false
        }).done(function(data) {
            // here we will handle errors and validation messages
            if(data.success == true){
                link(baseUrl + 'cms/announcement?page=' + m_current_page + '&language=' + $lang);
            }
        });
    }
});

$('#saveAsDraft').on('click', function(){

    var formData = new FormData();

    $lang = $('#nf-language-switcher').val();

    //Form data
    var form_data = $('form#Announcement-Form').serializeArray();
    $.each(form_data, function (key, input) {
        //formData.append(input.name, input.value);
        if(input.name == 'Content'){
            formData.append(input.name, htmlentities.encode(input.value));
        } else {
            formData.append(input.name, input.value);
        }
    });

    formData.append('submit','draft');

    if(validateFormInput()){
        // process the form
        $.ajax({
            type        : 'POST',
            url          :  m_api_url+'/announcement/detail',
            data         :  formData,
            processData: false,
            contentType: false
        }).done(function(data) {
            // here we will handle errors and validation messages
            if(data.success == true){
                link(baseUrl + 'cms/announcement?page=' + m_current_page + '&language=' + $lang);
            }
        });
    }
});

$('a#backLink').on('click', function(){
    $lang = $('#nf-language-switcher').val();
    link(baseUrl + 'cms/announcement?page=' + m_current_page + '&language=' + $lang);
}); 