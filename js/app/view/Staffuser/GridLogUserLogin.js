/******************************************
 *  Author : n1colius.lau@gmail.com
 *  Created On : Wed May 27 2020
 *  File : GridLogUserLogin.js
 *******************************************/
/*
    Param2 yg diperlukan ketika load View ini
    - PersonID
*/

Ext.define('Koltiva.view.Staffuser.GridLogUserLogin' ,{
    extend: 'Ext.panel.Panel',
    id: 'Koltiva.view.Staffuser.GridLogUserLogin',
    style:'margin-top:15px;',
    title:lang('User Login Log (Last 10 logs)'),
    frame: true,
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

        thisObj.MainGrid = Ext.create('Koltiva.store.Staffuser.GridLogUserLogin',{
        	storeVar: {
                PersonID: thisObj.viewVar.PersonID
            }
        });

        thisObj.items = [{
        	xtype:'grid',
            id: 'Koltiva.view.Staffuser.GridLogUserLogin-MainGrid',
            cls:'Sfr_GridNew',
            loadMask: true,
            selType: 'rowmodel',
            store: thisObj.MainGrid,
            viewConfig: {
                deferEmptyText: false,
                emptyText: GetDefaultContentNoData()
            },
            columns: [{
                text: lang('Type'),
                dataIndex: 'Type',
                width: '20%'
            },{
                text: lang('IP Address'),
                dataIndex: 'IPAddress',
                width: '20%'
            },{
                text: lang('Timestamp'),
                dataIndex: 'Timestamp',
                width: '20%'
            },{
                text: lang('Remark'),
                dataIndex: 'Remark',
                width: '39%',
                //lanjut sini
            }]
        }];

        this.callParent(arguments);
    }
});