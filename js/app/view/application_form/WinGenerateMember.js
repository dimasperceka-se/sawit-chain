/*
* @Author: nikolius
* @Date:   2017-11-09 15:39:49
* @Last Modified by:   nikolius
* @Last Modified time: 2017-11-10 10:36:30
*/

/*
    Param2 yg diperlukan ketika load View ini
    - opsiDisplay
    - Store yg panggil
    - FarmerGroupID
*/

Ext.define('Koltiva.view.application_form.WinGenerateMember' ,{
    extend: 'Ext.window.Window',
    id: 'Koltiva.view.application_form.WinGenerateMember',
    title: lang('Farmer Input'),
    closable: true,
    modal: true,
    closeAction: 'destroy',
    width: '78%',
    height: '90%',
    overflowY: 'auto',
    viewVar: false,
    setViewVar: function(value){
        this.viewVar = value;
    },
    initComponent: function() {
        var thisObj = this;

        var storeApplicantMemberInput = Ext.create('Koltiva.store.application_form.ApplicantMemberInput');

        var cmb_enumerator = Ext.create('Koltiva.store.FarmerGroup.CmbEnumerator');

        thisObj.items = [{
            xtype: 'grid',
            id: 'Koltiva.view.application_form.WinGenerateMember-gridInput',
            style: 'border:1px solid #CCC;margin:5px;',
            loadMask: true,
            selType: 'rowmodel',
            store: storeApplicantMemberInput,
            viewConfig: {
                deferEmptyText: false,
                emptyText: GetDefaultContentNoData()
            },
            dockedItems: [{
                xtype: 'pagingtoolbar',
                store: storeApplicantMemberInput,
                dock: 'bottom',
                displayInfo: true,
                style:'padding:4px 12px 4px 4px;'
            },{
                xtype: 'toolbar',
                dock:'top',
                items: [{
                    id: 'Koltiva.view.application_form.WinGenerateMember-gridInput-textSearch',
                    xtype: 'textfield',
                    width: 300,
                    emptyText: lang('Search by Name')
                },{
                    id: 'Koltiva.view.application_form.WinGenerateMember-gridInput-Enumerator',
                    xtype: 'combobox',
                    store: cmb_enumerator,
                    displayField: 'label',
                    valueField: 'id',
                    queryMode: 'local',
                    width: 300,
                    emptyText: lang('Enumerator')
                },{
                    xtype: 'button',
                    text: lang('Search'),
                    icon: varjs.config.base_url + 'images/icons/silk/search.png',
                    cls:'Sfr_BtnGridGreen', 
                    overCls:'Sfr_BtnGridGreen-Hover',
                    handler: function() {
                        storeApplicantMemberInput.setStoreVar({
                            textSearch: Ext.getCmp('Koltiva.view.application_form.WinGenerateMember-gridInput-textSearch').getValue(),
                            Enumerator: Ext.getCmp('Koltiva.view.application_form.WinGenerateMember-gridInput-Enumerator').getValue()
                        });
                        storeApplicantMemberInput.load();
                    }
                }]
            }],
            columns: [{
                dataIndex: 'ApplicantID',
                hidden: true
            },{
                xtype : 'checkcolumn',
                text : '&nbsp;',
                dataIndex : 'chdata',
                width:'6%'
            },{
                text: lang('ID'),
                flex:1,
                dataIndex: 'ApplicantID'
            },{
                text: lang('Full Name'),
                flex:5,
                dataIndex: 'Fullname'
            },{
                text: lang('Subdistrict'),
                flex:5,
                dataIndex: 'SubDistrict'
            },{
                text: lang('Village'),
                flex:5,
                dataIndex: 'Village'
            },{
                text: lang('Enumerator'),
                flex:5,
                dataIndex: 'Enumerator'
            }]
        }];

        thisObj.buttons = [{
            text: lang('Add Applicant'),
            id: 'Koltiva.view.application_form.WinGenerateMember-Form-BtnSave',
            icon: varjs.config.base_url + 'images/icons/new/save.png',
            cls: 'Sfr_BtnFormBlue',
            overCls: 'Sfr_BtnFormBlue-Hover',
            handler: function () {
                var records = storeApplicantMemberInput.queryBy(function(record) {
                    return record.get('chdata') === true;
                });
                var ids = [];
                records.each(function(record) {
                    ids.push(record.get('ApplicantID'));
                });

                if(ids.length > 0){
                    Ext.MessageBox.show({
                        msg: lang('Please wait')+'...',
                        progressText: lang('Loading')+'...',
                        width: 300,
                        wait: true,
                        waitConfig: {
                            interval: 200
                        },
                        icon: 'ext-mb-info', //custom class in msg-box.html
                        animateTarget: 'mb9'
                    });
                    //insert kan ke tabel
                    Ext.Ajax.request({
                        url: m_api + '/application_form/application_store/applicant_member_input',
                        method: 'POST',
                        waitMsg: 'Saving data...',
                        params: {
                            ApplicantID: Ext.encode(ids)
                        },
                        success: function(response, o) {
                            var obj = Ext.decode(response.responseText);

                            Ext.MessageBox.show({
                                title: 'Information',
                                msg: lang('Data saved'),
                                buttons: Ext.MessageBox.OK,
                                animateTarget: 'mb9',
                                icon: 'ext-mb-success'
                            });

                            thisObj.viewVar.callerStore.load();

                            thisObj.close();
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
            icon: varjs.config.base_url + 'images/icons/new/close.png',
            cls: 'Sfr_BtnFormGrey',
            overCls: 'Sfr_BtnFormGrey-Hover',
            handler: function() {
                thisObj.close();
            }
        }];

        this.callParent(arguments);
    },
    listeners: {
        afterRender: function(){
            var thisObj = this;

            //load store gridnya
            var store_grid = Ext.data.StoreManager.lookup('Koltiva.store.application_form.ApplicantMemberInput');
            store_grid.setStoreVar({FarmerGroupID:thisObj.viewVar.FarmerGroupID});
            store_grid.load();
        }
    }
});