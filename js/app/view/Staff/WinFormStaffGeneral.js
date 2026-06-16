Ext.define('Koltiva.view.Staff.WinFormStaffGeneral' ,{
    extend: 'Ext.window.Window',
    id: 'Koltiva.view.Staff.WinFormStaffGeneral',
    title: lang('Form Staff'),
    closable: true,
    modal: true,
    closeAction: 'destroy',
    width: '45%',
    height: '60%',
    overflowY: 'auto',
    viewVar: false,
    setViewVar: function(value){
        this.viewVar = value;
    },
    initComponent: function() {
        var thisObj = this;

        //store (begin)
        var cmb_position = Ext.create('Koltiva.store.Staff.RegisterStaff.ComboPosition',{
        	storeVar:{
        		ObjType : thisObj.viewVar.callFromRole
        	}
        });
        cmb_position.load();

        var cmb_wage_period = Ext.create('Koltiva.store.PlotSurvey.CmbWagePeriod');
        //store (end)

        //items -------------------------------------------------------------- (begin)
        thisObj.items = [{
            xtype: 'form',
            id: 'Koltiva.view.Staff.WinFormStaffGeneral-Form',
            padding:'5 25 5 8',
            items:[{
                layout: 'column',
                border: false,
                items:[{
                    columnWidth: 1,
                    layout:'form',
                    items:[{
                    	xtype: 'hiddenfield',
                        id: 'Koltiva.view.Staff.WinFormStaffGeneral-Form-callerObjID',
                        name: 'Koltiva.view.Staff.WinFormStaffGeneral-Form-callerObjID'
                    },{
                    	xtype: 'hiddenfield',
                        id: 'Koltiva.view.Staff.WinFormStaffGeneral-Form-callFromRole',
                        name: 'Koltiva.view.Staff.WinFormStaffGeneral-Form-callFromRole'
                    },{
                    	xtype: 'hiddenfield',
                        id: 'Koltiva.view.Staff.WinFormStaffGeneral-Form-StaffID',
                        name: 'Koltiva.view.Staff.WinFormStaffGeneral-Form-StaffID'
                    },{
                    	xtype: 'hiddenfield',
                        id: 'Koltiva.view.Staff.WinFormStaffGeneral-Form-PersonID',
                        name: 'Koltiva.view.Staff.WinFormStaffGeneral-Form-PersonID'
                    },{
                    	xtype: 'textfield',
                        id: 'Koltiva.view.Staff.WinFormStaffGeneral-Form-Name',
                        name: 'Koltiva.view.Staff.WinFormStaffGeneral-Form-Name',
                        fieldLabel: lang('Name'),
                        labelWidth: 200,
                        allowBlank: false
                    },{
                    	xtype: 'datefield',
                        id: 'Koltiva.view.Staff.WinFormStaffGeneral-Form-DateBirth',
                        name: 'Koltiva.view.Staff.WinFormStaffGeneral-Form-DateBirth',
                        fieldLabel: lang('Date of Birth'),
                        allowBlank: false,
                        labelWidth: 200,
                        format: 'Y-m-d',
                        listeners: {
                            change: function(cb, nv, ov) {
                                if( typeof nv === 'undefined' || nv === null ){
                                    return false;
                                }else{
                                    var ageDifMs = Date.now() - nv.getTime();
                                    var ageDate = new Date(ageDifMs); // miliseconds from epoch
                                    var age = Math.abs(ageDate.getUTCFullYear() - 1970);
    
                                    Ext.getCmp('Koltiva.view.Staff.WinFormStaffGeneral-Form-Age').setValue(age);
                                }
                            }
                        }
                    },{
                    	xtype: 'numericfield',
                        id: 'Koltiva.view.Staff.WinFormStaffGeneral-Form-Age',
                        name: 'Koltiva.view.Staff.WinFormStaffGeneral-Form-Age',
                        fieldLabel: lang('Age'),
                        readOnly: true,
                        labelWidth: 200
                    },{
                    	fieldLabel: lang('Gender'),
                        xtype: 'radiogroup',
                        labelWidth: 200,
                        columns: 2,
                        allowBlank: false,
                        msgTarget: 'side',
                        items:[{
                            boxLabel: lang('Male'),
                            name: 'Koltiva.view.Staff.WinFormStaffGeneral-Form-Gender',
                            inputValue: 'm',
                            id: 'Koltiva.view.Staff.WinFormStaffGeneral-Form-Gender1',
                            listeners:{
                                change: function(){
                                    return false;
                                }
                            }
                        },{
                            boxLabel: lang('Female'),
                            name: 'Koltiva.view.Staff.WinFormStaffGeneral-Form-Gender',
                            inputValue: 'f',
                            id: 'Koltiva.view.Staff.WinFormStaffGeneral-Form-Gender2',
                            listeners:{
                                change: function(){
                                    return false;
                                }
                            }
                        }]
                    },{
                    	xtype: 'combobox',
                        id: 'Koltiva.view.Staff.WinFormStaffGeneral-Form-PositionID',
                        name: 'Koltiva.view.Staff.WinFormStaffGeneral-Form-PositionID',
                        store: cmb_position,
                        fieldLabel: lang('Position'),
                        labelWidth: 200,
                        allowBlank: false,
                        queryMode: 'local',
                        displayField: 'label',
                        valueField: 'id'
                    },{
                    	xtype: 'numericfield',
                        id: 'Koltiva.view.Staff.WinFormStaffGeneral-Form-WageAmount',
                        name: 'Koltiva.view.Staff.WinFormStaffGeneral-Form-WageAmount',
                        fieldLabel: lang('Wage Amount')+((m_partner==14) ? ' (RM)': ' (Rp)'),
                        labelWidth: 200,
                        minValue: 0,
                        emptyText: (m_partner==14) ? lang('ringgit malaysia') :  lang('rupiah')
                    },{
                    	xtype: 'combobox',
                        id: 'Koltiva.view.Staff.WinFormStaffGeneral-Form-WagePeriod',
                        name: 'Koltiva.view.Staff.WinFormStaffGeneral-Form-WagePeriod',
                        store: cmb_wage_period,
                        fieldLabel: lang('Wage Period'),
                        labelWidth: 200,
                        queryMode: 'local',
                        displayField: 'label',
                        valueField: 'id'
                    },{
                        xtype: 'textfield',
                        id: 'Koltiva.view.Staff.WinFormStaffGeneral-Form-Handphone',
                        name: 'Koltiva.view.Staff.WinFormStaffGeneral-Form-Handphone',
                        fieldLabel: lang('Handphone'),
                        labelWidth: 200,
                        hidden:true
                    },{
                        xtype: 'textfield',
                        id: 'Koltiva.view.Staff.WinFormStaffGeneral-Form-Email',
                        name: 'Koltiva.view.Staff.WinFormStaffGeneral-Form-Email',
                        fieldLabel: lang('Email'),
                        vtype: 'email',
                        allowBlank: true,
                        labelWidth: 200,
                        hidden:true
                    },{
                    	fieldLabel: lang('Status'),
                        xtype: 'radiogroup',
                        labelWidth: 200,
                        columns: 2,
                        allowBlank: false,
                        msgTarget: 'side',
                        items:[{
                            boxLabel: lang('Active'),
                            name: 'Koltiva.view.Staff.WinFormStaffGeneral-Form-StatusCode',
                            inputValue: 'active',
                            id: 'Koltiva.view.Staff.WinFormStaffGeneral-Form-StatusCode1',
                            listeners:{
                                change: function(){
                                    return false;
                                }
                            }
                        },{
                            boxLabel: lang('Inactive'),
                            name: 'Koltiva.view.Staff.WinFormStaffGeneral-Form-StatusCode',
                            inputValue: 'inactive',
                            id: 'Koltiva.view.Staff.WinFormStaffGeneral-Form-StatusCode2',
                            listeners:{
                                change: function(){
                                    return false;
                                }
                            }
                        }]
                    }]
                }]
            }]
        }];
        //items -------------------------------------------------------------- (end)

        //buttons -------------------------------------------------------------- (begin)
        thisObj.buttons = [{
            text: lang('Save'),
            id: 'Koltiva.view.Staff.WinFormStaffGeneral-BtnSave',
            icon: varjs.config.base_url + 'images/icons/new/save.png',
            cls: 'Sfr_BtnFormBlue',
            overCls: 'Sfr_BtnFormBlue-Hover',
            handler: function () {
            	var FormNya = Ext.getCmp('Koltiva.view.Staff.WinFormStaffGeneral-Form').getForm();
                if (FormNya.isValid()) {
                    FormNya.submit({
                        url: m_api + '/basic_staff/staff_general',
                        method:'POST',
                        waitMsg: 'Saving data...',
                        submitEmptyText : false,
                        success: function(fp, o) {
                            Ext.MessageBox.show({
                                title: 'Information',
                                msg: lang('Data saved'),
                                buttons: Ext.MessageBox.OK,
                                animateTarget: 'mb9',
                                icon: 'ext-mb-success'
                            });

                            //refresh store vehicle yg manggil
                            thisObj.viewVar.callerStore.load();

                            //tutup popup
                            thisObj.close();
                        },
                        failure: function(fp, o){
                            var pesanNya;
                            if(o.result.message != undefined){
                                pesanNya = o.result.message;
                            }else{
                                pesanNya = lang('Connection error');
                            }
                            Ext.MessageBox.show({
                                title: 'Error',
                                msg: pesanNya,
                                buttons: Ext.MessageBox.OK,
                                animateTarget: 'mb9',
                                icon: 'ext-mb-error'
                            });
                        }
                    });
                }else{
                    Ext.MessageBox.show({
                        title: 'Attention',
                        msg: lang('Form not complete yet'),
                        buttons: Ext.MessageBox.OK,
                        animateTarget: 'mb9',
                        icon: 'ext-mb-info'
                    });
                }
            }
        },{
            text: lang('Close'),
            icon: varjs.config.base_url + 'images/icons/new/close.png',
            cls: 'Sfr_BtnFormGrey',
            overCls: 'Sfr_BtnFormGrey-Hover',
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
            var formNya = Ext.getCmp('Koltiva.view.Staff.WinFormStaffGeneral-Form');
            formNya.getForm().reset();

            Ext.getCmp('Koltiva.view.Staff.WinFormStaffGeneral-Form-callerObjID').setValue(thisObj.viewVar.callerObjID);
            Ext.getCmp('Koltiva.view.Staff.WinFormStaffGeneral-Form-callFromRole').setValue(thisObj.viewVar.callFromRole);

            if(thisObj.viewVar.opsiDisplay == 'insert'){
                //insert
            }

            if(thisObj.viewVar.opsiDisplay == 'update' || thisObj.viewVar.opsiDisplay == 'view'){
            	if(thisObj.viewVar.opsiDisplay == 'view'){
            		Ext.getCmp('Koltiva.view.Staff.WinFormStaffGeneral-BtnSave').setVisible(false);
            	}

            	formNya.getForm().load({
                    url: m_api + '/basic_staff/staff_general_form',
                    method: 'GET',
                    params: {
                        StaffID: thisObj.viewVar.StaffID
                    },
                    success: function(form, action) {
                        var r = Ext.decode(action.response.responseText);
                        Ext.getCmp('Koltiva.view.Staff.WinFormStaffGeneral-Form-PositionID').setReadOnly(true);
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