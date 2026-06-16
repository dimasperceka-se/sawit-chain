/*
* @Author: Gitandi Nadzari
* @Date:   2019-02-18 14:50:43
* @Last Modified by:   
* @Last Modified time: 
*/

/*
    Param2 yg diperlukan ketika load View ini
    
*/

Ext.define('Koltiva.view.Report.SawitTerampilWinCalculateKPI' ,{
    extend: 'Ext.window.Window',
    id: 'Koltiva.view.Report.SawitTerampilWinCalculateKPI',
    title: lang('Calculate KPI'),
    closable: true,
    modal: true,
    closeAction: 'destroy',
    width: '80%',
    height: '70%',
    overflowY: 'auto',
    viewVar: false,
    setViewVar: function(value){
        this.viewVar = value;
    },
    initComponent: function() {
        var thisObj = this;

        //Store
        thisObj.StoreMainGrid = Ext.create('Koltiva.store.Report.SawitTerampilMainFormGrid.CmbMonthYears',{
            storeVar: {
                showProcessDate: 'all' 
            }
        });
        
        thisObj.RowEditing = Ext.create('Ext.grid.plugin.RowEditing', {
            id: 'RowEditing',
            clicksToMoveEditor: 0,
            autoCancel: false,
            errorSummary : false,
            clicksToEdit: 2,
            listeners : {
               beforeedit : function(ev) {
                  return m_act_update;
               }
            }
        });
        //Context Menu
        thisObj.ContextMenuGtraining = Ext.create('Ext.menu.Menu', {
            cls: 'Sfr_ConMenu',
            items: [{
                    icon: varjs.config.base_url + 'images/icons/new/update.png',
                    text: lang('Update'),
                    hidden: !m_act_update,
                    id: 'DateBtnUpdate',
//                    cls: 'Sfr_BtnConMenuWhite ' + m_act_save,
                    handler: function () {
                        thisObj.RowEditing.cancelEdit();
                        var sm = Ext.getCmp('Koltiva.view.Report.SawitTerampilWinCalculateKPI-DataControl').getSelectionModel().getSelection();
                        thisObj.RowEditing.startEdit(sm[0].index, 0);
                    }
                }]
        });

        thisObj.items = [{
        	layout: 'column',
            border: false,
            items:[{
                columnWidth: 0.5,
                layout: 'form',
                style:'padding:10px 25px 10px 10px;',
                items:[{
                	xtype: 'grid',
                    id: 'Koltiva.view.Report.SawitTerampilWinCalculateKPI-DataControl',
                    style: 'border:1px solid #CCC;margin-top:4px;',
                    loadMask: true,
                    minHeight:200,
                    selType: 'rowmodel',
                    store: thisObj.StoreMainGrid,
                    viewConfig: {
                        deferEmptyText: false,
                        emptyText: lang('No data Available')
                    },
                    dockedItems: [{
                        xtype: 'toolbar',
                        dock: 'top',
                        items:[{
                            xtype: 'tbtext',
                            style:'font-weight:bold;',
                            text: lang('List of Date Process')
                        }]
                    },{
                        xtype: 'pagingtoolbar',
                        store: thisObj.StoreMainGrid,
                        dock: 'bottom',
                        width: '80%',
                        displayInfo: true
                    }],
                    columns: [{
                            text: '',
                            xtype: 'actioncolumn',
                            width: '4%',
                            items: [{
                                    icon: varjs.config.base_url + 'images/icons/new/action.png',
                                    handler: function (grid, rowIndex, colIndex, item, e, record) {
                                        thisObj.ContextMenuGtraining.showAt(e.getXY());
                                    }
                                }]
                    }, {
                        text: 'ID',
                        dataIndex: 'id',
                        hidden: true
                    },
                    {
                        text: 'No',
                        xtype: 'rownumberer',
                        align: 'center',
                        width: '20%'
                    },{
                        text: lang('Report Name'),
                        dataIndex: 'ReportName',
                        flex: 1,
                        editor: {
                            xtype:'textfield'
                        },
                    },{
                        text: 'DateProcess',
                        dataIndex: 'DateProcess',
                        width: '30%'
                    },{
                        xtype: 'checkcolumn',
		        disabled: true,
                        text:  lang('Reported'),
                        width: '20%',
                        dataIndex: 'ReportStatus',
                        editor: {
                            xtype:'checkbox'
                        },
                        renderer: function (val, meta, rec) {
                            if (val=='0') {
                                return  new Ext.grid.column.Check().renderer(false);
                            } else {
                                return  new Ext.grid.column.Check().renderer(true);
                            }
                        }
                    }],
                    plugins: [thisObj.RowEditing],
                    listeners: {
                        'canceledit':function(editor,e,eOpts){
                            thisObj.StoreMainGrid.load();
                        },
                        'edit': function(editor, e) {
                            var processId = e.record.data.id;
                            var ReportStatus = e.record.data.ReportStatus;
                            var ReportName = e.record.data.ReportName;
                            
                            Ext.MessageBox.confirm('Message', 'Update data ini ?', function(btn){
                            
                                if(btn == 'yes')
                                {
                                        Ext.Ajax.request({
                                            waitMsg: 'Please wait...',
                                            url: m_combo_monthyears,
                                            method : 'PUT',
                                            params: {
                                                processId: processId,
                                                ReportStatus: ReportStatus,
                                                ReportName: ReportName
                                            },
                                        success: function(response, opts){
                                             var obj = Ext.decode(response.responseText);
                                             switch(obj.success){
                                                 case true:
                                                    Ext.MessageBox.alert('Success',obj.message);
                                                    thisObj.StoreMainGrid.load();
                                                    break;
                                                 default:
                                                    Ext.MessageBox.alert('Warning',obj.message);
                                                 break;
                                             }
                                        },
                                        failure: function(response, opts){
                                            var obj = Ext.decode(response.responseText);
                                            Ext.MessageBox.alert('error','Could not connect to the database. Retry later');
                                            }
                                        });
                                }
                            });
                        }
                    }
                }]
            }, {
                columnWidth: 0.5,
                items: [{
                    html: '<div class="subtitleForm">' + lang('Calculation Process') + '</div>',
                    style: 'font-weight:bold;'
                }, {
                    xtype: 'button',
                    id: 'btnCalculateKPI',                    
                    margin: '0px 0px 0px 6px',
                    text: lang('Calculate KPI'),
                    handler: function () {
                        Ext.MessageBox.confirm('Message', 'Calculating KPI?', function(btn){
                            
                            if(btn == 'yes')
                            {   var calcGranted = m_act_calculate_kpi? false : true;
                                var prgVal = 5;
                                $('#calcprogressbar').val(prgVal);   
                                    Ext.Ajax.request({
                                        waitMsg: 'Please wait...',
                                        url: m_do_kpicalc,
                                        method : 'POST',
                                        params: {
                                            calcGranted: calcGranted,
                                            noCon:1
                                        },
                                    success: function(response, opts){
                                         var obj = Ext.decode(response.responseText);
                                         switch(obj.success){
                                             case true:
                                            Ext.getCmp('calcprogress').update(obj.step);
                                            prgVal += 10;
                                            $('#calcprogressbar').val(prgVal);
                                                Ext.Ajax.request({
                                                    waitMsg: 'Please wait...',
                                                    url: m_do_kpicalc,
                                                    method : 'POST',
                                                    params: {
                                                        calcGranted: calcGranted,
                                                        noCon:2
                                                    },
                                                success: function(response, opts){
                                                    var obj = Ext.decode(response.responseText);
                                                    switch(obj.success){
                                                        case true:
                                                            var currlabel = Ext.getCmp('calcprogress').getEl().dom.innerHTML;
                                                            Ext.getCmp('calcprogress').update(currlabel+' <br/> '+obj.step);
                                                            prgVal += 10;
                                                            $('#calcprogressbar').val(prgVal);
                                                            Ext.Ajax.request({
                                                                waitMsg: 'Please wait...',
                                                                url: m_do_kpicalc,
                                                                method : 'POST',
                                                                params: {
                                                                    calcGranted: calcGranted,
                                                                    noCon:3
                                                                },
                                                            success: function(response, opts){
                                                                var obj = Ext.decode(response.responseText);
                                                                switch(obj.success){
                                                                    case true:
                                                                        var currlabel = Ext.getCmp('calcprogress').getEl().dom.innerHTML;
                                                                        Ext.getCmp('calcprogress').update(currlabel+' <br/> '+obj.step);
                                                                        prgVal += 10;
                                                                        $('#calcprogressbar').val(prgVal);
                                                                        Ext.Ajax.request({
                                                                            waitMsg: 'Please wait...',
                                                                            url: m_do_kpicalc,
                                                                            method : 'POST',
                                                                            params: {
                                                                                calcGranted: calcGranted,
                                                                                noCon:4
                                                                            },
                                                                        success: function(response, opts){
                                                                            var obj = Ext.decode(response.responseText);
                                                                            switch(obj.success){
                                                                                case true:
                                                                                    var currlabel = Ext.getCmp('calcprogress').getEl().dom.innerHTML;
                                                                                    Ext.getCmp('calcprogress').update(currlabel+' <br/> '+obj.step);
                                                                                    prgVal += 10;
                                                                                    $('#calcprogressbar').val(prgVal);
                                                                                    Ext.Ajax.request({
                                                                                        waitMsg: 'Please wait...',
                                                                                        url: m_do_kpicalc,
                                                                                        method : 'POST',
                                                                                        params: {
                                                                                            calcGranted: calcGranted,
                                                                                            noCon:5
                                                                                        },
                                                                                    success: function(response, opts){
                                                                                        var obj = Ext.decode(response.responseText);
                                                                                        switch(obj.success){
                                                                                            case true:
                                                                                                var currlabel = Ext.getCmp('calcprogress').getEl().dom.innerHTML;
                                                                                                Ext.getCmp('calcprogress').update(currlabel+' <br/> '+obj.step);
                                                                                                prgVal += 10;
                                                                                                $('#calcprogressbar').val(prgVal);
                                                                                                Ext.Ajax.request({
                                                                                                    waitMsg: 'Please wait...',
                                                                                                    url: m_do_kpicalc,
                                                                                                    method : 'POST',
                                                                                                    params: {
                                                                                                        calcGranted: calcGranted,
                                                                                                        noCon:6
                                                                                                    },
                                                                                                success: function(response, opts){
                                                                                                    var obj = Ext.decode(response.responseText);
                                                                                                    switch(obj.success){
                                                                                                        case true:
                                                                                                            var currlabel = Ext.getCmp('calcprogress').getEl().dom.innerHTML;
                                                                                                            Ext.getCmp('calcprogress').update(currlabel+' <br/> '+obj.step);
                                                                                                            prgVal += 10;
                                                                                                            $('#calcprogressbar').val(prgVal);
                                                                                                            Ext.Ajax.request({
                                                                                                                waitMsg: 'Please wait...',
                                                                                                                url: m_do_kpicalc,
                                                                                                                method : 'POST',
                                                                                                                params: {
                                                                                                                    calcGranted: calcGranted,
                                                                                                                    noCon:7
                                                                                                                },
                                                                                                            success: function(response, opts){
                                                                                                                var obj = Ext.decode(response.responseText);
                                                                                                                switch(obj.success){
                                                                                                                    case true:
                                                                                                                        var currlabel = Ext.getCmp('calcprogress').getEl().dom.innerHTML;
                                                                                                                        Ext.getCmp('calcprogress').update(currlabel+' <br/> '+obj.step);
                                                                                                                        prgVal += 10;
                                                                                                                        $('#calcprogressbar').val(prgVal);
                                                                                                                        Ext.Ajax.request({
                                                                                                                            waitMsg: 'Please wait...',
                                                                                                                            url: m_do_kpicalc,
                                                                                                                            method : 'POST',
                                                                                                                            params: {
                                                                                                                                calcGranted: calcGranted,
                                                                                                                                noCon:8
                                                                                                                            },
                                                                                                                        success: function(response, opts){
                                                                                                                            var obj = Ext.decode(response.responseText);
                                                                                                                            switch(obj.success){
                                                                                                                                case true:
                                                                                                                                    var currlabel = Ext.getCmp('calcprogress').getEl().dom.innerHTML;
                                                                                                                                    Ext.getCmp('calcprogress').update(currlabel+' <br/> '+obj.step);
                                                                                                                                    prgVal += 10;
                                                                                                                                    $('#calcprogressbar').val(prgVal);
                                                                                                                                    Ext.Ajax.request({
                                                                                                                                        waitMsg: 'Please wait...',
                                                                                                                                        url: m_do_kpicalc,
                                                                                                                                        method : 'POST',
                                                                                                                                        params: {
                                                                                                                                            calcGranted: calcGranted,
                                                                                                                                            noCon:9
                                                                                                                                        },
                                                                                                                                    success: function(response, opts){
                                                                                                                                        var obj = Ext.decode(response.responseText);
                                                                                                                                        switch(obj.success){
                                                                                                                                            case true:
                                                                                                                                                var currlabel = Ext.getCmp('calcprogress').getEl().dom.innerHTML;
                                                                                                                                                Ext.getCmp('calcprogress').update(currlabel+' <br/> '+obj.step);
                                                                                                                                                prgVal += 10;
                                                                                                                                                $('#calcprogressbar').val(prgVal);
                                                                                                                                                Ext.Ajax.request({
                                                                                                                                                    waitMsg: 'Please wait...',
                                                                                                                                                    url: m_do_kpicalc,
                                                                                                                                                    method : 'POST',
                                                                                                                                                    params: {
                                                                                                                                                        calcGranted: calcGranted,
                                                                                                                                                        noCon:10
                                                                                                                                                    },
                                                                                                                                                success: function(response, opts){
                                                                                                                                                    var obj = Ext.decode(response.responseText);
                                                                                                                                                    switch(obj.success){
                                                                                                                                                        case true:
                                                                                                                                                            var currlabel = Ext.getCmp('calcprogress').getEl().dom.innerHTML;
                                                                                                                                                            Ext.getCmp('calcprogress').update(currlabel+' <br/> '+obj.step);
                                                                                                                                                            prgVal = 100;
                                                                                                                                                            $('#calcprogressbar').val(prgVal);
                                                                                                                                                            thisObj.StoreMainGrid.load();
                                                                                                                                                            break;
                                                                                                                                                        default:
                                                                                                                                                            Ext.MessageBox.alert('Warning',obj.message);
                                                                                                                                                        break;
                                                                                                                                                    }
                                                                                                                                                },
                                                                                                                                                failure: function(response, opts){
                                                                                                                                                    var obj = Ext.decode(response.responseText);
                                                                                                                                                    Ext.MessageBox.alert('error','Could not connect to the database. Retry later');
                                                                                                                                                    }
                                                                                                                                                });
                                                                                                                                                break;
                                                                                                                                            default:
                                                                                                                                                Ext.MessageBox.alert('Warning',obj.message);
                                                                                                                                            break;
                                                                                                                                        }
                                                                                                                                    },
                                                                                                                                    failure: function(response, opts){
                                                                                                                                        var obj = Ext.decode(response.responseText);
                                                                                                                                        Ext.MessageBox.alert('error','Could not connect to the database. Retry later');
                                                                                                                                        }
                                                                                                                                    });
                                                                                                                                    break;
                                                                                                                                default:
                                                                                                                                    Ext.MessageBox.alert('Warning',obj.message);
                                                                                                                                break;
                                                                                                                            }
                                                                                                                        },
                                                                                                                        failure: function(response, opts){
                                                                                                                            var obj = Ext.decode(response.responseText);
                                                                                                                            Ext.MessageBox.alert('error','Could not connect to the database. Retry later');
                                                                                                                            }
                                                                                                                        });
                                                                                                                        break;
                                                                                                                    default:
                                                                                                                        Ext.MessageBox.alert('Warning',obj.message);
                                                                                                                    break;
                                                                                                                }
                                                                                                            },
                                                                                                            failure: function(response, opts){
                                                                                                                var obj = Ext.decode(response.responseText);
                                                                                                                Ext.MessageBox.alert('error','Could not connect to the database. Retry later');
                                                                                                                }
                                                                                                            });
                                                                                                            break;
                                                                                                        default:
                                                                                                            Ext.MessageBox.alert('Warning',obj.message);
                                                                                                        break;
                                                                                                    }
                                                                                                },
                                                                                                failure: function(response, opts){
                                                                                                    var obj = Ext.decode(response.responseText);
                                                                                                    Ext.MessageBox.alert('error','Could not connect to the database. Retry later');
                                                                                                    }
                                                                                                });
                                                                                                break;
                                                                                            default:
                                                                                                Ext.MessageBox.alert('Warning',obj.message);
                                                                                            break;
                                                                                        }
                                                                                    },
                                                                                    failure: function(response, opts){
                                                                                        var obj = Ext.decode(response.responseText);
                                                                                        Ext.MessageBox.alert('error','Could not connect to the database. Retry later');
                                                                                        }
                                                                                    });
                                                                                    break;
                                                                                default:
                                                                                    Ext.MessageBox.alert('Warning',obj.message);
                                                                                break;
                                                                            }
                                                                        },
                                                                        failure: function(response, opts){
                                                                            var obj = Ext.decode(response.responseText);
                                                                            Ext.MessageBox.alert('error','Could not connect to the database. Retry later');
                                                                            }
                                                                        });
                                                                        break;
                                                                    default:
                                                                        Ext.MessageBox.alert('Warning',obj.message);
                                                                    break;
                                                                }
                                                            },
                                                            failure: function(response, opts){
                                                                var obj = Ext.decode(response.responseText);
                                                                Ext.MessageBox.alert('error','Could not connect to the database. Retry later');
                                                                }
                                                            });
                                                            break;
                                                        default:
                                                            Ext.MessageBox.alert('Warning',obj.message);
                                                        break;
                                                    }
                                                },
                                                failure: function(response, opts){
                                                    var obj = Ext.decode(response.responseText);
                                                    Ext.MessageBox.alert('error','Could not connect to the database. Retry later');
                                                    }
                                                });
                                                break;
                                             default:
                                                Ext.MessageBox.alert('Warning',obj.message);
                                             break;
                                         }
                                    },
                                    failure: function(response, opts){
                                        var obj = Ext.decode(response.responseText);
                                        Ext.MessageBox.alert('error','Could not connect to the database. Retry later'+obj);
                                        }
                                    });
                                    
                            }
                        });
                    }
                }, {
                    html: '<progress id="calcprogressbar" value="0" max="100"></progress>',
                    padding: 5
                }, {
                    xtype: 'label',
                    id: 'calcprogress',
                    cls: 'x-form-item-label'
                    // text: lang('Estimated time (in hour) allocated for different activities yesterday (24 hours)')
                    // html: '<div id="calcprogress" >&nbsp;</div>',
                    // padding: 5
                }]
            }]
        }];
        
        thisObj.buttons = [{
            text: lang('Close'),
            margin: '5px',
            scale: 'large',
            ui: 's-button',
            cls: 's-grey',
            handler: function() {
                Ext.getCmp('filterMonthYears').store.load();
                thisObj.close();
            }
        }];
        

        this.callParent(arguments);
    }
});