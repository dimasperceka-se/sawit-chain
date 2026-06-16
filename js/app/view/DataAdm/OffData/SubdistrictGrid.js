/*
* @Author: nikolius
* @Date:   2017-04-07 15:36:51
* @Last Modified by:   nikolius
* @Last Modified time: 2017-05-18 19:30:44
*/
var contextMenuGridSub = Ext.create('Koltiva.view.DataAdm.OffData.ContextMenuGridSubdistrict');

Ext.define('Koltiva.view.DataAdm.OffData.SubdistrictGrid' ,{
    extend: 'Ext.grid.Panel',
    id: 'mainGridSubdistrict',
    width: '99.5%',
    style: 'border:1px solid #CCC;margin:25px 5px 5px 5px;',
    renderTo: 'ext-content',
    loadMask: true,
    title: lang('Initial Data by Subdistrict'),
    selType: 'rowmodel',
    initComponent: function() {
        var objMainGridSubdistrict = this;

        //store grid
        objMainGridSubdistrict.store = Ext.create('Koltiva.store.DataAdm.OffData.MainListSubdistrict');

        this.callParent(arguments);
    },
    features: [{
        ftype: 'groupingsummary',
        groupHeaderTpl: '{name}',
        hideGroupedHeader: true,
        enableGroupingMenu: true,
        showSummaryRow: false,
        startCollapsed: true
    }],
    columns: [{
        text: lang('Region'),
        dataIndex: 'Subdistrict',
        width: '50%'
    },{
        header: lang('District'),
        sortable: true,
        dataIndex: 'DistrictLabel'
    },{
        text: lang('Query Available'),
        dataIndex: 'Query_Available',
        width: '25%',
        renderer: function(value, metaData, record, rowIndex, colIndex, store, view) {
            if(value == lang('Ya')){
                metaData.tdAttr = 'style="color:green;font-weight:bold;"';
            }else{
                metaData.tdAttr = 'style="color:red;font-weight:bold;"';
            }
            return value;
        }
    },{
        text: lang('File Available'),
        dataIndex: 'File_Available',
        width: '24.5%',
        renderer: function(value, metaData, record, rowIndex, colIndex, store, view) {
            if(value == lang('Ya')){
                metaData.tdAttr = 'style="color:green;font-weight:bold;"';
            }else{
                metaData.tdAttr = 'style="color:red;font-weight:bold;"';
            }
            return value;
        }
    }],
    listeners: {
        itemclick: function(view, record, item, index, e){
            var sm = record.data;

            if(sm.Query_Available == lang('Yes')){
                contextMenuGridSub.setRowData(sm);
                contextMenuGridSub.showAt(e.getXY());

                if(sm.File_Available == lang('No')){
                    Ext.getCmp('Koltiva-view-DataAdm-OffData-ContextMenuGridSubdistrict-download').setVisible(false);
                }else{
                    Ext.getCmp('Koltiva-view-DataAdm-OffData-ContextMenuGridSubdistrict-download').setVisible(true);
                }
            }
        }
    }
});