/*
* @Author: nikolius
* @Date:   2017-07-18 17:44:14
* @Last Modified by:   nikolius
* @Last Modified time: 2017-09-06 15:49:31
*/
Ext.define('Koltiva.store.SME.GridMain', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.SME.GridMain',
    fields: ['MemberIDInc','id', 'agCompanyName','StatusSME', 'Name','Desa','Kecamatan','LastUpdated','Province','District','Birthdate','Age','DateCollection','Handphone','Enumerator','MemberRole','MemberTypeID', 'GPS', 'NrFarmer', 'MillName'],
    pageSize: 30,
    autoLoad: false,
    remoteSort: true,
    proxy: {
        type: 'ajax',
        extraParams: {
            prov: m_ProvinceID,
            kab: m_DistrictID,
            kec: m_SubDistrictID,
        },
        url: m_api + '/sme/grid_main',
        reader: {
            type: 'json',
            root: 'data',
            totalProperty: 'total'
        }
    },
    listeners: { 
        sort: function(store, records, success){
            if(success == true){
                Ext.Ajax.request({
                    url: m_api + '/tools/information_grid',
                    waitMsg: lang('Please Wait'),
                    success: function(data) {
                        Ext.getCmp('Koltiva.view.Trader.GridMainTrader-gridInformation').update(data.responseText);
                    }
                });
            }
        },
        beforeload: function(store, operation, options){
            var ptextSearch, ptextSearchDesa, pAdvRowHandphone, pAdvTextHandphone, pAdvRowAge, pAdvOpAge, pAdvTextAge;

            var patchouli_trader_ls = JSON.parse(localStorage.getItem('patchouli_trader_ls'));

            if(patchouli_trader_ls != null){
                ptextSearch = patchouli_trader_ls.ptextSearch;
                ptextSearchDesa = patchouli_trader_ls.ptextSearchDesa;
                pCmbRoleSearch = patchouli_trader_ls.pCmbRoleSearch;
                pAdvRowHandphone = patchouli_trader_ls.pAdvRowHandphone;
                pAdvTextHandphone = patchouli_trader_ls.pAdvTextHandphone;
                pAdvRowAge = patchouli_trader_ls.pAdvRowAge;
                pAdvOpAge = patchouli_trader_ls.pAdvOpAge;
                pAdvTextAge = patchouli_trader_ls.pAdvTextAge;
            }else{
                ptextSearch = "";
                ptextSearchDesa = "";
                pCmbRoleSearch = "";
                pAdvRowHandphone = "";
                pAdvTextHandphone = "";
                pAdvRowAge = "";
                pAdvOpAge = "";
                pAdvTextAge = "";
            }

            store.proxy.extraParams.prov = m_ProvinceID;
            store.proxy.extraParams.kab = m_DistrictID;
            store.proxy.extraParams.kec = m_SubDistrictID;
            store.proxy.extraParams.textSearch = ptextSearch;
            store.proxy.extraParams.textSearchDesa = ptextSearchDesa;
            store.proxy.extraParams.roleSearch = (pCmbRoleSearch !== undefined) ? pCmbRoleSearch.toString(): "";
            store.proxy.extraParams.AdvRowHandphone = pAdvRowHandphone;
            store.proxy.extraParams.AdvTextHandphone = pAdvTextHandphone;
            store.proxy.extraParams.AdvRowAge = pAdvRowAge;
            store.proxy.extraParams.AdvOpAge = pAdvOpAge;
            store.proxy.extraParams.AdvTextAge = pAdvTextAge;
        }
    }
});