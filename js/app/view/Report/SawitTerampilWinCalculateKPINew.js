/**
 * *****************************************
 * Author : fikrifauzul@gmail.com
 * Created On : August 06, 2021
 * Updated On August 09, 2021
 * File : SawitTerampilWinCalculateKPINew.js
 * Desc : [description]
 * *****************************************
 */
/*
 Param2 yg diperlukan ketika load View ini
 
 */

Ext.define('Koltiva.view.Report.SawitTerampilWinCalculateKPINew', {
    extend: 'Ext.window.Window',
    id: 'Koltiva.view.Report.SawitTerampilWinCalculateKPINew',
    title: lang('Calculate Sawit Terampil KPI New'),
    closable: true,
    modal: true,
    closeAction: 'destroy',
    width: '90%',
    height: '80%',
    overflowY: 'auto',
    viewVar: false,
    setViewVar: function (value) {
        this.viewVar = value;
    },
    listeners: {
        afterRender: function () {
            var thisObj = this;

            var dt = new Date();
            Ext.getCmp('Koltiva.view.Report.SawitTerampilWinCalculateKPINew-GridCalculate-DateProcess').setValue(dt);

        }
    },
    initComponent: function () {
        var thisObj = this;

        //Store
        thisObj.StoreMainGrid = Ext.create('Koltiva.store.Report.SawitTerampilMainFormGrid.CmbMonthYears', {
            storeVar: {
                showProcessDate: 'all'
            }
        });
        thisObj.ComboWaveJB = Ext.create('Koltiva.store.Report.WaveJB');
        thisObj.ComboStoreProcedure = Ext.create('Koltiva.store.Report.CmbStoreProcedureJB', {
            storeVar: {
                ProgID: null
            }
        });
        thisObj.StoreMainGridCalculate = Ext.create('Koltiva.store.Report.StoreMainGridCalculateJB', {
            storeVar: {
                ProgID: null
            }
        });

        thisObj.RowEditing = Ext.create('Ext.grid.plugin.RowEditing', {
            id: 'RowEditing',
            clicksToMoveEditor: 0,
            autoCancel: false,
            errorSummary: false,
            clicksToEdit: 2,
            listeners: {
                beforeedit: function (ev) {
                    return m_act_update;
                }
            }
        });

        thisObj.ContextMenuGrid = Ext.create('Ext.menu.Menu', {
            cls: 'Sfr_ConMenu',
            items: [{
                icon: varjs.config.base_url + 'images/icons/new/update.png',
                text: lang('Update'),
                hidden: !m_act_update,
                id: 'DateBtnUpdate',
                //                    cls: 'Sfr_BtnConMenuWhite ' + m_act_save,
                handler: function () {
                    thisObj.RowEditing.cancelEdit();
                    var sm = Ext.getCmp('Koltiva.view.Report.SawitTerampilWinCalculateKPINew-DataControl').getSelectionModel().getSelection();
                    thisObj.RowEditing.startEdit(sm[0].index, 0);
                }
            }]
        });

        thisObj.items = [{
            layout: 'column',
            border: false,
            items: [{
                columnWidth: 0.5,
                layout: 'form',
                style: 'padding:5px 25px 10px 10px;',
                items: [{
                    html: '<div class="subtitleForm">' + lang('List of Date Process') + '</div>',
                    style: 'font-weight:bold;'
                }, {
                    xtype: 'grid',
                    id: 'Koltiva.view.Report.SawitTerampilWinCalculateKPINew-DataControl',
                    style: 'border:1px solid #CCC;margin-top:4px;',
                    cls: 'Sfr_GridNew',
                    loadMask: true,
                    selType: 'rowmodel',
                    store: thisObj.StoreMainGrid,
                    viewConfig: {
                        deferEmptyText: false,
                        emptyText: lang('No data Available')
                    },
                    dockedItems: [{
                        xtype: 'pagingtoolbar',
                        store: thisObj.StoreMainGrid,
                        dock: 'bottom',
                        width: '80%',
                        displayInfo: true
                    }],
                    columns: [{
                        text: '',
                        xtype: 'actioncolumn',
                        width: '4%',
                        items: [{
                            icon: varjs.config.base_url + 'images/icons/new/action.png',
                            handler: function (grid, rowIndex, colIndex, item, e, record) {
                                thisObj.ContextMenuGrid.showAt(e.getXY());
                            }
                        }]
                    }, {
                        text: 'ID',
                        dataIndex: 'id',
                        hidden: true
                    }, {
                        text: 'No',
                        xtype: 'rownumberer',
                        align: 'center',
                        width: '20%'
                    }, {
                        text: lang('Date Process'),
                        dataIndex: 'DateProcess',
                        flex: 1
                    }, {
                        text: lang('Report Name'),
                        dataIndex: 'ReportName',
                        flex: 1,
                        editor: {
                            xtype: 'textfield'
                        },
                    }, {
                        xtype: 'checkcolumn',
                        disabled: true,
                        text: lang('Report Status'),
                        width: '20%',
                        dataIndex: 'ReportStatus',
                        editor: {
                            xtype: 'checkbox'
                        },
                        renderer: function (val, meta, rec) {
                            if (val == '0') {
                                return new Ext.grid.column.Check().renderer(false);
                            } else {
                                return new Ext.grid.column.Check().renderer(true);
                            }
                        }
                    }],
                    plugins: [thisObj.RowEditing],
                    listeners: {
                        'canceledit': function (editor, e, eOpts) {
                            thisObj.StoreMainGrid.load();
                        },
                        'edit': function (editor, e) {
                            var processId = e.record.data.id;
                            var ReportName = e.record.data.ReportName;
                            var ReportStatus = e.record.data.ReportStatus;

                            Ext.MessageBox.confirm('Message', 'Update data ini ?', function (btn) {
                                if (btn == 'yes') {
                                    Ext.Ajax.request({
                                        waitMsg: 'Please wait...',
                                        url: m_combo_monthyears,
                                        method: 'PUT',
                                        params: {
                                            processId: processId,
                                            ReportName: ReportName,
                                            ReportStatus: ReportStatus
                                        },
                                        success: function (response, opts) {
                                            var obj = Ext.decode(response.responseText);
                                            switch (obj.success) {
                                                case true:
                                                    Ext.MessageBox.alert('Success', obj.message);
                                                    thisObj.StoreMainGrid.load();
                                                    break;
                                                default:
                                                    Ext.MessageBox.alert('Warning', obj.message);
                                                    break;
                                            }
                                        },
                                        failure: function (response, opts) {
                                            var obj = Ext.decode(response.responseText);
                                            Ext.MessageBox.alert('error', 'Could not connect to the database. Retry later');
                                        }
                                    });
                                }
                            });
                        }
                    }
                }]
            }, {
                columnWidth: 0.45,
                layout: 'form',
                style: 'padding:5px 5px 10px 5px;',
                items: [{
                    html: '<div class="subtitleForm">' + lang('Calculation Process') + '</div>',
                    style: 'font-weight:bold;'
                }, {
                    xtype: 'grid',
                    id: 'Koltiva.view.Report.SawitTerampilWinCalculateKPINew-GridCalculate',
                    style: 'border:1px solid #CCC;margin-top:4px;',
                    cls: 'Sfr_GridNew',
                    loadMask: true,
                    selType: 'rowmodel',
                    store: thisObj.StoreMainGridCalculate,
                    viewConfig: {
                        deferEmptyText: false,
                        emptyText: GetDefaultContentNoData()
                        //                                    emptyText: lang('No data Available')
                    },
                    dockedItems: [{
                        xtype: 'pagingtoolbar',
                        store: thisObj.StoreMainGridCalculate,
                        dock: 'bottom',
                        displayInfo: true,
                        displayMsg: lang('Showing') + ' {0} ' + lang('to') + ' {1} ' + lang('of') + ' {2} ' + lang('data')
                    }, {
                        xtype: 'toolbar',
                        dock: 'top',
                        items: [{
                            xtype: 'combobox',
                            id: 'Koltiva.view.Report.SawitTerampilWinCalculateKPINew-GridCalculate-ProgID',
                            name: 'Koltiva.view.Report.SawitTerampilWinCalculateKPINew-GridCalculate-ProgID',
                            emptyText: lang('Wave'),
                            store: thisObj.ComboWaveJB,
                            editable: false,
                            queryMode: 'local',
                            displayField: 'name',
                            valueField: 'id',
                            listeners: {
                                change: function (cb, nv, ov) {
                                    thisObj.ComboStoreProcedure.storeVar.ProgID = nv;
                                    thisObj.ComboStoreProcedure.load();
                                }
                            }
                        }, {
                            xtype: 'datefield',
                            id: 'Koltiva.view.Report.SawitTerampilWinCalculateKPINew-GridCalculate-DateProcess',
                            name: 'Koltiva.view.Report.SawitTerampilWinCalculateKPINew-GridCalculate-DateProcess',
                            format: 'Y-m-d'
                        }, {
                            xtype: 'combobox',
                            id: 'Koltiva.view.Report.SawitTerampilWinCalculateKPINew-GridCalculate-StoreProcedureName',
                            name: 'Koltiva.view.Report.SawitTerampilWinCalculateKPINew-GridCalculate-StoreProcedureName',
                            emptyText: lang('Store Procedure Name'),
                            flex: 1,
                            store: thisObj.ComboStoreProcedure,
                            editable: false,
                            queryMode: 'local',
                            displayField: 'label',
                            valueField: 'label'
                        }, {
                            xtype: 'button',
                            id: 'Koltiva.view.Report.SawitTerampilWinCalculateKPINew-GridCalculate-BtnCalculateNew',
                            icon: varjs.config.base_url + 'images/icons/new/process.png',
                            text: lang('Calculate'),
                            cls: 'Sfr_BtnGridBlue',
                            overCls: 'Sfr_BtnGridBlue-Hover',
                            handler: function () {
                                var ProgID = Ext.getCmp('Koltiva.view.Report.SawitTerampilWinCalculateKPINew-GridCalculate-ProgID').getValue();
                                var DateProcess = Ext.getCmp('Koltiva.view.Report.SawitTerampilWinCalculateKPINew-GridCalculate-DateProcess').getValue();
                                var StoreProcedureName = Ext.getCmp('Koltiva.view.Report.SawitTerampilWinCalculateKPINew-GridCalculate-StoreProcedureName').getValue();
                                if (ProgID == '' || ProgID == null) {
                                    Ext.MessageBox.alert('Warning', lang('Please Select ProgID!'));
                                    return false;
                                }
                                if (StoreProcedureName == '' || StoreProcedureName == null) {
                                    Ext.MessageBox.alert('Warning', lang('Please Select Store Procedure!'));
                                    return false;
                                }
                                Ext.MessageBox.confirm('Message', 'Calculating KPI?', function (btn) {
                                    if (btn == 'yes') {
                                        Ext.getCmp('Koltiva.view.Report.SawitTerampilWinCalculateKPINew-GridCalculate-BtnCalculateNew').setDisabled(true);
                                        Ext.Ajax.request({
                                            waitMsg: 'Please wait...',
                                            url: m_api + '/report_sawit_terampil/calculate_sawit_dinamis',
                                            method: 'POST',
                                            params: {
                                                ProgID: ProgID,
                                                DateProcess: DateProcess,
                                                StoreProcedureName: StoreProcedureName
                                            },
                                            success: function (response, opts) {
                                                var obj = Ext.decode(response.responseText);
                                                //                                                                    console.log(obj);
                                                Ext.getCmp('Koltiva.view.Report.SawitTerampilWinCalculateKPINew-GridCalculate-BtnCalculateNew').setDisabled(false);
                                                if (obj.success == true) {
                                                    thisObj.StoreMainGridCalculate.storeVar.ProgID = Ext.getCmp('Koltiva.view.Report.SawitTerampilWinCalculateKPINew-GridCalculate-ProgID').getValue();
                                                    thisObj.StoreMainGridCalculate.loadPage(1);
                                                    if (obj.max_order == true) {
                                                        Ext.MessageBox.show({
                                                            title: 'Information',
                                                            msg: lang(obj.message),
                                                            buttons: Ext.MessageBox.OK,
                                                            animateTarget: 'mb9',
                                                            icon: 'ext-mb-success'
                                                        });
                                                        thisObj.StoreMainGrid.storeVar.showProcessDate = 'all';
                                                        thisObj.StoreMainGrid.load();
                                                    } else {
                                                        Ext.MessageBox.show({
                                                            title: 'Information',
                                                            msg: lang(obj.message),
                                                            buttons: Ext.MessageBox.OK,
                                                            animateTarget: 'mb9',
                                                            icon: 'ext-mb-success'
                                                        });
                                                    }
                                                }
                                            },
                                            failure: function (response, opts) {
                                                var obj = Ext.decode(response.responseText);
                                                Ext.MessageBox.alert('error', 'Could not connect to the database. Retry later ' + obj);
                                                Ext.getCmp('Koltiva.view.Report.SawitTerampilWinCalculateKPINew-GridCalculate-BtnCalculateNew').setDisabled(false);
                                            }
                                        });
                                    }
                                });
                            }
                        }]
                    }],
                    columns: [{
                        text: lang('Store Procedure Name'),
                        dataIndex: 'StoreProcedureName',
                        flex: 1
                    }, {
                        text: lang('Date Generated'),
                        dataIndex: 'DateGenerated',
                        width: '25%'
                    }, {
                        text: lang('Order No'),
                        dataIndex: 'OrderNo',
                        width: '15%'
                    }]
                }]
            }]
        }];

        thisObj.buttons = [{
            icon: varjs.config.base_url + 'images/icons/new/close.png',
            text: lang('Close'),
            cls: 'Sfr_BtnFormGrey',
            overCls: 'Sfr_BtnFormGrey-Hover',
            handler: function () {
                Ext.getCmp('filterMonthYears').store.load();
                thisObj.close();
            }
        }];


        this.callParent(arguments);
    }
});