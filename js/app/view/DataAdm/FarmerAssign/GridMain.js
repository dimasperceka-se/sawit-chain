/*
 * @Author: fikri
 * @Date:   2017-05-16 12:10:39
 * @Last Modified by: 
 * @Last Modified time: 
 */

/*
 Param2 yg diperlukan ketika load View ini
 1. ....
 */

// Define Variabel2 / Object2 / Fungsi yg diperlukan oleh view ini (begin)

//variabel2 / object2
var contextMenuGrid = Ext.create('Ext.menu.Menu', {
    items: [{
            icon: varjs.config.base_url + 'images/icons/new/view.png',
            text: lang('View'),
            handler: function () {
                var sm = Ext.getCmp('view.DataAdm.FarmerAssign.GridMain-gridMainGrid').getSelectionModel().getSelection()[0];

                Ext.getCmp('Koltiva.view.DataAdm.FarmerAssign.GridMain-MainPanel').destroy(); //destory current view
                //create object View untuk FormMethod
//                if (Ext.getCmp('Koltiva.view.DataAdm.FarmerAssign.FormMethod') == undefined) {
//                    var FormMethod = Ext.create('Koltiva.view.DataAdm.FarmerAssign.FormMethod', {
//                        opsiDisplay: 'view',
//                        formVar: {
//                            MemberID: sm.get('MemberIDInc'),
//                            PartnerSurvey: sm.get('PartnerSurvey')
//                        }
//                    });
//                } else {
//                    //destroy, create ulang
//                    Ext.getCmp('Koltiva.view.DataAdm.FarmerAssign.FormMethod').destroy();
//                    var FormMethod = Ext.create('Koltiva.view.DataAdm.FarmerAssign.FormMethod', {
//                        opsiDisplay: 'view',
//                        formVar: {
//                            MemberID: sm.get('MemberIDInc'),
//                            PartnerSurvey: sm.get('PartnerSurvey')
//                        }
//                    });
//                }
            }
        }, {
            icon: varjs.config.base_url + 'images/icons/new/update.png',
            text: lang('Change Method'),
            handler: function () {
                var sm = Ext.getCmp('view.DataAdm.FarmerAssign.GridMain-gridMainGrid').getSelectionModel().getSelection()[0];

                //create object View untuk FormMethod
                var winFormMethod = Ext.create('Koltiva.view.DataAdm.FarmerAssign.WinFormMethod');
                winFormMethod.setFormVar({UserId: sm.get('UserId'), UserRealName: sm.get('UserRealName'), opsiDisplay: 'update'});
                if (!winFormMethod.isVisible()) {
                    winFormMethod.center();
                    winFormMethod.show();
                } else {
                    winFormMethod.close();
                }
            }
        }]
});

var storeGridMain = Ext.create('Koltiva.store.DataAdm.FarmerAssign.GridMain');

function setFilterLs() {
    localStorage.setItem('patchouli_grower_ls', JSON.stringify({
        ptextSearch: Ext.getCmp('view.DataAdm.FarmerAssign.GridMain-textSearch').getValue(),
        pCmbRoleSearch: Ext.getCmp('view.DataAdm.FarmerAssign.GridMain-CmbRoleSearch').getValue(),
        pCmbCompanySearch: Ext.getCmp('view.DataAdm.FarmerAssign.GridMain-CmbCompanySearch').getValue()
    }));
}

function getFilterLs() {
    var filters = {};
    var ptextSearch, pCmbRoleSearch, pCmbCompanySearch;

    var patchouli_grower_ls = JSON.parse(localStorage.getItem('patchouli_grower_ls'));
    if (patchouli_grower_ls != null) {
        ptextSearch = patchouli_grower_ls.ptextSearch;
        pCmbRoleSearch = patchouli_grower_ls.pCmbRoleSearch;
        pCmbCompanySearch = patchouli_grower_ls.pCmbCompanySearch;
    } else {
        ptextSearch = "";
        pCmbRoleSearch = "";
        pCmbCompanySearch = "";
    }
    var spCmbRoleSearch = pCmbRoleSearch.toString();
    var spCmbCompanySearch = pCmbCompanySearch.toString();

    filters.prov = m_ProvinceID;
    filters.kab = m_DistrictID;
    filters.kec = m_SubDistrictID;
    filters.textSearch = ptextSearch;
    filters.roleSearch = spCmbRoleSearch;
    filters.companySearch = spCmbCompanySearch;

    return filters;
}

