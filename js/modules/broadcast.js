Ext.Loader.setConfig({enabled: true});
Ext.Loader.setPath('js/ext-4.2.0.663', '../ux');
//Ext.Loader.setPath('js/ext-4.2.0.663/ux/form');
Ext.require([
    //'Ext.form.Panel',
    //'Ext.ux.form.MultiSelect',
    'Ext.ux.form.ItemSelector'
]);

function UpdateIssue(IsID) {
    if (Ext.getCmp('bcsNotifID').setValue(IsID)) {
        Ext.get('tcEdit').el.dom.click();
    }
}
function DeleteIssue(IsID) {
    if (Ext.getCmp('bcsNotifID').setValue(IsID)) {
        Ext.get('tcDelete').el.dom.click();
    }
}
function Download(IsID) {
    if (Ext.getCmp('bcsFilePath').setValue(IsID)) {
        Ext.get('tcDownload').el.dom.click();
    }
}
function BackPanel() {
    Ext.getCmp('bcsWinIssue').destroy();
}
function ReplayPanel() {
    Ext.get('tcUpdate').el.dom.click();
}
function ClosePanel() {
    Ext.get('tcClose').el.dom.click();
}


var form_data = {};
Ext.onReady(function () {
    Ext.tip.QuickTipManager.init();
    var selected_role = null;
    var store = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['NotifID', 'Partner', 'NotifMessage', 'Creator', 'DateCreated'],
        autoLoad: true,
        pageSize: 50,
        proxy: {
            type: 'ajax',
            url: m_crud + 'data',
            reader: {
                type: 'json',
                root: 'data',
                totalProperty: 'total'
            }
        },
        listeners: {
            beforeload: function (store, operation) {
                store.proxy.extraParams.key = Ext.getCmp('bcsKey').getValue();
            }
        }
    });

    var issues = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['NotifID', 'Subject', 'IssuesStatus', 'IssuesUpdated', 'UserRealName'],
        autoLoad: true,
        pageSize: 50,
        proxy: {
            type: 'ajax',
            url: m_crud + 'data',
            reader: {
                type: 'json',
                root: 'data',
                totalProperty: 'total'
            }
        },
        listeners: {
            beforeload: function (store, operation) {
                store.proxy.extraParams.key = Ext.getCmp('bcsKey').getValue();
            }
        }
    });

    var files = Ext.create('Ext.data.Store', {
        id: 'StoreFiles',
        extend: 'Ext.data.Model',
        fields: ['FileID', 'FilePath', 'FileName', 'FileSize', 'Creator', 'Creatime'],
        autoLoad: false,
        proxy: {
            type: 'ajax',
            url: m_crud + 'files',
            reader: {
                type: 'json',
                root: 'data'
            }
        },
        listeners: {
            beforeload: function (store, operation) {
                store.proxy.extraParams.NotifID = Ext.getCmp('bcsNotifID').getValue();
                store.proxy.extraParams.FileBundle = Ext.getCmp('bcsFileBundle').getValue();
            }
        }
    });

    var CmbPartnerID = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'label'],
        autoLoad: true,
        // pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_crud + 'combo_partner',
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });

    var issues_priority = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'label'],
        autoLoad: true,
        // pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_crud + 'issues_priority',
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });

    function displayFormWindow(editable) {
        if (!win.isVisible()) {
            //resetForm();
            win.show();
        } else {
            win.hide(this, function () {
            });
            win.toFront();
        }
    }

    function displayIssuesWindow(s_NotifID) {
        var winIssues = Ext.create('widget.window', {
            title: lang('Issue Detail'),
            frame: false,
            closable: false,
            id: 'bcsWinIssue',
            modal: true,
            closeAction: 'show',
            width: '90%',
            height: '90%',
            layout: 'fit',
            items: [{
                    xtype: 'textfield',
                    id: 'bcsFilePath',
                    name: 'FilePath',
                    hidden: true
                }, {
                    xtype: 'panel',
                    height: 500,
                    autoScroll: true,
                    width: 700,
                    id: 'bcsDataIssues',
                    fieldDefaults: {
                        labelAlign: 'left',
                        labelWidth: 130,
                        anchor: '100%'
                    },
                    loader: {
                        url: m_crud + 'issues',
                        params: {NotifID: s_NotifID},
                        //autoLoad: true
                    },
                    buttons: [
                        {
                            text: lang('Delete'),
                            hidden: true,
                            margin: '5px',
                            id: 'bcsDelete',
                            scale: 'large',
                            ui: 's-button',
                            cls: 's-yellow',
                            disabled: false,
                            handler: function () {
                                Ext.MessageBox.confirm('Message', lang('Apakah anda mau menghapus data ini ?'), function (btn) {
                                    if (btn == 'yes') {
                                        Ext.Ajax.request({
                                            waitMsg: lang('Please Wait'),
                                            url: m_crud + 'data',
                                            method: 'DELETE',
                                            params: {NotifID: Ext.getCmp('bcsNotifID').getValue()},
                                            success: function (response, opts) {
                                                var obj = Ext.decode(response.responseText);
                                                switch (obj.success) {
                                                    case true:
                                                        store.load({
                                                            params: {
                                                                key: Ext.getCmp('bcsKey').getValue()
                                                            }
                                                        });
                                                        Ext.getCmp('bcsWinIssue').destroy();
                                                        Ext.getCmp('bcsNotifID').setValue(obj.NotifID);
                                                        displayIssuesWindow(obj.NotifID);
                                                        Ext.MessageBox.alert('Success', obj.message);
                                                        break;
                                                    default:
                                                        Ext.MessageBox.alert('Warning', obj.message);
                                                        break;
                                                }
                                            },
                                            failure: function (response, opts) {
                                                Ext.MessageBox.alert('error', 'Could not connect to the database. Retry later');
                                            }
                                        });
                                    }
                                });
                            }
                        }, {
                            text: lang('Edit'),
                            hidden: true,
                            margin: '5px',
                            id: 'bcsEdit',
                            scale: 'large',
                            ui: 's-button',
                            cls: 's-yellow',
                            disabled: false,
                            handler: function () {
                                Ext.Ajax.request({
                                    url: m_crud + 'detail',
                                    method: 'GET',
                                    params: {NotifID: Ext.getCmp('bcsNotifID').getValue()},
                                    success: function (fp, o) {
                                        var data = Ext.decode(fp.responseText);
                                        if (data.IssuesParent == 0) {
                                            Ext.getCmp("tcSubject").setReadOnly(true);
                                            Ext.getCmp("tcIssuesType").setReadOnly(true);
                                            Ext.getCmp("tcIssuesPriority").setReadOnly(true);
                                        } else {
                                            Ext.getCmp("tcSubject").setReadOnly(false);
                                            Ext.getCmp("tcIssuesType").setReadOnly(false);
                                            Ext.getCmp("tcIssuesPriority").setReadOnly(false);
                                        }
                                        Ext.getCmp('bcsFileBundle').setValue('');
                                        Ext.getCmp('bcsNotifID').setValue(data.NotifID);
                                        Ext.getCmp('bcsIssuesParent').setValue(data.IssuesParent);
                                        Ext.getCmp('bcsSubject').setValue(data.Subject);
                                        Ext.getCmp('bcsIssuesType').setValue(data.IssuesTypeID);
                                        Ext.getCmp('bcsIssuesPriority').setValue(data.IssuesPriorityID);
                                        Ext.getCmp('bcsDescription').setValue(data.Description);
                                        files.load({
                                            params: {
                                                NotifID: Ext.getCmp('bcsNotifID').getValue(),
                                                FileBundle: Ext.getCmp('bcsFileBundle').getValue()
                                            }
                                        });
                                        displayFormWindow(true);
                                    },
                                    failure: function (response, opts) {
                                        Ext.MessageBox.alert('error', 'Could not connect to the database. Retry later');
                                    }
                                });
                            }
                        }, {
                            text: lang('Download'),
                            hidden: true,
                            margin: '5px',
                            id: 'bcsDownload',
                            scale: 'large',
                            ui: 's-button',
                            cls: 's-yellow',
                            disabled: false,
                            handler: function () {
                                //alert(Ext.getCmp('bcsFilePath').getValue());
                                Ext.Ajax.request({
                                    url: m_crud + 'download',
                                    method: 'POST',
                                    params: {File: Ext.getCmp('bcsFilePath').getValue()},
                                    success: function (fp, o) {
                                        var data = Ext.decode(fp.responseText);
                                        if (data.success == "true") {
                                            window.open(m_base_url + data.url);
                                        } else {
                                            Ext.MessageBox.alert('error', 'Could not connect to the database. Retry later');
                                        }
                                    },
                                    failure: function (response, opts) {
                                        Ext.MessageBox.alert('error', 'Could not connect to the database. Retry later');
                                    }
                                });
                            }
                        }, {
                            text: lang('Close Issue'),
                            margin: '5px',
                            hidden: true,
                            id: 'bcsClose',
                            scale: 'large',
                            ui: 's-button',
                            cls: 's-red',
                            disabled: false,
                            handler: function () {
                                Ext.MessageBox.confirm('Message', lang('Apakah anda mau menutup issue ini ?'), function (btn) {
                                    if (btn == 'yes') {
                                        Ext.Ajax.request({
                                            waitMsg: lang('Please Wait'),
                                            url: m_crud + 'close',
                                            method: 'POST',
                                            params: {NotifID: Ext.getCmp('bcsNotifID').getValue()},
                                            success: function (response, opts) {
                                                var obj = Ext.decode(response.responseText);
                                                switch (obj.success) {
                                                    case true:
                                                        store.load({
                                                            params: {
                                                                key: Ext.getCmp('bcsKey').getValue()
                                                            }
                                                        });
                                                        Ext.MessageBox.alert('Success', obj.message);
                                                        break;
                                                    default:
                                                        Ext.MessageBox.alert('Warning', obj.message);
                                                        break;
                                                }
                                            },
                                            failure: function (response, opts) {
                                                Ext.MessageBox.alert('error', 'Could not connect to the database. Retry later');
                                            }
                                        });
                                    }
                                });
                            }
                        }, {
                            text: lang('Update'),
                            margin: '5px',
                            hidden: true,
                            id: 'bcsUpdate',
                            scale: 'large',
                            ui: 's-button',
                            cls: 's-yellow',
                            disabled: false,
                            handler: function () {
                                Ext.Ajax.request({
                                    url: m_crud + 'detail',
                                    method: 'GET',
                                    params: {NotifID: Ext.getCmp('bcsNotifID').getValue()},
                                    success: function (fp, o) {
                                        var data = Ext.decode(fp.responseText);
                                        displayFormWindow(true);
                                        set_form_value(data);
                                    },
                                    failure: function (response, opts) {
                                        Ext.MessageBox.alert('error', 'Could not connect to the database. Retry later');
                                    }
                                });
                            }
                        }, {
                            text: lang('Close'),
                            margin: '5px',
                            hidden: true,
                            scale: 'large',
                            ui: 's-button',
                            cls: 's-grey',
                            disabled: false,
                            handler: function () {
                                winIssues.destroy();
                            }
                        }
                    ]
                }]
        });

        Ext.getCmp('bcsDataIssues').getLoader().load();

        if (!winIssues.isVisible()) {
            winIssues.show();
        } else {
            winIssues.hide(this, function () {
            });
            winIssues.toFront();
        }
    }

    function set_form_value(data) {
        Ext.getCmp('bcsDataForm').getForm().reset();
        if (data) {
            Ext.getCmp('bcsNotifID').setValue(data.NotifID);
            Ext.getCmp('bcsPartnerID').setValue(data.PartnerID);
            Ext.getCmp('bcsMessage').setValue(data.NotifMessage);
        } else {
            Ext.getCmp('bcsNotifID').setValue(data.NotifID);
            Ext.getCmp('bcsPartnerID').setValue(data.PartnerID);
            Ext.getCmp('bcsMessage').setValue(data.NotifMessage);
        }
        if(m_partnerid!='' && m_partnerid!='1' && m_partnerid!='37'){
            Ext.getCmp('bcsPartnerID').setValue(m_partnerid);
            Ext.getCmp('bcsPartnerID').hide();
        }else{
            Ext.getCmp('bcsPartnerID').show();
        }
    }

    var DataForm = Ext.create('Ext.form.Panel', {
        height: 400,
        autoScroll: true,
        width: 700,
        id: 'bcsDataForm',
        enctype: 'multipart/form-data',
        fieldDefaults: {
            labelAlign: 'left',
            labelWidth: 130,
            anchor: '100%'
        },
        items: [{
                xtype: 'hiddenfield',
                name: 'NotifID',
                id: 'bcsNotifID'
            },{
                layout: 'column',
                border: false,
                items: [{
                        columnWidth: .5,
                        layout: 'form',
                        padding: 5,
                        border: false,
                        items: [{
                                xtype: 'combobox',
                                fieldLabel: lang('Partner'),
                                emptyText: lang('Select Partner'),
                                id: 'bcsPartnerID',
                                name: 'PartnerID',
                                store: CmbPartnerID,
                                queryMode: 'local',
                                displayField: 'label',
                                valueField: 'id',
                                allowBlank: false,
                                baseCls: 'Sfr_FormInputMandatory'
                            }]
                    }, {
                        columnWidth: .5,
                        layout: 'form',
                        padding: 5,
                        border: false,
                        items: [{
                                /*xtype: 'combobox',
                                fieldLabel: lang('Priority'),
                                id: 'bcsIssuesPriority',
                                name: 'IssuesPriority',
                                store: issues_priority,
                                allowBlank: false,
                                queryMode: 'local',
                                displayField: 'label',
                                valueField: 'id'*/
                            }]
                    }]
            }, {
                xtype: 'fieldset',
                margin: '5 10 5 10',
                padding: '5',
                title: lang('Message'),
                items: [{
                        xtype: 'htmleditor',
                        id: 'bcsMessage',
                        name: 'Message',
                        padding: '2',
                        enableColors: true,
                        enableAlignments: true,
                        enableSourceEdit: true,
                        enableFont: true,
                        enableFontSize: true,
                        enableFormat: true,
                        enableLinks: true,
                        enableLists: true
                    }]
            }],
        buttons: [{
                id: 'bcsSave',
                text: lang('Save'),
                margin: '5px',
                scale: 'large',
                ui: 's-button',
                cls: 's-blue ',
                handler: function () {
                    var Formnya = Ext.getCmp('bcsDataForm').getForm();

                    if (Formnya.isValid()) {
                        var methode;
                        if (Ext.getCmp('bcsNotifID').getValue() == '') {
                            methode = 'POST';
                        } else {
                            methode = 'PUT';
                        }
                        Ext.Ajax.request({
                            url: m_crud + 'data',
                            method: methode,
                            waitMsg: lang('Sending data...'),
                            params: {
                                NotifID: Ext.getCmp('bcsNotifID').getValue(),
                                PartnerID: Ext.getCmp('bcsPartnerID').getValue(),
                                Message: Ext.getCmp('bcsMessage').getValue(),
                            },
                            success: function (response, opts) {
                                var obj = Ext.decode(response.responseText);
                                if (obj.success == "true") {
                                    store.load({
                                        params: {
                                            key: Ext.getCmp('bcsKey').getValue()
                                        }
                                    });
                                    if(obj.NotifID!=''){
                                        Ext.getCmp('bcsNotifID').setValue(obj.NotifID);
                                    }
                                    Ext.MessageBox.alert('Success', obj.message);
                                    Ext.getCmp('bcsWin').close();
                                } else {
                                    Ext.MessageBox.alert('Warning', obj.message);
                                }
                            },
                            failure: function (response, opts) {
                                Ext.MessageBox.alert('error', 'Could not connect to the database. Retry later');
                            }
                        });
                    } else {
                        Ext.MessageBox.show({
                            title: lang('Attention'),
                            msg: lang('Form not complete yet'),
                            buttons: Ext.MessageBox.OK,
                            animateTarget: 'mb9',
                            icon: 'ext-mb-info'
                        });
                    }
                }
            }, {
                text: lang('Close'),
                margin: '5px',
                scale: 'large',
                ui: 's-button',
                cls: 's-grey',
                disabled: false,
                handler: function () {
                    win.hide();
                    /*store_training.load({
                     params: {
                     cpg_id: Ext.getCmp('id').getValue()
                     }
                     });*/
                }
            }]
    });

    var win = Ext.create('widget.window', {
        title: lang('Form Broadcast Message'),
        frame: false,
        closable: true,
        id: 'bcsWin',
        modal: true,
        closeAction: 'show',
        width: '70%',
        height: 400,
        layout: 'fit',
        items: [DataForm]
    });

    var DataIssues = Ext.create('Ext.form.Panel', {
    });



    function submitOnEnter(field, event) {
        if (event.getKey() == event.ENTER) {
            filterRecord();
        }
    }

    function filterRecord() {
        store.load({
            params: {
                start: 0,
                key: Ext.getCmp('bcsKey').getValue()
            }
        });
    }

    function displayAddWindowContact() {
        if (!winAddContact.isVisible()) {

            winAddContact.show();
        } else {
            winAddContact.hide(this, function () {
            });
            winAddContact.toFront();
        }
    }

    var contextMenuGrid = Ext.create('Ext.menu.Menu', {
        items: [
            {
                icon: varjs.config.base_url + 'images/icons/new/update.png',
                text: lang('Update'),
                //hidden: !m_act_update,
                handler: function () {
                    var sm = Ext.getCmp('bcsgrid').getSelectionModel().getSelection()[0];
                    Ext.getCmp('bcsNotifID').setValue(sm.raw.NotifID);
                    Ext.Ajax.request({
                        url: m_crud + 'detail',
                        method: 'GET',
                        params: {NotifID: sm.raw.NotifID},
                        success: function (fp, o) {
                            var data = Ext.decode(fp.responseText);
                            displayFormWindow(true);
                            set_form_value(data);
                        },
                        failure: function (response, opts) {
                            Ext.MessageBox.alert('error', 'Could not connect to the database. Retry later');
                        }
                    });
                }
            },
            {
                icon: varjs.config.base_url + 'images/icons/new/delete.png',
                text: lang('Delete'),
                //hidden: !m_act_delete,
                handler: function () {
                    var smb = Ext.getCmp('bcsgrid').getSelectionModel().getSelection()[0];
                    Ext.MessageBox.confirm('Message', lang('Apakah anda mau menghapus data ini ?'), function (btn) {
                        if (btn == 'yes') {
                            Ext.Ajax.request({
                                waitMsg: lang('Please Wait'),
                                url: m_crud + 'data',
                                method: 'DELETE',
                                params: {NotifID: smb.raw.NotifID},
                                success: function (response, opts) {
                                    var obj = Ext.decode(response.responseText);
                                    switch (obj.success) {
                                        case true:
                                            store.load({
                                                params: {
                                                    key: Ext.getCmp('bcsKey').getValue()
                                                }
                                            });
                                            Ext.MessageBox.alert('Success', obj.message);
                                            break;
                                        default:
                                            Ext.MessageBox.alert('Warning', obj.message);
                                            break;
                                    }
                                },
                                failure: function (response, opts) {
                                    Ext.MessageBox.alert('error', 'Could not connect to the database. Retry later');
                                }
                            });
                        }
                    });
                }
            }
        ]
    });

    var contextMenuFileGrid = Ext.create('Ext.menu.Menu', {
        items: [
            {
                icon: varjs.config.base_url + 'images/icons/new/delete.png',
                text: lang('Delete'),
                //hidden: !m_act_delete,
                handler: function () {
                    var smb = Ext.getCmp('bcsFilesPanel').getSelectionModel().getSelection()[0];
                    Ext.MessageBox.confirm('Message', lang('Apakah anda mau menghapus data ini ?'), function (btn) {
                        if (btn == 'yes') {
                            Ext.Ajax.request({
                                waitMsg: lang('Please Wait'),
                                url: m_crud + 'file',
                                method: 'DELETE',
                                params: {
                                    FileID: smb.raw.FileID,
                                    FilePath: smb.raw.FilePath
                                },
                                success: function (response, opts) {
                                    var obj = Ext.decode(response.responseText);
                                    switch (obj.success) {
                                        case true:
                                            files.load({
                                                params: {
                                                    NotifID: Ext.getCmp('bcsNotifID').getValue(),
                                                    FileBundle: Ext.getCmp('bcsFileBundle').getValue()
                                                }
                                            });
                                            Ext.MessageBox.alert('Success', obj.message);
                                            break;
                                        default:
                                            Ext.MessageBox.alert('Warning', obj.message);
                                            break;
                                    }
                                },
                                failure: function (response, opts) {
                                    Ext.MessageBox.alert('error', 'Could not connect to the database. Retry later');
                                }
                            });
                        }
                    });
                }
            }
        ]
    });

    var grid = Ext.create('Ext.grid.Panel', {
        store: store,
        width: '100%',
        id: 'bcsgrid',
        minHeight: 250,
        //title: 'User List',
        style: 'border:1px solid #CCC;',
        renderTo: 'ext-content',
        loadMask: true,
        selType: 'rowmodel',
        listeners: {
            itemclick: function (view, record, item, index, e) {
                
            }
        },
        dockedItems: [{
                xtype: 'pagingtoolbar',
                store: store, // same store GridPanel is using
                dock: 'bottom',
                displayInfo: true
            }, {
                xtype: 'toolbar',
                items: [
                    {
                        icon: varjs.config.base_url + 'images/icons/new/add.png', cls:'Sfr_BtnGridGreen', overCls:'Sfr_BtnGridGreen-Hover',
                        text: 'Add',
                        scope: this,
                        handler: function () {
                            displayFormWindow(true);
                            set_form_value(false);
                        },
                        //cls: m_act_add?'':'hidden'
                    },
                    {
                        xtype: 'textfield',
                        emptyText: lang('Keyword'),
                        name: 'tcKey',
                        id: 'bcsKey',
                        listeners: {
                            specialkey: submitOnEnter
                        }
                    },
                    {
                        xtype: 'button',
                        margin: '0px 0px 0px 6px',
                        text: 'Search',
                        handler: function () {
                            filterRecord();
                            //alert(store.currentPage);
                        }
                    }]
            }],
        columns: [
            {
                text: 'ID',
                dataIndex: 'NotifID',
                hidden: true
            },{
                text: lang('Action'),
                xtype:'actioncolumn',
                width:70,
                items:[{
                    icon: varjs.config.base_url + 'images/icons/new/action.png',
                    tooltip: 'Action',
                    handler: function(grid, rowIndex, colIndex, item, e, record) {
                        contextMenuGrid.showAt(e.getXY());
                    }
                }]
            },{
                text: 'No',
                xtype: 'rownumberer',
                width:'5%'
            },
            {
                text: lang('Partner'),
                flex: 1,
                dataIndex: 'Partner'
            },
            {
                text: lang('Message'),
                flex: 3,
                dataIndex: 'NotifMessage'
            },
            {
                text: lang('Create BY'),
                flex: 1,
                dataIndex: 'Creator'
            },
            {
                text: lang('Date'),
                flex: 1,
                dataIndex: 'DateCreated'
            }]
    });
});
