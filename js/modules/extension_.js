Ext.Loader.setConfig({enabled: true});
Ext.Loader.setPath('Ext.ux', varjs.config.base_url+'js/'+varjs.config.extjs_version+'/ux');
Ext.require([
    'Ext.grid.*',
    'Ext.data.*',
    'Ext.panel.*',
    'Ext.ux.grid.FiltersFeature',
    'Ext.form.Panel',
    'Ext.tab.*',
    'Ext.window.*',
    'Ext.tip.*',
    'Ext.layout.container.Border'
]);

Ext.onReady(function(){
    var store = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id','PersonID','PersonNm','WritingAwal','WritingAkhir','BallotAwal','BallotAkhir','InstitutionID','InstitutionName','PositionID',''],
        autoLoad: true,
        pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_crud+'s',
            reader: {
                type: 'json',
                root: 'data',
                totalProperty: 'total'
            }
        }
    });
    var mc_RegionID = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id','label'],
        autoLoad: true,
        pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_RegionID,
            reader: {
                type: 'json',
                root: 'data',
                totalProperty: 'totalCount'
            }
        }
    });
    var mc_group = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['GroupId','GroupName'],
        autoLoad: true,
        proxy: {
            type: 'ajax',
            url: m_group,
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });
    
    function displayFormWindow(){
        if(!win.isVisible()){
            resetForm();
            win.show();
            //Ext.getCmp('name').focus(true,true);
        } else {
            win.hide(this, function() {});
            win.toFront();
        }
    }
    function resetForm(){
       /* Ext.getCmp('id').setValue('');
        Ext.getCmp('PersonID').setValue('');
         Ext.getCmp('WritingAwal').setValue('');
         Ext.getCmp('WritingAkhir').setValue('');
         Ext.getCmp('BallotAwal').setValue('');
         Ext.getCmp('BallotAkhir').setValue('');
         Ext.getCmp('InstitutionID').setValue('');
         Ext.getCmp('PositionID').setValue('');*/
    }
    var DataForm = Ext.create('Ext.form.Panel', {
        frame: false,
        height: 800,
        width: 400,
        bodyPadding: 5,
        id:'dataForm',
        items: [
            {
          xtype: 'fieldset',
          flex: 1,
          items: [
            {
                xtype: 'textfield',
                fieldLabel: 'Ssn',
                id: 'Ssn',
                name: 'Ssn'
            },
            {
                xtype: 'textfield',
                fieldLabel: 'Ext ID',
                id: 'ExtId',
                name: 'ExtId',
                hidden:true
            },
            {
                xtype: 'textfield',
                fieldLabel: 'Name',
                id: 'PersonNm',
                name: 'PersonNm'
            },
            {
                xtype: 'datefield',
                fieldLabel: 'Birthday',
                id: 'BirthDttm',
                name: 'BirthDttm'
            },
            {
                xtype: 'textfield',
                fieldLabel: 'Birthday Place',
                id: 'BirthPlace',
                name: 'BirthPlace'
            },
            {
               fieldLabel : 'Gender',
               xtype      : 'radiogroup',
               width: '100%',
               items: [
                   {
                       boxLabel  : 'Male',
                       name      : 'Gender',
                       inputValue: 'm',
                       id        : 'Gender1'
                   }, {
                       boxLabel  : 'Female',
                       name      : 'Gender',
                       inputValue: 'f',
                       id        : 'Gender2'
                   }
               ]
            },
            {
                xtype: 'textareafield',
                fieldLabel: 'Address',
                id: 'Address',
                name: 'Address'
            },
            {
                id: 'RegionCD',
                name: 'RegionCD',
                xtype: 'combo',
                fieldLabel: 'RegionName',
                minChars:1,
                store:mc_RegionID,
                displayField: 'label',
                valueField: 'id',
                typeAhead: true,
                queryMode: 'remote',
                mode:'remote'
            },
            {
                xtype: 'textfield',
                fieldLabel: 'ZIP',
                id: 'ZipCd',
                name: 'ZipCd'
            },
            {
                xtype: 'textfield',
                fieldLabel: 'Email',
                id: 'Email',
                name: 'Email'
            },
            {
                xtype: 'textfield',
                fieldLabel: 'Religion',
                id: 'ReligionCd',
                name: 'ReligionCd',
                hidden:true                
            },
            {
               fieldLabel : 'Blood',
               xtype      : 'radiogroup',
               width: '100%',
               items: [
                   {
                       boxLabel  : 'A',
                       name      : 'BloodT',
                       inputValue: 'A',
                       id        : 'BloodT1'
                   }, {
                       boxLabel  : 'B',
                       name      : 'BloodT',
                       inputValue: 'B',
                       id        : 'BloodT2'
                   }, {
                       boxLabel  : 'AB',
                       name      : 'BloodT',
                       inputValue: 'AB',
                       id        : 'BloodT3'
                   }, {
                       boxLabel  : 'O',
                       name      : 'BloodT',
                       inputValue: ')',
                       id        : 'BloodT4'
                   }
               ]
            },
            {
               fieldLabel : 'Marital',
               xtype      : 'radiogroup',
               width: '100%',
               items: [
                   {
                       boxLabel  : 'Maried',
                       name      : 'MaritalSt',
                       inputValue: 'maried',
                       id        : 'MaritalSt1'
                   }, {
                       boxLabel  : 'Single',
                       name      : 'MaritalSt',
                       inputValue: 'single',
                       id        : 'MaritalSt2'
                   }, {
                       boxLabel  : 'Widow',
                       name      : 'MaritalSt',
                       inputValue: 'widow',
                       id        : 'MaritalSt3'
                   }
               ]
            },
            {
                xtype: 'radiogroup',
                fieldLabel: 'Education',
                id: 'Education',
                name: 'Education',
                items: [
                        {
                            xtype: 'radiofield',
                            boxLabel: 'SD',
                            value:'SD, SMP, SMA, D3, S1, S2'
                        },
                        {
                            xtype: 'radiofield',
                            boxLabel: 'SMP',
                            value:'SMP'
                        },
                        {
                            xtype: 'radiofield',
                            boxLabel: 'SMA',
                            value:'SMA'
                        },
                        {
                            xtype: 'radiofield',
                            boxLabel: 'D3',
                            value:'D3'
                        },
                        {
                            xtype: 'radiofield',
                            boxLabel: 'S1',
                            value:'S1'
                        },
                        {
                            xtype: 'radiofield',
                            boxLabel: 'S2',
                            value:'S2'
                        },
                    ]
            },
            {
                xtype: 'textfield',
                fieldLabel: 'Nationality Name',
                id: 'NationalityNm',
                name: 'NationalityNm'
            },
            {
                xtype: 'textfield',
                fieldLabel: 'Handphone',
                id: 'Handphone',
                name: 'Handphone'
            }]
         },
            {
                xtype: 'textfield',
                fieldLabel: 'Person',
                id: 'PersonID',
                name: 'PersonID'
            },
            {
                xtype: 'textfield',
                fieldLabel: 'Writing Start',
                id: 'WritingAwal',
                name: 'WritingAwal'
            },
            {
                xtype: 'textfield',
                fieldLabel: 'Writing End',
                id: 'WritingAkhir',
                name: 'WritingAkhir'
            },
            {
                xtype: 'textfield',
                fieldLabel: 'Ballot Start',
                id: 'BallotAwal',
                name: 'BallotAwal'
            },
            {
                xtype: 'textfield',
                fieldLabel: 'Ballot End',
                id: 'BallotAkhir',
                name: 'BallotAkhir'
            },
            {
                xtype: 'textfield',
                fieldLabel: 'Institution',
                id: 'InstitutionID',
                name: 'InstitutionID'
            },
            {
                xtype: 'textfield',
                fieldLabel: 'Position',
                id: 'PositionID',
                name: 'PositionID'
            }
        ],
        buttons: [{
            id:'saveButton',
            text: 'Save',
            margin: '0px 0px 0px 6px',
            scale: 'medium',
            ui: 's-button',
            cls: 's-blue',
            handler: function() {
                var form = this.up('form').getForm();
                var methode;
                if (Ext.getCmp('id').getValue()=='') methode = 'POST'; else methode = 'PUT';
                form.submit({
                    url: m_crud,
                    method : methode,
                    waitMsg: 'Sending data...',
                    success: function(fp, o) {
                        Ext.MessageBox.alert('Success', 'Data saved.');
                    }
                });
                win.hide(this, function() {
                    store.load();
                });
            }
        },{
             text: 'Close',
             margin: '0px 0px 0px 6px',
             scale: 'medium',
             ui: 's-button',
             cls: 's-grey',
             disabled: false,
             handler: function() {
                 win.hide();
             }
        }]
    });
    var win = Ext.create('widget.window', {
        title: 'Data Extension',
        closable: true,
        modal:true,
        closeAction: 'show',
        width: 430,
        minWidth: 370,
        height: 850,
        layout: {
            type: 'border',
            padding: 5
        },
        items: [DataForm]
    });
    var grid = Ext.create('Ext.grid.Panel', {
       store: store,
       width: '100%',
       //title: 'Extension List',
       style: 'border:1px solid #CCC;',
       renderTo: 'ext-content',
       loadMask: true,
       selType: 'rowmodel',
       dockedItems: [{
              xtype: 'pagingtoolbar',
              store: store,   // same store GridPanel is using
              dock: 'bottom',
              displayInfo: true
         },{
            xtype: 'toolbar',
            items: [
            {
               icon: varjs.config.base_url + 'images/icons/new/add.png', cls:'Sfr_BtnGridGreen', overCls:'Sfr_BtnGridGreen-Hover', 
               text: 'Add',
               scope: this,
               handler : displayFormWindow,
               hidden : m_act_add
            },
            {
               icon: varjs.config.base_url+'images/icons/silk/pencil.png', 
               text: 'Edit',
               scope: this,
               handler : function() {
                   displayFormWindow;
                   console.log('update');
                   var sm = grid.getSelectionModel().getSelection()[0];
                   if(sm.get('id') !==undefined){
                       Ext.getCmp('id').setValue(sm.get('id'));
                       Ext.getCmp('PersonID').setValue(sm.get('PersonID'));
                       Ext.getCmp('WritingAwal').setValue(sm.get('WritingAwal'));
                       Ext.getCmp('WritingAkhir').setValue(sm.get('WritingAkhir'));
                       Ext.getCmp('BallotAwal').setValue(sm.get('BallotAwal'));
                       Ext.getCmp('BallotAkhir').setValue(sm.get('BallotAkhir'));
                       Ext.getCmp('InstitutionID').setValue(sm.get('InstitutionID'));
                       Ext.getCmp('PositionID').setValue(sm.get('PositionID'));
                       win.show();
                   }                 
               },
               hidden : m_act_update
            },
            {
               itemId: 'remove',
               icon: varjs.config.base_url+'images/icons/silk/delete.png', 
               text: 'Delete',
               scope: this,
               hidden : m_act_delete,
               handler : function(){
                    var sm = grid.getSelectionModel();
                    var id = sm.lastFocused.data.id;
                    var name = sm.lastFocused.data.name;
                    console.log(name);
                    Ext.MessageBox.confirm('Message', 'Apakah anda mau menghapus Extension ini ?' , function(btn){
                    if(btn == 'yes')
                        {
                            Ext.Ajax.request({
                               waitMsg: 'Please Wait',
                               url: m_crud,
                               method : 'DELETE',
                               params: {
                                   id:  id
                               },
                               success: function(response, opts){
                                   console.log(response);
                                   var obj = Ext.decode(response.responseText);
                                   console.log(obj);
                                   switch(obj.success){
                                       case true: store.load();
                                       break;
                                       default: Ext.MessageBox.alert('Warning',obj.message);
                                       break;
                                   }
                               },
                               failure: function(response, opts){
                                   var obj = Ext.decode(response.responseText);
                                   console.log(obj);
                                   Ext.MessageBox.alert('error','Could not connect to the database. Retry later');
                               }
                           });
                       }
                    });
                    if (store.getCount() > 0) sm.select(0);
               }
            }]
    }],
    columns: [
    {
        text: 'ID',
        dataIndex: 'id',
        hidden:true
    },
    {
        text: 'No',
        xtype: 'rownumberer',
        width:'5%'
    },
    {
        text: 'Name', 
        width: '14.285714285714%',
        dataIndex: 'PersonNm'
    },
    {
        text: 'Writing Start', 
        width: '14.285714285714%',
        dataIndex: 'WritingAwal'
    },
    {
        text: 'Writing End', 
        width: '14.285714285714%',
        dataIndex: 'WritingAkhir'
    },
    {
        text: 'Ballot Start', 
        width: '14.285714285714%',
        dataIndex: 'BallotAwal'
    },
    {
        text: 'Ballot End', 
        width: '14.285714285714%',
        dataIndex: 'BallotAkhir'
    },
    {
        text: 'Institution', 
        width: '14.285714285714%',
        dataIndex: 'InstitutionName'
    },
    {
        text: 'Position', 
        width: '14.285714285714%',
        dataIndex: 'PositionName'
    }
    ]
   });
});
