Ext.define('Koltiva.view.Partner.MainFormNew', {
    extend: 'Ext.panel.Panel',
    id: 'Koltiva.view.Partner.MainFormNew',
    style: 'padding:0 15px 15px 15px;margin:5px 0 0 0;',
    viewVar: false,
    setViewVar: function (value) {
        this.viewVar = value;
    },
    renderTo: 'ext-content',
    listeners: {
        afterRender: function () {
            var thisObj = this;

            
            // remove local storage group_access_area
            localStorage.removeItem("appkolti_group_access_area");
            localStorage.removeItem("appkolti_group_access_unit");

            // init local storage group_access_area, biar tdk error ketika dipanggil
            localStorage.setItem('appkolti_group_access_area', JSON.stringify({
                itemAdded: [],
                itemDeleted: []
            }));

            if (thisObj.viewVar.OpsiDisplay == 'view' || thisObj.viewVar.OpsiDisplay == 'update') {

                if (thisObj.viewVar.OpsiDisplay == 'view') {
                    Ext.getCmp('Koltiva.view.Partner.MainFormNew-FormBasicData-BtnSave').hide();
                    Ext.getCmp('Koltiva.view.Partner.MainFormNew-FormBasicData-LogoInput').hide();
                }

                Ext.getCmp('Koltiva.view.Partner.MainFormNew-FormBasicData').getForm().load({
                    url: m_api + '/partner_new/partner_basic_data_form',
                    method: 'GET',
                    params: {
                        PartnerID: this.viewVar.PartnerID
                    },
                    success: function (form, action) {
                        Ext.MessageBox.hide();
                        let r = Ext.decode(action.response.responseText);

                        if (r.data.Logo != null) {
                            Ext.getCmp('Koltiva.view.Partner.MainFormNew-FormBasicData-Logo').update('<a href="' + m_url_awss3 + '/' + r.data.Logo + '" data-lightbox="image-1" data-title="Logo" title="Logo"><img src="' + m_url_awss3 + '/' + r.data.Logo + '" style="height:150px;margin:0px 5px 5px 203px;float:left;" /></a>');
                        } else {
                            Ext.getCmp('Koltiva.view.Partner.MainFormNew-FormBasicData-Logo').update('<img src="' + m_api_base_url + '/assets/images/default-partner.png" style="height:150px;margin:0px 5px 5px 203px;float:left;" />');
                        }

                        //Title
                        Ext.getCmp('Koltiva.view.Partner.MainFormNew-labelInfoInsert').update('<div id="header_title_farmer">' + Ext.getCmp('Koltiva.view.Partner.MainFormNew-FormBasicData-PartnerName').getValue() + '</div>');
                        Ext.getCmp('Koltiva.view.Partner.MainFormNew-labelInfoInsert').doLayout();
                    },
                    failure: function (form, action) {
                        Ext.MessageBox.hide();
                        Ext.MessageBox.show({
                            title: 'Failed',
                            msg: 'Failed to retrieve data',
                            buttons: Ext.MessageBox.OK,
                            animateTarget: 'mb9',
                            icon: 'ext-mb-error'
                        });
                    }
                });
            } else {
                Ext.getCmp('Koltiva.view.Partner.MainFormNew-FormBasicData-SetasParentNo').setValue(true)
            }
        },
        beforerender: function () {
            var thisObj = this;
            if (thisObj.viewVar.OpsiDisplay != 'insert') {
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
    initComponent: function () {
        var thisObj = this;

        thisObj.ObjDinamis   = [];
        thisObj.ObjRegion    = {};
        thisObj.ObjCommodity = [];

        //Store yg dipakai =============================================================== (Begin)
        thisObj.OrganizationType      = Ext.create('Koltiva.store.Partner.StoreOrganizationType');
        thisObj.StoreCommodityOptions = Ext.create('Koltiva.store.Partner.StoreCommodityOptions');
        thisObj.StorePartnerID        = Ext.create('Koltiva.store.ComboGeneral.CmbPartner');
        thisObj.storeGridGroupAccessArea = Ext.create('Koltiva.store.Partner.MainGridPanelRegion', {
            storeVar: {
                PartnerID: thisObj.viewVar.PartnerID
            }
        });
        //Store yg dipakai =============================================================== (End)

        thisObj.itemSelectorCommodityOptions = Ext.create("Ext.ux.form.ItemSelector", {
            anchor: '100%',
            fieldLabel: lang('Commodity Options'),
            imagePath: '../ux/images/',
            id:'Koltiva.view.Partner.MainFormNew-FormBasicData-CommodityOptions',
            name:'Koltiva.view.Partner.MainFormNew-FormBasicData-CommodityOptions',
            store: thisObj.StoreCommodityOptions,
            displayField: 'label',
            valueField: 'id',
            allowBlank: false,
            msgTarget: 'side',
            fromTitle: lang('Available'),
            toTitle: lang('Selected'),
            height:300,
            labelAlign: 200,
            labelWidth: 200,
            fieldStyle: 'text-align: left;',
            padding :'0 0 10 5',
            baseCls: 'Sfr_FormInputMandatory' 
        });

       /* thisObj.itemSelectorInternalProgram = Ext.create("Ext.ux.form.ItemSelector", {
            anchor: '100%',
            fieldLabel: lang('Internal Program'),
            imagePath: '../ux/images/',
            id:'Koltiva.view.Partner.MainFormNew-FormBasicData-InternalProgram',
            name:'Koltiva.view.Partner.MainFormNew-FormBasicData-InternalProgram',
            store: thisObj.StoreCommodityOptions,
            displayField: 'label',
            valueField: 'id',
            allowBlank: false,
            msgTarget: 'side',
            fromTitle: lang('Available'),
            toTitle: lang('Selected'),
            height:300,
            labelAlign: 200,
            labelWidth: 200,
            fieldStyle: 'text-align: left;',
            padding :'0 0 10 5',
            baseCls: 'Sfr_FormInputMandatory' 
        });

        thisObj.itemSelectorExternalProgram = Ext.create("Ext.ux.form.ItemSelector", {
            anchor: '100%',
            fieldLabel: lang('External Program'),
            imagePath: '../ux/images/',
            id:'Koltiva.view.Partner.MainFormNew-FormBasicData-ExternalProgram',
            name:'Koltiva.view.Partner.MainFormNew-FormBasicData-ExternalProgram',
            store: thisObj.StoreCommodityOptions,
            displayField: 'label',
            valueField: 'id',
            allowBlank: false,
            msgTarget: 'side',
            fromTitle: lang('Available'),
            toTitle: lang('Selected'),
            height:300,
            labelAlign: 200,
            labelWidth: 200,
            fieldStyle: 'text-align: left;',
            padding :'0 0 10 5',
            baseCls: 'Sfr_FormInputMandatory' 
        }); */

        if (this.viewVar.OpsiDisplay != "insert") {
            thisObj.ObjCommodity = [thisObj.itemSelectorCommodityOptions];

            thisObj.ObjPanelInternalProgram = Ext.create('Koltiva.view.Partner.PanelInternalProgram', {
                viewVar: {
                    PartnerID: this.viewVar.PartnerID
                }
            });

            thisObj.ObjDinamis.push(thisObj.ObjPanelInternalProgram);

            thisObj.ObjPanelExternalProgram = Ext.create('Koltiva.view.Partner.PanelExternalProgram', {
                viewVar: {
                    PartnerID: this.viewVar.PartnerID
                }
            });

            thisObj.ObjDinamis.push(thisObj.ObjPanelExternalProgram);


            thisObj.Region = {
                xtype:'panel',
                title: lang('Region'),
                border: true,
                height:400,
                overflowY: 'auto',
                style: 'margin-left:5px;',
                items:[{
                    xtype: 'grid',
                    id: 'GridGroupAccessArea',
                    style: 'border:1px solid #CCC;margin-top:5px;margin-bottom:12px;',
                    cls:'Sfr_GridNew',
                    maxHeight:350,
                    loadMask: true,
                    selType: 'rowmodel',
                    store: thisObj.storeGridGroupAccessArea,
                    enableColumnHide: false,
                    height:425,
                    // title: lang('District Access'),
                    viewConfig: {
                        deferEmptyText: false,
                        emptyText: GetDefaultContentNoData()
                    },
                    selModel: {
                        selType: 'checkboxmodel',
                        checkOnly: true,
                        multiSelect: true,
                        mode: "MULTI",
                        headerWidth: 50
                    },
                    dockedItems: [{
                        xtype: 'pagingtoolbar',
                        store: thisObj.storeGridGroupAccessArea,
                        dock: 'bottom',
                        displayInfo: true,
                        style: 'padding-right:12px;'
                    }, {
                        xtype: 'toolbar',
                        items: [{
                            xtype: 'button',
                            id: 'GridGroupAccessArea-BtnAddAccessArea',
                            icon: varjs.config.base_url + 'images/icons/new/add.png',
                            text: lang('Add'),
                            cls: 'Sfr_BtnGridBlue',
                            overCls: 'Sfr_BtnGridBlue-Hover',
                            handler: function () {
                                var WinSelectAccessAreaMultiple = Ext.create('Koltiva.view.Partner.WinSelectAccessAreaMultiple', {
                                    viewVar: {
                                        PartnerID:  thisObj.viewVar.PartnerID,
                                        ParentGrid: thisObj.storeGridGroupAccessArea
                                    }
                                });
                                if (!WinSelectAccessAreaMultiple.isVisible()) {
                                    WinSelectAccessAreaMultiple.center();
                                    WinSelectAccessAreaMultiple.show();
                                } else {
                                    WinSelectAccessAreaMultiple.close();
                                }
                            }
                        },{
                            xtype: 'button',
                            id: 'GridGroupAccessArea-BtnDeleteAccessArea',
                            icon: varjs.config.base_url + 'images/icons/new/delete.png',
                            text: lang('Delete'),
                            cls: 'Sfr_BtnGridRed',
                            overCls: 'Sfr_BtnGridRed-Hover',
                            handler: function () {
                                let districtDeleted = [];
                                let group_access_area = JSON.parse(localStorage.getItem('appkolti_group_access_area'));
                                let selection = Ext.getCmp('GridGroupAccessArea').getSelectionModel().getSelection();

                                if(selection.length > 0) {
                                    // Add data yang sudah diselect
                                    if (group_access_area != null) {
                                        if (group_access_area.itemDeleted != null) {
                                            SelectPart = group_access_area.itemDeleted;
                                            Ext.each(SelectPart, function (row, index, value) {
                                                districtDeleted.push(row);
                                            });
                                        }
                                    }
                                    
                                    // Add ke data yang baru diselect
                                    Ext.each(selection, function (row, index, value) {
                                        districtDeleted.push(row.data.DistrictID);
                                    });

                                    // Update value local storage add
                                    if (group_access_area != null && Array.isArray(districtDeleted) && Array.isArray(group_access_area.itemAdded)) {
                                        group_access_area.itemAdded = group_access_area.itemAdded.filter(x => !districtDeleted.includes(x));
                                    }

                                    // Simpan kedalam Store
                                    //Set LocalStorage ================================= (Begin)
                                    localStorage.setItem('appkolti_group_access_area', JSON.stringify({
                                        itemDeleted: districtDeleted,
                                        itemAdded: (group_access_area != null) ? group_access_area.itemAdded : null
                                    }));
                                    //Set LocalStorage ================================= (End)

                                    // Load
                                    thisObj.storeGridGroupAccessArea.load();
                                } else {
                                    Ext.MessageBox.show({
                                        title: lang('Information'),
                                        msg: lang('No region selected'),
                                        buttons: Ext.MessageBox.OK,
                                        animateTarget: 'mb9',
                                        icon: 'ext-mb-info'
                                    });
                                }
                            }
                        },{
                            xtype:'tbspacer',
                            flex:1
                        },{
                            name: 'GridGroupAccessArea-TextSearch',
                            id: 'GridGroupAccessArea-TextSearch',
                            xtype: 'textfield',
                            baseCls: 'Sfr_TxtfieldSearchGrid',
                            width: 350,
                            emptyText: lang('Search by Province Name/District Name')
                        }, {
                            xtype: 'button',
                            icon: varjs.config.base_url + 'images/icons/new/search_white.png',
                            text: lang('Search'),
                            cls: 'Sfr_BtnGridBlue',
                            overCls: 'Sfr_BtnGridBlue-Hover',
                            handler: function () {
                                thisObj.storeGridGroupAccessArea.storeVar.TextSearch = Ext.getCmp('GridGroupAccessArea-TextSearch').getValue();
                                thisObj.storeGridGroupAccessArea.load();
                            }
                        }]
                    }],
                    columns:[{
                        dataIndex: 'DistrictID',
                        hidden: true
                    },{
                        text: 'No',
                        width: '5%',
                        xtype: 'rownumberer'
                    },{
                        text: lang('Country'),
                        dataIndex: 'CountryName',
                        width: '25%'
                    },{
                        text: lang('Province'),
                        dataIndex: 'Province',
                        width: '32%'
                    },{
                        text: lang('District'),
                        dataIndex: 'District',
                        width: '32%'
                    }]
                }]
            }
        }
        
        //Panel Basic ==================================== (Begin)
        thisObj.ObjPanelBasicData = Ext.create('Ext.panel.Panel', {
            title: lang('Organization Data'),
            frame: true,
            cls: 'Sfr_PanelLayoutForm',
            id: 'Koltiva.view.Partner.MainFormNew-FormGeneralData',
            collapsible: true,
            items: [{
                layout: 'column',
                border: false,
                padding: 10,
                items: [{
                    columnWidth: 1,
                    layout: 'form',
                    cls: 'Sfr_PanelLayoutFormContainer',
                    items: [{
                        xtype: 'tabpanel',
                        flex: 1,
                        activeTab: 0,
                        plain: true,
                        cls: 'Sfr_TabForm',
                        id: 'Koltiva.view.Partner.MainFormNew-FormBasicData-Tab',
                        items: [{
                            xtype: 'form',
                            id: 'Koltiva.view.Partner.MainFormNew-FormBasicData',
                            fileUpload: true,
                            buttonAlign: 'right',
                            title: lang('Basic Data'),
                            cls: 'Sfr_PanelSubLayoutForm',
                            items: [{
                                xtype: 'panel',
                                frame: false,
                                id: 'Koltiva.view.Partner.MainFormNew-FormBasicData-SectionAddLocation',
                                style: 'margin-top:12px;',
                                items: [{
                                    layout: 'column',
                                    border: false,
                                    items: [{
                                        columnWidth: 0.5,
                                        layout: 'form',
                                        style: 'padding:10px 0px 10px 5px;',
                                        id: 'Koltiva.view.Partner.MainFormNew-FormBasicData-SectionAddLocation-Left',
                                        defaults: {
                                            labelAlign: 'left'
                                        },
                                        items: [{
                                            xtype: 'textfield',
                                            fieldLabel: lang('Partner ID'),
                                            hidden:true,
                                            id: 'Koltiva.view.Partner.MainFormNew-FormBasicData-PartnerID',
                                            name: 'Koltiva.view.Partner.MainFormNew-FormBasicData-PartnerID',
                                            labelAlign: 'top'
                                        },{
                                            xtype: 'textfield',
                                            fieldLabel: lang('Organization Name'),
                                            id: 'Koltiva.view.Partner.MainFormNew-FormBasicData-PartnerName',
                                            name: 'Koltiva.view.Partner.MainFormNew-FormBasicData-PartnerName',
                                            labelAlign: 'left',
                                            baseCls: 'Sfr_FormInputMandatory',
                                            labelWidth: 200
                                        },{
                                            html: '<div style="height:13px;">&nbsp;</div>'
                                        },{
                                            xtype: 'panel',
                                            id: 'Koltiva.view.Partner.MainFormNew-FormBasicData-Logo',
                                            html: '<img src="' + m_api_base_url + '/assets/images/default-partner.png" style="height:150px;margin:0px 5px 5px 203px;float:left;" />'
                                        },{
                                            xtype: 'fileuploadfield',
                                            fieldLabel: lang('Logo Organization'),
                                            labelAlign: 'left',
                                            id: 'Koltiva.view.Partner.MainFormNew-FormBasicData-LogoInput',
                                            name: 'Koltiva.view.Partner.MainFormNew-FormBasicData-LogoInput',
                                            buttonText: 'Browse',
                                            baseCls: 'Sfr_FormInputMandatory',
                                            labelWidth: 200,
                                            listeners: {
                                                change: function (fb, v) {
                                                    Ext.getCmp('Koltiva.view.Partner.MainFormNew-FormBasicData').getForm().submit({
                                                        url: m_api + '/partner_new/photo_partner',
                                                        clientValidation: false,
                                                        params: {
                                                            OpsiDisplay: thisObj.viewVar.OpsiDisplay,
                                                            PartnerID: Ext.getCmp('Koltiva.view.Partner.MainFormNew-FormBasicData-PartnerID').getValue()
                                                        },
                                                        waitMsg: 'Sending Logo...',
                                                        success: function (fp, o) {
                                                            if (thisObj.viewVar.OpsiDisplay == 'insert') {
                                                                //Insert
                                                                Ext.getCmp('Koltiva.view.Partner.MainFormNew-FormBasicData-Logo').update('<img src="' + m_api_base_url + '/files/tmp/' + o.result.file + '" style="height:150px;margin:0px 5px 5px 203px;float:left;" />');
                                                                Ext.getCmp('Koltiva.view.Partner.MainFormNew-FormBasicData-LogoOld').setValue(o.result.file);
                                                            } else {
                                                                //Update / View
                                                                Ext.getCmp('Koltiva.view.Partner.MainFormNew-FormBasicData-Logo').update('<img src="' + o.result.fileurl + '" style="height:175px;margin:0px;float:right;" />');
                                                            }
                                                        },
                                                        failure: function (fp, o) {
                                                            Ext.MessageBox.show({
                                                                title: lang('Error'),
                                                                msg: o.result.message,
                                                                buttons: Ext.MessageBox.OK,
                                                                animateTarget: 'mb9',
                                                                icon: 'ext-mb-error'
                                                            });
                                                        }
                                                    });
                                                }
                                            }
                                        },{
                                            xtype: 'textfield',
                                            id: 'Koltiva.view.Partner.MainFormNew-FormBasicData-LogoOld',
                                            name: 'Koltiva.view.Partner.MainFormNew-FormBasicData-LogoOld',
                                            inputType: 'hidden'
                                        }]
                                    },{
                                        columnWidth: 0.5,
                                        layout: 'form',
                                        style: 'padding:10px 5px 10px 20px;',
                                        id: 'Koltiva.view.Partner.MainFormNew-FormBasicData-SectionAddLocation-Right',
                                        defaults: {
                                            labelAlign: 'left'
                                        },
                                        items: [{
                                            xtype: "combo",
                                            id: 'Koltiva.view.Partner.MainFormNew-FormBasicData-OrganizationType',
                                            name: "Koltiva.view.Partner.MainFormNew-FormBasicData-OrganizationType",
                                            fieldLabel: lang('Organization Type'),
                                            labelWidth: 200,
                                            store: thisObj.OrganizationType,
                                            queryMode: "local",
                                            displayField : 'label',
                                            valueField : 'id',
                                            allowBlank: false,
                                            baseCls: 'Sfr_FormInputMandatory'
                                        },{
                                            html: '<div style="height:13px;">&nbsp;</div>'
                                        },{
                                            xtype: 'radiogroup',
                                            fieldLabel: lang('Set as Parent'),
                                            allowBlank: false,
                                            baseCls: 'Sfr_FormInputMandatory',
                                            msgTarget: 'side',
                                            columns: 2,
                                            items: [{
                                                boxLabel: lang('Yes'),
                                                name: 'Koltiva.view.Partner.MainFormNew-FormBasicData-SetasParent',
                                                inputValue: 'Yes',
                                                id: 'Koltiva.view.Partner.MainFormNew-FormBasicData-SetasParentYes',
                                                listeners: {
                                                    change: function () {
                                                        if(this.checked == true){
                                                            Ext.getCmp('Koltiva.view.Partner.MainFormNew-FormBasicData-PartnerParentID').allowBlank = true

                                                            if (Ext.getCmp('Koltiva.view.Partner.MainFormNew-FormBasicData-PartnerParentID').hasCls('Sfr_FormInputMandatory')) {
                                                                Ext.getCmp('Koltiva.view.Partner.MainFormNew-FormBasicData-PartnerParentID').removeCls('Sfr_FormInputMandatory');
                                                            }

                                                            Ext.getCmp('Koltiva.view.Partner.MainFormNew-FormBasicData-PartnerParentID').setReadOnly(true)
                                                        }

                                                        return false
                                                    }
                                                }
                                            }, {
                                                boxLabel: lang('No'),
                                                name: 'Koltiva.view.Partner.MainFormNew-FormBasicData-SetasParent',
                                                inputValue: 'No',
                                                id: 'Koltiva.view.Partner.MainFormNew-FormBasicData-SetasParentNo',
                                                listeners: {
                                                    change: function () {
                                                        if(this.checked == true) {
                                                            Ext.getCmp('Koltiva.view.Partner.MainFormNew-FormBasicData-PartnerParentID').allowBlank = false
                                                            Ext.getCmp('Koltiva.view.Partner.MainFormNew-FormBasicData-PartnerParentID').addCls('Sfr_FormInputMandatory');
                                                            Ext.getCmp('Koltiva.view.Partner.MainFormNew-FormBasicData-PartnerParentID').setReadOnly(false)
                                                        }

                                                        return false
                                                    }
                                                }
                                            }]
                                        },{
                                            xtype: "combo",
                                            id: 'Koltiva.view.Partner.MainFormNew-FormBasicData-PartnerParentID',
                                            name: "Koltiva.view.Partner.MainFormNew-FormBasicData-PartnerParentID",
                                            fieldLabel: lang('Parent'),
                                            labelWidth: 200,
                                            store: thisObj.StorePartnerID,
                                            queryMode: "local",
                                            displayField : 'label',
                                            valueField : 'id',
                                            allowBlank: true,
                                            emptyText: lang('Select Parent')
                                        },{
                                            html: '<div style="height:13px;">&nbsp;</div>'
                                        },{
                                            xtype: 'datefield',
                                            id: 'Koltiva.view.Partner.MainFormNew-FormBasicData-ActivationDate',
                                            cls: 'Sfr_FormInputMandatory',
                                            name: 'Koltiva.view.Partner.MainFormNew-FormBasicData-ActivationDate',
                                            fieldLabel: lang('Activation Date'),
                                            allowBlank: false,
                                            format: 'Y-m-d',
                                            labelWidth: 200
                                        },{
                                            html: '<div style="height:13px;">&nbsp;</div>'
                                        },{
                                            xtype: 'radiogroup',
                                            fieldLabel: lang('Status'),
                                            allowBlank: false,
                                            baseCls: 'Sfr_FormInputMandatory',
                                            msgTarget: 'side',
                                            columns: 2,
                                            items: [{
                                                boxLabel: lang('Active'),
                                                name: 'Koltiva.view.Partner.MainFormNew-FormBasicData-Status',
                                                inputValue: 'active',
                                                id: 'Koltiva.view.Partner.MainFormNew-FormBasicData-StatusYes',
                                                listeners: {
                                                    change: function () {
                                                        return false;
                                                    }
                                                }
                                            }, {
                                                boxLabel: lang('In-Active'),
                                                name: 'Koltiva.view.Partner.MainFormNew-FormBasicData-Status',
                                                inputValue: 'inactive',
                                                id: 'Koltiva.view.Partner.MainFormNew-FormBasicData-StatusNo',
                                                listeners: {
                                                    change: function () {
                                                        return false;
                                                    }
                                                }
                                            }]
                                        }]
                                    }]
                                },{
                                    layout:{
                                        type:'vbox',
                                        align:'stretch'
                                    },
                                    items: thisObj.ObjCommodity
                                },
                                /* {
                                    layout:{
                                        type:'vbox',
                                        align:'stretch'
                                    },
                                    items:[
                                        thisObj.itemSelectorInternalProgram
                                    ]
                                },
                                {
                                    layout:{
                                        type:'vbox',
                                        align:'stretch'
                                    },
                                    items:[
                                        thisObj.itemSelectorExternalProgram
                                    ]
                                }, */
                                {
                                    items: thisObj.ObjDinamis
                                },
                                {
                                    html: '<div style="height:13px;">&nbsp;</div>'
                                },
                                thisObj.Region
                                ]
                            }],
                            buttons: [{
                                xtype: 'button',
                                icon: varjs.config.base_url + 'images/icons/new/save.png',
                                text: lang('Save'),
                                cls: 'Sfr_BtnFormBlue',
                                overCls: 'Sfr_BtnFormBlue-Hover',
                                id: 'Koltiva.view.Partner.MainFormNew-FormBasicData-BtnSave',
                                handler: function () {
                                    var Formnya = Ext.getCmp('Koltiva.view.Partner.MainFormNew-FormBasicData').getForm();
                                    let group_access_area = JSON.parse(localStorage.getItem('appkolti_group_access_area'));

                                    if (Formnya.isValid()) {
                                        Formnya.submit({
                                            url: m_api + '/partner_new/partner_data',
                                            method: 'POST',
                                            waitMsg: 'Saving data...',
                                            params: {
                                                OpsiDisplay: thisObj.viewVar.OpsiDisplay,
                                                passing_selection: JSON.stringify(localStorage.getItem('appkolti_group_access_area')),
                                                params: {
                                                    itemAdded: (group_access_area != null && Array.isArray(group_access_area.itemAdded)) ? group_access_area.itemAdded.join(',') : null,
                                                    itemDeleted: (group_access_area != null && Array.isArray(group_access_area.itemDeleted)) ? group_access_area.itemDeleted.join(',') : null
                                                }
                                            },
                                            success: function (fp, o) {
                                                Ext.MessageBox.show({
                                                    title: 'Information',
                                                    msg: lang('Data saved'),
                                                    buttons: Ext.MessageBox.OK,
                                                    animateTarget: 'mb9',
                                                    icon: 'ext-mb-success',
                                                    fn: function (btn) {
                                                        if (btn == 'ok') {
                                                            Ext.getCmp('Koltiva.view.Partner.MainFormNew').destroy(); //destory current view
                                                            var MainForm = [];
                                                            if (Ext.getCmp('Koltiva.view.Partner.MainFormNew') == undefined) {
                                                                MainForm = Ext.create('Koltiva.view.Partner.MainFormNew', {
                                                                    viewVar: {
                                                                        OpsiDisplay: 'update',
                                                                        PartnerID: o.result.PartnerID
                                                                    }
                                                                });
                                                            } else {
                                                                Ext.getCmp('Koltiva.view.Partner.MainFormNew').destroy();
                                                                MainForm = Ext.create('Koltiva.view.Partner.MainFormNew', {
                                                                    viewVar: {
                                                                        OpsiDisplay: 'update',
                                                                        PartnerID: o.result.PartnerID
                                                                    }
                                                                });
                                                            }
                                                        }
                                                    }
                                                });

                                                localStorage.removeItem("appkolti_group_access_area");
                                            },
                                            failure: function (fp, o) {
                                                try {
                                                    var r = Ext.decode(o.response.responseText);
                                                    Ext.MessageBox.show({
                                                        title: 'Error',
                                                        msg: r.message,
                                                        buttons: Ext.MessageBox.OK,
                                                        animateTarget: 'mb9',
                                                        icon: 'ext-mb-error'
                                                    });
                                                } catch (err) {
                                                    Ext.MessageBox.show({
                                                        title: 'Error',
                                                        msg: 'Connection Error',
                                                        buttons: Ext.MessageBox.OK,
                                                        animateTarget: 'mb9',
                                                        icon: 'ext-mb-error'
                                                    });
                                                }
                                            }
                                        });
                                    } else {
                                        Ext.MessageBox.show({
                                            title: lang('Attention'),
                                            msg: lang('Form not complete yet'),
                                            buttons: Ext.MessageBox.OK,
                                            animateTarget: 'mb9',
                                            icon: 'ext-mb-info'
                                        });
                                    }
                                }
                            }]
                        }]
                    }]
                }]
            }]
        });

        //============================= End DQ =========================================//

        //========================================================== LAYOUT UTAMA (Begin) ========================================//
        thisObj.items = [{
            xtype: 'panel',
            border: false,
            layout: {
                type: 'hbox'
            },
            items: [{
                id: 'Koltiva.view.Partner.MainFormNew-labelInfoInsert',
                html: '<div id="header_title_farmer">' + lang('Partner Data') + '</div>'
            }]
        }, {
            items: [{
                id: 'Koltiva.view.Partner.MainFormNew-LinkBackToList',
                html: '<div id="Sfr_IdBoxInfoDataGrid" class="Sfr_BoxInfoDataGrid"><ul class="Sft_UlListInfoDataGrid"><li class="Sft_ListInfoDataGrid"><a href="javascript:Ext.getCmp(\'Koltiva.view.Partner.MainFormNew\').BackToList()"><img class="Sft_ListIconInfoDataGrid" src="' + varjs.config.base_url + 'images/icons/new/back.png" width="20" />&nbsp;&nbsp;' + lang('Back to Partner List') + '</a></li></div>'
            }]
        }, {
            html: '<br />'
        }, {
            layout: 'column',
            border: false,
            items: [{
                //LEFT CONTENT
                columnWidth: 1,
                items: [
                    thisObj.ObjPanelBasicData
                ]
            }]
        }];
        //========================================================== LAYOUT UTAMA (END) ========================================//

        this.callParent(arguments);
    },
    BackToList: function () {
        Ext.getCmp('Koltiva.view.Partner.MainFormNew').destroy();
        // loadScript(varjs.config.base_url+'/js/modules/program_new.js');

        // window.location.reload()
        window.location.replace(`${m_base_url_additional}/partner/program_new`)
    }
});