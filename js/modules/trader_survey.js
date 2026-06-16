/*
* @Author: nikolius
* @Date:   2017-03-14 11:58:38
* @Last Modified by:   nikolius
* @Last Modified time: 2018-01-04 11:37:06
*/
function displayFormSurvey(){
    var TraderID = Ext.getCmp('TraderID').getValue();

    var traderSurListStore = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['TraderSurID','SurveyYear', 'InterviewDate','DateCreated','CreatedBy','DateUpdated','LastModifiedBy'],
        autoLoad: true,
        proxy: {
            type: 'ajax',
            url: m_api + '/trader/traderSurList',
            extraParams: {
                TraderID: TraderID
            },
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });

    var traderSurWin = Ext.create('widget.window', {
        title: lang('Trader Survey'),
        id: 'traderSurWin',
        closable: true,
        modal: true,
        closeAction: 'destroy',
        width: '75%',
        height: '50%',
        layout: {
            type: 'fit'
        },
        items: [{
            xtype: 'gridpanel',
            id: 'traderSurGrid',
            style: 'border:1px solid #CCC;',
            store: traderSurListStore,
            width: '100%',
            loadMask: true,
            selType: 'rowmodel',
            listeners: {
                itemdblclick: function(dv, record, item, index, e) {
                    if (record.get('TraderSurID') != undefined) {
                        displayFormTraderSurvey('update',record.get('TraderSurID'));
                    }
                }
            },
            dockedItems: [{
                xtype: 'toolbar',
                items: [{
                    icon: varjs.config.base_url + 'images/icons/new/add.png', cls:'Sfr_BtnGridGreen', overCls:'Sfr_BtnGridGreen-Hover',
                    hidden: !m_act_add,
                    text: lang('Add'),
                    scope: this,
                    handler: function() {
                        displayFormTraderSurvey('insert');
                    }
                },{
                    icon: varjs.config.base_url + 'images/icons/new/update.png',
                    text: lang('Update'),
                    scope: this,
                    handler: function() {
                        var smb = Ext.getCmp('traderSurGrid').getSelectionModel().getSelection()[0];
                        if(smb == undefined){
                            Ext.MessageBox.alert('Warning', 'No data selected');
                        }else{
                            displayFormTraderSurvey('update',smb.raw.TraderSurID);
                        }
                    }
                },{
                    itemId: 'remove',
                    icon: varjs.config.base_url + 'images/icons/new/delete.png',
                    text: lang('Delete'),
                    hidden: !m_act_delete,
                    scope: this,
                    handler: function() {
                        var smb = Ext.getCmp('traderSurGrid').getSelectionModel().getSelection()[0];
                        if(smb == undefined){
                            Ext.MessageBox.alert('Warning', 'No data selected');
                        }else{
                            //action delete
                            Ext.MessageBox.confirm('Message', 'Do you want to delete this data ?', function(btn) {
                                if (btn == 'yes') {
                                    Ext.Ajax.request({
                                        waitMsg: 'Please Wait',
                                        url: m_api + '/trader/traderSurvey',
                                        method: 'DELETE',
                                        params: {
                                            TraderSurID: smb.raw.TraderSurID
                                        },
                                        success: function(response, opts) {
                                            var obj = Ext.decode(response.responseText);
                                            switch (obj.success) {
                                                case true:
                                                    Ext.MessageBox.alert('Success', obj.message);
                                                    traderSurListStore.load();
                                                break;
                                                default:
                                                    Ext.MessageBox.alert('Warning', obj.message);
                                                break;
                                            }
                                        },
                                        failure: function(response, opts) {
                                            var obj = Ext.decode(response.responseText);
                                            Ext.MessageBox.alert('Failed', obj.message);
                                        }
                                    });
                                }
                            });
                        }
                    }
                }]
            }],
            columns: [{
                dataIndex: 'TraderSurID',
                hidden: true
            },{
                text: lang('Survey Year'),
                dataIndex: 'SurveyYear',
                width: '25%'
            },{
                text: lang('Interview Date'),
                dataIndex: 'InterviewDate',
                format: 'Y-m-d',
                width: '15%'
            },{
                text: lang('Created By'),
                dataIndex: 'CreatedBy',
                width: '15%'
            },{
                text: lang('Date Created'),
                dataIndex: 'DateCreated',
                width: '15%'
            },{
                text: lang('Last Modified By'),
                dataIndex: 'LastModifiedBy',
                width: '15%'
            },{
                text: lang('Date Updated'),
                dataIndex: 'DateUpdated',
                width: '15%'
            }],
            buttons: [{
                text: lang('Close'),
                margin: '5px',
                scale: 'large',
                ui: 's-button',
                cls: 's-grey',
                disabled: false,
                handler: function() {
                    traderSurWin.close();
                }
            }]
        }]
    });

    //show windows
    if (!traderSurWin.isVisible()) {
        traderSurWin.center();
        traderSurWin.show();
    } else {
        traderSurWin.close();
    }

    function displayFormTraderSurvey(showMethod, TraderSurID=null){

        var combo_ref_survey_store = Ext.create('Ext.data.Store', {
            extend: 'Ext.data.Model',
            fields: ['id', 'label'],
            autoLoad: true,
            proxy: {
                type: 'ajax',
                url: m_api + '/trader/comboSurveyYear',
                reader: {
                    type: 'json',
                    root: 'data'
                }
            }
        });

        var traderSurFormWin = Ext.create('widget.window', {
            title: lang('Trader Survey'),
            id: 'traderSurFormWin',
            closable: false,
            modal: true,
            closeAction: 'destroy',
            width: '80%',
            height: '90%',
            overflowY: 'auto',
            bodyStyle:{"background-color":"#F0F0F0"},
            style:'background-color:#F0F0F0;',
            padding:6,
            scrollOffset: 20,
            items:[{
                xtype: 'form',
                id: 'traderSurFormData',
                padding:15,
                items:[{
                    xtype:'panel',
                    title: lang('Survey Data'),
                    style:'background-color:#F0F0F0;margin-bottom:20px;',
                    padding:5,
                    frame:true,
                    items:[{
                        layout: 'column',
                        border: false,
                        items: [{
                            columnWidth: .5,
                            layout: 'form',
                            padding:7,
                            border: false,
                            defaults: {
                                labelWidth: 150
                            },
                            items:[{
                                xtype: 'textfield',
                                hidden: true,
                                id: 'tSurTraderSurID',
                                name: 'tSurTraderSurID'
                            },{
                                xtype: 'textfield',
                                fieldLabel: lang('Trader ID'),
                                id: 'tSurTraderID',
                                name: 'tSurTraderID',
                                readOnly: true
                            },{
                                xtype: 'textfield',
                                fieldLabel: lang('Trader Name'),
                                id: 'tSurTraderName',
                                name: 'tSurTraderName',
                                readOnly: true
                            }]
                        },{
                            columnWidth: .5,
                            layout: 'form',
                            padding:7,
                            border: false,
                            defaults: {
                                labelWidth: 150
                            },
                            items:[{
                                xtype: 'combo',
                                fieldLabel: lang('Survey Year'),
                                id: 'tSurSurveyYear',
                                name: 'tSurSurveyYear',
                                store: combo_ref_survey_store,
                                displayField: 'label',
                                valueField: 'id',
                                queryMode: 'local',
                                allowBlank: false
                            },{
                                xtype: 'datefield',
                                fieldLabel: lang('Interview Date'),
                                id: 'tSurInterviewDate',
                                name: 'tSurInterviewDate',
                                format: 'Y-m-d',
                                allowBlank:false
                            }]
                        }]
                    }]
                },{
                    xtype: 'tabpanel',
                    flex: 1,
                    margin: 0,
                    activeTab: 0,
                    plain: true,
                    items:[{
                        xtype: 'panel',
                        title: 'I. '+lang('General Data'),
                        padding: 7,
                        frame:true,
                        style:'background-color:#F0F0F0;margin-top:10px;',
                        items:[{
                            layout: 'column',
                            border: false,
                            items: [{
                                columnWidth: .03,
                                layout: 'form',
                                items:[{
                                    xtype: 'label',
                                    text: '1.',
                                }]
                            },{
                                columnWidth: .47,
                                layout: 'form',
                                items:[{
                                    xtype: 'label',
                                    text: lang('Nama'),
                                }]
                            },{
                                columnWidth: .5,
                                layout: 'form',
                                items:[{
                                    xtype: 'textfield',
                                    id: 'tSurName',
                                    name: 'tSurName'
                                }]
                            }]
                        },{
                            layout: 'column',
                            border: false,
                            items: [{
                                columnWidth: .03,
                                layout: 'form',
                                items:[{
                                    xtype: 'label',
                                    text: '2.',
                                }]
                            },{
                                columnWidth: .47,
                                layout: 'form',
                                items:[{
                                    xtype: 'label',
                                    text: lang('Nama Usaha'),
                                }]
                            },{
                                columnWidth: .5,
                                layout: 'form',
                                items:[{
                                    xtype: 'textfield',
                                    id: 'tSurCompanyName',
                                    name: 'tSurCompanyName'
                                }]
                            }]
                        },{
                            layout: 'column',
                            border: false,
                            items: [{
                                columnWidth: .03,
                                layout: 'form',
                                items:[{
                                    xtype: 'label',
                                    text: '3.',
                                }]
                            },{
                                columnWidth: .47,
                                layout: 'form',
                                items:[{
                                    xtype: 'label',
                                    text: lang('Tanggal Lahir'),
                                }]
                            },{
                                columnWidth: .15,
                                layout: 'form',
                                items:[{
                                    xtype: 'datefield',
                                    id: 'tSurBirthDate',
                                    name: 'tSurBirthDate',
                                    format: 'Y-m-d'
                                }]
                            }]
                        },{
                            layout: 'column',
                            border: false,
                            items: [{
                                columnWidth: .03,
                                layout: 'form',
                                items:[{
                                    xtype: 'label',
                                    text: '4.',
                                }]
                            },{
                                columnWidth: .47,
                                layout: 'form',
                                items:[{
                                    xtype: 'label',
                                    text: lang('Alamat'),
                                }]
                            },{
                                columnWidth: .5,
                                layout: 'form',
                                items:[{
                                    xtype: 'textfield',
                                    id: 'tSurAddress',
                                    name: 'tSurAddress'
                                }]
                            }]
                        },{
                            layout: 'column',
                            border: false,
                            items: [{
                                columnWidth: .03,
                                layout: 'form',
                                items:[{
                                    xtype: 'label',
                                    text: '5.',
                                }]
                            },{
                                columnWidth: .47,
                                layout: 'form',
                                items:[{
                                    xtype: 'label',
                                    text: lang('No KTP'),
                                }]
                            },{
                                columnWidth: .5,
                                layout: 'form',
                                items:[{
                                    xtype: 'textfield',
                                    id: 'tSurNoKTP',
                                    name: 'tSurNoKTP'
                                }]
                            }]
                        },{
                            layout: 'column',
                            border: false,
                            items: [{
                                columnWidth: .03,
                                layout: 'form',
                                items:[{
                                    xtype: 'label',
                                    text: '6.',
                                }]
                            },{
                                columnWidth: .47,
                                layout: 'form',
                                items:[{
                                    xtype: 'label',
                                    text: lang('Jenis Kelamin'),
                                }]
                            },{
                                columnWidth: .5,
                                layout:{
                                    type:'hbox',
                                    align:'stretch'
                                },
                                xtype: 'radiogroup',
                                defaults: {
                                    flex:true
                                },
                                items: [{
                                    name: 'tSurGender',
                                    id: 'tSurGenderM',
                                    boxLabel: lang('Male'),
                                    inputValue: 'm',
                                    listeners:{
                                        change: function() {
                                            return false;
                                        }
                                    }
                                },{
                                    name: 'tSurGender',
                                    id: 'tSurGenderF',
                                    boxLabel: lang('Female'),
                                    inputValue: 'f',
                                    listeners:{
                                        change: function() {
                                            return false;
                                        }
                                    }
                                }]
                            }]
                        },{
                            layout: 'column',
                            border: false,
                            items: [{
                                columnWidth: .03,
                                layout: 'form',
                                items:[{
                                    xtype: 'label',
                                    text: '7.',
                                }]
                            },{
                                columnWidth: .47,
                                layout: 'form',
                                items:[{
                                    xtype: 'label',
                                    text: lang('Handphone'),
                                }]
                            },{
                                columnWidth: .5,
                                layout: 'form',
                                items:[{
                                    xtype: 'textfield',
                                    id: 'tSurHandphone',
                                    name: 'tSurHandphone'
                                }]
                            }]
                        },{
                            layout: 'column',
                            border: false,
                            items: [{
                                columnWidth: .03,
                                layout: 'form',
                                items:[{
                                    xtype: 'label',
                                    text: '8.',
                                }]
                            },{
                                columnWidth: .47,
                                layout: 'form',
                                items:[{
                                    xtype: 'label',
                                    text: lang('Email'),
                                }]
                            },{
                                columnWidth: .5,
                                layout: 'form',
                                items:[{
                                    xtype: 'textfield',
                                    id: 'tSurEmail',
                                    name: 'tSurEmail'
                                }]
                            }]
                        },{
                            layout: 'column',
                            border: false,
                            items: [{
                                columnWidth: .03,
                                layout: 'form',
                                items:[{
                                    xtype: 'label',
                                    text: '9 a.',
                                }]
                            },{
                                columnWidth: .47,
                                layout: 'form',
                                items:[{
                                    xtype: 'label',
                                    text: lang('Latitude'),
                                }]
                            },{
                                columnWidth: .2,
                                layout: 'form',
                                items:[{
                                    xtype: 'numericfield',
                                    id: 'tSurLatitude',
                                    name: 'tSurLatitude',
                                    allowDecimals: true,
                                    decimalPrecision: 12
                                }]
                            }]
                        },{
                            layout: 'column',
                            border: false,
                            items: [{
                                columnWidth: .03,
                                layout: 'form',
                                items:[{
                                    xtype: 'label',
                                    text: '9 b.',
                                }]
                            },{
                                columnWidth: .47,
                                layout: 'form',
                                items:[{
                                    xtype: 'label',
                                    text: lang('Longitude'),
                                }]
                            },{
                                columnWidth: .2,
                                layout: 'form',
                                items:[{
                                    xtype: 'numericfield',
                                    id: 'tSurLongitude',
                                    name: 'tSurLongitude',
                                    allowDecimals: true,
                                    decimalPrecision: 12
                                }]
                            }]
                        },{
                            layout: 'column',
                            border: false,
                            items: [{
                                columnWidth: .03,
                                layout: 'form',
                                items:[{
                                    xtype: 'label',
                                    text: '10.',
                                }]
                            },{
                                columnWidth: .97,
                                layout: 'form',
                                items:[{
                                    xtype: 'label',
                                    text: lang('Pendidikan Terakhir'),
                                }]
                            }]
                        },{
                            layout: 'column',
                            border: false,
                            items: [{
                                columnWidth: .03,
                                layout: 'form',
                                items:[{}]
                            },{
                                columnWidth: .47,
                                layout: 'form',
                                items:[{
                                    xtype: 'radiofield',
                                    boxLabel: lang('Tidak sekolah atau tidak tamat SD'),
                                    name: 'tSurLastEducation',
                                    inputValue: '1',
                                    id: 'tSurLastEducation1',
                                    listeners:{
                                        change: function() {
                                            return false;
                                        }
                                    }
                                },{
                                    xtype: 'radiofield',
                                    boxLabel: lang('Lulus SD'),
                                    name: 'tSurLastEducation',
                                    inputValue: '2',
                                    id: 'tSurLastEducation2',
                                    listeners:{
                                        change: function() {
                                            return false;
                                        }
                                    }
                                },{
                                    xtype: 'radiofield',
                                    boxLabel: lang('Lulus SMP'),
                                    name: 'tSurLastEducation',
                                    inputValue: '3',
                                    id: 'tSurLastEducation3',
                                    listeners:{
                                        change: function() {
                                            return false;
                                        }
                                    }
                                }]
                            },{
                                columnWidth: .5,
                                layout: 'form',
                                items:[{
                                    xtype: 'radiofield',
                                    boxLabel: lang('Lulus SMA/SMK'),
                                    name: 'tSurLastEducation',
                                    inputValue: '4',
                                    id: 'tSurLastEducation4',
                                    listeners:{
                                        change: function() {
                                            return false;
                                        }
                                    }
                                },{
                                    xtype: 'radiofield',
                                    boxLabel: lang('Lulus D1/D3'),
                                    name: 'tSurLastEducation',
                                    inputValue: '5',
                                    id: 'tSurLastEducation5',
                                    listeners:{
                                        change: function() {
                                            return false;
                                        }
                                    }
                                },{
                                    xtype: 'radiofield',
                                    boxLabel: lang('Lulus S1/S2/S3'),
                                    name: 'tSurLastEducation',
                                    inputValue: '6',
                                    id: 'tSurLastEducation6',
                                    listeners:{
                                        change: function() {
                                            return false;
                                        }
                                    }
                                }]
                            }]
                        }]
                    },{
                        xtype: 'panel',
                        title: 'II. '+lang('Pertanyaan Bisnis'),
                        padding: 10,
                        frame:true,
                        style:'background-color:#F0F0F0;margin-top:10px;',
                        items:[{
                            layout: 'column',
                            border: false,
                            items: [{
                                columnWidth: .03,
                                layout: 'form',
                                items:[{
                                    xtype: 'label',
                                    text: '1.',
                                }]
                            },{
                                columnWidth: .47,
                                layout: 'form',
                                items:[{
                                    xtype: 'label',
                                    text: lang('Apakah berdagang adalah aktivitas penuh-waktu ?'),
                                }]
                            },{
                                columnWidth: .5,
                                layout:{
                                    type:'hbox',
                                    align:'stretch'
                                },
                                xtype: 'radiogroup',
                                defaults: {
                                    flex:true
                                },
                                items: [{
                                    name: 'tSurFulltimeTrader',
                                    id: 'tSurFulltimeTrader1',
                                    boxLabel: lang('Ya'),
                                    inputValue: '1',
                                    listeners:{
                                        change: function() {
                                            return false;
                                        }
                                    }
                                },{
                                    name: 'tSurFulltimeTrader',
                                    id: 'tSurFulltimeTrader2',
                                    boxLabel: lang('Tidak'),
                                    inputValue: '2',
                                    listeners:{
                                        change: function() {
                                            return false;
                                        }
                                    }
                                }]
                            }]
                        },{
                            layout: 'column',
                            border: false,
                            items:[{
                                columnWidth: .03,
                                layout: 'form',
                                items:[{
                                    xtype: 'label',
                                    text: '2.',
                                }]
                            },{
                                columnWidth: .97,
                                layout: 'form',
                                items:[{
                                    xtype: 'label',
                                    text: lang('Apakah status usaha Bapak/Ibu ? (sesuai dengan status terdaftar)'),
                                }]
                            }]
                        },{
                            layout: 'column',
                            border: false,
                            items:[{
                                columnWidth: .03,
                                layout: 'form',
                                items:[{}]
                            },{
                                columnWidth: .47,
                                layout: 'form',
                                items:[{
                                    xtype: 'radiofield',
                                    boxLabel: lang('Tidak ada status legal'),
                                    name: 'tSurStatusTrader',
                                    inputValue: '1',
                                    id: 'tSurStatusTrader1',
                                    listeners:{
                                        change: function() {
                                            return false;
                                        }
                                    }
                                },{
                                    xtype: 'radiofield',
                                    boxLabel: lang('CV'),
                                    name: 'tSurStatusTrader',
                                    inputValue: '2',
                                    id: 'tSurStatusTrader2',
                                    listeners:{
                                        change: function() {
                                            return false;
                                        }
                                    }
                                },{
                                    xtype: 'radiofield',
                                    boxLabel: lang('PT'),
                                    name: 'tSurStatusTrader',
                                    inputValue: '3',
                                    id: 'tSurStatusTrader3',
                                    listeners:{
                                        change: function() {
                                            return false;
                                        }
                                    }
                                }]
                            },{
                                columnWidth: .5,
                                layout: 'form',
                                items:[{
                                    xtype: 'radiofield',
                                    boxLabel: lang('UD'),
                                    name: 'tSurStatusTrader',
                                    inputValue: '4',
                                    id: 'tSurStatusTrader4',
                                    listeners:{
                                        change: function() {
                                            return false;
                                        }
                                    }
                                },{
                                    xtype: 'radiofield',
                                    boxLabel: lang('Koperasi'),
                                    name: 'tSurStatusTrader',
                                    inputValue: '5',
                                    id: 'tSurStatusTrader5',
                                    listeners:{
                                        change: function() {
                                            return false;
                                        }
                                    }
                                },{
                                    xtype: 'radiofield',
                                    boxLabel: lang('Lainnya'),
                                    name: 'tSurStatusTrader',
                                    inputValue: '6',
                                    id: 'tSurStatusTrader6',
                                    listeners:{
                                        change: function() {
                                            return false;
                                        }
                                    }
                                }]
                            }]
                        },{
                            layout: 'column',
                            border: false,
                            items:[{
                                columnWidth: .03,
                                layout: 'form',
                                items:[{
                                    xtype: 'label',
                                    text: '3.'
                                }]
                            },{
                                columnWidth: .47,
                                layout: 'form',
                                items:[{
                                    xtype: 'label',
                                    text: lang('Sudah berapa lama usaha Bapak/Ibu berjalan ?')
                                }]
                            },{
                                columnWidth: .2,
                                layout: 'form',
                                items:[{
                                    xtype: 'numericfield',
                                    id: 'tSurYearRunningTrader',
                                    name: 'tSurYearRunningTrader',
                                    allowDecimals: false,
                                    allowNegative: false,
                                    minValue: 0
                                }]
                            },{
                                columnWidth: .3,
                                layout: 'form',
                                style: 'margin-left:10px',
                                items:[{
                                    xtype: 'label',
                                    text: lang('tahun')
                                }]
                            }]
                        },{
                            layout: 'column',
                            border: false,
                            items:[{
                                columnWidth: .03,
                                layout: 'form',
                                items:[{
                                    xtype: 'label',
                                    text: '4.'
                                }]
                            },{
                                columnWidth: .97,
                                layout: 'form',
                                items:[{
                                    xtype: 'label',
                                    text: lang('Berapa jumlah karyawan penuh waktu Bapak/Ibu ?')
                                }]
                            }]
                        },{
                            layout: 'column',
                            border: false,
                            items:[{
                                columnWidth: .03,
                                layout: 'form',
                                items:[{}]
                            },{
                                columnWidth: .15,
                                layout: 'form',
                                items:[{
                                    xtype: 'label',
                                    text: 'a. '+lang('Female')
                                }]
                            },{
                                columnWidth: .15,
                                layout: 'form',
                                items:[{
                                    xtype: 'numericfield',
                                    id: 'tSurNrFulltimeStaffFemale',
                                    name: 'tSurNrFulltimeStaffFemale',
                                    allowDecimals: false,
                                    allowNegative: false,
                                    minValue: 0
                                }]
                            }]
                        },{
                            layout: 'column',
                            border: false,
                            items:[{
                                columnWidth: .03,
                                layout: 'form',
                                items:[{}]
                            },{
                                columnWidth: .15,
                                layout: 'form',
                                items:[{
                                    xtype: 'label',
                                    text: 'b. '+lang('Male')
                                }]
                            },{
                                columnWidth: .15,
                                layout: 'form',
                                items:[{
                                    xtype: 'numericfield',
                                    id: 'tSurNrFulltimeStaffMale',
                                    name: 'tSurNrFulltimeStaffMale',
                                    allowDecimals: false,
                                    allowNegative: false,
                                    minValue: 0
                                }]
                            }]
                        },{
                            layout: 'column',
                            border: false,
                            items:[{
                                columnWidth: .03,
                                layout: 'form',
                                items:[{
                                    xtype: 'label',
                                    text: '5.'
                                }]
                            },{
                                columnWidth: .97,
                                layout: 'form',
                                items:[{
                                    xtype: 'label',
                                    text: lang('Ada berapa orang karyawan informal (paruh waktu) ?')
                                }]
                            }]
                        },{
                            layout: 'column',
                            border: false,
                            items:[{
                                columnWidth: .03,
                                layout: 'form',
                                items:[{}]
                            },{
                                columnWidth: .15,
                                layout: 'form',
                                items:[{
                                    xtype: 'label',
                                    text: 'a. '+lang('Female')
                                }]
                            },{
                                columnWidth: .15,
                                layout: 'form',
                                items:[{
                                    xtype: 'numericfield',
                                    id: 'tSurNrParttimeStaffFemale',
                                    name: 'tSurNrParttimeStaffFemale',
                                    allowDecimals: false,
                                    allowNegative: false,
                                    minValue: 0
                                }]
                            }]
                        },{
                            layout: 'column',
                            border: false,
                            items:[{
                                columnWidth: .03,
                                layout: 'form',
                                items:[{}]
                            },{
                                columnWidth: .15,
                                layout: 'form',
                                items:[{
                                    xtype: 'label',
                                    text: 'b. '+lang('Male')
                                }]
                            },{
                                columnWidth: .15,
                                layout: 'form',
                                items:[{
                                    xtype: 'numericfield',
                                    id: 'tSurNrParttimeStaffMale',
                                    name: 'tSurNrParttimeStaffMale',
                                    allowDecimals: false,
                                    allowNegative: false,
                                    minValue: 0
                                }]
                            }]
                        },{
                            layout: 'column',
                            border: false,
                            items:[{
                                columnWidth: .03,
                                layout: 'form',
                                items:[{
                                    xtype: 'label',
                                    text: '6.'
                                }]
                            },{
                                columnWidth: .97,
                                layout: 'form',
                                items:[{
                                    xtype: 'label',
                                    text: lang('Komoditi apa saja yang Bapak/Ibu jual ? (berkaitan dengan usaha Bapak/Ibu)')
                                }]
                            }]
                        },{
                            layout: 'column',
                            border: false,
                            items:[{
                                columnWidth: .03,
                                layout: 'form',
                                items:[{}]
                            },{
                                columnWidth: .1,
                                layout: 'form',
                                items:[{
                                    xtype: 'label',
                                    text: lang('Kakao')
                                }]
                            },{
                                columnWidth: .1,
                                layout: 'form',
                                items:[{
                                    xtype: 'numericfield',
                                    id: 'tSurComodityCacaoSalePercentage',
                                    name: 'tSurComodityCacaoSalePercentage',
                                    allowDecimals: false,
                                    allowNegative: false,
                                    minValue: 0
                                }]
                            },{
                                columnWidth: .03,
                                layout: 'form',
                                items:[{
                                    xtype: 'label',
                                    style:'margin-left:5px',
                                    text: '%'
                                }]
                            },{
                                columnWidth: .1,
                                layout: 'form',
                                items:[{}]
                            },{
                                columnWidth: .1,
                                layout: 'form',
                                items:[{
                                    xtype: 'label',
                                    text: lang('Lainnya')
                                }]
                            },{
                                columnWidth: .1,
                                layout: 'form',
                                items:[{
                                    xtype: 'numericfield',
                                    id: 'tSurComodityOtherSalePercentage',
                                    name: 'tSurComodityOtherSalePercentage',
                                    allowDecimals: true,
                                    allowNegative: false,
                                    minValue: 0
                                }]
                            },{
                                columnWidth: .03,
                                layout: 'form',
                                items:[{
                                    xtype: 'label',
                                    style:'margin-left:5px',
                                    text: '%'
                                }]
                            }]
                        },{
                            layout: 'column',
                            border: false,
                            items:[{
                                columnWidth: .03,
                                layout: 'form',
                                items:[{
                                    xtype: 'label',
                                    text: '7.'
                                }]
                            },{
                                columnWidth: .97,
                                layout: 'form',
                                items:[{
                                    xtype: 'label',
                                    text: lang('Apakah Bapak/Ibu membeli biji berikut ?')
                                }]
                            }]
                        },{
                            layout: 'column',
                            border: false,
                            items:[{
                                columnWidth: .03,
                                layout: 'form',
                                items:[{}]
                            },{
                                columnWidth: .25,
                                layout: 'form',
                                items:[{
                                    xtype: 'label',
                                    text: 'a. '+lang('Biji basah')
                                }]
                            },{
                                columnWidth: .4,
                                layout:{
                                    type:'hbox',
                                    align:'stretch'
                                },
                                xtype: 'radiogroup',
                                defaults: {
                                    flex:true
                                },
                                items: [{
                                    name: 'tSurBuyWetBeans',
                                    id: 'tSurBuyWetBeans1',
                                    boxLabel: lang('Ya'),
                                    inputValue: '1',
                                    listeners:{
                                        change: function() {
                                            return false;
                                        }
                                    }
                                },{
                                    name: 'tSurBuyWetBeans',
                                    id: 'tSurBuyWetBeans2',
                                    boxLabel: lang('Tidak'),
                                    inputValue: '2',
                                    listeners:{
                                        change: function() {
                                            return false;
                                        }
                                    }
                                }]
                            }]
                        },{
                            layout: 'column',
                            border: false,
                            items:[{
                                columnWidth: .03,
                                layout: 'form',
                                items:[{}]
                            },{
                                columnWidth: .25,
                                layout: 'form',
                                items:[{
                                    xtype: 'label',
                                    text: 'b. '+lang('Biji terfermentasi')
                                }]
                            },{
                                columnWidth: .4,
                                layout:{
                                    type:'hbox',
                                    align:'stretch'
                                },
                                xtype: 'radiogroup',
                                defaults: {
                                    flex:true
                                },
                                items: [{
                                    name: 'tSurBuyFermentBeans',
                                    id: 'tSurBuyFermentBeans1',
                                    boxLabel: lang('Ya'),
                                    inputValue: '1',
                                    listeners:{
                                        change: function() {
                                            return false;
                                        }
                                    }
                                },{
                                    name: 'tSurBuyFermentBeans',
                                    id: 'tSurBuyFermentBeans2',
                                    boxLabel: lang('Tidak'),
                                    inputValue: '2',
                                    listeners:{
                                        change: function() {
                                            return false;
                                        }
                                    }
                                }]
                            }]
                        },{
                            layout: 'column',
                            border: false,
                            items:[{
                                columnWidth: .03,
                                layout: 'form',
                                items:[{}]
                            },{
                                columnWidth: .25,
                                layout: 'form',
                                items:[{
                                    xtype: 'label',
                                    text: 'c. '+lang('Biji kering')
                                }]
                            },{
                                columnWidth: .4,
                                layout:{
                                    type:'hbox',
                                    align:'stretch'
                                },
                                xtype: 'radiogroup',
                                defaults: {
                                    flex:true
                                },
                                items: [{
                                    name: 'tSurBuyDryBeans',
                                    id: 'tSurBuyDryBeans1',
                                    boxLabel: lang('Ya'),
                                    inputValue: '1',
                                    listeners:{
                                        change: function() {
                                            return false;
                                        }
                                    }
                                },{
                                    name: 'tSurBuyDryBeans',
                                    id: 'tSurBuyDryBeans2',
                                    boxLabel: lang('Tidak'),
                                    inputValue: '2',
                                    listeners:{
                                        change: function() {
                                            return false;
                                        }
                                    }
                                }]
                            }]
                        },{
                            layout: 'column',
                            border: false,
                            items:[{
                                columnWidth: .03,
                                layout: 'form',
                                items:[{
                                    xtype: 'label',
                                    text: '8.'
                                }]
                            },{
                                columnWidth: .97,
                                layout: 'form',
                                items:[{
                                    xtype: 'label',
                                    text: lang('Ada berapa transaksi dan volume penjualan/pembelian kakao dalam satu minggu ?')
                                }]
                            }]
                        },{
                            layout: 'column',
                            border: false,
                            items:[{
                                columnWidth: .25,
                                layout: 'form',
                                style: 'text-align:center',
                                items:[{
                                    xtype: 'label',
                                    style: 'font-weight:bold;',
                                    text: lang('Jenis Biji')
                                }]
                            },{
                                columnWidth: .25,
                                layout: 'form',
                                style: 'text-align:center',
                                items:[{
                                    xtype: 'label',
                                    style: 'font-weight:bold;',
                                    text: lang('Musim')
                                }]
                            },{
                                columnWidth: .25,
                                layout: 'form',
                                style: 'text-align:center',
                                items:[{
                                    xtype: 'label',
                                    style: 'text-align:center;font-weight:bold;',
                                    text: lang('Jumlah Transaksi')
                                }]
                            },{
                                columnWidth: .25,
                                layout: 'form',
                                style: 'text-align:center',
                                items:[{
                                    xtype: 'label',
                                    style: 'font-weight:bold;',
                                    text: lang('Volume (kg)')
                                }]
                            }]
                        },{
                            layout: 'column',
                            border: false,
                            items:[{
                                columnWidth: .25,
                                layout: 'form',
                                items:[{}]
                            },{
                                columnWidth: .25,
                                layout: 'form',
                                items:[{
                                    xtype: 'label',
                                    text: lang('Musim panen raya')
                                }]
                            },{
                                columnWidth: .25,
                                layout: 'form',
                                items:[{
                                    xtype: 'numericfield',
                                    id: 'tSurNrTransWetBeansHighHarvest',
                                    name: 'tSurNrTransWetBeansHighHarvest',
                                    allowDecimals: true,
                                    allowNegative: false,
                                    minValue: 0
                                }]
                            },{
                                columnWidth: .25,
                                layout: 'form',
                                items:[{
                                    xtype: 'numericfield',
                                    id: 'tSurNrVolumeWetBeansHighHarvest',
                                    name: 'tSurNrVolumeWetBeansHighHarvest',
                                    allowDecimals: true,
                                    allowNegative: false,
                                    minValue: 0
                                }]
                            }]
                        },{
                            layout: 'column',
                            border: false,
                            items:[{
                                columnWidth: .25,
                                layout: 'form',
                                style: 'text-align:center',
                                items:[{
                                    xtype: 'label',
                                    text: lang('Biji basah')
                                }]
                            },{
                                columnWidth: .25,
                                layout: 'form',
                                items:[{
                                    xtype: 'label',
                                    text: lang('Musim panen biasa')
                                }]
                            },{
                                columnWidth: .25,
                                layout: 'form',
                                items:[{
                                    xtype: 'numericfield',
                                    id: 'tSurNrTransWetBeansNormalHarvest',
                                    name: 'tSurNrTransWetBeansNormalHarvest',
                                    allowDecimals: false,
                                    allowNegative: false,
                                    minValue: 0
                                }]
                            },{
                                columnWidth: .25,
                                layout: 'form',
                                items:[{
                                    xtype: 'numericfield',
                                    id: 'tSurNrVolumeWetBeansNormalHarvest',
                                    name: 'tSurNrVolumeWetBeansNormalHarvest',
                                    allowDecimals: false,
                                    allowNegative: false,
                                    minValue: 0
                                }]
                            }]
                        },{
                            layout: 'column',
                            border: false,
                            items:[{
                                columnWidth: .25,
                                layout: 'form',
                                items:[{}]
                            },{
                                columnWidth: .25,
                                layout: 'form',
                                items:[{
                                    xtype: 'label',
                                    text: lang('Musim panen trek')
                                }]
                            },{
                                columnWidth: .25,
                                layout: 'form',
                                items:[{
                                    xtype: 'numericfield',
                                    id: 'tSurNrTransWetBeansLowHarvest',
                                    name: 'tSurNrTransWetBeansLowHarvest',
                                    allowDecimals: false,
                                    allowNegative: false,
                                    minValue: 0
                                }]
                            },{
                                columnWidth: .25,
                                layout: 'form',
                                items:[{
                                    xtype: 'numericfield',
                                    id: 'tSurNrVolumeWetBeansLowHarvest',
                                    name: 'tSurNrVolumeWetBeansLowHarvest',
                                    allowDecimals: false,
                                    allowNegative: false,
                                    minValue: 0
                                }]
                            }]
                        },{
                            layout: 'column',
                            border: false,
                            items:[{
                                columnWidth: .25,
                                layout: 'form',
                                items:[{}]
                            },{
                                columnWidth: .25,
                                layout: 'form',
                                items:[{
                                    xtype: 'label',
                                    text: lang('Musim panen raya')
                                }]
                            },{
                                columnWidth: .25,
                                layout: 'form',
                                items:[{
                                    xtype: 'numericfield',
                                    id: 'tSurNrTransFermentBeansHighHarvest',
                                    name: 'tSurNrTransFermentBeansHighHarvest',
                                    allowDecimals: false,
                                    allowNegative: false,
                                    minValue: 0
                                }]
                            },{
                                columnWidth: .25,
                                layout: 'form',
                                items:[{
                                    xtype: 'numericfield',
                                    id: 'tSurNrVolumeFermentBeansHighHarvest',
                                    name: 'tSurNrVolumeFermentBeansHighHarvest',
                                    allowDecimals: false,
                                    allowNegative: false,
                                    minValue: 0
                                }]
                            }]
                        },{
                            layout: 'column',
                            border: false,
                            items:[{
                                columnWidth: .25,
                                layout: 'form',
                                style: 'text-align:center;',
                                items:[{
                                    xtype: 'label',
                                    text: lang('Biji terfermentasi')
                                }]
                            },{
                                columnWidth: .25,
                                layout: 'form',
                                items:[{
                                    xtype: 'label',
                                    text: lang('Musim panen biasa')
                                }]
                            },{
                                columnWidth: .25,
                                layout: 'form',
                                items:[{
                                    xtype: 'numericfield',
                                    id: 'tSurNrTransFermentBeansNormalHarvest',
                                    name: 'tSurNrTransFermentBeansNormalHarvest',
                                    allowDecimals: false,
                                    allowNegative: false,
                                    minValue: 0
                                }]
                            },{
                                columnWidth: .25,
                                layout: 'form',
                                items:[{
                                    xtype: 'numericfield',
                                    id: 'tSurNrVolumeFermentBeansNormalHarvest',
                                    name: 'tSurNrVolumeFermentBeansNormalHarvest',
                                    allowDecimals: false,
                                    allowNegative: false,
                                    minValue: 0
                                }]
                            }]
                        },{
                            layout: 'column',
                            border: false,
                            items:[{
                                columnWidth: .25,
                                layout: 'form',
                                items:[{}]
                            },{
                                columnWidth: .25,
                                layout: 'form',
                                items:[{
                                    xtype: 'label',
                                    text: lang('Musim panen trek')
                                }]
                            },{
                                columnWidth: .25,
                                layout: 'form',
                                items:[{
                                    xtype: 'numericfield',
                                    id: 'tSurNrTransFermentBeansLowHarvest',
                                    name: 'tSurNrTransFermentBeansLowHarvest',
                                    allowDecimals: false,
                                    allowNegative: false,
                                    minValue: 0
                                }]
                            },{
                                columnWidth: .25,
                                layout: 'form',
                                items:[{
                                    xtype: 'numericfield',
                                    id: 'tSurNrVolumeFermentBeansLowHarvest',
                                    name: 'tSurNrVolumeFermentBeansLowHarvest',
                                    allowDecimals: false,
                                    allowNegative: false,
                                    minValue: 0
                                }]
                            }]
                        },{
                            layout: 'column',
                            border: false,
                            items:[{
                                columnWidth: .25,
                                layout: 'form',
                                items:[{}]
                            },{
                                columnWidth: .25,
                                layout: 'form',
                                items:[{
                                    xtype: 'label',
                                    text: lang('Musim panen raya')
                                }]
                            },{
                                columnWidth: .25,
                                layout: 'form',
                                items:[{
                                    xtype: 'numericfield',
                                    id: 'tSurNrTransDryBeansHighHarvest',
                                    name: 'tSurNrTransDryBeansHighHarvest',
                                    allowDecimals: false,
                                    allowNegative: false,
                                    minValue: 0
                                }]
                            },{
                                columnWidth: .25,
                                layout: 'form',
                                items:[{
                                    xtype: 'numericfield',
                                    id: 'tSurNrVolumeDryBeansHighHarvest',
                                    name: 'tSurNrVolumeDryBeansHighHarvest',
                                    allowDecimals: false,
                                    allowNegative: false,
                                    minValue: 0
                                }]
                            }]
                        },{
                            layout: 'column',
                            border: false,
                            items:[{
                                columnWidth: .25,
                                layout: 'form',
                                style: 'text-align:center',
                                items:[{
                                    xtype: 'label',
                                    text: lang('Biji kering')
                                }]
                            },{
                                columnWidth: .25,
                                layout: 'form',
                                items:[{
                                    xtype: 'label',
                                    text: lang('Musim panen biasa')
                                }]
                            },{
                                columnWidth: .25,
                                layout: 'form',
                                items:[{
                                    xtype: 'numericfield',
                                    id: 'tSurNrTransDryBeansNormalHarvest',
                                    name: 'tSurNrTransDryBeansNormalHarvest',
                                    allowDecimals: false,
                                    allowNegative: false,
                                    minValue: 0
                                }]
                            },{
                                columnWidth: .25,
                                layout: 'form',
                                items:[{
                                    xtype: 'numericfield',
                                    id: 'tSurNrVolumeDryBeansNormalHarvest',
                                    name: 'tSurNrVolumeDryBeansNormalHarvest',
                                    allowDecimals: false,
                                    allowNegative: false,
                                    minValue: 0
                                }]
                            }]
                        },{
                            layout: 'column',
                            border: false,
                            items:[{
                                columnWidth: .25,
                                layout: 'form',
                                items:[{}]
                            },{
                                columnWidth: .25,
                                layout: 'form',
                                items:[{
                                    xtype: 'label',
                                    text: lang('Musim panen trek')
                                }]
                            },{
                                columnWidth: .25,
                                layout: 'form',
                                items:[{
                                    xtype: 'numericfield',
                                    id: 'tSurNrTransDryBeansLowHarvest',
                                    name: 'tSurNrTransDryBeansLowHarvest',
                                    allowDecimals: false,
                                    allowNegative: false,
                                    minValue: 0
                                }]
                            },{
                                columnWidth: .25,
                                layout: 'form',
                                items:[{
                                    xtype: 'numericfield',
                                    id: 'tSurNrVolumeDryBeansLowHarvest',
                                    name: 'tSurNrVolumeDryBeansLowHarvest',
                                    allowDecimals: false,
                                    allowNegative: false,
                                    minValue: 0
                                }]
                            }]
                        },{
                            layout: 'column',
                            border: false,
                            items:[{
                                columnWidth: .03,
                                layout: 'form',
                                items:[{
                                    xtype: 'label',
                                    text: '9.'
                                }]
                            },{
                                columnWidth: .97,
                                layout: 'form',
                                items:[{
                                    xtype: 'label',
                                    text: lang('Ada berapa klien Bapak/Ibu untuk penjualan kakao ?')
                                }]
                            }]
                        },{
                            layout: 'column',
                            border: false,
                            items:[{
                                columnWidth: .03,
                                layout: 'form',
                                items:[{}]
                            },{
                                columnWidth: .4,
                                layout: 'form',
                                items:[{
                                    xtype: 'label',
                                    text: 'a. '+lang('yang selalu menjual ke Bapak/Ibu')
                                }]
                            },{
                                columnWidth: .1,
                                layout: 'form',
                                items:[{
                                    xtype: 'numericfield',
                                    id: 'tSurNrCacaoFrequentBuyer',
                                    name: 'tSurNrCacaoFrequentBuyer',
                                    allowDecimals: false,
                                    allowNegative: false,
                                    minValue: 0
                                }]
                            },{
                                columnWidth: .2,
                                layout: 'form',
                                items:[{
                                    xtype: 'label',
                                    style: 'margin-left:8px',
                                    text: lang('(isi dengan jumlah klien terkait)')
                                }]
                            }]
                        },{
                            layout: 'column',
                            border: false,
                            items:[{
                                columnWidth: .03,
                                layout: 'form',
                                items:[{}]
                            },{
                                columnWidth: .4,
                                layout: 'form',
                                items:[{
                                    xtype: 'label',
                                    text: 'b. '+lang('yang terkadang menjual ke Bapak/Ibu')
                                }]
                            },{
                                columnWidth: .1,
                                layout: 'form',
                                items:[{
                                    xtype: 'numericfield',
                                    id: 'tSurNrCacaoNormalBuyer',
                                    name: 'tSurNrCacaoNormalBuyer',
                                    allowDecimals: false,
                                    allowNegative: false,
                                    minValue: 0
                                }]
                            },{
                                columnWidth: .2,
                                layout: 'form',
                                items:[{
                                    xtype: 'label',
                                    style: 'margin-left:8px',
                                    text: lang('(isi dengan jumlah klien terkait)')
                                }]
                            }]
                        },{
                            layout: 'column',
                            border: false,
                            items:[{
                                columnWidth: .03,
                                layout: 'form',
                                items:[{
                                    xtype: 'label',
                                    text: '10.'
                                }]
                            },{
                                columnWidth: .97,
                                layout: 'form',
                                items:[{
                                    xtype: 'label',
                                    text: lang('Apa yang Bapak/Ibu lakukan berkaitan dengan kakao ?')
                                }]
                            }]
                        },{
                            layout: 'column',
                            border: false,
                            items:[{
                                columnWidth: .03,
                                layout: 'form',
                                items:[{}]
                            },{
                                columnWidth: .47,
                                defaultType: 'checkboxfield',
                                items:[{
                                    boxLabel: lang('Membeli/menjual biji kering'),
                                    name: 'tSurCacaoActivitySellBuyDryBeans',
                                    inputValue: '1',
                                    id: 'tSurCacaoActivitySellBuyDryBeans'
                                },{
                                    boxLabel: lang('Membeli/menjual biji terfermentasi'),
                                    name: 'tSurCacaoActivitySellBuyFermentBeans',
                                    inputValue: '1',
                                    id: 'tSurCacaoActivitySellBuyFermentBeans'
                                },{
                                    boxLabel: lang('Menjual pestisida/herbisida'),
                                    name: 'tSurCacaoActivitySellPest',
                                    inputValue: '1',
                                    id: 'tSurCacaoActivitySellPest'
                                }]
                            },{
                                columnWidth: .5,
                                defaultType: 'checkboxfield',
                                items:[{
                                    boxLabel: lang('Menjual pupuk'),
                                    name: 'tSurCacaoActivitySellFertilizer',
                                    inputValue: '1',
                                    id: 'tSurCacaoActivitySellFertilizer'
                                },{
                                    boxLabel: lang('Menyediakan pinjaman bagi para petani'),
                                    name: 'tSurCacaoActivityLoanToFarmer',
                                    inputValue: '1',
                                    id: 'tSurCacaoActivityLoanToFarmer'
                                }]
                            }]
                        },{
                            layout: 'column',
                            border: false,
                            items:[{
                                columnWidth: .03,
                                layout: 'form',
                                items:[{
                                    xtype: 'label',
                                    text: '11.'
                                }]
                            },{
                                columnWidth: .97,
                                layout: 'form',
                                items:[{
                                    xtype: 'label',
                                    text: lang('Alat tekhnis apa yang Bapak/Ibu gunakan ?')
                                }]
                            }]
                        },{
                            layout: 'column',
                            border: false,
                            items:[{
                                columnWidth: .03,
                                layout: 'form',
                                items:[{}]
                            },{
                                columnWidth: .47,
                                defaultType: 'checkboxfield',
                                items:[{
                                    boxLabel: lang('Timbangan digital'),
                                    name: 'tSurUseToolDigitalScale',
                                    inputValue: '1',
                                    id: 'tSurUseToolDigitalScale'
                                },{
                                    boxLabel: lang('Timbangan manual'),
                                    name: 'tSurUseToolManualScale',
                                    inputValue: '1',
                                    id: 'tSurUseToolManualScale'
                                },{
                                    boxLabel: lang('Aquaboy atau alat pengukur kadar air lainnya'),
                                    name: 'tSurUseToolAquaboy',
                                    inputValue: '1',
                                    id: 'tSurUseToolAquaboy'
                                },{
                                    boxLabel: lang('Alat penjemur dengan menggunakan tenaga matahari'),
                                    name: 'tSurUseToolSolarDryer',
                                    inputValue: '1',
                                    id: 'tSurUseToolSolarDryer'
                                },{
                                    boxLabel: lang('Alat pengering dengan bahan bakar'),
                                    name: 'tSurUseToolFuelDryer',
                                    inputValue: '1',
                                    id: 'tSurUseToolFuelDryer'
                                }]
                            },{
                                columnWidth: .5,
                                defaultType: 'checkboxfield',
                                items:[{
                                    boxLabel: lang('Mesin ayak'),
                                    name: 'tSurUseToolAyakMachine',
                                    inputValue: '1',
                                    id: 'tSurUseToolAyakMachine'
                                },{
                                    boxLabel: lang('Lantai jemur'),
                                    name: 'tSurUseToolFloorDryer',
                                    inputValue: '1',
                                    id: 'tSurUseToolFloorDryer'
                                },{
                                    boxLabel: lang('Gudang'),
                                    name: 'tSurUseToolWarehouse',
                                    inputValue: '1',
                                    id: 'tSurUseToolWarehouse'
                                },{
                                    boxLabel: lang('Box fermentasi'),
                                    name: 'tSurUseToolFermentBox',
                                    inputValue: '1',
                                    id: 'tSurUseToolFermentBox'
                                }]
                            }]
                        },{
                            layout: 'column',
                            border: false,
                            items:[{
                                columnWidth: .03,
                                layout: 'form',
                                items:[{
                                    xtype: 'label',
                                    text: '12.'
                                }]
                            },{
                                columnWidth: .97,
                                layout: 'form',
                                items:[{
                                    xtype: 'label',
                                    text: lang('Berapa besar modal kerja Bapak/Ibu yang tersedia ?')
                                }]
                            }]
                        },{
                            layout: 'column',
                            border: false,
                            items:[{
                                columnWidth: .03,
                                layout: 'form',
                                items:[{}]
                            },{
                                columnWidth: .25,
                                layout: 'form',
                                items:[{
                                    xtype: 'label',
                                    text: 'a. '+lang('Modal sendiri')
                                }]
                            },{
                                columnWidth: .2,
                                layout: 'form',
                                items:[{
                                    xtype: 'numericfield',
                                    id: 'tSurFundValueFromSelf',
                                    name: 'tSurFundValueFromSelf',
                                    allowDecimals: false,
                                    allowNegative: false,
                                    minValue: 0
                                }]
                            },{
                                columnWidth: .1,
                                layout: 'form',
                                items:[{
                                    xtype: 'label',
                                    style: 'margin-left:8px',
                                    text: lang('rupiah')
                                }]
                            }]
                        },{
                            layout: 'column',
                            border: false,
                            items:[{
                                columnWidth: .03,
                                layout: 'form',
                                items:[{}]
                            },{
                                columnWidth: .25,
                                layout: 'form',
                                items:[{
                                    xtype: 'label',
                                    text: 'b. '+lang('Modal dari meminjam')
                                }]
                            },{
                                columnWidth: .2,
                                layout: 'form',
                                items:[{
                                    xtype: 'numericfield',
                                    id: 'tSurFundValueFromLoan',
                                    name: 'tSurFundValueFromLoan',
                                    allowDecimals: false,
                                    allowNegative: false,
                                    minValue: 0
                                }]
                            },{
                                columnWidth: .1,
                                layout: 'form',
                                items:[{
                                    xtype: 'label',
                                    style: 'margin-left:8px',
                                    text: lang('rupiah')
                                }]
                            }]
                        },{
                            layout: 'column',
                            border: false,
                            items:[{
                                columnWidth: .03,
                                layout: 'form',
                                items:[{
                                    xtype: 'label',
                                    text: '13.'
                                }]
                            },{
                                columnWidth: .97,
                                layout: 'form',
                                items:[{
                                    xtype: 'label',
                                    text: lang('Bagaimana Bapak/Ibu menentukan harga ?')
                                }]
                            }]
                        },{
                            layout: 'column',
                            border: false,
                            items:[{
                                columnWidth: .03,
                                layout: 'form',
                                items:[{}]
                            },{
                                columnWidth: .97,
                                layout:{
                                    type:'hbox',
                                    align:'stretch'
                                },
                                xtype: 'radiogroup',
                                defaults: {
                                    flex:true
                                },
                                items: [{
                                    name: 'tSurPriceSource',
                                    id: 'tSurPriceSource1',
                                    boxLabel: lang('Dari informasi SMS yang diberikan oleh pihak swasta (contoh: Cargill, Ecom, Mars, dll.)'),
                                    inputValue: '1',
                                    listeners:{
                                        change: function() {
                                            return false;
                                        }
                                    }
                                },{
                                    name: 'tSurPriceSource',
                                    id: 'tSurPriceSource2',
                                    boxLabel: lang('Melihat harga yang ditawarkan pedagang kakao lain'),
                                    inputValue: '2',
                                    listeners:{
                                        change: function() {
                                            return false;
                                        }
                                    }
                                }]
                            }]
                        },{
                            layout: 'column',
                            border: false,
                            items:[{
                                columnWidth: .03,
                                layout: 'form',
                                items:[{
                                    xtype: 'label',
                                    text: '14.'
                                }]
                            },{
                                columnWidth: .97,
                                layout: 'form',
                                items:[{
                                    xtype: 'label',
                                    text: lang('Berapa rata-rata margin per kg biji kakao yang Bapak/Ibu terima ?')
                                }]
                            }]
                        },{
                            layout: 'column',
                            border: false,
                            items:[{
                                columnWidth: .03,
                                layout: 'form',
                                items:[{}]
                            },{
                                columnWidth: .2,
                                layout: 'form',
                                items:[{
                                    xtype: 'label',
                                    text: 'a. '+lang('Minimum')
                                }]
                            },{
                                columnWidth: .2,
                                layout: 'form',
                                items:[{
                                    xtype: 'numericfield',
                                    id: 'tSurAverageMarginCacaoPerKgReceivedMin',
                                    name: 'tSurAverageMarginCacaoPerKgReceivedMin',
                                    allowDecimals: false,
                                    allowNegative: false,
                                    minValue: 0
                                }]
                            },{
                                columnWidth: .1,
                                layout: 'form',
                                items:[{
                                    xtype: 'label',
                                    style: 'margin-left:8px',
                                    text: lang('rupiah')
                                }]
                            }]
                        },{
                            layout: 'column',
                            border: false,
                            items:[{
                                columnWidth: .03,
                                layout: 'form',
                                items:[{}]
                            },{
                                columnWidth: .2,
                                layout: 'form',
                                items:[{
                                    xtype: 'label',
                                    text: 'b. '+lang('Maximum')
                                }]
                            },{
                                columnWidth: .2,
                                layout: 'form',
                                items:[{
                                    xtype: 'numericfield',
                                    id: 'tSurAverageMarginCacaoPerKgReceivedMax',
                                    name: 'tSurAverageMarginCacaoPerKgReceivedMax',
                                    allowDecimals: false,
                                    allowNegative: false,
                                    minValue: 0
                                }]
                            },{
                                columnWidth: .1,
                                layout: 'form',
                                items:[{
                                    xtype: 'label',
                                    style: 'margin-left:8px',
                                    text: lang('rupiah')
                                }]
                            }]
                        },{
                            layout: 'column',
                            border: false,
                            items:[{
                                columnWidth: .03,
                                layout: 'form',
                                items:[{
                                    xtype: 'label',
                                    text: '15.'
                                }]
                            },{
                                columnWidth: .97,
                                layout: 'form',
                                items:[{
                                    xtype: 'label',
                                    text: lang('Bagaimana Bapak/Ibu menentukan kualitas biji kakao ? (kualitas yang dimaksud di sini adalah kualitas secara umum; biasanya berhubungan dengan sampah, jamur, kadar air dll.)')
                                }]
                            }]
                        },{
                            layout: 'column',
                            border: false,
                            items:[{
                                columnWidth: .03,
                                layout: 'form',
                                items:[{}]
                            },{
                                columnWidth: .37,
                                layout: 'form',
                                items:[{
                                    xtype: 'radiofield',
                                    name: 'tSurQualityCheckCacaoBeans',
                                    id: 'tSurQualityCheckCacaoBeans1',
                                    boxLabel: lang('Secara manual berdasarkan pengalaman; melihat penampakan fisik; tanpa alat apapun juga'),
                                    inputValue: '1',
                                    listeners:{
                                        change: function() {
                                            return false;
                                        }
                                    }
                                }]
                            },{
                                columnWidth: .3,
                                layout: 'form',
                                items:[{
                                    xtype: 'radiofield',
                                    name: 'tSurQualityCheckCacaoBeans',
                                    id: 'tSurQualityCheckCacaoBeans2',
                                    boxLabel: lang('Menggunakan aqua boy'),
                                    inputValue: '2',
                                    listeners:{
                                        change: function() {
                                            return false;
                                        }
                                    }
                                }]
                            },{
                                columnWidth: .3,
                                layout: 'form',
                                items:[{
                                    xtype: 'radiofield',
                                    name: 'tSurQualityCheckCacaoBeans',
                                    id: 'tSurQualityCheckCacaoBeans3',
                                    boxLabel: lang('Cut test'),
                                    inputValue: '3',
                                    listeners:{
                                        change: function() {
                                            return false;
                                        }
                                    }
                                }]
                            }]
                        },{
                            layout: 'column',
                            border: false,
                            items:[{
                                columnWidth: .03,
                                layout: 'form',
                                items:[{
                                    xtype: 'label',
                                    text: '16.'
                                }]
                            },{
                                columnWidth: .97,
                                layout: 'form',
                                items:[{
                                    xtype: 'label',
                                    text: lang('Kapan biasanya Bapak/Ibu membayar klien Bapak/Ibu ?')
                                }]
                            }]
                        },{
                            layout: 'column',
                            border: false,
                            items:[{
                                columnWidth: .03,
                                layout: 'form',
                                items:[{}]
                            },{
                                columnWidth: .47,
                                layout: 'form',
                                items:[{
                                    xtype: 'radiofield',
                                    boxLabel: lang('Semuanya di hari yang sama'),
                                    name: 'tSurWhenPayClient',
                                    inputValue: '1',
                                    id: 'tSurWhenPayClient1',
                                    listeners:{
                                        change: function() {
                                            return false;
                                        }
                                    }
                                },{
                                    xtype: 'radiofield',
                                    boxLabel: lang('Sebagian di hari yang sama, sebagian lain di hari lain'),
                                    name: 'tSurWhenPayClient',
                                    inputValue: '2',
                                    id: 'tSurWhenPayClient2',
                                    listeners:{
                                        change: function() {
                                            return false;
                                        }
                                    }
                                }]
                            },{
                                columnWidth: .5,
                                layout: 'form',
                                items:[{
                                    xtype: 'radiofield',
                                    boxLabel: lang('Biasanya dalam waktu 3 hari (maksimum 3 hari)'),
                                    name: 'tSurWhenPayClient',
                                    inputValue: '3',
                                    id: 'tSurWhenPayClient3',
                                    listeners:{
                                        change: function() {
                                            return false;
                                        }
                                    }
                                },{
                                    xtype: 'radiofield',
                                    boxLabel: lang('Biasanya butuh beberapa hari (lebih dari 3 hari)'),
                                    name: 'tSurWhenPayClient',
                                    inputValue: '4',
                                    id: 'tSurWhenPayClient4',
                                    listeners:{
                                        change: function() {
                                            return false;
                                        }
                                    }
                                }]
                            }]
                        },{
                            layout: 'column',
                            border: false,
                            items:[{
                                columnWidth: .03,
                                layout: 'form',
                                items:[{
                                    xtype: 'label',
                                    text: '17.'
                                }]
                            },{
                                columnWidth: .97,
                                layout: 'form',
                                items:[{
                                    xtype: 'label',
                                    text: lang('Bagaimana Bapak/Ibu membayar klien/petani Bapak/Ibu untuk pembelian biji kakao ?')
                                }]
                            }]
                        },{
                            layout: 'column',
                            border: false,
                            items:[{
                                columnWidth: .03,
                                layout: 'form',
                                items:[{}]
                            },{
                                columnWidth: .47,
                                layout: 'form',
                                items:[{
                                    xtype: 'radiofield',
                                    boxLabel: lang('Tunai'),
                                    name: 'tSurPayClientMethod',
                                    inputValue: '1',
                                    id: 'tSurPayClientMethod1',
                                    listeners:{
                                        change: function() {
                                            return false;
                                        }
                                    }
                                },{
                                    xtype: 'radiofield',
                                    boxLabel: lang('Transfer Bank'),
                                    name: 'tSurPayClientMethod',
                                    inputValue: '2',
                                    id: 'tSurPayClientMethod2',
                                    listeners:{
                                        change: function() {
                                            return false;
                                        }
                                    }
                                }]
                            },{
                                columnWidth: .5,
                                layout: 'form',
                                items:[{
                                    xtype: 'radiofield',
                                    boxLabel: lang('Cek'),
                                    name: 'tSurPayClientMethod',
                                    inputValue: '3',
                                    id: 'tSurPayClientMethod3',
                                    listeners:{
                                        change: function() {
                                            return false;
                                        }
                                    }
                                },{
                                    xtype: 'radiofield',
                                    boxLabel: lang('Lainnya'),
                                    name: 'tSurPayClientMethod',
                                    inputValue: '4',
                                    id: 'tSurPayClientMethod4',
                                    listeners:{
                                        change: function() {
                                            return false;
                                        }
                                    }
                                }]
                            }]
                        }]
                    },{
                        xtype: 'panel',
                        title: 'III. '+lang('Pertanyaan Traceability'),
                        padding: 10,
                        frame:true,
                        style:'background-color:#F0F0F0;margin-top:10px;',
                        items:[{
                            layout: 'column',
                            border: false,
                            items: [{
                                columnWidth: .03,
                                layout: 'form',
                                items:[{
                                    xtype: 'label',
                                    text: '18.',
                                }]
                            },{
                                columnWidth: .47,
                                layout: 'form',
                                items:[{
                                    xtype: 'label',
                                    text: lang('Apakah Bapak/Ibu menjual biji kakao yang sertifikasi ?')
                                }]
                            },{
                                columnWidth: .5,
                                layout:{
                                    type:'hbox',
                                    align:'stretch'
                                },
                                xtype: 'radiogroup',
                                defaults: {
                                    flex:true
                                },
                                items: [{
                                    name: 'tSurSellCertifiedCacaoBeans',
                                    id: 'tSurSellCertifiedCacaoBeans1',
                                    boxLabel: lang('Ya'),
                                    inputValue: '1',
                                    listeners:{
                                        change: function() {
                                            return false;
                                        }
                                    }
                                },{
                                    name: 'tSurSellCertifiedCacaoBeans',
                                    id: 'tSurSellCertifiedCacaoBeans2',
                                    boxLabel: lang('Tidak'),
                                    inputValue: '2',
                                    listeners:{
                                        change: function() {
                                            return false;
                                        }
                                    }
                                }]
                            }]
                        },{
                            layout: 'column',
                            border: false,
                            items: [{
                                columnWidth: .03,
                                layout: 'form',
                                items:[{
                                    xtype: 'label',
                                    text: '19.',
                                }]
                            },{
                                columnWidth: .97,
                                layout: 'form',
                                items:[{
                                    xtype: 'label',
                                    text: lang('Apakah Bapak/Ibu mengetahui asal biji kakao yang Bapak/Ibu terima ?')
                                }]
                            }]
                        },{
                            layout: 'column',
                            border: false,
                            items: [{
                                columnWidth: .03,
                                layout: 'form',
                                items:[{}]
                            },{
                                columnWidth: .2,
                                layout: 'form',
                                items:[{
                                    xtype: 'label',
                                    text: 'a. '+lang('Biji kakao sertifikasi')
                                }]
                            },{
                                columnWidth: .5,
                                layout:{
                                    type:'hbox',
                                    align:'stretch'
                                },
                                xtype: 'radiogroup',
                                defaults: {
                                    flex:true
                                },
                                items: [{
                                    name: 'tSurKnownCertifiedCacaoBeans',
                                    id: 'tSurKnownCertifiedCacaoBeans1',
                                    boxLabel: lang('Ya'),
                                    inputValue: '1',
                                    listeners:{
                                        change: function() {
                                            return false;
                                        }
                                    }
                                },{
                                    name: 'tSurKnownCertifiedCacaoBeans',
                                    id: 'tSurKnownCertifiedCacaoBeans2',
                                    boxLabel: lang('Tidak'),
                                    inputValue: '2',
                                    listeners:{
                                        change: function() {
                                            return false;
                                        }
                                    }
                                }]
                            }]
                        },{
                            layout: 'column',
                            border: false,
                            items: [{
                                columnWidth: .03,
                                layout: 'form',
                                items:[{}]
                            },{
                                columnWidth: .2,
                                layout: 'form',
                                items:[{
                                    xtype: 'label',
                                    text: 'b. '+lang('Biji kakao non-sertifikasi')
                                }]
                            },{
                                columnWidth: .5,
                                layout:{
                                    type:'hbox',
                                    align:'stretch'
                                },
                                xtype: 'radiogroup',
                                defaults: {
                                    flex:true
                                },
                                items: [{
                                    name: 'tSurKnownNonCertifiedCacaoBeans',
                                    id: 'tSurKnownNonCertifiedCacaoBeans1',
                                    boxLabel: lang('Ya'),
                                    inputValue: '1',
                                    listeners:{
                                        change: function() {
                                            return false;
                                        }
                                    }
                                },{
                                    name: 'tSurKnownNonCertifiedCacaoBeans',
                                    id: 'tSurKnownNonCertifiedCacaoBeans2',
                                    boxLabel: lang('Tidak'),
                                    inputValue: '2',
                                    listeners:{
                                        change: function() {
                                            return false;
                                        }
                                    }
                                }]
                            }]
                        },{
                            layout: 'column',
                            border: false,
                            items: [{
                                columnWidth: .03,
                                layout: 'form',
                                items:[{
                                    xtype: 'label',
                                    text: '20.',
                                }]
                            },{
                                columnWidth: .97,
                                layout: 'form',
                                items:[{
                                    xtype: 'label',
                                    text: lang('Apakah Bapak/Ibu menggunakan sebuah system untuk menelusuri asal biji kakao yang Bapak/Ibu terima ?')
                                }]
                            }]
                        },{
                            layout: 'column',
                            border: false,
                            items: [{
                                columnWidth: .03,
                                layout: 'form',
                                items:[{}]
                            },{
                                columnWidth: .2,
                                layout: 'form',
                                items:[{
                                    xtype: 'label',
                                    text: 'a. '+lang('Biji kakao sertifikasi')
                                }]
                            },{
                                columnWidth: .5,
                                layout:{
                                    type:'hbox',
                                    align:'stretch'
                                },
                                xtype: 'radiogroup',
                                defaults: {
                                    flex:true
                                },
                                items: [{
                                    name: 'tSurUseSystemTraceCertifiedCacaoBeans',
                                    id: 'tSurUseSystemTraceCertifiedCacaoBeans1',
                                    boxLabel: lang('Ya'),
                                    inputValue: '1',
                                    listeners:{
                                        change: function() {
                                            return false;
                                        }
                                    }
                                },{
                                    name: 'tSurUseSystemTraceCertifiedCacaoBeans',
                                    id: 'tSurUseSystemTraceCertifiedCacaoBeans2',
                                    boxLabel: lang('Tidak'),
                                    inputValue: '2',
                                    listeners:{
                                        change: function() {
                                            return false;
                                        }
                                    }
                                }]
                            }]
                        },{
                            layout: 'column',
                            border: false,
                            items: [{
                                columnWidth: .03,
                                layout: 'form',
                                items:[{}]
                            },{
                                columnWidth: .2,
                                layout: 'form',
                                items:[{
                                    xtype: 'label',
                                    text: 'b. '+lang('Biji kakao non-sertifikasi')
                                }]
                            },{
                                columnWidth: .5,
                                layout:{
                                    type:'hbox',
                                    align:'stretch'
                                },
                                xtype: 'radiogroup',
                                defaults: {
                                    flex:true
                                },
                                items: [{
                                    name: 'tSurUseSystemTraceNonCertifiedCacaoBeans',
                                    id: 'tSurUseSystemTraceNonCertifiedCacaoBeans1',
                                    boxLabel: lang('Ya'),
                                    inputValue: '1',
                                    listeners:{
                                        change: function() {
                                            return false;
                                        }
                                    }
                                },{
                                    name: 'tSurUseSystemTraceNonCertifiedCacaoBeans',
                                    id: 'tSurUseSystemTraceNonCertifiedCacaoBeans2',
                                    boxLabel: lang('Tidak'),
                                    inputValue: '2',
                                    listeners:{
                                        change: function() {
                                            return false;
                                        }
                                    }
                                }]
                            }]
                        },{
                            layout: 'column',
                            border: false,
                            items: [{
                                columnWidth: .03,
                                layout: 'form',
                                items:[{
                                    xtype: 'label',
                                    text: '21.',
                                }]
                            },{
                                columnWidth: .97,
                                layout: 'form',
                                items:[{
                                    xtype: 'label',
                                    text: lang('Apakah Bapak/Ibu menggunakan sebuah system untuk mencatat kemana biji kakao yang dijual oleh Bapak/Ibu ?')
                                }]
                            }]
                        },{
                            layout: 'column',
                            border: false,
                            items: [{
                                columnWidth: .03,
                                layout: 'form',
                                items:[{}]
                            },{
                                columnWidth: .2,
                                layout: 'form',
                                items:[{
                                    xtype: 'label',
                                    text: 'a. '+lang('Biji kakao sertifikasi')
                                }]
                            },{
                                columnWidth: .5,
                                layout:{
                                    type:'hbox',
                                    align:'stretch'
                                },
                                xtype: 'radiogroup',
                                defaults: {
                                    flex:true
                                },
                                items: [{
                                    name: 'tSurTraceSellingCertifiedCacaoBeans',
                                    id: 'tSurTraceSellingCertifiedCacaoBeans1',
                                    boxLabel: lang('Ya'),
                                    inputValue: '1',
                                    listeners:{
                                        change: function() {
                                            return false;
                                        }
                                    }
                                },{
                                    name: 'tSurTraceSellingCertifiedCacaoBeans',
                                    id: 'tSurTraceSellingCertifiedCacaoBeans2',
                                    boxLabel: lang('Tidak'),
                                    inputValue: '2',
                                    listeners:{
                                        change: function() {
                                            return false;
                                        }
                                    }
                                }]
                            }]
                        },{
                            layout: 'column',
                            border: false,
                            items: [{
                                columnWidth: .03,
                                layout: 'form',
                                items:[{}]
                            },{
                                columnWidth: .2,
                                layout: 'form',
                                items:[{
                                    xtype: 'label',
                                    text: 'b. '+lang('Biji kakao non-sertifikasi')
                                }]
                            },{
                                columnWidth: .5,
                                layout:{
                                    type:'hbox',
                                    align:'stretch'
                                },
                                xtype: 'radiogroup',
                                defaults: {
                                    flex:true
                                },
                                items: [{
                                    name: 'tSurTraceSellingNonCertifiedCacaoBeans',
                                    id: 'tSurTraceSellingNonCertifiedCacaoBeans1',
                                    boxLabel: lang('Ya'),
                                    inputValue: '1',
                                    listeners:{
                                        change: function() {
                                            return false;
                                        }
                                    }
                                },{
                                    name: 'tSurTraceSellingNonCertifiedCacaoBeans',
                                    id: 'tSurTraceSellingNonCertifiedCacaoBeans2',
                                    boxLabel: lang('Tidak'),
                                    inputValue: '2',
                                    listeners:{
                                        change: function() {
                                            return false;
                                        }
                                    }
                                }]
                            }]
                        },{
                            layout: 'column',
                            border: false,
                            items: [{
                                columnWidth: .03,
                                layout: 'form',
                                items:[{
                                    xtype: 'label',
                                    text: '22.',
                                }]
                            },{
                                columnWidth: .97,
                                layout: 'form',
                                items:[{
                                    xtype: 'label',
                                    text: lang('Apakah Bapak/Ibu ada catatan tertulis untuk setiap transaksi Bapak/Ibu ?')
                                }]
                            }]
                        },{
                            layout: 'column',
                            border: false,
                            items: [{
                                columnWidth: .03,
                                layout: 'form',
                                items:[{}]
                            },{
                                columnWidth: .2,
                                layout: 'form',
                                items:[{
                                    xtype: 'label',
                                    text: 'a. '+lang('Biji kakao sertifikasi')
                                }]
                            },{
                                columnWidth: .5,
                                layout:{
                                    type:'hbox',
                                    align:'stretch'
                                },
                                xtype: 'radiogroup',
                                defaults: {
                                    flex:true
                                },
                                items: [{
                                    name: 'tSurRecordTransCertifiedCacaoBeans',
                                    id: 'tSurRecordTransCertifiedCacaoBeans1',
                                    boxLabel: lang('Ya'),
                                    inputValue: '1',
                                    listeners:{
                                        change: function() {
                                            return false;
                                        }
                                    }
                                },{
                                    name: 'tSurRecordTransCertifiedCacaoBeans',
                                    id: 'tSurRecordTransCertifiedCacaoBeans2',
                                    boxLabel: lang('Tidak'),
                                    inputValue: '2',
                                    listeners:{
                                        change: function() {
                                            return false;
                                        }
                                    }
                                }]
                            }]
                        },{
                            layout: 'column',
                            border: false,
                            items: [{
                                columnWidth: .03,
                                layout: 'form',
                                items:[{}]
                            },{
                                columnWidth: .2,
                                layout: 'form',
                                items:[{
                                    xtype: 'label',
                                    text: 'b. '+lang('Biji kakao non-sertifikasi')
                                }]
                            },{
                                columnWidth: .5,
                                layout:{
                                    type:'hbox',
                                    align:'stretch'
                                },
                                xtype: 'radiogroup',
                                defaults: {
                                    flex:true
                                },
                                items: [{
                                    name: 'tSurRecordTransNonCertifiedCacaoBeans',
                                    id: 'tSurRecordTransNonCertifiedCacaoBeans1',
                                    boxLabel: lang('Ya'),
                                    inputValue: '1',
                                    listeners:{
                                        change: function() {
                                            return false;
                                        }
                                    }
                                },{
                                    name: 'tSurRecordTransNonCertifiedCacaoBeans',
                                    id: 'tSurRecordTransNonCertifiedCacaoBeans2',
                                    boxLabel: lang('Tidak'),
                                    inputValue: '2',
                                    listeners:{
                                        change: function() {
                                            return false;
                                        }
                                    }
                                }]
                            }]
                        },{
                            layout: 'column',
                            border: false,
                            items: [{
                                columnWidth: .03,
                                layout: 'form',
                                items:[{
                                    xtype: 'label',
                                    text: '23.',
                                }]
                            },{
                                columnWidth: .97,
                                layout: 'form',
                                items:[{
                                    xtype: 'label',
                                    text: lang('Jika ya, apakah Bapak/Ibu melakukan analisa atas transaksi-transaksi tersebut ?')
                                }]
                            }]
                        },{
                            layout: 'column',
                            border: false,
                            items: [{
                                columnWidth: .03,
                                layout: 'form',
                                items:[{}]
                            },{
                                columnWidth: .2,
                                layout: 'form',
                                items:[{
                                    xtype: 'label',
                                    text: 'a. '+lang('Biji kakao sertifikasi')
                                }]
                            },{
                                columnWidth: .5,
                                layout:{
                                    type:'hbox',
                                    align:'stretch'
                                },
                                xtype: 'radiogroup',
                                defaults: {
                                    flex:true
                                },
                                items: [{
                                    name: 'tSurAnalyzeTransCertifiedCacaoBeans',
                                    id: 'tSurAnalyzeTransCertifiedCacaoBeans1',
                                    boxLabel: lang('Ya'),
                                    inputValue: '1',
                                    listeners:{
                                        change: function() {
                                            return false;
                                        }
                                    }
                                },{
                                    name: 'tSurAnalyzeTransCertifiedCacaoBeans',
                                    id: 'tSurAnalyzeTransCertifiedCacaoBeans2',
                                    boxLabel: lang('Tidak'),
                                    inputValue: '2',
                                    listeners:{
                                        change: function() {
                                            return false;
                                        }
                                    }
                                }]
                            }]
                        },{
                            layout: 'column',
                            border: false,
                            items: [{
                                columnWidth: .03,
                                layout: 'form',
                                items:[{}]
                            },{
                                columnWidth: .2,
                                layout: 'form',
                                items:[{
                                    xtype: 'label',
                                    text: 'b. '+lang('Biji kakao non-sertifikasi')
                                }]
                            },{
                                columnWidth: .5,
                                layout:{
                                    type:'hbox',
                                    align:'stretch'
                                },
                                xtype: 'radiogroup',
                                defaults: {
                                    flex:true
                                },
                                items: [{
                                    name: 'tSurAnalyzeTransNonCertifiedCacaoBeans',
                                    id: 'tSurAnalyzeTransNonCertifiedCacaoBeans1',
                                    boxLabel: lang('Ya'),
                                    inputValue: '1',
                                    listeners:{
                                        change: function() {
                                            return false;
                                        }
                                    }
                                },{
                                    name: 'tSurAnalyzeTransNonCertifiedCacaoBeans',
                                    id: 'tSurAnalyzeTransNonCertifiedCacaoBeans2',
                                    boxLabel: lang('Tidak'),
                                    inputValue: '2',
                                    listeners:{
                                        change: function() {
                                            return false;
                                        }
                                    }
                                }]
                            }]
                        },{
                            layout: 'column',
                            border: false,
                            items: [{
                                columnWidth: .03,
                                layout: 'form',
                                items:[{
                                    xtype: 'label',
                                    text: '24.'
                                }]
                            },{
                                columnWidth: .47,
                                layout: 'form',
                                items:[{
                                    xtype: 'label',
                                    text: lang('Jika ya, bisakah kami suatu hari melihat analisa tersebut ?')
                                }]
                            },{
                                columnWidth: .5,
                                layout:{
                                    type:'hbox',
                                    align:'stretch'
                                },
                                xtype: 'radiogroup',
                                defaults: {
                                    flex:true
                                },
                                items: [{
                                    name: 'tSurShowAnalyzeResult',
                                    id: 'tSurShowAnalyzeResult1',
                                    boxLabel: lang('Ya'),
                                    inputValue: '1',
                                    listeners:{
                                        change: function() {
                                            return false;
                                        }
                                    }
                                },{
                                    name: 'tSurShowAnalyzeResult',
                                    id: 'tSurShowAnalyzeResult2',
                                    boxLabel: lang('Tidak'),
                                    inputValue: '2',
                                    listeners:{
                                        change: function() {
                                            return false;
                                        }
                                    }
                                }]
                            }]
                        },{
                            layout: 'column',
                            border: false,
                            items: [{
                                columnWidth: .03,
                                layout: 'form',
                                items:[{
                                    xtype: 'label',
                                    text: '25.'
                                }]
                            },{
                                columnWidth: .97,
                                layout: 'form',
                                items:[{
                                    xtype: 'label',
                                    text: lang('Bagaimana bisnis model Bapak/Ibu sekarang ini ?')
                                }]
                            }]
                        },{
                            layout: 'column',
                            border: false,
                            items: [{
                                columnWidth: .03,
                                layout: 'form',
                                items:[{}]
                            },{
                                columnWidth: .47,
                                layout: 'form',
                                items:[{
                                    xtype: 'radiofield',
                                    boxLabel: lang('Dengan petani: cash and carry (beli putus)'),
                                    name: 'tSurBusinessModel',
                                    inputValue: '1',
                                    id: 'tSurBusinessModel1',
                                    listeners:{
                                        change: function() {
                                            return false;
                                        }
                                    }
                                },{
                                    xtype: 'radiofield',
                                    boxLabel: lang('Dengan petani: investment to farmers'),
                                    name: 'tSurBusinessModel',
                                    inputValue: '2',
                                    id: 'tSurBusinessModel2',
                                    listeners:{
                                        change: function() {
                                            return false;
                                        }
                                    }
                                },{
                                    xtype: 'radiofield',
                                    boxLabel: lang('Dengan pihak swasta: cash and carry'),
                                    name: 'tSurBusinessModel',
                                    inputValue: '3',
                                    id: 'tSurBusinessModel3',
                                    listeners:{
                                        change: function() {
                                            return false;
                                        }
                                    }
                                }]
                            },{
                                columnWidth: .5,
                                layout: 'form',
                                items:[{
                                    xtype: 'radiofield',
                                    boxLabel: lang('Dengan pihak swasta: in advance (contract) with off-takers'),
                                    name: 'tSurBusinessModel',
                                    inputValue: '4',
                                    id: 'tSurBusinessModel4',
                                    listeners:{
                                        change: function() {
                                            return false;
                                        }
                                    }
                                },{
                                    layout: 'column',
                                    border: false,
                                    items:[{
                                        columnWidth: .2,
                                        layout: 'form',
                                        items:[{
                                            xtype: 'radiofield',
                                            boxLabel: lang('Lainnya'),
                                            name: 'tSurBusinessModel',
                                            inputValue: '5',
                                            id: 'tSurBusinessModel5',
                                            listeners:{
                                                change: function() {
                                                    if(this.checked == true){
                                                        Ext.getCmp('tSurBusinessModelOther').setDisabled(false);
                                                    }else{
                                                        Ext.getCmp('tSurBusinessModelOther').setDisabled(true);
                                                    }

                                                    return false;
                                                }
                                            }
                                        }]
                                    },{
                                        columnWidth: .8,
                                        layout: 'form',
                                        items:[{
                                            xtype: 'textfield',
                                            id: 'tSurBusinessModelOther',
                                            name: 'tSurBusinessModelOther',
                                            disabled: true
                                        }]
                                    }]
                                }]
                            }]
                        },{
                            layout: 'column',
                            border: false,
                            items: [{
                                columnWidth: .03,
                                layout: 'form',
                                items:[{
                                    xtype: 'label',
                                    text: '26.'
                                }]
                            },{
                                columnWidth: .97,
                                layout: 'form',
                                items:[{
                                    xtype: 'label',
                                    text: lang('Kemana Bapak/Ibu biasanya menjual biji kakao ? (bisa pilih lebih dari satu)')
                                }]
                            }]
                        },{
                            layout: 'column',
                            border: false,
                            items: [{
                                columnWidth: .03,
                                layout: 'form',
                                items:[{}]
                            },{
                                columnWidth: .47,
                                defaultType: 'checkboxfield',
                                items:[{
                                    boxLabel: lang('Pedagang lebih besar'),
                                    name: 'tSurSellToBigTrader',
                                    inputValue: '1',
                                    id: 'tSurSellToBigTrader'
                                },{
                                    boxLabel: lang('Koperasi'),
                                    name: 'tSurSellToCoop',
                                    inputValue: '1',
                                    id: 'tSurSellToCoop'
                                },{
                                    boxLabel: lang('Perusahaan besar'),
                                    name: 'tSurSellToBigCompany',
                                    inputValue: '1',
                                    id: 'tSurSellToBigCompany'
                                },{
                                    boxLabel: lang('Pabrik/prosesor'),
                                    name: 'tSurSellToFactory',
                                    inputValue: '1',
                                    id: 'tSurSellToFactory'
                                }]
                            },{
                                columnWidth: .5,
                                layout: 'form',
                                items:[{
                                    xtype: 'checkboxfield',
                                    boxLabel: lang('Eksportir'),
                                    name: 'tSurSellToExport',
                                    inputValue: '1',
                                    id: 'tSurSellToExport'
                                },{
                                    xtype: 'checkboxfield',
                                    boxLabel: lang('Pinjam modal dari orang lain saat panen raya'),
                                    name: 'tSurSellToLoaner',
                                    inputValue: '1',
                                    id: 'tSurSellToLoaner'
                                },{
                                    layout: 'column',
                                    border: false,
                                    items:[{
                                        columnWidth: .2,
                                        layout: 'form',
                                        items:[{
                                            xtype: 'checkboxfield',
                                            boxLabel: lang('Lainnya'),
                                            name: 'tSurSellToOther',
                                            inputValue: '5',
                                            id: 'tSurSellToOther',
                                            listeners:{
                                                change: function() {
                                                    if(this.checked == true){
                                                        Ext.getCmp('tSurSellToOtherText').setDisabled(false);
                                                    }else{
                                                        Ext.getCmp('tSurSellToOtherText').setDisabled(true);
                                                    }

                                                    return false;
                                                }
                                            }
                                        }]
                                    },{
                                        columnWidth: .8,
                                        layout: 'form',
                                        items:[{
                                            xtype: 'textfield',
                                            id: 'tSurSellToOtherText',
                                            name: 'tSurSellToOtherText',
                                            disabled: true
                                        }]
                                    }]
                                }]
                            }]
                        },{
                            layout: 'column',
                            border: false,
                            items: [{
                                columnWidth: .03,
                                layout: 'form',
                                items:[{
                                    xtype: 'label',
                                    text: '27.'
                                }]
                            },{
                                columnWidth: .97,
                                layout: 'form',
                                items:[{
                                    xtype: 'label',
                                    text: lang('Bagaimana Bapak/Ibu memilih buyer/offtaker ?')
                                }]
                            }]
                        },{
                            layout: 'column',
                            border: false,
                            items: [{
                                columnWidth: .03,
                                layout: 'form',
                                items:[{}]
                            },{
                                columnWidth: .47,
                                defaultType: 'checkboxfield',
                                items:[{
                                    boxLabel: lang('Saya memiliki kontrak dengan manufaktur/prosesor/eksportir'),
                                    name: 'tSurChooseBuyerContract',
                                    inputValue: '1',
                                    id: 'tSurChooseBuyerContract',
                                    listeners:{
                                        change: function() {
                                            if(this.checked == true){
                                                Ext.getCmp('tSurBuyerInfoDetail').setDisabled(false);
                                            }else{
                                                Ext.getCmp('tSurBuyerInfoDetail').setDisabled(true);
                                            }
                                        }
                                    }
                                },{
                                    boxLabel: lang('Harga tertinggi yang ditawarkan'),
                                    name: 'tSurChooseBuyerHighestValue',
                                    inputValue: '1',
                                    id: 'tSurChooseBuyerHighestValue'
                                },{
                                    boxLabel: lang('Jarak'),
                                    name: 'tSurChooseBuyerDistance',
                                    inputValue: '1',
                                    id: 'tSurChooseBuyerDistance'
                                }]
                            },{
                                columnWidth: .5,
                                defaultType: 'checkboxfield',
                                items:[{
                                    boxLabel: lang('Pembayaran yang cepat'),
                                    name: 'tSurChooseBuyerFastPayment',
                                    inputValue: '1',
                                    id: 'tSurChooseBuyerFastPayment'
                                },{
                                    boxLabel: lang('Menawarkan layanan tambahan seperti transport'),
                                    name: 'tSurChooseBuyerFacility',
                                    inputValue: '1',
                                    id: 'tSurChooseBuyerFacility'
                                },{
                                    boxLabel: lang('Menyediakan modal kerja'),
                                    name: 'tSurChooseBuyerFundingSource',
                                    inputValue: '1',
                                    id: 'tSurChooseBuyerFundingSource'
                                }]
                            }]
                        },{
                            layout: 'column',
                            border: false,
                            items: [{
                                columnWidth: .03,
                                layout: 'form',
                                items:[{
                                    xtype: 'label',
                                    text: '28.'
                                }]
                            },{
                                columnWidth: .47,
                                layout: 'form',
                                items:[{
                                    xtype: 'label',
                                    text: lang('Berhubungan dengan pertanyaan sebelumnya, apa nama manufaktur/prosesor/eksporter dengan siapa Bapak/Ibu memiliki kontrak :')
                                }]
                            },{
                                columnWidth: .5,
                                layout: 'form',
                                items:[{
                                    xtype: 'textfield',
                                    id: 'tSurBuyerInfoDetail',
                                    name: 'tSurBuyerInfoDetail',
                                    disabled: true
                                }]
                            }]
                        },{
                            layout: 'column',
                            border: false,
                            items: [{
                                columnWidth: .03,
                                layout: 'form',
                                items:[{
                                    xtype: 'label',
                                    text: '29.'
                                }]
                            },{
                                columnWidth: .97,
                                layout: 'form',
                                items:[{
                                    xtype: 'label',
                                    text: lang('Apa masalah-masalah besar yang harus Bapak/Ibu hadapi ketika membeli biji kakao ?')
                                }]
                            }]
                        },{
                            layout: 'column',
                            border: false,
                            items: [{
                                columnWidth: .03,
                                layout: 'form',
                                items:[{}]
                            },{
                                columnWidth: .47,
                                defaultType: 'checkboxfield',
                                items:[{
                                    boxLabel: lang('Modal kerja yang tidak cukup'),
                                    name: 'tSurProblemBuycacaoFund',
                                    inputValue: '1',
                                    id: 'tSurProblemBuycacaoFund'
                                },{
                                    boxLabel: lang('Kualitas biji kakao yang dikirim'),
                                    name: 'tSurProblemBuycacaoQuality',
                                    inputValue: '1',
                                    id: 'tSurProblemBuycacaoQuality'
                                },{
                                    boxLabel: lang('Tidak ada yang mengambil biji tersebut'),
                                    name: 'tSurProblemBuycacaoTransport',
                                    inputValue: '1',
                                    id: 'tSurProblemBuycacaoTransport'
                                }]
                            },{
                                columnWidth: .47,
                                defaultType: 'checkboxfield',
                                items:[{
                                    boxLabel: lang('Fluktuasi harga yang terkadang tidak bisa diprediksi'),
                                    name: 'tSurProblemBuycacaoPriceFluc',
                                    inputValue: '1',
                                    id: 'tSurProblemBuycacaoPriceFluc'
                                },{
                                    boxLabel: lang('Pedagang lain yang merusak harga'),
                                    name: 'tSurProblemBuycacaoPriceComp',
                                    inputValue: '1',
                                    id: 'tSurProblemBuycacaoPriceComp'
                                }]
                            }]
                        },{
                            html: '<div style="border-bottom:1px solid black;margin-bottom:5px;margin-top:5px;"></div>'
                        },{
                            layout: 'column',
                            border: false,
                            items: [{
                                columnWidth: .03,
                                layout: 'form',
                                items:[{
                                    xtype: 'label',
                                    text: '30.'
                                }]
                            },{
                                columnWidth: .47,
                                layout: 'form',
                                items:[{
                                    xtype: 'label',
                                    text: lang('Apakah Bapak/Ibu petani kakao ?')
                                }]
                            },{
                                columnWidth: .5,
                                layout:{
                                    type:'hbox',
                                    align:'stretch'
                                },
                                xtype: 'radiogroup',
                                defaults: {
                                    flex:true
                                },
                                items: [{
                                    name: 'tSurIsCacaoFarmer',
                                    id: 'tSurIsCacaoFarmer1',
                                    boxLabel: lang('Ya'),
                                    inputValue: '1',
                                    listeners:{
                                        change: function() {
                                            if(this.checked == true){
                                                Ext.getCmp('tSurCacaoLandSize').setDisabled(false);
                                                Ext.getCmp('tSurAverageProduction').setDisabled(false);
                                            }else{
                                                Ext.getCmp('tSurCacaoLandSize').setDisabled(true);
                                                Ext.getCmp('tSurAverageProduction').setDisabled(true);
                                            }

                                            return false;
                                        }
                                    }
                                },{
                                    name: 'tSurIsCacaoFarmer',
                                    id: 'tSurIsCacaoFarmer2',
                                    boxLabel: lang('Tidak'),
                                    inputValue: '2',
                                    listeners:{
                                        change: function() {
                                            return false;
                                        }
                                    }
                                }]
                            }]
                        },{
                            layout: 'column',
                            border: false,
                            items: [{
                                columnWidth: .03,
                                layout: 'form',
                                items:[{
                                    xtype: 'label',
                                    text: '31.'
                                }]
                            },{
                                columnWidth: .47,
                                layout: 'form',
                                items:[{
                                    xtype: 'label',
                                    text: lang('Ukuran kebun kakao')
                                }]
                            },{
                                columnWidth: .2,
                                layout: 'form',
                                items:[{
                                    xtype: 'numericfield',
                                    id: 'tSurCacaoLandSize',
                                    name: 'tSurCacaoLandSize',
                                    allowDecimals: true,
                                    allowNegative: false,
                                    minValue: 0,
                                    disabled: true
                                }]
                            }]
                        },{
                            layout: 'column',
                            border: false,
                            items: [{
                                columnWidth: .03,
                                layout: 'form',
                                items:[{
                                    xtype: 'label',
                                    text: '32.'
                                }]
                            },{
                                columnWidth: .47,
                                layout: 'form',
                                items:[{
                                    xtype: 'label',
                                    text: lang('Produksi rata-rata')
                                }]
                            },{
                                columnWidth: .2,
                                layout: 'form',
                                items:[{
                                    xtype: 'numericfield',
                                    id: 'tSurAverageProduction',
                                    name: 'tSurAverageProduction',
                                    allowDecimals: true,
                                    allowNegative: false,
                                    minValue: 0,
                                    disabled: true
                                }]
                            }]
                        },{
                            html: '<div style="border-bottom:1px solid black;margin-bottom:5px;margin-top:5px;"></div>'
                        },{
                            layout: 'column',
                            border: false,
                            items: [{
                                columnWidth: .03,
                                layout: 'form',
                                items:[{
                                    xtype: 'label',
                                    text: '33.'
                                }]
                            },{
                                columnWidth: .47,
                                layout: 'form',
                                items:[{
                                    xtype: 'label',
                                    text: lang('Apakah Bapak/Ibu dulu petani kakao ?')
                                }]
                            },{
                                columnWidth: .5,
                                layout:{
                                    type:'hbox',
                                    align:'stretch'
                                },
                                xtype: 'radiogroup',
                                defaults: {
                                    flex:true
                                },
                                items: [{
                                    name: 'tSurIsExCacaoFarmer',
                                    id: 'tSurIsExCacaoFarmer1',
                                    boxLabel: lang('Ya'),
                                    inputValue: '1',
                                    listeners:{
                                        change: function() {
                                            if(this.checked == true){
                                                Ext.getCmp('tSurExCacaoLandSize').setDisabled(false);
                                                Ext.getCmp('tSurExAverageProduction').setDisabled(false);
                                            }else{
                                                Ext.getCmp('tSurExCacaoLandSize').setDisabled(true);
                                                Ext.getCmp('tSurExAverageProduction').setDisabled(true);
                                            }

                                            return false;
                                        }
                                    }
                                },{
                                    name: 'tSurIsExCacaoFarmer',
                                    id: 'tSurIsExCacaoFarmer2',
                                    boxLabel: lang('Tidak'),
                                    inputValue: '2',
                                    listeners:{
                                        change: function() {
                                            return false;
                                        }
                                    }
                                }]
                            }]
                        },{
                            layout: 'column',
                            border: false,
                            items: [{
                                columnWidth: .03,
                                layout: 'form',
                                items:[{
                                    xtype: 'label',
                                    text: '34.'
                                }]
                            },{
                                columnWidth: .47,
                                layout: 'form',
                                items:[{
                                    xtype: 'label',
                                    text: lang('Ukuran kebun kakao')
                                }]
                            },{
                                columnWidth: .2,
                                layout: 'form',
                                items:[{
                                    xtype: 'numericfield',
                                    id: 'tSurExCacaoLandSize',
                                    name: 'tSurExCacaoLandSize',
                                    allowDecimals: true,
                                    allowNegative: false,
                                    minValue: 0,
                                    disabled: true
                                }]
                            }]
                        },{
                            layout: 'column',
                            border: false,
                            items: [{
                                columnWidth: .03,
                                layout: 'form',
                                items:[{
                                    xtype: 'label',
                                    text: '35.'
                                }]
                            },{
                                columnWidth: .47,
                                layout: 'form',
                                items:[{
                                    xtype: 'label',
                                    text: lang('Produksi rata-rata')
                                }]
                            },{
                                columnWidth: .2,
                                layout: 'form',
                                items:[{
                                    xtype: 'numericfield',
                                    id: 'tSurExAverageProduction',
                                    name: 'tSurExAverageProduction',
                                    allowDecimals: true,
                                    allowNegative: false,
                                    minValue: 0,
                                    disabled: true
                                }]
                            }]
                        },{
                            html: '<div style="border-bottom:1px solid black;margin-bottom:5px;margin-top:5px;"></div>'
                        },{
                            layout: 'column',
                            border: false,
                            items: [{
                                columnWidth: .03,
                                layout: 'form',
                                items:[{
                                    xtype: 'label',
                                    text: '36.'
                                }]
                            },{
                                columnWidth: .47,
                                layout: 'form',
                                items:[{
                                    xtype: 'label',
                                    text: lang('Apakah Bapak/Ibu menyediakan pupuk atau pestisida pada klien Bapak/Ibu yang kemudian bisa mereka bayar setelahnya ?')
                                }]
                            },{
                                columnWidth: .5,
                                layout:{
                                    type:'hbox',
                                    align:'stretch'
                                },
                                xtype: 'radiogroup',
                                defaults: {
                                    flex:true
                                },
                                items: [{
                                    name: 'tSurProvideFertPest',
                                    id: 'tSurProvideFertPest1',
                                    boxLabel: lang('Ya'),
                                    inputValue: '1',
                                    listeners:{
                                        change: function() {
                                            return false;
                                        }
                                    }
                                },{
                                    name: 'tSurProvideFertPest',
                                    id: 'tSurProvideFertPest2',
                                    boxLabel: lang('Tidak'),
                                    inputValue: '2',
                                    listeners:{
                                        change: function() {
                                            return false;
                                        }
                                    }
                                }]
                            }]
                        },{
                            layout: 'column',
                            border: false,
                            items: [{
                                columnWidth: .03,
                                layout: 'form',
                                items:[{
                                    xtype: 'label',
                                    text: '37.'
                                }]
                            },{
                                columnWidth: .47,
                                layout: 'form',
                                items:[{
                                    xtype: 'label',
                                    text: lang('Apakah Bapak/Ibu memberi pinjaman pada petani-petani ?')
                                }]
                            },{
                                columnWidth: .5,
                                layout:{
                                    type:'hbox',
                                    align:'stretch'
                                },
                                xtype: 'radiogroup',
                                defaults: {
                                    flex:true
                                },
                                items: [{
                                    name: 'tSurProvideLoan',
                                    id: 'tSurProvideLoan1',
                                    boxLabel: lang('Ya'),
                                    inputValue: '1',
                                    listeners:{
                                        change: function() {
                                            if(this.checked == true){
                                                Ext.getCmp('tSurLoanCreditCount').setDisabled(false);
                                                Ext.getCmp('tSurLoanCreditValueTotal').setDisabled(false);

                                                Ext.getCmp('tSurPayLoanMethod1').setDisabled(false);
                                                Ext.getCmp('tSurPayLoanMethod2').setDisabled(false);
                                                Ext.getCmp('tSurPayLoanMethod3').setDisabled(false);
                                                Ext.getCmp('tSurPayLoanMethod4').setDisabled(false);
                                                Ext.getCmp('tSurPayLoanMethod5').setDisabled(false);

                                                Ext.getCmp('tSurLoanerHaveTo1').setDisabled(false);
                                                Ext.getCmp('tSurLoanerHaveTo2').setDisabled(false);
                                            }else{
                                                Ext.getCmp('tSurLoanCreditCount').setDisabled(true);
                                                Ext.getCmp('tSurLoanCreditValueTotal').setDisabled(true);

                                                Ext.getCmp('tSurPayLoanMethod1').setDisabled(true);
                                                Ext.getCmp('tSurPayLoanMethod2').setDisabled(true);
                                                Ext.getCmp('tSurPayLoanMethod3').setDisabled(true);
                                                Ext.getCmp('tSurPayLoanMethod4').setDisabled(true);
                                                Ext.getCmp('tSurPayLoanMethod5').setDisabled(true);
                                                Ext.getCmp('tSurPayLoanMethodOther').setDisabled(true);

                                                Ext.getCmp('tSurLoanerHaveTo1').setDisabled(true);
                                                Ext.getCmp('tSurLoanerHaveTo2').setDisabled(true);
                                            }

                                            return false;
                                        }
                                    }
                                },{
                                    name: 'tSurProvideLoan',
                                    id: 'tSurProvideLoan2',
                                    boxLabel: lang('Tidak'),
                                    inputValue: '2',
                                    listeners:{
                                        change: function() {
                                            return false;
                                        }
                                    }
                                }]
                            }]
                        },{
                            layout: 'column',
                            border: false,
                            items: [{
                                columnWidth: .03,
                                layout: 'form',
                                items:[{
                                    xtype: 'label',
                                    text: '38.'
                                }]
                            },{
                                columnWidth: .47,
                                layout: 'form',
                                items:[{
                                    xtype: 'label',
                                    text: lang('Ada berapa jumlah pinjaman yang diberi Bapak/Ibu dan belum selesai dibayar ?')
                                }]
                            },{
                                columnWidth: .1,
                                layout: 'form',
                                items:[{
                                    xtype: 'numericfield',
                                    id: 'tSurLoanCreditCount',
                                    name: 'tSurLoanCreditCount',
                                    allowDecimals: true,
                                    allowNegative: false,
                                    minValue: 0,
                                    disabled: true
                                }]
                            },{
                                columnWidth: .2,
                                layout: 'form',
                                style: 'margin-left:8px;',
                                items:[{
                                    xtype: 'label',
                                    text: lang('pinjaman')
                                }]
                            }]
                        },{
                            layout: 'column',
                            border: false,
                            items: [{
                                columnWidth: .03,
                                layout: 'form',
                                items:[{
                                    xtype: 'label',
                                    text: '39.'
                                }]
                            },{
                                columnWidth: .47,
                                layout: 'form',
                                items:[{
                                    xtype: 'label',
                                    text: lang('Berapa besar pinjaman yang belum selesai dibayar tersebut ? (total)')
                                }]
                            },{
                                columnWidth: .2,
                                layout: 'form',
                                items:[{
                                    xtype: 'numericfield',
                                    id: 'tSurLoanCreditValueTotal',
                                    name: 'tSurLoanCreditValueTotal',
                                    allowDecimals: true,
                                    allowNegative: false,
                                    minValue: 0,
                                    disabled: true
                                }]
                            },{
                                columnWidth: .2,
                                layout: 'form',
                                style: 'margin-left:8px;',
                                items:[{
                                    xtype: 'label',
                                    text: lang('rupiah')
                                }]
                            }]
                        },{
                            layout: 'column',
                            border: false,
                            items: [{
                                columnWidth: .03,
                                layout: 'form',
                                items:[{
                                    xtype: 'label',
                                    text: '40.'
                                }]
                            },{
                                columnWidth: .97,
                                layout: 'form',
                                items:[{
                                    xtype: 'label',
                                    text: lang('Bagaimana biasanya pinjaman-pinjaman tersebut dibayar ?')
                                }]
                            }]
                        },{
                            layout: 'column',
                            border: false,
                            items: [{
                                columnWidth: .03,
                                layout: 'form',
                                items:[{}]
                            },{
                                columnWidth: .47,
                                layout: 'form',
                                items:[{
                                    xtype: 'radiofield',
                                    boxLabel: lang('Di panen berikutnya'),
                                    name: 'tSurPayLoanMethod',
                                    inputValue: '1',
                                    id: 'tSurPayLoanMethod1',
                                    disabled: true,
                                    listeners:{
                                        change: function() {
                                            return false;
                                        }
                                    }
                                },{
                                    xtype: 'radiofield',
                                    boxLabel: lang('Cicilan/angsuran'),
                                    name: 'tSurPayLoanMethod',
                                    inputValue: '2',
                                    id: 'tSurPayLoanMethod2',
                                    disabled: true,
                                    listeners:{
                                        change: function() {
                                            return false;
                                        }
                                    }
                                },{
                                    xtype: 'radiofield',
                                    boxLabel: lang('Dicicil dengan dipotong ketika petani menjual kakao ke saya'),
                                    name: 'tSurPayLoanMethod',
                                    inputValue: '3',
                                    id: 'tSurPayLoanMethod3',
                                    disabled: true,
                                    listeners:{
                                        change: function() {
                                            return false;
                                        }
                                    }
                                }]
                            },{
                                columnWidth: .5,
                                layout: 'form',
                                items:[{
                                    xtype: 'radiofield',
                                    boxLabel: lang('Membayar langsung'),
                                    name: 'tSurPayLoanMethod',
                                    inputValue: '4',
                                    id: 'tSurPayLoanMethod4',
                                    disabled: true,
                                    listeners:{
                                        change: function() {
                                            return false;
                                        }
                                    }
                                },{
                                    layout: 'column',
                                    border: false,
                                    items:[{
                                        columnWidth: .2,
                                        layout: 'form',
                                        items:[{
                                            xtype: 'radiofield',
                                            boxLabel: lang('Lainnya'),
                                            name: 'tSurPayLoanMethod',
                                            inputValue: '5',
                                            id: 'tSurPayLoanMethod5',
                                            disabled: true,
                                            listeners:{
                                                change: function() {
                                                    if(this.checked == true){
                                                        Ext.getCmp('tSurPayLoanMethodOther').setDisabled(false);
                                                    }else{
                                                        Ext.getCmp('tSurPayLoanMethodOther').setDisabled(true);
                                                    }
                                                    return false;
                                                }
                                            }
                                        }]
                                    },{
                                        columnWidth: .8,
                                        layout: 'form',
                                        items:[{
                                            xtype: 'textfield',
                                            id: 'tSurPayLoanMethodOther',
                                            name: 'tSurPayLoanMethodOther',
                                            disabled: true
                                        }]
                                    }]
                                }]
                            }]
                        },{
                            layout: 'column',
                            border: false,
                            items: [{
                                columnWidth: .03,
                                layout: 'form',
                                items:[{
                                    xtype: 'label',
                                    text: '41.'
                                }]
                            },{
                                columnWidth: .97,
                                layout: 'form',
                                items:[{
                                    xtype: 'label',
                                    text: lang('Para petani yang meminjam dari Bapak/Ibu, apakah mereka :')
                                }]
                            }]
                        },{
                            layout: 'column',
                            border: false,
                            items: [{
                                columnWidth: .03,
                                layout: 'form',
                                items:[{}]
                            },{
                                columnWidth: .47,
                                layout: 'form',
                                items:[{
                                    xtype: 'radiofield',
                                    boxLabel: lang('Harus membayar bunga'),
                                    name: 'tSurLoanerHaveTo',
                                    inputValue: '1',
                                    id: 'tSurLoanerHaveTo1',
                                    disabled: true,
                                    listeners:{
                                        change: function() {
                                            return false;
                                        }
                                    }
                                }]
                            },{
                                columnWidth: .5,
                                layout: 'form',
                                items:[{
                                    xtype: 'radiofield',
                                    boxLabel: lang('Mendapat harga lebih rendah'),
                                    name: 'tSurLoanerHaveTo',
                                    inputValue: '2',
                                    id: 'tSurLoanerHaveTo2',
                                    disabled: true,
                                    listeners:{
                                        change: function() {
                                            return false;
                                        }
                                    }
                                }]
                            }]
                        },{
                            layout: 'column',
                            border: false,
                            items: [{
                                columnWidth: .03,
                                layout: 'form',
                                items:[{
                                    xtype: 'label',
                                    text: '42.'
                                }]
                            },{
                                columnWidth: .97,
                                layout: 'form',
                                items:[{
                                    xtype: 'label',
                                    text: lang('Apa yang Bapak/Ibu lakukan bila mengalami kerugian ?')
                                }]
                            }]
                        },{
                            layout: 'column',
                            border: false,
                            items: [{
                                columnWidth: .03,
                                layout: 'form',
                                items:[{}]
                            },{
                                columnWidth: .47,
                                layout: 'form',
                                items:[{
                                    xtype: 'radiofield',
                                    boxLabel: lang('Saya biasanya tidak mengalami kerugian'),
                                    name: 'tSurLossAction',
                                    inputValue: '1',
                                    id: 'tSurLossAction1',
                                    listeners:{
                                        change: function() {
                                            return false;
                                        }
                                    }
                                },{
                                    xtype: 'radiofield',
                                    boxLabel: lang('Saya mempunyai simpanan untuk  menutup kerugian-kerugian saya'),
                                    name: 'tSurLossAction',
                                    inputValue: '2',
                                    id: 'tSurLossAction2',
                                    listeners:{
                                        change: function() {
                                            return false;
                                        }
                                    }
                                }]
                            },{
                                columnWidth: .5,
                                layout: 'form',
                                items:[{
                                    xtype: 'radiofield',
                                    boxLabel: lang('Nego harga dengan toke/pedagang besar'),
                                    name: 'tSurLossAction',
                                    inputValue: '3',
                                    id: 'tSurLossAction3',
                                    listeners:{
                                        change: function() {
                                            return false;
                                        }
                                    }
                                }]
                            }]
                        },{
                            layout: 'column',
                            border: false,
                            items: [{
                                columnWidth: .03,
                                layout: 'form',
                                items:[{
                                    xtype: 'label',
                                    text: '43.'
                                }]
                            },{
                                columnWidth: .97,
                                layout: 'form',
                                items:[{
                                    xtype: 'label',
                                    text: lang('Apakah Bapak/Ibu adalah agen Bank ? (branchless banking agent)')
                                }]
                            }]
                        },{
                            layout: 'column',
                            border: false,
                            items: [{
                                columnWidth: .03,
                                layout: 'form',
                                items:[{}]
                            },{
                                columnWidth: .47,
                                layout: 'form',
                                items:[{
                                    xtype: 'radiofield',
                                    boxLabel: lang('Ya, BTPN Wow!'),
                                    name: 'tSurIsBankAgent',
                                    inputValue: '1',
                                    id: 'tSurIsBankAgent1',
                                    listeners:{
                                        change: function() {
                                            return false;
                                        }
                                    }
                                },{
                                    xtype: 'radiofield',
                                    boxLabel: lang('Ya, BRI Links'),
                                    name: 'tSurIsBankAgent',
                                    inputValue: '2',
                                    id: 'tSurIsBankAgent2',
                                    listeners:{
                                        change: function() {
                                            return false;
                                        }
                                    }
                                },{
                                    xtype: 'radiofield',
                                    boxLabel: lang('Ya, lainnya'),
                                    name: 'tSurIsBankAgent',
                                    inputValue: '3',
                                    id: 'tSurIsBankAgent3',
                                    listeners:{
                                        change: function() {
                                            return false;
                                        }
                                    }
                                }]
                            },{
                                columnWidth: .47,
                                layout: 'form',
                                items:[{
                                    xtype: 'radiofield',
                                    boxLabel: lang('Saya tidak tahu apa itu'),
                                    name: 'tSurIsBankAgent',
                                    inputValue: '4',
                                    id: 'tSurIsBankAgent4',
                                    listeners:{
                                        change: function() {
                                            return false;
                                        }
                                    }
                                },{
                                    xtype: 'radiofield',
                                    boxLabel: lang('Saya pernah mendengar tentang itu, tapi saya bukan agen'),
                                    name: 'tSurIsBankAgent',
                                    inputValue: '5',
                                    id: 'tSurIsBankAgent5',
                                    listeners:{
                                        change: function() {
                                            return false;
                                        }
                                    }
                                }]
                            }]
                        },{
                            layout: 'column',
                            border: false,
                            items: [{
                                columnWidth: .03,
                                layout: 'form',
                                items:[{
                                    xtype: 'label',
                                    text: '44.'
                                }]
                            },{
                                columnWidth: .47,
                                layout: 'form',
                                items:[{
                                    xtype: 'label',
                                    text: lang('Apakah Bapak/Ibu punya usaha lain ?')
                                }]
                            },{
                                columnWidth: .5,
                                layout:{
                                    type:'hbox',
                                    align:'stretch'
                                },
                                xtype: 'radiogroup',
                                defaults: {
                                    flex:true
                                },
                                items: [{
                                    name: 'tSurHaveOtherBusiness',
                                    id: 'tSurHaveOtherBusiness1',
                                    boxLabel: lang('Ya'),
                                    inputValue: '1',
                                    listeners:{
                                        change: function() {
                                            return false;
                                        }
                                    }
                                },{
                                    name: 'tSurHaveOtherBusiness',
                                    id: 'tSurHaveOtherBusiness2',
                                    boxLabel: lang('Tidak'),
                                    inputValue: '2',
                                    listeners:{
                                        change: function() {
                                            return false;
                                        }
                                    }
                                }]
                            }]
                        },{
                            layout: 'column',
                            border: false,
                            items: [{
                                columnWidth: .03,
                                layout: 'form',
                                items:[{
                                    xtype: 'label',
                                    text: '45.'
                                }]
                            },{
                                columnWidth: .97,
                                layout: 'form',
                                items:[{
                                    xtype: 'label',
                                    text: lang('Menurut Bapak/Ibu, apa masalah-masalah terbesar petani ? (maximum 3 pilihan)')
                                }]
                            }]
                        },{
                            layout: 'column',
                            border: false,
                            items: [{
                                columnWidth: .03,
                                layout: 'form',
                                items:[{}]
                            },{
                                columnWidth: .47,
                                layout: 'form',
                                defaultType: 'checkboxfield',
                                items:[{
                                    boxLabel: lang('Produksi rendah'),
                                    name: 'tSurFarmerMainProblemLowProd',
                                    inputValue: '1',
                                    id: 'tSurFarmerMainProblemLowProd',
                                    listeners:{
                                        change: function() {
                                            if(this.checked == true){
                                                isValid = checkMax3FarmerProblem();
                                                if(isValid == false){
                                                    this.setValue(false);
                                                }
                                            }
                                        }
                                    }
                                },{
                                    boxLabel: lang('Pohon-pohon tua'),
                                    name: 'tSurFarmerMainProblemOldTree',
                                    inputValue: '1',
                                    id: 'tSurFarmerMainProblemOldTree',
                                    listeners:{
                                        change: function() {
                                            if(this.checked == true){
                                                isValid = checkMax3FarmerProblem();
                                                if(isValid == false){
                                                    this.setValue(false);
                                                }
                                            }
                                        }
                                    }
                                },{
                                    boxLabel: lang('Pengetahuan yang kurang mengenai praktek-praktek pertanian yang baik'),
                                    name: 'tSurFarmerMainProblemNoKnowledge',
                                    inputValue: '1',
                                    id: 'tSurFarmerMainProblemNoKnowledge',
                                    listeners:{
                                        change: function() {
                                            if(this.checked == true){
                                                isValid = checkMax3FarmerProblem();
                                                if(isValid == false){
                                                    this.setValue(false);
                                                }
                                            }
                                        }
                                    }
                                },{
                                    boxLabel: lang('Serangan hama'),
                                    name: 'tSurFarmerMainProblemPest',
                                    inputValue: '1',
                                    id: 'tSurFarmerMainProblemPest',
                                    listeners:{
                                        change: function() {
                                            if(this.checked == true){
                                                isValid = checkMax3FarmerProblem();
                                                if(isValid == false){
                                                    this.setValue(false);
                                                }
                                            }
                                        }
                                    }
                                },{
                                    boxLabel: lang('Kurang tahu bagaimana mengatasi serangan hama'),
                                    name: 'tSurFarmerMainProblemPestSolving',
                                    inputValue: '1',
                                    id: 'tSurFarmerMainProblemPestSolving',
                                    listeners:{
                                        change: function() {
                                            if(this.checked == true){
                                                isValid = checkMax3FarmerProblem();
                                                if(isValid == false){
                                                    this.setValue(false);
                                                }
                                            }
                                        }
                                    }
                                },{
                                    boxLabel: lang('Musim yang tidak menentu yang menyebabkan panen gagal atau tidak sesuai prediksi (perkiraan)'),
                                    name: 'tSurFarmerMainProblemSeasonChanging',
                                    inputValue: '1',
                                    id: 'tSurFarmerMainProblemSeasonChanging',
                                    listeners:{
                                        change: function() {
                                            if(this.checked == true){
                                                isValid = checkMax3FarmerProblem();
                                                if(isValid == false){
                                                    this.setValue(false);
                                                }
                                            }
                                        }
                                    }
                                }]
                            },{
                                columnWidth: .5,
                                layout: 'form',
                                defaultType: 'checkboxfield',
                                items:[{
                                    boxLabel: lang('Penyakit pada tanaman kakao yang menyebabkan produksi kakao sedikit/tidak ada'),
                                    name: 'tSurFarmerMainProblemDisease',
                                    inputValue: '1',
                                    id: 'tSurFarmerMainProblemDisease',
                                    listeners:{
                                        change: function() {
                                            if(this.checked == true){
                                                isValid = checkMax3FarmerProblem();
                                                if(isValid == false){
                                                    this.setValue(false);
                                                }
                                            }
                                        }
                                    }
                                },{
                                    boxLabel: lang('Daya dukung lahan yang semakin menurun'),
                                    name: 'tSurFarmerMainProblemLand',
                                    inputValue: '1',
                                    id: 'tSurFarmerMainProblemLand',
                                    listeners:{
                                        change: function() {
                                            if(this.checked == true){
                                                isValid = checkMax3FarmerProblem();
                                                if(isValid == false){
                                                    this.setValue(false);
                                                }
                                            }
                                        }
                                    }
                                },{
                                    boxLabel: lang('Petani kurang mempraktekkan ilmu tentang praktek pertanian yang baik'),
                                    name: 'tSurFarmerMainProblemLackSkill',
                                    inputValue: '1',
                                    id: 'tSurFarmerMainProblemLackSkill',
                                    listeners:{
                                        change: function() {
                                            if(this.checked == true){
                                                isValid = checkMax3FarmerProblem();
                                                if(isValid == false){
                                                    this.setValue(false);
                                                }
                                            }
                                        }
                                    }
                                },{
                                    boxLabel: lang('Petani mempunyai banyak komoditi (tidak fokus pada kakao)'),
                                    name: 'tSurFarmerMainProblemOtherComodity',
                                    inputValue: '1',
                                    id: 'tSurFarmerMainProblemOtherComodity',
                                    listeners:{
                                        change: function() {
                                            if(this.checked == true){
                                                isValid = checkMax3FarmerProblem();
                                                if(isValid == false){
                                                    this.setValue(false);
                                                }
                                            }
                                        }
                                    }
                                },{
                                    boxLabel: lang('Harga kakao yang rendah'),
                                    name: 'tSurFarmerMainProblemLowPrice',
                                    inputValue: '1',
                                    id: 'tSurFarmerMainProblemLowPrice',
                                    listeners:{
                                        change: function() {
                                            if(this.checked == true){
                                                isValid = checkMax3FarmerProblem();
                                                if(isValid == false){
                                                    this.setValue(false);
                                                }
                                            }
                                        }
                                    }
                                }]
                            }]
                        }]
                    },{
                        xtype: 'panel',
                        title: 'IV. '+lang('Pertanyaan Akses Keuangan'),
                        padding: 10,
                        frame:true,
                        style:'background-color:#F0F0F0;margin-top:10px;',
                        items:[{
                            layout: 'column',
                            border: false,
                            items: [{
                                columnWidth: .03,
                                layout: 'form',
                                items:[{
                                    xtype: 'label',
                                    text: '46.'
                                }]
                            },{
                                columnWidth: .47,
                                layout: 'form',
                                items:[{
                                    xtype: 'label',
                                    text: lang('Apakah Bapak/Ibu memiliki lebih dari satu rekening bank ?')
                                }]
                            },{
                                columnWidth: .5,
                                layout:{
                                    type:'hbox',
                                    align:'stretch'
                                },
                                xtype: 'radiogroup',
                                defaults: {
                                    flex:true
                                },
                                items: [{
                                    name: 'tSurMorethanOneBankAcc',
                                    id: 'tSurMorethanOneBankAcc1',
                                    boxLabel: lang('Ya'),
                                    inputValue: '1',
                                    listeners:{
                                        change: function() {
                                            return false;
                                        }
                                    }
                                },{
                                    name: 'tSurMorethanOneBankAcc',
                                    id: 'tSurMorethanOneBankAcc2',
                                    boxLabel: lang('Tidak'),
                                    inputValue: '2',
                                    listeners:{
                                        change: function() {
                                            return false;
                                        }
                                    }
                                }]
                            }]
                        },{
                            layout: 'column',
                            border: false,
                            items: [{
                                columnWidth: .03,
                                layout: 'form',
                                items:[{
                                    xtype: 'label',
                                    text: '47.'
                                }]
                            },{
                                columnWidth: .97,
                                layout: 'form',
                                items:[{
                                    xtype: 'label',
                                    text: lang('Seberapa sering Bapak/Ibu melakukan transaksi dengan bank (deposit uang, pengambilan uang, transfer uang) ?')
                                }]
                            }]
                        },{
                            layout: 'column',
                            border: false,
                            items: [{
                                columnWidth: .03,
                                layout: 'form',
                                items:[{}]
                            },{
                                columnWidth: .47,
                                layout: 'form',
                                items:[{
                                    xtype: 'radiofield',
                                    boxLabel: lang('Every day'),
                                    name: 'tSurBankTransactionFreq',
                                    inputValue: '1',
                                    id: 'tSurBankTransactionFreq1',
                                    listeners:{
                                        change: function() {
                                            return false;
                                        }
                                    }
                                },{
                                    xtype: 'radiofield',
                                    boxLabel: lang('2-4 times a week'),
                                    name: 'tSurBankTransactionFreq',
                                    inputValue: '2',
                                    id: 'tSurBankTransactionFreq2',
                                    listeners:{
                                        change: function() {
                                            return false;
                                        }
                                    }
                                },{
                                    xtype: 'radiofield',
                                    boxLabel: lang('Once a week'),
                                    name: 'tSurBankTransactionFreq',
                                    inputValue: '3',
                                    id: 'tSurBankTransactionFreq3',
                                    listeners:{
                                        change: function() {
                                            return false;
                                        }
                                    }
                                }]
                            },{
                                columnWidth: .5,
                                layout: 'form',
                                items:[{
                                    xtype: 'radiofield',
                                    boxLabel: lang('2-3 times per month'),
                                    name: 'tSurBankTransactionFreq',
                                    inputValue: '4',
                                    id: 'tSurBankTransactionFreq4',
                                    listeners:{
                                        change: function() {
                                            return false;
                                        }
                                    }
                                },{
                                    xtype: 'radiofield',
                                    boxLabel: lang('Once a month'),
                                    name: 'tSurBankTransactionFreq',
                                    inputValue: '5',
                                    id: 'tSurBankTransactionFreq5',
                                    listeners:{
                                        change: function() {
                                            return false;
                                        }
                                    }
                                },{
                                    xtype: 'radiofield',
                                    boxLabel: lang('Less frequent'),
                                    name: 'tSurBankTransactionFreq',
                                    inputValue: '6',
                                    id: 'tSurBankTransactionFreq6',
                                    listeners:{
                                        change: function() {
                                            return false;
                                        }
                                    }
                                }]
                            }]
                        },{
                            layout: 'column',
                            border: false,
                            items: [{
                                columnWidth: .03,
                                layout: 'form',
                                items:[{
                                    xtype: 'label',
                                    text: '48.'
                                }]
                            },{
                                columnWidth: .47,
                                layout: 'form',
                                items:[{
                                    xtype: 'label',
                                    text: lang('Selain modal kerja, apakah Bapak/Ibu menabung ?')
                                }]
                            },{
                                columnWidth: .5,
                                layout:{
                                    type:'hbox',
                                    align:'stretch'
                                },
                                xtype: 'radiogroup',
                                defaults: {
                                    flex:true
                                },
                                items: [{
                                    name: 'tSurSavingAsideFund',
                                    id: 'tSurSavingAsideFund1',
                                    boxLabel: lang('Ya'),
                                    inputValue: '1',
                                    listeners:{
                                        change: function() {
                                            return false;
                                        }
                                    }
                                },{
                                    name: 'tSurSavingAsideFund',
                                    id: 'tSurSavingAsideFund2',
                                    boxLabel: lang('Tidak'),
                                    inputValue: '2',
                                    listeners:{
                                        change: function() {
                                            return false;
                                        }
                                    }
                                }]
                            }]
                        },{
                            layout: 'column',
                            border: false,
                            items: [{
                                columnWidth: .03,
                                layout: 'form',
                                items:[{
                                    xtype: 'label',
                                    text: '49.'
                                }]
                            },{
                                columnWidth: .47,
                                layout: 'form',
                                items:[{
                                    xtype: 'label',
                                    text: lang('Kira-kira berapa besar modal kerja Bapak/Ibu ?')
                                }]
                            },{
                                columnWidth: .2,
                                layout: 'form',
                                items:[{
                                    xtype: 'numericfield',
                                    id: 'tSurFundValue',
                                    name: 'tSurFundValue',
                                    allowDecimals: false,
                                    allowNegative: false,
                                    minValue: 0
                                }]
                            },{
                                columnWidth: .15,
                                layout: 'form',
                                style: 'margin-left:8px;',
                                items:[{
                                    xtype: 'label',
                                    text: lang('rupiah')
                                }]
                            }]
                        },{
                            layout: 'column',
                            border: false,
                            items: [{
                                columnWidth: .03,
                                layout: 'form',
                                items:[{
                                    xtype: 'label',
                                    text: '50.'
                                }]
                            },{
                                columnWidth: .97,
                                layout: 'form',
                                items:[{
                                    xtype: 'label',
                                    text: lang('Apakah Bapak/Ibu ada pernah ada pinjaman ?')
                                }]
                            }]
                        },{
                            layout: 'column',
                            border: false,
                            items: [{
                                columnWidth: .03,
                                layout: 'form',
                                items:[{}]
                            },{
                                columnWidth: .47,
                                layout: 'form',
                                items:[{
                                    xtype: 'radiofield',
                                    boxLabel: lang('Ya, saya sekarang ini sedang ada pinjaman'),
                                    name: 'tSurHaveLoanBefore',
                                    inputValue: '1',
                                    id: 'tSurHaveLoanBefore1',
                                    listeners:{
                                        change: function() {
                                            return false;
                                        }
                                    }
                                },{
                                    xtype: 'radiofield',
                                    boxLabel: lang('Ya, saya dulu pernah meminjam tapi tidak sekarang ini'),
                                    name: 'tSurHaveLoanBefore',
                                    inputValue: '2',
                                    id: 'tSurHaveLoanBefore2',
                                    listeners:{
                                        change: function() {
                                            return false;
                                        }
                                    }
                                }]
                            },{
                                columnWidth: .5,
                                layout: 'form',
                                items:[{
                                    xtype: 'radiofield',
                                    boxLabel: lang('Tidak, saya tidak pernah meminjam'),
                                    name: 'tSurHaveLoanBefore',
                                    inputValue: '3',
                                    id: 'tSurHaveLoanBefore3',
                                    listeners:{
                                        change: function() {
                                            return false;
                                        }
                                    }
                                }]
                            }]
                        },{
                            layout: 'column',
                            border: false,
                            items: [{
                                columnWidth: .03,
                                layout: 'form',
                                items:[{
                                    xtype: 'label',
                                    text: '51.'
                                }]
                            },{
                                columnWidth: .47,
                                layout: 'form',
                                items:[{
                                    xtype: 'label',
                                    text: lang('Berapa besar pinjaman terakhir ?')
                                }]
                            },{
                                columnWidth: .2,
                                layout: 'form',
                                items:[{
                                    xtype: 'numericfield',
                                    id: 'tSurLastLoanValue',
                                    name: 'tSurLastLoanValue',
                                    allowDecimals: false,
                                    allowNegative: false,
                                    minValue: 0
                                }]
                            },{
                                columnWidth: .15,
                                layout: 'form',
                                style: 'margin-left:8px;',
                                items:[{
                                    xtype: 'label',
                                    text: lang('rupiah')
                                }]
                            }]
                        },{
                            layout: 'column',
                            border: false,
                            items: [{
                                columnWidth: .03,
                                layout: 'form',
                                items:[{
                                    xtype: 'label',
                                    text: '52.'
                                }]
                            },{
                                columnWidth: .47,
                                layout: 'form',
                                items:[{
                                    xtype: 'label',
                                    text: lang('Apakah pinjaman telah dibayar seluruhnya ?')
                                }]
                            },{
                                columnWidth: .5,
                                layout:{
                                    type:'hbox',
                                    align:'stretch'
                                },
                                xtype: 'radiogroup',
                                defaults: {
                                    flex:true
                                },
                                items: [{
                                    name: 'tSurLastLoanSettle',
                                    id: 'tSurLastLoanSettle1',
                                    boxLabel: lang('Ya'),
                                    inputValue: '1',
                                    listeners:{
                                        change: function() {
                                            return false;
                                        }
                                    }
                                },{
                                    name: 'tSurLastLoanSettle',
                                    id: 'tSurLastLoanSettle2',
                                    boxLabel: lang('Tidak'),
                                    inputValue: '2',
                                    listeners:{
                                        change: function() {
                                            return false;
                                        }
                                    }
                                }]
                            }]
                        },{
                            layout: 'column',
                            border: false,
                            items: [{
                                columnWidth: .03,
                                layout: 'form',
                                items:[{
                                    xtype: 'label',
                                    text: '53.'
                                }]
                            },{
                                columnWidth: .47,
                                layout: 'form',
                                items:[{
                                    xtype: 'label',
                                    text: lang('Berapa yang masih belum lunas dari pinjaman terakhir ?')
                                }]
                            },{
                                columnWidth: .2,
                                layout: 'form',
                                items:[{
                                    xtype: 'numericfield',
                                    id: 'tSurLastLoanCreditValue',
                                    name: 'tSurLastLoanCreditValue',
                                    allowDecimals: false,
                                    allowNegative: false,
                                    minValue: 0
                                }]
                            },{
                                columnWidth: .15,
                                layout: 'form',
                                style: 'margin-left:8px;',
                                items:[{
                                    xtype: 'label',
                                    text: lang('rupiah')
                                }]
                            }]
                        },{
                            layout: 'column',
                            border: false,
                            items: [{
                                columnWidth: .03,
                                layout: 'form',
                                items:[{
                                    xtype: 'label',
                                    text: '54.'
                                }]
                            },{
                                columnWidth: .97,
                                layout: 'form',
                                items:[{
                                    xtype: 'label',
                                    text: lang('Dimanakah bapak/ibu memperoleh pinjaman terakhir ?')
                                }]
                            }]
                        },{
                            layout: 'column',
                            border: false,
                            items: [{
                                columnWidth: .03,
                                layout: 'form',
                                items:[{}]
                            },{
                                columnWidth: .47,
                                defaultType: 'checkboxfield',
                                items:[{
                                    boxLabel: lang('Trader/kolektor/pedagang local/buying unit/muge'),
                                    name: 'tSurLastLoanSourceTrader',
                                    inputValue: '1',
                                    id: 'tSurLastLoanSourceTrader'
                                },{
                                    boxLabel: lang('Keluarga/teman'),
                                    name: 'tSurLastLoanSourceFamily',
                                    inputValue: '1',
                                    id: 'tSurLastLoanSourceFamily'
                                },{
                                    boxLabel: lang('Tengkulak'),
                                    name: 'tSurLastLoanSourceLoaner',
                                    inputValue: '1',
                                    id: 'tSurLastLoanSourceLoaner'
                                }]
                            },{
                                columnWidth: .5,
                                items:[{
                                    xtype: 'checkboxfield',
                                    boxLabel: lang('Bank'),
                                    name: 'tSurLastLoanSourceBank',
                                    inputValue: '1',
                                    id: 'tSurLastLoanSourceBank'
                                },{
                                    xtype: 'checkboxfield',
                                    boxLabel: lang('Koperasi'),
                                    name: 'tSurLastLoanSourceCoop',
                                    inputValue: '1',
                                    id: 'tSurLastLoanSourceCoop'
                                },{
                                    layout: 'column',
                                    border: false,
                                    items:[{
                                        columnWidth: .2,
                                        layout: 'form',
                                        items:[{
                                            xtype: 'checkboxfield',
                                            boxLabel: lang('Lainnya'),
                                            name: 'tSurLastLoanSourceOther',
                                            inputValue: '1',
                                            id: 'tSurLastLoanSourceOther',
                                            listeners:{
                                                change: function() {
                                                    if(this.checked == true){
                                                        Ext.getCmp('tSurLastLoanSourceOtherText').setDisabled(false);
                                                    }else{
                                                        Ext.getCmp('tSurLastLoanSourceOtherText').setDisabled(true);
                                                    }

                                                    return false;
                                                }
                                            }
                                        }]
                                    },{
                                        columnWidth: .8,
                                        layout: 'form',
                                        items:[{
                                            xtype: 'textfield',
                                            id: 'tSurLastLoanSourceOtherText',
                                            name: 'tSurLastLoanSourceOtherText',
                                            disabled: true
                                        }]
                                    }]
                                }]
                            }]
                        },{
                            layout: 'column',
                            border: false,
                            items: [{
                                columnWidth: .03,
                                layout: 'form',
                                items:[{
                                    xtype: 'label',
                                    text: '55.'
                                }]
                            },{
                                columnWidth: .97,
                                layout: 'form',
                                items:[{
                                    xtype: 'label',
                                    text: lang('Bagaimana Bapak/Ibu membayar pekerja ?')
                                }]
                            }]
                        },{
                            layout: 'column',
                            border: false,
                            items: [{
                                columnWidth: .03,
                                layout: 'form',
                                items:[{}]
                            },{
                                columnWidth: .47,
                                defaultType: 'checkboxfield',
                                items:[{
                                    boxLabel: lang('Gaji tetap'),
                                    name: 'tSurPayingStaffFixedSalary',
                                    inputValue: '1',
                                    id: 'tSurPayingStaffFixedSalary'
                                },{
                                    boxLabel: lang('Komisi'),
                                    name: 'tSurPayingStaffCommision',
                                    inputValue: '1',
                                    id: 'tSurPayingStaffCommision'
                                }]
                            },{
                                columnWidth: .5,
                                defaultType: 'checkboxfield',
                                items:[{
                                    boxLabel: lang('Usaha keluarga jadi tidak dibayar'),
                                    name: 'tSurPayingStaffFamilyNoPayment',
                                    inputValue: '1',
                                    id: 'tSurPayingStaffFamilyNoPayment'
                                }]
                            }]
                        },{
                            layout: 'column',
                            border: false,
                            items: [{
                                columnWidth: .03,
                                layout: 'form',
                                items:[{
                                    xtype: 'label',
                                    text: '56.'
                                }]
                            },{
                                columnWidth: .47,
                                layout: 'form',
                                items:[{
                                    xtype: 'label',
                                    text: lang('Orang bisa mempercayai saya')
                                }]
                            },{
                                columnWidth: .5,
                                layout:{
                                    type:'hbox',
                                    align:'stretch'
                                },
                                xtype: 'radiogroup',
                                defaults: {
                                    flex:true
                                },
                                items: [{
                                    name: 'tSurTrustedTrader',
                                    id: 'tSurTrustedTrader1',
                                    boxLabel: lang('Ya'),
                                    inputValue: '1',
                                    listeners:{
                                        change: function() {
                                            return false;
                                        }
                                    }
                                },{
                                    name: 'tSurTrustedTrader',
                                    id: 'tSurTrustedTrader2',
                                    boxLabel: lang('Tidak'),
                                    inputValue: '2',
                                    listeners:{
                                        change: function() {
                                            return false;
                                        }
                                    }
                                }]
                            }]
                        },{
                            layout: 'column',
                            border: false,
                            items: [{
                                columnWidth: .03,
                                layout: 'form',
                                items:[{
                                    xtype: 'label',
                                    text: '57.'
                                }]
                            },{
                                columnWidth: .47,
                                layout: 'form',
                                items:[{
                                    xtype: 'label',
                                    text: lang('Saya membutuhkan pinjaman dan saya pantas mendapatkannya')
                                }]
                            },{
                                columnWidth: .5,
                                layout:{
                                    type:'hbox',
                                    align:'stretch'
                                },
                                xtype: 'radiogroup',
                                defaults: {
                                    flex:true
                                },
                                items: [{
                                    name: 'tSurNeedLoanAndQualify',
                                    id: 'tSurNeedLoanAndQualify1',
                                    boxLabel: lang('Ya'),
                                    inputValue: '1',
                                    listeners:{
                                        change: function() {
                                            return false;
                                        }
                                    }
                                },{
                                    name: 'tSurNeedLoanAndQualify',
                                    id: 'tSurNeedLoanAndQualify2',
                                    boxLabel: lang('Tidak'),
                                    inputValue: '2',
                                    listeners:{
                                        change: function() {
                                            return false;
                                        }
                                    }
                                }]
                            }]
                        },{
                            layout: 'column',
                            border: false,
                            items: [{
                                columnWidth: .03,
                                layout: 'form',
                                items:[{
                                    xtype: 'label',
                                    text: '58.'
                                }]
                            },{
                                columnWidth: .47,
                                layout: 'form',
                                items:[{
                                    xtype: 'label',
                                    text: lang('Perdagangan kakao adalah bisnis yang menguntungkan')
                                }]
                            },{
                                columnWidth: .5,
                                layout:{
                                    type:'hbox',
                                    align:'stretch'
                                },
                                xtype: 'radiogroup',
                                defaults: {
                                    flex:true
                                },
                                items: [{
                                    name: 'tSurCacaoTraderIsProfitable',
                                    id: 'tSurCacaoTraderIsProfitable1',
                                    boxLabel: lang('Ya'),
                                    inputValue: '1',
                                    listeners:{
                                        change: function() {
                                            return false;
                                        }
                                    }
                                },{
                                    name: 'tSurCacaoTraderIsProfitable',
                                    id: 'tSurCacaoTraderIsProfitable2',
                                    boxLabel: lang('Tidak'),
                                    inputValue: '2',
                                    listeners:{
                                        change: function() {
                                            return false;
                                        }
                                    }
                                }]
                            }]
                        },{
                            layout: 'column',
                            border: false,
                            items: [{
                                columnWidth: .03,
                                layout: 'form',
                                items:[{
                                    xtype: 'label',
                                    text: '59.'
                                }]
                            },{
                                columnWidth: .47,
                                layout: 'form',
                                items:[{
                                    xtype: 'label',
                                    text: lang('Dibandingkan penduduk lain di desa saya, apakah Bapak/Ibu termasuk berada ?')
                                }]
                            },{
                                columnWidth: .5,
                                layout:{
                                    type:'hbox',
                                    align:'stretch'
                                },
                                xtype: 'radiogroup',
                                defaults: {
                                    flex:true
                                },
                                items: [{
                                    name: 'tSurWealthyPersonInSociety',
                                    id: 'tSurWealthyPersonInSociety1',
                                    boxLabel: lang('Ya'),
                                    inputValue: '1',
                                    listeners:{
                                        change: function() {
                                            return false;
                                        }
                                    }
                                },{
                                    name: 'tSurWealthyPersonInSociety',
                                    id: 'tSurWealthyPersonInSociety2',
                                    boxLabel: lang('Tidak'),
                                    inputValue: '2',
                                    listeners:{
                                        change: function() {
                                            return false;
                                        }
                                    }
                                },{
                                    name: 'tSurWealthyPersonInSociety',
                                    id: 'tSurWealthyPersonInSociety3',
                                    boxLabel: lang('Sama saja'),
                                    inputValue: '3',
                                    listeners:{
                                        change: function() {
                                            return false;
                                        }
                                    }
                                }]
                            }]
                        }]
                    }]
                }]
            }],
            buttons: [{
                text: lang('Save'),
                id: 'btnTraderSurveySave',
                margin: '5px',
                scale: 'large',
                ui: 's-button',
                cls: 's-blue',
                handler: function() {
                    var form = Ext.getCmp('traderSurFormData').getForm();
                    form.submit({
                        url: m_api + '/trader/traderSurvey',
                        method: 'POST',
                        waitMsg: 'Sending data...',
                        success: function(fp, o) {
                            var jsonResp = o.result;

                            Ext.MessageBox.show({
                                title: 'Success',
                                msg: jsonResp.message,
                                buttons: Ext.MessageBox.OK,
                                animateTarget: 'mb9',
                                icon: 'ext-mb-info'
                            });

                            //load store survey environment
                            traderSurListStore.load();
                            traderSurFormWin.close();
                        },
                        failure: function(fp, o) {
                            var jsonResp = o.result;
                            if(jsonResp == undefined){
                                var msgNotif = "Form not complete yet";
                            }else{
                                var msgNotif = jsonResp.message;
                            }

                            Ext.MessageBox.show({
                                title: 'Failed',
                                msg: msgNotif,
                                buttons: Ext.MessageBox.OK,
                                animateTarget: 'mb9',
                                icon: 'ext-mb-error'
                            });
                        }
                    });
                }
            },{
                text: lang('Close'),
                margin: '5px',
                scale: 'large',
                ui: 's-button',
                cls: 's-grey',
                handler: function() {
                    traderSurFormWin.close();
                }
            }]
        });

        Ext.getCmp('tSurTraderID').setValue(Ext.getCmp('TraderID').getValue());
        Ext.getCmp('tSurTraderName').setValue(Ext.getCmp('TraderName').getValue());
        if(showMethod == 'insert'){
            //insert
            console.log('insert');
        }else{
            //update
            console.log('update');

            //cek tombol save
            if(m_act_update == false){
                Ext.getCmp('btnTraderSurveySave').setVisible(false);
            }

            Ext.getCmp('traderSurFormData').getForm().load({
                url: m_api + '/trader/traderSurveyGetForm',
                method: 'GET',
                params: {
                    TraderSurID: TraderSurID
                },
                success: function(form, action) {
                    var r = Ext.decode(action.response.responseText);
                    Ext.getCmp('tSurSurveyYear').setReadOnly(true);
                },
                failure: function(form, action){
                    Ext.MessageBox.show({
                        title: 'Failed',
                        msg: 'Data not found',
                        buttons: Ext.MessageBox.OK,
                        animateTarget: 'mb9',
                        icon: 'ext-mb-error'
                    });
                }
            });
        }

        //show windows
        if (!traderSurFormWin.isVisible()) {
            traderSurFormWin.center();
            traderSurFormWin.show();
        } else {
            traderSurFormWin.close();
        }

    }

}

