function loadView(){
    $('#breadcrumb_title').text(lang('CMS Document'));
    $('#first-breadcrumb').text(lang('Document'));
    $('#second-breadcrumb').text(lang('Document List'));

    $lang = $('#nf-language-switcher').val();
    
    $.get(m_api_url+'/document/list?Page=' + m_current_page + '&Language=' + $lang , function(data) {
        if (data) {
            getData(data)
        } else {
            $('#main-document').empty();
            $('#ItemPagination').empty();
        }
    });

    $('#nf-language-switcher').change(function(){
        $lang = $(this).val();

        $.get(m_api_url+'/document/list?Page=' + 1 + '&Language=' + $lang , function(data) {
            getData(data)
        });
    });

    /* $('#search-form-action-document').click(function(e){
        e.preventDefault();

        $.get(m_api_url+'/document/list', {
            Page      : m_current_page,
            Language  : $lang,
            search    : $('input[name="search-form-value-document"]').val()
        }, function(data) {
            getData(data)
        });
    }); */
}

$('a#backLink').on('click', function(){
    $lang = $('#nf-language-switcher').val();
    link(baseUrl + 'cms/document?page=' + 1 + '&language=' + $lang);
});

$('a#addNew').on('click', function(){
    $lang = $('#nf-language-switcher').val();
    link(baseUrl + 'cms/document/add?page=' + m_current_page + '&language=' + $lang);
}); 

function preView(){
    $('#breadcrumb_title').text(lang('CMS Document'));
    $('#first-breadcrumb').text(lang('Document'));
    $('#second-breadcrumb').text(lang('Document Preview'));

    let document_id = this.m_document_id
    let cdn_url     = this.m_cdn_url

    $lang = $('#nf-language-switcher').val();

    $.get(m_api_url+'/document/detail?DocID=' + document_id  + '&Language=' + $lang , function(ret) {
        if(ret.success == true){
            var content = '<div class="row"><div class="col-md-12"></div><div class="news-content"><div class="news-content-title">' + ret.data.Name + '</div><div class="news-content-posted"><ul><li><i class="fa fa-user"></i>' + ret.data.PostedBy + '</li><li><i class="fa fa-clock-o"></i>' + ret.data.LastUpdated + '</li><li><i class="fa fa-gear"></i>' + ret.data.StatusType + '</li></ul></div><div class="news-content-description">' + ret.data.Description + '</div><div><a class="media" href="' + cdn_url + ret.data.DocUrl + '">PDF File</a></div></div>';

            $('#preview-document').append(content);

            $('a.media').media({width:'100%', height:700});
        }

    });

    $('#nf-language-switcher').change(function(){
        $lang = $(this).val();
        $.get(m_api_url+'/document/detail?DocID=' + document_id  + '&Language=' + $lang , function(ret) {
            if(ret.success == true){
                $('#preview-document').empty();
                var content = '<div class="row"><div class="col-md-12"></div><div class="news-content"><div class="news-content-title">' + ret.data.Name + '</div><div class="news-content-posted"><ul><li><i class="fa fa-user"></i>' + ret.data.PostedBy + '</li><li><i class="fa fa-clock-o"></i>' + ret.data.LastUpdated + '</li><li><i class="fa fa-gear"></i>' + ret.data.StatusType + '</li></ul></div><div class="news-content-description">' + ret.data.Description + '</div><div><a class="media" href="' + cdn_url + ret.data.DocUrl + '">PDF File</a></div></div>';
    
                $('#preview-document').append(content);
    
                $('a.media').media({width:'100%', height:700});
            }
    
        });
    });
}

