Ext.define('Koltiva.view.DataAdm.MappingMetadata.MainForm' ,{
    extend: 'Ext.window.Window',
    id: 'Koltiva.view.DataAdm.MappingMetadata.MainForm',
    title: lang('Mapping Data Element Form'),
    closable: true,
    modal: true,
    closeAction: 'destroy',
    width: '75%',
    height: '65%',
    overflowY: 'auto',
    formVar: false,
    setFormVar: function(value){
        this.formVar = value;
    },
    initComponent: function() {
        var thisObj = this;

        thisObj.StoreMainGrid = Ext.create('Ext.data.Store', {
            extend: 'Ext.data.Model',
            // fields: ['MappingID','DataElementUID','DataElement','TableName','FieldName','CustomFunction',
            // 'Execute','Priority','DataElQueue','FieldQueue'],
            fields: ['program_uid','name','sec_name','de_uid','de_name','mw_mapping_id','table_reff','field_reff','custom_function','executeSql','priority','de_queue','field_queue'],
            autoLoad: true,
            pageSize: 10,
            proxy: {
                type: 'ajax',
                url: m_selmetadata,
                reader: {
                    type: 'json',
                    root: 'data'
                }
            },
            listeners: {
                beforeload: function(store, operation, options){
                    store.proxy.extraParams.MappingId = thisObj.formVar.MappingId;
                    store.proxy.extraParams.ProgStageId = thisObj.formVar.IdProgram;
                    store.proxy.extraParams.tblReff = thisObj.formVar.tblReff;
                    store.proxy.extraParams.fieldReff = thisObj.formVar.fieldReff;
                    store.proxy.extraParams.DeUid = thisObj.formVar.DeUid;
                    // store.proxy.extraParams.ProgStageId = Ext.getCmp('filter-ProgStage').getValue();
                }
            }
        });

        thisObj.RowEditing = Ext.create('Ext.grid.plugin.RowEditing', {
            id: 'RowEditingForm',
            clicksToMoveEditor: 0,
            autoCancel: false,
            errorSummary : false,
            clicksToEdit: 2,
            listeners : {
               beforeedit : function(ev) {
                //   return m_act_update;
                    // return false;
               }
            }
        });

        thisObj.tablereff = Ext.create('Ext.data.Store', {
            extend: 'Ext.data.Model',
            fields: ['table_name'],
            autoLoad: true,
            pageSize: 10,
            proxy: {
                type: 'ajax',
                url: m_tablereff,
                reader: {
                    type: 'json',
                    root: 'data'
                }
            }
        });

        thisObj.routine = Ext.create('Ext.data.Store', {
            extend: 'Ext.data.Model',
            fields: ['routine_name'],
            autoLoad: true,
            pageSize: 10,
            proxy: {
                type: 'ajax',
                url: m_routine,
                reader: {
                    type: 'json',
                    root: 'data'
                }
            },
        });

        thisObj.columnreff = Ext.create('Ext.data.Store', {
            extend: 'Ext.data.Model',
            fields: ['column_name','column_detail'],
            autoLoad: false,
            pageSize: 10,
            proxy: {
                type: 'ajax',
                url: m_columnreff,
                reader: {
                    type: 'json',
                    root: 'data'
                }
            },
            /*
            listeners: {
                beforeload: function(store, operation) {
                      store.proxy.extraParams.TableName2 = Ext.getCmp('Koltiva.view.DataAdm.MainForm-FormBasicData-table_name').getValue();
                }
            }*/
            
        });

        thisObj.dataelementreff = Ext.create('Ext.data.Store', {
            extend: 'Ext.data.Model',
            fields: ['id','label'],
            autoLoad: true,
            pageSize: 10,
            proxy: {
                type: 'ajax',
                url: m_dataelementreff,
                reader: {
                    type: 'json',
                    root: 'data'
                }
            },     
            listeners: {
                beforeload: function(store, operation, options){
                    store.proxy.extraParams.ProgStageId = thisObj.formVar.IdProgram;
                }
            }       
        });

        // var RowEditing = Ext.create('Ext.grid.plugin.RowEditing', {
        //     id: 'RowEditing',
        //     clicksToMoveEditor: 0,
        //     autoCancel: false,
        //     errorSummary: false,
        //     clicksToEdit: 2,
        //     listeners: {
        //         beforeedit: function (ev) {
        //             return m_act_update;
        //         }
        //     }
        // });


        thisObj.items = [{
            xtype: 'form',
            id: 'Koltiva.view.DataAdm.MappingMetadata.MainForm-FormBasicData',
            padding:'5 25 5 8',
            items:[{
                xtype: 'grid',
                id: 'Koltiva.view.DataAdm.MappingMetadata.MainForm-DataControl',
                style: 'border:1px solid #CCC;margin-top:4px;',
                loadMask: true,
                selType: 'rowmodel',
                store: thisObj.StoreMainGrid,
                viewConfig: {
                    deferEmptyText: false,
                    emptyText: lang('No data Available')
                },
                dockedItems: [{
                    xtype: 'toolbar',
                    dock: 'top',
                    items:[{
                        icon: varjs.config.base_url + 'images/icons/new/add.png', cls:'Sfr_BtnGridGreen', overCls:'Sfr_BtnGridGreen-Hover',
                        text: 'Add',
                        scope: this,
                        cls:m_act_add,
                        // hidden: true,
                        handler : function(){
                             thisObj.RowEditing.cancelEdit();
                             thisObj.StoreMainGrid.insert(0, {
                                mw_mapping_id: '',
                                de_uid: '',
                                program_uid: Ext.getCmp('filter-ProgStage').getValue(),
                                de_name:'',
                                table_reff:'',
                                field_reff:'',
                                custom_function:'',
                                executeSql:'',
                                priority:'',
                                de_queue:'',
                                field_queue:''
                             });
                             thisObj.RowEditing.startEdit(0, 0);
                        }
                    },{
                        xtype: 'tbtext',
                        style:'font-weight:bold;',
                        text: lang('List of Mapping Data')
                    }]
                },{
                    xtype: 'pagingtoolbar',
                    store: thisObj.StoreMainGrid,
                    style: 'margin-top:25px;',
                    dock: 'bottom',
                    width: '80%',
                    displayInfo: true
                }],
                columns: [{
                    text: 'ID',
                    dataIndex: 'mw_mapping_id',
                    hidden: true
                },{
                    text: 'pUID',
                    dataIndex: 'program_uid',
                    hidden: true
                },{
                    text: 'DataElementUID',
                    dataIndex: 'de_uid',
                    hidden: true
                },{
                    text: 'No',
                    xtype: 'rownumberer',
                    align: 'center',
                    width: 30
                },{ 
                    text: lang('DataElement'),
                    dataIndex: 'de_name',
                    flex: 1,
                    editor: {
                        xtype: 'combobox',
                        store : thisObj.dataelementreff,
                        allowBlank: true,
                        displayField: 'label',
                        valueField: 'id',
                        queryMode: 'local',
                        listeners: {
                            change: function(cb, nv, ov) {
                                var sm = Ext.getCmp('Koltiva.view.DataAdm.MappingMetadata.MainForm-DataControl').getSelectionModel().getSelection()[0];
                                if(sm.get('mw_mapping_id')==''){
                                    sm.set('de_uid', nv);
                                }
                            }
                        }
                    }
                },{
                    text:  lang('TableName'),
                    flex: 1,
                    dataIndex: 'table_reff',
                    editor: {
                        xtype: 'combobox',
                        store : thisObj.tablereff,
                        allowBlank: true,
                        displayField: 'table_name',
                        valueField: 'table_name',
                        queryMode: 'local',
                        listeners: {
                            change: function(cb, nv, ov) {
                                thisObj.columnreff.load({                                    
                                    params: {
                                        TableName:nv
                                    }
                                });
                            },
                            afterRender: function(){
                                var sm = Ext.getCmp('Koltiva.view.DataAdm.MappingMetadata.MainForm-DataControl').getSelectionModel().getSelection()[0];
                                if(sm){
                                    thisObj.columnreff.load({                                    
                                        params: {
                                            TableName:sm.get('table_reff')
                                        }
                                    });
                                }
                            }
                        }
                    }
                },{ 
                    text:  lang('FieldName'),
                    width: '20%',
                    dataIndex: 'field_reff',
                    editor: {
                        xtype: 'combobox',
                        store : thisObj.columnreff,
                        allowBlank: true,
                        displayField: 'column_detail',
                        valueField: 'column_name',
                        queryMode: 'local'
                    },
                },{
                    text:  lang('CustomFunction'),
                    flex: 1,
                    dataIndex: 'custom_function',
                    editor: {
                        xtype: 'combobox',
                        store : thisObj.routine,
                        allowBlank: true,
                        displayField: 'routine_name',
                        valueField: 'routine_name',
                        queryMode: 'local'
                    }
                },{ 
                    text:  lang('Exec'),
                    width: 60,
                    dataIndex: 'executeSql',
                    editor: {
                        xtype:'textfield',
                        allowBlank:false
                    }
                },{ 
                    text:  lang('Prior'),
                    width: 50,
                    dataIndex: 'priority',
                    editor: {
                        xtype:'textfield',
                        allowBlank:false
                    }
                },{ 
                    text:  lang('DElQue'),
                    width: 60,
                    dataIndex: 'de_queue',
                    editor: {
                        xtype:'textfield',
                        allowBlank:true
                    }
                },{ 
                    text:  lang('FQue'),
                    width: 50,
                    dataIndex: 'field_queue',
                    editor: {
                        xtype:'textfield',
                        allowBlank:true
                    }
                }],
                plugins: [thisObj.RowEditing],
                listeners: {
                    'canceledit':function(editor,e,eOpts){
                        thisObj.StoreMainGrid.load();
                    },
                    'edit': function(editor, e) {
                        // 'program_uid','name','sec_name','de_uid','de_name','mw_mapping_id','table_reff','field_reff','custom_function','executeSql','priority','DataElQueue','FieldQueue'
                        var mw_mapping_id = e.record.data.mw_mapping_id;
                        var TableName = e.record.data.table_reff;
                        var ColumnName = e.record.data.field_reff;
                        var DataElement = e.record.data.de_uid;
                        var program_uid = e.record.data.program_uid;
                        var CustomFuction = e.record.data.custom_function;
                        var Execute = e.record.data.executeSql;
                        var Priority = e.record.data.priority;
                        var de_queue = e.record.data.de_queue;
                        var field_queue = e.record.data.field_queue;
                        // console.log(e.record.data.de_uid);
                        // if(e.field == 'de_name'){
                        //     e.record.set('de_uid', e.record.get('de_name'));
                        // }
                        // console.log(e);
                        // console.log(e.record.data.de_uid);
                        // return;
                        Ext.MessageBox.confirm('Message', 'Update data ini ?', function(btn){
                        
                            if(btn == 'yes')
                            { 
                                Ext.Ajax.request({
                                    waitMsg: 'Please wait...',
                                    url: m_rowmetadata_form,
                                    method : 'POST',
                                    params: {
                                        mw_mapping_id: mw_mapping_id,
                                        DataElement: DataElement,
                                        TableName: TableName,
                                        ColumnName: ColumnName,
                                        program_uid: program_uid,
                                        CustomFuction: CustomFuction,
                                        Execute: Execute,
                                        Priority: Priority,
                                        de_queue: de_queue,
                                        field_queue: field_queue
                                    },
                                success: function(response, opts){
                                        var obj = Ext.decode(response.responseText);
                                        switch(obj.success){
                                            case true:
                                            Ext.MessageBox.alert('Success',obj.message);
                                            thisObj.StoreMainGrid.load();
                                            Ext.data.StoreManager.lookup('Koltiva.store.DataAdm.MappingMetadata.MainGrid').load({
                                                params: {
                                                    ProgStageId: Ext.getCmp('filter-ProgStage').getValue(),
                                                }
                                            });
                                            break;
                                            default:
                                            Ext.MessageBox.alert('Warning',obj.message);
                                            break;
                                        }
                                },
                                failure: function(response, opts){
                                    var obj = Ext.decode(response.responseText);
                                    Ext.MessageBox.alert('error','Could not connect to the database. Retry later');
                                    }
                                });
                            }
                        });
                    }
                }
            // },{
            // 	layout: 'column',
            //     border: false,
            //     padding:10,
            //     items:[{
            //         columnWidth: 1,
            //         layout:'form',
            //       //  style:'padding-right:15px;border-right:1px dashed gray;',
            //         items:[{
            //             xtype:'hidden',
            //             id: 'Koltiva.view.DataAdm.MappingMetadata.MainForm-FormBasicData-ID',
            //             name: 'Koltiva.view.DataAdm.MappingMetadata.MainForm-FormBasicData-ID',
            //             fieldLabel: lang('ID'),
            //             labelAlign:'top',
            //             readOnly: false
            //         },{
            //             xtype:'hidden',
            //             id: 'Koltiva.view.DataAdm.MappingMetadata.MainForm-FormBasicData-program_uid',
            //             name: 'Koltiva.view.DataAdm.MappingMetadata.MainForm-FormBasicData-program_uid',
            //             fieldLabel: lang('ProgramID'),
            //             readOnly: false
            //         },{
            //             xtype:'hidden',
            //             id: 'Koltiva.view.DataAdm.MappingMetadata.MainForm-FormBasicData-mw_mapping_id',
            //             name: 'Koltiva.view..DataAdm.MappingMetadata.MainForm-FormBasicData-mw_mapping_id',
            //             fieldLabel: lang('MW Mapping ID'),
            //             readOnly: false
            //         },{
            //             xtype: 'hidden',
            //             id: 'Koltiva.view.DataAdm.MappingMetadata.MainForm-FormBasicData-DataElement',
            //             name: 'Koltiva.view.DataAdm.MappingMetadata.MainForm-FormBasicData-DataElement',
            //             fieldLabel: lang('ID Data Element'),
            //             allowBlank: false,
            //         },{
            //             xtype: 'textfield',
            //             id: 'Koltiva.view.DataAdm.MappingMetadata.MainForm-FormBasicData-NameDataElement',
            //             name: 'Koltiva.view.DataAdm.MappingMetadata.MainForm-FormBasicData-NameDataElement',
            //             fieldLabel: lang('Data Element'),
            //             allowBlank: false,
            //         },{
            //             xtype: 'label',
            //             margin:0,
            //             padding:0,
            //             cls: 'x-form-item-label',
            //             html: '<div class="companyLabel">' + lang('Map To') + '</div>'
            //         },{
            //             xtype: 'combobox',
            //             id: 'Koltiva.view.DataAdm.MappingMetadata.MainForm-FormBasicData-TableName',
            //             name: 'Koltiva.view.DataAdm.MappingMetadata.MainForm-FormBasicData-TableName',
            //             store: tablereff,
            //             fieldLabel: lang('Table Name'),
            //             queryMode: 'local',
            //             displayField: 'table_name',
            //             valueField: 'table_name',   
                                  
            //             listeners: {
            //                 change: function(cb, nv, ov) {
            //                     columnreff.load({                                    
            //                         params: {
            //                             TableName:nv
   
            //                         }
            //                     });

            //                   //  Ext.getCmp('Koltiva.view.DataAdm.MainForm-FormBasicData-table_name').setValue('');
            //                 }
            //             }
                        
            //         },{ html:'<div style="margin:0px;padding:0px;margin-top:-15px;">&nbsp;</div>'
            //         },{
            //             xtype: 'combobox',
            //             id: 'Koltiva.view.DataAdm.MappingMetadata.MainForm-FormBasicData-ColumnName',
            //             name: 'Koltiva.view.DataAdm.MappingMetadata.MainForm-FormBasicData-ColumnName',
            //             store: columnreff,
            //             fieldLabel: lang('Field Name'),
            //             queryMode: 'local',
            //             displayField: 'column_detail',
            //             valueField: 'column_name',
                        
            //         },{html:'<div style="margin:0px;padding:0px;margin-top:-15px;">&nbsp;</div>'
            //         },{
            //             xtype: 'combobox',
            //             id: 'Koltiva.view.DataAdm.MappingMetadata.MainForm-FormBasicData-CustomFuction',
            //             name: 'Koltiva.view.DataAdm.MappingMetadata.MainForm-FormBasicData-CustomFuction',
            //             store: routine,
            //             fieldLabel: lang('Costum Fuction'),
            //             queryMode: 'local',
            //             displayField: 'routine_name',
            //             valueField: 'routine_name',
                        
            //         },{
            //             xtype: 'textfield',
            //             id: 'Koltiva.view.DataAdm.MappingMetadata.MainForm-FormBasicData-Execute',
            //             name: 'Koltiva.view.DataAdm.MappingMetadata.MainForm-FormBasicData-Execute',
            //             value:1,
            //             fieldLabel: lang('Execute'),
            //             allowBlank: false,
            //         },{
            //             xtype: 'textfield',
            //             id: 'Koltiva.view.DataAdm.MappingMetadata.MainForm-FormBasicData-Priority',
            //             name: 'Koltiva.view.DataAdm.MappingMetadata.MainForm-FormBasicData-Priority',
            //             value:1,
            //             fieldLabel: lang('Priority'),
            //             allowBlank: false,
            //         },]
            //     }]
            }],
            listeners: {
                // afterrender: function(){
                //     if(thisObj.formVar.opsiDisplay == 'insert'){
                        
                //         //form reset
                //         var program_uid=thisObj.formVar.IdProgram;
                //         var DeName=thisObj.formVar.DeName;
                //         var DeUid=thisObj.formVar.DeUid;
                //         var MappingId=thisObj.formVar.MappingId;
                //         Ext.getCmp('Koltiva.view.DataAdm.MappingMetadata.MainForm-FormBasicData-program_uid').setValue(program_uid);
                //         Ext.getCmp('Koltiva.view.DataAdm.MappingMetadata.MainForm-FormBasicData-NameDataElement').setValue(DeName);
                //         Ext.getCmp('Koltiva.view.DataAdm.MappingMetadata.MainForm-FormBasicData-DataElement').setValue(DeUid);
                //         Ext.getCmp('Koltiva.view.DataAdm.MappingMetadata.MainForm-FormBasicData-mw_mapping_id').setValue(MappingId);
                //         Ext.getCmp('Koltiva.view.DataAdm.MappingMetadata.MainForm-FormBasicData-ID').setValue('1');
                //     }
  

                    
                //     if(thisObj.formVar.opsiDisplay == 'update' || thisObj.formVar.opsiDisplay == 'view'){
                //             //khusus view only
                //             if(thisObj.formVar.opsiDisplay == 'view'){
                //                 Ext.getCmp('Koltiva.view.DataAdm.MappingMetadata.MainForm-FormBasicData-BtnSaveForm').setVisible(false);
                //             }
                          
                //             Ext.getCmp('Koltiva.view.DataAdm.MappingMetadata.MainForm-FormBasicData').getForm().load({
                //                 url: m_api + '/map_metadata/metadata_form_open',
                //                 method: 'GET',
                //                 params: {
                //                     MappingId: thisObj.formVar.MappingId
                //                 },

                               
                //                 failure: function(form, action) {
                //                     Ext.MessageBox.show({
                //                         title: 'Failed',
                //                         msg: 'Failed to retrieve data',
                //                         buttons: Ext.MessageBox.OK,
                //                         animateTarget: 'mb9',
                //                         icon: 'ext-mb-error'
                //                     });
                //                 }
                //             });
                //     }
                    
                // }
            }
        }];

        // thisObj.buttons = [{
        //     text: 'Save',
        //     margin: '5 15 5 5',
        //     scale: 'large',
        //     ui: 's-button',
        //     id: 'Koltiva.view.DataAdm.MappingMetadata.MainForm-FormBasicData-BtnSaveForm',
        //     cls: 's-blue',
        //     handler: function () {
        //         var formHscode = Ext.getCmp('Koltiva.view.DataAdm.MappingMetadata.MainForm-FormBasicData').getForm();
        //         if (formHscode.isValid()) {
        //             formHscode.submit({
        //                 url: m_api + '/map_metadata/metadata_form',
        //                 method:'POST',
        //                 waitMsg: 'Saving data...',
        //                 success: function(fp, o) {
        //                     Ext.MessageBox.show({
        //                         title: 'Information',
        //                         msg: lang('Data saved'),
        //                         buttons: Ext.MessageBox.OK,
        //                         animateTarget: 'mb9',
        //                         icon: 'ext-mb-success'
        //                     });

        //                     //form reset
        //                     formHscode.reset();

        //                     //refresh store FamLab yg manggil
        //                     Ext.data.StoreManager.lookup('Koltiva-Store-Metadata-Gridmain').load();

        //                     //tutup popup
        //                     thisObj.close();
        //                 },
        //                 failure: function(fp, o){
        //                     var pesanNya;
        //                     if(o.result.message != undefined){
        //                         pesanNya = o.result.message;
        //                     }else{
        //                         pesanNya = lang('Connection error');
        //                     }
        //                     Ext.MessageBox.show({
        //                         title: 'Error',
        //                         msg: pesanNya,
        //                         buttons: Ext.MessageBox.OK,
        //                         animateTarget: 'mb9',
        //                         icon: 'ext-mb-error'
        //                     });
        //                 }
        //             });
        //         }
        //     }
        // },{
        //     text: lang('Close'),
        //     margin: '5px',
        //     scale: 'large',
        //     ui: 's-button',
        //     cls: 's-grey',
        //     handler: function() {
        //         thisObj.close();
        //     }
        // }];
        this.callParent(arguments);
    },
   
});