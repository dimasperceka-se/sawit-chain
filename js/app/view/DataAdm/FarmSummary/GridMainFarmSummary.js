/*
 * @Author: sofyan
 * @Date:   2021-11-08 
*/

/*
    Param2 yg diperlukan ketika load View ini
    1. ....
*/
function setFilterLs(){
    localStorage.setItem('patchouli_farm_summary_ls',
        JSON.stringify({
            ptextSearch: Ext.getCmp('Koltiva.view.DataAdm.FarmSummary.GridMainFarmSummary-textSearch').getValue(),
            CmbPolygonStatus: Ext.getCmp('Koltiva.view.DataAdm.FarmSummary.WinApplyFilter-CmbPolygonStatus').getValue(),
        })
    );
}

function submitOnEnterGridFarmSummary(field, event){
    if (event.getKey() == event.ENTER) {
        setFilterLs();
        Ext.getCmp('Koltiva.view.DataAdm.FarmSummary.GridMainFarmSummary-gridMainGrid').getStore().loadPage(1);
    }
}

Ext.define('Koltiva.view.DataAdm.FarmSummary.GridMainFarmSummary' ,{
    extend: 'Ext.panel.Panel',
    id: 'Koltiva.view.DataAdm.FarmSummary.GridMainFarmSummary',
    renderTo: 'ext-content',
    requires: [
        'Ext.data.*',
        'Ext.grid.*',
    ],     
    listeners: {
        afterRender: function(){
            //hilangkan view Filter region
            document.getElementById('divCommonContentRegion').style.display = 'block';
            //load storenya sebelum viewnya aktif
            setFilterLs();
            Ext.getCmp('Koltiva.view.DataAdm.FarmSummary.GridMainFarmSummary-gridMainGrid').getStore().load();
        }
    },
    style:'padding:0 15px 15px 15px;margin:5px 0 0 0;',
    initComponent: function() {
        var thisObj = this;
        
        //store
        var storeGridMain = Ext.create('Koltiva.store.DataAdm.FarmSummary.GridMainFarmSummary');

        thisObj.CmbPolygonStatus = Ext.create('Ext.data.Store', {
            fields: ['id', 'label'],
            data : [
                {"id":"all", "label":"All Polygon Status"},
                {"id":"new", "label":"New"},
                {"id":"verified", "label":"Verified"},
                {"id":"overlap", "label":"Overlap"},
                {"id":"retake", "label":"Retake"},
                {"id":"irrelevant", "label":"Irrelevant"},
                {"id":"nullified", "label":"Nullified"}
            ]
        });

        // Store (end)

        thisObj.items = [{
            xtype: 'panel',
            layout: 'column',
            border: false,
            items: [{
                columnWidth: .7,
                xtype: 'panel',
                frame: false,
                id: 'Koltiva.view.DataAdm.FarmSummary.GridMainFarmSummary-gridDiagram',
                html: '<div style="height:75px" id ="chart-status"></div>'
            },{
                columnWidth: .3,
                xtype: 'panel',
                frame: false,
                id: 'Koltiva.view.DataAdm.FarmSummary.GridMainFarmSummary-gridInformation',
                html: ''
            }]
        },{
            xtype: 'grid',
            id: 'Koltiva.view.DataAdm.FarmSummary.GridMainFarmSummary-gridMainGrid',
            style: 'border:1px solid #CCC;margin-top:4px;',
            loadMask: true,
            selType: 'rowmodel',
            store: storeGridMain,
            collapsible: true,
            collapsed: false,
            titleCollapse: true,
            title:lang('Plot Polygon Data'),
            height:650,
            viewConfig: {
                deferEmptyText: false,
                emptyText: GetDefaultContentNoData()
            },
            dockedItems: [{
                xtype: 'pagingtoolbar',
                id: 'Koltiva.view.DataAdm.FarmSummary.GridMainFarmSummary-gridToolbar',
                store: storeGridMain,
                dock: 'bottom',
                displayInfo: true
            },{
                xtype: 'toolbar',
                dock:'top',
                items: [{
                    icon: varjs.config.base_url + 'images/icons/new/export.png', cls:'Sfr_BtnGridPaleBlue',
                    text: 'XLS',
                    handler: function() {
                        Ext.MessageBox.confirm(lang('Message'), lang('Export data ?') , function(btn){
                            if (btn == 'yes') {
                                var patchouli_farm_summary_ls = JSON.parse(localStorage.getItem('patchouli_farm_summary_ls'));


                                Ext.Ajax.request({
                                    url: m_api + '/data_adm/farm_summary/farm_summary_export_excel',
                                    params: {
                                        prov : m_ProvinceID,
                                        kab : m_DistrictID,
                                        kec : m_SubDistrictID,
                                        textSearch : patchouli_farm_summary_ls.ptextSearch,
                                        CmbPolygonStatus : patchouli_farm_summary_ls.CmbPolygonStatus,
                                    },
                                    method: 'POST',
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
                        })        
                    }
                },{
                    store: thisObj.CmbPolygonStatus,
                    editable: false,
                    xtype: 'combobox',
                    queryMode: 'local',
                    displayField: 'label',
                    valueField: 'id',
                    id: 'Koltiva.view.DataAdm.FarmSummary.WinApplyFilter-CmbPolygonStatus',
                    name: 'Koltiva.view.DataAdm.FarmSummary.WinApplyFilter-CmbPolygonStatus',
                    style: 'margin-left:5px;margin-top:5px;',
                    emptyText: lang('All')+' '+lang("Polygon Status"),
                    width: 200,
                    listeners: {
                        change: function (cb, nv, ov) {
                            setFilterLs();
                            Ext.getCmp('Koltiva.view.DataAdm.FarmSummary.GridMainFarmSummary-gridMainGrid').getStore().loadPage(1);
                        }
                    }
                },{
                    name: 'key', baseCls:'Sfr_TxtfieldSearchGrid',
                    id: 'Koltiva.view.DataAdm.FarmSummary.GridMainFarmSummary-textSearch',
                    xtype: 'textfield',
                    width: 400,
                    emptyText: lang('Cari berdasar nama/ID')+', '+lang('Press \'Enter\' to search'),
                    listeners: {
                        specialkey: submitOnEnterGridFarmSummary
                    }
                },{
                    xtype:'tbspacer',
                    flex:1
                },{
                    icon: varjs.config.base_url + 'images/icons/new/reload.png', cls:'Sfr_BtnGridBlue', overCls:'Sfr_BtnGridBlue-Hover',
                    tooltip: lang('Reload'),
                    handler: function() {
                        //reload
                        setFilterLs();
                        Ext.getCmp('Koltiva.view.DataAdm.FarmSummary.GridMainFarmSummary-gridMainGrid').getStore().loadPage(1);
                    }
                }]
            }],
            // fields: ['MemberID','PlotNr','Revision','AreaHa','StatusCheck','DateCreated'],
            columns: [{
                text: 'No',
                width:'3%',
                xtype: 'rownumberer'
            },{
                text: lang('FarmerID'),
                dataIndex: 'MemberDisplayID',
                width: '7%'
            },{
                text: lang('Name'),
                dataIndex: 'MemberName',
                width: '15%'
            },{
                text: lang('PlotNr'),
                dataIndex: 'PlotNr',
                width: '7%'
            },{
                text: lang('Revision'),
                dataIndex: 'Revision',
                width: '7%'
            },{
                text: lang('AreaHa'),
                dataIndex: 'AreaHa',
                width: '7%'
            },{
                text: lang('Status'),
                dataIndex: 'StatusCheck',
                width: '7%'
            },{
                text: lang('District'),
                dataIndex: 'Region',
                width:'15%',
                renderer: function (t, meta, record) {
                    let RetVal;
                    RetVal = `<span class="Sfr_GridColPlaces">`+record.data.ProvinceName+`, `+record.data.DistrictName+`</span>`;
                    return RetVal;
                }
            },{
                text: lang('Location'),
                dataIndex: 'Location',
                width:'15%',
                renderer: function (t, meta, record) {
                    let RetVal, labelLocation;
                    if(record.data.SubDistrictName == '-' && record.data.VillageName == '-') labelLocation = '-';
                    if(record.data.SubDistrictName != '-' && record.data.VillageName == '-') labelLocation = record.data.SubDistrictName;
                    if(record.data.SubDistrictName != '-' && record.data.VillageName != '-') labelLocation = record.data.SubDistrictName+', '+record.data.VillageName;
                    RetVal = `<span class="Sfr_GridColPlaces">`+labelLocation+`</span>`;
                    return RetVal;
                }
            },{
                text: lang('Date Created'),
                dataIndex: 'DateCreated',
                width:'9%'
            }]
        },{
            xtype: 'panel',
            id: 'Koltiva.view.DataAdm.FarmSummary.GridMainFarmSummary-Map',
            style: 'border:1px solid #CCC;margin-top:4px;',
            collapsible: true,
            collapsed: false,
            titleCollapse: true,
            title:lang('Plot Polygon Map'),
            height:650,
        }];

        this.callParent(arguments);

    }

})