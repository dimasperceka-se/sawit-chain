function lang(str) {
    try {
        if (lang_arr[str] != undefined)
            return lang_arr[str];
        else
            return str
    } catch (e) {
        return str;
    }
}
var preview_type            = '';
var preview_prescription_id = null;
var preview_dispense_id     = null;
var preview_order_id        = null;
function preview_cetak_surat(url,width,left) {
    setup_preview('printserver');
    preview_type            = 'cetak';
    preview_prescription_id = 1;
    dvpreview.iframe.setAttribute('src',url);
    // if (width>0) dvpreview.frame.setAttribute('style','width:'+width+'px;left:'+left);
}
function preview_link(url,width,left) {
    setup_preview_link('printserver');
    preview_prescription_id = 1;
    dvpreview.iframe.setAttribute('src',url);        
    if (width>0) dvpreview.frame.setAttribute('style','width:'+width+'px;left:'+left);
}
function preview_video(url) {
    setup_videopreview('video');
    preview_type = 'video';
    preview_prescription_id = 1;
    dvpreview.iframe.setAttribute('src',url);
}


function checkFileExistsGeneral(url) {
    var http = new XMLHttpRequest();
    http.open('HEAD', url, false);
    http.send();
    return http.status!=404;
}

function checkImageExistsGeneral(imageUrl, callBack) {
    var imageData = new Image();
    imageData.onload = function() {
        callBack(true);
    };
    imageData.onerror = function() {
        callBack(false);
    };
    imageData.src = imageUrl;
}
// function preview_image(url,width,top) {
//     setup_imagepreview('image');
//     preview_type = 'image';
//     preview_prescription_id = 1;
//     dvpreview.iframe.setAttribute('src',url);
//     dvpreview.iframe.style.border = '1';
//     if (width>0) {
//         if (width>0) dvpreview.frame.setAttribute('style','position: fixed; width:60%; height:80%;align:center;padding:5px;');
//         //if (width>0) dvpreview.frame.setAttribute('style','float: left;height: 400px;width: 600px;padding: 2px;border: 1px #a0b1a0 solid;
//             //margin-top: 50px;margin-left: 2px;margin-bottom: 50px;');
//     }
// }

function openPanel(response,aurl,cont) {
    $('#'+cont).html(response);
    $('#'+cont).removeClass('cover');
    $( 'html, body' ).animate({ scrollTop: 0 }, 500);
    var module = aurl.replace(m_url+'/', '');
    $.get(m_api+'/page/info?module='+module, function(data) {
        if (data) {
            $('#page-info-content').html(data.Content);
            var button = '<button type="button" class="btn btn-space btn-default md-trigger" data-modal="page-info"><i class="icon s7-light"></i></button>';
            $('.page-head .row .col-md-2 h2').prepend(button);
            $('.md-trigger').modalEffects();
        }
    });
}

function link(aurl,cont) {
    if (cont===undefined) cont='wrapper';
    $('#'+cont).addClass('cover');
    $('#'+cont).html('');
    $.ajax({
        url: aurl,
        type: 'GET',
        // dataType: 'default: Intelligent Guess (Other values: xml, json, script, or html)',
        data: {ajax: '1'},
    })
    .done(function(response) {
        openPanel(response,aurl,cont);
    })
    .fail(function(response) {
    
        if (response.statusText == 'Unauthorized' && Ext.getCmp('frm-revoke-session-password') === undefined) {
            
            function unlock() {
                var passwd = Ext.getCmp('frm-revoke-session-password').getValue();
                Ext.Ajax.request({
                    url: '/api/common/revoke',
                    method:'POST',
                    params: {
                        uid:ktv.ktv_session,
                        passwd:passwd
                    },
                    success: function(response){
                        win.close();
                        link(aurl,cont);
                    }
                });
            }
    
            var win = Ext.create('Ext.Window',{
                modal:true,
                constraint:true,
                frame:true,
                title:'Session Expired',
                items:[
                    {
                        xtype:'form',
                        padding: 10,
                        items:[
                            {
                                xtype:'panel',
                                flex:1,
                                height:80,
                                width:350,
                                html:'<div>'+ktv.ktv_fullname+', your session has expired, please submit your password below to revoke your session or click the logout button to sign in as a different user</div>'
                            },
                            {
                                xtype:'textfield',
                                width:350,
                                height:40,
                                fieldStyle:'text-align:center',
                                id:'frm-revoke-session-password',
                                inputType:'password',
                                allowBlank:false,
                                emptyText:'Enter your password',
                                listeners: {
                                    specialkey: function(field, e){
                                        if (e.getKey() == e.ENTER) {
                                            unlock();
                                        }
                                    }
                                }
                            }    
                        ]
                    }
                ],
                buttonAlign:'center',
                buttons:[
                    {
                        xtype:'button',
                        text:'Unlock',
                        handler: function() {
                            unlock();
                        }
                    },
                    {
                        xtype:'button',
                        text:'Logout',
                        handler: function() {
                            Ext.MessageBox.show({
                                title: 'Logout',
                                msg: 'Are you sure you want to logout?',
                                buttonText:{ 
                                    yes: "Yes, I want to logout", 
                                },
                                fn: (btn) => {
                                    console.log('Application Logout..');
                                    if(btn === 'yes'){
                                        window.location = '/';
                                    }
                                }
                            });
                        }
                    }
                ]
            }).show();
          }
    })
    .always(function() {
        // console.log("complete");
    });    
}
function linknotif(aurl,memberLoanID) {

    var cont='wrapper';
    $('#'+cont).addClass('cover');
    $('#'+cont).html('');
    Ext.Ajax.request({
        url: aurl,
        async:true,
        method: 'GET',
        params: {ajax: '1',memberLoanID:memberLoanID},
        success: function(fp, o){
            $('#'+cont).html(fp.responseText);
            $('#'+cont).removeClass('cover');
        }
    })
    Ext.Ajax.request({
        url: '<?=$ApiUrl?>/loan/SetReadLoanNotif',
        async:true,
        method: 'POST',
        params: {Status: 1,memberLoanID:memberLoanID},
        success: function(fp, o){
        }
    })
}

