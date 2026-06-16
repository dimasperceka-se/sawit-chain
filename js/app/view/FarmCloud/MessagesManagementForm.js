/*
* @Author: Fashah Darullah
* @Date:   2019-06-12 11:19:19
*/

/*
    Param2 yg diperlukan ketika load View ini
    1. opsiDisplay
    2. viewVar
*/

Ext.define('Koltiva.view.FarmCloud.MessagesManagementForm' ,{
    extend: 'Ext.panel.Panel',
    id: 'Koltiva.view.FarmCloud.MessagesManagementForm',
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
    listeners: {
        afterRender: function(){
            var thisObj = this;

            //Div nya Filter Region
            document.getElementById('divCommonContentRegion').style.display = 'none';
            // document.getElementById('main-breadcrumb').style.display = 'none';

        	if(thisObj.viewVar.opsiDisplay == 'insert'){
        		//form reset
                Ext.getCmp('Koltiva.view.FarmCloud.MessageManagementForm-Form').getForm().reset();
            }
            
            if(thisObj.viewVar.opsiDisplay == 'update'){
                Ext.getCmp('Koltiva.view.FarmCloud.MessageManagementForm-Form').getForm().load({
                    url: m_api + '/farmcloud/messages',
                    method: 'GET',
                    params: {
                        MessagesID: thisObj.viewVar.MessagesID
                    },
                    success: function(form, action) {
                        Ext.MessageBox.hide();
                        var r = Ext.decode(action.response.responseText);
                        Ext.getCmp('SelectEditorData').setValue(decodeURI(r.data.ContentEncode));
                    },
                    failure: function(form, action) {
                        Ext.MessageBox.hide();
                        Ext.MessageBox.show({
                            title: lang('Failed'),
                            msg: lang('Failed to retrieve data'),
                            buttons: Ext.MessageBox.OK,
                            animateTarget: 'mb9',
                            icon: 'ext-mb-error'
                        });
                    }
                });
            }
        },
        beforerender: function () {
            var thisObj = this;

            if (thisObj.viewVar.opsiDisplay != 'insert') {
                Ext.MessageBox.show({
                    msg: 'Please wait...',
                    progressText: 'Loading...',
                    width: 300,
                    wait: true,
                    waitConfig: {
                        interval: 200
                    },
                    icon: 'ext-mb-info', //custom class in msg-box.html
                    animateTarget: 'mb9'
                });
            }
        }
    },
    initComponent: function() {
        var thisObj = this;

        //Store yg dipakai =============================================================== (Begin)
        var StoreCmbPartner = Ext.create('Koltiva.store.ComboGeneral.CmbPartnerCommon');
        var storeGridContact = Ext.create('Ext.data.Store', {
            extend: 'Ext.data.Model',
            fields: ['PersonName','PersonExtID','GroupName','Email'],
            autoLoad: true,
            proxy: {
                type: 'ajax',
                url: m_api_base_url+'/farmcloud/get_contact_list_messages',
                extraParams: {MessagesID: thisObj.viewVar.MessagesID},
                reader: {
                    type: 'json',
                    root: 'data',
                    totalProperty: 'total'
                }
            }
        });
        //Store yg dipakai =============================================================== (End)

        //Panel Basic ==================================== (Begin)
        thisObj.ObjPanelBasicData = Ext.create('Ext.form.Panel',{
            title: lang('Message Management'),
            frame: true,
            cls: 'Sfr_PanelLayoutForm',
            id: 'Koltiva.view.FarmCloud.MessageManagementForm-Form',
            fileUpload: true,
            collapsible:true,
		    buttonAlign : 'center',
            items: [{
                layout: 'column',
                border: false,
                padding:10,
                items:[{
                    columnWidth: 1,
                    layout:'form',
                    cls: 'Sfr_PanelLayoutFormContainer',
                    items:[{
                        xtype: 'panel',
                        deferredRender: false,
                        flex: 1,
                        activeTab: 0,
                        plain: true,
                        cls:'tabSce',
                        id: 'Koltiva.view.FarmCloud.MessageManagementForm-Form-Tab',
                        items:[{
                            layout: 'column',
                            border: false,
                            items:[
                                {
                                    xtype : 'container',
                                    columnWidth: 0.495,
                                    layout:'form',
                                    style:'border-right:1px dashed gray;padding-right:25px;',
                                    items:[
                                        {
                                            xtype: 'textfield',
                                            id: 'Koltiva.view.FarmCloud.MessageManagementForm-Form-FarmertypeID',
                                            name: 'MessagesID',
                                            fieldLabel: lang('Message ID'),
                                            queryMode: 'local',
                                            allowBlank: true,
                                            valueField: 'id',
                                            readOnly: true,
                                            hidden:true
                                        },
                                        {
                                            xtype: 'textfield',
                                            id: 'Koltiva.view.FarmCloud.MessageManagementForm-Form-Title',
                                            name: 'Title',
                                            fieldLabel: lang('Title'),
                                            labelAlign:'top',
                                            allowBlank: false,
                                            baseCls: 'Sfr_FormInputMandatory'
                                        },{html:'<div style="height:3px;">&nbsp;</div>'},
                                        {
                                            xtype: 'ckeditor', 
                                            id: 'SelectEditorData',
                                            fieldLabel: lang('Content'),
                                            labelAlign:'top',
                                            name: 'Content', 
                                            CKConfig: { 
                                                //Enter your CKEditor config paramaters here or define a custom CKEditor config file.
                                                toolbar: 'Basic',
                                                height : 200,
                                                width: '100%'
                                            }
                                        }
                                        ,{html:'<div style="padding-bottom:200px;">&nbsp;</div>'}
                                    ]
                                },
                                {
                                    columnWidth: 0.5,
                                    layout:'form',
                                    style:'padding:10px 15px 10px 20px;border-left:1px dashed gray;',
                                    defaults: {
                                        labelAlign: 'left',
                                        labelWidth: 150
                                    },
                                    items:[
                                        {
                                            fieldLabel: lang('Status Type'),
                                            labelAlign:'top',
                                            xtype: 'radiogroup',                        
                                            id:'Koltiva.view.FarmCloud.MessageManagementForm-Form-RowStatusType',                        
                                            columns: 3,
                                            allowBlank: false,
                                            msgTarget: 'under',
                                            items:[{
                                                boxLabel: 'Public',
                                                name: 'StatusType',
                                                inputValue: 'public',
                                                id: 'Koltiva.view.FarmCloud.MessageManagementForm-Form-StatusTypePublic',
                                                listeners:{
                                                    change: function(){
                                                        return false;
                                                    }
                                                }
                                            },{
                                                boxLabel: 'Private',
                                                name: 'StatusType',
                                                inputValue: 'private',
                                                id: 'Koltiva.view.FarmCloud.MessageManagementForm-Form-StatusTypePrivate',
                                                listeners:{
                                                    change: function(){
                                                        if(this.checked == true){
                                                            Ext.getCmp('Koltiva.view.FarmCloud.MessageManagementForm-Form-PartnerIDImplode').setVisible(true);
                                                            Ext.getCmp('Koltiva.view.FarmCloud.MessageManagementForm-Form-RowRoleAccess').setVisible(true);
                                                        }else{
                                                            Ext.getCmp('Koltiva.view.FarmCloud.MessageManagementForm-Form-PartnerIDImplode').setVisible(false);
                                                            Ext.getCmp('Koltiva.view.FarmCloud.MessageManagementForm-Form-RowRoleAccess').setVisible(false);
                                                        }
                    
                                                        //Scroll lagi kebawah
                                                        setTimeout(function(){
                                                            var d = Ext.getCmp('Koltiva.view.FarmCloud.MessageManagementForm-Form').body.dom;
                                                            d.scrollTop = d.scrollHeight - d.offsetHeight;
                                                        }, 500);
                    
                                                        return false;
                                                    }
                                                }
                                            },{
                                                boxLabel: 'Individu',
                                                name: 'StatusType',
                                                inputValue: 'individu',
                                                id: 'Koltiva.view.FarmCloud.MessageManagementForm-Form-StatusTypeIndividu',
                                                listeners:{
                                                    change: function(){
                                                        if(this.checked == true){
                                                            Ext.getCmp('Koltiva.view.FarmCloud.MessageManagementForm-Form-gridContact').setVisible(true);
                                                        }else{
                                                            Ext.getCmp('Koltiva.view.FarmCloud.MessageManagementForm-Form-gridContact').setVisible(false);
                                                        }
                    
                                                        //Scroll lagi kebawah
                                                        setTimeout(function(){
                                                            var d = Ext.getCmp('Koltiva.view.FarmCloud.MessageManagementForm-Form-gridContact').body.dom;
                                                            d.scrollTop = d.scrollHeight - d.offsetHeight;
                                                        }, 500);
                    
                                                        return false;
                                                    }
                                                }
                                            }]
                                        },{html:'<div style="height:3px;">&nbsp;</div>'},
                                        {
                                            layout: 'column',
                                            id: 'Koltiva.view.FarmCloud.MessageManagementForm-Form-gridContact',
                                            border: false,
                                            hidden: true,
                                            items:[{
                                                columnWidth: 1,
                                                style:'padding-right:0px;',
                                                layout:'form',
                                                items:[{
                                                    title: 'List of Contacts',
                                                    xtype: 'grid',
                                                    id: 'Koltiva.view.FarmCloud.MessageManagementForm-gridContact',
                                                    loadMask: true,
                                                    store: storeGridContact,                                                    
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
                                                            text: lang('Add New Recipient'),
                                                            handler: function() {
                                                                $('#loader-ext').show();
                                                                setTimeout(function(){
                                                                    $('#loader-ext').hide();
                                                                    var winFormSelectContact = Ext.create('Koltiva.view.FarmCloud.FormSelectContact');

                                                                    if (!winFormSelectContact.isVisible()) {
                                                                        winFormSelectContact.center();
                                                                        winFormSelectContact.show();
                                                                    } else {
                                                                        winFormSelectContact.close();
                                                                    }
                                                                }, 10);
                                                            }
                                                        }]
                                                    }],
                                                    columns: [{
                                                        text: lang('ID'),
                                                        dataIndex: 'PersonExtID',
                                                        hidden:true
                                                    },{
                                                        text: lang('Name'),
                                                        dataIndex: 'PersonName',
                                                        width: '30%'
                                                    },{
                                                        text: lang('Group Name'),
                                                        dataIndex: 'GroupName',
                                                        width: '35%'
                                                    },{
                                                        text: lang('Email'),
                                                        dataIndex: 'Email',
                                                        width: '35%'
                                                    }],
                                                    listeners: {
                                                        itemclick: function( elm, record, item, index, e, eOpts ) {
                                                            if(record.data.Email == null){
                                                                Ext.MessageBox.show({
                                                                    title: lang('Error'),
                                                                    msg: 'Please add email first to this contact data',
                                                                    buttons: Ext.MessageBox.OK,
                                                                    animateTarget: 'mb9',
                                                                    icon: 'ext-mb-error'
                                                                });
                                                            }
                                                        }
                                                    }
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
                                        },{html:'<div style="height:3px;">&nbsp;</div>'},
                                        {
                                            xtype: 'itemselector',
                                            flex:true,
                                            labelAlign:'top',
                                            id: 'Koltiva.view.FarmCloud.MessageManagementForm-Form-PartnerIDImplode',
                                            name: 'PartnerIDImplode',
                                            fieldLabel: lang('Partner Access'),
                                            fromTitle: lang('Available'),
                                            toTitle: lang('Selected'),
                                            anchor: '100%',
                                            height:300,
                                            hidden:true,
                                            store: StoreCmbPartner,
                                            displayField: 'label',
                                            valueField: 'id'
                                        },{
                                            html:'<div style="height:3px;"></div>',
                                        },{
                                            fieldLabel: lang('Role Access'),
                                            xtype: 'checkboxgroup',                        
                                            id:'Koltiva.view.FarmCloud.MessageManagementForm-Form-RowRoleAccess',                        
                                            hidden:true,
                                            columns: 3,
                                            items:[{
                                                boxLabel: lang('Farmer'),
                                                name: 'RoleAccessFarmer',
                                                inputValue: '1',
                                                id: 'Koltiva.view.FarmCloud.MessageManagementForm-Form-RoleAccessFarmer',
                                                listeners:{
                                                    change: function(){
                                                        return false;
                                                    }
                                                }
                                            },{
                                                boxLabel: lang('Trader'),
                                                name: 'RoleAccessTrader',
                                                inputValue: '1',
                                                id: 'Koltiva.view.FarmCloud.MessageManagementForm-Form-RoleAccessTrader',
                                                listeners:{
                                                    change: function(){
                                                        return false;
                                                    }
                                                }
                                            },{
                                                boxLabel: lang('Staff'),
                                                name: 'RoleAccessStaff',
                                                inputValue: '1',
                                                id: 'Koltiva.view.FarmCloud.MessageManagementForm-Form-RoleAccessStaff',
                                                listeners:{
                                                    change: function(){
                                                        return false;
                                                    }
                                                }
                                            }]
                                        }
                                    ]
                                }
                            ]
                        }],
                        listeners: {
                            
                        }
                    }]
                }],
                buttons: [{
                    xtype: 'button',
                    icon: varjs.config.base_url + 'images/icons/new/save.png',
                    text: lang('Save'),
                    cls: 'Sfr_BtnFormBlue',
                    overCls: 'Sfr_BtnFormBlue-Hover',
                    id: 'Koltiva.view.FarmCloud.MessageManagementForm-Form-BtnSave',
                    handler: function () {
                        var Formnya = Ext.getCmp('Koltiva.view.FarmCloud.MessageManagementForm-Form').getForm();

                        if (Formnya.isValid()) {
                            var Content = Ext.ComponentQuery.query('textfield[name=Content]')[0].getValue();
                            Ext.getCmp('ContentCKEditor').setValue(encodeURI(Content));
                            var gridContact = Ext.getCmp('Koltiva.view.FarmCloud.MessageManagementForm-gridContact');
                            var selected    = gridContact.getSelectionModel().getSelection();
                            
                            var contactList=[];
                            Ext.each(selected, function (item) {
                                contactList.push(item.data);
                            });

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
                                url: m_api + '/farmcloud/messages',
                                method: 'POST',
                                waitMsg: 'Saving data...',
                                params: {
                                    opsiDisplay: thisObj.viewVar.opsiDisplay
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
                                                Ext.getCmp('Koltiva.view.FarmCloud.MessageManagementForm-Form').destroy(); //destory current view
                                                var MainForm = [];
                                                if (Ext.getCmp('Koltiva.view.FarmCloud.MessagesManagementGrid') == undefined) {
                                                    MainForm = Ext.create('Koltiva.view.FarmCloud.MessagesManagementGrid');
                                                } else {
                                                    Ext.getCmp('Koltiva.view.FarmCloud.MessagesManagementGrid').destroy();
                                                    MainForm = Ext.create('Koltiva.view.FarmCloud.MessagesManagementGrid');
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
        //Panel Basic ==================================== (End)

        //========================================================== LAYOUT UTAMA (Begin) ========================================//
        thisObj.items = [{
            xtype: 'panel',
            border:false,
            layout:{
                type:'hbox'
            },
            items:[{
                id: 'Koltiva.view.Grower.FormMainGrower-labelInfoInsert',
                html:'<div id="header_title_farmer">'+lang('Message Management')+'</div>'
            }]
        },{
            items:[{
                id: 'Koltiva.view.Grower.FormMainGrower-LinkBackToList',
                html:'<div id="Sfr_IdBoxInfoDataGrid" class="Sfr_BoxInfoDataGrid"><ul class="Sft_UlListInfoDataGrid"><li class="Sft_ListInfoDataGrid"><a href="javascript:Ext.getCmp(\'Koltiva.view.FarmCloud.MessagesManagementForm\').BackToList()"><img class="Sft_ListIconInfoDataGrid" src="'+varjs.config.base_url+'images/icons/new/back.png" width="20" />&nbsp;&nbsp;'+lang('Back to Messages Management List')+'</a></li></div>'
            }]
        },{
            html:'<br />'
        },{
            layout: 'column',
            border: false,
            items: [{
                //LEFT CONTENT
                columnWidth: 1,
                items:[
                    thisObj.ObjPanelBasicData
                ]
            }]
        }];
        //========================================================== LAYOUT UTAMA (END) ========================================//

        this.callParent(arguments);
    },
    BackToList: function(){
        Ext.getCmp('Koltiva.view.FarmCloud.MessagesManagementForm').destroy(); //destory current view
        var GridMainGrower = [];
        if(Ext.getCmp('Koltiva.view.FarmCloud.MessagesManagementGrid') == undefined){
            GridMainGrower = Ext.create('Koltiva.view.FarmCloud.MessagesManagementGrid');
        }else{
            //destroy, create ulang
            Ext.getCmp('Koltiva.view.FarmCloud.MessagesManagementGrid').destroy();
            GridMainGrower = Ext.create('Koltiva.view.FarmCloud.MessagesManagementGrid');
        }
    }
});