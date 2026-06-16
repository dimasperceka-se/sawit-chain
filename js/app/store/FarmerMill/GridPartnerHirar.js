/******************************************
 *  Author : n1colius.lau@gmail.com   
 *  Created On : Fri Aug 16 2019
 *  File : GridPartnerHirar.js
 *******************************************/
Ext.define('Koltiva.store.FarmerMill.GridPartnerHirar', {
    extend: 'Ext.data.TreeStore',
    id: 'Koltiva.store.FarmerMill.GridPartnerHirar',
    storeId: 'Koltiva.store.FarmerMill.GridPartnerHirar',
    fields: ['PartnerID', 'PartnerName'],
    autoLoad: false,
    storeVar: false,
    setStoreVar: function (value) {
        this.storeVar = value;
    },
    proxy: {
        type: 'ajax',
        url: m_api + '/dboard/grid_partner_hirar'
    },
    listeners: {
        load: function (store, records, success) {
            var indexIncre = 0;
            var PartnerIDSel = Ext.getCmp('view.FarmerMill.GridMainFarmerMill-FarmerMillPartnerIDFilter').getValue();
            var PartnerIDArr = PartnerIDSel.split(',');

            var FirstReload = Ext.getCmp('view.FarmerMill.GridMainFarmerMill-FarmerMillFirstLoad').getValue();
            if (FirstReload == "1") {
                Ext.Ajax.request({
                    url: m_api + '/grower/getPartnerParent',
                    waitMsg: lang('Please Wait'),
                    success: function (data) {
                        var PartnerID = data.responseText.substring(1);
                        PartnerID = PartnerID.substring(0, PartnerID.length - 1);
                        Ext.getCmp('view.FarmerMill.GridMainFarmerMill-FarmerMillPartnerIDFilter').setValue(PartnerID);
                        var PartnerIDArr = PartnerID.split(',');
                        console.log(PartnerIDArr);
                        $.each(PartnerIDArr, function (key, val) {
                            Ext.getCmp('Koltiva.view.FarmerMill.PanelGridPartnerHirar').getSelectionModel().select(key, true);
                        });
                    }
                });
            } else {
                function traverse(node) {
                    node.eachChild(function (child) {
                        if (PartnerIDArr.includes(child.data.PartnerID)) {
                            //Centang Checkbox
                            Ext.getCmp('Koltiva.view.FarmerMill.PanelGridPartnerHirar').getSelectionModel().select(indexIncre, true);
                        }

                        indexIncre++;
                        traverse(child);
                    });
                }
                traverse(store.getRootNode());
            }
        },
        beforeload: function (store, operation, options) {
            store.proxy.extraParams.PartnerID = m_partner;
        }
    }
});