function getNotifHeader()
{
     Ext.Ajax.request({
        url: m_api+'/loan/GetNotifHeader',
        success: function(response){
            var text = Ext.decode(response.responseText);
            $('#NumNotifHeader').text(text.total);
            if (text.total !== '0') {
                $('#notif-indicator').removeClass('hidden');
            } else {
                $('#notif-indicator').addClass('hidden');
            }

            var ListNotif = '';
            for(var i = 0; i < text.data.length; i++) {
                var obj = text.data[i];
                ListNotif += '<li><a href="<?php echo site_url()?>/'+obj.Action+'" onClick="linknotif(this.href,'+obj.memberLoanID+');return false;"">\
                                <div class="logo"><span class="icon s7-pin"></span></div>\
                                <div class="user-content">'+obj.Message+'</div>\
                            </a></li>';
            }
            var lists = document.getElementById('ListNotifHeader');
            if(lists !== undefined && lists !== null){
                lists.innerHTML = ListNotif;
            }
        }
    });
}
function loadAnnouncement()
{
    $.get(m_api+'/announcement/list', function(data) {
        if (data) {
            $.each(data, function(index, val) {
                tpl = '<li>\
                            <div class="icon"><span class="icon s7-speaker"></span></div>\
                            <div class="content"><span>'+val.Message+'</span></div>\
                        </li>';
                $('ul#announcement').append(tpl);
            });
        }
    });
}
function loadDocument()
{
    $.get(m_api+'/document/list', function(data) {
        if (data) {
            $.each(data, function(index, val) {
                tpl = '<li>\
                            <div class="icon"><span class="icon s7-file"></span></div>\
                            <div class="content"><a target="_blank" href="'+m_url+'/documents/'+val.FileName+'">'+val.FileLabel+'</a></div>\
                        </li>';
                $('ul#document').append(tpl);
            });
        }
    });
}


function startSync()
{
    // Ext.Ajax.timeout= 60000; 
    // Ext.override(Ext.form.Basic, { timeout: Ext.Ajax.timeout / 1000 });
    // Ext.override(Ext.data.proxy.Server, { timeout: Ext.Ajax.timeout });
    // Ext.override(Ext.data.Connection, { timeout: Ext.Ajax.timeout });

    var msgbox =  Ext.MessageBox.show({
           msg: '<center>Mohon menunggu hingga proses sinkronisasi data selesai</center>',
           width:370,
           wait:true
        });

     Ext.Ajax.request({
        url: m_api+'index.php/cooperatives/sync',
        success: function(response){
            var text = Ext.decode(response.responseText);
            Ext.MessageBox.alert('Sinkronisasi Data', text.message);
        }
    });

}

function ifNaN(value, alt) {
    if (isNaN(value)) {
        return alt;
    }
    return value;
}

function GetCurrIdByCode(CtCode){
    var CurrID;
    switch(CtCode){
        case 'MY':
            CurrID = '2';
        break;
        default:
            CurrID = '1';
        break;
    }
    return CurrID;
}

function GetDefaultContentNoData(){
    var HtmlReturn;
    HtmlReturn = '<div class="Sfr_ContDataNotFound"><img src="'+m_url+'/assets/css/nodata-search.png" width="48" /><p style="margin-top:6px;">'+lang('No Data Available')+'</p></div>';
    return HtmlReturn;
}

function checkImageExistsGeneral(imageUrl, callBack) {
    var imageData = new Image();
    imageData.onload = function() {
        callBack(true);
    };
    imageData.onerror = function() {
        callBack(false);
    };
    imageData.src = imageUrl;
}