/******************************************
 *  Author : fikrifauzul@gmail.com
 *  Created On : 13-05-2020
 *  File : WinGridPolygonCompare.js
 *******************************************/

/*
    Param2 yg diperlukan ketika load View ini
    - ParentObj
    - UserId
*/

Ext.define('Koltiva.view.DataAdm.PolygonOver.WinGridPolygonCompare' ,{
    extend: 'Ext.window.Window',
    id: 'Koltiva.view.DataAdm.PolygonOver.WinGridPolygonCompare',
    title: lang('Polygon Compare Result'),
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

        thisObj.StoreGridMain = Ext.create('Koltiva.store.DataAdm.PolygonOver.WinGridPolygonCompareMainGrid',{
            storeVar: {
                UserId: thisObj.viewVar.UserId
            }
        });

        thisObj.items = [{
            xtype: 'grid',
            id: 'Koltiva.view.DataAdm.PolygonOver.WinGridPolygonCompare-MainGrid',
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
                    xtype:'button',
                    icon: varjs.config.base_url + 'images/icons/new/export.png',
                    text: lang('Export to Excel'),
                    cls:'Sfr_BtnGridPaleBlue',
                    overCls:'Sfr_BtnGridPaleBlue-Hover',
                    handler: function() {
                        Ext.MessageBox.show({
                            msg: 'Please wait...',
                            progressText: 'Exporting...',
                            width: 300,
                            wait: true,
                            waitConfig: {
                                interval: 200
                            },
                            icon: 'ext-mb-info', //custom class in msg-box.html
                            animateTarget: 'mb9'
                        });

                        Ext.Ajax.request({
                            url: m_api + '/data_adm/polygon_over/polygon_compare_export_excel',
                            method: 'POST',
                            waitMsg: lang('Please Wait'),
                            success: function(data) {
                                Ext.MessageBox.hide();
                                if(!testJSON(data.responseText)){
                                    Ext.MessageBox.show({
                                        title: 'Failed',
                                        msg: 'Connection Failed',
                                        buttons: Ext.MessageBox.OK,
                                        animateTarget: 'mb9',
                                        icon: 'ext-mb-error'
                                    });
                                    return false;
                                }

                                var jsonResp = JSON.parse(data.responseText);
                                window.location = jsonResp.filenya;
                            },
                            failure: function() {
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
                text: lang('Supplier ID'),
                dataIndex: 'SupplierID',
                width:'5%'
            },{
                text: lang('ID Compare'),
                dataIndex: 'ID',
                width:'8%'
            },{
                text: lang('Name Compare'),
                dataIndex: 'Name',
                width:'12%'
            },{
                text: lang('FarmNr Compare'),
                dataIndex: 'FarmNr',
                width:'7%'
            },{
                text: lang('Revision Compare'),
                dataIndex: 'Revision',
                width:'8%'
            },{
                text: lang('Status Compare'),
                dataIndex: 'StatusCheck',
                width:'10%'
            },{
                text: lang('Supplier ID Overlap'),
                dataIndex: 'SupplierIDOver',
                width:'5%'
            },{
                text: lang('ID Overlap'),
                dataIndex: 'IDOver',
                width:'8%'
            },{
                text: lang('Name Overlap'),
                dataIndex: 'NameOver',
                width:'12%'
            },{
                text: lang('FarmNr Overlap'),
                dataIndex: 'FarmNrOver',
                width:'7%'
            },{
                text: lang('Revision Overlap'),
                dataIndex: 'RevisionOver',
                width:'8%'
            },{
                text: lang('Status Overlap'),
                dataIndex: 'StatusCheckOver',
                width:'9%'
            }]
        }];

        //buttons -------------------------------------------------------------- (begin)
        thisObj.buttons = [{
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