function submitOnEnterGrid(field, event) {
    if (event.getKey() == event.ENTER) {
        setFilterLs();
        Ext.getCmp('view.DataAdm.FarmerAssign.GridMain-gridMainGrid').getStore().loadPage(1);
    }
}
// Define Variabel2 / Object2 yg diperlukan oleh view ini (end)

Ext.define('Koltiva.view.DataAdm.FarmerAssign.GridMain', {
    extend: 'Ext.panel.Panel',
    id: 'Koltiva.view.DataAdm.FarmerAssign.GridMain-MainPanel',
    renderTo: 'ext-content',
    opsiDisplay: false,
    setOpsiDisplay: function(value){
        this.opsiDisplay = value;
    },
    formVar: false,
    setFormVar: function(value){
        this.formVar = value;
    },
    listeners: {
        afterRender: function () {
            //hilangkan view Filter region
            document.getElementById('divCommonContentRegion').style.display = 'block';

            //isikan variabel dari local storage
            var patchouli_grower_ls = JSON.parse(localStorage.getItem('patchouli_grower_ls'));
            if (patchouli_grower_ls != null) {
                Ext.getCmp('view.DataAdm.FarmerAssign.GridMain-textSearch').setValue(patchouli_grower_ls.ptextSearch);
                Ext.getCmp('view.DataAdm.FarmerAssign.GridMain-CmbRoleSearch').setValue(patchouli_grower_ls.pCmbRoleSearch);
                Ext.getCmp('view.DataAdm.FarmerAssign.GridMain-CmbCompanySearch').setValue(patchouli_grower_ls.pCmbCompanySearch);
            }

            //load storenya sebelum viewnya aktif
            setFilterLs();
            Ext.getCmp('view.DataAdm.FarmerAssign.GridMain-gridMainGrid').getStore().load();
        }
    },
    style: 'padding:0 15px 15px 15px;margin:5px 0 0 0;',
    initComponent: function () {
        var thisObj = this;

        //store yg dipakai (begin)
        var cmb_role_user = Ext.create('Koltiva.store.DataAdm.FarmerAssign.CmbRoleUser');
        var cmb_company = Ext.create('Koltiva.store.DataAdm.FarmerAssign.CmbCompany');
        //store yg dipakai (end)

        //items
        thisObj.items = [{
                layout: 'column',
                border: false,
                items: [{
                        columnWidth: .3,
                        layout: 'form',
                        items: [{
                                xtype: 'button',
                                id: 'view.DataAdm.FarmerAssign.GridMain-btnListView',
                                style: 'margin-top:5px;',
                                text: lang('All Farmers'),
                                arrowAlign: 'right',
                                hidden: true,
                                menu: [{
                                        text: lang('All Farmers'),
                                        listeners: {
                                            click: function () {
                                                alert('All Farmers Click')
                                            }
                                        }
                                    }, {
                                        text: lang('New This Week'),
                                        listeners: {
                                            click: function () {
                                                alert('New This Week Click')
                                            }
                                        }
                                    }, {
                                        text: lang('Modified This Week'),
                                        listeners: {
                                            click: function () {
                                                alert('Modified This Week Click')
                                            }
                                        }
                                    }, {
                                        text: lang('Recently Views'),
                                        listeners: {
                                            click: function () {
                                                alert('Recently Views Click')
                                            }
                                        }
                                    }]
                            }]
                    }, {
                        columnWidth: .7,
                        xtype: 'panel',
                        frame: false,
                        id: 'view.DataAdm.FarmerAssign.GridMain-gridInformation',
                        html: ''
                    }]
            }, {
                xtype: 'grid',
                id: 'view.DataAdm.FarmerAssign.GridMain-gridMainGrid',
                style: 'border:1px solid #CCC;margin-top:4px;',
                loadMask: true,
                selType: 'rowmodel',
                store: storeGridMain,
                viewConfig: {
                    deferEmptyText: false,
                    emptyText: lang('No data Available')
                },
                dockedItems: [{
                        xtype: 'pagingtoolbar',
                        id: 'view.DataAdm.FarmerAssign.GridMain-gridToolbar',
                        store: storeGridMain,
                        dock: 'bottom',
                        displayInfo: true
                    }, {
                        xtype: 'toolbar',
                        dock: 'top',
                        items: [
                            {
                                id: 'view.DataAdm.FarmerAssign.GridMain-CmbRoleSearch',
                                xtype: 'combobox',
                                store: cmb_role_user,
                                emptyText: lang('User Role'),
                                displayField: 'label',
                                valueField: 'id',
                                queryMode: 'local',
//                                multiSelect: true,
                                width: 300,
                                listeners: {
                                    change: function (cb, nv, ov) {
                                        cmb_company.load({
                                            params: {
                                                UserRole: nv
                                            }
                                        });
                                        setFilterLs();
                                        Ext.getCmp('view.DataAdm.FarmerAssign.GridMain-gridMainGrid').getStore().loadPage(1);
                                        Ext.getCmp('view.DataAdm.FarmerAssign.GridMain-CmbCompanySearch').getStore().loadPage(1);
//                                        return false;
                                    }
                                }
                            }, {
                                id: 'view.DataAdm.FarmerAssign.GridMain-CmbCompanySearch',
                                xtype: 'combobox',
                                store: cmb_company,
                                emptyText: lang('Institution/ Company Name'),
                                displayField: 'label',
                                valueField: 'id',
                                queryMode: 'local',
//                                multiSelect: true,
                                width: 300,
                                listeners: {
                                    change: function (cb, nv, ov) {
                                        setFilterLs();
                                        Ext.getCmp('view.DataAdm.FarmerAssign.GridMain-gridMainGrid').getStore().loadPage(1);
                                    }
                                }
                            }, {
                                name: 'key', baseCls:'Sfr_TxtfieldSearchGrid',
                                id: 'view.DataAdm.FarmerAssign.GridMain-textSearch',
                                xtype: 'textfield',
                                width: 400,
                                emptyText: lang('Cari berdasar nama/ID') + ', ' + lang('Press \'Enter\' to search'),
                                listeners: {
                                    specialkey: submitOnEnterGrid
                                }
                            },
                            /*{
                             xtype: 'button',
                             margin: '0px 0px 0px 6px',
                             text: 'Search',
                             handler: function () {
                             setFilterLs();
                             Ext.getCmp('view.DataAdm.FarmerAssign.GridMain-gridMainGrid').getStore().loadPage(1);
                             }
                             },*/
                            {
                                xtype: 'tbspacer',
                                flex: 1
                            }, {
                                icon: varjs.config.base_url + 'images/icons/new/reload.png', cls:'Sfr_BtnGridBlue', overCls:'Sfr_BtnGridBlue-Hover',
                                tooltip: lang('Reload'),
                                handler: function () {
                                    //reload
                                    setFilterLs();
                                    Ext.getCmp('view.DataAdm.FarmerAssign.GridMain-gridMainGrid').getStore().loadPage(1);
                                }
                            }]
                    }],
                columns: [{
                        text: lang('Action'),
                        xtype: 'actioncolumn',
                        width: '3%',
                        items: [{
                                icon: varjs.config.base_url + 'images/icons/new/action.png',
                                handler: function (grid, rowIndex, colIndex, item, e, record) {
                                    contextMenuGrid.showAt(e.getXY());
                                }
                            }]
                    }, {
                        id: 'view.DataAdm.FarmerAssign.GridMain-colUserId',
                        text: lang('UserId'),
                        dataIndex: 'UserId',
                        hidden: true
                    }, {
                        id: 'view.DataAdm.FarmerAssign.GridMain-colUserRealName',
                        text: lang('Staff Name'),
                        width: '20%',
                        dataIndex: 'UserRealName'
                    }, {
                        id: 'view.DataAdm.FarmerAssign.GridMain-colRole',
                        text: lang('Role'),
                        dataIndex: 'ObjType',
                        width: '7%'
                    }, {
                        id: 'view.DataAdm.FarmerAssign.GridMain-colCompany',
                        text: lang('Insitution/Company'),
                        dataIndex: 'PartnerFullName',
                        width: '15%'
                    }, {
                        id: 'view.DataAdm.FarmerAssign.GridMain-colPosition',
                        text: lang('Position'),
                        dataIndex: 'GroupName',
                        width: '10%'
                    }, {
                        id: 'view.DataAdm.FarmerAssign.GridMain-colUserName',
                        text: lang('User Name'),
                        dataIndex: 'UserName',
                        width: '20%'
                    }, {
                        id: 'view.DataAdm.FarmerAssign.GridMain-colGroup',
                        text: lang('Group'),
                        dataIndex: 'GroupName',
                        width: '10%'
                    }, {
                        id: 'view.DataAdm.FarmerAssign.GridMain-colUserStatus',
                        text: lang('User Status'),
                        dataIndex: 'StatusCode',
                        width: '5'
                    }, {
                        id: 'view.DataAdm.FarmerAssign.GridMain-colSync',
                        text: lang('Sync Farmer Downloas Method'),
                        dataIndex: 'UserId',
                        width: '10%'
                    }]
            }];

        this.callParent(arguments);
    }
});