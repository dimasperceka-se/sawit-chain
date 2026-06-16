Ext.define('Koltiva.view.NewSocialization.WinStaff' ,{
    extend: 'Ext.window.Window',
    id: 'Koltiva.view.NewSocialization.WinStaff',
    title: lang('Staff List'),
    closable: true,
    modal: true,
    closeAction: 'destroy',
    width: '60%',
	height: '80%', 	
    overflowY: 'auto',
    viewVar: false,	
    initComponent: function() {
        var thisObj = this;
        thisObj.store_Staff_add = Ext.create('Koltiva.store.NewSocialization.GridStaff'); 
		thisObj.items = [{
                xtype: 'gridpanel',
                id: 'Koltiva.view.NewSocialization.WinStaff-Form-grid_Staff_add', 
                store: thisObj.store_Staff_add,
                loadMask: true, 
                dockedItems: [
                        {
                            xtype: 'toolbar',
                            items: [ 
                            {
                                xtype: 'textfield',
                                name: 'keyAddPart',
                                id: 'keyAddPart',
                                emptyText: 'Cari berdasar nama/ID',
                                width: 150,
                                listeners: {}
                            }, {
                                xtype: 'button',
                                icon: varjs.config.base_url + 'images/icons/silk/search.png',
                                margin: '0px 0px 0px 6px',
                                text: lang('Search'),
                                handler: function() { 
									thisObj.store_Staff_add.load({
                                        params: { 
                                            key: Ext.getCmp('keyAddPart').getValue() 
                                        }
                                    });
                                }
                            }]
                    }, 
                    {
                        xtype: 'pagingtoolbar',
						id: 'Koltiva.view.Staff.RegisterStaff.MainGrid-gridToolbar',
                        store: thisObj.store_Staff_add,
                        dock: 'bottom',
                        displayInfo: true
                    }, 
					{
                        xtype: 'pagingtoolbar',
						id: 'Koltiva.view.Staff.RegisterStaff.MainGrid-gridToolbar',
                        store: thisObj.store_Staff_add,
                        dock: 'top',
                        displayInfo: true
                    }, 
                ],
                selModel: {
                    selType: 'checkboxmodel',
                    checkOnly: true,
                    multiSelect: true,
                    mode: "MULTI",
                    headerWidth: 50,                    
                },   
                columns: [
                    {
                        text: lang('Name'),
                        dataIndex: 'PersonNm',
                        flex: 2,
                    },
					
                ]
            }];
		
		
		thisObj.buttons = [{
            id: 'Koltiva.view.NewSocialization.WinExistingParticipants-Form-btnSave',
            text: lang('Save'),
            margin: '5px',
            ui: 's-button',
            scale: 'large',
            cls: 's-blue',
            handler: function() {
                let StaffID = '';
                let selection = Ext.getCmp('Koltiva.view.NewSocialization.WinStaff-Form-grid_Staff_add').getSelectionModel().getSelection();
                Ext.each(selection, function(row, index, value) {
                    StaffID = StaffID + ',' + row.data.StaffID;
                });
                    
                if (StaffID !== '') {
                    Ext.Ajax.request({
                        url: m_api + '/new_socialization/save_staff',
                        method: 'POST',
                        waitMsg: lang('Sending data...'),
                        params: {
                            IMSSocID: Ext.getCmp('Koltiva.view.NewSocialization.MainForm-Form-IMSSocID').getValue(),
                            StaffID: StaffID
                        },
                        success: function(response, opts) {
                            var obj = Ext.decode(response.responseText);
                            switch (obj.success) {
                                case true:
                                    Ext.MessageBox.alert('Message', lang(obj.message));
                                    var IMSSocID = Ext.getCmp('Koltiva.view.NewSocialization.MainForm-Form-IMSSocID').getValue();  
                                    Ext.getCmp('Koltiva.view.NewSocialization.GridMainStaff-FormDetail').store.reload({ params : { IMSSocID: IMSSocID } });
                                    thisObj.close(); 
                                    break;
                                default:
                                    Ext.MessageBox.alert('Warning', lang(obj.message));
                                    break;
                            }
                        }
                    });
                } else {
                    Ext.Msg.alert("Warning", "Please select staff");
                } 
            }
        },{
            text: lang('Close'),
            margin: '5px',
            scale: 'large',
            ui: 's-button',
            cls: 's-grey',
            handler: function() {
                //tutup popup
                thisObj.close(); 
				var IMSSocID = Ext.getCmp('Koltiva.view.NewSocialization.MainForm-Form-IMSSocID').getValue();  
				Ext.getCmp('Koltiva.view.NewSocialization.GridMainStaff-FormDetail').store.reload({ params : { IMSSocID: IMSSocID } }); 
            }
        }];
		
		this.callParent(arguments);
	},
	listeners: {
        afterRender: function(){
			var thisObj = this; 
			thisObj.store_Staff_add.setStoreVar({ 
				IMSSocID: thisObj.viewVar.IMSSocID
			});
			thisObj.store_Staff_add.load(); 
			
			
		}
	}
	
});

 