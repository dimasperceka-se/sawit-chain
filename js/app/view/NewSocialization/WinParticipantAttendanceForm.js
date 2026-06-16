var objPanelAttandance = Ext.create('Ext.panel.Panel',{ 
            frame: false, 
            items: [] 
		});
		
Ext.define('Koltiva.view.NewSocialization.WinParticipantAttendanceForm' ,{
    extend: 'Ext.window.Window',
    id: 'Koltiva.view.NewSocialization.WinParticipantAttendanceForm',
    title: 'Add Attendance',
    closable: true,
    modal: true,
    closeAction: 'destroy',
    width: '70%', 
    overflowY: 'auto',
    viewVar: false,	
    initComponent: function() {
        var thisObj = this;
        thisObj.storeGridParticipant = Ext.create('Koltiva.store.NewSocialization.GridAttandance'); 
		thisObj.ComboHariEvent = Ext.create('Koltiva.store.NewSocialization.ComboHariEvent');
		thisObj.items = [{
                xtype: 'form', 
                id: 'Koltiva.view.NewSocialization.WinParticipantAttendanceForm-Attandance',
                items:[{
						layout: 'column',
						border: false,
						items:[
							//COLOUMN BASIC DATA START
								{	
									columnWidth: '.5',
									padding:'5 25 5 8',
									layout:'form', 
									items:[{
												xtype: 'textfield',
												fieldLabel : lang('nama acara'),
												name: 'Koltiva.view.NewSocialization.WinParticipantAttendanceForm-Form-EventName',
												id: 'Koltiva.view.NewSocialization.WinParticipantAttendanceForm-Form-EventName', 
												width: 250,
												readOnly: true							
											},
											{
												xtype: 'datefield',
												fieldLabel: 'Start', 
												format:'Y-m-d',
												id: 'Koltiva.view.NewSocialization.WinParticipantAttendanceForm-Form-EventStart',
												name: 'Koltiva.view.NewSocialization.WinParticipantAttendanceForm-Form-EventStart',
												readOnly: true
											},
											{
												xtype: 'datefield',
												fieldLabel: 'End', 
												format:'Y-m-d', 
												id: 'Koltiva.view.NewSocialization.WinParticipantAttendanceForm-Form-EventEnd',
												name: 'Koltiva.view.NewSocialization.WinParticipantAttendanceForm-Form-EventEnd',
												readOnly: true 
											}]
									//COLOUMN BASIC DATA END
								},
								{
								//COLOUMN BASIC DATA START
								columnWidth: '.5',
								layout:'form',
								items:[ 
										{
										xtype: 'combobox',
										fieldLabel : lang('Days'), 
										store : thisObj.ComboHariEvent,
										displayField: 'hari',
										valueField: 'hari',
										triggerAction : 'all', 
										queryMode: 'local',
										name: 'Koltiva.view.NewSocialization.WinParticipantAttendanceForm-Form-EventDays',
										id: 'Koltiva.view.NewSocialization.WinParticipantAttendanceForm-Form-EventDays',  
										readOnly: false,
										allowBlank : false,
										listeners : {
										change : function(a,v)
											{
												var dateString = Ext.getCmp('Koltiva.view.NewSocialization.WinParticipantAttendanceForm-Form-EventStart').getValue();
												var tommorow = dateString;
												if(parseInt(a.getValue()) > 1){
													var ints = parseInt(a.getValue()) - 1;
													tommorow = new Date(new Date(dateString).setDate(new Date(dateString).getDate() + ints ));
												} 
												Ext.getCmp('Koltiva.view.NewSocialization.WinParticipantAttendanceForm-Form-EventDate').setValue(tommorow); 
												var IMSSocID = Ext.getCmp('Koltiva.view.NewSocialization.MainForm-Form-IMSSocID').getValue();
												var EventDate = Ext.getCmp('Koltiva.view.NewSocialization.WinParticipantAttendanceForm-Form-EventDate').getValue()
												Ext.Ajax.request({
													url: m_api + '/new_socialization/save_participant_to_attadance',
													method: 'POST',
													waitMsg: lang('Sending data...'),
													params: {
														'IMSSocID'		:IMSSocID, 
														'DayNumber'		:a.getValue(),
														'EventDate' 	:EventDate
													},
													success: function(response, opts) {
														var obj = Ext.decode(response.responseText);
														thisObj.storeGridParticipant.setStoreVar({ 
															DayNumber : a.getValue(),
															IMSSocID: IMSSocID
														});
														thisObj.storeGridParticipant.load();
													}
												});
												
											}
										}
										},
										{
											xtype: 'datefield',
											fieldLabel: 'Event Date', 
											format:'Y-m-d', 
											id: 'Koltiva.view.NewSocialization.WinParticipantAttendanceForm-Form-EventDate', 
											name: 'Koltiva.view.NewSocialization.WinParticipantAttendanceForm-Form-EventDate',
											readOnly: true
										} ]
								//COLOUMN BASIC DATA END				
								}	
							]
						}]
				}, 
				{
                xtype: 'gridpanel',
                id: 'Koltiva.view.NewSocialization.WinParticipantAttendanceForm-Form-grid_participant_add', 
                store: thisObj.storeGridParticipant,
                loadMask: true, 
                dockedItems: [ 
                    {
                        xtype: 'pagingtoolbar',
                        store: thisObj.storeGridParticipant,
                        dock: 'top',
                        displayInfo: true
                    }
                    ], 
                columns: [
					{
                        text: '#',
                        xtype: 'rownumberer',
                        width: 50,
                    },
					
					{ 
						text:  lang('Participant ID'),
						dataIndex: 'participantID', 
						width: 150 
                    },
                    {
                        text:  lang('Participant Name (Farmer)'),
                        dataIndex: 'Fullname',
                        flex: 2 
                    },  
					{
                        text:  lang('Farmer Group'),
                        dataIndex: 'GroupName',
                        flex: 2 
                    }, 
					{
                        text:  lang('Participant Name (Farmer)'),
                        dataIndex: 'Fullname',
                        flex: 2 
                    }, 
					{
                        text: lang('Kehadiran'),
                        dataIndex: 'checkbox_status',
						xtype: 'checkcolumn', 
						editor: {
							xtype: 'checkbox',
							cls: 'x-grid-checkheader-editor',
							inputValue: true,
							uncheckedValue: false
						},
						listeners: { 
							checkchange: function (column, recordIndex, checked) {
							    var record = this.up('grid').getStore().getAt(recordIndex);
								var Reschecked = ''; 
								if(checked == true ){ Reschecked = 1; } else{ Reschecked = 2;}
								Ext.Ajax.request({
									url: m_api + '/new_socialization/save_attandance_participant',
									method: 'POST',
									waitMsg: lang('Sending data...'),
									params: {
										'IMSSocID'		: Ext.getCmp('Koltiva.view.NewSocialization.MainForm-Form-IMSSocID').getValue(),
										'ApplicantID'	: record.data.ApplicantID,
										'MobileUID'		: record.data.MobileUID,
										'checked'		: Reschecked,
										'DayNumber'		: Ext.getCmp('Koltiva.view.NewSocialization.WinParticipantAttendanceForm-Form-EventDays').getValue(),
										'EventDate' 	: Ext.getCmp('Koltiva.view.NewSocialization.WinParticipantAttendanceForm-Form-EventDate').getValue()
									},
									success: function(response, opts) {
										var obj = Ext.decode(response.responseText);
										switch (obj.success) {
											case true:
												Ext.getCmp('Koltiva.view.NewSocialization.GridParticipant-FormDetail').store.reload({ params : { IMSSocID: IMSSocID } });  
												break;
											default:
												Ext.MessageBox.alert(lang('Warning'), obj.message);
												break;
										}
									}
								});
							}
						}
						
                    } 
                ]
            }];
		
		thisObj.buttons = [{
	            text: lang('Close'),
	            margin: '5px',
	            scale: 'large',
	            ui: 's-button',
	            cls: 's-grey',
	            handler: function() {
	                thisObj.close();
					var IMSSocID = Ext.getCmp('Koltiva.view.NewSocialization.MainForm-Form-IMSSocID').getValue();  
					Ext.getCmp('Koltiva.view.NewSocialization.GridParticipant-FormDetail').store.reload({ params : { IMSSocID: IMSSocID } });  
	            }
        }];
		
		this.callParent(arguments);
	},
	listeners: {
        afterRender: function(){
			var thisObj = this;  
			thisObj.ComboHariEvent.load({params:{IMSSocID: thisObj.viewVar.IMSSocID}}); 
			Ext.Ajax.request({
				url: m_api + '/new_socialization/getDataSocialization',
				method: 'GET',
				params: {
					IMSSocID: thisObj.viewVar.IMSSocID
				},
				success: function (fp, o) {
					var r = Ext.decode(fp.responseText);   
					Ext.getCmp('Koltiva.view.NewSocialization.WinParticipantAttendanceForm-Form-EventName').setValue(r.data.EventName);
					Ext.getCmp('Koltiva.view.NewSocialization.WinParticipantAttendanceForm-Form-EventStart').setValue(r.data.EventStart);
					Ext.getCmp('Koltiva.view.NewSocialization.WinParticipantAttendanceForm-Form-EventEnd').setValue(r.data.EventEnd); 
				}
			});	
		}
	}
	
});


