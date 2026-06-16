/******************************************
 *  Author : n1colius.lau@gmail.com   
 *  Created On : Mon Oct 29 2018
 *  File : WinFormAttachmentFiles.js
 *******************************************/

/**
 *  Param
 *  - TrainID
    - TrainType
    - OpsiDisplay
    - TrainAttID
    - CallerStore
 * 
 */

Ext.define('Koltiva.view.Train.WinFormAttachmentFiles' ,{
    extend: 'Ext.window.Window',
    id: 'Koltiva.view.Train.WinFormAttachmentFiles',
    title: lang('Form Attachment Training Files'),
    closable: true,
    modal: true,
    closeAction: 'destroy',
    width: '58%',
    height: '60%',
    overflowY: 'auto',
    viewVar: false,
    setViewVar: function(value){
        this.viewVar = value;
    },
    bodyStyle: {
        "background-color": "#F0F0F0"
    },
    style: 'background-color:#F0F0F0;',
    padding: 6,
    scrollOffset: 25,
    initComponent: function() {
        var thisObj = this;

        //Items ---------------------------------------------------------------------------------------------------------------------------- (Begin)
        thisObj.items = [{
            xtype: 'form',
            id: 'Koltiva.view.Train.WinFormAttachmentFiles-Form',
            fileUpload: true,
            padding:'5 25 5 8',
            items:[{
                layout: 'column',
                border: false,
                items:[{
                    columnWidth: 0.55,
                    border: false,
                    layout: 'form',
                    items:[{
                        xtype: 'hiddenfield',
                        id: 'Koltiva.view.Train.WinFormAttachmentFiles-Form-TrainAttID',
                        name: 'Koltiva.view.Train.WinFormAttachmentFiles-Form-TrainAttID'
                    },{
                        xtype: 'hiddenfield',
                        id: 'Koltiva.view.Train.WinFormAttachmentFiles-Form-FilesExist',
                        name: 'Koltiva.view.Train.WinFormAttachmentFiles-Form-FilesExist'
                    },{
                        xtype: 'fileuploadfield',
                        labelWidth: 125,
                        fieldLabel: lang('Attachment'),
                        id: 'Koltiva.view.Train.WinFormAttachmentFiles-Form-FilenameInput',
                        name: 'Koltiva.view.Train.WinFormAttachmentFiles-Form-FilenameInput',
                        buttonText: 'Browse',
                        listeners: {
                            'change': function (fb, v) {
                                Ext.getCmp('Koltiva.view.Train.WinFormAttachmentFiles-Form').getForm().submit({
                                    url: m_api + '/train/attachment_file',
                                    clientValidation: false,
                                    params: {
                                        OpsiDisplay: thisObj.viewVar.OpsiDisplay,
                                        TrainAttID: Ext.getCmp('Koltiva.view.Train.WinFormAttachmentFiles-Form-TrainAttID').getValue()
                                    },
                                    waitMsg: 'Sending Image...',
                                    success: function (fp, o) {
                                        Ext.getCmp('Koltiva.view.Train.WinFormAttachmentFiles-Form-OldPhoto').setValue(o.result.FilePath);
                                        if(o.result.extNya == 'pdf'){
                                            Ext.getCmp('Koltiva.view.Train.WinFormAttachmentFiles-Form-FileImage').update('<a href="'+o.result.file+'" title="Download File" target="_blank">'+lang('Download File')+'    <img src="'+m_api_base_url+'/images/pdf-icon.png" height="24" /></a>');
                                            Ext.getCmp('Koltiva.view.Train.WinFormAttachmentFiles-Form-FileImage').doLayout();
                                            Ext.getCmp('Koltiva.view.Train.WinFormAttachmentFiles-Form-FilesExist').setValue('yes');
                                            Ext.getCmp('Koltiva.view.Train.WinFormAttachmentFiles-Form-OldPhoto').setValue(o.result.FilePath);
                                        }else{
                                            Ext.getCmp('Koltiva.view.Train.WinFormAttachmentFiles-Form-FileImage').update('<a href="'+o.result.file+'" data-lightbox="image-1" data-title="Attachment File" title="View Image"><img src="'+o.result.file+'" style="height:200px;" /></a>');
                                            Ext.getCmp('Koltiva.view.Train.WinFormAttachmentFiles-Form-FileImage').doLayout();
                                            Ext.getCmp('Koltiva.view.Train.WinFormAttachmentFiles-Form-FilesExist').setValue('yes');
                                            Ext.getCmp('Koltiva.view.Train.WinFormAttachmentFiles-Form-OldPhoto').setValue(o.result.FilePath);
                                        }
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
                        xtype: 'textareafield',
                        id: 'Koltiva.view.Train.WinFormAttachmentFiles-Form-Remark',
                        name: 'Koltiva.view.Train.WinFormAttachmentFiles-Form-Remark',
                        fieldLabel: lang('Remark'),
                        labelWidth: 125
                    },{
                        xtype: 'textareafield',
                        hidden:true,
                        id: 'Koltiva.view.Train.WinFormAttachmentFiles-Form-OldPhoto',
                        name: 'Koltiva.view.Train.WinFormAttachmentFiles-Form-OldPhoto',
                        fieldLabel: lang('OldPhoto'),
                        labelWidth: 125
                    }]
                },{
                    columnWidth: 0.45,
                    border: false,
                    style:'margin-left:30px;',
                    items:[{
                        id:'Koltiva.view.Train.WinFormAttachmentFiles-Form-FileImage',
                        html:'<img src="'+m_api_base_url+'/images/video/thumb-defa.png" height="200" />'        
                    },{
                        html:'<div style="margin-top:5px;font-size:10px;font-style:italic;">'+lang('File must be image or PDF')+'</div>'
                    }]
                }]
            }]
        }];
        //Items ---------------------------------------------------------------------------------------------------------------------------- (End)

        thisObj.buttons = [{
            text: lang('Save'),
            margin: '5 15 5 5',
            scale: 'large',
            ui: 's-button',
            cls: 's-blue',
            id: 'Koltiva.view.Train.WinFormAttachmentFiles-Form-BtnSave',
            handler: function () {
                var FormNya = Ext.getCmp('Koltiva.view.Train.WinFormAttachmentFiles-Form').getForm();
                var FilesExist = Ext.getCmp('Koltiva.view.Train.WinFormAttachmentFiles-Form-FilesExist').getValue();

                if(FilesExist == 'yes'){
                    FormNya.submit({
                        url: m_api + '/train/attachment_file_input',
                        method:'POST',
                        waitMsg: 'Saving data...',
                        params: {
                            OpsiDisplay: thisObj.viewVar.OpsiDisplay
                        },
                        success: function(rp, o){
                            var r = Ext.decode(o.response.responseText);
                            Ext.MessageBox.show({
                                title: 'Information',
                                msg: r.message,
                                buttons: Ext.MessageBox.OK,
                                animateTarget: 'mb9',
                                icon: 'ext-mb-success'
                            });
                            
                            //Load Store
                            thisObj.viewVar.CallerStore.load();

                            thisObj.close();
                        },
                        failure: function(rp, o){
                            try {
                                var r = Ext.decode(o.response.responseText);
                                Ext.MessageBox.show({
                                    title: 'Error',
                                    msg: r.message,
                                    buttons: Ext.MessageBox.OK,
                                    animateTarget: 'mb9',
                                    icon: 'ext-mb-error'
                                });
                            }
                            catch(err) {
                                Ext.MessageBox.show({
                                    title: 'Error',
                                    msg: 'Connection Error',
                                    buttons: Ext.MessageBox.OK,
                                    animateTarget: 'mb9',
                                    icon: 'ext-mb-error'
                                });
                            }
                        }
                    });
                }else{
                    Ext.MessageBox.show({
                        title: 'Attention',
                        msg: lang('No file yet'),
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
                //Load Store
                thisObj.viewVar.CallerStore.load();
            }
        }];

        this.callParent(arguments);
    },
    listeners: {
        afterRender: function(){
            var thisObj = this;

            if(thisObj.viewVar.OpsiDisplay == 'insert'){
                //insert prep insert
                Ext.Ajax.request({
                    waitMsg: lang('Please Wait'),
                    url: m_api + '/train/attachment_file_input_prep',
                    method : 'GET',
                    params: {
                        TrainID: thisObj.viewVar.TrainID,
                        TrainType: thisObj.viewVar.TrainType
                    },
                    success: function(response, opts){
                        var r = Ext.decode(response.responseText);
                        //console.log(r);
                        Ext.getCmp('Koltiva.view.Train.WinFormAttachmentFiles-Form-TrainAttID').setValue(r.TrainAttID);
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
                var FormNya = Ext.getCmp('Koltiva.view.Train.WinFormAttachmentFiles-Form').getForm();
                FormNya.reset();            

                //load formnya
                FormNya.load({
                    url: m_api + '/train/attachment_file_form_data',
                    method: 'GET',
                    params: {
                        TrainAttID: thisObj.viewVar.TrainAttID
                    },
                    success: function(form, action) {
                        var r = Ext.decode(action.response.responseText);
                        Ext.getCmp('Koltiva.view.Train.WinFormAttachmentFiles-Form-FilesExist').setValue('yes');
                        console.log(r);

                        switch(r.data.ExtensionFile){
                            case 'pdf':
                                if(r.data.Filename != ""){
                                    var FotoFile = r.data.Filename;
                                    var angkaRand = Math.floor((Math.random() * 100) + 1);
                                    Ext.getCmp('Koltiva.view.Train.WinFormAttachmentFiles-Form-OldPhoto').setValue(r.data.FilePath);
        
                                    Ext.getCmp('Koltiva.view.Train.WinFormAttachmentFiles-Form-FileImage').update('<a href="'+FotoFile+'" title="Download File" target="_blank">'+lang('Download File')+'    <img src="'+m_api_base_url+'/images/pdf-icon.png" height="24" /></a>');
                                    // Ext.getCmp('Koltiva.view.Train.WinFormAttachmentFiles-Form-FileImage').doLayout();
                                }else{                                    
                                    Ext.getCmp('Koltiva.view.Train.WinFormAttachmentFiles-Form-FileImage').update('<div><strong>'+lang('File not exist')+'</strong></div>');
                                }
                            break;

                            case 'jpg':
                            case 'jpeg':
                            case 'gif':
                            case 'png':
                                if(r.data.Filename != ""){
                                    var FotoFile = r.data.Filename;
                                    var angkaRand = Math.floor((Math.random() * 100) + 1);
                                    Ext.getCmp('Koltiva.view.Train.WinFormAttachmentFiles-Form-OldPhoto').setValue(r.data.FilePath);
        
                                    checkImageExistsGeneral(FotoFile, function(existsImage) {
                                        if (existsImage == true) {
                                            Ext.getCmp('Koltiva.view.Train.WinFormAttachmentFiles-Form-FileImage').update('<a href="'+FotoFile+'" data-lightbox="image-1" data-title="Receipt File" title="View Image"><img src="'+FotoFile+'?'+angkaRand+'" style="height:200px;" /></a>');
                                        } else {
                                            Ext.getCmp('Koltiva.view.Train.WinFormAttachmentFiles-Form-FileImage').update('<img src="'+m_api_base_url+'/images/video/thumb-defa.png" height="200"" />');
                                        }
                                        Ext.getCmp('Koltiva.view.Train.WinFormAttachmentFiles-Form-FileImage').doLayout();
                                    });
                                }
                            break;
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