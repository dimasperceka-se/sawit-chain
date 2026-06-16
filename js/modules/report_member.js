Ext.onReady(function(){
	
	Ext.tip.QuickTipManager.init();

	var memberStatus = Ext.create('Ext.data.Store', {
		fields: ['val', 'label'],
		data : [
			{'val':'1', 'label':'Active'},
			{'val':'2', 'label':'Inactive'},
			{'val':'3', 'label':'Suspended'},
			{'val':'4', 'label':'Candidate'},
		]
	});

    var districts = Ext.create('Ext.data.Store', {
        fields: ['DistrictID', 'District'],
        proxy: {
            type: 'rest',
            url: m_rpt + '/combo_district', // url that will load data with respect to start and limit params
            reader: {
                type: 'json',
                root: 'data',
                totalProperty: 'total',
                idProperty: 'DistrictID'
            },
            writer: {
                type: 'json'
            },
            appendId: true
        }
    });

    var subdistricts = Ext.create('Ext.data.Store', {
        fields: ['SubDistrictID', 'SubDistrict'],
        proxy: {
            type: 'rest',
            url: m_rpt + '/combo_subdistrict', // url that will load data with respect to start and limit params
            reader: {
                type: 'json',
                root: 'data',
                totalProperty: 'total',
                idProperty: 'SubDistrictID'
            },
            writer: {
                type: 'json'
            },
            appendId: true
        }
    });

    var villages = Ext.create('Ext.data.Store', {
        fields: ['VillageID', 'Village'],
        proxy: {
            type: 'rest',
            url: m_rpt + '/combo_village', // url that will load data with respect to start and limit params
            reader: {
                type: 'json',
                root: 'data',
                totalProperty: 'total',
                idProperty: 'VillageID'
            },
            writer: {
                type: 'json'
            },
            appendId: true
        }
    });


	var store = Ext.create('Ext.data.Store',{
		storeId: 'reportMemberStore',
        autoLoad:true,
        fields: ['id', 'primaryNo', 'name', 'GroupName', 'Village', 'saldoSimpok', 'saldoWajib', 'uangPangkal', 'registeredDate', 'status'],
        proxy: {
        	type: 'rest',
        	url: m_rpt + '/reportmember', // url that will load data with respect to start and limit params
            reader: {
                type: 'json',
                root: 'data',
                totalProperty: 'total',
                idProperty: 'id'
            },
            writer: {
                type: 'json'
            },
            appendId: true
        }
	});

	var filterPanel = Ext.create('Ext.panel.Panel', {
        width: '100%',
        title: 'Filter',
        id: 'gridFilterPanel',
        renderTo: 'ext-content',
        style:'border: 1px solid #CCCCCC',
        layout: { type: 'table', columns: 6},
        items: [
            {
                xtype: 'textfield',
                id: 'filterMemberName',
                emptyText: lang('Nama anggota'),
                padding: '10px 11px 0',
            },{
                xtype: 'textfield',
                id: 'filterPrimaryNo',
                emptyText: lang('No anggota'),
                padding: '10px 11px 0',
            },{
                xtype: 'combobox',
                id: 'filterMemberStatus',
                emptyText: lang('Status anggota'),
                store: memberStatus,
                queryMode: 'local',
                valueField: 'val',
                displayField: 'label',
                padding: '10px 11px 0',
            },{
                xtype:'datefield',
                id:'filterMemberRegDate',
                emptyText: lang('Tgl daftar'),
                format: 'Y-m-d',
                padding: '10px 11px 0',
            },{
                colspan: 1,
                items: [
                    {
                        xtype: 'button',
                        text: lang('Apply'),
                        margin: '20px 0px 20px 11px',
                        handler: function(){
                            var sMemberName = Ext.getCmp('filterMemberName').getValue();
                            var sPrimaryNo = Ext.getCmp('filterPrimaryNo').getValue();
                            var sMemberStatus = Ext.getCmp('filterMemberStatus').getValue();
                            var sMemberRegDate = Ext.getCmp('filterMemberRegDate').getRawValue();
                            var sDistrict = Ext.getCmp('filterDistrict').getValue();
                            var sSubDistrict = Ext.getCmp('filterSubDistrict').getValue();
                            var sVillage = Ext.getCmp('filterVillage').getValue();

                            grid.store.load({
                                params:{
                                    memberName : sMemberName,
                                    primaryNo : sPrimaryNo,
                                    memberStatus : sMemberStatus,
                                    memberRegDate : sMemberRegDate,
                                    district : sDistrict,
                                    subDistrict : sSubDistrict,
                                    village : sVillage
                                }
                            });
                        }
                    },{
                        xtype: 'button',
                        text: lang('Reset'),
                        margin: '20px 0px 20px 6px',
                        handler: function(){
                            
                            grid.store.load();
                        }
                    }
                ]
            },{
                xtype: 'button',
                text: lang('Export to xls'),
                ui: 's-button',
                scale: 'medium',
                margin: '20px 0px 20px 11px',
                handler: function(){
                    alert('Xport 2 .xls');
                }
            },{
                xtype: 'combobox',
                id: 'filterDistrict',
                emptyText: lang('District'),
                padding: '0 11px 20px 11px',
                store: districts,
                valueField: 'DistrictID',
                displayField: 'District',
                listeners:{
                    'select' : function(el){
                        var slcval = el.value;
                        var sdist = Ext.getCmp('filterSubDistrict');
                        var vil = Ext.getCmp('filterVillage');

                        sdist.reset(); //reset 1st
                        vil.reset(); //reset 1st
                        sdist.store.load({
                            params:{ DistrictID : slcval }
                        });

                        vil.disable();
                        sdist.enable(); //enable
                    }
                }
            },{
                xtype: 'combobox',
                id: 'filterSubDistrict',
                disabled: true,
                padding: '0 11px 20px 11px',
                emptyText: lang('Subdistrict'),
                store: subdistricts,
                queryMode: 'local',
                valueField: 'SubDistrictID',
                displayField: 'SubDistrict',
                listeners:{
                    'select' : function(el){
                        var slcval = el.value;
                        var vil = Ext.getCmp('filterVillage');

                        vil.reset(); //reset 1st
                        vil.store.load({
                            params:{ SubDistrictID : slcval }
                        });

                        vil.enable(); //enable
                    }
                }
            },{
                xtype: 'combobox',
                id: 'filterVillage',
                disabled: true,
                padding: '0 11px 20px 11px',
                emptyText: lang('Village'),
                store: villages,
                queryMode: 'local',
                valueField: 'VillageID',
                displayField: 'Village',
            }
        ]
    });

	var grid = Ext.create('Ext.grid.Panel', {
        width: '100%',
        id: 'gridReportMember',
        loadMask: true,
        style:'border: 1px solid #CCCCCC',
        store: Ext.data.StoreManager.lookup('reportMemberStore'),
        columns: [
        	{
                text: 'ID',
                dataIndex: 'id',
                hidden: true
            },{
                text: 'No',
                xtype: 'rownumberer',
                width: '3%'
            },{
                text: lang('No. Anggota'),
                width: 170,
                dataIndex: 'primaryNo'
            },{
                text: lang('Nama'),
                width: 200,
                flex:1,
                dataIndex: 'name'
            },{
                text: lang('Kelompok'),
                width: 200,
                flex:1,
                dataIndex: 'GroupName'
            }, {
                text: lang('Desa'),
                dataIndex: 'Village'
            },{
                xtype:'numbercolumn',
                format: '0,000',
                align:'right',
                text: 'Simpanan Pokok',
               	width: 130,
                dataIndex: 'saldoSimpok'
            },{
                xtype:'numbercolumn',
                format: '0,000',
                align:'right',
                text: 'Simpanan Wajib',
                width: 130,
                dataIndex: 'saldoWajib'
            },{
                xtype:'numbercolumn',
                format: '0,000',
                align:'right',
               	text: 'Uang Pangkal',
                width: 130,
                dataIndex: 'uangPangkal'
            },{
                text: lang('Tgl. Daftar'),
                dataIndex: 'registeredDate',
                flex: 1,
            },{
                text: 'Status',
                dataIndex: 'status',
                width: 100,
                renderer: function(value) {
                    if (value == '1') {
                        return lang('Active');
                    } else if (value == '2') {
                        return lang('Inactive');
                    } else if (value == '3') {
                        return lang('Suspended');
                    } else if (value == '4') {
                        return lang('Candidate');
                    }
                }
            },
        ],
        height: 590,
        renderTo: 'ext-content',
        dockedItems:[
        	{
                xtype: 'pagingtoolbar',
                dock: 'bottom',
                store: store,
                displayInfo: true,
                displayMsg: 'Displaying item(s) {0} - {1} of {2}',
                emptyMsg: "No item(s) to display"
            },
        ]
    });

}); // end of onReady