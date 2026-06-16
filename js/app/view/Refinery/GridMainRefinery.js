/*
* @Author: muhammad hidayaturrohman
* @Date:   2020-05-11
* @Last Modified by:   muhammad hidayatrurrohman
* @Last Modified time: 2020-05-11

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
var contextMenuGridRefinery = Ext.create('Ext.menu.Menu',{
    items:[{
        icon: varjs.config.base_url + 'images/icons/new/view.png',
        text: lang('View'),
        handler: function() {
            yourApp.showLoading();
            var sm = Ext.getCmp('Koltiva.view.Refinery.GridMainRefinery-gridMainGrid').getSelectionModel().getSelection()[0];

            Ext.getCmp('Koltiva.view.Refinery.GridMainRefinery').destroy(); //destory current view
            //create object View untuk FormMainRefinery
            if(Ext.getCmp('Koltiva.view.Refinery.FormMainRefinery') == undefined){
                var FormMainRefinery = Ext.create('Koltiva.view.Refinery.FormMainRefinery', {
                    opsiDisplay: 'view',
                    viewVar: {
                        RefineryID: sm.get('id')
                    }
                });
            }else{
                //destroy, create ulang
                Ext.getCmp('Koltiva.view.Refinery.FormMainRefinery').destroy();
                var FormMainRefinery = Ext.create('Koltiva.view.Refinery.FormMainRefinery', {
                    opsiDisplay: 'view',
                    viewVar: {
                        RefineryID: sm.get('id')
                    }
                });
            }

        }
    },{
        icon: varjs.config.base_url + 'images/icons/new/update.png',
        text: lang('Update'),
        hidden: m_act_update,
        handler: function() {
            yourApp.showLoading();
            var sm = Ext.getCmp('Koltiva.view.Refinery.GridMainRefinery-gridMainGrid').getSelectionModel().getSelection()[0];
            
            Ext.getCmp('Koltiva.view.Refinery.GridMainRefinery').destroy(); //destory current view
            //create object View untuk FormMainRefinery
            if(Ext.getCmp('Koltiva.view.Refinery.FormMainRefinery') == undefined){
                var FormMainRefinery = Ext.create('Koltiva.view.Refinery.FormMainRefinery', {
                    opsiDisplay: 'update',
                    viewVar: {
                        RefineryID: sm.get('id')
                    }
                });
            }else{
                //destroy, create ulang
                Ext.getCmp('Koltiva.view.Refinery.FormMainRefinery').destroy();
                var FormMainRefinery = Ext.create('Koltiva.view.Refinery.FormMainRefinery', {
                    opsiDisplay: 'update',
                    viewVar: {
                        RefineryID: sm.get('id')
                    }
                });
            }

        }
    },{
        icon: varjs.config.base_url + 'images/icons/new/update.png',
        text: lang('Set as Partner'),
        id: 'contextMenuGridRefinery.SetAsPartner',
        handler: function() {
            var sm = Ext.getCmp('Koltiva.view.Refinery.GridMainRefinery-gridMainGrid').getSelectionModel().getSelection()[0];

            Ext.MessageBox.confirm(
                    'Message', 
                    lang(
                        'Are you sure want to set this Refinery as Partner?'
                       +' Because after you set this as partner you can not edit '
                       +' refinery partner ownership status to other partner.'
                    )
                    , function(btn) 
                {
                if (btn == 'yes') {
                    Ext.Ajax.request({
                        waitMsg: 'Please Wait',
                        url: m_api + '/refinery/refinery_as_partner',
                        method: 'PUT',
                        params: {
                            RefineryID: sm.get('id'),
                            RefineryName : sm.get('Name')
                        },
                        success: function(response, opts) {
                            Ext.MessageBox.show({
                                title: 'Information',
                                msg: lang('Success'),
                                buttons: Ext.MessageBox.OK,
                                animateTarget: 'mb9',
                                icon: 'ext-mb-success'
                            });

                            //refresh store
                            setFilterLs();
                            Ext.getCmp('Koltiva.view.Refinery.GridMainRefinery-gridMainGrid').getStore().load();
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
    },{
        icon: varjs.config.base_url + 'images/icons/new/update.png',
        text: lang('Edit Partner Ownership'),
        id: 'contextMenuGridRefinery.PartnerOwner',
        hidden: m_act_set_partner_refinery,
        handler: function() {
            var sm = Ext.getCmp('Koltiva.view.Refinery.GridMainRefinery-gridMainGrid').getSelectionModel().getSelection()[0];
           
            var frm = Ext.create('Koltiva.view.Refinery.FormSetPartner');
            frm.getForm().load({method: 'GET', params: {RefineryID: sm.get('id')}});
            win = Ext.create('Ext.Window',{
                title: lang('Edit Refinery Ownership'),
                closable: true,
                modal: true,
                autoScroll: true,
                width: '40%',
                items:[frm]
            }).show();
        }
    },{
        icon: varjs.config.base_url + 'images/icons/new/update.png',
        text: lang('Edit FA Assignment'),
        id: 'contextMenuGridRefinery.FAAssignment',
        handler: function () {
            var sm = Ext.getCmp('Koltiva.view.Refinery.GridMainRefinery-gridMainGrid').getSelectionModel().getSelection()[0];
            //display window
            var winFaAssignment = Ext.create('Koltiva.view.Refinery.WinFaAssignment', {
                viewVar: {
                    RefineryID: sm.get('id')
                }
            });
            if (!winFaAssignment.isVisible()) {
                winFaAssignment.center();
                winFaAssignment.show();
            } else {
                winFaAssignment.close();
            }
        }
    },{
        icon: varjs.config.base_url + 'images/icons/silk/lock_start.png',
        text: lang('Report Locked Transaction'),
        hidden: m_act_reported_locked,
        handler: function() {
            var sm = Ext.getCmp('Koltiva.view.Refinery.GridMainRefinery-gridMainGrid').getSelectionModel().getSelection()[0];

            
            displayAddWindowLockedTransaction(sm.get('id'));

            // Ext.getCmp('Koltiva.view.Refinery.GridMainRefinery').destroy(); //destory current view
            // //create object View untuk FormMainRefinery
            // if(Ext.getCmp('Koltiva.view.Refinery.GridReportLockedTransaction') == undefined){
            //     var GridReportLockedTransaction = Ext.create('Koltiva.view.Refinery.GridReportLockedTransaction', {
            //         opsiDisplay: 'update',
            //         viewVar: {
            //             RefineryID: sm.get('id')
            //         }
            //     });
            // }else{
            //     //destroy, create ulang
            //     Ext.getCmp('Koltiva.view.Refinery.GridReportLockedTransaction').destroy();
            //     var GridReportLockedTransaction = Ext.create('Koltiva.view.Refinery.GridReportLockedTransaction', {
            //         opsiDisplay: 'update',
            //         viewVar: {
            //             RefineryID: sm.get('id')
            //         }
            //     });
            // }
        }
    },{
        icon: varjs.config.base_url + 'images/icons/new/view.png',
        text: lang('Generate Tracebility Declaration'),
        hidden: m_act_tracebility_declaration,
        handler: function() {
            var sm = Ext.getCmp('Koltiva.view.Refinery.GridMainRefinery-gridMainGrid').getSelectionModel().getSelection()[0];

            Ext.MessageBox.confirm('Message', 'Do you want to Generate this data ?', function(btn) {
                if (btn == 'yes') {
                    Ext.Ajax.request({
                        waitMsg: 'Please Wait',
                        url: m_api + '/refinery/traceabiliy_generate',
                        method: 'GET',
                        params: {
                            RefineryID: sm.get('id')
                        },
                        success: function(response, opts) {
                            Ext.MessageBox.show({
                                title: 'Information',
                                msg: lang('Data Generated'),
                                buttons: Ext.MessageBox.OK,
                                animateTarget: 'mb9',
                                icon: 'ext-mb-success'
                            });

                            //refresh store
                            setFilterLs();
                            Ext.getCmp('Koltiva.view.Refinery.GridMainRefinery-gridMainGrid').getStore().load();
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
    },{
        icon: varjs.config.base_url + 'images/icons/new/view.png',
        text: lang('Tracebility Declaration'),
        // hidden: m_act_tracebility_declaration,
        hidden:true,
        handler: function() {
            var sm = Ext.getCmp('Koltiva.view.Refinery.GridMainRefinery-gridMainGrid').getSelectionModel().getSelection()[0];

            Ext.getCmp('Koltiva.view.Refinery.GridMainRefinery').destroy(); //destory current view
            //create object View untuk FormMainRefinery
            if(Ext.getCmp('Koltiva.view.Refinery.FormTracebilityDeclaration') == undefined){
                var FormTracebilityDeclaration = Ext.create('Koltiva.view.Refinery.FormTracebilityDeclaration', {
                    opsiDisplay: 'update',
                    viewVar: {
                        RefineryID: sm.get('id')
                    }
                });
            }else{
                //destroy, create ulang
                Ext.getCmp('Koltiva.view.Refinery.FormTracebilityDeclaration').destroy();
                var FormTracebilityDeclaration = Ext.create('Koltiva.view.Refinery.FormTracebilityDeclaration', {
                    opsiDisplay: 'update',
                    viewVar: {
                        RefineryID: sm.get('id')
                    }
                });
            }
        }
    },{
        icon: varjs.config.base_url + 'images/icons/new/view.png',
        text: lang('Tracebility Declaration Manual'),
        hidden: true,
        handler: function() {
            var sm = Ext.getCmp('Koltiva.view.Refinery.GridMainRefinery-gridMainGrid').getSelectionModel().getSelection()[0];

            Ext.getCmp('Koltiva.view.Refinery.GridMainRefinery').destroy(); //destory current view
            //create object View untuk FormMainRefinery
            if(Ext.getCmp('Koltiva.view.Refinery.GridTracebilityDeclarationManual') == undefined){
                var GridTracebilityDeclarationManual = Ext.create('Koltiva.view.Refinery.GridTracebilityDeclarationManual', {
                    viewVar: {
                        RefineryID: sm.get('id')
                    }
                });
            }else{
                //destroy, create ulang
                Ext.getCmp('Koltiva.view.Refinery.GridTracebilityDeclarationManual').destroy();
                var GridTracebilityDeclarationManual = Ext.create('Koltiva.view.Refinery.GridTracebilityDeclarationManual', {
                    viewVar: {
                        RefineryID: sm.get('id')
                    }
                });
            }
        }
    },{
        icon: varjs.config.base_url + 'images/icons/new/delete.png',
        text: lang('Delete'),
        hidden: m_act_delete,
        handler: function(){
            var sm = Ext.getCmp('Koltiva.view.Refinery.GridMainRefinery-gridMainGrid').getSelectionModel().getSelection()[0];

            Ext.MessageBox.confirm('Message', 'Do you want to delete this data ?', function(btn) {
                if (btn == 'yes') {
                    Ext.Ajax.request({
                        waitMsg: 'Please Wait',
                        url: m_api + '/refinery/refinery_form',
                        method: 'DELETE',
                        params: {
                            RefineryID: sm.get('id')
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
                            Ext.getCmp('Koltiva.view.Refinery.GridMainRefinery-gridMainGrid').getStore().load();
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

var store_grid_report_locked    = Ext.create('Koltiva.store.Refinery.GridReportLocked');
var CmbYearOption               = Ext.create('Koltiva.store.ComboGeneral.CmbYearOption',{
    storeVar: {
        yearRange: 5
    }
});

var DataFormParAdd = Ext.create('Ext.panel.Panel', {
    height: '100%',
    //autoScroll: true,
    overflowY: 'auto',
    width: '100%',
    //bodyPadding: 5,
    id: 'dataFormParAdd',
    items: [{
            xtype: 'gridpanel',
            id: 'grid_report_locked',
            store: store_grid_report_locked,
            loadMask: true,
            viewConfig: {
                deferEmptyText: false,
                emptyText: GetDefaultContentNoData()
            },
            dockedItems: [
                {
                    xtype: 'toolbar',
                    items: [
                        {
                            id: 'RefineryID',
                            name: 'RefineryID',
                            xtype: 'textfield',
                            fieldLabel: lang('RefineryID'),
                            hidden:true
                        },
                        {
                            id: 'Year',
                            name: 'Year',
                            xtype: 'combo',
                            fieldLabel: lang('Year'),
                            labelWidth: 50,
                            store: CmbYearOption,
                            displayField: 'label',
                            valueField: 'id',
                            queryMode: 'local',
                            value:m_year
                        },{
                            xtype: 'button',
                            icon: varjs.config.base_url + 'images/icons/silk/search.png',
                            margin: '0px 0px 0px 6px',
                            text: lang('View'),
                            handler: function () {
                                store_grid_report_locked.load({
                                    params: {
                                        Year: Ext.getCmp('Year').getValue(),
                                        RefineryID: Ext.getCmp('RefineryID').getValue()
                                    }
                                });
                            }
                        }
                    ]
                },
                {
                    xtype: 'pagingtoolbar',
                    store: store_grid_report_locked,
                    dock: 'bottom',
                    displayInfo: true
                }, 
            ],
            columns: [
                {
                    text: lang('Supply Batch Number'),
                    dataIndex: 'SupplyBatchNumber',
                    flex: 2,
                },
                {
                    text: lang('Delivery Date'),
                    dataIndex: 'DeliveryDate',
                    flex: 2,
                },
                {
                    text: lang('PO Number'),
                    dataIndex: 'DestPO',
                    flex: 2,
                },
                {
                    text: lang('Netto'),
                    dataIndex: 'VolumeNetto',
                    flex: 2,
                },
                {
                    text: lang('Batch Status'),
                    dataIndex: 'SupplyBatchStatus',
                    flex: 2,
                }
            ]
        }],
    buttons: [{
            id: 'save_par_add',
            text: lang('Save'),
            margin: '5px',
            scale: 'large',
            ui: 's-button',
            cls: 's-blue ',
            handler: function() {
                var participants = '';
                Ext.each(Ext.getCmp('grid_participant_add').getSelectionModel().getSelection(), function(row, index, value) {
                    //participants.push(row.data.addFarmerID);
                    participants = participants + ',' + row.data.addFarmerID;
                });
                if (participants !== '') {
                    Ext.Ajax.request({
                        url: m_store_participant + 's',
                        method: 'POST',
                        waitMsg: lang('Sending data...'),
                        params: {
                            FarmerTrainingID: Ext.getCmp('idt').getValue(),
                            participants: participants,
                        },
                        success: function(response, opts) {
                            var obj = Ext.decode(response.responseText);
                            switch (obj.success) {
                                case true:
                                    store_participant.load({
                                        params: {
                                            training: Ext.getCmp('idt').getValue()
                                        }
                                    });
                                    winAddPar.hide();
                                    break;
                                default:
                                    Ext.MessageBox.alert('Warning', obj.message);
                                    break;
                            }
                        }
                    });
                } else {
                    Ext.Msg.alert("Warning", "Please select participants");
                }
            }
        }, {
            text: lang('Close'),
            margin: '5px',
            scale: 'large',
            ui: 's-button',
            cls: 's-grey',
            disabled: false,
            handler: function() {
                winLockedTrans.hide();
            }
        }]
});

var winLockedTrans = Ext.widget('window', {
    title: lang('Report Locked Transaction'),
    id: 'winLockedTrans',
    closeAction: 'hide',
    height: '70%',
    width: '50%',
    autoScroll: true,
    modal: true,
    layout: 'fit',
    items: [DataFormParAdd]
});

function displayAddWindowLockedTransaction(RefineryID) {
    Ext.getCmp('RefineryID').setValue(RefineryID);
    if (!winLockedTrans.isVisible()) {
        store_grid_report_locked.getProxy().extraParams.RefineryID = RefineryID;
        store_grid_report_locked.getProxy().extraParams.Year = m_year;
        store_grid_report_locked.load();
        winLockedTrans.show();
    } else {
        winLockedTrans.hide(this, function() {
        });
        winLockedTrans.toFront();
    }
}

function setFilterLs(){
    localStorage.setItem('patchouli_refinery_ls',
        JSON.stringify({
            ptextSearch: Ext.getCmp('Koltiva.view.Refinery.GridMainRefinery-textSearch').getValue()
        })
    );
}

function submitOnEnterGridRefinery(field, event){
    if (event.getKey() == event.ENTER) {
        setFilterLs();
        Ext.getCmp('Koltiva.view.Refinery.GridMainRefinery-gridMainGrid').getStore().loadPage(1);
    }
}
// Define Variabel2 / Object2 / Fungsi yg diperlukan oleh view ini (end)

Ext.define('Koltiva.view.Refinery.GridMainRefinery' ,{
extend: 'Ext.panel.Panel',
id: 'Koltiva.view.Refinery.GridMainRefinery',
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
        Ext.getCmp('Koltiva.view.Refinery.GridMainRefinery-gridMainGrid').getStore().load();
    }
},
style:'padding:0 15px 15px 15px;margin:5px 0 0 0;',
initComponent: function() {
    var thisObj = this;
    
    //store
    var storeGridMain = Ext.create('Koltiva.store.Refinery.GridMain');

    thisObj.items = [{
        layout: 'column',
        border: false,
        items: [{
            columnWidth: .3,
            layout: 'form',
            items:[{
                xtype: 'button',
                id: 'Koltiva.view.Refinery.GridMainRefinery-btnListView',
                style:'margin-top:5px;',
                text: lang('All Refinerys'),
                arrowAlign: 'right',
                hidden: true,
                menu : [{
                    text: lang('All Refinerys'),
                    listeners: {
                        click: function(){
                            alert('All Refinerys Click')
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
            id: 'Koltiva.view.Refinery.GridMainRefinery-gridInformation',
            html: ''
        }]
    },{
        xtype: 'grid',
        id: 'Koltiva.view.Refinery.GridMainRefinery-gridMainGrid',
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
            id: 'Koltiva.view.Refinery.GridMainRefinery-gridToolbar',
            store: storeGridMain,
            dock: 'bottom',
            displayInfo: true
        },{
            xtype: 'toolbar',
            dock:'top',
            items: [{
                icon: varjs.config.base_url + 'images/icons/new/add.png', cls:'Sfr_BtnGridGreen', overCls:'Sfr_BtnGridGreen-Hover',
                text: lang('Add'),
                // hidden: m_act_add,
                handler: function() {
                    Ext.getCmp('Koltiva.view.Refinery.GridMainRefinery').destroy(); //destory current view

                    //create object View untuk FormMainRefinery
                    if(Ext.getCmp('Koltiva.view.Refinery.FormMainRefinery') == undefined){
                        var FormMainRefinery = Ext.create('Koltiva.view.Refinery.FormMainRefinery', {
                            opsiDisplay: 'insert'
                        });
                    }else{
                        //destroy, create ulang
                        Ext.getCmp('Koltiva.view.Refinery.FormMainRefinery').destroy();
                        var FormMainRefinery = Ext.create('Koltiva.view.Refinery.FormMainRefinery', {
                            opsiDisplay: 'insert'
                        });
                    }
                }
            },{
                icon: varjs.config.base_url + 'images/icons/new/export.png', cls:'Sfr_BtnGridPaleBlue',
                text: lang('Export'),
                // hidden: m_act_export,
                handler: function() {
                    Ext.MessageBox.show({
                        title: 'Information',
                        msg: lang('Coming Soon!'),
                        buttons: Ext.MessageBox.OK,
                        animateTarget: 'mb9',
                        icon: 'ext-mb-info'
                    });
                }
            },{
                name: 'key', baseCls:'Sfr_TxtfieldSearchGrid',
                id: 'Koltiva.view.Refinery.GridMainRefinery-textSearch',
                xtype: 'textfield',
                width: 400,
                emptyText: lang('Cari berdasar nama/ID')+', '+lang('Press \'Enter\' to search'),
                listeners: {
                    specialkey: submitOnEnterGridRefinery
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
                    Ext.getCmp('Koltiva.view.Refinery.GridMainRefinery-gridMainGrid').getStore().loadPage(1);
                }
            },{
                icon: varjs.config.base_url + 'images/icons/silk/grid.png', cls:'Sfr_BtnGridPaleBlue',
                tooltip: lang('Custom Field Grid'),
                handler: function() {
                    //display field grid
                    var winGridDisplay = Ext.create('Koltiva.view.Refinery.WinGridDisplay');
                    if (!winGridDisplay.isVisible()) {
                        winGridDisplay.center();
                        winGridDisplay.show();
                    } else {
                        winGridDisplay.close();
                    }
                }
            },{
                icon: varjs.config.base_url + 'images/icons/silk/find.png', cls:'Sfr_BtnGridPaleBlue',
                tooltip: lang('Advanced Filter'),
                handler: function() {
                    //advanced search
                    var winAdvFilter = Ext.create('Koltiva.view.Refinery.WinAdvancedFilter');
                    if (!winAdvFilter.isVisible()) {
                        winAdvFilter.center();
                        winAdvFilter.show();
                    } else {
                        winAdvFilter.close();
                    }
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
                    contextMenuGridRefinery.showAt(e.getXY());
                    if(sm == "Yes"){
                        contextMenuGridRefinery.getComponent('contextMenuGridRefinery.SetAsPartner').setVisible(false);
                        contextMenuGridRefinery.getComponent('contextMenuGridRefinery.PartnerOwner').setVisible(false);
                    }else{
                        contextMenuGridRefinery.getComponent('contextMenuGridRefinery.SetAsPartner').setVisible(true);
                        contextMenuGridRefinery.getComponent('contextMenuGridRefinery.PartnerOwner').setVisible(true);
                    }
                }
            }]
        },{
            text: lang('ID'),
            dataIndex: 'id',
            hidden:true
        },{
            id: 'Koltiva.view.Refinery.GridMainRefinery-colid',
            text: lang('ID'),
            dataIndex: 'RefineryDisplayID',
            width: '7%'
        }, {
            id: 'Koltiva.view.Refinery.GridMainRefinery-colName',
            text: lang('Name'),
            width: '25%',
            dataIndex: 'Name'
        },{
            id: 'Koltiva.view.Refinery.GridMainRefinery-colCompanyName',
            text: lang('Company Name'),
            width: '15%',
            dataIndex: 'CompanyName'
        }, 
        // {
        //     id: 'Koltiva.view.Refinery.GridMainRefinery-colRefinerySMEName',
        //     text: lang('SME Name'),
        //     width: '10%',
        //     dataIndex: 'SMEName'
        // }, 
        // {
        //     id: 'Koltiva.view.Refinery.GridMainRefinery-colRefineryNrPlantation',
        //     text: lang('Nr Of Plantattion'),
        //     width: '10%',
        //     dataIndex: 'NrPlantation'
        // }, 
        // {
        //     id: 'Koltiva.view.Refinery.GridMainRefinery-colRefineryNrFarmer',
        //     text: lang('Nr Of Farmer'),
        //     width: '10%',
        //     dataIndex: 'NrFarmer'
        // }, 
        {
            id: 'Koltiva.view.Refinery.GridMainRefinery-colRefineryGPS',
            text: lang('Coordinate'),
            width: '10%',
            dataIndex: 'GPS'
        },{
            id: 'Koltiva.view.Refinery.GridMainRefinery-colAlias',
            text: lang('Alias'),
            width: '15%',
            dataIndex: 'Alias',
            hidden: true
        },{
            id: 'Koltiva.view.Refinery.GridMainRefinery-colAddress',
            text: lang('Address'),
            width: '15%',
            dataIndex: 'Address',
            hidden: true
        },{
            id: 'Koltiva.view.Refinery.GridMainRefinery-colProvince',
            text: lang('Province'),
            width: '15%',
            dataIndex: 'Province',
            hidden: true
        },{
            id: 'Koltiva.view.Refinery.GridMainRefinery-colDistrict',
            text: lang('District'),
            width: '15%',
            dataIndex: 'District',
            hidden: true
        },{
            id: 'Koltiva.view.Refinery.GridMainRefinery-colKecamatan',
            text: lang('Kecamatan'),
            width: '15%',
            dataIndex: 'Kecamatan'
        },{
            id: 'Koltiva.view.Refinery.GridMainRefinery-colDesa',
            text: lang('Desa'),
            width: '15%',
            dataIndex: 'Desa'
        },{
            id: 'Koltiva.view.Refinery.GridMainRefinery-colStatusPerusahaan',
            text: lang('Status Perusahaan'),
            width: '11%',
            dataIndex: 'StatusPerusahaan'
        },{
            id: 'Koltiva.view.Refinery.GridMainRefinery-colTahunTerbentuk',
            text: lang('Tahun Terbentuk'),
            width: '8%',
            dataIndex: 'TahunTerbentuk'
        },{
            id: 'Koltiva.view.Refinery.GridMainRefinery-colPhone',
            text: lang('Phone'),
            width: '15%',
            dataIndex: 'Phone',
            hidden: true
        },{
            id: 'Koltiva.view.Refinery.GridMainRefinery-colTotalPermanentEmployee',
            text: lang('Total Permanent Employee'),
            width: '15%',
            dataIndex: 'TotalPermanentEmployee',
            hidden: true
        },{
            id: 'Koltiva.view.Refinery.GridMainRefinery-colLastUpdated',
            text: lang('Last Updated'),
            width: '12%',
            dataIndex: 'LastUpdated'
        }]
    }];

    this.callParent(arguments);
}
});