/*
* @Author: nikolius
* @Date:   2017-08-10 11:21:23
* @Last Modified by:   nikolius
* @Last Modified time: 2017-11-06 11:36:23
*/

/*
    Param2 yg diperlukan ketika load View ini
    1. MemberID
*/

// Define Variabel2 / Object2 / Fungsi yg diperlukan oleh view ini (begin)

// Define Variabel2 / Object2 / Fungsi yg diperlukan oleh view ini (end)

Ext.define('Koltiva.view.DocumentSurvey.DocumentSurveyPanel' ,{
    extend: 'Ext.panel.Panel',
    id: 'Koltiva.view.DocumentSurvey.DocumentSurveyPanel',
    title: lang('Surveys Document'),
    viewVar: false,
    setViewVar: function(value){
        this.viewVar = value;
    },
    frame: true,
    collapsible:true,
    margin:'0 0 20 8',
    initComponent: function() {
        var thisObj = this;

        //store
        var storeGridDocumentSurveyPanel = Ext.create('Koltiva.store.DocumentSurvey.GridDocumentSurveyPanel', {
            storeVar: {
                MemberID: thisObj.viewVar.MemberID
            }
        });

        //context menu
        var contextMenuGridDocumentSurvey = Ext.create('Ext.menu.Menu',{
            items:[{
                icon: varjs.config.base_url + 'images/icons/silk/printer.png',
                itemId: 'Koltiva.view.DocumentSurvey.DocumentSurveyPanel-contextMenuPrint',
                text: lang('Print Document'),
                handler: function() {
                    var sm = Ext.getCmp('Koltiva.view.DocumentSurvey.DocumentSurveyPanel-gridDocumentSurvey').getSelectionModel().getSelection()[0];
                    var MemberID = thisObj.viewVar.MemberID;

                    switch(sm.get('DocNameID')){
                        case 'ProjBg':
                            var url = m_api + '/document_survey/cetak_proj_background';
                            preview_cetak_surat(url+'/'+MemberID);
                        break;
                        case 'ConNotes':
                            var url = m_api + '/grower/cetak_consent_notes';
                            preview_cetak_surat(url+'/'+MemberID+'/result/');
                        break;
                        case 'RSPODoc':
                            var url = m_api + '/grower/cetak_rspo_document';
                            preview_cetak_surat(url+'/'+MemberID+'/result/');
                        break;
                        case 'Withdrawal':
                            var url = m_api + '/document_survey/cetak_withdrawal_consent_notes';
                            preview_cetak_surat(url+'/'+MemberID+'/result/');
                        break;
                    }
                }
            },{
                icon: varjs.config.base_url + 'images/icons/new/view.png',
                itemId: 'Koltiva.view.DocumentSurvey.DocumentSurveyPanel-contextMenuView',
                text: lang('View File'),
                hidden:true,
                handler: function() {
                    var sm = Ext.getCmp('Koltiva.view.DocumentSurvey.DocumentSurveyPanel-gridDocumentSurvey').getSelectionModel().getSelection()[0];

                    switch(sm.get('DocNameID')){
                        case 'ConNotes':
                            var url = m_api + '/grower/view_consent_notes';

                            if(sm.get('FileAvail') == 'Yes'){
                                preview_cetak_surat(url+'/'+thisObj.viewVar.MemberID);
                            }else{
                                Ext.MessageBox.show({
                                    title: 'Warning',
                                    msg: lang('No File Available'),
                                    buttons: Ext.MessageBox.OK,
                                    animateTarget: 'mb9',
                                    icon: 'ext-mb-info'
                                });
                            }
                        break;
                        case 'Withdrawal':
                            var url = m_api + '/document_survey/view_withdrawal_consent_notes';

                            if(sm.get('FileAvail') == 'Yes'){
                                preview_cetak_surat(url+'/'+thisObj.viewVar.MemberID);
                            }else{
                                Ext.MessageBox.show({
                                    title: 'Warning',
                                    msg: lang('No File Available'),
                                    buttons: Ext.MessageBox.OK,
                                    animateTarget: 'mb9',
                                    icon: 'ext-mb-info'
                                });
                            }
                        break;
                    }

                }
            },{
                icon: varjs.config.base_url + 'images/icons/new/update.png',
                itemId: 'Koltiva.view.DocumentSurvey.DocumentSurveyPanel-contextMenuUpdate',
                text: lang('Update'),
                hidden: true,
                handler: function() {
                    var sm = Ext.getCmp('Koltiva.view.DocumentSurvey.DocumentSurveyPanel-gridDocumentSurvey').getSelectionModel().getSelection()[0];

                    //window form plot survey
                    var winFormUploadDoc = Ext.create('Koltiva.view.DocumentSurvey.WinFormUploadDoc');

                    winFormUploadDoc.setViewVar({
                        MemberID: thisObj.viewVar.MemberID,
                        DocNameID: sm.get('DocNameID'),
                        callerStore: storeGridDocumentSurveyPanel
                    });
                    if (!winFormUploadDoc.isVisible()) {
                        winFormUploadDoc.center();
                        winFormUploadDoc.show();
                    } else {
                        winFormUploadDoc.close();
                    }
                }
            }]
        });

        thisObj.items = [{
            xtype: 'grid',
            id: 'Koltiva.view.DocumentSurvey.DocumentSurveyPanel-gridDocumentSurvey',
            loadMask: true,
            selType: 'rowmodel',
            store: storeGridDocumentSurveyPanel,
            viewConfig: {
                deferEmptyText: false,
                emptyText: GetDefaultContentNoData()
            },
            columns: [{
                text: lang('Action'),
                xtype:'actioncolumn',
                flex: 0.5,
                items:[{
                    icon: varjs.config.base_url + 'images/icons/new/action.png',
                    handler: function(grid, rowIndex, colIndex, item, e, record) {
                        console.log(record);

                        //atur menu mana yg keluar
                        switch(record.data.DocNameID){
                            case 'ProjBg':
                                //contextMenuGridDocumentSurvey.getComponent('Koltiva.view.DocumentSurvey.DocumentSurveyPanel-contextMenuUpdate').setVisible(false);
                                //contextMenuGridDocumentSurvey.getComponent('Koltiva.view.DocumentSurvey.DocumentSurveyPanel-contextMenuView').setVisible(false);
                            break;
                            default:
                                //contextMenuGridDocumentSurvey.getComponent('Koltiva.view.DocumentSurvey.DocumentSurveyPanel-contextMenuUpdate').setVisible(true);
                                //contextMenuGridDocumentSurvey.getComponent('Koltiva.view.DocumentSurvey.DocumentSurveyPanel-contextMenuView').setVisible(true);
                                contextMenuGridDocumentSurvey.getComponent('Koltiva.view.DocumentSurvey.DocumentSurveyPanel-contextMenuPrint').setVisible(true);
                            break;
                        }

                        contextMenuGridDocumentSurvey.showAt(e.getXY());
                    }
                }]
            },{
                text: lang('Document Name'),
                dataIndex: 'DocName',
                flex: 2,
            },{
                dataIndex: 'DocNameID',
                hidden:true
            },{
                dataIndex: 'StatusId',
                hidden:true
            },{
                dataIndex: 'FileAvail',
                hidden:true
            },{
                text: lang('Status'),
                dataIndex: 'Status',
                flex: 1,
            }]
        }];

        this.callParent(arguments);
    }
});