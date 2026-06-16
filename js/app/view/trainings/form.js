Ext.define('Koltiva.view.trainings.form', {
    extend: 'Ext.form.Panel',
    xtype: 'trainings-form',    
    
    // frame: true,
    // title: 'Training',
    bodyPadding: 5,
    autoScroll:true,
    // width: 355,
    
    // The form will submit an AJAX request to this URL when submitted
    url: m_crud,

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
                    { inputType: 'hidden', name: 'CpgTrainingsID',},
                    { allowBlank:false, fieldLabel: lang('Parent'), name: 'ParentID', xtype: 'combo', store: Ext.create('Koltiva.store.trainings.parent'), valueField: 'id', displayField: 'label', queryMode: 'local', },
                    { allowBlank:false, fieldLabel: lang('Name'), name: 'CpgTrainings', emptyText: 'Name', inputType: 'text' },
                    { allowBlank:true, fieldLabel: lang('Abbreviation'), name: 'CpgAbbre', emptyText: 'Abbreviation', inputType: 'text' }
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
                method: form.findField('CpgTrainingsID').getValue()?'PUT':'POST',
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