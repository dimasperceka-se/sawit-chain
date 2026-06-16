/*
* @Author: Gitandi Nadzari
* @Date:   2019-05-29 15:40:00
* @Last Modified by:  
* @Last Modified time: 
*/



// var RowEditing = Ext.create('Ext.grid.plugin.RowEditing', {
//     id: 'RowEditing',
//     clicksToMoveEditor: 0,
//     autoCancel: false,
//     errorSummary: false,
//     clicksToEdit: 2
// });

var prog_stage = Ext.create('Ext.data.Store', {
    extend: 'Ext.data.Model',
    fields: ['uid', 'name'],
    autoLoad: true,
    pageSize: 10,
    proxy: {
        type: 'ajax',
        url: m_program,
        reader: {
            type: 'json',
            root: 'data'
        }
    }
});

// var cmbStatusCode = Ext.create('Ext.data.Store', {
//     fields: ['id', 'label'],
//     data: [{
//         "id": "active",
//         "label": "ACTIVE"
//     }, {
//         "id": "inactive",
//         "label": "INACTIVE"
//     }]
// });

// Ext.define('Scpp.Model', {
//     extend: 'Ext.data.Model',
//     fields: ['BrandID', 'BrandName', 'StatusCode']
// });

// function filterRecord() {
//     storeMainGrid.load({
//         params: {
//             start: 0,
//             ProgStageId: Ext.getCmp('filter-ProgStage').getValue(),
//         }
//     });
// }
var contextMappingGrid = Ext.create('Ext.menu.Menu',{
    items:[{
        icon: varjs.config.base_url + 'images/icons/new/update.png',
        text: lang('Update'),
        hidden: m_act_update,
        handler: function(){
            var sm = Ext.getCmp('Koltiva.view.DataAdm.MappingMetadata.MainGrid-gridMainGrid').getSelectionModel().getSelection()[0];
            var winFormMapping = Ext.create('Koltiva.view.DataAdm.MappingMetadata.MainForm');
        	winFormMapping.setFormVar({MappingId:sm.get('program_uid'),opsiDisplay:'update'});
            if(sm.get('mw_mapping_id')){
                winFormMapping.setFormVar({MappingId:sm.get('mw_mapping_id'),IdProgram:sm.get('program_uid'),DeUid:sm.get('de_uid'),tblReff:sm.get('table_reff'),fieldReff:sm.get('field_reff'),opsiDisplay:'update'});
                if (!winFormMapping.isVisible()) {
                    winFormMapping.center();
                    winFormMapping.show();
                } else {
                    winFormMapping.close();
                }
            } else {
                winFormMapping.setFormVar({DeName:sm.get('de_name'),DeUid:sm.get('de_uid'),IdProgram:sm.get('program_uid'),opsiDisplay:'insert'});

                if (!winFormMapping.isVisible()) {
                    winFormMapping.center();
                    winFormMapping.show();
                } else {
                    winFormMapping.close();
                }
            }
        }
    },{
        icon: varjs.config.base_url + 'images/icons/new/delete.png',
        text: lang('Reset Mapping'),
        hidden: m_act_delete,
        handler: function(){
            var sm = Ext.getCmp('Koltiva.view.DataAdm.MappingMetadata.MainGrid-gridMainGrid').getSelectionModel().getSelection()[0];
            if(!sm.get('mw_mapping_id')){
                alert('Cannot reset this mapping data!');
                return;
            }
            Ext.MessageBox.confirm(lang('Message'), lang('Do you want to reset this mapping data ?'), function(btn) {
                if (btn == 'yes') {
                    Ext.Ajax.request({
                        waitMsg: 'Please Wait',
                        url: m_api + '/data_adm/map_metadata/MappingData_form',
                        method: 'DELETE',
                        params: {
                            MappingId:sm.get('mw_mapping_id')
                        },
                          
                        success: function(response, opts) {
                            Ext.MessageBox.show({
                                title: 'Information',
                                msg: lang('Mapping Data Reseted'),
                                buttons: Ext.MessageBox.OK,
                                animateTarget: 'mb9',
                                icon: 'ext-mb-success'
                            });

                            //refresh store
                            // Ext.data.StoreManager.lookup('Koltiva-Store-Metadata-Gridmain').load();
                            Ext.data.StoreManager.lookup('Koltiva.store.DataAdm.MappingMetadata.MainGrid').load({
                                params: {
                                    ProgStageId: Ext.getCmp('filter-ProgStage').getValue(),
                                }
                            });
                            // storeMainGrid.load({
                            //     params: {
                            //         ProgStageId: Ext.getCmp('filter-ProgStage').getValue(),
                            //     }
                            // });
                        },
                        failure: function(rp, o) {
                            try {
                                var r = Ext.decode(rp.responseText);
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
                                    msg: 'Connection Error',
                                    buttons: Ext.MessageBox.OK,
                                    animateTarget: 'mb9',
                                    icon: 'ext-mb-error'
                                });
                            }                                }
                    });
                }
            });
        }
    }]
});

