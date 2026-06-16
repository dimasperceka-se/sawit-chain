
/*
 * @Author: Komarudin
 * @Date: 2018-05-23
 * @Last Modified by: komarudin
 * @Last Modified time: 2018-05-25 11:02:48
 */
var storeGridMain = Ext.create('Koltiva.store.Traceability.Transaction.MainGridFarmer');
Ext.define('Koltiva.view.Traceability.Transaction.DataFarmer' ,{
    extend: 'Ext.window.Window',
    id: 'Koltiva.view.Traceability.Transaction.DataFarmer',
    title: lang('Data Farmer'),
    closable: true,
    modal: true,
    closeAction: 'destroy',
    width: '85%',
    height: '50%',
    overflowY: 'auto',
    viewVar: false,
    setViewVar: function(value){
        this.viewVar = value;
    },
    initComponent: function() {
        var thisObj = this;

        //items
        thisObj.items = [{
            xtype: 'grid',
            id: 'view.FarmerGrid-gridMainGrid',
            style: 'border:1px solid #CCC;margin-top:4px;',
            loadMask: true,
            selType: 'rowmodel',
            store: storeGridMain,
            viewConfig: {
                deferEmptyText: false,
                emptyText: lang('No data Available')
            },
            dockedItems: [{
                xtype: 'pagingtoolbar',
                id: 'view.CountryGrid-gridToolbar',
                store: storeGridMain,
                dock: 'bottom',
                displayInfo: true
            }],
            columns: [{
                text: ' Farmer ID',
                dataIndex: 'FarmerID',
                hidden:true
            },
            {
                text: 'No',
                xtype: 'rownumberer',
                width:'5%'
            },
            {   text : 'Date Transaction',
                dataIndex: 'DateTransaction',
                width: '15%',
                xtype: 'datecolumn',
                format: 'd-m-Y',
            },
            {
                text: 'Name of Farmer',
                width: '15%',
                dataIndex: 'FarmerName',
            },
            {
                text: 'Garden Number',
                width: '10%',
                dataIndex: 'PlotNr',
            },
            {
                text: 'Garden Location',
                width: '15%',
                dataIndex: 'Village',
            },
            {
                text: 'Bruto',
                width: '10%',
                xtype: 'numbercolumn',
                format: '0,000',
                dataIndex: 'VolumeBruto',
            },
            {
                text: 'Netto',
                width: '10%',
                xtype: 'numbercolumn',
                format: '0,000',
                dataIndex: 'VolumeNetto',
            },
            {
                text: 'BJR',
                width: '10%',
                xtype: 'numbercolumn',
                format: '0,000',
                dataIndex: 'Bjr',
            },
            {
                text: 'Tandan',
                width: '10%',
                xtype: 'numbercolumn',
                format: '0,000',
                dataIndex: 'Tandan',
            },
        
        ]
        }];

        this.callParent(arguments);
    }
});
