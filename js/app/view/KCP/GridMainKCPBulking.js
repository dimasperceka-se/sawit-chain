/*
* @Author: fashah
* @Date:   2021-04-30 15:24:46
* @Last Modified by:   fashah
* @Last Modified time: 2021-04-30 18:44:18
*/

/*
    Param2 yg diperlukan ketika load View ini
    1. ....
*/

var yourApp = {
    'showLoading' : function() {
        Ext.MessageBox.show({
            msg: 'Loading, please wait...',
            progressText: 'Loading...',
            width:300,
            wait:true,
            waitConfig: {interval:200},
            icon:'ext-mb-download', //custom class in msg-box.html
            iconHeight: 50,
            animateTarget: 'mb7'
        });
    },
    'hideLoading' : function() {
        Ext.MessageBox.hide();
    }
};

// Define Variabel2 / Object2 / Fungsi yg diperlukan oleh view ini (begin)
var contextMenuGridMill = Ext.create('Ext.menu.Menu',{
    items:[{
        icon: varjs.config.base_url + 'images/icons/new/view.png',
        text: lang('View'),
        handler: function() {

            var sm = Ext.getCmp('Koltiva.view.KCP.GridMainKCPBulking-gridMainGrid').getSelectionModel().getSelection()[0];

            Ext.getCmp('Koltiva.view.KCP.GridMainKCPBulking').destroy(); //destory current view
            //create object View untuk FormMainMill
            if(Ext.getCmp('Koltiva.view.KCP.GridMainKCPBulking') == undefined){
                var FormMainMill = Ext.create('Koltiva.view.KCP.FormMainKCPBulk', {
                    opsiDisplay: 'view',
                    viewVar: {
                        KCPID: sm.get('id')
                    }
                });
            }else{
                //destroy, create ulang
                Ext.getCmp('Koltiva.view.KCP.GridMainKCPBulking').destroy();
                var FormMainMill = Ext.create('Koltiva.view.KCP.FormMainKCPBulk', {
                    opsiDisplay: 'view',
                    viewVar: {
                        KCPID: sm.get('id')
                    }
                });
            }

        }
    },{
        icon: varjs.config.base_url + 'images/icons/new/update.png',
        text: lang('Update'),
        hidden: m_act_update,
        handler: function() {

            var sm = Ext.getCmp('Koltiva.view.KCP.GridMainKCPBulking-gridMainGrid').getSelectionModel().getSelection()[0];

            Ext.getCmp('Koltiva.view.KCP.GridMainKCPBulking').destroy(); //destory current view
            //create object View untuk FormMainMill
            if(Ext.getCmp('Koltiva.view.KCP.GridMainKCPBulking') == undefined){
                var FormMainMill = Ext.create('Koltiva.view.KCP.FormMainKCPBulk', {
                    opsiDisplay: 'update',
                    viewVar: {
                        KCPID: sm.get('id')
                    }
                });
            }else{
                //destroy, create ulang
                Ext.getCmp('Koltiva.view.KCP.GridMainKCPBulking').destroy();
                var FormMainMill = Ext.create('Koltiva.view.KCP.FormMainKCPBulk', {
                    opsiDisplay: 'update',
                    viewVar: {
                        KCPID: sm.get('id')
                    }
                });
            }

        }
    },{
        icon: varjs.config.base_url + 'images/icons/new/delete.png',
        text: lang('Delete'),
        hidden: m_act_delete,
        handler: function(){
            var sm = Ext.getCmp('Koltiva.view.KCP.GridMainKCPBulking-gridMainGrid').getSelectionModel().getSelection()[0];

            Ext.MessageBox.confirm('Message', 'Do you want to delete this data ?', function(btn) {
                if (btn == 'yes') {
                    Ext.Ajax.request({
                        waitMsg: 'Please Wait',
                        url: m_api + '/kcp_bulk/data',
                        method: 'DELETE',
                        params: {
                            KCPID: sm.get('id')
                        },
                        success: function(response, opts) {
                            Ext.MessageBox.show({
                                title: 'Information',
                                msg: lang('Data deleted'),
                                buttons: Ext.MessageBox.OK,
                                animateTarget: 'mb9',
                                icon: 'ext-mb-success'
                            });

                            //refresh store
                            setFilterLs();
                            Ext.getCmp('Koltiva.view.KCP.GridMainKCPBulking-gridMainGrid').getStore().load();
                        },
                        failure: function(response, opts) {
                            var pesanNya;
                            if(o.result.message != undefined){
                                pesanNya = o.result.message;
                            }else{
                                pesanNya = lang('Connection error');
                            }
                            Ext.MessageBox.show({
                                title: 'Error',
                                msg: pesanNya,
                                buttons: Ext.MessageBox.OK,
                                animateTarget: 'mb9',
                                icon: 'ext-mb-error'
                            });
                        }
                    });
                }
            });

        }
    }]
});