Ext.define('Koltiva.view.DataAdm.MappingMetadata.MainGrid' ,{
    extend: 'Ext.panel.Panel',
    id: 'Koltiva.view.DataAdm.MappingMetadata.MainGrid-MainPanel',
    renderTo: 'ext-content',
    listeners: {
        afterRender: function(){
            Ext.getCmp('Koltiva.view.DataAdm.MappingMetadata.MainGrid-gridMainGrid').getStore().load();
        }
    },
    style:'padding:0 15px 15px 15px;margin:5px 0 0 0;',
    initComponent: function() {
        var thisObj = this;
        
        var storeMainGrid = Ext.create('Koltiva.store.DataAdm.MappingMetadata.MainGrid');
        thisObj.storeMainGrid = storeMainGrid;

        thisObj.RowEditing = Ext.create('Ext.grid.plugin.RowEditing', {
            id: 'RowEditing',
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
        
        thisObj.items = [{
            xtype: 'grid',
            id: 'Koltiva.view.DataAdm.MappingMetadata.MainGrid-gridMainGrid',
            style: 'border:1px solid #CCC;margin-top:4px;',
            loadMask: true,
            selType: 'rowmodel',
            store: storeMainGrid,
            viewConfig: {
                deferEmptyText: false,
                emptyText: lang('No data Available')
            },
            dockedItems: [{
                xtype: 'pagingtoolbar',
                id: 'Koltiva.view.DataAdm.MappingMetadata.MainGrid-gridToolbar',
                store: storeMainGrid,
                dock: 'bottom',
                displayInfo: true
            },{
                xtype: 'toolbar',
                items: [{
                     xtype: 'combobox',
                     id: 'filter-ProgStage',
                     emptyText: 'Program Name',
                     store: prog_stage,
                     queryMode: 'local',
                     displayField: 'name',
                     valueField: 'uid',
                 },{
                     xtype: 'button',
                     margin: '0px 0px 0px 6px',
                     text: 'Search',
                     handler: function () {
                         thisObj.filterRecord();
                     }
                 },{
                     xtype:'container',
                     flex:1
                 },{
                    xtype: 'button',
                    margin: '0px 0px 6px 6px',
                    text: 'Refresh Metadata',
                    handler: function () {
                        Ext.MessageBox.confirm('Message', lang('Refresh Metadata By ?'), function(btn){
                    
                            if(btn == 'yes')
                            { 
                                thisObj.refreshMetadataRecord();
                            }
                        });
                    }
                 }]
            }],
            columns: [{ 
                dataIndex: 'programid',
                hidden:true
            },{
                dataIndex: 'program_stage_uid',
                hidden:true
            },{
                text: lang('Action'),
                xtype:'actioncolumn',
                width:'5%',
                items:[{
                    icon: varjs.config.base_url + 'images/icons/new/action.png',
                    handler: function(grid, rowIndex, colIndex, item, e, record) {
                        contextMappingGrid.showAt(e.getXY());
                    }
                }]
            },{
                 text: 'No',
                 xtype: 'rownumberer',
                 width:'5%'
            },{
                text: 'mw_mapping_id',
                hidden: true,
                width: '20%',						
                dataIndex: 'mw_mapping_id'
            },{
                text: 'Name',
                width: '20%',
                dataIndex: 'name'
            },{
                text: 'Sec Name',
                width: '20%',
                dataIndex: 'sec_name'
            },{
                text: 'Data Elemen',
                width: '20%',
                dataIndex: 'de_name'
            },{ 
                xtype: 'checkcolumn',
                disabled: true,
                text:  lang('Pull'),
                width: 50,
                dataIndex: 'forPull',
                editor: {
                    xtype:'checkbox'
                },
                renderer: function (val, meta, rec) {
                    if (val=='false') {
                        return  new Ext.grid.column.Check().renderer(false);
                    } else {
                        return  new Ext.grid.column.Check().renderer(true);
                    }
                }
            },{
                text: 'Table Reff',
                width: '20%',
                dataIndex: 'table_reff'
            },{
                text: 'Field Reff',
                width: '20%',
                dataIndex: 'field_reff'
            },{
                text: 'Custom Function',
                width: '20%',
                dataIndex: 'custom_function'
            },{
                text: 'Execute SQL',
                width: '20%',
                dataIndex: 'executeSql'
            },{
                text: 'Priority',
                width: '20%',
                dataIndex: 'priority'
            }],
            plugins: [thisObj.RowEditing],
            listeners: {
                'canceledit':function(editor,e,eOpts){
                    storeMainGrid.load();
                },
                'edit': function(editor, e) {
                    var program_stage_uid = e.record.data.program_stage_uid;
                    var forPull = e.record.data.forPull;
                    Ext.MessageBox.confirm('Message', 'Update data ini ?', function(btn){
                    
                        if(btn == 'yes')
                        { 
                            Ext.Ajax.request({
                                waitMsg: 'Please wait...',
                                url: m_updatepullinfo,
                                method : 'POST',
                                params: {
                                    program_stage_uid: program_stage_uid,
                                    forPull: forPull
                                },
                                success: function(response, opts){
                                    var obj = Ext.decode(response.responseText);
                                    switch(obj.success){
                                        case true:
                                        Ext.MessageBox.alert('Success',obj.message);
                                        storeMainGrid.load({
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
        }]
        this.callParent(arguments);
    },
    filterRecord() {
        var thisObj = this;
        thisObj.storeMainGrid.load({
            params: {
                // start: 0,
                ProgStageId: Ext.getCmp('filter-ProgStage').getValue()
            }
        });
    }, 
    refreshMetadataRecord(){
        Ext.Ajax.request({
            waitMsg: 'Please wait...',
            url: m_reloadmetadatakafka,
            method : 'GET',
            success: function(response, opts){
                var obj = Ext.decode(response.responseText);
                switch(obj.result){
                    case 'OK':
                    Ext.MessageBox.alert('Success',obj.message+' (id :'+obj.id+')');
                    break;
                    default:
                    Ext.MessageBox.alert('Warning',obj.message);
                    break;
                }
            },
            failure: function(response, opts){
                var obj = Ext.decode(response.responseText);
                Ext.MessageBox.alert('error','Could not connect to api. Retry later');
            }
        });
    }
});
 