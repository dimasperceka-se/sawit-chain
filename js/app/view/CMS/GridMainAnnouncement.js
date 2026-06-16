/******************************************
 *  Author : nikolius.lau@gmail.com   
 *  Created On : Wed Sep 05 2018
 *  File : GridMainAnnouncement.js
 *******************************************/

Ext.define('Koltiva.view.CMS.GridMainAnnouncement' ,{
    extend: 'Ext.panel.Panel',
    id: 'Koltiva.view.ForumTopic',
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

        thisObj.StoreGridMain = Ext.create('Koltiva.store.CMS.GridMainAnnouncement');

        thisObj.items = [{
            xtype: 'grid',
            id: 'Koltiva.view.CMS.GridMainAnnouncement-GridMain',
            style: 'border:1px solid #CCC;margin-top:4px;',
            height: 650,
            loadMask: true,
            selType: 'rowmodel',
            store: thisObj.StoreGridMain,
            viewConfig: {
                deferEmptyText: false,
                emptyText: lang('No Data Available')
            },
            dockedItems: [{
                xtype: 'pagingtoolbar',
                id: 'Koltiva.view.CMS.GridMainAnnouncement-GridMain-GridToolbar',
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
                        var WinFormAnnouncement = Ext.create('Koltiva.view.CMS.WinFormAnnouncement', {
                            viewVar: {
                                OpsiDisplay: 'insert',
                                CallerStore: thisObj.StoreGridMain
                            }
                        });
                        if (!WinFormAnnouncement.isVisible()) {
                            WinFormAnnouncement.center();
                            WinFormAnnouncement.show();
                        } else {
                            WinFormAnnouncement.close();
                        }                    
                    }
                },{
                    icon: varjs.config.base_url + 'images/icons/new/update.png',
                    text: lang('Update'),
                    hidden: m_act_update,
                    handler: function() {
                        var sm = Ext.getCmp('Koltiva.view.CMS.GridMainAnnouncement-GridMain').getSelectionModel().getSelection()[0];
                        if(sm != undefined){
                            var WinFormAnnouncement = Ext.create('Koltiva.view.CMS.WinFormAnnouncement', {
                                viewVar: {
                                    OpsiDisplay: 'update',
                                    CallerStore: thisObj.StoreGridMain,
                                    AnnID: sm.get('AnnID')
                                }
                            });
                            if (!WinFormAnnouncement.isVisible()) {
                                WinFormAnnouncement.center();
                                WinFormAnnouncement.show();
                            } else {
                                WinFormAnnouncement.close();
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
                        var sm = Ext.getCmp('Koltiva.view.CMS.GridMainAnnouncement-GridMain').getSelectionModel().getSelection()[0];
                        if(sm != undefined){
                            Ext.MessageBox.confirm(lang('Message'), lang('Do you want to delete this data ?'), function(btn) {
                                if (btn == 'yes') {
                                    Ext.Ajax.request({
                                        waitMsg: 'Please Wait',
                                        url: m_api + '/cms/announcement',
                                        method: 'DELETE',
                                        params: {
                                            AnnID: sm.get('AnnID')
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
                dataIndex: 'AnnID',
                hidden:true
            },{
                text: lang('Announcement'),
                width: '99%',                
                renderer: function (t, meta, record) {
                    var data = record.getData();
                    var ContentHtml;

                    ContentHtml = `<div class="DivContentGridColumn">
                    <table width="100%">
                    <tr>
                        <td width="75%" valign="top" style="border-right:1px dashed gray;">
                            <h4 style="margin:0px 0px 8px 0px;padding:0px;"><u>`+data.Title+`</u></h4>
                            `+data.Content+`
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