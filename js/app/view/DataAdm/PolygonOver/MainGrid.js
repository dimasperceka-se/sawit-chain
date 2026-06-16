/******************************************
 *  Author : fikrifauzul@gmail.com
 *  Created On : 13-05-2020
 *  File : MainGrid.js
 *******************************************/

Ext.define('Koltiva.view.DataAdm.PolygonOver.MainGrid', {
    extend: 'Ext.panel.Panel',
    id: 'Koltiva.view.DataAdm.PolygonOver.MainGrid',
    style: 'padding:0 15px 15px 15px;margin:10px 0 0 0;',
    viewVar: false,
    setViewVar: function (value) {
        this.viewVar = value;
    },
    renderTo: 'ext-content',
    listeners: {
        afterRender: function () {
            var thisObj = this;
            // document.getElementById('ContentTopBar').style.display = 'none';
        }
    },
    initComponent: function () {
        var thisObj = this;

        thisObj.StoreGridMain = Ext.create('Koltiva.store.DataAdm.PolygonOver.MainGrid');

        thisObj.items = [{
            xtype:'panel',
            title: lang('Polygon to Compare'),
            frame: true,
            cls: 'Sfr_PanelLayoutForm',
            collapsible: true,
            style:'margin-top:0px;padding-top:0px;',
            items: [{
                xtype: 'grid',
                id: 'Koltiva.view.DataAdm.PolygonOver.MainGrid-MainGrid',
                style: 'border:1px solid #CCC;margin-top:4px;',
                cls:'Sfr_GridNew',
                loadMask: true,
                selType: 'rowmodel',
                store: thisObj.StoreGridMain,
                enableColumnHide: false,
                height:550,
                viewConfig: {
                    deferEmptyText: false,
                    emptyText: GetDefaultContentNoData()
                },
                dockedItems: [{
                    xtype: 'pagingtoolbar',
                    store: thisObj.StoreGridMain,
                    dock: 'bottom',
                    displayInfo: true,
                    style:'padding-right:12px;'
                },{
                    xtype: 'toolbar',
                    dock:'top',
                    items: [{
                        xtype:'button',
                        icon: varjs.config.base_url + 'images/icons/new/reload.png',
                        text: lang('Generate Compare'),
                        cls:'Sfr_BtnGridBlue',
                        overCls:'Sfr_BtnGridBlue-Hover',
                        handler: function() {
                            Ext.MessageBox.show({
                                msg: 'Please wait...',
                                progressText: 'Generating...',
                                width: 300,
                                wait: true,
                                waitConfig: {
                                    interval: 200
                                },
                                icon: 'ext-mb-download', //custom class in msg-box.html
                                animateTarget: 'mb7'
                            });

                            Ext.Ajax.request({
                                url: m_api + '/data_adm/polygon_over/generate_compare/',

                                method: 'GET',
                                waitMsg: lang('Please Wait'),
                                timeout: 3600000,
                                success: function (data) {
                                    Ext.MessageBox.hide();
                                    Ext.getCmp('Koltiva.view.DataAdm.PolygonOver.MainGrid-MainGrid').getStore().loadPage(1)
                                },
                                failure: function () {
                                    Ext.MessageBox.hide();
                                    Ext.MessageBox.show({
                                        title: 'Notifications',
                                        msg: 'Failed to export, Please try again.',
                                        buttons: Ext.MessageBox.OK,
                                        animateTarget: 'mb9',
                                        icon: 'ext-mb-error'
                                    });
                                }
                            });
                        }
                    },{
                        xtype:'tbspacer',
                        flex:1
                    },{
                        xtype:'button',
                        icon: varjs.config.base_url + 'images/icons/silk/excel.png',
                        text: lang('Export'),
                        cls:'Sfr_BtnGridGreen',
                        overCls:'Sfr_BtnGridGreen-Hover',
                        handler: function() {
                            Ext.MessageBox.show({
                                msg: 'Please wait...',
                                progressText: 'Exporting...',
                                width: 300,
                                wait: true,
                                waitConfig: {
                                    interval: 200
                                },
                                icon: 'ext-mb-download', //custom class in msg-box.html
                                animateTarget: 'mb7'
                            });

                            try {
                                Ext.destroy(Ext.get('downloadIframe'));
                            } catch (e) {}

                            Ext.Ajax.request({
                                url: m_api + '/data_adm/polygon_over/polygon_compare_export_excel/',

                                method: 'GET',
                                waitMsg: lang('Please Wait'),
                                timeout: 360000,
                                success: function (data) {
                                    Ext.MessageBox.hide();
                                    var jsonResp = JSON.parse(data.responseText);
                                    window.location = jsonResp.filenya;
                                },
                                failure: function () {
                                    Ext.MessageBox.hide();
                                    Ext.MessageBox.show({
                                        title: 'Notifications',
                                        msg: 'Failed to export, Please try again.',
                                        buttons: Ext.MessageBox.OK,
                                        animateTarget: 'mb9',
                                        icon: 'ext-mb-error'
                                    });
                                }
                            });
                        }
                    }]
                }],
                columns: [{
                    text: 'No',
                    width: '5%',
                    xtype: 'rownumberer'
                },{
                    text: lang('Farmer Display ID'),
                    dataIndex: 'MemberDisplayID',
                    flex:1
                },{
                    text: lang('Farmer Name'),
                    dataIndex: 'MemberName',
                    flex:1
                },{
                    text: lang('PlotNr'),
                    dataIndex: 'PlotNr',
                    flex:1
                },{
                    text: lang('Revision'),
                    dataIndex: 'Revision',
                    flex:1
                },{
                    text: lang('Status Check'),
                    dataIndex: 'StatusCheck',
                    flex:1
                },{
                    text: lang('Overlap Function'),
                    dataIndex: 'Function',
                    flex:1
                },{
                    text: lang('Overlap Function Desc'),
                    dataIndex: 'FunctionDescription',
                    flex:1
                }]
            }]
        }];

        this.callParent(arguments);
    }
});