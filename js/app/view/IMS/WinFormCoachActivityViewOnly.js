/*
 * @Author: nikolius
 * @Date:   2018-03-16 16:20:25
 * @Last Modified by:   nikolius
 * @Last Modified time: 2018-03-19 12:02:53
 */
var cmbPropinsiGeneral = Ext.create('Koltiva.store.ComboGeneral.ComboProvince');
var cmbDistrictGeneral = Ext.create('Koltiva.store.ComboGeneral.ComboDistrict');
var cmbSubDistrictGeneral = Ext.create('Koltiva.store.ComboGeneral.ComboSubDistrict');
var cmbVillageGeneral = Ext.create('Koltiva.store.ComboGeneral.ComboVillage');
var cmbFarmerGroupGeneral = Ext.create('Koltiva.store.ComboGeneral.CmbFarmerGroup');
var cmbCertHolderGeneral = Ext.create('Koltiva.store.ComboGeneral.CmbCertHolderGeneral');
var cmbCertProgramsGeneral = Ext.create('Koltiva.store.ComboGeneral.CmbCertProgramsGeneral');
var cmbImsEventGeneral = Ext.create('Koltiva.store.ComboGeneral.CmbImsEventGeneral');
var cmbFarmerTypeGeneral = Ext.create('Koltiva.store.ComboGeneral.CmbFarmerTypeGeneral');
/////////////////////////////////////////////////PARTTICIPANT/////////////////////////////////////////////////////////////////
var storeGridActivityNC = Ext.create('Ext.data.Store', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.socialization.GridMain',
    fields: ['ActivityNCID', 'ActivityID', 'Topic', 'UrgentlyStatus', 'Subtopic', 'Recommendation', 
    'FollowupStatus', 'Deadline', 'Explanation', 'CreatedBy', 'DateCreated'],
    pageSize: 20,
    autoLoad: false,
    remoteSort: true,
    setStoreVar: function(value) {
        this.storeVar = value;
    },
    proxy: {
        type: 'ajax',
        url: m_api + '/ims/getGridActivityNC',
        reader: {
            type: 'json',
            root: 'data',
            totalProperty: 'total'
        }
    },
    listeners: {
        beforeload: function(store, operation, options) {
            //store.proxy.extraParams.ActivityID =  thisObj.viewVar.ActivityID;
            store.proxy.extraParams.ActivityID = Ext.getCmp('Koltiva.view.IMS.WinFormCoachActivityViewOnly').viewVar.ActivityID;
        }
    }
});
var Grid_Participant_history = {
    xtype: 'grid',
    id: 'Koltiva.view.application_form.GridMainParticipantHistory-FormDetail',
    style: 'border:1px solid #CCC;margin-top:4px;',
    cls: 'Sfr_GridNew',
    loadMask: true,
    store: storeGridActivityNC, 
    columns: [{
        text: lang('Activity NC ID'),
        flex: 1,
        dataIndex: 'ActivityNCID',
    }, {
        text: lang('Topic'),
        flex: 1,
        dataIndex: 'Topic',
    }, {
        text: lang('Subtopic'),
        flex: 1,
        dataIndex: 'Subtopic',
    }, {
        text: lang('Urgently Status'),
        flex: 1,
        dataIndex: 'UrgentlyStatus',
    }, {
        text: lang('Recommendation'),
        flex: 1,
        dataIndex: 'Recommendation',
    }, {
        text: lang('FollowupStatus'),
        dataIndex: 'FollowupStatus',
        flex: 1,
    }, {
        text: lang('Deadline'),
        flex: 1,
        dataIndex: 'Deadline',
    }, {
        text: lang('Explanation'),
        flex: 1,
        dataIndex: 'Explanation',
    }, {
        text: lang('CreatedBy'),
        flex: 1,
        dataIndex: 'CreatedBy',
    }, {
        text: lang('DateCreated'),
        flex: 1,
        dataIndex: 'DateCreated',
    }],
    viewConfig: {
        deferEmptyText: false,
        emptyText: lang('No data Available')
    },
    listeners: {
        beforechange: function() {}
    },
    dockedItems: [  
	{
        xtype: 'pagingtoolbar',
        store: storeGridActivityNC,
        dock: 'bottom',
        beforePageText: '',
        hiddenInputItem: true,
        afterPageText: '',
        displayInfo: false,
        listeners: {
            beforechange: function() {
                var ActivityID = Ext.getCmp('Koltiva.view.IMS.WinFormCoachActivity-Form-ActivityID').getValue();
                storeGridActivityNC.setStoreVar({
                    ActivityID: ActivityID
                });
                storeGridActivityNC.load();
            }
        }
    } ],
};
Ext.define('Koltiva.view.IMS.WinFormCoachActivityViewOnly', {
    extend: 'Ext.window.Window',
    id: 'Koltiva.view.IMS.WinFormCoachActivityViewOnly',
    title: lang('Coaching Activity Form'),
    closable: true,
    modal: true,
    closeAction: 'destroy',
    width: '90%',
    height: '90%',
    overflowY: 'auto',
    viewVar: false,
    initComponent: function() {
        var thisObj = this;
        //var storeGridMain = Ext.create('Koltiva.store.application_form.GridMain');
        thisObj.items = [{
            xtype: 'form',
            id: 'Koltiva.view.IMS.WinFormCoachActivity-Form',
            padding: '5 25 5 8',
            items:
                //START
                [{
                    xtype: 'tabpanel',
                    flex: 1,
                    margin: 2,
                    activeTab: 0,
                    plain: true,
                    items: [{
                        xtype: 'panel',
                        title: lang('Coaching Activity Data'),
                        items: [{
                            layout: 'column',
                            border: false,
                            items: [
                                //COLOUMN BASIC DATA START
                                {
                                    columnWidth: '.5',
                                    padding: '5 25 5 8',
                                    layout: 'form',
                                    items: [{
                                        xtype: 'hiddenfield',
                                        id: 'Koltiva.view.IMS.WinFormCoachActivity-Form-ActivityID',
                                        name: 'Koltiva.view.IMS.WinFormCoachActivity-Form-ActivityID'
                                    }, {
                                        xtype: 'textfield',
                                        fieldLabel: lang('Farmer ID'),
                                        labelWidth: 200,
                                        readOnly: true,
                                        id: 'Koltiva.view.IMS.WinFormCoachActivity-Form-FarmerID',
                                        name: 'Koltiva.view.IMS.WinFormCoachActivity-Form-FarmerID'
                                    }, {
                                        xtype: 'textfield',
                                        fieldLabel: lang('Farmer Name'),
                                        labelWidth: 200,
                                        id: 'Koltiva.view.IMS.WinFormCoachActivity-Form-FarmerName',
                                        name: 'Koltiva.view.IMS.WinFormCoachActivity-Form-FarmerName',
                                        allowBlank: false
                                    }, {
                                        xtype: 'datefield',
                                        fieldLabel: lang('Date Collection'),
                                        labelWidth: 200,
                                        format: 'Y-m-d h:i:s',
                                        id: 'Koltiva.view.IMS.WinFormCoachActivity-Form-EventDate',
                                        name: 'Koltiva.view.IMS.WinFormCoachActivity-Form-EventDate',
                                        allowBlank: false
                                    }, {
                                        xtype: 'textfield',
                                        fieldLabel: lang('Farmer in Place'),
                                        labelWidth: 200,
                                        id: 'Koltiva.view.IMS.WinFormCoachActivity-Form-FarmerInPlace',
                                        name: 'Koltiva.view.IMS.WinFormCoachActivity-Form-FarmerInPlace',
                                        allowBlank: false
                                    }, {
                                        xtype: 'textfield',
                                        fieldLabel: lang('Reason'),
                                        labelWidth: 200,
                                        id: 'Koltiva.view.IMS.WinFormCoachActivity-Form-Reason',
                                        name: 'Koltiva.view.IMS.WinFormCoachActivity-Form-Reason',
                                        allowBlank: false
                                    }]
                                    //COLOUMN BASIC DATA END
                                }, {
                                    //COLOUMN BASIC DATA START
                                    columnWidth: '.5',
                                    layout: 'form',
                                    items: [{
                                        xtype: 'textfield',
                                        fieldLabel: lang('Time Start'),
                                        labelWidth: 200,
                                        id: 'Koltiva.view.IMS.WinFormCoachActivity-Form-TimeStart',
                                        name: 'Koltiva.view.IMS.WinFormCoachActivity-Form-TimeStart',
                                        allowBlank: true
                                    },{
                                        xtype: 'textfield',
                                        fieldLabel: lang('Time End'),
                                        labelWidth: 200,
                                        id: 'Koltiva.view.IMS.WinFormCoachActivity-Form-TimeEnd',
                                        name: 'Koltiva.view.IMS.WinFormCoachActivity-Form-TimeEnd',
                                        allowBlank: true
                                    },{
                                        xtype: 'textarea',
                                        fieldLabel: lang('Comment'),
                                        labelWidth: 200,
                                        id: 'Koltiva.view.IMS.WinFormCoachActivity-Form-Comment',
                                        name: 'Koltiva.view.IMS.WinFormCoachActivity-Form-Comment',
                                        allowBlank: true
                                    }]
                                    //COLOUMN BASIC DATA END
                                }
                            ] ///end
                        }]
                    }]
                },{
                    layout: 'column',
                    border: false,
                    items: [
                        //COLOUMN BASIC DATA START
                        {
                            columnWidth: '.5',
                            padding: '5 25 5 8',
                            layout: 'form',
                            items: [{
                                xtype: 'panel',
                                title: lang('Image'),
                                frame: false,
                                id: 'Koltiva.view.IMS.WinFormCoachActivity-Form-SectionImage',
                                cls: 'Sfr_PanelSubLayoutFormRoundedGray',
                                items: [{
                                    layout: 'column',
                                    border: false,
                                    items: [
                                        {
                                            columnWidth: '.5',
                                            layout: 'form',
                                            style: 'padding:5px 10px 5px 5px;',
                                            items: [{
                                                id: 'Koltiva.view.IMS.WinFormCoachActivity-Form-Photo',
                                                html: '<img src="' + m_api_base_url + '/images/user.png" style="height:250px;display: block;margin-left: auto;margin-right: auto;" />'
                                            }]
                                        },
                                        {
                                            columnWidth: '.5',
                                            layout: 'form',
                                            style: 'padding:5px 10px 5px 5px;',
                                            items: [{
                                                id: 'Koltiva.view.IMS.WinFormCoachActivity-Form-Signature',
                                                html: ''
                                            }]
                                        }
                                    ]
                                }]
                            }]
                        },
                        {
                            columnWidth: '.5',
                            padding: '5 25 5 8',
                            layout: 'form',
                            items: [{
                                xtype: 'panel',
                                title: lang('Location'),
                                frame: false,
                                id: 'Koltiva.view.IMS.WinFormCoachActivity-Form-SectionLocation',
                                cls: 'Sfr_PanelSubLayoutFormRoundedGray',
                                items: [{
                                    columnWidth: 1,
                                    layout:'form',
                                    items:[{
                                        xtype: 'component',
                                        autoEl: {
                                            html: '<div id="map-polygon" style="width:100%;height:300px;background:#e1e1e1;border:1px solid #e1e1e1;"></div>',
                                            style:'width:100%;'
                                        }
                                    }
                                    ]
                                }],
                                listeners: {
                                    afterRender: function(){
                                        thisObj.initmap();
                                    }
                                }
                            }]
                        }
                    ]
                },{
                    xtype: 'tabpanel',
                    id: 'tabpanel_activity_nc',
                    flex: 1,
                    margin: 2,
                    activeTab: 0,
                    plain: true,
                    items: [{
                        xtype: 'panel',
                        title: lang('Coaching Activity NC'),
                        items: [Grid_Participant_history]
                    }]
                }] //END
        }];
        thisObj.buttons = [{
                text: lang('Close'),
                margin: '5px',
                icon: varjs.config.base_url + 'images/icons/new/close.png',
                cls: 'Sfr_BtnFormGrey',
                overCls: 'Sfr_BtnFormGrey-Hover',
                handler: function () {
                    //tutup popup
                    thisObj.close();
                }
            }];
        this.callParent(arguments);
    },
    initmap: function(){
        
        lat  = '-6.406619';
        long = '106.861656';
        var thisObj = this;
        var zoom   = 8;
        var centermap = [lat, long];

        $('#map-polygon').gmap3({
            map: {
                options: {
                    center: centermap,
                    zoom: zoom,
                    //mapTypeControl: false,
                    panControl: true,
                    zoomControl: true,
                    //scaleControl: false,
                    streetViewControl: false,
                    rotateControl: false,
                    rotateControlOptions: false,
                    overviewMapControl: false,
                    OverviewMapControlOptions: false,
                    scrollwheel: true
                }
            }
        });
    },
    listeners: {
        afterRender: function() {
            var thisObj = this;
            if (thisObj.viewVar.opsiDisplay == 'update' || thisObj.viewVar.opsiDisplay == 'view') {
                getDisabledInputBasicForm()
                var formNya = Ext.getCmp('Koltiva.view.IMS.WinFormCoachActivity-Form').getForm();
                Ext.Ajax.request({
                    url: m_api + '/ims/getCoachingActivitybyID',
                    method: 'GET',
                    params: {
                        ActivityID: thisObj.viewVar.ActivityID
                    },
                    success: function(fp, o) {
                        var r = Ext.decode(fp.responseText);
                        Ext.getCmp('Koltiva.view.IMS.WinFormCoachActivity-Form-ActivityID').setValue(r.data[0].ActivityID);
                        Ext.getCmp('Koltiva.view.IMS.WinFormCoachActivity-Form-FarmerID').setValue(r.data[0].FarmerID);
                        Ext.getCmp('Koltiva.view.IMS.WinFormCoachActivity-Form-FarmerName').setValue(r.data[0].FarmerName);
                        Ext.getCmp('Koltiva.view.IMS.WinFormCoachActivity-Form-EventDate').setValue(r.data[0].EventDate);
                        Ext.getCmp('Koltiva.view.IMS.WinFormCoachActivity-Form-TimeStart').setValue(r.data[0].TimeStart);
                        Ext.getCmp('Koltiva.view.IMS.WinFormCoachActivity-Form-TimeEnd').setValue(r.data[0].TimeEnd);
                        Ext.getCmp('Koltiva.view.IMS.WinFormCoachActivity-Form-Comment').setValue(r.data[0].Comment);
                        Ext.getCmp('Koltiva.view.IMS.WinFormCoachActivity-Form-FarmerInPlace').setValue(r.data[0].FarmerInPlace);
                        Ext.getCmp('Koltiva.view.IMS.WinFormCoachActivity-Form-Reason').setValue(r.data[0].Reason);
                        //set photo
                        if(r.data[0].PhotoActPath != null) {
                            Ext.getCmp('Koltiva.view.IMS.WinFormCoachActivity-Form-Photo').update('<img src="' + r.data[0].PhotoActPath + '" style="height:250px;display: block;margin-left: auto;margin-right: auto;" /></a>');
                        } else {
                            Ext.getCmp('Koltiva.view.IMS.WinFormCoachActivity-Form-Photo').update('<img src="' + m_api_base_url + '/images/user.png" style="height:250px;display: block;margin-left: auto;margin-right: auto;" />');
                        }
                        //set signature
                        if(r.data[0].FarmerSigActPath != null) {
                            Ext.getCmp('Koltiva.view.IMS.WinFormCoachActivity-Form-Signature').update('<img src="' + r.data[0].FarmerSigActPath + '" style="height:250px;display: block;margin-left: auto;margin-right: auto;" /></a>');
                        } else {
                            Ext.getCmp('Koltiva.view.IMS.WinFormCoachActivity-Form-Signature').update('');
                        }
                        storeGridActivityNC.setStoreVar({
                            ActivityID: r.data.ActivityID
                        });
                        storeGridActivityNC.load();
                        var center = [r.data[0].Latitude, r.data[0].Longitude];
                        var zoom   = 10;

                        $('#map-polygon').gmap3({
                            marker:{
                                latLng: [r.data[0].Latitude, r.data[0].Longitude]
                            },
                            map: {
                                options: {
                                    center: center,
                                    zoom: zoom,
                                    //mapTypeControl: false,
                                    panControl: true,
                                    zoomControl: true,
                                    //scaleControl: false,
                                    streetViewControl: false,
                                    rotateControl: false,
                                    rotateControlOptions: false,
                                    overviewMapControl: false,
                                    OverviewMapControlOptions: false,
                                    scrollwheel: true
                                }
                            }
                        });
                    }
                });

            }
        }
    }
});

function getDisabledInputBasicForm() {
    Ext.getCmp('Koltiva.view.IMS.WinFormCoachActivity-Form-ActivityID').setReadOnly(true);
    Ext.getCmp('Koltiva.view.IMS.WinFormCoachActivity-Form-FarmerID').setReadOnly(true);
    Ext.getCmp('Koltiva.view.IMS.WinFormCoachActivity-Form-FarmerName').setReadOnly(true);
    Ext.getCmp('Koltiva.view.IMS.WinFormCoachActivity-Form-EventDate').setReadOnly(true);
    Ext.getCmp('Koltiva.view.IMS.WinFormCoachActivity-Form-TimeStart').setReadOnly(true);
    Ext.getCmp('Koltiva.view.IMS.WinFormCoachActivity-Form-TimeEnd').setReadOnly(true);
    Ext.getCmp('Koltiva.view.IMS.WinFormCoachActivity-Form-Comment').setReadOnly(true);
    Ext.getCmp('Koltiva.view.IMS.WinFormCoachActivity-Form-Reason').setReadOnly(true);
    Ext.getCmp('Koltiva.view.IMS.WinFormCoachActivity-Form-FarmerInPlace').setReadOnly(true);
}