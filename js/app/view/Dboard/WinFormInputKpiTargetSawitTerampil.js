Ext.define('Koltiva.view.Dboard.WinFormInputKpiTargetSawitTerampil' ,{
    extend: 'Ext.window.Window',
    id: 'Koltiva.view.Dboard.WinFormInputKpiTargetSawitTerampil',
    cls: 'Sfr_LayoutPopupWindows',
    title: lang('KPI Target Sawit Terampil Form'),
    closable: true,
    modal: true,
    closeAction: 'destroy',
    width: '70%',
    height: 500,
    overflowY: 'auto',
    viewVar: false,
    setViewVar: function(value){
        this.viewVar = value;
    },
    initComponent: function() {
        var thisObj = this;
        let labelWidth = 200;

        var cmb_program = Ext.create('Koltiva.store.Dboard.CmbProgram');
        var cmb_cluster = Ext.create('Koltiva.store.Dboard.CmbCluster');
        var cmb_year    = Ext.create('Koltiva.store.ComboGeneral.CmbYearOption');
        cmb_year.setStoreVar({yearRange:10, yearOrder:true});
        cmb_year.load();

        //items -------------------------------------------------------------- (begin)
        thisObj.items = [{
        	xtype: 'form',
            id: 'Koltiva.view.Dboard.WinFormInputKpiTargetSawitTerampil-Form',
            padding:'5 25 5 8',
            items:[{
                layout: 'column',
                border: false,
                items:[{
                    columnWidth: 0.5,
                    layout:'form',
                    items:[{
                        xtype: 'hiddenfield',
                        id: 'Koltiva.view.Dboard.WinFormInputKpiTargetSawitTerampil-Form-TargetID',
                        name: 'Koltiva.view.Dboard.WinFormInputKpiTargetSawitTerampil-Form-TargetID'
                    },{
                        xtype: 'combobox',
                        id: 'Koltiva.view.Dboard.WinFormInputKpiTargetSawitTerampil-Form-ProgID',
                        name: 'Koltiva.view.Dboard.WinFormInputKpiTargetSawitTerampil-Form-ProgID',
                        store: cmb_program,
                        fieldLabel: lang('Program'),
                        labelAlign:'top',
                        queryMode: 'local',
                        displayField: 'label',
                        allowBlank:false,
                        baseCls: 'Sfr_FormInputMandatory',
                        valueField: 'id',
                        listeners: {
                            change: function(cb, nv, ov) {
                                cmb_cluster.load({
                                    params: {
                                        ProgID: nv
                                    }
                                });
                            }
                        }
                    },{
                        html:'<div></div>',
                    },{
                        xtype: 'combobox',
                        id: 'Koltiva.view.Dboard.WinFormInputKpiTargetSawitTerampil-Form-ClusterID',
                        name: 'Koltiva.view.Dboard.WinFormInputKpiTargetSawitTerampil-Form-ClusterID',
                        store: cmb_cluster,
                        fieldLabel: lang('Cluster'),
                        allowBlank:false,
                        baseCls: 'Sfr_FormInputMandatory',
                        labelAlign:'top',
                        queryMode: 'local',
                        displayField: 'label',
                        valueField: 'id'
                    },{
                        html:'<div></div>',
                    },{
                        xtype: 'numberfield',
                        id: 'Koltiva.view.Dboard.WinFormInputKpiTargetSawitTerampil-Form-KsMill',
                        name: 'Koltiva.view.Dboard.WinFormInputKpiTargetSawitTerampil-Form-KsMill',
                        fieldLabel: lang('Mill Ksatria Sawit'),
                        labelAlign:'top',
                        allowNegative: false,
                        minValue: 0
                    },{
                        html:'<div></div>',
                    },{
                        xtype: 'numberfield',
                        id: 'Koltiva.view.Dboard.WinFormInputKpiTargetSawitTerampil-Form-StMill',
                        name: 'Koltiva.view.Dboard.WinFormInputKpiTargetSawitTerampil-Form-StMill',
                        fieldLabel: lang('Mill Sawit Terampil'),
                        labelAlign:'top',
                        allowNegative: false,
                        minValue: 0
                    },{
                        html:'<div></div>',
                    },{
                        xtype: 'numberfield',
                        id: 'Koltiva.view.Dboard.WinFormInputKpiTargetSawitTerampil-Form-FarmerReg',
                        name: 'Koltiva.view.Dboard.WinFormInputKpiTargetSawitTerampil-Form-FarmerReg',
                        fieldLabel: lang('Farmer Registration'),
                        labelAlign:'top',
                        allowNegative: false,
                        minValue: 0
                    },{
                        html:'<div></div>',
                    },{
                        xtype: 'numberfield',
                        id: 'Koltiva.view.Dboard.WinFormInputKpiTargetSawitTerampil-Form-FarmReg',
                        name: 'Koltiva.view.Dboard.WinFormInputKpiTargetSawitTerampil-Form-FarmReg',
                        fieldLabel: lang('Farm Registration'),
                        labelAlign:'top',
                        allowNegative: false,
                        minValue: 0
                    },{
                        html:'<div></div>',
                    },{
                        xtype: 'numberfield',
                        id: 'Koltiva.view.Dboard.WinFormInputKpiTargetSawitTerampil-Form-Ha',
                        name: 'Koltiva.view.Dboard.WinFormInputKpiTargetSawitTerampil-Form-Ha',
                        fieldLabel: lang('Ha Registration'),
                        labelAlign:'top',
                        allowNegative: false,
                        minValue: 0
                    },{
                        html:'<div></div>',
                    },{
                        xtype: 'numberfield',
                        id: 'Koltiva.view.Dboard.WinFormInputKpiTargetSawitTerampil-Form-SocSel',
                        name: 'Koltiva.view.Dboard.WinFormInputKpiTargetSawitTerampil-Form-SocSel',
                        fieldLabel: lang('Socialization & Selection'),
                        labelAlign:'top',
                        allowNegative: false,
                        minValue: 0
                    },{
                        html:'<div></div>',
                    },{
                        xtype: 'numberfield',
                        id: 'Koltiva.view.Dboard.WinFormInputKpiTargetSawitTerampil-Form-FarmerSurveyBP',
                        name: 'Koltiva.view.Dboard.WinFormInputKpiTargetSawitTerampil-Form-FarmerSurveyBP',
                        fieldLabel: lang('Farmer Survey'),
                        labelAlign:'top',
                        allowNegative: false,
                        minValue: 0
                    },{
                        html:'<div></div>',
                    },{
                        xtype: 'numberfield',
                        id: 'Koltiva.view.Dboard.WinFormInputKpiTargetSawitTerampil-Form-FarmSurvey',
                        name: 'Koltiva.view.Dboard.WinFormInputKpiTargetSawitTerampil-Form-FarmSurvey',
                        fieldLabel: lang('Farm Survey'),
                        labelAlign:'top',
                        allowNegative: false,
                        minValue: 0
                    }]
                },{
                    columnWidth: 0.495,
                    style:'padding-left:10px',
                    layout:'form',
                    items:[{
                        xtype: 'combobox',
                        id: 'Koltiva.view.Dboard.WinFormInputKpiTargetSawitTerampil-Form-Year',
                        name: 'Koltiva.view.Dboard.WinFormInputKpiTargetSawitTerampil-Form-Year',
                        store: cmb_year,
                        fieldLabel: lang('Target Year'),
                        allowBlank:false,
                        baseCls: 'Sfr_FormInputMandatory',
                        labelAlign:'top',
                        queryMode: 'local',
                        displayField: 'label',
                        valueField: 'id'
                    },{
                        html:'<div></div>',
                    },{
                        xtype: 'numberfield',
                        id: 'Koltiva.view.Dboard.WinFormInputKpiTargetSawitTerampil-Form-Polygon',
                        name: 'Koltiva.view.Dboard.WinFormInputKpiTargetSawitTerampil-Form-Polygon',
                        fieldLabel: lang('Polygon'),
                        labelAlign:'top',
                        allowNegative: false,
                        minValue: 0
                    },{
                        html:'<div></div>',
                    },{
                        xtype: 'numberfield',
                        id: 'Koltiva.view.Dboard.WinFormInputKpiTargetSawitTerampil-Form-FarmerCoach',
                        name: 'Koltiva.view.Dboard.WinFormInputKpiTargetSawitTerampil-Form-FarmerCoach',
                        fieldLabel: lang('Farmer Coaching'),
                        labelAlign:'top',
                        allowNegative: false,
                        minValue: 0
                    },{
                        html:'<div></div>',
                    },{
                        xtype: 'numberfield',
                        id: 'Koltiva.view.Dboard.WinFormInputKpiTargetSawitTerampil-Form-CoachingSess',
                        name: 'Koltiva.view.Dboard.WinFormInputKpiTargetSawitTerampil-Form-CoachingSess',
                        fieldLabel: lang('Coaching Session'),
                        labelAlign:'top',
                        allowNegative: false,
                        minValue: 0
                    },{
                        html:'<div></div>',
                    },{
                        xtype: 'numberfield',
                        id: 'Koltiva.view.Dboard.WinFormInputKpiTargetSawitTerampil-Form-Sms',
                        name: 'Koltiva.view.Dboard.WinFormInputKpiTargetSawitTerampil-Form-Sms',
                        fieldLabel: lang('SMS Broadcast'),
                        labelAlign:'top',
                        allowNegative: false,
                        minValue: 0
                    },{
                        html:'<div></div>',
                    },{
                        xtype: 'numberfield',
                        id: 'Koltiva.view.Dboard.WinFormInputKpiTargetSawitTerampil-Form-IdCard',
                        name: 'Koltiva.view.Dboard.WinFormInputKpiTargetSawitTerampil-Form-IdCard',
                        fieldLabel: lang('ID Card'),
                        labelAlign:'top',
                        allowNegative: false,
                        minValue: 0
                    },{
                        html:'<div></div>',
                    },{
                        xtype: 'numberfield',
                        id: 'Koltiva.view.Dboard.WinFormInputKpiTargetSawitTerampil-Form-FarmX',
                        name: 'Koltiva.view.Dboard.WinFormInputKpiTargetSawitTerampil-Form-FarmX',
                        fieldLabel: lang('FarmXtenstion Users'),
                        labelAlign:'top',
                        allowNegative: false,
                        minValue: 0
                    },{
                        html:'<div></div>',
                    },{
                        xtype: 'numberfield',
                        id: 'Koltiva.view.Dboard.WinFormInputKpiTargetSawitTerampil-Form-FarmG',
                        name: 'Koltiva.view.Dboard.WinFormInputKpiTargetSawitTerampil-Form-FarmG',
                        fieldLabel: lang('FarmGate Users'),
                        labelAlign:'top',
                        allowNegative: false,
                        minValue: 0
                    },{
                        html:'<div></div>',
                    },{
                        xtype: 'numberfield',
                        id: 'Koltiva.view.Dboard.WinFormInputKpiTargetSawitTerampil-Form-FarmR',
                        name: 'Koltiva.view.Dboard.WinFormInputKpiTargetSawitTerampil-Form-FarmR',
                        fieldLabel: lang('FarmRetail Users'),
                        labelAlign:'top',
                        allowNegative: false,
                        minValue: 0
                    },{
                        html:'<div></div>',
                    },{
                        xtype: 'numberfield',
                        id: 'Koltiva.view.Dboard.WinFormInputKpiTargetSawitTerampil-Form-FarmC',
                        name: 'Koltiva.view.Dboard.WinFormInputKpiTargetSawitTerampil-Form-FarmC',
                        fieldLabel: lang('FarmCloud Users'),
                        labelAlign:'top',
                        allowNegative: false,
                        minValue: 0
                    }]
                }]
            }]
        }];
        //items -------------------------------------------------------------- (end)

        //buttons -------------------------------------------------------------- (begin)
        thisObj.buttons = [{
            icon: varjs.config.base_url + 'images/icons/new/save.png',
            cls:'Sfr_BtnFormBlue',
            overCls:'Sfr_BtnFormBlue-Hover',
            text: lang('Save'),
            id: 'Koltiva.view.Dboard.WinFormInputKpiTargetSawitTerampil-Form-BtnSave',
            handler: function () {
                var FormNya = Ext.getCmp('Koltiva.view.Dboard.WinFormInputKpiTargetSawitTerampil-Form').getForm();
                if (FormNya.isValid()) {

                    FormNya.submit({
                        url: m_api + '/kpi_general/kpi_target_sawit_terampil',
                        method:'POST',
                        waitMsg: 'Saving data...',
                        success: function(fp, o) {
                            Ext.MessageBox.show({
                                title: 'Information',
                                msg: lang('Data saved'),
                                buttons: Ext.MessageBox.OK,
                                animateTarget: 'mb9',
                                icon: 'ext-mb-success'
                            });

                            //form reset
                            FormNya.reset();

                            Ext.data.StoreManager.lookup('Koltiva.store.Dboard.MainGridKpiTargetSawitTerampil').load();
                            Ext.data.StoreManager.lookup('Koltiva.store.Dboard.CmbFilterYearKpiSawitTarget').load();
                            

                            //tutup popup
                            thisObj.close();
                        },
                        failure: function(rp, o) {
                            var r = Ext.decode(o.response.responseText);
                            
                            try {
                                Ext.MessageBox.show({
                                    title: 'Error',
                                    msg: r.message,
                                    buttons: Ext.MessageBox.OK,
                                    animateTarget: 'mb9',
                                    icon: 'ext-mb-error'
                                });
                            }
                            catch(err) {
                                Ext.MessageBox.show({
                                    title: 'Error',
                                    msg: r.message,
                                    buttons: Ext.MessageBox.OK,
                                    animateTarget: 'mb9',
                                    icon: 'ext-mb-error'
                                });
                            }
                        }
                    });

                }else{
                    Ext.MessageBox.show({
                        title: lang('Attention'),
                        msg: lang('Form not complete yet'),
                        buttons: Ext.MessageBox.OK,
                        animateTarget: 'mb9',
                        icon: 'ext-mb-info'
                    });
                }
            }
        },{
            icon: varjs.config.base_url + 'images/icons/new/close.png',
			text: lang('Close'),
			cls:'Sfr_BtnFormGrey',
			overCls:'Sfr_BtnFormGrey-Hover',
            handler: function() {
                thisObj.close();
            }
        }];
        //buttons -------------------------------------------------------------- (end)

        this.callParent(arguments);
    },
    listeners: {
        afterRender: function(){
            var thisObj = this;

            //form reset
            var formNya = Ext.getCmp('Koltiva.view.Dboard.WinFormInputKpiTargetSawitTerampil-Form');
            formNya.getForm().reset();

            //set MemberID
            Ext.getCmp('Koltiva.view.Dboard.WinFormInputKpiTargetSawitTerampil-Form-TargetID').setValue(thisObj.viewVar.TargetID);

            if(thisObj.viewVar.opsiDisplay == 'update' || thisObj.viewVar.opsiDisplay == 'view'){
                formNya.getForm().load({
                    url: m_api + '/kpi_general/target_sawit_terampil_form',
                    method: 'GET',
                    params: {
                        TargetID: thisObj.viewVar.TargetID
                    },
                    success: function(form, action) {
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