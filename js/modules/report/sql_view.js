/*
* @Author: nikolius
* @Date:   2017-02-09 15:57:18
* @Last Modified by:   nikolius
* @Last Modified time: 2018-01-04 11:37:06
*/
//override time out ajax exts js yg cuman 30 detikan jadi 10 menit
Ext.Ajax.timeout = 600000;
Ext.override(Ext.form.Basic, {
    timeout: Ext.Ajax.timeout / 1000
});
Ext.override(Ext.data.proxy.Server, {
    timeout: Ext.Ajax.timeout
});
Ext.override(Ext.data.Connection, {
    timeout: Ext.Ajax.timeout
});

Ext.onReady(function(){
    Ext.tip.QuickTipManager.init();

    Ext.define('mainGridModel.Model', {
        extend: 'Ext.data.Model',
        fields: ['SqlvID','UserIDOwner','User','Name','Description','CreatedDate','UpdatedDate']
    });

    var store = Ext.create('Ext.data.Store', {
        model: 'mainGridModel.Model',
        autoLoad: true,
        pageSize: 50,
        remoteSort: true,
        proxy: {
            type: 'ajax',
            url: m_api + '/report_sql_view/main_list',
            params: {
                'X-API-KEY': '030584'
            },
            reader: {
                type: 'json',
                root: 'data',
                totalProperty: 'total'
            }
        },
        listeners: {
            'beforeload': function(store, options) {
                store.proxy.extraParams.SqlvName_Search = Ext.getCmp('SqlvName_Search').getValue();
            }
        }
    });

    var contextMenuGrid = Ext.create('Ext.menu.Menu',{
        items:[{
            icon: varjs.config.base_url + 'images/icons/new/view.png',
            text: lang('View'),
            itemId: 'contextMenuViewItem',
            handler: function() {
                var sm = Ext.getCmp('grid').getSelectionModel().getSelection()[0];
                displayMainForm('update',store,sm.get('SqlvID'),true);
            }
        },{
            icon: varjs.config.base_url + 'images/icons/new/update.png',
            text: lang('Update'),
            itemId: 'contextMenuUpdateItem',
            hidden: m_act_update,
            handler: function(){
                var sm = Ext.getCmp('grid').getSelectionModel().getSelection()[0];
                displayMainForm('update',store,sm.get('SqlvID'),false);
            }
        },{
            icon: varjs.config.base_url + 'images/icons/silk/find.png', cls:'Sfr_BtnGridPaleBlue',
            text: lang('Add Filter'),
            itemId: 'contextMenuFilterItem',
            hidden: m_sql_view_filter,
            handler: function(){
                var sm = Ext.getCmp('grid').getSelectionModel().getSelection()[0];
                displayAddFilterSetting(sm.get('SqlvID'),sm.get('Name'),sm.get('Description'));
            }
        },{
            icon: varjs.config.base_url + 'images/icons/silk/server_connect.png',
            text: lang('Share'),
            itemId: 'contextMenuShareItem',
            hidden: m_sql_view_share,
            handler: function(){
                var sm = Ext.getCmp('grid').getSelectionModel().getSelection()[0];
                displayShareSetting(sm.get('SqlvID'),sm.get('Name'),sm.get('Description'));
            }
        },{
            icon: varjs.config.base_url + 'images/icons/silk/script_start.png',
            text: lang('Run Query'),
            itemId: 'contextMenuRunQueryItem',
            hidden: m_run_query,
            handler: function(){
                var sm = Ext.getCmp('grid').getSelectionModel().getSelection()[0];
                displayRunQuery(sm.get('SqlvID'));
            }
        },{
            icon: varjs.config.base_url + 'images/icons/new/delete.png',
            text: lang('Delete'),
            itemId: 'contextMenuDeleteItem',
            hidden: m_act_delete,
            handler: function() {
                var sm = Ext.getCmp('grid').getSelectionModel().getSelection()[0];

                //hapus data staff (begin)===============
                Ext.MessageBox.confirm('Message', lang('Are you sure want to delete this data ?') , function(btn){
                    if(btn == 'yes'){
                        Ext.Ajax.request({
                            waitMsg: lang('Please Wait'),
                            url: m_api + '/report_sql_view/sql_view',
                            method : 'DELETE',
                            params: {SqlvID:  sm.get('SqlvID')},
                            success: function(response, opts){
                                var obj = Ext.decode(response.responseText);
                                switch(obj.success){
                                    case true:
                                        Ext.MessageBox.alert('Success', obj.message);
                                        store.load();
                                    break;
                                    default:
                                        Ext.MessageBox.show({
                                            title: 'Failed',
                                            msg: obj.message,
                                            buttons: Ext.MessageBox.OK,
                                            animateTarget: 'mb9',
                                            icon: 'ext-mb-error'
                                        });
                                    break;
                                }
                            },
                            failure: function(response, opts){
                                Ext.MessageBox.show({
                                    title: 'Failed',
                                    msg: 'Failed to delete data',
                                    buttons: Ext.MessageBox.OK,
                                    animateTarget: 'mb9',
                                    icon: 'ext-mb-error'
                                });
                            }
                        })
                    }
                });
                //hapus data staff (end)===============
            }
        }]
    });

    function submitOnEnter(field, event) {
        if (event.getKey() == event.ENTER) {
            store.load({
                params: {
                    page: 1,
                    start: 0,
                    limit: 50
                }
            });
        }
    }

    var grid = Ext.create('Ext.grid.Panel', {
        store: store,
        width: '100%',
        id: 'grid',
        minHeight: 250,
        maxHeight: 550,
        style: 'border:1px solid #CCC;',
        renderTo: 'ext-content',
        loadMask: true,
        selType: 'rowmodel',
        listeners: {
            itemclick: function(view, record, item, index, e){
                contextMenuGrid.showAt(e.getXY());

                //cek hak akses untuk yg bukan quernya
                var sm = record;
                if(m_userid == sm.data.UserIDOwner){
                    if(m_act_update == false) contextMenuGrid.getComponent('contextMenuUpdateItem').setVisible(true);
                    if(m_sql_view_share == false) contextMenuGrid.getComponent('contextMenuShareItem').setVisible(true);
                    if(m_act_delete == false) contextMenuGrid.getComponent('contextMenuDeleteItem').setVisible(true);
                }else{
                    // dibypass kalau admin
                    if(m_is_admin != 1){
                        contextMenuGrid.getComponent('contextMenuUpdateItem').setVisible(false);
                        contextMenuGrid.getComponent('contextMenuShareItem').setVisible(false);
                        contextMenuGrid.getComponent('contextMenuDeleteItem').setVisible(false);
                    }
                }
            }
        },
        dockedItems: [{
            xtype: 'pagingtoolbar',
            store: store, // same store GridPanel is using
            dock: 'bottom',
            displayInfo: true
        },{
            xtype: 'toolbar',
            items: [{
                icon: varjs.config.base_url + 'images/icons/new/add.png', cls:'Sfr_BtnGridGreen', overCls:'Sfr_BtnGridGreen-Hover',
                text: lang('Add'),
                scope: this,
                hidden: m_act_add,
                handler: function() {
                    displayMainForm('add',store,null,false);
                }
            },{
                name: 'SqlvName_Search',
                id: 'SqlvName_Search',
                xtype: 'textfield',
                width: 300,
                emptyText: lang('SQL View Name'),
                listeners: {
                    specialkey: submitOnEnter
                }
            },{
                xtype: 'button',
                icon: varjs.config.base_url + 'images/icons/silk/search.png',
                margin: '0px 0px 0px 6px',
                text: lang('Search'),
                handler: function() {
                    store.load({
                        params: {
                            page: 1,
                            start: 0,
                            limit: 50
                        }
                    });
                }
            }]
        }],
        columns: [{
            text: 'SqlvID',
            dataIndex: 'SqlvID',
            hidden: true
        },{
            text: 'UserIDOwner',
            dataIndex: 'UserIDOwner',
            hidden: true
        },{
            text: 'No',
            xtype: 'rownumberer',
            width: '3%'
        }, {
            text: lang('User'),
            dataIndex: 'User',
            width: '19%'
        },{
            text: lang('Name'),
            dataIndex: 'Name',
            width: '25%'
        },{
            text: lang('Description'),
            dataIndex: 'Description',
            width: '27%'
        },{
            text: lang('Created Date'),
            dataIndex: 'CreatedDate',
            width: '13%'
        },{
            text: lang('Updated Date'),
            dataIndex: 'UpdatedDate',
            width: '13%'
        }]
    });

    function displayMainForm(displayMethod,store,SqlvID,viewOnly){
        var winMainForm = Ext.create('widget.window', {
            title: lang('Form SQL View'),
            id: 'winMainForm',
            closable: true,
            modal: true,
            closeAction: 'destroy',
            width: '65%',
            height: '90%',
            overflowY: 'auto',
            bodyStyle:{"background-color":"#F0F0F0"},
            style:'background-color:#F0F0F0;',
            padding:6,
            scrollOffset: 20,
            items:[{
                xtype: 'form',
                id: 'mainForm',
                fileUpload: true,
                padding:'5 20 5 8',
                items:[{
                    layout: 'column',
                    border: false,
                    items: [{
                        columnWidth: 1,
                        padding: 4,
                        layout:'form',
                        items:[{
                            xtype: 'hiddenfield',
                            id: 'SqlvID',
                            name: 'SqlvID'
                        },{
                            xtype: 'textfield',
                            fieldLabel: lang('Name'),
                            labelWidth: 150,
                            allowBlank: false,
                            id: 'SqlvName',
                            name: 'SqlvName'
                        },{
                            xtype: 'textfield',
                            fieldLabel: lang('Description'),
                            labelWidth: 150,
                            id: 'SqlvDesc',
                            name: 'SqlvDesc'
                        },{
                            html: '<p style="color:red;font-size:11px;font-style:italic;">No INSERT, UPDATE, DELETE and DROP syntax<br />Characters not allowed including ; and ?</p>'
                        },{
                            xtype     : 'textareafield',
                            grow      : true,
                            anchor : '100%',
                            height: 500,
                            fieldLabel: lang('SQL Statement'),
                            labelWidth: 150,
                            allowBlank: false,
                            msgTarget: 'side',
                            id: 'SqlvStatement',
                            name: 'SqlvStatement'
                        }]
                    }]
                }]
            }],
            buttons:[{
                text: 'Save',
                margin: '5px',
                scale: 'large',
                ui: 's-button',
                cls: 's-blue',
                hidden:viewOnly,
                handler: function () {
                    var form = Ext.getCmp('mainForm').getForm();

                    if (form.isValid()) {
                        form.submit({
                            url: m_api + '/report_sql_view/sql_view',
                            method:'POST',
                            waitMsg: 'Saving data...',
                            success: function(fp, o) {
                                var obj = o.result;
                                switch(obj.success){
                                    case true:
                                        Ext.MessageBox.alert('Success', obj.message);
                                        winMainForm.close();
                                        store.load();
                                    break;
                                    default:
                                        Ext.MessageBox.show({
                                            title: 'Failed',
                                            msg: obj.message,
                                            buttons: Ext.MessageBox.OK,
                                            animateTarget: 'mb9',
                                            icon: 'ext-mb-error'
                                        });
                                    break;
                                }
                            },
                            failure: function(fp, o){
                                Ext.MessageBox.show({
                                    title: 'Failed',
                                    msg: 'Failed to save data',
                                    buttons: Ext.MessageBox.OK,
                                    animateTarget: 'mb9',
                                    icon: 'ext-mb-error'
                                });
                            }
                        });
                    }else{
                        Ext.MessageBox.show({
                            title: 'Failed',
                            msg: 'Form not complete yet',
                            buttons: Ext.MessageBox.OK,
                            animateTarget: 'mb9',
                            icon: 'ext-mb-error'
                        });
                    }
                }
            },{
                text: lang('Close'),
                margin: '5px',
                scale: 'large',
                ui: 's-button',
                cls: 's-grey',
                disabled: false,
                handler: function() {
                    winMainForm.close();
                }
            }]
        });

        if(displayMethod == 'add'){
            //tambah
        } else if(displayMethod == 'update') {
            //update
            Ext.getCmp('mainForm').getForm().load({
                url: m_api + '/report_sql_view/form_sql_view',
                method: 'GET',
                params: {
                    SqlvID: SqlvID
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

        //show windows
        if (!winMainForm.isVisible()) {
            winMainForm.show();
        } else {
            winMainForm.close();
        }
    }

    function displayRunQuery(SqlvID){
        Ext.MessageBox.show({
            msg: 'Please wait...',
            progressText: 'Exporting...',
            width: 300,
            wait: true,
            waitConfig: {
                interval: 200
            },
            icon: 'ext-mb-download', //custom class in msg-box.html
            animateTarget: 'mb7'
        });

        Ext.Ajax.request({
            url: m_api + '/report_sql_view/prep_run_query',
            method: 'GET',
            params: {
                SqlvID: SqlvID
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

                        var store_sql_view = Ext.create('Ext.data.Store', {
                            model: 'dinamisPartGridModel.Model',
                            autoLoad: true,
                            pageSize: 50,
                            proxy: {
                                type: 'ajax',
                                url: m_api + '/report_sql_view/sql_view_main_list',
                                reader: {
                                    type: 'json',
                                    root: 'data',
                                    totalProperty: 'total'
                                },
                                extraParams: {
                                    SqlvID: SqlvID
                                }
                            }
                        });

                        var winSqlView = Ext.create('widget.window', {
                            title: lang('SQL View - Data'),
                            id: 'winSqlView',
                            closable: true,
                            modal: true,
                            closeAction: 'destroy',
                            width: '78%',
                            height: '95%',
                            overflowY: 'auto',
                            bodyStyle:{"background-color":"#F0F0F0"},
                            style:'background-color:#F0F0F0;padding:4px;',
                            items:[{
                                layout: 'column',
                                border: false,
                                padding: '0 20 0 0',
                                items: [{
                                    columnWidth: 1,
                                    layout:'form',
                                    items: [{
                                        items:[{
                                            layout:'fit',
                                            items:[{
                                                xtype: 'gridpanel',
                                                title: lang('Data List'),
                                                id: 'gridSqlView',
                                                style: 'border:1px solid #CCC;',
                                                store: store_sql_view,
                                                //width: '100%',
                                                autoScroll: true,
                                                loadMask: true,
                                                selType: 'rowmodel',
                                                minHeight: 250,
                                                maxHeight: 550,
                                                columns: obj.gridColumnNya,
                                                dockedItems: [{
                                                    xtype: 'pagingtoolbar',
                                                    store: store_sql_view, // same store GridPanel is using
                                                    dock: 'bottom',
                                                    displayInfo: true
                                                },{
                                                    xtype: 'toolbar',
                                                    items: [{
                                                        icon: varjs.config.base_url + 'images/icons/new/export.png', cls:'Sfr_BtnGridPaleBlue',
                                                        text: lang('Export to Excel (Node)'),
                                                        hidden: m_export_excel_utilities,
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
                                                                url: m_api + '/report_sql_view/sql_view_export_excel',
                                                                method: 'POST',
                                                                waitMsg: lang('Please Wait'),
                                                                params: {
                                                                    SqlvID: SqlvID
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
                                                                    window.location = jsonResp.filenya;
                                                                },
                                                                failure: function() {
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
                                                    },{
                                                        icon: varjs.config.base_url + 'images/icons/new/export.png', cls:'Sfr_BtnGridPaleBlue',
                                                        text: lang('Export to Excel'),
                                                        hidden: m_sql_view_export_excel,
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
                                                                icon: 'ext-mb-download', //custom class in msg-box.html
                                                                animateTarget: 'mb7'
                                                            });

                                                            Ext.Ajax.request({
                                                              //old  url: m_api + '/report_sql_view/sql_view_export_excel',
                                                                //url: m_api + '/report_sql_view/sql_view_export_excel_xml',
                                                                url: m_api + '/report_sql_view/sql_view_export_excel',
                                                                method: 'POST',
                                                                waitMsg: lang('Please Wait'),
                                                                params: {
                                                                    SqlvID: SqlvID
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
                                                                    window.location = jsonResp.filenya;
                                                                },
                                                                failure: function() {
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
                                                    },{
                                                        icon: varjs.config.base_url + 'images/icons/new/export.png', cls:'Sfr_BtnGridPaleBlue',
                                                        text: lang('Export to CSV'),
                                                        hidden: m_sql_view_export_csv,
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
                                                                icon: 'ext-mb-download', //custom class in msg-box.html
                                                                animateTarget: 'mb7'
                                                            });

                                                            Ext.Ajax.request({
                                                                url: m_api + '/report_sql_view/sql_view_export_csv',
                                                                method: 'POST',
                                                                waitMsg: lang('Please Wait'),
                                                                params: {
                                                                    SqlvID: SqlvID
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
                                                                    window.location = jsonResp.filenya;
                                                                },
                                                                failure: function() {
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
                                                    }]
                                                }]
                                            }]
                                        }]
                                    }]
                                }]
                            }],
                            buttons:[{
                                text: lang('Close'),
                                margin: '5px',
                                scale: 'large',
                                ui: 's-button',
                                cls: 's-grey',
                                disabled: false,
                                handler: function() {
                                    winSqlView.close();
                                }
                            }]
                        });

                        //show windows
                        if (!winSqlView.isVisible()) {
                            winSqlView.show();
                        } else {
                            winSqlView.close();
                        }

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
            failure: function(response, action){
                Ext.MessageBox.hide();
                Ext.MessageBox.show({
                    title: 'Failed',
                    msg: 'Failed to connect',
                    buttons: Ext.MessageBox.OK,
                    animateTarget: 'mb9',
                    icon: 'ext-mb-error'
                });
            }
        });
    }

    function displayAddFilterSetting(SqlvID,ShareName,ShareDescription){

        var s_grid_add_filter_sql_view = Ext.create('Ext.data.Store', {
            extend: 'Ext.data.Model',
            fields: ['FilterID', 'FilterBy', 'Operator', 'FilterValue'],
            autoLoad: true,
            proxy: {
                type: 'ajax',
                url: m_api + '/report_sql_view/grid_add_filter',
                reader: {
                    type: 'json',
                    root: 'data'
                }
            },
            listeners: {
                'beforeload': function(store, options) {
                    store.proxy.extraParams.SqlvID = SqlvID;
                }
            }
        });

        var cmb_add_filter_filter_by = Ext.create('Ext.data.Store', {
            extend: 'Ext.data.Model',
            fields: ['id', 'label'],
            autoLoad: true,
            proxy: {
                type: 'ajax',
                url: m_api + '/report_sql_view/cmb_add_filter_by',
                reader: {
                    type: 'json',
                    root: 'data'
                }
            },
            listeners: {
                'beforeload': function(store, options) {
                    store.proxy.extraParams.SqlvID = SqlvID;
                }
            }
        });

        var cmb_add_filter_operator = Ext.create('Ext.data.Store', {
            extend: 'Ext.data.Model',
            fields: ['id', 'label'],
            data: [{
                "id": "=",
                "label": "="
            }, {
                "id": "<>",
                "label": "<>"
            }, {
                "id": ">",
                "label": ">"
            }, {
                "id": "<",
                "label": "<"
            },{
                "id": "LIKE",
                "label": "LIKE"
            }]
        });

        Ext.define('addFilterSqlViewModel.Model', {
            extend: 'Ext.data.Model',
            fields: ['FilterID', 'FilterBy', 'Operator', 'FilterValue']
        });

        var addFilterRowEditing = Ext.create('Ext.grid.plugin.RowEditing', {
            id: 'rowEditGridAddFilterSqlView',
            clicksToMoveEditor: 0,
            autoCancel: false,
            errorSummary: false,
            clicksToEdit: 2
        });

        var winAddFilterSqlView = Ext.create('widget.window', {
            title: lang('Add Filter'),
            id: 'winAddFilterSQLView',
            closable: true,
            modal: true,
            closeAction: 'destroy',
            width: '45%',
            height: '50%',
            overflowY: 'auto',
            bodyStyle:{"background-color":"#F0F0F0"},
            style:'background-color:#F0F0F0;',
            padding:6,
            scrollOffset: 20,
            items:[{
                xtype:'grid',
                store: s_grid_add_filter_sql_view,
                width: '98%',
                minHeight: 150,
                id: 'gridAddFilterSQLView',
                style: 'border:1px solid #CCC;margin-top:5px;',
                loadMask: true,
                selType: 'rowmodel',
                viewConfig: {
                    deferEmptyText: false,
                    emptyText: lang('No filter yet')
                },
                dockedItems: [{
                    xtype: 'toolbar',
                    items: [{
                        icon: varjs.config.base_url + 'images/icons/new/add.png', cls:'Sfr_BtnGridGreen', overCls:'Sfr_BtnGridGreen-Hover',
                        hidden: m_act_add,
                        text: lang('Add'),
                        scope: this,
                        handler: function() {
                            addFilterRowEditing.cancelEdit();
                            var r = Ext.create('addFilterSqlViewModel.Model', {
                                FilterID:'',
                                FilterBy:'',
                                Operator:'',
                                FilterValue:''
                            });
                            s_grid_add_filter_sql_view.insert(0, r);
                            addFilterRowEditing.startEdit(0, 0);
                        }
                    },{
                        icon: varjs.config.base_url + 'images/icons/new/update.png',
                        hidden: m_act_update,
                        text: lang('Update'),
                        scope: this,
                        handler: function() {
                            var sm = Ext.getCmp('gridAddFilterSQLView').getSelectionModel().getSelection()[0];

                            //get last row from store
                            var lastRow = s_grid_add_filter_sql_view.getAt(s_grid_add_filter_sql_view.getCount()-1);
                            //console.log(lastRow);

                            if(sm.data.FilterID == lastRow.data.FilterID){
                                var heightGridNow = Ext.getCmp('gridAddFilterSQLView').getHeight();
                                heightGridNow = heightGridNow + 55;
                                Ext.getCmp('gridAddFilterSQLView').setHeight(heightGridNow);
                            }

                            addFilterRowEditing.cancelEdit();
                            addFilterRowEditing.startEdit(sm.index, 0);
                        }
                    },{
                        icon: varjs.config.base_url + 'images/icons/new/delete.png',
                        hidden: m_act_delete,
                        text: lang('Delete'),
                        scope: this,
                        handler: function() {
                            var sm = Ext.getCmp('gridAddFilterSQLView').getSelectionModel().getSelection()[0];

                            Ext.MessageBox.confirm('Message', 'Do you want to delete this data ?', function(btn) {
                                if (btn == 'yes') {
                                    Ext.Ajax.request({
                                        waitMsg: 'Please Wait',
                                        url: m_api + '/report_sql_view/add_filter_item',
                                        method: 'DELETE',
                                        params: {
                                            FilterID: sm.data.FilterID
                                        },
                                        success: function(response, opts) {
                                            Ext.MessageBox.show({
                                                title: 'Information',
                                                msg: lang('Data deleted'),
                                                buttons: Ext.MessageBox.OK,
                                                animateTarget: 'mb9',
                                                icon: 'ext-mb-success'
                                            });

                                            //refresh store
                                            s_grid_add_filter_sql_view.load();
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
                    }]
                }],
                plugins: [addFilterRowEditing],
                columns: [{
                    dataIndex: 'FilterID',
                    hidden: true
                },{
                    text: 'No',
                    xtype: 'rownumberer',
                    width: '5%'
                },{
                    text: lang('Filter By'),
                    width: '40%',
                    dataIndex: 'FilterBy',
                    editor: {
                        xtype: 'combobox',
                        store: cmb_add_filter_filter_by,
                        displayField: 'label',
                        valueField: 'id',
                        queryMode: 'local',
                        id: 'gridRowEdit.ColFilterBy',
                        allowBlank: false
                    }
                },{
                    text: lang('Operator'),
                    width: '10%',
                    dataIndex: 'Operator',
                    editor: {
                        xtype: 'combobox',
                        store: cmb_add_filter_operator,
                        displayField: 'label',
                        valueField: 'id',
                        queryMode: 'local',
                        id: 'gridRowEdit.ColOperator',
                        allowBlank: false
                    }
                },{
                    text: lang('Value'),
                    width: '44%',
                    dataIndex: 'FilterValue',
                    editor: {
                        xtype: 'textfield',
                        id: 'gridRowEdit.ColFilterValue',
                        allowBlank: false
                    }
                }],
                listeners: {
                    itemdblclick: function(dv, record, item, index, e) {
                        if (m_act_update) {
                            addFilterRowEditing.cancelEdit();
                        }else{
                            //console.log(record);

                            //get last row from store
                            var lastRow = s_grid_add_filter_sql_view.getAt(s_grid_add_filter_sql_view.getCount()-1);
                            //console.log(lastRow);

                            if(record.data.FilterID == lastRow.data.FilterID){
                                var heightGridNow = Ext.getCmp('gridAddFilterSQLView').getHeight();
                                heightGridNow = heightGridNow + 55;
                                Ext.getCmp('gridAddFilterSQLView').setHeight(heightGridNow);
                            }
                        }
                    },
                    'canceledit': function(editor, e, eOpts) {
                        s_grid_add_filter_sql_view.load();
                    },
                    'edit': function(editor, e) {
                        console.log(e);

                        if (e.record.data.FilterID == '') {
                            //insert
                            var opsiPost = 'insert';
                            var FilterBy = e.record.data.FilterBy;
                            var Operator = e.record.data.Operator;
                            var FilterValue = e.record.data.FilterValue;
                        }else{
                            //update
                            var opsiPost = 'update';
                            var FilterBy = Ext.getCmp('gridRowEdit.ColFilterBy').getValue();
                            var Operator = Ext.getCmp('gridRowEdit.ColOperator').getValue();
                            var FilterValue = Ext.getCmp('gridRowEdit.ColFilterValue').getValue();
                        }

                        Ext.Ajax.request({
                            waitMsg: 'Please wait...',
                            url: m_api + '/report_sql_view/add_filter_item',
                            method: 'POST',
                            params: {
                                opsiPost: opsiPost,
                                SqlvID: SqlvID,
                                FilterID: e.record.data.FilterID,
                                FilterBy: FilterBy,
                                Operator: Operator,
                                FilterValue: FilterValue
                            },
                            success: function(response, opts) {
                                Ext.MessageBox.show({
                                    title: 'Information',
                                    msg: lang('Data saved'),
                                    buttons: Ext.MessageBox.OK,
                                    animateTarget: 'mb9',
                                    icon: 'ext-mb-success'
                                });

                                //refresh store
                                s_grid_add_filter_sql_view.load();
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
                }
            }],
            buttons:[{
                text: lang('Close'),
                margin: '5px',
                scale: 'large',
                ui: 's-button',
                cls: 's-grey',
                disabled: false,
                handler: function() {
                    winAddFilterSqlView.close();
                }
            }]
        });

        //show windows
        if (!winAddFilterSqlView.isVisible()) {
            winAddFilterSqlView.center();
            winAddFilterSqlView.show();
        } else {
            winAddFilterSqlView.close();
        }
    }

    function displayShareSetting(SqlvID,ShareName,ShareDescription){
        var s_share_user_list = Ext.create('Ext.data.Store', {
            extend: 'Ext.data.Model',
            fields: ['id', 'name', 'position', 'role'],
            autoLoad: true,
            proxy: {
                type: 'ajax',
                url: m_api + '/report_sql_view/share_user_list',
                reader: {
                    type: 'json',
                    root: 'data'
                }
            },
            listeners: {
                'beforeload': function(store, options) {
                    store.proxy.extraParams.SqlvID = SqlvID;
                }
            }
        });

        var winShareSQLView = Ext.create('widget.window', {
            title: lang('Share SQL View'),
            id: 'winShareSQLView',
            closable: true,
            modal: true,
            closeAction: 'destroy',
            width: '75%',
            height: '75%',
            overflowY: 'auto',
            bodyStyle:{"background-color":"#F0F0F0"},
            style:'background-color:#F0F0F0;',
            padding:6,
            scrollOffset: 20,
            items:[{
                layout: 'column',
                border: false,
                items: [{
                    columnWidth: 1,
                    layout:'form',
                    items: [{
                        layout: {
                            type: 'hbox',
                            align: 'stretch'
                        },
                        items:[{
                            xtype: 'textfield',
                            fieldLabel: lang('Name'),
                            readOnly:true,
                            width:'98%',
                            id: 'ShareSqlViewName',
                            name: 'ShareSqlViewName',
                            value : ShareName
                        }]
                    },{
                        layout: {
                            type: 'hbox',
                            align: 'stretch'
                        },
                        items:[{
                            xtype: 'textfield',
                            fieldLabel: lang('Deskripsi'),
                            readOnly:true,
                            width:'98%',
                            id: 'ShareSqlViewDeskripsi',
                            name: 'ShareSqlViewDeskripsi',
                            value: ShareDescription
                        }]
                    },{
                        layout: 'fit',
                        items:[{
                            xtype:'grid',
                            store: s_share_user_list,
                            width: '98%',
                            height: 500,
                            id: 'grid_share_user_list',
                            style: 'border:1px solid #CCC;margin-top:5px;',
                            loadMask: true,
                            title:lang('Share User'),
                            selType: 'rowmodel',
                            dockedItems: [{
                                xtype: 'toolbar',
                                items: [{
                                    icon: varjs.config.base_url + 'images/icons/new/add.png', cls:'Sfr_BtnGridGreen', overCls:'Sfr_BtnGridGreen-Hover',
                                    hidden: m_act_add,
                                    text: lang('Add'),
                                    scope: this,
                                    handler: function() {
                                        displayPopupSelShareUser(s_share_user_list,SqlvID);
                                    }
                                },{
                                    icon: varjs.config.base_url + 'images/icons/new/delete.png',
                                    hidden: m_act_add,
                                    text: lang('Delete'),
                                    scope: this,
                                    handler: function() {
                                        var sm = Ext.getCmp('grid_share_user_list').getSelectionModel().getSelection()[0];
                                        if(sm != undefined){
                                            Ext.MessageBox.confirm('Message', lang('Are you sure want to delete this data ?') , function(btn){
                                                if(btn == 'yes'){
                                                    Ext.Ajax.request({
                                                        waitMsg: lang('Please Wait'),
                                                        url: m_api + '/report_sql_view/share_user',
                                                        method : 'DELETE',
                                                        params: {id:  sm.get('id')},
                                                        success: function(response, opts){
                                                            var obj = Ext.decode(response.responseText);
                                                            switch(obj.success){
                                                                case true:
                                                                    Ext.MessageBox.alert('Success', obj.message);
                                                                    s_share_user_list.load();
                                                                break;
                                                                default:
                                                                    Ext.MessageBox.show({
                                                                        title: 'Failed',
                                                                        msg: obj.message,
                                                                        buttons: Ext.MessageBox.OK,
                                                                        animateTarget: 'mb9',
                                                                        icon: 'ext-mb-error'
                                                                    });
                                                                break;
                                                            }
                                                        },
                                                        failure: function(response, opts){
                                                            Ext.MessageBox.show({
                                                                title: 'Failed',
                                                                msg: 'Failed to delete data',
                                                                buttons: Ext.MessageBox.OK,
                                                                animateTarget: 'mb9',
                                                                icon: 'ext-mb-error'
                                                            });
                                                        }
                                                    })
                                                }
                                            });
                                        }else{
                                            Ext.MessageBox.show({
                                                title: 'Notifications',
                                                msg: 'No data selected',
                                                buttons: Ext.MessageBox.OK,
                                                animateTarget: 'mb9',
                                                icon: 'ext-mb-info'
                                            });
                                        }
                                    }
                                }]
                            }],
                            columns: [{
                                dataIndex: 'id',
                                hidden: true
                            },{
                                text: 'No',
                                xtype: 'rownumberer',
                                width: '5%'
                            },{
                                text: lang('Name'),
                                width: '50%',
                                dataIndex: 'name'
                            },{
                                text: lang('Position'),
                                width: '30%',
                                dataIndex: 'position'
                            },{
                                text: lang('Role'),
                                width: '15%',
                                dataIndex: 'role'
                            }]
                        }]
                    }]
                }]
            }],
            buttons:[{
                text: lang('Close'),
                margin: '5px',
                scale: 'large',
                ui: 's-button',
                cls: 's-grey',
                disabled: false,
                handler: function() {
                    winShareSQLView.close();
                }
            }]
        });

        //isi data


        //show windows
        if (!winShareSQLView.isVisible()) {
            winShareSQLView.center();
            winShareSQLView.show();
        } else {
            winShareSQLView.close();
        }
    }

    function displayPopupSelShareUser(s_share_user_list,SqlvID){
        var store_sel_share_user = Ext.create('Ext.data.Store', {
            extend: 'Ext.data.Model',
            fields: ['id', 'name', 'position', 'role'],
            autoLoad: true,
            pageSize: 20,
            remoteSort: true,
            proxy: {
                type: 'ajax',
                url: m_api + '/report_sql_view/share_user_list_filter',
                reader: {
                    type: 'json',
                    root: 'data',
                    totalProperty: 'total'
                }
            },
            listeners: {
                'beforeload': function(store, options) {
                    store.proxy.extraParams.filter_name = Ext.getCmp('sSelShareUserName').getValue();
                }
            }
        });

        var winPopupSelShareUser = Ext.create('widget.window', {
            title: lang('Search User'),
            id: 'winPopupSelShareUser',
            closable: true,
            modal: true,
            closeAction: 'destroy',
            width: '75%',
            height: '90%',
            overflowY: 'auto',
            bodyStyle:{"background-color":"#F0F0F0"},
            style:'background-color:#F0F0F0;',
            padding:3,
            items:[{
                xtype: 'gridpanel',
                title: lang('User Search List'),
                id: 'grid_sel_share_user',
                style: 'border:1px solid #CCC;',
                store: store_sel_share_user,
                width: '100%',
                loadMask: true,
                selType: 'rowmodel',
                minHeight:625,
                dockedItems: [{
                    xtype: 'pagingtoolbar',
                    store: store_sel_share_user, // same store GridPanel is using
                    dock: 'bottom',
                    displayInfo: true
                },{
                    xtype: 'toolbar',
                    items: [{
                        name: 'sSelShareUserName',
                        id: 'sSelShareUserName',
                        xtype: 'textfield',
                        width: 200,
                        emptyText: lang('Name'),
                        listeners: {
                            specialkey: function(f,event){
                                if (event.getKey() == event.ENTER) {
                                    store_sel_share_user.load({
                                        params: {
                                            page: 1,
                                            start: 0,
                                            limit: 20
                                        }
                                    });
                                }
                            }
                        }
                    },{
                        xtype: 'button',
                        icon: varjs.config.base_url + 'images/icons/silk/search.png',
                        margin: '0px 0px 0px 6px',
                        text: lang('Search'),
                        handler: function() {
                            store_sel_share_user.load({
                                params: {
                                    page: 1,
                                    start: 0,
                                    limit: 10
                                }
                            });
                        }
                    }]
                }],
                columns: [{
                    dataIndex: 'id',
                    hidden: true
                },{
                    xtype : 'checkcolumn',
                    text : '&nbsp;',
                    dataIndex : 'chdata',
                    width:'3%'
                },{
                    text: 'No',
                    xtype: 'rownumberer',
                    width: '5%'
                },{
                    text: lang('Name'),
                    width: '47%',
                    dataIndex: 'name'
                },{
                    text: lang('Position'),
                    width: '30%',
                    dataIndex: 'position'
                },{
                    text: lang('Role'),
                    width: '15%',
                    dataIndex: 'role'
                }]
            }],
            buttons:[{
                text: 'Insert',
                margin: '5px',
                scale: 'large',
                ui: 's-button',
                cls: 's-blue',
                handler: function () {
                    var records = store_sel_share_user.queryBy(function(record) {
                        return record.get('chdata') === true;
                    });
                    var ids = [];
                    records.each(function(record) {
                        ids.push(record.get('id'));
                    });

                    if(ids.length > 0){
                        //insert kan ke tabel
                        Ext.Ajax.request({
                            url: m_api + '/report_sql_view/insert_share_user',
                            method: 'POST',
                            params: {
                                UserIDs: Ext.encode(ids),
                                SqlvID: SqlvID
                            },
                            success: function(response, o) {
                                Ext.MessageBox.alert('Success', 'User Inserted');
                                s_share_user_list.load();
                            },
                            failure: function(response, o){
                                Ext.MessageBox.show({
                                    title: 'Failed',
                                    msg: Ext.decode(response.responseText),
                                    buttons: Ext.MessageBox.OK,
                                    animateTarget: 'mb9',
                                    icon: 'ext-mb-error'
                                });
                            }
                        });
                    }else{
                        Ext.MessageBox.show({
                            title: 'Notifications',
                            msg: 'No item selected',
                            buttons: Ext.MessageBox.OK,
                            animateTarget: 'mb9',
                            icon: 'ext-mb-info'
                        });
                    }
                }
            },{
                text: lang('Close'),
                margin: '5px',
                scale: 'large',
                ui: 's-button',
                cls: 's-grey',
                disabled: false,
                handler: function() {
                    winPopupSelShareUser.close();
                }
            }]
        });

        //show windows
        if (!winPopupSelShareUser.isVisible()) {
            winPopupSelShareUser.center();
            winPopupSelShareUser.show();
        } else {
            winPopupSelShareUser.close();
        }
    }

});


function testJSON(text){
    try{
        JSON.parse(text);
        return true;
    }
    catch (error){
        return false;
    }
}