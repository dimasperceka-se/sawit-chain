
Ext.define('Koltiva.view.Mill.PanelTabSummaryReport' ,{
    extend: 'Ext.form.Panel',
    id: 'Koltiva.view.Mill.PanelTabSummaryReport',    
    fileUpload: true,
    style:'padding:0 15px 15px 15px;margin:12px 0 0 0;',
    title: lang('Summary Report'),
    fileUpload: true,
    opsiDisplay: false,
    setOpsiDisplay: function(value){
        this.opsiDisplay = value;
    },
    viewVar: false,
    setViewVar: function(value){
        this.viewVar = value;
    },
    listeners: {
        afterRender: function(){
        }
    },
    setFilterListFarcan: function(YearStartReport){
        localStorage.setItem('palm_trdec_list_searchp', JSON.stringify({
            YearStartReport: Ext.getCmp('YearStartReport').getValue(),
        }));
    },
    PrintProfile: function(MillID,YearStartReport,YearEndReport,MonthStartReport,MonthEnd){
        preview_cetak_surat(m_api + '/mill/mill_summary/MillID/'+MillID+'/YearStart/'+YearStart+'/YearEnd/'+YearEnd+'/MonthStart/'+MonthStart+'/MonthEnd/'+MonthEnd);
    },
    initComponent: function() {
        var thisObj = this;

        //store yg dipakai (start)
        var cmb_year_option = Ext.create('Koltiva.store.ComboGeneral.CmbYearOption',{
            storeVar: {
                yearRange: 2
            }
        });

        var cmb_month_option = Ext.create('Koltiva.store.ComboGeneral.CmbMonthOption');
        //store yg dipakai (end)
        var url = m_url+'/mill/summary_report';
        
        thisObj.items = [{
            layout: 'column',
            border: false,
            items:[{
                columnWidth: 1,
                layout:'form',
                style:'padding-right:25px;',
                items:[{
                    layout: 'column',
                    border: false,
                    items:[{
                        columnWidth: 0.8,
                        layout:'form',
                        style:'padding-right:25px;',
                        items:[{
                            layout: 'column',
                            border: false,
                            items:[{
                                columnWidth: 1,
                                html: '<p><b>'+lang("Filter")+' :</b></p>'
                            },{
                                columnWidth: 0.2,
                                layout:'form',
                                style:'padding-right:5px;',
                                items:[{
                                    id: 'YearStartReport',
                                    name: 'YearStartReport',
                                    xtype: 'combobox',
                                    anchor: '50%',
                                    fieldLabel: lang('Year Start'),
                                    labelAlign:'top',
                                    store: cmb_year_option,
                                    value:m_year_start,
                                    displayField: 'label',
                                    valueField: 'id',
                                    queryMode: 'local',
                                }]
                            },{
                                columnWidth: 0.2,
                                layout:'form',
                                style:'padding-right:5px;',
                                items:[{
                                    id: 'MonthStartReport',
                                    name: 'MonthStartReport',
                                    xtype: 'combobox',
                                    anchor: '50%',
                                    fieldLabel: lang('Month Start'),
                                    labelAlign:'top',
                                    value:m_month_start,
                                    store: cmb_month_option,
                                    displayField: 'label',
                                    valueField: 'id',
                                    queryMode: 'local',
                                }]
                            },{
                                columnWidth: 0.2,
                                layout:'form',
                                style:'padding-right:5px;',
                                items:[{
                                    id: 'YearEndReport',
                                    name: 'YearEndReport',
                                    xtype: 'combobox',
                                    anchor: '50%',
                                    fieldLabel: lang('Year End'),
                                    value:m_year_end,
                                    labelAlign:'top',
                                    store: cmb_year_option,
                                    displayField: 'label',
                                    valueField: 'id',
                                    queryMode: 'local',
                                }]
                            },{
                                columnWidth: 0.2,
                                layout:'form',
                                items:[{
                                    id: 'MonthEnd',
                                    name: 'MonthEnd',
                                    xtype: 'combobox',
                                    anchor: '50%',
                                    fieldLabel: lang('Month End'),
                                    value:m_month_end,
                                    labelAlign:'top',
                                    store: cmb_month_option,
                                    displayField: 'label',
                                    valueField: 'id',
                                    queryMode: 'local',
                                }]
                            },{
                                columnWidth: 0.15,
                                layout:'form',
                                items: [{
                                    style:'float:left;margin-left:10px;margin-top:30px',
                                    xtype:'button',
                                    iconCls:'search',
                                    text:'View',
                                    handler:function(c){
                                        var MonthStartReport  = Ext.getCmp("MonthStartReport").getValue();
                                        var MonthEnd    = Ext.getCmp("MonthEnd").getValue();
                                        var YearStartReport   = Ext.getCmp("YearStartReport").getValue();
                                        var YearEndReport     = Ext.getCmp("YearEndReport").getValue();
                                        thisObj.PrintProfile(thisObj.viewVar.MillID,YearStartReport,YearEndReport,MonthStartReport,MonthEnd);
                                    }
                                }]
                            }]
                        }]
                    },{
                        columnWidth: 1,
                        layout:'form',
                        style:'padding-right:25px;',
                        items:[{
                            html:'<div style="border-bottom:1px dashed gray;color:#34AA00;">&nbsp;</div>'
                        }]
                    },{
                        columnWidth: 1,
                        layout:'form',
                        style:'padding-right:25px;',
                        items:[{
                            id : 'summary-report-content',
                            html:''
                        }]
                    }]
                }]
            }]
        }];

        this.callParent(arguments);
    },
    listeners: {
        afterRender: function(){
            var thisObj = this;

            //load data form
            Ext.getCmp('Koltiva.view.Mill.PanelTabSummaryReport').getForm().load({
                url: m_api + '/mill/mill_tracebilityDeclaration',
                method: 'GET',
                params: {
                    MillID: this.viewVar.MillID,
                    YearStartReport : Ext.getCmp('YearStartReport').getValue(),
                    MonthStartReport : Ext.getCmp('MonthStartReport').getValue(),
                    YearEndReport : Ext.getCmp('YearEndReport').getValue(),
                    MonthEnd : Ext.getCmp('MonthEnd').getValue(),
                },
                success: function(form, action) {
                    var r = Ext.decode(action.response.responseText);
                },
                failure: function(form, action) {
                    Ext.MessageBox.show({
                        title: 'Failed',
                        msg: 'Failed to retrieve data',
                        buttons: Ext.MessageBox.OK,
                        animateTarget: 'mb9',
                        icon: 'ext-mb-error'
                    });
                }
            });
        }
    }
})