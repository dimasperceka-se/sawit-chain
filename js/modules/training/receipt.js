/*
* @Author: nikolius
* @Date:   2017-01-12 14:04:54
* @Last Modified by:   nikolius
* @Last Modified time: 2017-01-20 11:02:31
*/
// console.log(m_ProvinceID);
// console.log(m_DistrictID);
// console.log(m_SubDistrictID);
Ext.Loader.setConfig({enabled: true});
Ext.Loader.setPath('js/ext-4.2.0.663', '../ux');

Ext.onReady(function() {
    Ext.tip.QuickTipManager.init();

    Ext.define('mainGridModel.Model', {
        extend: 'Ext.data.Model',
        fields: [`ReceiptID`,`Type`,`Training`,`Label`,`TrainingStart`,`TrainingEnd`,`Province`,`District`]
    });

    var store = Ext.create('Ext.data.Store', {
        model: 'mainGridModel.Model',
        autoLoad: true,
        pageSize: 50,
        remoteSort: true,
        proxy: {
            type: 'ajax',
            url: m_api + '/training_receipt/main_list',
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
                store.proxy.extraParams.prov = m_ProvinceID;
                store.proxy.extraParams.dist = m_DistrictID;
                store.proxy.extraParams.sub_dist = m_SubDistrictID;
                store.proxy.extraParams.sTrainingId = Ext.getCmp('sTrainingId').getValue();
                store.proxy.extraParams.sObjType = Ext.getCmp('sObjType').getValue();
            }
        }
    });

    var contextMenuGrid = Ext.create('Ext.menu.Menu',{
        items:[{
            icon: varjs.config.base_url + 'images/icons/new/update.png',
            text: lang('Update'),
            hidden: m_act_update,
            handler: function(){
                var sm = Ext.getCmp('grid').getSelectionModel().getSelection()[0];
                displayFormReceipt(store,sm.get('ReceiptID'));
            }
        },{
            icon: varjs.config.base_url + 'images/icons/silk/overlays.png',
            text: lang('Activity'),
            hidden: m_act_receipt_activity,
            handler: function(){
                var sm = Ext.getCmp('grid').getSelectionModel().getSelection()[0];
                displayFormActivity(sm.get('ReceiptID'));
            }
        },{
            icon: varjs.config.base_url + 'images/icons/silk/group.png',
            text: lang('Participant'),
            hidden: m_act_receipt_participant,
            handler: function(){
                var sm = Ext.getCmp('grid').getSelectionModel().getSelection()[0];
                displayFormPart(sm.get('ReceiptID'));
            }
        },{
            icon: varjs.config.base_url + 'images/icons/new/delete.png',
            text: lang('Delete'),
            hidden: m_act_delete,
            handler: function() {
                var sm = Ext.getCmp('grid').getSelectionModel().getSelection()[0];

                if(sm.get('ReceiptID') != ""){
                    Ext.MessageBox.confirm('Message', lang('Are you sure want to delete this data ?') , function(btn){
                        if(btn == 'yes'){
                            Ext.Ajax.request({
                                waitMsg: lang('Please Wait'),
                                url: m_api + '/training_receipt/receipt',
                                method : 'DELETE',
                                params: {ReceiptID:  sm.get('ReceiptID')},
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
                            });
                        }
                    });
                }

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

    var cmb_objtype = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'label'],
        data: [{
            "id": "farmergroup",
            "label": lang("CPG Training")
        }, {
            "id": "cadre",
            "label": lang("Cadre Training")
        }, {
            "id": "master",
            "label": lang("Master Training")
        }, {
            "id": "farmer",
            "label": lang("Farmer Training")
        },{
            "id": "business",
            "label": lang("Business Training")
        }]
    });

    var cmb_training = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'label'],
        autoLoad: true,
        proxy: {
            type: 'ajax',
            url: m_api + '/training_receipt_setting/training',
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });

    var grid = Ext.create('Ext.grid.Panel', {
        store: store,
        width: '100%',
        minHeight: 250,
        id: 'grid',
        style: 'border:1px solid #CCC;',
        renderTo: 'ext-content',
        loadMask: true,
        selType: 'rowmodel',
        listeners: {
            itemclick: function(view, record, item, index, e){
               contextMenuGrid.showAt(e.getXY());
            }
        },
        dockedItems: [{
            xtype: 'pagingtoolbar',
            store: store, // same store GridPanel is using
            dock: 'bottom',
            displayInfo: true
        },{
            xtype: 'toolbar',
            minHeight: 38,
            items: [{
                id: 'sObjType',
                name: 'sObjType',
                xtype: 'combo',
                width: 190,
                store: cmb_objtype,
                displayField: 'label',
                valueField: 'id',
                queryMode: 'local',
                selectOnFocus: true,
                emptyText: lang('Training Type'),
                listeners: {
                    specialkey: submitOnEnter
                }
            },{
                id: 'sTrainingId',
                name: 'sTrainingId',
                xtype: 'combo',
                width: 350,
                store: cmb_training,
                displayField: 'label',
                valueField: 'id',
                queryMode: 'local',
                selectOnFocus: true,
                emptyText: lang('Training'),
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
            dataIndex: 'ReceiptID',
            hidden: true
        },{
            text: 'No',
            xtype: 'rownumberer',
            width: '3%'
        },{
            text: lang('Type'),
            width: '10%',
            dataIndex: 'Type'
        },{
            text: lang('Training'),
            width: '25%',
            dataIndex: 'Training'
        },{
            text: lang('Label'),
            width: '22%',
            dataIndex: 'Label'
        },{
            text: lang('Training Start'),
            width: '9%',
            dataIndex: 'TrainingStart'
        },{
            text: lang('Training End'),
            width: '9%',
            dataIndex: 'TrainingEnd'
        },{
            text: lang('Province'),
            width: '11%',
            dataIndex: 'Province'
        },{
            text: lang('District'),
            width: '11%',
            dataIndex: 'District'
        }]
    });

    function displayFormReceipt(store,ReceiptID){

        var winReceipt = Ext.create('widget.window', {
            title: lang('Receipt Data'),
            id: 'winReceipt',
            closable: true,
            modal: true,
            closeAction: 'destroy',
            width: '50%',
            height: '55%',
            overflowY: 'auto',
            bodyStyle:{"background-color":"#F0F0F0"},
            style:'background-color:#F0F0F0;',
            items:[{
                xtype: 'form',
                id: 'formReceipt',
                fileUpload: true,
                padding:'5 25 5 8',
                items:[{
                    layout: 'column',
                    border: false,
                    items: [{
                        columnWidth: 1,
                        layout:'form',
                        items: [{
                            xtype: 'hiddenfield',
                            id: 'ReceiptID',
                            name: 'ReceiptID'
                        },{
                            layout: {
                                type: 'hbox',
                                align: 'stretch'
                            },
                            items:[{
                                xtype: 'textfield',
                                labelWidth:'180px',
                                fieldLabel: lang('Type'),
                                allowBlank: false,
                                readOnly:true,
                                width:'98%',
                                id: 'ObjTypeLabel',
                                name: 'ObjTypeLabel'
                            }]
                        },{
                            layout: {
                                type: 'hbox',
                                align: 'stretch'
                            },
                            items:[{
                                xtype: 'textfield',
                                labelWidth:'180px',
                                fieldLabel: lang('Training Topic'),
                                allowBlank: false,
                                readOnly:true,
                                width:'98%',
                                id: 'TrainingTopic',
                                name: 'TrainingTopic'
                            }]
                        },{
                            layout: {
                                type: 'hbox',
                                align: 'stretch'
                            },
                            items:[{
                                xtype: 'textfield',
                                labelWidth:'180px',
                                fieldLabel: lang('Training'),
                                allowBlank: false,
                                readOnly:true,
                                width:'90%',
                                id: 'ObjIDLabel',
                                name: 'ObjIDLabel'
                            }]
                        },{
                            layout: {
                                type: 'hbox',
                                align: 'stretch'
                            },
                            items:[{
                                xtype: 'datefield',
                                width: '45%',
                                labelWidth:'180px',
                                fieldLabel: lang('Training Start'),
                                id: 'TrainingStart',
                                name: 'TrainingStart',
                                format: 'Y-m-d',
                                readOnly: true
                            }]
                        },{
                            layout: {
                                type: 'hbox',
                                align: 'stretch'
                            },
                            items:[{
                                xtype: 'datefield',
                                width: '45%',
                                labelWidth:'180px',
                                fieldLabel: lang('Training End'),
                                id: 'TrainingEnd',
                                name: 'TrainingEnd',
                                format: 'Y-m-d',
                                readOnly: true
                            }]
                        },{
                            layout: {
                                type: 'hbox',
                                align: 'stretch'
                            },
                            items:[{
                                xtype: 'datefield',
                                width: '45%',
                                labelWidth:'180px',
                                fieldLabel: lang('Training Date'),
                                id: 'TrainingDate',
                                name: 'TrainingDate',
                                format: 'Y-m-d'
                            }]
                        },{
                            layout: {
                                type: 'hbox',
                                align: 'stretch'
                            },
                            items:[{
                                xtype: 'textfield',
                                width: '45%',
                                labelWidth:'180px',
                                fieldLabel: lang('Location'),
                                id: 'Location',
                                name: 'Location'
                            }]
                        },{
                            layout: {
                                type: 'hbox',
                                align: 'stretch'
                            },
                            items:[{
                                xtype: 'datefield',
                                width: '45%',
                                labelWidth:'180px',
                                fieldLabel: lang('Participant Receipt Date'),
                                id: 'PartReceiptDate',
                                name: 'PartReceiptDate',
                                format: 'Y-m-d'
                            }]
                        },{
                            layout: {
                                type: 'hbox',
                                align: 'stretch'
                            },
                            items:[{
                                xtype: 'datefield',
                                width: '45%',
                                labelWidth:'180px',
                                fieldLabel: lang('Activity Receipt Date'),
                                id: 'ActReceiptDate',
                                name: 'ActReceiptDate',
                                format: 'Y-m-d'
                            }]
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
                handler: function () {
                    var form = Ext.getCmp('formReceipt').getForm();

                    if (form.isValid()) {
                        form.submit({
                            url: m_api + '/training_receipt/receipt',
                            method:'POST',
                            waitMsg: 'Saving data...',
                            success: function(fp, data) {
                                var jsonResp = data.result;
                                Ext.MessageBox.alert('Success', 'Data saved.');
                                store.load();
                                winReceipt.close();
                            },
                            failure: function(fp, o) {
                                if(o.response.responseText == undefined){
                                    var errText = "Failed to save data";
                                }else{
                                    var errText = o.response.responseText;
                                    errText = errText.replace(/^"(.*)"$/, '$1');
                                }

                                Ext.MessageBox.show({
                                    title: 'Failed',
                                    msg: errText,
                                    buttons: Ext.MessageBox.OK,
                                    animateTarget: 'mb9',
                                    icon: 'ext-mb-error'
                                });
                            }
                        });
                    }else{
                        Ext.MessageBox.show({
                            title: 'Failed',
                            msg: 'Please fill the required field',
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
                    winReceipt.close();
                }
            }]
        });

        //fill form
        var form = Ext.getCmp('formReceipt').getForm();
        form.load({
            url: m_api + '/training_receipt/fill_form_receipt',
            method: 'GET',
            params: {ReceiptID: ReceiptID},
            success: function(form, action) {
                var r = Ext.decode(action.response.responseText);
                console.log(r);
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

        //show windows
        if (!winReceipt.isVisible()) {
            winReceipt.show();
        } else {
            winReceipt.close();
        }
    }

    function displayFormActivity(ReceiptID){
        Ext.define('actGoods.Model', {
            extend: 'Ext.data.Model',
            fields: ['ReceiptActID','GoodsID','GoodsCode','GoodsName','ActGoodsQty','ActRemarks']
        });

        var store_act_goods = Ext.create('Ext.data.Store', {
            model: 'actGoods.Model',
            pageSize: 10,
            remoteSort: true,
            autoLoad: true,
            proxy: {
                type: 'ajax',
                url: m_api + '/training_receipt/act_goods',
                reader: {
                    type: 'json',
                    root: 'data',
                    totalProperty: 'total'
                }
            },
            listeners: {
                'beforeload': function(store, options) {
                    store.proxy.extraParams.ReceiptID = ReceiptID;
                }
            }
        });

        var activityRowEditing = Ext.create('Ext.grid.plugin.RowEditing', {
            id: 'activityRowEditing',
            clicksToMoveEditor: 0,
            autoCancel: false,
            errorSummary: false,
            clicksToEdit: 2
        });

        var winActivity = Ext.create('widget.window', {
            title: lang('Receipt - Activity Goods'),
            id: 'winActivity',
            closable: true,
            modal: true,
            closeAction: 'destroy',
            width: '78%',
            height: '80%',
            overflowY: 'auto',
            bodyStyle:{"background-color":"#F0F0F0"},
            style:'background-color:#F0F0F0;padding:4px;',
            items:[{
                layout: 'column',
                border: false,
                items: [{
                    columnWidth: 1,
                    layout:'form',
                    items: [{
                        xtype: 'gridpanel',
                        title: lang('Goods'),
                        id: 'gridReceiptActGoods',
                        style: 'border:1px solid #CCC;',
                        store: store_act_goods,
                        width: '100%',
                        loadMask: true,
                        selType: 'rowmodel',
                        minHeight:505,
                        dockedItems: [{
                            xtype: 'pagingtoolbar',
                            store: store_act_goods, // same store GridPanel is using
                            dock: 'bottom',
                            displayInfo: true
                        },{
                            xtype: 'toolbar',
                            items: [{
                                icon: varjs.config.base_url + 'images/icons/new/update.png',
                                hidden: m_act_update,
                                text: lang('Update'),
                                scope: this,
                                handler: function() {
                                    activityRowEditing.cancelEdit();
                                    var sm = Ext.getCmp('gridReceiptActGoods').getSelectionModel().getSelection();
                                    activityRowEditing.startEdit(sm[0].index, 0);
                                }
                            }]
                        }],
                        columns: [{
                            dataIndex: 'ReceiptActID',
                            hidden: true
                        },{
                            text: lang('No'),
                            xtype: 'rownumberer',
                            width: '4%'
                        },{
                            text: lang('Code'),
                            dataIndex: 'GoodsCode',
                            width: '14%',
                            editor: {
                                xtype: 'textfield',
                                id: 'inputGoodsCode',
                                readOnly:true
                            }
                        },{
                            text: lang('Name'),
                            dataIndex: 'GoodsName',
                            width: '25%',
                            editor: {
                                xtype: 'textfield',
                                id: 'inputGoodsName',
                                readOnly:true
                            }
                        },{
                            text: lang('Qty'),
                            dataIndex: 'ActGoodsQty',
                            width: '10%',
                            xtype: 'numbercolumn',
                            format:'0,000',
                            editor: {
                                xtype: 'numericfield',
                                allowBlank: false,
                                id: 'inputActGoodsQty'
                            }
                        },{
                            text: lang('Remark'),
                            dataIndex: 'ActRemarks',
                            width: '46%',
                            editor: {
                                xtype: 'textfield',
                                id: 'inputActRemarks'
                            }
                        }],
                        plugins: [activityRowEditing],
                        listeners: {
                            itemdblclick: function(dv, record, item, index, e) {
                                //buat hak akses saja, tidak ada aksi apa2, updatenya otomatis detek
                                if (m_act_update == true) {
                                    nRowEditing.cancelEdit();
                                }
                            },
                            'canceledit': function(editor, e, eOpts) {
                                store_act_goods.load();
                            },
                            'edit': function(editor, e) {
                                //update
                                Ext.Ajax.request({
                                    waitMsg: lang('Please Wait'),
                                    url: m_api + '/training_receipt/receipt_activity',
                                    method : 'POST',
                                    params: {
                                        ReceiptActID:  e.record.data.ReceiptActID,
                                        ActGoodsQty: e.record.data.ActGoodsQty,
                                        ActRemarks: e.record.data.ActRemarks
                                    },
                                    success: function(response, opts){
                                        var obj = Ext.decode(response.responseText);
                                        switch(obj.success){
                                            case true:
                                                Ext.MessageBox.alert('Success', obj.message);
                                                store_act_goods.load();
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
                                            msg: 'Failed to update data',
                                            buttons: Ext.MessageBox.OK,
                                            animateTarget: 'mb9',
                                            icon: 'ext-mb-error'
                                        });
                                    }
                                });
                            }
                        }
                    }]
                }]
            },{
                html:'<span style="color:red;font:sans-serif 11px;">* Click in the row data to update date</span>'
            }],
            buttons:[{
                text: 'Print Receipt',
                margin: '5px',
                scale: 'large',
                ui: 's-button',
                cls: 's-blue',
                handler: function () {
                    preview_cetak_surat(m_url_cetak+'print_receipt_act/'+ReceiptID);
                }
            },{
                text: lang('Close'),
                margin: '5px',
                scale: 'large',
                ui: 's-button',
                cls: 's-grey',
                disabled: false,
                handler: function() {
                    winActivity.close();
                }
            }]
        });

        //show windows
        if (!winActivity.isVisible()) {
            winActivity.show();
        } else {
            winActivity.close();
        }
    }

    function displayFormPart(ReceiptID){
        var partCellEditing = Ext.create('Ext.grid.plugin.CellEditing', {
            id: 'partCellEditing',
            clicksToEdit: 2
        });

        //get jumlah field yg diperlukan grid
        Ext.Ajax.request({
            url: m_api + '/training_receipt/receipt_field_model',
            method: 'GET',
            params: {
                ReceiptID: ReceiptID
            },
            success: function(response, action) {
                var obj = Ext.decode(response.responseText);
                //console.log(obj);

                Ext.define('dinamisPartGridModel.Model', {
                    extend: 'Ext.data.Model',
                    fields: obj.fieldNya
                });

                var store_part_goods = Ext.create('Ext.data.Store', {
                    model: 'dinamisPartGridModel.Model',
                    autoLoad: true,
                    proxy: {
                        type: 'ajax',
                        url: m_api + '/training_receipt/part_goods_item',
                        reader: {
                            type: 'json',
                            root: 'data'
                        }
                    },
                    listeners: {
                        'beforeload': function(store, options) {
                            store.proxy.extraParams.ReceiptID = ReceiptID;
                        }
                    }
                });

                var winParticipant = Ext.create('widget.window', {
                    title: lang('Receipt - Participant Goods'),
                    id: 'winParticipant',
                    closable: true,
                    modal: true,
                    closeAction: 'destroy',
                    width: '78%',
                    height: '85%',
                    overflowY: 'auto',
                    bodyStyle:{"background-color":"#F0F0F0"},
                    style:'background-color:#F0F0F0;padding:4px;',
                    items:[{
                        layout: 'column',
                        border: false,
                        items: [{
                            columnWidth: 1,
                            layout:'form',
                            items: [{
                                items:[{
                                    layout:'fit',
                                    items:[{
                                        xtype: 'gridpanel',
                                        title: lang('Participant Goods'),
                                        id: 'gridReceiptPartGoods',
                                        style: 'border:1px solid #CCC;',
                                        store: store_part_goods,
                                        width: '100%',
                                        autoScroll: true,
                                        loadMask: true,
                                        selType: 'rowmodel',
                                        height:525,
                                        columns: obj.gridColumnNya,
                                        plugins: [partCellEditing]
                                    }]
                                }]
                            },{
                                html:'<span style="color:red;font:sans-serif 10px;">* Double click in remark cell to update</span>'
                            }]
                        }]
                    }],
                    buttons:[{
                        text: 'Save',
                        margin: '5px',
                        scale: 'large',
                        ui: 's-button',
                        cls: 's-blue',
                        handler: function () {
                            var records = store_part_goods.queryBy(function(record) {
                                return record;
                            });

                            var paramKirim = [];
                            records.each(function(record) {
                                //console.log(record);
                                paramKirim.push(record.data);
                            });

                            //update ke tabel
                            Ext.Ajax.request({
                                url: m_api + '/training_receipt/receipt_participant_goods',
                                method: 'POST',
                                params: {
                                    paramKirim: Ext.encode(paramKirim)
                                },
                                success: function(response, o) {
                                    var obj = Ext.decode(response.responseText);
                                    Ext.MessageBox.alert('Success', obj.message);
                                    winParticipant.close();
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

                        }
                    },{
                        text: 'Print Receipt',
                        margin: '5px',
                        scale: 'large',
                        ui: 's-button',
                        cls: 's-blue',
                        handler: function () {
                            preview_cetak_surat(m_url_cetak+'print_receipt_part/'+ReceiptID);
                        }
                    },{
                        text: lang('Close'),
                        margin: '5px',
                        scale: 'large',
                        ui: 's-button',
                        cls: 's-grey',
                        disabled: false,
                        handler: function() {
                            winParticipant.close();
                        }
                    }]
                });

                //show windows
                if (!winParticipant.isVisible()) {
                    winParticipant.show();
                } else {
                    winParticipant.close();
                }
            },
            failure: function(response, action){
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

});