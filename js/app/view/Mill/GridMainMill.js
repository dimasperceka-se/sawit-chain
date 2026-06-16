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

                var sm = Ext.getCmp('Koltiva.view.Mill.GridMainMill-gridMainGrid').getSelectionModel().getSelection()[0];

                Ext.getCmp('Koltiva.view.Mill.GridMainMill').destroy(); //destory current view
                //create object View untuk FormMainMill
                if(Ext.getCmp('Koltiva.view.Mill.FormMainMill') == undefined){
                    var FormMainMill = Ext.create('Koltiva.view.Mill.FormMainMill', {
                        opsiDisplay: 'view',
                        viewVar: {
                            MillID: sm.get('id')
                        }
                    });
                }else{
                    //destroy, create ulang
                    Ext.getCmp('Koltiva.view.Mill.FormMainMill').destroy();
                    var FormMainMill = Ext.create('Koltiva.view.Mill.FormMainMill', {
                        opsiDisplay: 'view',
                        viewVar: {
                            MillID: sm.get('id')
                        }
                    });
                }

            }
        },{
            icon: varjs.config.base_url + 'images/icons/new/update.png',
            text: lang('Update'),
            hidden: m_act_update,
            handler: function() {

                var sm = Ext.getCmp('Koltiva.view.Mill.GridMainMill-gridMainGrid').getSelectionModel().getSelection()[0];

                Ext.getCmp('Koltiva.view.Mill.GridMainMill').destroy(); //destory current view
                //create object View untuk FormMainMill
                if(Ext.getCmp('Koltiva.view.Mill.FormMainMill') == undefined){
                    var FormMainMill = Ext.create('Koltiva.view.Mill.FormMainMill', {
                        opsiDisplay: 'update',
                        viewVar: {
                            MillID: sm.get('id')
                        }
                    });
                }else{
                    //destroy, create ulang
                    Ext.getCmp('Koltiva.view.Mill.FormMainMill').destroy();
                    var FormMainMill = Ext.create('Koltiva.view.Mill.FormMainMill', {
                        opsiDisplay: 'update',
                        viewVar: {
                            MillID: sm.get('id')
                        }
                    });
                }

            }
        },{
            icon: varjs.config.base_url + 'images/icons/new/update.png',
            text: lang('Set as Partner'),
            id: 'contextMenuGridMill.SetAsPartner',
            handler: function() {
                var sm = Ext.getCmp('Koltiva.view.Mill.GridMainMill-gridMainGrid').getSelectionModel().getSelection()[0];

                Ext.MessageBox.confirm(
                        'Message', 
                        lang(
                            'Are you sure want to set this Mill as Partner?'
                           +' Because after you set this as partner you can not edit '
                           +' mill partner ownership status to other partner.'
                        )
                        , function(btn) 
                    {
                    if (btn == 'yes') {
                        Ext.Ajax.request({
                            waitMsg: 'Please Wait',
                            url: m_api + '/mill/mill_as_partner',
                            method: 'PUT',
                            params: {
                                MillID: sm.get('id'),
                                MillName : sm.get('Name')
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
                                Ext.getCmp('Koltiva.view.Mill.GridMainMill-gridMainGrid').getStore().load();
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
            id: 'contextMenuGridMill.PartnerOwner',
            hidden: m_act_set_partner_mill,
            handler: function() {
                var sm = Ext.getCmp('Koltiva.view.Mill.GridMainMill-gridMainGrid').getSelectionModel().getSelection()[0];
               
                var frm = Ext.create('Koltiva.view.Mill.FormSetPartner');
                frm.getForm().load({method: 'GET', params: {MillID: sm.get('id')}});
                win = Ext.create('Ext.Window',{
                    title: lang('Edit Mill Ownership'),
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
            hidden:m_act_mill_fa_assisgnment,
            id: 'contextMenuGridMill.FAAssignment',
            handler: function () {
                var sm = Ext.getCmp('Koltiva.view.Mill.GridMainMill-gridMainGrid').getSelectionModel().getSelection()[0];
                //display window
                var winFaAssignment = Ext.create('Koltiva.view.Mill.WinFaAssignment', {
                    viewVar: {
                        MillID: sm.get('id')
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
                var sm = Ext.getCmp('Koltiva.view.Mill.GridMainMill-gridMainGrid').getSelectionModel().getSelection()[0];

                
                displayAddWindowLockedTransaction(sm.get('id'));

                // Ext.getCmp('Koltiva.view.Mill.GridMainMill').destroy(); //destory current view
                // //create object View untuk FormMainMill
                // if(Ext.getCmp('Koltiva.view.Mill.GridReportLockedTransaction') == undefined){
                //     var GridReportLockedTransaction = Ext.create('Koltiva.view.Mill.GridReportLockedTransaction', {
                //         opsiDisplay: 'update',
                //         viewVar: {
                //             MillID: sm.get('id')
                //         }
                //     });
                // }else{
                //     //destroy, create ulang
                //     Ext.getCmp('Koltiva.view.Mill.GridReportLockedTransaction').destroy();
                //     var GridReportLockedTransaction = Ext.create('Koltiva.view.Mill.GridReportLockedTransaction', {
                //         opsiDisplay: 'update',
                //         viewVar: {
                //             MillID: sm.get('id')
                //         }
                //     });
                // }
            }
        },{
            icon: varjs.config.base_url + 'images/icons/new/export.png',
            text: lang('Export Supplier'),
            handler: function() {
                var sm = Ext.getCmp('Koltiva.view.Mill.GridMainMill-gridMainGrid').getSelectionModel().getSelection()[0];

                Ext.MessageBox.confirm('Message', 'Do you want to export this data ?', function(btn) {
                    if (btn == 'yes') {
                        Ext.MessageBox.show({
                            msg: 'Please wait...',
                            progressText: 'Exporting...',
                            width: 300,
                            wait: true,
                            waitConfig: {
                                interval: 200
                            },
                            icon: 'ext-mb-download', //custom class in msg-box.html
                            animateTarget: 'mb7'
                        });
                        
                        var param_string = '?MillID='+sm.get('id');

                        Ext.Ajax.request({
                            url: m_api + '/mill/export_supplier/' + param_string,

                            method: 'GET',
                            waitMsg: lang('Please Wait'),
                            timeout: 360000,
                            success: function (data) {
                                Ext.MessageBox.hide();
                                var jsonResp = JSON.parse(data.responseText);
                                window.location = jsonResp.filenya;
                            },
                            failure: function () {
                                Ext.MessageBox.hide();
                                Ext.MessageBox.show({
                                    title: 'Notifications',
                                    msg: 'Failed to export, Please try again.',
                                    buttons: Ext.MessageBox.OK,
                                    animateTarget: 'mb9',
                                    icon: 'ext-mb-error'
                                });
                            }
                        });
                    }
                });
            }
        },
        // {
        //     icon: varjs.config.base_url + 'images/icons/new/view.png',
        //     text: lang('Generate Tracebility Declaration'),
        //     hidden: m_act_tracebility_declaration,
        //     handler: function() {
        //         var sm = Ext.getCmp('Koltiva.view.Mill.GridMainMill-gridMainGrid').getSelectionModel().getSelection()[0];

        //         Ext.MessageBox.confirm('Message', 'Do you want to Generate this data ?', function(btn) {
        //             if (btn == 'yes') {
        //                 Ext.Ajax.request({
        //                     waitMsg: 'Please Wait',
        //                     url: m_api + '/mill/traceabiliy_generate',
        //                     method: 'GET',
        //                     params: {
        //                         MillID: sm.get('id')
        //                     },
        //                     success: function(response, opts) {
        //                         Ext.MessageBox.show({
        //                             title: 'Information',
        //                             msg: lang('Data Generated'),
        //                             buttons: Ext.MessageBox.OK,
        //                             animateTarget: 'mb9',
        //                             icon: 'ext-mb-success'
        //                         });

        //                         //refresh store
        //                         setFilterLs();
        //                         Ext.getCmp('Koltiva.view.Mill.GridMainMill-gridMainGrid').getStore().load();
        //                     },
        //                     failure: function(response, opts) {
        //                         var pesanNya;
        //                         if(o.result.message != undefined){
        //                             pesanNya = o.result.message;
        //                         }else{
        //                             pesanNya = lang('Connection error');
        //                         }
        //                         Ext.MessageBox.show({
        //                             title: 'Error',
        //                             msg: pesanNya,
        //                             buttons: Ext.MessageBox.OK,
        //                             animateTarget: 'mb9',
        //                             icon: 'ext-mb-error'
        //                         });
        //                     }
        //                 });
        //             }
        //         });
        //     }
        // },
        // {
        //     icon: varjs.config.base_url + 'images/icons/new/view.png',
        //     text: lang('Tracebility Declaration'),
        //     // hidden: m_act_tracebility_declaration,
        //     hidden:true,
        //     handler: function() {
        //         var sm = Ext.getCmp('Koltiva.view.Mill.GridMainMill-gridMainGrid').getSelectionModel().getSelection()[0];

        //         Ext.getCmp('Koltiva.view.Mill.GridMainMill').destroy(); //destory current view
        //         //create object View untuk FormMainMill
        //         if(Ext.getCmp('Koltiva.view.Mill.FormTracebilityDeclaration') == undefined){
        //             var FormTracebilityDeclaration = Ext.create('Koltiva.view.Mill.FormTracebilityDeclaration', {
        //                 opsiDisplay: 'update',
        //                 viewVar: {
        //                     MillID: sm.get('id')
        //                 }
        //             });
        //         }else{
        //             //destroy, create ulang
        //             Ext.getCmp('Koltiva.view.Mill.FormTracebilityDeclaration').destroy();
        //             var FormTracebilityDeclaration = Ext.create('Koltiva.view.Mill.FormTracebilityDeclaration', {
        //                 opsiDisplay: 'update',
        //                 viewVar: {
        //                     MillID: sm.get('id')
        //                 }
        //             });
        //         }
        //     }
        // },
        // {
        //     icon: varjs.config.base_url + 'images/icons/new/view.png',
        //     text: lang('Tracebility Declaration Manual'),
        //     hidden: true,
        //     handler: function() {
        //         var sm = Ext.getCmp('Koltiva.view.Mill.GridMainMill-gridMainGrid').getSelectionModel().getSelection()[0];

        //         Ext.getCmp('Koltiva.view.Mill.GridMainMill').destroy(); //destory current view
        //         //create object View untuk FormMainMill
        //         if(Ext.getCmp('Koltiva.view.Mill.GridTracebilityDeclarationManual') == undefined){
        //             var GridTracebilityDeclarationManual = Ext.create('Koltiva.view.Mill.GridTracebilityDeclarationManual', {
        //                 viewVar: {
        //                     MillID: sm.get('id')
        //                 }
        //             });
        //         }else{
        //             //destroy, create ulang
        //             Ext.getCmp('Koltiva.view.Mill.GridTracebilityDeclarationManual').destroy();
        //             var GridTracebilityDeclarationManual = Ext.create('Koltiva.view.Mill.GridTracebilityDeclarationManual', {
        //                 viewVar: {
        //                     MillID: sm.get('id')
        //                 }
        //             });
        //         }
        //     }
        // },
        {
            icon: varjs.config.base_url + 'images/icons/new/delete.png',
            text: lang('Delete'),
            hidden: m_act_delete,
            handler: function(){
                var sm = Ext.getCmp('Koltiva.view.Mill.GridMainMill-gridMainGrid').getSelectionModel().getSelection()[0];

                Ext.MessageBox.confirm('Message', 'Do you want to delete this data ?', function(btn) {
                    if (btn == 'yes') {
                        Ext.Ajax.request({
                            waitMsg: 'Please Wait',
                            url: m_api + '/mill/mill_form',
                            method: 'DELETE',
                            params: {
                                MillID: sm.get('id')
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
                                Ext.getCmp('Koltiva.view.Mill.GridMainMill-gridMainGrid').getStore().load();
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
        }
    ]
    }); 
    
    var store_grid_report_locked    = Ext.create('Koltiva.store.Mill.GridReportLocked');
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
                                id: 'MillID',
                                name: 'MillID',
                                xtype: 'textfield',
                                fieldLabel: lang('MillID'),
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
                                            MillID: Ext.getCmp('MillID').getValue()
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

    function displayAddWindowLockedTransaction(MillID) {
        Ext.getCmp('MillID').setValue(MillID);
        if (!winLockedTrans.isVisible()) {
            store_grid_report_locked.getProxy().extraParams.MillID = MillID;
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
        localStorage.setItem('patchouli_mill_ls',
            JSON.stringify({
                ptextSearch: Ext.getCmp('Koltiva.view.Mill.GridMainMill-textSearch').getValue()
            })
        );
    }

    function submitOnEnterGridMill(field, event){
        if (event.getKey() == event.ENTER) {
            setFilterLs();
            Ext.getCmp('Koltiva.view.Mill.GridMainMill-gridMainGrid').getStore().loadPage(1);
        }
    }
// Define Variabel2 / Object2 / Fungsi yg diperlukan oleh view ini (end)

Ext.define('Koltiva.view.Mill.GridMainMill' ,{
    extend: 'Ext.panel.Panel',
    id: 'Koltiva.view.Mill.GridMainMill',
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
            Ext.getCmp('Koltiva.view.Mill.GridMainMill-gridMainGrid').getStore().load();
        }
    },
    style:'padding:0 15px 15px 15px;margin:5px 0 0 0;',
    initComponent: function() {
        var thisObj = this;
        
        //store
        var storeGridMain = Ext.create('Koltiva.store.Mill.GridMain');

        thisObj.items = [{
            layout: 'column',
            border: false,
            items: [{
                columnWidth: .3,
                layout: 'form',
                items:[{
                    xtype: 'button',
                    id: 'Koltiva.view.Mill.GridMainMill-btnListView',
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
                id: 'Koltiva.view.Mill.GridMainMill-gridInformation',
                html: ''
            }]
        },{
            xtype: 'grid',
            id: 'Koltiva.view.Mill.GridMainMill-gridMainGrid',
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
                id: 'Koltiva.view.Mill.GridMainMill-gridToolbar',
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
                        Ext.getCmp('Koltiva.view.Mill.GridMainMill').destroy(); //destory current view

                        //create object View untuk FormMainMill
                        if(Ext.getCmp('Koltiva.view.Mill.FormMainMill') == undefined){
                            var FormMainMill = Ext.create('Koltiva.view.Mill.FormMainMill', {
                                opsiDisplay: 'insert'
                            });
                        }else{
                            //destroy, create ulang
                            Ext.getCmp('Koltiva.view.Mill.FormMainMill').destroy();
                            var FormMainMill = Ext.create('Koltiva.view.Mill.FormMainMill', {
                                opsiDisplay: 'insert'
                            });
                        }
                    }
                },{
                    icon: varjs.config.base_url + 'images/icons/new/export.png', cls:'Sfr_BtnGridPaleBlue',
                    text: lang('Export'),
                    hidden: m_act_export,
                    handler: function() {                        
                        Ext.MessageBox.show({
                            msg: 'Please wait...',
                            progressText: 'Exporting...',
                            width: 300,
                            wait: true,
                            waitConfig: {
                                interval: 200
                            },
                            icon: 'ext-mb-download', //custom class in msg-box.html
                            animateTarget: 'mb7'
                        });

                        var filter = {};
                        filter.prov = m_ProvinceID;
                        filter.kab = m_DistrictID;
                        var keys = Object.keys(filter);
                        var param_string = '?search=1';
                        $.each(keys, function (index, val) {
                            param_string += '&' + val + '=' + filter[val];
                        });

                        try {
                            Ext.destroy(Ext.get('downloadIframe'));
                        } catch (e) {

                        }

                        Ext.Ajax.request({
                            url: m_api + '/mill/export_mill/' + param_string,

                            method: 'GET',
                            waitMsg: lang('Please Wait'),
                            timeout: 360000,
                            success: function (data) {
                                Ext.MessageBox.hide();
                                var jsonResp = JSON.parse(data.responseText);
                                window.location = jsonResp.filenya;
                            },
                            failure: function () {
                                Ext.MessageBox.hide();
                                Ext.MessageBox.show({
                                    title: 'Notifications',
                                    msg: 'Failed to export, Please try again.',
                                    buttons: Ext.MessageBox.OK,
                                    animateTarget: 'mb9',
                                    icon: 'ext-mb-error'
                                });
                            }
                        });
                    }
                },{
                    name: 'key', baseCls:'Sfr_TxtfieldSearchGrid',
                    id: 'Koltiva.view.Mill.GridMainMill-textSearch',
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
                        Ext.getCmp('Koltiva.view.Mill.GridMainMill-gridMainGrid').getStore().loadPage(1);
                    }
                },{
                    icon: varjs.config.base_url + 'images/icons/silk/grid.png', cls:'Sfr_BtnGridPaleBlue',
                    tooltip: lang('Custom Field Grid'),
                    handler: function() {
                        //display field grid
                        var winGridDisplay = Ext.create('Koltiva.view.Mill.WinGridDisplay');
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
                        var winAdvFilter = Ext.create('Koltiva.view.Mill.WinAdvancedFilter');
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
                        contextMenuGridMill.showAt(e.getXY());
                        if(sm == "Yes"){
                            contextMenuGridMill.getComponent('contextMenuGridMill.SetAsPartner').setVisible(false);
                            contextMenuGridMill.getComponent('contextMenuGridMill.PartnerOwner').setVisible(false);
                        }else{
                            contextMenuGridMill.getComponent('contextMenuGridMill.SetAsPartner').setVisible(true);
                            contextMenuGridMill.getComponent('contextMenuGridMill.PartnerOwner').setVisible(true);
                        }
                    }
                }]
            },{
                text: lang('ID'),
                dataIndex: 'id',
                hidden:true
            },{
                id: 'Koltiva.view.Mill.GridMainMill-colid',
                text: lang('ID'),
                dataIndex: 'MillDisplayID',
                width: '7%'
            }, {
                id: 'Koltiva.view.Mill.GridMainMill-colName',
                text: lang('Name'),
                width: '25%',
                dataIndex: 'Name'
            },{
                id: 'Koltiva.view.Mill.GridMainMill-colCompanyName',
                text: lang('Company Name'),
                width: '15%',
                dataIndex: 'CompanyName'
            }, {
                id: 'Koltiva.view.Mill.GridMainMill-colMillSMEName',
                text: lang('SME Name'),
                width: '10%',
                dataIndex: 'SMEName'
            }, {
                id: 'Koltiva.view.Mill.GridMainMill-colMillNrPlantation',
                text: lang('Nr Of Plantattion'),
                width: '10%',
                dataIndex: 'NrPlantation'
            }, {
                id: 'Koltiva.view.Mill.GridMainMill-colMillNrFarmer',
                text: lang('Nr Of Farmer'),
                width: '10%',
                dataIndex: 'NrFarmer'
            }, {
                id: 'Koltiva.view.Mill.GridMainMill-colMillGPS',
                text: lang('Coordinate'),
                width: '10%',
                dataIndex: 'GPS'
            }, {
                id: 'Koltiva.view.Mill.GridMainMill-colMillGroupName',
                text: lang('Mill Group'),
                width: '10%',
                dataIndex: 'GroupName'
            },{
                id: 'Koltiva.view.Mill.GridMainMill-colAlias',
                text: lang('Alias'),
                width: '15%',
                dataIndex: 'Alias',
                hidden: true
            },{
                id: 'Koltiva.view.Mill.GridMainMill-colAddress',
                text: lang('Address'),
                width: '15%',
                dataIndex: 'Address',
                hidden: true
            },{
                id: 'Koltiva.view.Mill.GridMainMill-colProvince',
                text: lang('Province'),
                width: '15%',
                dataIndex: 'Province',
                hidden: true
            },{
                id: 'Koltiva.view.Mill.GridMainMill-colDistrict',
                text: lang('District'),
                width: '15%',
                dataIndex: 'District',
                hidden: true
            },{
                id: 'Koltiva.view.Mill.GridMainMill-colKecamatan',
                text: lang('Kecamatan'),
                width: '15%',
                dataIndex: 'Kecamatan'
            },{
                id: 'Koltiva.view.Mill.GridMainMill-colDesa',
                text: lang('Desa'),
                width: '15%',
                dataIndex: 'Desa'
            },{
                id: 'Koltiva.view.Mill.GridMainMill-colStatusPerusahaan',
                text: lang('Status Perusahaan'),
                width: '11%',
                dataIndex: 'StatusPerusahaan'
            },{
                id: 'Koltiva.view.Mill.GridMainMill-colTahunTerbentuk',
                text: lang('Tahun Terbentuk'),
                width: '8%',
                dataIndex: 'TahunTerbentuk'
            },{
                id: 'Koltiva.view.Mill.GridMainMill-colPhone',
                text: lang('Phone'),
                width: '15%',
                dataIndex: 'Phone',
                hidden: true
            },{
                id: 'Koltiva.view.Mill.GridMainMill-colTotalPermanentEmployee',
                text: lang('Total Permanent Employee'),
                width: '15%',
                dataIndex: 'TotalPermanentEmployee',
                hidden: true
            },{
                id: 'Koltiva.view.Mill.GridMainMill-colLastUpdated',
                text: lang('Last Updated'),
                width: '12%',
                dataIndex: 'LastUpdated'
            }]
        }];

        this.callParent(arguments);
    }
});