function checkMax3FarmerProblem(){
    var returnnya = true;
    var totalCeked = 0;

    if(Ext.getCmp('tSurFarmerMainProblemLowProd').checked == true) totalCeked = totalCeked + 1;
    if(Ext.getCmp('tSurFarmerMainProblemOldTree').checked == true) totalCeked = totalCeked + 1;
    if(Ext.getCmp('tSurFarmerMainProblemNoKnowledge').checked == true) totalCeked = totalCeked + 1;
    if(Ext.getCmp('tSurFarmerMainProblemPest').checked == true) totalCeked = totalCeked + 1;
    if(Ext.getCmp('tSurFarmerMainProblemPestSolving').checked == true) totalCeked = totalCeked + 1;
    if(Ext.getCmp('tSurFarmerMainProblemSeasonChanging').checked == true) totalCeked = totalCeked + 1;
    if(Ext.getCmp('tSurFarmerMainProblemDisease').checked == true) totalCeked = totalCeked + 1;
    if(Ext.getCmp('tSurFarmerMainProblemLand').checked == true) totalCeked = totalCeked + 1;
    if(Ext.getCmp('tSurFarmerMainProblemLackSkill').checked == true) totalCeked = totalCeked + 1;
    if(Ext.getCmp('tSurFarmerMainProblemOtherComodity').checked == true) totalCeked = totalCeked + 1;
    if(Ext.getCmp('tSurFarmerMainProblemLowPrice').checked == true) totalCeked = totalCeked + 1;

    if(totalCeked > 3) returnnya = false;
    return returnnya;
}