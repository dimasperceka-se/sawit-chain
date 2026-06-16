/*
* @Author: nikolius
* @Date:   2017-10-11 14:53:17
* @Last Modified by:   nikolius
* @Last Modified time: 2017-10-11 15:52:49
*/

Ext.define('Koltiva.view.DataAdm.AdcMember.PanelMemberNotAssignYet' ,{
    extend: 'Ext.panel.Panel',
    frame: true,
    id: 'Koltiva.view.DataAdm.AdcMember.PanelMemberNotAssignYet',
    title: lang('Member Not Assign Yet'),
    style: 'padding:10px;',
    viewVar: false,
    setViewVar: function(value){
        this.viewVar = value;
    },
    initComponent: function() {
        var thisObj = this;

        var storeGridMemberNotAssignYet = Ext.create('Koltiva.store.DataAdm.AdcMember.GridMemberNotAssignYet');

        thisObj.items = [{
            layout: 'column',
            border: false,
            items:[{
                columnWidth: 1,
                layout: 'form',
                style:'padding:10px;',
                items:[{
                    xtype: 'grid',
                    id: 'Koltiva.view.DataAdm.AdcMember.PanelMemberNotAssignYet-gridMemberNotAssignYet',
                    style: 'border:1px solid #CCC;margin-top:4px;',
                    loadMask: true,
                    selType: 'checkboxmodel',
                    store: storeGridMemberNotAssignYet,
                    viewConfig: {
                        deferEmptyText: false,
                        emptyText: lang('No data Available')
                    },
                    dockedItems: [{
                        xtype: 'pagingtoolbar',
                        store: storeGridMemberNotAssignYet,
                        dock: 'bottom',
                        displayInfo: true
                    }],
                    columns: [{
                        text: lang('ID'),
                        dataIndex: 'id',
                        flex: 1
                    },{
                        text: lang('Name'),
                        dataIndex: 'Name',
                        flex: 4
                    },{
                        text: lang('Province'),
                        dataIndex: 'Province',
                        flex: 2
                    },{
                        text: lang('District'),
                        dataIndex: 'District',
                        flex: 2
                    },{
                        text: lang('Kecamatan'),
                        dataIndex: 'Kecamatan',
                        flex: 2
                    },{
                        text: lang('Desa'),
                        dataIndex: 'Desa',
                        flex: 2
                    },{
                        text: lang('Member Role'),
                        dataIndex: 'MemberType',
                        flex: 1
                    }]
                }]
            }]
        }];

        this.callParent(arguments);
    }
});