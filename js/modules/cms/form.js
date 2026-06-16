function isEmpty(str) {
    return (!str || 0 === str.length);
}

function getVimeoID($vimeo){
    var url = $vimeo;//"http://www.vimeo.com/7058755";
    var regExp = /\/(www\.)?vimeo.com\/(\d+)($|\/)/;

    var match = url.match(regExp);

    if (match){
        var id = match[2];
    }
    else{
        var id = 0;
    }
    return id;
}

function getYoutubeID(url){
    var regExp = /^.*((youtu.be\/)|(v\/)|(\/u\/\w\/)|(embed\/)|(watch\?))\??v?=?([^#&?]*).*/;
    var match = url.match(regExp);
    return (match&&match[7].length==11)? match[7] : false;
}

function validateFormInput(){
    $('.left-side .grid-content').find('.validate').each(function(idx){
        var vd = $(this);
        if(vd.val() != undefined ){
            if(vd.val() == null || vd.val() == ''){
                vd.addClass('warning');
                vd.focus(function(){
                    vd.removeClass('warning');
                }).blur(function(){
                    if(vd.val() == ''){
                        $confirm = false;
                        vd.addClass('warning');
                    } 
                });
            } 
            if($('.note-editing-area.validate').find('.note-editable').text() != ''){
                $('.note-editing-area.validate').removeClass('warning');
                $('.description.validate').removeClass('warning');
            }
            //for summernote
            $('.note-editing-area.validate').find('.note-editable').focus(function(){
                $('.note-editing-area.validate').removeClass('warning');
                $('.description.validate').removeClass('warning');
            }).blur(function(){
                if($('.note-editing-area.validate').find('.note-editable').text() == ''){
                    $('.note-editing-area.validate').addClass('warning');
                    $('.description.validate').addClass('warning');
                } 
            });
        }
    });

    //Check all if still have warning
    $confirm = true;
    $('.left-side .grid-content').find('.validate').each(function(idv){
        var vd = $(this);
        if(vd.hasClass('warning')){
            $confirm = false;
        }
    });

    return $confirm;
}

function populate(frm, data) {
    $.each(data, function(key, value){
        $('[name='+key+']', frm).val(value);
    });
}

function readURL(input, fileTarget) {
    if (input.files && input.files[0]) {
      var reader = new FileReader();
      reader.onload = function(e) {
        //$('#' + fileTarget + '').find('img').attr('src', e.target.result);
        $('#FileIcon').remove();
        $('#' + fileTarget + '').find('img').remove();
        $('#' + fileTarget + '').append('<img class="preview-photo" src="' + e.target.result + '">');
      }
  
      reader.readAsDataURL(input.files[0]);
    }
}

function del_file(eleId) {
    var ele = document.getElementById("delete_file" + eleId);
    ele.parentNode.removeChild(ele);
}

function delConfirm() {
    if (confirm("Are you really want to delete this file?")) {
        return true;
    } else {
        return false;
    }
}

function ValidateSize(file) {
    var FileSize = file.files[0].size / 1024 / 1024; // in MB
    if (FileSize > 20) {
        alert('File size exceeds 20 MB');
        this.val = "";
        return false;
    } else {
        return true;
    }
}