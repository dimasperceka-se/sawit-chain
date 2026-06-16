/******************************************
 *  Author : n1colius.lau@gmail.com   
 *  Created On : Fri Sep 14 2018
 *  File : WinFormNews.js
 *******************************************/

Ext.define('Koltiva.view.CMS.WinFormNews' ,{
    extend: 'Ext.window.Window',
    id: 'Koltiva.view.CMS.WinFormNews',
    title: lang('Form Input News'),
    closable: true,
    modal: true,
    closeAction: 'destroy',
    width: '70%',
    height: '90%',
    overflowY: 'auto',
    viewVar: false,
    setViewVar: function(value){
        this.viewVar = value;
    },
    initComponent: function() {
        var thisObj = this;

        //Store
        thisObj.StoreCmbPartner = Ext.create('Koltiva.store.ComboGeneral.CmbPartnerCommon');

        //items -------------------------------------------------------------- (Begin)
        thisObj.items = [{
            xtype: 'form',
            id: 'Koltiva.view.CMS.WinFormNews-Form',
            fileUpload: true,
            padding:'5 25 5 8',            
            items:[{
                layout: 'column',
                border: false,
                items:[{
                    columnWidth: 1,
                    layout:'form',
                    defaults:{
                        labelWidth: 150,
                    },
                    items:[{
                        xtype: 'hiddenfield',
                        id: 'Koltiva.view.CMS.WinFormNews-Form-NewsID',
                        name: 'Koltiva.view.CMS.WinFormNews-Form-NewsID'
                    },{
                        xtype: 'textfield',
                        id: 'Koltiva.view.CMS.WinFormNews-Form-Title',
                        name: 'Koltiva.view.CMS.WinFormNews-Form-Title',
                        fieldLabel: lang('Title'),
                        allowBlank: false
                    },{
                        layout:'column',
                        border:false,          
                        items:[{
                            columnWidth: 0.6,
                            border: false,
                            layout: 'form',
                            items:[{
                                xtype: 'fileuploadfield',
                                labelWidth: 150,
                                fieldLabel: lang('News Picture'),
                                id: 'Koltiva.view.CMS.WinFormNews-Form-PhotoFile',
                                name: 'Koltiva.view.CMS.WinFormNews-Form-PhotoFile',
                                buttonText: 'Browse',
                                listeners: {
                                    'change': function (fb, v) {
                                        Ext.getCmp('Koltiva.view.CMS.WinFormNews-Form').getForm().submit({
                                            url: m_api + '/cms/news_input_photo',
                                            clientValidation: false,
                                            params: {
                                                OpsiDisplay: thisObj.viewVar.OpsiDisplay,
                                                NewsID: Ext.getCmp('Koltiva.view.CMS.WinFormNews-Form-NewsID').getValue()
                                            },
                                            waitMsg: 'Sending Photo...',
                                            success: function (fp, o) {
                                                Ext.getCmp('Koltiva.view.CMS.WinFormNews-Form-PhotoFilePic').update('<img src="'+m_api_base_url+'/uploads/news/'+o.result.file+'" height="240" style="float:right;" />');
                                                
                                                var x = document.getElementById('img_'+o.result.NewsID);
                                                x.setAttribute("src", m_api_base_url+'/uploads/news/'+o.result.file);
                                            },
                                            failure: function (fp, o) {
                                                Ext.MessageBox.show({
                                                    title: lang('Attention'),
                                                    msg: lang('File upload failed'),
                                                    buttons: Ext.MessageBox.OK,
                                                    animateTarget: 'mb9',
                                                    icon: 'ext-mb-error'
                                                });
                                            }
                                        });
                                    }
                                }
                            }]
                        },{
                            columnWidth: 0.4,
                            border: false,
                            layout: 'form',
                            items:[{
                                id:'Koltiva.view.CMS.WinFormNews-Form-PhotoFilePic',
                                html:'<img src="'+m_api_base_url+'/images/video/thumb-defa.png" width="320" height="240" style="float:right;" />'        
                            },{
                                html:'<div style="float:right;margin-top:-5px;font-size:10px;font-style:italic;">'+lang('Image dimension must be in 4:3 aspect ratio')+'</div>',
                                hidden:true
                            }]
                        }]
                    },{
                        xtype: 'htmleditor',
                        id: 'Koltiva.view.CMS.WinFormNews-Form-Content',
                        name: 'Koltiva.view.CMS.WinFormNews-Form-Content',
                        fieldLabel: lang('Content'),                    
                        height: 320,
                        padding: '2',
                        enableColors: true,
                        enableAlignments: true,
                        enableSourceEdit: true,
                        enableFont: true,
                        enableFontSize: true,
                        enableFormat: true,
                        enableLinks: true,
                        enableLists: true,
                        allowBlank: false
                    },{
                        html:'<div style="float:right;margin-top:-5px;font-size:10px;font-style:italic;color:green;">Example untuk kasih image di content "&lt;img src="http://www.nikolius-lau.web.id/wp-content/uploads/2017/11/chasingthedragon.jpg" width="320"&gt;" dalam mode source edit (icon paling kanan)</div><br><div style="float:right;margin-top:-5px;font-size:10px;font-style:italic;color:red;">'+lang('Warning!, Jangan copy paste langsung dari suatu website ke dalam editor "Content" ini langsung')+'</div>',
                        hidden: true
                    },{
                        fieldLabel: lang('Status Type'),
                        xtype: 'radiogroup',       
                        id:'Koltiva.view.CMS.WinFormNews-Form-RowStatusType',                        
                        columns: 2,
                        allowBlank: false,
                        msgTarget: 'under',
                        items:[{
                            boxLabel: 'Public',
                            name: 'Koltiva.view.CMS.WinFormNews-Form-StatusType',
                            inputValue: 'public',
                            id: 'Koltiva.view.CMS.WinFormNews-Form-StatusTypePublic',
                            listeners:{
                                change: function(){
                                    return false;
                                }
                            }
                        },{
                            boxLabel: 'Private',
                            name: 'Koltiva.view.CMS.WinFormNews-Form-StatusType',
                            inputValue: 'private',
                            id: 'Koltiva.view.CMS.WinFormNews-Form-StatusTypePrivate',
                            listeners:{
                                change: function(){
                                    if(this.checked == true){
                                        Ext.getCmp('Koltiva.view.CMS.WinFormNews-Form-PartnerIDImplode').setVisible(true);
                                        Ext.getCmp('Koltiva.view.CMS.WinFormNews-Form-RowRoleAccess').setVisible(true);
                                    }else{
                                        Ext.getCmp('Koltiva.view.CMS.WinFormNews-Form-PartnerIDImplode').setVisible(false);
                                        Ext.getCmp('Koltiva.view.CMS.WinFormNews-Form-RowRoleAccess').setVisible(false);
                                    }

                                    //Scroll lagi kebawah
                                    setTimeout(function(){
                                        var d = Ext.getCmp('Koltiva.view.CMS.WinFormNews').body.dom;
                                        d.scrollTop = d.scrollHeight - d.offsetHeight;
                                    }, 500);

                                    return false;
                                }
                            }
                        }]
                    },{
                        xtype: 'itemselector',
                        flex:true,
                        id: 'Koltiva.view.CMS.WinFormNews-Form-PartnerIDImplode',
                        name: 'Koltiva.view.CMS.WinFormNews-Form-PartnerIDImplode',
                        fieldLabel: lang('Partner Access'),
                        fromTitle: lang('Available'),
                        toTitle: lang('Selected'),
                        anchor: '100%',
                        height:300,
                        hidden:true,
                        store: thisObj.StoreCmbPartner,
                        displayField: 'label',
                        valueField: 'id'
                    },{
                        html:'<div style="margin-top:-5px;"></div>',
                    },{
                        fieldLabel: lang('Role Access'),
                        xtype: 'checkboxgroup',                        
                        id:'Koltiva.view.CMS.WinFormNews-Form-RowRoleAccess',                        
                        hidden:true,
                        columns: 3,
                        items:[{
                            boxLabel: lang('Farmer'),
                            name: 'Koltiva.view.CMS.WinFormNews-Form-RoleAccessFarmer',
                            inputValue: '1',
                            id: 'Koltiva.view.CMS.WinFormNews-Form-RoleAccessFarmer',
                            listeners:{
                                change: function(){
                                    return false;
                                }
                            }
                        },{
                            boxLabel: lang('Trader'),
                            name: 'Koltiva.view.CMS.WinFormNews-Form-RoleAccessTrader',
                            inputValue: '1',
                            id: 'Koltiva.view.CMS.WinFormNews-Form-RoleAccessTrader',
                            listeners:{
                                change: function(){
                                    return false;
                                }
                            }
                        },{
                            boxLabel: lang('Staff'),
                            name: 'Koltiva.view.CMS.WinFormNews-Form-RoleAccessStaff',
                            inputValue: '1',
                            id: 'Koltiva.view.CMS.WinFormNews-Form-RoleAccessStaff',
                            listeners:{
                                change: function(){
                                    return false;
                                }
                            }
                        }]
                    }]
                }]
            }]
        }];
        //items -------------------------------------------------------------- (End)

        //buttons -------------------------------------------------------------- (begin)
        thisObj.buttons = [{
            text: lang('Save'),
            margin: '5 15 5 5',
            scale: 'large',
            ui: 's-button',
            cls: 's-blue',
            id: 'Koltiva.view.CMS.WinFormNews-Form-BtnSave',
            handler: function () {                
                var FormNya = Ext.getCmp('Koltiva.view.CMS.WinFormNews-Form').getForm();
                var FormValidOrNot = FormNya.isValid();
                                
                if (FormValidOrNot ==  true) {
                    FormNya.submit({
                        url: m_api + '/cms/news_input',
                        method:'POST',
                        params: {
                            OpsiDisplay: thisObj.viewVar.OpsiDisplay
                        },
                        waitMsg: 'Saving data...',
                        success: function(fp, o) {
                            Ext.MessageBox.show({
                                title: 'Information',
                                msg: lang('Data saved'),
                                buttons: Ext.MessageBox.OK,
                                animateTarget: 'mb9',
                                icon: 'ext-mb-success'
                            });

                            //form reset
                            FormNya.reset();

                            //refresh page content
                            Ext.getCmp('Koltiva.view.CMS.GridMainNews').LoadNewsContent(1);

                            //tutup popup
                            thisObj.close();
                        },
                        failure: function(fp, o){
                            var pesanNya;
                            if(o.result.message != undefined){
                                pesanNya = o.result.message;
                            }else{
                                pesanNya = lang('Connection error');
                            }
                            Ext.MessageBox.show({
                                title: 'Attention',
                                msg: pesanNya,
                                buttons: Ext.MessageBox.OK,
                                animateTarget: 'mb9',
                                icon: 'ext-mb-error'
                            });
                        }
                    });
                }else{
                    Ext.MessageBox.show({
                        title: lang('Attention'),
                        msg: lang('Form not valid yet'),
                        buttons: Ext.MessageBox.OK,
                        animateTarget: 'mb9',
                        icon: 'ext-mb-info'
                    });
                }
            }
        },{
            text: lang('Close'),
            margin: '5px',
            scale: 'large',
            ui: 's-button',
            cls: 's-grey',
            handler: function() {
                thisObj.close();
            }
        }];
        //buttons -------------------------------------------------------------- (end)

        this.callParent(arguments);
    },
    listeners: {
        afterRender: function(){
            var thisObj = this;

            //form reset
            var FormNya = Ext.getCmp('Koltiva.view.CMS.WinFormNews-Form').getForm();
            FormNya.reset();

            if(thisObj.viewVar.OpsiDisplay == 'insert'){
                Ext.Ajax.request({
                    waitMsg: lang('Please Wait'),
                    url: m_api + '/cms/news_input_prep',
                    method : 'GET',
                    success: function(response, opts){
                        var r = Ext.decode(response.responseText);
                        //console.log(r);
                        
                        Ext.getCmp('Koltiva.view.CMS.WinFormNews-Form-NewsID').setValue(r.NewsID);
                    },
                    failure: function(response, opts){
                        Ext.MessageBox.show({
                            title: 'Failed',
                            msg: 'Network Error',
                            buttons: Ext.MessageBox.OK,
                            animateTarget: 'mb9',
                            icon: 'ext-mb-error'
                        });
                    }
                });
            }

            if(thisObj.viewVar.OpsiDisplay == 'update'){
                //load formnya
                FormNya.load({
                    url: m_api + '/cms/news_form_open',
                    method: 'GET',
                    params: {
                        NewsID: thisObj.viewVar.NewsID
                    },
                    success: function(form, action) {
                        var r = Ext.decode(action.response.responseText);
                        //console.log(r);

                        if(r.data.PhotoFile != ""){
                            var FotoPhotoFile = m_api_base_url + '/uploads/news/'+ r.data.PhotoFile;
                            var angkaRand = Math.floor((Math.random() * 100) + 1);
                            checkImageExistsGeneral(FotoPhotoFile, function(existsImage) {
                                if (existsImage == true) {
                                    Ext.getCmp('Koltiva.view.CMS.WinFormNews-Form-PhotoFilePic').update('<img src="'+FotoPhotoFile+'?'+angkaRand+'" height="240" style="float:right;" />');
                                } else {
                                	Ext.getCmp('Koltiva.view.CMS.WinFormNews-Form-PhotoFilePic').update('<img src="'+m_api_base_url+'/images/video/thumb-defa.png" width="320" height="240" style="float:right;" />');
                                }
                            });
                        }
                    },
                    failure: function(form, action) {
                        Ext.MessageBox.show({
                            title: 'Failed',
                            msg: 'Failed to retrieve data',
                            buttons: Ext.MessageBox.OK,
                            animateTarget: 'mb9',
                            icon: 'ext-mb-error'
                        });
                    }
                });
            }

        }
    }
});