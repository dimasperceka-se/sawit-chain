var prof = new Array();

Ext.Ajax.request({
    url: m_staff_profile,
    method: 'GET',
    params: {id: m_id},
    success: function(fp, o){
        var r = Ext.decode(fp.responseText);
        if (r.PersonPhoto==null) r.PersonPhoto= m_photo+'no-user.jpg'
        else r.PersonPhoto = m_photo+r.PersonPhoto;
        r.PartnerPhoto = m_photo+r.PartnerPhoto;
        prof = r;
    }
});

Ext.onReady(function(){
    Ext.tip.QuickTipManager.init();

    var tab = Ext.create('Ext.tab.Panel', {
        renderTo: 'ext-content',
        width: '98%',
        height : 450,
        frame: false,
        plain: true,
        items: [{
                xtype: 'panel',
                title: lang('Data Profile'),
                items: [{
                    xtype: 'form',
                    id:'form',
                    layout:'column',
                    border: false,
                    items :[
                    {
                    columnWidth: .25,
                    border: false,
                    padding: '5',
                    margin:2,
                   // style: 'border:2px solid #157FCC;text-align:center;',
                    layout: {
                        type: 'vbox'
                    },
                    items :[{
                        xtype: 'textfield',
                        padding : '0 0 0 10',
                        id: 'UserId',
                        name: 'UserId',
                        inputType:'hidden',
                        value:prof.UserId
                        },{
                            xtype:'image',
                            id:'PersonPhoto',
                            height:'210px',
                            //src: (prof.PersonPhoto!=='')?prof.PersonPhoto:'http://redmine.koltiva.com/attachments/download/317/no-user.jpg'
                            src: 'http://redmine.koltiva.com/attachments/download/317/no-user.jpg',
                            margin: '0 0 10 0'
                        },{
                            xtype: 'label',
                            text:prof.Name,
                            style: 'font-size:1.6em;font-weight:bold;text-align:center;',
                            margin: '0 0 20 50'

                        },{
                            xtype:'image',
                            id:'PartnerPhoto',
                            height:'60px',
                            src: prof.PartnerPhoto
                        }]
                    },
                    {
                    columnWidth: .75,
                    border: false,
                    margin:2,
                    padding: '5',
                    //style: 'border:2px solid #157FCC',
                    fieldDefaults: {
                            labelAlign: 'left',
                            labelWidth: 180,
                            anchor: '95%'
                        },
                    items :[{
                        xtype: 'textfield',
                        padding : '0 0 0 10',
                        id: 'UserId',
                        name: 'UserId',
                        inputType:'hidden',
                        value:prof.UserId
                    },{
                        xtype: 'textfield',
                        fieldLabel: 'Name',
                        id: 'PersonName',
                        name: 'PersonName',
                        width:400,
                        value:prof.Name,
                        readOnly:true
                    },{
                        xtype: 'datefield',
                        fieldLabel: 'Tanggal Lahir',
                        id: 'TanggalLahir',
                        name: 'TanggalLahir',
                        format:'Y-m-d',
                        width:400,
                        value:prof.TanggalLahir,
                        readOnly:true
                    },{
                        xtype: 'textfield',
                        fieldLabel: 'Partner',
                        id: 'PartnerName',
                        name: 'PartnerName',
                        width:400,
                        value:prof.PartnerName,
                        readOnly:true
                    },{
                        xtype: 'textfield',
                        fieldLabel: 'Pivate Phone',
                        id: 'PrivatePhone',
                        name: 'PrivatePhone',
                        width:400,
                        value:prof.PrivatePhone,
                        readOnly:true
                    },{
                        xtype: 'textfield',
                        fieldLabel: 'Official Phone',
                        id: 'OfficialPhone',
                        name: 'OfficialPhone',
                        width:400,
                        value:prof.OfficialPhone,
                        readOnly:true
                    },{
                        xtype: 'textfield',
                        fieldLabel: 'Private Email',
                        id: 'PrivateEmail',
                        name: 'PrivateEmail',
                        width:400,
                        value:prof.PrivateEMail,
                        readOnly:true
                    },{
                        xtype: 'textfield',
                        fieldLabel: 'Official Email',
                        id: 'OfficialEmail',
                        name: 'OfficialEmail',
                        width:400,
                        value:prof.OfficialEmail,
                        readOnly:true
                    },{
                        xtype: 'combobox',
                        id:'bahasa',
                        name: 'bahasa',
                        fieldLabel: 'Bahasa',
                        displayField: 'name',
                        width:400,
                        value:prof.UserLanguage,
                        store: Ext.create('Ext.data.Store', {
                            fields: [ {type: 'string', name: 'name'}
                            ], data: [ {"name":"Indonesia"}, {"name":"English"}
                            ] }),
                        queryMode: 'local',
                        typeAhead: true
                    },{
                        xtype: 'combobox',
                        id:'notification',
                        name: 'notification',
                        fieldLabel: 'Notification',
                        displayField: 'name',
                        width:400,
                        value:prof.UserNotification,
                        store: Ext.create('Ext.data.Store', {
                            fields: [ {type: 'string', name: 'name'}],
                            data: [ {"name":"Official Email Only"}, {"name":"Official and Private Email"}] }),
                        queryMode: 'local',
                        typeAhead: true
                    }]
                    }
                    ]
                }]
                ,
                buttons: [{
                    id:'saveProfile',
                    margin: '5px',
                    scale: 'large',
                    ui: 's-button',
                    text: 'Save',
                    cls: 's-green',
                    handler: function() {
                        var form = Ext.getCmp('form').getForm();
                        form.submit({
                            url: m_staff_profile,
                            method : 'PUT',
                            waitMsg: 'Sending the info...',
                            success: function(fp, o) {
                                window.location.reload();
                                Ext.Msg.alert('Success', 'Profile changed.');
                            }
                        });
                    }
                }]
            },
            {
                xtype: 'panel',
                title: 'Ubah Password',
                height: 140,
                items: [{
                    xtype: 'form',
                    fieldDefaults: {
                        labelAlign: 'left',
                        labelWidth: 160,
                        anchor: '50%'
                    },
                    items :[
                        {
                            xtype: 'textfield',
                            padding : '0 0 0 10',
                            id: 'id',
                            name: 'id',
                            inputType:'hidden',
                            value:prof.UserId
                        },{
                            xtype: 'textfield',
                            padding : '0 0 0 10',
                            id: 'Oldpassword',
                            name: 'Oldpassword',
                            inputType:'hidden',
                            value:prof.Oldpassword
                        },
                        {
                            xtype: 'textfield',
                            inputType:'password',
                            padding : '0 0 0 10',
                            fieldLabel: 'Password Lama',
                            name: 'password_lama',
                            id: 'password_lama',
                            allowBlank:false

                        },{
                            xtype: 'textfield',
                            inputType:'password',
                            fieldLabel: 'Password Baru',
                            padding : '0 0 0 10',
                            name: 'password',
                            id: 'password',
                            allowBlank:false

                        },{
                            xtype: 'textfield',
                            inputType:'password',
                            fieldLabel: 'Konfirmasi Password Baru',
                            padding : '0 0 0 10',
                            name: 'repassword',
                            id: 'repassword',
                            allowBlank:false
                        }],
                        buttons: [{
                            id:'savePassword',
                            margin: '5px',
                            scale: 'large',
                            ui: 's-button',
                            text: 'Save',
                            cls: 's-green',
                            handler: function() {
                                var form = this.up('form').getForm();
                                if( (Ext.getCmp('password_lama').getValue() == '') || (Ext.getCmp('password').getValue()=='')
                                    || (Ext.getCmp('password').getValue()=='') )
                                    {Ext.Msg.alert('Warning','Semua field tidak boleh kosong!');}
                                if (Ext.getCmp('password').getValue() != Ext.getCmp('repassword').getValue())
                                    {Ext.Msg.alert('Warning','Password baru tidak sama!');}
                                /*
                                if (Ext.getCmp('Oldpassword').getValue() != md5(Ext.getCmp('password_lama').getValue()) )
                                    {Ext.Msg.alert('Warning','Password lama salah!');}
                                    */
                                else
                                {
                                form.submit({
                                    url: m_crud+'passu',
                                    waitMsg: 'Sending the info...',
                                    success: function(fp, o) {
                                        Ext.Msg.alert('Success', 'Password changed.');
                                        Ext.getCmp('password_lama').setValue('');
                                        Ext.getCmp('password').setValue('');
                                        Ext.getCmp('repassword').setValue('');
                                    }
                                });
                                }
                            }
                        }]
                }]
            }
        ]
    });
});
