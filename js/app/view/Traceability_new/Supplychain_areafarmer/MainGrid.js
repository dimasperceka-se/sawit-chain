


function submitOnEnterGridGrower(field, event) {
    if (event.getKey() == event.ENTER) {
        setFilterLs();
        // if (field.value.length >= 3) {
            Ext.getCmp('Koltiva.view.Traceability_new.Supplychain_areafarmer.MainGrid-gridMainGrid').getStore().loadPage(1);
        // }
    }
}

function setFilterLs() {
    localStorage.setItem('patchouli_grower_ls', JSON.stringify({
        ptextSearch: Ext.getCmp('view.Grower.GridMainGrower-textSearch').getValue()
    }));
}


Ext.define('Koltiva.view.Traceability_new.Supplychain_areafarmer.MainGrid' ,{
    extend: 'Ext.panel.Panel',
    id: 'Koltiva.view.Traceability_new.Supplychain_areafarmer.MainGrid',  
    initComponent: function() {
        var thisObj = this;
        //store   
		var storeGridMainAreaDistrict = Ext.create('Koltiva.store.Traceability_new.Reference.Supplychain_areafarmer.MainGrid');
        //items
         
		var contextMenuAreaFarmGrid = Ext.create('Ext.menu.Menu',{
			cls:'Sfr_ConMenu',
            items:[
            {
                icon: varjs.config.base_url + 'images/icons/new/update.png',
                text: lang('Update Date'),
                cls:'Sfr_BtnConMenuWhite', 
                handler: function(){
                    var sm = Ext.getCmp('Koltiva.view.Traceability_new.Supplychain_areafarmer.MainGrid-gridMainGrid').getSelectionModel().getSelection()[0];

                    var SupplychainFarmerID = sm.get('SupplychainFarmerID');
                    var WinupdateFarmer = Ext.create('Koltiva.view.Traceability_new.Supplychain_areafarmer.WinupdateFarmer',{
                        viewVar: {
                            SupplychainFarmerID: SupplychainFarmerID, 
                        }
                    }); 
                    
                    if (!WinupdateFarmer.isVisible()) {
                        WinupdateFarmer.center();
                        WinupdateFarmer.show();
                    } else {
                        WinupdateFarmer.close();
                    }
                }
            },
			{
	            icon: varjs.config.base_url + 'images/icons/new/delete.png',
                text: lang('Delete'),
                cls:'Sfr_BtnConMenuWhite', 
	            handler: function(){
                    var sm = Ext.getCmp('Koltiva.view.Traceability_new.Supplychain_areafarmer.MainGrid-gridMainGrid').getSelectionModel().getSelection()[0];
                    Ext.Ajax.request({
                        waitMsg: lang('Please Wait'),
                        url:  m_api + '/traceability_api/Supplychain_areafarmer/del',
                        method : 'POST',
                        params: {
                            SupplychainFarmerID: sm.get('SupplychainFarmerID')
                        },
                        success: function(response, opts){
                            var obj = Ext.decode(response.responseText);  
                            Ext.getCmp('Koltiva.view.Traceability_new.Supplychain_areafarmer.MainGrid-gridMainGrid').getStore().load();
                        }
                    });
				}
			}]
        });

		var generated	= true;
		if(m_daerah_access.includes("73") || m_daerah_access.includes("61")){
			generated	= false;
		}else if(m_daerah_access.includes("43") || m_daerah_access.includes("44")){
			generated	= true;
		}
        
        thisObj.items = [{
            xtype: 'grid',
            id: 'Koltiva.view.Traceability_new.Supplychain_areafarmer.MainGrid-gridMainGrid',
            style: 'border:1px solid #CCC;margin-top:4px;',
            loadMask: true,
            selType: 'rowmodel',
            store: storeGridMainAreaDistrict,
            width: '100%',
            minHeight:400,
            viewConfig: {
                deferEmptyText: false,
                emptyText: lang('No data Available'),
            }, 
            dockedItems: [{
                xtype: 'pagingtoolbar',
                id: 'Koltiva.view.Traceability_new.Supplychain_areafarmer.MainGrid-gridToolbar',
                store: storeGridMainAreaDistrict,
                dock: 'bottom',
                displayInfo: true
            },{
                xtype: 'toolbar',
                dock:'top',
                items: [{
                    icon: varjs.config.base_url + 'images/icons/new/add.png',
                    text: lang('Add'),
                    scope: this,
                    cls:'Sfr_BtnGridGreen',
					overCls:'Sfr_BtnGridGreen-Hover',
					id :'Koltiva.view.Traceability_new.Supplychain_areafarmer.MainGrid-gridMainGrid-Btn',
                    handler: function () { 
                        var Role = Ext.getCmp('Koltiva.view.Traceability_new.Reference.Supplychain_org-dataForm-ObjType').getValue();
                        var WinListFarmer = Ext.create('Koltiva.view.Traceability_new.Supplychain_areafarmer.WinpilihanFarmer',{
                        viewVar: {
                            role: Role, 
                        }
                        }); 
                        
                        if (!WinListFarmer.isVisible()) {
                            WinListFarmer.center();
                            WinListFarmer.show();
                            Ext.getCmp('Koltiva.view.Traceability_new.Supplychain_areafarmer.WinpilihanFarmer-grid').getStore().load();
                        } else {
                            WinListFarmer.close();
                        }
                    }
                },{
                    icon: varjs.config.base_url + 'images/icons/new/reload.png',
                    text: lang('Generate'),
                    scope: this,
                    cls:'Sfr_BtnGridGreen',
                    overCls:'Sfr_BtnGridGreen-Hover',
                    hidden: generated,
					id :'Koltiva.view.Traceability_new.Supplychain_areafarmer.MainGrid-gridMainGrid-BtnGemerate',
                    handler: function () {

                        Ext.MessageBox.confirm('Message', 'Generating Data ?', function(btn) {
                            if (btn == 'yes') {
                                Ext.Ajax.request({
                                    waitMsg: 'Please Wait',
                                    url: m_api + '/traceability_api/Supplychain_areafarmer/generate_farmer_access',
                                    method: 'POST',
                                    params: {
                                        SupplychainID : Ext.getCmp('Koltiva.view.Traceability_new.Reference.Supplychain_org-dataForm-SupplychainID').getValue()
                                    },
                                    success: function(response, o) {
                                        var obj = Ext.JSON.decode(response.responseText);
                                        console.log(obj.code);
                                        if(obj.code == "400"){
                                            Ext.MessageBox.show({
                                                title: 'Information',
                                                msg: obj.message,
                                                buttons: Ext.MessageBox.OK,
                                                animateTarget: 'mb9',
                                                icon: 'ext-mb-warning'
                                            });
                                            return false;
                                        }
                                        
                                        if(obj.code == 200){
                                            Ext.MessageBox.show({
                                                title: 'Information',
                                                msg: lang('Data Generated'),
                                                buttons: Ext.MessageBox.OK,
                                                animateTarget: 'mb9',
                                                icon: 'ext-mb-success'
                                            });
                                        }
                                        Ext.getCmp('Koltiva.view.Traceability_new.Supplychain_areafarmer.MainGrid-gridMainGrid').getStore().load();
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
                },{
                    name: 'key',
                    baseCls: 'Sfr_TxtfieldSearchGrid',
                    id: 'view.Grower.GridMainGrower-textSearch',
                    xtype: 'textfield',
                    width: 400,
                    emptyText: lang('Cari berdasar nama/ID') + ', ' + lang('Press \'Enter\' to search'),
                    minLength: 3,
                    msgTarget: 'side',
                    listeners: {
                        specialkey: submitOnEnterGridGrower,
                        // keyup: function (v) {
                        //     if (v.length < 3) {
                        //         this.addCls('error');
                        //     } else {
                        //         this.removeCls('error');
                        //     }
                        // }
                    }
                }]
            }],
            columns: [{
				text: lang('Action'),	
				xtype:'actioncolumn',
				width:'5%',
				items:[{
					icon: varjs.config.base_url + 'images/icons/new/action.png',
					handler: function(grid, rowIndex, colIndex, item, e, record) {
						if(Ext.getCmp('setVarParameters').getValue() != 'view'){
							contextMenuAreaFarmGrid.showAt(e.getXY());
						}
					 
					}
				}]
			},{
                text: lang('Farmer ID'),
                dataIndex: 'MemberDisplayID',
                width:'10%' 
            },
			{
                text: lang('Farmer Name'),
                dataIndex: 'MemberName',
                width:'15%' 
            },
			{
                text: lang('Village'),
                dataIndex: 'Desa',
                 flex:1, 
            },
			{
                text: lang('SubDistrict'),
                dataIndex: 'Kecamatan',
                flex:1,
            },
			{
                text: lang('District'),
                dataIndex: 'District',
                 flex:1,
            },
			{
                text: lang('Start Date'),
                dataIndex: 'DateStart',
                flex:1,
				format:'Y-m-d' 
            },
			{
                text: lang('End Date'),
                dataIndex: 'DateEnd',
                flex:1,
                format:'Y-m-d' 
            },
			{
                text: lang('Status'),
                dataIndex: 'Status'
            }],
            listeners: { 
				 
            }
        }];

        this.callParent(arguments);
    }
});
 
