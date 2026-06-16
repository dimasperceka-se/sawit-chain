if (Ext.getCmp('win')) {Ext.getCmp('win').destroy()}
if (Ext.getCmp('winCompostPenjualan')) {Ext.getCmp('winCompostPenjualan').destroy()}
if (Ext.getCmp('winClonalGardenPolygonCoop')) {Ext.getCmp('winClonalGardenPolygonCoop').destroy()}
if (Ext.getCmp('winNurseyPenjualan')) {Ext.getCmp('winNurseyPenjualan').destroy()}
if (Ext.getCmp('winIcs')) {Ext.getCmp('winIcs').destroy()}
if (Ext.getCmp('winClonalGarden_idcoop')) {Ext.getCmp('winClonalGarden_idcoop').destroy()}
if (Ext.getCmp('winNurseyCoopList')) {Ext.getCmp('winNurseyCoopList').destroy()}
if (Ext.getCmp('winNurseyCoop')) {Ext.getCmp('winNurseyCoop').destroy()}
if (Ext.getCmp('winTrainingList')) {Ext.getCmp('winTrainingList').destroy()}
if (Ext.getCmp('winparchecklist')) Ext.getCmp('winparchecklist').destroy();
if (Ext.getCmp('printAttendanceList')) Ext.getCmp('printAttendanceList').destroy();

Ext.Loader.setConfig({enabled: true});
Ext.Loader.setPath('js/ext-4.2.0.663', '../ux');
Ext.require([
    'Ext.ux.form.ItemSelector'
]);

