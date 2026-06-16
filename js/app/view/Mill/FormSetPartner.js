Ext.define('Koltiva.view.Mill.FormSetPartner', {
    extend: 'Ext.form.Panel',
    xtype: 'form',    
    
    // frame: true,
    // title: 'Training',
    // bodyPadding: 5,
    // autoScroll:true,
    width: '100%',
    height: 200,
    
    // The form will submit an AJAX request to this URL when submitted
    url: m_api + '/mill/mill_basic_data_form',

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
                // width: '95%',
                defaults: {
                    anchor: '100%'
                },
                items: [
                    { 
                        inputType: 'text', 
                        name: 'Koltiva.view.Mill.FormMainMill-FormBasicData-MillID',
                        fieldLabel: lang('Mill ID'),
                        readOnly: true,
                    },
                    { 
                        inputType: 'text', 
                        name: 'Koltiva.view.Mill.FormMainMill-FormBasicData-MillName',
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
                handler: this.onCompleteClick
            }],
        });   

        this.callParent();
    },
    
    onCompleteClick: function(){
        var form = this.getForm();
        if (form.isValid()) {
            // Ext.MessageBox.alert('Submitted Values', form.getValues(true));
            form.submit({
                url: m_api+'/mill/setPartnerMill',
                method : 'POST',
                success: function(form, action) {
                    Ext.Msg.alert('Success', action.result.message);
                    win.close();
                    Ext.getCmp('Koltiva.view.Mill.GridMainMill-gridMainGrid').getStore().load();
                },
                failure: function(form, action) {
                    Ext.Msg.alert('Failed', action.result.message);
                }
            });
        }
    },
});