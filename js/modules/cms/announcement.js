function loadView(){
    $('#breadcrumb_title').text(lang('CMS Announcement'));
    $('#first-breadcrumb').text(lang('Announcement'));
    $('#second-breadcrumb').text(lang('Announcement List'));

    $lang = $('#n-language-switcher').val();
    
    $.get(m_api_url+'/announcement/list?Page=' + m_current_page + '&Language=' + $lang , function(data) {
        /*optional stuff to do after success */
        if (data.Items) {
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

                var UpdateLink = baseUrl + "cms/announcement/update?announ_id=" + val.AnnID + "&proccess=update&page=" + m_current_page + "&language=" + $lang;
                var ViewLink = baseUrl + "cms/announcement/update?announ_id=" + val.AnnID + "&proccess=preview&page=" + m_current_page + "&language=" + $lang;
                
                var content = '<div class="grid-column ' + Template + '" id="' + TempID +'"><div class="row"><div class="col-md-12"><div class="title"><a href="#" onClick="javascript:link(\'' + ViewLink + '\');">' + val.Title + '</a></div><div class="col-md-6 posted"><div class="row"><span class="bold">Posted by</span> ' + val.PostedBy + ' (' + val.LastUpdated + ')</div></div><div class="col-md-6 status right"><div class="row"><span class="bold">Status</span> : ' + val.StatusType + '</div></div><div class="summary clearfix">' + val.Summary + '</div><div class="action"><button class="btn btn-' + val.StatusPublish + '">' + ucWords(val.StatusPublish) + '</button><a href="#"  onClick="javascript:link(\'' + UpdateLink + '\');" class="btn btn-add-new"><i class="fa fa-pencil"></i> Update</a>&nbsp;<a id="deleteItem-' + val.AnnID + '" href="#" onClick="javascript:deleteItem(this.id);return false;" data-id="' + val.AnnID + '" data-language="' + $lang + '" class="btn btn-delete"><i class="fa fa-trash"></i> Delete</a></div></div></div></div>';
                
                $('#main-announcement').append(content);
            });
        }

        // Add Prev Page Button
        if(data.CurrentPage > data.PrevPage){
            var PrevLink = baseUrl + "cms/announcement?page=" + data.PrevPage + "&language=" + m_language;
            $('#ItemPagination').append('<li><a href="#" onClick="javascript:link(\'' + PrevLink + '\');" aria-label="Previous"><span aria-hidden="true">«</span></a></li>');
        }
        // Add List of Pagination Number
        for (var i = 1; i <= data.TotalPage; i++){
            var NumberLink = baseUrl + "cms/announcement?page=" + i + "&language=" + m_language;
            $active = '';
            if(i == data.CurrentPage){
                $active = 'class="active"';
            }
            $('#ItemPagination').append('<li '+$active+'><a href="#" onClick="javascript:link(\'' + NumberLink + '\');">'+ i +'</a></li>');
        }
        // Add Next Page Button
        if(data.CurrentPage < data.NextPage){
            var NextLink = baseUrl + "cms/announcement?page=" + data.NextPage + "&language=" + m_language;
            $('#ItemPagination').append('<li><a href="#" onClick="javascript:link(\'' + NextLink + '\');" aria-label="Next"><span aria-hidden="true">»</span></a></li>');
        }
    });

    $('#n-language-switcher').change(function(){
        $lang = $(this).val();
    
        $.get(m_api_url+'/announcement/list?Page=' + m_current_page + '&Language=' + $lang , function(data) {
            /*optional stuff to do after success */
            if (data.Items) {
                $('#main-announcement').empty();
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
    
                    var UpdateLink = baseUrl + "cms/announcement/update?announ_id=" + val.AnnID + "&proccess=update&page=" + m_current_page + "&language=" + $lang;
                    var ViewLink = baseUrl + "cms/announcement/update?announ_id=" + val.AnnID + "&proccess=preview&page=" + m_current_page + "&language=" + $lang;
                    
                    var content = '<div class="grid-column ' + Template + '" id="' + TempID +'"><div class="row"><div class="col-md-12"><div class="title"><a href="#" onClick="javascript:link(\'' + ViewLink + '\');">' + val.Title + '</a></div><div class="col-md-6 posted"><div class="row"><span class="bold">Posted by</span> ' + val.PostedBy + ' (' + val.LastUpdated + ')</div></div><div class="col-md-6 status right"><div class="row"><span class="bold">Status</span> : ' + val.StatusType + '</div></div><div class="summary clearfix">' + val.Summary + '</div><div class="action"><button class="btn btn-' + val.StatusPublish + '">' + ucWords(val.StatusPublish) + '</button><a href="#"  onClick="javascript:link(\'' + UpdateLink + '\');" class="btn btn-add-new"><i class="fa fa-pencil"></i> Update</a>&nbsp;<a id="deleteItem-' + val.AnnID + '" href="#" onClick="javascript:deleteItem(this.id);return false;" data-id="' + val.AnnID + '" data-language="' + $lang + '" class="btn btn-delete"><i class="fa fa-trash"></i> Delete</a></div></div></div></div>';
                    
                    $('#main-announcement').append(content);
                });
            }
    
            $('#ItemPagination').empty();
            // Add Prev Page Button
            if(data.CurrentPage > data.PrevPage){
                var PrevLink = baseUrl + "cms/announcement?page=" + data.PrevPage + '&language=' + $lang;
                $('#ItemPagination').append('<li><a href="#" onClick="javascript:link(\'' + PrevLink + '\');" aria-label="Previous"><span aria-hidden="true">«</span></a></li>');
            }
            // Add List of Pagination Number
            for (var i = 1; i <= data.TotalPage; i++){
                var NumberLink = baseUrl + "cms/announcement?page=" + i + '&language=' + $lang;
                $active = '';
                if(i == data.CurrentPage){
                    $active = 'class="active"';
                }
                $('#ItemPagination').append('<li '+$active+'><a href="#" onClick="javascript:link(\'' + NumberLink + '\');">'+ i +'</a></li>');
            }
            // Add Next Page Button
            if(data.CurrentPage < data.NextPage){
                var NextLink = baseUrl + "cms/announcement?page=" + data.NextPage + '&language=' + $lang;
                $('#ItemPagination').append('<li><a href="#" onClick="javascript:link(\'' + NextLink + '\');" aria-label="Next"><span aria-hidden="true">»</span></a></li>');
            }
        });
    
    });
}

