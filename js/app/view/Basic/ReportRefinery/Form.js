Ext.define('Koltiva.view.Basic.ReportRefinery.Form', {
    extend: 'Ext.form.Panel',
    xtype: 'trainings-form',    
    
    // frame: true,
    // title: 'Training',
    bodyPadding: 5,
    autoScroll:true,
    // width: 355,
    
    // The form will submit an AJAX request to this URL when submitted
    url: m_crud+'s',

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
                width: '95%',
                defaults: {
                    anchor: '100%'
                },
                items: [
                    { inputType: 'hidden', name: 'PartnerID',},
                    { readOnly:true,allowBlank:false, fieldLabel: lang('Parent'), name: 'PartnerParentID', xtype: 'combo', store: Ext.create('Koltiva.store.Basic.ReportRefinery.Parent'), valueField: 'id', displayField: 'label', queryMode: 'local', },
                    { readOnly:true,allowBlank:false, fieldLabel: lang('Name'), name: 'PartnerFullName', emptyText: 'Name', inputType: 'text' }
                ]
            },
            ]
        });   

        this.callParent();
    },
    onCompleteClick: function(){
        var form = this.getForm();
        if (form.isValid()) {
            // Ext.MessageBox.alert('Submitted Values', form.getValues(true));
            form.submit({
                method: form.findField('PartnerID').getValue()?'PUT':'POST',
                success: function(form, action) {
                    Ext.Msg.alert('Success', action.result.msg);
                    grid.store.load();
                    win.close();
                },
                failure: function(form, action) {
                    Ext.Msg.alert('Failed', action.result.msg);
                }
            });
        }
    },
});