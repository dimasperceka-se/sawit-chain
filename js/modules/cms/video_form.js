$(document).ready(function() {
    $('#breadcrumb_title').text(lang('CMS Video'));
    $('#first-breadcrumb').text(lang('Video'));
    $('#second-breadcrumb').text(lang('Add Video'));

    //initialize the javascript
    $('#video-description').summernote({
        fontNames: ["Helvetica", "sans-serif", "Arial", "Arial Black", "Comic Sans MS", "Courier New"],
        fontNamesIgnoreCheck: ["Helvetica", "sans-serif", "Arial", "Arial Black", "Comic Sans MS", "Courier New"],
        height: 400,
        placeholder: 'Type video description here...',
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
    if(parseInt(m_video_id) != 0){
        $.get(m_api_url+'/video/detail?VidID=' + m_video_id + '&Language=' + $lang, function(ret) {
            
            populate('#Video-Form', ret.data);
            $('#video-description').summernote('code', ret.data.Description);

            $('#nf-language-switcher option[value='+ret.data.Language+']').attr('selected','selected');
            $('#nf-language-switcher').select2('val', ret.data.Language);

            $('#video-status option[value='+ret.data.StatusType+']').attr('selected','selected');
            $('#video-status').select2('val', ret.data.StatusType);
            
            $('#video-type').val(ret.data.VideoType);
            $('#video-type').select2('val', ret.data.VideoType);

            var partners = [];
            if(ret.data.PartnerIDImplode != null){
                var expPartners = ret.data.PartnerIDImplode.split(',');
                console.log(expPartners);
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
            $.get(m_api_url+'/video/detail?VidID=' + m_video_id + '&Language=' + $lang , function(ret) {
                if (parseInt(m_video_id) == 0) {
                    populate('#Video-Form', ret.data);
                    $('#video-description').summernote('code', '');
                    $('#video-description').summernote('pasteHTML', ret.data.Description);
        
                    $('#video-status').val(ret.data.StatusType);
                    $('#video-status option[value='+ret.data.StatusType+']').attr('selected','selected');
                    $('#video-status').select2('val', ret.data.StatusType);
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

    $('#video-status').select2({
        width: '100%',
        placeholder: "Select status"
    });

    if(m_video_id == 0){
        $("#video-status").select2("val", "Public");
    }

    $('#video-status').on('select2:select', function (e) {
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

    $('#video-type').select2({
        width: '100%',
        placeholder: "Select Video Type"
    });

    $('input[name=VideoUrl]').blur(function(){
        var url = $(this).val();
        if(url.includes('youtube')){
            $("#video-type").select2("val", "youtube");
            var youtubeID = getYoutubeID(url);
            $('input[name=VideoTypeID]').val(youtubeID);
        } else if(url.includes('vimeo')){
            var vimeoID = getVimeoID(url);
            $("#video-type").select2("val", "vimeo");
            $('input[name=VideoTypeID]').val(vimeoID);
        } else {
            $("#video-type").select2("val", "");
            $('input[name=VideoTypeID]').val('');
        }
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
            var form_data = $('form#Video-Form').serializeArray();
            $.each(form_data, function (key, input) {
                //formData.append(input.name, input.value);
                if(input.name == 'Description'){
                    formData.append(input.name, htmlentities.encode(input.value));
                } else {
                    formData.append(input.name, input.value);
                }
            });

            formData.append('submit', variableSubmit);

            let f = function(v){
                return function(){
                    if(v == 12){
                        if(validateFormInput()){
                            $.ajax({
                                type        : 'POST',
                                url          :  m_api_url + '/video/detail',
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
                                    link(baseUrl + 'cms/video?page=' + pageReturn + '&language=' + $lang);
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
    link(baseUrl + 'cms/video?page=' + 1 + '&language=' + $lang);
});