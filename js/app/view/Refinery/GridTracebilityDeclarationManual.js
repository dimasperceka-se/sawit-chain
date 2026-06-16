/*
* @Author: muhammad hidayaturrohman
* @Date:   2020-11-05 
* @Last Modified by:   muhammad hidayaturrohman
* @Last Modified time: 2020-11-05
*/

/*
    Param2 yg diperlukan ketika load View ini
    1. ....
*/

// Define Variabel2 / Object2 / Fungsi yg diperlukan oleh view ini (begin)
var contextMenuGridRefinery = Ext.create('Ext.menu.Menu',{
    items:[{
        icon: varjs.config.base_url + 'images/icons/new/view.png',
        text: lang('View'),
        handler: function() {
            var sm = Ext.getCmp('Koltiva.view.Refinery.GridTracebilityDeclarationManual-gridMainGrid').getSelectionModel().getSelection()[0];

            Ext.getCmp('Koltiva.view.Refinery.GridTracebilityDeclarationManual').destroy(); //destory current view
            //create object View untuk FormMainRefinery
            if(Ext.getCmp('Koltiva.view.Refinery.FormGenerateTracebilityDeclarationManual') == undefined){
                var FormMainRefinery = Ext.create('Koltiva.view.Refinery.FormGenerateTracebilityDeclarationManual', {
                    opsiDisplay: 'view',
                    RefineryID: sm.get('RefineryID'),
                    RefineryTCDID : sm.get('RefineryTCDID')
                });
            }else{
                //destroy, create ulang
                Ext.getCmp('Koltiva.view.Refinery.FormGenerateTracebilityDeclarationManual').destroy();
                var FormMainRefinery = Ext.create('Koltiva.view.Refinery.FormGenerateTracebilityDeclarationManual', {
                    opsiDisplay: 'view',
                    RefineryID: sm.get('RefineryID'),
                    RefineryTCDID : sm.get('RefineryTCDID')
                });
            }

        }
    },{
        icon: varjs.config.base_url + 'images/icons/new/update.png',
        text: lang('Update'),
        hidden: m_act_update,
        handler: function() {
            var sm = Ext.getCmp('Koltiva.view.Refinery.GridTracebilityDeclarationManual-gridMainGrid').getSelectionModel().getSelection()[0];

            Ext.getCmp('Koltiva.view.Refinery.GridTracebilityDeclarationManual').destroy(); //destory current view

            //create object View untuk FormMainRefinery
            if(Ext.getCmp('Koltiva.view.Refinery.FormTracebilityDeclarationManual') == undefined){
                var FormTracebilityDeclarationManual = Ext.create('Koltiva.view.Refinery.FormTracebilityDeclarationManual', {
                    opsiDisplay: 'update',
                    RefineryID: sm.get('RefineryID'),
                    RefineryTCDID : sm.get('RefineryTCDID')
                });
            }else{
                //destroy, create ulang
                Ext.getCmp('Koltiva.view.Refinery.FormTracebilityDeclarationManual').destroy();
                var FormTracebilityDeclarationManual = Ext.create('Koltiva.view.Refinery.FormTracebilityDeclarationManual', {
                    opsiDisplay: 'update',
                    RefineryID: sm.get('RefineryID'),
                    RefineryTCDID : sm.get('RefineryTCDID')
                });
            }

        }
    },{
        icon: varjs.config.base_url + 'images/icons/new/delete.png',
        text: lang('Delete'),
        hidden: m_act_delete,
        handler: function(){
            var sm = Ext.getCmp('Koltiva.view.Refinery.GridTracebilityDeclarationManual-gridMainGrid').getSelectionModel().getSelection()[0];

            Ext.MessageBox.confirm('Message', 'Do you want to delete this data ?', function(btn) {
                if (btn == 'yes') {
                    Ext.Ajax.request({
                        waitMsg: 'Please Wait',
                        url: m_api + '/refinery/form_tc_declaration',
                        method: 'DELETE',
                        params: {
                            RefineryTCDID : sm.get('RefineryTCDID')
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
                            Ext.getCmp('Koltiva.view.Refinery.GridTracebilityDeclarationManual-gridMainGrid').getStore().load();
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
    localStorage.setItem('patchouli_refinery_ls',
        JSON.stringify({
            RefineryID : Ext.getCmp('RefineryIDSearch').getValue(),
            ptextSearch: Ext.getCmp('Koltiva.view.Refinery.GridTracebilityDeclarationManual-textSearch').getValue()
        })
    );
}

function submitOnEnterGridRefinery(field, event){
    if (event.getKey() == event.ENTER) {
        setFilterLs();
        Ext.getCmp('Koltiva.view.Refinery.GridTracebilityDeclarationManual-gridMainGrid').getStore().loadPage(1);
    }
}
// Define Variabel2 / Object2 / Fungsi yg diperlukan oleh view ini (end)

Ext.define('Koltiva.view.Refinery.GridTracebilityDeclarationManual' ,{
extend: 'Ext.panel.Panel',
id: 'Koltiva.view.Refinery.GridTracebilityDeclarationManual',
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
        var patchouli_refinery_ls = JSON.parse(localStorage.getItem('patchouli_refinery_ls'));
        if(patchouli_refinery_ls != null){
            Ext.getCmp('Koltiva.view.Refinery.GridTracebilityDeclarationManual-textSearch').setValue(patchouli_refinery_ls.ptextSearch);

            console.log(patchouli_refinery_ls.RefineryID+'sess');
            Ext.getCmp('RefineryIDSearch').setValue(patchouli_refinery_ls.RefineryID);
        }

        if(typeof this.viewVar.RefineryID !== 'undefined'){
            Ext.getCmp("RefineryIDSearch").setValue(this.viewVar.RefineryID);
        }

        console.log(this.viewVar.RefineryID);

        setFilterLs();
        Ext.getCmp('Koltiva.view.Refinery.GridTracebilityDeclarationManual-gridMainGrid').getStore().load();
    }
},
style:'padding:0 15px 15px 15px;margin:5px 0 0 0;',
initComponent: function() {
    var thisObj = this;
    
    //store
    var storeGridTracebilityDeclarationManual = Ext.create('Koltiva.store.Refinery.GridTracebilityDeclarationManual');

    var RefineryID = this.viewVar.RefineryID;

    thisObj.items = [{
        xtype: 'panel',
        border:false,
        layout:{
            type:'hbox'
        },
        items:[{
            id: 'Koltiva.view.Refinery.FormTracebilityDeclarationManual-labelInfoTitle',
            html:'<h3 style="margin:0;padding:0px;">'+lang('Tracebility Declaration')+'</h3>'
        },{
            id: 'Koltiva.view.Refinery.FormTracebilityDeclarationManual-labelInfo',
            html:'',
        }]
    },{
        html: '<div id="Sfr_IdBoxInfoDataGrid" class="Sfr_BoxInfoDataGrid">' +
              '<ul class="Sft_UlListInfoDataGrid"><li class="Sft_ListInfoDataGrid">' +
              '<a><img class="Sft_ListIconInfoDataGrid" src="' + varjs.config.base_url + 'images/icons/new/back.png" width="20" />' +
              '&nbsp;&nbsp;' + lang('Back to Tracebilitry Refinery List')  + '</a></li></ul></div>',
        listeners: {
            click: {
                element: 'el',
                preventDefault: true,
                fn: function(e, target){
                    Ext.getCmp('Koltiva.view.Refinery.GridTracebilityDeclarationManual').destroy(); //destory current view
                    var GridMainFarcan = [];

                    if(Ext.getCmp('Koltiva.view.Refinery.GridMainRefinery') == undefined){
                        GridMainFarcan = Ext.create('Koltiva.view.Refinery.GridMainRefinery');
                    }else{
                        //destroy, create ulang
                        Ext.getCmp('Koltiva.view.Refinery.GridMainRefinery').destroy();
                        GridMainFarcan = Ext.create('Koltiva.view.Refinery.GridMainRefinery');
                    }
                }
            }
        }
    },{
        xtype: 'grid',
        id: 'Koltiva.view.Refinery.GridTracebilityDeclarationManual-gridMainGrid',
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
            id: 'Koltiva.view.Refinery.GridTracebilityDeclarationManual-gridToolbar',
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
                    Ext.getCmp('Koltiva.view.Refinery.GridTracebilityDeclarationManual').destroy(); //destory current view

                    //create object View untuk FormMainRefinery
                    if(Ext.getCmp('Koltiva.view.Refinery.FormTracebilityDeclarationManual') == undefined){
                        var FormTracebilityDeclarationManual = Ext.create('Koltiva.view.Refinery.FormTracebilityDeclarationManual', {
                            opsiDisplay: 'insert',
                            RefineryID: RefineryID
                        });
                    }else{
                        //destroy, create ulang
                        Ext.getCmp('Koltiva.view.Refinery.FormTracebilityDeclarationManual').destroy();
                        var FormTracebilityDeclarationManual = Ext.create('Koltiva.view.Refinery.FormTracebilityDeclarationManual', {
                            opsiDisplay: 'insert',
                            RefineryID: RefineryID
                        });
                    }
                }
            },{
                name: 'key', baseCls:'Sfr_TxtfieldSearchGrid',
                id: 'Koltiva.view.Refinery.GridTracebilityDeclarationManual-textSearch',
                xtype: 'textfield',
                width: 400,
                emptyText: lang('Cari berdasar nama')+', '+lang('Press \'Enter\' to search'),
                listeners: {
                    specialkey: submitOnEnterGridRefinery
                }
            },{
                name: 'RefineryIDSearch',
                id: 'RefineryIDSearch',
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
                    setFilterLs(this.viewVar.RefineryID);
                    Ext.getCmp('Koltiva.view.Refinery.GridTracebilityDeclarationManual-gridMainGrid').getStore().loadPage(1);
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
                    contextMenuGridRefinery.showAt(e.getXY());
                }
            }]
        },{
            text: lang('RefineryID'),
            dataIndex: 'RefineryID',
            hidden:true
        },{
            text: lang('RefineryTCDID'),
            dataIndex: 'RefineryTCDID',
            hidden:true
        },{
            id: 'Koltiva.view.Refinery.GridTracebilityDeclarationManual-colRefineryTCDName',
            text: lang('Name'),
            dataIndex: 'RefineryTCDName',
            width: '25%'
        }, {
            id: 'Koltiva.view.Refinery.GridTracebilityDeclarationManual-colDateCreated',
            text: lang('Date Created'),
            width: '25%',
            dataIndex: 'DateCreated'
        }]
    }];

    this.callParent(arguments);
}
});