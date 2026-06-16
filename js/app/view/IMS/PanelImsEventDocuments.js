/*
* @Author: nikolius
* @Date:   2018-07-16 13:29:27
* @Last Modified by:   nikolius
* @Last Modified time: 2018-07-23 13:15:19
*/

/*
    Param2 yg diperlukan ketika load View ini
    - IMSID
*/


Ext.define('Koltiva.view.IMS.PanelImsEventDocuments' ,{
    extend: 'Ext.tree.Panel',
    id: 'Koltiva.view.IMS.PanelImsEventDocuments',
    requires: [
        'Ext.data.*',
        'Ext.grid.*',
        'Ext.tree.*'
    ],
    xtype: 'tree-grid',
    title:lang('List of IMS Event Documents'),
    cls: 'Sfr_GridNew',
    style:'border:1px solid #CCC;',
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
        thisObj.store = Ext.create('Koltiva.store.IMS.GridImsEventDocuments',{
        	storeVar: {
                IMSID: thisObj.viewVar.IMSID
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
                    icon: varjs.config.base_url + 'images/icons/new/application_view_list.png',
                    text: lang('View Document Remark'),
                    cls: 'Sfr_BtnConMenuWhite',
                    handler: function () {
                        var sm = thisObj.getSelectionModel().getSelection()[0];

                        Ext.MessageBox.show({
                            title: lang('Document Remark'),
                            msg: sm.get('DocumentRemark'),
                            buttons: Ext.MessageBox.OK,
                            animateTarget: 'mb9',
                            icon: 'ext-mb-info'
                        });
                    }
                }, {
                    icon: varjs.config.base_url + 'images/icons/new/update.png',
                    text: lang('Update Document Check'),
                    cls: 'Sfr_BtnConMenuWhite',
                    hidden: m_act_update,
                    itemId: 'Koltiva.view.IMS.PanelImsEventDocuments-CMGrid-UpdateDocCheck',
                    handler: function () {
                        var sm = thisObj.getSelectionModel().getSelection()[0];
                        var WinFormImsEventDocument = Ext.create('Koltiva.view.IMS.WinFormImsEventDocument', {
                            viewVar: {
                                IMSID: thisObj.viewVar.IMSID,
                                DocEveID: sm.get('DocEveID'),
                                CallerStore: thisObj.store
                            }
                        });

                        if (!WinFormImsEventDocument.isVisible()) {
                            WinFormImsEventDocument.center();
                            WinFormImsEventDocument.show();
                        } else {
                            WinFormImsEventDocument.close();
                        }
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
                            window.open(file_detail + 'template/' + sm.get('TemplatePath'));
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

                            //Hanya didisable ketika sudah IMS Finalization Period completed
                            if (Ext.getCmp('imsStatusImsFinalPeriod').getValue() == "2") {
                                thisObj.ContextMenuGrid.items.get('Koltiva.view.IMS.PanelImsEventDocuments-CMGrid-UpdateDocCheck').disable();
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
                dataIndex: 'DocEveID',
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
                dataIndex: 'DocumentRemark',
                hidden: true
            }, {
                xtype: 'treecolumn', //this is so we know which column will show the tree
                text: lang('Document Name'),
                flex: 2,
                sortable: true,
                dataIndex: 'DocumentName',
                width: '40%'
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
                text: lang('Notes'),
                dataIndex: 'Remark',
                width: '24%',
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