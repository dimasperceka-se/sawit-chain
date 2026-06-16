/*
 Param2 yg diperlukan ketika load View ini
 - PartnerID
 */

function setFilterLs() {
    localStorage.setItem('patchouli_grower_ls', JSON.stringify({
        ptextSearch: Ext.getCmp('view.FarmerMill.GridMainFarmerMill-textSearch').getValue(),
        pCmbRoleSearch: "",
        pPartnerSearch: Ext.getCmp('view.FarmerMill.GridMainFarmerMill-FarmerMillPartnerIDFilter').getValue(),        
        pPartnerFirstLoad: Ext.getCmp('view.FarmerMill.GridMainFarmerMill-FarmerMillFirstLoad').getValue()
    }));
}
function searchByPartner() {
    setFilterLs();
    Ext.getCmp('view.FarmerMill.GridMainFarmerMill-gridMainGrid').getStore().loadPage(1);
}
Ext.define('Koltiva.view.FarmerMill.WinPopupFarmerMillFilter', {
    extend: 'Ext.window.Window',
    id: 'Koltiva.view.FarmerMill.WinPopupFarmerMillFilter',
    title: lang('Filter Partner'),
    closable: true,
    modal: true,
    closeAction: 'destroy',
    width: '40%',
    height: '80%',
    overflowY: 'auto',
    viewVar: false,
    setViewVar: function (value) {
        this.viewVar = value;
    },
    listeners: {
        afterRender: function () {
            var thisObj = this;
        }
    },
    initComponent: function () {
        var thisObj = this;

        var PanelGridPartnerHirar = Ext.create('Koltiva.view.FarmerMill.PanelGridPartnerHirar', {
            viewVar: {
                PartnerID: thisObj.viewVar.PartnerID
            }
        });

        thisObj.items = [PanelGridPartnerHirar];

        //buttons -------------------------------------------------------------- (begin)
        thisObj.buttons = [{
                text: lang('Search'),
                id: 'Koltiva.view.FarmerMill.WinPopupFarmerMillFilter-BtnFilter',
                icon: varjs.config.base_url + 'images/icons/new/search-white.png',
                cls: 'Sfr_BtnFormBlue',
                overCls: 'Sfr_BtnFormBlue-Hover',
                handler: function () {
                    //document.getElementById("SupChainMillPartnerIDFilter").value = "1,2,3";
                    //runFilter();

                    var gridSelected = Ext.getCmp('Koltiva.view.FarmerMill.PanelGridPartnerHirar').getSelectionModel().getSelection();
                    var IdSelectedArr = [];
                    for (var i = gridSelected.length - 1; i >= 0; i--) {
                        IdSelectedArr.push(gridSelected[i].get('PartnerID'));
                    }
                    console.log(IdSelectedArr);

                    if (IdSelectedArr.length > 0) {
                        Ext.getCmp('view.FarmerMill.GridMainFarmerMill-FarmerMillPartnerIDFilter').setValue(IdSelectedArr.join());
                        Ext.getCmp('view.FarmerMill.GridMainFarmerMill-FarmerMillFirstLoad').setValue("2");
                        searchByPartner();
                        thisObj.close();
                    } else {
                        Ext.MessageBox.show({
                            title: 'Information',
                            msg: lang('No data selected'),
                            buttons: Ext.MessageBox.OK,
                            animateTarget: 'mb9',
                            icon: 'ext-mb-info'
                        });
                    }
                }
            }];
        //buttons -------------------------------------------------------------- (end)

        this.callParent(arguments);
    }
});