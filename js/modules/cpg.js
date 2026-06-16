Ext.Loader.setConfig({enabled: true});
Ext.Loader.setPath('js/ext-4.2.0.663', '../ux');
//Ext.Loader.setPath('js/ext-4.2.0.663/ux/form');
Ext.require([
    //'Ext.form.Panel',
    //'Ext.ux.form.MultiSelect',
    'Ext.ux.form.ItemSelector'
]);

if (Ext.getCmp('winparchecklist')) Ext.getCmp('winparchecklist').destroy();
if (Ext.getCmp('winparchecklistday')) Ext.getCmp('winparchecklistday').destroy();
if (Ext.getCmp('winSelectDay')) Ext.getCmp('winSelectDay').destroy();

//override time out ajax exts js yg cuman 30 detikan
Ext.Ajax.timeout = 120000;
Ext.override(Ext.form.Basic, {
    timeout: Ext.Ajax.timeout / 1000
});
Ext.override(Ext.data.proxy.Server, {
    timeout: Ext.Ajax.timeout
});
Ext.override(Ext.data.Connection, {
    timeout: Ext.Ajax.timeout
});

Ext.onReady(function() {
    Ext.tip.QuickTipManager.init();

    var store = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'OldCPGid', 'GroupName', 'Address', 'RegionID', 'TahunTerbentuk', 'RegionName', 'Anggota', 'BatchNumber', 'PartnerName','totalLandSize','totalGarden'],
        pageSize: 50,
        autoLoad: true,
        remoteSort: true,
        proxy: {
            type: 'ajax',
            url: m_crud + 's',
            extraParams: {prov: m_ProvinceID, kab:m_District, subdist:m_SubDistrictID},
            reader: {
                type: 'json',
                root: 'data',
                totalProperty: 'total'
            }
        },
        sorters: [{
                property: 'GroupName',
                direction: 'ASC'
            }]
    });
    store.on('beforeload', function() {
        var proxy = store.getProxy();
        var isAdvFilter = Ext.getCmp('idPanelAdvFilter').isVisible();
        if(isAdvFilter == true){
            var opsiSearch = 'adv';
        }else{
            var opsiSearch = 'simple';
        }

        if(opsiSearch == 'simple'){
            proxy.setExtraParam('opsiSearch', opsiSearch);
            proxy.setExtraParam('key', Ext.getCmp('key').getValue());
            // proxy.setExtraParam('kab', Ext.getCmp('Kab').getValue());
        }

        if(opsiSearch == 'adv'){
            //cek dipilih atau kaga
            if(Ext.getCmp('rowNameId').isVisible() == true){
                var parAdvNamaId = Ext.getCmp('advNameId').getValue();
            }else{
                var parAdvNamaId = 'not_set';
            }
            if(Ext.getCmp('rowDistrict').isVisible() == true){
                var parAdvDistrict = Ext.getCmp('advDistrict').getValue().join().replace(/,/g, '::');
            }else{
                var parAdvDistrict = 'not_set';
            }
            if(Ext.getCmp('rowBatch').isVisible() == true){
                var parAdvBatch = Ext.getCmp('advBatch').getValue();
            }else{
                var parAdvBatch = 'not_set';
            }
            if(Ext.getCmp('rowNursery').isVisible() == true){
                var parAdvNursery = Ext.getCmp('advNursery').getValue();
            }else{
                var parAdvNursery = 'not_set';
            }

            proxy.setExtraParam('opsiSearch', opsiSearch);
            proxy.setExtraParam('parAdvNamaId', parAdvNamaId);
            proxy.setExtraParam('parAdvDistrict', parAdvDistrict);
            proxy.setExtraParam('parAdvBatch', parAdvBatch);
            proxy.setExtraParam('parAdvNursery', parAdvNursery);
        }

    });

    var store_training = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'label', 'CpgTrainings', 'TrainingStart', 'TrainingEnd', 'participant', 'CPGID', 'CpgTrainingsID',
            'ProgramStaffID', 'ExtensionStaffID', 'KeyFarmerID', 'DemoplotOwnerID', 'PetaniKakao', 'FamilyID', 'CpgBatchID', 'TrainingDays', 'GroupName', 'BatchNumber','TrainingDayStatus', 'subtopic'],
        autoLoad: false,
        pageSize: 10,
        sorters: [{
                property: 'TrainingStart',
                direction: 'ASC'
            }],
        proxy: {
            type: 'ajax',
            url: m_training + 's',
            reader: {
                type: 'json',
                root: 'data',
                totalProperty: 'total'
            }
        }
    });
    var mc_Provinsi = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'label'],
        autoLoad: true,
        pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_Provinsi,
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });
    var mc_Kabupaten = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'label'],
        autoLoad: true,
        pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_Kabupaten,
            extraParams: {prov: m_ProvinceID},
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });
    var mc_Kecamatan = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'label'],
        autoLoad: false,
        pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_Kecamatan,
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });
    var mc_demo_plot = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'label'],
        autoLoad: false,
        pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_demo_plot,
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });
    var mc_Desa = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'label'],
        autoLoad: false,
        pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_Desa,
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });

    var cmb_respon_type = Ext.create('Ext.data.Store', {
        fields: ['id', 'label'],
        data: [
        {
            "id": "farmer",
            "label": lang("Farmer")
        }, {
            "id": "staff",
            "label": "Staff"
        }, {
            "id": "other",
            "label": lang("Other")
        },
        ]
    });

    var cmb_respon_id = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'label'],
        autoLoad: false,
        proxy: {
            type: 'ajax',
            url: m_api + '/cpg/respon_by_type',
            reader: {
                type: 'json',
                root: 'data'
            }
        },
        listeners: {
            'beforeload': function(store, options) {
                store.proxy.extraParams.responsibleType = Ext.getCmp('nurResponsibleType').getValue();
                store.proxy.extraParams.CPGid = Ext.getCmp('CPGId').getValue();
            }
        }
    });

    var store_participant = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['CpgBatchTrainingsFarmerID', 'CpgBatchTrainingID', 'pFarmerID', 'PetaniKakao', 'FamilyID', 'AnggotaName',
            'WritingAwal', 'WritingAkhir', 'BallotAwal', 'BallotAkhir', 'PersonNm', 'partisipan'],
        pageSize: 10,
        autoLoad: false,
        proxy: {
            type: 'ajax',
            url: m_participant + 's',
            reader: {
                type: 'json',
                root: 'data',
                totalProperty: 'total'
            }
        }
    });
    var store_participant_checklist = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['DayNumber', 'Attendance1', 'Attendance2', 'TrainingDate'],
        autoLoad: false,
        proxy: {
            type: 'ajax',
            url: m_participant_checklist + 's',
            reader: {
                type: 'json',
                root: 'data',
                totalProperty: 'total'
            }
        }
    });
    var store_participant_checklist_day = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['FarmerID','FamilyID','FarmerName','AnggotaName','Attendance1','Attendance2'],
        autoLoad: false,
        proxy: {
            type: 'ajax',
            url: m_participant_checklist_day,
            reader: {
                type: 'json',
                root: 'data',
                totalProperty: 'total'
            }
        }
    });
    var mc_RegionID = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'label'],
        autoLoad: false,
        pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_RegionID,
            reader: {
                type: 'json',
                root: 'data',
                totalProperty: 'totalCount'
            }
        }
    });
    var mc_batch = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'label'],
        autoLoad: true,
        proxy: {
            type: 'ajax',
            url: m_batch,
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });
    var mc_family = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'label'],
        autoLoad: false,
        pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_family,
            reader: {
                type: 'json',
                root: 'data',
            }
        }
    });
    var mc_training = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'label'],
        autoLoad: true,
        pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_training_name,
            reader: {
                type: 'json',
                root: 'data',
                totalProperty: 'totalCount'
            }
        }
    });

    var mc_family_training = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'label'],
        autoLoad: false,
        proxy: {
            type: 'ajax',
            url: m_family_training,
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });

    var store_participant_add = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: [{name: 'addFarmerID'}, {name: 'addFarmerName'}],
        //pageSize: 10,
        autoLoad: false,
        proxy: {
            type: 'ajax',
            url: m_participant + 's_add',
            reader: {
                type: 'json',
                root: 'data',
                totalProperty: 'total'
            }
        }
    });

    var store_staff_access = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'label'],
        autoLoad: false,
        proxy: {
            type: 'ajax',
            url: m_store_staff_access,
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });
    var store_staff = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'label'],
        autoLoad: false,
        proxy: {
            type: 'ajax',
            url: m_store_staff,
            extraParams: {prov: m_param},
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });
    var cellEditing = new Ext.grid.plugin.CellEditing({
        clicksToEdit: 1
    });
    //staff
    Ext.define('staff.Model', {
        extend: 'Ext.data.Model',
        fields: ['StaffID', 'CPGid', 'Status', 'FarmerID', 'StaffName', 'Position', 'Phone', 'Email', 'StaffBirthday', 'StaffGender'],
    });
    var store_staff_cpg = Ext.create('Ext.data.Store', {
        model: 'staff.Model',
        autoLoad: false,
        pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_staff + 's',
            reader: {
                type: 'json',
                root: 'data',
                totalProperty: 'total'
            }
        }
    });
    var store_member_cpg = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['FarmerID', 'FarmerName', 'cpgMemberGender', 'cpgMemberVillage', 'cpgMemberAge', 'garden_count', 'garden_ha'],
        autoLoad: false,
        //pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_member,
            reader: {
                type: 'json',
                root: 'data',
                totalProperty: 'total'
            }
        }
    });
    var sRowEditing = Ext.create('Ext.grid.plugin.RowEditing', {
        id: 'sRowEditing',
        clicksToMoveEditor: 0,
        autoCancel: false,
        errorSummary: false,
        clicksToEdit: 2
    });
    var cposition = Ext.create('Ext.data.Store', {
        fields: ['label'],
        data: [
            {"label": "Ketua Badan Pengawas"},
            {"label": "Ketua"},
            {"label": "Wakil Ketua"},
            {"label": "Sekretaris"},
            {"label": "Wakil Sekretaris"},
            {"label": "Bendahara"},
            {"label": "Wakil Bendahara"}
        ]
    });
    var ckelamin = Ext.create('Ext.data.Store', {
        fields: ['id', 'label'],
        data: [
            {"id": "1", "label": "Laki-laki"},
            {"id": "2", "label": "Perempuan"}
        ]
    });
    var ceducation = Ext.create('Ext.data.Store', {
        fields: ['id', 'label'],
        data: [
            {"id": "1", "label": "Belum pernah sekolah"},
            {"id": "2", "label": "Tidak tamat SD"},
            {"id": "3", "label": "Tamat SD, tidak melanjutkan"},
            {"id": "4", "label": "Tamat SMP"},
            {"id": "5", "label": "Tamat SMA/SMK"},
            {"id": "6", "label": "Tamat perguruan tinggi"}
        ]
    });
    var cfarmer = Ext.create('Ext.data.Store', {
        fields: ['label'],
        data: [
            {"label": "Farmer"},
            {"label": "Non Farmer"}
        ]
    });

    function gs_edit() {
        if (Ext.getCmp('farmer').getValue() == 'Farmer') {
            Ext.getCmp('lfarmer').setVisible(true)
            Ext.getCmp('lnon').setVisible(false)
            Ext.getCmp('lhp').setReadOnly(true)
            //Ext.getCmp('lemail').setReadOnly(true)
            Ext.getCmp('StaffGender').setReadOnly(true)
            Ext.getCmp('lbirthday').setReadOnly(true)
        } else {
            Ext.getCmp('lfarmer').setVisible(false)
            Ext.getCmp('lnon').setVisible(true)
            Ext.getCmp('lhp').setReadOnly(false)
            //Ext.getCmp('lemail').setReadOnly(false)
            Ext.getCmp('StaffGender').setReadOnly(false)
            Ext.getCmp('lbirthday').setReadOnly(false)
        }
    }

    Ext.define("Post", {
        extend: 'Ext.data.Model',
        proxy: {
            type: 'ajax',
            url: m_staff + '_farmers',
            reader: {
                type: 'json',
                root: 'data',
                totalProperty: 'total'
            }
        },
        fields: [
            {name: 'id', mapping: 'id'},
            {name: 'name', mapping: 'name'},
            {name: 'handphone', mapping: 'hp'},
            {name: 'email', mapping: 'email'},
            {name: 'birthdate', mapping: 'birthdate'},
            {name: 'kelamin', mapping: 'kelamin'}
        ]
    });

    var ds = Ext.create('Ext.data.Store', {
        pageSize: 10,
        model: 'Post'
    });
    //end staff
    function displayFormWindow() {
        if (!win.isVisible()) {
            Ext.getCmp('Desa').setValue('');
            DataForm.getForm().reset();
            win.center();
            win.show();
            Ext.Ajax.request({
                url: m_area,
                method: 'GET',
                params: {key: m_param},
                success: function(fp, o) {
                    var r = Ext.decode(fp.responseText);
                    Ext.getCmp('Provinsi').setValue(r.Provinsi);
                    // Ext.getCmp('Kabupaten').setValue(Ext.getCmp('Kab').getValue());
                    Ext.getCmp('Kabupaten').setValue(m_District);

                }
            });
            Ext.getCmp('Desa').disable();
            Ext.getCmp('GroupName').focus(true, true);
        } else {
            win.hide(this, function() {
            });
            win.toFront();
        }
        Ext.getCmp('win').center()
    }

    function hideSave() {
        Ext.getCmp('saveButton').hide();
        Ext.getCmp('save_par').hide();
        Ext.getCmp('saveButtonDemoplot').hide();
        Ext.getCmp('nsaveButton').hide();
        Ext.getCmp('save_par_check').hide();
        Ext.getCmp('save_par_add').hide();
        if (Ext.getCmp('CPGId').getValue() === '' && m_act_add) {
            Ext.getCmp('saveButton').show();
            Ext.getCmp('save_par').show();
            Ext.getCmp('saveButtonDemoplot').show();
            Ext.getCmp('nsaveButton').show();
            Ext.getCmp('save_par_check').show();
            Ext.getCmp('save_par_add').show();
        }
        if (Ext.getCmp('CPGId').getValue() !== '' && m_act_update) {
            Ext.getCmp('saveButton').show();
            Ext.getCmp('save_par').show();
            Ext.getCmp('saveButtonDemoplot').show();
            Ext.getCmp('nsaveButton').show();
            Ext.getCmp('save_par_check').show();
            Ext.getCmp('save_par_add').show();
        }
    }

    function displayBeforeCetak() {
        if (!winBeforeCetak.isVisible()) {
            winBeforeCetak.center();
            winBeforeCetak.show();
        } else {
            winBeforeCetak.hide(this, function() {
            });
            winBeforeCetak.toFront();
        }
        Ext.getCmp('h_p1').hide();
        Ext.getCmp('h_f1').hide();
        Ext.getCmp('h_n1').hide();
        Ext.getCmp('h_ppi').hide();
        if (jenis == 'P1') {
            Ext.getCmp('h_p1').show();
            Ext.getCmp('print').setTitle('Cetak GAP');
        } else if (jenis == 'F1') {
            Ext.getCmp('h_f1').show();
            Ext.getCmp('print').setTitle('Cetak GFP');
        } else if (jenis == 'N1') {
            Ext.getCmp('h_n1').show();
            Ext.getCmp('print').setTitle('Cetak GNP');
        } else if (jenis == 'PPI') {
            Ext.getCmp('h_ppi').show();
            Ext.getCmp('print').setTitle('Cetak PPI');
        }
    }

    function displayBeforeCetakAttendanceList() {
        if (!winBeforeCetakAttendanceList.isVisible()) {
            winBeforeCetakAttendanceList.center();
            winBeforeCetakAttendanceList.show();
        } else {
            winBeforeCetakAttendanceList.hide(this, function() {
            });
            winBeforeCetakAttendanceList.toFront();
        }
         Ext.getCmp('printAttendanceList').setTitle(lang('Print Attendance List'));
    }

    var CpgBatchTrainingID;
    var SurveyID;
    var jenis;
    var DataFormAccess = Ext.create('Ext.form.Panel', {
        frame: false,
        autoScroll: true,
        height: 350,
        width: 500,
        bodyPadding: 5,
        id: 'dataFormAccess',
        items: [{
                xtype: 'gridpanel',
                id: 'gaccess',
                store: store_staff_access,
                width: '100%',
                loadMask: true,
                selType: 'rowmodel',
                dockedItems: [{
                        xtype: 'toolbar',
                        items: [{
                                xtype: 'textfield',
                                id: 'cpg_id',
                                inputType: 'hidden'
                            }, {
                                id: 'staffa',
                                name: 'staffa',
                                xtype: 'combo',
                                store: store_staff,
                                displayField: 'label',
                                valueField: 'id',
                                queryMode: 'local'
                            }, {
                                icon: varjs.config.base_url + 'images/icons/new/add.png', cls:'Sfr_BtnGridGreen', overCls:'Sfr_BtnGridGreen-Hover',
                                text: lang('Add'),
                                scope: this,
                                handler: function() {
                                    Ext.Ajax.request({
                                        waitMsg: lang('Please Wait'),
                                        url: m_crud + '_access',
                                        method: 'POST',
                                        params: {cpg: Ext.getCmp('cpg_id').getValue(), staff: Ext.getCmp('staffa').getValue()},
                                        success: function(response, opts) {
                                            var obj = Ext.decode(response.responseText);
                                            switch (obj.success) {
                                                case true:
                                                    Ext.getCmp('staffa').setValue('');
                                                    store_staff_access.load({
                                                        params: {
                                                            id: Ext.getCmp('cpg_id').getValue()
                                                        }
                                                    });
                                                    store_staff.load({
                                                        params: {
                                                            cpg: Ext.getCmp('cpg_id').getValue()
                                                        }
                                                    });
                                                    break;
                                                default:
                                                    Ext.MessageBox.alert('Warning', obj.message);
                                                    break;
                                            }
                                        }
                                    })
                                }
                            }]
                    }],
                columns: [{
                        text: '#',
                        xtype: 'rownumberer',
                        width: '10%'
                    },
                    {
                        text: lang('Nama'),
                        dataIndex: 'label',
                        width: '75%'
                    }, {
                        text: lang('Action'),
                        xtype: 'actioncolumn',
                        width: '15%',
                        items: [{
                                icon: varjs.config.base_url + 'images/icons/new/delete.png',
                                tooltip: 'Delete',
                                hidden: m_act_delete,
                                handler: function(grid, rowIndex, colIndex) {
                                    var sma = grid.getStore().getAt(rowIndex);
                                    Ext.MessageBox.confirm('Message', lang('Apakah anda mau menghapus data ini ?'), function(btn) {
                                        if (btn == 'yes') {
                                            Ext.Ajax.request({
                                                waitMsg: lang('Please Wait'),
                                                url: m_crud + '_access',
                                                method: 'DELETE',
                                                params: {cpg: Ext.getCmp('cpg_id').getValue(), staff: sma.get('label')},
                                                success: function(response, opts) {
                                                    var obj = Ext.decode(response.responseText);
                                                    switch (obj.success) {
                                                        case true:
                                                            store_staff.load({
                                                                params: {
                                                                    cpg: Ext.getCmp('cpg_id').getValue()
                                                                }
                                                            });
                                                            store_staff_access.load({
                                                                params: {
                                                                    id: Ext.getCmp('cpg_id').getValue()
                                                                }
                                                            });
                                                            break;
                                                        default:
                                                            Ext.MessageBox.alert('Warning', obj.message);
                                                            break;
                                                    }
                                                },
                                                failure: function(response, opts) {
                                                    var obj = Ext.decode(response.responseText);
                                                    Ext.MessageBox.alert('error', lang('Could not connect to the database. Retry later'));
                                                }
                                            });
                                        }
                                    });
                                }
                            }]
                    }]
            }]
    });
    var AccessWin = Ext.create('widget.window', {
        id: 'access_win',
        title: lang('Access Farmer Group'),
        closable: true,
        modal: true,
        closeAction: 'show',
        width: 530,
        height: 400,
        layout: {
            type: 'border',
            padding: 5
        },
        items: [DataFormAccess]
    });

    function displayAccessWindow() {
        if (!AccessWin.isVisible()) {
            AccessWin.center();
            AccessWin.show();
        } else {
            AccessWin.hide(this, function() {
            });
            AccessWin.toFront();
        }
    }

    var currentTime = new Date();
    var now = currentTime.getFullYear();
    var years = [];
    var y = new Date().getFullYear()
    while (y > 1990) {
        years.push([y]);
        y--;
    }
    var storeThn = new Ext.data.SimpleStore({
        fields: ['tahun'],
        data: years
    });
    //training
    function displayFormTraining() {
        if (!winTraining.isVisible()) {
            store_training.load({
                params: {
                    cpg_id: Ext.getCmp('CPGId').getValue()
                }
            });
            winTraining.center();
            winTraining.show();
        } else {
            winTraining.hide(this, function() {
            });
            winTraining.toFront();
        }
    }

    var DataFormTraining = Ext.create('Ext.form.Panel', {
        frame: false,
        autoScroll: true,
        height: 350,
        width: '100%',
        bodyPadding: 5,
        id: 'dataFormTraining',
        items: [{
                xtype: 'gridpanel',
                id: 'gtraining',
                style: 'border:1px solid #CCC;',
                store: store_training,
                width: '100%',
                loadMask: true,
                selType: 'rowmodel',
                listeners: {
                    itemdblclick: function(dv, record, item, index, e) {
                        var sm = record;
                        setFormTrainingValue(sm);
                    }
                },
                dockedItems: [{
                        xtype: 'toolbar',
                        items: [{
                                icon: varjs.config.base_url + 'images/icons/new/add.png', cls:'Sfr_BtnGridGreen', overCls:'Sfr_BtnGridGreen-Hover',
                                // cls: m_act_save,
                                hidden: !m_act_add,
                                text: lang('Add'),
                                scope: this,
                                handler: function() {
                                    if (Ext.getCmp('GroupName').getValue() == '')
                                        Ext.MessageBox.alert('Info', lang('Silahkan lengkapi data CPG di atas'));
                                    else if (Ext.getCmp('id').getValue() == '') {
                                        var form = DataForm.getForm();
                                        form.submit({
                                            url: m_crud,
                                            waitMsg: lang('Sending data...'),
                                            success: function(fp, o) {
                                                displayFormWindowParticipant();
                                                Ext.getCmp('CpgTrainingsIDSubTopic').setValue([]);
                                                hideSave();
                                                Ext.getCmp('id').setValue(o.result.id);
                                                Ext.getCmp('idd').setValue(o.result.id);
                                                store.load({
                                                    params: {
                                                        key: Ext.getCmp('key').getValue(),
                                                        // kab: Ext.getCmp('Kab').getValue()
                                                    }
                                                });
                                            }
                                        });
                                    } else {
                                        displayFormWindowParticipant();
                                        Ext.getCmp('CpgTrainingsIDSubTopic').setValue([]);
                                        hideSave();
                                        Ext.getCmp('idd').setValue(Ext.getCmp('id').getValue());
                                    }
                                    store_participant.load();
                                }
                            }, {
                                icon: varjs.config.base_url + 'images/icons/new/update.png',
                                // cls: m_act_save,
                                hidden: !m_act_update,
                                text: lang('Update'),
                                scope: this,
                                handler: function() {
                                    var sm = Ext.getCmp('gtraining').getSelectionModel().getSelection()[0];
                                    setFormTrainingValue(sm);
                                }
                            },
                            {
                                itemId: 'remove',
                                icon: varjs.config.base_url + 'images/icons/new/delete.png',
                                // cls: m_act_save,
                                hidden: !m_act_delete,
                                text: lang('Delete'),
                                scope: this,
                                handler: function() {
                                    var sm = Ext.getCmp('gtraining').getSelectionModel().getSelection()[0];
                                    var id = sm.get('id');
                                    var name = sm.get('label');
                                    console.log(name);
                                    Ext.MessageBox.confirm('Message', lang('Apakah anda mau menghapus Training ini ?'), function(btn) {
                                        if (btn == 'yes') {
                                            Ext.Ajax.request({
                                                waitMsg: lang('Please Wait'),
                                                url: m_training,
                                                method: 'DELETE',
                                                params: {
                                                    id: id
                                                },
                                                success: function(response, opts) {
                                                    console.log(response);
                                                    var obj = Ext.decode(response.responseText);
                                                    console.log(obj);
                                                    switch (obj.success) {
                                                        case true:
                                                            store_training.load({
                                                                params: {
                                                                    cpg_id: sm.get('CPGID')
                                                                }
                                                            });
                                                            break;
                                                        default:
                                                            Ext.MessageBox.alert('Warning', obj.message);
                                                            break;
                                                    }
                                                },
                                                failure: function(response, opts) {
                                                    var obj = Ext.decode(response.responseText);
                                                    console.log(obj);
                                                    Ext.MessageBox.alert('error', lang('Could not connect to the database. Retry later'));
                                                }
                                            });
                                        }
                                    });
                                }
                            }]
                    }],
                columns: [{
                        text: lang('ID'),
                        dataIndex: 'id',
                        hidden: true
                    },
                    {
                        text: lang('No'),
                        xtype: 'rownumberer',
                        width: '5%'
                    },
                    {
                        text: lang('Trainings'),
                        dataIndex: 'CpgTrainings',
                        width: '45%'
                    },
                    {
                        text: lang('Participants'),
                        dataIndex: 'participant',
                        width: '20%'
                    },
                    {
                        text: lang('Start'),
                        dataIndex: 'TrainingStart',
                        width: '15%'
                    },
                    {
                        text: lang('End'),
                        dataIndex: 'TrainingEnd',
                        width: '15%'
                    }]
            }],
        buttons: [{
                text: lang('Close'),
                margin: '5px',
                scale: 'large',
                ui: 's-button',
                cls: 's-grey',
                disabled: false,
                handler: function() {
                    winTraining.hide();
                }
            }]
    });

    var winTraining = Ext.create('widget.window', {
        title: lang('Training'),
        id: 'winTraining',
        closable: true,
        modal: true,
        closeAction: 'show',
        width: '80%',
        height: '90%',
        layout: 'fit',
        items: [DataFormTraining]
    });
    //end training

    //==compost 2
    Ext.define('penjualan.Model', {
        extend: 'Ext.data.Model',
        fields: ['id', 'Buyer', 'Volume', 'Price', 'Total', 'DateTransaction', 'CloneTypeID'],
    });
    var store_compost_penjualan = Ext.create('Ext.data.Store', {
        model: 'penjualan.Model',
        autoLoad: false,
        proxy: {
            type: 'ajax',
            url: m_store_compost_penjualans,
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });
    var cRowEditing = Ext.create('Ext.grid.plugin.RowEditing', {
        id: 'cRowEditing',
        clicksToMoveEditor: 0,
        autoCancel: false,
        errorSummary: false,
        clicksToEdit: 2
    });

    var mc_petani_pic = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'label'],
        autoLoad: false,
        proxy: {
            type: 'ajax',
            url: m_compost + '_petani',
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });

   var mc_clone_type_combo = Ext.create('Ext.data.Store',{
      extend: 'Ext.data.Model',
      fields: ['id', 'label'],
      autoLoad: true,
      proxy: {
         type: 'ajax',
         url: m_clone_ref + '_combo',
         reader: {
            type: 'json',
            root: 'data'
         }
      }
   });

    var mc_pembeli = Ext.create('Ext.data.Store', {
        fields: ['label'],
        data: [
            {'id': 'Anggota Kelompok', 'label': lang('Anggota Kelompok')},
            {'id': 'Petani Lain', 'label': lang('Petani Lain')},
            {'id': 'Traders', 'label': lang('Traders')},
            {'id': 'Lainnya', 'label': lang('Lainnya')},
            {'id': 'Pemerintah', 'label': lang('Pemerintah')},
        ],
    });

    function displayFormCompostPenjualan() {
        if (!winCompostPenjualan.isVisible()) {
            DataFormCompostPenjualan.getForm().reset();
            winCompostPenjualan.center();
            winCompostPenjualan.show();
        } else {
            winCompostPenjualan.hide(this, function() {
            });
            winCompostPenjualan.toFront();
        }
    }

    var DataFormCompostPenjualan = Ext.create('Ext.form.Panel', {
        autoScroll: true,
        bodyPadding: 5,
        id: 'dataFormCompostPenjualan',
        fieldDefaults: {
            labelAlign: 'left',
            labelWidth: 120,
            anchor: '95%'
        },
        items: [{
                layout: 'column',
                border: false,
                items: [{
                        columnWidth: .5,
                        layout: 'form',
                        border: false,
                        padding: 5,
                        items: [{
                                xtype: 'textfield',
                                id: 'CompostID',
                                name: 'CompostID',
                                hidden: true
                            }, {
                                xtype: 'textfield',
                                id: 'id_obj',
                                name: 'id_obj',
                                hidden: true
                            }, {
                                xtype: 'textfield',
                                id: 'type_obj',
                                name: 'type_obj',
                                hidden: true,
                                value: 'cpg'
                            }, {
                                xtype: 'textfield',
                                fieldLabel: lang('Nama Farmer Group'),
                                id: 'cGroupName',
                                name: 'cGroupName',
                                readOnly: true
                            }, {
                                xtype: 'datefield',
                                fieldLabel: lang('Tanggal Berdiri'),
                                id: 'Established',
                                name: 'Established',
                                format: 'Y-m-d'
                            },{
                                xtype: 'textfield',
                                fieldLabel: lang('Latitude'),
                                id: 'CompostLatitude',
                                name: 'CompostLatitude',
                                readOnly: m_hakakses_lat_short
                            }, {
                                xtype: 'textfield',
                                fieldLabel: lang('Longitude'),
                                id: 'CompostLongitude',
                                name: 'CompostLongitude',
                                readOnly: m_hakakses_long_short
                            }]
                    }, {
                        columnWidth: .5,
                        layout: 'form',
                        border: false,
                        padding: 5,
                        items: [{
                                xtype: 'radiogroup',
                                fieldLabel: lang('Mesin Chooper'),
                                items: [{
                                        name: 'MesinChooper',
                                        id: 'MesinChooper',
                                        boxLabel: lang('Ya'),
                                        inputValue: '1'
                                    }, {
                                        name: 'MesinChooper',
                                        id: 'MesinChooper2',
                                        boxLabel: lang('Tidak'),
                                        inputValue: '2'
                                    }]
                            }, {
                                xtype: 'radiogroup',
                                fieldLabel: lang('Rumah Kompos'),
                                items: [{
                                        name: 'RumahKompos',
                                        id: 'RumahKompos',
                                        boxLabel: lang('Ya'),
                                        inputValue: '1'
                                    }, {
                                        name: 'RumahKompos',
                                        id: 'RumahKompos2',
                                        boxLabel: lang('Tidak'),
                                        inputValue: '2'
                                    }]
                            }]
                    }]
            }, {
                xtype: 'gridpanel',
                id: 'gcompostpenjualan',
                style: 'border:1px solid #CCC;',
                store: store_compost_penjualan,
                width: '100%',
                loadMask: true,
                selType: 'rowmodel',
                dockedItems: [{
                        xtype: 'toolbar',
                        items: [{
                                icon: varjs.config.base_url + 'images/icons/new/add.png', cls:'Sfr_BtnGridGreen', overCls:'Sfr_BtnGridGreen-Hover',
                                // cls: m_act_save,
                                hidden: !m_act_add,
                                text: lang('Add'),
                                scope: this,
                                handler: function() {
                                    cRowEditing.cancelEdit();
                                    var r = Ext.create('penjualan.Model', {
                                        id: '', Buyer: '', Volume: '', Price: '', Total: '', DateTransaction: ''
                                    });
                                    store_compost_penjualan.insert(0, r);
                                    cRowEditing.startEdit(0, 0);
                                }
                            }, {
                                icon: varjs.config.base_url + 'images/icons/new/update.png',
                                // cls: m_act_save,
                                hidden: !m_act_update,
                                text: lang('Update'),
                                scope: this,
                                handler: function() {
                                    cRowEditing.cancelEdit();
                                    var sm = Ext.getCmp('gcompostpenjualan').getSelectionModel().getSelection();
                                    cRowEditing.startEdit(sm[0].index, 0);
                                }
                            }, {
                                itemId: 'remove',
                                icon: varjs.config.base_url + 'images/icons/new/delete.png',
                                // cls: m_act_save,
                                hidden: !m_act_delete,
                                text: lang('Delete'),
                                scope: this,
                                handler: function() {
                                    var smb = Ext.getCmp('gcompostpenjualan').getSelectionModel().getSelection()[0];
                                    cRowEditing.cancelEdit();
                                    Ext.MessageBox.confirm('Message', lang('Apakah anda mau menghapus data ini ?'), function(btn) {
                                        if (btn == 'yes') {
                                            Ext.Ajax.request({
                                                waitMsg: lang('Please Wait'),
                                                url: m_compost + '_penjualan',
                                                method: 'DELETE',
                                                params: {
                                                    id: smb.raw.id
                                                },
                                                success: function(response, opts) {
                                                    var obj = Ext.decode(response.responseText);
                                                    switch (obj.success) {
                                                        case true:
                                                            store_compost_penjualan.load({
                                                                params: {
                                                                    compost_id: Ext.getCmp('CompostID').getValue()
                                                                }
                                                            });
                                                            break;
                                                        default:
                                                            Ext.MessageBox.alert('Warning', obj.message);
                                                            break;
                                                    }
                                                },
                                                failure: function(response, opts) {
                                                    var obj = Ext.decode(response.responseText);
                                                    Ext.MessageBox.alert('error', lang('Could not connect to the database. Retry later'));
                                                }
                                            });
                                        }
                                    });
                                }
                            }]
                    }],
                columns: [{
                        text: lang('ID'),
                        dataIndex: 'id',
                        hidden: true
                    }, {
                        text: lang('No'),
                        xtype: 'rownumberer',
                        width: '5%'
                    }, {
                        text: lang('Pembeli'),
                        dataIndex: 'Buyer',
                        width: '25%',
                        editor: {
                            xtype: 'combo',
                            store: mc_pembeli,
                            displayField: 'label',
                            valueField: 'id',
                            queryMode: 'local',
                            allowBlank: false
                        }
                    }, {
                        text: lang('Volume'),
                        dataIndex: 'Volume',
                        width: '10%',
                        editor: {
                            xtype: 'textfield',
                            id: 'cvol',
                            allowBlank: false,
                            listeners: {
                                change: function() {
                                    Ext.getCmp('ctot').setValue(Ext.getCmp('cvol').getValue() * Ext.getCmp('cpri').getValue());
                                }
                            }
                        }
                    }, {
                        text: lang('Harga Satuan'),
                        dataIndex: 'Price',
                        width: '15%',
                        editor: {
                            xtype: 'textfield',
                            id: 'cpri',
                            allowBlank: false,
                            listeners: {
                                change: function() {
                                    //Ext.getCmp('cpri').setValue(nnumber_format(Ext.getCmp('cpri').getValue()));
                                    Ext.getCmp('ctot').setValue(nnumber_format(Ext.getCmp('cvol').getValue() * Ext.getCmp('cpri').getValue()));
                                }
                            }
                        }
                    }, {
                        text: lang('Total Harga'),
                        dataIndex: 'Total',
                        width: '15%',
                        editor: {
                            xtype: 'textfield',
                            allowBlank: false,
                            id: 'ctot',
                            readOnly: true
                        }
                    }, {
                        text: lang('Tanggal Penjualan'),
                        dataIndex: 'DateTransaction',
                        format: 'Y-m-d',
                        width: '28%',
                        editor: {
                            xtype: 'datefield',
                            format: 'Y-m-d',
                            allowBlank: false
                        }
                    }],
                plugins: [cRowEditing],
                listeners: {
                    itemdblclick: function(dv, record, item, index, e) {
                        if (!m_act_update) {
                            cRowEditing.cancelEdit();
                        }
                    },
                    'canceledit': function(editor, e, eOpts) {
                        store_compost_penjualan.load({
                            params: {
                                compost_id: Ext.getCmp('CompostID').getValue()
                            }
                        });
                    },
                    'edit': function(editor, e) {
                        if (e.record.data.id == '') {
                            Ext.Ajax.request({
                                waitMsg: lang('Please wait...'),
                                url: m_compost + '_penjualan',
                                method: 'POST',
                                params: {
                                    id_compost: Ext.getCmp('CompostID').getValue(),
                                    Buyer: e.record.data.Buyer,
                                    Volume: e.record.data.Volume,
                                    Price: e.record.data.Price,
                                    Total: e.record.data.Totel,
                                    DateTransaction: e.record.data.DateTransaction
                                },
                                success: function(response, opts) {
                                    var obj = Ext.decode(response.responseText);
                                    switch (obj.success) {
                                        case true:
                                            Ext.MessageBox.alert('Success', obj.message);
                                            store_compost_penjualan.load({
                                                params: {
                                                    compost_id: Ext.getCmp('CompostID').getValue()
                                                }
                                            });
                                            break;
                                        default:
                                            Ext.MessageBox.alert('Warning', obj.message);
                                            break;
                                    }
                                },
                                failure: function(response, opts) {
                                    var obj = Ext.decode(response.responseText);
                                    Ext.MessageBox.alert('error', lang('Could not connect to the database. Retry later'));
                                }
                            });
                        } else {
                            Ext.MessageBox.confirm('Message', lang('Update data ini ?'), function(btn) {
                                if (btn == 'yes') {
                                    Ext.Ajax.request({
                                        waitMsg: lang('Please wait...'),
                                        url: m_compost + '_penjualan',
                                        method: 'PUT',
                                        params: {
                                            id: e.record.data.id,
                                            id_compost: Ext.getCmp('CompostID').getValue(),
                                            Buyer: e.record.data.Buyer,
                                            Volume: e.record.data.Volume,
                                            Price: e.record.data.Price,
                                            Total: e.record.data.Totel,
                                            DateTransaction: e.record.data.DateTransaction
                                        },
                                        success: function(response, opts) {
                                            var obj = Ext.decode(response.responseText);
                                            switch (obj.success) {
                                                case true:
                                                    Ext.MessageBox.alert('Success', obj.message);
                                                    store_compost_penjualan.load({
                                                        params: {
                                                            compost_id: Ext.getCmp('CompostID').getValue()
                                                        }
                                                    });
                                                    break;
                                                default:
                                                    Ext.MessageBox.alert('Warning', obj.message);
                                                    break;
                                            }
                                        },
                                        failure: function(response, opts) {
                                            var obj = Ext.decode(response.responseText);
                                            Ext.MessageBox.alert('error', lang('Could not connect to the database. Retry later'));
                                        }
                                    });
                                }
                            });
                        }
                    }
                }
            }],
        buttons: [{
                id: 'csaveButton',
                text: lang('Save'),
                margin: '5px',
                scale: 'large',
                ui: 's-button',
                cls: 's-blue ',
                handler: function() {
                    var form = this.up('form').getForm();
                    var methode;
                    if (Ext.getCmp('CompostID').getValue() != '')
                        methode = 'PUT';
                    else
                        methode = 'POST';
                    form.submit({
                        url: m_compost,
                        method: methode,
                        waitMsg: lang('Sending data...'),
                        param:{type_obj:'cpg'},
                        success: function(fp, o) {
                            Ext.MessageBox.alert('Success', lang('Data saved.'));
                            Ext.getCmp('CompostID').setValue(o.result.id);
                            Ext.getCmp('gcompostpenjualan').setDisabled(false);
                        }
                    });
                }
            }, {
                text: lang('Close'),
                margin: '5px',
                scale: 'large',
                ui: 's-button',
                cls: 's-grey',
                disabled: false,
                handler: function() {
                    winCompostPenjualan.hide();
                }
            }]
    });
    var winCompostPenjualan = Ext.create('widget.window', {
        title: lang('Farmer Group Compost Unit'),
        id: 'winCompostPenjualan',
        closable: true,
        modal: true,
        closeAction: 'show',
        width: '80%',
        height: '90%',
        layout: {
            type: 'fit'
        },
        items: [DataFormCompostPenjualan]
    });
    //==end compos 2

    //======================= nursery ================================================================
    // combobox status monitoring action
    function act_nursery_status(val) {
        if (val != 'Tidak Berjalan') {
            Ext.getCmp('mDescription').allowBlank = true;
            Ext.getCmp('mDescription').getStore().loadData(['']);
        } else {
            Ext.getCmp('mDescription').allowBlank = false;
            Ext.getCmp('mDescription').getStore().loadData([
                [lang('Masalah air/Penyakit')],
                [lang('Rusak')],
                [lang('Tidak ada pemeliharaan/Konflik anggota kelompok')],
                [lang('Tidak ada pasar penjualan')]
            ]);
        }
    }

    var nRowEditing = Ext.create('Ext.grid.plugin.RowEditing', {
        id: 'nRowEditing',
        clicksToMoveEditor: 0,
        autoCancel: false,
        errorSummary: false,
        clicksToEdit: 2
    });

    var mRowEditing = Ext.create('Ext.grid.plugin.RowEditing', {
        id: 'nRowEditing',
        clicksToMoveEditor: 0,
        autoCancel: false,
        errorSummary: false,
        clicksToEdit: 2
    });

    var store_nursey = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'FarmerPIC', 'Volume', 'DateStarted'],
        proxy: {
            type: 'ajax',
            url: m_store_nurseys,
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });
    var store_nursey_penjualan = Ext.create('Ext.data.Store', {
        model: 'penjualan.Model',
        autoLoad: false,
        proxy: {
            type: 'ajax',
            url: m_store_nursey_penjualans,
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });

    // store nursey monitoring
    // model monitoring
    Ext.define('monitoring.Model', {
        extend: 'Ext.data.Model',
        fields: ['id', 'MonitoringDate', 'MonitoringStatus', 'Description'],
    });
    // store nursery monitoring
    var store_nursey_monitoring = Ext.create('Ext.data.Store', {
        model: 'monitoring.Model',
        autoLoad: false,
        proxy: {
            type: 'ajax',
            url: m_store_nursey_monitorings,
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });
    // store combobox monitoring
    var mc_status_monitoring = Ext.create('Ext.data.Store', {
        fields: ['label'],
        data: [
            {'label': lang('Sedang di bangun/Belum selesai')},
            {'label': lang('Berjalan/Produktif')},
            {'label': lang('Tidak Berjalan')}
        ]
    });

    //demoplot
    var store_demoplot = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'CPGid', 'CpgBatchTrainingID', 'CpgBatchTrainingName', 'TrainingDate', 'TrainingDateVal', 'ObjType', 'OwnerLabel'],
        autoLoad: false,
        pageSize: 10,
//        sorters: [{
//                property: 'TrainingDate',
//                direction: 'DESC'
//            }],
        proxy: {
            type: 'ajax',
            url: m_demoplot + 's',
            reader: {
                type: 'json',
                root: 'data',
                totalProperty: 'total'
            }
        }
    });

    function displayFormDemoplot() {
        if (!winDemoplot.isVisible()) {
            store_demoplot.load({
                params: {
                    cpg_id: Ext.getCmp('CPGId').getValue()
                }
            });
            winDemoplot.center();
            winDemoplot.show();
        } else {
            winDemoplot.hide(this, function() {
            });
            winDemoplot.toFront();
        }
    }

    function setFormDemoplot(r){
        Ext.getCmp('demoplot_id').setValue(r.id);
        Ext.getCmp('demoplot_cpg_id').setValue(r.CPGid);
        Ext.getCmp('demoplot_batch_training_id').setValue(r.CpgBatchTrainingID);
        Ext.getCmp('demoplot_training_date').setValue(r.TrainingDate);
        Ext.getCmp('demoplot_comment').setValue(r.Comment);

        if(r.ObjType == 'cpg'){
            Ext.getCmp('demoplot_ObjType_cpg').setValue(true);

            var cpgOwnerLabel = Ext.getCmp('CPGId').getValue()+' - '+Ext.getCmp('GroupName').getValue();
            Ext.getCmp('fsCpgOwner').setVisible(true);
            Ext.getCmp('fsFarmerOwner').setVisible(false);
            Ext.getCmp('demoplot_cpg_owner_label').setValue(cpgOwnerLabel);

            Ext.getCmp('demoplot_cpg_owner_panjang').setValue(r.KebunPanjang);
            Ext.getCmp('demoplot_cpg_owner_lebar').setValue(r.KebunLebar);
        }

        if(r.ObjType == 'farmer'){
            Ext.getCmp('demoplot_ObjType_farmer').setValue(true);

            Ext.getCmp('demoplot_farmer_owner_id').setValue(r.ObjID);
            mc_garden_number.load({params: {demoplot_owner_id: r.ObjID},callback: function(records, operation, success) {
                Ext.getCmp('demoplot_farmer_garden_number').setValue(r.GardenNr);
            }});
            Ext.getCmp('demoplot_farmer_garden_ha').setValue(r.GardenHa);
        }

        if(r.KbBayam == "1"){
            Ext.getCmp('demoplot_KbBayam').setValue(true);
        }
        if(r.KbTomat == "1"){
            Ext.getCmp('demoplot_KbTomat').setValue(true);
        }
        if(r.KbKangkung == "1"){
            Ext.getCmp('demoplot_KbKangkung').setValue(true);
        }
        if(r.KbKelor == "1"){
            Ext.getCmp('demoplot_KbKelor').setValue(true);
        }
        if(r.KbKacangPanjang == "1"){
            Ext.getCmp('demoplot_KbKacangPanjang').setValue(true);
        }
        if(r.KbUbi == "1"){
            Ext.getCmp('demoplot_KbSingkong').setValue(true);
        }
        if(r.KbCabe == "1"){
            Ext.getCmp('demoplot_KbCabai').setValue(true);
        }
        if(r.KbLabu == "1"){
            Ext.getCmp('demoplot_KbLabu').setValue(true);
        }
        if(r.KbTerong == "1"){
            Ext.getCmp('demoplot_KbTerong').setValue(true);
        }
        if(r.KbKatuk == "1"){
            Ext.getCmp('demoplot_KbKatuk').setValue(true);
        }
        if(r.KbSawi == "1"){
            Ext.getCmp('demoplot_KbSawi').setValue(true);
        }

        switch(r.HaveFishPond){
            case '1':
                Ext.getCmp('demoplot_HaveFishPond_1').setValue(true);
            break;
            case '2':
                Ext.getCmp('demoplot_HaveFishPond_2').setValue(true);
            break;
        }

        Ext.getCmp('demoplot_FpPanjang').setValue(r.FpPanjang);
        Ext.getCmp('demoplot_FpLebar').setValue(r.FpLebar);

        if(r.FpNila == "1"){
            Ext.getCmp('demoplot_FpNila').setValue(true);
        }
        if(r.FpIkanMas == "1"){
            Ext.getCmp('demoplot_FpCarp').setValue(true);
        }
        if(r.FpLele == "1"){
            Ext.getCmp('demoplot_FpCatfish').setValue(true);
        }
        if(r.FpMujair == "1"){
            Ext.getCmp('demoplot_FpTilapia').setValue(true);
        }
        if(r.FpLainnya == "1"){
            Ext.getCmp('demoplot_FpOthers').setValue(true);
        }
    }

    var DataFormDemoplot = Ext.create('Ext.form.Panel', {
        frame: false,
        autoScroll: true,
        height: 350,
        width: '100%',
        bodyPadding: 5,
        id: 'dataFormDemoplot',
        items: [{
                xtype: 'gridpanel',
                id: 'gdemoplot',
                style: 'border:1px solid #CCC;',
                store: store_demoplot,
                width: '100%',
                loadMask: true,
                selType: 'rowmodel',
                listeners: {
                    itemdblclick: function(dv, record, item, index, e) {
                        var sm = record;
                        if (sm.get('id') != undefined) {
                            displayFormWindowDemoplot(Ext.getCmp('id').getValue());
                            hideSave();
                            var id = sm.get('id');
                            Ext.Ajax.request({
                                url: m_demoplot,
                                method: 'GET',
                                params: {id: sm.get('id')},
                                success: function(fp, o) {
                                    var r = Ext.decode(fp.responseText);
                                    setFormDemoplot(r);
                                    /*
                                    Ext.getCmp('demoplot_id').setValue(sm.get('id'));
                                    Ext.getCmp('demoplot_cpg_id').setValue(r.CPGid);
                                    Ext.getCmp('demoplot_batch_training_id').setValue(r.CpgBatchTrainingID);
                                    Ext.getCmp('demoplot_training_date').setValue(r.TrainingDate);
                                    Ext.getCmp('demoplot_owner_id').setValue(r.DemoplotOwnerID);
                                    Ext.getCmp('demoplot_garden_number').setValue(r.GardenNr);
                                    Ext.getCmp('demoplot_comment').setValue(r.Comment);
                                    */
                                }
                            });
                        }
                    }
                },
                dockedItems: [{
                        xtype: 'toolbar',
                        items: [{
                                icon: varjs.config.base_url + 'images/icons/new/add.png', cls:'Sfr_BtnGridGreen', overCls:'Sfr_BtnGridGreen-Hover',
                                // cls: m_act_save,
                                hidden: !m_act_add,
                                text: lang('Add'),
                                scope: this,
                                handler: function() {
                                    displayFormWindowDemoplot(Ext.getCmp('id').getValue());
                                    hideSave();
                                    Ext.getCmp('demoplot_cpg_id').setValue(Ext.getCmp('id').getValue());
                                    Ext.getCmp('demoplot_HaveFishPond_2').setValue(true);
                                }
                            }, {
                                icon: varjs.config.base_url + 'images/icons/new/update.png',
                                // cls: m_act_save,
                                hidden: !m_act_update,
                                text: lang('Update'),
                                scope: this,
                                handler: function() {
                                    var sm = Ext.getCmp('gdemoplot').getSelectionModel().getSelection()[0];
                                    if (sm.get('id') != undefined) {
                                        displayFormWindowDemoplot(Ext.getCmp('id').getValue());
                                        hideSave();
//                            Ext.getCmp('demoplot_cpg_id').setValue(Ext.getCmp('id').getValue());
                                        var id = sm.get('id');
                                        Ext.Ajax.request({
                                            url: m_demoplot,
                                            method: 'GET',
                                            params: {id: sm.get('id')},
                                            success: function(fp, o) {
                                                var r = Ext.decode(fp.responseText);
                                                setFormDemoplot(r);
                                                /*
                                                Ext.getCmp('demoplot_id').setValue(sm.get('id'));
                                                Ext.getCmp('demoplot_cpg_id').setValue(r.CPGid);
                                                Ext.getCmp('demoplot_batch_training_id').setValue(r.CpgBatchTrainingID);
                                                Ext.getCmp('demoplot_training_date').setValue(r.TrainingDate);
                                                Ext.getCmp('demoplot_owner_id').setValue(r.DemoplotOwnerID);
                                                Ext.getCmp('demoplot_garden_number').setValue(r.GardenNr);
                                                Ext.getCmp('demoplot_comment').setValue(r.Comment);
                                                */
                                            }
                                        });
                                    }
                                }
                            },
                            {
                                itemId: 'remove',
                                icon: varjs.config.base_url + 'images/icons/new/delete.png',
                                // cls: m_act_save,
                                hidden: !m_act_delete,
                                text: lang('Delete'),
                                scope: this,
                                handler: function() {
                                    var sm = Ext.getCmp('gdemoplot').getSelectionModel().getSelection()[0];
                                    var id = sm.get('id');
                                    var name = sm.get('label');
                                    console.log(name);
                                    Ext.MessageBox.confirm('Message', lang('Apakah anda mau menghapus Demoplot ini ?'), function(btn) {
                                        if (btn == 'yes') {
                                            Ext.Ajax.request({
                                                waitMsg: lang('Please Wait'),
                                                url: m_demoplot,
                                                method: 'DELETE',
                                                params: {
                                                    id: id
                                                },
                                                success: function(response, opts) {
                                                    console.log(response);
                                                    var obj = Ext.decode(response.responseText);
                                                    console.log(obj);
                                                    switch (obj.success) {
                                                        case true:
                                                            store_demoplot.load({
                                                                params: {
                                                                    cpg_id: sm.get('CPGid')
                                                                }
                                                            });
                                                            break;
                                                        default:
                                                            Ext.MessageBox.alert('Warning', obj.message);
                                                            break;
                                                    }
                                                },
                                                failure: function(response, opts) {
                                                    var obj = Ext.decode(response.responseText);
                                                    console.log(obj);
                                                    Ext.MessageBox.alert('error', lang('Could not connect to the database. Retry later'));
                                                }
                                            });
                                        }
                                    });
                                }
                            }]
                    }],
                columns: [{
                        text: lang('ID'),
                        dataIndex: 'id',
                        hidden: true
                    }, {
                        text: 'No',
                        xtype: 'rownumberer',
                        width: '5%'
                    }, {
                        text: lang('Batch Training'),
                        dataIndex: 'CpgBatchTrainingName',
                        width: '30%'
                    }, {
                        text: lang('Training Date'),
                        dataIndex: 'TrainingDateVal',
                        width: '20%'
                    }, {
                        text: lang('Demoplot Type'),
                        dataIndex: 'ObjType',
                        width: '20%'
                    },{
                        text: lang('Demoplot Owner'),
                        dataIndex: 'OwnerLabel',
                        width: '25%'
                    }]
            }],
        buttons: [{
                text: lang('Close'),
                margin: '5px',
                scale: 'large',
                ui: 's-button',
                cls: 's-grey',
                disabled: false,
                handler: function() {
                    winDemoplot.hide();
                }
            }]
    });

    var winDemoplot = Ext.create('widget.window', {
        title: lang('Demoplot'),
        id: 'winDemoplot',
        closable: true,
        modal: true,
        closeAction: 'show',
        width: '80%',
        height: '90%',
        layout: 'fit',
        items: [DataFormDemoplot]
    });


    ////////////////////////////////////////////////////////////////////////////
    // Form Demoplot

    function resetFormWindowDemoplot() {
        Ext.getCmp('demoplot_id').setValue('');
        Ext.getCmp('demoplot_cpg_id').setValue('');
        Ext.getCmp('demoplot_batch_training_id').setValue('');
        Ext.getCmp('demoplot_training_date').setValue('');

        /*
        Ext.getCmp('demoplot_owner_id').setValue('');
        Ext.getCmp('demoplot_garden_number').setValue('');
        */
        Ext.getCmp('demoplot_comment').setValue('');
    }

    function displayFormWindowDemoplot($cpg_id) {
//        console.log($cpg_id);
        mc_batch_training.load({
            params: {cpg_id: $cpg_id}
        });
        mc_demoplot_owner.load({
            params: {cpg_id: $cpg_id}
        });
        if (!winFormDemoplot.isVisible()) {
            resetFormWindowDemoplot();
            winFormDemoplot.center();
            winFormDemoplot.show();
            //Ext.getCmp('name').focus(true,true);
        } else {
            winFormDemoplot.hide(this, function() {
            });
            winFormDemoplot.toFront();
        }
    }

    var mc_batch_training = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'label'],
        autoLoad: true,
        pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_batch_training,
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });

    var mc_demoplot_owner = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'label'],
        autoLoad: true,
        pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_demoplot_owner,
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });

    var mc_garden_number = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'label'],
        autoLoad: false,
        pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_garden_number,
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });

    var DataFormWindowDemoplot = Ext.create('Ext.form.Panel', {
        autoScroll: true,
        width: 500,
        id: 'dataFormWindowDemoplot',
        style: 'padding:7px;',
        scrollOffset: 20,
        fieldDefaults: {
            labelAlign: 'left',
            labelWidth: 230,
            anchor: '98%'
        },
        items: [
            {
                xtype: 'textfield',
                id: 'demoplot_id',
                name: 'demoplot_id',
                inputType: 'hidden'
            },
            {
                xtype: 'textfield',
                fieldLabel: 'Farmer Group ID',
                id: 'demoplot_cpg_id',
                name: 'demoplot_cpg_id',
                readOnly: true
            },
            {
                id: 'demoplot_batch_training_id',
                name: 'demoplot_batch_training_id',
                xtype: 'combo',
                emptyText: '-- Batch Training --',
                fieldLabel: 'Batch Training',
                multiSelect: false,
                store: mc_batch_training,
                displayField: 'label',
                valueField: 'id',
                queryMode: 'local'
            },
            {
                xtype: 'datefield',
                fieldLabel: 'Training Date',
                format: 'd M Y',
                submitFormat: 'Y-m-d',
                id: 'demoplot_training_date',
                name: 'demoplot_training_date'
            },
            {
                fieldLabel: lang('Demoplot Owner Type'),
                xtype: 'radiogroup',
                allowBlank: false,
                msgTarget: 'side',
                width: '100%',
                columns: 2,
                items: [{
                    boxLabel: lang('CPG'),
                    name: 'demoplot_ObjType',
                    inputValue: 'cpg',
                    id: 'demoplot_ObjType_cpg',
                    listeners:{
                        change: function(){
                            if(this.checked == true){
                                var cpgOwnerLabel = Ext.getCmp('CPGId').getValue()+' - '+Ext.getCmp('GroupName').getValue();
                                Ext.getCmp('fsCpgOwner').setVisible(true);
                                Ext.getCmp('fsFarmerOwner').setVisible(false);
                                Ext.getCmp('demoplot_cpg_owner_label').setValue(cpgOwnerLabel);
                            }

                            return false;
                        }
                    }
                }, {
                    boxLabel: lang('Farmer'),
                    name: 'demoplot_ObjType',
                    inputValue: 'farmer',
                    id: 'demoplot_ObjType_farmer',
                    listeners:{
                        change: function(){
                            if(this.checked == true){
                                Ext.getCmp('fsCpgOwner').setVisible(false);
                                Ext.getCmp('fsFarmerOwner').setVisible(true);
                            }

                            return false;
                        }
                    }
                }]
            },
            {
                xtype:'fieldset',
                style:'margin:5px 13px 5px 5px;',
                title: lang('CPG Owner'),
                id: 'fsCpgOwner',
                hidden:true,
                layout: 'form',
                fieldDefaults: {
                    labelAlign: 'left',
                    labelWidth: 230,
                    anchor: '96%'
                },
                items :[{
                    xtype: 'textfield',
                    fieldLabel: lang('Owner'),
                    id: 'demoplot_cpg_owner_label',
                    name: 'demoplot_cpg_owner_label',
                    readOnly: true
                },{
                    xtype: 'numericfield',
                    fieldLabel: lang('Panjang'),
                    id: 'demoplot_cpg_owner_panjang',
                    name: 'demoplot_cpg_owner_panjang',
                    listeners:{
                        change: function(){
                            var luasnya = Ext.getCmp('demoplot_cpg_owner_panjang').getValue() * Ext.getCmp('demoplot_cpg_owner_lebar').getValue();
                            Ext.getCmp('demoplot_cpg_owner_area').setValue(luasnya);
                        }
                    }
                },{
                    xtype: 'numericfield',
                    fieldLabel: lang('Lebar'),
                    id: 'demoplot_cpg_owner_lebar',
                    name: 'demoplot_cpg_owner_lebar',
                    listeners:{
                        change: function(){
                            var luasnya = Ext.getCmp('demoplot_cpg_owner_panjang').getValue() * Ext.getCmp('demoplot_cpg_owner_lebar').getValue();
                            Ext.getCmp('demoplot_cpg_owner_area').setValue(luasnya);
                        }
                    }
                },{
                    xtype: 'numericfield',
                    fieldLabel: lang('Area (m2)'),
                    id: 'demoplot_cpg_owner_area',
                    name: 'demoplot_cpg_owner_area',
                    readOnly: true
                }]
            },
            {
                xtype:'fieldset',
                id: 'fsFarmerOwner',
                hidden:true,
                title: lang('Farmer Owner'),
                layout: 'form',
                style:'margin:5px 13px 5px 5px;',
                fieldDefaults: {
                    labelAlign: 'left',
                    labelWidth: 230,
                    anchor: '96%'
                },
                items :[{
                    id: 'demoplot_farmer_owner_id',
                    name: 'demoplot_farmer_owner_id',
                    xtype: 'combo',
                    emptyText: lang('Owner'),
                    fieldLabel: lang('Owner'),
                    multiSelect: false,
                    store: mc_demoplot_owner,
                    displayField: 'label',
                    valueField: 'id',
                    queryMode: 'local',
                    listeners: {
                        change: function(cb, nv, ov) {
                            mc_garden_number.load({params: {demoplot_owner_id: Ext.getCmp('demoplot_farmer_owner_id').getValue()}});
                        }
                    }
                },{
                    id: 'demoplot_farmer_garden_number',
                    name: 'demoplot_farmer_garden_number',
                    xtype: 'combo',
                    emptyText: lang('GardenNr'),
                    fieldLabel: lang('GardenNr'),
                    multiSelect: false,
                    store: mc_garden_number,
                    displayField: 'label',
                    valueField: 'id',
                    queryMode: 'local',
                    listeners: {
                        change: function(cb, nv, ov) {
                            Ext.Ajax.request({
                                waitMsg: lang('Please Wait'),
                                url: m_api + '/cpg/demoplot_farmer_garden_detail',
                                method : 'GET',
                                params: {GardenNr:  Ext.getCmp('demoplot_farmer_garden_number').getValue(),FarmerID: Ext.getCmp('demoplot_farmer_owner_id').getValue()},
                                success: function(response, opts){
                                    var obj = Ext.decode(response.responseText);
                                    Ext.getCmp('demoplot_farmer_garden_ha').setValue(obj.GardenHa);
                                }
                            });
                        }
                    }
                },{
                    xtype: 'numericfield',
                    fieldLabel: lang('Garden Ha'),
                    id: 'demoplot_farmer_garden_ha',
                    name: 'demoplot_farmer_garden_ha',
                    readOnly: true
                }]
            },
            {
                layout: 'column',
                border: false,
                items: [{
                    columnWidth: 0.25,
                    xtype: 'label',
                    cls: 'x-form-item-label',
                    text: lang('Grown vegetables :')
                },{
                    columnWidth: 0.4,
                    border: false,
                    defaultType: 'checkboxfield',
                    items:[{
                        boxLabel: lang('Spinach'),
                        name: 'demoplot_KbBayam',
                        inputValue: '1',
                        id: 'demoplot_KbBayam'
                    },{
                        boxLabel: lang('Tomato'),
                        name: 'demoplot_KbTomat',
                        inputValue: '1',
                        id: 'demoplot_KbTomat'
                    },{
                        boxLabel: lang('Kangkung'),
                        name: 'demoplot_KbKangkung',
                        inputValue: '1',
                        id: 'demoplot_KbKangkung'
                    },{
                        boxLabel: lang('Moringa'),
                        name: 'demoplot_KbKelor',
                        inputValue: '1',
                        id: 'demoplot_KbKelor'
                    },{
                        boxLabel: lang('Longbeans'),
                        name: 'demoplot_KbKacangPanjang',
                        inputValue: '1',
                        id: 'demoplot_KbKacangPanjang'
                    },{
                        boxLabel: lang('Cassava'),
                        name: 'demoplot_KbSingkong',
                        inputValue: '1',
                        id: 'demoplot_KbSingkong'
                    }]
                },{
                    columnWidth: 0.35,
                    border: false,
                    defaultType: 'checkboxfield',
                    items:[{
                        boxLabel: lang('Chili'),
                        name: 'demoplot_KbCabai',
                        inputValue: '1',
                        id: 'demoplot_KbCabai'
                    },{
                        boxLabel: lang('Pumpkin'),
                        name: 'demoplot_KbLabu',
                        inputValue: '1',
                        id: 'demoplot_KbLabu'
                    },{
                        boxLabel: lang('Eggplant'),
                        name: 'demoplot_KbTerong',
                        inputValue: '1',
                        id: 'demoplot_KbTerong'
                    },{
                        boxLabel: lang('Katuk'),
                        name: 'demoplot_KbKatuk',
                        inputValue: '1',
                        id: 'demoplot_KbKatuk'
                    },{
                        boxLabel: lang('Sawi'),
                        name: 'demoplot_KbSawi',
                        inputValue: '1',
                        id: 'demoplot_KbSawi'
                    }]
                }]
            },{
                fieldLabel: lang('Do you have a fish pond ?'),
                xtype: 'radiogroup',
                width: '100%',
                columns: 2,
                items: [{
                    boxLabel: lang('Ya'),
                    name: 'demoplot_HaveFishPond',
                    inputValue: '1',
                    id: 'demoplot_HaveFishPond_1',
                    listeners:{
                        change: function(){
                            if(this.checked == true){
                                Ext.getCmp('demoplot_FpPanjang').setDisabled(false);
                                Ext.getCmp('demoplot_FpLebar').setDisabled(false);
                                Ext.getCmp('demoplot_FpArea').setDisabled(false);
                                Ext.getCmp('demoplot_FpNila').setDisabled(false);
                                Ext.getCmp('demoplot_FpCarp').setDisabled(false);
                                Ext.getCmp('demoplot_FpCatfish').setDisabled(false);
                                Ext.getCmp('demoplot_FpTilapia').setDisabled(false);
                                Ext.getCmp('demoplot_FpOthers').setDisabled(false);
                            }
                            return false;
                        }
                    }
                }, {
                    boxLabel: lang('Tidak'),
                    name: 'demoplot_HaveFishPond',
                    inputValue: '2',
                    id: 'demoplot_HaveFishPond_2',
                    listeners:{
                        change: function(){
                            if(this.checked == true){
                                Ext.getCmp('demoplot_FpPanjang').setDisabled(true);
                                Ext.getCmp('demoplot_FpLebar').setDisabled(true);
                                Ext.getCmp('demoplot_FpArea').setDisabled(true);
                                Ext.getCmp('demoplot_FpNila').setDisabled(true);
                                Ext.getCmp('demoplot_FpCarp').setDisabled(true);
                                Ext.getCmp('demoplot_FpCatfish').setDisabled(true);
                                Ext.getCmp('demoplot_FpTilapia').setDisabled(true);
                                Ext.getCmp('demoplot_FpOthers').setDisabled(true);
                            }
                            return false;
                        }
                    }
                }]
            },{
                columnWidth: 1,
                xtype: 'label',
                cls: 'x-form-item-label',
                text: lang('Size of fish pond :')
            },{
                xtype: 'numericfield',
                fieldLabel: lang('Panjang'),
                id: 'demoplot_FpPanjang',
                name: 'demoplot_FpPanjang',
                listeners:{
                    change: function(){
                        var luasnya = Ext.getCmp('demoplot_FpPanjang').getValue() * Ext.getCmp('demoplot_FpLebar').getValue();
                        Ext.getCmp('demoplot_FpArea').setValue(luasnya);
                    }
                }
            },{
                xtype: 'numericfield',
                fieldLabel: lang('Lebar'),
                id: 'demoplot_FpLebar',
                name: 'demoplot_FpLebar',
                listeners:{
                    change: function(){
                        var luasnya = Ext.getCmp('demoplot_FpPanjang').getValue() * Ext.getCmp('demoplot_FpLebar').getValue();
                        Ext.getCmp('demoplot_FpArea').setValue(luasnya);
                    }
                }
            },{
                xtype: 'numericfield',
                fieldLabel: lang('Area (m2)'),
                id: 'demoplot_FpArea',
                name: 'demoplot_FpArea',
                readOnly: true
            },{
                layout: 'column',
                border: false,
                items: [{
                    columnWidth: 0.25,
                    xtype: 'label',
                    cls: 'x-form-item-label',
                    text: lang('Fish species')
                },{
                    columnWidth: 0.4,
                    border: false,
                    defaultType: 'checkboxfield',
                    items:[{
                        boxLabel: lang('Nila'),
                        name: 'demoplot_FpNila',
                        id: 'demoplot_FpNila',
                        inputValue: '1'
                    },{
                        boxLabel: lang('Carp'),
                        name: 'demoplot_FpCarp',
                        id: 'demoplot_FpCarp',
                        inputValue: '1'
                    },{
                        boxLabel: lang('Catfish'),
                        name: 'demoplot_FpCatfish',
                        id: 'demoplot_FpCatfish',
                        inputValue: '1'
                    }]
                },{
                    columnWidth: 0.35,
                    border: false,
                    defaultType: 'checkboxfield',
                    items:[{
                        boxLabel: lang('Tilapia'),
                        name: 'demoplot_FpTilapia',
                        id: 'demoplot_FpTilapia',
                        inputValue: '1'
                    },{
                        boxLabel: lang('Others'),
                        name: 'demoplot_FpOthers',
                        id: 'demoplot_FpOthers',
                        inputValue: '1'
                    }]
                }]
            },{
                html:'<br />'
            },{
                xtype: 'textarea',
                fieldLabel: 'Comment',
                id: 'demoplot_comment',
                name: 'demoplot_comment'
            }
        ],
        buttons: [{
                id: 'saveButtonDemoplot',
                text: 'Save',
                margin: '5px',
                scale: 'large',
                ui: 's-button',
                cls: 's-blue',
                handler: function() {
                    var form = this.up('form').getForm();
                    var methode;
                    if (Ext.getCmp('demoplot_id').getValue() == '')
                        methode = 'POST';
                    else
                        methode = 'PUT';
                    form.submit({
                        url: m_demoplot,
                        method: methode,
                        waitMsg: 'Sending data...',
                        success: function(fp, o) {
                            Ext.MessageBox.alert('Success', 'Data saved.');
                        }
                    });
                    winFormDemoplot.hide(this, function() {
                        store_demoplot.load({
                            params: {
                                cpg_id: Ext.getCmp('demoplot_cpg_id').getValue()
                            }
                        });
                    });
                }
            }
            , {
                text: 'Close',
                margin: '5px',
                scale: 'large',
                ui: 's-button',
                cls: 's-grey',
                disabled: false,
                handler: function() {
                    winFormDemoplot.hide();
                }
            }
        ]
    });

    var winFormDemoplot = Ext.create('widget.window', {
        title: 'Form Demoplot',
        id: 'winFormDemoplot',
        closeAction: 'hide',
        width: '50%',
        height: '50%',
        autoScroll: true,
        modal: true,
        layout: 'fit',
        items: [DataFormWindowDemoplot]
    });

    function resetFormDemoplot() {
//        Ext.getCmp('id').setValue('');
    }

    //
    ////////////////////////////////////////////////////////////////////////////

    //== show nursery panel
    function displayFormNurseyPenjualan() {
        mc_petani_pic.load({
            params: {
                cpg_id: Ext.getCmp('CPGId').getValue()
            }
        });
        if (!winNurseyPenjualan.isVisible()) {
            Ext.getCmp('dataFormNurseyPenjualan').getForm().reset();
            winNurseyPenjualan.center();
            winNurseyPenjualan.show();
        } else {
            winNurseyPenjualan.hide(this, function() {
            });
            winNurseyPenjualan.toFront();
        }
    }

    var mc_combo_nurserynr = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'label'],
        autoLoad: false,
        proxy: {
            type: 'ajax',
            url: m_api + '/cpg/nurserynr_combo',
            reader: {
                type: 'json',
                root: 'data'
            }
        },
        listeners: {
            'beforeload': function(store, options) {
                store.proxy.extraParams.CPGId = Ext.getCmp('CPGId').getValue();
            }
        }
    });

    var DataFormNurseyPenjualan = Ext.create('Ext.panel.Panel', {
        frame: false,
        autoScroll: true,
        height: 350,
        width: '100%',
        bodyPadding: 5,
        id: 'dataFormNurseyPenjualanWin',
        fieldDefaults: {
            labelAlign: 'left',
            labelWidth: 175,
            anchor: '95%'
        },
        items:[{
            xtype: 'form',
            id: 'dataFormNurseyPenjualan',
            fileUpload: true,
            items: [{
                layout: 'column',
                border: false,
                items: [{
                        columnWidth: .5,
                        layout: 'form',
                        border: false,
                        padding: 5,
                        items: [{
                                xtype: 'hidden',
                                id: 'NurseryID',
                                name: 'NurseryID'
                            }, {
                                xtype: 'hidden',
                                value: 'cpg',
                                id: 'type_obj',
                                name: 'type_obj'
                            }, {
                                xtype: 'hidden',
                                id: 'nid_obj',
                                name: 'id_obj'
                            }, {
                                xtype: 'textfield',
                                id: 'NurseryNrSend',
                                name: 'NurseryNrSend',
                                hidden: true
                            },{
                                fieldLabel: lang('Nursery Nr'),
                                labelWidth: '175',
                                id: 'NurseryNr',
                                name: 'NurseryNr',
                                xtype: 'combo',
                                store: mc_combo_nurserynr,
                                displayField: 'label',
                                valueField: 'id',
                                queryMode: 'local',
                                allowBlank: false,
                                listeners: {
                                    change: function(cb, nv, ov) {
                                        if (this.value != '-1') {

                                            //reset all dulu
                                            //reset all form but NurseryNr ============================================
                                            var fields = this.up('form').query('[isFormField][name!="NurseryNr"]');
                                            for (var i = 0, len = fields.length; i < len; i++) {
                                                fields[i].reset();
                                            }
                                            Ext.getCmp('iphoto').setSrc(m_api_base_url + '/images/nursery/no-image.png');
                                            Ext.getCmp('iphotoResponsible').setSrc(m_api_base_url + '/images/Photo/no-user.jpg');
                                            //reset all form but NurseryNr ============================================

                                            //reset data di grid
                                            store_nursey_penjualan.load();
                                            store_nursey_monitoring.load();

                                            Ext.getCmp('dataFormNurseyPenjualan').getForm().load({
                                                url: m_crud,
                                                method: 'GET',
                                                params: {id: Ext.getCmp('CPGId').getValue(), NurseryNr: this.value, opsiCall:'form'},
                                                success: function(form,action) {
                                                    var actionDec = Ext.decode(action.response.responseText);
                                                    var r = actionDec.data;

                                                    //photo===========================================
                                                    if(r.Photo != ""){
                                                        var fotoUser = m_api_base_url + '/images/nursery/' + r.Photo;
                                                        Ext.getCmp('Photo_old').setValue(r.Photo);
                                                        checkImageExists(fotoUser, function(existsImage) {
                                                            if (existsImage == true) {
                                                                Ext.getCmp('iphoto').setSrc(fotoUser);
                                                            } else {
                                                                Ext.getCmp('iphoto').setSrc(m_api_base_url + '/images/nursery/no-image.png');
                                                            }
                                                        });
                                                    }

                                                    //photo responsible=====================================
                                                    if(r.ResponsiblePhoto != ""){
                                                        var fotoUserResponsible = m_api_base_url + '/images/photo_responsible/' + r.ResponsiblePhoto;
                                                        Ext.getCmp('Photo_old_responsible').setValue(r.ResponsiblePhoto);
                                                        checkImageExists(fotoUserResponsible, function(existsImage) {
                                                            if (existsImage == true) {
                                                                Ext.getCmp('iphotoResponsible').setSrc(fotoUserResponsible);
                                                            } else {
                                                                Ext.getCmp('iphotoResponsible').setSrc(m_api_base_url + '/images/Photo/no-user.jpg');
                                                            }
                                                        });
                                                    }

                                                    if(r.nurResponsibleGender == "m"){
                                                        Ext.getCmp('nurResponsibleGenderM').setValue(true);
                                                    }
                                                    if(r.nurResponsibleGender == "f"){
                                                        Ext.getCmp('nurResponsibleGenderF').setValue(true);
                                                    }

                                                    Ext.getCmp('nid_obj').setValue(Ext.getCmp('CPGId').getValue());
                                                    Ext.getCmp('NurseryID').setValue(r.NurseryID);
                                                    Ext.getCmp('Responsible').setValue(r.Responsible);
                                                    Ext.getCmp('nEstablished').setValue(r.nEstablished);
                                                    Ext.getCmp('Panjang').setValue(r.Panjang);
                                                    Ext.getCmp('Lebar').setValue(r.Lebar);
                                                    Ext.getCmp('Luas').setValue(nnumber_format(r.Lebar * r.Panjang));
                                                    Ext.getCmp('Kapasitas').setValue(nnumber_format(r.Kapasitas));
                                                    Ext.getCmp('Latitude').setValue(r.Latitude);
                                                    Ext.getCmp('Longitude').setValue(r.Longitude);
                                                    Ext.getCmp('LatitudeDeg1').setValue(r.LatitudeDeg1);
                                                    Ext.getCmp('LatitudeDeg2').setValue(r.LatitudeDeg2);
                                                    Ext.getCmp('LatitudeDeg3').setValue(r.LatitudeDeg3);
                                                    Ext.getCmp('LongitudeDeg1').setValue(r.LongitudeDeg1);
                                                    Ext.getCmp('LongitudeDeg2').setValue(r.LongitudeDeg2);
                                                    Ext.getCmp('LongitudeDeg3').setValue(r.LongitudeDeg3);
                                                    if(r.CertificationStatus == 'Yes'){
                                                        Ext.getCmp('NursCertBp2Ya').setValue(true);
                                                        Ext.getCmp('tglCertificate').setValue(r.DateCertification);
                                                        Ext.getCmp('DateAppliedCertification').setValue(r.DateAppliedCertification);
                                                    }else{
                                                       Ext.getCmp('NursCertBp2Tidak').setValue(true);
                                                    }
                                                    store_nursey_penjualan.load({
                                                        params: {
                                                            nursery_id: r.NurseryID
                                                        }
                                                    });
                                                    store_nursey_monitoring.load({
                                                        params: {
                                                            nursery_id: r.NurseryID
                                                        }
                                                    });

                                                    console.log(Ext.getCmp('NurseryID').getValue());
                                                }
                                            });

                                            //proses update
                                            /*
                                            Ext.Ajax.request({
                                                url: m_crud,
                                                method: 'GET',
                                                params: {id: Ext.getCmp('CPGId').getValue(), NurseryNr: this.value, opsiCall:'form'},
                                                success: function(fp, o) {
                                                    var r = Ext.decode(fp.responseText);
                                                    Ext.getCmp('nid_obj').setValue(Ext.getCmp('CPGId').getValue());
                                                    Ext.getCmp('NurseryID').setValue(r.NurseryID);
                                                    Ext.getCmp('Responsible').setValue(r.Responsible);
                                                    Ext.getCmp('nEstablished').setValue(r.nEstablished);
                                                    Ext.getCmp('Panjang').setValue(r.Panjang);
                                                    Ext.getCmp('Lebar').setValue(r.Lebar);
                                                    Ext.getCmp('Luas').setValue(nnumber_format(r.Lebar * r.Panjang));
                                                    Ext.getCmp('Kapasitas').setValue(nnumber_format(r.Kapasitas));
                                                    Ext.getCmp('Latitude').setValue(r.Latitude);
                                                    Ext.getCmp('Longitude').setValue(r.Longitude);
                                                    Ext.getCmp('LatitudeDeg1').setValue(r.LatitudeDeg1);
                                                    Ext.getCmp('LatitudeDeg2').setValue(r.LatitudeDeg2);
                                                    Ext.getCmp('LatitudeDeg3').setValue(r.LatitudeDeg3);
                                                    Ext.getCmp('LongitudeDeg1').setValue(r.LongitudeDeg1);
                                                    Ext.getCmp('LongitudeDeg2').setValue(r.LongitudeDeg2);
                                                    Ext.getCmp('LongitudeDeg3').setValue(r.LongitudeDeg3);
                                                    if(r.CertificationStatus == 'Yes'){
                                                        Ext.getCmp('NursCertBp2Ya').setValue(true);
                                                        Ext.getCmp('tglCertificate').setValue(r.DateCertification);
                                                        Ext.getCmp('DateAppliedCertification').setValue(r.DateAppliedCertification);
                                                    }else{
                                                       Ext.getCmp('NursCertBp2Tidak').setValue(true);
                                                    }
                                                    store_nursey_penjualan.load({
                                                        params: {
                                                            nursery_id: r.NurseryID
                                                        }
                                                    });
                                                    store_nursey_monitoring.load({
                                                        params: {
                                                            nursery_id: r.NurseryID
                                                        }
                                                    });
                                                }
                                            })
                                            */

                                        }else{
                                            //reset all form but NurseryNr ============================================
                                            var fields = this.up('form').query('[isFormField][name!="NurseryNr"]');
                                            for (var i = 0, len = fields.length; i < len; i++) {
                                                fields[i].reset();
                                            }
                                            Ext.getCmp('iphoto').setSrc(m_api_base_url + '/images/nursery/no-image.png');
                                            Ext.getCmp('iphotoResponsible').setSrc(m_api_base_url + '/images/Photo/no-user.jpg');
                                            //reset all form but NurseryNr ============================================

                                            //reset data di grid
                                            store_nursey_penjualan.load();
                                            store_nursey_monitoring.load();
                                        }
                                    }
                                }
                            },{
                                xtype: 'combo',
                                store: cmb_respon_type,
                                labelWidth: '175',
                                fieldLabel: lang('Responsible Type'),
                                id: 'nurResponsibleType',
                                name: 'nurResponsibleType',
                                displayField: 'label',
                                valueField: 'id',
                                queryMode: 'local',
                                allowBlank: false,
                                listeners: {
                                    change: function(cb, nv, ov) {
                                        if(nv != 'other'){
                                            Ext.getCmp('Responsible').setDisabled(false);
                                            Ext.getCmp('nurResponsibleName').setVisible(false);
                                            Ext.getCmp('nurResponsibleBirthday').setVisible(false);
                                            Ext.getCmp('nurResponsiblePhone').setVisible(false);
                                            Ext.getCmp('nurResponsibleGender').setVisible(false);
                                            Ext.getCmp('divPhotoResponsible').setVisible(false);
                                            Ext.getCmp('PhotoResponsible').setVisible(false);
                                            cmb_respon_id.load();
                                        }else{
                                            Ext.getCmp('Responsible').setDisabled(true);
                                            Ext.getCmp('nurResponsibleName').setVisible(true);
                                            Ext.getCmp('nurResponsibleBirthday').setVisible(true);
                                            Ext.getCmp('nurResponsiblePhone').setVisible(true);
                                            Ext.getCmp('nurResponsibleGender').setVisible(true);
                                            Ext.getCmp('divPhotoResponsible').setVisible(true);
                                            Ext.getCmp('PhotoResponsible').setVisible(true);
                                        }
                                    }
                                }
                            },{
                                xtype: 'combo',
                                store: cmb_respon_id,
                                labelWidth: '175',
                                fieldLabel: lang('Penanggung Jawab'),
                                id: 'Responsible',
                                name: 'Responsible',
                                displayField: 'label',
                                valueField: 'id',
                                queryMode: 'local'
                            }, {
                                xtype: 'textfield',
                                fieldLabel: lang('Responsible Name'),
                                id: 'nurResponsibleName',
                                name: 'nurResponsibleName',
                                hidden:true
                            },{
                                xtype: 'datefield',
                                fieldLabel: lang('Responsible Birthdate'),
                                labelWidth: '175',
                                id: 'nurResponsibleBirthday',
                                name: 'nurResponsibleBirthday',
                                format: 'Y-m-d',
                                hidden:true
                            },{
                                xtype: 'textfield',
                                fieldLabel: lang('Responsible Phone'),
                                id: 'nurResponsiblePhone',
                                name: 'nurResponsiblePhone',
                                hidden:true
                            },{
                                xtype: 'radiogroup',
                                fieldLabel: lang('Responsible Gender'),
                                id:'nurResponsibleGender',
                                labelWidth: '175',
                                hidden:true,
                                items: [{
                                    name: 'nurResponsibleGender',
                                    id: 'nurResponsibleGenderM',
                                    boxLabel: lang('Male'),
                                    inputValue: 'm'
                                }, {
                                    name: 'nurResponsibleGender',
                                    id: 'nurResponsibleGenderF',
                                    boxLabel: lang('Female'),
                                    inputValue: 'f'
                                }]
                            },{
                                layout:'column',
                                border:false,
                                style:'margin-bottom:5px;margin-right:-5px;',
                                id:'divPhotoResponsible',
                                hidden:true,
                                items:[{
                                    columnWidth: 1,
                                    border: false,
                                    layout:{
                                        type:'hbox',
                                        pack:'end'
                                    },
                                    items:[{
                                        xtype: 'image',
                                        id: 'iphotoResponsible',
                                        width: '150px',
                                        height:'150px',
                                        src: m_api_base_url + '/images/Photo/no-user.jpg'
                                    },{
                                        xtype: 'textfield',
                                        id: 'Photo_old_responsible',
                                        name: 'Photo_old_responsible',
                                        inputType: 'hidden'
                                    }]
                                }]
                            },{
                                xtype: 'fileuploadfield',
                                fieldLabel: lang('Photo'),
                                labelWidth: 130,
                                id: 'PhotoResponsible',
                                name: 'PhotoResponsible',
                                buttonText: 'Browse',
                                hidden:true,
                                listeners: {
                                    'change': function (fb, v) {
                                        var form = Ext.getCmp('dataFormNurseyPenjualan').getForm();
                                        form.submit({
                                            url: m_api + '/cpg/nursery_form_photo_responsible',
                                            clientValidation: false,
                                            waitMsg: 'Sending Photo...',
                                            success: function (fp, o) {
                                                Ext.getCmp('iphotoResponsible').setSrc(m_api_base_url + '/images/photo_responsible/' + o.result.file);
                                                Ext.getCmp('Photo_old_responsible').setValue(o.result.file);
                                            }
                                        });
                                    }
                                }
                            },{
                                xtype: 'datefield',
                                fieldLabel: lang('Tanggal Berdiri'),
                                labelWidth: '175',
                                id: 'nEstablished',
                                name: 'nEstablished',
                                format: 'Y-m-d'
                            }, {
                              xtype: 'radiogroup',
                              fieldLabel: lang('Nursery Ceritification - BP2MB'),
                              labelWidth: '175',
                              items: [{
                                  name: 'NursCertBp2YaTidak',
                                  id: 'NursCertBp2Ya',
                                  boxLabel: lang('ya'),
                                  inputValue: 'Yes'
                              }, {
                                  name: 'NursCertBp2YaTidak',
                                  id: 'NursCertBp2Tidak',
                                  boxLabel: lang('Tidak'),
                                  inputValue: 'No'
                              }],
                              listeners: {
                                 change: function() {
                                    if(Ext.getCmp('NursCertBp2Ya').getValue() == true){
                                       Ext.getCmp('tglCertificate').setDisabled(false);
                                       Ext.getCmp('DateAppliedCertification').setDisabled(false);
                                    }else{
                                       Ext.getCmp('tglCertificate').setDisabled(true);
                                       Ext.getCmp('tglCertificate').setValue('');
                                       Ext.getCmp('DateAppliedCertification').setDisabled(true);
                                       Ext.getCmp('DateAppliedCertification').setValue('');
                                    }
                                 }
                              }
                            },{
                              xtype: 'datefield',
                              fieldLabel: lang('Date of Certificate Issue'),
                              labelWidth: '175',
                              id: 'tglCertificate',
                              name: 'tglCertificate',
                              format: 'Y-m-d'
                            },{
                              xtype: 'datefield',
                              fieldLabel: lang('Date Applied for Certification'),
                              labelWidth: '175',
                              id: 'DateAppliedCertification',
                              name: 'DateAppliedCertification',
                              format: 'Y-m-d'
                            },{
                                items: [{
                                    xtype: 'button',
                                    margin: '0',
                                    width:'150px',
                                    id: 'buttonPrintNurseryProfile',
                                    text: lang('Print Nursery Profile'),
                                    handler: function() {
                                        var cek = cekNurseryID();
                                        if(cek == true){
                                            var urlPrint = m_api + '/nursery/cetak_nursery_summary/cpg/'+Ext.getCmp('CPGId').getValue()+'/'+Ext.getCmp('NurseryNr').getValue()+'/';
                                            preview_cetak_surat(urlPrint);
                                        }
                                    }
                                }]
                            }
                            ]
                    }, {
                        columnWidth: .5,
                        layout: 'form',
                        border: false,
                        padding: 5,
                        items: [{
                                xtype: 'textfield',
                                fieldLabel: lang('Panjang (m)'),
                                id: 'Panjang',
                                name: 'Panjang',
                                fieldCls: 'classuang',
                                listeners: {
                                    change: function(cb, nv, ov) {
                                        Ext.getCmp('Luas').setValue(nnumber_format(nnumber_format(Ext.getCmp('Panjang').getValue(), 2) * nnumber_format(Ext.getCmp('Lebar').getValue(), 2)))
                                    }
                                }
                            }, {
                                xtype: 'textfield',
                                fieldLabel: lang('Lebar (m)'),
                                id: 'Lebar',
                                name: 'Lebar',
                                listeners: {
                                    change: function(cb, nv, ov) {
                                        Ext.getCmp('Luas').setValue(nnumber_format(nnumber_format(Ext.getCmp('Panjang').getValue(), 2) * nnumber_format(Ext.getCmp('Lebar').getValue(), 2)))
                                    }
                                }
                            }, {
                                xtype: 'textfield',
                                fieldLabel: lang('Luas (m2)'),
                                id: 'Luas',
                                name: 'Luas',
                                readOnly: true,
                                listeners: {
                                    change: function(cb, nv, ov) {
                                        Ext.getCmp('Kapasitas').setValue(nnumber_format(nnumber_format(Ext.getCmp('Luas').getValue(), 2) * 40))
                                    }
                                }
                            },{
                                xtype: 'textfield',
                                fieldLabel: lang('Kapasitas (Luas (m2) x 40)'),
                                id: 'Kapasitas',
                                name: 'Kapasitas',
                                labelWidth: 160,
                                readOnly: true
                            }, {
                                xtype: 'textfield',
                                fieldLabel: lang('Latitude (Dec)'),
                                id: 'Latitude',
                                name: 'Latitude',
                                readOnly: m_hakakses_lat_short
                            }, {
                                xtype: 'textfield',
                                fieldLabel: lang('Longitude (Dec)'),
                                id: 'Longitude',
                                name: 'Longitude',
                                readOnly: m_hakakses_long_short
                            }, {
                                layout: 'column',
                                border: false,
                                hidden:true,
                                items: [{
                                        columnWidth: .7,
                                        layout: 'form',
                                        border: false,
                                        items: [{
                                                xtype: 'textfield',
                                                fieldLabel: lang('Latitude (Deg)'),
                                                labelWidth: 160,
                                                id: 'LatitudeDeg1',
                                                name: 'LatitudeDeg1',
                                                readOnly: m_hakakses_lat_long
                                            }]
                                    }, {
                                        columnWidth: .15,
                                        layout: 'form',
                                        border: false,
                                        items: [{
                                                xtype: 'textfield',
                                                id: 'LatitudeDeg2',
                                                name: 'LatitudeDeg2',
                                                readOnly: m_hakakses_lat_long
                                            }]
                                    }, {
                                        columnWidth: .15,
                                        layout: 'form',
                                        border: false,
                                        items: [{
                                                xtype: 'textfield',
                                                id: 'LatitudeDeg3',
                                                name: 'LatitudeDeg3',
                                                readOnly: m_hakakses_lat_long
                                            }]
                                    }]
                            }, {
                                layout: 'column',
                                border: false,
                                hidden:true,
                                items: [{
                                        columnWidth: .7,
                                        layout: 'form',
                                        border: false,
                                        items: [{
                                                xtype: 'textfield',
                                                labelWidth: 160,
                                                fieldLabel: lang('Longitude (Deg)'),
                                                id: 'LongitudeDeg1',
                                                name: 'LongitudeDeg1',
                                                readOnly: m_hakakses_long_long
                                            }]
                                    }, {
                                        columnWidth: .15,
                                        layout: 'form',
                                        border: false,
                                        items: [{
                                                xtype: 'textfield',
                                                id: 'LongitudeDeg2',
                                                name: 'LongitudeDeg2',
                                                readOnly: m_hakakses_long_long
                                            }]
                                    }, {
                                        columnWidth: .15,
                                        layout: 'form',
                                        border: false,
                                        items: [{
                                                xtype: 'textfield',
                                                id: 'LongitudeDeg3',
                                                name: 'LongitudeDeg3',
                                                readOnly: m_hakakses_long_long
                                            }]
                                    }]
                            },{
                                items: [{
                                    layout: 'column',
                                    labelWidth: 500,
                                    items: [{
                                        html: lang('Map Area'),
                                    }, {
                                        items: [{
                                            xtype: 'button',
                                            margin: '0 0 0 120',
                                            width:'100px',
                                            id: 'buttonShowPolygon',
                                            text: lang('Show Polygon'),
                                            handler: function() {
                                                var cek = cekNurseryID();
                                                if(cek == true){
                                                    displayNurseryPolygon(Ext.getCmp('NurseryID').getValue(),Ext.getCmp('NurseryNr').getValue());
                                                }
                                            }
                                        }]
                                    }]
                                }]
                            },{

                                layout:'column',
                                border:false,
                                style:'margin-bottom:5px;margin-right:-5px;',
                                items:[{
                                    columnWidth: 1,
                                    border: false,
                                    layout:{
                                        type:'hbox',
                                        pack:'end'
                                    },
                                    items:[{
                                        xtype: 'image',
                                        id: 'iphoto',
                                        width: '150px',
                                        height:'150px',
                                        src: m_api_base_url + '/images/nursery/no-image.png'
                                    },{
                                        xtype: 'textfield',
                                        id: 'Photo_old',
                                        name: 'Photo_old',
                                        inputType: 'hidden'
                                    }]
                                }]

                            },{

                                xtype: 'fileuploadfield',
                                fieldLabel: lang('Photo'),
                                labelWidth: 130,
                                id: 'Photo',
                                name: 'Photo',
                                buttonText: 'Browse',
                                listeners: {
                                    'change': function (fb, v) {
                                        var form = Ext.getCmp('dataFormNurseyPenjualan').getForm();
                                        form.submit({
                                            url: m_api + '/cpg/nursery_form_photo',
                                            clientValidation: false,
                                            waitMsg: 'Sending Photo...',
                                            success: function (fp, o) {
                                                Ext.getCmp('iphoto').setSrc(m_api_base_url + '/images/nursery/' + o.result.file);
                                                Ext.getCmp('Photo_old').setValue(o.result.file);
                                            }
                                        });
                                    }
                                }

                            }]
                    }]
            }, {
                xtype: 'tabpanel',
                flex: 1,
                margin: 2,
                activeTab: 0,
                plain: true,
                items: [{// tab nursery penjualan
                        xtype: 'gridpanel',
                        title: lang('Nursery Penjualan'),
                        id: 'gnurseypenjualan',
                        style: 'border:1px solid #CCC;',
                        store: store_nursey_penjualan,
                        width: '100%',
                        loadMask: true,
                        selType: 'rowmodel',
                        minHeight: 190,
                        dockedItems: [{
                                xtype: 'toolbar',
                                items: [{
                                        icon: varjs.config.base_url + 'images/icons/new/add.png', cls:'Sfr_BtnGridGreen', overCls:'Sfr_BtnGridGreen-Hover',
                                        // cls: m_act_save,
                                        hidden: !m_act_add,
                                        text: lang('Add'),
                                        scope: this,
                                        handler: function() {
                                            var cek = cekNurseryID();
                                            if(cek == true){
                                                nRowEditing.cancelEdit();
                                                var r = Ext.create('penjualan.Model', {
                                                    id: '', Buyer: '', Volume: '', Price: '', Total: '', DateTransaction: ''
                                                });
                                                store_nursey_penjualan.insert(0, r);
                                                nRowEditing.startEdit(0, 0);
                                                uang(document.getElementById('nvol'));
                                            }
                                        }
                                    }, {
                                        icon: varjs.config.base_url + 'images/icons/new/update.png',
                                        // cls: m_act_save,
                                        hidden: !m_act_update,
                                        text: lang('Update'),
                                        scope: this,
                                        handler: function() {
                                            nRowEditing.cancelEdit();
                                            var sm = Ext.getCmp('gnurseypenjualan').getSelectionModel().getSelection();
                                            nRowEditing.startEdit(sm[0].index, 0);
                                        }
                                    }, {
                                        itemId: 'remove',
                                        icon: varjs.config.base_url + 'images/icons/new/delete.png',
                                        // cls: m_act_save,
                                        hidden: !m_act_delete,
                                        text: lang('Delete'),
                                        scope: this,
                                        handler: function() {
                                            var smb = Ext.getCmp('gnurseypenjualan').getSelectionModel().getSelection()[0];
                                            nRowEditing.cancelEdit();
                                            Ext.MessageBox.confirm('Message', lang('Apakah anda mau menghapus data ini ?'), function(btn) {
                                                if (btn == 'yes') {
                                                    Ext.Ajax.request({
                                                        waitMsg: lang('Please Wait'),
                                                        url: m_nursey + '_penjualan',
                                                        method: 'DELETE',
                                                        params: {
                                                            id: smb.raw.id
                                                        },
                                                        success: function(response, opts) {
                                                            var obj = Ext.decode(response.responseText);
                                                            switch (obj.success) {
                                                                case true:
                                                                    store_nursey_penjualan.load({
                                                                        params: {
                                                                            nursery_id: Ext.getCmp('NurseryID').getValue()
                                                                        }
                                                                    });
                                                                    break;
                                                                default:
                                                                    Ext.MessageBox.alert('Warning', obj.message);
                                                                    break;
                                                            }
                                                        },
                                                        failure: function(response, opts) {
                                                            var obj = Ext.decode(response.responseText);
                                                            Ext.MessageBox.alert('error', lang('Could not connect to the database. Retry later'));
                                                        }
                                                    });
                                                }
                                            });
                                        }
                                    }]
                            }],
                        columns: [{
                                text: lang('ID'),
                                dataIndex: 'id',
                                hidden: true
                            }, {
                                text: lang('No'),
                                xtype: 'rownumberer',
                                width: '5%'
                            }, {
                                text: lang('Pembeli'),
                                dataIndex: 'Buyer',
                                width: '15%',
                                editor: {
                                    xtype: 'combo',
                                    store: mc_pembeli,
                                    displayField: 'label',
                                    valueField: 'id',
                                    queryMode: 'local',
                                    allowBlank: false
                                }
                            }, {
                                text: lang('Bibit Dijual'),
                                dataIndex: 'Volume',
                                width: '10%',
                                editor: {
                                    xtype: 'textfield',
                                    id: 'nvol',
                                    allowBlank: false,
                                    listeners: {
                                        change: function() {
                                            Ext.getCmp('ntot').setValue(Ext.getCmp('nvol').getValue() * Ext.getCmp('npri').getValue());
                                        }
                                    }
                                }
                            }, {

                              text: lang('Clone Type'),
                              dataIndex: 'CloneTypeID',
                              width: '15%',
                              editor: {
                                 xtype: 'combo',
                                 store: mc_clone_type_combo,
                                 displayField: 'label',
                                 valueField: 'id',
                                 queryMode: 'local',
                                 allowBlank: false
                              }

                            }, {
                                text: lang('Harga Satuan'),
                                dataIndex: 'Price',
                                width: '10%',
                                editor: {
                                    xtype: 'textfield',
                                    id: 'npri',
                                    allowBlank: false,
                                    listeners: {
                                        change: function() {
                                            Ext.getCmp('ntot').setValue(Ext.getCmp('nvol').getValue() * Ext.getCmp('npri').getValue());
                                        }
                                    }
                                }
                            }, {
                                text: lang('Total'),
                                dataIndex: 'Total',
                                width: '15%',
                                editor: {
                                    xtype: 'textfield',
                                    allowBlank: false,
                                    id: 'ntot',
                                    readOnly: true
                                }
                            }, {
                                text: lang('Tanggal Penjualan'),
                                dataIndex: 'DateTransaction',
                                format: 'Y-m-d',
                                width: '28%',
                                editor: {
                                    xtype: 'datefield',
                                    format: 'Y-m-d',
                                    allowBlank: false
                                }
                            }],
                        plugins: [nRowEditing],
                        listeners: {
                            itemdblclick: function(dv, record, item, index, e) {
                                if (!m_act_update) {
                                    nRowEditing.cancelEdit();
                                }
                            },
                            'canceledit': function(editor, e, eOpts) {
                                store_nursey_penjualan.load({
                                    params: {
                                        nursery_id: Ext.getCmp('NurseryID').getValue()
                                    }
                                });
                            },
                            'edit': function(editor, e) {
                                if (e.record.data.id == '') {
                                    Ext.Ajax.request({
                                        waitMsg: lang('Please wait...'),
                                        url: m_nursey + '_penjualan',
                                        method: 'POST',
                                        params: {
                                            id_nursey: Ext.getCmp('NurseryID').getValue(),
                                            Buyer: e.record.data.Buyer,
                                            CloneTypeID: e.record.data.CloneTypeID,
                                            Volume: e.record.data.Volume,
                                            Price: e.record.data.Price,
                                            Total: e.record.data.Totel,
                                            DateTransaction: e.record.data.DateTransaction
                                        },
                                        success: function(response, opts) {
                                            var obj = Ext.decode(response.responseText);
                                            switch (obj.success) {
                                                case true:
                                                    Ext.MessageBox.alert('Success', obj.message);
                                                    store_nursey_penjualan.load({
                                                        params: {
                                                            nursery_id: Ext.getCmp('NurseryID').getValue()
                                                        }
                                                    });
                                                    break;
                                                default:
                                                    Ext.MessageBox.alert('Warning', obj.message);
                                                    break;
                                            }
                                        },
                                        failure: function(response, opts) {
                                            var obj = Ext.decode(response.responseText);
                                            Ext.MessageBox.alert('error', lang('Could not connect to the database. Retry later'));
                                        }
                                    });
                                } else {
                                    Ext.MessageBox.confirm('Message', lang('Update data ini ?'), function(btn) {
                                        if (btn == 'yes') {
                                            Ext.Ajax.request({
                                                waitMsg: lang('Please wait...'),
                                                url: m_nursey + '_penjualan',
                                                method: 'PUT',
                                                params: {
                                                    id: e.record.data.id,
                                                    id_nursey: Ext.getCmp('NurseryID').getValue(),
                                                    Buyer: e.record.data.Buyer,
                                                    CloneTypeID: e.record.data.CloneTypeID,
                                                    Volume: e.record.data.Volume,
                                                    Price: e.record.data.Price,
                                                    Total: e.record.data.Totel,
                                                    DateTransaction: e.record.data.DateTransaction
                                                },
                                                success: function(response, opts) {
                                                    var obj = Ext.decode(response.responseText);
                                                    switch (obj.success) {
                                                        case true:
                                                            Ext.MessageBox.alert('Success', obj.message);
                                                            store_nursey_penjualan.load({
                                                                params: {
                                                                    nursery_id: Ext.getCmp('NurseryID').getValue()
                                                                }
                                                                , callback: function(r, options, success) {
                                                                    console.log(r)
                                                                }
                                                            });
                                                            break;
                                                        default:
                                                            Ext.MessageBox.alert('Warning', obj.message);
                                                            break;
                                                    }
                                                },
                                                failure: function(response, opts) {
                                                    var obj = Ext.decode(response.responseText);
                                                    Ext.MessageBox.alert('error', lang('Could not connect to the database. Retry later'));
                                                }
                                            });
                                        }
                                    });
                                }
                            }
                        }
                    }, {// tab nursery monitoring
                        xtype: 'gridpanel',
                        title: lang('Nursery Monitoring'),
                        id: 'gnurseymonitoring',
                        style: 'border:1px solid #CCC;',
                        store: store_nursey_monitoring,
                        width: '100%',
                        loadMask: true,
                        selType: 'rowmodel',
                        minHeight: 190,
                        dockedItems: [{
                                xtype: 'toolbar',
                                items: [{
                                        icon: varjs.config.base_url + 'images/icons/new/add.png', cls:'Sfr_BtnGridGreen', overCls:'Sfr_BtnGridGreen-Hover',
                                        // cls: m_act_save,
                                        hidden: !m_act_add,
                                        text: lang('Add'),
                                        scope: this,
                                        handler: function() {
                                            var cek = cekNurseryID();
                                            if(cek == true){
                                                mRowEditing.cancelEdit();
                                                var r = Ext.create('monitoring.Model', {
                                                    id: '',
                                                    MonitoringDate: '',
                                                    MonitoringStatus: '',
                                                    Description: ''
                                                });
                                                store_nursey_monitoring.insert(0, r);
                                                mRowEditing.startEdit(0, 0);
                                            }
                                        }
                                    }, {
                                        icon: varjs.config.base_url + 'images/icons/new/update.png',
                                        // cls: m_act_save,
                                        hidden: !m_act_update,
                                        text: lang('Update'),
                                        scope: this,
                                        handler: function() {
                                            mRowEditing.cancelEdit();
                                            var sm = Ext.getCmp('gnurseymonitoring').getSelectionModel().getSelection();
                                            mRowEditing.startEdit(sm[0].index, 0);
                                            act_nursery_status(Ext.getCmp('mStatus').getValue());
                                        }
                                    }, {
                                        itemId: 'remove',
                                        icon: varjs.config.base_url + 'images/icons/new/delete.png',
                                        // cls: m_act_save,
                                        hidden: !m_act_delete,
                                        text: lang('Delete'),
                                        scope: this,
                                        handler: function() {
                                            var smb = Ext.getCmp('gnurseymonitoring').getSelectionModel().getSelection()[0];
                                            mRowEditing.cancelEdit();
                                            Ext.MessageBox.confirm('Message', lang('Apakah anda mau menghapus data ini ?'), function(btn) {
                                                if (btn == 'yes') {
                                                    Ext.Ajax.request({
                                                        waitMsg: lang('Please Wait'),
                                                        url: m_nursey + '_monitorings',
                                                        method: 'DELETE',
                                                        params: {
                                                            id: smb.raw.id
                                                        },
                                                        success: function(response, opts) {
                                                            var obj = Ext.decode(response.responseText);
                                                            switch (obj.success) {
                                                                case true:
                                                                    store_nursey_monitoring.load({
                                                                        params: {
                                                                            nursery_id: Ext.getCmp('NurseryID').getValue()
                                                                        }
                                                                    });
                                                                    break;
                                                                default:
                                                                    Ext.MessageBox.alert('Warning', obj.message);
                                                                    break;
                                                            }
                                                        },
                                                        failure: function(response, opts) {
                                                            var obj = Ext.decode(response.responseText);
                                                            Ext.MessageBox.alert('error', lang('Could not connect to the database. Retry later'));
                                                        }
                                                    });
                                                }
                                            });
                                        }
                                    }]
                            }],
                        columns: [{
                                text: lang('ID'),
                                dataIndex: 'id',
                                hidden: true
                            }, {
                                text: lang('No'),
                                xtype: 'rownumberer',
                                width: '5%'
                            }, {
                                text: lang('Tanggal Kedatangan'),
                                dataIndex: 'MonitoringDate',
                                width: '15%',
                                editor: {
                                    xtype: 'datefield',
                                    id: 'mDate',
                                    format: 'Y-m-d',
                                    allowBlank: false
                                }
                            }, {
                                text: lang('Status'),
                                dataIndex: 'MonitoringStatus',
                                width: '20%',
                                editor: {
                                    xtype: 'combo',
                                    id: 'mStatus',
                                    store: mc_status_monitoring,
                                    displayField: 'label',
                                    valueField: 'label',
                                    queryMode: 'local',
                                    allowBlank: false,
                                    listeners: {
                                        change: function(combo, selection) {
                                            Ext.getCmp('mDescription').setValue('');
                                            act_nursery_status(Ext.getCmp('mStatus').getValue());
                                        }
                                    }
                                }
                            }, {
                                text: lang('Keterangan'),
                                dataIndex: 'Description',
                                width: '59%',
                                editor: {
                                    xtype: 'combo',
                                    id: 'mDescription',
                                    allowBlank: true,
                                    store: [''],
                                    hideTrigger: false,
                                    listeners: {
                                        beforequery: function(record) {
                                            record.query = new RegExp(record.query, 'i');
                                            record.forceAll = true;
                                        }
                                    }
                                }
                            }],
                        plugins: [mRowEditing],
                        listeners: {
                            'canceledit': function(editor, e, eOpts) {
                                store_nursey_monitoring.load({
                                    params: {
                                        nursery_id: Ext.getCmp('NurseryID').getValue()
                                    }
                                });
                            },
                            'edit': function(editor, e) {
                                if (Ext.getCmp('NurseryID').getValue() == '' || Ext.getCmp('NurseryID').getValue() == undefined) {
                                    Ext.Msg.alert("Alert", 'Belum ada data nursery');
                                } else {
                                    if (e.record.data.id == '') {
                                        Ext.Ajax.request({
                                            waitMsg: lang('Please wait...'),
                                            url: m_nursey + '_monitorings',
                                            method: 'POST',
                                            params: {
                                                id_nursey: Ext.getCmp('NurseryID').getValue(),
                                                MonitoringDate: e.record.data.MonitoringDate,
                                                MonitoringStatus: e.record.data.MonitoringStatus,
                                                Description: e.record.data.Description
                                            },
                                            success: function(response, opts) {
                                                var obj = Ext.decode(response.responseText);
                                                switch (obj.success) {
                                                    case true:
                                                        Ext.MessageBox.alert('Success', obj.message);
                                                        store_nursey_monitoring.load({
                                                            params: {
                                                                nursery_id: Ext.getCmp('NurseryID').getValue()
                                                            }
                                                        });
                                                        break;
                                                    default:
                                                        Ext.MessageBox.alert('Warning', obj.message);
                                                        break;
                                                }
                                            },
                                            failure: function(response, opts) {
                                                var obj = Ext.decode(response.responseText);
                                                Ext.MessageBox.alert('error', lang('Could not connect to the database. Retry later'));
                                            }
                                        });
                                    } else {
                                        Ext.MessageBox.confirm('Message', lang('Update data ini ?'), function(btn) {
                                            if (btn == 'yes') {
                                                Ext.Ajax.request({
                                                    waitMsg: lang('Please wait...'),
                                                    url: m_nursey + '_monitorings',
                                                    method: 'PUT',
                                                    params: {
                                                        id: e.record.data.id,
                                                        id_nursey: Ext.getCmp('NurseryID').getValue(),
                                                        MonitoringDate: e.record.data.MonitoringDate,
                                                        MonitoringStatus: e.record.data.MonitoringStatus,
                                                        Description: e.record.data.Description
                                                    },
                                                    success: function(response, opts) {
                                                        var obj = Ext.decode(response.responseText);
                                                        switch (obj.success) {
                                                            case true:
                                                                Ext.MessageBox.alert('Success', obj.message);
                                                                store_nursey_monitoring.load({
                                                                    params: {
                                                                        nursery_id: Ext.getCmp('NurseryID').getValue()
                                                                    }
                                                                });
                                                                break;
                                                            default:
                                                                Ext.MessageBox.alert('Warning', obj.message);
                                                                break;
                                                        }
                                                    },
                                                    failure: function(response, opts) {
                                                        var obj = Ext.decode(response.responseText);
                                                        Ext.MessageBox.alert('error', lang('Could not connect to the database. Retry later'));
                                                    }
                                                });
                                            }
                                        });
                                    }
                                }
                            },
                            itemdblclick: function() {
                                if (!m_act_update) {
                                    mRowEditing.cancelEdit();
                                } else {
                                    act_nursery_status(Ext.getCmp('mStatus').getValue());
                                }
                            }
                        }
                    },{
                        //tab nursery checklist
                        xtype: 'panel',
                        autoScroll: true,
                        width:'100%',
                        minHeight: 200,
                        title: lang('Nursery Checklist'),
                        padding: 3,
                        items:[{
                            layout:'column',
                            width:'100%',
                            border:false,
                            items:[{
                                columnWidth: 0.05,
                                padding: 2,
                                items:[{
                                    xtype: 'label',
                                    style:'font-weight:bold;font-size:11px;',
                                    text: 'No'
                                }]
                            },{
                                columnWidth: 0.5,
                                padding: 2,
                                items:[{
                                    xtype: 'label',
                                    style:'font-weight:bold;font-size:11px;',
                                    text: lang('Key Quality Attribute')
                                }]
                            },{
                                columnWidth: 0.15,
                                padding: 2,
                                items:[{
                                    xtype: 'label',
                                    style:'font-weight:bold;font-size:11px;',
                                    text: lang('Yes / No')
                                }]
                            },{
                                columnWidth: 0.3,
                                padding: 2,
                                items:[{
                                    xtype: 'label',
                                    style:'font-weight:bold;font-size:11px;',
                                    text: lang('If No, Justification')
                                }]
                            }]
                        },{
                            layout:'column',
                            width:'100%',
                            border:false,
                            items:[{
                                columnWidth: 0.05,
                                padding: 2,
                                items:[{
                                    xtype: 'label',
                                    style:'font-size:11px;line-height:31px;',
                                    text: lang('1.')
                                }]
                            },{
                                columnWidth: 0.5,
                                padding: 2,
                                items:[{
                                    xtype: 'label',
                                    style:'font-size:11px;line-height:31px;',
                                    text: lang('Location with good access to main roads')
                                }]
                            },{
                                columnWidth: 0.15,
                                padding: 2,
                                items:[{
                                    xtype: 'radiogroup',
                                    width: '100%',
                                    items:[{
                                        boxLabel: lang('Yes'),
                                        id: 'LocationCloseToCommunity1',
                                        name: 'LocationCloseToCommunity',
                                        style:'font-size:11px;',
                                        inputValue: '1',
                                        listeners:{
                                            change: function(){
                                                return false;
                                            }
                                        }
                                    },{
                                        boxLabel: lang('No'),
                                        id: 'LocationCloseToCommunity2',
                                        name: 'LocationCloseToCommunity',
                                        style:'font-size:11px;',
                                        inputValue: '2',
                                        listeners:{
                                            change: function(){
                                                return false;
                                            }
                                        }
                                    }]
                                }]
                            },{
                                columnWidth: 0.3,
                                padding: 2,
                                items:[{
                                    xtype: 'textfield',
                                    width:'100%',
                                    id: 'LocationCloseToCommunityNo',
                                    name: 'LocationCloseToCommunityNo',
                                    fieldStyle: {
                                        'fontSize' : '11px',
                                        'margin': '3px 0'
                                    }
                                }]
                            }]
                        },{
                            layout:'column',
                            width:'100%',
                            border:false,
                            items:[{
                                columnWidth: 0.05,
                                padding: 2,
                                items:[{
                                    xtype: 'label',
                                    style:'font-size:11px;line-height:31px;',
                                    text: lang('2.')
                                }]
                            },{
                                columnWidth: 0.5,
                                padding: 2,
                                items:[{
                                    xtype: 'label',
                                    style:'font-size:11px;line-height:31px;',
                                    text: lang('Flat, well drained and uniform land area')
                                }]
                            },{
                                columnWidth: 0.15,
                                padding: 2,
                                items:[{
                                    xtype: 'radiogroup',
                                    width: '100%',
                                    items:[{
                                        boxLabel: lang('Yes'),
                                        id: 'GoodLandArea1',
                                        name: 'GoodLandArea',
                                        style:'font-size:11px;',
                                        inputValue: '1',
                                        listeners:{
                                            change: function(){
                                                return false;
                                            }
                                        }
                                    },{
                                        boxLabel: lang('No'),
                                        id: 'GoodLandArea2',
                                        name: 'GoodLandArea',
                                        style:'font-size:11px;',
                                        inputValue: '2',
                                        listeners:{
                                            change: function(){
                                                return false;
                                            }
                                        }
                                    }]
                                }]
                            },{
                                columnWidth: 0.3,
                                padding: 2,
                                items:[{
                                    xtype: 'textfield',
                                    id: 'GoodLandAreaNo',
                                    name: 'GoodLandAreaNo',
                                    width:'100%',
                                    fieldStyle: {
                                        'fontSize' : '11px',
                                        'margin': '3px 0',
                                        'width': '100%'
                                    }
                                }]
                            }]
                        },{
                            layout:'column',
                            width:'100%',
                            border:false,
                            items:[{
                                columnWidth: 0.05,
                                padding: 2,
                                items:[{
                                    xtype: 'label',
                                    style:'font-size:11px;line-height:31px;',
                                    text: lang('3.')
                                }]
                            },{
                                columnWidth: 0.5,
                                padding: 2,
                                items:[{
                                    xtype: 'label',
                                    style:'font-size:11px;line-height:31px;',
                                    text: lang('Located at least 100 metres from cocoa plantations')
                                }]
                            },{
                                columnWidth: 0.15,
                                padding: 2,
                                items:[{
                                    xtype: 'radiogroup',
                                    width: '100%',
                                    items:[{
                                        boxLabel: lang('Yes'),
                                        id: 'LocationNearCocoaFarm1',
                                        name: 'LocationNearCocoaFarm',
                                        style:'font-size:11px;',
                                        inputValue: '1',
                                        listeners:{
                                            change: function(){
                                                return false;
                                            }
                                        }
                                    },{
                                        boxLabel: lang('No'),
                                        id: 'LocationNearCocoaFarm2',
                                        name: 'LocationNearCocoaFarm',
                                        style:'font-size:11px;',
                                        inputValue: '2',
                                        listeners:{
                                            change: function(){
                                                return false;
                                            }
                                        }
                                    }]
                                }]
                            },{
                                columnWidth: 0.3,
                                padding: 2,
                                items:[{
                                    xtype: 'textfield',
                                    id: 'LocationNearCocoaFarmNo',
                                    name: 'LocationNearCocoaFarmNo',
                                    width:'100%',
                                    fieldStyle: {
                                        'fontSize' : '11px',
                                        'margin': '3px 0',
                                        'width': '100%'
                                    }
                                }]
                            }]
                        },{
                            layout:'column',
                            width:'100%',
                            border:false,
                            items:[{
                                columnWidth: 0.05,
                                padding: 2,
                                items:[{
                                    xtype: 'label',
                                    style:'font-size:11px;line-height:31px;',
                                    text: lang('4.')
                                }]
                            },{
                                columnWidth: 0.5,
                                padding: 2,
                                items:[{
                                    xtype: 'label',
                                    style:'font-size:11px;line-height:31px;',
                                    text: lang('Continuous water supply available')
                                }]
                            },{
                                columnWidth: 0.15,
                                padding: 2,
                                items:[{
                                    xtype: 'radiogroup',
                                    width: '100%',
                                    items:[{
                                        boxLabel: lang('Yes'),
                                        id: 'ContinuousWaterSupply1',
                                        name: 'ContinuousWaterSupply',
                                        style:'font-size:11px;',
                                        inputValue: '1',
                                        listeners:{
                                            change: function(){
                                                return false;
                                            }
                                        }
                                    },{
                                        boxLabel: lang('No'),
                                        id: 'ContinuousWaterSupply2',
                                        name: 'ContinuousWaterSupply',
                                        style:'font-size:11px;',
                                        inputValue: '2',
                                        listeners:{
                                            change: function(){
                                                return false;
                                            }
                                        }
                                    }]
                                }]
                            },{
                                columnWidth: 0.3,
                                padding: 2,
                                items:[{
                                    xtype: 'textfield',
                                    id: 'ContinuousWaterSupplyNo',
                                    name: 'ContinuousWaterSupplyNo',
                                    width:'100%',
                                    fieldStyle: {
                                        'fontSize' : '11px',
                                        'margin': '3px 0',
                                        'width': '100%'
                                    }
                                }]
                            }]
                        },{
                            layout:'column',
                            width:'100%',
                            border:false,
                            items:[{
                                columnWidth: 0.05,
                                padding: 2,
                                items:[{
                                    xtype: 'label',
                                    style:'font-size:11px;line-height:31px;',
                                    text: lang('5.')
                                }]
                            },{
                                columnWidth: 0.5,
                                padding: 2,
                                items:[{
                                    xtype: 'label',
                                    style:'font-size:11px;line-height:31px;',
                                    text: lang('Irrigation system installed')
                                }]
                            },{
                                columnWidth: 0.15,
                                padding: 2,
                                items:[{
                                    xtype: 'radiogroup',
                                    width: '100%',
                                    items:[{
                                        boxLabel: lang('Yes'),
                                        id: 'IrrigationInstalled1',
                                        name: 'IrrigationInstalled',
                                        style:'font-size:11px;',
                                        inputValue: '1',
                                        listeners:{
                                            change: function(){
                                                return false;
                                            }
                                        }
                                    },{
                                        boxLabel: lang('No'),
                                        id: 'IrrigationInstalled2',
                                        name: 'IrrigationInstalled',
                                        style:'font-size:11px;',
                                        inputValue: '2',
                                        listeners:{
                                            change: function(){
                                                return false;
                                            }
                                        }
                                    }]
                                }]
                            },{
                                columnWidth: 0.3,
                                padding: 2,
                                items:[{
                                    xtype: 'textfield',
                                    id: 'IrrigationInstalledNo',
                                    name: 'IrrigationInstalledNo',
                                    width:'100%',
                                    fieldStyle: {
                                        'fontSize' : '11px',
                                        'margin': '3px 0',
                                        'width': '100%'
                                    }
                                }]
                            }]
                        },{
                            layout:'column',
                            width:'100%',
                            border:false,
                            items:[{
                                columnWidth: 0.05,
                                padding: 2,
                                items:[{
                                    xtype: 'label',
                                    style:'font-size:11px;line-height:31px;',
                                    text: lang('6.')
                                }]
                            },{
                                columnWidth: 0.5,
                                padding: 2,
                                items:[{
                                    xtype: 'label',
                                    style:'font-size:11px;line-height:31px;',
                                    text: lang('Use of appropriate shading')
                                }]
                            },{
                                columnWidth: 0.15,
                                padding: 2,
                                items:[{
                                    xtype: 'radiogroup',
                                    width: '100%',
                                    items:[{
                                        boxLabel: lang('Yes'),
                                        id: 'UseShadingNet1',
                                        name: 'UseShadingNet',
                                        style:'font-size:11px;',
                                        inputValue: '1',
                                        listeners:{
                                            change: function(){
                                                return false;
                                            }
                                        }
                                    },{
                                        boxLabel: lang('No'),
                                        id: 'UseShadingNet2',
                                        name: 'UseShadingNet',
                                        style:'font-size:11px;',
                                        inputValue: '2',
                                        listeners:{
                                            change: function(){
                                                return false;
                                            }
                                        }
                                    }]
                                }]
                            },{
                                columnWidth: 0.3,
                                padding: 2,
                                items:[{
                                    xtype: 'textfield',
                                    id: 'UseShadingNetNo',
                                    name: 'UseShadingNetNo',
                                    width:'100%',
                                    fieldStyle: {
                                        'fontSize' : '11px',
                                        'margin': '3px 0',
                                        'width': '100%'
                                    }
                                }]
                            }]
                        },{
                            layout:'column',
                            width:'100%',
                            border:false,
                            items:[{
                                columnWidth: 0.05,
                                padding: 2,
                                items:[{
                                    xtype: 'label',
                                    style:'font-size:11px;line-height:31px;',
                                    text: lang('7.')
                                }]
                            },{
                                columnWidth: 0.5,
                                padding: 2,
                                items:[{
                                    xtype: 'label',
                                    style:'font-size:11px;line-height:31px;',
                                    text: lang('Adequate supply of top soil or substrate for potting mix')
                                }]
                            },{
                                columnWidth: 0.15,
                                padding: 2,
                                items:[{
                                    xtype: 'radiogroup',
                                    width: '100%',
                                    items:[{
                                        boxLabel: lang('Yes'),
                                        id: 'AdequateSupplyTopSoil1',
                                        name: 'AdequateSupplyTopSoil',
                                        style:'font-size:11px;',
                                        inputValue: '1',
                                        listeners:{
                                            change: function(){
                                                return false;
                                            }
                                        }
                                    },{
                                        boxLabel: lang('No'),
                                        id: 'AdequateSupplyTopSoil2',
                                        name: 'AdequateSupplyTopSoil',
                                        style:'font-size:11px;',
                                        inputValue: '2',
                                        listeners:{
                                            change: function(){
                                                return false;
                                            }
                                        }
                                    }]
                                }]
                            },{
                                columnWidth: 0.3,
                                padding: 2,
                                items:[{
                                    xtype: 'textfield',
                                    id: 'AdequateSupplyTopSoilNo',
                                    name: 'AdequateSupplyTopSoilNo',
                                    width:'100%',
                                    fieldStyle: {
                                        'fontSize' : '11px',
                                        'margin': '3px 0',
                                        'width': '100%'
                                    }
                                }]
                            }]
                        },{
                            layout:'column',
                            width:'100%',
                            border:false,
                            items:[{
                                columnWidth: 0.05,
                                padding: 2,
                                items:[{
                                    xtype: 'label',
                                    style:'font-size:11px;line-height:31px;',
                                    text: lang('8.')
                                }]
                            },{
                                columnWidth: 0.5,
                                padding: 2,
                                items:[{
                                    xtype: 'label',
                                    style:'font-size:11px;line-height:31px;',
                                    text: lang('Improved varieties from certified seed and budwood sources')
                                }]
                            },{
                                columnWidth: 0.15,
                                padding: 2,
                                items:[{
                                    xtype: 'radiogroup',
                                    width: '100%',
                                    items:[{
                                        boxLabel: lang('Yes'),
                                        id: 'ImprovedVariety1',
                                        name: 'ImprovedVariety',
                                        style:'font-size:11px;',
                                        inputValue: '1',
                                        listeners:{
                                            change: function(){
                                                return false;
                                            }
                                        }
                                    },{
                                        boxLabel: lang('No'),
                                        id: 'ImprovedVariety2',
                                        name: 'ImprovedVariety',
                                        style:'font-size:11px;',
                                        inputValue: '2',
                                        listeners:{
                                            change: function(){
                                                return false;
                                            }
                                        }
                                    }]
                                }]
                            },{
                                columnWidth: 0.3,
                                padding: 2,
                                items:[{
                                    xtype: 'textfield',
                                    id: 'ImprovedVarietyNo',
                                    name: 'ImprovedVarietyNo',
                                    width:'100%',
                                    fieldStyle: {
                                        'fontSize' : '11px',
                                        'margin': '3px 0',
                                        'width': '100%'
                                    }
                                }]
                            }]
                        },{
                            layout:'column',
                            width:'100%',
                            border:false,
                            hidden:true,
                            items:[{
                                columnWidth: 0.05,
                                padding: 2,
                                items:[{
                                    xtype: 'label',
                                    style:'font-size:11px;line-height:31px;',
                                    text: lang('9.')
                                }]
                            },{
                                columnWidth: 0.5,
                                padding: 2,
                                items:[{
                                    xtype: 'label',
                                    style:'font-size:11px;line-height:31px;',
                                    text: lang('Construction of storing and bag-filling facilities')
                                }]
                            },{
                                columnWidth: 0.15,
                                padding: 2,
                                items:[{
                                    xtype: 'radiogroup',
                                    width: '100%',
                                    items:[{
                                        boxLabel: lang('Yes'),
                                        id: 'ConstructStoring1',
                                        name: 'ConstructStoring',
                                        style:'font-size:11px;',
                                        inputValue: '1',
                                        listeners:{
                                            change: function(){
                                                return false;
                                            }
                                        }
                                    },{
                                        boxLabel: lang('No'),
                                        id: 'ConstructStoring2',
                                        name: 'ConstructStoring',
                                        style:'font-size:11px;',
                                        inputValue: '2',
                                        listeners:{
                                            change: function(){
                                                return false;
                                            }
                                        }
                                    }]
                                }]
                            },{
                                columnWidth: 0.3,
                                padding: 2,
                                items:[{
                                    xtype: 'textfield',
                                    id: 'ConstructStoringNo',
                                    name: 'ConstructStoringNo',
                                    width:'100%',
                                    fieldStyle: {
                                        'fontSize' : '11px',
                                        'margin': '3px 0',
                                        'width': '100%'
                                    }
                                }]
                            }]
                        },{
                            layout:'column',
                            width:'100%',
                            border:false,
                            items:[{
                                columnWidth: 0.05,
                                padding: 2,
                                items:[{
                                    xtype: 'label',
                                    style:'font-size:11px;line-height:31px;',
                                    text: lang('9.')
                                }]
                            },{
                                columnWidth: 0.5,
                                padding: 2,
                                items:[{
                                    xtype: 'label',
                                    style:'font-size:11px;line-height:31px;',
                                    text: lang('Correct equipment is available to operator(s)')
                                }]
                            },{
                                columnWidth: 0.15,
                                padding: 2,
                                items:[{
                                    xtype: 'radiogroup',
                                    width: '100%',
                                    items:[{
                                        boxLabel: lang('Yes'),
                                        id: 'CorrectEquipment1',
                                        name: 'CorrectEquipment',
                                        style:'font-size:11px;',
                                        inputValue: '1',
                                        listeners:{
                                            change: function(){
                                                return false;
                                            }
                                        }
                                    },{
                                        boxLabel: lang('No'),
                                        id: 'CorrectEquipment2',
                                        name: 'CorrectEquipment',
                                        style:'font-size:11px;',
                                        inputValue: '2',
                                        listeners:{
                                            change: function(){
                                                return false;
                                            }
                                        }
                                    }]
                                }]
                            },{
                                columnWidth: 0.3,
                                padding: 2,
                                items:[{
                                    xtype: 'textfield',
                                    id: 'CorrectEquipmentNo',
                                    name: 'CorrectEquipmentNo',
                                    width:'100%',
                                    fieldStyle: {
                                        'fontSize' : '11px',
                                        'margin': '3px 0',
                                        'width': '100%'
                                    }
                                }]
                            }]
                        },{
                            layout:'column',
                            width:'100%',
                            border:false,
                            items:[{
                                columnWidth: 0.05,
                                padding: 2,
                                items:[{
                                    xtype: 'label',
                                    style:'font-size:11px;line-height:31px;',
                                    text: lang('10.')
                                }]
                            },{
                                columnWidth: 0.5,
                                padding: 2,
                                items:[{
                                    xtype: 'label',
                                    style:'font-size:11px;line-height:31px;',
                                    text: lang('Wind break installed (if needed)')
                                }]
                            },{
                                columnWidth: 0.15,
                                padding: 2,
                                items:[{
                                    xtype: 'radiogroup',
                                    width: '100%',
                                    items:[{
                                        boxLabel: lang('Yes'),
                                        id: 'WindBreakInstalled1',
                                        name: 'WindBreakInstalled',
                                        style:'font-size:11px;',
                                        inputValue: '1',
                                        listeners:{
                                            change: function(){
                                                return false;
                                            }
                                        }
                                    },{
                                        boxLabel: lang('No'),
                                        id: 'WindBreakInstalled2',
                                        name: 'WindBreakInstalled',
                                        style:'font-size:11px;',
                                        inputValue: '2',
                                        listeners:{
                                            change: function(){
                                                return false;
                                            }
                                        }
                                    }]
                                }]
                            },{
                                columnWidth: 0.3,
                                padding: 2,
                                items:[{
                                    xtype: 'textfield',
                                    id: 'WindBreakInstalledNo',
                                    name: 'WindBreakInstalledNo',
                                    width:'100%',
                                    fieldStyle: {
                                        'fontSize' : '11px',
                                        'margin': '3px 0',
                                        'width': '100%'
                                    }
                                }]
                            }]
                        },{
                            layout:'column',
                            width:'100%',
                            border:false,
                            items:[{
                                columnWidth: 0.05,
                                padding: 2,
                                items:[{
                                    xtype: 'label',
                                    style:'font-size:11px;line-height:31px;',
                                    text: lang('11.')
                                }]
                            },{
                                columnWidth: 0.5,
                                padding: 2,
                                items:[{
                                    xtype: 'label',
                                    style:'font-size:11px;line-height:31px;',
                                    text: lang('Security fence installed (if needed)')
                                }]
                            },{
                                columnWidth: 0.15,
                                padding: 2,
                                items:[{
                                    xtype: 'radiogroup',
                                    width: '100%',
                                    items:[{
                                        boxLabel: lang('Yes'),
                                        id: 'SecurityFenceInstalled1',
                                        name: 'SecurityFenceInstalled',
                                        style:'font-size:11px;',
                                        inputValue: '1',
                                        listeners:{
                                            change: function(){
                                                return false;
                                            }
                                        }
                                    },{
                                        boxLabel: lang('No'),
                                        id: 'SecurityFenceInstalled2',
                                        name: 'SecurityFenceInstalled',
                                        style:'font-size:11px;',
                                        inputValue: '2',
                                        listeners:{
                                            change: function(){
                                                return false;
                                            }
                                        }
                                    }]
                                }]
                            },{
                                columnWidth: 0.3,
                                padding: 2,
                                items:[{
                                    xtype: 'textfield',
                                    id: 'SecurityFenceInstalledNo',
                                    name: 'SecurityFenceInstalledNo',
                                    width:'100%',
                                    fieldStyle: {
                                        'fontSize' : '11px',
                                        'margin': '3px 0',
                                        'width': '100%'
                                    }
                                }]
                            }]
                        },{
                            layout:'column',
                            width:'100%',
                            border:false,
                            items:[{
                                columnWidth: 0.05,
                                padding: 2,
                                items:[{
                                    xtype: 'label',
                                    style:'font-size:11px;line-height:31px;',
                                    text: lang('12.')
                                }]
                            },{
                                columnWidth: 0.5,
                                padding: 2,
                                items:[{
                                    xtype: 'label',
                                    style:'font-size:11px;line-height:31px;',
                                    text: lang('Fertilizer used in seedling establishment')
                                }]
                            },{
                                columnWidth: 0.15,
                                padding: 2,
                                items:[{
                                    xtype: 'radiogroup',
                                    width: '100%',
                                    items:[{
                                        boxLabel: lang('Yes'),
                                        id: 'FertilizerUsed1',
                                        name: 'FertilizerUsed',
                                        style:'font-size:11px;',
                                        inputValue: '1',
                                        listeners:{
                                            change: function(){
                                                return false;
                                            }
                                        }
                                    },{
                                        boxLabel: lang('No'),
                                        id: 'FertilizerUsed2',
                                        name: 'FertilizerUsed',
                                        style:'font-size:11px;',
                                        inputValue: '2',
                                        listeners:{
                                            change: function(){
                                                return false;
                                            }
                                        }
                                    }]
                                }]
                            },{
                                columnWidth: 0.3,
                                padding: 2,
                                items:[{
                                    xtype: 'textfield',
                                    id: 'FertilizerUsedNo',
                                    name: 'FertilizerUsedNo',
                                    width:'100%',
                                    fieldStyle: {
                                        'fontSize' : '11px',
                                        'margin': '3px 0',
                                        'width': '100%'
                                    }
                                }]
                            }]
                        },{
                            layout:'column',
                            width:'100%',
                            border:false,
                            items:[{
                                columnWidth: 0.05,
                                padding: 2,
                                items:[{
                                    xtype: 'label',
                                    style:'font-size:11px;line-height:31px;',
                                    text: lang('13.')
                                }]
                            },{
                                columnWidth: 0.5,
                                padding: 2,
                                items:[{
                                    xtype: 'label',
                                    style:'font-size:11px;line-height:31px;',
                                    text: lang('Operators possess adequate skills')
                                }]
                            },{
                                columnWidth: 0.15,
                                padding: 2,
                                items:[{
                                    xtype: 'radiogroup',
                                    width: '100%',
                                    items:[{
                                        boxLabel: lang('Yes'),
                                        id: 'OperatorAdequateTraining1',
                                        name: 'OperatorAdequateTraining',
                                        style:'font-size:11px;',
                                        inputValue: '1',
                                        listeners:{
                                            change: function(){
                                                return false;
                                            }
                                        }
                                    },{
                                        boxLabel: lang('No'),
                                        id: 'OperatorAdequateTraining2',
                                        name: 'OperatorAdequateTraining',
                                        style:'font-size:11px;',
                                        inputValue: '2',
                                        listeners:{
                                            change: function(){
                                                return false;
                                            }
                                        }
                                    }]
                                }]
                            },{
                                columnWidth: 0.3,
                                padding: 2,
                                items:[{
                                    xtype: 'textfield',
                                    id: 'OperatorAdequateTrainingNo',
                                    name: 'OperatorAdequateTrainingNo',
                                    width:'100%',
                                    fieldStyle: {
                                        'fontSize' : '11px',
                                        'margin': '3px 0',
                                        'width': '100%'
                                    }
                                }]
                            }]
                        },{
                            layout:'column',
                            width:'100%',
                            border:false,
                            items:[{
                                columnWidth: 0.05,
                                padding: 2,
                                items:[{
                                    xtype: 'label',
                                    style:'font-size:11px;line-height:31px;',
                                    text: lang('14.')
                                }]
                            },{
                                columnWidth: 0.5,
                                padding: 2,
                                items:[{
                                    xtype: 'label',
                                    style:'font-size:11px;line-height:31px;',
                                    text: lang('Adequate facilities for workers, and requisite safety equipment provided')
                                }]
                            },{
                                columnWidth: 0.15,
                                padding: 2,
                                items:[{
                                    xtype: 'radiogroup',
                                    width: '100%',
                                    items:[{
                                        boxLabel: lang('Yes'),
                                        id: 'AdequateFacility1',
                                        name: 'AdequateFacility',
                                        style:'font-size:11px;',
                                        inputValue: '1',
                                        listeners:{
                                            change: function(){
                                                return false;
                                            }
                                        }
                                    },{
                                        boxLabel: lang('No'),
                                        id: 'AdequateFacility2',
                                        name: 'AdequateFacility',
                                        style:'font-size:11px;',
                                        inputValue: '2',
                                        listeners:{
                                            change: function(){
                                                return false;
                                            }
                                        }
                                    }]
                                }]
                            },{
                                columnWidth: 0.3,
                                padding: 2,
                                items:[{
                                    xtype: 'textfield',
                                    id: 'AdequateFacilityNo',
                                    name: 'AdequateFacilityNo',
                                    width:'100%',
                                    fieldStyle: {
                                        'fontSize' : '11px',
                                        'margin': '3px 0',
                                        'width': '100%'
                                    }
                                }]
                            }]
                        },{
                            layout:'column',
                            width:'100%',
                            border:false,
                            items:[{
                                columnWidth: 0.05,
                                padding: 2,
                                items:[{
                                    xtype: 'label',
                                    style:'font-size:11px;line-height:31px;',
                                    text: lang('15.')
                                }]
                            },{
                                columnWidth: 0.5,
                                padding: 2,
                                items:[{
                                    xtype: 'label',
                                    style:'font-size:11px;line-height:31px;',
                                    text: lang('Sustainable and rational pest and disease control')
                                }]
                            },{
                                columnWidth: 0.15,
                                padding: 2,
                                items:[{
                                    xtype: 'radiogroup',
                                    width: '100%',
                                    items:[{
                                        boxLabel: lang('Yes'),
                                        id: 'SustainablePestDisease1',
                                        name: 'SustainablePestDisease',
                                        style:'font-size:11px;',
                                        inputValue: '1',
                                        listeners:{
                                            change: function(){
                                                return false;
                                            }
                                        }
                                    },{
                                        boxLabel: lang('No'),
                                        id: 'SustainablePestDisease2',
                                        name: 'SustainablePestDisease',
                                        style:'font-size:11px;',
                                        inputValue: '2',
                                        listeners:{
                                            change: function(){
                                                return false;
                                            }
                                        }
                                    }]
                                }]
                            },{
                                columnWidth: 0.3,
                                padding: 2,
                                items:[{
                                    xtype: 'textfield',
                                    id: 'SustainablePestDiseaseNo',
                                    name: 'SustainablePestDiseaseNo',
                                    width:'100%',
                                    fieldStyle: {
                                        'fontSize' : '11px',
                                        'margin': '3px 0',
                                        'width': '100%'
                                    }
                                }]
                            }]
                        },{
                            layout:'column',
                            width:'100%',
                            border:false,
                            hidden:true,
                            items:[{
                                columnWidth: 0.05,
                                padding: 2,
                                items:[{
                                    xtype: 'label',
                                    style:'font-size:11px;line-height:31px;',
                                    text: lang('17.')
                                }]
                            },{
                                columnWidth: 0.5,
                                padding: 2,
                                items:[{
                                    xtype: 'label',
                                    style:'font-size:11px;line-height:31px;',
                                    text: lang('There are clone grading in nursery')
                                }]
                            },{
                                columnWidth: 0.15,
                                padding: 2,
                                items:[{
                                    xtype: 'radiogroup',
                                    width: '100%',
                                    items:[{
                                        boxLabel: lang('Yes'),
                                        id: 'CloneGrading1',
                                        name: 'CloneGrading',
                                        style:'font-size:11px;',
                                        inputValue: '1',
                                        listeners:{
                                            change: function(){
                                                return false;
                                            }
                                        }
                                    },{
                                        boxLabel: lang('No'),
                                        id: 'CloneGrading2',
                                        name: 'CloneGrading',
                                        style:'font-size:11px;',
                                        inputValue: '2',
                                        listeners:{
                                            change: function(){
                                                return false;
                                            }
                                        }
                                    }]
                                }]
                            },{
                                columnWidth: 0.3,
                                padding: 2,
                                items:[{
                                    xtype: 'textfield',
                                    id: 'CloneGradingNo',
                                    name: 'CloneGradingNo',
                                    width:'100%',
                                    fieldStyle: {
                                        'fontSize' : '11px',
                                        'margin': '3px 0',
                                        'width': '100%'
                                    }
                                }]
                            }]
                        },{
                            layout:'column',
                            width:'100%',
                            border:false,
                            items:[{
                                columnWidth: 0.05,
                                padding: 2,
                                items:[{
                                    xtype: 'label',
                                    style:'font-size:11px;line-height:31px;',
                                    text: lang('16.')
                                }]
                            },{
                                columnWidth: 0.5,
                                padding: 2,
                                items:[{
                                    xtype: 'label',
                                    style:'font-size:11px;line-height:31px;',
                                    text: lang('Seedling culling is done')
                                }]
                            },{
                                columnWidth: 0.15,
                                padding: 2,
                                items:[{
                                    xtype: 'radiogroup',
                                    width: '100%',
                                    items:[{
                                        boxLabel: lang('Yes'),
                                        id: 'SeedlingCullingDone1',
                                        name: 'SeedlingCullingDone',
                                        style:'font-size:11px;',
                                        inputValue: '1',
                                        listeners:{
                                            change: function(){
                                                return false;
                                            }
                                        }
                                    },{
                                        boxLabel: lang('No'),
                                        id: 'SeedlingCullingDone2',
                                        name: 'SeedlingCullingDone',
                                        style:'font-size:11px;',
                                        inputValue: '2',
                                        listeners:{
                                            change: function(){
                                                return false;
                                            }
                                        }
                                    }]
                                }]
                            },{
                                columnWidth: 0.3,
                                padding: 2,
                                items:[{
                                    xtype: 'textfield',
                                    id: 'SeedlingCullingDoneNo',
                                    name: 'SeedlingCullingDoneNo',
                                    width:'100%',
                                    fieldStyle: {
                                        'fontSize' : '11px',
                                        'margin': '3px 0',
                                        'width': '100%'
                                    }
                                }]
                            }]
                        },{
                            layout:'column',
                            width:'100%',
                            border:false,
                            items:[{
                                columnWidth: 0.05,
                                padding: 2,
                                items:[{
                                    xtype: 'label',
                                    style:'font-size:11px;line-height:31px;',
                                    text: lang('17.')
                                }]
                            },{
                                columnWidth: 0.5,
                                padding: 2,
                                items:[{
                                    xtype: 'label',
                                    style:'font-size:11px;line-height:31px;',
                                    text: lang('Proper input and sales records are maintained')
                                }]
                            },{
                                columnWidth: 0.15,
                                padding: 2,
                                items:[{
                                    xtype: 'radiogroup',
                                    width: '100%',
                                    items:[{
                                        boxLabel: lang('Yes'),
                                        id: 'ProperInputSalesRecord1',
                                        name: 'ProperInputSalesRecord',
                                        style:'font-size:11px;',
                                        inputValue: '1',
                                        listeners:{
                                            change: function(){
                                                return false;
                                            }
                                        }
                                    },{
                                        boxLabel: lang('No'),
                                        id: 'ProperInputSalesRecord2',
                                        name: 'ProperInputSalesRecord',
                                        style:'font-size:11px;',
                                        inputValue: '2',
                                        listeners:{
                                            change: function(){
                                                return false;
                                            }
                                        }
                                    }]
                                }]
                            },{
                                columnWidth: 0.3,
                                padding: 2,
                                items:[{
                                    xtype: 'textfield',
                                    id: 'ProperInputSalesRecordNo',
                                    name: 'ProperInputSalesRecordNo',
                                    width:'100%',
                                    fieldStyle: {
                                        'fontSize' : '11px',
                                        'margin': '3px 0',
                                        'width': '100%'
                                    }
                                }]
                            }]
                        },{
                            layout:'column',
                            width:'100%',
                            border:false,
                            items:[{
                                columnWidth: 0.05,
                                padding: 2,
                                items:[{
                                    xtype: 'label',
                                    style:'font-size:11px;line-height:31px;',
                                    text: lang('18.')
                                }]
                            },{
                                columnWidth: 0.5,
                                padding: 2,
                                items:[{
                                    xtype: 'label',
                                    style:'font-size:11px;line-height:31px;',
                                    text: lang('Seeds are pre-germinated before planting')
                                }]
                            },{
                                columnWidth: 0.15,
                                padding: 2,
                                items:[{
                                    xtype: 'radiogroup',
                                    width: '100%',
                                    items:[{
                                        boxLabel: lang('Yes'),
                                        id: 'SeedsPreGerminated1',
                                        name: 'SeedsPreGerminated',
                                        style:'font-size:11px;',
                                        inputValue: '1',
                                        listeners:{
                                            change: function(){
                                                return false;
                                            }
                                        }
                                    },{
                                        boxLabel: lang('No'),
                                        id: 'SeedsPreGerminated2',
                                        name: 'SeedsPreGerminated',
                                        style:'font-size:11px;',
                                        inputValue: '2',
                                        listeners:{
                                            change: function(){
                                                return false;
                                            }
                                        }
                                    }]
                                }]
                            },{
                                columnWidth: 0.3,
                                padding: 2,
                                items:[{
                                    xtype: 'textfield',
                                    id: 'SeedsPreGerminatedNo',
                                    name: 'SeedsPreGerminatedNo',
                                    width:'100%',
                                    fieldStyle: {
                                        'fontSize' : '11px',
                                        'margin': '3px 0',
                                        'width': '100%'
                                    }
                                }]
                            }]
                        }]
                    }]
            }],
        }],
        buttons: [{
                id: 'nprintButton',
                text: lang('Print'),
                margin: '5px',
                scale: 'large',
                ui: 's-button',
                cls: 's-green ',
                hidden: true,
                handler: function() {
                    var urlPrint = m_api + '/nursery/cetak_nursery_summary/cpg/'+Ext.getCmp('CPGId').getValue()+'/'
                    preview_cetak_surat(urlPrint);
                }
            },{
                id: 'nsaveButton',
                text: lang('Save'),
                margin: '5px',
                scale: 'large',
                ui: 's-button',
                cls: 's-blue ',
                handler: function() {
                    var form = Ext.getCmp('dataFormNurseyPenjualan').getForm();
                    var methode;
                    if (Ext.getCmp('NurseryID').getValue() != '')
                        methode = 'PUT';
                    else
                        methode = 'POST';

                    //console.log(Ext.getCmp('NurseryID').getValue());
                    //console.log(methode);

                    Ext.getCmp('Luas').setValue(nnumber_format(Ext.getCmp('Luas').getValue(), 2))
                    Ext.getCmp('Kapasitas').setValue(nnumber_format(Ext.getCmp('Kapasitas').getValue(), 2))
                    form.submit({
                        url: m_nursey,
                        method: 'POST',
                        waitMsg: lang('Sending data...'),
                        params: {CPGId: Ext.getCmp('CPGId').getValue()},
                        success: function(fp, o) {
                            //Ext.getCmp('Kapasitas').setValue(nnumber_format(Ext.getCmp('Kapasitas').getValue()))
                            /*
                            hidden dl
                            Ext.getCmp('Luas').setValue(nnumber_format(Ext.getCmp('Luas').getValue()))
                            Ext.getCmp('NurseryID').setValue(o.result.id);
                            Ext.getCmp('gnurseypenjualan').setDisabled(false);
                            */
                            if(o.result.prosesnya == 'insert'){
                                mc_combo_nurserynr.load();
                                Ext.getCmp('NurseryNr').setValue(o.result.NurseryNr);
                            }
                            Ext.MessageBox.alert('Success', lang('Data saved.'));
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
                }
            }, {
                text: lang('Delete'),
                margin: '5px',
                scale: 'large',
                ui: 's-button',
                cls: 's-grey ',
                disabled: false,
                handler: function() {
                    var cek = cekNurseryID();
                    if(cek == true){
                        Ext.MessageBox.confirm('Message', 'Do you want to delete this data ?', function(btn) {
                            if (btn == 'yes') {
                                Ext.Ajax.request({
                                    waitMsg: lang('Please Wait'),
                                    url: m_nursey,
                                    method: 'DELETE',
                                    params: {
                                        id: Ext.getCmp('NurseryID').getValue(),
                                    },
                                    success: function(response, opts) {
                                        Ext.MessageBox.alert('Success', lang('Data deleted.'));

                                        //set NurseryNr
                                        mc_combo_nurserynr.load();
                                        Ext.getCmp('NurseryNr').setValue('-1');
                                    },
                                    failure: function(response, opts) {
                                        Ext.MessageBox.alert('error', lang('Could not connect to the database. Retry later'));
                                    }
                                });
                            }
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
                handler: function() {
                    winNurseyPenjualan.hide();
                }
            }]
    });

    var winNurseyPenjualan = Ext.create('widget.window', {
        title: lang('Farmer Group Nursery Unit'),
        id: 'winNurseyPenjualan',
        closable: true,
        modal: true,
        closeAction: 'show',
        width: '80%',
        height: '90%',
        layout: 'fit',
        items: [DataFormNurseyPenjualan]
    });

    function cekNurseryID(){
        if(Ext.getCmp('NurseryID').getValue() != ""){
            return true;
        }else{
            Ext.MessageBox.show({
                title: 'Notifications',
                msg: 'No NurseryNr selected',
                buttons: Ext.MessageBox.OK,
                animateTarget: 'mb9',
                icon: 'ext-mb-info'
            });
            return false;
        }
    }

    function displayNurseryPolygon(NurseryID,NurseryNr){
        var areawindow = Ext.create('widget.window', {
            id : 'areawindow',
            title: lang('Nursery Polygon'),
            closable: true,
            modal:true,
            layout : 'fit',
            closeAction: 'destroy',
            width: '75%',
            height: 550,
            bodyPadding: 5,
            listeners: {
                close: function(cb, nv, ov) {
                    hitungAreaNurseryPolygon();
                }
            }
        });
        areawindow.center();
        areawindow.show();

        Ext.Ajax.request({
            url: m_api + '/cpg/nursery_polygon',
            method: 'GET',
            params: {
                NurseryID: NurseryID,
                NurseryNr: NurseryNr,
                lati: Ext.getCmp('Latitude').getValue(),
                longi: Ext.getCmp('Longitude').getValue(),
                hakAksesPolygon: m_hakakses_polygon
            },
            success: function(response){
                var htmlText = response.responseText;
                areawindow.update(htmlText, true);
            }
        });

    }

    function hitungAreaNurseryPolygon(){
        Ext.Ajax.request({
            url: m_api + '/cpg/nursery_update_area',
            method: 'GET',
            params: {
                NurseryID: Ext.getCmp('NurseryID').getValue(),
            },
            success: function(fp, o) {
                var r = Ext.decode(fp.responseText);
                Ext.getCmp('Latitude').setValue(r.Latitude);
                Ext.getCmp('Longitude').setValue(r.Longitude);
            }
        });
    }

    //==end compos 2

    // general form panel
    var DataForm = Ext.create('Ext.form.Panel', {
        frame: false,
        autoScroll: true,
        height: 500,
        width: '100%',
        fieldDefaults: {
            labelAlign: 'left',
            labelWidth: 120,
            anchor: '95%'
        },
        dockedItems: [{
                xtype: 'toolbar',
                id: 'toolbar_cpg',
                flex: 1,
                dock: 'top',
                //cls:'x-toolbar-garis',
                items: [{
                        xtype: 'button',
                        height: 85,
                        width: 85,
                        text: '<img src="' + varjs.config.base_url + 'img/general/training-24px.png" /> <br /> ' + lang('Training'),
                        tooltip: 'Training',
                        hidden: !m_act_training,
                        handler: function() {
                            displayFormTraining();
                        }
                    }, {
                        xtype: 'button',
                        height: 85,
                        width: 85,
                        text: '<img src="' + varjs.config.base_url + 'img/general/compost-24px.png" /> <br /> ' + lang('Compost'),
                        tooltip: 'Compost',
                        id: 'Compost',
                        hidden: !m_act_compost,
                        handler: function() {
                            displayFormCompostPenjualan();
                            Ext.getCmp('type_obj').setValue('cpg');
                            Ext.Ajax.request({
                                url: m_crud,
                                method: 'GET',
                                params: {id: Ext.getCmp('CPGId').getValue()},
                                success: function(fp, o) {
                                    var r = Ext.decode(fp.responseText);
                                    Ext.getCmp('id_obj').setValue(Ext.getCmp('CPGId').getValue());
                                    Ext.getCmp('CompostID').setValue(r.CompostID);
                                    Ext.getCmp('cGroupName').setValue(r.GroupName);
                                    Ext.getCmp('Established').setValue(r.Established);
                                    Ext.getCmp('CompostLatitude').setValue(r.CompostLatitude);
                                    Ext.getCmp('CompostLongitude').setValue(r.CompostLongitude);
                                    if (r.MesinChooper == '1')
                                        Ext.getCmp('MesinChooper').setValue(true);
                                    if (r.MesinChooper == '2')
                                        Ext.getCmp('MesinChooper2').setValue(true);
                                    if (r.RumahKompos == '1')
                                        Ext.getCmp('RumahKompos').setValue(true);
                                    if (r.RumahKompos == '2')
                                        Ext.getCmp('RumahKompos2').setValue(true);
                                    store_compost_penjualan.load({
                                        params: {
                                            compost_id: r.CompostID
                                        }
                                    });
                                }
                            })
                        }
                    }, {
                        xtype: 'button',
                        height: 85,
                        width: 85,
                        id: 'Nursery',
                        text: '<img src="' + varjs.config.base_url + 'img/general/nursery-24px.png" /> <br /> ' + lang('Nursery'),
                        tooltip: 'Nursery',
                        hidden: !m_act_nursery,
                        handler: function() {
                            displayFormNurseyPenjualan();

                            //set auto select ke item pertama
                            mc_combo_nurserynr.load();
                            Ext.getCmp('NurseryNr').setValue('-1');
                            /*
                            Hidden dl
                            Ext.Ajax.request({
                                url: m_crud,
                                method: 'GET',
                                params: {id: Ext.getCmp('CPGId').getValue()},
                                success: function(fp, o) {
                                    var r = Ext.decode(fp.responseText);
                                    Ext.getCmp('nid_obj').setValue(Ext.getCmp('CPGId').getValue());
                                    Ext.getCmp('NurseryID').setValue(r.NurseryID);
                                    Ext.getCmp('Responsible').setValue(r.Responsible);
                                    Ext.getCmp('nEstablished').setValue(r.nEstablished);
                                    Ext.getCmp('Panjang').setValue(r.Panjang);
                                    Ext.getCmp('Lebar').setValue(r.Lebar);
                                    Ext.getCmp('Luas').setValue(nnumber_format(r.Lebar * r.Panjang));
                                    Ext.getCmp('Kapasitas').setValue(nnumber_format(r.Kapasitas));
                                    Ext.getCmp('Latitude').setValue(r.Latitude);
                                    Ext.getCmp('Longitude').setValue(r.Longitude);
                                    Ext.getCmp('LatitudeDeg1').setValue(r.LatitudeDeg1);
                                    Ext.getCmp('LatitudeDeg2').setValue(r.LatitudeDeg2);
                                    Ext.getCmp('LatitudeDeg3').setValue(r.LatitudeDeg3);
                                    Ext.getCmp('LongitudeDeg1').setValue(r.LongitudeDeg1);
                                    Ext.getCmp('LongitudeDeg2').setValue(r.LongitudeDeg2);
                                    Ext.getCmp('LongitudeDeg3').setValue(r.LongitudeDeg3);
                                    if(r.CertificationStatus == 'Yes'){
                                        Ext.getCmp('NursCertBp2Ya').setValue(true);
                                        Ext.getCmp('tglCertificate').setValue(r.DateCertification);
                                        Ext.getCmp('DateAppliedCertification').setValue(r.DateAppliedCertification);
                                    }else{
                                       Ext.getCmp('NursCertBp2Tidak').setValue(true);
                                    }
                                    store_nursey_penjualan.load({
                                        params: {
                                            nursery_id: r.NurseryID
                                        }
                                    });
                                    store_nursey_monitoring.load({
                                        params: {
                                            nursery_id: r.NurseryID
                                        }
                                    });
                                }
                            })
                            $('.classuang').each(function() {
                                uangc(this)
                            })
                            */
                        }
                    }, {
                        xtype: 'button',
                        height: 85,
                        width: 85,
                        id: 'Demoplot',
                        text: '<img src="' + varjs.config.base_url + 'img/general/demoplot.png"  height="44" width="44" /> <br /> ' + lang('Demoplot'),
                        tooltip: 'Demoplot',
                        hidden: !m_act_demoplot,
                        handler: function() {
                            displayFormDemoplot();
//                    Ext.Ajax.request({
//                        url: m_demoplot + "s",
//                        method: 'GET',
//                        params: {cpg_id: Ext.getCmp('CPGId').getValue()},
//                        success: function (fp, o) {
//                            var r = Ext.decode(fp.responseText);
//                            store_demoplot.load({
//                                params: {
//                                    cpg_id: r.id
//                                }
//                            });
//                        }
//                    })
                        }
                    }]
            }],
        items: [{
                xtype: 'tabpanel',
                flex: 1,
                margin: 2,
                activeTab: 0,
                plain: true,
                items: [{
                        xtype: 'panel',
                        autoScroll: true,
                        title: lang('Data Umum'),
                        padding: 5,
                        style: 'border:2px solid #D6EDA4',
                        items: [{
                                layout: 'column',
                                border: false,
                                items: [{
                                        columnWidth: .5,
                                        layout: 'form',
                                        border: false,
                                        padding: 5,
                                        items: [{
                                                xtype: 'textfield',
                                                id: 'id',
                                                name: 'id',
                                                inputType: 'hidden'
                                            }, {
                                                xtype: 'textfield',
                                                fieldLabel: lang('Farmer Group ID'),
                                                id: 'CPGId',
                                                name: 'CPGId',
                                                readOnly: true
                                            }, {
                                                xtype: 'textfield',
                                                fieldLabel: lang('Nama Kelompok'),
                                                id: 'GroupName',
                                                name: 'GroupName'
                                            }, {
                                                xtype: 'textfield',
                                                fieldLabel: lang('Alamat'),
                                                id: 'Address',
                                                name: 'Address'
                                            }, {
                                                id: 'Provinsi',
                                                name: 'Provinsi',
                                                xtype: 'combo',
                                                fieldLabel: lang('Provinsi'),
                                                store: mc_Provinsi,
                                                displayField: 'label',
                                                valueField: 'label',
                                                queryMode: 'local',
                                                disabled: m_ProvinceID?true:false,
                                                listeners: {
                                                    change: function(cb, nv, ov) {
                                                        mc_Kabupaten.load({
                                                            params: {
                                                                key: Ext.getCmp('Provinsi').getValue()
                                                            }
                                                        });
                                                        //Ext.getCmp('Kabupaten').enable();
                                                    }
                                                }
                                            }, {
                                                fieldLabel: lang('Pengurus'),
                                                id: 'radio_pengurus',
                                                xtype: 'radiogroup',
                                                items: [{
                                                        name: 'AdaPengurus',
                                                        id: 'AdaPengurus',
                                                        boxLabel: lang('Ada'),
                                                        inputValue: '1'
                                                    }, {
                                                        name: 'AdaPengurus',
                                                        id: 'AdaPengurus2',
                                                        boxLabel: lang('Tidak Ada'),
                                                        inputValue: '0'
                                                    }],
                                                listeners: {
                                                    'change': function(fb, v) {
                                                        if (Ext.getCmp('AdaPengurus2').getValue())
                                                            Ext.getCmp('pengurus').disable()
                                                        else if (Ext.getCmp('AdaPengurus').getValue())
                                                            Ext.getCmp('pengurus').enable()
                                                    }
                                                }
                                            }, {
                                                xtype: 'fieldset',
                                                id: 'pengurus',
                                                disabled: true,
                                                items: [{
                                                        id: 'ketua',
                                                        name: 'ketua',
                                                        xtype: 'combo',
                                                        fieldLabel: lang('Ketua'),
                                                        store: mc_demo_plot,
                                                        displayField: 'label',
                                                        valueField: 'id',
                                                        queryMode: 'local',
                                                    }, {
                                                        id: 'sekretaris',
                                                        name: 'sekretaris',
                                                        fieldLabel: lang('Sekretaris'),
                                                        xtype: 'combo',
                                                        store: mc_demo_plot,
                                                        displayField: 'label',
                                                        valueField: 'id',
                                                        queryMode: 'local',
                                                    }, {
                                                        id: 'bendahara',
                                                        name: 'bendahara',
                                                        xtype: 'combo',
                                                        store: mc_demo_plot,
                                                        fieldLabel: lang('Bendahara'),
                                                        displayField: 'label',
                                                        valueField: 'id',
                                                        queryMode: 'local',
                                                    }]
                                            }]
                                    }, {
                                        columnWidth: .5,
                                        layout: 'form',
                                        border: false,
                                        padding: 5,
                                        items: [{
                                                id: 'Kabupaten',
                                                name: 'Kabupaten',
                                                xtype: 'combo',
                                                fieldLabel: lang('Kabupaten'),
                                                store: mc_Kabupaten,
                                                displayField: 'label',
                                                valueField: 'label',
                                                queryMode: 'local',
                                                disabled: m_DistrictID?true:false,
                                                listeners: {
                                                    change: function(cb, nv, ov) {
                                                        mc_Kecamatan.load({
                                                            params: {
                                                                key: Ext.getCmp('Kabupaten').getValue()
                                                            },
                                                            callback: function() {
                                                                if (m_SubDistrict) Ext.getCmp('Kecamatan').setValue(m_SubDistrict);
                                                            }
                                                        });
                                                        // Ext.getCmp('Kecamatan').enable();
                                                    }
                                                }
                                            }, {
                                                xtype: 'combo',
                                                store: storeThn,
                                                displayField: 'tahun',
                                                valueField: 'tahun',
                                                fieldLabel: lang('Tahun Terbentuk'),
                                                queryMode: 'local',
                                                id: 'TahunTerbentuk',
                                                name: 'TahunTerbentuk'
                                            }, {
                                                id: 'Kecamatan',
                                                name: 'Kecamatan',
                                                xtype: 'combo',
                                                fieldLabel: lang('Kecamatan'),
                                                store: mc_Kecamatan,
                                                displayField: 'label',
                                                valueField: 'label',
                                                queryMode: 'local',
                                                disabled: m_SubDistrictID?true:false,
                                                listeners: {
                                                    change: function(cb, nv, ov) {
                                                        mc_Desa.load({
                                                            params: {
                                                                key: Ext.getCmp('Kecamatan').getValue()
                                                            }
                                                        });
                                                        Ext.getCmp('Desa').enable();
                                                    }
                                                }
                                            }, {
                                                id: 'Desa',
                                                name: 'Desa',
                                                xtype: 'combo',
                                                fieldLabel: lang('Desa'),
                                                store: mc_Desa,
                                                displayField: 'label',
                                                disabled: true,
                                                valueField: 'id',
                                                queryMode: 'local'
                                            }, {
                                                xtype: 'label',
                                                text: lang('Lokasi Pertemuan'),
                                                cls: 'x-form-item-label'
                                            }, {
                                                id: 'PertemuanLatitude',
                                                name: 'PertemuanLatitude',
                                                xtype: 'textfield',
                                                fieldLabel: lang('Latitude'),
                                                readOnly: m_hakakses_lat_short
                                            }, {
                                                xtype: 'textfield',
                                                fieldLabel: lang('Longitude'),
                                                id: 'PertemuanLongitude',
                                                name: 'PertemuanLongitude',
                                                readOnly: m_hakakses_long_short
                                            }, {
                                                xtype: 'radiogroup',
                                                fieldLabel: lang('Unit pembelian'),
                                                disabled: true,
                                                items: [{
                                                        name: 'bu',
                                                        id: 'bu',
                                                        boxLabel: lang('ya'),
                                                        inputValue: '1'
                                                    }, {
                                                        name: 'bu',
                                                        id: 'bu2',
                                                        boxLabel: lang('Tidak'),
                                                        inputValue: '2'
                                                    }]
                                            }]
                                    }]
                            }]
                    }, {
                        xtype: 'panel',
                        autoScroll: true,
                        id: 'panel_staff',
                        disabled: true,
                        title: lang('Staff'),
                        padding: 5,
                        style: 'border:2px solid #D6EDA4',
                        items: [{
                                xtype: 'gridpanel',
                                id: 'grid_staff',
                                store: store_staff_cpg,
                                width: '100%',
                                loadMask: true,
                                selType: 'rowmodel',
                                dockedItems: [{
                                        xtype: 'toolbar',
                                        items: [{
                                                icon: varjs.config.base_url + 'images/icons/new/add.png', cls:'Sfr_BtnGridGreen', overCls:'Sfr_BtnGridGreen-Hover',
                                                text: lang('Add'),
                                                // cls: m_act_save,
                                                hidden: !m_act_add,
                                                hidden:true,
                                                scope: this,
                                                handler: function() {
                                                    sRowEditing.cancelEdit();
                                                    var r = Ext.create('staff.Model', {
                                                        StaffID: '',
                                                        CPGid: '',
                                                        Status: '',
                                                        FarmerID: '',
                                                        StaffName: '',
                                                        Position: '',
                                                        Phone: '',
                                                        Email: '',
                                                        StaffBirthday: '',
                                                        StaffGender: ''
                                                    });
                                                    store_staff_cpg.insert(0, r);
                                                    sRowEditing.startEdit(0, 0);
                                                }
                                            }, {
                                                icon: varjs.config.base_url + 'images/icons/new/update.png',
                                                // cls: m_act_save,
                                                hidden: !m_act_update,
                                                hidden:true,
                                                text: lang('Edit'),
                                                scope: this,
                                                handler: function() {
                                                    sRowEditing.cancelEdit();
                                                    var sm = Ext.getCmp('grid_staff').getSelectionModel().getSelection();
                                                    sRowEditing.startEdit(sm[0].index, 0);
                                                    gs_edit()
                                                }
                                            }, {
                                                itemId: 'remove',
                                                icon: varjs.config.base_url + 'images/icons/new/delete.png',
                                                text: lang('Hapus'),
                                                hidden: !m_act_delete,
                                                hidden:true,
                                                scope: this,
                                                handler: function() {
                                                    var smb = Ext.getCmp('grid_staff').getSelectionModel().getSelection()[0];
                                                    sRowEditing.cancelEdit();
                                                    Ext.MessageBox.confirm('Message', lang('Apakah anda mau menghapus staff ini ?'), function(btn) {
                                                        if (btn == 'yes') {
                                                            Ext.Ajax.request({
                                                                waitMsg: lang('Please Wait'),
                                                                url: m_staff,
                                                                method: 'DELETE',
                                                                params: {
                                                                    id: smb.raw.StaffID
                                                                },
                                                                success: function(response, opts) {
                                                                    var obj = Ext.decode(response.responseText);
                                                                    switch (obj.success) {
                                                                        case true:
                                                                            store_staff_cpg.load({
                                                                                params: {
                                                                                    id: Ext.getCmp('CPGId').getValue()
                                                                                }
                                                                            });
                                                                            break;
                                                                        default:
                                                                            Ext.MessageBox.alert('Warning', obj.message);
                                                                            break;
                                                                    }
                                                                },
                                                                failure: function(response, opts) {
                                                                    var obj = Ext.decode(response.responseText);
                                                                    Ext.MessageBox.alert('error', lang('Could not connect to the database. Retry later'));
                                                                }
                                                            });
                                                        }
                                                    });
                                                }
                                            }]
                                    }],
                                columns: [{
                                        text: lang('Status'),
                                        id: 'lstatus',
                                        dataIndex: 'Status',
                                        width: '10%',
                                        editor: {
                                            xtype: 'combo',
                                            store: cfarmer,
                                            id: 'farmer',
                                            queryMode: 'local',
                                            displayField: 'label',
                                            valueField: 'label',
                                            listeners: {
                                                change: function(combo, selection) {
                                                    gs_edit();
                                                }
                                            }
                                        }
                                    }, {
                                        text: lang('Nama'),
                                        id: 'lfarmer',
                                        dataIndex: 'StaffName',
                                        width: '35%',
                                        editor: {
                                            xtype: 'combo',
                                            store: ds,
                                            id: 'lfarmerid',
                                            displayField: 'name',
                                            typeAhead: false,
                                            hideLabel: true,
                                            hideTrigger: true,
                                            anchor: '100%',
                                            listConfig: {
                                                loadingText: 'Searching...',
                                                emptyText: 'No matching farmer found.',
                                                getInnerTpl: function() {
                                                    return '<div class="search-item">' +
                                                            '{id} - {name}' +
                                                            '{excerpt}' +
                                                            '</div>';
                                                }
                                            },
                                            pageSize: 10,
                                            listeners: {
                                                select: function(combo, selection) {
                                                    var post = selection[0];
                                                    if (post) {
                                                        Ext.getCmp('lfarmerid').setValue('[' + post.get('id') + '] ' + post.get('name'))
                                                        Ext.getCmp('namanon').setValue(post.get('id'))
                                                        Ext.getCmp('lhp').setValue(post.get('handphone'))
                                                        Ext.getCmp('lhp').setReadOnly(true)
                                                        Ext.getCmp('lemail').setValue(post.get('email'))
                                                        //Ext.getCmp('lemail').setReadOnly(true)
                                                        Ext.getCmp('StaffGender').setValue(post.get('kelamin'))
                                                        Ext.getCmp('StaffGender').setReadOnly(true)
                                                        console.log(post.get('birthdate'))
                                                        Ext.getCmp('lbirthday').setValue(post.get('birthdate'))
                                                        Ext.getCmp('lbirthday').setReadOnly(true)
                                                    }
                                                }
                                            }
                                        }
                                    }, {
                                        text: lang('Nama'),
                                        id: 'lnon',
                                        dataIndex: 'StaffName',
                                        width: '30%',
                                        hidden: true,
                                        editor: {
                                            xtype: 'textfield',
                                            id: 'namanon',
                                            name: 'namanon',
                                        }
                                    }, {
                                        text: lang('Position'),
                                        dataIndex: 'Position',
                                        width: '15%',
                                        editor: {
                                            xtype: 'combo',
                                            store: cposition,
                                            id: 'Position',
                                            queryMode: 'local',
                                            displayField: 'label',
                                            valueField: 'label'
                                        }
                                    }, {
                                        text: lang('Handphone'),
                                        dataIndex: 'Phone',
                                        width: '10%',
                                        editor: {
                                            id: 'lhp',
                                            xtype: 'textfield'
                                        }
                                    }, {
                                        text: lang('Email'),
                                        dataIndex: 'Email',
                                        width: '10%',
                                        editor: {
                                            xtype: 'textfield',
                                            id: 'lemail',
                                            allowBlank: false
                                        }
                                    }, {
                                        text: lang('Birthday'),
                                        dataIndex: 'StaffBirthday',
                                        width: '10%',
                                        editor: {
                                            xtype: 'datefield',
                                            id: 'lbirthday',
                                            format: 'Y-m-d'
                                        }
                                    }, {
                                        text: lang('Kelamin'),
                                        dataIndex: 'StaffGender',
                                        width: '10%',
                                        editor: {
                                            xtype: 'combo',
                                            store: ckelamin,
                                            queryMode: 'local',
                                            id: 'StaffGender',
                                            displayField: 'label',
                                            valueField: 'id'
                                        }
                                    }],
                                //plugins: [sRowEditing],
                                listeners: {
                                    itemdblclick: function(dv, record, item, index, e) {
                                        if (!m_act_update) {
                                            sRowEditing.canceledit();
                                        } else {
                                            gs_edit()
                                        }
                                    },
                                    'canceledit': function(editor, e, eOpts) {
                                        store_staff_cpg.load({
                                            params: {
                                                id: Ext.getCmp('CPGId').getValue()
                                            }
                                        });
                                    },
                                    'edit': function(editor, e) {
                                        if (e.record.data.StaffID == '') {
                                            Ext.Ajax.request({
                                                waitMsg: lang('Please wait...'),
                                                url: m_staff,
                                                method: 'POST',
                                                params: {
                                                    CPGid: Ext.getCmp('CPGId').getValue(),
                                                    Status: e.record.data.Status,
                                                    FarmerID: Ext.getCmp('namanon').getValue(),
                                                    Position: e.record.data.Position,
                                                    StaffName: e.record.data.StaffName,
                                                    Phone: e.record.data.Phone,
                                                    Email: e.record.data.Email,
                                                    StaffBirthday: e.record.data.StaffBirthday,
                                                    StaffGender: Ext.getCmp('StaffGender').getValue(),
                                                },
                                                success: function(response, opts) {
                                                    var obj = Ext.decode(response.responseText);
                                                    switch (obj.success) {
                                                        case true:
                                                            Ext.MessageBox.alert('Success', obj.message);
                                                            store_staff_cpg.load({
                                                                params: {
                                                                    id: Ext.getCmp('CPGId').getValue()
                                                                }
                                                            });
                                                            break;
                                                        default:
                                                            Ext.MessageBox.alert('Warning', obj.message);
                                                            break;
                                                    }
                                                },
                                                failure: function(response, opts) {
                                                    var obj = Ext.decode(response.responseText);
                                                    Ext.MessageBox.alert('error', lang('Could not connect to the database. Retry later'));
                                                }
                                            });
                                        } else {
                                            Ext.MessageBox.confirm('Message', lang('Update data staff ini ?'), function(btn) {
                                                if (btn == 'yes') {
                                                    Ext.Ajax.request({
                                                        waitMsg: lang('Please wait...'),
                                                        url: m_staff,
                                                        method: 'PUT',
                                                        params: {
                                                            StaffID: e.record.data.StaffID,
                                                            CPGid: Ext.getCmp('CPGId').getValue(),
                                                            Status: e.record.data.Status,
                                                            FarmerID: Ext.getCmp('namanon').getValue(),
                                                            Position: e.record.data.Position,
                                                            StaffName: e.record.data.StaffName,
                                                            Phone: e.record.data.Phone,
                                                            Email: e.record.data.Email,
                                                            StaffBirthday: e.record.data.StaffBirthday,
                                                            StaffGender: Ext.getCmp('StaffGender').getValue(),
                                                        },
                                                        success: function(response, opts) {
                                                            var obj = Ext.decode(response.responseText);
                                                            switch (obj.success) {
                                                                case true:
                                                                    Ext.MessageBox.alert('Success', obj.message);
                                                                    store_staff_cpg.load({
                                                                        params: {
                                                                            id: Ext.getCmp('CPGId').getValue()
                                                                        }
                                                                    });
                                                                    break;
                                                                default:
                                                                    Ext.MessageBox.alert('Warning', obj.message);
                                                                    break;
                                                            }
                                                        },
                                                        failure: function(response, opts) {
                                                            var obj = Ext.decode(response.responseText);
                                                            Ext.MessageBox.alert('error', lang('Could not connect to the database. Retry later'));
                                                        }
                                                    });
                                                }
                                            });
                                        }
                                    }
                                }
                            }]
                    }, {
                        xtype: 'panel',
                        //autoScroll: true,
                        id: 'panel_anggota',
                        title: lang('Anggota'),
                        padding: 5,
                        style: 'border:2px solid #D6EDA4',
                        items: [{
                                xtype: 'gridpanel',
                                id: 'grid_anggota',
                                store: store_member_cpg,
                                width: '100%',
                                loadMask: true,
                                selType: 'rowmodel',
                                columns: [{
                                        text: lang('Farmer ID'),
                                        dataIndex: 'FarmerID',
                                        width: '10%'
                                    }, {
                                        text: lang('Nama'),
                                        dataIndex: 'FarmerName',
                                        width: '20%'
                                    }, {
                                        text: lang('Gender'),
                                        dataIndex: 'cpgMemberGender',
                                        width: '10%',
                                        renderer: function(value, metaData, record, row, col, store, gridView){
                                            return lang(value);
                                        }
                                    }, {
                                        text: lang('Village'),
                                        dataIndex: 'cpgMemberVillage',
                                        width: '15%'
                                    }, {
                                        text: lang('Age'),
                                        dataIndex: 'cpgMemberAge',
                                        align: 'left',
                                        width: '15%'
                                    }, {
                                        text: lang('Nr of Cocoa Garden'),
                                        dataIndex: 'garden_count',
                                        align: 'center',
                                        width: '15%'
                                    }, {
                                        text: lang('Total area Cocoa Garden'),
                                        dataIndex: 'garden_ha',
                                        align: 'center',
                                        width: '15%'
                                    },
                                    ]
                            }]
                    }]
            }],
        buttons: [{
                id: 'saveButton',
                text: lang('Save'),
                margin: '5px',
                scale: 'large',
                ui: 's-button',
                cls: 's-blue ',
                handler: function() {
                    var form = this.up('form').getForm();
                    var methode;
                    if (Ext.getCmp('id').getValue() != '')
                        methode = 'PUT';
                    else
                        methode = 'POST';
                    form.submit({
                        url: m_crud,
                        method: methode,
                        waitMsg: lang('Sending data...'),
                        success: function(fp, o) {
                            Ext.MessageBox.alert('Success', lang('Data saved.'));
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
                    win.hide(this, function() {
                        store.load({
                            params: {
                                key: Ext.getCmp('key').getValue(),
                                // kab: Ext.getCmp('Kab').getValue()
                            }
                        });
                    });
                }
            }, {
                text: lang('Close'),
                margin: '5px',
                scale: 'large',
                ui: 's-button',
                cls: 's-grey',
                disabled: false,
                handler: function() {
                    win.hide();
                }
            }]
    });
    var win = Ext.create('widget.window', {
        id: 'win',
        title: lang('Data Farmer Group'),
        closable: true,
        modal: false,
        closeAction: 'show',
        width: '80%',
        height: '90%',
        layout: {
            type: 'fit'
        },
        items: [DataForm]
    });
    var store_CekSurvey = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'surveya'],
        autoLoad: true,
        proxy: {
            type: 'ajax',
            url: m_CekSurvey,
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });

    var store_DayNumber = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id'],
        autoLoad: true,
        proxy: {
            type: 'ajax',
            url: m_DayNumber,
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });

    function submitOnEnter(field, event) {
        if (event.getKey() == event.ENTER) {
            store.load({
                params: {
                    key: Ext.getCmp('key').getValue(),
                    // kab: Ext.getCmp('Kab').getValue()
                }
            });
        }
    }

    function fupdate(sm) {
        Ext.getCmp('panel_staff').enable()
        Ext.getCmp('toolbar_cpg').show()
        Ext.getCmp('radio_pengurus').enable()
        displayFormWindow();
        Ext.Ajax.request({
            url: m_crud,
            method: 'GET',
            params: {id: sm.get('id')},
            success: function(fp, o) {
                var r = Ext.decode(fp.responseText);
                Ext.getCmp('id').setValue(sm.get('id'));

                Ext.getCmp('CPGId').setValue(sm.get('id'));
                Ext.getCmp('GroupName').setValue(r.GroupName);
                Ext.getCmp('Address').setValue(r.Address);
                Ext.getCmp('TahunTerbentuk').setValue(r.TahunTerbentuk);
                if (r.RegionalCd != '') {
                    Ext.getCmp('Kecamatan').setValue(r.Kecamatan);
                    Ext.getCmp('Desa').setValue(r.RegionID);
                }
                if (r.Provinsi) Ext.getCmp('Provinsi').setValue(r.Provinsi);
                if (r.Kabupaten) Ext.getCmp('Kabupaten').setValue(r.Kabupaten);
                if (r.AdaPengurus == '1')
                    Ext.getCmp('AdaPengurus').setValue(true);
                else if (r.AdaPengurus == '0')
                    Ext.getCmp('AdaPengurus2').setValue(true);
                Ext.getCmp('ketua').setValue(r.Ketua);
                Ext.getCmp('sekretaris').setValue(r.Sekretaris);
                Ext.getCmp('bendahara').setValue(r.Bendahara);
                Ext.getCmp('PertemuanLatitude').setValue(r.PertemuanLatitude);
                Ext.getCmp('PertemuanLongitude').setValue(r.PertemuanLongitude);
                if (r.bu == '1')
                    Ext.getCmp('bu').setValue(true);
                else if (r.bu == '2')
                    Ext.getCmp('bu2').setValue(true);
                store_staff_cpg.load({
                    params: {
                        id: Ext.getCmp('CPGId').getValue()
                    }
                });
                ds.getProxy().setExtraParam("CPGid", Ext.getCmp('CPGId').getValue());
                hideSave();
            }
        });
        mc_demo_plot.load({
            params: {
                cpg: sm.get('id')
            }
        });
        store_member_cpg.load({
            params: {
                cpg: sm.get('id')
            }
        });
    }

    var mc_sub_topic = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'label'],
        autoLoad: false,
        proxy: {
            type: 'ajax',
            url: m_api + '/cpg/training_subtopic',
            reader: {
                type: 'json',
                root: 'data'
            }
        },
        listeners: {
            'beforeload': function(store, options) {
                store.proxy.extraParams.CpgTrainingsID = Ext.getCmp('CpgTrainingsID').getValue();
            }
        }
    });

    function setFormTrainingValue (sm) {
        store_participant.load({
            params: {
                key: sm.get('id')
            }
        });
        if (sm.get('id') != undefined) {
            displayFormWindowParticipant();
            hideSave();
            CpgBatchTrainingID = sm.get('id');
            mc_family_training.load({
                params: {
                    id: sm.get('KeyFarmerID')
                }
            });
            Ext.getCmp('idd').setValue(sm.get('CPGID'));
            Ext.getCmp('idt').setValue(sm.get('id'));
            Ext.getCmp('batch').setValue(sm.get('CpgBatchID'));
            Ext.getCmp('CpgTrainingsID').setValue(sm.get('CpgTrainingsID'));
            Ext.getCmp('TrainingStart').setValue(sm.get('TrainingStart'));
            Ext.getCmp('TrainingEnd').setValue(sm.get('TrainingEnd'));
            if (sm.get('TrainingDays') != '0')
                Ext.getCmp('TrainingDays').setValue(sm.get('TrainingDays'));
                store_DayNumber.load({
                    params: {
                        dayNumber: sm.get('TrainingDays')
                    }
                });

            if(sm.get('TrainingDayStatus') == 'half')
                Ext.getCmp('TrainingDayStatusHalf').setValue(true);
            if(sm.get('TrainingDayStatus') == 'full')
                Ext.getCmp('TrainingDayStatusFull').setValue(true);

            mc_sub_topic.load({
                callback : function(records, options, success) {
                    if(sm.get('subtopic') != null){
                        var setSubtopic = sm.get('subtopic').split('@');
                        console.log(setSubtopic);
                        Ext.getCmp('CpgTrainingsIDSubTopic').setValue(setSubtopic);
                    }else{
                        Ext.getCmp('CpgTrainingsIDSubTopic').setValue([]);
                    }
                }
            });

            if (sm.get('PetaniKakao') == '1')
                Ext.getCmp('PetaniKakao').setValue(true);
            if (sm.get('PetaniKakao') == '2')
                Ext.getCmp('PetaniKakao2').setValue(true);
            if (sm.get('FamilyID') != '0')
                Ext.getCmp('FamilyID').setValue(sm.get('FamilyID'));
            if (sm.get('ProgramStaffID') != '0')
                Ext.getCmp('ProgramStaffID').setValue(sm.get('ProgramStaffID'));
            if (sm.get('ExtensionStaffID') != '0')
                Ext.getCmp('ExtensionStaffID').setValue(sm.get('ExtensionStaffID'));
            if (sm.get('KeyFarmerID') != '0')
                Ext.getCmp('KeyFarmerID').setValue(sm.get('KeyFarmerID'));
            if (sm.get('DemoplotOwnerID') != '0')
                Ext.getCmp('DemoplotOwnerID').setValue(sm.get('DemoplotOwnerID'));
            /*if (sm.get('CpgTrainingsID') == '2')
                Ext.getCmp('t_n1').show();
            else
                Ext.getCmp('t_n1').hide();
           */
           Ext.getCmp('parcheklistday_cpgbatchtrainingid').setValue(sm.get('id'));
           Ext.getCmp('parcheklistday_groupname').setValue(sm.get('GroupName'));
           Ext.getCmp('parcheklistday_batch').setValue(sm.get('BatchNumber'));
           Ext.getCmp('parcheklistday_training_name').setValue(sm.get('label'));
           Ext.getCmp('parcheklistday_startdate').setValue(Ext.Date.format(new Date(sm.get('TrainingStart')), 'Y-m-d'));
           Ext.getCmp('parcheklistday_enddate').setValue(Ext.Date.format(new Date(sm.get('TrainingEnd')), 'Y-m-d'));
           Ext.getCmp('parcheklistday_daycount').setValue(sm.get('TrainingDays'));
        }
    }

    //============================================== Advanced Filter (Begin) =======================================//
    var cmbAdvFilter = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'label'],
        data: [
            {"id": "nameId", "label": lang('Cari berdasar nama/ID')},
            // {"id": "district", "label": lang('District')},
            {"id": "batch", "label": lang('batch')},
            {"id": "nursery", "label": lang('Nursery')}
        ]
    });

    var cmbAdvFilterDistrict = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'label'],
        autoLoad: false,
        proxy: {
            type: 'ajax',
            url: m_Kabupaten,
            extraParams: {
                prov: m_param
            },
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });

    var cmbAdvFilterBatchTraining = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'label'],
        autoLoad: true,
        proxy: {
            type: 'ajax',
            url: m_batch_training_search,
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });

    var cmbAdvYesNo = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'label'],
        data: [
            {"id": "1", "label": lang('Yes')},
            {"id": "2", "label": lang('No')}
        ]
    });

    var panelAdvFilter = Ext.create('Ext.panel.Panel', {
        id: 'idPanelAdvFilter',
        //bodyPadding: 5,  // Don't want content to crunch against the borders
        width: '100%',
        title: 'Advanced Filter',
        cls: 'panelAdvFilter',
        renderTo: 'ext-content',
        style: 'border:1px solid #CCC;margin-bottom:10px;',
        layout: {
            type: 'vbox',
            align: 'left'
        },
        items: [{
            xtype:'container',
            id:'rowFilter',
            cls: 'x-table-layout-cell-top-align',
            layout:{
                type:'table',
                columns:3
            },
            width:'100%',
            margin:'10px 0 0 12px',
            items: [{
                xtype: 'label',
                text: 'Add Filter',
                margin:'5px 171px 0 5px',
                style:'line-height:15px;'
            },{
                xtype: 'boxselect',
                width: 350,
                margin:'0 0 0 0',
                id: 'cmbAdvFilter',
                name: 'cmbAdvFilter[]',
                store: cmbAdvFilter,
                displayField: 'label',
                valueField: 'id',
                queryMode: 'local',
                stacked: true,
                pinList: false,
                triggerOnClick: false,
                filterPickList: true
            },{
                xtype: 'button',
                width: 100,
                margin: '0px 0px 0px 18px',
                text: 'Reload Filter',
                handler: function(){
                    hideAllElementAdvFilter();

                    var filterDipilih = Ext.getCmp('cmbAdvFilter').getValue(); //array
                    for (var i = 0; i < filterDipilih.length; i++) {
                        switch(filterDipilih[i]){
                            case 'nameId':
                                Ext.getCmp('rowNameId').setVisible(true);
                            break;
                            case 'district':
                                Ext.getCmp('rowDistrict').setVisible(true);
                            break;
                            case 'batch':
                                Ext.getCmp('rowBatch').setVisible(true);
                            break;
                            case 'nursery':
                                Ext.getCmp('rowNursery').setVisible(true);
                            break;
                        }
                    }
                }
            }]
        },{
            xtype: 'box',
            width:'100%',
            autoEl : {
               tag : 'hr'
            },
            style:'border:1px solid #EFF0F1;margin:10px 0px;padding:0px;'
        },{
            xtype:'container',
            id:'rowNameId',
            layout:'column',
            width:'100%',
            margin:'10px 0 0 12px',
            height:30,
            items:[
               {
                  xtype: 'label',
                  text: lang('Cari berdasar nama/ID'),
                  margin:'5px 0px 0 0',
                  width: 225
               },{
                  name: 'advNameId',
                  id: 'advNameId',
                  xtype: 'textfield',
                  width:450
               }
            ]
        },{
            xtype:'container',
            id:'rowDistrict',
            layout:'column',
            width:'100%',
            margin:'10px 0 0 12px',
            height:30,
            items: [
               {
                  xtype: 'label',
                  text: lang('District'),
                  margin:'5px 0px 0 0',
                  width: 225
               },{
                  xtype: 'combo',
                  width: 200,
                  listConfig: {
                     cls: 'x-boundlist-item comboAdvFilterItemList'
                  },
                  id: 'advDistrict',
                  name: 'advDistrict[]',
                  store: cmbAdvFilterDistrict,
                  displayField: 'label',
                  valueField: 'id',
                  queryMode: 'local',
                  selectOnFocus: true,
                  multiSelect: true,
                  editable: false
               }
            ]
        },{
            xtype:'container',
            id:'rowBatch',
            layout:'column',
            width:'100%',
            margin:'10px 0 0 12px',
            height:30,
            items: [
               {
                  xtype: 'label',
                  text: lang('batch'),
                  margin:'5px 0px 0 0',
                  width:225
               },{
                  xtype: 'combo',
                  width: 300,
                  listConfig: {
                     cls: 'x-boundlist-item comboAdvFilterItemList'
                  },
                  id: 'advBatch',
                  name: 'advBatch',
                  store: cmbAdvFilterBatchTraining,
                  displayField: 'label',
                  valueField: 'id',
                  queryMode: 'local',
                  selectOnFocus: true,
                  editable: false
               }
            ]
        },{
            xtype:'container',
            id:'rowNursery',
            layout:'column',
            width:'100%',
            margin:'10px 0 0 12px',
            height:30,
            items: [
               {
                  xtype: 'label',
                  text: lang('Nursery'),
                  margin:'5px 0px 0 0',
                  width:225
               },{
                  xtype: 'combo',
                  width: 60,
                  listConfig: {
                     cls: 'x-boundlist-item comboAdvFilterItemList'
                  },
                  id: 'advNursery',
                  name: 'advNursery',
                  store: cmbAdvYesNo,
                  displayField: 'label',
                  valueField: 'id',
                  queryMode: 'local',
                  selectOnFocus: true,
                  editable: false
               }
            ]
        },{
            xtype: 'container',
            id: 'rowBtnSearch',
            layout: {
                type: 'table',
                columns: 3
            },
            width: '100%',
            height: 46,
            items: [{
                xtype: 'button',
                width: 150,
                margin: '10px 0px 10px 238px',
                text: 'Search',
                style: 'text-align:center;',
                handler: function() {
                    var isValidSeach = true;

                    if(Ext.getCmp('rowNameId').isVisible() == true){
                        var parAdvNama = Ext.getCmp('advNameId').getValue();
                        if(parAdvNama == "") isValidSeach = false;
                    }

                    if(Ext.getCmp('rowDistrict').isVisible() == true){
                        var parAdvDistrict = Ext.getCmp('advDistrict').getValue().join().replace(/,/g, '::');
                        if(parAdvDistrict == null) isValidSeach = false;
                    }

                    if(Ext.getCmp('rowBatch').isVisible() == true){
                        var parBatch = Ext.getCmp('advBatch').getValue();
                        if(parBatch == "") isValidSeach = false;
                    }

                    if(Ext.getCmp('rowNursery').isVisible() == true){
                        var parNursery = Ext.getCmp('advNursery').getValue();
                        if(parNursery == "") isValidSeach = false;
                    }

                    //event click
                    if(isValidSeach == false){
                        //cek dulu apakah semua filter yg pilih sudah diisikan nilainya
                        Ext.MessageBox.show({
                           title: 'Notifications',
                           msg: 'Selected filter must be filled',
                           buttons: Ext.MessageBox.OK,
                           animateTarget: 'mb9',
                           icon: 'ext-mb-warning'
                        });
                    }else{
                        store.load({
                            params: {
                                page:1,
                                start:0,
                                limit:50
                            }
                        });
                    }
                }
            },{
                xtype: 'button',
                width: '150',
                margin: '10px 0px 10px 18px',
                text: 'Simple Search',
                style: 'text-align:center;',
                handler: function() {
                    Ext.getCmp('key').setVisible(true);
                    // Ext.getCmp('Kab').setVisible(true);
                    Ext.getCmp('btnSimpleSearch').setVisible(true);

                    Ext.getCmp('btnAdvSearch').setVisible(true);
                    Ext.getCmp('idPanelAdvFilter').setVisible(false);
                }
            },{
                xtype: 'button',
                width: '150',
                margin: '10px 0px 10px 18px',
                text: 'Export to Excel',
                style: 'text-align:center;',
                handler: function() {
                    if(Ext.getCmp('rowNameId').isVisible() == true){
                        var parAdvNamaId = Ext.getCmp('advNameId').getValue();
                    }else{
                        var parAdvNamaId = 'not_set';
                    }
                    if(Ext.getCmp('rowDistrict').isVisible() == true){
                        var parAdvDistrict = Ext.getCmp('advDistrict').getValue().join().replace(/,/g, '::');
                    }else{
                        var parAdvDistrict = 'not_set';
                    }
                    if(Ext.getCmp('rowBatch').isVisible() == true){
                        var parAdvBatch = Ext.getCmp('advBatch').getValue();
                    }else{
                        var parAdvBatch = 'not_set';
                    }
                    if(Ext.getCmp('rowNursery').isVisible() == true){
                        var parAdvNursery = Ext.getCmp('advNursery').getValue();
                    }else{
                        var parAdvNursery = 'not_set';
                    }

                    Ext.MessageBox.show({
                        msg: 'Please wait...',
                        progressText: 'Exporting...',
                        width:300,
                        wait:true,
                        waitConfig: {interval:200},
                        icon:'ext-mb-download', //custom class in msg-box.html
                        animateTarget: 'mb7'
                     });

                    //event click
                    Ext.Ajax.request({
                        url: m_list_excel,
                        method: 'POST',
                        waitMsg: lang('Please Wait'),
                        params: {
                            prov : m_param,
                            parAdvNamaId : parAdvNamaId,
                            parAdvDistrict : parAdvDistrict,
                            parAdvBatch : parAdvBatch,
                            parAdvNursery : parAdvNursery
                        },
                        success: function(data) {
                            Ext.MessageBox.hide();

                            var jsonResp = JSON.parse(data.responseText);
                            window.location = jsonResp.filenya;
                        },
                        failure: function(){
                            Ext.MessageBox.hide();

                            Ext.MessageBox.show({
                                title: 'Notifications',
                                msg: 'Failed to export, Please try again.',
                                buttons: Ext.MessageBox.OK,
                                animateTarget: 'mb9',
                                icon: 'ext-mb-error'
                            });
                        }
                    });

                }
            }]
        }]
    });

    //hilangkan semua elemen advanced filter
    Ext.getCmp('idPanelAdvFilter').setVisible(false);
    hideAllElementAdvFilter();

    function hideAllElementAdvFilter(){
        Ext.getCmp('rowNameId').setVisible(false);
        Ext.getCmp('advNameId').setValue();
        Ext.getCmp('rowDistrict').setVisible(false);
        Ext.getCmp('advDistrict').setValue();
        Ext.getCmp('rowBatch').setVisible(false);
        Ext.getCmp('advBatch').setValue();
        Ext.getCmp('rowNursery').setVisible(false);
        Ext.getCmp('advNursery').setValue();
    }
    //============================================== Advanced Filter (End) =========================================//

    //================================================= Window Assign Partner (begin) ===================================//
    function displayAssignPartner(mainStore,CPGid){
        var s_ass_partner = Ext.create('Ext.data.Store', {
            extend: 'Ext.data.Model',
            fields: ['PartnerID', 'PartnerName'],
            autoLoad: true,
            proxy: {
                type: 'ajax',
                url: m_api + '/cpg/ass_partner_list',
                extraParams: {
                    CPGid: CPGid
                },
                reader: {
                    type: 'json',
                    root: 'data'
                }
            }
        });

        var winAssignPartner = Ext.create('widget.window', {
            title: lang('Assign Partner'),
            id: 'winAssignPartner',
            closable: true,
            modal: true,
            closeAction: 'destroy',
            width: '45%',
            height: '48%',
            overflowY: 'auto',
            bodyStyle:{"background-color":"#F0F0F0"},
            style:'background-color:#F0F0F0;',
            padding:6,
            scrollOffset: 20,
            items:[{
                xtype: 'form',
                id: 'winFormAssignPartner',
                padding:'5 20 0 8',
                items:[{
                    layout: 'column',
                    border: false,
                    items: [{
                        xtype: 'hiddenfield',
                        id: 'AssPartCPGid',
                        name: 'AssPartCPGid',
                        value: CPGid
                    },{
                        xtype: 'hiddenfield',
                        id: 'AssPartPartnerID',
                        name: 'AssPartPartnerID'
                    },{
                        columnWidth: 1,
                        xtype: 'textfield',
                        fieldLabel: lang('Submitted by'),
                        labelWidth: 150,
                        id: 'LabelSubmittedBy',
                        name: 'LabelSubmittedBy',
                        readOnly:true
                    },{
                        columnWidth: 1,
                        xtype: 'itemselector',
                        flex:true,
                        id: 'cmbAssignPartner',
                        name: 'cmbAssignPartner',
                        fieldLabel: lang('Assign Partner'),
                        labelWidth: 150,
                        fromTitle: lang('Available'),
                        toTitle: lang('Selected'),
                        anchor: '100%',
                        height:300,
                        store: s_ass_partner,
                        displayField: 'PartnerName',
                        valueField: 'PartnerID',
                        value: [],
                        allowBlank: false,
                        msgTarget: 'side',
                        listeners: {
                            change: function() {
                            }
                        }
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
                    var form = Ext.getCmp('winFormAssignPartner').getForm();
                    if (form.isValid()) {
                        form.submit({
                            url: m_api + '/cpg/ass_partner_form',
                            method:'POST',
                            waitMsg: 'Saving data...',
                            success: function(fp, o) {
                                Ext.MessageBox.alert('Success', 'Data saved');
                                winAssignPartner.close();
                                mainStore.load();
                            },
                            failure: function(fp, o){
                                var jsonResp = o.result;
                                Ext.MessageBox.show({
                                    title: 'Failed',
                                    msg: jsonResp.message,
                                    buttons: Ext.MessageBox.OK,
                                    animateTarget: 'mb9',
                                    icon: 'ext-mb-error'
                                });
                            }
                        });
                    }else{
                        Ext.MessageBox.show({
                            title: 'Failed',
                            msg: 'Form is not complete yet',
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
                    winAssignPartner.close();
                }
            }]
        });

        //isi data
        Ext.getCmp('winFormAssignPartner').getForm().load({
            url: m_api + '/cpg/ass_partner_form',
            method: 'GET',
            params: {
                CPGid: CPGid
            },
            success: function(form, action) {
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
        if (!winAssignPartner.isVisible()) {
            winAssignPartner.center();
            winAssignPartner.show();
        } else {
            winAssignPartner.close();
        }
    }
    //================================================= Window Assign Partner (end)   ===================================//

    var contextMenuGrid = Ext.create('Ext.menu.Menu',{
        items:[{
            icon: varjs.config.base_url + 'images/icons/new/update.png',
            text: lang('Update'),
            hidden: !m_act_update,
            handler: function(){
                var sm = Ext.getCmp('grid').getSelectionModel().getSelection()[0];
                fupdate(sm);
            }
        },{
            icon: varjs.config.base_url + 'images/icons/silk/user_suit.png',
            text: lang('Assign Partner'),
            hidden: !m_act_cpg_assign_partner,
            handler: function() {
                var sm = Ext.getCmp('grid').getSelectionModel().getSelection()[0];
                displayAssignPartner(store,sm.get('id'));
            }
        }]
    });

    var grid = Ext.create('Ext.grid.Panel', {
        store: store,
        remoteSort: true,
        width: '100%',
        id: 'grid',
        minHeight: 250,
        //title: lang('Farmer Group List'),
        style: 'border:1px solid #CCC;',
        renderTo: 'ext-content',
        loadMask: true,
        selType: 'rowmodel',
        listeners: {
            itemclick: function(view, record, item, index, e){
               contextMenuGrid.showAt(e.getXY());
            }
            /*,itemdblclick: function(dv, record, item, index, e) {
                var sm = record;
                fupdate(sm);
            }*/
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
                        text: lang('Add'),
                        // cls: m_act_add,
                        hidden: !m_act_add,
                        handler: function() {
                            displayFormWindow();
                            hideSave();
                            Ext.getCmp('toolbar_cpg').hide();
                            Ext.getCmp('radio_pengurus').disable();
                            Ext.getCmp('panel_staff').disable();
                        }
                    }, {
                        icon: varjs.config.base_url + 'images/icons/new/update.png',
                        text: lang('Update'),
                        // cls: m_act_update,
                        hidden: !m_act_update,
                        handler: function() {
                            var sm = Ext.getCmp('grid').getSelectionModel().getSelection()[0];
                            fupdate(sm);
                        }
                    }, {
                        itemId: 'remove',
                        icon: varjs.config.base_url + 'images/icons/new/delete.png',
                        // cls: m_act_delete,
                        hidden: !m_act_delete,
                        text: lang('Hapus'),
                        scope: this,
                        handler: function() {
                            var smb = Ext.getCmp('grid').getSelectionModel().getSelection()[0];
                            Ext.MessageBox.confirm('Message', lang('Apakah anda mau menghapus data ini ?'), function(btn) {
                                if (btn == 'yes') {
                                    Ext.Ajax.request({
                                        waitMsg: lang('Please Wait'),
                                        url: m_crud,
                                        method: 'DELETE',
                                        params: {id: smb.raw.id},
                                        success: function(response, opts) {
                                            var obj = Ext.decode(response.responseText);
                                            switch (obj.success) {
                                                case true:
                                                    store.load({
                                                        params: {
                                                            key: Ext.getCmp('key').getValue(),
                                                            // kab: Ext.getCmp('Kab').getValue()
                                                        }
                                                    });
                                                    break;
                                                default:
                                                    Ext.MessageBox.alert('Warning', obj.message);
                                                    break;
                                            }
                                        },
                                        failure: function(response, opts) {
                                            var obj = Ext.decode(response.responseText);
                                            Ext.MessageBox.alert('error', lang('Could not connect to the database. Retry later'));
                                        }
                                    });
                                }
                            });
                        }
                    }, {
                        itemId: 'access',
                        icon: varjs.config.base_url + 'images/icons/silk/lock_key.png',
                        cls: m_act_access,
                        text: lang('Access'),
                        scope: this,
                        handler: function() {
                            var sm = Ext.getCmp('grid').getSelectionModel().getSelection()[0];
                            displayAccessWindow();
                            store_staff_access.load({
                                params: {
                                    id: sm.get('id')
                                }
                            });
                            store_staff.load({
                                params: {
                                    cpg: sm.get('id')
                                }
                            });
                            Ext.getCmp('cpg_id').setValue(sm.get('id'));
                        }
                    }, {
                        xtype: 'textfield',
                        name: 'key', baseCls:'Sfr_TxtfieldSearchGrid',
                        id: 'key',
                        emptyText: lang('Cari berdasar nama/ID'),
                        listeners: {
                            specialkey: submitOnEnter
                        }
                    // }, {
                    //     id: 'Kab',
                    //     name: 'Kab',
                    //     xtype: 'combo',
                    //     store: mc_Kabupaten,
                    //     displayField: 'label',
                    //     valueField: 'label',
                    //     queryMode: 'local',
                    //     selectOnFocus: true,
                    //     listeners: {
                    //         specialkey: submitOnEnter
                    //     }
                    }, {
                        xtype: 'button',
                        id:'btnSimpleSearch',
                        icon: varjs.config.base_url + 'images/icons/silk/search.png',
                        margin: '0px 0px 0px 6px',
                        text: lang('Search'),
                        handler: function() {
                            store.load({
                                params: {
                                    key: Ext.getCmp('key').getValue(),
                                    // kab: Ext.getCmp('Kab').getValue()
                                }
                            });
                        }
                    },
                    {
                        xtype: 'button',
                        id: 'btnAdvSearch',
                        icon: varjs.config.base_url + 'images/icons/silk/page_white_wrench.png',
                        margin: '0px 0px 0px 6px',
                        text: lang('Advanced Search'),
                        handler: function() {
                            //aksi disini
                            Ext.getCmp('key').setVisible(false);
                            // Ext.getCmp('Kab').setVisible(false);
                            Ext.getCmp('btnSimpleSearch').setVisible(false);

                            Ext.getCmp('btnAdvSearch').setVisible(false);
                            Ext.getCmp('idPanelAdvFilter').setVisible(true);
                        }
                    }
                    ]
            }],
        columns: [
            {
                text: lang('GAP Batch Nr'),
                dataIndex: 'BatchNumber',
                width: '8%'
            },
            {
                text: lang('ID'),
                dataIndex: 'id',
                width: '10%'
            },
            {
                text: lang('CPGID SCPP'),
                dataIndex: 'OldCPGid',
                hidden: true
            },
            {
                text: lang('Farmer Group Name'),
                width: '20%',
                dataIndex: 'GroupName',
                sortable: true
            },
            {
                text: lang('Address'),
                dataIndex: 'Address',
                hidden: true
            },
            {
                text: lang('Village'),
                width: '15%',
                dataIndex: 'RegionName'
            },
            {
                text: lang('Tahun Terbentuk'),
                width: '10%',
                dataIndex: 'TahunTerbentuk'
            },
            {
                text: lang('Group Members'),
                width: '10%',
                dataIndex: 'Anggota'
            },
            /*{
                text: lang('Partner Name'),
                width: '10%',
                dataIndex: 'PartnerName'
            },*/
            {
                text: lang('Nr of Cocoa Garden'),
                width: '13%',
                dataIndex: 'totalGarden'
            },
            {
                text: lang('Total area Cocoa Garden'),
                width: '15%',
                dataIndex: 'totalLandSize'
            }
            ]
    });
    // mc_Kabupaten.on('load', function(st) {
    //     if (Ext.getCmp('Kab').getValue() == null) {
    //         Ext.getCmp('Kab').setValue(st.getAt('0').get('label'));
    //         store.load({
    //             params: {
    //                 key: Ext.getCmp('key').getValue(),
    //                 kab: Ext.getCmp('Kab').getValue()
    //             }
    //         });
    //     }
    // });

    //PARTICIPANT
    var mc_key_farmer = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'label'],
        pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_key_farmer,
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });

    var mc_fasilitator = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'label'],
        autoLoad: true,
        pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_fasilitator,
            extraParams: {workarea: m_param},
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });
    var mc_penyuluh = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'label'],
        autoLoad: true,
        // pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_penyuluh,
            //extraParams: {prov: m_param},
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });

    function displayFormWindowParticipant() {
        if (!winPar.isVisible()) {
            resetFormPar();
            mc_demo_plot.load({
                params: {
                    cpg: Ext.getCmp('id').getValue()
                }
            });
            winPar.center();
            winPar.show();
        } else {
            winPar.hide(this, function() {
            });
            winPar.toFront();
        }
    }

    function displayFormWindowParticipantCheckList() {
        if (!winParCheckList.isVisible()) {
            winParCheckList.center();
            winParCheckList.show();
        } else {
            winParCheckList.hide();
            winParCheckList.toFront();
        }
    }

    function displayFormWindowParticipantCheckListDay() {
        if (!winParCheckListDay.isVisible()) {
            winParCheckListDay.center();
            winParCheckListDay.show();
        } else {
            winParCheckListDay.hide(this, function() {
            });
            winParCheckListDay.toFront();
        }
    }

    function displayWinSelectDay() {
        // TrainingDate
        var min = new Date(Ext.getCmp('TrainingStart').getValue());
        var max = new Date(Ext.getCmp('TrainingEnd').getValue());
        Ext.getCmp('TrainingDate2').setMinValue(min);
        Ext.getCmp('TrainingDate2').setMaxValue(max);
        Ext.getCmp('TrainingDay').setMaxValue(Ext.getCmp('TrainingDays').getValue());
        if (!winSelectDay.isVisible()) {
            winSelectDay.center();
            winSelectDay.show();
        } else {
            winSelectDay.hide(this, function() {
            });
            winSelectDay.toFront();
        }
    }

    function resetFormPar() {
        Ext.getCmp('KeyFarmerID').setValue();
        Ext.getCmp('idd').setValue('');
        Ext.getCmp('idt').setValue('');
        Ext.getCmp('batch').setValue('');
        Ext.getCmp('CpgTrainingsID').setValue('');
        Ext.getCmp('TrainingDays').setValue('');
        Ext.getCmp('TrainingStart').setValue('');
        Ext.getCmp('TrainingEnd').setValue('');
        Ext.getCmp('ProgramStaffID').setValue('');
        Ext.getCmp('PetaniKakao').setValue(false);
        Ext.getCmp('PetaniKakao2').setValue(false);
        Ext.getCmp('FamilyID').setValue();
        Ext.getCmp('ExtensionStaffID').setValue('');
        Ext.getCmp('DemoplotOwnerID').setValue('');
    }

    function displayAddWindowParticipant() {
        if (!winAddPar.isVisible()) {
            store_participant_add.load({
                params: {
                    CpgBatchTrainingID: Ext.getCmp('idt').getValue(),
                    cpgID: Ext.getCmp('idd').getValue()
                }
            });
            winAddPar.center();
            winAddPar.show();
        } else {
            winAddPar.hide(this, function() {
            });
            winAddPar.toFront();
        }
    }

    var RowEditing = Ext.create('Ext.grid.plugin.RowEditing', {
        id: 'rowEditing',
        clicksToMoveEditor: 0,
        autoCancel: false,
        errorSummary: false,
        clicksToEdit: 2
    });

    function isNumber(n) {
        return !isNaN(parseFloat(n)) && isFinite(n);
    }

    var ya_tidak = Ext.create('Ext.data.Store', {
        fields: ['id', 'label'],
        data: [
            {"id": "1", "label": "Ya"},
            {"id": "2", "label": "Tidak"},
        ]
    });

    // Training participant panel container
    var DataFormPar = Ext.create('Ext.form.Panel', {
        height: 659,
        autoScroll: true,
        width: 1014,
        id: 'dataFormPar',
        fieldDefaults: {
            labelAlign: 'left',
            labelWidth: 120,
            anchor: '100%'
        },
        items: [{
                layout: 'column',
                border: false,
                items: [{
                        columnWidth: .55,
                        layout: 'form',
                        padding: 5,
                        border: false,
                        items: [{
                                xtype: 'textfield',
                                id: 'idd',
                                name: 'idd',
                                fieldLabel: lang('Farmer Group ID'),
                                readOnly: true
                            }, {
                                xtype: 'textfield',
                                id: 'idt',
                                name: 'idt',
                                inputType: 'hidden'
                            }, {
                                xtype: 'combo',
                                store: mc_batch,
                                displayField: 'label',
                                valueField: 'id',
                                fieldLabel: lang('CPG/FFS Batch'),
                                queryMode: 'local',
                                id: 'batch',
                                name: 'batch'
                            }, {
                                xtype: 'combo',
                                store: mc_training,
                                displayField: 'label',
                                valueField: 'id',
                                fieldLabel: lang('Topic'),
                                id: 'CpgTrainingsID',
                                name: 'CpgTrainingsID',
                                queryMode: 'local',
                                listeners: {
                                    change: function(cb, nv, ov) {
                                        mc_key_farmer.load({
                                            params: {
                                                district: Ext.getCmp('Kabupaten').getValue()
                                            }
                                        });
                                        mc_sub_topic.load();
                                        /*
                                        if (Ext.getCmp('CpgTrainingsID').getValue() == '2')
                                            Ext.getCmp('t_n1').show();
                                        else
                                            Ext.getCmp('t_n1').hide();
                                       */
                                        //Ext.getCmp('Kabupaten').enable();
                                    }
                                }
                            }, {
                                xtype: 'boxselect',
                                id: 'CpgTrainingsIDSubTopic',
                                name: 'CpgTrainingsIDSubTopic[]',
                                store: mc_sub_topic,
                                displayField: 'label',
                                valueField: 'id',
                                queryMode: 'local',
                                fieldLabel: lang('Subtopics'),
                                stacked: true,
                                pinList: false,
                                triggerOnClick: false,
                                filterPickList: true
                            },{
                                xtype: 'datefield',
                                format: 'Y-m-d',
                                fieldLabel: lang('Training Start'),
                                id: 'TrainingStart',
                                name: 'TrainingStart'
                            }, {
                                xtype: 'datefield',
                                fieldLabel: lang('Training End'),
                                format: 'Y-m-d',
                                id: 'TrainingEnd',
                                name: 'TrainingEnd'
                            }, {
                                xtype: 'radiogroup',
                                fieldLabel: lang('Day Status'),
                                items: [{
                                    name: 'TrainingDayStatus',
                                    id: 'TrainingDayStatusHalf',
                                    boxLabel: lang('Half day'),
                                    inputValue: 'half'
                                },{
                                    name: 'TrainingDayStatus',
                                    id: 'TrainingDayStatusFull',
                                    boxLabel: lang('Full day'),
                                    inputValue: 'full'
                                }]
                            }, {
                                xtype: 'textfield',
                                allowBlank: false,
                                id: 'TrainingDays',
                                name: 'TrainingDays',
                                fieldLabel: lang('Training Days')
                            }]
                    }, {
                        columnWidth: .45,
                        layout: 'form',
                        padding: 5,
                        border: false,
                        items: [{
                                xtype: 'combo',
                                store: mc_penyuluh,
                                displayField: 'label',
                                valueField: 'id',
                                fieldLabel: lang('Penyuluh'),
                                id: 'ExtensionStaffID',
                                name: 'ExtensionStaffID',
                                queryMode: 'local'
                            }, {
                                xtype: 'combo',
                                store: mc_fasilitator,
                                displayField: 'label',
                                valueField: 'id',
                                fieldLabel: lang('Fasilitator'),
                                id: 'ProgramStaffID',
                                name: 'ProgramStaffID',
                                queryMode: 'local'
                            }, {
                                xtype: 'combo',
                                store: mc_demo_plot,
                                displayField: 'label',
                                valueField: 'id',
                                fieldLabel: lang('Demo Plot Farmer'),
                                id: 'DemoplotOwnerID',
                                name: 'DemoplotOwnerID',
                                queryMode: 'local',
                                labelWidth: 130,
                            }, {
                                xtype: 'combo',
                                store: mc_key_farmer,
                                displayField: 'label',
                                valueField: 'id',
                                fieldLabel: lang('Key Farmer'),
                                id: 'KeyFarmerID',
                                name: 'KeyFarmerID',
                                queryMode: 'remote',
                                typeAhead: true,
                                listeners: {
                                    change: function(cb, nv, ov) {
                                        if (isNumber(Ext.getCmp('KeyFarmerID').getValue())) {
                                            mc_family_training.load({
                                                params: {
                                                    id: Ext.getCmp('KeyFarmerID').getValue()
                                                }
                                            });
                                        }
                                    }
                                }
                            }, {
                                xtype: 'radiogroup',
                                fieldLabel: lang('Key Farmer as Trainer'),
                                items: [{
                                        name: 'PetaniKakao',
                                        id: 'PetaniKakao',
                                        boxLabel: lang('ya'),
                                        inputValue: '1'
                                    }, {
                                        name: 'PetaniKakao',
                                        id: 'PetaniKakao2',
                                        boxLabel: lang('Tidak'),
                                        inputValue: '2'
                                    }],
                                listeners: {
                                    change: function() {
                                        if (Ext.getCmp('PetaniKakao').getValue() == '1') {
                                            Ext.getCmp('famili').setDisabled(true);
                                        } else {
                                            Ext.getCmp('famili').setDisabled(false);
                                        }
                                    }
                                }
                            }, {
                                xtype: 'fieldset',
                                border: false,
                                id: 'famili',
                                items: [{
                                        xtype: 'combo',
                                        store: mc_family_training,
                                        displayField: 'label',
                                        valueField: 'id',
                                        fieldLabel: lang('If no:family member as key farmer'),
                                        id: 'FamilyID',
                                        name: 'FamilyID',
                                        queryMode: 'local'
                                    }]
                            }]
                    }]
            }, {
                xtype: 'gridpanel',
                title: lang('Training Participant'),
                style: 'border:1px solid #CCC;',
                id: 'grid_participant',
                store: store_participant,
                width: '100%',
                loadMask: true,
                selType: 'rowmodel',
                dockedItems: [{
                        xtype: 'toolbar',
                        items: [{
                                icon: varjs.config.base_url + 'images/icons/new/add.png', cls:'Sfr_BtnGridGreen', overCls:'Sfr_BtnGridGreen-Hover',
                                text: lang('Add'),
                                // cls: m_act_save,
                                hidden: !m_act_add,
                                scope: this,
                                handler: function() {
                                    displayAddWindowParticipant();
                                    hideSave();
                                    /*
                                     if (Ext.getCmp('CpgTrainingsID').getValue()=='')
                                     Ext.MessageBox.alert('Info', lang('Silahkan lengkapi data training di atas'));
                                     else if (Ext.getCmp('idt').getValue()=='') {
                                     var form = DataFormPar.getForm();
                                     form.submit({
                                     url: m_training,
                                     waitMsg: lang('Sending data...'),
                                     success: function(fp, o) {
                                     store_training.load({
                                     params: {
                                     key: Ext.getCmp('idd').getValue()
                                     }
                                     });
                                     Ext.getCmp('idt').setValue(o.result.id);
                                     RowEditing.cancelEdit();
                                     store_participant.insert(0,{CpgBatchTrainingsFarmerID: '',CpgBatchTrainingID: o.result.id,pFarmerID:'',PetaniKakao:'',FamilyID:'',AnggotaName:'',
                                     WritingAwal:'',WritingAkhir:'',BallotAwal:'',BallotAkhir:'',PersonNm:'',partisipan:''});
                                     RowEditing.startEdit(0, 0);
                                     }
                                     });
                                     } else {
                                     RowEditing.cancelEdit();
                                     store_participant.insert(0,{CpgBatchTrainingsFarmerID: '',CpgBatchTrainingID: Ext.getCmp('idt').getValue(),
                                     pFarmerID:'',PetaniKakao:1,FamilyID:'',AnggotaName:'',
                                     WritingAwal:'',WritingAkhir:'',BallotAwal:'',BallotAkhir:'',PersonNm:'',partisipan:'1'});
                                     RowEditing.startEdit(0, 0);
                                     }
                                     */
                                } // end add
                            }, {
                                icon: varjs.config.base_url + 'images/icons/new/update.png',
                                text: lang('Update'),
                                // cls: m_act_save,
                                hidden: !m_act_update,
                                scope: this,
                                handler: function() {
                                    RowEditing.cancelEdit();
                                    var sm = Ext.getCmp('grid_participant').getSelectionModel().getSelection();
                                    RowEditing.startEdit(sm[0].index, 0);
                                    mc_family.load({
                                        params: {
                                            key: sm[0].data.pFarmerID
                                        }
                                    });
                                    Ext.getCmp('pFarmerID').setValue(sm[0].data.pFarmerID)
                                    //console.log(Ext.getCmp('partt').getValue())
                                    if (Ext.getCmp('partt').getValue() == 'Tidak') {
                                        Ext.getCmp('famm').enable();
                                    } else {
                                        Ext.getCmp('famm').setDisabled(true);
                                    }
                                }
                            }, {
                                itemId: 'remove',
                                icon: varjs.config.base_url + 'images/icons/new/delete.png',
                                // cls: m_act_save,
                                hidden: !m_act_delete,
                                text: lang('Delete'),
                                scope: this,
                                handler: function() {
                                    var smz = Ext.getCmp('grid_participant').getSelectionModel().getSelection();
                                    //var smz = grid.getStore().getAt(rowIndex);
                                    //var smz = Ext.getCmp('grid_participant').getSelectionModel();
                                    RowEditing.cancelEdit();
                                    Ext.MessageBox.confirm('Message', lang('Apakah anda mau menghapus participant ini ?'), function(btn) {
                                        if (btn == 'yes') {
                                            Ext.Ajax.request({
                                                waitMsg: lang('Please Wait'),
                                                url: m_participant,
                                                method: 'DELETE',
                                                params: {
                                                    id: smz[0].data.CpgBatchTrainingsFarmerID
                                                },
                                                success: function(response, opts) {
                                                    var obj = Ext.decode(response.responseText);
                                                    switch (obj.success) {
                                                        case true:
                                                            store_participant.load({
                                                                params: {
                                                                    key: smz[0].data.CpgBatchTrainingID
                                                                }
                                                            });
                                                            break;
                                                        default:
                                                            Ext.MessageBox.alert('Warning', obj.message);
                                                            break;
                                                    }
                                                },
                                                failure: function(response, opts) {
                                                    var obj = Ext.decode(response.responseText);
                                                    Ext.MessageBox.alert('error', lang('Could not connect to the database. Retry later'));
                                                }
                                            });
                                        }
                                    });

                                    if (store.getCount() > 0) {
                                        smz.select(0);
                                    }
                                }
                            },{
                                xtype: 'splitbutton',
                                icon: varjs.config.base_url + 'images/icons/silk/printer.png',
                                text: lang('Daftar Hadir'),
                                menu: {
                                    items: [{
                                            text: lang('Form Kosong'),
                                            handler: function() {
                                                preview_cetak_surat(m_cetak + Ext.getCmp('idt').getValue());
                                            }
                                        }
                                        , {
                                            text: lang('Form Hasil'),
                                            handler: function() {
                                                //preview_cetak_surat(m_cetak + Ext.getCmp('idt').getValue(),'Form Hasil');
                                                displayBeforeCetakAttendanceList();
                                            }
                                        }
                                    ]
                                }
                            },/*{
                                icon: varjs.config.base_url + 'images/icons/silk/printer.png',
                                text: lang('GAP'),
                                cls:'hide-icon',
                                scope: this,
                                handler: function() {
                                    jenis = 'P1';
                                    displayBeforeCetak();
                                }
                            }, {
                                icon: varjs.config.base_url + 'images/icons/silk/printer.png',
                                text: lang('GFP'),
                                cls:'hide-icon',
                                scope: this,
                                handler: function() {
                                    jenis = 'F1';
                                    displayBeforeCetak();
                                }
                            }, {
                                icon: varjs.config.base_url + 'images/icons/silk/printer.png',
                                id: 't_n1',
                                text: lang('GNP'),
                                cls:'hide-icon',
                                scope: this,
                                handler: function() {
                                    jenis = 'N1';
                                    displayBeforeCetak();
                                }
                            }, {
                                icon: varjs.config.base_url + 'images/icons/silk/printer.png',
                                text: lang('PPI'),
                                cls:'hide-icon',
                                scope: this,
                                handler: function() {
                                    jenis = 'PPI';
                                    displayBeforeCetak();
                                }
                            }, {
                                icon: varjs.config.base_url + 'images/icons/silk/printer.png',
                                text: lang('Learning Contract'),
                                scope: this,
                                cls:'hide-icon',
                                handler: function() {
                                    preview_cetak_surat(m_cetak_learning_contract + 'CpgBatchTrainingID/' + CpgBatchTrainingID);
                                }
                            },*/
                            {
                                icon: varjs.config.base_url + 'images/icons/new/update.png',
                                text: lang('Attendance Check List Per Participant'),
                                scope: this,
                                handler: function() {
                                    var sm = Ext.getCmp('grid_participant').getSelectionModel().getSelection();
                                    // console.log(sm[0]);
                                    if (!sm[0]) {
                                        Ext.MessageBox.alert(lang('Warning'), lang('Silahkan pilih petani'));
                                    } else {
                                        $.ajax({
                                            url: m_participant_detail,
                                            data: {
                                                CpgBatchTrainingsFarmerID: sm[0].data.CpgBatchTrainingsFarmerID
                                            },
                                        })
                                        .done(function(data) {
                                            displayFormWindowParticipantCheckList();

                                            Ext.getCmp('parcheklist_farmerid').setValue(data['FarmerID']);
                                            Ext.getCmp('parcheklist_farmename').setValue(data['FarmerName']);
                                            Ext.getCmp('parcheklist_groupname').setValue(data['GroupName']);
                                            Ext.getCmp('parcheklist_trainingdays').setValue(data['TrainingDays']);
                                            store_DayNumber.load({
                                                params: {
                                                    dayNumber: data['TrainingDays']
                                                }
                                            });
                                            Ext.getCmp('parcheklist_startdate').setValue(data['TrainingStart']);
                                            Ext.getCmp('parcheklist_enddate').setValue(data['TrainingEnd']);

                                            if(data['TrainingDayStatus'] == 'half'){
                                                Ext.getCmp('grid_participant_checklist').down('[dataIndex=Attendance2]').setVisible(false);
                                                Ext.getCmp('grid_participant_checklist').down('[dataIndex=Attendance1]').setText(lang('Kehadiran'))
                                            }else{
                                                Ext.getCmp('grid_participant_checklist').down('[dataIndex=Attendance1]').setVisible(true);
                                                Ext.getCmp('grid_participant_checklist').down('[dataIndex=Attendance2]').setVisible(true);

                                                Ext.getCmp('grid_participant_checklist').down('[dataIndex=Attendance1]').setText(lang('Pagi'))
                                                Ext.getCmp('grid_participant_checklist').down('[dataIndex=Attendance2]').setText(lang('Siang'))
                                            }
                                        });
                                        store_participant_checklist.load({
                                            params: {
                                                CpgBatchTrainingsFarmerID: sm[0].data.CpgBatchTrainingsFarmerID,
                                                CpgBatchTrainingID: sm[0].data.CpgBatchTrainingID,
                                                FarmerID: sm[0].data.pFarmerID,
                                            }
                                        })

                                    }
                                }
                            },
                            {
                                icon: varjs.config.base_url + 'images/icons/new/update.png',
                                text: lang('Attendance Check List Per Day'),
                                scope: this,
                                handler: function() {
                                    displayWinSelectDay();
                                }
                            },
                            {
                                xtype: 'splitbutton',
                                icon: varjs.config.base_url + 'images/icons/silk/printer.png',
                                text: lang('Certificate'),
                                menu: {
                                    items: [{
                                            text: lang('Download All'),
                                            handler: function() {
                                                window.open(m_certificates + Ext.getCmp('idt').getValue());
                                            }
                                        }
                                        , {
                                            text: lang('Preview'),
                                            handler: function() {
                                                var sm = Ext.getCmp('grid_participant').getSelectionModel().getSelection();
                                                if (!sm[0]) {
                                                    Ext.MessageBox.alert(lang('Warning'), lang('Silahkan pilih petani'));
                                                } else {
                                                    preview_cetak_surat(m_certificate + sm[0].data.CpgBatchTrainingsFarmerID);
                                                }
                                            }
                                        }
                                    ]
                                }
                            }
                        ]
                    }],
                columns: [{
                        text: lang('ID'),
                        dataIndex: 'CpgBatchTrainingsFarmerID',
                        width: '10%',
                        hidden: true
                    }, {
                        text: lang('ID'),
                        dataIndex: 'pFarmerID',
                        width: '13%'
                    },
                    {
                        text: lang('Farmer'),
                        dataIndex: 'PersonNm',
                        width: '30%',
                        editor: {
                            xtype: 'combo',
                            displayField: 'label',
                            id: 'pFarmerID',
                            name: 'pFarmerID',
                            valueField: 'id',
                            queryMode: 'local',
                            store: mc_demo_plot,
                            typeAhead: true,
                            listeners: {
                                beforequery: function(record) {
                                    record.query = new RegExp(record.query, 'i');
                                    record.forceAll = true;
                                },
                                change: function(cb, nv, ov) {
                                    if (Ext.getCmp('pFarmerID').getValue() != nv) {
                                        Ext.Ajax.request({
                                            waitMsg: lang('Check data...'),
                                            url: m_check,
                                            method: 'GET',
                                            params: {
                                                trainingid: Ext.getCmp('idt').getValue(),
                                                farmerid: Ext.getCmp('pFarmerID').getValue()
                                            },
                                            success: function(response, opts) {
                                                var obj = Ext.decode(response.responseText);
                                                if (!obj.data) {
                                                    Ext.MessageBox.alert('Warning', lang('Farmer telah terdapat dalam list'));
                                                    Ext.getCmp('pFarmerID').setValue('');
                                                    return;
                                                }
                                            }
                                        });
                                        var form = DataForm.getForm();
                                        if (Ext.getCmp('pFarmerID').getValue() != '') {
                                            mc_family.load({
                                                params: {
                                                    key: Ext.getCmp('pFarmerID').getValue()
                                                }
                                            });
                                        }
                                    }
                                }
                            }
                        }
                    },
                    {
                        text: lang('Participants'),
                        dataIndex: 'partisipan',
                        width: '10%',
                        editor: {
                            xtype: 'combo',
                            store: ya_tidak,
                            queryMode: 'local',
                            displayField: 'label',
                            valueField: 'id',
                            id: 'partt',
                            listeners: {
                                change: function() {
                                    if (Ext.getCmp('partt').getValue() == '2') {
                                        Ext.getCmp('famm').enable();
                                    } else {
                                        Ext.getCmp('famm').setValue('');
                                        Ext.getCmp('famm').setDisabled(true);
                                    }
                                }
                            }
                        }
                    }, {
                        text: lang('If no participants'),
                        dataIndex: 'AnggotaName',
                        width: '15%',
                        editor: {
                            xtype: 'combo',
                            disabled: true,
                            displayField: 'label',
                            valueField: 'id',
                            queryMode: 'local',
                            id: 'famm',
                            store: mc_family
                        }
                    },
                    {
                        text: lang('W Awal'),
                        dataIndex: 'WritingAwal',
                        width: '8%',
                        editor: {
                            xtype: 'textfield',
                        }
                    },
                    {
                        text: lang('W Akhir'),
                        dataIndex: 'WritingAkhir',
                        width: '8%',
                        editor: {
                            xtype: 'textfield',
                        }
                    },
                    {
                        text: lang('B Awal'),
                        dataIndex: 'BallotAwal',
                        width: '8%',
                        editor: {
                            xtype: 'textfield',
                        }
                    },
                    {
                        text: lang('B Akhir'),
                        dataIndex: 'BallotAkhir',
                        width: '8%',
                        editor: {
                            xtype: 'textfield',
                        }
                    }],
                plugins: [RowEditing],
                listeners: {
                    itemdblclick: function(dv, record, item, index, e) {
                        if (!m_act_update) {
                            RowEditing.cancelEdit();
                        } else {
                            var sm = record;
                            mc_family.load({
                                params: {
                                    key: sm.get('pFarmerID')
                                }
                            });
                            Ext.getCmp('pFarmerID').setValue(sm.get('pFarmerID'))
                            if (Ext.getCmp('partt').getValue() == 'Tidak') {
                                Ext.getCmp('famm').enable();
                            } else {
                                Ext.getCmp('famm').setDisabled(true);
                            }
                        }
                    },
                    'canceledit': function(editor, e, eOpts) {
                        store.load({
                            params: {
                                key: Ext.getCmp('key').getValue(),
                                // kab: Ext.getCmp('Kab').getValue()
                            }
                        });
                    },
                    'edit': function(editor, e) {
                        if (e.record.data.CpgBatchTrainingsFarmerID == '') {
                            Ext.Ajax.request({
                                waitMsg: lang('Please wait...'),
                                url: m_participant,
                                method: 'POST',
                                params: {
                                    CpgBatchTrainingID: e.record.data.CpgBatchTrainingID,
                                    PersonNm: e.record.data.PersonNm,
                                    pFarmerID: e.record.data.pFarmerID,
                                    partisipan: e.record.data.partisipan,
                                    PetaniKakao: e.record.data.PetaniKakao,
                                    AnggotaName: e.record.data.AnggotaName,
                                    FamilyID: e.record.data.FamilyID,
                                    WritingAwal: e.record.data.WritingAwal,
                                    WritingAkhir: e.record.data.WritingAkhir,
                                    BallotAwal: e.record.data.BallotAwal,
                                    BallotAkhir: e.record.data.BallotAkhir,
                                },
                                success: function(response, opts) {
                                    var obj = Ext.decode(response.responseText);
                                    switch (obj.success) {
                                        case true:
                                            Ext.MessageBox.alert('Success', obj.message);
                                            store_participant.load({
                                                params: {
                                                    key: e.record.data.CpgBatchTrainingID
                                                }
                                            });
                                            break;
                                        default:
                                            Ext.MessageBox.alert('Warning', obj.message);
                                            break;
                                    }
                                },
                                failure: function(response, opts) {
                                    var obj = Ext.decode(response.responseText);
                                    Ext.MessageBox.alert('error', lang('Could not connect to the database. Retry later'));
                                }
                            });
                        } else {
                            Ext.MessageBox.confirm('Message', lang('Update data participant ini ?'), function(btn) {
                                if (btn == 'yes') {
                                    console.log(e.record.data);
                                    Ext.Ajax.request({
                                        waitMsg: lang('Please wait...'),
                                        url: m_participant,
                                        method: 'PUT',
                                        params: {
                                            id: e.record.data.CpgBatchTrainingsFarmerID,
                                            CpgBatchTrainingID: e.record.data.CpgBatchTrainingID,
                                            PersonNm: e.record.data.PersonNm,
                                            pFarmerID: e.record.data.pFarmerID,
                                            partisipan: e.record.data.partisipan,
                                            PetaniKakao: e.record.data.PetaniKakao,
                                            AnggotaName: e.record.data.AnggotaName,
                                            FamilyID: e.record.data.FamilyID,
                                            WritingAwal: e.record.data.WritingAwal,
                                            WritingAkhir: e.record.data.WritingAkhir,
                                            BallotAwal: e.record.data.BallotAwal,
                                            BallotAkhir: e.record.data.BallotAkhir,
                                        },
                                        success: function(response, opts) {
                                            var obj = Ext.decode(response.responseText);
                                            switch (obj.success) {
                                                case true:
                                                    Ext.MessageBox.alert('Success', obj.message);
                                                    store_participant.load({
                                                        params: {
                                                            key: e.record.data.CpgBatchTrainingID
                                                        }
                                                    });
                                                    break;
                                                default:
                                                    Ext.MessageBox.alert('Warning', obj.message);
                                                    break;
                                            }
                                        },
                                        failure: function(response, opts) {
                                            var obj = Ext.decode(response.responseText);
                                            console.log(obj);
                                            Ext.MessageBox.alert('error', lang('Could not connect to the database. Retry later'));
                                        }
                                    });
                                }
                            });
                        }
                    }
                }
            }],
        buttons: [{
                id: 'save_par',
                text: lang('Save'),
                margin: '5px',
                scale: 'large',
                ui: 's-button',
                cls: 's-blue ',
                handler: function() {
                    var form = this.up('form').getForm();
                    var methode;
                    if (Ext.getCmp('idt').getValue() == '')
                        methode = 'POST';
                    else
                        methode = 'PUT';
                    form.submit({
                        url: m_training,
                        method: methode,
                        waitMsg: lang('Sending data...'),
                        success: function(fp, o) {
                            Ext.MessageBox.alert('Success', lang('Data saved.'));
                            if (methode == 'POST')
                                Ext.getCmp('idt').setValue(o.result.idt);
                            store_training.load({
                                params: {
                                    cpg_id: Ext.getCmp('id').getValue()
                                }
                            });
                        }
                    });
                }
            }, {
                text: lang('Close'),
                margin: '5px',
                scale: 'large',
                ui: 's-button',
                cls: 's-grey',
                disabled: false,
                handler: function() {
                    winPar.hide();
                    store_training.load({
                        params: {
                            cpg_id: Ext.getCmp('id').getValue()
                        }
                    });
                }
            }]
    });
    // Training participant panel container
    var DataFormParCheckList = Ext.create('Ext.form.Panel', {
        height: '100%',
        width: '100%',
        autoScroll: true,
        id: 'dataFormParCheckList',
        fieldDefaults: {
            labelAlign: 'left',
            labelWidth: 120,
            anchor: '100%'
        },
        items: [
            {
                layout: 'column',
                border: false,
                items: [{
                        columnWidth: .5,
                        layout: 'form',
                        padding: 5,
                        border: false,
                        items: [
                            {
                                xtype: 'textfield',
                                id: 'parcheklist_farmerid',
                                fieldLabel: lang('Farmer ID'),
                                readOnly: true
                            },
                            {
                                xtype: 'textfield',
                                id: 'parcheklist_farmename',
                                fieldLabel: lang('Farmer Name'),
                                readOnly: true
                            },
                            {
                                xtype: 'textfield',
                                id: 'parcheklist_groupname',
                                fieldLabel: lang('Farmer Group Name'),
                                readOnly: true
                            },
                        ]
                    }, {
                        columnWidth: .45,
                        layout: 'form',
                        padding: 5,
                        border: false,
                        items: [
                            {
                                xtype: 'textfield',
                                id: 'parcheklist_trainingdays',
                                fieldLabel: lang('Training Days'),
                                readOnly: true
                            },
                            {
                                xtype: 'textfield',
                                id: 'parcheklist_startdate',
                                fieldLabel: lang('Start Date'),
                                readOnly: true
                            },
                            {
                                xtype: 'textfield',
                                id: 'parcheklist_enddate',
                                fieldLabel: lang('End Date'),
                                readOnly: true
                            },
                        ]
                    }]
            }, {
                xtype: 'gridpanel',
                style: 'border:1px solid #CCC;',
                id: 'grid_participant_checklist',
                store: store_participant_checklist,
                width: '100%',
                //loadMask: true,
                selType: 'rowmodel',
                plugins: [new Ext.grid.plugin.CellEditing({clicksToEdit: 1})],
                columns: [
                    {
                        text: lang('Hari Pertemuan'),
                        dataIndex: 'DayNumber',
                        renderer: function(value) {
                            return lang('Pertemuan') + ' ' + value;
                        },
                        flex: 3,
                    }, {
                        text: lang('Training Date'),
                        dataIndex: 'TrainingDate',
                        xtype: 'datecolumn',
                        format:'Y-m-d',
                        //renderer: Ext.util.Format.dateRenderer('d M Y'),
                        editor: {
                            xtype: 'datefield',
                            id: 'TrainingDate',
                            format: 'Y-m-d',
                            submitFormat: 'Y-m-d',
                            minValue: '2010-01-01',
                            // disabledDays: [0, 6],
                            // disabledDaysText: 'Plants are not available on the weekends'
                        },
                        flex: 1,
                    }, {
                        text: lang('Pagi'),
                        id: 'sinTDayAttendancePagi',
                        dataIndex: 'Attendance1',
                        xtype: 'checkcolumn',
                        flex: 1,
                    }, {
                        text: lang('Siang'),
                        id: 'sinTDayAttendanceSiang',
                        dataIndex: 'Attendance2',
                        xtype: 'checkcolumn',
                        flex: 1,
                    },
                ],
            }
        ],
        buttons: [
            {
                id: 'save_par_check',
                text: lang('Save'),
                margin: '5px',
                scale: 'large',
                ui: 's-button',
                cls: 's-blue ',
                handler: function() {
                    var sm = Ext.getCmp('grid_participant').getSelectionModel().getSelection();
                    var data = [];
                    $.each(Ext.getCmp('grid_participant_checklist').getStore().data.items, function(index, val) {
                        val.data.TrainingDate = Ext.util.Format.date(val.data.TrainingDate,'Y-m-d');
                        //console.log(val.data.TrainingDate);
                        data.push(val.data);
                    });
                    console.log(data);
                    $.ajax({
                        url: m_attendance,
                        type: 'POST',
                        data: {
                            CpgBatchTrainingID: sm[0].data.CpgBatchTrainingID,
                            FarmerID: sm[0].data.pFarmerID,
                            data: data
                        },
                    })
                            .done(function() {
                        Ext.MessageBox.alert(lang('Info'), lang('Attendance saved'));
                    })
                            .fail(function() {
                        Ext.MessageBox.alert(lang('Warning'), lang('Failed to save attendance'));
                    })
                            .always(function() {
                        // console.log("complete");
                    });
                }
            }, {
                text: lang('Close'),
                margin: '5px',
                scale: 'large',
                ui: 's-button',
                cls: 's-grey',
                disabled: false,
                handler: function() {
                    winParCheckList.hide();
                }
            }
        ]
    });
    var DataFormParCheckListDay = Ext.create('Ext.form.Panel', {
        height: '100%',
        width: '100%',
        autoScroll: true,
        id: 'dataFormParCheckListDay',
        fieldDefaults: {
            labelAlign: 'left',
            labelWidth: 120,
            anchor: '100%'
        },
        items: [
            {
                layout: 'column',
                border: false,
                items: [
                    {
                        columnWidth: .5,
                        layout: 'form',
                        padding: 5,
                        border: false,
                        items: [
                            {
                                xtype: 'hiddenfield',
                                id: 'parcheklistday_cpgbatchtrainingid',
                                name: 'CpgBatchTrainingID',
                            },
                            {
                                xtype: 'textfield',
                                id: 'parcheklistday_groupname',
                                fieldLabel: lang('Farmer Group'),
                                readOnly: true
                            },
                            {
                                xtype: 'textfield',
                                id: 'parcheklistday_batch',
                                fieldLabel: lang('Farmer Group Batch'),
                                readOnly: true
                            },
                            {
                                xtype: 'textfield',
                                id: 'parcheklistday_training_day',
                                fieldLabel: lang('Training Day'),
                                readOnly: true
                            },
                        ]
                    },
                    {
                        columnWidth: .5,
                        layout: 'form',
                        padding: 5,
                        border: false,
                        items: [
                            {
                                xtype: 'textfield',
                                id: 'parcheklistday_training_name',
                                fieldLabel: lang('Training Name'),
                                readOnly: true
                            },
                            {
                                layout: 'hbox',
                                border: false,
                                padding: 0,
                                items: [
                                    {
                                        flex: 3,
                                        xtype: 'panel',
                                        items: [
                                            {
                                                xtype: 'textfield',
                                                id: 'parcheklistday_startdate',
                                                fieldLabel: lang('Training Period'),
                                                readOnly: true
                                            },
                                        ]
                                    },
                                    {
                                        flex: 2,
                                        xtype: 'panel',
                                        items: [
                                            {
                                                xtype: 'textfield',
                                                id: 'parcheklistday_enddate',
                                                fieldLabel: lang('Until'),
                                                labelWidth: 50,
                                                readOnly: true
                                            },
                                        ]
                                    },
                                    {
                                        flex: 1,
                                        xtype: 'panel',
                                        items: [
                                            {
                                                xtype: 'textfield',
                                                id: 'parcheklistday_daycount',
                                                fieldLabel: lang('Days'),
                                                labelWidth: 50,
                                                readOnly: true
                                            },
                                        ]
                                    },
                                ]
                            },
                            {
                                xtype: 'textfield',
                                id: 'parcheklistday_training_date',
                                fieldLabel: lang('Training Date'),
                                readOnly: true
                            },
                        ]
                    }
                ]
            },
            {
                xtype: 'gridpanel',
                style: 'border:1px solid #CCC;',
                id: 'grid_participant_checklist_day',
                store: store_participant_checklist_day,
                width: '100%',
                //loadMask: true,
                selType: 'rowmodel',
                plugins: [new Ext.grid.plugin.CellEditing({clicksToEdit: 1})],
                listeners: {
                    itemclick: function(dv, record, item, index, e) {
                        console.log(record.data.FarmerID);
                        mc_family.load({
                            params: {
                                key: record.data.FarmerID
                            }
                        });
                    },
                },
                columns: [
                    {
                        text: '#',
                        xtype: 'rownumberer',
                        width: 50,
                    },
                    {
                        text: '#',
                        dataIndex: 'FarmerID',
                        hidden: true
                    },
                    {
                        text: '#',
                        dataIndex: 'FamilyID',
                        hidden: true
                    },
                    {
                        text: lang('Participant Name (Farmer)'),
                        dataIndex: 'FarmerName',
                        flex: 3,
                    },
                    {
                        text: lang('Participant Substitute (Family)'),
                        dataIndex: 'AnggotaName',
                        flex: 3,
                        editor: {
                            xtype: 'combobox',
                            displayField: 'label',
                            valueField: 'label',
                            queryMmode: 'local',
                            store: mc_family,
                        }
                    },
                    {
                        text: lang('Pagi'),
                        dataIndex: 'Attendance1',
                        xtype: 'checkcolumn',
                        flex: 1,
                    },
                    {
                        text: lang('Siang'),
                        dataIndex: 'Attendance2',
                        xtype: 'checkcolumn',
                        flex: 1,
                    },
                ],
            }
        ],
        buttons: [
            {
                id: 'save_par_check_day',
                text: lang('Save'),
                margin: '5px',
                scale: 'large',
                ui: 's-button',
                cls: 's-blue ',
                handler: function() {
                    // var sm = Ext.getCmp('grid_participant_').getSelectionModel().getSelection();
                    var data = [];
                    $.each(Ext.getCmp('grid_participant_checklist_day').getStore().data.items, function(index, val) {
                        // val.data.TrainingDate = Ext.util.Format.date(val.data.TrainingDate,'Y-m-d');
                        //console.log(val.data.TrainingDate);
                        data.push(val.data);
                    });
                    // console.log(data);
                    $.ajax({
                        url: m_attendance_day,
                        type: 'POST',
                        data: {
                            CpgBatchTrainingID: Ext.getCmp('idt').getValue(),
                            DayNumber: Ext.getCmp('TrainingDay').getValue(),
                            TrainingDate: Ext.Date.format(Ext.getCmp('TrainingDate2').getValue(),'Y-m-d'),
                            data: data
                        },
                    })
                        .done(function() {
                        Ext.MessageBox.alert(lang('Info'), lang('Attendance saved'));
                    })
                        .fail(function() {
                        Ext.MessageBox.alert(lang('Warning'), lang('Failed to save attendance'));
                    })
                        .always(function() {
                        // console.log("complete");
                    });
                }
            }, {
                text: lang('Close'),
                margin: '5px',
                scale: 'large',
                ui: 's-button',
                cls: 's-grey',
                disabled: false,
                handler: function() {
                    winParCheckListDay.hide();
                }
            }
        ]
    });

    var DataWinSelectDay = Ext.create('Ext.form.Panel', {
        height: '100%',
        width: '100%',
        autoScroll: true,
        padding: 10,
        id: 'dataWinSelectDay',
        fieldDefaults: {
            labelAlign: 'left',
            labelWidth: 120,
            anchor: '100%'
        },
        items: [
            {
                xtype: 'numberfield',
                fieldLabel: lang('Training Day'),
                id: 'TrainingDay',
                name: 'TrainingDay',
                displayField: 'label',
                valueField: 'id',
                queryMode: 'local',
                minValue: 1,
                allowBlank: false
            },
            {
                xtype: 'datefield',
                format: 'Y-m-d',
                anchor: '100%',
                fieldLabel: lang('Training Date'),
                id: 'TrainingDate2',
                name: 'TrainingDate',
                // maxValue: new Date(),
                allowBlank: false
            }
        ],
        buttons: [
            {
                text: lang('Select'),
                margin: '5px',
                scale: 'large',
                ui: 's-button',
                cls: 's-blue ',
                handler: function() {
                    var form = this.up('form').getForm();
                    if (form.isValid()) {
                        displayFormWindowParticipantCheckListDay();
                        Ext.getCmp('parcheklistday_training_day').setValue(Ext.getCmp('TrainingDay').getValue());
                        var date = new Date(Ext.getCmp('TrainingDate2').getValue());
                        Ext.getCmp('parcheklistday_training_date').setValue(Ext.Date.format(date, 'Y-m-d'));

                        store_participant_checklist_day.load({
                            params: {
                                CpgBatchTrainingID: Ext.getCmp('idt').getValue(),
                                DayNumber: Ext.getCmp('TrainingDay').getValue(),
                            }
                        });

                        var TrainingDayStatusHalf = Ext.getCmp('TrainingDayStatusHalf').getValue();
                        if(TrainingDayStatusHalf == true){
                            Ext.getCmp('grid_participant_checklist_day').down('[dataIndex=Attendance2]').setVisible(false);
                            Ext.getCmp('grid_participant_checklist_day').down('[dataIndex=Attendance1]').setText(lang('Kehadiran'))
                        }else{
                            Ext.getCmp('grid_participant_checklist_day').down('[dataIndex=Attendance1]').setVisible(true);
                            Ext.getCmp('grid_participant_checklist_day').down('[dataIndex=Attendance2]').setVisible(true);

                            Ext.getCmp('grid_participant_checklist_day').down('[dataIndex=Attendance1]').setText(lang('Pagi'));
                            Ext.getCmp('grid_participant_checklist_day').down('[dataIndex=Attendance2]').setText(lang('Siang'));
                        }
                    }
                }
            }, {
                text: lang('Close'),
                margin: '5px',
                scale: 'large',
                ui: 's-button',
                cls: 's-grey',
                disabled: false,
                handler: function() {
                    winSelectDay.hide();
                }
            }
        ]
    }
    );


    var winPar = Ext.widget('window', {
        title: lang('Data Training'),
        id: 'winpar',
        closeAction: 'hide',
        width: '80%',
        height: '90%',
        autoScroll: true,
        modal: true,
        layout: 'fit',
        items: [DataFormPar]
    });

    var winParCheckList = Ext.widget('window', {
        title: lang('Daftar Hadir'),
        id: 'winparchecklist',
        closeAction: 'hide',
        width: '60%',
        height: '70%',
        autoScroll: true,
        modal: true,
        layout: 'fit',
        items: [DataFormParCheckList]
    });

    var winParCheckListDay = Ext.widget('window', {
        title: lang('Daftar Hadir'),
        id: 'winparchecklistday',
        closeAction: 'hide',
        width: '70%',
        height: '70%',
        autoScroll: true,
        modal: true,
        layout: 'fit',
        items: [DataFormParCheckListDay]
    });

    var winSelectDay = Ext.widget('window', {
        title: lang('Training Day List'),
        id: 'winSelectDay',
        closeAction: 'hide',
        width: 500,
        height: 200,
        autoScroll: true,
        modal: true,
        layout: 'fit',
        items: [DataWinSelectDay]
    });

    var DataFormParAdd = Ext.create('Ext.panel.Panel', {
        height: '100%',
        //autoScroll: true,
        overflowY: 'auto',
        width: '100%',
        //bodyPadding: 5,
        id: 'dataFormParAdd',
        items: [{
                xtype: 'gridpanel',
                id: 'grid_participant_add',
                store: store_participant_add,
                loadMask: true,
                dockedItems: [{
                        xtype: 'toolbar',
                        items: [{
                                xtype: 'textfield',
                                name: 'keyAddPart',
                                id: 'keyAddPart',
                                emptyText: 'Cari berdasar nama/ID',
                                width: 200,
                                listeners: {}
                            }, {
                                xtype: 'button',
                                icon: varjs.config.base_url + 'images/icons/silk/search.png',
                                margin: '0px 0px 0px 6px',
                                text: lang('Search'),
                                handler: function() {
                                    store_participant_add.load({
                                        params: {
                                            CpgBatchTrainingID: Ext.getCmp('idt').getValue(),
                                            cpgID: Ext.getCmp('idd').getValue(),
                                            keyAddPart: Ext.getCmp('keyAddPart').getValue()
                                        }
                                    });
                                }
                            }]
                    }],
                selType: 'checkboxmodel',
                selModel: {
                    checkOnly: true,
                    mode: "MULTI",
                    headerWidth: '10%'
                },
                columns: [{
                        text: lang('NAME'),
                        dataIndex: 'addFarmerName',
                        width: '50%'
                    }, {
                        text: lang('ID'),
                        dataIndex: 'addFarmerID',
                        width: '40%'
                    }]
            }],
        buttons: [{
                id: 'save_par_add',
                text: lang('Save'),
                margin: '5px',
                scale: 'large',
                ui: 's-button',
                cls: 's-blue ',
                handler: function() {
                    var participants = '';
                    Ext.each(Ext.getCmp('grid_participant_add').getSelectionModel().getSelection(), function(row, index, value) {
                        //participants.push(row.data.addFarmerID);
                        participants = participants + ',' + row.data.addFarmerID;
                    });
                    if (participants != '') {
                        Ext.Ajax.request({
                            url: m_participant,
                            method: 'POST',
                            waitMsg: lang('Sending data...'),
                            params: {
                                CpgBatchTrainingID: Ext.getCmp('idt').getValue(),
                                participants: participants,
                                PetaniKakao: Ext.getCmp('PetaniKakao').getValue()
                            },
                            success: function(response, opts) {
                                var obj = Ext.decode(response.responseText);
                                switch (obj.success) {
                                    case true:
                                        store_participant.load({
                                            params: {
                                                key: Ext.getCmp('idt').getValue()
                                            }
                                        });
                                        winAddPar.hide();
                                        break;
                                    default:
                                        Ext.MessageBox.alert('Warning', obj.message);
                                        break;
                                }
                            }
                        });
                    } else {
                        Ext.Msg.alert("Warning", "Please select participants");
                    }
                }
            }, {
                text: lang('Close'),
                margin: '5px',
                scale: 'large',
                ui: 's-button',
                cls: 's-grey',
                disabled: false,
                handler: function() {
                    winAddPar.hide();
                }
            }]
    });
    var winAddPar = Ext.widget('window', {
        title: lang('Add Participants'),
        id: 'winParAdd',
        closeAction: 'hide',
        height: '70%',
        width: '24%',
        autoScroll: true,
        modal: true,
        layout: 'fit',
        items: [DataFormParAdd]
    });

    //cetak
    var DataBeforeCetak = Ext.create('Ext.form.Panel', {
        autoScroll: true,
        width: 420,
        height: 100,
        id: 'dataBeforeCetak',
        xtype: 'form',
        bodyPadding: 5,
        layout: {
            align: 'stretch',
            type: 'vbox'
        },
        header: false,
        title: lang('My Form'),
        items: [{
                xtype: 'combobox',
                id: 'survey',
                name: 'id',
                store: store_CekSurvey,
                fieldLabel: lang('Survey'),
                displayField: 'surveya',
                valueField: 'id',
                queryMode: 'local',
                listeners: {
                    change: function(cb, nv, ov) {
                        SurveyID = nv
                        //console.log(SurveyID);
                    }
                }
            },
            {
                xtype: 'container',
                height: 43,
                layout: {
                    align: 'stretch',
                    pack: 'center',
                    padding: 2,
                    type: 'hbox'
                },
                items: [
                    {
                        id: 'h_p1',
                        xtype: 'button',
                        text: lang('Cetak'),
                        margin: '5 5 5 2',
                        scale: 'large',
                        ui: 's-button',
                        disabled: false,
                        cls: 's-blue',
                        handler: function() {
                            //winPar.hide();
                            //win.hide();
                            if (!isNumber(SurveyID)) {
                                Ext.MessageBox.alert('Warning', lang('Silahkan pilih surveynya'));
                                return;
                            }
                            winBeforeCetak.hide();
                            preview_cetak_surat(m_cetak_basic_farmer + 'CpgBatchTrainingID/' + CpgBatchTrainingID + '/SurveyID/' + SurveyID);
                        }
                    }, {
                        id: 'h_f1',
                        xtype: 'button',
                        text: lang('Cetak'),
                        margin: '5 5 5 2',
                        scale: 'large',
                        ui: 's-button',
                        disabled: false,
                        cls: 's-blue',
                        handler: function() {
                            //winPar.hide();
                            //win.hide();
                            if (!isNumber(SurveyID)) {
                                Ext.MessageBox.alert('Warning', lang('Silahkan pilih surveynya'));
                                return;
                            }
                            winBeforeCetak.hide();
                            preview_cetak_surat(m_cetak_basic_aff + 'CpgBatchTrainingID/' + CpgBatchTrainingID + '/SurveyID/' + SurveyID);
                        }
                    },
                    {
                        id: 'h_n1',
                        xtype: 'button',
                        text: lang('Cetak'),
                        margin: '5px',
                        scale: 'large',
                        ui: 's-button',
                        cls: 's-blue',
                        disabled: false,
                        handler: function() {
                            // winPar.hide();
                            //win.hide();
                            if (!isNumber(SurveyID)) {
                                Ext.MessageBox.alert('Warning', lang('Silahkan pilih surveynya'));
                                return;
                            }
                            winBeforeCetak.hide();
                            preview_cetak_surat(m_cetak_basic_nutrisi + 'CpgBatchTrainingID/' + CpgBatchTrainingID + '/SurveyID/' + SurveyID);
                        }
                    },
                    {
                        id: 'h_ppi',
                        xtype: 'button',
                        text: lang('Cetak'),
                        margin: '5px',
                        scale: 'large',
                        ui: 's-button',
                        cls: 's-blue',
                        disabled: false,
                        handler: function() {
                            winPar.hide();
                            win.hide();
                            if (!isNumber(SurveyID)) {
                                Ext.MessageBox.alert('Warning', lang('Silahkan pilih surveynya'));
                                return;
                            }
                            winBeforeCetak.hide();
                            preview_cetak_surat(m_cetak_basic_ppi2012 + 'CpgBatchTrainingID/' + CpgBatchTrainingID + '/SurveyID/' + SurveyID);
                        }
                    },
                    {
                        xtype: 'button',
                        text: lang('Batal'),
                        margin: '5px',
                        scale: 'large',
                        ui: 's-button',
                        cls: 's-blue',
                        disabled: false,
                        handler: function() {
                            winBeforeCetak.hide();
                        }
                    }
                ]
            }
        ]
    });

    var DataBeforeCetakAttendanceList = Ext.create('Ext.form.Panel', {
        autoScroll: true,
        width: 420,
        height: 100,
        id: 'dataBeforeCetakAttendanceList',
        xtype: 'form',
        bodyPadding: 5,
        layout: {
            align: 'stretch',
            type: 'vbox'
        },
        header: false,
        title: lang('Print Attendance List'),
        items: [{
                xtype: 'combobox',
                id: 'DayNumber',
                name: 'DayNumber',
                store: store_DayNumber,
                fieldLabel: lang('Day Number'),
                displayField: 'id',
                valueField: 'id',
                queryMode: 'local',
                listeners: {

                }
            },
            {
                xtype: 'container',
                height: 43,
                layout: {
                    align: 'stretch',
                    pack: 'center',
                    padding: 2,
                    type: 'hbox'
                },
                items: [
                    {
                        id: 'h_AttendanceList',
                        xtype: 'button',
                        text: lang('Cetak'),
                        margin: '5 5 5 2',
                        scale: 'large',
                        ui: 's-button',
                        disabled: false,
                        cls: 's-blue',
                        handler: function() {
                            var DayNumber = Ext.getCmp('DayNumber').getValue();
                            if (!isNumber(DayNumber)) {
                                Ext.MessageBox.alert('Warning', lang('Silahkan pilih Hari'));
                                return;
                            }
                            winBeforeCetakAttendanceList.hide();
                            //preview_cetak_surat(m_cetak_basic_farmer + 'CpgBatchTrainingID/' + CpgBatchTrainingID + '/SurveyID/' + SurveyID);
                            preview_cetak_surat(m_cetak + Ext.getCmp('idt').getValue()+ '/DayNumber/' + DayNumber);
                        }
                    },
                    {
                        xtype: 'button',
                        text: lang('Batal'),
                        margin: '5px',
                        scale: 'large',
                        ui: 's-button',
                        cls: 's-blue',
                        disabled: false,
                        handler: function() {
                            winBeforeCetakAttendanceList.hide();
                        }
                    }
                ]
            }
        ]
    });

    //end cetak
    var winBeforeCetak = Ext.create('widget.window', {
        id: 'print',
        closable: true,
        modal: true,
        layout: 'fit',
        closeAction: 'show',
        width: 450,
        height: 130,
        items: [DataBeforeCetak]
    });

    var winBeforeCetakAttendanceList = Ext.create('widget.window', {
        id: 'printAttendanceList',
        closable: true,
        modal: true,
        layout: 'fit',
        closeAction: 'show',
        width: 450,
        height: 130,
        items: [DataBeforeCetakAttendanceList]
    });

    if (m_add != '') {
        var adde = m_add.split('-')
        Ext.getCmp('key').setValue(adde[1])
        // Ext.getCmp('Kab').setValue(decodeURI(adde[0]))
        Ext.getCmp('grid').fireEvent('itemdblclick', Ext.getCmp('grid'), 0)
    }

});

function checkImageExists(imageUrl, callBack) {
    var imageData = new Image();
    imageData.onload = function() {
        callBack(true);
    };
    imageData.onerror = function() {
        callBack(false);
    };
    imageData.src = imageUrl;
}