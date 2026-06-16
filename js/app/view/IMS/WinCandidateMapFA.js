/*
* @Author: nikolius
* @Date:   2018-04-19 10:13:51
* @Last Modified by:   nikolius
* @Last Modified time: 2018-04-23 14:20:55
*/

Ext.define('Koltiva.view.IMS.WinCandidateMapFA' ,{
    extend: 'Ext.window.Window',
    id: 'Koltiva.view.IMS.WinCandidateMapFA',
    title: lang('Farmer Candidates Mapping per Field Agents (FA)'),
    closable: true,
    modal: true,
    closeAction: 'destroy',
    width: '94%',
    height: '90%',
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
    scrollOffset: 20,
    initComponent: function() {
        var thisObj = this;

        //STORE
        thisObj.StoreGridMappingFA = Ext.create('Koltiva.store.IMS.GridMappingFA', {
            storeVar: {
                IMSID: thisObj.viewVar.IMSID
            }
        });
        thisObj.StoreCmbFilterFA = Ext.create('Koltiva.store.IMS.CmbFilterFA', {
            storeVar: {
                IMSID: thisObj.viewVar.IMSID
            }
        });

        thisObj.items = [{
                xtype: 'panel',
                frame: true,
                bodyStyle: {
                    "background-color": "#F0F0F0"
                },
                style: 'background-color:#F0F0F0;margin:10px;padding:10px;',
                title: lang('Import Mapping'),
                items: [{
                        xtype: 'form',
                        id: 'Koltiva.view.IMS.WinCandidateMapFA-Form',
                        fieldDefaults: {
                            labelAlign: 'left',
                            labelWidth: 250
                        },
                        fileUpload: true,
                        layout: 'form',
                        items: [{
                                xtype: 'fileuploadfield',
                                fieldLabel: lang('File') + ' (type: xlsx)',
                                labelWidth: 125,
                                id: 'Koltiva.view.IMS.WinCandidateMapFA-Form-FileImport',
                                name: 'Koltiva.view.IMS.WinCandidateMapFA-Form-FileImport',
                                buttonText: 'Browse',
                                allowBlank: false,
                                listeners: {
                                    'change': function (fb, v) {
                                        var form = Ext.getCmp('Koltiva.view.IMS.WinCandidateMapFA-Form').getForm();
                                        form.submit({
                                            url: m_api + '/ims/import_candidate_mapping_fa',
                                            waitMsg: 'Sending and importing file...',
                                            params: {IMSID: thisObj.viewVar.IMSID},
                                            success: function (fp, o) {
                                                var r = Ext.decode(o.response.responseText);

                                                Ext.MessageBox.show({
                                                    title: lang('Success'),
                                                    msg: lang('Data Imported'),
                                                    buttons: Ext.MessageBox.OK,
                                                    animateTarget: 'mb9',
                                                    icon: 'ext-mb-success'
                                                });

                                                thisObj.StoreGridMappingFA.load();
                                            },
                                            failure: function (fp, o) {
                                                var r = Ext.decode(o.response.responseText);
                                                Ext.MessageBox.show({
                                                    title: 'Failed',
                                                    msg: r.message,
                                                    buttons: Ext.MessageBox.OK,
                                                    animateTarget: 'mb9',
                                                    icon: 'ext-mb-error'
                                                });
                                            }
                                        });
                                    }
                                }
                            }, {
                                //id:'Koltiva.view.Grower.FormMainGrower-ConsentLetterUrl',
                                //html:'<a style="text-decoration:underline;" href="'+varjs.config.base_url+'api/files/template-import-ims-candidate-map-fa.xlsx" target="_blank">Download Template File for Import</a>'
                                html: '<a style="text-decoration:underline;" href="' + varjs.config.base_url + 'api/ims/ims_candidate_mapping/' + thisObj.viewVar.IMSID + '" target="_blank">Download Template File for Import</a>'
                            }]
                    }]
            }, {
                xtype: 'gridpanel',
                title: lang('Candidate'),
                id: 'Koltiva.view.IMS.WinCandidateMapFA-Grid',
                style: 'border:1px solid #CCC;margin:25px 10px 10px 10px;',
                store: thisObj.StoreGridMappingFA,
                width: '99%',
                height: 500,
                loadMask: true,
                selType: 'rowmodel',
                viewConfig: {
                    deferEmptyText: false,
                    emptyText: lang('No data Available')
                },
                dockedItems: [{
                        xtype: 'pagingtoolbar',
                        store: thisObj.StoreGridMappingFA,
                        dock: 'bottom',
                        displayInfo: true
                    }, {
                        xtype: 'toolbar',
                        items: [{
                                xtype: 'combobox',
                                id: 'Koltiva.view.IMS.WinCandidateMapFA-Grid-CmbFilterFA',
                                name: 'Koltiva.view.IMS.WinCandidateMapFA-Grid-CmbFilterFA',
                                store: thisObj.StoreCmbFilterFA,
                                width: 375,
                                emptyText: lang('Filter FA'),
                                queryMode: 'local',
                                displayField: 'label',
                                valueField: 'id',
                                listeners: {
                                    change: function (cb, nv, ov) {
                                        thisObj.StoreGridMappingFA.setStoreVar({
                                            IMSID: thisObj.viewVar.IMSID,
                                            UserId: nv
                                        })
                                        thisObj.StoreGridMappingFA.load();
                                    }
                                }
                            }, {
                                icon: varjs.config.base_url + 'images/icons/new/export.png',
                                text: lang('Export'),
                                cls: 'Sfr_BtnGridPaleBlue',
                                overCls: 'Sfr_BtnGridPaleBlue-Hover',
                                hidden: m_act_export,
                                handler: function () {
                                    Ext.MessageBox.show({
                                        msg: 'Loading, please wait...',
                                        progressText: 'Exporting...',
                                        width: 300,
                                        wait: true,
                                        waitConfig: {interval: 200},
                                        icon: 'ext-mb-download', //custom class in msg-box.html
                                        iconHeight: 50,
                                        animateTarget: 'mb7'
                                    });

                                    var FilterFA = Ext.getCmp('Koltiva.view.IMS.WinCandidateMapFA-Grid-CmbFilterFA').getValue();
                                    var url = m_api + '/ims/ims_event_detail_mapping_fa_grid/' + thisObj.viewVar.IMSID + '/' + FilterFA + '/';
                                    if (window.open(url, 'Export', "height=200,width=200")) {
                                        Ext.MessageBox.hide();
                                    }
                                }
                            }]
                    }],
                columns: [{
                        text: 'No',
                        xtype: 'rownumberer',
                        width: '3%'
                    }, {
                        text: lang('Field Agent'),
                        dataIndex: 'FieldAgent',
                        width: '12%'
                    }, {
                        text: lang('Farmer'),
                        dataIndex: 'Farmer',
                        flex: 1
                    }, {
                        text: lang('Farmer Group'),
                        dataIndex: 'FarmerGroup',
                        flex: 1
                    }, {
                        text: lang('Province'),
                        dataIndex: 'Province',
                        width: '10%'
                    }, {
                        text: lang('District'),
                        dataIndex: 'District',
                        width: '10%'
                    }, {
                        text: lang('SubDistrict'),
                        dataIndex: 'SubDistrict',
                        flex: 1
                    }, {
                        text: lang('Village'),
                        dataIndex: 'Village',
                        flex: 1
                    }]
            }];

        thisObj.buttons = [{
                margin: '5px',
                icon: varjs.config.base_url + 'images/icons/new/close.png',
                text: lang('Close'),
                cls: 'Sfr_BtnFormGrey',
                overCls: 'Sfr_BtnFormGrey-Hover',
                handler: function () {
                    thisObj.close();
                }
            }];

        this.callParent(arguments);
    }
});