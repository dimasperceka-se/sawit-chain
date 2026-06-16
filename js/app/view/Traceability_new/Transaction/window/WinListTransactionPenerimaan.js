  

Ext.define('Koltiva.view.Traceability_new.Transaction.window.WinListTransactionPenerimaan' ,{ 
    extend: 'Ext.window.Window',
    id: 'Koltiva.view.Traceability_new.Transaction.window.WinListTransactionPenerimaan',
    title: lang('Transaction Detail'),
    closable: true,
    modal: true,
    closeAction: 'destroy',
    width: '70%',
    height: '60%',
    overflowY: 'auto',
    viewVar: false,
    setViewVar: function(value){
        this.viewVar = value;
    },
    initComponent: function() {
        var thisObj = this;

        var SupplychainID = m_sid;
        var SupplyBatchID = thisObj.viewVar.SupplyBatchID;

        //alert(SupplyBatchID);
        
        //store --------------------------------------------------------------------------------------------------------------- (begin)
        //var storeWindowTransactionPengiriman = Ext.create('Koltiva.store.Traceability_new.Transaction.window.storeWindowTransactionPengiriman');
        var MainGridTransactionDetail = Ext.create('Koltiva.store.Traceability_new.Transaction.StoreGridDetailTransaction');
        MainGridTransactionDetail.proxy.extraParams.SID = m_sid;
        MainGridTransactionDetail.proxy.extraParams.SBID = SupplyBatchID;
        MainGridTransactionDetail.load();
        

        //store --------------------------------------------------------------------------------------------------------------- (end)

        //items --------------------------------------------------------------------------------------------------------------- (begin)
        thisObj.items = [{
                            xtype: 'grid',
                            id: 'Koltiva.view.Traceability_new.Transaction.window.WinListTransactionPenerimaan-grid',
                            style: 'border:1px solid #CCC;margin-top:4px;',
                            loadMask: true,
                            selType: 'rowmodel',
                            store: MainGridTransactionDetail,
                            viewConfig: {
                                deferEmptyText: false,
                                emptyText: lang('No data Available')
                            },
                            dockedItems: [{
                                xtype: 'pagingtoolbar',
                                id: 'Koltiva.view.Traceability_new.Transaction.window.WinListTransactionPenerimaan-gridToolbar',
                                store: MainGridTransactionDetail,
                                dock: 'bottom',
                                displayInfo: true
                            },{
                                xtype: 'toolbar',
                                dock:'top',
                                items: [{
                                    name: 'SupplyKey',
                                    id: 'Koltiva.view.Traceability_new.Transaction.window.WinListTransactionPenerimaan-gridToolbar-SupplyKey',
                                    xtype: 'textfield',
                                    width: 150,
                                    hidden:true,
                                    emptyText: lang('Search by Name / ID')
                                },{
                                    xtype: 'button',
                                    hidden:true,
                                    id: 'Koltiva.view.Traceability_new.Transaction.window.WinListTransactionPenerimaan-gridToolbar-BtnSearch',
                                    icon: varjs.config.base_url + 'images/icons/silk/search.png',
                                    margin: '0px 10px 0px 6px',
                                    text: lang('Search'),
                                    handler: function() {
                                        MainGridTransactionDetail.load()
                                    }
                                },{
                                    xtype: 'button',
                                    id: 'Koltiva.view.Traceability_new.Transaction.window.WinListTransactionPenerimaan-gridToolbar-BtnExport',
                                    icon: varjs.config.base_url + 'images/icons/new/export.png',
                                    margin: '0px 10px 0px 6px',
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
                                        Ext.Ajax.request({
                                            url: m_api + '/traceability_api/web_penerimaan/export_detail_transaction',
                                            method: 'GET',
                                            waitMsg: lang('Export data...'),
                                            params: { 
                                                SID: m_sid,
                                                SBID: SupplyBatchID,
                                            },
                                            success: function(data) {
                                                Ext.MessageBox.hide();
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
                            columns: [
							{
                                text: 'ID',
                                dataIndex: 'SupplyTransID',
                                hidden: true
                            },
							{
                                text: 'ID',
                                dataIndex: 'SupplyID',
                                hidden: true
                            },
                            {
                                text: 'ID',
                                dataIndex: 'SupplyBatchID',
                                hidden: true
                            },{
                                text: lang('Date'),
                                dataIndex: 'DateTransaction',
                                width:150,
                            },
                            {
                                text: lang('Farmer ID'),
                                dataIndex: 'MemberDisplayID',
                                flex:1
                            },
                            {
                                text: lang('Name'),
                                dataIndex: 'SupplierName',
                                flex:1
                            },
                            {
                                text: lang('Type'),
                                dataIndex: 'SupplyType',
                                width:150,
                            },
                           {
                                text: lang('Gross'),
                                dataIndex: 'VolumeBruto',
                                width:125
                            }, {
                                text: lang('Netto'),
                                dataIndex: 'VolumeNetto',
                                width:125
                            } ]  
            }];
        //items --------------------------------------------------------------------------------------------------------------- (end)

        //buttons --------------------------------------------------------------------------------------------------------------- (begin)
        thisObj.buttons = [ {
            text: lang('Close'),
            margin: '5px',
            scale: 'large',
            ui: 's-button',
            cls: 's-grey',
            handler: function() {
                thisObj.close();
            }
        }];
        //buttons --------------------------------------------------------------------------------------------------------------- (end)

        this.callParent(arguments);
    },
    listeners: {
        afterRender: function(){
            var thisObj = this; 

        }
    }
});

  