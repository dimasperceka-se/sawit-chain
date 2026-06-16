/******************************************
 *  Author : fikrifauzul@gmail.com
 *  Created On : 13-05-2020
 *  File : WinFormAddPolygonCompare.js
 *******************************************/

/*
    Param2 yg diperlukan ketika load View ini
    - ParentObj
    - UserId
*/

Ext.define('Koltiva.view.DataAdm.PolygonOver.WinFormAddPolygonCompare' ,{
    extend: 'Ext.window.Window',
    id: 'Koltiva.view.DataAdm.PolygonOver.WinFormAddPolygonCompare',
    title: lang('List of Polygon'),
    closable: true,
    modal: true,
    closeAction: 'destroy',
    width: '96%',
    height: 600,
    overflowY: 'auto',
    style:'padding:2px;',
    viewVar: false,
    setViewVar: function(value){
        this.viewVar = value;
    },
    initComponent: function() {
        var thisObj = this;

        thisObj.StoreGridMain = Ext.create('Koltiva.store.DataAdm.PolygonOver.WinFormAddPolygonCompareMainGrid',{
            storeVar: {
                UserId: thisObj.viewVar.UserId,
                TxtSearchLabel: null,
                CmbStatusCheck: null
            }
        });

        thisObj.CmbStatusCheck = Ext.create('Ext.data.Store', {
            fields: ['id', 'label'],
            data: [{
                "id": "new",
                "label": "new"
            }, {
                "id": "verified",
                "label": "verified"
            }]
        });

        thisObj.items = [{
            xtype: 'grid',
            id: 'Koltiva.view.DataAdm.PolygonOver.WinFormAddPolygonCompare-MainGrid',
            style: 'border:1px solid #CCC;',
            cls:'Sfr_GridNew',
            loadMask: true,
            selType: 'rowmodel',
            store: thisObj.StoreGridMain,
            enableColumnHide: false,
            height:500,
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
                    name: 'Koltiva.view.DataAdm.PolygonOver.WinFormAddPolygonCompare-TxtSearchLabel',
                    id: 'Koltiva.view.DataAdm.PolygonOver.WinFormAddPolygonCompare-TxtSearchLabel',
                    xtype: 'textfield',
                    baseCls:'Sfr_TxtfieldSearchGrid',
                    width: 300,
                    emptyText: lang('Cari berdasar nama / id')
                },{
                    store: thisObj.CmbStatusCheck,
                    xtype: 'combobox',
                    queryMode: 'local',
                    displayField: 'label',
                    valueField: 'id',
                    id: 'Koltiva.view.DataAdm.PolygonOver.WinFormAddPolygonCompare-CmbStatusCheck',
                    name: 'Koltiva.view.DataAdm.PolygonOver.WinFormAddPolygonCompare-CmbStatusCheck',
                    emptyText: lang('All Status'),
                    style: 'margin-top:5px;'
                },{
                    xtype:'button',
                    icon: varjs.config.base_url + 'images/icons/silk/search.png',
                    text: lang('Search'),
                    cls:'Sfr_BtnGridBlue',
                    overCls:'Sfr_BtnGridBlue-Hover',
                    handler: function() {
                        thisObj.StoreGridMain.storeVar.TxtSearchLabel = Ext.getCmp('Koltiva.view.DataAdm.PolygonOver.WinFormAddPolygonCompare-TxtSearchLabel').getValue();
                        thisObj.StoreGridMain.storeVar.CmbStatusCheck = Ext.getCmp('Koltiva.view.DataAdm.PolygonOver.WinFormAddPolygonCompare-CmbStatusCheck').getValue();
                        thisObj.StoreGridMain.load();
                    }
                }]
            }],
            columns: [{
                text: 'No',
                width: '5%',
                xtype: 'rownumberer'
            },{
                xtype : 'checkcolumn',
                text : '&nbsp;',
                dataIndex : 'chdata',
                width:'4%'
            },{
                text: lang('Supplier ID'),
                dataIndex: 'SupplierID',
                width:'6%'
            },{
                text: lang('Supplier Display ID'),
                dataIndex: 'ID',
                width:'10%'
            },{
                text: lang('Name'),
                dataIndex: 'Name',
                width:'35%'
            },{
                text: lang('FarmNr'),
                dataIndex: 'FarmNr',
                width:'7%'
            },{
                text: lang('Revision'),
                dataIndex: 'Revision',
                width:'8%'
            },{
                text: lang('Status Check'),
                dataIndex: 'StatusCheck',
                width:'10%'
            },{
                text: lang('Date Created'),
                dataIndex: 'DateCreated',
                width:'14%'
            }]
        }];

        //buttons -------------------------------------------------------------- (begin)
        thisObj.buttons = [{
            icon: varjs.config.base_url + 'images/icons/new/save.png',
            cls:'Sfr_BtnFormBlue',
            overCls:'Sfr_BtnFormBlue-Hover',
            text: lang('Add to compare'),
            handler: function () {
                var records = thisObj.StoreGridMain.queryBy(function(record) {
                    return record.get('chdata') === true;
                });
                var ids = [];
                records.each(function(record) {
                    ids.push(record.get('SupplierID')+'@'+record.get('FarmNr')+'@'+record.get('Revision'));
                });

                if(ids.length > 0){

                    Ext.Ajax.request({
                        url: m_api + '/data_adm/polygon_over/add_polygon',
                        method: 'POST',
                        params: {
                            Ids: Ext.encode(ids)
                        },
                        success: function(rp, o) {
                            var r = Ext.decode(rp.responseText);
                            Ext.MessageBox.show({
                                title: 'Information',
                                msg: r.message,
                                buttons: Ext.MessageBox.OK,
                                animateTarget: 'mb9',
                                icon: 'ext-mb-success'
                            });

                            thisObj.StoreGridMain.load();
                            thisObj.viewVar.ParentObj.StoreGridMain.load();
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

                } else {
                    Ext.MessageBox.show({
                        title: lang('Attention'),
                        msg: lang('No item selected'),
                        buttons: Ext.MessageBox.OK,
                        animateTarget: 'mb9',
                        icon: 'ext-mb-info'
                    });
                }
            }
        },{
            icon: varjs.config.base_url + 'images/icons/new/save.png',
            cls:'Sfr_BtnFormBlue',
            overCls:'Sfr_BtnFormBlue-Hover',
            text: lang('Add all data from grid to compare'),
            handler: function () {
                Ext.MessageBox.confirm('Message', lang('Do you want to add all data to compare ?'), function(btn) {
                    if (btn == 'yes') {
                        Ext.Ajax.request({
                            url: m_api + '/data_adm/polygon_over/add_all_polygon',
                            method: 'POST',
                            params: {
                                TxtSearchLabel: Ext.getCmp('Koltiva.view.DataAdm.PolygonOver.WinFormAddPolygonCompare-TxtSearchLabel').getValue(),
                                CmbStatusCheck: Ext.getCmp('Koltiva.view.DataAdm.PolygonOver.WinFormAddPolygonCompare-CmbStatusCheck').getValue()
                            },
                            success: function(rp, o) {
                                var r = Ext.decode(rp.responseText);
                                Ext.MessageBox.show({
                                    title: 'Information',
                                    msg: r.message,
                                    buttons: Ext.MessageBox.OK,
                                    animateTarget: 'mb9',
                                    icon: 'ext-mb-success'
                                });

                                thisObj.StoreGridMain.load();
                                thisObj.viewVar.ParentObj.StoreGridMain.load();
                                thisObj.close();
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
        },{
            icon: varjs.config.base_url + 'images/icons/new/close.png',
			text: lang('Close'),
			cls:'Sfr_BtnFormGrey',
			overCls:'Sfr_BtnFormGrey-Hover',
            handler: function() {
                thisObj.close();
            }
        }];
        //buttons -------------------------------------------------------------- (end)

        this.callParent(arguments);
    }
});