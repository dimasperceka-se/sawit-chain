/*
* @Author: nikolius
* @Date:   2017-11-08 15:49:07
* @Last Modified by:   nikolius
* @Last Modified time: 2017-11-09 13:38:37
*/

// Define Variabel2 / Object2 / Fungsi yg diperlukan oleh view ini (Begin)
function submitOnEnterGridFarmerGroup(field, event){
    if (event.getKey() == event.ENTER) {
        setFilterLs();
        Ext.getCmp('Koltiva.view.FarmerGroup.GridMainFarmerGroup-gridMainGrid').getStore().loadPage(1);
    }
}

function setFilterLs(){
    localStorage.setItem('patchouli_farmergroup_ls',
        JSON.stringify({
            ptextSearch: Ext.getCmp('Koltiva.view.FarmerGroup.GridMainFarmerGroup-textSearch').getValue()
        })
    );
}

// Define Variabel2 / Object2 / Fungsi yg diperlukan oleh view ini (End)

Ext.define('Koltiva.view.FarmerGroup.GridMainFarmerGroup' ,{
    extend: 'Ext.panel.Panel',
    id: 'Koltiva.view.FarmerGroup.GridMainFarmerGroup',
    renderTo: 'ext-content',
    minHeight:300,
    listeners: {
        afterRender: function(){
            //hilangkan view Filter region
            document.getElementById('divCommonContentRegion').style.display = 'block';

            //load storenya sebelum viewnya aktif
            setFilterLs();
            Ext.getCmp('Koltiva.view.FarmerGroup.GridMainFarmerGroup-gridMainGrid').getStore().load();
        }
    },
    style:'padding:0 15px 15px 15px;margin:5px 0 0 0;',
    initComponent: function() {
        var thisObj = this;

        //store
        var storeGridMain = Ext.create('Koltiva.store.FarmerGroup.GridMain');

        var contextMenuGridFarmerGroup = Ext.create('Ext.menu.Menu',{
            items:[{
                icon: varjs.config.base_url + 'images/icons/new/view.png',
                text: lang('View'),
                handler: function() {
                    var sm = Ext.getCmp('Koltiva.view.FarmerGroup.GridMainFarmerGroup-gridMainGrid').getSelectionModel().getSelection()[0];

                    Ext.getCmp('Koltiva.view.FarmerGroup.GridMainFarmerGroup').destroy(); //destory current view
                    var FormMainFarmerGroup = [];

                    //create object View
                    if(Ext.getCmp('Koltiva.view.FarmerGroup.FormMainFarmerGroup') == undefined){
                        FormMainFarmerGroup = Ext.create('Koltiva.view.FarmerGroup.FormMainFarmerGroup', {
                            opsiDisplay: 'view',
                            viewVar: {
                                FarmerGroupID: sm.get('FarmerGroupID')
                            }
                        });
                    }else{
                        //destroy, create ulang
                        Ext.getCmp('Koltiva.view.FarmerGroup.FormMainFarmerGroup').destroy();
                        FormMainFarmerGroup = Ext.create('Koltiva.view.FarmerGroup.FormMainFarmerGroup', {
                            opsiDisplay: 'view',
                            viewVar: {
                                FarmerGroupID: sm.get('FarmerGroupID')
                            }
                        });
                    }
                }
            },{
                icon: varjs.config.base_url + 'images/icons/new/update.png',
                text: lang('Update'),
                hidden: m_act_update,
                handler: function() {
                    var sm = Ext.getCmp('Koltiva.view.FarmerGroup.GridMainFarmerGroup-gridMainGrid').getSelectionModel().getSelection()[0];

                    Ext.getCmp('Koltiva.view.FarmerGroup.GridMainFarmerGroup').destroy(); //destory current view
                    var FormMainFarmerGroup = [];

                    //create object View
                    if(Ext.getCmp('Koltiva.view.FarmerGroup.FormMainFarmerGroup') == undefined){
                        FormMainFarmerGroup = Ext.create('Koltiva.view.FarmerGroup.FormMainFarmerGroup', {
                            opsiDisplay: 'update',
                            viewVar: {
                                FarmerGroupID: sm.get('FarmerGroupID')
                            }
                        });
                    }else{
                        //destroy, create ulang
                        Ext.getCmp('Koltiva.view.FarmerGroup.FormMainFarmerGroup').destroy();
                        FormMainFarmerGroup = Ext.create('Koltiva.view.FarmerGroup.FormMainFarmerGroup', {
                            opsiDisplay: 'update',
                            viewVar: {
                                FarmerGroupID: sm.get('FarmerGroupID')
                            }
                        });
                    }
                }
            },{
                icon: varjs.config.base_url + 'images/icons/new/delete.png',
                text: lang('Delete'),
                hidden: m_act_delete,
                handler: function(){
                    var sm = Ext.getCmp('Koltiva.view.FarmerGroup.GridMainFarmerGroup-gridMainGrid').getSelectionModel().getSelection()[0];

                    Ext.MessageBox.confirm('Message', 'Do you want to delete this data ?', function(btn) {
                        if (btn == 'yes') {
                            Ext.Ajax.request({
                                waitMsg: 'Please Wait',
                                url: m_api + '/farmer_group/farmer_group_form',
                                method: 'DELETE',
                                params: {
                                    FarmerGroupID: sm.get('FarmerGroupID')
                                },
                                success: function(response, opts) {
                                    Ext.MessageBox.show({
                                        title: 'Information',
                                        msg: lang('Data deleted'),
                                        buttons: Ext.MessageBox.OK,
                                        animateTarget: 'mb9',
                                        icon: 'ext-mb-success'
                                    });

                                    //refresh store
                                    setFilterLs();
                                    Ext.getCmp('Koltiva.view.FarmerGroup.GridMainFarmerGroup-gridMainGrid').getStore().load();
                                },
                                failure: function(response, opts) {
                                    var pesanNya;
                                    if(o.result.message != undefined){
                                        pesanNya = o.result.message;
                                    }else{
                                        pesanNya = lang('Connection error');
                                    }
                                    Ext.MessageBox.show({
                                        title: 'Error',
                                        msg: pesanNya,
                                        buttons: Ext.MessageBox.OK,
                                        animateTarget: 'mb9',
                                        icon: 'ext-mb-error'
                                    });
                                }
                            });
                        }
                    });
                }
            }]
        });

        thisObj.items = [{
            layout: 'column',
            border: false,
            items: [{
                columnWidth: 0.3,
                layout: 'form',
                items:[{
                    xtype: 'button',
                    id: 'Koltiva.view.FarmerGroup.GridMainFarmerGroup-btnListView',
                    style:'margin-top:5px;',
                    text: lang('All Farmer Group'),
                    arrowAlign: 'right',
                    hidden: true,
                    menu : [{
                        text: lang('All Farmer Group'),
                        listeners: {
                            click: function(){
                                alert('All Farmer Group Click')
                            }
                        }
                    },{
                        text: lang('New This Week'),
                        listeners: {
                            click: function(){
                                alert('New This Week Click')
                            }
                        }
                    },{
                        text: lang('Modified This Week'),
                        listeners: {
                            click: function(){
                                alert('Modified This Week Click')
                            }
                        }
                    },{
                        text: lang('Recently Views'),
                        listeners: {
                            click: function(){
                                alert('Recently Views Click')
                            }
                        }
                    }]
                }]
            },{
                columnWidth: 0.7,
                xtype: 'panel',
                frame: false,
                id: 'Koltiva.view.FarmerGroup.GridMainFarmerGroup-gridInformation',
                html: ''
            }]
        },{
            xtype: 'grid',
            id: 'Koltiva.view.FarmerGroup.GridMainFarmerGroup-gridMainGrid',
            style: 'border:1px solid #CCC;margin-top:4px;',
            loadMask: true,
            minHeight:125,
            selType: 'rowmodel',
            store: storeGridMain,
            viewConfig: {
                deferEmptyText: false,
                emptyText: GetDefaultContentNoData()
            },
            dockedItems: [{
                xtype: 'pagingtoolbar',
                store: storeGridMain,
                dock: 'bottom',
                displayInfo: true
            },{
                xtype: 'toolbar',
                dock:'top',
                items: [{
                    icon: varjs.config.base_url + 'images/icons/new/add.png', cls:'Sfr_BtnGridGreen', overCls:'Sfr_BtnGridGreen-Hover',
                    text: lang('Add'),
                    hidden: m_act_add,
                    handler: function() {
                        Ext.getCmp('Koltiva.view.FarmerGroup.GridMainFarmerGroup').destroy(); //destory current view
                        var FormMainFarmerGroup = [];

                        //create object View
                        if(Ext.getCmp('Koltiva.view.FarmerGroup.FormMainFarmerGroup') == undefined){
                            FormMainFarmerGroup = Ext.create('Koltiva.view.FarmerGroup.FormMainFarmerGroup', {
                                opsiDisplay: 'insert'
                            });
                        }else{
                            //destroy, create ulang
                            Ext.getCmp('Koltiva.view.FarmerGroup.FormMainFarmerGroup').destroy();
                            FormMainFarmerGroup = Ext.create('Koltiva.view.FarmerGroup.FormMainFarmerGroup', {
                                opsiDisplay: 'insert'
                            });
                        }
                    }
                },{
                    icon: varjs.config.base_url + 'images/icons/new/export.png', cls:'Sfr_BtnGridPaleBlue',
                    text: lang('Export'),
                    hidden: m_act_export,
                    handler: function() {
                        Ext.MessageBox.show({
                            msg: 'Please wait...',
                            progressText: 'Exporting...',
                            width: 300,
                            wait: true,
                            waitConfig: {
                                interval: 200
                            },
                            icon: 'ext-mb-download', //custom class in msg-box.html
                            animateTarget: 'mb7'
                        });

                        var filter = {};
                        filter.prov = m_ProvinceID;
                        filter.kab = m_DistrictID;
                        var keys = Object.keys(filter);
                        var param_string = '?search=1';
                        $.each(keys, function (index, val) {
                            param_string += '&' + val + '=' + filter[val];
                        });

                        try {
                            Ext.destroy(Ext.get('downloadIframe'));
                        } catch (e) {

                        }

                        Ext.Ajax.request({
                            url: m_api + '/farmer_group/export_farmer_group/' + param_string,

                            method: 'GET',
                            waitMsg: lang('Please Wait'),
                            timeout: 360000,
                            success: function (data) {
                                Ext.MessageBox.hide();
                                var jsonResp = JSON.parse(data.responseText);
                                window.location = jsonResp.filenya;
                            },
                            failure: function () {
                                Ext.MessageBox.hide();
                                Ext.MessageBox.show({
                                    title: 'Notifications',
                                    msg: 'Failed to export, Please try again.',
                                    buttons: Ext.MessageBox.OK,
                                    animateTarget: 'mb9',
                                    icon: 'ext-mb-error'
                                });
                            }
                        });
                    }
                },{
                    name: 'key', baseCls:'Sfr_TxtfieldSearchGrid',
                    id: 'Koltiva.view.FarmerGroup.GridMainFarmerGroup-textSearch',
                    xtype: 'textfield',
                    width: 400,
                    emptyText: lang('Cari berdasar nama/ID')+', '+lang('Press \'Enter\' to search'),
                    hidden: true,
                    listeners: {
                        specialkey: submitOnEnterGridFarmerGroup
                    }
                },{
                    xtype:'tbspacer',
                    flex:1
                },{
                    xtype:'button',
                    icon: varjs.config.base_url + 'images/icons/new/add-filter.png',
                    text:lang('Apply Filter'),
                    cls:'Sfr_BtnGridPaleBlue',
                    overCls:'Sfr_BtnGridPaleBlue-Hover',
                    handler: function() {
                        var WinApplyFilter = Ext.create('Koltiva.view.FarmerGroup.WinApplyFilter', {
                            viewVar: {
                                FarmerGroupStoreGrid: storeGridMain
                            }
                        });
                        if (!WinApplyFilter.isVisible()) {
                            WinApplyFilter.center();
                            WinApplyFilter.show();
                        } else {
                            WinApplyFilter.close();
                        }
                    }
                },{
                    icon: varjs.config.base_url + 'images/icons/new/reload.png', cls:'Sfr_BtnGridBlue', overCls:'Sfr_BtnGridBlue-Hover',
                    tooltip: lang('Reload'),
                    handler: function() {
                        //reload
                        setFilterLs();
                        Ext.getCmp('Koltiva.view.FarmerGroup.GridMainFarmerGroup-gridMainGrid').getStore().loadPage(1);
                    }
                }]
            }],
            columns: [{
                text: lang('Action'),
                xtype:'actioncolumn',
                flex: 0.3,
                items:[{
                    icon: varjs.config.base_url + 'images/icons/new/action.png',
                    handler: function(grid, rowIndex, colIndex, item, e, record) {
                        contextMenuGridFarmerGroup.showAt(e.getXY());
                    }
                }]
            },{
                text: lang('Farmer Group ID'),
                dataIndex: 'FarmerGroupID',
                flex: 1,
            },{
                text: lang('Group Name'),
                dataIndex: 'GroupName',
                flex: 1.5,
            }, {
                text: lang('Year Establised'),
                flex: 1,
                dataIndex: 'YearEstablished'
            },{
                text: lang('Province'),
                flex: 1,
                dataIndex: 'Province'
            },{
                text: lang('District'),
                flex: 1,
                dataIndex: 'District'
            },{
                text: lang('Registered Farmers'),
                flex: 1,
                dataIndex: 'FarmerRegistered'
            },{
                text: lang('Last Updated'),
                flex: 1,
                dataIndex: 'LastUpdated'
            }]
        }];

        this.callParent(arguments);
    }
});