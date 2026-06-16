/*
* @Author: nikolius
* @Date:   2017-04-06 18:04:26
* @Last Modified by:   nikolius
* @Last Modified time: 2017-04-21 11:04:40
*/
//variabel yg diperlukan
var contextMenuGrid = Ext.create('Koltiva.view.DataAdm.OffData.ContextMenuGridDistrict');

Ext.define('Koltiva.view.DataAdm.OffData.DistrictGrid' ,{
    extend: 'Ext.grid.Panel',
    id: 'mainGridDistrict',
    width: '99.5%',
    style: 'border:1px solid #CCC;margin:25px 5px 5px 5px;',
    renderTo: 'ext-content',
    loadMask: true,
    title: lang('Initial Data by District'),
    selType: 'rowmodel',
    initComponent: function() {
        var objMainGridDistrict = this;

        //store grid
        objMainGridDistrict.store = Ext.create('Koltiva.store.DataAdm.OffData.MainListDistrict');

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
        dataIndex: 'District',
        width: '50%'
    },{
        header: lang('Province'),
        sortable: true,
        dataIndex: 'Province'
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
            //console.log(sm);

            if(sm.Query_Available == lang('Yes')){
                contextMenuGrid.setRowData(sm);
                contextMenuGrid.showAt(e.getXY());

                if(sm.File_Available == lang('No')){
                    Ext.getCmp('Koltiva-view-DataAdm-OffData-ContextMenuGridDistrict-download').setVisible(false);
                }else{
                    Ext.getCmp('Koltiva-view-DataAdm-OffData-ContextMenuGridDistrict-download').setVisible(true);
                }
            }
        }
    }
});