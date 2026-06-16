Ext.define('Koltiva.view.Mill.WinFaAssignment', {
    extend: 'Ext.window.Window',
    id: 'Koltiva.view.Mill.WinFaAssignment',
    title: lang('Edit FA Assignment'),
    closable: true,
    modal: true,
    closeAction: 'destroy',
    width: 970,
    frame: false,
    minWidth: 370,
    height: 650,
    overflowY: 'auto',
    initComponent: function () {
        var thisObj = this;

        var storeStaffList = Ext.create('Koltiva.store.Mill.ListStaffAssignment', {
            storeVar: {
                MillID: thisObj.viewVar.MillID
            }
        });

        var dsReport = Ext.create('Ext.data.ArrayStore', {
            fields: [{
                    name: 'value',
                    type: 'string'
                }, {
                    name: 'text',
                    type: 'string'
                }],
            proxy: {
                type: 'ajax',
                url: m_api + '/mill/staff_assignment',
                extraParams: {MillID: thisObj.viewVar.MillID},
                reader: {
                    type: 'json',
                    root: 'data'
                }
            },
            autoLoad: true,
            listeners: {
                load: function () {
                    var selected2 = [];
                    var selector2 = Ext.getCmp("itemselector-fa_assignment");
                    dsReport.data.each(function (item, index, totalItems) {
                        selected2.push(parseInt(item.data['value']));
                    });
                    selector2.setValue(selected2);
                }
            }
        });

        //isi formnya
        thisObj.items = [{
                xtype: 'form',
                id: 'Koltiva.view.Mill.WinFaAssignment-Form',
                padding: '20 25 10 10',
                items: [{
                        layout: 'column',
                        border: false,
                        items: [{
                            columnWidth: 1,
                            layout:{
                                type:'vbox',
                                align:'stretch'
                            },
                            items: [{
                                    xtype: 'itemselector',
                                    name: 'itemselector-fa_assignment',
                                    fieldLabel: lang('Select FA Assignment'),
                                    labelWidth: 230,
                                    id: 'itemselector-fa_assignment',
                                    buttons: ['add', 'remove'],
                                    anchor: '100%',
                                    height: 400,
                                    store: storeStaffList,
                                    displayField: 'text',
                                    valueField: 'value',
                                    value: [],
                                    allowBlank: true,
                                    msgTarget: 'side',
                                    fromTitle: lang('List Staff'),
                                    toTitle: lang('Mill FA Assignment')
                            }]
                        }]
                    }]
            }]
        thisObj.buttons = [{
                text: lang('Save'),
                formBind: true,
                scope: this,
                handler: this.onSaveClick,
                icon: varjs.config.base_url + 'images/icons/new/save.png',
                cls: 'Sfr_BtnFormBlue',
                overCls: 'Sfr_BtnFormBlue-Hover',
            }, {
                text: lang('Close'),
                icon: varjs.config.base_url + 'images/icons/new/close.png',
                cls: 'Sfr_BtnFormGrey',
                overCls: 'Sfr_BtnFormGrey-Hover',
                handler: function () {
                    thisObj.close();
                }
            }]
        this.callParent(arguments);
    },
    onSaveClick: function () {
        var thisObj = this;
        var form = Ext.getCmp('Koltiva.view.Mill.WinFaAssignment-Form').getForm();
        if (form.isValid()) {
            // Ext.MessageBox.alert('Submitted Values', form.getValues(true));
            form.submit({
                url: m_api + '/mill/setFaAssignment',
                method: 'POST',
                params: {MillID: this.viewVar.MillID},
                success: function (form, action) {
                    Ext.Msg.alert('Success', action.result.message);
                    thisObj.close();
                    Ext.getCmp('Koltiva.view.Mill.GridMainMill-gridMainGrid').getStore().load();
                },
                failure: function (form, action) {
                    Ext.Msg.alert('Failed', action.result.message);
                }
            });
        }
    }
});