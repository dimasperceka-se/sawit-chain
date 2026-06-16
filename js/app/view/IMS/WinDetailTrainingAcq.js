/*
* @Author: nikolius
* @Date:   2018-03-19 16:26:05
* @Last Modified by:   nikolius
* @Last Modified time: 2018-06-05 14:05:26
*/

/*
    Param2 yg diperlukan ketika load View ini
    - IMSID
    - FarmerID
    - ApplicantDisplayID
    - CallerStore
*/

Ext.define('Koltiva.view.IMS.WinDetailTrainingAcq' ,{
    extend: 'Ext.window.Window',
    id: 'Koltiva.view.IMS.WinDetailTrainingAcq',
    title: lang('IMS - Training GAP & CoC Detail'),
    closable: true,
    modal: true,
    closeAction: 'destroy',
    width: '85%',
    height: '80%',
    overflowY: 'auto',
    viewVar: false,
    setViewVar: function(value){
        this.viewVar = value;
    },
    initComponent: function() {
        var thisObj = this;

        var store_training_info = Ext.create('Koltiva.store.IMS.GridDetailTrainingInfo', {
            storeVar: {
                IMSID: thisObj.viewVar.IMSID,
                FarmerID: thisObj.viewVar.FarmerID
            }
        });

        thisObj.items = [{
            xtype: 'form',
            id: 'Koltiva.view.IMS.WinDetailTrainingAcq-Form',
            fileUpload: true,
            padding:'5 25 5 8',
            items:[{
                layout: 'column',
                border: false,
                items:[{
                    columnWidth: 1,
                    layout:'form',
                    items:[{
                    	xtype: 'gridpanel',
	                    title: lang('Training Information'),
	                    id: 'Koltiva.view.IMS.WinDetailTrainingAcq-Grid',
	                    style: 'border:1px solid #CCC;',
                            cls: 'Sfr_GridNew',
	                    store: store_training_info,
	                    width: '99%',
	                    loadMask: true,
	                    selType: 'rowmodel',
	                    viewConfig: {
	                        deferEmptyText: false,
	                        emptyText: lang('No data Available')
	                    },
	                    columns: [{
	                    	dataIndex: 'CpgBatchTrainingID',
			                hidden: true
	                    },{
	                    	text: lang('Topic'),
                                    flex: 1,
		                    dataIndex: 'Topic'
	                    },{
	                    	text: lang('Batch Number'),
		                    width: '12%',
		                    dataIndex: 'BatchNumber'
	                    },{
	                    	text: lang('Start'),
		                    width: '9%',
		                    dataIndex: 'Start'
	                    },{
	                    	text: lang('End'),
		                    width: '9%',
		                    dataIndex: 'End'
	                    },{
	                    	text: lang('Attendance Percentage')+' (%)',
		                    width: '24%',
		                    dataIndex: 'AttendancePercentage'
	                    }]
                    }]
                }]
            }]
        }];

        thisObj.buttons = [{
                text: lang('Process to Candidate'),
                id: 'Koltiva.view.IMS.WinDetailTrainingAcq-Form-BtnSave',
                margin: '5px',
                cls: 'Sfr_BtnFormBlue',
                overCls: 'Sfr_BtnFormBlue-Hover',
                hidden: true,
                handler: function () {

                    Ext.Ajax.request({
                        waitMsg: 'Please Wait',
                        url: m_api + '/ims/acq_process_to_candidate',
                        method: 'POST',
                        params: {
                            IMSID: thisObj.viewVar.IMSID,
                            FarmerID: thisObj.viewVar.FarmerID,
                        },
                        success: function (response, opts) {
                            var r = Ext.decode(response.responseText);
                            //console.log(r);

                            Ext.MessageBox.show({
                                title: lang('Success'),
                                msg: lang(r.message),
                                buttons: Ext.MessageBox.OK,
                                animateTarget: 'mb9',
                                icon: 'ext-mb-success'
                            });

                            thisObj.viewVar.CallerStore.load();
                            thisObj.close();
                        },
                        failure: function (response, o) {
                            var r = Ext.decode(response.responseText);
                            //console.log(r);

                            var pesanNya;
                            if (r.message != undefined) {
                                pesanNya = r.message;
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
            }, {
                margin: '5px',
                icon: varjs.config.base_url + 'images/icons/new/close.png',
                text: lang('Close'),
                cls: 'Sfr_BtnFormGrey',
                overCls: 'Sfr_BtnFormGrey-Hover',
                handler: function () {
                    thisObj.close();
                }
            }];

        this.callParent(arguments);
    },
    listeners: {
        afterRender: function(){
            var thisObj = this;

            //Cek apakah Training sudah di approve
            Ext.Ajax.request({
                url: m_api + '/ims/cek_acq_training_approval',
                method: 'GET',
                params: {
                    IMSID: thisObj.viewVar.IMSID
                },
                success: function(response, action) {
                    //console.log(response);
                    var objReturn = Ext.decode(response.responseText);
                    if(objReturn.TrainStatus != undefined){
                        if(objReturn.TrainStatus == "1"){
                            Ext.getCmp('Koltiva.view.IMS.WinDetailTrainingAcq-Form-BtnSave').setVisible(false);
                        }
                    }
                },
                failure: function(response, action){
                    Ext.MessageBox.show({
                        title: 'Failed',
                        msg: 'Network Connection Error',
                        buttons: Ext.MessageBox.OK,
                        animateTarget: 'mb9',
                        icon: 'ext-mb-error'
                    });
                }
            });

        }
    }
});