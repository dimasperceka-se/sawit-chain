/*
 * @Author: fikri
 * @Date:   2019-11-26 11:28:07
 */
Ext.define('Koltiva.view.IMS.WinFormParticipant', {
    extend: 'Ext.window.Window',
    id: 'Koltiva.view.IMS.WinFormParticipant',
    title: lang('Add Participants'),
    height: '70%',
    width: '70%',
    autoScroll: true,
    modal: true,
    layout: 'fit',
    closable: true,
    closeAction: 'destroy',
    viewVar: false,
    setViewVar: function (value) {
        this.viewVar = value;
    },
    listeners: {
        afterRender: function () {
            var thisObj = this;
            Ext.getCmp('grid_participant_add').getStore().load({
                params: {
                    FarmerTrainingID: thisObj.viewVar.idt
                }
            });
        }
    },
    initComponent: function () {
        var thisObj = this;

        var store_provinsi_participant = Ext.create('Ext.data.Store', {
            extend: 'Ext.data.Model',
            fields: ['id', 'label'],
            autoLoad: true,
            pageSize: 10,
            proxy: {
                type: 'ajax',
                url: m_store_provinsi,
                extraParams: {prov: m_param},
                reader: {
                    type: 'json',
                    root: 'data'
                }
            }
        });
        var store_kabupaten_participant = Ext.create('Ext.data.Store', {
            extend: 'Ext.data.Model',
            fields: ['id', 'label'],
            // autoLoad: true,
            pageSize: 10,
            proxy: {
                type: 'ajax',
                url: m_store_kabupaten,
                extraParams: {prov: m_param},
                reader: {
                    type: 'json',
                    root: 'data'
                }
            }
        });
        var store_cpg_participant = Ext.create('Ext.data.Store', {
            extend: 'Ext.data.Model',
            fields: ['id', 'label'],
            autoLoad: false,
            proxy: {
                type: 'ajax',
                url: m_store_cpg,
                reader: {
                    type: 'json',
                    root: 'data'
                }
            }
        });
        var store_participant_add = Ext.create('Ext.data.Store', {
            extend: 'Ext.data.Model',
            fields: ['addFarmerID', 'addFarmerDisplayID', 'addFarmerName', 'CPGid', 'CPG', 'Province', 'District', 'SubDistrict', 'Village'],
            autoLoad: false,
            proxy: {
                type: 'ajax',
                url: m_store_participant + 's_add',
                reader: {
                    type: 'json',
                    root: 'data',
                    totalProperty: 'total'
                }
            },
            listeners: {
                'beforeload': function (store, options) {
                    store.proxy.extraParams.FarmerTrainingID = Ext.getCmp('idt').getValue()
                    store.proxy.extraParams.key = Ext.getCmp('keyAddPart').getValue()
                    store.proxy.extraParams.prov = Ext.getCmp('provAddPart').getValue()
                    store.proxy.extraParams.kab = Ext.getCmp('kabAddPart').getValue()
                    store.proxy.extraParams.cpg = Ext.getCmp('cpgAddPart').getValue()
                }
            }
        });

        thisObj.items = [{
                xtype: 'panel',
                height: '100%',
                overflowY: 'auto',
                width: '100%',
                id: 'dataFormParAdd',
                items: [{
                        xtype: 'gridpanel',
                        id: 'grid_participant_add',
                        store: store_participant_add,
                        cls: 'Sfr_GridNew',
                        loadMask: true,
                        dockedItems: [{
                                xtype: 'toolbar',
                                items: [{
                                        id: 'provAddPart',
                                        name: 'Provinsi',
                                        xtype: 'combo',
                                        fieldLabel: lang('Province'),
                                        labelWidth: 70,
                                        store: store_provinsi_participant,
                                        displayField: 'label',
                                        valueField: 'id',
                                        readOnly: false,
                                        queryMode: 'local',
                                        listeners: {
                                            change: function (cb, nv, ov) {
                                                store_kabupaten_participant.load({
                                                    params: {
                                                        prov: nv
                                                    }});
                                            }
                                        }
                                    }, {
                                        id: 'kabAddPart',
                                        name: 'Kabupaten',
                                        xtype: 'combo',
                                        fieldLabel: lang('District'),
                                        labelWidth: 90,
                                        store: store_kabupaten_participant,
                                        displayField: 'label',
                                        valueField: 'id',
                                        queryMode: 'local',
                                        listeners: {
                                            change: function (cb, nv, ov) {
                                                store_cpg_participant.load({
                                                    params: {
                                                        DistrictID: nv
                                                    }});
                                            }
                                        }
                                    }, {
                                        id: 'cpgAddPart',
                                        name: 'CPG',
                                        xtype: 'combo',
                                        fieldLabel: lang('CPG'),
                                        labelWidth: 50,
                                        hidden:true,
                                        store: store_cpg_participant,
                                        displayField: 'label',
                                        valueField: 'id',
                                        queryMode: 'local',
                                        listeners: {
                                        }
                                    }, {
                                        xtype: 'textfield',
                                        name: 'keyAddPart',
                                        id: 'keyAddPart',
                                        emptyText: lang('Search By Name / ID'),
                                        width: 150,
                                        listeners: {}
                                    }, {
                                        xtype: 'button',
                                        icon: varjs.config.base_url + 'images/icons/new/search-white.png',
                                        cls: 'Sfr_BtnGridBlue',
                                        overCls: 'Sfr_BtnGridBlue-Hover',
                                        text: lang('Search'),
                                        handler: function () {
                                            store_participant_add.load({
                                                params: {
                                                    FarmerTrainingID: Ext.getCmp('idt').getValue(),
                                                    key: Ext.getCmp('keyAddPart').getValue(),
                                                    prov: Ext.getCmp('provAddPart').getValue(),
                                                    kab: Ext.getCmp('kabAddPart').getValue(),
                                                    cpg: Ext.getCmp('cpgAddPart').getValue(),
                                                }
                                            });
                                        }
                                    }]
                            }, {
                                xtype: 'pagingtoolbar',
                                store: store_participant_add,
                                dock: 'top',
                                displayInfo: true
                            }, {
                                xtype: 'pagingtoolbar',
                                store: store_participant_add,
                                dock: 'bottom',
                                displayInfo: true
                            }],
                        selType: 'checkboxmodel',
                        selModel: {
                            checkOnly: true,
                            mode: "MULTI",
                            headerWidth: '10%'
                        },
                        columns: [ {
                                text: lang('ID'),
                                dataIndex: 'addFarmerDisplayID',
                                flex: 1
                            },{
                                text: lang('Name'),
                                dataIndex: 'addFarmerName',
                                flex: 2
                            }, {
                                text: lang('Provinsi'),
                                dataIndex: 'Province',
                                flex: 1
                            }, {
                                text: lang('District'),
                                dataIndex: 'District',
                                flex: 1
                            }, {
                                text: lang('SubDistrict'),
                                dataIndex: 'SubDistrict',
                                flex: 1
                            }, {
                                text: lang('Village'),
                                dataIndex: 'Village',
                                flex: 1
                            }]
                    }]
            }];

        thisObj.buttons = [{
                id: 'save_par_add',
                icon: varjs.config.base_url + 'images/icons/new/save.png',
                text: lang('Save'),
                cls: 'Sfr_BtnFormBlue',
                overCls: 'Sfr_BtnFormBlue-Hover',
                handler: function () {
                    var participants = '';
                    Ext.each(Ext.getCmp('grid_participant_add').getSelectionModel().getSelection(), function (row, index, value) {
                        //participants.push(row.data.addFarmerID);
                        participants = participants + ',' + row.data.addFarmerID;
                    });
                    if (participants !== '') {
                        Ext.Ajax.request({
                            url: m_store_participant + 's',
                            method: 'POST',
                            waitMsg: lang('Sending data...'),
                            params: {
                                FarmerTrainingID: thisObj.viewVar.idt,
                                participants: participants,
                            },
                            success: function (response, opts) {
                                var obj = Ext.decode(response.responseText);
                                switch (obj.success) {
                                    case true:
                                        thisObj.viewVar.callStore.load({
                                            params: {
                                                training: thisObj.viewVar.idt
                                            }
                                        });
                                        thisObj.close();
                                        break;
                                    default:
                                        Ext.MessageBox.alert('Warning', obj.message);
                                        break;
                                }
                            }
                        });
                    } else {
                        Ext.Msg.alert("Warning", "Please select participants");
                    }
                }
            }, {
                icon: varjs.config.base_url + 'images/icons/new/close.png',
                text: lang('Close'),
                cls: 'Sfr_BtnFormGrey',
                overCls: 'Sfr_BtnFormGrey-Hover',
                disabled: false,
                handler: function () {
                    thisObj.close();
                }
            }];

        this.callParent(arguments);
    }
});