function getData(data) {
    $('#main-document').empty();
    $('#ItemPagination').empty();

    let countAll = []
    if (data.Items) {
        $.each(data.Items, function(index, val) {
            if (parseInt(val.count) > 0) {
                if (Math.abs((index+1) % 2) == 1) {
                    var Template = 'odd';
                    var ID = parseInt(index) + parseInt(1);
                    var TempID = 'temp-' + ID;
                } else {
                    var Template = 'even';
                    var ID = parseInt(index) + parseInt(1);
                    var TempID = 'temp-' + ID;
                }

                var UpdateLink = baseUrl + "cms/document/update?document_id=" + val.DocID + "&proccess=update&page=" + m_current_page + "&language=" + $lang;
                var ViewLink = baseUrl + "cms/document/update?document_id=" + val.DocID + "&proccess=preview&page=" + m_current_page + "&language=" + $lang;
                
                var content = '<div class="grid-column ' + Template + '" id="' + TempID +'"><div class="row"><div class="col-md-12"><div class="title"><a href="#" onClick="javascript:link(\'' + ViewLink + '\');">' + val.Name + '</a></div><div class="col-md-6 posted"><div class="row"><span class="bold">Posted by</span> ' + val.PostedBy + ' (' + val.LastUpdated + ')</div></div><div class="col-md-6 status right"><div class="row"><span class="bold">Status</span> : ' + val.StatusType + '</div></div><div class="summary clearfix">' + val.Summary + '</div><div class="action"><button class="btn btn-' + val.StatusPublish + '">' + ucWords(val.StatusPublish) + '</button><a href="#"  onClick="javascript:link(\'' + UpdateLink + '\');" class="btn btn-add-new"><i class="fa fa-pencil"></i> Update</a>&nbsp;<a href="#" id="deleteItem-' + val.DocID + '" href="#" onClick="javascript:deleteItem(this.id);return false;" data-id="' + val.DocID + '" data-language="' + $lang + '" class="btn btn-delete"><i class="fa fa-trash"></i> Delete</a></div></div></div></div>';
                
                $('#main-document').append(content);

                countAll.push(val.count)
            }
        });

        if (countAll.length > 0) {
            // Add Prev Page Button
            if(data.CurrentPage > data.PrevPage){
                var PrevLink = baseUrl + "cms/document?page=" + data.PrevPage + "&language=" + m_language;;
                $('#ItemPagination').append('<li><a href="#" onClick="javascript:link(\'' + PrevLink + '\');" aria-label="Previous"><span aria-hidden="true">«</span></a></li>');
            }
            // Add List of Pagination Number
            for (var i = 1; i <= data.TotalPage; i++){
                var NumberLink = baseUrl + "cms/document?page=" + i + "&language=" + m_language;;
                $active = '';
                if(i == data.CurrentPage){
                    $active = 'class="active"';
                }
                $('#ItemPagination').append('<li '+$active+'><a href="#" onClick="javascript:link(\'' + NumberLink + '\');">'+ i +'</a></li>');
            }
            // Add Next Page Button
            if(data.CurrentPage < data.NextPage){
                var NextLink = baseUrl + "cms/document?page=" + data.NextPage + "&language=" + m_language;;
                $('#ItemPagination').append('<li><a href="#" onClick="javascript:link(\'' + NextLink + '\');" aria-label="Next"><span aria-hidden="true">»</span></a></li>');
            }
        }
    } else {
        $('#main-document').empty();
        $('#ItemPagination').empty();
    }
}

