Ext.define('Koltiva.view.Grower.FormSetPartner', {
    extend: 'Ext.form.Panel',
    xtype: 'trainings-form',    
    
    // frame: true,
    // title: 'Training',
    bodyPadding: 5,
    autoScroll: false,
    width: 'auto',
    height: 170,
    
    // The form will submit an AJAX request to this URL when submitted
    url: m_api + '/grower/member_basic_data_form',

    fieldDefaults: {
        labelAlign: 'left',
        labelWidth: 115,
        msgTarget: 'side'
    },

    initComponent: function() {
        Ext.apply(this, {
            items : [
            {
                xtype: 'fieldset',
                // title: lang('Training Data'),
                defaultType: 'textfield',
                width: 'auto',
                defaults: {
                    anchor: '100%'
                },
                items: [
                    {
                        xtype: 'hiddenfield',
                        id: 'Koltiva.view.Grower.FormMainGrower-MemberID',
                        name: 'Koltiva.view.Grower.FormMainGrower-MemberID'
                    },
                    { 
                        inputType: 'text', 
                        name: 'Koltiva.view.Grower.FormMainGrower-MemberDisplayID',
                        fieldLabel: lang('Farmer ID'),
                        readOnly: true,
                    },
                    { 
                        inputType: 'text', 
                        name: 'Koltiva.view.Grower.FormMainGrower-Fullname',
                        fieldLabel: lang('Name'),
                        readOnly: true,
                    },
                    { 
                        allowBlank:false, 
                        fieldLabel: lang('Partner'), 
                        name: 'PartnerID',
                        xtype: 'combo', 
                        store: Ext.create('Koltiva.store.ComboGeneral.ComboPartner'), 
                        valueField: 'id', 
                        displayField: 'label', 
                        queryMode: 'local', 
                    }
                ]
            },
            ],
            buttons: [{
                text: lang('Save'),
                // disabled: true,
                formBind: true,
                scope: this,
                handler: this.onCompleteClick,
                icon: varjs.config.base_url + 'images/icons/new/save.png',
                cls: 'Sfr_BtnFormBlue',
                overCls: 'Sfr_BtnFormBlue-Hover',
            }],
        });   

        this.callParent();
    },
    
    onCompleteClick: function(){
        var form = this.getForm();
        if (form.isValid()) {
            // Ext.MessageBox.alert('Submitted Values', form.getValues(true));
            form.submit({
                url: m_api+'/grower/setPartnerFarmer',
                method : 'POST',
                success: function(form, action) {
                    Ext.Msg.alert('Success', action.result.message);
                    win.close();
                    Ext.getCmp('view.Grower.GridMainGrower-gridMainGrid').getStore().load();
                },
                failure: function(form, action) {
                    Ext.Msg.alert('Failed', action.result.message);
                }
            });
        }
    },
});