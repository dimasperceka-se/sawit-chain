/*
* @Author: nikolius
* @Date:   2018-07-13 10:32:31
* @Last Modified by:   nikolius
* @Last Modified time: 2018-07-23 12:55:51
*/

/*
    Param2 yg diperlukan ketika load View ini
    - IMSMasterID
*/

Ext.define('Koltiva.store.IMS.GridImsMasterDocuments', {
    extend: 'Ext.data.TreeStore',
    id: 'Koltiva.store.IMS.GridImsMasterDocuments',
    storeId: 'Koltiva.store.IMS.GridImsMasterDocuments',
    fields: ['DocMasID','DocumentName','StatusUpload','DateCheck','Remark','isCheck','TemplatePath','StatusLock','StatusLockRaw','DocumentRemark','DocumentFilePath'],
    autoLoad: false,
    storeVar: false,
    setStoreVar: function(value){
        this.storeVar = value;
    },
    proxy: {
        type: 'ajax',
        url: m_api + '/ims/grid_ims_documents_master'
    },
    listeners: {
        load: function(store, records, success) {
            Ext.Ajax.request({
                url: m_api + '/ims/ims_documents_master_information_title',
                waitMsg: lang('Please Wait'),
                params: {
                    IMSMasterID: this.storeVar.IMSMasterID
                },
                success: function(data) {
                    var obj = Ext.decode(data.responseText);
                    var SubTitle;

                    SubTitle = '('+obj.DocUploaded+' '+lang('of')+' '+obj.DocMaster+' '+lang('documents is uploaded')+')';
                    Ext.getCmp('Koltiva.view.IMS.PanelImsMasterDocuments').setTitle(lang('List of IMS Master Documents')+' '+SubTitle);
                }
            });
        },
        beforeload: function(store, operation, options){
            store.proxy.extraParams.IMSMasterID = this.storeVar.IMSMasterID;
        }
    }
});