Ext.onReady(function() {
    Ext.tip.QuickTipManager.init();
    var store = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['CoopID', 'CoopCode', 'CoopName', 'Phone', 'Email', 'TahunTerbentuk', 'Status', 'District'],
        autoLoad: true,
        pageSize: 50,
        proxy: {
            type: 'ajax',
            url: m_crud + 's',
            extraParams: {
                prov: m_param,
                kab: m_DistrictID,
                kec: m_SubDistrictID,
            },
            reader: {
                type: 'json',
                root: 'data',
                totalProperty: 'total'
            }
        }
    });
    store.on('beforeload', function() {
        var proxy = store.getProxy();
        var isAdvFilter = Ext.getCmp('idPanelAdvFilter').isVisible();
        if (isAdvFilter == true) {
            var opsiSearch = 'adv';
        } else {
            var opsiSearch = 'simple';
        }
        if (opsiSearch == 'simple') {
            proxy.setExtraParam('opsiSearch', opsiSearch);
            proxy.setExtraParam('key', Ext.getCmp('key').getValue());
            // proxy.setExtraParam('kab', Ext.getCmp('sKabupaten').getValue());
            // proxy.setExtraParam('prov', Ext.getCmp('sProvinsi').getValue());
        }
        if (opsiSearch == 'adv') {
            //cek dipilih atau kaga
            if (Ext.getCmp('rowNama').isVisible() == true) {
                var parAdvNama = Ext.getCmp('advNama').getValue();
            } else {
                var parAdvNama = 'not_set';
            }
            if (Ext.getCmp('rowTahun').isVisible() == true) {
                var parAdvOpTahun = Ext.getCmp('advOpTahun').getValue();
                var parAdvTahun = Ext.getCmp('advTahun').getValue();
            } else {
                var parAdvOpTahun = 'not_set';
                var parAdvTahun = 'not_set';
            }
            if (Ext.getCmp('rowStatus').isVisible() == true) {
                var parAdvStatus = Ext.getCmp('advStatus').getValue().join().replace(/,/g, '::');
            } else {
                var parAdvStatus = 'not_set';
            }
            if (Ext.getCmp('rowDistrict').isVisible() == true) {
                var parAdvDistrict = Ext.getCmp('advDistrict').getValue().join().replace(/,/g, '::');
            } else {
                var parAdvDistrict = 'not_set';
            }
            if (Ext.getCmp('rowTglModified').isVisible() == true) {
                var parAdvTglModiStart = Ext.getCmp('advTglModiStart').getValue();
                var parAdvTglModiEnd = Ext.getCmp('advTglModiEnd').getValue();
            } else {
                var parAdvTglModiStart = 'not_set';
                var parAdvTglModiEnd = 'not_set';
            }
            proxy.setExtraParam('opsiSearch', opsiSearch);
            proxy.setExtraParam('advNama', parAdvNama);
            proxy.setExtraParam('advOpTahun', parAdvOpTahun);
            proxy.setExtraParam('advTahun', parAdvTahun);
            proxy.setExtraParam('advStatus', parAdvStatus);
            proxy.setExtraParam('advDistrict', parAdvDistrict);
            proxy.setExtraParam('advTglModiStart', parAdvTglModiStart);
            proxy.setExtraParam('advTglModiEnd', parAdvTglModiEnd);
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
            extraParams: {
                prov: m_param
            },
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
            extraParams: {
                prov: m_param
            },
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });

    var mc_Province = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'label'],
        autoLoad: true,
        pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_Provinsi,
            extraParams: {
                prov: m_param
            },
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });

    var mc_District = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'label'],
        autoLoad: true,
        pageSize: 10,
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
    // mc_Kabupaten.on('load', function(st) {
    //     Ext.getCmp('sKabupaten').setValue(Ext.getCmp('sKabupaten').store.getAt(0).get('label'))
    //     store.load({
    //         params: {
    //             prov: m_param,
    //             kab: Ext.getCmp('sKabupaten').store.getAt(0).get('label')
    //         }
    //     });
    // });

    var mc_Kecamatan = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'label'],
        autoLoad: true,
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
    var mc_Desa = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['label', 'id'],
        autoLoad: true,
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
    var mc_clone_type_combo = Ext.create('Ext.data.Store', {
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

    function display_area(GardenNr) {
        var areaPanel = Ext.getCmp('areawindow_idcoop');
        areaPanel.center();
        areaPanel.show();
        Ext.Ajax.request({
            url: m_clonal + '_polygon/coop',
            method: 'GET',
            params: {
                clonal_id: Ext.getCmp('ClonalID_idcoop').getValue(),
                garden_nr: GardenNr,
                lati: Ext.getCmp('ClonalGardenLatitude_idcoop').getValue(),
                longi: Ext.getCmp('ClonalGardenLongitude_idcoop').getValue(),
                cooplat: Ext.getCmp('Latitude').getValue(),
                cooplong: Ext.getCmp('Longitude').getValue(),
                hakAksesPolygon: m_hakakses_polygon
            },
            success: function(response) {
                var htmlText = response.responseText;
                //Get the Panel component using its id
                // update the panel content's with
                // HTML response from Ajax call
                areaPanel.update(htmlText, true);
            }
        });
    }

    function CheckClonalGardenCoop(thi) {
        if (thi != undefined) {
            if (thi.value == '1' || thi.value != ''){
                Ext.getCmp(thi.id.replace('_idcoop','') + 'Nr_idcoop').setDisabled(false);
            }else{
                Ext.getCmp(thi.id.replace('_idcoop','') + 'Nr_idcoop').setDisabled(true);
                Ext.getCmp(thi.id.replace('_idcoop','') + 'Nr_idcoop').setValue('');

            }
        }
    }

    function JumlahClonalGardenCoop() {
        var total = eval((isNaN(parseFloat(Ext.getCmp('TSH858Nr_idcoop').getValue())) ? 0 : parseFloat(Ext.getCmp('TSH858Nr_idcoop').getValue())) + (isNaN(parseFloat(Ext.getCmp('RCC70Nr_idcoop').getValue())) ? 0 : parseFloat(Ext.getCmp('RCC70Nr_idcoop').getValue())) + (isNaN(parseFloat(Ext.getCmp('RCC71Nr_idcoop').getValue())) ? 0 : parseFloat(Ext.getCmp('RCC71Nr_idcoop').getValue())) + (isNaN(parseFloat(Ext.getCmp('RCC72Nr_idcoop').getValue())) ? 0 : parseFloat(Ext.getCmp('RCC72Nr_idcoop').getValue())) + (isNaN(parseFloat(Ext.getCmp('RCC73Nr_idcoop').getValue())) ? 0 : parseFloat(Ext.getCmp('RCC73Nr_idcoop').getValue())) + (isNaN(parseFloat(Ext.getCmp('LocalNr_idcoop').getValue())) ? 0 : parseFloat(Ext.getCmp('LocalNr_idcoop').getValue())) + (isNaN(parseFloat(Ext.getCmp('S1Nr_idcoop').getValue())) ? 0 : parseFloat(Ext.getCmp('S1Nr_idcoop').getValue())) + (isNaN(parseFloat(Ext.getCmp('S2Nr_idcoop').getValue())) ? 0 : parseFloat(Ext.getCmp('S2Nr_idcoop').getValue())) + (isNaN(parseFloat(Ext.getCmp('ICCRI3Nr_idcoop').getValue())) ? 0 : parseFloat(Ext.getCmp('ICCRI3Nr_idcoop').getValue())) + (isNaN(parseFloat(Ext.getCmp('ICCRI4Nr_idcoop').getValue())) ? 0 : parseFloat(Ext.getCmp('ICCRI4Nr_idcoop').getValue())) + (isNaN(parseFloat(Ext.getCmp('ICCRI5Nr_idcoop').getValue())) ? 0 : parseFloat(Ext.getCmp('ICCRI5Nr_idcoop').getValue())) + (isNaN(parseFloat(Ext.getCmp('RCLNr_idcoop').getValue())) ? 0 : parseFloat(Ext.getCmp('RCLNr_idcoop').getValue())) + (isNaN(parseFloat(Ext.getCmp('M01Nr_idcoop').getValue())) ? 0 : parseFloat(Ext.getCmp('M01Nr_idcoop').getValue())) + (isNaN(parseFloat(Ext.getCmp('M06Nr_idcoop').getValue())) ? 0 : parseFloat(Ext.getCmp('M06Nr_idcoop').getValue())) + (isNaN(parseFloat(Ext.getCmp('THRNr_idcoop').getValue())) ? 0 : parseFloat(Ext.getCmp('THRNr_idcoop').getValue())) + (isNaN(parseFloat(Ext.getCmp('CG45Nr_idcoop').getValue())) ? 0 : parseFloat(Ext.getCmp('CG45Nr_idcoop').getValue())) + (isNaN(parseFloat(Ext.getCmp('ScavinaNr_idcoop').getValue())) ? 0 : parseFloat(Ext.getCmp('ScavinaNr_idcoop').getValue())) + (isNaN(parseFloat(Ext.getCmp('BLBNr_idcoop').getValue())) ? 0 : parseFloat(Ext.getCmp('BLBNr_idcoop').getValue())) + (isNaN(parseFloat(Ext.getCmp('BB01Nr_idcoop').getValue())) ? 0 : parseFloat(Ext.getCmp('BB01Nr_idcoop').getValue())) + (isNaN(parseFloat(Ext.getCmp('M04Nr_idcoop').getValue())) ? 0 : parseFloat(Ext.getCmp('M04Nr_idcoop').getValue())) + (isNaN(parseFloat(Ext.getCmp('MTNr_idcoop').getValue())) ? 0 : parseFloat(Ext.getCmp('MTNr_idcoop').getValue())) + (isNaN(parseFloat(Ext.getCmp('M02Nr_idcoop').getValue())) ? 0 : parseFloat(Ext.getCmp('M02Nr_idcoop').getValue())) + (isNaN(parseFloat(Ext.getCmp('APNr_idcoop').getValue())) ? 0 : parseFloat(Ext.getCmp('APNr_idcoop').getValue())) + (isNaN(parseFloat(Ext.getCmp('PRNr_idcoop').getValue())) ? 0 : parseFloat(Ext.getCmp('PRNr_idcoop').getValue())) + (isNaN(parseFloat(Ext.getCmp('BRTNr_idcoop').getValue())) ? 0 : parseFloat(Ext.getCmp('BRTNr_idcoop').getValue())) + (isNaN(parseFloat(Ext.getCmp('MHP03Nr_idcoop').getValue())) ? 0 : parseFloat(Ext.getCmp('MHP03Nr_idcoop').getValue())) + (isNaN(parseFloat(Ext.getCmp('MHP04Nr_idcoop').getValue())) ? 0 : parseFloat(Ext.getCmp('MHP04Nr_idcoop').getValue())) + (isNaN(parseFloat(Ext.getCmp('OtherClonesNr_idcoop').getValue())) ? 0 : parseFloat(Ext.getCmp('OtherClonesNr_idcoop').getValue())));
        Ext.getCmp('TotalClonesNr_idcoop').setValue(total);
    }

    function JumlahShadeTreesCoop() {
        var total = eval((isNaN(parseFloat(Ext.getCmp('CoconutNr_idcoop').getValue())) ? 0 : parseFloat(Ext.getCmp('CoconutNr_idcoop').getValue())) + (isNaN(parseFloat(Ext.getCmp('ArecaPalmNr_idcoop').getValue())) ? 0 : parseFloat(Ext.getCmp('ArecaPalmNr_idcoop').getValue())) + (isNaN(parseFloat(Ext.getCmp('RubberNr_idcoop').getValue())) ? 0 : parseFloat(Ext.getCmp('RubberNr_idcoop').getValue())) + (isNaN(parseFloat(Ext.getCmp('CloveNr_idcoop').getValue())) ? 0 : parseFloat(Ext.getCmp('CloveNr_idcoop').getValue())) + (isNaN(parseFloat(Ext.getCmp('CashewNr_idcoop').getValue())) ? 0 : parseFloat(Ext.getCmp('CashewNr_idcoop').getValue())) + (isNaN(parseFloat(Ext.getCmp('OilPalmNr_idcoop').getValue())) ? 0 : parseFloat(Ext.getCmp('OilPalmNr_idcoop').getValue())) + (isNaN(parseFloat(Ext.getCmp('SugarPalmNr_idcoop').getValue())) ? 0 : parseFloat(Ext.getCmp('SugarPalmNr_idcoop').getValue())) + (isNaN(parseFloat(Ext.getCmp('NutmegNr_idcoop').getValue())) ? 0 : parseFloat(Ext.getCmp('NutmegNr_idcoop').getValue())) + (isNaN(parseFloat(Ext.getCmp('HazelnutNr_idcoop').getValue())) ? 0 : parseFloat(Ext.getCmp('HazelnutNr_idcoop').getValue())) + (isNaN(parseFloat(Ext.getCmp('KapokNr_idcoop').getValue())) ? 0 : parseFloat(Ext.getCmp('KapokNr_idcoop').getValue())) + (isNaN(parseFloat(Ext.getCmp('MahagonyNr_idcoop').getValue())) ? 0 : parseFloat(Ext.getCmp('MahagonyNr_idcoop').getValue())) + (isNaN(parseFloat(Ext.getCmp('TeakNr_idcoop').getValue())) ? 0 : parseFloat(Ext.getCmp('TeakNr_idcoop').getValue())) + (isNaN(parseFloat(Ext.getCmp('VitexNr_idcoop').getValue())) ? 0 : parseFloat(Ext.getCmp('VitexNr_idcoop').getValue())) + (isNaN(parseFloat(Ext.getCmp('ErmerillaNr_idcoop').getValue())) ? 0 : parseFloat(Ext.getCmp('ErmerillaNr_idcoop').getValue())) + (isNaN(parseFloat(Ext.getCmp('AnthocephalusNr_idcoop').getValue())) ? 0 : parseFloat(Ext.getCmp('AnthocephalusNr_idcoop').getValue())) + (isNaN(parseFloat(Ext.getCmp('AlbiziaNr_idcoop').getValue())) ? 0 : parseFloat(Ext.getCmp('AlbiziaNr_idcoop').getValue())) + (isNaN(parseFloat(Ext.getCmp('JackfruitNr_idcoop').getValue())) ? 0 : parseFloat(Ext.getCmp('JackfruitNr_idcoop').getValue())) + (isNaN(parseFloat(Ext.getCmp('BananaNr_idcoop').getValue())) ? 0 : parseFloat(Ext.getCmp('BananaNr_idcoop').getValue())) + (isNaN(parseFloat(Ext.getCmp('RambutanNr_idcoop').getValue())) ? 0 : parseFloat(Ext.getCmp('RambutanNr_idcoop').getValue())) + (isNaN(parseFloat(Ext.getCmp('MangoNr_idcoop').getValue())) ? 0 : parseFloat(Ext.getCmp('MangoNr_idcoop').getValue())) + (isNaN(parseFloat(Ext.getCmp('SpondiasDulcisNr_idcoop').getValue())) ? 0 : parseFloat(Ext.getCmp('SpondiasDulcisNr_idcoop').getValue())) + (isNaN(parseFloat(Ext.getCmp('LangsatNr_idcoop').getValue())) ? 0 : parseFloat(Ext.getCmp('LangsatNr_idcoop').getValue())) + (isNaN(parseFloat(Ext.getCmp('DurianNr_idcoop').getValue())) ? 0 : parseFloat(Ext.getCmp('DurianNr_idcoop').getValue())) + (isNaN(parseFloat(Ext.getCmp('GuavaNr_idcoop').getValue())) ? 0 : parseFloat(Ext.getCmp('GuavaNr_idcoop').getValue())) + (isNaN(parseFloat(Ext.getCmp('AvocadoNr_idcoop').getValue())) ? 0 : parseFloat(Ext.getCmp('AvocadoNr_idcoop').getValue())) + (isNaN(parseFloat(Ext.getCmp('CempedakNr_idcoop').getValue())) ? 0 : parseFloat(Ext.getCmp('CempedakNr_idcoop').getValue())) + (isNaN(parseFloat(Ext.getCmp('BreadfruitNr_idcoop').getValue())) ? 0 : parseFloat(Ext.getCmp('BreadfruitNr_idcoop').getValue())) + (isNaN(parseFloat(Ext.getCmp('PapayaNr_idcoop').getValue())) ? 0 : parseFloat(Ext.getCmp('PapayaNr_idcoop').getValue())) + (isNaN(parseFloat(Ext.getCmp('MangosteenNr_idcoop').getValue())) ? 0 : parseFloat(Ext.getCmp('MangosteenNr_idcoop').getValue())) + (isNaN(parseFloat(Ext.getCmp('CitrusNr_idcoop').getValue())) ? 0 : parseFloat(Ext.getCmp('CitrusNr_idcoop').getValue())) + (isNaN(parseFloat(Ext.getCmp('GliricidiaNr_idcoop').getValue())) ? 0 : parseFloat(Ext.getCmp('GliricidiaNr_idcoop').getValue())) + (isNaN(parseFloat(Ext.getCmp('LeucaenaNr_idcoop').getValue())) ? 0 : parseFloat(Ext.getCmp('LeucaenaNr_idcoop').getValue())) + (isNaN(parseFloat(Ext.getCmp('ParkiaNr_idcoop').getValue())) ? 0 : parseFloat(Ext.getCmp('ParkiaNr_idcoop').getValue())) + (isNaN(parseFloat(Ext.getCmp('ArchidendronNr_idcoop').getValue())) ? 0 : parseFloat(Ext.getCmp('ArchidendronNr_idcoop').getValue())));
        Ext.getCmp('TotalShadeTreesNr_idcoop').setValue(total);
    }
    function hideSave() {
        Ext.getCmp('saveButton').hide();
        Ext.getCmp('csaveButton').hide();
        Ext.getCmp('nsaveButton_idcoop').hide();
        Ext.getCmp('btnCreateIcs').hide();
        Ext.getCmp('cgsaveButton_idcoop').hide();
        if (Ext.getCmp('CoopID').getValue() === '' && m_act_add) {
            Ext.getCmp('saveButton').show();
            Ext.getCmp('csaveButton').show();
            Ext.getCmp('nsaveButton_idcoop').show();
            Ext.getCmp('btnCreateIcs').show();
            Ext.getCmp('cgsaveButton_idcoop').show();
        }
        if (Ext.getCmp('CoopID').getValue() !== '' && m_act_update) {
            Ext.getCmp('saveButton').show();
            Ext.getCmp('csaveButton').show();
            Ext.getCmp('nsaveButton_idcoop').show();
            Ext.getCmp('btnCreateIcs').show();
            Ext.getCmp('cgsaveButton_idcoop').show();
        }
    }
    function displayFormWindow() {
        if (!win.isVisible()) {
            DataForm.getForm().reset();
            win.center();
            win.show();
        } else {
            win.hide(this, function() {});
            win.toFront();
        }
        Ext.getCmp('Provinsi').setValue(m_param);
        // if (parseInt(Ext.getCmp('CoopID').getValue()) > 0) {
        //     Ext.getCmp('buttonTraining').setDisabled(false);
        // } else {
        //     Ext.getCmp('buttonTraining').setDisabled(true);
        // }
    }
    //staff
    Ext.define('staff.Model', {
        extend: 'Ext.data.Model',
        fields: ['StaffID', 'CoopID', 'Status', 'FarmerID', 'StaffName', 'Position', 'Phone', 'Email', 'StaffBirthday', 'StaffGender', 'StaffStatus', 'PaymentStatus'],
    });
    var store_staff = Ext.create('Ext.data.Store', {
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
    var RowEditing = Ext.create('Ext.grid.plugin.RowEditing', {
        id: 'RowEditing',
        clicksToMoveEditor: 0,
        autoCancel: false,
        errorSummary: false,
        clicksToEdit: 2
    });
    var cposition = Ext.create('Ext.data.Store', {
        fields: ['label'],
        data: [{
            "label": lang("Ketua Badan Pengawas")
        }, {
            "label": lang("Ketua")
        }, {
            "label": lang("Wakil Ketua")
        }, {
            "label": lang("Sekretaris")
        }, {
            "label": lang("Wakil Sekretaris")
        }, {
            "label": lang("Bendahara")
        }, {
            "label": lang("Wakil Bendahara")
        }]
    });
    var cstaffstatus = Ext.create('Ext.data.Store', {
        fields: ['label'],
        data: [{
            "label": lang("Full-Time")
        }, {
            "label": lang("Part-Time")
        }, ]
    });
    var cpaymentstatus = Ext.create('Ext.data.Store', {
        fields: ['label'],
        data: [{
            "label": lang("Paid")
        }, {
            "label": lang("Unpaid")
        }, ]
    });
    var ckelamin = Ext.create('Ext.data.Store', {
        fields: ['id', 'label'],
        data: [{
            "id": "1",
            "label": lang("Laki-laki")
        }, {
            "id": "2",
            "label": lang("Perempuan")
        }]
    });
    var ceducation = Ext.create('Ext.data.Store', {
        fields: ['id', 'label'],
        data: [{
            "id": "1",
            "label": lang("Belum pernah sekolah")
        }, {
            "id": "2",
            "label": lang("Tidak tamat SD")
        }, {
            "id": "3",
            "label": lang("Tamat SD, tidak melanjutkan")
        }, {
            "id": "4",
            "label": lang("Tamat SMP")
        }, {
            "id": "5",
            "label": lang("Tamat SMA/SMK")
        }, {
            "id": "6",
            "label": lang("Tamat perguruan tinggi")
        }]
    });
    var cfarmer = Ext.create('Ext.data.Store', {
        fields: ['label'],
        data: [{
            "id": "Farmer",
            "label": lang("Farmer")
        }, {
            "id": "Non Farmer",
            "label": lang("Non Farmer")
        }]
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
        fields: [{
            name: 'id',
            mapping: 'id'
        }, {
            name: 'name',
            mapping: 'name'
        }, {
            name: 'handphone',
            mapping: 'hp'
        }, {
            name: 'email',
            mapping: 'email'
        }, {
            name: 'birthdate',
            mapping: 'birthdate'
        }, {
            name: 'kelamin',
            mapping: 'kelamin'
        }]
    });
    var ds = Ext.create('Ext.data.Store', {
        pageSize: 10,
        model: 'Post'
    });
    //end staff
    // ICS
    // store ics member
    Ext.define('ics.Model', {
        extend: 'Ext.data.Model',
        fields: ['IcsMemberID', 'FarmerID', 'FarmerName', 'Gender', 'SubDistrict', 'District']
    });
    var store_ics_members = Ext.create('Ext.data.Store', {
        model: 'ics.Model',
        //extend: 'Ext.data.Model',
        //fields:['IcsMemberID','FarmerID','FarmerName','Gender','SubDistrict','District'],
        pageSize: 10,
        autoLoad: false,
        proxy: {
            type: 'ajax',
            url: m_ics_member,
            reader: {
                type: 'json',
                root: 'data',
                totalProperty: 'total'
            }
        },
        listeners: {
            'beforeload': function(store, options) {
                store.proxy.extraParams.icsID = Ext.getCmp('IcsID').getValue();
            }
        }
    });
    Ext.define("PostIcs", {
        extend: 'Ext.data.Model',
        proxy: {
            type: 'ajax',
            url: m_ics_member + '_find',
            reader: {
                type: 'json',
                root: 'data',
                totalProperty: 'total'
            }
        },
        fields: [{
            name: 'id',
            mapping: 'id'
        }, {
            name: 'name',
            mapping: 'name'
        }, {
            name: 'gender',
            mapping: 'gender'
        }, {
            name: 'subdistrict',
            mapping: 'subdistrict'
        }, {
            name: 'district',
            mapping: 'district'
        }]
    });
    var dsIcs = Ext.create('Ext.data.Store', {
        pageSize: 10,
        model: 'PostIcs'
    });



    function displayFormNurseyCoop() {
        if (!winNurseyCoop.isVisible()) {
            Ext.getCmp('DataFormNurseyCoop').getForm().reset();
            winNurseyCoop.center();
            winNurseyCoop.show();
        } else {
            winNurseyCoop.hide(this, function() {
            });
            winNurseyCoop.toFront();
        }
    }

    Ext.define('nurseryTransaction.Model', {
        extend: 'Ext.data.Model',
        fields: ['NurseryTransactionID', 'NurseryID', 'Buyer', 'Volume', 'Price','Total', 'DateTransaction'],
    });

    var store_nursey_trans = Ext.create('Ext.data.Store', {
        model: 'nurseryTransaction.Model',
        autoLoad: false,
        proxy: {
            type: 'ajax',
            url: m_crud + '_nursery_trans',
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });

    var store_nursery_list = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['NurseryID', 'NurseryNr', 'ObjID', 'Luas'],
        autoLoad: false,
        proxy: {
            type: 'ajax',
            url: m_crud + '_nursery_list',
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });

    var DataFormNurseyCoopList = Ext.create('Ext.form.Panel', {
        autoScroll: true,
        bodyPadding: 5,
        id: 'DataFormNurseyCoopList',
        fieldDefaults: {
            labelAlign: 'left',
            labelWidth: 120,
            anchor: '95%'
        },
        items: [{
            xtype: 'gridpanel',
            id: 'gridDataFormNurseyCoopList',
            style: 'border:1px solid #CCC;',
            store: store_nursery_list,
            width: '100%',
            loadMask: true,
            dockedItems: [{
                xtype: 'toolbar',
                items: [{
                    icon: varjs.config.base_url + 'images/icons/new/add.png', cls:'Sfr_BtnGridGreen', overCls:'Sfr_BtnGridGreen-Hover',
                    //cls: m_act_save,
                    //hidden: !m_act_add,
                    text: lang('Add'),
                    scope: this,
                    handler: function() {
                        displayFormNurseyCoop();
                        //fillNurseryForm();
                        //reset form
                        Ext.getCmp('DataFormNurseyCoop').getForm().reset();

                        Ext.getCmp('nid_obj_idcoop').setValue(Ext.getCmp('CoopID').getValue());
                        //Ext.getCmp('NamaResponsible_idcoop').setValue(Ext.getCmp('CoopName').getValue());
                        //Ext.getCmp('Responsible_idcoop').setValue(Ext.getCmp('CoopID').getValue());
                        Ext.getCmp('NurseryNr_idcoop').setReadOnly(false);
                        store_nursey_trans.clearData();
                        store_nursey_trans.removeAll();
                        store_nursey_monitoring.clearData();
                        store_nursey_monitoring.removeAll();

                        Ext.getCmp('iphoto_idcoop').setSrc(m_api_base_url + '/images/nursery/no-image.png');
                        Ext.getCmp('iphotoResponsible_idcoop').setSrc(m_api_base_url + '/images/Photo/no-user.jpg');
                    }
                }, {
                    icon: varjs.config.base_url + 'images/icons/new/update.png',
                    //cls: m_act_save,
                    //hidden: !m_act_update,
                    text: lang('Update'),
                    scope: this,
                    handler: function() {
                        var sm = Ext.getCmp('gridDataFormNurseyCoopList').getSelectionModel().getSelection()[0];
                        if (sm == undefined) {
                            Ext.MessageBox.alert('Warning', lang('Please select Nursery!'));
                        } else {
                            //reset form
                            Ext.getCmp('DataFormNurseyCoop').getForm().reset();
                            Ext.getCmp('iphoto_idcoop').setSrc(m_api_base_url + '/images/nursery/no-image.png');
                            Ext.getCmp('iphotoResponsible_idcoop').setSrc(m_api_base_url + '/images/Photo/no-user.jpg');

                            displayFormNurseyCoop();
                            fillNurseryForm(sm.get('NurseryID'));
                            Ext.getCmp('nid_obj_idcoop').setValue(Ext.getCmp('CoopID').getValue());
                            //Ext.getCmp('NamaResponsible_idcoop').setValue(Ext.getCmp('CoopName').getValue());
                            //Ext.getCmp('Responsible_idcoop').setValue(Ext.getCmp('CoopID').getValue());
                            Ext.getCmp('NurseryNr_idcoop').setReadOnly(true);
                        }
                    }
                }, {
                    itemId: 'remove',
                    icon: varjs.config.base_url + 'images/icons/new/delete.png',
                    cls: m_act_save,
                    hidden: !m_act_delete,
                    text: lang('Delete'),
                    scope: this,
                    handler: function() {
                        var sm = Ext.getCmp('gridDataFormNurseyCoopList').getSelectionModel().getSelection()[0];
                        if (sm == undefined) {
                            Ext.MessageBox.alert('Warning', lang('Please select Nursery!'));
                        } else {
                            Ext.MessageBox.confirm('Message', lang('Apakah anda mau menghapus data ini ?'), function(btn) {
                                if (btn == 'yes') {
                                    Ext.Ajax.request({
                                        waitMsg: lang('Please Wait'),
                                        url: m_crud + '_nursery',
                                        method: 'DELETE',
                                        params: {
                                            ObjType: 'koperasi',
                                            ObjID: Ext.getCmp('CoopID').getValue(),
                                            NurseryID: sm.get('NurseryID')
                                        },
                                        success: function(response, opts) {
                                            var obj = Ext.decode(response.responseText);
                                            switch (obj.success) {
                                                case true:
                                                    Ext.MessageBox.alert('Success', obj.message);
                                                    store_nursery_list.load({
                                                        params: {
                                                            ObjType: 'koperasi',
                                                            ObjID: Ext.getCmp('CoopID').getValue()
                                                        }
                                                    });
                                                    break;
                                                default:
                                                    Ext.MessageBox.alert('Warning', obj.message);
                                                    break;
                                            }
                                        },
                                        failure: function(response, opts) {
                                            Ext.MessageBox.alert('error', lang('Could not connect to the database. Retry later'));
                                        }
                                    });
                                }
                            });
                        }
                    }
                }]
            }],
            columns: [{
                text: lang('NurseryID'),
                dataIndex: 'NurseryID',
                align: 'center',
                hidden: true
            }, {
                text: lang('No'),
                xtype: 'rownumberer',
                width: '10%',
                align: 'center'
            }, {
                text: lang('NurseryNr'),
                dataIndex: 'NurseryNr',
                width: '45%',
            }, {
                text: lang('Area (m2)'),
                dataIndex: 'Luas',
                width: '45%',
            }],
            listeners: {
                itemdblclick: function() {
                    //reset form
                    Ext.getCmp('DataFormNurseyCoop').getForm().reset();
                    Ext.getCmp('iphoto_idcoop').setSrc(m_api_base_url + '/images/nursery/no-image.png');
                    Ext.getCmp('iphotoResponsible_idcoop').setSrc(m_api_base_url + '/images/Photo/no-user.jpg');

                    var sm = Ext.getCmp('gridDataFormNurseyCoopList').getSelectionModel().getSelection()[0];
                    displayFormNurseyCoop();
                    fillNurseryForm(sm.get('NurseryID'));
                    Ext.getCmp('nid_obj_idcoop').setValue(Ext.getCmp('CoopID').getValue());
                    //Ext.getCmp('NamaResponsible_idcoop').setValue(Ext.getCmp('CoopName').getValue());
                    //Ext.getCmp('Responsible_idcoop').setValue(Ext.getCmp('CoopID').getValue());
                    Ext.getCmp('NurseryNr_idcoop').setReadOnly(false);
                }
            }
        }],
        buttons: [{
            text: lang('Close'),
            margin: '5px',
            scale: 'large',
            ui: 's-button',
            cls: 's-grey',
            disabled: false,
            handler: function() {
                winNurseyCoopList.hide();
            }
        }]
    });

    var winNurseyCoopList = Ext.create('widget.window', {
        title: lang('Nursery'),
        id: 'winNurseyCoopList',
        closable: true,
        modal: true,
        closeAction: 'show',
        width: '50%',
        height: 400,
        layout: {
            type: 'fit'
        },
        items: [DataFormNurseyCoopList]
    });

    function displayFormNurseyCoopList() {
        if (!winNurseyCoopList.isVisible()) {
            DataFormNurseyCoopList.getForm().reset();
            winNurseyCoopList.center();
            winNurseyCoopList.show();
        } else {
            winNurseyCoopList.hide(this, function() {});
            winNurseyCoopList.toFront();
        }
    }

    function displayFormNurseyCoopList() {
        if (!winNurseyCoopList.isVisible()) {
            DataFormNurseyCoopList.getForm().reset();
            winNurseyCoopList.center();
            winNurseyCoopList.show();
        } else {
            winNurseyCoopList.hide(this, function() {});
            winNurseyCoopList.toFront();
        }
    }

    // general panel container
    var DataForm = Ext.create('Ext.form.Panel', {
        frame: false,
        height: 500,
        autoScroll: true,
        width: 1010,
        bodyPadding: 5,
        fileUpload: true,
        enctype: 'multipart/form-data',
        id: 'asdataForm',
        fieldDefaults: {
            labelAlign: 'left',
            labelWidth: 175,
            anchor: '100%'
        },
        dockedItems: [{
            xtype: 'toolbar',
            flex: 1,
            dock: 'top',
            items: [{
                xtype: 'button',
                height: 70,
                width: 85,
                text: '<img src="' + varjs.config.base_url + 'img/general/compost-24px.png" /> <br /> ' + lang('Compost'),
                tooltip: lang('Compost'),
                hidden: !m_act_compost,
                handler: function() {
                    displayFormCompostPenjualan();
                    Ext.Ajax.request({
                        url: m_crud,
                        method: 'GET',
                        params: {
                            id: Ext.getCmp('CoopID').getValue()
                        },
                        success: function(fp, o) {
                            var r = Ext.decode(fp.responseText);
                            Ext.getCmp('id_obj').setValue(Ext.getCmp('CoopID').getValue());
                            Ext.getCmp('CompostID').setValue(r.CompostID);
                            Ext.getCmp('cCoopName').setValue(r.CoopName);
                            Ext.getCmp('Established').setValue(r.Established);
                            Ext.getCmp('CompostLatitude').setValue(r.CompostLatitude);
                            Ext.getCmp('CompostLongitude').setValue(r.CompostLongitude);
                            if (r.MesinChooper == '1') Ext.getCmp('MesinChooper').setValue(true);
                            if (r.MesinChooper == '2') Ext.getCmp('MesinChooper2').setValue(true);
                            if (r.RumahKompos == '1') Ext.getCmp('RumahKompos').setValue(true);
                            if (r.RumahKompos == '2') Ext.getCmp('RumahKompos2').setValue(true);
                            if (r.CompostID) {
                                Ext.getCmp('gcompostpenjualan').setDisabled(false);
                            } else Ext.getCmp('gcompostpenjualan').setDisabled(true);
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
                height: 70,
                width: 85,
                text: '<img src="' + varjs.config.base_url + 'img/general/nursery-24px.png" /> <br /> ' + lang('Nursery'),
                tooltip: lang('Nursery'),
                hidden: !m_act_nursery,
                handler: function() {
                    store_nursery_list.load({
                        params: {
                            ObjType: 'koperasi',
                            ObjID: Ext.getCmp('CoopID').getValue()
                        }
                    });
                    displayFormNurseyCoopList();
                }
            }, {
                xtype: 'button',
                height: 70,
                width: 85,
                text: '<img src="' + varjs.config.base_url + 'img/general/summary-24px.png" /> <br /> ' + lang('ICS'),
                tooltip: lang('Internal Monitoring System'),
                hidden: !m_act_ics_member,
                handler: function() {
                    displayFormIcs();
                    Ext.Ajax.request({
                        url: m_ics_group,
                        method: 'GET',
                        params: {
                            id: Ext.getCmp('CoopID').getValue()
                        },
                        success: function(fp, o) {
                            var r = Ext.decode(fp.responseText);
                            Ext.getCmp('IcsObjID').setValue(Ext.getCmp('CoopID').getValue());
                            if (r.IcsID == null) {
                                Ext.getCmp('btn_add_ics').setDisabled(true);
                                Ext.getCmp('btn_delete_ics').setDisabled(true);
                                if (m_act_add) Ext.getCmp('btnCreateIcs').show();
                                Ext.getCmp('iCoopName').setValue('');
                                /*
                                Ext.getCmp('iCoopName').setValue('');
                                Ext.getCmp('icsType').setValue('');
                                Ext.getCmp('IcsID').setValue('');
                                Ext.getCmp('btnCreateIcs').show();
                                store_ics_members.load({
                                    params: {
                                        icsID: ''
                                    }
                                });
                                */
                            } else {
                                Ext.getCmp('btn_add_ics').setDisabled(false);
                                Ext.getCmp('btn_delete_ics').setDisabled(false);
                                Ext.getCmp('iCoopName').setValue(r.CoopName);
                                Ext.getCmp('icsType').setValue('Organisasi Petani');
                                Ext.getCmp('IcsID').setValue(r.IcsID);
                                Ext.getCmp('btnCreateIcs').hide();
                                dsIcs.getProxy().setExtraParam("district", r.District);
                                dsIcs.getProxy().setExtraParam("province", Ext.getCmp('Provinsi').getValue());
                                store_ics_members.load({
                                    params: {
                                        icsID: r.IcsID
                                    }
                                });
                            }
                            // hideSave();
                        }
                    });

                }
            }, {
                xtype: 'button',
                height: 70,
                width: 100,
                text: '<img src="' + varjs.config.base_url + 'img/general/kebun-24px.png" /> <br /> ' + lang('Clonal Garden'),
                tooltip: lang('Clonal Garden'),
                hidden: !m_act_clonal_garden,
                handler: function() {
                    store_clonal_polygon_coop.load({
                        params: {
                            ObjType: 'koperasi',
                            ObjID: Ext.getCmp('CoopID').getValue()
                        }
                    });
                    displayFormClonalGardenPolygonCoop();
                }
            }, {
                xtype: 'button',
                id: 'buttonTraining',
                height: 70,
                width: 100,
                text: '<img src="' + varjs.config.base_url + 'img/general/training-24px.png" /> <br /> ' + lang('Training'),
                tooltip: lang('Trainig'),
                hidden: !m_act_training,
                // disabled: typeof(Ext.getCmp('CoopID'))!='undefined'?false:true,
                handler: function() {
                    displayFormTrainingList();
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
                    xtype: 'textfield',
                    id: 'CoopID',
                    name: 'CoopID',
                    hidden: true
                }, {
                    layout: 'column',
                    items: [{
                        columnWidth: 0.5,
                        items: [{
                            xtype: 'fieldset',
                            title: lang('Data Perusahaan'),
                            items: [{
                                xtype: 'textfield',
                                id: 'CoopName',
                                name: 'CoopName',
                                labelWidth: 180,
                                fieldLabel: lang('Nama')
                            }, {
                                xtype: 'textfield',
                                id: 'CoopCode',
                                name: 'CoopCode',
                                labelWidth: 180,
                                fieldLabel: lang('Code')
                            }, {
                                xtype: 'textfield',
                                id: 'Phone',
                                name: 'Phone',
                                labelWidth: 180,
                                fieldLabel: lang('No Telepon')
                            }, , {
                                xtype: 'textfield',
                                id: 'Email',
                                name: 'Email',
                                labelWidth: 180,
                                fieldLabel: lang('Email')
                            }, {
                                xtype: 'radiogroup',
                                labelWidth: 180,
                                fieldLabel: lang('Status Hukum Perusahaan'),
                                columns: 1,
                                items: [{
                                    xtype: 'radiofield',
                                    boxLabel: lang('Koperasi'),
                                    id: 'Status',
                                    name: 'Status',
                                    inputValue: 'Koperasi'
                                }, {
                                    xtype: 'radiofield',
                                    boxLabel: lang('Gapoktan'),
                                    id: 'Status2',
                                    name: 'Status',
                                    inputValue: 'Gapoktan'
                                }, {
                                    xtype: 'radiofield',
                                    boxLabel: lang('KUR'),
                                    id: 'Status3',
                                    name: 'Status',
                                    inputValue: 'KUR'
                                }, {
                                    xtype: 'radiofield',
                                    boxLabel: lang('Tidak Berbadan Hukum'),
                                    id: 'Status4',
                                    name: 'Status',
                                    inputValue: 'Tidak Berbadan Hukum'
                                }]
                            }, {
                                xtype: 'textfield',
                                id: 'TahunTerbentuk',
                                labelWidth: 180,
                                name: 'TahunTerbentuk',
                                fieldLabel: lang('Tahun Berdiri')
                            }]
                        }]
                    }, {
                        columnWidth: 0.5,
                        margin: 5,
                        items: [{
                            layout: 'column',
                            //hidden:true,
                            border: true,
                            items: [{
                                    columnWidth: 0.5,
                                    padding: 10,
                                    items: [{
                                        xtype: 'textfield',
                                        id: 'Photo_old',
                                        name: 'Photo_old',
                                        inputType: 'hidden'
                                    }, {
                                        xtype: 'textfield',
                                        id: 'Photo_name',
                                        name: 'Photo_name',
                                        inputType: 'hidden'
                                    }, {
                                        xtype: 'fileuploadfield',
                                        fieldLabel: lang('Icon'),
                                        labelWidth: 50,
                                        id: 'Photo',
                                        name: 'Photo',
                                        buttonText: 'Browse',
                                        listeners: {
                                            'change': function(fb, v) {
                                                var form = Ext.getCmp('asdataForm').getForm();
                                                form.submit({
                                                    url: m_crud + '_image',
                                                    waitMsg: lang('Sending Photo...'),
                                                    success: function(fp, o) {
                                                        Ext.getCmp('iphoto').setSrc(m_photo + o.result.file);
                                                        if (Ext.getCmp('Photo_old').setValue(Ext.getCmp('Photo_name').getValue())) {
                                                            Ext.getCmp('Photo_name').setValue(o.result.file);
                                                        }
                                                    }
                                                });
                                            }
                                        }
                                    }]
                                }
                                /* ,{
                                                        columnWidth: 0.5,
                                                        padding: 10,
                                                        items: [{
                                                           xtype: 'textfield',
                                                           id: 'Photo_cert_old',
                                                           name: 'Photo_cert_old',
                                                           inputType: 'hidden'
                                                        },{
                                                           xtype: 'textfield',
                                                           id: 'Photo_cert_name',
                                                           name: 'Photo_cert_name',
                                                           inputType: 'hidden'
                                                        }, {
                                                           xtype: 'fileuploadfield',
                                                           fieldLabel: lang('Sertifikasi'),
                                                           labelWidth: 100,
                                                           id: 'Photo_cert',
                                                           name: 'Photo_cert',
                                                           buttonText: 'Browse',
                                                           listeners: {
                                                              'change': function(fb, v) {
                                                                 var form = Ext.getCmp('asdataForm').getForm();
                                                                 form.submit({
                                                                    url: m_crud + '_image',
                                                                    waitMsg: lang('Sending Photo...'),
                                                                    success: function(fp, o) {
                                                                       Ext.getCmp('iphoto_cert').setSrc(m_photo + o.result.file);
                                                                       if(Ext.getCmp('Photo_cert_old').setValue(Ext.getCmp('Photo_cert_name').getValue())){
                                                                          Ext.getCmp('Photo_cert_name').setValue(o.result.file);
                                                                       }
                                                                    }
                                                                 });
                                                              }
                                                           }
                                                        }]
                                                     }*/
                                , {
                                    columnWidth: 0.5,
                                    items: [{
                                        xtype: 'image',
                                        id: 'iphoto',
                                        height: '120px'
                                    }]
                                }
                                /*, {
                                                        columnWidth: 0.5,
                                                        items: [{
                                                           xtype: 'image',
                                                           id: 'iphoto_cert',
                                                           height: '120px'
                                                        }]
                                                     }*/
                            ]
                        }, {
                            xtype: 'fieldset',
                            title: lang('Lokasi'),
                            items: [{
                                id: 'Provinsi',
                                name: 'Provinsi',
                                xtype: 'combo',
                                fieldLabel: lang('Provinsi'),
                                store: mc_Provinsi,
                                displayField: 'label',
                                valueField: 'id',
                                queryMode: 'local',
                                readOnly: true,
                                listeners: {
                                    change: function(cb, nv, ov) {
                                        mc_Kabupaten.load({
                                            params: {
                                                key: Ext.getCmp('Provinsi').getValue()
                                            }
                                        });
                                        Ext.getCmp('Kabupaten').enable();
                                    }
                                }
                            }, {
                                id: 'Kabupaten',
                                name: 'Kabupaten',
                                xtype: 'combo',
                                fieldLabel: lang('Kabupaten'),
                                disabled: 'true',
                                store: mc_Kabupaten,
                                displayField: 'label',
                                valueField: 'label',
                                queryMode: 'local',
                                listeners: {
                                    change: function(cb, nv, ov) {
                                        mc_Kecamatan.load({
                                            params: {
                                                key: Ext.getCmp('Kabupaten').getValue()
                                            }
                                        });
                                        Ext.getCmp('Kecamatan').enable();
                                        ds.getProxy().setExtraParam("district", Ext.getCmp('Kabupaten').getValue())
                                    }
                                }
                            }, {
                                id: 'Kecamatan',
                                name: 'Kecamatan',
                                xtype: 'combo',
                                fieldLabel: lang('Kecamatan'),
                                store: mc_Kecamatan,
                                displayField: 'label',
                                valueField: 'label',
                                queryMode: 'local',
                                disabled: 'true',
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
                                disabled: 'true',
                                valueField: 'id',
                                queryMode: 'local'
                            }, {
                                xtype: 'textfield',
                                fieldLabel: lang('Alamat'),
                                id: 'Address',
                                name: 'Address'
                            }, {
                                xtype: 'textfield',
                                id: 'Latitude',
                                name: 'Latitude',
                                fieldLabel: lang('Latitude'),
                                readOnly: m_hakakses_lat_short
                            }, {
                                xtype: 'textfield',
                                id: 'Longitude',
                                name: 'Longitude',
                                fieldLabel: lang('Longitude'),
                                readOnly: m_hakakses_long_short
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
                    store: store_staff,
                    width: '100%',
                    loadMask: true,
                    selType: 'rowmodel',
                    dockedItems: [{
                        xtype: 'toolbar',
                        items: [{
                            icon: varjs.config.base_url + 'images/icons/new/add.png', cls:'Sfr_BtnGridGreen', overCls:'Sfr_BtnGridGreen-Hover',
                            text: lang('Add'),
                            cls: m_act_save,
                            hidden: !m_act_add,
                            scope: this,
                            handler: function() {
                                RowEditing.cancelEdit();
                                var r = Ext.create('staff.Model', {
                                    StaffID: '',
                                    CoopID: '',
                                    Status: '',
                                    FarmerID: '',
                                    StaffName: '',
                                    Position: '',
                                    Phone: '',
                                    Email: '',
                                    StaffBirthday: '',
                                    StaffGender: ''
                                });
                                store_staff.insert(0, r);
                                RowEditing.startEdit(0, 0);
                            }
                        }, {
                            icon: varjs.config.base_url + 'images/icons/new/update.png',
                            cls: m_act_save,
                            hidden: !m_act_update,
                            text: lang('Edit'),
                            hidden:true,
                            scope: this,
                            handler: function() {
                                RowEditing.cancelEdit();
                                var sm = Ext.getCmp('grid_staff').getSelectionModel().getSelection();
                                RowEditing.startEdit(sm[0].index, 0);
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
                                RowEditing.cancelEdit();
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
                                                        store_staff.load({
                                                            params: {
                                                                id: Ext.getCmp('CoopID').getValue()
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
                            valueField: 'id',
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
                        width: '20%',
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
                                emptyText: lang('No matching farmer found.'),
                                getInnerTpl: function() {
                                    return '<div class="search-item">' + '{id} - {name}' + '{excerpt}' + '</div>';
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
                                            //Ext.getCmp('lemail').setValue(post.get('email'))
                                            //Ext.getCmp('lemail').setReadOnly(true)
                                        Ext.getCmp('StaffGender').setValue(post.get('kelamin'))
                                        Ext.getCmp('StaffGender').setReadOnly(true)
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
                        width: '20%',
                        hidden: true,
                        editor: {
                            xtype: 'textfield',
                            id: 'namanon',
                            name: 'namanon',
                        }
                    }, {
                        text: lang('Staff Status'),
                        dataIndex: 'StaffStatus',
                        width: '10%',
                        editor: {
                            xtype: 'combo',
                            store: cstaffstatus,
                            id: 'StaffStatus',
                            queryMode: 'local',
                            displayField: 'label',
                            valueField: 'label'
                        }
                    }, {
                        text: lang('Payment Status'),
                        dataIndex: 'PaymentStatus',
                        width: '10%',
                        editor: {
                            xtype: 'combo',
                            store: cpaymentstatus,
                            id: 'PaymentStatus',
                            queryMode: 'local',
                            displayField: 'label',
                            valueField: 'label'
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
                    //plugins: [RowEditing],
                    listeners: {
                        itemdblclick: function(dv, record, item, index, e) {
                            if (!m_act_update) {
                                RowEditing.cancelEdit();
                            } else {
                                gs_edit()
                            }
                        },
                        'canceledit': function(editor, e, eOpts) {
                            store_staff.load({
                                params: {
                                    id: Ext.getCmp('CoopID').getValue()
                                }
                            });
                        },
                        'edit': function(editor, e) {
                            if (e.record.data.Status == 'Farmer') {
                                name = e.record.data.StaffName;
                                farmer_id = e.record.data.StaffName.split("]")[0].split('[')[1];
                            } else {
                                name = Ext.getCmp('namanon').getValue();
                                farmer_id = null;
                            };
                            if (e.record.data.StaffID == '') {
                                Ext.Ajax.request({
                                    waitMsg: lang('Please wait...'),
                                    url: m_staff,
                                    method: 'POST',
                                    params: {
                                        CoopID: Ext.getCmp('CoopID').getValue(),
                                        Status: e.record.data.Status,
                                        FarmerID: farmer_id,
                                        Position: e.record.data.Position,
                                        StaffName: name,
                                        Phone: e.record.data.Phone,
                                        Email: e.record.data.Email,
                                        StaffBirthday: e.record.data.StaffBirthday,
                                        StaffGender: Ext.getCmp('StaffGender').getValue(),
                                        StaffStatus: e.record.data.StaffStatus,
                                        PaymentStatus: e.record.data.PaymentStatus,
                                    },
                                    success: function(response, opts) {
                                        var obj = Ext.decode(response.responseText);
                                        switch (obj.success) {
                                            case true:
                                                Ext.MessageBox.alert('Success', obj.message);
                                                store_staff.load({
                                                    params: {
                                                        id: Ext.getCmp('CoopID').getValue()
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
                                                CoopID: Ext.getCmp('CoopID').getValue(),
                                                Status: e.record.data.Status,
                                                // FarmerID:       Ext.getCmp('namanon').getValue(),
                                                FarmerID: farmer_id,
                                                Position: e.record.data.Position,
                                                StaffName: name,
                                                Phone: e.record.data.Phone,
                                                Email: e.record.data.Email,
                                                StaffBirthday: e.record.data.StaffBirthday,
                                                StaffGender: Ext.getCmp('StaffGender').getValue(),
                                                StaffStatus: e.record.data.StaffStatus,
                                                PaymentStatus: e.record.data.PaymentStatus,
                                            },
                                            success: function(response, opts) {
                                                var obj = Ext.decode(response.responseText);
                                                switch (obj.success) {
                                                    case true:
                                                        Ext.MessageBox.alert('Success', obj.message);
                                                        store_staff.load({
                                                            params: {
                                                                id: Ext.getCmp('CoopID').getValue()
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
            }]
        }],
        buttons: [{
            id: 'saveButton',
            text: lang('Save'),
            margin: '5px',
            scale: 'large',
            ui: 's-button',
            cls: 's-blue',
            handler: function() {
                var form = this.up('form').getForm();
                var urle;
                if (Ext.getCmp('CoopID').getValue() != '') urle = m_crud + 'u';
                else urle = m_crud;
                form.submit({
                    url: urle,
                    waitMsg: lang('Sending data...'),
                    success: function(fp, o) {
                        Ext.MessageBox.alert('Success', lang('Data saved.'));
                    }
                });
                win.hide(this, function() {
                    store.load();
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
        title: lang('Organisasi Petani'),
        id: 'win',
        closable: true,
        modal: true,
        closeAction: 'show',
        autoScroll: true,
        width: '90%',
        height: '90%',
        layout: {
            type: 'fit'
        },
        items: [DataForm]
    });

    function fset(r) {
        Ext.getCmp('panel_staff').enable()
        store_staff.load({
            params: {
                id: Ext.getCmp('CoopID').getValue()
            }
        });
        Ext.getCmp('CoopID').setValue(r.CoopID);
        Ext.getCmp('CoopCode').setValue(r.CoopCode);
        Ext.getCmp('CoopName').setValue(r.CoopName);
        Ext.getCmp('Address').setValue(r.Address);
        Ext.getCmp('Phone').setValue(r.Phone);
        Ext.getCmp('Email').setValue(r.Email);
        if (r.VillageID != '') {
            Ext.getCmp('Provinsi').setValue(r.ProvinceID);
            Ext.getCmp('Kabupaten').setValue(r.District);
            Ext.getCmp('Kecamatan').setValue(r.SubDistrict);
            Ext.getCmp('Desa').setValue(r.VillageID);
        }
        if (r.Status == 'Koperasi') Ext.getCmp('Status').setValue(true);
        if (r.Status == 'Gapoktan') Ext.getCmp('Status2').setValue(true);
        if (r.Status == 'KUR') Ext.getCmp('Status3').setValue(true);
        if (r.Status == 'Tidak Berbadan Hukum') Ext.getCmp('Status4').setValue(true);
        Ext.getCmp('TahunTerbentuk').setValue(r.TahunTerbentuk);
        Ext.getCmp('Latitude').setValue(r.Latitude);
        Ext.getCmp('Longitude').setValue(r.Longitude);
        Ext.getCmp('Photo_old').setValue(r.Photo);
        Ext.getCmp('iphoto').setSrc(m_photo + r.Photo);

        Ext.getCmp('buttonTraining').setDisabled(false);
        mc_coop_member.load({
            params: {
                CoopID: r.CoopID
            }
        });
        //Ext.getCmp('Photo_cert_old').setValue(r.PhotoCertification);
        //Ext.getCmp('iphoto_cert').setSrc(m_photo +  r.PhotoCertification);
    }
    //============================================== Advanced Filter (Begin) =====================================//
    var cmbAdvFilter = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'label'],
        data: [{
            "id": "name",
            "label": lang('Nama')
        }, {
            "id": "year",
            "label": lang('Tahun')
        }, {
            "id": "status",
            "label": lang('Status')
        // }, {
        //     "id": "district",
        //     "label": lang('District')
        }, {
            "id": "date_modified",
            "label": lang('Date Modified')
        }]
    });
    var cmbAdvFilterTahun = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'label'],
        autoLoad: true,
        proxy: {
            type: 'ajax',
            url: m_prep_adv_filter_coop,
            extraParams: {
                opsi: 'cmb_year_establish'
            },
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });
    var cmbAdvFilterDistrict = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'label'],
        autoLoad: true,
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
    var cmbAdvFilterStatus = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'label'],
        data: [{
            "id": "Koperasi",
            "label": "Koperasi"
        }, {
            "id": "Gapoktan",
            "label": "Gapoktan"
        }, {
            "id": "KUR",
            "label": "KUR"
        }, {
            "id": "Tidak Berbadan Hukum",
            "label": "Tidak Berbadan Hukum"
        }]
    });
    var cmbOperatorSearch = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'label'],
        data: [{
            "id": "=",
            "label": "="
        }, {
            "id": "!=",
            "label": "!="
        }, {
            "id": ">=",
            "label": ">="
        }, {
            "id": "<=",
            "label": "<="
        }]
    });
    var panelAdvFilter = Ext.create('Ext.panel.Panel', {
        id: 'idPanelAdvFilter',
        //bodyPadding: 5,  // Don't want content to crunch against the borders
        width: '100%',
        title: 'Advanced Filter',
        cls: 'panelAdvFilter',
        style: 'border:1px solid #CCC;margin-bottom:10px;',
        layout: {
            type: 'vbox',
            align: 'left'
        },
        items: [{
            xtype: 'container',
            id: 'rowFilter',
            cls: 'x-table-layout-cell-top-align',
            layout: {
                type: 'table',
                columns: 3
            },
            width: '100%',
            margin: '10px 0 0 12px',
            items: [{
                xtype: 'label',
                text: 'Add Filter',
                margin: '5px 100px 0 5px',
                style: 'line-height:15px;'
            }, {
                xtype: 'boxselect',
                width: 350,
                margin: '0 0 0 0',
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
            }, {
                xtype: 'button',
                width: 90,
                margin: '0px 0px 0px 18px',
                text: 'Reload Filter',
                handler: function() {
                    //event click
                    //hide semuanya dulu
                    hideAllElementAdvFilter();
                    var filterDipilih = Ext.getCmp('cmbAdvFilter').getValue(); //array
                    for (var i = 0; i < filterDipilih.length; i++) {
                        switch (filterDipilih[i]) {
                            case 'name':
                                Ext.getCmp('rowNama').setVisible(true);
                                break;
                            case 'year':
                                Ext.getCmp('rowTahun').setVisible(true);
                                break;
                            case 'status':
                                Ext.getCmp('rowStatus').setVisible(true);
                                break;
                            case 'district':
                                Ext.getCmp('rowDistrict').setVisible(true);
                                break;
                            case 'date_modified':
                                Ext.getCmp('rowTglModified').setVisible(true);
                                break;
                        }
                    }
                }
            }]
        }, {
            xtype: 'box',
            width: '100%',
            autoEl: {
                tag: 'hr'
            },
            style: 'border:1px solid #EFF0F1;margin:5px 0px;padding:0px;'
        }, {
            xtype: 'container',
            id: 'rowNama',
            layout: {
                type: 'table',
                columns: 2
            },
            width: '100%',
            margin: '10px 0 0 12px',
            height: 30,
            items: [{
                xtype: 'label',
                text: lang('Nama'),
                margin: '2px 124px 0 0'
            }, {
                name: 'advNama',
                id: 'advNama',
                xtype: 'textfield',
                width: 450
            }]
        }, {
            xtype: 'container',
            id: 'rowTahun',
            layout: {
                type: 'table',
                columns: 3
            },
            width: '100%',
            margin: '10px 0 0 12px',
            height: 30,
            items: [{
                xtype: 'label',
                text: lang('Tahun'),
                margin: '2px 132px 0 0'
            }, {
                xtype: 'combo',
                width: 60,
                listConfig: {
                    cls: 'x-boundlist-item comboAdvFilterItemList'
                },
                id: 'advOpTahun',
                name: 'advOpTahun',
                store: cmbOperatorSearch,
                displayField: 'label',
                valueField: 'id',
                queryMode: 'local',
                selectOnFocus: true,
                editable: false,
                margin: '-5px 10px 0px 0px'
            }, {
                name: 'advTahun',
                id: 'advTahun',
                xtype: 'textfield',
                width: 80
            }]
        }, {
            xtype: 'container',
            id: 'rowStatus',
            layout: {
                type: 'table',
                columns: 2
            },
            width: '100%',
            margin: '10px 0 0 12px',
            height: 30,
            items: [{
                xtype: 'label',
                text: 'Status',
                margin: '2px 121px 0 0'
            }, {
                xtype: 'combo',
                width: 450,
                listConfig: {
                    cls: 'x-boundlist-item comboAdvFilterItemList'
                },
                id: 'advStatus',
                name: 'advStatus[]',
                store: cmbAdvFilterStatus,
                displayField: 'label',
                valueField: 'id',
                queryMode: 'local',
                selectOnFocus: true,
                multiSelect: true,
                editable: false
            }]
        }, {
            xtype: 'container',
            id: 'rowDistrict',
            layout: {
                type: 'table',
                columns: 2
            },
            width: '100%',
            margin: '10px 0 0 12px',
            height: 30,
            items: [{
                xtype: 'label',
                text: lang('District'),
                margin: '2px 117px 0 0'
            }, {
                xtype: 'combo',
                width: 450,
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
            }]
        }, {
            xtype: 'container',
            id: 'rowTglModified',
            layout: {
                type: 'table',
                columns: 4
            },
            width: '100%',
            margin: '10px 0 0 12px',
            height: 30,
            items: [{
                xtype: 'label',
                text: lang('Date Modified'),
                margin: '2px 79px 0 0'
            }, {
                xtype: 'datefield',
                format: 'Y-m-d',
                id: 'advTglModiStart',
                name: 'advTglModiStart',
                width: 102,
                hideLabel: true
            }, {
                xtype: 'label',
                text: lang('to'),
                margin: '2px 40px 0px 50px'
            }, {
                xtype: 'datefield',
                format: 'Y-m-d',
                id: 'advTglModiEnd',
                name: 'advTglModiEnd',
                width: 102,
                hideLabel: true
            }]
        }, {
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
                width: '150',
                margin: '10px 0px 10px 165px',
                text: 'Search',
                style: 'text-align:center;',
                handler: function() {
                    //event click
                    store.load({
                        params: {
                            page: 1,
                            start: 0,
                            limit: 50
                        }
                    });
                }
            }, {
                xtype: 'button',
                width: '150',
                margin: '10px 0px 10px 18px',
                text: 'Simple Search',
                style: 'text-align:center;',
                handler: function() {
                    //event click
                    Ext.getCmp('key').setVisible(true);
                    // Ext.getCmp('sKabupaten').setVisible(true);
                    // Ext.getCmp('btnSimpleSearch').setVisible(true);
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
                    if (Ext.getCmp('rowNama').isVisible() == true) {
                        var parAdvNama = Ext.getCmp('advNama').getValue();
                    } else {
                        var parAdvNama = 'not_set';
                    }
                    if (Ext.getCmp('rowTahun').isVisible() == true) {
                        var parAdvOpTahun = Ext.getCmp('advOpTahun').getValue();
                        var parAdvTahun = Ext.getCmp('advTahun').getValue();
                    } else {
                        var parAdvOpTahun = 'not_set';
                        var parAdvTahun = 'not_set';
                    }
                    if (Ext.getCmp('rowStatus').isVisible() == true) {
                        var parAdvStatus = Ext.getCmp('advStatus').getValue().join().replace(/,/g, '::');
                    } else {
                        var parAdvStatus = 'not_set';
                    }
                    if (Ext.getCmp('rowDistrict').isVisible() == true) {
                        var parAdvDistrict = Ext.getCmp('advDistrict').getValue().join().replace(/,/g, '::');
                    } else {
                        var parAdvDistrict = 'not_set';
                    }
                    if (Ext.getCmp('rowTglModified').isVisible() == true) {
                        var parAdvTglModiStart = Ext.getCmp('advTglModiStart').getValue();
                        var parAdvTglModiEnd = Ext.getCmp('advTglModiEnd').getValue();
                    } else {
                        var parAdvTglModiStart = 'not_set';
                        var parAdvTglModiEnd = 'not_set';
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
                            parAdvNama : parAdvNama,
                            parAdvOpTahun : parAdvOpTahun,
                            parAdvTahun : parAdvTahun,
                            parAdvStatus : parAdvStatus,
                            parAdvDistrict : parAdvDistrict,
                            parAdvTglModiStart : parAdvTglModiStart,
                            parAdvTglModiEnd : parAdvTglModiEnd
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
        }],
        renderTo: 'ext-content'
    });
    //hilangkan semua elemen advanced filter
    Ext.getCmp('idPanelAdvFilter').setVisible(false);
    hideAllElementAdvFilter();

    function hideAllElementAdvFilter() {
        Ext.getCmp('rowNama').setVisible(false);
        Ext.getCmp('advNama').setValue();
        Ext.getCmp('rowTahun').setVisible(false);
        Ext.getCmp('advTahun').setValue();
        Ext.getCmp('rowStatus').setVisible(false);
        Ext.getCmp('advStatus').setValue();
        Ext.getCmp('rowDistrict').setVisible(false);
        Ext.getCmp('advDistrict').setValue();
        Ext.getCmp('rowTglModified').setVisible(false);
        Ext.getCmp('advTglModiStart').setValue();
        Ext.getCmp('advTglModiEnd').setValue();
    }
    //============================================== Advanced Filter (End) =======================================//
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
            itemdblclick: function(dv, record, item, index, e) {
                displayFormWindow();
                var sm = record;
                Ext.Ajax.request({
                    url: m_crud,
                    method: 'GET',
                    params: {
                        id: sm.get('CoopID')
                    },
                    success: function(fp, o) {
                        var r = Ext.decode(fp.responseText);
                        Ext.getCmp('CoopID').setValue(sm.get('CoopID'));
                        fset(r);
                        hideSave();
                    }
                });
            }
        },
        dockedItems: [{
            xtype: 'pagingtoolbar',
            store: store, // same store GridPanel is using
            dock: 'bottom',
            displayInfo: true
        }, {
            xtype: 'toolbar',
            items: [{
                icon: varjs.config.base_url + 'images/icons/new/add.png', cls:'Sfr_BtnGridGreen', overCls:'Sfr_BtnGridGreen-Hover',
                text: lang('Add'),
                hidden: !m_act_add,
                scope: this,
                handler: function() {
                    Ext.getCmp('panel_staff').disable()
                    displayFormWindow();
                    Ext.getCmp('iphoto').setSrc('');
                    //Ext.getCmp('iphoto_cert').setSrc('');
                    Ext.getCmp('Kabupaten').setValue('');
                    Ext.getCmp('Kecamatan').disable();
                    Ext.getCmp('Desa').disable();
                    Ext.getCmp('buttonTraining').setDisabled(true);
                },
                cls: m_act_add
            }, {
                icon: varjs.config.base_url + 'images/icons/new/update.png',
                text: lang('Update'),
                hidden: !m_act_update,
                scope: this,
                handler: function() {
                    displayFormWindow();
                    var sm = Ext.getCmp('grid').getSelectionModel().getSelection()[0];
                    Ext.Ajax.request({
                        url: m_crud,
                        method: 'GET',
                        params: {
                            id: sm.get('CoopID')
                        },
                        success: function(fp, o) {
                            var r = Ext.decode(fp.responseText);
                            Ext.getCmp('CoopID').setValue(sm.get('CoopID'));
                            fset(r);
                            hideSave();
                        }
                    });
                },
                cls: m_act_update
            }, {
                itemId: 'remove',
                icon: varjs.config.base_url + 'images/icons/new/delete.png',
                cls: m_act_delete,
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
                                params: {
                                    id: smb.raw.CoopID
                                },
                                success: function(response, opts) {
                                    var obj = Ext.decode(response.responseText);
                                    switch (obj.success) {
                                        case true:
                                            store.load();
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
                name: 'key', baseCls:'Sfr_TxtfieldSearchGrid',
                id: 'key',
                xtype: 'textfield',
                emptyText: lang('Cari berdasar nama/ID')
            }, {
                id: 'sProvinsi',
                name: 'sProvinsi',
                xtype: 'combo',
                store: mc_Provinsi,
                displayField: 'label',
                valueField: 'id',
                queryMode: 'local',
                hidden: true,
                value: m_param,
                listeners: {
                    change: function(cb, nv, ov) {
                        mc_Kabupaten.load({
                            params: {
                                key: Ext.getCmp('sProvinsi').getValue()
                            }
                        });
                        Ext.getCmp('sKabupaten').enable();
                    }
                }
            }, {
                id: 'sKabupaten',
                name: 'sKabupaten',
                xtype: 'combo',
                store: mc_Kabupaten,
                displayField: 'label',
                valueField: 'label',
                queryMode: 'local',
                hidden: true,
            }, {
                xtype: 'button',
                id: 'btnSimpleSearch',
                icon: varjs.config.base_url + 'images/icons/silk/search.png',
                margin: '0px 0px 0px 6px',
                text: lang('Search'),
                handler: function() {
                    store.load({
                        params: {
                            key: Ext.getCmp('key').getValue(),
                            // kab: Ext.getCmp('sKabupaten').getValue(),
                            // prov: Ext.getCmp('sProvinsi').getValue(),
                            page: 1,
                            start: 0,
                            limit: 50
                        }
                    });
                }
            }, {
                xtype: 'button',
                id: 'btnAdvSearch',
                icon: varjs.config.base_url + 'images/icons/silk/page_white_wrench.png',
                margin: '0px 0px 0px 6px',
                text: lang('Advanced Search'),
                handler: function() {
                    //aksi disini
                    Ext.getCmp('key').setVisible(false);
                    // Ext.getCmp('sProvinsi').setVisible(false);
                    // Ext.getCmp('sKabupaten').setVisible(false);
                    Ext.getCmp('btnSimpleSearch').setVisible(false);
                    Ext.getCmp('btnAdvSearch').setVisible(false);
                    Ext.getCmp('idPanelAdvFilter').setVisible(true);
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
            text: lang('Code'),
            width: '15%',
            dataIndex: 'CoopCode'
        }, {
            text: lang('Nama'),
            width: '15%',
            dataIndex: 'CoopName'
        }, {
            text: lang('Phone'),
            width: '10%',
            dataIndex: 'Phone'
        }, {
            text: lang('Email'),
            width: '15%',
            dataIndex: 'Email'
        }, {
            text: lang('Tahun Terbentuk'),
            width: '15%',
            dataIndex: 'TahunTerbentuk'
        }, {
            text: lang('Status'),
            width: '10%',
            dataIndex: 'Status'
        }, {
            text: lang('District'),
            width: '15%',
            dataIndex: 'District'
        }]
    });
    //==compost 2
    Ext.define('penjualan.Model', {
        extend: 'Ext.data.Model',
        fields: ['id', 'Buyer', 'Volume', 'Price', 'Total', 'DateTransaction', 'CloneTypeID', 'CloneTypeName'],
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
    var mc_pembeli = Ext.create('Ext.data.Store', {
        fields: ['label'],
        data: [{
            'label': 'Anggota Kelompok'
        }, {
            'label': 'Petani Lain'
        }, {
            'label': 'Traders'
        }, {
            'label': 'Lainnya'
        }, {
            'label': 'Pemerintah'
        }],
    });

    function displayFormCompostPenjualan() {
        if (!winCompostPenjualan.isVisible()) {
            DataFormCompostPenjualan.getForm().reset();
            winCompostPenjualan.center();
            winCompostPenjualan.show();
        } else {
            winCompostPenjualan.hide(this, function() {});
            winCompostPenjualan.toFront();
        }
    }

    function displayFormClonalGardenPolygonCoop() {
        if (!winClonalGardenPolygonCoop.isVisible()) {
            DataFormClonalGardenPolygonCoop.getForm().reset();
            winClonalGardenPolygonCoop.center();
            winClonalGardenPolygonCoop.show();
        } else {
            winClonalGardenPolygonCoop.hide(this, function() {});
            winClonalGardenPolygonCoop.toFront();
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
                    value: 'koperasi',
                    hidden: true
                }, {
                    xtype: 'textfield',
                    fieldLabel: lang('Nama'),
                    id: 'cCoopName',
                    name: 'cCoopName',
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
                    cls: m_act_save,
                    hidden: !m_act_add,
                    text: lang('Add'),
                    scope: this,
                    handler: function() {
                        cRowEditing.cancelEdit();
                        var r = Ext.create('penjualan.Model', {
                            id: '',
                            Buyer: '',
                            Volume: '',
                            Price: '',
                            Total: '',
                            DateTransaction: ''
                        });
                        store_compost_penjualan.insert(0, r);
                        cRowEditing.startEdit(0, 0);
                    }
                }, {
                    icon: varjs.config.base_url + 'images/icons/new/update.png',
                    cls: m_act_save,
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
                    cls: m_act_save,
                    hidden: !m_act_delete,
                    text: lang('Delete'),
                    scope: this,
                    handler: function() {
                        var smb = Ext.getCmp('gcompostpenjualan').getSelectionModel().getSelection()[0];
                        cRowEditing.cancelEdit();
                        Ext.MessageBox.confirm('Message', 'Apakah anda mau menghapus data ini ?', function(btn) {
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
                    valueField: 'label',
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
            cls: 's-blue ' + m_act_save,
            handler: function() {
                var form = this.up('form').getForm();
                var methode;
                if (Ext.getCmp('CompostID').getValue() != '') methode = 'PUT';
                else methode = 'POST';
                form.submit({
                    url: m_compost,
                    method: methode,
                    waitMsg: lang('Sending data...'),
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
    //****//
    var store_clonal_polygon_coop = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['ClonalID', 'GardenNr', 'Area', 'StatusCode'],
        autoLoad: false,
        proxy: {
            type: 'ajax',
            url: m_store_clonal_polygons,
            /*extraParams: {
                clonal_id: Ext.getCmp('ClonalID').getValue(),
            },*/
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });

    function hitung_area_coop() {
        Ext.Ajax.request({
            url: m_crud + '_clonal_garden_area',
            method: 'GET',
            params: {
                ObjType: Ext.getCmp('ObjType_idcoop').getValue(),
                ObjID: Ext.getCmp('ObjID_idcoop').getValue(),
                GardenNr: Ext.getCmp('GardenNr_idcoop').getValue(),
                ClonalID: Ext.getCmp('ClonalID_idcoop').getValue(),
            },
            success: function(fp, o) {
                var r = Ext.decode(fp.responseText);
                Ext.getCmp('Area_idcoop').setValue(r.Area);
                Ext.getCmp('ClonalGardenLatitude_idcoop').setValue(r.Latitude);
                Ext.getCmp('ClonalGardenLongitude_idcoop').setValue(r.Longitude);
                store_clonal_polygon_coop.load({
                    params: {
                        ObjType: 'koperasi',
                        ObjID: Ext.getCmp('CoopID').getValue()
                    }
                });
            }
        })
    }

    function edit_clonal_garden_coop(clonal_id, garden_nr) {
        displayFormClonalGardenCoop();
        Ext.Ajax.request({
            url: m_crud + '_clonal_garden',
            method: 'GET',
            params: {
                ObjID: Ext.getCmp('ObjID_idcoop').getValue(),
                ObjType: Ext.getCmp('ObjType_idcoop').getValue(),
                ClonalID: clonal_id,
                GardenNr: garden_nr
            },
            success: function(fp, o) {
                var r = Ext.decode(fp.responseText);
                if (r.ClonalID != '') {
                    Ext.getCmp('ClonalID_idcoop').setValue(r.ClonalID);
                    Ext.getCmp('EstablishedYear_idcoop').setValue(r.EstablishedYear);
                    if (r.CertificationStatus == 'Yes') {
                        Ext.getCmp('CertificationStatus1_idcoop').setValue(true);
                    } else {
                        Ext.getCmp('CertificationStatus2_idcoop').setValue(true);
                    }
                    Ext.getCmp('GardenNr_idcoop').setValue(r.GardenNr);
                    Ext.getCmp('GardenNr_default_idcoop').setValue(r.GardenNr);
                    /*if(r.CertificateProvider=='1') Ext.getCmp('CertificateProvider1').setValue(true);
                    if(r.CertificateProvider=='2') Ext.getCmp('CertificateProvider2').setValue(true);
                    if(r.CertificateProvider=='3') Ext.getCmp('CertificateProvider3').setValue(true);
                    Ext.getCmp('CertificateProviderOther').setValue(r.CertificateProviderOther);*/
                    if (r.LandCertificate == '1') Ext.getCmp('LandCertificate1_idcoop').setValue(true);
                    if (r.LandCertificate == '2') Ext.getCmp('LandCertificate2_idcoop').setValue(true);
                    if (r.LandCertificate == '3') Ext.getCmp('LandCertificate3_idcoop').setValue(true);
                    if (r.LandCertificate == '4') Ext.getCmp('LandCertificate4_idcoop').setValue(true);
                    if (r.LandCertificate == '5') Ext.getCmp('LandCertificate5_idcoop').setValue(true);
                    //Ext.getCmp('CertificateProviderOther').setValue(r.CertificateProviderOther);
                    Ext.getCmp('DateAppliedCertification_idcoop').setValue(r.DateAppliedCertification);
                    Ext.getCmp('DateReceivedCertification_idcoop').setValue(r.DateReceivedCertification);
                    Ext.getCmp('Area_idcoop').setValue(r.Area);
                    Ext.getCmp('ClonalGardenLatitude_idcoop').setValue(r.Latitude);
                    Ext.getCmp('ClonalGardenLongitude_idcoop').setValue(r.Longitude);
                    if (r.TSH858 == '1') Ext.getCmp('TSH858_idcoop').setValue(true);
                    Ext.getCmp('TSH858Nr_idcoop').setValue(r.TSH858Nr);
                    if (r.RCC70 == '1') Ext.getCmp('RCC70_idcoop').setValue(true);
                    Ext.getCmp('RCC70Nr_idcoop').setValue(r.RCC70Nr);
                    if (r.RCC71 == '1') Ext.getCmp('RCC71_idcoop').setValue(true);
                    Ext.getCmp('RCC71Nr_idcoop').setValue(r.RCC71Nr);
                    if (r.RCC72 == '1') Ext.getCmp('RCC72_idcoop').setValue(true);
                    Ext.getCmp('RCC72Nr_idcoop').setValue(r.RCC72Nr);
                    if (r.RCC73 == '1') Ext.getCmp('RCC73_idcoop').setValue(true);
                    Ext.getCmp('RCC73Nr_idcoop').setValue(r.RCC73Nr);
                    if (r.Local == '1') Ext.getCmp('Local_idcoop').setValue(true);
                    Ext.getCmp('LocalNr_idcoop').setValue(r.LocalNr);
                    if (r.S1 == '1') Ext.getCmp('S1_idcoop').setValue(true);
                    Ext.getCmp('S1Nr_idcoop').setValue(r.S1Nr);
                    if (r.S2 == '1') Ext.getCmp('S2_idcoop').setValue(true);
                    Ext.getCmp('S2Nr_idcoop').setValue(r.S2Nr);
                    if (r.ICCRI3 == '1') Ext.getCmp('ICCRI3_idcoop').setValue(true);
                    Ext.getCmp('ICCRI3Nr_idcoop').setValue(r.ICCRI3Nr);
                    if (r.ICCRI4 == '1') Ext.getCmp('ICCRI4_idcoop').setValue(true);
                    Ext.getCmp('ICCRI4Nr_idcoop').setValue(r.ICCRI4Nr);
                    if (r.ICCRI5 == '1') Ext.getCmp('ICCRI5_idcoop').setValue(true);
                    Ext.getCmp('ICCRI5Nr_idcoop').setValue(r.ICCRI5Nr);
                    if (r.RCL == '1') Ext.getCmp('RCL_idcoop').setValue(true);
                    Ext.getCmp('RCLNr_idcoop').setValue(r.RCLNr);
                    if (r.M01 == '1') Ext.getCmp('M01_idcoop').setValue(true);
                    Ext.getCmp('M01Nr_idcoop').setValue(r.M01Nr);
                    if (r.M06 == '1') Ext.getCmp('M06_idcoop').setValue(true);
                    Ext.getCmp('M06Nr_idcoop').setValue(r.M06Nr);
                    if (r.THR == '1') Ext.getCmp('THR_idcoop').setValue(true);
                    Ext.getCmp('THRNr_idcoop').setValue(r.THRNr);
                    if (r.CG45 == '1') Ext.getCmp('CG45_idcoop').setValue(true);
                    Ext.getCmp('CG45Nr_idcoop').setValue(r.CG45Nr);
                    if (r.Scavina == '1') Ext.getCmp('Scavina_idcoop').setValue(true);
                    Ext.getCmp('ScavinaNr_idcoop').setValue(r.ScavinaNr);
                    if (r.BLB == '1') Ext.getCmp('BLB_idcoop').setValue(true);
                    Ext.getCmp('BLBNr_idcoop').setValue(r.BLBNr);
                    if (r.M04 == '1') Ext.getCmp('M04_idcoop').setValue(true);
                    Ext.getCmp('M04Nr_idcoop').setValue(r.M04Nr);
                    if (r.MT == '1') Ext.getCmp('MT_idcoop').setValue(true);
                    Ext.getCmp('MTNr_idcoop').setValue(r.MTNr);
                    if (r.M02 == '1') Ext.getCmp('M02_idcoop').setValue(true);
                    Ext.getCmp('M02Nr_idcoop').setValue(r.M02Nr);
                    if (r.AP == '1') Ext.getCmp('AP_idcoop').setValue(true);
                    Ext.getCmp('APNr_idcoop').setValue(r.APNr);
                    if (r.PR == '1') Ext.getCmp('PR_idcoop').setValue(true);
                    Ext.getCmp('PRNr_idcoop').setValue(r.PRNr);
                    if (r.BRT == '1') Ext.getCmp('BRT_idcoop').setValue(true);
                    Ext.getCmp('BRTNr_idcoop').setValue(r.BRTNr);
                    if (r.MHP03 == '1') Ext.getCmp('MHP03_idcoop').setValue(true);
                    Ext.getCmp('MHP03Nr_idcoop').setValue(r.MHP03Nr);
                    if (r.MHP04 == '1') Ext.getCmp('MHP04_idcoop').setValue(true);
                    Ext.getCmp('MHP04Nr_idcoop').setValue(r.MHP04Nr);
                    if (r.BB01 == '1') Ext.getCmp('BB01_idcoop').setValue(true);
                    Ext.getCmp('BB01Nr_idcoop').setValue(r.BB01Nr);
                    Ext.getCmp('OtherClones_idcoop').setValue(r.OtherClones);
                    Ext.getCmp('OtherClonesNr_idcoop').setValue(r.OtherClonesNr);
                    Ext.getCmp('TotalClonesNr_idcoop').setValue(r.TotalClonesNr);
                    //
                    if (r.Coconut == '1') Ext.getCmp('Coconut_idcoop').setValue(true);
                    Ext.getCmp('CoconutNr_idcoop').setValue(r.CoconutNr);
                    if (r.ArecaPalm == '1') Ext.getCmp('ArecaPalm_idcoop').setValue(true);
                    Ext.getCmp('ArecaPalmNr_idcoop').setValue(r.ArecaPalmNr);
                    if (r.Rubber == '1') Ext.getCmp('Rubber_idcoop').setValue(true);
                    Ext.getCmp('RubberNr_idcoop').setValue(r.RubberNr);
                    if (r.Clove == '1') Ext.getCmp('Clove_idcoop').setValue(true);
                    Ext.getCmp('CloveNr_idcoop').setValue(r.CloveNr);
                    if (r.Cashew == '1') Ext.getCmp('Cashew_idcoop').setValue(true);
                    Ext.getCmp('CashewNr_idcoop').setValue(r.CashewNr);
                    if (r.OilPalm == '1') Ext.getCmp('OilPalm_idcoop').setValue(true);
                    Ext.getCmp('OilPalmNr_idcoop').setValue(r.OilPalmNr);
                    if (r.SugarPalm == '1') Ext.getCmp('SugarPalm_idcoop').setValue(true);
                    Ext.getCmp('SugarPalmNr_idcoop').setValue(r.SugarPalmNr);
                    if (r.Nutmeg == '1') Ext.getCmp('Nutmeg_idcoop').setValue(true);
                    Ext.getCmp('NutmegNr_idcoop').setValue(r.NutmegNr);
                    if (r.Hazelnut == '1') Ext.getCmp('Hazelnut_idcoop').setValue(true);
                    Ext.getCmp('HazelnutNr_idcoop').setValue(r.HazelnutNr);
                    if (r.Kapok == '1') Ext.getCmp('Kapok_idcoop').setValue(true);
                    Ext.getCmp('KapokNr_idcoop').setValue(r.KapokNr);
                    //
                    if (r.Mahagony == '1') Ext.getCmp('Mahagony_idcoop').setValue(true);
                    Ext.getCmp('MahagonyNr_idcoop').setValue(r.MahagonyNr);
                    if (r.Teak == '1') Ext.getCmp('Teak_idcoop').setValue(true);
                    Ext.getCmp('TeakNr_idcoop').setValue(r.TeakNr);
                    if (r.Vitex == '1') Ext.getCmp('Vitex_idcoop').setValue(true);
                    Ext.getCmp('VitexNr_idcoop').setValue(r.VitexNr);
                    if (r.Ermerilla == '1') Ext.getCmp('Ermerilla_idcoop').setValue(true);
                    Ext.getCmp('ErmerillaNr_idcoop').setValue(r.ErmerillaNr);
                    if (r.Anthocephalus == '1') Ext.getCmp('Anthocephalus_idcoop').setValue(true);
                    Ext.getCmp('AnthocephalusNr_idcoop').setValue(r.AnthocephalusNr);
                    if (r.Albizia == '1') Ext.getCmp('Albizia_idcoop').setValue(true);
                    Ext.getCmp('AlbiziaNr_idcoop').setValue(r.AlbiziaNr);
                    //
                    if (r.Jackfruit == '1') Ext.getCmp('Jackfruit_idcoop').setValue(true);
                    Ext.getCmp('JackfruitNr_idcoop').setValue(r.JackfruitNr);
                    if (r.Banana == '1') Ext.getCmp('Banana_idcoop').setValue(true);
                    Ext.getCmp('BananaNr_idcoop').setValue(r.BananaNr);
                    if (r.Rambutan == '1') Ext.getCmp('Rambutan_idcoop').setValue(true);
                    Ext.getCmp('RambutanNr_idcoop').setValue(r.RambutanNr);
                    if (r.Mango == '1') Ext.getCmp('Mango_idcoop').setValue(true);
                    Ext.getCmp('MangoNr_idcoop').setValue(r.MangoNr);
                    if (r.SpondiasDulcis == '1') Ext.getCmp('SpondiasDulcis_idcoop').setValue(true);
                    Ext.getCmp('SpondiasDulcisNr_idcoop').setValue(r.SpondiasDulcisNr);
                    if (r.Langsat == '1') Ext.getCmp('Langsat_idcoop').setValue(true);
                    Ext.getCmp('LangsatNr_idcoop').setValue(r.LangsatNr);
                    if (r.Durian == '1') Ext.getCmp('Durian_idcoop').setValue(true);
                    Ext.getCmp('DurianNr_idcoop').setValue(r.DurianNr);
                    if (r.Guava == '1') Ext.getCmp('Guava_idcoop').setValue(true);
                    Ext.getCmp('GuavaNr_idcoop').setValue(r.GuavaNr);
                    if (r.Avocado == '1') Ext.getCmp('Avocado_idcoop').setValue(true);
                    Ext.getCmp('AvocadoNr_idcoop').setValue(r.AvocadoNr);
                    if (r.Cempedak == '1') Ext.getCmp('Cempedak_idcoop').setValue(true);
                    Ext.getCmp('CempedakNr_idcoop').setValue(r.CempedakNr);
                    if (r.Breadfruit == '1') Ext.getCmp('Breadfruit_idcoop').setValue(true);
                    Ext.getCmp('BreadfruitNr_idcoop').setValue(r.BreadfruitNr);
                    if (r.Papaya == '1') Ext.getCmp('Papaya_idcoop').setValue(true);
                    Ext.getCmp('PapayaNr_idcoop').setValue(r.PapayaNr);
                    if (r.Mangosteen == '1') Ext.getCmp('Mangosteen_idcoop').setValue(true);
                    Ext.getCmp('MangosteenNr_idcoop').setValue(r.MangosteenNr);
                    if (r.Citrus == '1') Ext.getCmp('Citrus_idcoop').setValue(true);
                    Ext.getCmp('CitrusNr_idcoop').setValue(r.CitrusNr);
                    //
                    if (r.Gliricidia == '1') Ext.getCmp('Gliricidia_idcoop').setValue(true);
                    Ext.getCmp('GliricidiaNr_idcoop').setValue(r.GliricidiaNr);
                    if (r.Leucaena == '1') Ext.getCmp('Leucaena_idcoop').setValue(true);
                    Ext.getCmp('LeucaenaNr_idcoop').setValue(r.LeucaenaNr);
                    if (r.Parkia == '1') Ext.getCmp('Parkia_idcoop').setValue(true);
                    Ext.getCmp('ParkiaNr_idcoop').setValue(r.ParkiaNr);
                    if (r.Archidendron == '1') Ext.getCmp('Archidendron_idcoop').setValue(true);
                    Ext.getCmp('ArchidendronNr_idcoop').setValue(r.ArchidendronNr);
                    Ext.getCmp('TotalShadeTreesNr_idcoop').setValue(r.TotalShadeTreesNr);
                    store_clonal_penjualan_coop.load({
                        params: {
                            clonal_id: r.ClonalID
                        }
                    });
                    store_clonal_monitoring_coop.load({
                        params: {
                            clonal_id: r.ClonalID
                        }
                    });
                }
            }
        })
    }
    var DataFormClonalGardenPolygonCoop = Ext.create('Ext.form.Panel', {
        autoScroll: true,
        bodyPadding: 5,
        id: 'DataFormClonalGardenPolygonCoop',
        fieldDefaults: {
            labelAlign: 'left',
            labelWidth: 120,
            anchor: '95%'
        },
        items: [{
            xtype: 'gridpanel',
            id: 'gridClonalGardenPolygon_idcoop',
            style: 'border:1px solid #CCC;',
            store: store_clonal_polygon_coop,
            width: '100%',
            loadMask: true,
            dockedItems: [{
                xtype: 'toolbar',
                items: [{
                    icon: varjs.config.base_url + 'images/icons/new/add.png', cls:'Sfr_BtnGridGreen', overCls:'Sfr_BtnGridGreen-Hover',
                    cls: m_act_save,
                    hidden: !m_act_add,
                    text: lang('Add'),
                    scope: this,
                    handler: function() {
                        //display_area('');
                        Ext.getCmp('ObjType_idcoop').setValue('koperasi');
                        Ext.getCmp('ObjID_idcoop').setValue(Ext.getCmp('CoopID').getValue());
                        store_clonal_penjualan_coop.clearData();
                        store_clonal_penjualan_coop.removeAll();
                        store_clonal_monitoring_coop.clearData();
                        store_clonal_monitoring_coop.removeAll();
                        displayFormClonalGardenCoop();
                    }
                }, {
                    icon: varjs.config.base_url + 'images/icons/new/update.png',
                    cls: m_act_save,
                    hidden: !m_act_update,
                    text: lang('Update'),
                    scope: this,
                    handler: function() {
                        var sm = Ext.getCmp('gridClonalGardenPolygon_idcoop').getSelectionModel().getSelection()[0];
                        if (sm == undefined) {
                            Ext.MessageBox.alert('Warning', lang('Please select Garden!'));
                        } else {
                            edit_clonal_garden_coop(sm.get('ClonalID_idcoop'), sm.get('GardenNr'));
                        }
                    }
                }, {
                    itemId: 'remove',
                    icon: varjs.config.base_url + 'images/icons/new/delete.png',
                    cls: m_act_save,
                    hidden: !m_act_delete,
                    text: lang('Delete'),
                    scope: this,
                    handler: function() {
                        var sm = Ext.getCmp('gridClonalGardenPolygon_idcoop').getSelectionModel().getSelection()[0];
                        if (sm == undefined) {
                            Ext.MessageBox.alert('Warning', lang('Please select Garden!'));
                        } else {
                            Ext.MessageBox.confirm('Message', lang('Apakah anda mau menghapus data ini ?'), function(btn) {
                                if (btn == 'yes') {
                                    Ext.Ajax.request({
                                        waitMsg: lang('Please Wait'),
                                        url: m_clonal + '_polygon',
                                        method: 'DELETE',
                                        params: {
                                            ObjType: 'koperasi',
                                            ObjID: Ext.getCmp('CoopID').getValue(),
                                            clonal_id: sm.get('ClonalID'),
                                            garden_nr: sm.get('GardenNr')
                                        },
                                        success: function(response, opts) {
                                            var obj = Ext.decode(response.responseText);
                                            switch (obj.success) {
                                                case true:
                                                    Ext.MessageBox.alert('Success', obj.message);
                                                    store_clonal_polygon_coop.load({
                                                        params: {
                                                            ObjType: 'koperasi',
                                                            ObjID: Ext.getCmp('CoopID').getValue()
                                                        }
                                                    });
                                                    break;
                                                default:
                                                    Ext.MessageBox.alert('Warning', obj.message);
                                                    break;
                                            }
                                        },
                                        failure: function(response, opts) {
                                            Ext.MessageBox.alert('error', lang('Could not connect to the database. Retry later'));
                                        }
                                    });
                                }
                            });
                        }
                    }
                }]
            }],
            columns: [{
                text: lang('ID'),
                dataIndex: 'ClonalID',
                align: 'center',
                hidden: true
            }, {
                text: lang('No'),
                xtype: 'rownumberer',
                width: '10%'
            }, {
                text: lang('Garden Number') + ' (GardenNr)',
                dataIndex: 'GardenNr',
                width: '45%',
            }, {
                text: lang('Area (Ha)'),
                dataIndex: 'Area',
                width: '45%',
            }],
            listeners: {
                itemdblclick: function() {
                    var sm = Ext.getCmp('gridClonalGardenPolygon_idcoop').getSelectionModel().getSelection()[0];
                    edit_clonal_garden_coop(sm.get('ClonalID'), sm.get('GardenNr'));
                }
            }
        }],
        buttons: [{
            text: lang('Close'),
            margin: '5px',
            scale: 'large',
            ui: 's-button',
            cls: 's-grey',
            disabled: false,
            handler: function() {
                winClonalGardenPolygonCoop.hide();
            }
        }]
    });
    //****//
    var winCompostPenjualan = Ext.create('widget.window', {
        title: lang('Organisasi Petani Compost Unit'),
        id: 'winCompostPenjualan',
        closable: true,
        modal: true,
        closeAction: 'show',
        width: '80%',
        height: 470,
        layout: {
            type: 'fit'
        },
        items: [DataFormCompostPenjualan]
    });
    var winClonalGardenPolygonCoop = Ext.create('widget.window', {
        title: lang('Clonal Garden'),
        id: 'winClonalGardenPolygonCoop',
        closable: true,
        modal: true,
        closeAction: 'show',
        width: '50%',
        height: 400,
        layout: {
            type: 'fit'
        },
        items: [DataFormClonalGardenPolygonCoop]
    });
    //==end compos 2
    //====== nursery ==========================================
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

    function act_clonal_status_coop(val) {
        if (val != 'Tidak Berjalan') {
            Ext.getCmp('clonalDescription_idcoop').allowBlank = true;
            Ext.getCmp('clonalDescription_idcoop').getStore().loadData(['']);
        } else {
            Ext.getCmp('clonalDescription_idcoop').allowBlank = false;
            Ext.getCmp('clonalDescription_idcoop').getStore().loadData([
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
    var clonalRowEditing_idcoop = Ext.create('Ext.grid.plugin.RowEditing', {
        id: 'clonalRowEditing_idcoop',
        clicksToMoveEditor: 0,
        autoCancel: false,
        errorSummary: false,
        clicksToEdit: 2
    });
    var mRowEditing = Ext.create('Ext.grid.plugin.RowEditing', {
        id: 'mRowEditing',
        clicksToMoveEditor: 0,
        autoCancel: false,
        errorSummary: false,
        clicksToEdit: 2
    });
    var mclonalRowEditing_idcoop = Ext.create('Ext.grid.plugin.RowEditing', {
        id: 'mclonalRowEditing_idcoop',
        clicksToMoveEditor: 0,
        autoCancel: false,
        errorSummary: false,
        clicksToEdit: 2
    });
    /*
    var store_nursey = Ext.create('Ext.data.Store', {
          extend: 'Ext.data.Model',
          autoLoad: false,
          fields: ['id','FarmerPIC','Volume','DateStarted'],
          proxy: {
              type: 'ajax',
              url: m_store_nurseys,
              reader: {
                  type: 'json',
                  root: 'data'
              }
          }
      });
     */
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
    var store_clonal_penjualan_coop = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'Buyer', 'Volume', 'Price', 'Total', 'DateTransaction', 'CloneTypeID', 'CloneTypeName', 'ClonalID', 'ClonalTransactionID'],
        autoLoad: false,
        proxy: {
            type: 'ajax',
            url: m_store_clonal_penjualans,
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
    var store_clonal_monitoring_coop = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'MonitoringDate', 'MonitoringStatus', 'Description'],
        autoLoad: false,
        proxy: {
            type: 'ajax',
            url: m_store_clonal_monitorings,
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });
    // store combobox monitoring
    var mc_status_monitoring = Ext.create('Ext.data.Store', {
        fields: ['label'],
        data: [{
            'label': lang('Sedang di bangun/Belum selesai')
        }, {
            'label': lang('Berjalan/Produktif')
        }, {
            'label': lang('Tidak Berjalan')
        }]
    });
    //==nursey 2
    function displayFormNurseyPenjualan() {
        if (!winNurseyPenjualan.isVisible()) {
            DataFormNurseyPenjualan.getForm().reset();
            winNurseyPenjualan.center();
            winNurseyPenjualan.show();
        } else {
            winNurseyPenjualan.hide(this, function() {});
            winNurseyPenjualan.toFront();
        }
    }

    var areawindow_nursery_idcoop = Ext.create('widget.window', {
        id: 'areawindow_nursery_idcoop',
        title: lang('Nursery Polygon'),
        closable: false,
        modal: true,
        layout: 'fit',
        closeAction: 'show',
        width: '75%',
        height: 600,
        bodyPadding: 5,
        listeners: {
            close: function(cb, nv, ov) {
                hitung_area_nursery_idcoop();
            }
        },
        buttons: [/*{
            id: 'polygonsaveButton',
            text: lang('Save'),
            margin: '5px',
            scale: 'large',
            ui: 's-button',
            cls: 's-blue ' + m_act_save,
            handler: function() {

            }
        },*/ {
            text: lang('Close'),
            margin: '5px',
            id: 'cLosePolygon_idcoop',
            scale: 'large',
            ui: 's-button',
            cls: 's-grey',
            disabled: false,
            handler: function() {
                areawindow_nursery_idcoop.hide();
                hitung_area_nursery_idcoop();
            }
        }]
    });

    function hitung_area_nursery_idcoop() {
        Ext.Ajax.request({
            url: m_crud + '_nursery_polygon_area',
            method: 'GET',
            params: {
                ObjType: 'koperasi',
                ObjID: Ext.getCmp('CoopID').getValue(),
                NurseryNr: Ext.getCmp('NurseryNr_idcoop').getValue(),
                NurseryID: Ext.getCmp('NurseryID_idcoop').getValue(),
            },
            success: function(fp, o) {
                var r = Ext.decode(fp.responseText);
                //Ext.getCmp('Area_idcoop').setValue(r.Area);
                Ext.getCmp('Latitude_idcoop').setValue(r.Latitude);
                Ext.getCmp('Longitude_idcoop').setValue(r.Longitude);
            }
        })
    }

    function display_area_nursery(nursery_id,nursery_nr) {
        var areaPanel = Ext.getCmp('areawindow_nursery_idcoop');
        areaPanel.center();
        areaPanel.show();
        Ext.Ajax.request({
            url: m_trader + 'nursery_polygon/koperasi',
            method: 'GET',
            params: {
                NurseryID: nursery_id,
                NurseryNr: nursery_nr,
                lati: Ext.getCmp('Latitude_idcoop').getValue(),
                longi: Ext.getCmp('Longitude_idcoop').getValue(),
                hakAksesPolygon: m_hakakses_polygon
            },
            success: function(response) {
                var htmlText = response.responseText;
                //Get the Panel component using its id
                // update the panel content's with
                // HTML response from Ajax call
                areaPanel.update(htmlText, true);
            }
        });
    }

    function fillNurseryForm(NurseryNr){

        Ext.getCmp('DataFormNurseyCoop').getForm().load({
            url: m_crud + '_dataFormNursery',
            method: 'GET',
            params: {
                id: Ext.getCmp('CoopID').getValue(),
                nursery_id : NurseryNr
            },
            success: function(form, action) {
                //var d = Ext.decode(form.responseText);
                var actionData = Ext.decode(action.response.responseText);
                var d = actionData.data;

                store_nursey_trans.load({
                    params: {
                        id: d.NurseryID
                    }
                });
                store_nursey_monitoring.load({
                    params: {
                        nursery_id: d.NurseryID
                    }
                });

                if(d.NurseryID == null){
                    store_nursey_trans.removeAll();
                    store_nursey_trans.sync()
                    Ext.getCmp('gnurseypenjualan_idcoop').setDisabled(true);
                    Ext.getCmp('gnurseymonitoring_idcoop').setDisabled(true);
                } else {
                    store_nursey_trans.load({
                        params: {
                            id: d.NurseryID
                        }
                    });

                    Ext.getCmp('NurseryID_idcoop').setValue(d.NurseryID);
                    Ext.getCmp('NurseryNr_idcoop').setValue(d.NurseryNr);
                    Ext.getCmp('nid_obj_idcoop').setValue(d.ObjID);
                    Ext.getCmp('ntype_obj_idcoop').setValue(d.ObjType);
                    //Ext.getCmp('Responsible_idcoop').setValue(d.Responsible);
                    Ext.getCmp('Established_idcoop').setValue(d.Established);
                    Ext.getCmp('Panjang_idcoop').setValue(d.Panjang);
                    Ext.getCmp('Lebar_idcoop').setValue(d.Lebar);
                    Ext.getCmp('Luas_idcoop').setValue(d.Luas);
                    Ext.getCmp('Kapasitas_idcoop').setValue(nnumber_format(d.Kapasitas));
                    Ext.getCmp('Latitude_idcoop').setValue(d.Latitude);
                    Ext.getCmp('Longitude_idcoop').setValue(d.Longitude);
                    if (d.CertificationStatus == 'Yes') {
                        Ext.getCmp('CertificationStatusYes_idcoop').setValue(true);
                        Ext.getCmp('DateCertification_idcoop').setValue(d.DateCertification);
                        Ext.getCmp('DateAppliedCertification').setValue(d.DateAppliedCertification);
                    } else {
                        Ext.getCmp('CertificationStatusNo_idcoop').setValue(true);
                    }
                    /*Ext.getCmp('LatitudeDeg1').setValue(d.LatitudeDeg1);
                    Ext.getCmp('LatitudeDeg2').setValue(d.LatitudeDeg2);
                    Ext.getCmp('LatitudeDeg3').setValue(d.LatitudeDeg3);
                    Ext.getCmp('LongitudeDeg1').setValue(d.LongitudeDeg1);
                    Ext.getCmp('LongitudeDeg2').setValue(d.LongitudeDeg2);
                    Ext.getCmp('LongitudeDeg3').setValue(d.LongitudeDeg3);*/

                    Ext.getCmp('gnurseypenjualan_idcoop').setDisabled(false);
                    Ext.getCmp('gnurseymonitoring_idcoop').setDisabled(false);

                    //photo===========================================
                    if(d.Photo != ""){
                        var fotoUser = m_api_base_url + '/images/nursery/' + d.Photo;
                        Ext.getCmp('Photo_old_idcoop').setValue(d.Photo);
                        checkImageExists(fotoUser, function(existsImage) {
                            if (existsImage == true) {
                                Ext.getCmp('iphoto_idcoop').setSrc(fotoUser);
                            } else {
                                Ext.getCmp('iphoto_idcoop').setSrc(m_api_base_url + '/images/nursery/no-image.png');
                            }
                        });
                    }

                    //photo responsible=====================================
                    if(d.ResponsiblePhoto != ""){
                        var fotoUserResponsible = m_api_base_url + '/images/photo_responsible/' + d.ResponsiblePhoto;
                        Ext.getCmp('Photo_old_responsible_idcoop').setValue(d.ResponsiblePhoto);
                        checkImageExists(fotoUserResponsible, function(existsImage) {
                            if (existsImage == true) {
                                Ext.getCmp('iphotoResponsible_idcoop').setSrc(fotoUserResponsible);
                            } else {
                                Ext.getCmp('iphotoResponsible_idcoop').setSrc(m_api_base_url + '/images/Photo/no-user.jpg');
                            }
                        });
                    }

                    if(d.ResponsibleGender == "m"){
                        Ext.getCmp('ResponsibleGenderM_idcoop').setValue(true);
                    }
                    if(d.ResponsibleGender == "f"){
                        Ext.getCmp('ResponsibleGenderF_idcoop').setValue(true);
                    }

                    Ext.getCmp('Responsible_idcoop').setValue(d.Responsible);
                    Ext.getCmp('ResponsibleType_idcoop').setValue(d.ResponsibleType);
                    Ext.getCmp('ResponsibleName_idcoop').setValue(d.ResponsibleName);
                    Ext.getCmp('ResponsiblePhone_idcoop').setValue(d.ResponsiblePhone);
                    Ext.getCmp('ResponsibleBirthday_idcoop').setValue(d.ResponsibleBirthday);
                }

                Ext.getCmp('NurseryNr_idcoop').setReadOnly(true);
            },
            failure: function(form, action) {
                Ext.MessageBox.show({
                    title: 'Failed',
                    msg: 'Failed to get data',
                    buttons: Ext.MessageBox.OK,
                    animateTarget: 'mb9',
                    icon: 'ext-mb-error'
                });
            }
        });
    }

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
            url: m_api + '/cooperatives/nursery_respon_by_type',
            reader: {
                type: 'json',
                root: 'data'
            }
        },
        listeners: {
            'beforeload': function(store, options) {
                store.proxy.extraParams.responsibleType = Ext.getCmp('ResponsibleType_idcoop').getValue();
                store.proxy.extraParams.CoopID = Ext.getCmp('CoopID').getValue();
            }
        }
    });

    function cekNurseryID(){
        if(Ext.getCmp('NurseryID_idcoop').getValue() != ""){
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

    // nursery panel container
    var DataFormNurseyCoop = Ext.create('Ext.panel.Panel', {
        frame: false,
        autoScroll: true,
        height: 475,
        width: '100%',
        bodyPadding: 5,
        id: 'DataFormNurseyCoopWin',
        items:[{
            xtype: 'form',
            id: 'DataFormNurseyCoop',
            fileUpload: true,
            fieldDefaults: {
                labelAlign: 'left',
                labelWidth: 200,
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
                        id: 'NurseryID_idcoop',
                        name: 'NurseryID',
                        hidden: true
                    }, {
                        xtype: 'textfield',
                        id: 'nid_obj_idcoop',
                        name: 'id_obj',
                        hidden: true
                    }, {
                            xtype: 'textfield',
                            id: 'ntype_obj_idcoop',
                            name: 'type_obj',
                            value: 'koperasi',
                            hidden: true
                    }, {
                            xtype: 'numberfield',
                            fieldLabel: lang('NurseryNr'),
                            id: 'NurseryNr_idcoop',
                            name: 'NurseryNr',
                            allowBlank: false,
                            minValue: 1
                    }, {
                            xtype: 'combo',
                            store: cmb_respon_type,
                            labelWidth: '175',
                            fieldLabel: lang('Responsible Type'),
                            id: 'ResponsibleType_idcoop',
                            name: 'ResponsibleType_idcoop',
                            displayField: 'label',
                            valueField: 'id',
                            queryMode: 'local',
                            allowBlank: false,
                            listeners: {
                                change: function(cb, nv, ov) {
                                    if(nv != 'other'){
                                        Ext.getCmp('Responsible_idcoop').setDisabled(false);
                                        Ext.getCmp('ResponsibleName_idcoop').setVisible(false);
                                        Ext.getCmp('ResponsibleBirthday_idcoop').setVisible(false);
                                        Ext.getCmp('ResponsiblePhone_idcoop').setVisible(false);
                                        Ext.getCmp('ResponsibleGender_idcoop').setVisible(false);
                                        Ext.getCmp('divPhotoResponsible_idcoop').setVisible(false);
                                        Ext.getCmp('PhotoResponsible_idcoop').setVisible(false);
                                        cmb_respon_id.load();
                                    }else{
                                        Ext.getCmp('Responsible_idcoop').setDisabled(true);
                                        Ext.getCmp('ResponsibleName_idcoop').setVisible(true);
                                        Ext.getCmp('ResponsibleBirthday_idcoop').setVisible(true);
                                        Ext.getCmp('ResponsiblePhone_idcoop').setVisible(true);
                                        Ext.getCmp('ResponsibleGender_idcoop').setVisible(true);
                                        Ext.getCmp('divPhotoResponsible_idcoop').setVisible(true);
                                        Ext.getCmp('PhotoResponsible_idcoop').setVisible(true);
                                    }
                                }
                            }
                    },{
                            xtype: 'combo',
                            store: cmb_respon_id,
                            labelWidth: '175',
                            fieldLabel: lang('Penanggung Jawab'),
                            id: 'Responsible_idcoop',
                            name: 'Responsible_idcoop',
                            displayField: 'label',
                            valueField: 'id',
                            queryMode: 'local'
                    },{
                                xtype: 'textfield',
                                fieldLabel: lang('Responsible Name'),
                                id: 'ResponsibleName_idcoop',
                                name: 'ResponsibleName_idcoop',
                                hidden:true
                            },{
                                xtype: 'datefield',
                                fieldLabel: lang('Responsible Birthdate'),
                                id: 'ResponsibleBirthday_idcoop',
                                name: 'ResponsibleBirthday_idcoop',
                                format: 'Y-m-d',
                                hidden:true
                            },{
                                xtype: 'textfield',
                                fieldLabel: lang('Responsible Phone'),
                                id: 'ResponsiblePhone_idcoop',
                                name: 'ResponsiblePhone_idcoop',
                                hidden:true
                            },{
                                xtype: 'radiogroup',
                                fieldLabel: lang('Responsible Gender'),
                                id:'ResponsibleGender_idcoop',
                                hidden:true,
                                items: [{
                                    name: 'ResponsibleGender_idcoop',
                                    id: 'ResponsibleGenderM_idcoop',
                                    boxLabel: lang('Male'),
                                    inputValue: 'm'
                                }, {
                                    name: 'ResponsibleGender_idcoop',
                                    id: 'ResponsibleGenderF_idcoop',
                                    boxLabel: lang('Female'),
                                    inputValue: 'f'
                                }]
                            },{
                                layout:'column',
                                border:false,
                                style:'margin-bottom:5px;margin-right:-5px;',
                                id:'divPhotoResponsible_idcoop',
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
                                        id: 'iphotoResponsible_idcoop',
                                        width: '150px',
                                        height:'150px',
                                        src: m_api_base_url + '/images/Photo/no-user.jpg'
                                    },{
                                        xtype: 'textfield',
                                        id: 'Photo_old_responsible_idcoop',
                                        name: 'Photo_old_responsible_idcoop',
                                        inputType: 'hidden'
                                    }]
                                }]
                            },{
                                xtype: 'fileuploadfield',
                                fieldLabel: lang('Photo'),
                                labelWidth: 130,
                                id: 'PhotoResponsible_idcoop',
                                name: 'PhotoResponsible_idcoop',
                                buttonText: 'Browse',
                                hidden:true,
                                listeners: {
                                    'change': function (fb, v) {
                                        var form = Ext.getCmp('DataFormNurseyCoop').getForm();
                                        form.submit({
                                            url: m_api + '/cooperatives/nursery_form_photo_responsible',
                                            clientValidation: false,
                                            waitMsg: 'Sending Photo...',
                                            success: function (fp, o) {
                                                Ext.getCmp('iphotoResponsible_idcoop').setSrc(m_api_base_url + '/images/photo_responsible/' + o.result.file);
                                                Ext.getCmp('Photo_old_responsible_idcoop').setValue(o.result.file);
                                            }
                                        });
                                    }
                                }
                            }
                    ,{
                            xtype: 'datefield',
                            fieldLabel: lang('Tanggal Berdiri'),
                            id: 'Established_idcoop',
                            name: 'Established',
                            format: 'Y-m-d'
                    }, {
                            xtype: 'radiogroup',
                            fieldLabel: lang('Certification Status'),
                            items: [{
                                name: 'CertificationStatus',
                                id: 'CertificationStatusYes_idcoop',
                                boxLabel: lang('Yes, BP2MB'),
                                inputValue: 'Yes'
                            }, {
                                name: 'CertificationStatus',
                                id: 'CertificationStatusNo_idcoop',
                                boxLabel: lang('Tidak'),
                                inputValue: 'No',
                                // checked: true,
                            }],
                            listeners: {
                                change: function(cb, nv, ov) {
                                   if(Ext.getCmp('CertificationStatusYes_idcoop').getValue() == true){
                                       Ext.getCmp('DateCertification_idcoop').setDisabled(false);
                                       Ext.getCmp('DateAppliedCertification').setDisabled(false);
                                    }else{
                                       Ext.getCmp('DateCertification_idcoop').setDisabled(true);
                                       Ext.getCmp('DateCertification_idcoop').setValue('');
                                       Ext.getCmp('DateAppliedCertification').setDisabled(true);
                                       Ext.getCmp('DateAppliedCertification').setValue('');
                                    }
                                }
                            }
                    }, {
                            xtype: 'datefield',
                            fieldLabel: lang('Date of Certification Status'),
                            id: 'DateCertification_idcoop',
                            name: 'DateCertification',
                            format: 'Y-m-d'
                    }, {
                            xtype: 'datefield',
                            fieldLabel: lang('Date Applied for Certification'),
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
                                    var urlPrint = m_api + '/nursery/cetak_nursery_summary/koperasi/'+Ext.getCmp('CoopID').getValue()+'/'+Ext.getCmp('NurseryNr_idcoop').getValue()+'/';
                                    preview_cetak_surat(urlPrint);
                                }
                            }
                        }]
                    }]
                    },{
                    columnWidth: .5,
                    layout: 'form',
                    border: false,
                    padding: 5,
                    items: [{
                        layout: 'column',
                        border: false,
                        items: [{
                            columnWidth: 1,
                            layout: 'form',
                            border: false,
                            //padding: 5,
                            items: [{
                                xtype: 'textfield',
                                fieldLabel: lang('Panjang (m)'),
                                id: 'Panjang_idcoop',
                                name: 'Panjang',
                                fieldCls: 'classuang',
                                listeners: {
                                    change: function(cb, nv, ov) {
                                        Ext.getCmp('Luas_idcoop').setValue(nnumber_format(nnumber_format(Ext.getCmp('Panjang_idcoop').getValue(), 2) *
                                        nnumber_format(Ext.getCmp('Lebar_idcoop').getValue(), 2)))
                                    }
                                }
                            }]
                        }, {
                            columnWidth: 1,
                            layout: 'form',
                            border: false,
                            //padding: 5,
                            items: [{
                                xtype: 'textfield',
                                fieldLabel: lang('Lebar (m)'),
                                id: 'Lebar_idcoop',
                                name: 'Lebar',
                                listeners: {
                                    change: function(cb, nv, ov) {
                                        Ext.getCmp('Luas_idcoop').setValue(nnumber_format(nnumber_format(Ext.getCmp('Panjang_idcoop').getValue(), 2) *
                                        nnumber_format(Ext.getCmp('Lebar_idcoop').getValue(), 2)))
                                    }
                                }
                            }]
                        }]
                    }, {
                        xtype: 'textfield',
                        fieldLabel: lang('Luas (m2)'),
                        id: 'Luas_idcoop',
                        name: 'Luas',
                        readOnly: true,
                        listeners: {
                            change: function(cb, nv, ov) {
                                Ext.getCmp('Kapasitas_idcoop').setValue(nnumber_format(nnumber_format(Ext.getCmp('Luas_idcoop').getValue(), 2) * 40))
                            }
                        }
                    }, {
                        xtype: 'textfield',
                        fieldLabel: lang('Kapasitas (Luas (m2) x 40)'),
                        id: 'Kapasitas_idcoop',
                        name: 'Kapasitas',
                        labelWidth: 160,
                        readOnly: true
                    }, {
                        xtype: 'textfield',
                        fieldLabel: lang('Latitude (Dec)'),
                        id: 'Latitude_idcoop',
                        name: 'Latitude',
                        readOnly: m_hakakses_lat_short
                    }, {
                        xtype: 'textfield',
                        fieldLabel: lang('Longitude (Dec)'),
                        id: 'Longitude_idcoop',
                        name: 'Longitude',
                        readOnly: m_hakakses_long_short
                    }, {
                        items: [{
                            layout: 'column',
                            items: [{
                                html: lang('Map Area')
                            }, {
                                items: [{
                                    xtype: 'button',
                                    margin: '0 0 0 148',
                                    id: 'buttonShowPolygonNursery_idcoop',
                                    text: lang('Show Polygon'),
                                    handler: function() {
                                        if (Ext.getCmp('NurseryID_idcoop').getValue() == '') {
                                            Ext.MessageBox.alert('Warning', 'Please save Nursery first!');
                                        } else {
                                            display_area_nursery(Ext.getCmp('NurseryID_idcoop').getValue(),Ext.getCmp('NurseryNr_idcoop').getValue());
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
                                id: 'iphoto_idcoop',
                                width: '150px',
                                height:'150px',
                                src: m_api_base_url + '/images/nursery/no-image.png'
                            },{
                                xtype: 'textfield',
                                id: 'Photo_old_idcoop',
                                name: 'Photo_old_idcoop',
                                inputType: 'hidden'
                            }]
                        }]
                    },{
                        xtype: 'fileuploadfield',
                        fieldLabel: lang('Photo'),
                        id: 'Photo_idcoop',
                        name: 'Photo_idcoop',
                        buttonText: 'Browse',
                        listeners: {
                            'change': function (fb, v) {
                                var form = Ext.getCmp('DataFormNurseyCoop').getForm();
                                form.submit({
                                    url: m_api + '/cooperatives/nursery_form_photo',
                                    clientValidation: false,
                                    waitMsg: 'Sending Photo...',
                                    success: function (fp, o) {
                                        Ext.getCmp('iphoto_idcoop').setSrc(m_api_base_url + '/images/nursery/' + o.result.file);
                                        Ext.getCmp('Photo_old_idcoop').setValue(o.result.file);
                                    }
                                });
                            }
                        }
                    }]
                }]
                },{
                xtype: 'tabpanel',
                flex: 1,
                margin:2,
                activeTab: 0,
                plain: true,
                items: [{ // grid nursery penjualan
                    xtype: 'gridpanel',
                    title: lang('Nursery Penjualan'),
                    id: 'gnurseypenjualan_idcoop',
                    style: 'border:1px solid #CCC;',
                    store: store_nursey_trans,
                    width: '100%',
                    height: 500,
                    loadMask: true,
                    selType: 'rowmodel',
                    minHeight:190,
                    dockedItems: [{
                        xtype: 'toolbar',
                        items: [{
                                    icon: varjs.config.base_url + 'images/icons/new/add.png', cls:'Sfr_BtnGridGreen', overCls:'Sfr_BtnGridGreen-Hover',
                                    cls: m_act_save,
                                    text: lang('Add'),
                                    scope: this,
                                    handler: function() {
                                        if(Ext.getCmp('NurseryID_idcoop').getValue()==''){
                                            Ext.MessageBox.alert('Warning', 'Please save Nursery first!');
                                        }else{
                                            nRowEditing.cancelEdit();
                                            var r = Ext.create('nurseryTransaction.Model', {
                                                NurseryTransactionID: '', Buyer: '', Volume: '', Price: '', Total: '', DateTransaction: ''
                                            });
                                            store_nursey_trans.insert(0, r);
                                            nRowEditing.startEdit(0, 0);
                                            uang(document.getElementById('nvol_idcoop'))
                                        }
                                    }
                                },{
                                    icon: varjs.config.base_url + 'images/icons/new/update.png',
                                    cls: m_act_save,
                                    text: lang('Update'),
                                    scope: this,
                                    handler: function() {
                                        nRowEditing.cancelEdit();
                                        var sm = Ext.getCmp('gnurseypenjualan_idcoop').getSelectionModel().getSelection();
                                        nRowEditing.startEdit(sm[0].index, 0);
                                    }
                                },
                                {
                                    itemId: 'remove',
                                    icon: varjs.config.base_url + 'images/icons/new/delete.png',
                                    cls: m_act_save,
                                    text: lang('Delete'),
                                    scope: this,
                                    handler: function() {
                                        var smb = Ext.getCmp('gnurseypenjualan_idcoop').getSelectionModel().getSelection()[0];
                                        nRowEditing.cancelEdit();
                                        Ext.MessageBox.confirm('Message', lang('Apakah anda mau menghapus data ini ?'), function(btn) {
                                            if (btn == 'yes') {
                                                Ext.Ajax.request({
                                                    waitMsg: lang('Please Wait'),
                                                    url: m_crud + '_nursery_transaction',
                                                    method: 'DELETE',
                                                    params: {
                                                        id: smb.raw.NurseryTransactionID
                                                    },
                                                    success: function(response, opts) {
                                                        var obj = Ext.decode(response.responseText);
                                                        switch (obj.success) {
                                                            case true:
                                                                store_nursey_trans.load({
                                                                    params: {
                                                                        id: Ext.getCmp('NurseryID_idcoop').getValue()
                                                                    }});
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
                            text: lang('NurseryTransactionID'),
                            dataIndex: 'NurseryTransactionID',
                            hidden: true
                        }, {
                            text: lang('NurseryID'),
                            dataIndex: 'NurseryID',
                            hidden: true
                        }, {
                            text: lang('No'),
                            xtype: 'rownumberer',
                            width: '5%'
                        }, {
                            text: lang('Pembeli'),
                            dataIndex: 'Buyer',
                            width: '20%',
                            editor: {
                                xtype: 'combo',
                                store: mc_pembeli,
                                displayField: 'label',
                                valueField: 'label',
                                queryMode: 'local',
                                allowBlank: false
                            }
                        }, {
                            text: lang('Bibit Dijual'),
                            dataIndex: 'Volume',
                            width: '15%',
                            editor: {
                                xtype: 'textfield',
                                id: 'nvol_idcoop',
                                allowBlank: false,
                                listeners: {
                                    change: function() {
                                        Ext.getCmp('ntot_idcoop').setValue(Ext.getCmp('nvol_idcoop').getValue() * Ext.getCmp('npri_idcoop').getValue());
                                    }
                                }
                            }
                        }, {
                            text: lang('Harga Satuan'),
                            dataIndex: 'Price',
                            width: '15%',
                            editor: {
                                xtype: 'textfield',
                                id: 'npri_idcoop',
                                allowBlank: false,
                                listeners: {
                                    change: function() {
                                        Ext.getCmp('ntot_idcoop').setValue(Ext.getCmp('nvol_idcoop').getValue() * Ext.getCmp('npri_idcoop').getValue());
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
                                id: 'ntot_idcoop',
                                readOnly: true
                            }
                        }, {
                            text: lang('Tanggal Transaksi'),
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
                            'canceledit': function(editor, e, eOpts) {
                                store_nursey_trans.load({
                                    params: {
                                        id: Ext.getCmp('NurseryID_idcoop').getValue()
                                    }
                                });
                            },
                            'edit': function(editor, e) {
                                if (e.record.data.NurseryTransactionID == '') {
                                    Ext.Ajax.request({
                                        waitMsg: lang('Please wait...'),
                                        url: m_crud + '_nursery_transaction',
                                        method: 'POST',
                                        params: {
                                            id_nursey: Ext.getCmp('NurseryID_idcoop').getValue(),
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
                                                    store_nursey_trans.load({
                                                        params: {
                                                            id: Ext.getCmp('NurseryID_idcoop').getValue()
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
                                                url: m_crud + '_nursery_transaction',
                                                method: 'PUT',
                                                params: {
                                                    id: e.record.data.NurseryTransactionID,
                                                    id_nursey: Ext.getCmp('NurseryID_idcoop').getValue(),
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
                                                            store_nursey_trans.load({
                                                                params: {
                                                                    id: Ext.getCmp('NurseryID_idcoop').getValue()
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
                },{ // tab nursery monitoring
                    xtype: 'gridpanel',
                    title: lang('Nursery Monitoring'),
                    id:'gnurseymonitoring_idcoop',
                    style: 'border:1px solid #CCC;',
                    store: store_nursey_monitoring,
                    width: '100%',
                    loadMask: true,
                    selType: 'rowmodel',
                    minHeight:190,
                    dockedItems: [{
                        xtype: 'toolbar',
                        items: [{
                            icon: varjs.config.base_url + 'images/icons/new/add.png', cls:'Sfr_BtnGridGreen', overCls:'Sfr_BtnGridGreen-Hover',
                            cls:m_act_save,
                            text: lang('Add'),
                            scope: this,
                            handler : function(){
                                if(Ext.getCmp('NurseryID_idcoop').getValue()==''){
                                    Ext.MessageBox.alert('Warning', 'Please save Nursery first!');
                                }else{
                                    mRowEditing.cancelEdit();
                                    var r = Ext.create('monitoring.Model',{
                                        id:'',
                                        MonitoringDate:'',
                                        MonitoringStatus:'',
                                        Description:''
                                    });
                                    store_nursey_monitoring.insert(0,r);
                                    mRowEditing.startEdit(0,0);
                                }
                            }
                        },{
                            icon: varjs.config.base_url+'images/icons/silk/pencil.png',
                            cls:m_act_save,
                            text: lang('Update'),
                            scope: this,
                            handler : function(){
                                mRowEditing.cancelEdit();
                                var sm = Ext.getCmp('gnurseymonitoring_idcoop').getSelectionModel().getSelection();
                                mRowEditing.startEdit(sm[0].index, 0);
                                act_nursery_status(Ext.getCmp('mStatus_idcoop').getValue());
                            }
                        },{
                            itemId: 'remove',
                            icon: varjs.config.base_url+'images/icons/silk/delete.png',
                            cls:m_act_save,
                            text: lang('Delete'),
                            scope: this,
                            handler : function(){
                                var smb = Ext.getCmp('gnurseymonitoring_idcoop').getSelectionModel().getSelection()[0];
                                mRowEditing.cancelEdit();
                                Ext.MessageBox.confirm('Message', lang('Apakah anda mau menghapus data ini ?') , function(btn){
                                    if(btn == 'yes'){
                                        Ext.Ajax.request({
                                            waitMsg: lang('Please Wait'),
                                            url: m_nursey+'_monitorings',
                                            method : 'DELETE',
                                            params: {
                                                id:  smb.raw.id
                                            },
                                            success: function(response, opts){
                                                var obj = Ext.decode(response.responseText);
                                                switch(obj.success){
                                                    case true:
                                                        store_nursey_monitoring.load({
                                                            params: {
                                                                nursery_id: Ext.getCmp('NurseryID_idcoop').getValue()
                                                            }
                                                        });
                                                        break;
                                                    default:
                                                        Ext.MessageBox.alert('Warning',obj.message);
                                                        break;
                                                }
                                            },
                                            failure: function(response, opts){
                                                var obj = Ext.decode(response.responseText);
                                                Ext.MessageBox.alert('error',lang('Could not connect to the database. Retry later'));
                                            }
                                        });
                                    }
                                });
                            }
                        }]
                    }],
                    columns:[{
                        text: lang('ID'),
                        dataIndex: 'id',
                        hidden:true
                    },{
                        text: lang('No'),
                        xtype: 'rownumberer',
                        width:'5%'
                    },{
                        text: lang('Tanggal Kedatangan'),
                        dataIndex: 'MonitoringDate',
                        width:'15%',
                        editor: {
                            xtype:'datefield',
                            id:'mDate',
                            format:'Y-m-d',
                            allowBlank:false
                        }
                    },{
                        text: lang('Status'),
                        dataIndex: 'MonitoringStatus',
                        width:'20%',
                        editor: {
                            xtype: 'combo',
                            id: 'mStatus_idcoop',
                            store: mc_status_monitoring,
                            displayField:'label',
                            valueField: 'label',
                            queryMode: 'local',
                            allowBlank: false,
                            listeners: {
                                change: function(combo, selection) {
                                    Ext.getCmp('mDescription_idcoop').setValue('');
                                    act_nursery_status(Ext.getCmp('mStatus_idcoop').getValue());
                                }
                            }
                        }
                    },{
                        text:lang('Keterangan'),
                        dataIndex:'Description',
                        width:'59%',
                        editor:{
                            xtype:'combo',
                            id:'mDescription_idcoop',
                            allowBlank:true,
                            store:[''],
                            hideTrigger:false,
                            listeners:{
                                beforequery:function(record){
                                    record.query=new RegExp(record.query,'i');
                                    record.forceAll=true;
                                }
                            }
                        }
                    }],
                    plugins:[mRowEditing],
                    listeners:{
                        'canceledit':function(editor,e,eOpts){
                            store_nursey_monitoring.load({
                                params: {
                                    nursery_id: Ext.getCmp('NurseryID_idcoop').getValue()
                                }
                            });
                        },
                        'edit':function(editor,e){
                            if(Ext.getCmp('NurseryID_idcoop').getValue() == '' || Ext.getCmp('NurseryID_idcoop').getValue() == undefined){
                                Ext.Msg.alert("Alert", 'Belum ada data nursery');
                            }else{
                                if(e.record.data.id==''){
                                    Ext.Ajax.request({
                                        waitMsg: lang('Please wait...'),
                                        url: m_nursey+'_monitorings',
                                        method : 'POST',
                                        params: {
                                            id_nursey : Ext.getCmp('NurseryID_idcoop').getValue(),
                                            MonitoringDate : e.record.data.MonitoringDate,
                                            MonitoringStatus : e.record.data.MonitoringStatus,
                                            Description : e.record.data.Description
                                        },
                                        success: function(response, opts){
                                            var obj = Ext.decode(response.responseText);
                                            switch(obj.success){
                                                case true:
                                                    Ext.MessageBox.alert('Success',obj.message);
                                                    store_nursey_monitoring.load({
                                                        params: {
                                                            nursery_id: Ext.getCmp('NurseryID_idcoop').getValue()
                                                        }
                                                    });
                                                    break;
                                                default:
                                                    Ext.MessageBox.alert('Warning',obj.message);
                                                    break;
                                            }
                                        },
                                        failure: function(response, opts){
                                            var obj = Ext.decode(response.responseText);
                                            Ext.MessageBox.alert('error',lang('Could not connect to the database. Retry later'));
                                        }
                                    });
                                } else {
                                    Ext.MessageBox.confirm('Message', lang('Update data ini ?') , function(btn){
                                        if(btn == 'yes') {
                                            Ext.Ajax.request({
                                                waitMsg: lang('Please wait...'),
                                                url: m_nursey+'_monitorings',
                                                method : 'PUT',
                                                params: {
                                                    id : e.record.data.id,
                                                    id_nursey : Ext.getCmp('NurseryID_idcoop').getValue(),
                                                    MonitoringDate : e.record.data.MonitoringDate,
                                                    MonitoringStatus : e.record.data.MonitoringStatus,
                                                    Description : e.record.data.Description
                                                },
                                                success: function(response, opts){
                                                    var obj = Ext.decode(response.responseText);
                                                    switch(obj.success){
                                                        case true:
                                                            Ext.MessageBox.alert('Success',obj.message);
                                                            store_nursey_monitoring.load({
                                                                params: {
                                                                    nursery_id: Ext.getCmp('NurseryID_idcoop').getValue()
                                                                }
                                                            });
                                                            break;
                                                        default:
                                                            Ext.MessageBox.alert('Warning',obj.message);
                                                            break;
                                                    }
                                                },
                                                failure: function(response, opts){
                                                    var obj = Ext.decode(response.responseText);
                                                    Ext.MessageBox.alert('error',lang('Could not connect to the database. Retry later'));
                                                }
                                            });
                                        }
                                    });
                                }
                            }
                        },
                        itemdblclick: function() {
                            act_nursery_status(Ext.getCmp('mStatus_idcoop').getValue());
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
                id: 'nsaveButton_idcoop',
                text: lang('Save'),
                margin: '5px',
                scale: 'large',
                ui: 's-button',
                cls: 's-blue ' + m_act_save,
                handler: function() {
                    var form = Ext.getCmp('DataFormNurseyCoop').getForm();
                    var methode;
                    if (Ext.getCmp('NurseryID_idcoop').getValue() != '')
                        methode = 'POST';
                    else
                        methode = 'POST';

                    Ext.getCmp('Luas_idcoop').setValue(nnumber_format(Ext.getCmp('Luas_idcoop').getValue(), 2))
                    Ext.getCmp('Kapasitas_idcoop').setValue(nnumber_format(Ext.getCmp('Kapasitas_idcoop').getValue(), 2))
                    form.submit({
//                        url: m_crud,
                        url: m_crud + '_nursery',
                        method: methode,
                        waitMsg: lang('Sending data...'),
                        success: function(fp, o) {
                            Ext.MessageBox.alert('Success', 'Data saved.');
                            Ext.getCmp('Luas_idcoop').setValue(nnumber_format(Ext.getCmp('Luas_idcoop').getValue()));
                            Ext.getCmp('NurseryID_idcoop').setValue(o.result.id);
                            var r = Ext.decode(o.response.responseText);
                            Ext.getCmp('NurseryID_idcoop').setValue(r.id);
                            Ext.getCmp('NurseryNr_idcoop').setReadOnly(true);
                            store_nursery_list.load({
                                params: {
                                    ObjType: 'koperasi',
                                    ObjID: Ext.getCmp('CoopID').getValue()
                                }
                            });
                        },
                        failure: function(fp, o) {
                            if(o.response.responseText == undefined){
                                var errText = "Form is not complete yet";
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
                text: lang('Close'),
                margin: '5px',
                scale: 'large',
                ui: 's-button',
                cls: 's-grey',
                disabled: false,
                handler: function() {
                    winNurseyCoop.hide();
                }
            }]
    });

    var winNurseyCoop = Ext.create('widget.window', {
        title: lang('Nursery Unit'),
        id: 'winNurseyCoop',
        closable: true,
        modal: true,
        closeAction: 'show',
        width: '90%',
        height: '90%',
        layout: 'fit',
        items: [DataFormNurseyCoop]
    });
    // ICS
    var iRowEditing = Ext.create('Ext.grid.plugin.RowEditing', {
        id: 'iRowEditing',
        clicksToMoveEditor: 0,
        autoCancel: false,
        errorSummary: false,
        clicksToEdit: 2
    });

    function displayFormIcs() {
        if (!winIcs.isVisible()) {
            DataFormIcs.getForm().reset();
            winIcs.center();
            winIcs.show();
        } else {
            winIcs.hide(this, function() {});
            winIcs.toFront();
        }
    }
    var DataFormIcs = Ext.create('Ext.form.Panel', {
        autoScroll: true,
        bodyPadding: 5,
        id: 'dataFormIcs',
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
                    id: 'IcsID',
                    name: 'IcsID',
                    hidden: true
                }, {
                    xtype: 'textfield',
                    id: 'IcsObjID',
                    name: 'IcsObjID',
                    hidden: true
                }, {
                    xtype: 'textfield',
                    id: 'IcsDistrict',
                    name: 'IcsDistrict',
                    hidden: true
                }, {
                    xtype: 'textfield',
                    fieldLabel: lang('Nama'),
                    id: 'iCoopName',
                    name: 'iCoopName',
                    readOnly: true
                }]
            }, {
                columnWidth: .5,
                layout: 'form',
                border: false,
                padding: 5,
                items: [{
                    xtype: 'textfield',
                    fieldLabel: lang('ICS Type'),
                    id: 'icsType',
                    name: 'icsType',
                    readOnly: true
                }]
            }]
        }, {
            xtype: 'gridpanel',
            id: 'gicsmember',
            store: store_ics_members,
            width: '100%',
            minHeight: 350,
            title: 'ICS Members',
            loadMask: true,
            selType: 'rowmodel',
            dockedItems: [{
                xtype: 'pagingtoolbar',
                store: store_ics_members,
                dock: 'bottom',
                displayInfo: true
            }, {
                xtype: 'toolbar',
                items: [{
                    icon: varjs.config.base_url + 'images/icons/new/add.png', cls:'Sfr_BtnGridGreen', overCls:'Sfr_BtnGridGreen-Hover',
                    text: lang('Add'),
                    hidden: !m_act_add,
                    id: 'btn_add_ics',
                    hidden:true,
                    scope: this,
                    handler: function() { // 'IcsMemberID','FarmerID','FarmerName','Gender','SubDistrict','District']
                        iRowEditing.cancelEdit();
                        var r = Ext.create('ics.Model', {
                            IcsMemberID: '',
                            FarmerID: '',
                            FarmerName: '',
                            Gender: '',
                            SubDistrict: '',
                            District: ''
                        });
                        store_ics_members.insert(0, r);
                        iRowEditing.startEdit(0, 0);
                    },
                    cls: m_act_add
                }, {
                    itemId: 'remove',
                    icon: varjs.config.base_url + 'images/icons/new/delete.png',
                    cls: m_act_delete,
                    hidden: !m_act_delete,
                    text: lang('Hapus'),
                    id: 'btn_delete_ics',
                    hidden:true,
                    scope: this,
                    handler: function() {
                        var smb = Ext.getCmp('gicsmember').getSelectionModel().getSelection()[0];
                        iRowEditing.cancelEdit();
                        Ext.MessageBox.confirm('Message', lang('Apakah anda mau menghapus member ICS ini ?'), function(btn) {
                            if (btn == 'yes') {
                                Ext.Ajax.request({
                                    waitMsg: lang('Please Wait'),
                                    url: m_ics_member,
                                    method: 'DELETE',
                                    params: {
                                        id: smb.raw.IcsMemberID
                                    },
                                    success: function(response, opts) {
                                        var obj = Ext.decode(response.responseText);
                                        switch (obj.success) {
                                            case true:
                                                store_ics_members.load({
                                                    params: {
                                                        icsID: Ext.getCmp('IcsID').getValue()
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
            }], // fields: ['IcsMemberID','FarmerID','FarmerName','gender','SubDistrict','District'],
            columns: [{
                text: lang('No'),
                xtype: 'rownumberer',
                width: '5%'
            }, {
                text: lang('ID'),
                dataIndex: 'IcsMemberID',
                hidden: true
            }, {
                text: lang('Name'),
                dataIndex: 'FarmerName',
                width: '25%',
                id: 'icsLn',
                editor: {
                    xtype: 'combo',
                    store: dsIcs,
                    displayfield: 'name',
                    id: 'icsEname',
                    typeAhead: false,
                    hideLabel: true,
                    hideTrigger: true,
                    anchor: '100%',
                    listConfig: {
                        loadingText: 'Searching...',
                        emptyText: 'No matching farmer found',
                        getInnerTpl: function() {
                            return '<div class="search-item">' + '{id} - {name}' + '{excerpt}' + '</div>';
                        }
                    },
                    pageSize: 10,
                    listeners: {
                        select: function(combo, selection) {
                            var post = selection[0];
                            if (post) {
                                Ext.getCmp('icsEname').setValue('[' + post.get('id') + '] ' + post.get('name'))
                                Ext.getCmp('icsEgender').setValue(post.get('gender'))
                                Ext.getCmp('icsEgender').setValue(post.get('gender'))
                                Ext.getCmp('icsEgender').setReadOnly(true)
                                Ext.getCmp('icsEsubdistrict').setValue(post.get('subdistrict'))
                                Ext.getCmp('icsEsubdistrict').setReadOnly(true)
                                Ext.getCmp('icsEdistrict').setValue(post.get('district'))
                                Ext.getCmp('icsEdistrict').setReadOnly(true)
                                Ext.getCmp('tmpEid').setValue(post.get('id'));
                            }
                        }
                    }
                }
            }, {
                text: lang('Gender'),
                dataIndex: 'Gender',
                width: '15%',
                editor: {
                    xtype: 'textfield',
                    id: 'icsEgender'
                }
            }, {
                text: lang('Sub District'),
                dataIndex: 'SubDistrict',
                width: '15%',
                editor: {
                    xtype: 'textfield',
                    id: 'icsEsubdistrict'
                }
            }, {
                text: lang('District'),
                dataIndex: 'District',
                width: '15%',
                editor: {
                    xtype: 'textfield',
                    id: 'icsEdistrict'
                }
            }, {
                text: lang('Farmer ID'),
                dataIndex: 'FarmerID',
                id: 'tmpId',
                hidden: true,
                editor: {
                    xtype: 'textfield',
                    id: 'tmpEid',
                    name: 'tmpEid'
                }
            }],
            //plugins: [iRowEditing],
            listeners: {
                itemdblclick: function(dv, record, item, index, e) {
                    if (!m_act_update) {
                        iRowEditing.cancelEdit();
                    }
                },
                'canceledit': function() {
                    store_ics_members.load({
                        params: {
                            icsID: Ext.getCmp('IcsID').getValue()
                        }
                    });
                },
                'edit': function(editor, e) {
                    Ext.Ajax.request({
                        waitMsg: lang('Please wait...'),
                        url: m_ics_member,
                        method: 'POST',
                        params: {
                            farmerID: Ext.getCmp('tmpEid').getValue(),
                            icsID: Ext.getCmp('IcsID').getValue()
                        },
                        success: function(response, opts) {
                            var obj = Ext.decode(response.responseText);
                            switch (obj.success) {
                                case true:
                                    Ext.MessageBox.alert('Success', obj.message);
                                    // store load
                                    break;
                                default:
                                    Ext.MessageBox.alert('Warning', obj.message);
                                    break;
                            }
                            store_ics_members.load({
                                params: {
                                    icsID: Ext.getCmp('IcsID').getValue()
                                }
                            });
                        },
                        failure: function(response, opts) {
                            var obj = Ext.decode(response.responseText);
                            Ext.MessageBox.alert('error', lang('Could not connect to the database. Retry later'));
                        }
                    });
                }
            }
        }],
        buttons: [{
            id: 'btnCreateIcs',
            text: lang('Create'),
            margin: '5px',
            scale: 'large',
            ui: 's-button',
            cls: 's-blue ' + m_act_save,
            handler: function() {
                var form = this.up('form').getForm();
                form.submit({
                    url: m_ics_group,
                    method: 'POST',
                    waitMsg: lang('Sending data...'),
                    success: function(fp, o) {
                        Ext.MessageBox.alert('Success', lang('Data saved.'));
                        Ext.getCmp('IcsID').setValue(o.result.id);
                        Ext.getCmp('btnCreateIcs').hide();
                        Ext.getCmp('iCoopName').setValue(Ext.getCmp('CoopName').getValue());
                        Ext.getCmp('icsType').setValue('Organisasi Petani');
                        Ext.getCmp('btn_add_ics').setDisabled(false);
                        Ext.getCmp('btn_delete_ics').setDisabled(false);
                        //dsIcs.getProxy().setExtraParam("district", r.District);
                        dsIcs.getProxy().setExtraParam("province", Ext.getCmp('Provinsi').getValue());
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
                winIcs.hide();
            }
        }]
    });
    var winIcs = Ext.create('widget.window', {
        title: lang('Internal Monitoring System'),
        id: 'winIcs',
        closable: true,
        modal: true,
        closeAction: 'hide',
        width: '90%',
        height: 590,
        layout: {
            type: 'fit'
        },
        items: [DataFormIcs]
    });
    //**//
    function displayFormClonalGardenCoop() {
        if (!winClonalGardenCoop.isVisible()) {
            DataFormClonalGardenCoop.getForm().reset();
            Ext.getCmp('ObjID_idcoop').setValue(Ext.getCmp('CoopID').getValue());
            winClonalGardenCoop.center();
            winClonalGardenCoop.show();
        } else {
            winClonalGardenCoop.hide(this, function() {});
            winClonalGardenCoop.toFront();
        }
    }
    var areawindow_idcoop = Ext.create('widget.window', {
        id: 'areawindow_idcoop',
        title: lang('Clonal Garden Polygon'),
        closable: true,
        modal: true,
        layout: 'fit',
        closeAction: 'show',
        width: '75%',
        height: 600,
        bodyPadding: 5,
        listeners: {
            close: function(cb, nv, ov) {
                hitung_area_coop();
            }
        },
        buttons: [/*{
            id: 'polygonsaveButton',
            text: lang('Save'),
            margin: '5px',
            scale: 'large',
            ui: 's-button',
            cls: 's-blue ' + m_act_save,
            handler: function() {

            }
        },*/ {
            text: lang('Close'),
            margin: '5px',
            scale: 'large',
            ui: 's-button',
            cls: 's-grey',
            disabled: false,
            handler: function() {
                areawindow_idcoop.hide();
                hitung_area_coop();
            }
        }]
    });
    var DataFormClonalGardenCoop = Ext.create('Ext.form.Panel', {
        frame: true,
        autoScroll: true,
        minHeight: 500,
        width: '100%',
        bodyPadding: 5,
        id: 'dataFormClonalGarden_idcoop',
        fieldDefaults: {
            labelAlign: 'left',
            labelWidth: 180,
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
                            id: 'ClonalID_idcoop',
                            name: 'ClonalID',
                            hidden: true
                        }, {
                            xtype: 'textfield',
                            id: 'ObjType_idcoop',
                            name: 'ObjType',
                            value: 'koperasi',
                            hidden: true
                        }, {
                            xtype: 'textfield',
                            id: 'ObjID_idcoop',
                            name: 'ObjID',
                            hidden: true
                        }, {
                            xtype: 'numberfield',
                            fieldLabel: lang('GardenNr Default'),
                            id: 'GardenNr_default_idcoop',
                            name: 'GardenNr_default',
                            hidden: true
                        }, {
                            xtype: 'numberfield',
                            fieldLabel: lang('GardenNr'),
                            id: 'GardenNr_idcoop',
                            name: 'GardenNr',
                            allowBlank: false,
                            minValue: 1
                        }, {
                            xtype: 'textfield',
                            fieldLabel: lang('Year Established'),
                            id: 'EstablishedYear_idcoop',
                            name: 'EstablishedYear'
                        }, {
                            xtype: 'radiogroup',
                            id: 'LandCertificate_idcoop',
                            columns: 1,
                            fieldLabel: lang('Land Ownership'),
                            items: [{
                                name: 'LandCertificate',
                                id: 'LandCertificate1_idcoop',
                                boxLabel: lang('None'),
                                inputValue: '1'
                            }, {
                                name: 'LandCertificate',
                                id: 'LandCertificate2_idcoop',
                                boxLabel: lang('Notary Deed/BPN'),
                                inputValue: '2'
                            }, {
                                name: 'LandCertificate',
                                id: 'LandCertificate3_idcoop',
                                boxLabel: lang('Sub District'),
                                inputValue: '3'
                            }, {
                                name: 'LandCertificate',
                                id: 'LandCertificate4_idcoop',
                                boxLabel: lang('Village/ward'),
                                inputValue: '4'
                            }, {
                                name: 'LandCertificate',
                                id: 'LandCertificate5_idcoop',
                                boxLabel: lang('Do not know'),
                                inputValue: '5'
                            }],
                            listeners: {}
                        }, {
                            xtype: 'radiogroup',
                            fieldLabel: lang('Clonal Garden Certification Status'),
                            items: [{
                                name: 'CertificationStatus',
                                id: 'CertificationStatus1_idcoop',
                                boxLabel: lang('Yes, BP2MB'),
                                inputValue: 'Yes'
                            }, {
                                name: 'CertificationStatus',
                                id: 'CertificationStatus2_idcoop',
                                boxLabel: lang('Tidak'),
                                inputValue: 'No',
                                checked: true,
                            }],
                            listeners: {
                                change: function(cb, nv, ov) {
                                    if (Ext.getCmp('CertificationStatus1_idcoop').getValue() == true) {
                                        //Ext.getCmp('LandCertificate').setDisabled(false);
                                        /*if(Ext.getCmp('CertificateProvider3').getValue() == true){
                                            Ext.getCmp('CertificateProviderOther').setDisabled(false);
                                        }else{
                                            Ext.getCmp('CertificateProviderOther').setDisabled(true);
                                        }*/
                                        Ext.getCmp('DateAppliedCertification_idcoop').setDisabled(false);
                                        Ext.getCmp('DateReceivedCertification_idcoop').setDisabled(false);
                                    } else {
                                        //Ext.getCmp('LandCertificate').setDisabled(true);
                                        Ext.getCmp('DateAppliedCertification_idcoop').setDisabled(true);
                                        Ext.getCmp('DateReceivedCertification_idcoop').setDisabled(true);
                                        //Ext.getCmp('CertificateProviderOther').setDisabled(true);
                                    }
                                }
                            }
                        }, {
                            xtype: 'datefield',
                            disabled: true,
                            fieldLabel: lang('Date Applied for Certification'),
                            id: 'DateAppliedCertification_idcoop',
                            name: 'DateAppliedCertification',
                            format: 'Y-m-d'
                        }, {
                            xtype: 'datefield',
                            disabled: true,
                            fieldLabel: lang('Date Received for Certification'),
                            id: 'DateReceivedCertification_idcoop',
                            name: 'DateReceivedCertification',
                            format: 'Y-m-d'
                        },
                        /*{
                                        xtype: 'radiogroup',
                                        disabled: true,
                                        id: 'CertificateProvider',
                                        fieldLabel: lang('Certificate Provider'),
                                        items: [{
                                            name: 'CertificateProvider',
                                            id: 'CertificateProvider1',
                                            boxLabel: lang('Goverment'),
                                            inputValue: '1'
                                        }, {
                                            name: 'CertificateProvider',
                                            id: 'CertificateProvider2',
                                            boxLabel: lang('ICCRI'),
                                            inputValue: '2'
                                        }, {
                                            name: 'CertificateProvider',
                                            id: 'CertificateProvider3',
                                            boxLabel: lang('Others'),
                                            inputValue: '3'
                                        }],
                                        listeners: {
                                            change: function(cb, nv, ov) {
                                                if(Ext.getCmp('CertificateProvider3').getValue() == true){
                                                    Ext.getCmp('CertificateProviderOther').setDisabled(false);
                                                }else{
                                                    Ext.getCmp('CertificateProviderOther').setDisabled(true);
                                                }
                                            }
                                        }
                                    }, {
                                        xtype: 'textfield',
                                        disabled: true,
                                        fieldLabel: lang('Other Certificate Provider'),
                                        id: 'CertificateProviderOther',
                                        name: 'CertificateProviderOther'
                                    }, */
                    ]
                }, {
                    columnWidth: .5,
                    layout: 'form',
                    border: false,
                    padding: 5,
                    items: [{
                        xtype: 'textfield',
                        fieldLabel: lang('Area (Ha)'),
                        id: 'Area_idcoop',
                        name: 'Area',
                        labelWidth: 180,
                        maskRe: /[0-9.]/,
                        readOnly: true
                    }, {
                        xtype: 'textfield',
                        fieldLabel: lang('Latitude (Dec)'),
                        id: 'ClonalGardenLatitude_idcoop',
                        name: 'Latitude',
                        readOnly: m_hakakses_lat_short
                    }, {
                        xtype: 'textfield',
                        fieldLabel: lang('Longitude (Dec)'),
                        id: 'ClonalGardenLongitude_idcoop',
                        name: 'Longitude',
                        readOnly: m_hakakses_long_short
                    }, {
                        items: [{
                            layout: 'column',
                            labelWidth: 500,
                            items: [{
                                html: lang('Map Area'),
                                //hidden: true
                            }, {
                                items: [{
                                    xtype: 'button',
                                    margin: '0 0 0 128',
                                    id: 'buttonShowPolygon_idcoop',
                                    text: lang('Show Polygon'),
                                    handler: function() {
                                        if (Ext.getCmp('ClonalID_idcoop').getValue() == '') {
                                            Ext.MessageBox.alert('Warning', 'Please save clonal garden first!');
                                        } else {
                                            display_area(Ext.getCmp('GardenNr_default_idcoop').getValue());
                                        }
                                    },
                                    //hidden: true
                                }]
                            }]
                        }]
                    }]
                }]
            }, {
                html: '<b> &nbsp; Cocoa Clone</b>',
            }, {
                xtype: 'fieldset',
                margin: '0 0 0 0',
                padding: '0 0 0 1',
                border: false,
                items: [{
                    layout: 'column',
                    border: false,
                    items: [{
                        columnWidth: 0.19,
                        xtype: 'label',
                        padding: 5,
                        cls: 'x-form-item-label',
                        text: lang('Cocoa Clone Type and Total (Multiple Choice)')
                    }, {
                        columnWidth: 0.81,
                        border: false,
                        items: [{
                            xtype: 'fieldset',
                            title: lang('Certified'),
                            items: [{
                                layout: 'column',
                                border: false,
                                items: [{
                                    columnWidth: 0.16,
                                    layout: 'form',
                                    border: false,
                                    defaultType: 'checkboxfield',
                                    items: [{
                                        boxLabel: lang('S1'),
                                        name: 'S1',
                                        inputValue: '1',
                                        id: 'S1_idcoop',
                                        listeners: {
                                            change: function() {
                                                CheckClonalGardenCoop(this)
                                            }
                                        }
                                    }, {
                                        boxLabel: lang('S2'),
                                        name: 'S2',
                                        inputValue: '1',
                                        id: 'S2_idcoop',
                                        listeners: {
                                            change: function() {
                                                CheckClonalGardenCoop(this)
                                            }
                                        }
                                    }, {
                                        boxLabel: lang('45/MCC02'),
                                        name: 'CG45',
                                        inputValue: '1',
                                        id: 'CG45_idcoop',
                                        listeners: {
                                            change: function() {
                                                CheckClonalGardenCoop(this)
                                            }
                                        }
                                    }, {
                                        boxLabel: lang('M01/MCC01'),
                                        name: 'M01',
                                        inputValue: '1',
                                        id: 'M01_idcoop',
                                        listeners: {
                                            change: function() {
                                                CheckClonalGardenCoop(this)
                                            }
                                        }
                                    }]
                                }, {
                                    columnWidth: 0.16,
                                    layout: 'form',
                                    defaultType: 'textfield',
                                    border: false,
                                    padding: '0 20px 0 0',
                                    items: [{
                                        id: 'S1Nr_idcoop',
                                        name: 'S1Nr',
                                        disabled: true,
                                        maskRe: /[0-9.]/,
                                        listeners: {
                                            change: function() {
                                                JumlahClonalGardenCoop()
                                            }
                                        }
                                    }, {
                                        id: 'S2Nr_idcoop',
                                        name: 'S2Nr',
                                        disabled: true,
                                        maskRe: /[0-9.]/,
                                        listeners: {
                                            change: function() {
                                                JumlahClonalGardenCoop()
                                            }
                                        }
                                    }, {
                                        id: 'CG45Nr_idcoop',
                                        name: 'CG45Nr',
                                        disabled: true,
                                        maskRe: /[0-9.]/,
                                        listeners: {
                                            change: function() {
                                                JumlahClonalGardenCoop()
                                            }
                                        }
                                    }, {
                                        id: 'M01Nr_idcoop',
                                        name: 'M01Nr',
                                        disabled: true,
                                        maskRe: /[0-9.]/,
                                        listeners: {
                                            change: function() {
                                                JumlahClonalGardenCoop()
                                            }
                                        }
                                    }]
                                }, {
                                    columnWidth: 0.16,
                                    layout: 'form',
                                    border: false,
                                    defaultType: 'checkboxfield',
                                    items: [{
                                        boxLabel: lang('TSH 858'),
                                        name: 'TSH858',
                                        inputValue: '1',
                                        id: 'TSH858_idcoop',
                                        listeners: {
                                            change: function() {
                                                CheckClonalGardenCoop(this)
                                            }
                                        }
                                    }, {
                                        boxLabel: lang('ICCRI3'),
                                        name: 'ICCRI3',
                                        inputValue: '1',
                                        id: 'ICCRI3_idcoop',
                                        listeners: {
                                            change: function() {
                                                CheckClonalGardenCoop(this)
                                            }
                                        }
                                    }, {
                                        boxLabel: lang('ICCRI4'),
                                        name: 'ICCRI4',
                                        inputValue: '1',
                                        id: 'ICCRI4_idcoop',
                                        listeners: {
                                            change: function() {
                                                CheckClonalGardenCoop(this)
                                            }
                                        }
                                    }, {
                                        boxLabel: lang('ICCRI5'),
                                        name: 'ICCRI5',
                                        inputValue: '1',
                                        id: 'ICCRI5_idcoop',
                                        listeners: {
                                            change: function() {
                                                CheckClonalGardenCoop(this)
                                            }
                                        }
                                    }]
                                }, {
                                    columnWidth: 0.16,
                                    layout: 'form',
                                    defaultType: 'textfield',
                                    border: false,
                                    padding: '0 20px 0 0',
                                    items: [{
                                        id: 'TSH858Nr_idcoop',
                                        name: 'TSH858Nr',
                                        disabled: true,
                                        maskRe: /[0-9.]/,
                                        listeners: {
                                            change: function() {
                                                JumlahClonalGardenCoop()
                                            }
                                        }
                                    }, {
                                        id: 'ICCRI3Nr_idcoop',
                                        name: 'ICCRI3Nr',
                                        disabled: true,
                                        maskRe: /[0-9.]/,
                                        listeners: {
                                            change: function() {
                                                JumlahClonalGardenCoop()
                                            }
                                        }
                                    }, {
                                        id: 'ICCRI4Nr_idcoop',
                                        name: 'ICCRI4Nr',
                                        disabled: true,
                                        maskRe: /[0-9.]/,
                                        listeners: {
                                            change: function() {
                                                JumlahClonalGardenCoop()
                                            }
                                        }
                                    }, {
                                        id: 'ICCRI5Nr_idcoop',
                                        name: 'ICCRI5Nr',
                                        disabled: true,
                                        maskRe: /[0-9.]/,
                                        listeners: {
                                            change: function() {
                                                JumlahClonalGardenCoop()
                                            }
                                        }
                                    }]
                                }, {
                                    columnWidth: 0.16,
                                    layout: 'form',
                                    border: false,
                                    defaultType: 'checkboxfield',
                                    items: [{
                                        boxLabel: lang('RCC70'),
                                        name: 'RCC70',
                                        inputValue: '1',
                                        id: 'RCC70_idcoop',
                                        listeners: {
                                            change: function() {
                                                CheckClonalGardenCoop(this)
                                            }
                                        }
                                    }, {
                                        boxLabel: lang('RCC71'),
                                        name: 'RCC71',
                                        inputValue: '1',
                                        id: 'RCC71_idcoop',
                                        listeners: {
                                            change: function() {
                                                CheckClonalGardenCoop(this)
                                            }
                                        }
                                    }, {
                                        boxLabel: lang('RCC72'),
                                        name: 'RCC72',
                                        inputValue: '1',
                                        id: 'RCC72_idcoop',
                                        listeners: {
                                            change: function() {
                                                CheckClonalGardenCoop(this)
                                            }
                                        }
                                    }, {
                                        boxLabel: lang('RCC73'),
                                        name: 'RCC73',
                                        inputValue: '1',
                                        id: 'RCC73_idcoop',
                                        listeners: {
                                            change: function() {
                                                CheckClonalGardenCoop(this)
                                            }
                                        }
                                    }]
                                }, {
                                    columnWidth: 0.16,
                                    layout: 'form',
                                    defaultType: 'textfield',
                                    border: false,
                                    padding: '0 20px 0 0',
                                    items: [{
                                        id: 'RCC70Nr_idcoop',
                                        name: 'RCC70Nr',
                                        disabled: true,
                                        maskRe: /[0-9.]/,
                                        listeners: {
                                            change: function() {
                                                JumlahClonalGardenCoop()
                                            }
                                        }
                                    }, {
                                        id: 'RCC71Nr_idcoop',
                                        name: 'RCC71Nr',
                                        disabled: true,
                                        maskRe: /[0-9.]/,
                                        listeners: {
                                            change: function() {
                                                JumlahClonalGardenCoop()
                                            }
                                        }
                                    }, {
                                        id: 'RCC72Nr_idcoop',
                                        name: 'RCC72Nr',
                                        disabled: true,
                                        maskRe: /[0-9.]/,
                                        listeners: {
                                            change: function() {
                                                JumlahClonalGardenCoop()
                                            }
                                        }
                                    }, {
                                        id: 'RCC73Nr_idcoop',
                                        name: 'RCC73Nr',
                                        disabled: true,
                                        maskRe: /[0-9.]/,
                                        listeners: {
                                            change: function() {
                                                JumlahClonalGardenCoop()
                                            }
                                        }
                                    }]
                                }]
                            }]
                        }, {
                            xtype: 'fieldset',
                            title: lang('Not Certified'),
                            items: [{
                                layout: 'column',
                                border: false,
                                items: [{
                                    columnWidth: 0.16,
                                    layout: 'form',
                                    border: false,
                                    defaultType: 'checkboxfield',
                                    items: [{
                                        boxLabel: lang('Local'),
                                        name: 'Local',
                                        inputValue: '1',
                                        id: 'Local_idcoop',
                                        listeners: {
                                            change: function() {
                                                CheckClonalGardenCoop(this)
                                            }
                                        }
                                    }, {
                                        boxLabel: lang('RCL'),
                                        name: 'RCL',
                                        inputValue: '1',
                                        id: 'RCL_idcoop',
                                        listeners: {
                                            change: function() {
                                                CheckClonalGardenCoop(this)
                                            }
                                        }
                                    }, {
                                        boxLabel: lang('THR'),
                                        name: 'THR',
                                        inputValue: '1',
                                        id: 'THR_idcoop',
                                        listeners: {
                                            change: function() {
                                                CheckClonalGardenCoop(this)
                                            }
                                        }
                                    }, {
                                        boxLabel: lang('AP'),
                                        name: 'AP',
                                        inputValue: '1',
                                        id: 'AP_idcoop',
                                        listeners: {
                                            change: function() {
                                                CheckClonalGardenCoop(this)
                                            }
                                        }
                                    }, {
                                        boxLabel: lang('PR'),
                                        name: 'PR',
                                        inputValue: '1',
                                        id: 'PR_idcoop',
                                        listeners: {
                                            change: function() {
                                                CheckClonalGardenCoop(this)
                                            }
                                        }
                                    }, {
                                        boxLabel: lang('Scavina'),
                                        name: 'Scavina',
                                        inputValue: '1',
                                        id: 'Scavina_idcoop',
                                        listeners: {
                                            change: function() {
                                                CheckClonalGardenCoop(this)
                                            }
                                        }
                                    }]
                                }, {
                                    columnWidth: 0.16,
                                    layout: 'form',
                                    defaultType: 'textfield',
                                    border: false,
                                    padding: '0 20px 0 0',
                                    items: [{
                                        id: 'LocalNr_idcoop',
                                        name: 'LocalNr',
                                        disabled: true,
                                        maskRe: /[0-9.]/,
                                        listeners: {
                                            change: function() {
                                                JumlahClonalGardenCoop()
                                            }
                                        }
                                    }, {
                                        id: 'RCLNr_idcoop',
                                        name: 'RCLNr',
                                        disabled: true,
                                        maskRe: /[0-9.]/,
                                        listeners: {
                                            change: function() {
                                                JumlahClonalGardenCoop()
                                            }
                                        }
                                    }, {
                                        id: 'THRNr_idcoop',
                                        name: 'THRNr',
                                        disabled: true,
                                        maskRe: /[0-9.]/,
                                        listeners: {
                                            change: function() {
                                                JumlahClonalGardenCoop()
                                            }
                                        }
                                    }, {
                                        id: 'APNr_idcoop',
                                        name: 'APNr',
                                        disabled: true,
                                        maskRe: /[0-9.]/,
                                        listeners: {
                                            change: function() {
                                                JumlahClonalGardenCoop()
                                            }
                                        }
                                    }, {
                                        id: 'PRNr_idcoop',
                                        name: 'PRNr',
                                        disabled: true,
                                        maskRe: /[0-9.]/,
                                        listeners: {
                                            change: function() {
                                                JumlahClonalGardenCoop()
                                            }
                                        }
                                    }, {
                                        id: 'ScavinaNr_idcoop',
                                        name: 'ScavinaNr',
                                        disabled: true,
                                        maskRe: /[0-9.]/,
                                        listeners: {
                                            change: function() {
                                                JumlahClonalGardenCoop()
                                            }
                                        }
                                    }]
                                }, {
                                    columnWidth: 0.16,
                                    layout: 'form',
                                    border: false,
                                    defaultType: 'checkboxfield',
                                    items: [{
                                        boxLabel: lang('MT'),
                                        name: 'MT',
                                        inputValue: '1',
                                        id: 'MT_idcoop',
                                        listeners: {
                                            change: function() {
                                                CheckClonalGardenCoop(this)
                                            }
                                        }
                                    }, {
                                        boxLabel: lang('M02'),
                                        name: 'M02',
                                        inputValue: '1',
                                        id: 'M02_idcoop',
                                        listeners: {
                                            change: function() {
                                                CheckClonalGardenCoop(this)
                                            }
                                        }
                                    }, {
                                        boxLabel: lang('M04'),
                                        name: 'M04',
                                        inputValue: '1',
                                        id: 'M04_idcoop',
                                        listeners: {
                                            change: function() {
                                                CheckClonalGardenCoop(this)
                                            }
                                        }
                                    }, {
                                        boxLabel: lang('M06'),
                                        name: 'M06',
                                        inputValue: '1',
                                        id: 'M06_idcoop',
                                        listeners: {
                                            change: function() {
                                                CheckClonalGardenCoop(this)
                                            }
                                        }
                                    }, {
                                        boxLabel: lang('MHP03'),
                                        name: 'MHP03',
                                        inputValue: '1',
                                        id: 'MHP03_idcoop',
                                        listeners: {
                                            change: function() {
                                                CheckClonalGardenCoop(this)
                                            }
                                        }
                                    }, {
                                        boxLabel: lang('MHP04'),
                                        name: 'MHP04',
                                        inputValue: '1',
                                        id: 'MHP04_idcoop',
                                        listeners: {
                                            change: function() {
                                                CheckClonalGardenCoop(this)
                                            }
                                        }
                                    }]
                                }, {
                                    columnWidth: 0.16,
                                    layout: 'form',
                                    defaultType: 'textfield',
                                    border: false,
                                    padding: '0 20px 0 0',
                                    items: [{
                                        id: 'MTNr_idcoop',
                                        name: 'MTNr',
                                        disabled: true,
                                        maskRe: /[0-9.]/,
                                        listeners: {
                                            change: function() {
                                                JumlahClonalGardenCoop()
                                            }
                                        }
                                    }, {
                                        id: 'M02Nr_idcoop',
                                        name: 'M02Nr',
                                        disabled: true,
                                        maskRe: /[0-9.]/,
                                        listeners: {
                                            change: function() {
                                                JumlahClonalGardenCoop()
                                            }
                                        }
                                    }, {
                                        id: 'M04Nr_idcoop',
                                        name: 'M04Nr',
                                        disabled: true,
                                        maskRe: /[0-9.]/,
                                        listeners: {
                                            change: function() {
                                                JumlahClonalGardenCoop()
                                            }
                                        }
                                    }, {
                                        id: 'M06Nr_idcoop',
                                        name: 'M06Nr',
                                        disabled: true,
                                        maskRe: /[0-9.]/,
                                        listeners: {
                                            change: function() {
                                                JumlahClonalGardenCoop()
                                            }
                                        }
                                    }, {
                                        id: 'MHP03Nr_idcoop',
                                        name: 'MHP03Nr',
                                        disabled: true,
                                        maskRe: /[0-9.]/,
                                        listeners: {
                                            change: function() {
                                                JumlahClonalGardenCoop()
                                            }
                                        }
                                    }, {
                                        id: 'MHP04Nr_idcoop',
                                        name: 'MHP04Nr',
                                        disabled: true,
                                        maskRe: /[0-9.]/,
                                        listeners: {
                                            change: function() {
                                                JumlahClonalGardenCoop()
                                            }
                                        }
                                    }]
                                }, {
                                    columnWidth: 0.16,
                                    layout: 'form',
                                    border: false,
                                    defaultType: 'checkboxfield',
                                    items: [{
                                        boxLabel: lang('BB01'),
                                        name: 'BB01',
                                        inputValue: '1',
                                        id: 'BB01_idcoop',
                                        listeners: {
                                            change: function() {
                                                CheckClonalGardenCoop(this)
                                            }
                                        }
                                    }, {
                                        boxLabel: lang('BLB'),
                                        name: 'BLB',
                                        inputValue: '1',
                                        id: 'BLB_idcoop',
                                        listeners: {
                                            change: function() {
                                                CheckClonalGardenCoop(this)
                                            }
                                        }
                                    }, {
                                        boxLabel: lang('BRT'),
                                        name: 'BRT',
                                        inputValue: '1',
                                        id: 'BRT_idcoop',
                                        listeners: {
                                            change: function() {
                                                CheckClonalGardenCoop(this)
                                            }
                                        }
                                    }]
                                }, {
                                    columnWidth: 0.16,
                                    layout: 'form',
                                    defaultType: 'textfield',
                                    border: false,
                                    padding: '0 20px 0 0',
                                    items: [{
                                        id: 'BB01Nr_idcoop',
                                        name: 'BB01Nr',
                                        disabled: true,
                                        maskRe: /[0-9.]/,
                                        listeners: {
                                            change: function() {
                                                JumlahClonalGardenCoop()
                                            }
                                        }
                                    }, {
                                        id: 'BLBNr_idcoop',
                                        name: 'BLBNr',
                                        disabled: true,
                                        maskRe: /[0-9.]/,
                                        listeners: {
                                            change: function() {
                                                JumlahClonalGardenCoop()
                                            }
                                        }
                                    }, {
                                        id: 'BRTNr_idcoop',
                                        name: 'BRTNr',
                                        disabled: true,
                                        maskRe: /[0-9.]/,
                                        listeners: {
                                            change: function() {
                                                JumlahClonalGardenCoop()
                                            }
                                        }
                                    }]
                                }]
                            }]
                        }]
                    }]
                }]
            }, {
                layout: 'column',
                border: false,
                items: [{
                    columnWidth: .5,
                    layout: 'form',
                    border: false,
                    padding: 5,
                    items: [{
                        xtype: 'textfield',
                        fieldLabel: lang('Others'),
                        hidden: true,
                        id: 'OtherClones_idcoop',
                        name: 'OtherClones'
                    }]
                }, {
                    columnWidth: .5,
                    layout: 'form',
                    border: false,
                    padding: 5,
                    items: [{
                        xtype: 'textfield',
                        fieldLabel: lang('Total'),
                        id: 'OtherClonesNr_idcoop',
                        hidden: true,
                        name: 'OtherClonesNr',
                        maskRe: /[0-9.]/,
                        listeners: {
                            change: function() {
                                JumlahClonalGardenCoop()
                            }
                        }
                    }, {
                        xtype: 'textfield',
                        fieldLabel: lang('Cocoa Clone Total'),
                        id: 'TotalClonesNr_idcoop',
                        name: 'TotalClonesNr',
                        readOnly: true
                    }]
                }]
            }, {
                html: '<b> &nbsp; Shade</b>',
            }, {
                xtype: 'fieldset',
                margin: '0 0 0 0',
                padding: '0 0 0 1',
                border: false,
                items: [{
                    layout: 'column',
                    border: false,
                    items: [{
                        columnWidth: 0.19,
                        xtype: 'label',
                        padding: 5,
                        cls: 'x-form-item-label',
                        text: lang('Non-Cocoa Trees: Species and Number in Garden Type and Total (Multiple Choice)')
                    }, {
                        columnWidth: 0.81,
                        border: false,
                        items: [{
                            xtype: 'fieldset',
                            title: lang('Non-Cocoa Trees'),
                            items: [{
                                layout: 'column',
                                border: false,
                                items: [{
                                    columnWidth: 0.16,
                                    layout: 'form',
                                    border: false,
                                    defaultType: 'checkboxfield',
                                    items: [{
                                        boxLabel: lang('Coconut'),
                                        name: 'Coconut',
                                        inputValue: '1',
                                        id: 'Coconut_idcoop',
                                        listeners: {
                                            change: function() {
                                                CheckClonalGardenCoop(this)
                                            }
                                        }
                                    }, {
                                        boxLabel: lang('ArecaPalm'),
                                        name: 'ArecaPalm',
                                        inputValue: '1',
                                        id: 'ArecaPalm_idcoop',
                                        listeners: {
                                            change: function() {
                                                CheckClonalGardenCoop(this)
                                            }
                                        }
                                    }, {
                                        boxLabel: lang('Rubber'),
                                        name: 'Rubber',
                                        inputValue: '1',
                                        id: 'Rubber_idcoop',
                                        listeners: {
                                            change: function() {
                                                CheckClonalGardenCoop(this)
                                            }
                                        }
                                    }, {
                                        boxLabel: lang('Clove'),
                                        name: 'Clove',
                                        inputValue: '1',
                                        id: 'Clove_idcoop',
                                        listeners: {
                                            change: function() {
                                                CheckClonalGardenCoop(this)
                                            }
                                        }
                                    }]
                                }, {
                                    columnWidth: 0.16,
                                    layout: 'form',
                                    defaultType: 'textfield',
                                    border: false,
                                    padding: '0 20px 0 0',
                                    items: [{
                                        id: 'CoconutNr_idcoop',
                                        name: 'CoconutNr',
                                        disabled: true,
                                        maskRe: /[0-9.]/,
                                        listeners: {
                                            change: function() {
                                                JumlahShadeTreesCoop()
                                            }
                                        }
                                    }, {
                                        id: 'ArecaPalmNr_idcoop',
                                        name: 'ArecaPalmNr',
                                        disabled: true,
                                        maskRe: /[0-9.]/,
                                        listeners: {
                                            change: function() {
                                                JumlahShadeTreesCoop()
                                            }
                                        }
                                    }, {
                                        id: 'RubberNr_idcoop',
                                        name: 'RubberNr',
                                        disabled: true,
                                        maskRe: /[0-9.]/,
                                        listeners: {
                                            change: function() {
                                                JumlahShadeTreesCoop()
                                            }
                                        }
                                    }, {
                                        id: 'CloveNr_idcoop',
                                        name: 'CloveNr',
                                        disabled: true,
                                        maskRe: /[0-9.]/,
                                        listeners: {
                                            change: function() {
                                                JumlahShadeTreesCoop()
                                            }
                                        }
                                    }]
                                }, {
                                    columnWidth: 0.16,
                                    layout: 'form',
                                    border: false,
                                    defaultType: 'checkboxfield',
                                    items: [{
                                        boxLabel: lang('Cashew'),
                                        name: 'Cashew',
                                        inputValue: '1',
                                        id: 'Cashew_idcoop',
                                        listeners: {
                                            change: function() {
                                                CheckClonalGardenCoop(this)
                                            }
                                        }
                                    }, {
                                        boxLabel: lang('OilPalm'),
                                        name: 'OilPalm',
                                        inputValue: '1',
                                        id: 'OilPalm_idcoop',
                                        listeners: {
                                            change: function() {
                                                CheckClonalGardenCoop(this)
                                            }
                                        }
                                    }, {
                                        boxLabel: lang('SugarPalm'),
                                        name: 'SugarPalm',
                                        inputValue: '1',
                                        id: 'SugarPalm_idcoop',
                                        listeners: {
                                            change: function() {
                                                CheckClonalGardenCoop(this)
                                            }
                                        }
                                    }, {
                                        boxLabel: lang('Nutmeg'),
                                        name: 'Nutmeg',
                                        inputValue: '1',
                                        id: 'Nutmeg_idcoop',
                                        listeners: {
                                            change: function() {
                                                CheckClonalGardenCoop(this)
                                            }
                                        }
                                    }]
                                }, {
                                    columnWidth: 0.16,
                                    layout: 'form',
                                    defaultType: 'textfield',
                                    border: false,
                                    padding: '0 20px 0 0',
                                    items: [{
                                        id: 'CashewNr_idcoop',
                                        name: 'CashewNr',
                                        disabled: true,
                                        maskRe: /[0-9.]/,
                                        listeners: {
                                            change: function() {
                                                JumlahShadeTreesCoop()
                                            }
                                        }
                                    }, {
                                        id: 'OilPalmNr_idcoop',
                                        name: 'OilPalmNr',
                                        disabled: true,
                                        maskRe: /[0-9.]/,
                                        listeners: {
                                            change: function() {
                                                JumlahShadeTreesCoop()
                                            }
                                        }
                                    }, {
                                        id: 'SugarPalmNr_idcoop',
                                        name: 'SugarPalmNr',
                                        disabled: true,
                                        maskRe: /[0-9.]/,
                                        listeners: {
                                            change: function() {
                                                JumlahShadeTreesCoop()
                                            }
                                        }
                                    }, {
                                        id: 'NutmegNr_idcoop',
                                        name: 'NutmegNr',
                                        disabled: true,
                                        maskRe: /[0-9.]/,
                                        listeners: {
                                            change: function() {
                                                JumlahShadeTreesCoop()
                                            }
                                        }
                                    }]
                                }, {
                                    columnWidth: 0.16,
                                    layout: 'form',
                                    border: false,
                                    defaultType: 'checkboxfield',
                                    items: [{
                                        boxLabel: lang('Hazelnut'),
                                        name: 'Hazelnut',
                                        inputValue: '1',
                                        id: 'Hazelnut_idcoop',
                                        listeners: {
                                            change: function() {
                                                CheckClonalGardenCoop(this)
                                            }
                                        }
                                    }, {
                                        boxLabel: lang('Kapok'),
                                        name: 'Kapok',
                                        inputValue: '1',
                                        id: 'Kapok_idcoop',
                                        listeners: {
                                            change: function() {
                                                CheckClonalGardenCoop(this)
                                            }
                                        }
                                    }]
                                }, {
                                    columnWidth: 0.16,
                                    layout: 'form',
                                    defaultType: 'textfield',
                                    border: false,
                                    padding: '0 20px 0 0',
                                    items: [{
                                        id: 'HazelnutNr_idcoop',
                                        name: 'HazelnutNr',
                                        disabled: true,
                                        maskRe: /[0-9.]/,
                                        listeners: {
                                            change: function() {
                                                JumlahShadeTreesCoop()
                                            }
                                        }
                                    }, {
                                        id: 'KapokNr_idcoop',
                                        name: 'KapokNr',
                                        disabled: true,
                                        maskRe: /[0-9.]/,
                                        listeners: {
                                            change: function() {
                                                JumlahShadeTreesCoop()
                                            }
                                        }
                                    }]
                                }]
                            }]
                        }, {
                            xtype: 'fieldset',
                            title: lang('Hard Wood'),
                            items: [{
                                layout: 'column',
                                border: false,
                                items: [{
                                    columnWidth: 0.16,
                                    layout: 'form',
                                    border: false,
                                    defaultType: 'checkboxfield',
                                    items: [{
                                        boxLabel: lang('Mahagony'),
                                        name: 'Mahagony',
                                        inputValue: '1',
                                        id: 'Mahagony_idcoop',
                                        listeners: {
                                            change: function() {
                                                CheckClonalGardenCoop(this)
                                            }
                                        }
                                    }, {
                                        boxLabel: lang('Teak'),
                                        name: 'Teak',
                                        inputValue: '1',
                                        id: 'Teak_idcoop',
                                        listeners: {
                                            change: function() {
                                                CheckClonalGardenCoop(this)
                                            }
                                        }
                                    }]
                                }, {
                                    columnWidth: 0.16,
                                    layout: 'form',
                                    defaultType: 'textfield',
                                    border: false,
                                    padding: '0 20px 0 0',
                                    items: [{
                                        id: 'MahagonyNr_idcoop',
                                        name: 'MahagonyNr',
                                        disabled: true,
                                        maskRe: /[0-9.]/,
                                        listeners: {
                                            change: function() {
                                                JumlahShadeTreesCoop()
                                            }
                                        }
                                    }, {
                                        id: 'TeakNr_idcoop',
                                        name: 'TeakNr',
                                        disabled: true,
                                        maskRe: /[0-9.]/,
                                        listeners: {
                                            change: function() {
                                                JumlahShadeTreesCoop()
                                            }
                                        }
                                    }]
                                }, {
                                    columnWidth: 0.16,
                                    layout: 'form',
                                    border: false,
                                    defaultType: 'checkboxfield',
                                    items: [{
                                        boxLabel: lang('Vitex'),
                                        name: 'Vitex',
                                        inputValue: '1',
                                        id: 'Vitex_idcoop',
                                        listeners: {
                                            change: function() {
                                                CheckClonalGardenCoop(this)
                                            }
                                        }
                                    }, {
                                        boxLabel: lang('Ermerilla'),
                                        name: 'Ermerilla',
                                        inputValue: '1',
                                        id: 'Ermerilla_idcoop',
                                        listeners: {
                                            change: function() {
                                                CheckClonalGardenCoop(this)
                                            }
                                        }
                                    }]
                                }, {
                                    columnWidth: 0.16,
                                    layout: 'form',
                                    defaultType: 'textfield',
                                    border: false,
                                    padding: '0 20px 0 0',
                                    items: [{
                                        id: 'VitexNr_idcoop',
                                        name: 'VitexNr',
                                        disabled: true,
                                        maskRe: /[0-9.]/,
                                        listeners: {
                                            change: function() {
                                                JumlahShadeTreesCoop()
                                            }
                                        }
                                    }, {
                                        id: 'ErmerillaNr_idcoop',
                                        name: 'ErmerillaNr',
                                        disabled: true,
                                        maskRe: /[0-9.]/,
                                        listeners: {
                                            change: function() {
                                                JumlahShadeTreesCoop()
                                            }
                                        }
                                    }]
                                }, {
                                    columnWidth: 0.16,
                                    layout: 'form',
                                    border: false,
                                    defaultType: 'checkboxfield',
                                    items: [{
                                        boxLabel: lang('Anthocephalus'),
                                        name: 'Anthocephalus',
                                        inputValue: '1',
                                        id: 'Anthocephalus_idcoop',
                                        listeners: {
                                            change: function() {
                                                CheckClonalGardenCoop(this)
                                            }
                                        }
                                    }, {
                                        boxLabel: lang('Albizia'),
                                        name: 'Albizia',
                                        inputValue: '1',
                                        id: 'Albizia_idcoop',
                                        listeners: {
                                            change: function() {
                                                CheckClonalGardenCoop(this)
                                            }
                                        }
                                    }]
                                }, {
                                    columnWidth: 0.16,
                                    layout: 'form',
                                    defaultType: 'textfield',
                                    border: false,
                                    padding: '0 20px 0 0',
                                    items: [{
                                        id: 'AnthocephalusNr_idcoop',
                                        name: 'AnthocephalusNr',
                                        disabled: true,
                                        maskRe: /[0-9.]/,
                                        listeners: {
                                            change: function() {
                                                JumlahShadeTreesCoop()
                                            }
                                        }
                                    }, {
                                        id: 'AlbiziaNr_idcoop',
                                        name: 'AlbiziaNr',
                                        disabled: true,
                                        maskRe: /[0-9.]/,
                                        listeners: {
                                            change: function() {
                                                JumlahShadeTreesCoop()
                                            }
                                        }
                                    }]
                                }]
                            }]
                        }, {
                            xtype: 'fieldset',
                            title: lang('Fruit Trees'),
                            items: [{
                                layout: 'column',
                                border: false,
                                items: [{
                                    columnWidth: 0.16,
                                    layout: 'form',
                                    border: false,
                                    defaultType: 'checkboxfield',
                                    items: [{
                                        boxLabel: lang('Jackfruit'),
                                        name: 'Jackfruit',
                                        inputValue: '1',
                                        id: 'Jackfruit_idcoop',
                                        listeners: {
                                            change: function() {
                                                CheckClonalGardenCoop(this)
                                            }
                                        }
                                    }, {
                                        boxLabel: lang('Banana'),
                                        name: 'Banana',
                                        inputValue: '1',
                                        id: 'Banana_idcoop',
                                        listeners: {
                                            change: function() {
                                                CheckClonalGardenCoop(this)
                                            }
                                        }
                                    }, {
                                        boxLabel: lang('Rambutan'),
                                        name: 'Rambutan',
                                        inputValue: '1',
                                        id: 'Rambutan_idcoop',
                                        listeners: {
                                            change: function() {
                                                CheckClonalGardenCoop(this)
                                            }
                                        }
                                    }, {
                                        boxLabel: lang('Mango'),
                                        name: 'Mango',
                                        inputValue: '1',
                                        id: 'Mango_idcoop',
                                        listeners: {
                                            change: function() {
                                                CheckClonalGardenCoop(this)
                                            }
                                        }
                                    }, {
                                        boxLabel: lang('SpondiasDulcis'),
                                        name: 'SpondiasDulcis',
                                        inputValue: '1',
                                        id: 'SpondiasDulcis_idcoop',
                                        listeners: {
                                            change: function() {
                                                CheckClonalGardenCoop(this)
                                            }
                                        }
                                    }]
                                }, {
                                    columnWidth: 0.16,
                                    layout: 'form',
                                    defaultType: 'textfield',
                                    border: false,
                                    padding: '0 20px 0 0',
                                    items: [{
                                        id: 'JackfruitNr_idcoop',
                                        name: 'JackfruitNr',
                                        disabled: true,
                                        maskRe: /[0-9.]/,
                                        listeners: {
                                            change: function() {
                                                JumlahShadeTreesCoop()
                                            }
                                        }
                                    }, {
                                        id: 'BananaNr_idcoop',
                                        name: 'BananaNr',
                                        disabled: true,
                                        maskRe: /[0-9.]/,
                                        listeners: {
                                            change: function() {
                                                JumlahShadeTreesCoop()
                                            }
                                        }
                                    }, {
                                        id: 'RambutanNr_idcoop',
                                        name: 'RambutanNr',
                                        disabled: true,
                                        maskRe: /[0-9.]/,
                                        listeners: {
                                            change: function() {
                                                JumlahShadeTreesCoop()
                                            }
                                        }
                                    }, {
                                        id: 'MangoNr_idcoop',
                                        name: 'MangoNr',
                                        disabled: true,
                                        maskRe: /[0-9.]/,
                                        listeners: {
                                            change: function() {
                                                JumlahShadeTreesCoop()
                                            }
                                        }
                                    }, {
                                        id: 'SpondiasDulcisNr_idcoop',
                                        name: 'SpondiasDulcisNr',
                                        disabled: true,
                                        maskRe: /[0-9.]/,
                                        listeners: {
                                            change: function() {
                                                JumlahShadeTreesCoop()
                                            }
                                        }
                                    }]
                                }, {
                                    columnWidth: 0.16,
                                    layout: 'form',
                                    border: false,
                                    defaultType: 'checkboxfield',
                                    items: [{
                                        boxLabel: lang('Langsat'),
                                        name: 'Langsat',
                                        inputValue: '1',
                                        id: 'Langsat_idcoop',
                                        listeners: {
                                            change: function() {
                                                CheckClonalGardenCoop(this)
                                            }
                                        }
                                    }, {
                                        boxLabel: lang('Durian'),
                                        name: 'Durian',
                                        inputValue: '1',
                                        id: 'Durian_idcoop',
                                        listeners: {
                                            change: function() {
                                                CheckClonalGardenCoop(this)
                                            }
                                        }
                                    }, {
                                        boxLabel: lang('Guava'),
                                        name: 'Guava',
                                        inputValue: '1',
                                        id: 'Guava_idcoop',
                                        listeners: {
                                            change: function() {
                                                CheckClonalGardenCoop(this)
                                            }
                                        }
                                    }, {
                                        boxLabel: lang('Avocado'),
                                        name: 'Avocado',
                                        inputValue: '1',
                                        id: 'Avocado_idcoop',
                                        listeners: {
                                            change: function() {
                                                CheckClonalGardenCoop(this)
                                            }
                                        }
                                    }, {
                                        boxLabel: lang('Cempedak'),
                                        name: 'Cempedak',
                                        inputValue: '1',
                                        id: 'Cempedak_idcoop',
                                        listeners: {
                                            change: function() {
                                                CheckClonalGardenCoop(this)
                                            }
                                        }
                                    }]
                                }, {
                                    columnWidth: 0.16,
                                    layout: 'form',
                                    defaultType: 'textfield',
                                    border: false,
                                    padding: '0 20px 0 0',
                                    items: [{
                                        id: 'LangsatNr_idcoop',
                                        name: 'LangsatNr',
                                        disabled: true,
                                        maskRe: /[0-9.]/,
                                        listeners: {
                                            change: function() {
                                                JumlahShadeTreesCoop()
                                            }
                                        }
                                    }, {
                                        id: 'DurianNr_idcoop',
                                        name: 'DurianNr',
                                        disabled: true,
                                        maskRe: /[0-9.]/,
                                        listeners: {
                                            change: function() {
                                                JumlahShadeTreesCoop()
                                            }
                                        }
                                    }, {
                                        id: 'GuavaNr_idcoop',
                                        name: 'GuavaNr',
                                        disabled: true,
                                        maskRe: /[0-9.]/,
                                        listeners: {
                                            change: function() {
                                                JumlahShadeTreesCoop()
                                            }
                                        }
                                    }, {
                                        id: 'AvocadoNr_idcoop',
                                        name: 'AvocadoNr',
                                        disabled: true,
                                        maskRe: /[0-9.]/,
                                        listeners: {
                                            change: function() {
                                                JumlahShadeTreesCoop()
                                            }
                                        }
                                    }, {
                                        id: 'CempedakNr_idcoop',
                                        name: 'CempedakNr',
                                        disabled: true,
                                        maskRe: /[0-9.]/,
                                        listeners: {
                                            change: function() {
                                                JumlahShadeTreesCoop()
                                            }
                                        }
                                    }]
                                }, {
                                    columnWidth: 0.16,
                                    layout: 'form',
                                    border: false,
                                    defaultType: 'checkboxfield',
                                    items: [{
                                        boxLabel: lang('Breadfruit'),
                                        name: 'Breadfruit',
                                        inputValue: '1',
                                        id: 'Breadfruit_idcoop',
                                        listeners: {
                                            change: function() {
                                                CheckClonalGardenCoop(this)
                                            }
                                        }
                                    }, {
                                        boxLabel: lang('Papaya'),
                                        name: 'Papaya',
                                        inputValue: '1',
                                        id: 'Papaya_idcoop',
                                        listeners: {
                                            change: function() {
                                                CheckClonalGardenCoop(this)
                                            }
                                        }
                                    }, {
                                        boxLabel: lang('Mangosteen'),
                                        name: 'Mangosteen',
                                        inputValue: '1',
                                        id: 'Mangosteen_idcoop',
                                        listeners: {
                                            change: function() {
                                                CheckClonalGardenCoop(this)
                                            }
                                        }
                                    }, {
                                        boxLabel: lang('Citrus'),
                                        name: 'Citrus',
                                        inputValue: '1',
                                        id: 'Citrus_idcoop',
                                        listeners: {
                                            change: function() {
                                                CheckClonalGardenCoop(this)
                                            }
                                        }
                                    }]
                                }, {
                                    columnWidth: 0.16,
                                    layout: 'form',
                                    defaultType: 'textfield',
                                    border: false,
                                    padding: '0 20px 0 0',
                                    items: [{
                                        id: 'BreadfruitNr_idcoop',
                                        name: 'BreadfruitNr',
                                        disabled: true,
                                        maskRe: /[0-9.]/,
                                        listeners: {
                                            change: function() {
                                                JumlahShadeTreesCoop()
                                            }
                                        }
                                    }, {
                                        id: 'PapayaNr_idcoop',
                                        name: 'PapayaNr',
                                        disabled: true,
                                        maskRe: /[0-9.]/,
                                        listeners: {
                                            change: function() {
                                                JumlahShadeTreesCoop()
                                            }
                                        }
                                    }, {
                                        id: 'MangosteenNr_idcoop',
                                        name: 'MangosteenNr',
                                        disabled: true,
                                        maskRe: /[0-9.]/,
                                        listeners: {
                                            change: function() {
                                                JumlahShadeTreesCoop()
                                            }
                                        }
                                    }, {
                                        id: 'CitrusNr_idcoop',
                                        name: 'CitrusNr',
                                        disabled: true,
                                        maskRe: /[0-9.]/,
                                        listeners: {
                                            change: function() {
                                                JumlahShadeTreesCoop()
                                            }
                                        }
                                    }]
                                }]
                            }]
                        }, {
                            xtype: 'fieldset',
                            title: lang('Leguminosae'),
                            items: [{
                                layout: 'column',
                                border: false,
                                items: [{
                                    columnWidth: 0.16,
                                    layout: 'form',
                                    border: false,
                                    defaultType: 'checkboxfield',
                                    items: [{
                                        boxLabel: lang('Gliricidia'),
                                        name: 'Gliricidia',
                                        inputValue: '1',
                                        id: 'Gliricidia_idcoop',
                                        listeners: {
                                            change: function() {
                                                CheckClonalGardenCoop(this)
                                            }
                                        }
                                    }, {
                                        boxLabel: lang('Leucaena'),
                                        name: 'Leucaena',
                                        inputValue: '1',
                                        id: 'Leucaena_idcoop',
                                        listeners: {
                                            change: function() {
                                                CheckClonalGardenCoop(this)
                                            }
                                        }
                                    }]
                                }, {
                                    columnWidth: 0.16,
                                    layout: 'form',
                                    defaultType: 'textfield',
                                    border: false,
                                    padding: '0 20px 0 0',
                                    items: [{
                                        id: 'GliricidiaNr_idcoop',
                                        name: 'GliricidiaNr',
                                        disabled: true,
                                        maskRe: /[0-9.]/,
                                        listeners: {
                                            change: function() {
                                                JumlahShadeTreesCoop()
                                            }
                                        }
                                    }, {
                                        id: 'LeucaenaNr_idcoop',
                                        name: 'LeucaenaNr',
                                        disabled: true,
                                        maskRe: /[0-9.]/,
                                        listeners: {
                                            change: function() {
                                                JumlahShadeTreesCoop()
                                            }
                                        }
                                    }]
                                }, {
                                    columnWidth: 0.16,
                                    layout: 'form',
                                    border: false,
                                    defaultType: 'checkboxfield',
                                    items: [{
                                        boxLabel: lang('Parkia'),
                                        name: 'Parkia',
                                        inputValue: '1',
                                        id: 'Parkia_idcoop',
                                        listeners: {
                                            change: function() {
                                                CheckClonalGardenCoop(this)
                                            }
                                        }
                                    }]
                                }, {
                                    columnWidth: 0.16,
                                    layout: 'form',
                                    defaultType: 'textfield',
                                    border: false,
                                    padding: '0 20px 0 0',
                                    items: [{
                                        id: 'ParkiaNr_idcoop',
                                        name: 'ParkiaNr',
                                        disabled: true,
                                        listeners: {
                                            change: function() {
                                                JumlahShadeTreesCoop()
                                            }
                                        }
                                    }]
                                }, {
                                    columnWidth: 0.16,
                                    layout: 'form',
                                    border: false,
                                    defaultType: 'checkboxfield',
                                    items: [{
                                        boxLabel: lang('Archidendron'),
                                        name: 'Archidendron',
                                        inputValue: '1',
                                        id: 'Archidendron_idcoop',
                                        listeners: {
                                            change: function() {
                                                CheckClonalGardenCoop(this)
                                            }
                                        }
                                    }]
                                }, {
                                    columnWidth: 0.16,
                                    layout: 'form',
                                    defaultType: 'textfield',
                                    border: false,
                                    padding: '0 20px 0 0',
                                    items: [{
                                        id: 'ArchidendronNr_idcoop',
                                        name: 'ArchidendronNr',
                                        disabled: true,
                                        maskRe: /[0-9.]/,
                                        listeners: {
                                            change: function() {
                                                JumlahShadeTreesCoop()
                                            }
                                        }
                                    }]
                                }]
                            }]
                        }]
                    }]
                }]
            }, {
                layout: 'column',
                border: false,
                items: [{
                    columnWidth: .5,
                    layout: 'form',
                    border: false,
                    padding: 5,
                    items: [{}]
                }, {
                    columnWidth: .5,
                    layout: 'form',
                    border: false,
                    padding: 5,
                    items: [{
                        xtype: 'textfield',
                        fieldLabel: lang('Total of Shade Trees'),
                        id: 'TotalShadeTreesNr_idcoop',
                        name: 'TotalShadeTreesNr',
                        maskRe: /[0-9.]/,
                        readOnly: true
                    }]
                }]
            },
            ///***///
            {
                xtype: 'tabpanel',
                flex: 1,
                margin: 2,
                activeTab: 0,
                plain: true,
                items: [{ // grid panel clonal penjualan
                    xtype: 'gridpanel',
                    title: lang('Penjualan'),
                    id: 'clonalpenjualan_idcoop',
                    style: 'border:1px solid #CCC;',
                    store: store_clonal_penjualan_coop,
                    width: '100%',
                    loadMask: true,
                    selType: 'rowmodel',
                    minHeight: 190,
                    dockedItems: [{
                        xtype: 'toolbar',
                        items: [{
                            icon: varjs.config.base_url + 'images/icons/new/add.png', cls:'Sfr_BtnGridGreen', overCls:'Sfr_BtnGridGreen-Hover',
                            cls: m_act_save,
                            hidden: !m_act_add,
                            text: lang('Add'),
                            scope: this,
                            handler: function() {
                                if (Ext.getCmp('ClonalID_idcoop').getValue() == '') {
                                    Ext.MessageBox.alert('Warning', 'Please save clonal garden first!');
                                } else {
                                    clonalRowEditing_idcoop.cancelEdit();
                                    var r = Ext.create('penjualan.Model', {
                                        id: '',
                                        Buyer: '',
                                        Volume: '',
                                        Price: '',
                                        Total: '',
                                        DateTransaction: ''
                                    });
                                    store_clonal_penjualan_coop.insert(0, r);
                                    clonalRowEditing_idcoop.startEdit(0, 0);
                                    uang(document.getElementById('clonalvol_idcoop'))
                                }
                            }
                        }, {
                            icon: varjs.config.base_url + 'images/icons/new/update.png',
                            cls: m_act_save,
                            hidden: !m_act_update,
                            text: lang('Update'),
                            scope: this,
                            handler: function() {
                                clonalRowEditing_idcoop.cancelEdit();
                                var sm = Ext.getCmp('clonalpenjualan_idcoop').getSelectionModel().getSelection();
                                clonalRowEditing_idcoop.startEdit(sm[0].index, 0);
                            }
                        }, {
                            itemId: 'remove',
                            icon: varjs.config.base_url + 'images/icons/new/delete.png',
                            cls: m_act_save,
                            hidden: !m_act_delete,
                            text: lang('Delete'),
                            scope: this,
                            handler: function() {
                                var smb = Ext.getCmp('clonalpenjualan_idcoop').getSelectionModel().getSelection()[0];
                                clonalRowEditing_idcoop.cancelEdit();
                                Ext.MessageBox.confirm('Message', lang('Apakah anda mau menghapus data ini ?'), function(btn) {
                                    if (btn == 'yes') {
                                        Ext.Ajax.request({
                                            waitMsg: lang('Please Wait'),
                                            url: m_clonal + '_penjualan',
                                            method: 'DELETE',
                                            params: {
                                                id: smb.raw.id
                                            },
                                            success: function(response, opts) {
                                                var obj = Ext.decode(response.responseText);
                                                switch (obj.success) {
                                                    case true:
                                                        store_clonal_penjualan_coop.load({
                                                            params: {
                                                                clonal_id: Ext.getCmp('ClonalID_idcoop').getValue()
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
                        width: '20%',
                        editor: {
                            xtype: 'combo',
                            store: mc_pembeli,
                            displayField: 'label',
                            valueField: 'label',
                            queryMode: 'local',
                            allowBlank: false
                        }
                    }, {
                        text: lang('Bibit Dijual'),
                        dataIndex: 'Volume',
                        width: '15%',
                        editor: {
                            xtype: 'textfield',
                            id: 'clonalvol_idcoop',
                            allowBlank: false,
                            listeners: {
                                change: function() {
                                    Ext.getCmp('clonaltot_idcoop').setValue(Ext.getCmp('clonalvol_idcoop').getValue() * Ext.getCmp('clonalpri_idcoop').getValue());
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
                        width: '15%',
                        editor: {
                            xtype: 'textfield',
                            id: 'clonalpri_idcoop',
                            allowBlank: false,
                            listeners: {
                                change: function() {
                                    Ext.getCmp('clonaltot_idcoop').setValue(Ext.getCmp('clonalvol_idcoop').getValue() * Ext.getCmp('clonalpri_idcoop').getValue());
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
                            id: 'clonaltot_idcoop',
                            readOnly: true
                        }
                    }, {
                        text: lang('Tanggal Penjualan'),
                        dataIndex: 'DateTransaction',
                        format: 'Y-m-d',
                        width: '13%',
                        editor: {
                            xtype: 'datefield',
                            format: 'Y-m-d',
                            allowBlank: false
                        }
                    }],
                    plugins: [clonalRowEditing_idcoop],
                    listeners: {
                        itemdblclick: function(dv, record, item, index, e) {
                            if (!m_act_update) {
                                clonalRowEditing_idcoop.cancelEdit();
                            }
                        },
                        'canceledit': function(editor, e, eOpts) {
                            store_clonal_penjualan_coop.load({
                                params: {
                                    clonal_id: Ext.getCmp('ClonalID_idcoop').getValue()
                                }
                            });
                        },
                        'edit': function(editor, e) {
                            if (e.record.data.id == '') {
                                Ext.Ajax.request({
                                    waitMsg: lang('Please wait...'),
                                    url: m_clonal + '_penjualan',
                                    method: 'POST',
                                    params: {
                                        id_clonal: Ext.getCmp('ClonalID_idcoop').getValue(),
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
                                                store_clonal_penjualan_coop.load({
                                                    params: {
                                                        clonal_id: Ext.getCmp('ClonalID_idcoop').getValue()
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
                                            url: m_clonal + '_penjualan',
                                            method: 'PUT',
                                            params: {
                                                id: e.record.data.id,
                                                id_clonal: Ext.getCmp('ClonalID_idcoop').getValue(),
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
                                                        store_clonal_penjualan_coop.load({
                                                            params: {
                                                                clonal_id: Ext.getCmp('ClonalID_idcoop').getValue()
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
                }, { // tab nursery monitoring
                    xtype: 'gridpanel',
                    title: lang('Monitoring'),
                    id: 'clonalmonitoring_idcoop',
                    style: 'border:1px solid #CCC;',
                    store: store_clonal_monitoring_coop,
                    width: '100%',
                    loadMask: true,
                    selType: 'rowmodel',
                    minHeight: 190,
                    dockedItems: [{
                        xtype: 'toolbar',
                        items: [{
                            icon: varjs.config.base_url + 'images/icons/new/add.png', cls:'Sfr_BtnGridGreen', overCls:'Sfr_BtnGridGreen-Hover',
                            cls: m_act_save,
                            hidden: !m_act_add,
                            text: lang('Add'),
                            scope: this,
                            handler: function() {
                                if (Ext.getCmp('ClonalID_idcoop').getValue() == '') {
                                    Ext.MessageBox.alert('Warning', 'Please save clonal garden first!');
                                } else {
                                    mclonalRowEditing_idcoop.cancelEdit();
                                    var r = Ext.create('monitoring.Model', {
                                        id: '',
                                        MonitoringDate: '',
                                        MonitoringStatus: '',
                                        Description: ''
                                    });
                                    store_clonal_monitoring_coop.insert(0, r);
                                    mclonalRowEditing_idcoop.startEdit(0, 0);
                                }
                            }
                        }, {
                            icon: varjs.config.base_url + 'images/icons/new/update.png',
                            cls: m_act_save,
                            hidden: !m_act_add,
                            text: lang('Update'),
                            scope: this,
                            handler: function() {
                                mclonalRowEditing_idcoop.cancelEdit();
                                var sm = Ext.getCmp('clonalmonitoring_idcoop').getSelectionModel().getSelection();
                                mclonalRowEditing_idcoop.startEdit(sm[0].index, 0);
                                act_clonal_status_coop(Ext.getCmp('clonalStatus_idcoop').getValue());
                            }
                        }, {
                            itemId: 'remove',
                            icon: varjs.config.base_url + 'images/icons/new/delete.png',
                            cls: m_act_save,
                            hidden: !m_act_add,
                            text: lang('Delete'),
                            scope: this,
                            handler: function() {
                                var smb = Ext.getCmp('clonalmonitoring_idcoop').getSelectionModel().getSelection()[0];
                                mclonalRowEditing_idcoop.cancelEdit();
                                Ext.MessageBox.confirm('Message', lang('Apakah anda mau menghapus data ini ?'), function(btn) {
                                    if (btn == 'yes') {
                                        Ext.Ajax.request({
                                            waitMsg: lang('Please Wait'),
                                            url: m_clonal + '_monitorings',
                                            method: 'DELETE',
                                            params: {
                                                id: smb.raw.id
                                            },
                                            success: function(response, opts) {
                                                var obj = Ext.decode(response.responseText);
                                                switch (obj.success) {
                                                    case true:
                                                        store_clonal_monitoring_coop.load({
                                                            params: {
                                                                clonal_id: Ext.getCmp('ClonalID_idcoop').getValue()
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
                            id: 'clonalDate',
                            format: 'Y-m-d',
                            allowBlank: false
                        }
                    }, {
                        text: lang('Status'),
                        dataIndex: 'MonitoringStatus',
                        width: '20%',
                        editor: {
                            xtype: 'combo',
                            id: 'clonalStatus_idcoop',
                            store: mc_status_monitoring,
                            displayField: 'label',
                            valueField: 'label',
                            queryMode: 'local',
                            allowBlank: false,
                            listeners: {
                                change: function(combo, selection) {
                                    Ext.getCmp('clonalDescription_idcoop').setValue('');
                                    act_clonal_status_coop(Ext.getCmp('clonalStatus_idcoop').getValue());
                                }
                            }
                        }
                    }, {
                        text: lang('Keterangan'),
                        dataIndex: 'Description',
                        width: '59%',
                        editor: {
                            xtype: 'combo',
                            id: 'clonalDescription_idcoop',
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
                    plugins: [mclonalRowEditing_idcoop],
                    listeners: {
                        itemdblclick: function(dv, record, item, index, e) {
                            if (!m_act_update) {
                                mclonalRowEditing_idcoop.cancelEdit();
                            }
                        },
                        'canceledit': function(editor, e, eOpts) {
                            store_clonal_monitoring_coop.load({
                                params: {
                                    clonal_id: Ext.getCmp('ClonalID_idcoop').getValue()
                                }
                            });
                        },
                        'edit': function(editor, e) {
                            if (Ext.getCmp('ClonalID_idcoop').getValue() == '' || Ext.getCmp('ClonalID_idcoop').getValue() == undefined) {
                                Ext.Msg.alert("Alert", 'Belum ada data Clonal Garden');
                            } else {
                                if (e.record.data.id == '') {
                                    Ext.Ajax.request({
                                        waitMsg: lang('Please wait...'),
                                        url: m_clonal + '_monitorings',
                                        method: 'POST',
                                        params: {
                                            id_clonal: Ext.getCmp('ClonalID_idcoop').getValue(),
                                            MonitoringDate: e.record.data.MonitoringDate,
                                            MonitoringStatus: e.record.data.MonitoringStatus,
                                            Description: e.record.data.Description
                                        },
                                        success: function(response, opts) {
                                            var obj = Ext.decode(response.responseText);
                                            switch (obj.success) {
                                                case true:
                                                    Ext.MessageBox.alert('Success', obj.message);
                                                    store_clonal_monitoring_coop.load({
                                                        params: {
                                                            clonal_id: Ext.getCmp('ClonalID_idcoop').getValue()
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
                                                url: m_clonal + '_monitorings',
                                                method: 'PUT',
                                                params: {
                                                    id: e.record.data.id,
                                                    id_clonal: Ext.getCmp('ClonalID_idcoop').getValue(),
                                                    MonitoringDate: e.record.data.MonitoringDate,
                                                    MonitoringStatus: e.record.data.MonitoringStatus,
                                                    Description: e.record.data.Description
                                                },
                                                success: function(response, opts) {
                                                    var obj = Ext.decode(response.responseText);
                                                    switch (obj.success) {
                                                        case true:
                                                            Ext.MessageBox.alert('Success', obj.message);
                                                            store_clonal_monitoring_coop.load({
                                                                params: {
                                                                    clonal_id: Ext.getCmp('ClonalID_idcoop').getValue()
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
                            act_clonal_status_coop(Ext.getCmp('clonalStatus_idcoop').getValue());
                        }
                    }
                }]
            }
            ///***///
        ],
        buttons: [{
            id: 'cgsaveButton_idcoop',
            text: lang('Save'),
            margin: '5px',
            scale: 'large',
            ui: 's-button',
            cls: 's-blue ' + m_act_save,
            handler: function() {
                var form = Ext.getCmp('dataFormClonalGarden_idcoop').getForm();
                var methode;
                if (Ext.getCmp('ClonalID_idcoop').getValue() != '') methode = 'PUT';
                else methode = 'POST';
                //Ext.getCmp('Luas').setValue(nnumber_format(Ext.getCmp('Luas').getValue(), 2))
                //Ext.getCmp('Kapasitas').setValue(nnumber_format(Ext.getCmp('Kapasitas').getValue(), 2))
                form.submit({
                    url: m_crud + '_clonal_garden',
                    method: methode,
                    waitMsg: lang('Sending data...'),
                    success: function(fp, o) {
                        var r = Ext.decode(o.response.responseText);
                        if (r.success == 'sukses') {
                            Ext.getCmp('GardenNr_default_idcoop').setValue(Ext.getCmp('GardenNr_idcoop').getValue());
                            Ext.MessageBox.alert('Success', lang(r.message));
                            if (r.id != '') {
                                Ext.getCmp('ClonalID_idcoop').setValue(r.id);
                            }
                            store_clonal_polygon_coop.load({
                                params: {
                                    ObjType: 'koperasi',
                                    ObjID: Ext.getCmp('CoopID').getValue()
                                }
                            });
                        } else {
                            Ext.MessageBox.alert('Warning', lang(r.message));
                        }
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
                winClonalGardenCoop.hide();
            }
        }]
    });
    var winClonalGardenCoop = Ext.create('widget.window', {
        title: lang('Farmer Organization Clonal Garden Unit'),
        id: 'winClonalGarden_idcoop',
        closable: true,
        modal: true,
        closeAction: 'show',
        width: '80%',
        height: 550,
        layout: 'fit',
        items: [DataFormClonalGardenCoop]
    });
    //**//

    //training
    var store_training_list = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['CoopTrainingsID', 'CoopID', 'CoopTrainingID', 'TrainingStart', 'TrainingEnd', 'TrainingDays', 'participants', 'CoopTrainingName', 'ServiceProvID', 'FacilitatorStaff', 'DistrictID', 'ProvinceID', 'Location',],
        autoLoad: false,
        // pageSize: 10,
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
    var DataFormTrainingList = Ext.create('Ext.form.Panel', {
        frame: false,
        autoScroll: true,
        height: 350,
        width: '100%',
        bodyPadding: 5,
        id: 'dataFormTrainingList',
        items: [{
                xtype: 'gridpanel',
                id: 'gtraining',
                style: 'border:1px solid #CCC;',
                store: store_training_list,
                width: '100%',
                loadMask: true,
                selType: 'rowmodel',
                listeners: {
                    itemdblclick: function(dv, record, item, index, e) {
                        displayFormWindowTraining();
                        var sm = record;
                        setFormTraining(sm.data);
                        store_participant.load({
                            params: {
                                CoopTrainingsID: Ext.getCmp('CoopTrainingsID').getValue()
                            }
                        });
                        setTrainingParticipantButton();
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
                                    displayFormWindowTraining();
                                    hideSave();
                                    store_participant.load();
                                    setTrainingParticipantButton();
                                }
                            }, {
                                icon: varjs.config.base_url + 'images/icons/new/update.png',
                                // cls: m_act_save,
                                hidden: !m_act_update,
                                text: lang('Update'),
                                scope: this,
                                handler: function() {
                                    displayFormWindowTraining();
                                    var sm = Ext.getCmp('gtraining').getSelectionModel().getSelection()[0];
                                    setFormTraining(sm.data);
                                    store_participant.load({
                                        params: {
                                            CoopTrainingsID: Ext.getCmp('CoopTrainingsID').getValue()
                                        }
                                    });
                                    setTrainingParticipantButton();
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
                                                    var obj = Ext.decode(response.responseText);
                                                    switch (obj.success) {
                                                        case true:
                                                            store_training_list.load({
                                                                params: {
                                                                    CoopID: sm.get('CoopID')
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
                columns: [
                    {
                        text: lang('ID'),
                        dataIndex: 'CoopTrainingsID',
                        hidden: true
                    },
                    {
                        text: lang('No'),
                        xtype: 'rownumberer',
                        width: '5%'
                    },
                    {
                        text: lang('Trainings'),
                        dataIndex: 'CoopTrainingName',
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
                    winTrainingList.hide();
                }
            }]
    });

    var winTrainingList = Ext.create('widget.window', {
        title: lang('Training'),
        id: 'winTrainingList',
        closable: true,
        modal: true,
        closeAction: 'show',
        width: '80%',
        height: '90%',
        layout: 'fit',
        items: [DataFormTrainingList]
    });

    function displayFormTrainingList() {
        if (!winTrainingList.isVisible()) {
            store_training_list.load({
                params: {
                    CoopID: Ext.getCmp('CoopID').getValue()
                }
            });
            winTrainingList.center();
            winTrainingList.show();
        } else {
            winTrainingList.hide(this, function() {
            });
            winTrainingList.toFront();
        }
    }

    var mc_training_type = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'label'],
        autoLoad: true,
        proxy: {
            type: 'ajax',
            url: m_training_type,
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });

    var mc_service_provider = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'label'],
        autoLoad: true,
        proxy: {
            type: 'ajax',
            url: m_service_provider,
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });

    var store_participant = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['ParticipantsID', 'CoopTrainingsID', 'MemberID', 'MemberFarmerID', 'WritingStart', 'WritingEnd', 'BallotStart', 'BallotEnd', 'Name'],
        // pageSize: 10,
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

    var mc_coop_member = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'label'],
        autoLoad: false,
        pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_coop_member,
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });
    Ext.define('Participant.Model', {
        extend: 'Ext.data.Model',
        fields: ['ParticipantsID','CoopTrainingsID','MemberID','MemberFarmerID','WritingStart','WritingEnd','BallotStart','BallotEnd',]
    });
    var DataFormTraining = Ext.create('Ext.form.Panel', {
        height: 659,
        autoScroll: true,
        width: 1014,
        id: 'dataFormTraining',
        fieldDefaults: {
            labelAlign: 'left',
            labelWidth: 120,
            anchor: '100%'
        },
        items: [{
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
                                id: 'tCoopName',
                                name: 'CoopName',
                                fieldLabel: lang('Cooperative Name'),
                                readOnly: true
                            }, {
                                xtype: 'textfield',
                                id: 'tCoopID',
                                name: 'CoopID',
                                inputType: 'hidden'
                            }, {
                                xtype: 'textfield',
                                id: 'CoopTrainingsID',
                                name: 'CoopTrainingsID',
                                inputType: 'hidden'
                            }, {
                                xtype: 'combo',
                                store: mc_training_type,
                                displayField: 'label',
                                valueField: 'id',
                                fieldLabel: lang('Training Type'),
                                allowBlank:false,
                                queryMode: 'local',
                                id: 'CoopTrainingID',
                                name: 'CoopTrainingID'
                            }, {
                                id: 'ProvinceID',
                                name: 'ProvinceID',
                                xtype: 'combo',
                                fieldLabel: lang('Provinsi'),
                                store: mc_Province,
                                displayField: 'label',
                                valueField: 'id',
                                readOnly: false,
                                queryMode: 'local',
                                listeners: {
                                    change: function(cb, nv, ov) {
                                        if (nv) {
                                            mc_District.load({
                                                params: {
                                                    prov: nv
                                                }});
                                        }
                                    }
                                }
                            }, {
                                id: 'DistrictID',
                                name: 'DistrictID',
                                xtype: 'combo',
                                fieldLabel: lang('Kabupaten'),
                                store: mc_District,
                                displayField: 'label',
                                valueField: 'id',
                                queryMode: 'local',
                            }, {
                                xtype: 'textfield',
                                fieldLabel: lang('ToT Location'),
                                id: 'Location',
                                name: 'Location'
                            },
                        ]
                    }, {
                        columnWidth: .5,
                        layout: 'form',
                        padding: 5,
                        border: false,
                        items: [{
                                xtype: 'combo',
                                store: mc_service_provider,
                                displayField: 'label',
                                valueField: 'id',
                                fieldLabel: lang('Service Provider'),
                                allowBlank:false,
                                id: 'ServiceProvID',
                                name: 'ServiceProvID',
                                queryMode: 'local'
                            }, {
                                xtype: 'textfield',
                                fieldLabel: lang('Fasilitator'),
                                id: 'FacilitatorStaff',
                                name: 'FacilitatorStaff',
                            }, {
                                xtype: 'datefield',
                                format: 'Y-m-d',
                                fieldLabel: lang('Training Start'),
                                allowBlank:false,
                                id: 'TrainingStart',
                                name: 'TrainingStart'
                            }, {
                                xtype: 'datefield',
                                fieldLabel: lang('Training End'),
                                allowBlank:false,
                                format: 'Y-m-d',
                                id: 'TrainingEnd',
                                name: 'TrainingEnd'
                            }, {
                                xtype: 'textfield',
                                allowBlank: false,
                                id: 'TrainingDays',
                                name: 'TrainingDays',
                                fieldLabel: lang('Training Days')
                            }
                        ]
                    }
                ]
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
                                id: 'buttonParticipantAdd',
                                text: lang('Add'),
                                hidden: !m_act_add,
                                scope: this,
                                handler: function() {
                                    // displayAddWindowParticipant();
                                    // hideSave();
                                    RowEditing.cancelEdit();
                                    var r = Ext.create('Participant.Model', {
                                        // id: '', staf: '', wstart: '', wend: '', bstart: '', bend: ''
                                        ParticipantsID :'', CoopTrainingsID : Ext.getCmp('CoopTrainingsID').getValue(), MemberID :'', MemberFarmerID :'', WritingStart :'', WritingEnd :'', BallotStart :'', BallotEnd :''
                                    });
                                    store_participant.insert(0, r);
                                    RowEditing.startEdit(0, 0);
                                }
                            }, {
                                icon: varjs.config.base_url + 'images/icons/new/update.png',
                                id: 'buttonParticipantUpdate',
                                text: lang('Update'),
                                hidden: !m_act_update,
                                scope: this,
                                handler: function() {
                                    RowEditing.cancelEdit();
                                    var sm = Ext.getCmp('grid_participant').getSelectionModel().getSelection();
                                    RowEditing.startEdit(sm[0].index, 0);
                                    // Ext.getCmp('pFarmerID').setValue(sm[0].data.pFarmerID)
                                }
                            }, {
                                itemId: 'remove',
                                icon: varjs.config.base_url + 'images/icons/new/delete.png',
                                id: 'buttonParticipantDelete',
                                hidden: !m_act_delete,
                                text: lang('Delete'),
                                scope: this,
                                handler: function() {
                                    var smz = Ext.getCmp('grid_participant').getSelectionModel().getSelection();
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
                                                                    CoopTrainingsID: smz[0].data.CoopTrainingsID
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
                                id: 'buttonAttendancePrint',
                                text: lang('Daftar Hadir'),
                                menu: {
                                    items: [{
                                            text: lang('Form Kosong'),
                                            handler: function() {
                                                preview_cetak_surat(m_cetak +'?search=&CoopTrainingsID='+ Ext.getCmp('CoopTrainingsID').getValue());
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
                            },
                            {
                                icon: varjs.config.base_url + 'images/icons/new/update.png',
                                id: 'buttonAttendance',
                                text: lang('Attendance Check List'),
                                scope: this,
                                handler: function() {
                                    var sm = Ext.getCmp('grid_participant').getSelectionModel().getSelection();
                                    if (!sm[0]) {
                                        Ext.MessageBox.alert(lang('Warning'), lang('Silahkan pilih peserta'));
                                    } else {
                                        $.ajax({
                                            url: m_participant_detail,
                                            data: {
                                                ParticipantsID: sm[0].data.ParticipantsID
                                            },
                                        })
                                        .done(function(data) {
                                            displayFormWindowParticipantCheckList();

                                            Ext.getCmp('parcheklist_participantid').setValue(data['ParticipantsID']);
                                            Ext.getCmp('parcheklist_name').setValue(data['Name']);
                                            Ext.getCmp('parcheklist_groupname').setValue(data['GroupName']);
                                            Ext.getCmp('parcheklist_trainingdays').setValue(data['TrainingDays']);
                                            Ext.getCmp('parcheklist_startdate').setValue(data['TrainingStart']);
                                            Ext.getCmp('parcheklist_enddate').setValue(data['TrainingEnd']);
                                        });
                                        store_participant_checklist.load({
                                            params: {
                                                CoopTrainingsID: sm[0].data.CoopTrainingsID,
                                                MemberID: sm[0].data.MemberID,
                                            }
                                        })

                                    }
                                }
                            },
                        ]
                    }],
                columns: [
                    {
                        text: lang('ID'),
                        dataIndex: 'CoopTrainingsID',
                        hidden: true,
                    },
                    {
                        text: lang('ID'),
                        dataIndex: 'ParticipantsID',
                        flex: 1,
                    },
                    {
                        text: lang('Member'),
                        dataIndex: 'Name',
                        flex: 3,
                        editor: {
                            xtype: 'combo',
                            displayField: 'label',
                            id: 'MemberID',
                            name: 'MemberID',
                            valueField: 'id',
                            queryMode: 'local',
                            store: mc_coop_member,
                            typeAhead: true,
                            listeners: {
                                beforequery: function(record) {
                                    record.query = new RegExp(record.query, 'i');
                                    record.forceAll = true;
                                },
                                change: function(cb, nv, ov) {
                                    if (Ext.getCmp('MemberID').getValue() != nv) {
                                        Ext.Ajax.request({
                                            waitMsg: lang('Check data...'),
                                            url: m_check_participant,
                                            method: 'GET',
                                            params: {
                                                CoopTrainingsID: Ext.getCmp('CoopTrainingsID').getValue(),
                                                MemberID: Ext.getCmp('MemberID').getValue()
                                            },
                                            success: function(response, opts) {
                                                var obj = Ext.decode(response.responseText);
                                                if (!obj.data) {
                                                    Ext.MessageBox.alert('Warning', lang('Anggota telah terdapat dalam list'));
                                                    Ext.getCmp('MemberID').setValue('');
                                                    return;
                                                }
                                            }
                                        });
                                    }
                                }
                            }
                        }
                    },
                    {
                        text: lang('W Awal'),
                        dataIndex: 'WritingStart',
                        flex: 1,
                        editor: {
                            xtype: 'textfield',
                        }
                    },
                    {
                        text: lang('W Akhir'),
                        dataIndex: 'WritingEnd',
                        flex: 1,
                        editor: {
                            xtype: 'textfield',
                        }
                    },
                    {
                        text: lang('B Awal'),
                        dataIndex: 'BallotStart',
                        flex: 1,
                        editor: {
                            xtype: 'textfield',
                        }
                    },
                    {
                        text: lang('B Akhir'),
                        dataIndex: 'BallotEnd',
                        flex: 1,
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
                            Ext.getCmp('ParticipantsID').setValue(sm.get('ParticipantsID'))
                        }
                    },
                    'canceledit': function(editor, e, eOpts) {
                        store_participant.load({
                            params: {
                                CoopTrainingsID: Ext.getCmp('CoopTrainingsID').getValue(),
                            }
                        });
                    },
                    'edit': function(editor, e) {
                        if (e.record.data.ParticipantsID == '') {
                            Ext.Ajax.request({
                                waitMsg: lang('Please wait...'),
                                url: m_participant,
                                method: 'POST',
                                params: {
                                    CoopTrainingsID: e.record.data.CoopTrainingsID,
                                    MemberID:       e.record.data.MemberID,
                                    WritingStart:   e.record.data.WritingStart,
                                    WritingEnd:     e.record.data.WritingEnd,
                                    BallotStart:    e.record.data.BallotStart,
                                    BallotEnd:      e.record.data.BallotEnd,
                                },
                                success: function(response, opts) {
                                    var obj = Ext.decode(response.responseText);
                                    switch (obj.success) {
                                        case true:
                                            Ext.MessageBox.alert('Success', lang('Participant added'));
                                            store_participant.load({
                                                params: {
                                                    CoopTrainingsID: Ext.getCmp('CoopTrainingsID').getValue(),
                                                }
                                            });
                                            mc_coop_member.load({
                                                params: {
                                                    CoopID: Ext.getCmp('CoopID').getValue(),
                                                    CoopTrainingsID: Ext.getCmp('CoopTrainingsID').getValue()
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
                                    Ext.Ajax.request({
                                        waitMsg: lang('Please wait...'),
                                        url: m_participant,
                                        method: 'PUT',
                                        params: {
                                            ParticipantsID: e.record.data.ParticipantsID,
                                            CoopTrainingsID: e.record.data.CoopTrainingsID,
                                            MemberID:       e.record.data.MemberID,
                                            WritingStart:   e.record.data.WritingStart,
                                            WritingEnd:     e.record.data.WritingEnd,
                                            BallotStart:    e.record.data.BallotStart,
                                            BallotEnd:      e.record.data.BallotEnd,
                                        },
                                        success: function(response, opts) {
                                            var obj = Ext.decode(response.responseText);
                                            switch (obj.success) {
                                                case true:
                                                    Ext.MessageBox.alert('Success', lang('Participant updated'));
                                                    store_participant.load({
                                                        params: {
                                                            CoopTrainingsID: Ext.getCmp('CoopTrainingsID').getValue()
                                                        }
                                                    });
                                                    mc_coop_member.load({
                                                        params: {
                                                            CoopID: Ext.getCmp('CoopID').getValue(),
                                                            CoopTrainingsID: Ext.getCmp('CoopTrainingsID').getValue()
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
                id: 'save_par',
                text: lang('Save'),
                margin: '5px',
                scale: 'large',
                ui: 's-button',
                cls: 's-blue ',
                handler: function() {
                    var form = this.up('form').getForm();
                    var methode;
                    if (Ext.getCmp('CoopTrainingsID').getValue() == '')
                        methode = 'POST';
                    else
                        methode = 'PUT';
                    if (form.isValid()) {
                        form.submit({
                            url: m_training,
                            method: methode,
                            waitMsg: lang('Sending data...'),
                            success: function(fp, o) {
                                Ext.MessageBox.alert('Success', lang('Data saved.'));
                                if (methode == 'POST')
                                    Ext.getCmp('CoopTrainingsID').setValue(o.result.id);
                                store_training_list.load({
                                    params: {
                                        CoopID: Ext.getCmp('CoopID').getValue()
                                    }
                                });
                                setTrainingParticipantButton();
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
                    winTraining.hide();
                    store_training_list.load({
                        params: {
                            CoopID: Ext.getCmp('CoopID').getValue()
                        }
                    });
                }
            }]
    });

    var winTraining = Ext.widget('window', {
        title: lang('Data Training'),
        id: 'winTraining',
        closeAction: 'hide',
        width: '80%',
        height: '90%',
        autoScroll: true,
        modal: true,
        layout: 'fit',
        items: [DataFormTraining]
    });

    function setTrainingParticipantButton() {
        if (Ext.getCmp('CoopTrainingsID').getValue()) {
            Ext.getCmp('buttonParticipantAdd').setDisabled(false);
            Ext.getCmp('buttonParticipantUpdate').setDisabled(false);
            Ext.getCmp('buttonParticipantDelete').setDisabled(false);
            Ext.getCmp('buttonAttendancePrint').setDisabled(false);
            Ext.getCmp('buttonAttendance').setDisabled(false);
        } else {
            Ext.getCmp('buttonParticipantAdd').setDisabled(true);
            Ext.getCmp('buttonParticipantUpdate').setDisabled(true);
            Ext.getCmp('buttonParticipantDelete').setDisabled(true);
            Ext.getCmp('buttonAttendancePrint').setDisabled(true);
            Ext.getCmp('buttonAttendance').setDisabled(true);
        }
    }

    function setFormTraining(data) {
        Ext.getCmp('dataFormTraining').getForm().reset();
        Ext.getCmp('tCoopName').setValue(Ext.getCmp('CoopName').getValue());
        Ext.getCmp('tCoopID').setValue(Ext.getCmp('CoopID').getValue());
        if (data) {
            Ext.getCmp('CoopTrainingsID').setValue(data.CoopTrainingsID);
            Ext.getCmp('CoopTrainingID').setValue(data.CoopTrainingID);
            Ext.getCmp('TrainingStart').setValue(data.TrainingStart);
            Ext.getCmp('TrainingEnd').setValue(data.TrainingEnd);
            Ext.getCmp('TrainingDays').setValue(data.TrainingDays);
            Ext.getCmp('ServiceProvID').setValue(data.ServiceProvID);
            Ext.getCmp('FacilitatorStaff').setValue(data.FacilitatorStaff);
            Ext.getCmp('ProvinceID').setValue(data.ProvinceID);
            Ext.getCmp('DistrictID').setValue(data.DistrictID);
            Ext.getCmp('Location').setValue(data.Location);
            mc_coop_member.load({
                params: {
                    CoopID: Ext.getCmp('CoopID').getValue(),
                    CoopTrainingsID: data.CoopTrainingsID
                }
            });
        }

    }

    function displayFormWindowTraining() {
        if (!winTraining.isVisible()) {
            setFormTraining();
            winTraining.center();
            winTraining.show();
        } else {
            winTraining.hide(this, function() {
            });
            winTraining.toFront();
        }
    }


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
                                id: 'parcheklist_participantid',
                                fieldLabel: lang('Participant ID'),
                                readOnly: true
                            },
                            {
                                xtype: 'textfield',
                                id: 'parcheklist_name',
                                fieldLabel: lang('Participant Name'),
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
                        data.push(val.data);
                    });
                    $.ajax({
                        url: m_attendance,
                        type: 'POST',
                        data: {
                            CoopTrainingsID: sm[0].data.CoopTrainingsID,
                            MemberID: sm[0].data.MemberID,
                            data: data
                        },
                    })
                    .done(function(data) {
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

    function displayFormWindowParticipantCheckList() {
        if (!winParCheckList.isVisible()) {
            winParCheckList.show();
        } else {
            winParCheckList.hide();
            winParCheckList.toFront();
        }
    }

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
                xtype: 'numberfield',
                id: 'DayNumber',
                name: 'DayNumber',
                fieldLabel: lang('Day Number'),
                minValue: 1,
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
                            //preview_cetak_surat(m_cetak_basic_farmer + 'FarmerTrainingID/' + FarmerTrainingID + '/SurveyID/' + SurveyID);
                            preview_cetak_surat(m_cetak +'?search=&CoopTrainingsID='+ Ext.getCmp('CoopTrainingsID').getValue()+ '&DayNumber=' + DayNumber + '&result=1');
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

    function displayBeforeCetakAttendanceList() {
        if (!winBeforeCetakAttendanceList.isVisible()) {
            winBeforeCetakAttendanceList.show();
        } else {
            winBeforeCetakAttendanceList.hide(this, function() {
            });
            winBeforeCetakAttendanceList.toFront();
        }
         Ext.getCmp('printAttendanceList').setTitle(lang('Print Attendance List'));
    }
    //end training
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

function isNumber(n) {
    return !isNaN(parseFloat(n)) && isFinite(n);
}