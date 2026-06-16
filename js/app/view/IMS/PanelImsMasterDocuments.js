/*
* @Author: nikolius
* @Date:   2018-07-12 18:06:16
* @Last Modified by:   nikolius
* @Last Modified time: 2018-07-23 13:15:24
*/

/*
    Param2 yg diperlukan ketika load View ini
    - IMSMasterID
*/


Ext.define('Koltiva.view.IMS.PanelImsMasterDocuments' ,{
    extend: 'Ext.tree.Panel',
    id: 'Koltiva.view.IMS.PanelImsMasterDocuments',
    requires: [
        'Ext.data.*',
        'Ext.grid.*',
        'Ext.tree.*'
    ],
    xtype: 'tree-grid',
    title:lang('List of IMS Master Documents'),
    style:'border:1px solid #CCC;',
    cls: 'Sfr_GridNew',
    useArrows: true,
    rootVisible: false,
    multiSelect: false,
    singleExpand: false,
    width: '100%',
    viewConfig: {
        deferEmptyText: false,
        emptyText: lang('No Data Available')
    },
    viewVar: false,
    setViewVar: function(value){
        this.viewVar = value;
    },
    listeners: {
        afterRender: function(){
            var thisObj = this;
        }
    },
    initComponent: function() {
        var thisObj = this;

        //Main Store
        thisObj.store = Ext.create('Koltiva.store.IMS.GridImsMasterDocuments',{
        	storeVar: {
                IMSMasterID: thisObj.viewVar.IMSMasterID
            }
        });

        thisObj.ContextMenuGrid = Ext.create('Ext.menu.Menu', {
            cls: 'Sfr_ConMenu',
            items: [{
                    icon: varjs.config.base_url + 'images/icons/new/book_go.png',
                    text: lang('Download Document (One Drive)'),
                    cls: 'Sfr_BtnConMenuWhite',
                    handler: function () {
                        var sm = thisObj.getSelectionModel().getSelection()[0];

                        if (sm.get('DocumentFilePath') == null) {
                            Ext.MessageBox.show({
                                title: 'Information',
                                msg: lang('No Document File'),
                                buttons: Ext.MessageBox.OK,
                                animateTarget: 'mb9',
                                icon: 'ext-mb-info'
                            });
                        } else {
                            window.open(sm.get('DocumentFilePath'));
                        }
                    }
                }, {
                    icon: varjs.config.base_url + 'images/icons/new/update.png',
                    text: lang('Update Document Check'),
                    cls: 'Sfr_BtnConMenuWhite',
                    hidden: m_act_update,
                    handler: function () {
                        var sm = thisObj.getSelectionModel().getSelection()[0];
                        var WinFormImsMasterDocument = Ext.create('Koltiva.view.IMS.WinFormImsMasterDocument', {
                            viewVar: {
                                IMSMasterID: thisObj.viewVar.IMSMasterID,
                                DocMasID: sm.get('DocMasID'),
                                CallerStore: thisObj.store
                            }
                        });

                        if (!WinFormImsMasterDocument.isVisible()) {
                            WinFormImsMasterDocument.center();
                            WinFormImsMasterDocument.show();
                        } else {
                            WinFormImsMasterDocument.close();
                        }
                    }
                }, {
                    icon: varjs.config.base_url + 'images/icons/new/unlock_document.png',
                    text: lang('Unlock Document'),
                    cls: 'Sfr_BtnConMenuWhite',
                    hidden: true,
                    itemId: 'Koltiva.view.IMS.PanelImsMasterDocuments-CMMenu-UnlockDoc',
                    handler: function () {
                        var sm = thisObj.getSelectionModel().getSelection()[0];

                        Ext.MessageBox.confirm('Message', 'Do you want to unlock this document ?', function (btn) {
                            if (btn == 'yes') {
                                Ext.Ajax.request({
                                    waitMsg: 'Please Wait',
                                    url: m_api + '/ims/ims_documents_master_unlock_document',
                                    method: 'POST',
                                    params: {
                                        IMSMasterID: thisObj.viewVar.IMSMasterID,
                                        DocMasID: sm.get('DocMasID')
                                    },
                                    success: function (response, opts) {
                                        Ext.MessageBox.show({
                                            title: 'Information',
                                            msg: lang('Data updated'),
                                            buttons: Ext.MessageBox.OK,
                                            animateTarget: 'mb9',
                                            icon: 'ext-mb-success'
                                        });

                                        //refresh store
                                        thisObj.store.load();
                                    },
                                    failure: function (response, o) {
                                        var pesanNya;
                                        if (o.result.message != undefined) {
                                            pesanNya = o.result.message;
                                        } else {
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
                }, {
                    icon: varjs.config.base_url + 'images/icons/new/application_view_list.png',
                    text: lang('View Document Remark'),
                    cls: 'Sfr_BtnConMenuWhite',
                    handler: function () {
                        var sm = thisObj.getSelectionModel().getSelection()[0];

                        Ext.MessageBox.show({
                            title: lang('Document Remark'),
                            msg: lang(sm.get('DocumentRemark')),
                            buttons: Ext.MessageBox.OK,
                            animateTarget: 'mb9',
                            icon: 'ext-mb-info'
                        });
                    }
                }, {
                    icon: varjs.config.base_url + 'images/icons/new/bookmark.png',
                    text: lang('Download Template Document'),
                    cls: 'Sfr_BtnConMenuWhite',
                    handler: function () {
                        var sm = thisObj.getSelectionModel().getSelection()[0];

                        if (sm.get('TemplatePath') == null) {
                            Ext.MessageBox.show({
                                title: 'Information',
                                msg: lang('No Template File'),
                                buttons: Ext.MessageBox.OK,
                                animateTarget: 'mb9',
                                icon: 'ext-mb-info'
                            });
                        } else {
                            window.open(m_file + 'template/' + sm.get('TemplatePath'));
                        }
                    }
                }]
        });

        thisObj.columns = [{
                text: lang('Action'),
                xtype: 'actioncolumn',
                width: '5%',
                items: [{
                        icon: varjs.config.base_url + 'images/icons/new/action.png',
                        handler: function (grid, rowIndex, colIndex, item, e, record) {
                            thisObj.ContextMenuGrid.showAt(e.getXY());

                            if (record.data.StatusLockRaw == "1") {
                                if (m_act_update == false) {
                                    thisObj.ContextMenuGrid.items.get('Koltiva.view.IMS.PanelImsMasterDocuments-CMMenu-UnlockDoc').setVisible(true);
                                } else {
                                    thisObj.ContextMenuGrid.items.get('Koltiva.view.IMS.PanelImsMasterDocuments-CMMenu-UnlockDoc').setVisible(false);
                                }
                            } else {
                                thisObj.ContextMenuGrid.items.get('Koltiva.view.IMS.PanelImsMasterDocuments-CMMenu-UnlockDoc').setVisible(false);
                            }
                        },
                        getClass: function (v, meta, rec) {
                            if (rec.data.isCheck == "0") {
                                return 'x-hide-display';
                            }
                        }
                    }]
            }, {
                text: lang('ID'),
                dataIndex: 'DocMasID',
                hidden: true
            }, {
                dataIndex: 'isCheck',
                hidden: true
            }, {
                dataIndex: 'TemplatePath',
                hidden: true
            }, {
                dataIndex: 'DocumentFilePath',
                hidden: true
            }, {
                xtype: 'treecolumn', //this is so we know which column will show the tree
                text: lang('Document Name'),
                flex: 2,
                sortable: true,
                dataIndex: 'DocumentName',
                width: '35%',
                renderer: function (t, meta, record) {
                    var data = record.getData();
                    return lang(data.DocumentName);
                }
            }, {
                dataIndex: 'DocumentRemark',
                hidden: true
            }, {
                text: lang('Status'),
                dataIndex: 'StatusUpload',
                width: '15%',
                renderer: function (t, meta, record) {
                    var data = record.getData();
                    var ReturnNya;

                    if (data.isCheck == "0") {
                        return '';
                    } else {
                        if (data.StatusUpload == '1') {
                            ReturnNya = '<span style="color:green;">' + lang('File uploaded') + '</span>';
                        } else {
                            ReturnNya = '<span style="color:red;">' + lang('No file yet') + '</span>';
                        }
                        return ReturnNya;
                    }
                }
            }, {
                text: lang('Check Date'),
                dataIndex: 'DateCheck',
                width: '15%',
                renderer: function (t, meta, record) {
                    var data = record.getData();

                    if (data.isCheck == "0") {
                        return '';
                    } else {
                        return data.DateCheck;
                    }
                }
            }, {
                text: lang('Locked'),
                dataIndex: 'StatusLock',
                width: '10%'/*,
                 renderer: function (t, meta, record) {
                 }*/
            }, {
                text: lang('Notes'),
                dataIndex: 'Remark',
                width: '19%',
                renderer: function (t, meta, record) {
                    var data = record.getData();

                    if (data.isCheck == "0") {
                        return '';
                    } else {
                        return data.Remark;
                    }
                }
            }];

        this.callParent(arguments);
    }
});