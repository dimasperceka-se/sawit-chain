/******************************************
 *  Author : n1colius.lau@gmail.com   
 *  Created On : Fri Aug 16 2019
 *  File : GridPartnerHirar.js
 *******************************************/
Ext.define('Koltiva.store.Dashboard.GridPartnerHirar', {
    extend: 'Ext.data.TreeStore',
    id: 'Koltiva.store.Dashboard.GridPartnerHirar',
    storeId: 'Koltiva.store.Dashboard.GridPartnerHirar',
    fields: ['PartnerID','PartnerName'],
    autoLoad: false,
    storeVar: false,
    setStoreVar: function(value){
        this.storeVar = value;
    },
    proxy: {
        type: 'ajax',
        url: m_api + '/dboard/grid_partner_hirar'
    },
    listeners: {
        load: function(store, records, success) {
            var indexIncre = 0;
            var PartnerIDSel = document.getElementById("SupChainMillPartnerIDFilter").value;
            var PartnerIDArr = PartnerIDSel.split(',');

            function traverse(node) {
                node.eachChild(function(child) {
                    if(PartnerIDArr.includes(child.data.PartnerID)) {
                        //Centang Checkbox
                        Ext.getCmp('Koltiva.view.Dashboard.PanelGridPartnerHirar').getSelectionModel().select(indexIncre,true);
                    }

                    indexIncre++;
                    traverse(child);
                });
            }
            traverse(store.getRootNode());
        },
        beforeload: function(store, operation, options){
            store.proxy.extraParams.PartnerID = m_partner;
        }
    }
});