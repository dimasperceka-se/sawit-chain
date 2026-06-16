Ext.Loader.setConfig({enabled: true});
Ext.Loader.setPath('js/ext-4.2.0.663', '../ux');
//Ext.Loader.setPath('js/ext-4.2.0.663/ux/form');
Ext.require([
    //'Ext.form.Panel',
    //'Ext.ux.form.MultiSelect',
    'Ext.ux.form.ItemSelector'
]);

function UpdateIssue(IsID) {
    if (Ext.getCmp('nwNewsID').setValue(IsID)) {
        Ext.get('tcEdit').el.dom.click();
    }
}
function DeleteIssue(IsID) {
    if (Ext.getCmp('nwNewsID').setValue(IsID)) {
        Ext.get('tcDelete').el.dom.click();
    }
}
function Download(IsID) {
    if (Ext.getCmp('nwFilePath').setValue(IsID)) {
        Ext.get('tcDownload').el.dom.click();
    }
}
function BackPanel() {
    Ext.getCmp('nwWinIssue').destroy();
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
        fields: ['NewsID', 'PartnerName', 'Title', 'StatusNews', 'PublishDate', 'Creator'],
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
                store.proxy.extraParams.key = Ext.getCmp('nwKey').getValue();
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

    var CmbStatusNews = Ext.create('Ext.data.Store', {
        fields: ['id','label'],
        data: [{
            "id": 'draft',
            "label": lang("Draft")
        }, {
            "id":'publish',
            "label": lang("Publish")
        }]
    });

    var issues = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['NewsID', 'Subject', 'IssuesStatus', 'IssuesUpdated', 'UserRealName'],
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
                store.proxy.extraParams.key = Ext.getCmp('nwKey').getValue();
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
                store.proxy.extraParams.NewsID = Ext.getCmp('nwNewsID').getValue();
                store.proxy.extraParams.FileBundle = Ext.getCmp('nwFileBundle').getValue();
            }
        }
    });

    var issues_type = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'label'],
        autoLoad: true,
        // pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_crud + 'issues_type',
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

    function displayIssuesWindow(s_NewsID) {
        var winIssues = Ext.create('widget.window', {
            title: lang('Issue Detail'),
            frame: false,
            closable: false,
            id: 'nwWinIssue',
            modal: true,
            closeAction: 'show',
            width: '90%',
            height: '90%',
            layout: 'fit',
            items: [{
                    xtype: 'textfield',
                    id: 'nwFilePath',
                    name: 'FilePath',
                    hidden: true
                }, {
                    xtype: 'panel',
                    height: 500,
                    autoScroll: true,
                    width: 700,
                    id: 'nwDataIssues',
                    fieldDefaults: {
                        labelAlign: 'left',
                        labelWidth: 130,
                        anchor: '100%'
                    },
                    loader: {
                        url: m_crud + 'issues',
                        params: {NewsID: s_NewsID},
                        //autoLoad: true
                    },
                    buttons: [
                        {
                            text: lang('Delete'),
                            hidden: true,
                            margin: '5px',
                            id: 'nwDelete',
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
                                            params: {NewsID: Ext.getCmp('nwNewsID').getValue()},
                                            success: function (response, opts) {
                                                var obj = Ext.decode(response.responseText);
                                                switch (obj.success) {
                                                    case true:
                                                        store.load({
                                                            params: {
                                                                key: Ext.getCmp('nwKey').getValue()
                                                            }
                                                        });
                                                        Ext.getCmp('nwWinIssue').destroy();
                                                        Ext.getCmp('nwNewsID').setValue(obj.NewsID);
                                                        displayIssuesWindow(obj.NewsID);
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
                            id: 'nwEdit',
                            scale: 'large',
                            ui: 's-button',
                            cls: 's-yellow',
                            disabled: false,
                            handler: function () {
                                Ext.Ajax.request({
                                    url: m_crud + 'detail',
                                    method: 'GET',
                                    params: {NewsID: Ext.getCmp('nwNewsID').getValue()},
                                    success: function (fp, o) {
                                        var data = Ext.decode(fp.responseText);
                                        if (data.IssuesParent == 0) {
                                            Ext.getCmp("tcSubject").setReadOnly(true);
                                            Ext.getCmp("tcPartnerID").setReadOnly(true);
                                            Ext.getCmp("tcIssuesPriority").setReadOnly(true);
                                        } else {
                                            Ext.getCmp("tcSubject").setReadOnly(false);
                                            Ext.getCmp("tcPartnerID").setReadOnly(false);
                                            Ext.getCmp("tcIssuesPriority").setReadOnly(false);
                                        }
                                        Ext.getCmp('nwFileBundle').setValue('');
                                        Ext.getCmp('nwNewsID').setValue(data.NewsID);
                                        Ext.getCmp('nwIssuesParent').setValue(data.IssuesParent);
                                        Ext.getCmp('nwSubject').setValue(data.Subject);
                                        Ext.getCmp('nwPartnerID').setValue(data.PartnerID);
                                        Ext.getCmp('nwIssuesPriority').setValue(data.IssuesPriorityID);
                                        Ext.getCmp('nwDescription').setValue(data.Description);
                                        files.load({
                                            params: {
                                                NewsID: Ext.getCmp('nwNewsID').getValue(),
                                                FileBundle: Ext.getCmp('nwFileBundle').getValue()
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
                            id: 'nwDownload',
                            scale: 'large',
                            ui: 's-button',
                            cls: 's-yellow',
                            disabled: false,
                            handler: function () {
                                //alert(Ext.getCmp('nwFilePath').getValue());
                                Ext.Ajax.request({
                                    url: m_crud + 'download',
                                    method: 'POST',
                                    params: {File: Ext.getCmp('nwFilePath').getValue()},
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
                            id: 'nwClose',
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
                                            params: {NewsID: Ext.getCmp('nwNewsID').getValue()},
                                            success: function (response, opts) {
                                                var obj = Ext.decode(response.responseText);
                                                switch (obj.success) {
                                                    case true:
                                                        store.load({
                                                            params: {
                                                                key: Ext.getCmp('nwKey').getValue()
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
                            id: 'nwUpdate',
                            scale: 'large',
                            ui: 's-button',
                            cls: 's-yellow',
                            disabled: false,
                            handler: function () {
                                Ext.Ajax.request({
                                    url: m_crud + 'detail',
                                    method: 'GET',
                                    params: {NewsID: Ext.getCmp('nwNewsID').getValue()},
                                    success: function (fp, o) {
                                        var dt = new Date();
                                        var Filebundle = m_user + '_' + Ext.Date.format(dt, 'YmdHis');
                                        Ext.getCmp('nwFileBundle').setValue(Filebundle);
                                        var data = Ext.decode(fp.responseText);
                                        Ext.getCmp('nwNewsID').setValue('');
                                        Ext.getCmp("tcSubject").setReadOnly(true);
                                        Ext.getCmp("tcPartnerID").setReadOnly(true);
                                        Ext.getCmp("tcIssuesPriority").setReadOnly(true);
                                        Ext.getCmp('nwIssuesParent').setValue(data.NewsID);
                                        Ext.getCmp('nwSubject').setValue(data.Subject);
                                        Ext.getCmp('nwPartnerID').setValue(data.PartnerID);
                                        Ext.getCmp('nwIssuesPriority').setValue(data.IssuesPriorityID);
                                        Ext.getCmp('nwDescription').setValue('');
                                        files.clearData();
                                        files.removeAll();
                                        displayFormWindow(true);
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

        Ext.getCmp('nwDataIssues').getLoader().load();

        if (!winIssues.isVisible()) {
            winIssues.show();
        } else {
            winIssues.hide(this, function () {
            });
            winIssues.toFront();
        }
    }

    function set_form_value(data) {
        form_data = data;
        Ext.getCmp('nwDataForm').getForm().reset();
        if (data) {
            Ext.getCmp('nwNewsID').setValue(data.NewsID);
            Ext.getCmp('nwImageName').setValue(data.ImageName);
            Ext.getCmp('nwImagePath').setValue(data.ImagePath);
            Ext.getCmp('nwImageShow').setValue(data.ImageShow);
            Ext.getCmp('nwImageShow2').setSrc(m_base_url + '/files/news/' + data.ImageShow);
            Ext.getCmp('nwPartnerID').setValue(data.PartnerID);
            Ext.getCmp('nwStatusNews').setValue(data.StatusNews);
            Ext.getCmp('nwPublishDate').setValue(data.PublishDate);
            Ext.getCmp('nwTitle').setValue(data.Title);
            Ext.getCmp('nwContent').setValue(data.Content);
            Ext.getCmp('OpsiDisplay').setValue('update');
        } else {
            Ext.getCmp('nwNewsID').setValue();
            Ext.getCmp('nwImageName').setValue();
            Ext.getCmp('nwImagePath').setValue();
            Ext.getCmp('nwImageShow').setValue();
            Ext.getCmp('nwImageShow2').setSrc(m_base_url + '/images/nursery/no-image.png');
            Ext.getCmp('nwPartnerID').setValue();
            Ext.getCmp('nwStatusNews').setValue();
            Ext.getCmp('nwPublishDate').setValue(Ext.Date.format( new Date(), 'Y-m-d'));
            Ext.getCmp('nwTitle').setValue();
            Ext.getCmp('nwContent').setValue();
            Ext.getCmp('OpsiDisplay').setValue('insert');
        }
        if(m_partnerid!='' && m_partnerid!='1' && m_partnerid!='37'){
            Ext.getCmp('bcsPartnerID').setValue(m_partnerid);
            Ext.getCmp('bcsPartnerID').hide();
        }else{
            Ext.getCmp('bcsPartnerID').show();
        }
    }

    var DataForm = Ext.create('Ext.form.Panel', {
        height: 500,
        autoScroll: true,
        width: 700,
        id: 'nwDataForm',
        fileUpload: true,
        enctype: 'multipart/form-data',
        fieldDefaults: {
            labelAlign: 'left',
            labelWidth: 130,
            anchor: '100%'
        },
        items: [{
                layout: 'column',
                border: false,
                items: [{
                        xtype: 'hiddenfield',
                        name: 'NewsID',
                        id: 'nwNewsID'
                    },{
                        xtype: 'hiddenfield',
                        name: 'ImagePath',
                        id: 'nwImagePath'
                    },{
                        xtype: 'hiddenfield',
                        name: 'ImageName',
                        id: 'nwImageName'
                    },{
                        xtype: 'hiddenfield',
                        name: 'ImageShow',
                        id: 'nwImageShow'
                    },{
                        xtype: 'hiddenfield',
                        name: 'OpsiDisplay',
                        id: 'OpsiDisplay'
                    },{
                        columnWidth: .5,
                        layout: 'form',
                        padding: 5,
                        border: false,
                        items: [{
                                xtype: 'combobox',
                                fieldLabel: lang('Partner'),
                                id: 'nwPartnerID',
                                name: 'PartnerID',
                                store: CmbPartnerID,
                                allowBlank: false,
                                queryMode: 'local',
                                displayField: 'label',
                                valueField: 'id'
                            }]
                    }, {
                        columnWidth: .5,
                        layout: 'form',
                        padding: 5,
                        border: false,
                        items: [{
                                
                            }]
                    }]
            }, {
                layout: 'column',
                border: false,
                items: [{
                        columnWidth: .5,
                        layout: 'form',
                        padding: 5,
                        border: false,
                        items: [{
                                xtype: 'combobox',
                                fieldLabel: lang('Status'),
                                id: 'nwStatusNews',
                                name: 'StatusNews',
                                store: CmbStatusNews,
                                allowBlank: false,
                                queryMode: 'local',
                                displayField: 'label',
                                valueField: 'id'
                            }]
                    }, {
                        columnWidth: .5,
                        layout: 'form',
                        padding: 5,
                        border: false,
                        items: [{
                            xtype: 'datefield',
                            format: 'Y-m-d',
                            fieldLabel: lang('Publish Date'),
                            id: 'nwPublishDate',
                            name: 'PublishDate',
                            emptyText: lang('Publish Date'),
                            value:  Ext.Date.format( new Date(), 'Y-m-d'),
                            minValue: new Date(),
                            padding: 5
                        }]
                    }]
            }, {
                xtype: 'fieldset',
                margin: '5 10 5 10',
                padding: '5',
                title: lang('Image'),
                items: [{
                        layout: 'column',
                        border: false,
                        items: [{
                                columnWidth: .2,
                                layout: 'form',
                                padding: 5,
                                border: false,
                                items: [{
                                        xtype: 'fileuploadfield',
                                        id: 'nwFiles',
                                        padding: 5,
                                        name: 'File',
                                        buttonText: 'Browse',
                                        listeners: {
                                            'change': function (fb, v) {
                                                var form = Ext.getCmp('nwDataForm').getForm();
                                                form.submit({
                                                    url: m_crud + 'upload',
                                                    method: 'POST',
                                                    waitMsg: 'Sending File...',
                                                    success: function (fp, o) {
                                                        if (Ext.getCmp('nwPartnerID').getValue() == '' || Ext.getCmp('nwStatusNews').getValue() == '') {
                                                            Ext.MessageBox.alert('error', 'Please complete the form first.');
                                                        }
                                                        if (o.result.status == "true") {
                                                            Ext.getCmp('nwImageName').setValue(o.result.ImageName);
                                                            Ext.getCmp('nwImagePath').setValue(o.result.ImagePath);
                                                            Ext.getCmp('nwImageShow').setValue(o.result.ImageShow);
                                                            Ext.getCmp('nwImageShow2').setSrc(m_base_url + '/files/news/' + o.result.ImageShow);
                                                        }
                                                        Ext.MessageBox.alert(o.result.infos, o.result.message);
                                                    },
                                                    failure: function (fp, o) {
                                                        if (Ext.getCmp('nwPartnerID').getValue() == '' || Ext.getCmp('nwStatusNews').getValue() == '') {
                                                            Ext.MessageBox.alert('error', 'Please complete the form first.');
                                                        }
                                                        if (o.result.status == "true") {
                                                            Ext.getCmp('nwImageName').setValue(o.result.ImageName);
                                                            Ext.getCmp('nwImagePath').setValue(o.result.ImagePath);
                                                            Ext.getCmp('nwImageShow').setValue(o.result.ImageShow);
                                                            Ext.getCmp('nwImageShow2').setSrc(m_base_url + '/files/news/' + o.result.ImageShow);
                                                        }
                                                        Ext.MessageBox.alert(o.result.infos, o.result.message);

                                                    },

                                                });
                                            }
                                        }
                                    }]
                            }, {
                                columnWidth: .7,
                                layout: 'form',
                                padding: 5,
                                border: false,
                                items: [{
                                    xtype: 'image',
                                    id: 'nwImageShow2',
                                    width: '150px',
                                    height:'150px',
                                    src: m_base_url + '/images/nursery/no-image.png'
                                }]
                            }]
                    }]
            }, {
                xtype: 'fieldset',
                margin: '5 10 5 10',
                padding: '5',
                title: lang('Title'),
                items: [{
                        xtype: 'textfield',
                        id: 'nwTitle',
                        allowBlank:false,
                        padding: '2',
                        name: 'Title'
                    }]
            }, {
                xtype: 'fieldset',
                margin: '5 10 5 10',
                padding: '5',
                title: lang('Content'),
                items: [{
                    xtype: 'htmleditor',
                    id: 'nwContent',
                    name: 'Content',
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
                id: 'nwSave',
                text: lang('Save'),
                margin: '5px',
                scale: 'large',
                ui: 's-button',
                cls: 's-blue ',
                handler: function () {
                    var methode;
                    if (Ext.getCmp('nwNewsID').getValue() == '') {
                        methode = 'POST';
                    } else {
                        methode = 'PUT';
                    }
                    if(DataForm.isValid()){
                        Ext.MessageBox.show({
                            msg: lang('Please wait')+'......',
                            progressText: lang('Sending Data')+'...',
                            width: 300,
                            wait: true,
                            waitConfig: {
                                interval: 200
                            },
                            icon: 'ext-mb-download', //custom class in msg-box.html
                            animateTarget: 'mb7'
                        });
                        Ext.Ajax.request({
                            url: m_crud + 'data',
                            method: methode,
                            waitMsg: lang('Sending data...'),
                            params: {
                                NewsID: Ext.getCmp('nwNewsID').getValue(),
                                PartnerID: Ext.getCmp('nwPartnerID').getValue(),
                                StatusNews: Ext.getCmp('nwStatusNews').getValue(),
                                PublishDate: Ext.getCmp('nwPublishDate').getValue(),
                                ImageName: Ext.getCmp('nwImageName').getValue(),
                                ImagePath: Ext.getCmp('nwImagePath').getValue(),
                                ImageShow: Ext.getCmp('nwImageShow').getValue(),
                                Title: Ext.getCmp('nwTitle').getValue(),
                                Content: Ext.getCmp('nwContent').getValue()
                            },
                            success: function (response, opts) {
                                var obj = Ext.decode(response.responseText);
                                if (obj.success == "true") {
                                    store.load({
                                        params: {
                                            key: Ext.getCmp('nwKey').getValue()
                                        }
                                    });
                                    if(obj.NewsID!=''){
                                        Ext.getCmp('nwNewsID').setValue(obj.NewsID);
                                    }
                                    Ext.MessageBox.alert('Success', obj.message);
                                    Ext.getCmp('nwWin').close();
                                } else {
                                    Ext.MessageBox.alert('Warning', obj.message);
                                }
                            },
                            failure: function (response, opts) {
                                Ext.MessageBox.alert('error', 'Could not connect to the database. Retry later');
                            }
                        });
                    }else{
                        Ext.MessageBox.show({
                            title: 'Attention',
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
                }
            }]
    });

    var win = Ext.create('widget.window', {
        title: lang('Form News'),
        frame: false,
        closable: true,
        id: 'nwWin',
        modal: true,
        closeAction: 'show',
        width: '70%',
        height: '70%',
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
                key: Ext.getCmp('nwKey').getValue()
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
                    var sm = Ext.getCmp('nwgrid').getSelectionModel().getSelection()[0];
                    Ext.getCmp('nwNewsID').setValue(sm.raw.NewsID);
                    Ext.Ajax.request({
                        url: m_crud + 'detail',
                        method: 'GET',
                        params: {NewsID: sm.raw.NewsID},
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
                    var smb = Ext.getCmp('nwgrid').getSelectionModel().getSelection()[0];
                    Ext.MessageBox.confirm('Message', lang('Do you want to delete this data?'), function (btn) {
                        if (btn == 'yes') {
                            Ext.Ajax.request({
                                waitMsg: lang('Please Wait'),
                                url: m_crud + 'data',
                                method: 'DELETE',
                                params: {NewsID: smb.raw.NewsID},
                                success: function (response, opts) {
                                    var obj = Ext.decode(response.responseText);
                                    switch (obj.success) {
                                        case true:
                                            store.load({
                                                params: {
                                                    key: Ext.getCmp('nwKey').getValue()
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
                    var smb = Ext.getCmp('nwFilesPanel').getSelectionModel().getSelection()[0];
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
                                                    NewsID: Ext.getCmp('nwNewsID').getValue(),
                                                    FileBundle: Ext.getCmp('nwFileBundle').getValue()
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
        id: 'nwgrid',
        minHeight: 250,
        //title: 'User List',
        style: 'border:1px solid #CCC;',
        renderTo: 'ext-content',
        loadMask: true,
        selType: 'rowmodel',
        listeners: {
            itemclick: function (view, record, item, index, e) {
                //contextMenuGrid.showAt(e.getXY());
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
                        id: 'nwKey',
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
                dataIndex: 'NewsID',
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
                dataIndex: 'PartnerName'
            },
            {
                text: lang('Title'),
                flex: 2,
                dataIndex: 'Title'
            },
            {
                text: lang('Status'),
                flex: 1,
                dataIndex: 'StatusNews'
            },
            {
                text: lang('Publish Date'),
                flex: 1,
                dataIndex: 'PublishDate'
            },
            {
                text: lang('Created By'),
                flex: 1,
                dataIndex: 'Creator'
            }]
    });
});