function preView(){
    $('#breadcrumb_title').text(lang('CMS Announcement'));
    $('#first-breadcrumb').text(lang('Announcement'));
    $('#second-breadcrumb').text(lang('Announcement Preview'));

    $lang = $('#np-language-switcher').val();

    $.get(m_api_url+'/announcement/detail?AnnID=' + announ_id  + '&Language=' + $lang , function(ret) {
        if(ret.success == true){
            var content = '<div class="row"><div class="col-md-12"></div><div class="news-content"><div class="news-content-title">' + ret.data.Title + '</div><div class="news-content-posted"><ul><li><i class="fa fa-user"></i>' + ret.data.PostedBy + '</li><li><i class="fa fa-clock-o"></i>1 Year Ago</li><li><i class="fa fa-gear"></i>' + ret.data.StatusType + '</li></ul></div><div class="news-content-description">' + ret.data.Content + '</div></div>';

            $('#preview-announcement').append(content);
        }

    });

    $('#np-language-switcher').change(function(){
        $lang = $(this).val();

        $.get(m_api_url+'/announcement/detail?AnnID=' + announ_id  + '&Language=' + $lang , function(ret) {
            if(ret.success == true){
                $('#preview-announcement').empty();

                var content = '<div class="row"><div class="col-md-12"></div><div class="news-content"><div class="news-content-title">' + ret.data.Title + '</div><div class="news-content-posted"><ul><li><i class="fa fa-user"></i>' + ret.data.PostedBy + '</li><li><i class="fa fa-clock-o"></i>1 Year Ago</li><li><i class="fa fa-gear"></i>' + ret.data.StatusType + '</li></ul></div><div class="news-content-description">' + ret.data.Content + '</div></div>';
    
                $('#preview-announcement').append(content);
            }
    
        });
    });
}

