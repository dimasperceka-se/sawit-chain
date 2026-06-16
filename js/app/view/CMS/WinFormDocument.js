/******************************************
 *  Author : n1colius.lau@gmail.com   
 *  Created On : Mon Sep 17 2018
 *  File : WinFormDocument.js
 *******************************************/

Ext.define('Koltiva.view.CMS.WinFormDocument' ,{
    extend: 'Ext.window.Window',
    id: 'Koltiva.view.CMS.WinFormDocument',
    title: lang('Document Form'),
    closable: true,
    modal: true,
    closeAction: 'destroy',
    width: '60%',
    height: '80%',
    overflowY: 'auto',
    viewVar: false,
    setViewVar: function(value){
        this.viewVar = value;
    },
    listeners: {
        afterRender: function(){
            var thisObj = this;

            //form reset
            var FormNya = Ext.getCmp('Koltiva.view.CMS.WinFormDocument-Form').getForm();
            FormNya.reset();

            if(thisObj.viewVar.OpsiDisplay == 'insert'){
                Ext.Ajax.request({
                    waitMsg: lang('Please Wait'),
                    url: m_api + '/cms/document_input_prep',
                    method : 'GET',                    
                    success: function(response, opts){
                        var r = Ext.decode(response.responseText);
                        //console.log(r);
                        
                        Ext.getCmp('Koltiva.view.CMS.WinFormDocument-Form-DocID').setValue(r.DocID);
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
                    url: m_api + '/cms/document_form_open',
                    method: 'GET',
                    params: {
                        DocID: thisObj.viewVar.DocID
                    },
                    success: function(form, action) {
                        var r = Ext.decode(action.response.responseText);
                        //console.log(r);
                        
                        if(r.data.DocUrl != ""){
                            var DocUrl = m_api_base_url + '/files/cms_document/'+ r.data.DocUrl;
                            var angkaRand = Math.floor((Math.random() * 100) + 1);

                            if(checkFileExistsGeneral(DocUrl) == true){
                                Ext.getCmp('Koltiva.view.CMS.WinFormDocument-Form-DocIconDownload').update('<a style="margin-left:25px;margin-top:-7px;" title="Download Document" target="_blank" href="'+DocUrl+'?'+angkaRand+'"><img src="'+m_api_base_url+'/images/pdf-icon.png" height="50" />'+r.data.DocUrl+'</a>');
                                Ext.getCmp('DocumentUpload').setValue(r.data.DocUrl);
                            }else{
                                Ext.getCmp('Koltiva.view.CMS.WinFormDocument-Form-DocIconDownload').update('<a style="margin-left:25px;margin-top:-7px;" title="Download Document" href="#"><img src="'+m_api_base_url+'/images/no-file-icon.png" height="50" />No File</a>');
                            }
                            Ext.getCmp('Koltiva.view.CMS.WinFormDocument-Form-DocIconDownload').doLayout();
                        }

                        //Bikin Readonly untuk Name
                        Ext.getCmp('Koltiva.view.CMS.WinFormDocument-Form-Name').setReadOnly(true);
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
    },
    initComponent: function() {
        var thisObj = this;

        thisObj.StoreCmbPartner = Ext.create('Koltiva.store.ComboGeneral.CmbPartnerCommon');

        //items ------------------------------------------------------------------------------------ (begin)
        thisObj.items = [{
            xtype: 'form',
            id: 'Koltiva.view.CMS.WinFormDocument-Form',
            fileUpload: true,
            padding:'12 30 12 12',        
            items:[{
                layout: 'column',
                border: false,
                items:[{
                    columnWidth: 1,
                    layout:'form',
                    defaults: {
                        labelWidth: 200
                    },                    
                    items:[{
                        xtype: 'hiddenfield',
                        id: 'Koltiva.view.CMS.WinFormDocument-Form-DocID',
                        name: 'Koltiva.view.CMS.WinFormDocument-Form-DocID'
                    },{
                        xtype: 'textfield',
                        id: 'Koltiva.view.CMS.WinFormDocument-Form-Name',
                        name: 'Koltiva.view.CMS.WinFormDocument-Form-Name',
                        allowBlank: false,
                        fieldLabel: lang('Name')
                    },{
                        xtype: 'textareafield',
                        id: 'Koltiva.view.CMS.WinFormDocument-Form-Description',
                        name: 'Koltiva.view.CMS.WinFormDocument-Form-Description',                        
                        fieldLabel: lang('Description')
                    },{
                        layout:'column',
                        border:false,          
                        items:[{
                            columnWidth: 0.55,
                            border: false,
                            layout: 'form',
                            items:[{
                                xtype: 'fileuploadfield',
                                labelWidth: 200,
                                fieldLabel: lang('Document File'),
                                id: 'Koltiva.view.CMS.WinFormDocument-Form-DocUrlInput',
                                name: 'Koltiva.view.CMS.WinFormDocument-Form-DocUrlInput',
                                buttonText: 'Browse',
                                listeners: {
                                    'change': function (fb, v) {
                                        Ext.getCmp('Koltiva.view.CMS.WinFormDocument-Form').getForm().submit({
                                            url: m_api + '/cms/document_input_file',
                                            clientValidation: false,
                                            params: {
                                                OpsiDisplay: thisObj.viewVar.OpsiDisplay,
                                                DocID: Ext.getCmp('Koltiva.view.CMS.WinFormDocument-Form-DocID').getValue(),
                                                Name: Ext.getCmp('Koltiva.view.CMS.WinFormDocument-Form-Name').getValue()
                                            },
                                            waitMsg: 'Sending File...',
                                            success: function (fp, o) {
                                                Ext.getCmp('Koltiva.view.CMS.WinFormDocument-Form-DocIconDownload').update('<a style="margin-left:25px;margin-top:-7px;" target="_blank" title="Download Document" href="'+m_api_base_url+'/files/cms_document/'+o.result.file_with_rand+'"><img src="'+m_api_base_url+'/images/pdf-icon.png" height="50" />'+o.result.file+'</a>');
                                                Ext.getCmp('DocumentUpload').setValue(o.result.file_with_rand);
                                            },
                                            failure: function (fp, o) {
                                                Ext.MessageBox.show({
                                                    title: lang('Attention'),
                                                    msg: o.result.message,
                                                    buttons: Ext.MessageBox.OK,
                                                    animateTarget: 'mb9',
                                                    icon: 'ext-mb-error'
                                                });
                                            }
                                        });
                                    }
                                }
                            },{
                                html:'<div style="float:right;margin-top:-5px;font-size:10px;font-style:italic;">'+lang('Document type allowed: pdf')+'</div>'
                            }]
                        },{
                            xtype: 'textfield',
                            id: 'DocumentUpload',
                            name: 'DocumentUpload',
                            fieldLabel: lang('Document Upload'),
                            hidden:true,
                        },{
                            columnWidth: 0.45,
                            border: false,
                            layout: 'form',
                            items:[{
                                id:'Koltiva.view.CMS.WinFormDocument-Form-DocIconDownload',
                                html:'<a style="margin-left:25px;margin-top:-7px;" title="Download Document" href="#"><img src="'+m_api_base_url+'/images/no-file-icon.png" height="50" />No File</a>'
                            }]
                        }]
                    },{
                        fieldLabel: lang('Status Type'),
                        xtype: 'radiogroup',       
                        id:'Koltiva.view.CMS.WinFormDocument-Form-RowStatusType',                        
                        columns: 2,
                        allowBlank: false,
                        msgTarget: 'under',
                        items:[{
                            boxLabel: 'Public',
                            name: 'Koltiva.view.CMS.WinFormDocument-Form-StatusType',
                            inputValue: 'public',
                            id: 'Koltiva.view.CMS.WinFormDocument-Form-StatusTypePublic',
                            listeners:{
                                change: function(){
                                    return false;
                                }
                            }
                        },{
                            boxLabel: 'Private',
                            name: 'Koltiva.view.CMS.WinFormDocument-Form-StatusType',
                            inputValue: 'private',
                            id: 'Koltiva.view.CMS.WinFormDocument-Form-StatusTypePrivate',
                            listeners:{
                                change: function(){
                                    if(this.checked == true){
                                        Ext.getCmp('Koltiva.view.CMS.WinFormDocument-Form-PartnerIDImplode').setVisible(true);
                                        Ext.getCmp('Koltiva.view.CMS.WinFormDocument-Form-RowRoleAccess').setVisible(true);
                                    }else{
                                        Ext.getCmp('Koltiva.view.CMS.WinFormDocument-Form-PartnerIDImplode').setVisible(false);
                                        Ext.getCmp('Koltiva.view.CMS.WinFormDocument-Form-RowRoleAccess').setVisible(false);
                                    }

                                    //Scroll lagi kebawah
                                    setTimeout(function(){
                                        var d = Ext.getCmp('Koltiva.view.CMS.WinFormDocument').body.dom;
                                        d.scrollTop = d.scrollHeight - d.offsetHeight;
                                    }, 500);

                                    return false;
                                }
                            }
                        }]
                    },{
                        xtype: 'itemselector',
                        flex:true,
                        id: 'Koltiva.view.CMS.WinFormDocument-Form-PartnerIDImplode',
                        name: 'Koltiva.view.CMS.WinFormDocument-Form-PartnerIDImplode',
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
                        id:'Koltiva.view.CMS.WinFormDocument-Form-RowRoleAccess',                        
                        hidden:true,
                        columns: 3,
                        items:[{
                            boxLabel: lang('Farmer'),
                            name: 'Koltiva.view.CMS.WinFormDocument-Form-RoleAccessFarmer',
                            inputValue: '1',
                            id: 'Koltiva.view.CMS.WinFormDocument-Form-RoleAccessFarmer',
                            listeners:{
                                change: function(){
                                    return false;
                                }
                            }
                        },{
                            boxLabel: lang('Trader'),
                            name: 'Koltiva.view.CMS.WinFormDocument-Form-RoleAccessTrader',
                            inputValue: '1',
                            id: 'Koltiva.view.CMS.WinFormDocument-Form-RoleAccessTrader',
                            listeners:{
                                change: function(){
                                    return false;
                                }
                            }
                        },{
                            boxLabel: lang('Staff'),
                            name: 'Koltiva.view.CMS.WinFormDocument-Form-RoleAccessStaff',
                            inputValue: '1',
                            id: 'Koltiva.view.CMS.WinFormDocument-Form-RoleAccessStaff',
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
        //items ------------------------------------------------------------------------------------ (end)

        //buttons -------------------------------------------------------------- (begin)
        thisObj.buttons = [{
            text: 'Save',
            margin: '5 15 5 5',
            scale: 'large',
            ui: 's-button',
            cls: 's-blue',
            id: 'Koltiva.view.CMS.WinFormDocument-Form-BtnSave',
            handler: function () {                
                var FormNya = Ext.getCmp('Koltiva.view.CMS.WinFormDocument-Form').getForm();
                var valuesForm = FormNya.getValues();

                if(valuesForm.DocumentUpload == ""){
                    Ext.MessageBox.show({
                        title: 'Error',
                        msg: 'File Document is Empty',
                        buttons: Ext.MessageBox.OK,
                        animateTarget: 'mb9',
                        icon: 'ext-mb-error'
                    });
                    return;
                }

            	if(FormNya.isValid()){                    
            		FormNya.submit({
                        url: m_api + '/cms/document_input',
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

                            //tutup popup
                            thisObj.close();
                            
                            //refresh store yg manggil
                            thisObj.viewVar.CallerStore.load();                           
                        },
                        failure: function(fp, o){
                            var pesanNya;
                            if(o.result.message != undefined){
                                pesanNya = o.result.message;
                            }else{
                                pesanNya = lang('Connection error');
                            }
                            Ext.MessageBox.show({
                                title: 'Error',
                                msg: pesanNya,
                                buttons: Ext.MessageBox.OK,
                                animateTarget: 'mb9',
                                icon: 'ext-mb-error'
                            });
                        }
                    });                    
            	}else{
            		Ext.MessageBox.show({
                        title: 'Attention',
                        msg: lang('Form not complete yet!'),
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
    }
});