/*
* @Author: nikolius
* @Date:   2017-10-11 18:18:08
* @Last Modified by:   nikolius
* @Last Modified time: 2017-10-11 18:20:07
*/

Ext.define('Koltiva.view.DataAdm.AdcMill.PanelMillNotAssignYet' ,{
    extend: 'Ext.panel.Panel',
    frame: true,
    id: 'Koltiva.view.DataAdm.AdcMill.PanelMillNotAssignYet',
    title: lang('Mill Not Assign Yet'),
    style: 'padding:10px;',
    viewVar: false,
    setViewVar: function(value){
        this.viewVar = value;
    },
    initComponent: function() {
        var thisObj = this;

        var storeGridMillNotAssignYet = Ext.create('Koltiva.store.DataAdm.AdcMill.GridMillNotAssignYet');

        thisObj.items = [{
            layout: 'column',
            border: false,
            items:[{
                columnWidth: 1,
                layout: 'form',
                style:'padding:10px;',
                items:[{
                    xtype: 'grid',
                    id: 'Koltiva.view.DataAdm.AdcMill.PanelMillNotAssignYet-gridMillNotAssignYet',
                    style: 'border:1px solid #CCC;margin-top:4px;',
                    loadMask: true,
                    selType: 'checkboxmodel',
                    store: storeGridMillNotAssignYet,
                    viewConfig: {
                        deferEmptyText: false,
                        emptyText: lang('No data Available')
                    },
                    dockedItems: [{
                        xtype: 'pagingtoolbar',
                        store: storeGridMillNotAssignYet,
                        dock: 'bottom',
                        displayInfo: true
                    }],
                    columns: [{
                        text: lang('ID'),
                        dataIndex: 'id',
                        width:'10%'
                    },{
                        text: lang('Name'),
                        dataIndex: 'Name',
                        width:'40%'
                    },{
                        text: lang('Kecamatan'),
                        dataIndex: 'Kecamatan',
                        width:'22%'
                    },{
                        text: lang('Desa'),
                        dataIndex: 'Desa',
                        width:'22%'
                    }]
                }]
            }]
        }];

        this.callParent(arguments);
    }
});