$('a#backLink').on('click', function(){
    $lang = $('#np-language-switcher').val();
    link(baseUrl + 'cms/announcement?page=' + m_current_page + '&language=' + $lang);
});

$('a#addNew').on('click', function(){
    $lang = $('#n-language-switcher').val();
    link(baseUrl + 'cms/announcement/add?page=' + m_current_page + '&language=' + $lang);
}); 

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
                        url: m_api_url + '/announcement/detail',
                        type: 'DELETE',
                        data: {
                            'AnnID' : $id
                        },
                        success: function(result) {
                            // Do something with the result
                            if(result.success == true){
                                $.get(m_api_url+'/announcement/list?Page=' + m_current_page + '&Language=' + $lang , function(data) {
                                    /*optional stuff to do after success */
                                    if (data.Items) {
                                        $('#main-announcement').empty();
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
                                            
                                            var UpdateLink = baseUrl + "cms/announcement/update?announ_id=" + val.AnnID + "&proccess=update&page=" + m_current_page + "&language=" + $lang;
                                            var ViewLink = baseUrl + "cms/announcement/update?announ_id=" + val.AnnID + "&proccess=preview&page=" + m_current_page + "&language=" + $lang;

                                            var content = '<div class="grid-column ' + Template + '" id="' + TempID +'"><div class="row"><div class="col-md-12"><div class="title"><a href="#" onClick="javascript:link(\'' + ViewLink + '\');">' + val.Title + '</a></div><div class="col-md-6 posted"><div class="row"><span class="bold">Posted by</span> ' + val.PostedBy + ' (' + val.LastUpdated + ')</div></div><div class="col-md-6 status right"><div class="row"><span class="bold">Status</span> : ' + val.StatusType + '</div></div><div class="summary clearfix">' + val.Summary + '</div><div class="action"><button class="btn btn-' + val.StatusPublish + '">' + ucWords(val.StatusPublish) + '</button><a href="#"  onClick="javascript:link(\'' + UpdateLink + '\');" class="btn btn-add-new"><i class="fa fa-pencil"></i> Update</a>&nbsp;<a id="deleteItem-' + val.AnnID + '" href="#" onClick="javascript:deleteItem(this.id);return false;" data-id="' + val.AnnID + '" data-language="' + $lang + '" class="btn btn-delete"><i class="fa fa-trash"></i> Delete</a></div></div></div></div>';
                                            
                                            $('#main-announcement').append(content);
                                            
                                        });
                                    }
                            
                                    $('#ItemPagination').empty();
                                    // Add Prev Page Button
                                    if(data.CurrentPage > data.PrevPage){
                                        var PrevLink = baseUrl + "cms/announcement?page=" + data.PrevPage + '&language=' + $lang;
                                        $('#ItemPagination').append('<li><a href="#" onClick="javascript:link(\'' + PrevLink + '\');" aria-label="Previous"><span aria-hidden="true">«</span></a></li>');
                                    }
                                    // Add List of Pagination Number
                                    for (var i = 1; i <= data.TotalPage; i++){
                                        var NumberLink = baseUrl + "cms/announcement?page=" + i + '&language=' + $lang;
                                        $active = '';
                                        if(i == data.CurrentPage){
                                            $active = 'class="active"';
                                        }
                                        $('#ItemPagination').append('<li '+$active+'><a href="#" onClick="javascript:link(\'' + NumberLink + '\');">'+ i +'</a></li>');
                                    }
                                    // Add Next Page Button
                                    if(data.CurrentPage < data.NextPage){
                                        var NextLink = baseUrl + "cms/announcement?page=" + data.NextPage + '&language=' + $lang;
                                        $('#ItemPagination').append('<li><a href="#" onClick="javascript:link(\'' + NextLink + '\');" aria-label="Next"><span aria-hidden="true">»</span></a></li>');
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