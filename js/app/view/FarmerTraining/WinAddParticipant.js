/*
    Param2 yg diperlukan ketika load View ini
    - FarmerGroupStoreGrid
*/

Ext.define('Koltiva.view.FarmerTraining.WinAddParticipant', {
    extend: 'Ext.window.Window',
    id: 'Koltiva.view.FarmerTraining.WinAddParticipant',
    title: lang('Add Participant'),
    closable: true,
    modal: true,
    closeAction: 'destroy',
    overflowY: 'auto',
    width: '90%',
    viewVar: false,
    setviewVar: function (value) {
        this.viewVar = value;
    },
    initComponent: function () {
        var thisObj = this;

        //Store ========================= (Begin)
        var store_provinsi_participant = Ext.create('Koltiva.store.FarmerTraining.CmbProvinceParticipant');
        var store_kabupaten_participant = Ext.create('Koltiva.store.FarmerTraining.CmbDistrictParticipant');
        var store_participant_add = Ext.create('Koltiva.store.FarmerTraining.AddParticipantGrid');
        //Store ========================= (End)

        thisObj.items = [{
            xtype: 'panel',
            border: false,
            padding: '5 12 5 5',
            items: [{
                xtype: 'gridpanel',
                id: 'grid_participant_add',
                store: store_participant_add,
                viewConfig: {
                    deferEmptyText: false,
                    emptyText: GetDefaultContentNoData()
                },
                loadMask: true,
                dockedItems: [{
                    xtype: 'toolbar',
                    items: [{
                            id: 'provAddPart',
                            name: 'Provinsi',
                            xtype: 'combo',
                            fieldLabel: lang('Provinsi'),
                            labelWidth: 50,
                            store: store_provinsi_participant,
                            displayField: 'label',
                            valueField: 'id',
                            readOnly: false,
                            queryMode: 'local',
                            listeners: {
                                change: function (cb, nv, ov) {
                                    store_kabupaten_participant.load({
                                        params: {
                                            prov: Ext.getCmp('provAddPart').getValue()
                                        }
                                    });
                                }
                            }
                        },
                        {
                            id: 'kabAddPart',
                            name: 'Kabupaten',
                            xtype: 'combo',
                            fieldLabel: lang('Kabupaten'),
                            labelWidth: 50,
                            store: store_kabupaten_participant,
                            displayField: 'label',
                            valueField: 'id',
                            queryMode: 'local',
                            listeners: {

                            }
                        },
                        {
                            xtype: 'textfield',
                            name: 'keyAddPart',
                            id: 'keyAddPart',
                            emptyText: lang('Search by Name / ID'),
                            width: 150,
                            listeners: {}
                        }, {
                            xtype: 'button',
                            margin: '0px 0px 0px 6px',
                            text: lang('Search'),
                            icon: varjs.config.base_url + 'images/icons/new/search-white.png',
                            cls: 'Sfr_BtnFormBlue',
                            overCls: 'Sfr_BtnFormBlue-Hover',
                            handler: function () {
                                store_participant_add.load({
                                    params: {
                                        FarmerTrainingID: Ext.getCmp('idt').getValue(),
                                        key: Ext.getCmp('keyAddPart').getValue(),
                                        prov: Ext.getCmp('provAddPart').getValue(),
                                        kab: Ext.getCmp('kabAddPart').getValue(),
                                        // cpg: Ext.getCmp('cpgAddPart').getValue(),
                                    }
                                });
                            }
                        }]
                    },
                    {
                        xtype: 'pagingtoolbar',
                        store: store_participant_add,
                        dock: 'top',
                        displayInfo: true
                    },
                    {
                        xtype: 'pagingtoolbar',
                        store: store_participant_add,
                        dock: 'bottom',
                        displayInfo: true
                    },
                ],
                selType: 'checkboxmodel',
                selModel: {
                    checkOnly: true,
                    mode: "MULTI",
                    headerWidth: '10%'
                },
                columns: [{
                    text: lang('Name'),
                    dataIndex: 'addFarmerName',
                    flex: 1,
                }, {
                    text: '',
                    dataIndex: 'addFarmerID',
                    hidden: true,
                }, {
                    text: lang('Farmer ID'),
                    dataIndex: 'addFarmerDisplayID',
                    flex: 1,
                }, {
                    text: lang('Province'),
                    dataIndex: 'Province',
                    flex: 1,
                }, {
                    text: lang('District'),
                    dataIndex: 'District',
                    flex: 1,
                }, {
                    text: lang('SubDistrict'),
                    dataIndex: 'SubDistrict',
                    flex: 1,
                }, {
                    text: lang('Village'),
                    dataIndex: 'Village',
                    flex: 1,
                }, ]
            }],
        }];

        thisObj.buttons = [{
            id: 'save_par_add',
            text: lang('Save'),
            icon: varjs.config.base_url + 'images/icons/new/save.png',
            cls: 'Sfr_BtnFormBlue',
            overCls: 'Sfr_BtnFormBlue-Hover',
            handler: function () {
                var participants = '';
                Ext.each(Ext.getCmp('grid_participant_add').getSelectionModel().getSelection(), function (row, index, value) {
                    participants = participants + ',' + row.data.addFarmerID;
                });
                if (participants !== '') {
                    Ext.Ajax.request({
                        url: m_store_participant + 's',
                        method: 'POST',
                        waitMsg: lang('Sending data...'),
                        params: {
                            FarmerTrainingID: Ext.getCmp('idt').getValue(),
                            participants: participants,
                        },
                        success: function (response, opts) {
                            var obj = Ext.decode(response.responseText);
                            switch (obj.success) {
                                case true:
                                    thisObj.viewVar.Grid.load({
                                        params: {
                                            training: Ext.getCmp('idt').getValue()
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
            text: lang('Close'),
            icon: varjs.config.base_url + 'images/icons/new/close.png',
            cls: 'Sfr_BtnFormGrey',
            overCls: 'Sfr_BtnFormGrey-Hover',
            handler: function () {
                thisObj.close();
            }
        }];

        this.callParent(arguments);
    },
    listeners: {
        afterRender: function () {
            var thisObj = this;
            Ext.getCmp('grid_participant_add').getStore().load({
                params: {
                    FarmerTrainingID: Ext.getCmp('idt').getValue(),
                }
            });
        }
    }
});