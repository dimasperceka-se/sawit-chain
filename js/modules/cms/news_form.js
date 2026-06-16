
$(document).ready(function() {
    $('#breadcrumb_title').text(lang('CMS News'));
    $('#first-breadcrumb').text(lang('News'));
    $('#second-breadcrumb').text(lang('Add News'));
    
    //initialize the javascript
    $('#news-description').summernote({
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
            ['view', ['fullscreen', 'codeview' ]], // remove codeview button 
            ['help', ['help']]
        ],
        onChange: function() {
            $("#stage_"+i).val($("#description_"+i).code());
        }
    });

    $('#files').change(function(){
        readURL(this, 'PhotoBox');
    });

    $('.note-editing-area').addClass('validate');

    $lang = $('#nf-language-switcher').val();
    if(parseInt(m_news_id) != 0){
        $.get(m_api_url+'/news/detail?NewsID=' + m_news_id + '&Language=' + $lang , function(ret) {
            populate('#News-Form', ret.data);
            if(!isEmpty(ret.data.PhotoFile)){
                $('#FileIcon').remove();
                $('#PhotoBox').append('<img class="preview-photo" src="' + m_cdn_url + ret.data.PhotoFile + '">');
                $('#oldfile').val(ret.data.PhotoFile);
            }
            $('#news-description').summernote('code', ret.data.Content);
            $('#news-status').val(ret.data.StatusType);
            $('#news-status option[value='+ret.data.StatusType+']').attr('selected','selected');
            $('#news-status').select2('val', ret.data.StatusType);
            
            $('#moreImageUpload').empty();

            if (ret.data.Attachment != false) {
                var upload_number = 2;
                $.each(ret.data.Attachment, function (index, val) {
                    fileName = val.FileNameOriginal.length >= 20 ? val.FileNameOriginal.substr(0, 20) + "." + val.FileNameOriginal.split('.').pop() : val.FileNameOriginal;
                    if (upload_number <= 12) { //maksimal 10 attachment
                        var moreUploadTag = '';
                        moreUploadTag += '<div class="row"><div class="col-md-9">';
                        moreUploadTag += '<div class="attachment-file" style="margin-top:15px;text-decoration: underline;"><a class="text-danger" target="_blank" href="' + val.FileURL + '">' + fileName + '</a></div>';
                        moreUploadTag += '</div><div class="col-md-3" style="margin-top:15px;"><a style="cursor:pointer;font-weight:bold;" onclick="javascript:deleteAttachment(' + upload_number + ',' + val.CmsAttachmentID + ');return false;"><i class="fa fa-minus-circle text-danger" aria-hidden="true" title="delete"></i></a></div></div>';
                        $('<dl class="rm-t rm-b" id="delete_file' + upload_number + '">' + moreUploadTag + '</dl>').fadeIn('slow').appendTo('#moreImageUpload');
                    }

                    upload_number++;
                });
            }

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

        /* $('#nf-language-switcher').change(function(){
            $lang = $(this).val();
            $.get(m_api_url+'/news/detail?NewsID=' + m_news_id + '&Language=' + $lang , function(ret) {
                if (parseInt(m_news_id) == 0) {
                    populate('#News-Form', ret.data);
                    if(!isEmpty(ret.data.PhotoFile)){
                        $('#FileIcon').remove();
                        $('#PhotoBox').find('img').remove();
                        $('#PhotoBox').append('<img src="' + m_cdn_url + ret.data.PhotoFile + '">');
                    }

                    $('#news-description').summernote('code', '');
                    $('#news-description').summernote('pasteHTML', ret.data.Content);
                    
                    $('#news-status').val(ret.data.StatusType);
                    $('#news-status option[value='+ret.data.StatusType+']').attr('selected','selected');
                    $('#news-status').select2('val', ret.data.StatusType);
                    if(ret.data.StatusType == 'Public'){
                        $(".partner-access").removeClass('show');
                    } else {
                        $(".partner-access").addClass('show');
                    }
                }
            });
    
        }); */
    } else {
        $('input[name=PostedBy]').val(m_author);
    }

    $('#nf-language-switcher').select2({
        width: '100%',
        placeholder: "Select Language"
    });

    $('#news-status').select2({
        width: '100%',
        placeholder: "Select status"
    });

    if(m_news_id == 0){
        $("#news-status").select2("val", "Public");
    }

    $('#news-status').on('select2:select', function (e) {
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
        if (upload_number <= 12) { //maksimal 10 attachment
            var moreUploadTag = '';
            moreUploadTag += '<div class="row"><div class="col-md-9">';
            moreUploadTag += '<div class="attachment-file"><input class="form-control" type="file" class="attachment-files" onchange="ValidateSize(this)" id="upload_file' + upload_number + '" name="upload_file' + upload_number + '"/></div>';
            moreUploadTag += '</div><div class="col-md-3" style="margin-top:15px;"><a href="javascript:del_file(' + upload_number + ')" style="cursor:pointer;font-weight:bold;" onclick="return delConfirm()">delete</a></div></div>';
            $('<dl class="rm-t rm-b" id="delete_file' + upload_number + '">' + moreUploadTag + '</dl>').fadeIn('slow').appendTo('#moreImageUpload');
        }

        upload_number++;
    });
});

cmsNew('publish')
cmsNew('unpublish')
cmsNew('draft')

function cmsNew(param) {
    let id, variableSubmit, pageReturn

    pageReturn = 1
    if (param == 'publish') {
        id             = '#savePublish'
        variableSubmit = 'publish'
    } else if (param == 'unpublish') {
        id             = '#saveUnPublish'
        variableSubmit = 'unpublish'
        pageReturn     = 1
    } else {
        id             = '#saveAsDraft'
        variableSubmit = 'draft'
    }


    $(id).on('click', function(){
        Ext.MessageBox.show({
            title: lang('Please wait...'),
            msg: lang('Please do not close your browser..'),
            progressText: 'Initializing...',
            width: 300,
            wait: true,
            waitConfig: {
                interval: 300
            },
            icon: 'ext-mb-info',
            animateTarget: 'mb6'
        });

        if(!validateFormInput()) {
            Ext.MessageBox.hide();

            Ext.MessageBox.show({
                title: lang('Error'),
                msg: lang('Form not complete'),
                buttons: Ext.MessageBox.OK,
                animateTarget: 'mb9',
                icon: 'ext-mb-error'
            });

            return;
        }

        $lang = $('#nf-language-switcher').val();

        setTimeout(() => {
            var formData = new FormData();

            //Form data
            var form_data = $('form#News-Form').serializeArray();
            $.each(form_data, function (key, input) {
                //formData.append(input.name, input.value);
                if(input.name == 'Content'){
                    formData.append(input.name, htmlentities.encode(input.value));
                } else {
                    formData.append(input.name, input.value);
                }
            });

            formData.append('submit', variableSubmit);

            formData.append('file', $('#files')[0].files[0]);

            for (i = 1; i <= 12; i++) {
                if ($('#upload_file' + i).val() !== undefined)
                    formData.append('upload_file' + i, $('#upload_file' + i)[0].files[0]);
            }

            let f = function(v){
                return function(){
                    if(v == 12){
                        if(validateFormInput()){
                            $.ajax({
                                type        : 'POST',
                                url          :  m_api_url + '/news/detail',
                                data         :  formData,
                                processData: false,
                                contentType: false
                            }).error(function(data){
                                Ext.MessageBox.hide();

                                if (data.responseJSON.message !== undefined) {
                                    Ext.MessageBox.show({
                                        title: lang('Error'),
                                        msg: lang(data.responseJSON.message),
                                        buttons: Ext.MessageBox.OK,
                                        animateTarget: 'mb9',
                                        icon: 'ext-mb-error'
                                    });

                                    return;
                                }
                            }).done(function(data) {
                                Ext.MessageBox.hide();

                                // here we will handle errors and validation messages
                                if (data.message !== undefined) {
                                    Ext.MessageBox.show({
                                        title: lang('Information'),
                                        msg: lang(data.message),
                                        buttons: Ext.MessageBox.OK,
                                        animateTarget: 'mb9',
                                        icon: 'ext-mb-success'
                                    });
                                }

                                if(data.success == true){
                                    link(baseUrl + 'cms/news?page=' + pageReturn + '&language=' + $lang);
                                }
                            });
                        }
                    }else{
                        let i = v/11;
                        Ext.MessageBox.updateText(lang('Processing data...'));
                        Ext.MessageBox.updateProgress(i, Math.round(100*i)+`% ${lang('Completed')}`);
                    }
               };
            };
           
            for(let i = 1; i < 13; i++){
               setTimeout(f(i), i*500);
            }
        }, 2000)
    });
}

$('a#backLink').on('click', function(){
    $lang = $('#nf-language-switcher').val();
    link(baseUrl + 'cms/news?page=' + 1 + '&language=' + $lang);
});

function deleteAttachment(anchor, idAttachment) {

    var conf = confirm('Are you sure want to delete this record?');

    if (conf) {
        $.ajax({
            url: m_api_url + '/news/attachment',
            type: 'DELETE',
            data: {
                'CmsAttachmentID': idAttachment
            },
            success: function (result) {
                $("#delete_file" + anchor).remove();
            }
        });
    }
}