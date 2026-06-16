    if (Ext.getCmp('areawindow')) {Ext.getCmp('areawindow').destroy();};
    function isNumber(n) {
    	return !isNaN(parseFloat(n)) && isFinite(n);
    }

	var FarmerID,SurveyID,PartnerID;

    var store_partner = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'label'],
        proxy: {
            type: 'ajax',
            url: url_partner,
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });
    var store_CekSurvey = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id','surveya'],
        proxy: {
            type: 'ajax',
            url: url_ceksurvey,
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });
    var DataBeforeCetak = Ext.create('Ext.form.Panel', {
        autoScroll: true,
        width: 420,
        height: 125,
        id:'dataBeforeCetak',
        xtype: 'form',
        bodyPadding: 5,
        layout: {
            align: 'stretch',
            type: 'vbox'
        },
        header: false,
        title: lang('My Form'),
        items: [
        	{
               xtype: 'textfield',
               id: 'result',
               name: 'tipe',
               value: '0',
               hidden :true
           	},
            {
                xtype: 'combobox',
                id: 'survey',
                name: 'id',
                store: store_CekSurvey,
                fieldLabel: lang('Survey'),
                displayField: 'surveya',
                valueField: 'id',
                queryMode: 'local',
                listeners: {
                    change: function (cb, nv, ov) {
                        SurveyID = nv
                    }
                }
            }
            // ,{
            //     xtype: 'combobox',
            //     id: 'partner',
            //     name: 'partner',
            //     store: store_partner,
            //     fieldLabel: lang('Partner'),
            //     displayField: 'label',
            //     valueField: 'id',
            //     queryMode: 'local',
            //     listeners: {
            //         change: function(cb, nv, ov) {
            //             PartnerID = nv
            //         }
            //     }
            // }
            ,{
                xtype: 'container',
                height:43,
                layout: {
                    align: 'stretch',
                    pack: 'center',
                    padding: 2,
                    type: 'hbox'
                },
                items: [
	                {
						xtype: 'button',
						text: lang('GAP'),
						margin: '5 5 5 2',
						scale: 'large',
						ui: 's-button',
						disabled: false,
						cls: 's-blue',
						handler: function() {
	                    	if (!isNumber(SurveyID)) {alert('Silahkan pilih surveynya');return;}
	                      	winBeforeCetak.hide();
	                        // hasil
	                        preview_cetak_surat(m_cetak_result_farmer+'FarmerID/'+FarmerID+'/SurveyID/'+SurveyID+ '/PartnerID/' + PartnerID);
	                    }
	                },
                    {
                        xtype: 'button',
                        text: lang('GNP'),
                        margin: '5px',
                        scale: 'large',
                        ui: 's-button',
                        cls: 's-blue',
                        disabled: false,
                        handler: function() {
                            if (!isNumber(SurveyID)) {alert('Silahkan pilih surveynya');return;}
                            winBeforeCetak.hide();

                            preview_cetak_surat(m_cetak_result_nutrisi+'FarmerID/'+FarmerID+'/SurveyID/'+SurveyID+ '/PartnerID/' + PartnerID);
                          }
                    },
	                {
	                	xtype: 'button',
	                	text: lang('GFP'),
	                	margin: '5px',
	                	scale: 'large',
	                	ui: 's-button',
	                	cls: 's-blue',
	                	disabled: false,
	                	handler: function() {
	                		if (!isNumber(SurveyID)) {alert('Silahkan pilih surveynya');return;}
	                		winBeforeCetak.hide();

	                        preview_cetak_surat(m_cetak_result_aff+'FarmerID/'+FarmerID+'/SurveyID/'+SurveyID+ '/PartnerID/' + PartnerID);
	                      }
	                },
                    {
                        xtype: 'button',
                        text: lang('PPI'),
                        margin: '5px',
                        scale: 'large',
                        ui: 's-button',
                        cls: 's-blue',
                        disabled: false,
                        handler: function() {
                            if (!isNumber(SurveyID)) {alert(lang('Silahkan pilih type dan surveynya'));return;}
                            winBeforeCetak.hide();

                            preview_cetak_surat(m_cetak_result_ppi2012+'FarmerID/'+FarmerID+'/SurveyID/'+SurveyID+ '/PartnerID/' + PartnerID);
                        }
                    },
	                {
	                	xtype: 'button',
	                	text: lang('Beneficiary Profiles'),
	                	margin: '5px',
	                	scale: 'large',
	                	ui: 's-button',
	                	cls: 's-blue',
	                	disabled: false,
	                	handler: function() {
	                		if (!isNumber(SurveyID)) {alert(lang('Silahkan pilih type dan surveynya'));return;}
	                		winBeforeCetak.hide();

	                        preview_cetak_surat(m_cetak_beneficiary_profiles+'FarmerID/'+FarmerID+'/SurveyID/'+SurveyID+ '/PartnerID/' + PartnerID);
	                    }
	                }
            	]
            }
        ]
    });  
    if(winBeforeCetak) winBeforeCetak.destroy();
    var winBeforeCetak = Ext.create('widget.window', {
        id : 'print_map',
        title: lang('Cetak'),
        closable: true,
        modal:true,
        layout : 'fit',
        closeAction: 'show',
        width: 480,
        height:125,
        items: [DataBeforeCetak]
    });
    var areawindow = Ext.create('widget.window', {
        id : 'areawindow',
        title: lang('Area'),
        closable: true,
        modal:true,
        layout : 'fit',
        closeAction: 'show',
        width: width*0.9,
        height: height*0.95
    });
    
    function displayBeforeCetak(Farmer){
     	FarmerID = Farmer;
     	store_CekSurvey.load({
     		params: {
     			FarmerID: FarmerID
     		}}
     	);
        // store_partner.load({
        //     params: {
        //         FarmerID: FarmerID
        //     }}
        // );
        if(!winBeforeCetak.isVisible()){
            winBeforeCetak.show();
        } else {
            winBeforeCetak.hide(this, function() {});
            winBeforeCetak.toFront();
        }
        Ext.getCmp('survey').setValue();
        // Ext.getCmp('partner').setValue()
    }

    function display_area (MemberID, PlotNr, SurveyNr) {
        var areaPanel = Ext.getCmp('areawindow');
        areaPanel.show();

        var url = url_info+'?area=1&MemberID='+MemberID+'&PlotNr='+PlotNr+'&SurveyNr='+SurveyNr+'&province='+province+'&district='+district;

        Ext.Ajax.request({
            url: url,
            success: function(response){
                var htmlText = response.responseText;
                //Get the Panel component using its id
                
                // update the panel content's with 
                // HTML response from Ajax call
                areaPanel.update(htmlText, true);

            }
        });
    }

    function display_clone_area (id) {
    	var areaPanel = Ext.getCmp('areawindow');
		areaPanel.show();

    	var url = url_info+'?clone_area=1&id='+id+'&province='+province+'&district='+district;

    	Ext.Ajax.request({
    		url: url,
    		success: function(response){
    			var htmlText = response.responseText;
				//Get the Panel component using its id
				
				// update the panel content's with 
		        // HTML response from Ajax call
		        areaPanel.update(htmlText, true);

		    }
		});
    }

    function preview_farmer_summary(FarmerID) {
        preview_cetak_surat(m_cetak_farmer_summary+'FarmerID/'+FarmerID);
    }
