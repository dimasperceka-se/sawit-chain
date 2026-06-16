Ext.define('Koltiva.store.Refinery.GridTracebilityDeclarationManual', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.Refinery.GridTracebilityDeclarationManual',
    fields: ['RefineryTCDID','RefineryID','RefineryTCDName','DateCreated'],
    pageSize: 50,
    autoLoad: false,
    remoteSort: true,
    proxy: {
        type: 'ajax',
        url: m_api + '/refinery/grid_main_tc_declaration_manual',
        reader: {
            type: 'json',
            root: 'data',
            totalProperty: 'total'
        }
    },
    listeners: {
        beforeload: function(store, operation, options){
            var ptextSearch,RefineryID;

            var patchouli_refinery_ls = JSON.parse(localStorage.getItem('patchouli_refinery_ls'));
            if(patchouli_refinery_ls != null){
                ptextSearch = patchouli_refinery_ls.ptextSearch;
                RefineryID = patchouli_refinery_ls.RefineryID;
            }else{
                ptextSearch = "";
                RefineryID = "";
            }
            store.proxy.extraParams.textSearch = ptextSearch;
            store.proxy.extraParams.RefineryID = RefineryID;
        }
    }
});