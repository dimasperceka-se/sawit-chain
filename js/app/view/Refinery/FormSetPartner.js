Ext.define('Koltiva.view.Refinery.FormSetPartner', {
    extend: 'Ext.form.Panel',
    id: 'Koltiva.view.Refinery.FormSetPartner',
    modal: true,
    closeAction: 'destroy',
    overflowY: 'auto',
    width: '100%',
    height: 200,

    // The form will submit an AJAX request to this URL when submitted
    url: m_api + '/refinery/refinery_basic_data_form',

    fieldDefaults: {
        labelAlign: 'left',
        labelWidth: 115,
        msgTarget: 'side'
    },

    initComponent: function() {
            var thisObj = this;
        
            thisObj.items = [
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
                        name: 'Koltiva.view.Refinery.FormMainRefinery-FormBasicData-RefineryID',
                        fieldLabel: lang('Refinery ID'),
                        readOnly: true,
                    },
                    { 
                        inputType: 'text', 
                        name: 'Koltiva.view.Refinery.FormMainRefinery-FormBasicData-RefineryName',
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
            thisObj.buttons = [{
                text: lang('Save'),
                formBind: true,
                scope: this,
                handler: this.onCompleteClick,
                icon: varjs.config.base_url + 'images/icons/new/save.png',
                cls: 'Sfr_BtnFormBlue',
                overCls: 'Sfr_BtnFormBlue-Hover',
            }],

        this.callParent();
    },
    onCompleteClick: function(){
        var form = this.getForm();
        if (form.isValid()) {
            // Ext.MessageBox.alert('Submitted Values', form.getValues(true));
            form.submit({
                url: m_api+'/refinery/setPartnerRefinery',
                method : 'POST',
                success: function(form, action) {
                    Ext.Msg.alert('Success', action.result.message);
                    win.close();
                    Ext.getCmp('Koltiva.view.Refinery.GridMainRefinery-gridMainGrid').getStore().load();
                },
                failure: function(form, action) {
                    Ext.Msg.alert('Failed', action.result.message);
                }
            });
        }
    },
});