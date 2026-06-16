Ext.define('Koltiva.view.Traceability_new.Delivery.MainForm', {
    extend: 'Ext.panel.Panel',
    id: 'Koltiva.view.Traceability_new.Delivery.MainForm',
    style: 'padding:0 15px 15px 15px;margin:5px 0 0 0;',
    viewVar: false,
    setViewVar: function (value) {
        this.viewVar = value;
    },
    renderTo: 'ext-content',
    listeners: {
        afterRender: function () {
            var thisObj = this;
            document.getElementById('divCommonContentRegion2').style.display = 'none';

            if (thisObj.viewVar.OpsiDisplay == 'view' || thisObj.viewVar.OpsiDisplay == 'update') {
                Ext.getCmp('Koltiva.view.Traceability_new.Delivery.MainForm-FormBasicData-DeliveryID').setReadOnly(true);

                //load formnya
                Ext.getCmp('Koltiva.view.Traceability_new.Delivery.MainForm-FormBasicData').getForm().load({
                    url: m_api + '/traceability_api/delivery/supplychain_delivery_form_open',
                    method: 'GET',
                    params: {
                        DeliveryID: this.viewVar.DeliveryID
                    },
                    success: function (form, action) {
                        Ext.MessageBox.hide();
                        var r = Ext.decode(action.response.responseText);

                        //Title
                        Ext.getCmp('Koltiva.view.Traceability_new.Delivery.MainForm-labelInfoInsert').update('<div id="header_title_farmer">' + Ext.getCmp('Koltiva.view.Traceability_new.Delivery.MainForm-FormBasicData-DeliveryNumber').getValue() + '</div>');
                        Ext.getCmp('Koltiva.view.Traceability_new.Delivery.MainForm-labelInfoInsert').doLayout();
                        
                        var DeliveryStatusID = r.data['Koltiva.view.Traceability_new.Delivery.MainForm-FormBasicData-DeliveryStatusID'];
                        var SupplyDestMillOtherName = r.data['Koltiva.view.Traceability_new.Delivery.MainForm-FormBasicData-SupplyDestMillOtherName'];
                        
                        if(DeliveryStatusID=='1'){
                            Ext.getCmp('PanelDataDeliveryPick').show();
                            Ext.getCmp('DetailDeliveryPanel').hide();
                            Ext.getCmp('Koltiva.view.Traceability_new.Delivery.MainForm-FormBasicData-BtnSent').hide();
                    
                            if (thisObj.viewVar.OpsiDisplay == 'update') {
                                Ext.getCmp('Koltiva.view.Traceability_new.Delivery.MainForm-FormBasicData-BtnSave').hide();
                            }
                        }else if(DeliveryStatusID=='2'){
                            Ext.getCmp('PanelDataDeliveryPick').show();
                            Ext.getCmp('DetailDeliveryPanel').show();
                            Ext.getCmp('Koltiva.view.Traceability_new.Delivery.MainForm-FormBasicData-FinalCapacity').setReadOnly(true);
                            Ext.getCmp('Koltiva.view.Traceability_new.Delivery.MainForm-FormBasicData-PaymentDelivery').setReadOnly(true);
                            Ext.getCmp('Koltiva.view.Traceability_new.Delivery.PanelDataDeliveryPick-BtnAdd').hide();
                            Ext.getCmp('Koltiva.view.Traceability_new.Delivery.PanelDataDeliveryPick-BtnClose').hide();
                            Ext.getCmp('Koltiva.view.Traceability_new.Delivery.MainForm-FormBasicData-BtnSave').show();
                            Ext.getCmp('Koltiva.view.Traceability_new.Delivery.MainForm-FormBasicData-BtnSent').show();
                            Ext.getCmp('Koltiva.view.Traceability_new.Delivery.PanelDataDeliveryPick-ActionColumn').setVisible(false);

                        }else if(DeliveryStatusID=='3'){
                            Ext.getCmp('PanelDataDeliveryPick').show();
                            Ext.getCmp('DetailDeliveryPanel').show();
                            Ext.getCmp('Koltiva.view.Traceability_new.Delivery.PanelDataDeliveryPick-BtnAdd').hide();
                            Ext.getCmp('Koltiva.view.Traceability_new.Delivery.PanelDataDeliveryPick-BtnClose').hide();
                            Ext.getCmp('Koltiva.view.Traceability_new.Delivery.MainForm-FormBasicData-BtnSave').show();

                            Ext.getCmp('Koltiva.view.Traceability_new.Delivery.MainForm-FormBasicData-BtnSent').hide();

                            Ext.getCmp('Koltiva.view.Traceability_new.Delivery.MainForm-FormBasicData-DeliveryDate').setReadOnly(true);
                            Ext.getCmp('Koltiva.view.Traceability_new.Delivery.MainForm-FormBasicData-ExternalCode').setReadOnly(true);
                            Ext.getCmp('Koltiva.view.Traceability_new.Delivery.MainForm-FormBasicData-Destination').setReadOnly(true);
                            Ext.getCmp('Koltiva.view.Traceability_new.Delivery.MainForm-FormBasicData-TotalWeight').setReadOnly(true);
                            Ext.getCmp('Koltiva.view.Traceability_new.Delivery.MainForm-FormBasicData-PackageWeight').setReadOnly(true);
                            Ext.getCmp('Koltiva.view.Traceability_new.Delivery.MainForm-FormBasicData-ArrivalEstimation').setReadOnly(true);
                            Ext.getCmp('Koltiva.view.Traceability_new.Delivery.MainForm-FormBasicData-DestDriver').setReadOnly(true);
                            Ext.getCmp('Koltiva.view.Traceability_new.Delivery.MainForm-FormBasicData-DestTransportNumber').setReadOnly(true);
                            Ext.getCmp('Koltiva.view.Traceability_new.Delivery.MainForm-FormBasicData-DestTransportID').setReadOnly(true);
                            Ext.getCmp('Koltiva.view.Traceability_new.Delivery.MainForm-FormBasicData-SupplyDestOrgID').setReadOnly(true);
                           
                            if(SupplyDestMillOtherName != ''){
                                Ext.getCmp('Koltiva.view.Traceability_new.Delivery.MainForm-FormBasicData-OtherMill').setValue('1');
                                Ext.getCmp('Koltiva.view.Traceability_new.Delivery.MainForm-FormBasicData-SupplyDestMillOtherName').getValue();
                                Ext.getCmp('Koltiva.view.Traceability_new.Delivery.MainForm-FormBasicData-SupplyDestMillOtherName').setReadOnly(true);
                            }

                            Ext.getCmp('Koltiva.view.Traceability_new.Delivery.MainForm-FormBasicData-SupplyDestType').setReadOnly(true);
                            Ext.getCmp('Koltiva.view.Traceability_new.Delivery.MainForm-FormBasicData-SMESPCodeID').setReadOnly(true);
                            Ext.getCmp('Koltiva.view.Traceability_new.Delivery.MainForm-FormBasicData-DestPo').setReadOnly(true);

                            Ext.getCmp('Koltiva.view.Traceability_new.Delivery.MainForm-FormBasicData-FinalCapacity').setReadOnly(false);
                            Ext.getCmp('Koltiva.view.Traceability_new.Delivery.MainForm-FormBasicData-PaymentDelivery').setReadOnly(false);

                            Ext.getCmp('Koltiva.view.Traceability_new.Delivery.PanelDataDeliveryPick-BtnAdd').setVisible(false);
                            Ext.getCmp('Koltiva.view.Traceability_new.Delivery.PanelDataDeliveryPick-BtnClose').setVisible(false);
                            Ext.getCmp('Koltiva.view.Traceability_new.Delivery.PanelDataDeliveryPick-reload').setVisible(false);
                            Ext.getCmp('Koltiva.view.Traceability_new.Delivery.PanelDataDeliveryPick-ActionColumn').setVisible(false);
                        } else{
                            Ext.getCmp('PanelDataDeliveryPick').show();
                            Ext.getCmp('DetailDeliveryPanel').show();
                            Ext.getCmp('Koltiva.view.Traceability_new.Delivery.MainForm-FormBasicData-BtnSave').show();
                            Ext.getCmp('Koltiva.view.Traceability_new.Delivery.MainForm-FormBasicData-BtnSent').hide();

                            Ext.getCmp('Koltiva.view.Traceability_new.Delivery.MainForm-FormBasicData-DeliveryDate').setReadOnly(true);
                            Ext.getCmp('Koltiva.view.Traceability_new.Delivery.MainForm-FormBasicData-ExternalCode').setReadOnly(true);
                            Ext.getCmp('Koltiva.view.Traceability_new.Delivery.MainForm-FormBasicData-Destination').setReadOnly(true);
                            Ext.getCmp('Koltiva.view.Traceability_new.Delivery.MainForm-FormBasicData-TotalWeight').setReadOnly(true);
                            Ext.getCmp('Koltiva.view.Traceability_new.Delivery.MainForm-FormBasicData-PackageWeight').setReadOnly(true);
                            Ext.getCmp('Koltiva.view.Traceability_new.Delivery.MainForm-FormBasicData-ArrivalEstimation').setReadOnly(true);
                            Ext.getCmp('Koltiva.view.Traceability_new.Delivery.MainForm-FormBasicData-DestDriver').setReadOnly(true);
                            Ext.getCmp('Koltiva.view.Traceability_new.Delivery.MainForm-FormBasicData-DestTransportNumber').setReadOnly(true);
                            Ext.getCmp('Koltiva.view.Traceability_new.Delivery.MainForm-FormBasicData-DestTransportID').setReadOnly(true);
                            Ext.getCmp('Koltiva.view.Traceability_new.Delivery.MainForm-FormBasicData-SupplyDestOrgID').setReadOnly(true);
                            Ext.getCmp('Koltiva.view.Traceability_new.Delivery.MainForm-FormBasicData-SupplyDestMillOtherName').setReadOnly(true);
                            Ext.getCmp('Koltiva.view.Traceability_new.Delivery.MainForm-FormBasicData-SupplyDestType').setReadOnly(true);
                            Ext.getCmp('Koltiva.view.Traceability_new.Delivery.MainForm-FormBasicData-SMESPCodeID').setReadOnly(true);
                            Ext.getCmp('Koltiva.view.Traceability_new.Delivery.MainForm-FormBasicData-DestPo').setReadOnly(true);

                            Ext.getCmp('Koltiva.view.Traceability_new.Delivery.MainForm-FormBasicData-FinalCapacity').setReadOnly(false);
                            Ext.getCmp('Koltiva.view.Traceability_new.Delivery.MainForm-FormBasicData-PaymentDelivery').setReadOnly(false);

                            Ext.getCmp('Koltiva.view.Traceability_new.Delivery.PanelDataDeliveryPick-BtnAdd').setVisible(false);
                            Ext.getCmp('Koltiva.view.Traceability_new.Delivery.PanelDataDeliveryPick-BtnClose').setVisible(false);
                            Ext.getCmp('Koltiva.view.Traceability_new.Delivery.PanelDataDeliveryPick-reload').setVisible(false);
                            Ext.getCmp('Koltiva.view.Traceability_new.Delivery.PanelDataDeliveryPick-ActionColumn').setVisible(false);

                            if(SupplyDestMillOtherName != ''){
                                Ext.getCmp('Koltiva.view.Traceability_new.Delivery.MainForm-FormBasicData-OtherMill').setValue('1');
                                Ext.getCmp('MillOther').setVisible(false);
                                Ext.getCmp('Koltiva.view.Traceability_new.Delivery.MainForm-FormBasicData-SupplyDestMillOtherName').getValue();
                                Ext.getCmp('Koltiva.view.Traceability_new.Delivery.MainForm-FormBasicData-SupplyDestMillOtherName').setReadOnly(true);
                            }
                        }

                        if (thisObj.viewVar.OpsiDisplay == 'view') {
                            Ext.getCmp('Koltiva.view.Traceability_new.Delivery.MainForm-FormBasicData-BtnSave').hide();
                            Ext.getCmp('Koltiva.view.Traceability_new.Delivery.PanelDataDeliveryPick-BtnAdd').hide();
                            Ext.getCmp('Koltiva.view.Traceability_new.Delivery.PanelDataDeliveryPick-BtnClose').hide();
                            Ext.getCmp('Koltiva.view.Traceability_new.Delivery.MainForm-FormBasicData-BtnSent').hide();
                        }

                        let checkDestType = Ext.getCmp('Koltiva.view.Traceability_new.Delivery.MainForm-FormBasicData-SupplyDestType').getValue()

                        if (checkDestType == null) {
                            Ext.getCmp('Koltiva.view.Traceability_new.Delivery.MainForm-FormBasicData-SupplyDestOrgID').hide();
                        } else if (checkDestType == 'do' || checkDestType == 'agent') {
                            Ext.getCmp('Koltiva.view.Traceability_new.Delivery.MainForm-FormBasicData-SupplyDestOrgID').show();
                        } else {
                            Ext.getCmp('Koltiva.view.Traceability_new.Delivery.MainForm-FormBasicData-SupplyDestOrgID').hide();
                        }
                       
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
                Ext.getCmp('Koltiva.view.Traceability_new.Delivery.MainForm-FormBasicData-DeliveryStatusID').setValue('1');
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
        var labelWidth = 200;

        //Store ==================================== (Begin)

        thisObj.PalmoilType = Ext.create('Koltiva.store.Traceability_new.Transaction.PalmoilType', {
            storeVar : {
                SupplyTransID : null
            }
        });

        thisObj.storeSPCode                  = Ext.create('Koltiva.store.Traceability_new.Delivery.GridSPCodePanel');
        
        thisObj.DeliveryStatus               = Ext.create('Koltiva.store.Traceability_new.Delivery.DeliveryStatus');
        thisObj.StoreComboTransportationType = Ext.create('Koltiva.store.Traceability_new.Delivery.StoreComboTransportationType');
        thisObj.StoreComboDestinantionName   = Ext.create('Koltiva.store.Traceability_new.Delivery.StoreComboDestination');
        thisObj.StoreComboDealer             = Ext.create('Koltiva.store.Traceability_new.Delivery.StoreComboDealer');
        thisObj.ComboDestType                = Ext.create('Koltiva.store.Traceability_new.Delivery.ComboDestType');
        
        //Additional Panel ==================================== (Begin)
        thisObj.ObjPanelDataDeliveryPick = Ext.create('Koltiva.view.Traceability_new.Delivery.PanelDataDeliveryPick', {
            viewVar: {
                DeliveryID: thisObj.viewVar.DeliveryID,
                DeliveryStatusID: thisObj.viewVar.DeliveryStatusID
            }
        });

        //Additional Panel ==================================== (Begin)
        thisObj.ObjPanelDataPurchaseDetail = Ext.create('Koltiva.view.Traceability_new.Delivery.PanelDataPurchaseDetail', {
            viewVar: {
                DeliveryID: thisObj.viewVar.DeliveryID
            }
        });

        var ComboProcDestination = Ext.create('Ext.data.Store', {
            fields: ['id', 'label'],
            data : [
                {"label":lang('Processing in Mill'), "id":'mill'}
                
            ]
		});

        var ComboDestinationDO = Ext.create('Koltiva.store.Traceability_new.Transaction.ComboDestinationDO'); 

        var ComboSPB = Ext.create('Koltiva.store.Traceability_new.Transaction.ComboSPB');

        //Panel Basic ==================================== (Begin)
        thisObj.ObjPanelBasicData = Ext.create('Ext.panel.Panel', {
            title: lang('Selling Form'),
            frame: true,
            cls: 'Sfr_PanelLayoutForm',
            id: 'Koltiva.view.Traceability_new.Delivery.MainForm-FormGeneralData',
            collapsible: true,
            items: [{
                    xtype: 'form',
                    id: 'Koltiva.view.Traceability_new.Delivery.MainForm-FormBasicData',
                    buttonAlign: 'right',
                    cls: 'Sfr_PanelSubLayoutForm',
                    items: [{
                            xtype: 'panel',
                            title: lang('Information'),
                            frame: false,
                            id: 'Koltiva.view.Traceability_new.Delivery.MainForm-FormBasicData-SectionProcessing',
                            style: 'margin-top:15px;margin-left:10px;margin-right:10px',
                            cls: 'Sfr_PanelSubLayoutFormRoundedGray',
                            items: [{
                                layout: 'column',
                                border: false,
                                items: [{
                                        columnWidth: 0.435,
                                        layout: 'form',
                                        style: 'padding:10px 5px 10px 20px;',
                                        defaults: {
                                            labelAlign: 'left',
                                            labelWidth: 150
                                        },
                                        items: [{
                                            xtype: 'textfield',
                                            id: 'Koltiva.view.Traceability_new.Delivery.MainForm-FormBasicData-DeliveryID',
                                            name: 'Koltiva.view.Traceability_new.Delivery.MainForm-FormBasicData-DeliveryID',
                                            inputType: 'hidden'
                                        },{
                                            xtype: 'textfield',
                                            id: 'Koltiva.view.Traceability_new.Delivery.MainForm-FormBasicData-DeliveryNumber',
                                            name: 'Koltiva.view.Traceability_new.Delivery.MainForm-FormBasicData-DeliveryNumber',
                                            baseCls: 'Sfr_FormInputMandatory',
                                            hidden:true,
                                            fieldLabel: lang('Selling Number')
                                        },
                                        {
                                            xtype: 'combobox',
                                            id: 'Koltiva.view.Traceability_new.Delivery.MainForm-FormBasicData-DeliveryStatusID',
                                            name: 'Koltiva.view.Traceability_new.Delivery.MainForm-FormBasicData-DeliveryStatusID',
                                            store: thisObj.DeliveryStatus,
                                            queryMode: 'local',
                                            displayField: 'label',
                                            valueField: 'id',
                                            readOnly: true,
                                            fieldLabel: lang('Selling Status')
                                        },
                                        {
                                            xtype: 'datefield',
                                            fieldLabel: lang('Delivery Date'),
                                            width: 500,
                                            labelAlign:'left',
                                            format: 'Y-m-d H:i:s',
                                            id: 'Koltiva.view.Traceability_new.Delivery.MainForm-FormBasicData-DeliveryDate',
                                            name: 'Koltiva.view.Traceability_new.Delivery.MainForm-FormBasicData-DeliveryDate',
                                            value: m_now,
                                        },
                                        {
                                            xtype: 'textfield',
                                            id: 'Koltiva.view.Traceability_new.Delivery.MainForm-FormBasicData-ExternalCode',
                                            name: 'Koltiva.view.Traceability_new.Delivery.MainForm-FormBasicData-ExternalCode',
                                            fieldLabel: lang('External Code')
                                        },
                                        {
                                            xtype: 'textfield',
                                            id: 'Koltiva.view.Traceability_new.Delivery.MainForm-FormBasicData-PackageWeight',
                                            name: 'Koltiva.view.Traceability_new.Delivery.MainForm-FormBasicData-PackageWeight',
                                            hidden:true
                                        }]
                                    },]
                            }, 
                            {
                                layout: 'column',
                                border: false,
                                items: [{
                                    columnWidth: 1,
                                    hidden:true,
                                    id:"PanelDataDeliveryPick",
                                    items: [
                                        thisObj.ObjPanelDataDeliveryPick
                                    ]
                                },{
                                    columnWidth: 1,
                                    items: [
                                        {
                                            columnWidth: 1,
                                            layout:'form',
                                            items:[{
                                                xtype: 'panel',
                                                hidden:true,
                                                id:'DetailDeliveryPanel',
                                                title: lang('Data Selling Detail'),
                                                items: [{
                                                    layout: 'column',
                                                    items: [{
                                                        columnWidth: 0.5,
                                                        layout: 'form',
                                                        padding:5,
                                                        items:[ 
                                                            {
                                                                xtype: 'combo',
                                                                labelAlign:'top',
                                                                labelWidth: 150,
                                                                fieldLabel: lang('Selling Type'),
                                                                id: 'Koltiva.view.Traceability_new.Delivery.MainForm-FormBasicData-SupplyDestType',
                                                                name: 'Koltiva.view.Traceability_new.Delivery.MainForm-FormBasicData-SupplyDestType',
                                                                store: thisObj.ComboDestType,
                                                                displayField: 'label',
                                                                valueField: 'id',
                                                                queryMode: 'local',
                                                                baseCls: 'Sfr_FormInputMandatory',
                                                                // allowBlank: false,
                                                                enableKeyEvents: true,
                                                                listeners:{
                                                                    select: function(combo, records, eOpts) {
                                                                        if(records[0].data.id == 'mill'){
                                                                            
                                                                            Ext.getCmp('Koltiva.view.Traceability_new.Delivery.MainForm-FormBasicData-Destination').setVisible(true);
                                                                            Ext.getCmp('MillOther').setVisible(true);
                                                                            Ext.getCmp('Koltiva.view.Traceability_new.Delivery.MainForm-FormBasicData-SupplyDestOrgID').setVisible(false);
                                                                            Ext.getCmp('Koltiva.view.Traceability_new.Delivery.MainForm-FormBasicData-SMESPCodeID').setVisible(true);
                                                                        
                                                                            Ext.getCmp('Koltiva.view.Traceability_new.Delivery.MainForm-FormBasicData-Destination').setValue('')
                                                                            Ext.getCmp('Koltiva.view.Traceability_new.Delivery.MainForm-FormBasicData-SupplyDestOrgID').setValue('')

                                                                            thisObj.StoreComboDestinantionName.load({params: {DestinationID: null}});

                                                                        } else if (records[0].data.id == 'do') {

                                                                            Ext.getCmp('MillOther').setVisible(true);
                                                                            Ext.getCmp('Koltiva.view.Traceability_new.Delivery.MainForm-FormBasicData-Destination').setVisible(true);
                                                                            Ext.getCmp('Koltiva.view.Traceability_new.Delivery.MainForm-FormBasicData-SMESPCodeID').setVisible(true);
                                                                            Ext.getCmp('Koltiva.view.Traceability_new.Delivery.MainForm-FormBasicData-SupplyDestOrgID').setVisible(true);
                                                                            Ext.getCmp('Koltiva.view.Traceability_new.Delivery.MainForm-FormBasicData-DestPo').setVisible(true);

                                                                            Ext.getCmp('Koltiva.view.Traceability_new.Delivery.MainForm-FormBasicData-Destination').setValue('')
                                                                            Ext.getCmp('Koltiva.view.Traceability_new.Delivery.MainForm-FormBasicData-SupplyDestOrgID').setValue('')
                                                                            Ext.getCmp('Koltiva.view.Traceability_new.Delivery.MainForm-FormBasicData-SupplyDestOrgID').allowBlank = false;
                                                                           
                                                                        } else if(records[0].data.id == 'agent'){
                                                                            
                                                                            Ext.getCmp('MillOther').setVisible(false);
                                                                            Ext.getCmp('Koltiva.view.Traceability_new.Delivery.MainForm-FormBasicData-Destination').setVisible(false);
                                                                            Ext.getCmp('Koltiva.view.Traceability_new.Delivery.MainForm-FormBasicData-SMESPCodeID').setVisible(false);
                                                                            Ext.getCmp('Koltiva.view.Traceability_new.Delivery.MainForm-FormBasicData-SupplyDestOrgID').setVisible(true);

                                                                            Ext.getCmp('Koltiva.view.Traceability_new.Delivery.MainForm-FormBasicData-Destination').setValue('')
                                                                            Ext.getCmp('Koltiva.view.Traceability_new.Delivery.MainForm-FormBasicData-SupplyDestOrgID').setValue('')
                                                                           
                                                                        } else {

                                                                            return false;
                                                                        }

                                                                        return false;
                                                                    }
                                                                }
                                                            },
                                                            {
                                                                xtype: 'combo',
                                                                id: 'Koltiva.view.Traceability_new.Delivery.MainForm-FormBasicData-SupplyDestOrgID',
                                                                name: 'Koltiva.view.Traceability_new.Delivery.MainForm-FormBasicData-SupplyDestOrgID',
                                                                allowBlank : true,
                                                                hidden:true,
                                                                labelAlign:'top',
                                                                store: thisObj.StoreComboDealer, 
                                                                labelWidth:200, 
                                                                fieldLabel: lang('Dealer'),
                                                                baseCls: 'Sfr_FormInputMandatory', 
                                                                queryMode: 'local',
                                                                displayField: 'label',
                                                                valueField: 'id',
                                                                listeners: {
                                                                    change: function (cb, nv, ov) {
                                                                        if (thisObj.viewVar.OpsiDisplay == 'insert') {
                                                                            Ext.getCmp('Koltiva.view.Traceability_new.Delivery.MainForm-FormBasicData-Destination').setValue('')
                                                                        }  

                                                                        thisObj.StoreComboDestinantionName.load({params: {DestinationID: nv}});
                                                                    }
                                                                }
                                                            },
                                                            {
                                                                xtype: 'combo',
                                                                labelAlign:'top',
                                                                labelWidth: 150,
                                                                fieldLabel: lang('Destination Name'),
                                                                id: 'Koltiva.view.Traceability_new.Delivery.MainForm-FormBasicData-Destination',
                                                                name: 'Koltiva.view.Traceability_new.Delivery.MainForm-FormBasicData-Destination',
                                                                store: thisObj.StoreComboDestinantionName,
                                                                displayField: 'label',
                                                                valueField: 'id',
                                                                queryMode: 'local',
                                                                baseCls: 'Sfr_FormInputMandatory',
                                                                enableKeyEvents: true,
                                                                // allowBlank: false,
                                                                listeners:{
                                                                    change: function(checkbox, newValue, oldValue, eOpts) {
                                                                        if(newValue){
                                                                            Ext.getCmp('MillOther').setVisible(false);
                                                                            Ext.getCmp('Koltiva.view.Traceability_new.Delivery.MainForm-FormBasicData-OtherMill').setVisible(false);
                                                                            Ext.getCmp('Koltiva.view.Traceability_new.Delivery.MainForm-FormBasicData-SupplyDestMillOtherName').setVisible(false);
                                                                        }else{
                                                                            Ext.getCmp('MillOther').setVisible(true);
                                                                            Ext.getCmp('Koltiva.view.Traceability_new.Delivery.MainForm-FormBasicData-OtherMill').setVisible(true);
                                                                            Ext.getCmp('Koltiva.view.Traceability_new.Delivery.MainForm-FormBasicData-SupplyDestMillOtherName').setVisible(true);
                                                                        }

                                                                        thisObj.storeSPCode.load({params: {id: newValue}});
                                                                        
                                                                    }
                                                                }
                                                            },
                                                            {
                                                                xtype: 'fieldcontainer',
                                                                fieldLabel: lang('Other Seller'),
                                                                defaultType: 'checkboxfield',
                                                                labelAlign:'top',
                                                                id:'MillOther',
                                                                items: [
                                                                    {
                                                                        boxLabel  : lang('Yes'),
                                                                        name      : 'Koltiva.view.Traceability_new.Delivery.MainForm-FormBasicData-OtherMill',
                                                                        inputValue: '1',
                                                                        id        : 'Koltiva.view.Traceability_new.Delivery.MainForm-FormBasicData-OtherMill',
                                                                        listeners:{
                                                                            change: function(checkbox, newValue, oldValue, eOpts) {
                                                                                if(newValue){
                                                                                    Ext.getCmp('Koltiva.view.Traceability_new.Delivery.MainForm-FormBasicData-SMESPCodeID').setVisible(false);
                                                                                    Ext.getCmp('Koltiva.view.Traceability_new.Delivery.MainForm-FormBasicData-SupplyDestMillOtherName').setVisible(true);
                                                                                    Ext.getCmp('Koltiva.view.Traceability_new.Delivery.MainForm-FormBasicData-Destination').setReadOnly(true);
                                                                                    Ext.getCmp('Koltiva.view.Traceability_new.Delivery.MainForm-FormBasicData-Destination').setValue('');
                                                                                }else{
                                                                                    Ext.getCmp('Koltiva.view.Traceability_new.Delivery.MainForm-FormBasicData-SMESPCodeID').setVisible(true);
                                                                                    Ext.getCmp('Koltiva.view.Traceability_new.Delivery.MainForm-FormBasicData-SupplyDestMillOtherName').setVisible(false);
                                                                                    Ext.getCmp('Koltiva.view.Traceability_new.Delivery.MainForm-FormBasicData-Destination').setReadOnly(false);
                                                                                    Ext.getCmp('Koltiva.view.Traceability_new.Delivery.MainForm-FormBasicData-Destination').setValue('');
                                                                                }
                                                                            }
                                                                        }
                                                                    }
                                                                ]
                                                            },
                                                            {
                                                                xtype: 'textfield',
                                                                id: 'Koltiva.view.Traceability_new.Delivery.MainForm-FormBasicData-SupplyDestMillOtherName',
                                                                name: 'Koltiva.view.Traceability_new.Delivery.MainForm-FormBasicData-SupplyDestMillOtherName',
                                                                fieldLabel: lang('Other Mill Name'),
                                                                labelAlign:'top',
                                                                listeners :{
                                                                    change:function(val){
                                                                         
                                                                    }
                                                                } 
                                                            },{
                                                                xtype: 'combo',
                                                                labelAlign:'top',
                                                                labelWidth: 150,
                                                                fieldLabel: lang('SPB Code'),
                                                                id: 'Koltiva.view.Traceability_new.Delivery.MainForm-FormBasicData-SMESPCodeID',
                                                                name: 'Koltiva.view.Traceability_new.Delivery.MainForm-FormBasicData-SMESPCodeID',
                                                                store: thisObj.storeSPCode,
                                                                displayField: 'name',
                                                                valueField: 'id',
                                                                queryMode: 'local',
                                                                listeners: {
                                                                    change:function(val){
                                                                         
                                                                    }
                                                                }
                                                            },
                                                            {
                                                                xtype: 'numericfield',
                                                                labelWidth: 150,
                                                                fieldLabel: lang('Dest Weight (Kg)'),
                                                                id: 'Koltiva.view.Traceability_new.Delivery.MainForm-FormBasicData-DestWeight',
                                                                name: 'Koltiva.view.Traceability_new.Delivery.MainForm-FormBasicData-DestWeight',
                                                                readOnly: true,
                                                                hidden:true,
                                                                listeners: {
                                                                    change : function(){
                                                                    }
                                                                }
                                                            }
                                                        ]
                                                    },
                                                    {
                                                        columnWidth: 0.5,
                                                        layout: 'form',
                                                        padding:5,
                                                        items:[ 
                                                            {
                                                                xtype: 'datefield',
                                                                labelAlign:'top',
                                                                labelWidth: 150,
                                                                // allowBlank: false,
                                                                fieldLabel: lang('Shipping Date'),
                                                                id: 'Koltiva.view.Traceability_new.Delivery.MainForm-FormBasicData-ReceivedDate',
                                                                name: 'Koltiva.view.Traceability_new.Delivery.MainForm-FormBasicData-ReceivedDate',
                                                                format: 'Y-m-d H:i:s',
                                                                baseCls: 'Sfr_FormInputMandatory'
                                                            },
                                                            {
                                                                xtype: 'datefield',
                                                                labelAlign:'top',
                                                                labelWidth: 150,
                                                                // allowBlank: false,
                                                                fieldLabel: lang('Arrival Estimation'),
                                                                id: 'Koltiva.view.Traceability_new.Delivery.MainForm-FormBasicData-ArrivalEstimation',
                                                                name: 'Koltiva.view.Traceability_new.Delivery.MainForm-FormBasicData-ArrivalEstimation',
                                                                format: 'Y-m-d H:i:s',
                                                                baseCls: 'Sfr_FormInputMandatory'
                                                            },
                                                            {
                                                                xtype: 'textfield',
                                                                labelAlign:'top',
                                                                labelWidth: 150,
                                                                fieldLabel: lang('PO Number'),
                                                                id: 'Koltiva.view.Traceability_new.Delivery.MainForm-FormBasicData-DestPo',
                                                                name: 'Koltiva.view.Traceability_new.Delivery.MainForm-FormBasicData-DestPo',
                                                            },
                                                            {
                                                                xtype: 'numericfield',
                                                                labelAlign:'top',
                                                                labelWidth: 150,
                                                                // allowBlank: false,
                                                                fieldLabel: lang('Gross weight Dealer (kg)'),
                                                                id: 'Koltiva.view.Traceability_new.Delivery.MainForm-FormBasicData-TotalWeight',
                                                                name: 'Koltiva.view.Traceability_new.Delivery.MainForm-FormBasicData-TotalWeight',
                                                                baseCls: 'Sfr_FormInputMandatory'
                                                            },
                                                            {
                                                                xtype: 'numericfield',
                                                                labelAlign:'top',
                                                                labelWidth: 150,
                                                                fieldLabel: lang('Final Capacity'),
                                                                id: 'Koltiva.view.Traceability_new.Delivery.MainForm-FormBasicData-FinalCapacity',
                                                                name: 'Koltiva.view.Traceability_new.Delivery.MainForm-FormBasicData-FinalCapacity',
                                                                hidden:false,
                                                                listeners: {
                                                                    change : function(){
                                                                    }
                                                                }
                                                            },
                                                            {
                                                                xtype: 'numericfield',
                                                                labelAlign:'top',
                                                                labelWidth: 150,
                                                                fieldLabel: lang('Payment Delivery'),
                                                                id: 'Koltiva.view.Traceability_new.Delivery.MainForm-FormBasicData-PaymentDelivery',
                                                                name: 'Koltiva.view.Traceability_new.Delivery.MainForm-FormBasicData-PaymentDelivery',
                                                                hidden:false,
                                                                listeners: {
                                                                    change : function(){
                                                                    }
                                                                }
                                                            }
                                                        ]
                                                    }, 
                                                    {
                                                        columnWidth: 1,
                                                        layout: 'form',
                                                        padding:5,
                                                        items:[
                                                            {
                                                                xtype: 'fieldset',
                                                                title: lang('Data Driver'),
                                                                id : 'PanelDriver',
                                                                items: [{
                                                                    layout: 'column',
                                                                    items: [
                                                                        {                              
                                                                        columnWidth: 0.5,
                                                                        layout: 'form',
                                                                        padding:5,
                                                                        items:[{
                                                                            xtype: 'textfield',
                                                                            readOnly: false,
                                                                            labelWidth: 150,
                                                                            // allowBlank: false,
                                                                            fieldLabel: lang('Driver Name'),
                                                                            forfield: 'DestDriver',
                                                                            id: 'Koltiva.view.Traceability_new.Delivery.MainForm-FormBasicData-DestDriver',
                                                                            name: 'Koltiva.view.Traceability_new.Delivery.MainForm-FormBasicData-DestDriver',
                                                                            baseCls: 'Sfr_FormInputMandatory',
                                                                        }]
                                                                    },{                              
                                                                        columnWidth: 0.5,
                                                                        layout: 'form',
                                                                        padding:5,
                                                                        items:[
                                                                            {
                                                                                xtype: 'textfield',
                                                                                readOnly: false,
                                                                                labelWidth: 150,
                                                                                // allowBlank: false,
                                                                                fieldLabel: lang('license Plate'),
                                                                                forfield: 'DestTransportNumber',
                                                                                id: 'Koltiva.view.Traceability_new.Delivery.MainForm-FormBasicData-DestTransportNumber',
                                                                                name: 'Koltiva.view.Traceability_new.Delivery.MainForm-FormBasicData-DestTransportNumber',
                                                                                baseCls: 'Sfr_FormInputMandatory',
                                                                            },
                                                                            {
                                                                                xtype: 'combo',
                                                                                labelWidth: 150,
                                                                                fieldLabel: lang('Transportation Type'),
                                                                                id: 'Koltiva.view.Traceability_new.Delivery.MainForm-FormBasicData-DestTransportID',
                                                                                name: 'Koltiva.view.Traceability_new.Delivery.MainForm-FormBasicData-DestTransportID',
                                                                                store: thisObj.StoreComboTransportationType,
                                                                                displayField: 'label',
                                                                                valueField: 'id',
                                                                                queryMode: 'local',
                                                                                baseCls: 'Sfr_FormInputMandatory',
                                                                                enableKeyEvents: true,
                                                                                // allowBlank: false,
                                                                                listeners: {
                                                                                    keydown : function (field_, e_  )  {
                                                                                        e_.stopEvent();
                                                                                        return false;
                                                                                    }
                                                                                }
                                                                            }
                                                                        ]
                                                                    }]
                                                                }]
                                                            }
                                                         ]
                                                    }
                                                    ]
                                                }]
                                            }]
                                        }
                                    ]// end detail delivery
                                }, ]
                            }
                        ]
                        }],
                    buttons: [
                        {
                            xtype: 'button',
                            icon: varjs.config.base_url + 'images/icons/new/save.png',
                            text: lang('SEND'),
                            hidden:true,
                            cls: 'Sfr_BtnFormGreen',
                            overCls: 'Sfr_BtnFormGreen-Hover',
                            id: 'Koltiva.view.Traceability_new.Delivery.MainForm-FormBasicData-BtnSent',
                            handler: function () {
                                var Formnya = Ext.getCmp('Koltiva.view.Traceability_new.Delivery.MainForm-FormBasicData').getForm();

                                let status = Ext.getCmp('Koltiva.view.Traceability_new.Delivery.MainForm-FormBasicData-DeliveryStatusID').getValue();
                                let TotalWeight = Ext.getCmp('Koltiva.view.Traceability_new.Delivery.MainForm-FormBasicData-TotalWeight').getValue();

                                if (Formnya.isValid()) {
                                    if(status == '2' && TotalWeight == null || TotalWeight == '0'){
                                        Ext.MessageBox.show({
                                            title: lang('Attention'),
                                            msg: lang('Form not complete yet'),
                                            buttons: Ext.MessageBox.OK,
                                            animateTarget: 'mb9',
                                            icon: 'ext-mb-info'
                                        });
                                    } else {
                                        Formnya.submit({
                                            url: m_api + '/traceability_api/delivery/submit_send',
                                            method: 'PUT',
                                            waitMsg: 'Saving data...',
                                            params: {
                                                OpsiDisplay: thisObj.viewVar.OpsiDisplay
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
                                                            Ext.getCmp('Koltiva.view.Traceability_new.Delivery.MainForm').destroy(); //destory current view
                                                            var MainForm = [];
                                                            if (Ext.getCmp('Koltiva.view.Traceability_new.Delivery.MainForm') == undefined) {
                                                                MainForm = Ext.create('Koltiva.view.Traceability_new.Delivery.MainForm', {
                                                                    viewVar: {
                                                                        OpsiDisplay: 'update',
                                                                        DeliveryID: o.result.DeliveryID,
                                                                        DeliveryStatusID : o.result.DeliveryStatusID
                                                                    }
                                                                });
                                                            } else {
                                                                Ext.getCmp('Koltiva.view.Traceability_new.Delivery.MainForm').destroy();
                                                                MainForm = Ext.create('Koltiva.view.Traceability_new.Delivery.MainForm', {
                                                                    viewVar: {
                                                                        OpsiDisplay: 'update',
                                                                        DeliveryID: o.result.DeliveryID,
                                                                        DeliveryStatusID : o.result.DeliveryStatusID
                                                                    }
                                                                });
                                                            }
                                                        }
                                                    }
                                                });            
                                            },
                                            failure: function (fp, o) {
                                                try {
                                                    var r = Ext.decode(o.response.responseText);
                                                    Ext.MessageBox.show({
                                                        title: 'Error',
                                                        msg: (r.error) ? r.error : r.message,
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
                                    }

                                    Ext.getCmp('Koltiva.view.Traceability_new.Delivery.PanelDataDeliveryPick-ActionColumn').hide();
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
                        },
                        {
                            xtype: 'button',
                            icon: varjs.config.base_url + 'images/icons/new/save.png',
                            text: lang('Save'),
                            cls: 'Sfr_BtnFormBlue',
                            overCls: 'Sfr_BtnFormBlue-Hover',
                            id: 'Koltiva.view.Traceability_new.Delivery.MainForm-FormBasicData-BtnSave',
                            handler: function () {
                                var Formnya = Ext.getCmp('Koltiva.view.Traceability_new.Delivery.MainForm-FormBasicData').getForm();
                                
                                let status = Ext.getCmp('Koltiva.view.Traceability_new.Delivery.MainForm-FormBasicData-DeliveryStatusID').getValue();
                                let TotalWeight = Ext.getCmp('Koltiva.view.Traceability_new.Delivery.MainForm-FormBasicData-TotalWeight').getValue();
                                
                                if (Formnya.isValid()) {
                                    if(status == '2' && TotalWeight == null || TotalWeight == '0'){
                                        Ext.MessageBox.show({
                                            title: lang('Attention'),
                                            msg: lang('Form not complete yet'),
                                            buttons: Ext.MessageBox.OK,
                                            animateTarget: 'mb9',
                                            icon: 'ext-mb-info'
                                        });
                                    } else {
                                        Formnya.submit({
                                            url: m_api + '/traceability_api/delivery/submit_delivery',
                                            method: 'POST',
                                            waitMsg: 'Saving data...',
                                            params: {
                                                OpsiDisplay: thisObj.viewVar.OpsiDisplay
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
                                                            Ext.getCmp('Koltiva.view.Traceability_new.Delivery.MainForm').destroy(); //destory current view
                                                            var MainForm = [];
                                                            if (Ext.getCmp('Koltiva.view.Traceability_new.Delivery.MainForm') == undefined) {
                                                                MainForm = Ext.create('Koltiva.view.Traceability_new.Delivery.MainForm', {
                                                                    viewVar: {
                                                                        OpsiDisplay: 'update',
                                                                        DeliveryID: o.result.DeliveryID,
                                                                        DeliveryStatusID : o.result.DeliveryStatusID
                                                                    }
                                                                });
                                                            } else {
                                                                Ext.getCmp('Koltiva.view.Traceability_new.Delivery.MainForm').destroy();
                                                                MainForm = Ext.create('Koltiva.view.Traceability_new.Delivery.MainForm', {
                                                                    viewVar: {
                                                                        OpsiDisplay: 'update',
                                                                        DeliveryID: o.result.DeliveryID,
                                                                        DeliveryStatusID : o.result.DeliveryStatusID
                                                                    }
                                                                });
                                                            }
                                                        }
                                                    }
                                                });
                                            },
                                            failure: function (fp, o) {
                                                try {
                                                    var r = Ext.decode(o.response.responseText);
                                                    Ext.MessageBox.show({
                                                        title: 'Error',
                                                        msg: (r.error) ? r.error : r.message,
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
                                    }
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
                        }
                    ]
                }]
        });
        //Panel Basic ==================================== (End)

        function mandatoryField(status){
            if(status=='2' || status=='3'){
                Ext.getCmp('Delivery-Destination').allowBlank=false;
                Ext.getCmp('Delivery-Destination').validateValue(Ext.getCmp('Delivery-Destination').getValue());

                Ext.getCmp('Delivery-DestPO').allowBlank=false;
                Ext.getCmp('Delivery-DestPO').validateValue(Ext.getCmp('Delivery-DestPO').getValue());

                Ext.getCmp('Delivery-DestWeight').allowBlank=false;
                Ext.getCmp('Delivery-DestWeight').validate(Ext.getCmp('Delivery-DestWeight').getValue());

                // Ext.getCmp('Delivery-PackageNumber').allowBlank=false;
                // Ext.getCmp('Delivery-PackageNumber').validate(Ext.getCmp('Delivery-PackageNumber').getValue());

                Ext.getCmp('Delivery-InvoiceNumber').allowBlank=false;
                Ext.getCmp('Delivery-InvoiceNumber').validate(Ext.getCmp('Delivery-InvoiceNumber').getValue());

                Ext.getCmp('Delivery-PackingNumber').allowBlank=false;
                Ext.getCmp('Delivery-PackingNumber').validate(Ext.getCmp('Delivery-PackingNumber').getValue());

                Ext.getCmp('Delivery-PriceKg').allowBlank=false;
                Ext.getCmp('Delivery-PriceKg').validate(Ext.getCmp('Delivery-PriceKg').getValue());
                
                Ext.getCmp('Delivery-BiayaPengiriman').allowBlank=false;
                Ext.getCmp('Delivery-BiayaPengiriman').validate(Ext.getCmp('Delivery-BiayaPengiriman').getValue());

                
            }

        }

        //========================================================== LAYOUT UTAMA (Begin) ========================================//
        thisObj.items = [{
                xtype: 'panel',
                border: false,
                layout: {
                    type: 'hbox'
                },
                items: [{
                        id: 'Koltiva.view.Traceability_new.Delivery.MainForm-labelInfoInsert',
                        html: '<div id="header_title_farmer">' + lang('Selling') + '</div>'
                    }]
            }, {
                items: [{
                        id: 'Koltiva.view.Traceability_new.Delivery.MainForm-LinkBackToList',
                        html: '<div id="Sfr_IdBoxInfoDataGrid" class="Sfr_BoxInfoDataGrid"><ul class="Sft_UlListInfoDataGrid"><li class="Sft_ListInfoDataGrid"><a href="javascript:Ext.getCmp(\'Koltiva.view.Traceability_new.Delivery.MainForm\').BackToList()"><img class="Sft_ListIconInfoDataGrid" src="' + varjs.config.base_url + 'images/icons/new/back.png" width="20" />&nbsp;&nbsp;' + lang('Back to Selling List') + '</a></li></div>'
                    }]
            }, {
                html: '<br />'
            }, {
                layout: 'column',
                border: false,
                items: [{
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
        Ext.getCmp('Koltiva.view.Traceability_new.Delivery.MainForm').destroy(); //destory current view
        var GridMain = [];
        if (Ext.getCmp('Koltiva.view.Traceability_new.Delivery.MainGrid') == undefined) {
            GridMain = Ext.create('Koltiva.view.Traceability_new.Delivery.MainGrid');
        } else {
            //destroy, create ulang
            Ext.getCmp('Koltiva.view.Traceability_new.Delivery.MainGrid').destroy();
            GridMain = Ext.create('Koltiva.view.Traceability_new.Delivery.MainGrid');
        }
    }
});