/*
* @Author: nikolius
* @Date:   2017-04-12 11:29:42
* @Last Modified by:   nikolius
* @Last Modified time: 2017-04-21 14:01:02
*/
//variabel yg diperlukan
var contextMenuGrid = Ext.create('Koltiva.view.DataAdm.OffData.ContextMenuGridMetadataKcp');

Ext.define('Koltiva.view.DataAdm.OffData.BackendMetadataGrid' ,{
    extend: 'Ext.grid.Panel',
    id: 'mainGridMetadataBackend',
    width: '99.5%',
    style: 'border:1px solid #CCC;margin:5px;',
    renderTo: 'ext-content',
    loadMask: true,
    title: lang('Offline Metadata'),
    selType: 'rowmodel',
    initComponent: function() {
        var objMainGridMetadata = this;

        //store grid
        objMainGridMetadata.store = Ext.create('Koltiva.store.DataAdm.OffData.MainListMetadataBackend');

        //docked item
        objMainGridMetadata.dockedItems = [{
            xtype: 'toolbar',
            items: [{
                icon: varjs.config.base_url + 'images/icons/new/add.png', cls:'Sfr_BtnGridGreen', overCls:'Sfr_BtnGridGreen-Hover',
                text: lang('Generate Metadata (BE Tools)'),
                hidden: !m_act_offline_metadata_generate,
                handler: function() {
                    Ext.MessageBox.show({
                        msg: 'Please wait...',
                        progressText: 'Generating...',
                        width: 300,
                        wait: true,
                        waitConfig: {
                            interval: 200
                        },
                        icon: 'ext-mb-download', //custom class in msg-box.html
                        animateTarget: 'mb7'
                    });

                    Ext.Ajax.request({
                        url: m_api + '/data_adm/off_data/generate_metadata_backend',
                        method: 'POST',
                        params: {opsiDevelopment: 'no'},
                        waitMsg: lang('Please Wait'),
                        success: function(data) {
                            Ext.getCmp('mainGridMetadataBackend').store.load();
                            Ext.MessageBox.hide();
                            Ext.MessageBox.alert('Success', 'File Generated');
                        },
                        failure: function() {
                            Ext.MessageBox.hide();
                            Ext.MessageBox.show({
                                title: 'Notifications',
                                msg: 'Failed to generate offline metadata, Please try again.',
                                buttons: Ext.MessageBox.OK,
                                animateTarget: 'mb9',
                                icon: 'ext-mb-error'
                            });
                        }
                    });
                }
            },{
                icon: varjs.config.base_url + 'images/icons/new/add.png', cls:'Sfr_BtnGridGreen', overCls:'Sfr_BtnGridGreen-Hover',
                text: lang('Generate Metadata Development'),
                hidden: !m_act_offline_metadata_generate_devel,
                handler: function() {
                    Ext.MessageBox.show({
                        msg: 'Please wait...',
                        progressText: 'Generating...',
                        width: 300,
                        wait: true,
                        waitConfig: {
                            interval: 200
                        },
                        icon: 'ext-mb-download', //custom class in msg-box.html
                        animateTarget: 'mb7'
                    });

                    Ext.Ajax.request({
                        url: m_api + '/data_adm/off_data/generate_metadata_backend',
                        method: 'POST',
                        params: {opsiDevelopment: 'yes'},
                        waitMsg: lang('Please Wait'),
                        success: function(data) {
                            Ext.getCmp('mainGridMetadataBackend').store.load();
                            Ext.MessageBox.hide();
                            Ext.MessageBox.alert('Success', 'File Generated');
                            window.location = m_api_base_url+'/files/offline_metadata_devel/metadataoffline-devel.zip';
                        },
                        failure: function() {
                            Ext.MessageBox.hide();
                            Ext.MessageBox.show({
                                title: 'Notifications',
                                msg: 'Failed to generate offline metadata, Please try again.',
                                buttons: Ext.MessageBox.OK,
                                animateTarget: 'mb9',
                                icon: 'ext-mb-error'
                            });
                        }
                    });
                }
            }]
        }]

        this.callParent(arguments);
    },
    columns: [{
        text: 'ID',
        dataIndex: 'MdoffID',
        hidden: true
    },{
        text: lang('Filename'),
        dataIndex: 'Filename',
        width: '40%'
    },{
        text: lang('Date Created'),
        dataIndex: 'DateCreated',
        width: '30%'
    },{
        text: lang('Created By'),
        dataIndex: 'CreatedBy',
        width: '29.5%'
    }],
    listeners: {
        itemclick: function(view, record, item, index, e){
            var sm = record.data;
            //console.log(sm);
            contextMenuGrid.setRowData(sm);
            contextMenuGrid.showAt(e.getXY());
        }
    }
});