function deleteItem(id){
    $id = $('#' + id + '').data('id');
    $lang = $('#' + id + '').data('language');
    $.confirm({
        title: 'Warning',
        content: 'Are you sure to delete this items',
        icon: 'fa fa-warning',
        animation: 'scale',
        closeAnimation: 'scale',
        opacity: 1,
        buttons: {
            confirm: {
                btnClass: 'btn-add-new',
                action : function(){
                    $.ajax({
                        url: m_api_url + '/document/detail',
                        type: 'DELETE',
                        data: {
                            'DocID' : $id
                        },
                        success: function(result) {
                            // Do something with the result
                            if(result.success == true){
                                $.get(m_api_url+'/document/list?Page=' + 1 + '&Language=' + $lang , function(data) {
                                    if (data) {
                                        /*optional stuff to do after success */
                                        if (data.Items) {
                                            $('#main-document').empty();
                                            $.each(data.Items, function(index, val) {
                                                if (Math.abs((index+1) % 2) == 1) {
                                                    var Template = 'odd';
                                                    var ID = parseInt(index) + parseInt(1);
                                                    var TempID = 'temp-' + ID;
                                                } else {
                                                    var Template = 'even';
                                                    var ID = parseInt(index) + parseInt(1);
                                                    var TempID = 'temp-' + ID;
                                                }
                                                
                                                var UpdateLink = baseUrl + "cms/document/update?document_id=" + val.DocID + "&proccess=update&page=" + m_current_page + "&language=" + $lang;
                                                var ViewLink = baseUrl + "cms/document/update?document_id=" + val.DocID + "&proccess=preview&page=" + m_current_page + "&language=" + $lang;
                                                
                                                var content = '<div class="grid-column ' + Template + '" id="' + TempID +'"><div class="row"><div class="col-md-12"><div class="title"><a href="#" onClick="javascript:link(\'' + ViewLink + '\');">' + val.Name + '</a></div><div class="col-md-6 posted"><div class="row"><span class="bold">Posted by</span> ' + val.PostedBy + ' (' + val.LastUpdated + ')</div></div><div class="col-md-6 status right"><div class="row"><span class="bold">Status</span> : ' + val.StatusType + '</div></div><div class="summary clearfix">' + val.Summary + '</div><div class="action"><button class="btn btn-' + val.StatusPublish + '">' + ucWords(val.StatusPublish) + '</button><a href="#"  onClick="javascript:link(\'' + UpdateLink + '\');" class="btn btn-add-new"><i class="fa fa-pencil"></i> Update</a>&nbsp;<a href="#" id="deleteItem-' + val.DocID + '" href="#" onClick="javascript:deleteItem(this.id);return false;" data-id="' + val.DocID + '" data-language="' + $lang + '" class="btn btn-delete"><i class="fa fa-trash"></i> Delete</a></div></div></div></div>';
                                                
                                                $('#main-document').append(content);
                                                
                                            });

                                            $('#ItemPagination').empty();
                                            // Add Prev Page Button
                                            if(data.CurrentPage > data.PrevPage){
                                                var PrevLink = baseUrl + "cms/document?page=" + data.PrevPage + '&language=' + $lang;
                                                $('#ItemPagination').append('<li><a href="#" onClick="javascript:link(\'' + PrevLink + '\');" aria-label="Previous"><span aria-hidden="true">«</span></a></li>');
                                            }
                                            // Add List of Pagination Number
                                            for (var i = 1; i <= data.TotalPage; i++){
                                                var NumberLink = baseUrl + "cms/document?page=" + i + '&language=' + $lang;
                                                $active = '';
                                                if(i == data.CurrentPage){
                                                    $active = 'class="active"';
                                                }
                                                $('#ItemPagination').append('<li '+$active+'><a href="#" onClick="javascript:link(\'' + NumberLink + '\');">'+ i +'</a></li>');
                                            }

                                            // Add Next Page Button
                                            if(data.CurrentPage < data.NextPage){
                                                var NextLink = baseUrl + "cms/document?page=" + data.NextPage + '&language=' + $lang;
                                                $('#ItemPagination').append('<li><a href="#" onClick="javascript:link(\'' + NextLink + '\');" aria-label="Next"><span aria-hidden="true">»</span></a></li>');
                                            }
                                        } else {
                                            $('#main-document').empty();
                                            $('#ItemPagination').empty();
                                        }
                                    } else {
                                        $('#main-document').empty();
                                        $('#ItemPagination').empty();
                                    }
                                });
                            }
                        }
                    });
                }
            },
            cancel: {
                //$.alert('you clicked on <strong>cancel</strong>');
                btnClass: 'btn-delete'
            }
        }
    });
}

function ucWords(str){
    str = str.toLowerCase().replace(/\b[a-z]/g, function(letter) {
        return letter.toUpperCase();
    });
    return str;
}