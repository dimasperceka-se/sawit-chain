/******************************************
 *  Author : nikolius.lau@gmail.com   
 *  Created On : Wed Sep 05 2018
 *  File : GridMainAnnouncement.js
 *******************************************/

Ext.define('Koltiva.view.FarmCloud.MessagesManagementGrid' ,{
    extend: 'Ext.panel.Panel',
    id: 'Koltiva.view.FarmCloud.MessagesManagementGrid',
    margin: '15px 15px 15px 15px',
    renderTo: 'ext-content',
    viewVar: false,
    setViewVar: function(value){
        this.viewVar = value;
    },
    listeners: {
        afterRender: function(){
            var thisObj = this;
        }
    },
    initComponent: function() {
        var thisObj = this;
        
        //Div nya Filter Region
        document.getElementById('divCommonContentRegion').style.display = 'none';
        // document.getElementById('main-breadcrumb').style.display = 'block';

        thisObj.StoreGridMain = Ext.create('Koltiva.store.FarmCloud.MessagesManagementGrid');

        thisObj.items = [{
            xtype: 'grid',
            id: 'Koltiva.view.FarmCloud.MessagesManagementGrid-GridMain',
            style: 'border:1px solid #CCC;margin-top:4px;',
            loadMask: true,
            selType: 'rowmodel',
            store: thisObj.StoreGridMain,
            viewConfig: {
                deferEmptyText: false,
                emptyText: lang('No Data Available')
            },
            dockedItems: [{
                xtype: 'pagingtoolbar',
                id: 'Koltiva.view.FarmCloud.MessagesManagementGrid-GridMain-GridToolbar',
                store: thisObj.StoreGridMain,
                dock: 'bottom',
                displayInfo: true
            },{
            	xtype: 'toolbar',
                dock:'top',
                items: [{
                    icon: varjs.config.base_url + 'images/icons/new/add.png', cls:'Sfr_BtnGridGreen', overCls:'Sfr_BtnGridGreen-Hover',
                    text: lang('Add'),
                    hidden: m_act_add,
                    handler: function() {
                        Ext.getCmp('Koltiva.view.FarmCloud.MessagesManagementGrid').destroy(); //destory current view
                        var FormMainFarmer = [];

                        //create object View untuk FormMainGrower
                        if(Ext.getCmp('Koltiva.view.FarmCloud.MessagesManagementForm') == undefined){
                            FormMainFarmer = Ext.create('Koltiva.view.FarmCloud.MessagesManagementForm', {
                                viewVar: {
                                    opsiDisplay: 'insert'
                                }
                            });
                        }else{
                            //destroy, create ulang
                            Ext.getCmp('Koltiva.view.FarmCloud.MessagesManagementForm').destroy();
                            FormMainFarmer = Ext.create('Koltiva.view.FarmCloud.MessagesManagementForm', {
                                viewVar: {
                                    opsiDisplay: 'insert'
                                }
                            });
                        }
                    }
                },{
                    icon: varjs.config.base_url + 'images/icons/new/update.png',
                    text: lang('Update'),
                    hidden: m_act_update,
                    handler: function() {
                        var FormMainFarmer = [];
                        var sm = Ext.getCmp('Koltiva.view.FarmCloud.MessagesManagementGrid-GridMain').getSelectionModel().getSelection()[0];
                        if(sm != undefined){
                            Ext.getCmp('Koltiva.view.FarmCloud.MessagesManagementGrid').destroy(); //destory current view
                            //create object View untuk FormMainGrower
                            if(Ext.getCmp('Koltiva.view.FarmCloud.MessagesManagementForm') == undefined){
                                FormMainFarmer = Ext.create('Koltiva.view.FarmCloud.MessagesManagementForm', {
                                    viewVar: {
                                        opsiDisplay: 'update',
                                        MessagesID: sm.get('MessagesID')
                                    }
                                });
                            }else{
                                //destroy, create ulang
                                Ext.getCmp('Koltiva.view.FarmCloud.MessagesManagementForm').destroy();
                                FormMainFarmer = Ext.create('Koltiva.view.FarmCloud.MessagesManagementForm', {
                                    viewVar: {
                                        opsiDisplay: 'update',
                                        MessagesID: sm.get('MessagesID')
                                    }
                                });
                            }
                        }else{
                            Ext.MessageBox.show({
                                title: 'Attention',
                                msg: lang('No item selected'),
                                buttons: Ext.MessageBox.OK,
                                animateTarget: 'mb9',
                                icon: 'ext-mb-info'
                            });
                        }
                    }
                },{
                    icon: varjs.config.base_url + 'images/icons/new/delete.png',
                    text: lang('Delete'),
                    hidden: m_act_update,
                    handler: function() {
                        var sm = Ext.getCmp('Koltiva.view.FarmCloud.MessagesManagementGrid-GridMain').getSelectionModel().getSelection()[0];
                        if(sm != undefined){
                            Ext.MessageBox.confirm(lang('Message'), lang('Do you want to delete this data ?'), function(btn) {
                                if (btn == 'yes') {
                                    Ext.Ajax.request({
                                        waitMsg: 'Please Wait',
                                        url: m_api + '/farmcloud/messages',
                                        method: 'DELETE',
                                        params: {
                                            MessagesID: sm.get('MessagesID')
                                        },
                                        success: function(response, opts) {
                                            Ext.MessageBox.show({
                                                title: 'Information',
                                                msg: lang('Data deleted'),
                                                buttons: Ext.MessageBox.OK,
                                                animateTarget: 'mb9',
                                                icon: 'ext-mb-success'
                                            });
        
                                            //refresh store
                                            thisObj.StoreGridMain.load();
                                        },
                                        failure: function(response, o) {
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
                                }
                            });
                        }else{
                            Ext.MessageBox.show({
                                title: 'Attention',
                                msg: lang('No item selected'),
                                buttons: Ext.MessageBox.OK,
                                animateTarget: 'mb9',
                                icon: 'ext-mb-info'
                            });
                        }
                    }
                }]
            }],
            columns: [{
                dataIndex: 'MessagesID',
                hidden:true
            },{
                text: lang('Message'),
                width: '99%',                
                renderer: function (t, meta, record) {
                    var data = record.getData();
                    var ContentHtml;

                    ContentHtml = `<div class="DivContentGridColumn">
                    <table width="100%">
                    <tr>
                        <td width="75%" valign="top" style="border-right:1px dashed gray;">
                            <h4 style="margin:0px 0px 8px 0px;padding:0px;"><u>`+data.Title+`</u></h4>
                            `+decodeURI(data.Content)+`
                        </td>
                        <td style="padding-left:10px;" width="25%" valign="top">
                            <div><b>`+lang('Status Type')+`: </b>`+data.StatusType+`</div>
                            <div><b>`+lang('Created By')+`: </b>`+data.CreatedBy+`</div>
                            <div><b>`+lang('Last Updated')+`: </b>`+data.LastUpdated+`</div>
                        </td>
                    </tr>
                    </table>
                    </div>`;

                    return ContentHtml;
                }
            }]
        }];

        this.callParent(arguments);
    }
});