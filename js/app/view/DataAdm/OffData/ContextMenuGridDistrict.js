/*
* @Author: nikolius
* @Date:   2017-04-07 11:47:30
* @Last Modified by:   nikolius
* @Last Modified time: 2017-08-18 11:41:16
*/
Ext.define('Koltiva.view.DataAdm.OffData.ContextMenuGridDistrict' ,{
    extend: 'Ext.menu.Menu',
    id: 'Koltiva-view-DataAdm-OffData-ContextMenuGridDistrict',
    rowData: false,
    setRowData: function(objRowData){
        this.rowData = objRowData;
    },
    items:[{
        icon: varjs.config.base_url + 'images/icons/new/view.png',
        id: 'Koltiva-view-DataAdm-OffData-ContextMenuGridDistrict-generate',
        text: lang('Generate File'),
        hidden: !m_act_offline_data_generate,
        handler: function() {
            var rowData = Ext.getCmp('Koltiva-view-DataAdm-OffData-ContextMenuGridDistrict').rowData;

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
                url: m_api + '/data_adm/off_data/generate',
                method: 'POST',
                waitMsg: lang('Please Wait'),
                params: {
                    DhisSqlViewID: rowData.DhisSqlViewID,
                    DhisSqlViewName: rowData.DhisSqlViewName
                },
                success: function(data) {
                    Ext.getCmp('mainGridDistrict').store.load();
                    Ext.MessageBox.hide();
                    Ext.MessageBox.alert('Success', 'File Generated');
                },
                failure: function() {
                    Ext.MessageBox.hide();
                    Ext.MessageBox.show({
                        title: 'Notifications',
                        msg: 'Failed to generate offline data, Please try again.',
                        buttons: Ext.MessageBox.OK,
                        animateTarget: 'mb9',
                        icon: 'ext-mb-error'
                    });
                }
            });
        }
    },{
        icon: varjs.config.base_url + 'images/icons/new/view.png',
        id: 'Koltiva-view-DataAdm-OffData-ContextMenuGridDistrict-download',
        text: lang('Download File'),
        hidden: !m_act_offline_data_download,
        handler: function() {
            var rowData = Ext.getCmp('Koltiva-view-DataAdm-OffData-ContextMenuGridDistrict').rowData;
            window.location = m_api_base_url+'/files/offline_data/'+m_sys_date+'_'+rowData.DhisSqlViewName+'.zip';
        }
    }]
});