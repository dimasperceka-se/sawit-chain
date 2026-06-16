/******************************************
 *  Author : fikrifauzul@gmail.com   
 *  Created On : 08-01-2020
 *  File : MainView.js
 *******************************************/
Ext.define('Koltiva.view.DataAdm.FarmSurveyLoc.MainView', {
    extend: 'Ext.panel.Panel',
    id: 'Koltiva.view.DataAdm.FarmSurveyLoc.MainView',
    style: 'padding:10px 15px 15px 10px;margin:7px 0 0 0;',
    viewVar: false,
    setViewVar: function (value) {
        this.viewVar = value;
    },
    renderTo: 'ext-content',
    listeners: {
        afterRender: function () {
            var thisObj = this;

            //loading
            Ext.MessageBox.show({
                msg: lang('Please wait'),
                progressText: lang('Loading'),
                width: 300,
                wait: true,
                waitConfig: {
                    interval: 200
                },
                icon: 'ext-mb-info',
                animateTarget: 'mb9'
            });

            //render map
            Ext.Ajax.request({
                url: m_api + '/data_adm/farm_survey_loc/render_map',
                method: 'POST',
                params: {
                    ContWidth: Ext.getCmp('Koltiva.view.DataAdm.FarmSurveyLoc.MainView-MainMap').getWidth(),
                    ContHeight: Ext.getCmp('Koltiva.view.DataAdm.FarmSurveyLoc.MainView-MainMap').getHeight()
                },
                success: function (response) {
                    Ext.MessageBox.hide();
                    var MapReturn = response.responseText;
                    Ext.getCmp('Koltiva.view.DataAdm.FarmSurveyLoc.MainView-MainMap').update(MapReturn, true);
                    Ext.getCmp('Koltiva.view.DataAdm.FarmSurveyLoc.MainView-MainMap').doLayout();
                },
                failure: function (response) {
                    Ext.MessageBox.hide();
                    Ext.MessageBox.show({
                        title: 'Failed',
                        msg: lang('Failed to render map'),
                        buttons: Ext.MessageBox.OK,
                        animateTarget: 'mb9',
                        icon: 'ext-mb-error'
                    });

                    //tutup popup
                    thisObj.close();
                }
            });
        }
    },
    initComponent: function () {
        var thisObj = this;

        //Store
        thisObj.StoreGridCoor = Ext.create('Koltiva.store.DataAdm.FarmSurveyLoc.GridCoor', {
            storeVar: {
                FarmerID: null
            }
        });
        thisObj.StoreGridPolygon = Ext.create('Koltiva.store.DataAdm.FarmSurveyLoc.GridPolygon', {
            storeVar: {
                FarmerID: null
            }
        });

        thisObj.ContextMenuCoor = Ext.create('Ext.menu.Menu', {
            items: [{
                    icon: varjs.config.base_url + 'images/icons/new/update.png',
                    text: lang('Update'),
                    cls: 'Sfr_BtnConMenuWhite',
                    hidden: m_act_update,
                    handler: function () {
                        var sm = Ext.getCmp('Koltiva.view.DataAdm.FarmSurveyLoc.MainView-GridCoor').getSelectionModel().getSelection()[0];

                        var WinFormUpdateCoor = Ext.create('Koltiva.view.DataAdm.FarmSurveyLoc.WinFormUpdateCoor', {
                            viewVar: {
                                FarmerID: sm.get('FarmerID'),
                                GardenNr: sm.get('GardenNr'),
                                SurveyNr: sm.get('SurveyNr')
                            }
                        });
                        if (!WinFormUpdateCoor.isVisible()) {
                            WinFormUpdateCoor.center();
                            WinFormUpdateCoor.show();
                        } else {
                            WinFormUpdateCoor.close();
                        }
                    }
                }]
        });
        thisObj.ContextMenuPolygon = Ext.create('Ext.menu.Menu', {
            items: [{
                    icon: varjs.config.base_url + 'images/icons/new/update.png',
                    text: lang('Update Status'),
                    cls: 'Sfr_BtnConMenuWhite',
                    hidden: m_act_update,
                    handler: function () {
                        var sm = Ext.getCmp('Koltiva.view.DataAdm.FarmSurveyLoc.MainView-GridPolygon').getSelectionModel().getSelection()[0];

                        var WinFormUpdateStatusPoly = Ext.create('Koltiva.view.DataAdm.FarmSurveyLoc.WinFormUpdateStatusPoly', {
                            viewVar: {
                                FarmerID: sm.get('FarmerID'),
                                GardenNr: sm.get('GardenNr'),
                                SurveyNr: sm.get('SurveyNr'),
                                Revision: sm.get('Revision')
                            }
                        });
                        if (!WinFormUpdateStatusPoly.isVisible()) {
                            WinFormUpdateStatusPoly.center();
                            WinFormUpdateStatusPoly.show();
                        } else {
                            WinFormUpdateStatusPoly.close();
                        }
                    }
                }, {
                    icon: varjs.config.base_url + 'images/icons/new/delete.png',
                    text: lang('Delete'),
                    cls: 'Sfr_BtnConMenuWhite',
                    hidden: m_act_delete,
                    handler: function () {
                        var sm = Ext.getCmp('Koltiva.view.DataAdm.FarmSurveyLoc.MainView-GridPolygon').getSelectionModel().getSelection()[0];

                        Ext.MessageBox.confirm('Message', 'Do you want to delete this data ?', function (btn) {
                            if (btn == 'yes') {
                                Ext.Ajax.request({
                                    waitMsg: 'Please Wait',
                                    url: m_api + '/data_adm/farm_survey_loc/polygon',
                                    method: 'DELETE',
                                    params: {
                                        FarmerID: sm.get('FarmerID'),
                                        GardenNr: sm.get('GardenNr'),
                                        SurveyNr: sm.get('SurveyNr'),
                                        Revision: sm.get('Revision')
                                    },
                                    success: function (rp, o) {
                                        var r = Ext.decode(rp.responseText);

                                        //trigger button show data
                                        Ext.getCmp('Koltiva.view.DataAdm.FarmSurveyLoc.MainView-BtnShowData').fireHandler(); //buat trigger click event
                                    },
                                    failure: function (rp, o) {
                                        try {
                                            var r = Ext.decode(rp.responseText);
                                            Ext.MessageBox.show({
                                                title: 'Error',
                                                msg: r.message,
                                                buttons: Ext.MessageBox.OK,
                                                animateTarget: 'mb9',
                                                icon: 'ext-mb-error'
                                            });
                                        } catch (err) {
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
                html: '<style>.panelBgPutih .x-panel-body-default {background-color:white !important;}</style>'
            }, {
                layout: 'column',
                border: false,
                style: 'margin-top:-4px;',
                items: [{
                        columnWidth: 0.65,
                        items: [{
                                xtype: 'panel',
                                id: 'Koltiva.view.DataAdm.FarmSurveyLoc.MainView-MainMap',
                                title: lang('Map Location'),
                                frame: true,
                                height: 790
                            }]
                    }, {
                        columnWidth: 0.35,
                        style: 'padding-left:7px;',
                        items: [{
                                xtype: 'panel',
                                title: lang('Search Farmer'),
                                id:'Koltiva.view.DataAdm.FarmSurveyLoc.MainView-PanelSearchFarmer',
                                frame: true,
                                collapsible: true,
                                style: 'padding: 5px 10px;',
                                items: [{
                                        layout: 'column',
                                        border: false,
                                        items: [{
                                                columnWidth: 1,
                                                border: false,
                                                cls: 'panelBgPutih',
                                                style: 'padding:10px 10px 20px 0px;background-color:white !important;border-bottom:1px dashed gray;',
                                                layout: {
                                                    type: 'hbox',
                                                    pack: 'left',
                                                    align: 'middle'
                                                },
                                                items: [{
                                                        xtype: 'textfield',
                                                        id: 'Koltiva.view.DataAdm.FarmSurveyLoc.MainView-TextFarmerID',
                                                        name: 'Koltiva.view.DataAdm.FarmSurveyLoc.MainView-TextFarmerID',
                                                        emptyText: lang('Enter FarmerID here')
                                                    }, {
                                                        xtype: 'button',
                                                        text: lang('Show Data'),
                                                        id: 'Koltiva.view.DataAdm.FarmSurveyLoc.MainView-BtnShowData',
                                                        style: 'margin-left:25px;',
                                                        handler: function () {
                                                            //Check apakah ada FarmerID
                                                            var FarmerID = Ext.getCmp('Koltiva.view.DataAdm.FarmSurveyLoc.MainView-TextFarmerID').getValue();
                                                            if (FarmerID != '') {
                                                                Ext.MessageBox.show({
                                                                    msg: lang('Please wait'),
                                                                    progressText: lang('Loading'),
                                                                    width: 300,
                                                                    wait: true,
                                                                    waitConfig: {
                                                                        interval: 200
                                                                    },
                                                                    icon: 'ext-mb-info',
                                                                    animateTarget: 'mb9'
                                                                });

                                                                Ext.Ajax.request({
                                                                    url: m_api + '/data_adm/farm_survey_loc/show_location',
                                                                    method: 'POST',
                                                                    params: {
                                                                        FarmerID: FarmerID
                                                                    },
                                                                    success: function (rp, o) {
                                                                        var r = Ext.decode(rp.responseText);
                                                                        jml_cek_all_gar = r.data_koor.length;
                                                                        jml_cek_gar = r.data_koor.length;
                                                                        data_gar = r.data_koor;

                                                                        jml_cek_all_poly = r.data_poly.length;
                                                                        jml_cek_poly = r.data_poly.length;

                                                                        Ext.getCmp('Koltiva.view.DataAdm.FarmSurveyLoc.MainView-Garden-BtnCheck').setDisabled(true);
                                                                        Ext.getCmp('Koltiva.view.DataAdm.FarmSurveyLoc.MainView-Garden-BtnUncheck').setDisabled(false);
                                                                        Ext.getCmp('Koltiva.view.DataAdm.FarmSurveyLoc.MainView-Poly-BtnCheck').setDisabled(true);
                                                                        Ext.getCmp('Koltiva.view.DataAdm.FarmSurveyLoc.MainView-Poly-BtnUncheck').setDisabled(false);
                                                                        //console.log(r);

                                                                        //Set value farmer
                                                                        Ext.getCmp('Koltiva.view.DataAdm.FarmSurveyLoc.MainView-FarmerID').setValue(r.data_farmer.FarmerID);
                                                                        Ext.getCmp('Koltiva.view.DataAdm.FarmSurveyLoc.MainView-FarmerName').setValue(r.data_farmer.FarmerName);
                                                                        if (r.data_farmer.Gender == '2')
                                                                            Ext.getCmp('Koltiva.view.DataAdm.FarmSurveyLoc.MainView-Gender2').setValue(true);
                                                                        else
                                                                            Ext.getCmp('Koltiva.view.DataAdm.FarmSurveyLoc.MainView-Gender1').setValue(true);

                                                                        //Load store
                                                                        thisObj.StoreGridCoor.storeVar.FarmerID = FarmerID;
                                                                        thisObj.StoreGridCoor.load();
                                                                        thisObj.StoreGridPolygon.storeVar.FarmerID = FarmerID;
                                                                        thisObj.StoreGridPolygon.load();

                                                                        //Panggil fungsi render koordinat
                                                                        if (r.data_koor.length > 0) {
//                                                                            console.log(r.data_koor);
                                                                            renderCoordinatesAndPolygon(r.data_koor, r.data_poly);
                                                                        } else {
                                                                            removeCoordinates();
                                                                            removePolygon();
                                                                        }

                                                                        Ext.MessageBox.hide();
                                                                        Ext.getCmp('Koltiva.view.DataAdm.FarmSurveyLoc.MainView-PanelSearchFarmer').collapse();

                                                                    },
                                                                    failure: function (rp, o) {
                                                                        Ext.MessageBox.hide();
                                                                        try {
                                                                            var r = Ext.decode(rp.responseText);
                                                                            Ext.MessageBox.show({
                                                                                title: lang('Information'),
                                                                                msg: r.message,
                                                                                buttons: Ext.MessageBox.OK,
                                                                                animateTarget: 'mb9',
                                                                                icon: 'ext-mb-error'
                                                                            });
                                                                        } catch (err) {
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
                                                            } else {
                                                                Ext.MessageBox.show({
                                                                    title: lang('Information'),
                                                                    msg: lang('FarmerID is empty'),
                                                                    buttons: Ext.MessageBox.OK,
                                                                    animateTarget: 'mb9',
                                                                    icon: 'ext-mb-info'
                                                                });
                                                            }
                                                        }
                                                    }]
                                            }, {
                                                columnWidth: 1,
                                                border: false,
                                                cls: 'panelBgPutih',
                                                layout: 'form',
                                                style: 'padding-top:15px;background-color:white !important;',
                                                items: [{
                                                        xtype: 'textfield',
                                                        fieldLabel: lang('Farmer ID'),
                                                        readOnly: true,
                                                        id: 'Koltiva.view.DataAdm.FarmSurveyLoc.MainView-FarmerID',
                                                        name: 'Koltiva.view.DataAdm.FarmSurveyLoc.MainView-FarmerID',
                                                    }, {
                                                        xtype: 'textfield',
                                                        fieldLabel: lang('Name'),
                                                        readOnly: true,
                                                        id: 'Koltiva.view.DataAdm.FarmSurveyLoc.MainView-FarmerName',
                                                        name: 'Koltiva.view.DataAdm.FarmSurveyLoc.MainView-FarmerName',
                                                    }, {
                                                        xtype: 'radiogroup',
                                                        fieldLabel: lang('Gender'),
                                                        columns: 2,
                                                        readOnly: true,
                                                        items: [{
                                                                boxLabel: lang('Male'),
                                                                name: 'Koltiva.view.DataAdm.FarmSurveyLoc.MainView-Gender',
                                                                inputValue: '1',
                                                                id: 'Koltiva.view.DataAdm.FarmSurveyLoc.MainView-Gender1',
                                                                listeners: {
                                                                    change: function () {
                                                                        return false;
                                                                    }
                                                                }
                                                            }, {
                                                                boxLabel: lang('Female'),
                                                                name: 'Koltiva.view.DataAdm.FarmSurveyLoc.MainView-Gender',
                                                                inputValue: '2',
                                                                id: 'Koltiva.view.DataAdm.FarmSurveyLoc.MainView-Gender2',
                                                                listeners: {
                                                                    change: function () {
                                                                        return false;
                                                                    }
                                                                }
                                                            }]
                                                    }]
                                            }]
                                    }]
                            }, {
                                xtype: 'grid',
                                id: 'Koltiva.view.DataAdm.FarmSurveyLoc.MainView-GridCoor',
                                cls: 'Sfr_GridNew',
                                style: 'border:1px solid #CCC;margin-top:15px;',
                                loadMask: true,
                                selType: 'rowmodel',
                                store: thisObj.StoreGridCoor,
                                height: 300,
                                viewConfig: {
                                    deferEmptyText: false,
                                    emptyText: lang('No Data Available')
                                },
                                dockedItems: [{
                                        xtype: 'toolbar',
                                        dock: 'top',
                                        items: [{
                                                text: lang('Check All'),
                                                id: 'Koltiva.view.DataAdm.FarmSurveyLoc.MainView-Garden-BtnCheck',
                                                cls: 'Sfr_BtnGridBlue',
                                                overCls: 'Sfr_BtnGridBlue-Hover',
                                                handler: function () {
                                                    var i = 0;
                                                    for (i = 0; i < jml_cek_all_gar; i++) {
                                                        showHideCoordinates(true, data_gar[i].urutanIndex);
                                                        $('.check_gar').prop('checked', true);
                                                        if (i == (jml_cek_all_gar - 1)) {
                                                            jml_cek_gar = jml_cek_all_gar;
                                                            Ext.getCmp('Koltiva.view.DataAdm.FarmSurveyLoc.MainView-Garden-BtnCheck').setDisabled(true);
                                                            Ext.getCmp('Koltiva.view.DataAdm.FarmSurveyLoc.MainView-Garden-BtnUncheck').setDisabled(false);
                                                        }
                                                    }
                                                }
                                            }, {
                                                text: lang('Uncheck All'),
                                                id: 'Koltiva.view.DataAdm.FarmSurveyLoc.MainView-Garden-BtnUncheck',
                                                cls: 'Sfr_BtnGridRed',
                                                overCls: 'Sfr_BtnGridRed-Hover',
                                                handler: function () {
                                                    var i = 0;
                                                    for (i = 0; i < jml_cek_all_gar; i++) {
                                                        showHideCoordinates(false, data_gar[i].urutanIndex);
                                                        $('.check_gar').prop('checked', false);
                                                        if (i == (jml_cek_all_gar - 1)) {
                                                            jml_cek_gar = 0;
                                                            Ext.getCmp('Koltiva.view.DataAdm.FarmSurveyLoc.MainView-Garden-BtnCheck').setDisabled(false);
                                                            Ext.getCmp('Koltiva.view.DataAdm.FarmSurveyLoc.MainView-Garden-BtnUncheck').setDisabled(true);
                                                        }
                                                    }
                                                }
                                            }]
                                    }],
                                columns: [{
                                        dataIndex: 'checkboxhtml',
                                        flex: 0.5,
                                        renderer: function (t, meta, record) {
                                            var RetVal = '<input value="1" type="checkbox" checked="" class="check_gar" onclick="javascript:showHideCoordinates('+ 'this.checked,' + record.data.urutanIndex + ')" />';
                                            return RetVal;
                                        }
                                    }, {
                                        text: ' ',
                                        xtype: 'actioncolumn',
                                        flex: 0.5,
                                        items: [{
                                                icon: varjs.config.base_url + 'images/icons/new/action.png',
                                                handler: function (grid, rowIndex, colIndex, item, e, record) {
                                                    thisObj.ContextMenuCoor.showAt(e.getXY());
                                                }
                                            }]
                                    }, {
                                        text: lang('Garden Information'),
                                        dataIndex: 'GardenInfo',
                                        flex: 8,
                                        renderer: function (t, meta, record) {
                                            //console.log(record);
                                            var RetVal = lang('GardenNr') + ': ' + record.data.GardenNr + ', ' + lang('SurveyNr') + ': ' + record.data.SurveyNr;
                                            return RetVal;
                                        }
                                    }, {
                                        text: lang('Coordinate'),
                                        dataIndex: 'CoordinateLabel',
                                        flex: 6
                                    }, {
                                        dataIndex: 'FarmerID',
                                        hidden: true
                                    }, {
                                        dataIndex: 'GardenNr',
                                        hidden: true
                                    }, {
                                        dataIndex: 'SurveyNr',
                                        hidden: true
                                    }]
                            }, {
                                xtype: 'grid',
                                id: 'Koltiva.view.DataAdm.FarmSurveyLoc.MainView-GridPolygon',
                                cls: 'Sfr_GridNew',
                                style: 'border:1px solid #CCC;margin-top:15px;',
                                loadMask: true,
                                selType: 'rowmodel',
                                store: thisObj.StoreGridPolygon,
                                height: 430,
                                viewConfig: {
                                    deferEmptyText: false,
                                    emptyText: lang('No Data Available')
                                },
                                dockedItems: [{
                                        xtype: 'toolbar',
                                        dock: 'top',
                                        items: [{
                                                text: lang('Check All'),
                                                id: 'Koltiva.view.DataAdm.FarmSurveyLoc.MainView-Poly-BtnCheck',
                                                cls: 'Sfr_BtnGridBlue',
                                                overCls: 'Sfr_BtnGridBlue-Hover',
                                                handler: function () {
                                                    var i = 0;
                                                    for (i = 0; i < jml_cek_all_poly; i++) {
                                                        showHidePolygon(true, i);
                                                        $('.check_poly').prop('checked', true);
                                                        if (i == (jml_cek_all_poly - 1)) {
                                                            jml_cek_poly = jml_cek_all_poly;
                                                            Ext.getCmp('Koltiva.view.DataAdm.FarmSurveyLoc.MainView-Poly-BtnCheck').setDisabled(true);
                                                            Ext.getCmp('Koltiva.view.DataAdm.FarmSurveyLoc.MainView-Poly-BtnUncheck').setDisabled(false);
                                                        }
                                                    }
                                                }
                                            }, {
                                                text: lang('Uncheck All'),
                                                id: 'Koltiva.view.DataAdm.FarmSurveyLoc.MainView-Poly-BtnUncheck',
                                                cls: 'Sfr_BtnGridRed',
                                                overCls: 'Sfr_BtnGridRed-Hover',
                                                handler: function () {
                                                    var i = 0;
                                                    for (i = 0; i < jml_cek_all_poly; i++) {
                                                        showHidePolygon(false, i);
                                                        $('.check_poly').prop('checked', false);
                                                        if (i == (jml_cek_all_poly - 1)) {
                                                            jml_cek_poly = 0;
                                                            Ext.getCmp('Koltiva.view.DataAdm.FarmSurveyLoc.MainView-Poly-BtnCheck').setDisabled(false);
                                                            Ext.getCmp('Koltiva.view.DataAdm.FarmSurveyLoc.MainView-Poly-BtnUncheck').setDisabled(true);
                                                        }
                                                    }
                                                }
                                            }]
                                    }],
                                columns: [{
                                        dataIndex: 'checkboxhtml',
                                        flex: 0.5,
                                        renderer: function (t, meta, record) {
                                            //var RetVal = '<input value="1" type="checkbox" checked="" onclick="javascript:showHideCoordinates('+record.data.FarmerID+','+record.data.GardenNr+','+record.data.SurveyNr+',this.checked,'+record.data.urutanIndex+')" />';
                                            var RetVal = '<input value="1" type="checkbox" checked="" class="check_poly" onclick="javascript:showHidePolygon(this.checked,' + record.data.UrutanIndex + ')" />';
                                            return RetVal;
                                        }
                                    }, {
                                        text: ' ',
                                        xtype: 'actioncolumn',
                                        flex: 0.5,
                                        items: [{
                                                icon: varjs.config.base_url + 'images/icons/new/action.png',
                                                handler: function (grid, rowIndex, colIndex, item, e, record) {
                                                    thisObj.ContextMenuPolygon.showAt(e.getXY());
                                                }
                                            }]
                                    }, {
                                        text: lang('Polygon Information'),
                                        dataIndex: 'GardenInfo',
                                        flex: 8,
                                        renderer: function (t, meta, record) {
                                            var RetVal = '<table><tr><td>' + lang('Color') + ':</td><td><div style="width:25px;height:15px;background-color:' + record.data.ColorCode + ';float:left;margin-left:10px;"></div>&nbsp;' + record.data.ColorName + '</td></tr></table>';
                                            RetVal += lang('GardenNr') + ': ' + record.data.GardenNr + ', ' + lang('SurveyNr') + ': ' + record.data.SurveyNr + '<br>';
                                            RetVal += lang('Revision') + ': ' + record.data.Revision + ', ' + lang('Status Polygon') + ': ' + record.data.StatusPolygon + '<br>';
                                            RetVal += lang('Jumlah Titik') + ': ' + record.data.JumlahTitik;
                                            return RetVal;
                                        }
                                    }]
                            }]
                    }]
            }];

        //fields: ['FarmerID', 'GardenNr','SurveyNr','Revision','StatusPolygon','JumlahTitik','GardenInfo','UrutanIndex','ColorName','ColorCode'],

        this.callParent(arguments);
    }
});