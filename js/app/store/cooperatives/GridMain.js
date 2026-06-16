Ext.define('Koltiva.store.Cooperatives.GridMain',{
    extend: 'Ext.data.Store',
    storeId:'koltiva-cooperatives-list',
    fields: ['CoopID', 'CoopCode', 'CoopName', 'Phone', 'Email', 'YearEstablished', 'Status', 'District','Subdistrict'],
    autoLoad: true,
    pageSize: 50,
    proxy: {
        type: 'ajax',
        url: m_crud + 's',
        extraParams: {
            prov: m_param
        },
        reader: {
            type: 'json',
            root: 'data',
            totalProperty: 'total'
        }
    },
    listeners: {
        beforeload: function(store, operation, options){
            var ptextSearch;

            var patchouli_coop_ls = JSON.parse(localStorage.getItem('patchouli_coop_ls'));
            if(patchouli_coop_ls != null){
                ptextSearch        = patchouli_coop_ls.ptextSearch;
            }else{
                ptextSearch        = "";
            }

            store.proxy.extraParams.prov = m_ProvinceID;
            store.proxy.extraParams.kab = m_DistrictID;
            store.proxy.extraParams.kec = m_SubDistrictID;

            store.proxy.extraParams.textSearch = Ext.getCmp('Koltiva.view.Cooperatives.GridMain-textSearch').getValue();
        }
    }
});
