/*
* @Author: nikolius
* @Date:   2016-12-30 15:00:55
* @Last Modified by:   nikolius
* @Last Modified time: 2017-01-20 14:24:53
*/

// console.log(m_ProvinceID);
// console.log(m_DistrictID);
// console.log(m_SubDistrictID);

Ext.onReady(function() {
    Ext.tip.QuickTipManager.init();

    Ext.define('mainGridModel.Model', {
        extend: 'Ext.data.Model',
        fields: [`ReceiptSetID`,`Type`,`Training`,`Label`,`TrainingStart`,`TrainingEnd`,`Province`,`District`,`LastModifiedDate`, `ReceiptCreated`, `ReceiptCreatedValue`]
    });

    var store = Ext.create('Ext.data.Store', {
        model: 'mainGridModel.Model',
        autoLoad: true,
        pageSize: 50,
        remoteSort: true,
        proxy: {
            type: 'ajax',
            url: m_api + '/training_receipt_setting/main_list',
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
            icon: varjs.config.base_url + 'images/icons/new/view.png',
            text: lang('View'),
            handler: function() {
                var sm = Ext.getCmp('grid').getSelectionModel().getSelection()[0];
                displayFormRSet('update',store,sm.get('ReceiptSetID'),'yes');
            }
        },{
            icon: varjs.config.base_url + 'images/icons/new/update.png',
            text: lang('Update'),
            hidden: m_act_update,
            handler: function(){
                var sm = Ext.getCmp('grid').getSelectionModel().getSelection()[0];

                if(sm.get('ReceiptCreatedValue') == "0"){
                    displayFormRSet('update',store,sm.get('ReceiptSetID'));
                }else{
                    Ext.MessageBox.show({
                        title: 'Failed',
                        msg: lang('Receipt already created'),
                        buttons: Ext.MessageBox.OK,
                        animateTarget: 'mb9',
                        icon: 'ext-mb-error'
                    });
                }
            }
        },{
            icon: varjs.config.base_url + 'images/icons/new/delete.png',
            text: lang('Delete'),
            hidden: m_act_delete,
            handler: function() {
                var sm = Ext.getCmp('grid').getSelectionModel().getSelection()[0];

                if(sm.get('ReceiptCreatedValue') == "0"){
                    if(sm.get('ReceiptSetID') != ""){
                        Ext.MessageBox.confirm('Message', lang('Are you sure want to delete this data ?') , function(btn){
                            if(btn == 'yes'){
                                Ext.Ajax.request({
                                    waitMsg: lang('Please Wait'),
                                    url: m_api + '/training_receipt_setting/setting',
                                    method : 'DELETE',
                                    params: {ReceiptSetID:  sm.get('ReceiptSetID')},
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
                                })
                            }
                        });
                    }
                }else{
                    Ext.MessageBox.show({
                        title: 'Failed',
                        msg: lang('Receipt already created'),
                        buttons: Ext.MessageBox.OK,
                        animateTarget: 'mb9',
                        icon: 'ext-mb-error'
                    });
                }
            }
        },{
            icon: varjs.config.base_url + 'images/icons/silk/notebook--pencil.png',
            text: lang('Create Receipt'),
            hidden: m_act_create_receipt,
            handler: function() {
                var sm = Ext.getCmp('grid').getSelectionModel().getSelection()[0];
                //console.log(sm.get('ReceiptSetID'));

                if(sm.get('ReceiptCreatedValue') == "0"){
                    Ext.MessageBox.confirm('Message', lang('Are you sure want to create receipt?') , function(btn){
                        if(btn == 'yes'){
                            Ext.Ajax.request({
                                waitMsg: lang('Please Wait'),
                                url: m_api + '/training_receipt_setting/create_receipt',
                                method : 'POST',
                                params: {ReceiptSetID:  sm.get('ReceiptSetID')},
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
                                        msg: 'Failed to create data',
                                        buttons: Ext.MessageBox.OK,
                                        animateTarget: 'mb9',
                                        icon: 'ext-mb-error'
                                    });
                                }
                            });
                        }
                    });
                }else{
                    Ext.MessageBox.show({
                        title: 'Failed',
                        msg: lang('Receipt already created'),
                        buttons: Ext.MessageBox.OK,
                        animateTarget: 'mb9',
                        icon: 'ext-mb-error'
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
                icon: varjs.config.base_url + 'images/icons/new/add.png', cls:'Sfr_BtnGridGreen', overCls:'Sfr_BtnGridGreen-Hover',
                text: lang('Add'),
                scope: this,
                hidden: m_act_add,
                handler: function() {
                    displayFormRSet('add',store,null);
                }
            },{
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
            dataIndex: 'ReceiptSetID',
            hidden: true
        },{
            dataIndex: 'ReceiptCreatedValue',
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
            text: lang('Receipt Created'),
            width: '10%',
            dataIndex: 'ReceiptCreated'
        },{
            text: lang('Province'),
            width: '6%',
            dataIndex: 'Province'
        },{
            text: lang('District'),
            width: '6%',
            dataIndex: 'District'
        }]
    });

    function displayFormRSet(displayMethod,store,ReceiptSetID,noSave='no'){
        if(noSave == 'yes'){
            var btnSaveHidden = true;
        }else{
            var btnSaveHidden = false;
        }

        //================================================ Store Auto Complete (begin) ========================================//
        Ext.define('autocomModel.Model', {
            extend: 'Ext.data.Model',
            fields: [{
                name: 'id',
                mapping: 'id'
            }, {
                name: 'label',
                mapping: 'label'
            }]
        });

        var store_staff_farmer_autocom_part1 = Ext.create('Ext.data.Store', {
            model: 'autocomModel.Model',
            pageSize: 10,
            proxy: {
                type: 'ajax',
                url: m_api + '/training_receipt_setting/staff_farmer_autocom',
                reader: {
                    type: 'json',
                    root: 'data',
                    totalProperty: 'total'
                }
            },
            listeners: {
                'beforeload': function(store, options) {
                    store.proxy.extraParams.isStaff = Ext.getCmp('PartGiverTypeStaff').getValue();
                    store.proxy.extraParams.isFarmer = Ext.getCmp('PartGiverTypeFarmer').getValue();
                    store.proxy.extraParams.prov = m_ProvinceID;
                    store.proxy.extraParams.dist = m_DistrictID;
                    store.proxy.extraParams.sub_dist = m_SubDistrictID;
                }
            }
        });

        var store_staff_farmer_autocom_part2 = Ext.create('Ext.data.Store', {
            model: 'autocomModel.Model',
            pageSize: 10,
            proxy: {
                type: 'ajax',
                url: m_api + '/training_receipt_setting/staff_farmer_autocom',
                reader: {
                    type: 'json',
                    root: 'data',
                    totalProperty: 'total'
                }
            },
            listeners: {
                'beforeload': function(store, options) {
                    store.proxy.extraParams.isStaff = Ext.getCmp('PartReceiverTypeStaff').getValue();
                    store.proxy.extraParams.isFarmer = Ext.getCmp('PartReceiverTypeFarmer').getValue();
                    store.proxy.extraParams.prov = m_ProvinceID;
                    store.proxy.extraParams.dist = m_DistrictID;
                    store.proxy.extraParams.sub_dist = m_SubDistrictID;
                }
            }
        });

        var store_staff_farmer_autocom_part3 = Ext.create('Ext.data.Store', {
            model: 'autocomModel.Model',
            pageSize: 10,
            proxy: {
                type: 'ajax',
                url: m_api + '/training_receipt_setting/staff_farmer_autocom',
                reader: {
                    type: 'json',
                    root: 'data',
                    totalProperty: 'total'
                }
            },
            listeners: {
                'beforeload': function(store, options) {
                    store.proxy.extraParams.isStaff = Ext.getCmp('PartKnownByTypeStaff').getValue();
                    store.proxy.extraParams.isFarmer = Ext.getCmp('PartKnownByTypeFarmer').getValue();
                    store.proxy.extraParams.prov = m_ProvinceID;
                    store.proxy.extraParams.dist = m_DistrictID;
                    store.proxy.extraParams.sub_dist = m_SubDistrictID;
                }
            }
        });

        var store_staff_farmer_autocom_part4 = Ext.create('Ext.data.Store', {
            model: 'autocomModel.Model',
            pageSize: 10,
            proxy: {
                type: 'ajax',
                url: m_api + '/training_receipt_setting/staff_farmer_autocom',
                reader: {
                    type: 'json',
                    root: 'data',
                    totalProperty: 'total'
                }
            },
            listeners: {
                'beforeload': function(store, options) {
                    store.proxy.extraParams.isStaff = Ext.getCmp('PartKnownByType2Staff').getValue();
                    store.proxy.extraParams.isFarmer = Ext.getCmp('PartKnownByType2Farmer').getValue();
                    store.proxy.extraParams.prov = m_ProvinceID;
                    store.proxy.extraParams.dist = m_DistrictID;
                    store.proxy.extraParams.sub_dist = m_SubDistrictID;
                }
            }
        });

        var store_staff_farmer_autocom_act1 = Ext.create('Ext.data.Store', {
            model: 'autocomModel.Model',
            pageSize: 10,
            proxy: {
                type: 'ajax',
                url: m_api + '/training_receipt_setting/staff_farmer_autocom',
                reader: {
                    type: 'json',
                    root: 'data',
                    totalProperty: 'total'
                }
            },
            listeners: {
                'beforeload': function(store, options) {
                    store.proxy.extraParams.isStaff = Ext.getCmp('ActGiverTypeStaff').getValue();
                    store.proxy.extraParams.isFarmer = Ext.getCmp('ActGiverTypeFarmer').getValue();
                    store.proxy.extraParams.prov = m_ProvinceID;
                    store.proxy.extraParams.dist = m_DistrictID;
                    store.proxy.extraParams.sub_dist = m_SubDistrictID;
                }
            }
        });

        var store_staff_farmer_autocom_act2 = Ext.create('Ext.data.Store', {
            model: 'autocomModel.Model',
            pageSize: 10,
            proxy: {
                type: 'ajax',
                url: m_api + '/training_receipt_setting/staff_farmer_autocom',
                reader: {
                    type: 'json',
                    root: 'data',
                    totalProperty: 'total'
                }
            },
            listeners: {
                'beforeload': function(store, options) {
                    store.proxy.extraParams.isStaff = Ext.getCmp('ActReceiverTypeStaff').getValue();
                    store.proxy.extraParams.isFarmer = Ext.getCmp('ActReceiverTypeFarmer').getValue();
                    store.proxy.extraParams.prov = m_ProvinceID;
                    store.proxy.extraParams.dist = m_DistrictID;
                    store.proxy.extraParams.sub_dist = m_SubDistrictID;
                }
            }
        });

        var store_staff_farmer_autocom_act3 = Ext.create('Ext.data.Store', {
            model: 'autocomModel.Model',
            pageSize: 10,
            proxy: {
                type: 'ajax',
                url: m_api + '/training_receipt_setting/staff_farmer_autocom',
                reader: {
                    type: 'json',
                    root: 'data',
                    totalProperty: 'total'
                }
            },
            listeners: {
                'beforeload': function(store, options) {
                    store.proxy.extraParams.isStaff = Ext.getCmp('ActKnownByTypeStaff').getValue();
                    store.proxy.extraParams.isFarmer = Ext.getCmp('ActKnownByTypeFarmer').getValue();
                    store.proxy.extraParams.prov = m_ProvinceID;
                    store.proxy.extraParams.dist = m_DistrictID;
                    store.proxy.extraParams.sub_dist = m_SubDistrictID;
                }
            }
        });

        var store_staff_farmer_autocom_act4 = Ext.create('Ext.data.Store', {
            model: 'autocomModel.Model',
            pageSize: 10,
            proxy: {
                type: 'ajax',
                url: m_api + '/training_receipt_setting/staff_farmer_autocom',
                reader: {
                    type: 'json',
                    root: 'data',
                    totalProperty: 'total'
                }
            },
            listeners: {
                'beforeload': function(store, options) {
                    store.proxy.extraParams.isStaff = Ext.getCmp('ActKnownByType2Staff').getValue();
                    store.proxy.extraParams.isFarmer = Ext.getCmp('ActKnownByType2Farmer').getValue();
                    store.proxy.extraParams.prov = m_ProvinceID;
                    store.proxy.extraParams.dist = m_DistrictID;
                    store.proxy.extraParams.sub_dist = m_SubDistrictID;
                }
            }
        });
        //================================================ Store Auto Complete (end) ========================================//

        var cmb_obj_type = Ext.create('Ext.data.Store', {
            fields: ['id', 'label'],
            data: [{
                "id": "farmergroup",
                "label": "CPG Training"
            }, {
                "id": "cadre",
                "label": "Cadre Training"
            },{
                "id": "master",
                "label": "Master Training"
            }]
        });

        Ext.define('goodsList.Model', {
            extend: 'Ext.data.Model',
            fields: ['id','code','name','unit']
        });
        var store_participant_goods = Ext.create('Ext.data.Store', {
            model: 'goodsList.Model',
            autoLoad: false,
            proxy: {
                type: 'ajax',
                url: m_api + '/training_receipt_setting/goods_list_rset',
                reader: {
                    type: 'json',
                    root: 'data'
                }
            },
            listeners: {
                'beforeload': function(store, options) {
                    store.proxy.extraParams.goods_tipe = 'participant';
                    store.proxy.extraParams.ReceiptSetID = Ext.getCmp('ReceiptSetID').getValue();
                }
            }
        });
        var store_activity_goods = Ext.create('Ext.data.Store', {
            model: 'goodsList.Model',
            autoLoad: false,
            proxy: {
                type: 'ajax',
                url: m_api + '/training_receipt_setting/goods_list_rset',
                reader: {
                    type: 'json',
                    root: 'data'
                }
            },
            listeners: {
                'beforeload': function(store, options) {
                    store.proxy.extraParams.goods_tipe = 'activity';
                    store.proxy.extraParams.ReceiptSetID = Ext.getCmp('ReceiptSetID').getValue();
                }
            }
        });

        var winRSet = Ext.create('widget.window', {
            title: lang('Receipt Setting'),
            id: 'winRSet',
            closable: true,
            modal: true,
            closeAction: 'destroy',
            width: '70%',
            height: '90%',
            overflowY: 'auto',
            bodyStyle:{"background-color":"#F0F0F0"},
            style:'background-color:#F0F0F0;',
            items:[{
                xtype: 'form',
                id: 'winFormRSet',
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
                            id: 'ObjID',
                            name: 'ObjID'
                        },{
                            xtype: 'hiddenfield',
                            id: 'ReceiptSetID',
                            name: 'ReceiptSetID'
                        },{
                            layout: {
                                type: 'hbox',
                                align: 'stretch'
                            },
                            items:[{
                                xtype: 'combobox',
                                fieldLabel: lang('Type'),
                                allowBlank: false,
                                width:'98%',
                                store: cmb_obj_type,
                                queryMode: 'local',
                                displayField: 'label',
                                valueField: 'id',
                                id: 'ObjType',
                                name: 'ObjType'
                            }]
                        },{
                            layout: {
                                type: 'hbox',
                                align: 'stretch'
                            },
                            items:[{
                                xtype: 'textfield',
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
                                fieldLabel: lang('Training'),
                                allowBlank: false,
                                readOnly:true,
                                width:'90%',
                                id: 'ObjIDLabel',
                                name: 'ObjIDLabel'
                            },{
                                xtype: 'button',
                                width: 65,
                                id: 'btnSearchObjID',
                                scale: 'small',
                                margin: '0px 0px 0px 10px',
                                text: lang('Search'),
                                style: 'text-align:center;',
                                handler: function () {
                                    //cek apakah tipe training terlebih
                                    if(Ext.getCmp('ObjType').getValue() == null){
                                        Ext.MessageBox.show({
                                            title: 'Notifications',
                                            msg: lang('Training')+' is required',
                                            buttons: Ext.MessageBox.OK,
                                            animateTarget: 'mb9',
                                            icon: 'ext-mb-info'
                                        });
                                    }else{
                                        displayPopupSelTraining(Ext.getCmp('ObjType').getValue());
                                    }
                                }
                            }]
                        },{
                            layout: 'fit',
                            items:[{
                                xtype:'grid',
                                store: store_participant_goods,
                                width: '98%',
                                height: 275,
                                id: 'grid_participant_goods',
                                style: 'border:1px solid #CCC;margin-top:5px;',
                                loadMask: true,
                                title:lang('Participant Goods'),
                                selType: 'rowmodel',
                                dockedItems: [{
                                    xtype: 'toolbar',
                                    items: [{
                                        icon: varjs.config.base_url + 'images/icons/new/add.png', cls:'Sfr_BtnGridGreen', overCls:'Sfr_BtnGridGreen-Hover',
                                        hidden: m_act_add,
                                        text: lang('Add'),
                                        scope: this,
                                        handler: function() {
                                            if(cekUpdateState()){
                                                displayPopupSelGoods('participant',store_participant_goods,null);
                                            }
                                        }
                                    },{
                                        itemId: 'remove',
                                        icon: varjs.config.base_url + 'images/icons/new/delete.png',
                                        hidden: m_act_delete,
                                        text: lang('Delete'),
                                        scope: this,
                                        handler: function() {
                                            var sm = Ext.getCmp('grid_participant_goods').getSelectionModel().getSelection()[0];
                                            if(sm != undefined){
                                                Ext.MessageBox.confirm('Message', lang('Are you sure want to delete this data ?') , function(btn){
                                                    if(btn == 'yes'){
                                                        Ext.Ajax.request({
                                                            waitMsg: lang('Please Wait'),
                                                            url: m_api + '/training_receipt_setting/setting_goods',
                                                            method : 'DELETE',
                                                            params: {id:  sm.get('id'), callFrom: 'participant'},
                                                            success: function(response, opts){
                                                                var obj = Ext.decode(response.responseText);
                                                                switch(obj.success){
                                                                    case true:
                                                                        Ext.MessageBox.alert('Success', obj.message);
                                                                        store_participant_goods.load();
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
                                                        })
                                                    }
                                                });
                                            }else{
                                                Ext.MessageBox.show({
                                                    title: 'Notifications',
                                                    msg: 'No data selected',
                                                    buttons: Ext.MessageBox.OK,
                                                    animateTarget: 'mb9',
                                                    icon: 'ext-mb-info'
                                                });
                                            }
                                        }
                                    }]
                                }],
                                columns: [{
                                    dataIndex: 'id',
                                    hidden: true
                                },{
                                    text: lang('No'),
                                    xtype: 'rownumberer',
                                    width: '5%'
                                },{
                                    text: lang('Code'),
                                    width: '30%',
                                    dataIndex: 'code'
                                },{
                                    text: lang('Name'),
                                    width: '50%',
                                    dataIndex: 'name'
                                },{
                                    text: lang('Unit'),
                                    width: '15%',
                                    dataIndex: 'unit'
                                }]
                            }]
                        },{
                            html:'<div style="height:3px;"></div>'
                        },{
                            layout:'fit',
                            items:[{
                                xtype:'grid',
                                store: store_activity_goods,
                                width: '98%',
                                height: 375,
                                id: 'grid_activity_goods',
                                style: 'border:1px solid #CCC;margin-top:2px;',
                                loadMask: true,
                                title:lang('Activity Goods'),
                                selType: 'rowmodel',
                                dockedItems: [{
                                    xtype: 'toolbar',
                                    items: [{
                                        icon: varjs.config.base_url + 'images/icons/new/add.png', cls:'Sfr_BtnGridGreen', overCls:'Sfr_BtnGridGreen-Hover',
                                        hidden: m_act_add,
                                        text: lang('Add'),
                                        scope: this,
                                        handler: function() {
                                            if(cekUpdateState()){
                                                displayPopupSelGoods('activity',null,store_activity_goods);
                                            }
                                        }
                                    },{
                                        itemId: 'remove',
                                        icon: varjs.config.base_url + 'images/icons/new/delete.png',
                                        hidden: m_act_delete,
                                        text: lang('Delete'),
                                        scope: this,
                                        handler: function() {
                                            var sm = Ext.getCmp('grid_activity_goods').getSelectionModel().getSelection()[0];
                                            if(sm != undefined){
                                                Ext.MessageBox.confirm('Message', lang('Are you sure want to delete this data ?') , function(btn){
                                                    if(btn == 'yes'){
                                                        Ext.Ajax.request({
                                                            waitMsg: lang('Please Wait'),
                                                            url: m_api + '/training_receipt_setting/setting_goods',
                                                            method : 'DELETE',
                                                            params: {id:  sm.get('id'), callFrom: 'activity'},
                                                            success: function(response, opts){
                                                                var obj = Ext.decode(response.responseText);
                                                                switch(obj.success){
                                                                    case true:
                                                                        Ext.MessageBox.alert('Success', obj.message);
                                                                        store_activity_goods.load();
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
                                                        })
                                                    }
                                                });
                                            }else{
                                                Ext.MessageBox.show({
                                                    title: 'Notifications',
                                                    msg: 'No data selected',
                                                    buttons: Ext.MessageBox.OK,
                                                    animateTarget: 'mb9',
                                                    icon: 'ext-mb-info'
                                                });
                                            }
                                        }
                                    }]
                                }],
                                columns: [{
                                    dataIndex: 'id',
                                    hidden: true
                                },{
                                    text: lang('No'),
                                    xtype: 'rownumberer',
                                    width: '5%'
                                },{
                                    text: lang('Code'),
                                    width: '30%',
                                    dataIndex: 'code'
                                },{
                                    text: lang('Name'),
                                    width: '50%',
                                    dataIndex: 'name'
                                },{
                                    text: lang('Unit'),
                                    width: '15%',
                                    dataIndex: 'unit'
                                }]
                            }]
                        },{
                            html:'<div style="height:10px;"></div>'
                        },{
                            xtype:'panel',
                            title: lang('Signature'),
                            frame:true,
                            bodyStyle:{"background-color":"#F0F0F0"},
                            style:'background-color:#F0F0F0;margin-bottom:3px;',
                            padding:'0 7px 7px 7px',
                            //height:570,
                            items:[{
                                layout: 'column',
                                border: false,
                                items: [{
                                    columnWidth: 1,
                                    layout:'form',
                                    items: [{
                                        html:'<h3 style="margin:0px;font-size:15px;font-weight:bold;">'+lang('Participant')+'</h3>'
                                    },{
                                        layout: 'column',
                                        border: false,
                                        items: [{
                                            columnWidth: 0.2,
                                            xtype: 'label',
                                            cls: 'x-form-item-label',
                                            text: lang('Giver')+' 1 :'
                                        },{
                                            columnWidth: 0.4,
                                            border: false,
                                            layout:{
                                                type:'hbox',
                                                align:'stretch'
                                            },
                                            xtype: 'radiogroup',
                                            defaults: {
                                                margin: '0 20 0 0',
                                                flex:true
                                            },
                                            items: [{
                                                name: 'PartGiverType',
                                                id: 'PartGiverTypeStaff',
                                                boxLabel: lang('Staff'),
                                                inputValue: 'staff',
                                                listeners:{
                                                    change: function() {
                                                        Ext.getCmp('PartGiverID').setValue('');
                                                        Ext.getCmp('PartGiverIDLabel').setValue('');
                                                        return false;
                                                    }
                                                }
                                            },{
                                                name: 'PartGiverType',
                                                id: 'PartGiverTypeFarmer',
                                                boxLabel: lang('Farmer'),
                                                inputValue: 'farmer',
                                                listeners:{
                                                    change: function() {
                                                        Ext.getCmp('PartGiverID').setValue('');
                                                        Ext.getCmp('PartGiverIDLabel').setValue('');
                                                        return false;
                                                    }
                                                }
                                            }]
                                        }]
                                    },{
                                        layout: 'column',
                                        border: false,
                                        margin:'-15px 0 0 0',
                                        items: [{
                                            columnWidth: 0.2,
                                            xtype: 'label',
                                            cls: 'x-form-item-label',
                                            text: ''
                                        },{
                                            columnWidth: 0.8,
                                            border: false,
                                            layout:'form',
                                            items:[{
                                                xtype: 'hiddenfield',
                                                id: 'PartGiverID',
                                                name: 'PartGiverID'
                                            },{
                                                xtype: 'combo',
                                                width:'100%',
                                                store: store_staff_farmer_autocom_part1,
                                                id: 'PartGiverIDLabel',
                                                name: 'PartGiverIDLabel',
                                                displayField: 'label',
                                                typeAhead: false,
                                                hideTrigger: true,
                                                anchor: '100%',
                                                listConfig: {
                                                    loadingText: 'Searching...',
                                                    emptyText: 'No matching data found.',
                                                    getInnerTpl: function() {
                                                        return '<div class="search-item">' + '{label}' + '{excerpt}' + '</div>';
                                                    }
                                                },
                                                pageSize: 10,
                                                listeners: {
                                                    select: function(combo, selection) {
                                                        var post = selection[0];
                                                        if (post) {
                                                            Ext.getCmp('PartGiverID').setValue(post.data.id);
                                                            Ext.getCmp('PartGiverIDLabel').setValue(post.data.label);
                                                        }
                                                    }
                                                }
                                            }]
                                        }]
                                    },{
                                        layout: 'column',
                                        border: false,
                                        items: [{
                                            columnWidth: 0.2,
                                            xtype: 'label',
                                            cls: 'x-form-item-label',
                                            text: lang('Receiver')+' 1 :'
                                        },{
                                            columnWidth: 0.4,
                                            border: false,
                                            layout:{
                                                type:'hbox',
                                                align:'stretch'
                                            },
                                            xtype: 'radiogroup',
                                            defaults: {
                                                margin: '0 20 0 0',
                                                flex:true
                                            },
                                            items: [{
                                                name: 'PartReceiverType',
                                                id: 'PartReceiverTypeStaff',
                                                boxLabel: lang('Staff'),
                                                inputValue: 'staff',
                                                listeners:{
                                                    change: function() {
                                                        Ext.getCmp('PartReceiverID').setValue('');
                                                        Ext.getCmp('PartReceiverIDLabel').setValue('');
                                                        return false;
                                                    }
                                                }
                                            },{
                                                name: 'PartReceiverType',
                                                id: 'PartReceiverTypeFarmer',
                                                boxLabel: lang('Farmer'),
                                                inputValue: 'farmer',
                                                listeners:{
                                                    change: function() {
                                                        Ext.getCmp('PartReceiverID').setValue('');
                                                        Ext.getCmp('PartReceiverIDLabel').setValue('');
                                                        return false;
                                                    }
                                                }
                                            }]
                                        }]
                                    },{
                                        layout: 'column',
                                        border: false,
                                        margin:'-15px 0 0 0',
                                        items: [{
                                            columnWidth: 0.2,
                                            xtype: 'label',
                                            cls: 'x-form-item-label',
                                            text: ''
                                        },{
                                            columnWidth: 0.8,
                                            border: false,
                                            layout:'form',
                                            items:[{
                                                xtype: 'hiddenfield',
                                                id: 'PartReceiverID',
                                                name: 'PartReceiverID'
                                            },{
                                                xtype: 'combo',
                                                width:'100%',
                                                store: store_staff_farmer_autocom_part2,
                                                id: 'PartReceiverIDLabel',
                                                name: 'PartReceiverIDLabel',
                                                displayField: 'label',
                                                typeAhead: false,
                                                hideTrigger: true,
                                                anchor: '100%',
                                                listConfig: {
                                                    loadingText: 'Searching...',
                                                    emptyText: 'No matching data found.',
                                                    getInnerTpl: function() {
                                                        return '<div class="search-item">' + '{label}' + '{excerpt}' + '</div>';
                                                    }
                                                },
                                                pageSize: 10,
                                                listeners: {
                                                    select: function(combo, selection) {
                                                        var post = selection[0];
                                                        if (post) {
                                                            Ext.getCmp('PartReceiverID').setValue(post.data.id);
                                                            Ext.getCmp('PartReceiverIDLabel').setValue(post.data.label);
                                                        }
                                                    }
                                                }
                                            }]
                                        }]
                                    },{
                                        layout: 'column',
                                        border: false,
                                        items: [{
                                            columnWidth: 0.2,
                                            xtype: 'label',
                                            cls: 'x-form-item-label',
                                            text: lang('Known By')+' 1 :'
                                        },{
                                            columnWidth: 0.4,
                                            border: false,
                                            layout:{
                                                type:'hbox',
                                                align:'stretch'
                                            },
                                            xtype: 'radiogroup',
                                            defaults: {
                                                margin: '0 20 0 0',
                                                flex:true
                                            },
                                            items: [{
                                                name: 'PartKnownByType',
                                                id: 'PartKnownByTypeStaff',
                                                boxLabel: lang('Staff'),
                                                inputValue: 'staff',
                                                listeners:{
                                                    change: function() {
                                                        Ext.getCmp('PartKnownByID').setValue('');
                                                        Ext.getCmp('PartKnownByIDLabel').setValue('');
                                                        return false;
                                                    }
                                                }
                                            },{
                                                name: 'PartKnownByType',
                                                id: 'PartKnownByTypeFarmer',
                                                boxLabel: lang('Farmer'),
                                                inputValue: 'farmer',
                                                listeners:{
                                                    change: function() {
                                                        Ext.getCmp('PartKnownByID').setValue('');
                                                        Ext.getCmp('PartKnownByIDLabel').setValue('');
                                                        return false;
                                                    }
                                                }
                                            }]
                                        }]
                                    },{
                                        layout: 'column',
                                        border: false,
                                        margin:'-15px 0 0 0',
                                        items: [{
                                            columnWidth: 0.2,
                                            xtype: 'label',
                                            cls: 'x-form-item-label',
                                            text: ''
                                        },{
                                            columnWidth: 0.8,
                                            border: false,
                                            layout:'form',
                                            items:[{
                                                xtype: 'hiddenfield',
                                                id: 'PartKnownByID',
                                                name: 'PartKnownByID'
                                            },{
                                                xtype: 'combo',
                                                width:'100%',
                                                store: store_staff_farmer_autocom_part3,
                                                id: 'PartKnownByIDLabel',
                                                name: 'PartKnownByIDLabel',
                                                displayField: 'label',
                                                typeAhead: false,
                                                hideTrigger: true,
                                                anchor: '100%',
                                                listConfig: {
                                                    loadingText: 'Searching...',
                                                    emptyText: 'No matching data found.',
                                                    getInnerTpl: function() {
                                                        return '<div class="search-item">' + '{label}' + '{excerpt}' + '</div>';
                                                    }
                                                },
                                                pageSize: 10,
                                                listeners: {
                                                    select: function(combo, selection) {
                                                        var post = selection[0];
                                                        if (post) {
                                                            Ext.getCmp('PartKnownByID').setValue(post.data.id);
                                                            Ext.getCmp('PartKnownByIDLabel').setValue(post.data.label);
                                                        }
                                                    }
                                                }
                                            }]
                                        }]
                                    },{
                                        layout: 'column',
                                        border: false,
                                        items: [{
                                            columnWidth: 0.2,
                                            xtype: 'label',
                                            cls: 'x-form-item-label',
                                            text: lang('Known By')+' 2 :'
                                        },{
                                            columnWidth: 0.4,
                                            border: false,
                                            layout:{
                                                type:'hbox',
                                                align:'stretch'
                                            },
                                            xtype: 'radiogroup',
                                            defaults: {
                                                margin: '0 20 0 0',
                                                flex:true
                                            },
                                            items: [{
                                                name: 'PartKnownByType2',
                                                id: 'PartKnownByType2Staff',
                                                boxLabel: lang('Staff'),
                                                inputValue: 'staff',
                                                listeners:{
                                                    change: function() {
                                                        Ext.getCmp('PartKnownByID2').setValue('');
                                                        Ext.getCmp('PartKnownByID2Label').setValue('');
                                                        return false;
                                                    }
                                                }
                                            },{
                                                name: 'PartKnownByType2',
                                                id: 'PartKnownByType2Farmer',
                                                boxLabel: lang('Farmer'),
                                                inputValue: 'farmer',
                                                listeners:{
                                                    change: function() {
                                                        Ext.getCmp('PartKnownByID2').setValue('');
                                                        Ext.getCmp('PartKnownByID2Label').setValue('');
                                                        return false;
                                                    }
                                                }
                                            }]
                                        }]
                                    },{
                                        layout: 'column',
                                        border: false,
                                        margin:'-15px 0 0 0',
                                        items: [{
                                            columnWidth: 0.2,
                                            xtype: 'label',
                                            cls: 'x-form-item-label',
                                            text: ''
                                        },{
                                            columnWidth: 0.8,
                                            border: false,
                                            layout:'form',
                                            items:[{
                                                xtype: 'hiddenfield',
                                                id: 'PartKnownByID2',
                                                name: 'PartKnownByID2'
                                            },{
                                                xtype: 'combo',
                                                width:'100%',
                                                store: store_staff_farmer_autocom_part4,
                                                id: 'PartKnownByID2Label',
                                                name: 'PartKnownByID2Label',
                                                displayField: 'label',
                                                typeAhead: false,
                                                hideTrigger: true,
                                                anchor: '100%',
                                                listConfig: {
                                                    loadingText: 'Searching...',
                                                    emptyText: 'No matching data found.',
                                                    getInnerTpl: function() {
                                                        return '<div class="search-item">' + '{label}' + '{excerpt}' + '</div>';
                                                    }
                                                },
                                                pageSize: 10,
                                                listeners: {
                                                    select: function(combo, selection) {
                                                        var post = selection[0];
                                                        if (post) {
                                                            Ext.getCmp('PartKnownByID2').setValue(post.data.id);
                                                            Ext.getCmp('PartKnownByID2Label').setValue(post.data.label);
                                                        }
                                                    }
                                                }
                                            }]
                                        }]
                                    },{
                                        html:'<h3 style="margin:18px 0 0 0;font-size:15px;font-weight:bold;">'+lang('Activity')+'</h3>'
                                    },{
                                        layout: 'column',
                                        border: false,
                                        items: [{
                                            columnWidth: 0.2,
                                            xtype: 'label',
                                            cls: 'x-form-item-label',
                                            text: lang('Giver')+' 1 :'
                                        },{
                                            columnWidth: 0.4,
                                            border: false,
                                            layout:{
                                                type:'hbox',
                                                align:'stretch'
                                            },
                                            xtype: 'radiogroup',
                                            defaults: {
                                                margin: '0 20 0 0',
                                                flex:true
                                            },
                                            items: [{
                                                name: 'ActGiverType',
                                                id: 'ActGiverTypeStaff',
                                                boxLabel: lang('Staff'),
                                                inputValue: 'staff',
                                                listeners:{
                                                    change: function() {
                                                        Ext.getCmp('ActGiverID').setValue('');
                                                        Ext.getCmp('ActGiverIDLabel').setValue('');
                                                        return false;
                                                    }
                                                }
                                            },{
                                                name: 'ActGiverType',
                                                id: 'ActGiverTypeFarmer',
                                                boxLabel: lang('Farmer'),
                                                inputValue: 'farmer',
                                                listeners:{
                                                    change: function() {
                                                        Ext.getCmp('ActGiverID').setValue('');
                                                        Ext.getCmp('ActGiverIDLabel').setValue('');
                                                        return false;
                                                    }
                                                }
                                            }]
                                        }]
                                    },{
                                        layout: 'column',
                                        border: false,
                                        margin:'-15px 0 0 0',
                                        items: [{
                                            columnWidth: 0.2,
                                            xtype: 'label',
                                            cls: 'x-form-item-label',
                                            text: ''
                                        },{
                                            columnWidth: 0.8,
                                            border: false,
                                            layout:'form',
                                            items:[{
                                                xtype: 'hiddenfield',
                                                id: 'ActGiverID',
                                                name: 'ActGiverID'
                                            },{
                                                xtype: 'combo',
                                                width:'100%',
                                                store: store_staff_farmer_autocom_act1,
                                                id: 'ActGiverIDLabel',
                                                name: 'ActGiverIDLabel',
                                                displayField: 'label',
                                                typeAhead: false,
                                                hideTrigger: true,
                                                anchor: '100%',
                                                listConfig: {
                                                    loadingText: 'Searching...',
                                                    emptyText: 'No matching data found.',
                                                    getInnerTpl: function() {
                                                        return '<div class="search-item">' + '{label}' + '{excerpt}' + '</div>';
                                                    }
                                                },
                                                pageSize: 10,
                                                listeners: {
                                                    select: function(combo, selection) {
                                                        var post = selection[0];
                                                        if (post) {
                                                            Ext.getCmp('ActGiverID').setValue(post.data.id);
                                                            Ext.getCmp('ActGiverIDLabel').setValue(post.data.label);
                                                        }
                                                    }
                                                }
                                            }]
                                        }]
                                    },{
                                        layout: 'column',
                                        border: false,
                                        items: [{
                                            columnWidth: 0.2,
                                            xtype: 'label',
                                            cls: 'x-form-item-label',
                                            text: lang('Receiver')+' 1 :'
                                        },{
                                            columnWidth: 0.4,
                                            border: false,
                                            layout:{
                                                type:'hbox',
                                                align:'stretch'
                                            },
                                            xtype: 'radiogroup',
                                            defaults: {
                                                margin: '0 20 0 0',
                                                flex:true
                                            },
                                            items: [{
                                                name: 'ActReceiverType',
                                                id: 'ActReceiverTypeStaff',
                                                boxLabel: lang('Staff'),
                                                inputValue: 'staff',
                                                listeners:{
                                                    change: function() {
                                                        Ext.getCmp('ActReceiverID').setValue('');
                                                        Ext.getCmp('ActReceiverIDLabel').setValue('');
                                                        return false;
                                                    }
                                                }
                                            },{
                                                name: 'ActReceiverType',
                                                id: 'ActReceiverTypeFarmer',
                                                boxLabel: lang('Farmer'),
                                                inputValue: 'farmer',
                                                listeners:{
                                                    change: function() {
                                                        Ext.getCmp('ActReceiverID').setValue('');
                                                        Ext.getCmp('ActReceiverIDLabel').setValue('');
                                                        return false;
                                                    }
                                                }
                                            }]
                                        }]
                                    },{
                                        layout: 'column',
                                        border: false,
                                        margin:'-15px 0 0 0',
                                        items: [{
                                            columnWidth: 0.2,
                                            xtype: 'label',
                                            cls: 'x-form-item-label',
                                            text: ''
                                        },{
                                            columnWidth: 0.8,
                                            border: false,
                                            layout:'form',
                                            items:[{
                                                xtype: 'hiddenfield',
                                                id: 'ActReceiverID',
                                                name: 'ActReceiverID'
                                            },{
                                                xtype: 'combo',
                                                width:'100%',
                                                store: store_staff_farmer_autocom_act2,
                                                id: 'ActReceiverIDLabel',
                                                name: 'ActReceiverIDLabel',
                                                displayField: 'label',
                                                typeAhead: false,
                                                hideTrigger: true,
                                                anchor: '100%',
                                                listConfig: {
                                                    loadingText: 'Searching...',
                                                    emptyText: 'No matching data found.',
                                                    getInnerTpl: function() {
                                                        return '<div class="search-item">' + '{label}' + '{excerpt}' + '</div>';
                                                    }
                                                },
                                                pageSize: 10,
                                                listeners: {
                                                    select: function(combo, selection) {
                                                        var post = selection[0];
                                                        if (post) {
                                                            Ext.getCmp('ActReceiverID').setValue(post.data.id);
                                                            Ext.getCmp('ActReceiverIDLabel').setValue(post.data.label);
                                                        }
                                                    }
                                                }
                                            }]
                                        }]
                                    },{
                                        layout: 'column',
                                        border: false,
                                        items: [{
                                            columnWidth: 0.2,
                                            xtype: 'label',
                                            cls: 'x-form-item-label',
                                            text: lang('Known By')+' 1 :'
                                        },{
                                            columnWidth: 0.4,
                                            border: false,
                                            layout:{
                                                type:'hbox',
                                                align:'stretch'
                                            },
                                            xtype: 'radiogroup',
                                            defaults: {
                                                margin: '0 20 0 0',
                                                flex:true
                                            },
                                            items: [{
                                                name: 'ActKnownByType',
                                                id: 'ActKnownByTypeStaff',
                                                boxLabel: lang('Staff'),
                                                inputValue: 'staff',
                                                listeners:{
                                                    change: function() {
                                                        Ext.getCmp('ActKnownByID').setValue('');
                                                        Ext.getCmp('ActKnownByIDLabel').setValue('');
                                                        return false;
                                                    }
                                                }
                                            },{
                                                name: 'ActKnownByType',
                                                id: 'ActKnownByTypeFarmer',
                                                boxLabel: lang('Farmer'),
                                                inputValue: 'farmer',
                                                listeners:{
                                                    change: function() {
                                                        Ext.getCmp('ActKnownByID').setValue('');
                                                        Ext.getCmp('ActKnownByIDLabel').setValue('');
                                                        return false;
                                                    }
                                                }
                                            }]
                                        }]
                                    },{
                                        layout: 'column',
                                        border: false,
                                        margin:'-15px 0 0 0',
                                        items: [{
                                            columnWidth: 0.2,
                                            xtype: 'label',
                                            cls: 'x-form-item-label',
                                            text: ''
                                        },{
                                            columnWidth: 0.8,
                                            border: false,
                                            layout:'form',
                                            items:[{
                                                xtype: 'hiddenfield',
                                                id: 'ActKnownByID',
                                                name: 'ActKnownByID'
                                            },{
                                                xtype: 'combo',
                                                width:'100%',
                                                store: store_staff_farmer_autocom_act3,
                                                id: 'ActKnownByIDLabel',
                                                name: 'ActKnownByIDLabel',
                                                displayField: 'label',
                                                typeAhead: false,
                                                hideTrigger: true,
                                                anchor: '100%',
                                                listConfig: {
                                                    loadingText: 'Searching...',
                                                    emptyText: 'No matching data found.',
                                                    getInnerTpl: function() {
                                                        return '<div class="search-item">' + '{label}' + '{excerpt}' + '</div>';
                                                    }
                                                },
                                                pageSize: 10,
                                                listeners: {
                                                    select: function(combo, selection) {
                                                        var post = selection[0];
                                                        if (post) {
                                                            Ext.getCmp('ActKnownByID').setValue(post.data.id);
                                                            Ext.getCmp('ActKnownByIDLabel').setValue(post.data.label);
                                                        }
                                                    }
                                                }
                                            }]
                                        }]
                                    },{
                                        layout: 'column',
                                        border: false,
                                        items: [{
                                            columnWidth: 0.2,
                                            xtype: 'label',
                                            cls: 'x-form-item-label',
                                            text: lang('Known By')+' 2 :'
                                        },{
                                            columnWidth: 0.4,
                                            border: false,
                                            layout:{
                                                type:'hbox',
                                                align:'stretch'
                                            },
                                            xtype: 'radiogroup',
                                            defaults: {
                                                margin: '0 20 0 0',
                                                flex:true
                                            },
                                            items: [{
                                                name: 'ActKnownByType2',
                                                id: 'ActKnownByType2Staff',
                                                boxLabel: lang('Staff'),
                                                inputValue: 'staff',
                                                listeners:{
                                                    change: function() {
                                                        Ext.getCmp('ActKnownByID2').setValue('');
                                                        Ext.getCmp('ActKnownByID2Label').setValue('');
                                                        return false;
                                                    }
                                                }
                                            },{
                                                name: 'ActKnownByType2',
                                                id: 'ActKnownByType2Farmer',
                                                boxLabel: lang('Farmer'),
                                                inputValue: 'farmer',
                                                listeners:{
                                                    change: function() {
                                                        Ext.getCmp('ActKnownByID2').setValue('');
                                                        Ext.getCmp('ActKnownByID2Label').setValue('');
                                                        return false;
                                                    }
                                                }
                                            }]
                                        }]
                                    },{
                                        layout: 'column',
                                        border: false,
                                        margin:'-15px 0 0 0',
                                        items: [{
                                            columnWidth: 0.2,
                                            xtype: 'label',
                                            cls: 'x-form-item-label',
                                            text: ''
                                        },{
                                            columnWidth: 0.8,
                                            border: false,
                                            layout:'form',
                                            items:[{
                                                xtype: 'hiddenfield',
                                                id: 'ActKnownByID2',
                                                name: 'ActKnownByID2'
                                            },{
                                                xtype: 'combo',
                                                width:'100%',
                                                store: store_staff_farmer_autocom_act4,
                                                id: 'ActKnownByID2Label',
                                                name: 'ActKnownByID2Label',
                                                displayField: 'label',
                                                typeAhead: false,
                                                hideTrigger: true,
                                                anchor: '100%',
                                                listConfig: {
                                                    loadingText: 'Searching...',
                                                    emptyText: 'No matching data found.',
                                                    getInnerTpl: function() {
                                                        return '<div class="search-item">' + '{label}' + '{excerpt}' + '</div>';
                                                    }
                                                },
                                                pageSize: 10,
                                                listeners: {
                                                    select: function(combo, selection) {
                                                        var post = selection[0];
                                                        if (post) {
                                                            Ext.getCmp('ActKnownByID2').setValue(post.data.id);
                                                            Ext.getCmp('ActKnownByID2Label').setValue(post.data.label);
                                                        }
                                                    }
                                                }
                                            }]
                                        }]
                                    }]
                                }]
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
                hidden:btnSaveHidden,
                handler: function () {
                    var form = Ext.getCmp('winFormRSet').getForm();

                    if (form.isValid()) {
                        form.submit({
                            url: m_api + '/training_receipt_setting/setting',
                            method:'POST',
                            waitMsg: 'Saving data...',
                            success: function(fp, data) {
                                var jsonResp = data.result;

                                if (jsonResp.prosesnya == 'insert') {
                                    Ext.getCmp('ReceiptSetID').setValue(jsonResp.id);
                                    Ext.getCmp('ObjType').setReadOnly(true);
                                    Ext.getCmp('btnSearchObjID').setVisible(false);
                                }

                                Ext.MessageBox.alert('Success', 'Data saved.');
                                store.load();
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
                    winRSet.close();
                }
            }]
        });
        var form = Ext.getCmp('winFormRSet').getForm();

        //form edit winFormRSet
        if(displayMethod == "add"){
            form.reset();
        }
        if(displayMethod == "update"){
            form.load({
                url: m_api + '/training_receipt_setting/form_setting',
                method: 'GET',
                params: {ReceiptSetID: ReceiptSetID},
                success: function(form, action) {
                    Ext.getCmp('ObjType').setReadOnly(true);
                    Ext.getCmp('btnSearchObjID').setVisible(false);

                    store_participant_goods.load();
                    store_activity_goods.load();
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
        }

        //show windows
        if (!winRSet.isVisible()) {
            winRSet.show();
        } else {
            winRSet.close();
        }

        function cekUpdateState(){
            if(Ext.getCmp('ReceiptSetID').getValue() != ""){
                return true;
            }else{
                Ext.MessageBox.show({
                    title: 'Notifications',
                    msg: 'Receipt not save yet',
                    buttons: Ext.MessageBox.OK,
                    animateTarget: 'mb9',
                    icon: 'ext-mb-info'
                });
                return false;
            }
        }
    }

    function displayPopupSelTraining(tipeTrain){

        var cmb_propinsi = Ext.create('Ext.data.Store', {
            extend: 'Ext.data.Model',
            fields: ['id', 'label'],
            autoLoad: true,
            proxy: {
                type: 'ajax',
                url: m_api + '/training_receipt_setting/provinsi',
                reader: {
                    type: 'json',
                    root: 'data'
                }
            },
            listeners: {
                'beforeload': function(store, options) {
                    store.proxy.extraParams.filter_prov = m_ProvinceID;
                }
            }
        });

        var cmb_district = Ext.create('Ext.data.Store', {
            extend: 'Ext.data.Model',
            fields: ['id', 'label'],
            autoLoad: true,
            proxy: {
                type: 'ajax',
                url: m_api + '/training_receipt_setting/district',
                reader: {
                    type: 'json',
                    root: 'data'
                }
            },
            listeners: {
                'beforeload': function(store, options) {
                    store.proxy.extraParams.filter_district = m_DistrictID;
                    store.proxy.extraParams.prov = Ext.getCmp('seltrainProvince').getValue();
                }
            }
        });

        /*
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
        */

        if(tipeTrain == 'farmergroup'){

            Ext.define('seltrainCpgModel.Model', {
                extend: 'Ext.data.Model',
                fields: ['id','Training','CPGLabel','Batch','TrainingStart','TrainingEnd']
            });

            var store_seltrain_cpg = Ext.create('Ext.data.Store', {
                model: 'seltrainCpgModel.Model',
                autoLoad: true,
                pageSize: 10,
                remoteSort: true,
                proxy: {
                    type: 'ajax',
                    url: m_api + '/training_receipt_setting/seltrain_cpg_grid',
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
                        store.proxy.extraParams.seltrainProvince = Ext.getCmp('seltrainProvince').getValue();
                        store.proxy.extraParams.seltrainDistrict = Ext.getCmp('seltrainDistrict').getValue();
                        store.proxy.extraParams.seltrainTraining = Ext.getCmp('seltrainTraining').getValue();
                        store.proxy.extraParams.seltrainTrainingDateRange = Ext.getCmp('seltrainTrainingDateRange').getValue();
                    }
                }
            });

            var winPopupSelTrain = Ext.create('widget.window', {
                title: lang('Search CPG Training'),
                id: 'winPopupSelTrain',
                closable: true,
                modal: true,
                closeAction: 'destroy',
                width: '85%',
                height: '85%',
                overflowY: 'auto',
                bodyStyle:{"background-color":"#F0F0F0"},
                style:'background-color:#F0F0F0;',
                padding:6,
                scrollOffset: 20,
                items:[{
                    xtype:'panel',
                    title: 'Filter',
                    frame:true,
                    bodyStyle:{"background-color":"#F0F0F0"},
                    style:'background-color:#F0F0F0;',
                    padding:2,
                    items:[{
                        layout: 'column',
                        border: false,
                        items: [{
                            columnWidth: 0.45,
                            padding: 4,
                            layout:'form',
                            defaults: {
                                labelWidth: 180
                            },
                            items:[{
                                xtype: 'combobox',
                                fieldLabel: lang('Province'),
                                store: cmb_propinsi,
                                queryMode: 'local',
                                displayField: 'label',
                                valueField: 'id',
                                id: 'seltrainProvince',
                                name: 'seltrainProvince',
                                listeners: {
                                    change: function(cb, nv, ov) {
                                        Ext.getCmp('seltrainDistrict').setValue('');
                                        cmb_district.load();
                                    }
                                }
                            },{
                                xtype: 'combobox',
                                fieldLabel: lang('District'),
                                store: cmb_district,
                                queryMode: 'local',
                                displayField: 'label',
                                valueField: 'id',
                                id: 'seltrainDistrict',
                                name: 'seltrainDistrict'
                            },{
                                xtype: 'combobox',
                                fieldLabel: lang('Training'),
                                store: cmb_training,
                                queryMode: 'local',
                                displayField: 'label',
                                valueField: 'id',
                                id: 'seltrainTraining',
                                name: 'seltrainTraining'
                            },{
                                xtype: 'datefield',
                                fieldLabel: lang('Training Date Range'),
                                id: 'seltrainTrainingDateRange',
                                name: 'seltrainTrainingDateRange',
                                format: 'Y-m-d'
                            },{
                                xtype: 'button',
                                width: 65,
                                id: 'seltrainBtnSearch',
                                scale: 'small',
                                margin: '0px 0px 0px 185px',
                                text: lang('Search'),
                                style: 'text-align:center;',
                                handler: function () {
                                    store_seltrain_cpg.load();
                                }
                            }]
                        },{
                            columnWidth: 0.55,
                            padding: 4,
                            layout:'form',
                            items:[{}]
                        }]
                    }]
                },{
                    xtype: 'gridpanel',
                    title: lang('CPG Training'),
                    id: 'seltrainGridCpg',
                    style: 'border:1px solid #CCC;margin-top:10px;',
                    store: store_seltrain_cpg,
                    width: '100%',
                    loadMask: true,
                    selType: 'rowmodel',
                    minHeight:300,
                    dockedItems: [{
                        xtype: 'pagingtoolbar',
                        store: store_seltrain_cpg, // same store GridPanel is using
                        dock: 'bottom',
                        displayInfo: true
                    }],
                    columns: [{
                        text: lang('ID'),
                        dataIndex: 'id',
                        hidden: true
                    },{
                        text: lang('No'),
                        xtype: 'rownumberer',
                        width: '5%'
                    },{
                        text: lang('Training'),
                        dataIndex: 'Training',
                        width: '30%'
                    },{
                        text: lang('CPG'),
                        dataIndex: 'CPGLabel',
                        width: '30%'
                    },{
                        text: lang('Batch'),
                        dataIndex: 'Batch',
                        width: '15%'
                    },{
                        text: lang('Training Start'),
                        dataIndex: 'TrainingStart',
                        width: '9%'
                    },{
                        text: lang('Training End'),
                        dataIndex: 'TrainingEnd',
                        width: '9%'
                    }],
                    listeners: {
                        itemdblclick: function(dv, record, item, index, e) {
                            var sm = record;
                            Ext.getCmp('TrainingTopic').setValue(sm.data.Training);
                            Ext.getCmp('ObjID').setValue(sm.data.id);
                            Ext.getCmp('ObjIDLabel').setValue('['+sm.data.CPGLabel+'] Batch : '+sm.data.Batch);

                            winPopupSelTrain.close();
                        }
                    },
                },{
                    html:'<span style="color:red;font:sans-serif 11px;">* Double click in the row data to select</span>'
                }],
                buttons:[{
                    text: lang('Close'),
                    margin: '5px',
                    scale: 'large',
                    ui: 's-button',
                    cls: 's-grey',
                    disabled: false,
                    handler: function() {
                        winPopupSelTrain.close();
                    }
                }]
            });
        }

        if(tipeTrain == 'cadre'){
            Ext.define('seltrainCpgModel.Model', {
                extend: 'Ext.data.Model',
                fields: ['id','Training','Batch','TrainingStart','TrainingEnd']
            });

            var store_seltrain_cadre = Ext.create('Ext.data.Store', {
                model: 'seltrainCpgModel.Model',
                autoLoad: true,
                pageSize: 10,
                remoteSort: true,
                proxy: {
                    type: 'ajax',
                    url: m_api + '/training_receipt_setting/seltrain_cadre_grid',
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
                        store.proxy.extraParams.seltrainProvince = Ext.getCmp('seltrainProvince').getValue();
                        store.proxy.extraParams.seltrainDistrict = Ext.getCmp('seltrainDistrict').getValue();
                        store.proxy.extraParams.seltrainTraining = Ext.getCmp('seltrainTraining').getValue();
                        store.proxy.extraParams.seltrainTrainingDateRange = Ext.getCmp('seltrainTrainingDateRange').getValue();
                    }
                }
            });

            var winPopupSelTrain = Ext.create('widget.window', {
                title: lang('Search Cadre Training'),
                id: 'winPopupSelTrain',
                closable: true,
                modal: true,
                closeAction: 'destroy',
                width: '85%',
                height: '85%',
                overflowY: 'auto',
                bodyStyle:{"background-color":"#F0F0F0"},
                style:'background-color:#F0F0F0;',
                padding:6,
                scrollOffset: 20,
                items:[{
                    xtype:'panel',
                    title: 'Filter',
                    frame:true,
                    bodyStyle:{"background-color":"#F0F0F0"},
                    style:'background-color:#F0F0F0;',
                    padding:2,
                    items:[{
                        layout: 'column',
                        border: false,
                        items: [{
                            columnWidth: 0.45,
                            padding: 4,
                            layout:'form',
                            defaults: {
                                labelWidth: 180
                            },
                            items:[{
                                xtype: 'combobox',
                                fieldLabel: lang('Province'),
                                store: cmb_propinsi,
                                queryMode: 'local',
                                displayField: 'label',
                                valueField: 'id',
                                id: 'seltrainProvince',
                                name: 'seltrainProvince',
                                listeners: {
                                    change: function(cb, nv, ov) {
                                        Ext.getCmp('seltrainDistrict').setValue('');
                                        cmb_district.load();
                                    }
                                }
                            },{
                                xtype: 'combobox',
                                fieldLabel: lang('District'),
                                store: cmb_district,
                                queryMode: 'local',
                                displayField: 'label',
                                valueField: 'id',
                                id: 'seltrainDistrict',
                                name: 'seltrainDistrict'
                            },{
                                xtype: 'combobox',
                                fieldLabel: lang('Training'),
                                store: cmb_training,
                                queryMode: 'local',
                                displayField: 'label',
                                valueField: 'id',
                                id: 'seltrainTraining',
                                name: 'seltrainTraining'
                            },{
                                xtype: 'datefield',
                                fieldLabel: lang('Training Date Range'),
                                id: 'seltrainTrainingDateRange',
                                name: 'seltrainTrainingDateRange',
                                format: 'Y-m-d'
                            },{
                                xtype: 'button',
                                width: 65,
                                id: 'seltrainBtnSearch',
                                scale: 'small',
                                margin: '0px 0px 0px 185px',
                                text: lang('Search'),
                                style: 'text-align:center;',
                                handler: function () {
                                    store_seltrain_cadre.load();
                                }
                            }]
                        },{
                            columnWidth: 0.55,
                            padding: 4,
                            layout:'form',
                            items:[{}]
                        }]
                    }]
                },{
                    xtype: 'gridpanel',
                    title: lang('Cadre Training'),
                    id: 'seltrainGridCpg',
                    style: 'border:1px solid #CCC;margin-top:10px;',
                    store: store_seltrain_cadre,
                    width: '100%',
                    loadMask: true,
                    selType: 'rowmodel',
                    minHeight:300,
                    dockedItems: [{
                        xtype: 'pagingtoolbar',
                        store: store_seltrain_cadre, // same store GridPanel is using
                        dock: 'bottom',
                        displayInfo: true
                    }],
                    columns: [{
                        text: lang('ID'),
                        dataIndex: 'id',
                        hidden: true
                    },{
                        text: lang('No'),
                        xtype: 'rownumberer',
                        width: '5%'
                    },{
                        text: lang('Training'),
                        dataIndex: 'Training',
                        width: '40%'
                    },{
                        text: lang('Batch'),
                        dataIndex: 'Batch',
                        width: '30%'
                    },{
                        text: lang('Training Start'),
                        dataIndex: 'TrainingStart',
                        width: '11%'
                    },{
                        text: lang('Training End'),
                        dataIndex: 'TrainingEnd',
                        width: '11%'
                    }],
                    listeners: {
                        itemdblclick: function(dv, record, item, index, e) {
                            var sm = record;
                            Ext.getCmp('TrainingTopic').setValue(sm.data.Training);
                            Ext.getCmp('ObjID').setValue(sm.data.id);
                            Ext.getCmp('ObjIDLabel').setValue('Batch : '+sm.data.Batch);

                            winPopupSelTrain.close();
                        }
                    },
                },{
                    html:'<span style="color:red;font:sans-serif 11px;">* Double click in the row data to select</span>'
                }],
                buttons:[{
                    text: lang('Close'),
                    margin: '5px',
                    scale: 'large',
                    ui: 's-button',
                    cls: 's-grey',
                    disabled: false,
                    handler: function() {
                        winPopupSelTrain.close();
                    }
                }]
            });

        }

        if(tipeTrain == 'master'){
            Ext.define('seltrainCpgModel.Model', {
                extend: 'Ext.data.Model',
                fields: ['id','Training','Batch','TrainingStart','TrainingEnd']
            });

            var store_seltrain_master = Ext.create('Ext.data.Store', {
                model: 'seltrainCpgModel.Model',
                autoLoad: true,
                pageSize: 10,
                remoteSort: true,
                proxy: {
                    type: 'ajax',
                    url: m_api + '/training_receipt_setting/seltrain_master_grid',
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
                        store.proxy.extraParams.seltrainProvince = Ext.getCmp('seltrainProvince').getValue();
                        store.proxy.extraParams.seltrainDistrict = Ext.getCmp('seltrainDistrict').getValue();
                        store.proxy.extraParams.seltrainTraining = Ext.getCmp('seltrainTraining').getValue();
                        store.proxy.extraParams.seltrainTrainingDateRange = Ext.getCmp('seltrainTrainingDateRange').getValue();
                    }
                }
            });

            var winPopupSelTrain = Ext.create('widget.window', {
                title: lang('Search Master Training'),
                id: 'winPopupSelTrain',
                closable: true,
                modal: true,
                closeAction: 'destroy',
                width: '85%',
                height: '85%',
                overflowY: 'auto',
                bodyStyle:{"background-color":"#F0F0F0"},
                style:'background-color:#F0F0F0;',
                padding:6,
                scrollOffset: 20,
                items:[{
                    xtype:'panel',
                    title: 'Filter',
                    frame:true,
                    bodyStyle:{"background-color":"#F0F0F0"},
                    style:'background-color:#F0F0F0;',
                    padding:2,
                    items:[{
                        layout: 'column',
                        border: false,
                        items: [{
                            columnWidth: 0.45,
                            padding: 4,
                            layout:'form',
                            defaults: {
                                labelWidth: 180
                            },
                            items:[{
                                xtype: 'combobox',
                                fieldLabel: lang('Province'),
                                store: cmb_propinsi,
                                queryMode: 'local',
                                displayField: 'label',
                                valueField: 'id',
                                id: 'seltrainProvince',
                                name: 'seltrainProvince',
                                listeners: {
                                    change: function(cb, nv, ov) {
                                        Ext.getCmp('seltrainDistrict').setValue('');
                                        cmb_district.load();
                                    }
                                }
                            },{
                                xtype: 'combobox',
                                fieldLabel: lang('District'),
                                store: cmb_district,
                                queryMode: 'local',
                                displayField: 'label',
                                valueField: 'id',
                                id: 'seltrainDistrict',
                                name: 'seltrainDistrict'
                            },{
                                xtype: 'combobox',
                                fieldLabel: lang('Training'),
                                store: cmb_training,
                                queryMode: 'local',
                                displayField: 'label',
                                valueField: 'id',
                                id: 'seltrainTraining',
                                name: 'seltrainTraining'
                            },{
                                xtype: 'datefield',
                                fieldLabel: lang('Training Date Range'),
                                id: 'seltrainTrainingDateRange',
                                name: 'seltrainTrainingDateRange',
                                format: 'Y-m-d'
                            },{
                                xtype: 'button',
                                width: 65,
                                id: 'seltrainBtnSearch',
                                scale: 'small',
                                margin: '0px 0px 0px 185px',
                                text: lang('Search'),
                                style: 'text-align:center;',
                                handler: function () {
                                    store_seltrain_master.load();
                                }
                            }]
                        },{
                            columnWidth: 0.55,
                            padding: 4,
                            layout:'form',
                            items:[{}]
                        }]
                    }]
                },{
                    xtype: 'gridpanel',
                    title: lang('Master Training'),
                    id: 'seltrainGridCpg',
                    style: 'border:1px solid #CCC;margin-top:10px;',
                    store: store_seltrain_master,
                    width: '100%',
                    loadMask: true,
                    selType: 'rowmodel',
                    minHeight:300,
                    dockedItems: [{
                        xtype: 'pagingtoolbar',
                        store: store_seltrain_master, // same store GridPanel is using
                        dock: 'bottom',
                        displayInfo: true
                    }],
                    columns: [{
                        text: lang('ID'),
                        dataIndex: 'id',
                        hidden: true
                    },{
                        text: lang('No'),
                        xtype: 'rownumberer',
                        width: '5%'
                    },{
                        text: lang('Training'),
                        dataIndex: 'Training',
                        width: '40%'
                    },{
                        text: lang('Batch'),
                        dataIndex: 'Batch',
                        width: '30%'
                    },{
                        text: lang('Training Start'),
                        dataIndex: 'TrainingStart',
                        width: '11%'
                    },{
                        text: lang('Training End'),
                        dataIndex: 'TrainingEnd',
                        width: '11%'
                    }],
                    listeners: {
                        itemdblclick: function(dv, record, item, index, e) {
                            var sm = record;
                            Ext.getCmp('TrainingTopic').setValue(sm.data.Training);
                            Ext.getCmp('ObjID').setValue(sm.data.id);
                            Ext.getCmp('ObjIDLabel').setValue('Batch : '+sm.data.Batch);

                            winPopupSelTrain.close();
                        }
                    },
                },{
                    html:'<span style="color:red;font:sans-serif 11px;">* Double click in the row data to select</span>'
                }],
                buttons:[{
                    text: lang('Close'),
                    margin: '5px',
                    scale: 'large',
                    ui: 's-button',
                    cls: 's-grey',
                    disabled: false,
                    handler: function() {
                        winPopupSelTrain.close();
                    }
                }]
            });

        }

        //show windows
        if (!winPopupSelTrain.isVisible()) {
            winPopupSelTrain.show();
        } else {
            winPopupSelTrain.close();
        }
    }

    function displayPopupSelGoods(callFrom,store_participant_goods,store_activity_goods){
        var store_selgoods = Ext.create('Ext.data.Store', {
            model: 'goodsList.Model',
            autoLoad: true,
            pageSize: 10,
            remoteSort: true,
            proxy: {
                type: 'ajax',
                url: m_api + '/training_receipt_setting/goods_list_filter',
                reader: {
                    type: 'json',
                    root: 'data',
                    totalProperty: 'total'
                }
            },
            listeners: {
                'beforeload': function(store, options) {
                    store.proxy.extraParams.call_from = callFrom;
                    store.proxy.extraParams.filter_name = Ext.getCmp('sGoodsFilterName').getValue();
                }
            }
        });

        var winPopupSelGoods = Ext.create('widget.window', {
            title: lang('Search Goods'),
            id: 'winPopupSelGoods',
            closable: true,
            modal: true,
            closeAction: 'destroy',
            width: '60%',
            height: '70%',
            overflowY: 'auto',
            bodyStyle:{"background-color":"#F0F0F0"},
            style:'background-color:#F0F0F0;',
            padding:3,
            items:[{
                xtype: 'gridpanel',
                title: lang('Goods List'),
                id: 'selgoodsGrid',
                style: 'border:1px solid #CCC;',
                store: store_selgoods,
                width: '100%',
                loadMask: true,
                selType: 'rowmodel',
                minHeight:425,
                dockedItems: [{
                    xtype: 'pagingtoolbar',
                    store: store_selgoods, // same store GridPanel is using
                    dock: 'bottom',
                    displayInfo: true
                },{
                    xtype: 'toolbar',
                    items: [{
                        name: 'sGoodsFilterName',
                        id: 'sGoodsFilterName',
                        xtype: 'textfield',
                        width: 200,
                        emptyText: lang('Name'),
                        listeners: {
                            specialkey: function(f,event){
                                if (event.getKey() == event.ENTER) {
                                    store_selgoods.load({
                                        params: {
                                            page: 1,
                                            start: 0,
                                            limit: 10
                                        }
                                    });
                                }
                            }
                        }
                    },{
                        xtype: 'button',
                        icon: varjs.config.base_url + 'images/icons/silk/search.png',
                        margin: '0px 0px 0px 6px',
                        text: lang('Search'),
                        handler: function() {
                            store_selgoods.load({
                                params: {
                                    page: 1,
                                    start: 0,
                                    limit: 10
                                }
                            });
                        }
                    }]
                }],
                columns: [{
                    dataIndex: 'id',
                    hidden: true
                },{
                    xtype : 'checkcolumn',
                    text : '&nbsp;',
                    dataIndex : 'chdata',
                    width:'3%'
                },{
                    text: lang('No'),
                    xtype: 'rownumberer',
                    width: '4%'
                },{
                    text: lang('Code'),
                    width: '25%',
                    dataIndex: 'code'
                },{
                    text: lang('Name'),
                    width: '47%',
                    dataIndex: 'name'
                },{
                    text: lang('Unit'),
                    width: '15%',
                    dataIndex: 'unit'
                }]
            }],
            buttons:[{
                text: 'Insert',
                margin: '5px',
                scale: 'large',
                ui: 's-button',
                cls: 's-blue',
                handler: function () {
                    var records = store_selgoods.queryBy(function(record) {
                        return record.get('chdata') === true;
                    });
                    var ids = [];
                    records.each(function(record) {
                        ids.push(record.get('id'));
                    });

                    if(ids.length > 0){
                        //insert kan ke tabel
                        Ext.Ajax.request({
                            url: m_api + '/training_receipt_setting/setting_insert_goods',
                            method: 'POST',
                            params: {
                                GoodsID: Ext.encode(ids),
                                ReceiptSetID: Ext.getCmp('ReceiptSetID').getValue(),
                                callFrom : callFrom
                            },
                            success: function(response, o) {
                                var obj = Ext.decode(response.responseText);

                                if(callFrom == "participant"){
                                    store_participant_goods.load();
                                }
                                if(callFrom == "activity"){
                                    store_activity_goods.load();
                                }

                                winPopupSelGoods.close();
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
                margin: '5px',
                scale: 'large',
                ui: 's-button',
                cls: 's-grey',
                disabled: false,
                handler: function() {
                    winPopupSelGoods.close();
                }
            }]
        });

        //show windows
        if (!winPopupSelGoods.isVisible()) {
            winPopupSelGoods.show();
        } else {
            winPopupSelGoods.close();
        }
    }

});