/*
* @Author: nikolius
* @Date:   2018-07-16 13:32:23
* @Last Modified by:   nikolius
* @Last Modified time: 2018-07-23 12:57:06
*/

/*
    Param2 yg diperlukan ketika load View ini
    - IMSID
*/

Ext.define('Koltiva.store.IMS.GridImsEventDocuments', {
    extend: 'Ext.data.TreeStore',
    id: 'Koltiva.store.IMS.GridImsEventDocuments',
    storeId: 'Koltiva.store.IMS.GridImsEventDocuments',
    fields: ['DocEveID','DocumentName','StatusUpload','DateCheck','Remark','isCheck','TemplatePath','DocumentRemark','DocumentFilePath'],
    autoLoad: false,
    storeVar: false,
    setStoreVar: function(value){
        this.storeVar = value;
    },
    proxy: {
        type: 'ajax',
        url: m_api + '/ims/grid_ims_documents_event'
    },
    listeners: {
        load: function(store, records, success) {
            Ext.Ajax.request({
                url: m_api + '/ims/ims_documents_event_information_title',
                waitMsg: lang('Please Wait'),
                params: {
                    IMSID: this.storeVar.IMSID
                },
                success: function(data) {
                    var obj = Ext.decode(data.responseText);
                    var SubTitle;

                    SubTitle = '('+obj.DocUploaded+' '+lang('of')+' '+obj.DocMaster+' '+lang('documents is uploaded')+')';
                    Ext.getCmp('Koltiva.view.IMS.PanelImsEventDocuments').setTitle(lang('List of IMS Event Documents')+' '+SubTitle);
                }
            });
        },
        beforeload: function(store, operation, options){
            store.proxy.extraParams.IMSID = this.storeVar.IMSID;
        }
    }
});