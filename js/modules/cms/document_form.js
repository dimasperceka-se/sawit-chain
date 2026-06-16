$(document).ready(function() {
    $('#breadcrumb_title').text(lang('CMS Document'));
    $('#first-breadcrumb').text(lang('Document'));
    $('#second-breadcrumb').text(lang('Add Document'));

    //initialize the javascript
    $('#document-description').summernote({
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
            $("#stage_"+i).val($("#description_"+i).code());
        }
    });

    $('.note-editing-area').addClass('validate');

    $lang = $('#nf-language-switcher').val();
    if(parseInt(m_document_id) != 0){
        $.get(m_api_url+'/document/detail?DocID=' + m_document_id + '&Language=' + $lang , function(ret) {
            populate('#Document-Form', ret.data);

            $('#document-description').summernote('code', ret.data.Description);
            $('#document-status').val(ret.data.StatusType);
            $('#document-status option[value='+ret.data.StatusType+']').attr('selected','selected');
            $('#document-status').select2('val', ret.data.StatusType);
            if(ret.data.DocUrl != ''){
                $('#document-url').html('<label class="properties-title" style="margin-top: 10px;">Summary</label><div><a href="' + m_cdn_url + ret.data.DocUrl + '" download target="_blank" style="color: #D83715">Download File</a></div>');
                $('#oldfile').val(ret.data.DocUrl);
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

            /* $('#nf-language-switcher').change(function(){
                $lang = $(this).val();
                $.get(m_api_url+'/document/detail?DocID=' + m_document_id + '&Language=' + $lang , function(ret) {
                    if (parseInt(document_id) == 0) {
                        populate('#Document-Form', ret.data);
                        $('#document-description').summernote('code', '');
                        $('#document-description').summernote('pasteHTML', ret.data.Description);
                        $('#document-status').val(ret.data.StatusType);
                        $('#document-status option[value='+ret.data.StatusType+']').attr('selected','selected');
                        $('#document-status').select2('val', ret.data.StatusType);
                        if(ret.data.StatusType == 'Public'){
                            $(".partner-access").removeClass('show');
                        } else {
                            $(".partner-access").addClass('show');
                        }
                    }
                });
            }); */
        });
    } else {
        $('input[name=PostedBy]').val(m_author);
    }

    $('#nf-language-switcher').select2({
        width: '100%',
        placeholder: "Select Language"
    });

    $('#document-status').select2({
        width: '100%',
        placeholder: "Select status"
    });

    if(m_document_id == 0){
        $("#document-status").select2("val", "Public");
    }

    $('#document-status').on('select2:select', function (e) {
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
        /* pageReturn     = m_current_page */
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
            var form_data = $('form#Document-Form').serializeArray();
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

            let f = function(v){
                return function(){
                    if(v == 12){
                        if(validateFormInput()){
                            $.ajax({
                                type        : 'POST',
                                url          :  m_api_url + '/document/detail',
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
                                    link(baseUrl + 'cms/document?page=' + pageReturn + '&language=' + $lang);
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

$('#files').change(function(){
    var fullPath = document.getElementById('files').value;
    if (fullPath) {
        var startIndex = (fullPath.indexOf('\\') >= 0 ? fullPath.lastIndexOf('\\') : fullPath.lastIndexOf('/'));
        var filename = fullPath.substring(startIndex);
        if (filename.indexOf('\\') === 0 || filename.indexOf('/') === 0) {
            filename = filename.substring(1);
        }

        $("#pdf").html(filename);
    }
});

$('a#backLink').on('click', function(){
    $lang = $('#nf-language-switcher').val();
    link(baseUrl + 'cms/document?page=' + 1 + '&language=' + $lang);
});