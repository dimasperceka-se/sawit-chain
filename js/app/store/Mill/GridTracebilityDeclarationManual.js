/*
* @Author: nikolius
* @Date:   2017-08-03 15:28:31
* @Last Modified by:   nikolius
* @Last Modified time: 2017-08-08 12:11:43
*/

Ext.define('Koltiva.store.Mill.GridTracebilityDeclarationManual', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.Mill.GridTracebilityDeclarationManual',
    fields: ['MillTCDID','MillID','MillTCDName','DateCreated'],
    pageSize: 50,
    autoLoad: false,
    remoteSort: true,
    proxy: {
        type: 'ajax',
        url: m_api + '/mill/grid_main_tc_declaration_manual',
        reader: {
            type: 'json',
            root: 'data',
            totalProperty: 'total'
        }
    },
    listeners: {
        beforeload: function(store, operation, options){
            var ptextSearch,MillID;

            var patchouli_mill_ls = JSON.parse(localStorage.getItem('patchouli_mill_ls'));
            if(patchouli_mill_ls != null){
                ptextSearch = patchouli_mill_ls.ptextSearch;
                MillID = patchouli_mill_ls.MillID;
            }else{
                ptextSearch = "";
                MillID = "";
            }
            store.proxy.extraParams.textSearch = ptextSearch;
            store.proxy.extraParams.MillID = MillID;
        }
    }
});