function setFilterLs(){
    localStorage.setItem('patchouli_mill_ls',
        JSON.stringify({
            ptextSearch: Ext.getCmp('Koltiva.view.KCP.GridMainKCPBulking-textSearch').getValue()
        })
    );
}

function submitOnEnterGridMill(field, event){
    if (event.getKey() == event.ENTER) {
        setFilterLs();
        Ext.getCmp('Koltiva.view.KCP.GridMainKCPBulking-gridMainGrid').getStore().loadPage(1);
    }
}
// Define Variabel2 / Object2 / Fungsi yg diperlukan oleh view ini (end)

Ext.define('Koltiva.view.KCP.GridMainKCPBulking' ,{
    extend: 'Ext.panel.Panel',
    id: 'Koltiva.view.KCP.GridMainKCPBulking',
    renderTo: 'ext-content',
    requires: [
        'Ext.data.*',
        'Ext.grid.*',
    ],   
    listeners: {
        afterRender: function(){
            //hilangkan view Filter region
            document.getElementById('divCommonContentRegion').style.display = 'block';
            //load storenya sebelum viewnya aktif
            setFilterLs();
            Ext.getCmp('Koltiva.view.KCP.GridMainKCPBulking-gridMainGrid').getStore().load();
        }
    },
    style:'padding:0 15px 15px 15px;margin:5px 0 0 0;',
    initComponent: function() {
        var thisObj = this;
        
        //store
        var storeGridMain = Ext.create('Koltiva.store.KCP.GridMain');

        thisObj.items = [{
            layout: 'column',
            border: false,
            items: [{
                columnWidth: .3,
                layout: 'form',
                items:[{
                    xtype: 'button',
                    id: 'Koltiva.view.KCP.GridMainKCPBulking-btnListView',
                    style:'margin-top:5px;',
                    text: lang('All Mills'),
                    arrowAlign: 'right',
                    hidden: true,
                    menu : [{
                        text: lang('All Mills'),
                        listeners: {
                            click: function(){
                                alert('All Mills Click')
                            }
                        }
                    },{
                        text: lang('New This Week'),
                        listeners: {
                            click: function(){
                                alert('New This Week Click')
                            }
                        }
                    },{
                        text: lang('Modified This Week'),
                        listeners: {
                            click: function(){
                                alert('Modified This Week Click')
                            }
                        }
                    },{
                        text: lang('Recently Views'),
                        listeners: {
                            click: function(){
                                alert('Recently Views Click')
                            }
                        }
                    }]
                }]
            },{
                columnWidth: .7,
                xtype: 'panel',
                frame: false,
                id: 'Koltiva.view.KCP.GridMainKCPBulking-gridInformation',
                html: ''
            }]
        },{
            xtype: 'grid',
            id: 'Koltiva.view.KCP.GridMainKCPBulking-gridMainGrid',
            style: 'border:1px solid #CCC;margin-top:4px;',
            loadMask: true,
            selType: 'rowmodel',
            store: storeGridMain,
            viewConfig: {
                deferEmptyText: false,
                emptyText: GetDefaultContentNoData()
            },
            dockedItems: [{
                xtype: 'pagingtoolbar',
                id: 'Koltiva.view.KCP.GridMainKCPBulking-gridToolbar',
                store: storeGridMain,
                dock: 'bottom',
                displayInfo: true
            },{
                xtype: 'toolbar',
                dock:'top',
                items: [{
                    icon: varjs.config.base_url + 'images/icons/new/add.png', cls:'Sfr_BtnGridGreen', overCls:'Sfr_BtnGridGreen-Hover',
                    text: lang('Add'),
                    hidden: m_act_add,
                    handler: function() {
                        Ext.getCmp('Koltiva.view.KCP.GridMainKCPBulking').destroy(); //destory current view

                        //create object View untuk FormMainMill
                        if(Ext.getCmp('Koltiva.view.KCP.FormMainKCPBulk') == undefined){
                            var FormMainKCPBulk = Ext.create('Koltiva.view.KCP.FormMainKCPBulk', {
                                opsiDisplay: 'insert'
                            });
                        }else{
                            //destroy, create ulang
                            Ext.getCmp('Koltiva.view.KCP.FormMainKCPBulk').destroy();
                            var FormMainKCPBulk = Ext.create('Koltiva.view.KCP.FormMainKCPBulk', {
                                opsiDisplay: 'insert'
                            });
                        }
                    }
                },{
                    name: 'key', baseCls:'Sfr_TxtfieldSearchGrid',
                    id: 'Koltiva.view.KCP.GridMainKCPBulking-textSearch',
                    xtype: 'textfield',
                    width: 400,
                    emptyText: lang('Cari berdasar nama/ID')+', '+lang('Press \'Enter\' to search'),
                    listeners: {
                        specialkey: submitOnEnterGridMill
                    }
                },{
                    xtype:'tbspacer',
                    flex:1
                },{
                    icon: varjs.config.base_url + 'images/icons/new/reload.png', cls:'Sfr_BtnGridBlue', overCls:'Sfr_BtnGridBlue-Hover',
                    tooltip: lang('Reload'),
                    handler: function() {
                        //reload
                        setFilterLs();
                        Ext.getCmp('Koltiva.view.KCP.GridMainKCPBulking-gridMainGrid').getStore().loadPage(1);
                    }
                }]
            }],
            columns: [{
                text: lang('Action'),
                xtype:'actioncolumn',
                width:70,
                items:[{
                    icon: varjs.config.base_url + 'images/icons/new/action.png',
                    tooltip: 'Action',
                    handler: function(grid, rowIndex, colIndex, item, e, record, row, action) {
                        var sm = record.data.SetAsPartner;
                        contextMenuGridMill.showAt(e.getXY());
                    }
                }]
            },{
                text: lang('ID'),
                dataIndex: 'id',
                hidden:true
            },{
                id: 'Koltiva.view.KCP.GridMainKCPBulking-colid',
                text: lang('ID'),
                dataIndex: 'KCPDisplayID',
                width: '7%'
            }, {
                id: 'Koltiva.view.KCP.GridMainKCPBulking-colName',
                text: lang('Name'),
                flex:1,
                dataIndex: 'Name'
            },{
                id: 'Koltiva.view.KCP.GridMainKCPBulking-colCompanyName',
                text: lang('Company Name'),
                flex:1,
                dataIndex: 'CompanyName'
            }, {
                id: 'Koltiva.view.KCP.GridMainKCPBulking-colMillGPS',
                text: lang('Coordinate'),
                flex:1,
                dataIndex: 'GPS'
            },{
                id: 'Koltiva.view.KCP.GridMainKCPBulking-colAlias',
                text: lang('Alias'),
                flex:1,
                dataIndex: 'Alias',
                hidden: true
            },{
                id: 'Koltiva.view.KCP.GridMainKCPBulking-colAddress',
                text: lang('Address'),
                flex:1,
                dataIndex: 'Address',
                hidden: true
            },{
                id: 'Koltiva.view.KCP.GridMainKCPBulking-colProvince',
                text: lang('Province'),
                flex:1,
                dataIndex: 'Province',
                hidden: true
            },{
                id: 'Koltiva.view.KCP.GridMainKCPBulking-colDistrict',
                text: lang('District'),
                flex:1,
                dataIndex: 'District',
                hidden: true
            },{
                id: 'Koltiva.view.KCP.GridMainKCPBulking-colKecamatan',
                text: lang('Subdistrict'),
                flex:1,
                dataIndex: 'Kecamatan'
            },{
                id: 'Koltiva.view.KCP.GridMainKCPBulking-colDesa',
                text: lang('Village'),
                flex:1,
                dataIndex: 'Desa'
            },{
                id: 'Koltiva.view.KCP.GridMainKCPBulking-colTahunTerbentuk',
                text: lang('Year Established'),
                flex:1,
                dataIndex: 'TahunTerbentuk'
            },{
                id: 'Koltiva.view.KCP.GridMainKCPBulking-colPhone',
                text: lang('Phone'),
                flex:1,
                dataIndex: 'Phone',
                hidden: true
            },{
                id: 'Koltiva.view.KCP.GridMainKCPBulking-colLastUpdated',
                text: lang('Last Updated'),
                flex:1,
                dataIndex: 'LastUpdated'
            }]
        }];

        this.callParent(arguments);
    }
});