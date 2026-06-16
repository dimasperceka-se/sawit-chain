Ext.define('Koltiva.view.IMS.GridPanelDataReview' ,{
    extend: 'Ext.panel.Panel',
    id: 'Koltiva.view.IMS.GridPanelDataReview',
    initComponent: function() {
        var thisObj = this;
        var cmb_report_tools = Ext.create('Ext.data.Store', {
            extend: 'Ext.data.Model',
            fields: ['id', 'label'],
            autoLoad: true,
            proxy: {
                type: 'ajax',
                url: m_api + '/ims/cmb_report_tools',
                reader: {
                    type: 'json',
                    root: 'data'
                }
            }
        });
        cmb_report_tools.proxy.extraParams = { type: 'review'};

        thisObj.items = [{
            layout: 'column',
            border: false,
            items:[{
                style:"padding-top: 20px",
                columnWidth: 1,
                border: false,
                layout:{
                    type:'hbox',
                    pack:'left',
                    align: 'middle'
                },
                items:[{
                    xtype: 'combobox',
                    store: cmb_report_tools,
                    queryMode: 'local',
                    displayField: 'label',
                    valueField: 'id',
                    id: 'Koltiva.view.IMS.GridPanelDataReview-Tab-SummaryData-SummaryOption',
                    name: 'Koltiva.view.IMS.GridPanelDataReview-Tab-SummaryData-SummaryOption',
                    editable: false,
                    emptyText: lang('Select Report Tools'),
                    style:'margin-right:20px;',
                    width:475
                },{
                    xtype: 'button',
                    text: lang('Show Data'),
                    cls:'Sfr_BtnFormBlue',
                    overCls:'Sfr_BtnFormBlue-Hover',
                    handler: function() {
                        thisObj.SummaryShowData(Ext.getCmp('Koltiva.view.IMS.GridPanelDataReview-Tab-SummaryData-SummaryOption').getValue());
                    }
                }]
            },{
                columnWidth: 1,
                border: false,
                layout: 'form',
                items:[{
                    xtype: 'grid',
                    id: 'Koltiva.view.IMS.GridPanelDataReview-Tab-SummaryData-GridData',
                    style: 'border:1px solid #CCC;margin-top:15px;',
                    cls: 'Sfr_GridNew',
                    store: [],
                    autoScroll: true,
                    loadMask: true,
                    selType: 'rowmodel',
                    hidden:true,
                    viewConfig: {
                        deferEmptyText: false,
                        emptyText: lang('No Data Available')
                    },
                    columns: [],
                    dockedItems: [{
                        xtype: 'pagingtoolbar',
                        id:'Koltiva.view.IMS.GridPanelDataReview-Tab-SummaryData-GridData-PagingToolbar',
                        store: [], // same store GridPanel is using
                        dock: 'bottom',
                        displayInfo: true
                    },{
                        xtype: 'toolbar',
                        items: [{
                            icon: varjs.config.base_url + 'images/icons/new/export.png',
                            text: lang('Export to Excel'),
                            cls:'Sfr_BtnGridPaleBlue',
                            overCls:'Sfr_BtnGridPaleBlue-Hover',
                            scope: this,
                            handler: function() {
                                Ext.MessageBox.show({
                                    msg: 'Please wait...',
                                    progressText: 'Exporting...',
                                    width: 300,
                                    wait: true,
                                    waitConfig: {
                                        interval: 200
                                    },
                                    icon: 'ext-mb-info', //custom class in msg-box.html
                                    animateTarget: 'mb9'
                                });

                                Ext.Ajax.request({
                                    url: m_api + '/ims/data_review_excel',
                                    method: 'POST',
                                    waitMsg: lang('Please Wait'),
                                    params: {                                        
                                        RepID: Ext.getCmp('Koltiva.view.IMS.GridPanelDataReview-Tab-SummaryData-SummaryOption').getValue(),
                                        IMSID: thisObj.IMSID
                                    },
                                    success: function(data) {
                                        Ext.MessageBox.hide();
                                        if(!testJSON(data.responseText)){
                                            Ext.MessageBox.show({
                                                title: 'Failed',
                                                msg: 'Connection Failed',
                                                buttons: Ext.MessageBox.OK,
                                                animateTarget: 'mb9',
                                                icon: 'ext-mb-error'
                                            });
                                            return false;
                                        }

                                        var jsonResp = JSON.parse(data.responseText);
                                        if (jsonResp.count_data == 0){                                            
                                            Ext.MessageBox.show({
                                                title: 'Attention',
                                                msg: lang('No data found'),
                                                buttons: Ext.MessageBox.OK,
                                                animateTarget: 'mb9',
                                                icon: 'ext-mb-info'
                                            });
                                        } else {
                                            window.location = jsonResp.filenya;
                                        }
                                    },
                                    failure: function(rp, o){
                                        Ext.MessageBox.hide();
                                        try {
                                            var r = Ext.decode(o.response.responseText);
                                            Ext.MessageBox.show({
                                                title: 'Error',
                                                msg: r.message,
                                                buttons: Ext.MessageBox.OK,
                                                animateTarget: 'mb9',
                                                icon: 'ext-mb-error'
                                            });
                                        }
                                        catch(err) {
                                            Ext.MessageBox.show({
                                                title: 'Error',
                                                msg: 'Connection Error',
                                                buttons: Ext.MessageBox.OK,
                                                animateTarget: 'mb9',
                                                icon: 'ext-mb-error'
                                            });
                                        }
                                    }
                                });

                            }
                        }]
                    }]
                }]
            }]
        }];

        this.callParent(arguments);
    },
    SummaryShowData: function(RepID){
        var thisObj = this;
        var GridSummary = Ext.getCmp('Koltiva.view.IMS.GridPanelDataReview-Tab-SummaryData-GridData');
        var GridSummaryToolbar = Ext.getCmp('Koltiva.view.IMS.GridPanelDataReview-Tab-SummaryData-GridData-PagingToolbar');
        
        Ext.MessageBox.show({
            msg: 'Please wait...',
            progressText: 'Showing Data...',
            width: 300,
            wait: true,
            waitConfig: {
                interval: 200
            },
            icon: 'ext-mb-download', //custom class in msg-box.html
            animateTarget: 'mb7'
        });

        if(RepID != null){
            Ext.Ajax.request({
                url: m_api + '/ims/getReportTools',
                method: 'GET',
                params: {
                    RepID: RepID,
                    IMSID: thisObj.IMSID
                },
                success: function(response, action) {
                    Ext.MessageBox.hide();
                    if(!testJSON(response.responseText)){
                        Ext.MessageBox.hide();
                        Ext.MessageBox.show({
                            title: 'Failed',
                            msg: 'Connection Failed',
                            buttons: Ext.MessageBox.OK,
                            animateTarget: 'mb9',
                            icon: 'ext-mb-error'
                        });
                        return false;
                    }

                    var obj = Ext.decode(response.responseText);
                    switch(obj.success){
                        case true:
                            Ext.define('dinamisPartGridModel.Model', {
                                extend: 'Ext.data.Model',
                                fields: obj.fieldNya
                            });
                            thisObj.SummaryGridStore = Ext.create('Ext.data.Store', {
                                model: 'dinamisPartGridModel.Model',
                                autoLoad: true,
                                pageSize: 50,
                                proxy: {
                                    type: 'ajax',
                                    url: m_api + '/ims/view_main_list',
                                    reader: {
                                        type: 'json',
                                        root: 'data',
                                        totalProperty: 'total'
                                    },
                                    extraParams: {
                                        RepID: RepID,
                                        IMSID: thisObj.IMSID
                                    }
                                }
                            });                            

                            GridSummaryToolbar.bindStore(thisObj.SummaryGridStore);
                            GridSummary.reconfigure(thisObj.SummaryGridStore,obj.gridColumnNya);
                            // GridSummary.reconfigure(thisObj.SummaryGridStore,[obj.gridColumnNya]);

                            //Show Grid
                            GridSummary.setVisible(true);
                            GridSummary.getStore().loadPage(1);
                        break;
                        case false:
                            Ext.MessageBox.show({
                                title: 'Failed',
                                msg: 'Query syntax error',
                                buttons: Ext.MessageBox.OK,
                                animateTarget: 'mb9',
                                icon: 'ext-mb-error'
                            });
                        break;
                    }
                },
                failure: function(rp, o){
                    Ext.MessageBox.hide();
                    try {
                        var r = Ext.decode(o.response);
                        Ext.MessageBox.show({
                            title: 'Error',
                            msg: r.message,
                            buttons: Ext.MessageBox.OK,
                            animateTarget: 'mb9',
                            icon: 'ext-mb-error'
                        });
                    }
                    catch(err) {
                        Ext.MessageBox.show({
                            title: 'Error',
                            msg: 'Connection Error',
                            buttons: Ext.MessageBox.OK,
                            animateTarget: 'mb9',
                            icon: 'ext-mb-error'
                        });
                    }
                }
            })
        }else{
            Ext.MessageBox.show({
                title: 'Attention',
                msg: lang('No Summary Selected'),
                buttons: Ext.MessageBox.OK,
                animateTarget: 'mb9',
                icon: 'ext-mb-info'
            });
        }
    }
})