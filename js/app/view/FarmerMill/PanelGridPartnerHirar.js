/******************************************
 *  Author : n1colius.lau@gmail.com   
 *  Created On : Fri Aug 16 2019
 *  File : PanelGridPartnerHirar.js
 *******************************************/
/*
    Param2 yg diperlukan ketika load View ini
    - PartnerID
*/

Ext.define('Koltiva.view.FarmerMill.PanelGridPartnerHirar' ,{
    extend: 'Ext.tree.Panel',
    id: 'Koltiva.view.FarmerMill.PanelGridPartnerHirar',
    requires: [
        'Ext.data.*',
        'Ext.grid.*',
        'Ext.tree.*'
    ],
    xtype: 'tree-grid',
    //title:lang('List of IMS Master Documents'),
    style:'border:1px solid #CCC;',
    useArrows: true,
    rootVisible: false,
    multiSelect: false,
    singleExpand: false,
    selType: 'checkboxmodel',
    width: '100%',
    viewConfig: {
        deferEmptyText: false,
        emptyText: lang('No Data Available')
    },
    viewVar: false,
    setViewVar: function(value){
        this.viewVar = value;
    },
    listeners: {
        afterRender: function(){
            var thisObj = this;
        }
    },
    initComponent: function() {
        var thisObj = this;

        //Main Store
        thisObj.store = Ext.create('Koltiva.store.FarmerMill.GridPartnerHirar',{
        	storeVar: {
                PartnerID: thisObj.viewVar.PartnerID
            }
        });

        thisObj.columns = [{
            HeaderCheckbox: true,
            dataIndex : 'CheckData',
            width:50
        },{
            text: lang('ID'),
            dataIndex: 'PartnerID',
            hidden:true
        },{
            xtype: 'treecolumn',
            text: lang('Partner'),
            flex: 2,
            sortable: true,
            dataIndex: 'PartnerName'
        }];

        this.callParent(arguments);
    }
});