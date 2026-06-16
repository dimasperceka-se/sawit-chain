/******************************************
 *  Author : n1colius.lau@gmail.com   
 *  Created On : Mon Oct 29 2018
 *  File : GridCpgTrainAttachmentFiles.js
 *******************************************/

/*
    Param
    - TrainID
    - TrainType
*/

Ext.define('Koltiva.view.Train.GridTrainAttachmentFiles' ,{
    extend: 'Ext.window.Window',
    id: 'Koltiva.view.Train.GridTrainAttachmentFiles',
    title: 'Attachment Training Files',
    closable: true,
    modal: true,
    closeAction: 'destroy',
    width: '70%',
    height: '80%',
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

        thisObj.StoreMainGrid = Ext.create('Koltiva.store.Train.GridTrainAttachmentFiles', {
            storeVar: {
                TrainID: thisObj.viewVar.TrainID,
                TrainType: thisObj.viewVar.TrainType
            }
        });

        thisObj.ContextMenuMainGrid = Ext.create('Ext.menu.Menu',{
            items:[{
                icon: varjs.config.base_url + 'images/icons/silk/pencil.png',
                text: lang('Update'),
                hidden: !m_act_update,
                handler: function() {
                    var sm = Ext.getCmp('Koltiva.view.Train.GridTrainAttachmentFiles-Grid').getSelectionModel().getSelection()[0];

                    var WinFormAttachmentFiles = Ext.create('Koltiva.view.Train.WinFormAttachmentFiles', {
                        viewVar: {
                            TrainID: thisObj.viewVar.TrainID,
                            TrainType: thisObj.viewVar.TrainType,
                            OpsiDisplay: 'update',
                            TrainAttID: sm.get('TrainAttID'),
                            CallerStore: thisObj.StoreMainGrid
                        }
                    });
                    if (!WinFormAttachmentFiles.isVisible()) {
                        WinFormAttachmentFiles.center();
                        WinFormAttachmentFiles.show();
                    } else {
                        WinFormAttachmentFiles.close();
                    }
                }
            },{
                icon: varjs.config.base_url + 'images/icons/silk/delete.png',
                text: lang('Delete'),
                hidden: !m_act_delete,
                handler: function(){
                    var sm = Ext.getCmp('Koltiva.view.Train.GridTrainAttachmentFiles-Grid').getSelectionModel().getSelection()[0];                    
                    Ext.MessageBox.confirm('Message', 'Do you want to delete this data ?', function(btn) {
                        if (btn == 'yes') {
                            Ext.Ajax.request({
                                waitMsg: 'Please Wait',
                                url: m_api + '/train/attachment_file',
                                method: 'DELETE',
                                params: {
                                    TrainAttID: sm.get('TrainAttID')
                                },
                                success: function(rp, o) {
                                    Ext.MessageBox.show({
                                        title: 'Information',
                                        msg: lang('Data deleted'),
                                        buttons: Ext.MessageBox.OK,
                                        animateTarget: 'mb9',
                                        icon: 'ext-mb-success'
                                    });

                                    //refresh store
                                    thisObj.StoreMainGrid.load();
                                },
                                failure: function(rp, o) {
                                    try {
                                        var r = Ext.decode(rp.responseText);
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
                        }
                    });
                }
            }]
        });

        thisObj.items = [{
            xtype: 'gridpanel',
            id: 'Koltiva.view.Train.GridTrainAttachmentFiles-Grid',
            style: 'border:1px solid #CCC;',
            store: thisObj.StoreMainGrid,
            width: '99%',
            loadMask: true,
            selType: 'rowmodel',
            viewConfig: {
                deferEmptyText: false,
                emptyText: lang('No files Available')
            },
            dockedItems: [{
            	xtype: 'pagingtoolbar',
                store: thisObj.StoreMainGrid,
                dock: 'bottom',
                displayInfo: true
            },{
            	xtype: 'toolbar',
            	items: [{
                    xtype: 'button',
                    icon: varjs.config.base_url + 'images/icons/silk/arrow_up.png',
                    margin: '0px 0px 0px 6px',
                    text: lang('Upload File'),
                    hidden: !m_act_add,
                    handler: function() {
                        var WinFormAttachmentFiles = Ext.create('Koltiva.view.Train.WinFormAttachmentFiles', {
                            viewVar: {
                                TrainID: thisObj.viewVar.TrainID,
                                TrainType: thisObj.viewVar.TrainType,
                                OpsiDisplay: 'insert',
                                TrainAttID: null,
                                CallerStore: thisObj.StoreMainGrid
                            }
                        });
                        if (!WinFormAttachmentFiles.isVisible()) {
                            WinFormAttachmentFiles.center();
                            WinFormAttachmentFiles.show();
                        } else {
                            WinFormAttachmentFiles.close();
                        }
                    }
                }]
            }],
            columns: [{
                dataIndex: 'TrainAttID',
                hidden: true
            },{
                dataIndex: 'TrainID',
                hidden: true
            },{
                dataIndex: 'Filename',
                hidden: true
            },{
                text: lang('Action'),
                xtype:'actioncolumn',
                width: '7%',
                items:[{
                    icon: varjs.config.base_url + 'images/icons/silk/download_arrow.png',
                    handler: function(grid, rowIndex, colIndex, item, e, record) {
                        thisObj.ContextMenuMainGrid.showAt(e.getXY());
                    }
                }]
            },{
                text: lang('File'),
                width: '25%',
                dataIndex: 'Filename',
                renderer: function (t, meta, record) {
                    var data = record.getData();

                    var FileNya = data.Filename;
                    var angkaRand = Math.floor((Math.random() * 100) + 1);
                    var HtmlReturn = '';
                    
                    switch(data.ExtensionFile){
                        case 'pdf':
                            HtmlReturn = '<div align="center"><a title="'+lang('Download File')+'" href="'+FileNya+'" target="_blank">'+lang('Download File')+'   <img src="'+m_api_base_url+'/images/pdf-icon.png" height="16" /></a></div>';
                        break;

                        case 'jpg':
                        case 'jpeg':
                        case 'gif':
                        case 'png':
                            var HtmlFotoFile;

                            if(data.FileExist == 'yes'){
                                HtmlFotoFile = '<a target="_blank" href="'+FileNya+'" data-lightbox="image-1" data-title="Receipt File" title="View Image"><img src="'+FileNya+'?'+angkaRand+'" style="height:80px;" /></a>';
                            }else{
                                HtmlFotoFile = '<img src="'+m_api_base_url+'/images/video/thumb-defa.png" height="80"" />';
                            }
        
                            HtmlReturn = '<div align="center">'+HtmlFotoFile+'</div>';
                        break;

                        default:
                            HtmlReturn = '<div align="center">'+lang('Files not recognized')+'</div>';
                        break;
                    }

                    return HtmlReturn;
                }
            },{
                text: lang('Remark'),
                flex: 1,
                dataIndex: 'Remark'
            }]
        }];

        thisObj.buttons = [{
            text: lang('Close'),
            margin: '5px',
            scale: 'large',
            ui: 's-button',
            cls: 's-grey',
            handler: function() {
                thisObj.close();
            }
        }];

        this.callParent(arguments);
    },
    listeners: {
        afterRender: function(){
            var thisObj = this;

            switch(thisObj.viewVar.TrainType){
                case 'cpg':
                    Ext.getCmp('Koltiva.view.Train.GridTrainAttachmentFiles').setTitle(lang('CPG Training - Attachment Training Files'));
                break;
                case 'kader':
                    Ext.getCmp('Koltiva.view.Train.GridTrainAttachmentFiles').setTitle(lang('Cadre Training - Attachment Training Files'));
                break;
                case 'master':
                    Ext.getCmp('Koltiva.view.Train.GridTrainAttachmentFiles').setTitle(lang('Master Training - Attachment Training Files'));
                break;
                case 'business':
                    Ext.getCmp('Koltiva.view.Train.GridTrainAttachmentFiles').setTitle(lang('Business Training - Attachment Training Files'));
                break;
                case 'farmer':
                    Ext.getCmp('Koltiva.view.Train.GridTrainAttachmentFiles').setTitle(lang('Farmer Training - Attachment Training Files'));
                break;
            }
        }
    }
});