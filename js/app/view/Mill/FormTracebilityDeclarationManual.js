/*
* @Author: nikolius
* @Date:   2017-08-21 10:19:23
* @Last Modified by:   nikolius
* @Last Modified time: 2018-01-15 17:18:34
*/
/*
    Param2 yg diperlukan ketika load View ini
    1. opsiDisplay
    2. viewVar (MillID)
*/

Ext.define('Koltiva.view.Mill.FormTracebilityDeclarationManual' ,{
    extend: 'Ext.panel.Panel',
    id: 'Koltiva.view.Mill.FormTracebilityDeclarationManual',
    style:'padding:0 15px 15px 15px;margin:12px 0 0 0;',
    opsiDisplay: false,
    setOpsiDisplay: function(value){
        this.opsiDisplay = value;
    },
    viewVar: false,
    setViewVar: function(value){
        this.viewVar = value;
    },
    renderTo: 'ext-content',
    initComponent: function() {
        var thisObj = this;

        //Tracebility Declaration Document - Tracebility to Plantation ================= (Begin)
        var StoreGridSupplier = Ext.create('Ext.data.Store', {
            extend: 'Ext.data.Model',
            fields: ['SupplierName','id','KategoriKebunName','KategoriKebun','FFBSupply','Tracebility'],
            autoLoad: true,
            proxy: {
                type: 'ajax',
                url: m_api_base_url+'/mill/get_supplier_list',
                extraParams: {MillTCDID: thisObj.MillTCDID},
                reader: {
                    type: 'json',
                    root: 'data',
                    totalProperty: 'total'
                }
            }
        });

        //Tracebility Declaration Document - Tracebility to Plantation ================= (End) 


        var objTracebiltyDeclaration = Ext.create('Ext.form.Panel',{
            title: lang('Tracebility Declaration'),
            frame: true,
            id: 'Koltiva.view.Mill.FormTracebilityDeclarationManual-FormTracebiltyDeclaration',
            fileUpload: true,
            margin:'0 0 20 0',
            items: [{
                layout: 'column',
                border: false,
                padding:5,
                items:[{
                    columnWidth: 1,
                    layout:'form',
                    items:[{
                        layout: 'column',
                        border: false,
                        items:[{
                            columnWidth: 1,
                            layout:'form',
                            style:'padding-right:25px;',
                            items:[{
                                xtype: 'hiddenfield',
                                id: 'MillTCDID',
                                name: 'MillTCDID'
                            },{
                                xtype: 'textfield',
                                id: 'MillTCDName',
                                name: 'MillTCDName',
                                fieldLabel: lang('Traceability Declaration Name'),
                                allowBlank: false,
                                labelWidth: 250
                            }]
                        }]
                    },{
                        layout: 'column',
                        id: 'Koltiva.view.Mill.FormTracebilityDeclarationManual-Form-gridSupplier',
                        border: false,
                        items:[{
                            columnWidth: 1,
                            style:'padding-right:0px;',
                            layout:'form',
                            items:[{
                                title: 'List of Contacts',
                                xtype: 'grid',
                                id: 'Koltiva.view.Mill.FormTracebilityDeclarationManual-gridSupplier',
                                loadMask: true,
                                store: StoreGridSupplier,                                                    
                                viewConfig: {
                                    deferEmptyText: false,
                                    emptyText: lang('No contact available')
                                },
                                minHeight:125,
                                selType: 'checkboxmodel',
                                checked:true,
                                dockedItems: [{
                                    xtype: 'tbtext',
                                    style:'font-weight:bold;text-decoration:underline;',
                                    text: ''
                                },{
                                    xtype:'tbspacer',
                                    flex:1
                                },{
                                    xtype: 'toolbar',
                                    dock:'top',
                                    items: [{
                                        icon: varjs.config.base_url + 'images/icons/new/add.png', cls:'Sfr_BtnGridGreen', overCls:'Sfr_BtnGridGreen-Hover',
                                        text: lang('Add New Supplier'),
                                        handler: function() {
                                            $('#loader-ext').show();
                                            setTimeout(function(){
                                                $('#loader-ext').hide();
                                                var winFromAddSupplier = Ext.create('Koltiva.view.Mill.FormAddSupplier');
    
                                                if (!winFromAddSupplier.isVisible()) {
                                                    winFromAddSupplier.center();
                                                    winFromAddSupplier.show();
                                                } else {
                                                    winFromAddSupplier.close();
                                                }
                                            }, 10);
                                        }
                                    }]
                                }],
                                columns: [{
                                    text: lang('ID'),
                                    dataIndex: 'id',
                                    hidden:true
                                },{
                                    text: lang('Nama Supplier'),
                                    dataIndex: 'SupplierName',
                                    width: '20%'
                                },{
                                    text: lang('Kategori Kebun'),
                                    dataIndex: 'KategoriKebunName',
                                    width: '20%'
                                },{
                                    text: lang('Kategori ID'),
                                    dataIndex: 'KategoriKebun',
                                    width: '20%',
                                    hidden:true
                                },{
                                    text: lang('FFB Supply (Ton)'),
                                    dataIndex: 'FFBSupply',
                                    width: '20%'
                                },{
                                    text: lang('Tracebility'),
                                    dataIndex: 'Tracebility',
                                    width: '20%'
                                }]
                            },{
                                xtype: 'hiddenfield',
                                id: 'ListRecipient',
                                name: 'ContactID'
                            },{
                                xtype: 'hiddenfield',
                                id: 'ListRecipientTemp',
                                name: 'RecipientListID'
                            },{
                                xtype: 'hiddenfield',
                                id: 'ContentCKEditor',
                                name: 'ContentCKEditor'
                            }]
                        }]
                    }]
                }],
                buttons: [{
                    xtype: 'button',
                    icon: varjs.config.base_url + 'images/icons/new/save.png',
                    text: lang('Save'),
                    cls: 'Sfr_BtnFormBlue',
                    overCls: 'Sfr_BtnFormBlue-Hover',
                    id: 'Koltiva.view.Mill.FormTracebilityDeclarationManual-FormTracebiltyDeclaration-BtnSave',
                    handler: function () {
                        var Formnya = Ext.getCmp('Koltiva.view.Mill.FormTracebilityDeclarationManual-FormTracebiltyDeclaration').getForm();

                        if (Formnya.isValid()) {
                            var gridContact = Ext.getCmp('Koltiva.view.Mill.FormTracebilityDeclarationManual-gridSupplier');
                            var selected    = gridContact.getSelectionModel().getSelection();
                            
                            var contactList=[];
                            Ext.each(selected, function (item) {
                                contactList.push(item.data);
                            });

                            if(contactList.length == 0){
                                Ext.MessageBox.show({
                                    title: 'Attention',
                                    msg: lang('Please Select Supplier'),
                                    buttons: Ext.MessageBox.OK,
                                    animateTarget: 'mb9',
                                    icon: 'ext-mb-info'
                                });
                                return;
                            }

                            Ext.getCmp('ListRecipient').setValue(Ext.encode(contactList));

                            var draftRecipientList=[];

                            var draftRecipients = gridContact.getStore().data.items;
                            if(draftRecipients.length > 0){
                                Ext.each(draftRecipients, function (item) {
                                    draftRecipientList.push(item.data);
                                });
                                Ext.getCmp('ListRecipientTemp').setValue(Ext.encode(draftRecipientList));
                            }
                            Formnya.submit({
                                url: m_api + '/mill/submit_tc_declaration',
                                method: 'POST',
                                waitMsg: 'Saving data...',
                                params: {
                                    opsiDisplay : thisObj.opsiDisplay,
                                    MillID      : thisObj.MillID
                                },
                                success: function (fp, o) {
                                    Ext.MessageBox.show({
                                        title: 'Information',
                                        msg: 'Data Saved',
                                        buttons: Ext.MessageBox.OK,
                                        animateTarget: 'mb9',
                                        icon: 'ext-mb-success',
                                        fn: function (btn) {
                                            if (btn == 'ok') {
                                                Ext.getCmp('Koltiva.view.Mill.FormTracebilityDeclarationManual').destroy(); //destory current view
                                                var MainForm = [];
                                                if (Ext.getCmp('Koltiva.view.Mill.GridTracebilityDeclarationManual') == undefined) {
                                                    MainForm = Ext.create('Koltiva.view.Mill.GridTracebilityDeclarationManual');
                                                } else {
                                                    Ext.getCmp('Koltiva.view.Mill.GridTracebilityDeclarationManual').destroy();
                                                    MainForm = Ext.create('Koltiva.view.Mill.GridTracebilityDeclarationManual');
                                                }
                                            }
                                        }
                                    });
                                },
                                failure: function (fp, o) {
                                    Ext.MessageBox.show({
                                        title: 'Fail',
                                        msg: 'Connection Error',
                                        buttons: Ext.MessageBox.OK,
                                        animateTarget: 'mb9',
                                        icon: 'ext-mb-error'
                                    });
                                }
                            });

                        } else {
                            Ext.MessageBox.show({
                                title: 'Attention',
                                msg: lang('Form not complete yet'),
                                buttons: Ext.MessageBox.OK,
                                animateTarget: 'mb9',
                                icon: 'ext-mb-info'
                            });
                        }
                    }
                }]
            }]
        });


        //======================== LAYOUT UTAMA (Begin) =========================//
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
                  '&nbsp;&nbsp;' + lang('Back to Tracebilitry Declaration List')  + '</a></li></ul></div>',
            listeners: {
                click: {
                    element: 'el',
                    preventDefault: true,
                    fn: function(e, target){
                        Ext.getCmp('Koltiva.view.Mill.FormTracebilityDeclarationManual').destroy(); //destory current view
                        var GridMainFarcan = [];

                        if(Ext.getCmp('Koltiva.view.Mill.GridTracebilityDeclarationManual') == undefined){
                            GridMainFarcan = Ext.create('Koltiva.view.Mill.GridTracebilityDeclarationManual');
                        }else{
                            //destroy, create ulang
                            Ext.getCmp('Koltiva.view.Mill.GridTracebilityDeclarationManual').destroy();
                            GridMainFarcan = Ext.create('Koltiva.view.Mill.GridTracebilityDeclarationManual');
                        }
                    }
                }
            }
        },{
            layout: 'column',
            border: false,
            items: [{
                //LEFT CONTENT
                columnWidth: 0.5,
                items:[
                    objTracebiltyDeclaration
                ]
            }]
        }];

        //======================== LAYOUT UTAMA (End)   =========================//

        this.callParent(arguments);
    },
    listeners: {
        afterRender: function(){
            var thisObj = this;
            //update
            if(thisObj.opsiDisplay == 'update'){
                //load data form
                Ext.getCmp('Koltiva.view.Mill.FormTracebilityDeclarationManual-FormTracebiltyDeclaration').getForm().load({
                    url: m_api + '/mill/form_tc_declaration',
                    method: 'GET',
                    params: {
                        MillID: thisObj.MillID,
                        MillTCDID: thisObj.MillTCDID
                    },
                    success: function(form, action) {
                        var r = Ext.decode(action.response.responseText);
                    },
                    failure: function(form, action) {
                        Ext.MessageBox.show({
                            title: 'Failed',
                            msg: 'Failed to retrieve data',
                            buttons: Ext.MessageBox.OK,
                            animateTarget: 'mb9',
                            icon: 'ext-mb-error'
                        });
                    }
                });


            }
        }
    }
});