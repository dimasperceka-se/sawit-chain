/*
* @Author: nikolius
* @Date:   2017-10-11 17:06:54
* @Last Modified by:   nikolius
* @Last Modified time: 2017-10-11 17:19:16
*/
Ext.define('Koltiva.view.DataAdm.AdcMill.WinDataSetControl' ,{
    extend: 'Ext.window.Window',
    id: 'Koltiva.view.DataAdm.AdcMill.WinDataSetControl',
    title: lang('Set Data Control'),
    closable: true,
    modal: true,
    closeAction: 'destroy',
    width: '75%',
    height: '90%',
    overflowY: 'auto',
    viewVar: false,
    setViewVar: function(value){
        this.viewVar = value;
    },
    initComponent: function() {
        var thisObj = this;

        //store yg dipakai (begin)
        var storeGridSetDataControl = Ext.create('Koltiva.store.DataAdm.AdcMill.GridSetDataControl',{
            storeVar: {
                MillIDSelected: thisObj.viewVar.MillIDSelected
            }
        })

        var combo_store_partner_data_control = Ext.create('Koltiva.store.ComboGeneral.ComboPartner');
        //store yg dipakai (end)

        thisObj.items = [{
            layout: 'column',
            border: false,
            items:[{
                columnWidth: 1,
                layout: 'form',
                style:'padding:10px;',
                items:[{
                    xtype: 'grid',
                    id: 'Koltiva.view.DataAdm.AdcMill.WinDataSetControl-gridSetDataControl',
                    style: 'border:1px solid #CCC;margin-top:4px;',
                    loadMask: true,
                    selType: 'rowmodel',
                    store: storeGridSetDataControl,
                    viewConfig: {
                        deferEmptyText: false,
                        emptyText: lang('No data Available')
                    },
                    dockedItems: [{
                        xtype: 'pagingtoolbar',
                        store: storeGridSetDataControl,
                        dock: 'bottom',
                        displayInfo: true
                    }],
                    columns: [{
                        dataIndex: 'MemberIDInc',
                        hidden:true
                    },{
                        text: lang('ID'),
                        dataIndex: 'id',
                        width:'10%'
                    },{
                        text: lang('Name'),
                        dataIndex: 'Name',
                        width:'20%'
                    },{
                        text: lang('Kecamatan'),
                        dataIndex: 'Kecamatan',
                        width:'12%'
                    },{
                        text: lang('Desa'),
                        dataIndex: 'Desa',
                        width:'12%'
                    },{
                        text: lang('Partner Access'),
                        dataIndex: 'PartnerAccess',
                        width:'45%'
                    }]
                },{
                    html: '<div></div>'
                },{
                    layout: 'column',
                    border: false,
                    items:[{
                        columnWidth: 1,
                        margin:'0 5 10 0',
                        padding:2,
                        layout:{
                            type:'vbox',
                            align:'stretch'
                        },
                        items:[{
                            xtype: 'itemselector',
                            id: 'Koltiva.view.DataAdm.AdcMill.WinDataSetControl-PartnerAccess',
                            name: 'Koltiva.view.DataAdm.AdcMill.WinDataSetControl-PartnerAccess',
                            fromTitle: lang('Available Partner'),
                            toTitle: lang('Partner Access'),
                            store: combo_store_partner_data_control,
                            displayField: 'label',
                            valueField: 'id',
                            value: []
                        }]
                    }]
                }]
            }]
        }];

        thisObj.buttons = [{
            text: lang('Save'),
            margin: '5px',
            scale: 'large',
            ui: 's-button',
            cls: 's-blue',
            handler: function() {
                //cek apakah ada partner terselect
                if(Ext.getCmp('Koltiva.view.DataAdm.AdcMill.WinDataSetControl-PartnerAccess').getValue().join().replace(/,/g, '::') == ""){
                    Ext.MessageBox.show({
                        title: 'Notifications',
                        msg: 'No item selected',
                        buttons: Ext.MessageBox.OK,
                        animateTarget: 'mb9',
                        icon: 'ext-mb-info'
                    });
                    return false;
                }

                Ext.MessageBox.confirm('Message', 'Apakah semua item sudah terinput dengan benar ?', function(btn) {
                    if (btn == 'yes') {

                        Ext.Ajax.request({
                            waitMsg: 'Please Wait',
                            url: m_api + '/data_adm/adc_mill/data_control',
                            method: 'POST',
                            params: {
                                MillIDSelected: thisObj.viewVar.MillIDSelected,
                                PartnerAccess: Ext.getCmp('Koltiva.view.DataAdm.AdcMill.WinDataSetControl-PartnerAccess').getValue().join().replace(/,/g, '::')
                            },
                            success: function(response, opts) {
                                Ext.MessageBox.show({
                                    title: 'Information',
                                    msg: lang('Data updated'),
                                    buttons: Ext.MessageBox.OK,
                                    animateTarget: 'mb9',
                                    icon: 'ext-mb-success'
                                });

                                //load store n tutup popup
                                Ext.data.StoreManager.lookup('Koltiva.store.DataAdm.AdcMill.GridSetByMill').load();
                                Ext.getCmp('Koltiva.view.DataAdm.AdcMill.WinDataSetControl').close();
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
        },{
            text: lang('Close'),
            margin: '5px',
            scale: 'large',
            ui: 's-button',
            cls: 's-grey',
            handler: function() {
                Ext.getCmp('Koltiva.view.DataAdm.AdcMill.WinDataSetControl').close();
            }
        }];

        this.callParent(arguments);
    }
});