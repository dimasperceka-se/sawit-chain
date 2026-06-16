Ext.define('Koltiva.view.Refinery.SPCodePanel' ,{
    extend: 'Ext.panel.Panel',
    id: 'Koltiva.view.Refinery.SPCodePanel',
    title: lang('SPB Code'),
    viewVar: false,
    setViewVar: function(value){
        this.viewVar = value;
    },
    frame: true,
    collapsible:true,
    margin:'0 0 20 8',
    initComponent: function() {
        var thisObj = this;

        //store
        var storeGridSPCodePanel = Ext.create('Koltiva.store.Refinery.GridSpCodePanel', {
            storeVar: {
                RefineryID: thisObj.viewVar.RefineryID,
                CallFrom: thisObj.viewVar.CallFrom
            }
        });

        //context menu
        var contextMenuGridSPCode = Ext.create('Ext.menu.Menu',{
            items:[{
                icon: varjs.config.base_url + 'images/icons/new/update.png',
                text: lang('Update'),
                handler: function() {
                    var sm = Ext.getCmp('Koltiva.view.Refinery.SPCodePanel-gridSPCode').getSelectionModel().getSelection()[0];
                    
                    console.log(sm.raw);
                    var WinFormSPCode = Ext.create('Koltiva.view.Refinery.WinFormSPCode', {
                        viewVar: {
                            SPCodeID: sm.get('SPCodeID'),
                            RefineryID: sm.raw['RefineryID'],
                            OpsiDisplay: 'update',
                            CallerStore: thisObj.MainGrid,
                            CallFrom: 'refinery'
                        }
                    });
                    if (!WinFormSPCode.isVisible()) {
                        WinFormSPCode.center();
                        WinFormSPCode.show();
                    } else {
                        WinFormSPCode.close();
                    }
                }
            },{
                icon: varjs.config.base_url + 'images/icons/new/delete.png',
                text: lang('Delete'),
                // hidden: m_act_delete,
                handler: function(){
                    var sm = Ext.getCmp('Koltiva.view.Refinery.SPCodePanel-gridSPCode').getSelectionModel().getSelection()[0];
    
                    Ext.MessageBox.confirm('Message', 'Do you want to delete this data ?', function(btn) {
                        if (btn == 'yes') {
                            Ext.Ajax.request({
                                waitMsg: 'Please Wait',
                                url: m_api + '/refinery/submit_sp_code',
                                method: 'DELETE',
                                params: {
                                    SPCodeID: sm.get('SPCodeID')
                                },
                                success: function(response, opts) {
                                    Ext.MessageBox.show({
                                        title: 'Information',
                                        msg: lang('Data deleted'),
                                        buttons: Ext.MessageBox.OK,
                                        animateTarget: 'mb9',
                                        icon: 'ext-mb-success'
                                    });

                                    //refresh store
                                    Ext.getCmp('Koltiva.view.Refinery.SPCodePanel-gridSPCode').getStore().reload();
                                    
                                },
                                failure: function(response, opts) {
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
                        }
                    });
    
                }
            }]
        });

        thisObj.items = [{
            xtype: 'grid',
            id: 'Koltiva.view.Refinery.SPCodePanel-gridSPCode',
            loadMask: true,
            selType: 'rowmodel',
            store: storeGridSPCodePanel,
            viewConfig: {
                deferEmptyText: false,
                emptyText: GetDefaultContentNoData()
            },
            dockedItems: [{
                xtype: 'toolbar',
                dock:'top',
                items: [{
                    id: 'Koltiva.view.Refinery.SPCodePanel.btnAdd',
                    icon: varjs.config.base_url + 'images/icons/new/add.png', cls:'Sfr_BtnGridGreen', overCls:'Sfr_BtnGridGreen-Hover',
                    text: lang('Add'),
                    handler: function() {
                        var WinFormSPCode = Ext.create('Koltiva.view.Refinery.WinFormSPCode', {
                            viewVar: {
                                RefineryID: thisObj.viewVar.RefineryID,
                                OpsiDisplay: 'insert',
                                CallerStore: thisObj.MainGrid,
                                CallFrom: thisObj.viewVar.CallFrom
                            }
                        });
                        if (!WinFormSPCode.isVisible()) {
                            WinFormSPCode.center();
                            WinFormSPCode.show();
                        } else {
                            WinFormSPCode.close();
                        }
                    }
                }]
            }],
            columns: [{
                text: lang('Action'),
                xtype:'actioncolumn',
                flex: 0.3,
                items:[{
                    icon: varjs.config.base_url + 'images/icons/new/action.png',
                    tooltip: 'Action',
                    handler: function(grid, rowIndex, colIndex, item, e, record) {
                        contextMenuGridSPCode.showAt(e.getXY());
                    }
                }]
            },{
                text: lang('SPCodeID'),
                dataIndex: 'SPCodeID',
                hidden:true
            },{
                text: lang('No Surat'),
                dataIndex: 'SuratNr',
                flex: 1,
            },{
                text: lang('Keterangan'),
                dataIndex: 'Note',
                flex: 1,
            }]
        }];

        this.callParent(arguments);
    }
});