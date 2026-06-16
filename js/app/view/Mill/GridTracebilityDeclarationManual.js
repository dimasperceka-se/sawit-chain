/*
* @Author: nikolius
* @Date:   2017-08-03 15:24:46
* @Last Modified by:   nikolius
* @Last Modified time: 2017-08-21 18:44:18
*/

/*
    Param2 yg diperlukan ketika load View ini
    1. ....
*/

// Define Variabel2 / Object2 / Fungsi yg diperlukan oleh view ini (begin)
    var contextMenuGridMill = Ext.create('Ext.menu.Menu',{
        items:[{
            icon: varjs.config.base_url + 'images/icons/new/view.png',
            text: lang('View'),
            handler: function() {
                var sm = Ext.getCmp('Koltiva.view.Mill.GridTracebilityDeclarationManual-gridMainGrid').getSelectionModel().getSelection()[0];

                Ext.getCmp('Koltiva.view.Mill.GridTracebilityDeclarationManual').destroy(); //destory current view
                //create object View untuk FormMainMill
                if(Ext.getCmp('Koltiva.view.Mill.FormGenerateTracebilityDeclarationManual') == undefined){
                    var FormMainMill = Ext.create('Koltiva.view.Mill.FormGenerateTracebilityDeclarationManual', {
                        opsiDisplay: 'view',
                        MillID: sm.get('MillID'),
                        MillTCDID : sm.get('MillTCDID')
                    });
                }else{
                    //destroy, create ulang
                    Ext.getCmp('Koltiva.view.Mill.FormGenerateTracebilityDeclarationManual').destroy();
                    var FormMainMill = Ext.create('Koltiva.view.Mill.FormGenerateTracebilityDeclarationManual', {
                        opsiDisplay: 'view',
                        MillID: sm.get('MillID'),
                        MillTCDID : sm.get('MillTCDID')
                    });
                }

            }
        },{
            icon: varjs.config.base_url + 'images/icons/new/update.png',
            text: lang('Update'),
            hidden: m_act_update,
            handler: function() {
                var sm = Ext.getCmp('Koltiva.view.Mill.GridTracebilityDeclarationManual-gridMainGrid').getSelectionModel().getSelection()[0];

                Ext.getCmp('Koltiva.view.Mill.GridTracebilityDeclarationManual').destroy(); //destory current view

                //create object View untuk FormMainMill
                if(Ext.getCmp('Koltiva.view.Mill.FormTracebilityDeclarationManual') == undefined){
                    var FormTracebilityDeclarationManual = Ext.create('Koltiva.view.Mill.FormTracebilityDeclarationManual', {
                        opsiDisplay: 'update',
                        MillID: sm.get('MillID'),
                        MillTCDID : sm.get('MillTCDID')
                    });
                }else{
                    //destroy, create ulang
                    Ext.getCmp('Koltiva.view.Mill.FormTracebilityDeclarationManual').destroy();
                    var FormTracebilityDeclarationManual = Ext.create('Koltiva.view.Mill.FormTracebilityDeclarationManual', {
                        opsiDisplay: 'update',
                        MillID: sm.get('MillID'),
                        MillTCDID : sm.get('MillTCDID')
                    });
                }

            }
        },{
            icon: varjs.config.base_url + 'images/icons/new/delete.png',
            text: lang('Delete'),
            hidden: m_act_delete,
            handler: function(){
                var sm = Ext.getCmp('Koltiva.view.Mill.GridTracebilityDeclarationManual-gridMainGrid').getSelectionModel().getSelection()[0];

                Ext.MessageBox.confirm('Message', 'Do you want to delete this data ?', function(btn) {
                    if (btn == 'yes') {
                        Ext.Ajax.request({
                            waitMsg: 'Please Wait',
                            url: m_api + '/mill/form_tc_declaration',
                            method: 'DELETE',
                            params: {
                                MillTCDID : sm.get('MillTCDID')
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
                                Ext.getCmp('Koltiva.view.Mill.GridTracebilityDeclarationManual-gridMainGrid').getStore().load();
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
                MillID : Ext.getCmp('MillIDSearch').getValue(),
                ptextSearch: Ext.getCmp('Koltiva.view.Mill.GridTracebilityDeclarationManual-textSearch').getValue()
            })
        );
    }

    function submitOnEnterGridMill(field, event){
        if (event.getKey() == event.ENTER) {
            setFilterLs();
            Ext.getCmp('Koltiva.view.Mill.GridTracebilityDeclarationManual-gridMainGrid').getStore().loadPage(1);
        }
    }
// Define Variabel2 / Object2 / Fungsi yg diperlukan oleh view ini (end)

Ext.define('Koltiva.view.Mill.GridTracebilityDeclarationManual' ,{
    extend: 'Ext.panel.Panel',
    id: 'Koltiva.view.Mill.GridTracebilityDeclarationManual',
    renderTo: 'ext-content',
    requires: [
        'Ext.data.*',
        'Ext.grid.*',
    ],
    viewVar: false,
    setViewVar: function(value){
        this.viewVar = value;
    },
    listeners: {
        afterRender: function(){
            //hilangkan view Filter region
            document.getElementById('divCommonContentRegion').style.display = 'block';

            //load storenya sebelum viewnya aktif//isikan variabel dari local storage
            var patchouli_mill_ls = JSON.parse(localStorage.getItem('patchouli_mill_ls'));
            if(patchouli_mill_ls != null){
                Ext.getCmp('Koltiva.view.Mill.GridTracebilityDeclarationManual-textSearch').setValue(patchouli_mill_ls.ptextSearch);

                console.log(patchouli_mill_ls.MillID+'sess');
                Ext.getCmp('MillIDSearch').setValue(patchouli_mill_ls.MillID);
            }

            if(typeof this.viewVar.MillID !== 'undefined'){
                Ext.getCmp("MillIDSearch").setValue(this.viewVar.MillID);
            }

            console.log(this.viewVar.MillID);

            setFilterLs();
            Ext.getCmp('Koltiva.view.Mill.GridTracebilityDeclarationManual-gridMainGrid').getStore().load();
        }
    },
    style:'padding:0 15px 15px 15px;margin:5px 0 0 0;',
    initComponent: function() {
        var thisObj = this;
        
        //store
        var storeGridTracebilityDeclarationManual = Ext.create('Koltiva.store.Mill.GridTracebilityDeclarationManual');

        var MillID = this.viewVar.MillID;

        thisObj.items = [{
            xtype: 'panel',
            border:false,
            layout:{
                type:'hbox'
            },
            items:[{
            	id: 'Koltiva.view.Mill.FormTracebilityDeclarationManual-labelInfoTitle',
                html:'<h3 style="margin:0;padding:0px;">'+lang('Tracebility Declaration')+'</h3>'
            },{
                id: 'Koltiva.view.Mill.FormTracebilityDeclarationManual-labelInfo',
                html:'',
            }]
        },{
            html: '<div id="Sfr_IdBoxInfoDataGrid" class="Sfr_BoxInfoDataGrid">' +
                  '<ul class="Sft_UlListInfoDataGrid"><li class="Sft_ListInfoDataGrid">' +
                  '<a><img class="Sft_ListIconInfoDataGrid" src="' + varjs.config.base_url + 'images/icons/new/back.png" width="20" />' +
                  '&nbsp;&nbsp;' + lang('Back to Tracebilitry Mill List')  + '</a></li></ul></div>',
            listeners: {
                click: {
                    element: 'el',
                    preventDefault: true,
                    fn: function(e, target){
                        Ext.getCmp('Koltiva.view.Mill.GridTracebilityDeclarationManual').destroy(); //destory current view
                        var GridMainFarcan = [];

                        if(Ext.getCmp('Koltiva.view.Mill.GridMainMill') == undefined){
                            GridMainFarcan = Ext.create('Koltiva.view.Mill.GridMainMill');
                        }else{
                            //destroy, create ulang
                            Ext.getCmp('Koltiva.view.Mill.GridMainMill').destroy();
                            GridMainFarcan = Ext.create('Koltiva.view.Mill.GridMainMill');
                        }
                    }
                }
            }
        },{
            xtype: 'grid',
            id: 'Koltiva.view.Mill.GridTracebilityDeclarationManual-gridMainGrid',
            style: 'border:1px solid #CCC;margin-top:4px;',
            loadMask: true,
            selType: 'rowmodel',
            store: storeGridTracebilityDeclarationManual,
            viewConfig: {
                deferEmptyText: false,
                emptyText: lang('No data Available'),
            },
            dockedItems: [{
                xtype: 'pagingtoolbar',
                id: 'Koltiva.view.Mill.GridTracebilityDeclarationManual-gridToolbar',
                store: storeGridTracebilityDeclarationManual,
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
                        Ext.getCmp('Koltiva.view.Mill.GridTracebilityDeclarationManual').destroy(); //destory current view

                        //create object View untuk FormMainMill
                        if(Ext.getCmp('Koltiva.view.Mill.FormTracebilityDeclarationManual') == undefined){
                            var FormTracebilityDeclarationManual = Ext.create('Koltiva.view.Mill.FormTracebilityDeclarationManual', {
                                opsiDisplay: 'insert',
                                MillID: MillID
                            });
                        }else{
                            //destroy, create ulang
                            Ext.getCmp('Koltiva.view.Mill.FormTracebilityDeclarationManual').destroy();
                            var FormTracebilityDeclarationManual = Ext.create('Koltiva.view.Mill.FormTracebilityDeclarationManual', {
                                opsiDisplay: 'insert',
                                MillID: MillID
                            });
                        }
                    }
                },{
                    name: 'key', baseCls:'Sfr_TxtfieldSearchGrid',
                    id: 'Koltiva.view.Mill.GridTracebilityDeclarationManual-textSearch',
                    xtype: 'textfield',
                    width: 400,
                    emptyText: lang('Cari berdasar nama')+', '+lang('Press \'Enter\' to search'),
                    listeners: {
                        specialkey: submitOnEnterGridMill
                    }
                },{
                    name: 'MillIDSearch',
                    id: 'MillIDSearch',
                    xtype: 'hiddenfield',
                    width: 400
                },{
                    xtype:'tbspacer',
                    flex:1
                },{
                    icon: varjs.config.base_url + 'images/icons/new/reload.png', cls:'Sfr_BtnGridBlue', overCls:'Sfr_BtnGridBlue-Hover',
                    tooltip: lang('Reload'),
                    handler: function() {
                        //reload
                        setFilterLs(this.viewVar.MillID);
                        Ext.getCmp('Koltiva.view.Mill.GridTracebilityDeclarationManual-gridMainGrid').getStore().loadPage(1);
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
                        contextMenuGridMill.showAt(e.getXY());
                    }
                }]
            },{
                text: lang('MillID'),
                dataIndex: 'MillID',
                hidden:true
            },{
                text: lang('MillTCDID'),
                dataIndex: 'MillTCDID',
                hidden:true
            },{
                id: 'Koltiva.view.Mill.GridTracebilityDeclarationManual-colMillTCDName',
                text: lang('Name'),
                dataIndex: 'MillTCDName',
                width: '25%'
            }, {
                id: 'Koltiva.view.Mill.GridTracebilityDeclarationManual-colDateCreated',
                text: lang('Date Created'),
                width: '25%',
                dataIndex: 'DateCreated'
            }]
        }];

        this.callParent(arguments);
    }
});