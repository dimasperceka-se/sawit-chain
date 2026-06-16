/*
* @Author: nikolius
* @Date:   2016-08-29 10:05:16
* @Last Modified by:   nikolius
* @Last Modified time: 2016-12-28 16:51:40
*/
Ext.onReady(function() {
    Ext.tip.QuickTipManager.init();

    //cek apakah ada sce terselect
    if(m_SceID == ""){
        window.location = m_base_url+'prog_sce/profile';
        /*Ext.MessageBox.show({
            title: 'Notifications',
            msg: 'Failed to get data. No Professional Farmer selected',
            buttons: Ext.MessageBox.OK,
            animateTarget: 'mb9',
            icon: 'ext-mb-info'
        });*/
    }

    var cRowEditing = Ext.create('Ext.grid.plugin.RowEditing', {
        id: 'cRowEditing',
        clicksToMoveEditor: 0,
        autoCancel: false,
        errorSummary: false,
        clicksToEdit: 2
    });

    var mRowEditing = Ext.create('Ext.grid.plugin.RowEditing', {
        id: 'cRowEditing',
        clicksToMoveEditor: 0,
        autoCancel: false,
        errorSummary: false,
        clicksToEdit: 2
    });

    var mc_combo_gardennr = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'label'],
        autoLoad: true,
        proxy: {
            type: 'ajax',
            url: m_api + '/prog_sce/gardennr_combo',
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });

    Ext.define('penjualan.Model', {
        extend: 'Ext.data.Model',
        fields: ['id', 'Buyer', 'Volume', 'Price', 'Total', 'DateTransaction', 'CloneTypeID', 'CloneTypeName'],
    });
    var store_clonal_penjualan = Ext.create('Ext.data.Store', {
        model: 'penjualan.Model',
        autoLoad: false,
        pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_api + '/prog_sce/clonal_penjualan',
            reader: {
                type: 'json',
                root: 'data',
                totalProperty: 'total'
            }
        }
    });
    store_clonal_penjualan.on('beforeload', function() {
        var proxy = store_clonal_penjualan.getProxy();
        proxy.setExtraParam('ClonalID', Ext.getCmp('ClonalID').getValue());
    });

    Ext.define('monitoring.Model', {
        extend: 'Ext.data.Model',
        fields: ['id', 'MonitoringDate', 'MonitoringStatus', 'Description'],
    });
    var store_clonal_monitoring = Ext.create('Ext.data.Store', {
        model: 'monitoring.Model',
        autoLoad: false,
        pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_api + '/prog_sce/clonal_monitoring',
            reader: {
                type: 'json',
                root: 'data',
                totalProperty: 'total'
            }
        }
    });
    store_clonal_monitoring.on('beforeload', function() {
        var proxy = store_clonal_monitoring.getProxy();
        proxy.setExtraParam('ClonalID', Ext.getCmp('ClonalID').getValue());
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
            'label': 'Dll'
        }, {
            'label': 'Pemerintah'
        }],
    });

    var mc_clone_type_combo = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'label'],
        autoLoad: true,
        proxy: {
            type: 'ajax',
            url: m_api + '/cpg/clone_ref_combo',
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });

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

    function act_clonal_status(val) {
        if (val != 'Tidak Berjalan') {
            //Ext.getCmp('mDescription').allowBlank = true;
            Ext.getCmp('mDescription').getStore().loadData(['']);
        } else {
            //Ext.getCmp('mDescription').allowBlank = false;
            Ext.getCmp('mDescription').getStore().loadData([
                [lang('Masalah air/Penyakit')],
                [lang('Rusak')],
                [lang('Tidak ada pemeliharaan/Konflik anggota kelompok')],
                [lang('Tidak ada pasar penjualan')]
            ]);
        }
    }

    function cekGardenNrSel(){
        if(Ext.getCmp('GardenNr').getValue() != "-1"){
            return true;
        }else{
            Ext.MessageBox.show({
                title: 'Notifications',
                msg: 'No Garden Nr selected',
                buttons: Ext.MessageBox.OK,
                animateTarget: 'mb9',
                icon: 'ext-mb-info'
            });
            return false;
        }
    }

    function cekClonalSave(){
        if(Ext.getCmp('ClonalID').getValue() != "-1"){
            return true;
        }else{
            Ext.MessageBox.show({
                title: 'Notifications',
                msg: 'Please save clonal garden first!',
                buttons: Ext.MessageBox.OK,
                animateTarget: 'mb9',
                icon: 'ext-mb-info'
            });
            return false;
        }
    }

    function CheckClonalGarden(thi){
        if(thi!=undefined){
            if (thi.value == '1' || thi.value != '') Ext.getCmp(thi.id + 'Nr').setDisabled(false)
            else Ext.getCmp(thi.id + 'Nr').setDisabled(true)
        }
    }

    function JumlahClonalGarden(){
        var total = eval((isNaN(parseFloat(Ext.getCmp('FCGTSH858Nr').getValue()))?0:parseFloat(Ext.getCmp('FCGTSH858Nr').getValue()))
                    + (isNaN(parseFloat(Ext.getCmp('FCGRCC70Nr').getValue()))?0:parseFloat(Ext.getCmp('FCGRCC70Nr').getValue()))
                    + (isNaN(parseFloat(Ext.getCmp('FCGRCC71Nr').getValue()))?0:parseFloat(Ext.getCmp('FCGRCC71Nr').getValue()))
                    + (isNaN(parseFloat(Ext.getCmp('FCGRCC72Nr').getValue()))?0:parseFloat(Ext.getCmp('FCGRCC72Nr').getValue()))
                    + (isNaN(parseFloat(Ext.getCmp('FCGRCC73Nr').getValue()))?0:parseFloat(Ext.getCmp('FCGRCC73Nr').getValue()))
                    + (isNaN(parseFloat(Ext.getCmp('FCGLocalNr').getValue()))?0:parseFloat(Ext.getCmp('FCGLocalNr').getValue()))
                    + (isNaN(parseFloat(Ext.getCmp('FCGS1Nr').getValue()))?0:parseFloat(Ext.getCmp('FCGS1Nr').getValue()))
                    + (isNaN(parseFloat(Ext.getCmp('FCGS2Nr').getValue()))?0:parseFloat(Ext.getCmp('FCGS2Nr').getValue()))
                    + (isNaN(parseFloat(Ext.getCmp('FCGICCRI3Nr').getValue()))?0:parseFloat(Ext.getCmp('FCGICCRI3Nr').getValue()))
                    + (isNaN(parseFloat(Ext.getCmp('FCGICCRI4Nr').getValue()))?0:parseFloat(Ext.getCmp('FCGICCRI4Nr').getValue()))
                    + (isNaN(parseFloat(Ext.getCmp('FCGICCRI5Nr').getValue()))?0:parseFloat(Ext.getCmp('FCGICCRI5Nr').getValue()))
                    + (isNaN(parseFloat(Ext.getCmp('FCGRCLNr').getValue()))?0:parseFloat(Ext.getCmp('FCGRCLNr').getValue()))
                    + (isNaN(parseFloat(Ext.getCmp('FCGM01Nr').getValue()))?0:parseFloat(Ext.getCmp('FCGM01Nr').getValue()))
                    + (isNaN(parseFloat(Ext.getCmp('FCGM06Nr').getValue()))?0:parseFloat(Ext.getCmp('FCGM06Nr').getValue()))
                    + (isNaN(parseFloat(Ext.getCmp('FCGTHRNr').getValue()))?0:parseFloat(Ext.getCmp('FCGTHRNr').getValue()))
                    + (isNaN(parseFloat(Ext.getCmp('FCGCG45Nr').getValue()))?0:parseFloat(Ext.getCmp('FCGCG45Nr').getValue()))
                    + (isNaN(parseFloat(Ext.getCmp('FCGScavinaNr').getValue()))?0:parseFloat(Ext.getCmp('FCGScavinaNr').getValue()))
                    + (isNaN(parseFloat(Ext.getCmp('FCGBLBNr').getValue()))?0:parseFloat(Ext.getCmp('FCGBLBNr').getValue()))
                    + (isNaN(parseFloat(Ext.getCmp('FCGBB01Nr').getValue()))?0:parseFloat(Ext.getCmp('FCGBB01Nr').getValue()))
                    + (isNaN(parseFloat(Ext.getCmp('FCGM04Nr').getValue()))?0:parseFloat(Ext.getCmp('FCGM04Nr').getValue()))
                    + (isNaN(parseFloat(Ext.getCmp('FCGMTNr').getValue()))?0:parseFloat(Ext.getCmp('FCGMTNr').getValue()))
                    + (isNaN(parseFloat(Ext.getCmp('FCGM02Nr').getValue()))?0:parseFloat(Ext.getCmp('FCGM02Nr').getValue()))
                    + (isNaN(parseFloat(Ext.getCmp('FCGAPNr').getValue()))?0:parseFloat(Ext.getCmp('FCGAPNr').getValue()))
                    + (isNaN(parseFloat(Ext.getCmp('FCGPRNr').getValue()))?0:parseFloat(Ext.getCmp('FCGPRNr').getValue()))
                    + (isNaN(parseFloat(Ext.getCmp('FCGBRTNr').getValue()))?0:parseFloat(Ext.getCmp('FCGBRTNr').getValue()))
                    + (isNaN(parseFloat(Ext.getCmp('FCGMHP03Nr').getValue()))?0:parseFloat(Ext.getCmp('FCGMHP03Nr').getValue()))
                    + (isNaN(parseFloat(Ext.getCmp('FCGMHP04Nr').getValue()))?0:parseFloat(Ext.getCmp('FCGMHP04Nr').getValue()))

                    + (isNaN(parseFloat(Ext.getCmp('FCGOtherClonesNr').getValue()))?0:parseFloat(Ext.getCmp('FCGOtherClonesNr').getValue()))
                    );
        Ext.getCmp('FCGTotalClonesNr').setValue(total);
    }

    function JumlahShadeTrees(){
        var total = eval((isNaN(parseFloat(Ext.getCmp('CoconutNr').getValue()))?0:parseFloat(Ext.getCmp('CoconutNr').getValue()))
                    + (isNaN(parseFloat(Ext.getCmp('ArecaPalmNr').getValue()))?0:parseFloat(Ext.getCmp('ArecaPalmNr').getValue()))
                    + (isNaN(parseFloat(Ext.getCmp('RubberNr').getValue()))?0:parseFloat(Ext.getCmp('RubberNr').getValue()))
                    + (isNaN(parseFloat(Ext.getCmp('CloveNr').getValue()))?0:parseFloat(Ext.getCmp('CloveNr').getValue()))
                    + (isNaN(parseFloat(Ext.getCmp('CashewNr').getValue()))?0:parseFloat(Ext.getCmp('CashewNr').getValue()))
                    + (isNaN(parseFloat(Ext.getCmp('OilPalmNr').getValue()))?0:parseFloat(Ext.getCmp('OilPalmNr').getValue()))
                    + (isNaN(parseFloat(Ext.getCmp('SugarPalmNr').getValue()))?0:parseFloat(Ext.getCmp('SugarPalmNr').getValue()))
                    + (isNaN(parseFloat(Ext.getCmp('NutmegNr').getValue()))?0:parseFloat(Ext.getCmp('NutmegNr').getValue()))
                    + (isNaN(parseFloat(Ext.getCmp('HazelnutNr').getValue()))?0:parseFloat(Ext.getCmp('HazelnutNr').getValue()))
                    + (isNaN(parseFloat(Ext.getCmp('FCGKapokNr').getValue()))?0:parseFloat(Ext.getCmp('FCGKapokNr').getValue()))
                    + (isNaN(parseFloat(Ext.getCmp('MahagonyNr').getValue()))?0:parseFloat(Ext.getCmp('MahagonyNr').getValue()))
                    + (isNaN(parseFloat(Ext.getCmp('TeakNr').getValue()))?0:parseFloat(Ext.getCmp('TeakNr').getValue()))
                    + (isNaN(parseFloat(Ext.getCmp('VitexNr').getValue()))?0:parseFloat(Ext.getCmp('VitexNr').getValue()))
                    + (isNaN(parseFloat(Ext.getCmp('ErmerillaNr').getValue()))?0:parseFloat(Ext.getCmp('ErmerillaNr').getValue()))
                    + (isNaN(parseFloat(Ext.getCmp('AnthocephalusNr').getValue()))?0:parseFloat(Ext.getCmp('AnthocephalusNr').getValue()))
                    + (isNaN(parseFloat(Ext.getCmp('AlbiziaNr').getValue()))?0:parseFloat(Ext.getCmp('AlbiziaNr').getValue()))
                    + (isNaN(parseFloat(Ext.getCmp('JackfruitNr').getValue()))?0:parseFloat(Ext.getCmp('JackfruitNr').getValue()))
                    + (isNaN(parseFloat(Ext.getCmp('BananaNr').getValue()))?0:parseFloat(Ext.getCmp('BananaNr').getValue()))
                    + (isNaN(parseFloat(Ext.getCmp('FCGRambutanNr').getValue()))?0:parseFloat(Ext.getCmp('FCGRambutanNr').getValue()))
                    + (isNaN(parseFloat(Ext.getCmp('MangoNr').getValue()))?0:parseFloat(Ext.getCmp('MangoNr').getValue()))
                    + (isNaN(parseFloat(Ext.getCmp('SpondiasDulcisNr').getValue()))?0:parseFloat(Ext.getCmp('SpondiasDulcisNr').getValue()))
                    + (isNaN(parseFloat(Ext.getCmp('FCGLangsatNr').getValue()))?0:parseFloat(Ext.getCmp('FCGLangsatNr').getValue()))
                    + (isNaN(parseFloat(Ext.getCmp('FCGDurianNr').getValue()))?0:parseFloat(Ext.getCmp('FCGDurianNr').getValue()))
                    + (isNaN(parseFloat(Ext.getCmp('GuavaNr').getValue()))?0:parseFloat(Ext.getCmp('GuavaNr').getValue()))
                    + (isNaN(parseFloat(Ext.getCmp('AvocadoNr').getValue()))?0:parseFloat(Ext.getCmp('AvocadoNr').getValue()))
                    + (isNaN(parseFloat(Ext.getCmp('FCGCempedakNr').getValue()))?0:parseFloat(Ext.getCmp('FCGCempedakNr').getValue()))
                    + (isNaN(parseFloat(Ext.getCmp('BreadfruitNr').getValue()))?0:parseFloat(Ext.getCmp('BreadfruitNr').getValue()))
                    + (isNaN(parseFloat(Ext.getCmp('PapayaNr').getValue()))?0:parseFloat(Ext.getCmp('PapayaNr').getValue()))
                    + (isNaN(parseFloat(Ext.getCmp('MangosteenNr').getValue()))?0:parseFloat(Ext.getCmp('MangosteenNr').getValue()))
                    + (isNaN(parseFloat(Ext.getCmp('CitrusNr').getValue()))?0:parseFloat(Ext.getCmp('CitrusNr').getValue()))
                    + (isNaN(parseFloat(Ext.getCmp('GliricidiaNr').getValue()))?0:parseFloat(Ext.getCmp('GliricidiaNr').getValue()))
                    + (isNaN(parseFloat(Ext.getCmp('LeucaenaNr').getValue()))?0:parseFloat(Ext.getCmp('LeucaenaNr').getValue()))
                    + (isNaN(parseFloat(Ext.getCmp('ParkiaNr').getValue()))?0:parseFloat(Ext.getCmp('ParkiaNr').getValue()))
                    + (isNaN(parseFloat(Ext.getCmp('ArchidendronNr').getValue()))?0:parseFloat(Ext.getCmp('ArchidendronNr').getValue()))
                    );
        Ext.getCmp('TotalShadeTreesNr').setValue(total);
    }

    function setFillForm(comboLabel,objForm){
        //console.log(comboLabel);
        var strTemp = comboLabel.split("@");
        var GardenNr = strTemp[0];
        var ClonalID = strTemp[1];

        if(ClonalID == '-'){
            //insert (ambil data dari garden)
            console.log('insert');

            Ext.Ajax.request({
                url: m_api + '/prog_sce/clonal_get_farmer_garden',
                method: 'GET',
                params: {
                    GardenNr : GardenNr
                },
                success: function(fp, o) {
                    var r = Ext.decode(fp.responseText);

                    Ext.getCmp('SurveyNr').setValue(r.SurveyNr);

                    Ext.getCmp('EstablishedYear').setValue(r.EstablishedYear);
                    if(r.CertificationStatus == 'Yes'){
                        Ext.getCmp('CertificationStatus1').setValue(true);
                    }else{
                        Ext.getCmp('CertificationStatus2').setValue(true);
                    }

                    Ext.getCmp('DateAppliedCertification').setValue(r.DateAppliedCertification);
                    Ext.getCmp('DateReceivedCertification').setValue(r.DateReceivedCertification);

                    if(r.LandCertificate=='1') Ext.getCmp('LandCertificate1').setValue(true);
                    if(r.LandCertificate=='2') Ext.getCmp('LandCertificate2').setValue(true);
                    if(r.LandCertificate=='3') Ext.getCmp('LandCertificate3').setValue(true);
                    if(r.LandCertificate=='4') Ext.getCmp('LandCertificate4').setValue(true);
                    if(r.LandCertificate=='5') Ext.getCmp('LandCertificate5').setValue(true);

                    Ext.getCmp('Area').setValue(r.Area);
                    Ext.getCmp('ClonalGardenLatitude').setValue(r.Latitude);
                    Ext.getCmp('ClonalGardenLongitude').setValue(r.Longitude);

                    if(r.TSH858=='1') Ext.getCmp('FCGTSH858').setValue(true);
                    Ext.getCmp('FCGTSH858Nr').setValue(r.TSH858Nr);
                    if(r.RCC70=='1') Ext.getCmp('FCGRCC70').setValue(true);
                    Ext.getCmp('FCGRCC70Nr').setValue(r.RCC70Nr);
                    if(r.RCC71=='1') Ext.getCmp('FCGRCC71').setValue(true);
                    Ext.getCmp('FCGRCC71Nr').setValue(r.RCC71Nr);
                    if(r.RCC72=='1') Ext.getCmp('FCGRCC72').setValue(true);
                    Ext.getCmp('FCGRCC72Nr').setValue(r.RCC72Nr);
                    if(r.RCC73=='1') Ext.getCmp('FCGRCC73').setValue(true);
                    Ext.getCmp('FCGRCC73Nr').setValue(r.RCC73Nr);
                    if(r.LOCAL=='1') Ext.getCmp('FCGLocal').setValue(true);
                    Ext.getCmp('FCGLocalNr').setValue(r.LocalNr);
                    if(r.S1=='1') Ext.getCmp('FCGS1').setValue(true);
                    Ext.getCmp('FCGS1Nr').setValue(r.S1Nr);
                    if(r.S2=='1') Ext.getCmp('FCGS2').setValue(true);
                    Ext.getCmp('FCGS2Nr').setValue(r.S2Nr);
                    if(r.ICCRI3=='1') Ext.getCmp('FCGICCRI3').setValue(true);
                    Ext.getCmp('FCGICCRI3Nr').setValue(r.ICCRI3Nr);
                    if(r.ICCRI4=='1') Ext.getCmp('FCGICCRI4').setValue(true);
                    Ext.getCmp('FCGICCRI4Nr').setValue(r.ICCRI4Nr);
                    if(r.ICCRI5=='1') Ext.getCmp('FCGICCRI5').setValue(true);
                    Ext.getCmp('FCGICCRI5Nr').setValue(r.ICCRI5Nr);
                    if(r.RCL=='1') Ext.getCmp('FCGRCL').setValue(true);
                    Ext.getCmp('FCGRCLNr').setValue(r.RCLNr);
                    if(r.M01=='1') Ext.getCmp('FCGM01').setValue(true);
                    Ext.getCmp('FCGM01Nr').setValue(r.M01Nr);
                    if(r.M06=='1') Ext.getCmp('FCGM06').setValue(true);
                    Ext.getCmp('FCGM06Nr').setValue(r.M06Nr);
                    if(r.THR=='1') Ext.getCmp('FCGTHR').setValue(true);
                    Ext.getCmp('FCGTHRNr').setValue(r.THRNr);
                    if(r.CG45=='1') Ext.getCmp('FCGCG45').setValue(true);
                    Ext.getCmp('FCGCG45Nr').setValue(r.CG45Nr);
                    if(r.Scavina=='1') Ext.getCmp('FCGScavina').setValue(true);
                    Ext.getCmp('FCGScavinaNr').setValue(r.ScavinaNr);
                    if(r.BLB=='1') Ext.getCmp('FCGBLB').setValue(true);
                    Ext.getCmp('FCGBLBNr').setValue(r.BLBNr);
                    if(r.M04=='1') Ext.getCmp('FCGM04').setValue(true);
                    Ext.getCmp('FCGM04Nr').setValue(r.M04Nr);
                    if(r.MT=='1') Ext.getCmp('FCGMT').setValue(true);
                    Ext.getCmp('FCGMTNr').setValue(r.MTNr);
                    if(r.M02=='1') Ext.getCmp('FCGM02').setValue(true);
                    Ext.getCmp('FCGM02Nr').setValue(r.M02Nr);
                    if(r.AP=='1') Ext.getCmp('FCGAP').setValue(true);
                    Ext.getCmp('FCGAPNr').setValue(r.APNr);
                    if(r.PR=='1') Ext.getCmp('FCGPR').setValue(true);
                    Ext.getCmp('FCGPRNr').setValue(r.PRNr);
                    if(r.BRT=='1') Ext.getCmp('FCGBRT').setValue(true);
                    Ext.getCmp('FCGBRTNr').setValue(r.BRTNr);
                    if(r.MHP03=='1') Ext.getCmp('FCGMHP03').setValue(true);
                    Ext.getCmp('FCGMHP03Nr').setValue(r.MHP03Nr);
                    if(r.MHP04=='1') Ext.getCmp('FCGMHP04').setValue(true);
                    Ext.getCmp('FCGMHP04Nr').setValue(r.MHP04Nr);
                    if(r.BB01=='1') Ext.getCmp('FCGBB01').setValue(true);
                    Ext.getCmp('FCGBB01Nr').setValue(r.BB01Nr);

                    Ext.getCmp('FCGOtherClones').setValue(r.OtherClones);
                    Ext.getCmp('FCGOtherClonesNr').setValue(r.OtherClonesNr);

                    if(r.Coconut=='1') Ext.getCmp('Coconut').setValue(true);
                    Ext.getCmp('CoconutNr').setValue(r.CoconutNr);
                    if(r.ArecaPalm=='1') Ext.getCmp('ArecaPalm').setValue(true);
                    Ext.getCmp('ArecaPalmNr').setValue(r.ArecaPalmNr);
                    if(r.Rubber=='1') Ext.getCmp('Rubber').setValue(true);
                    Ext.getCmp('RubberNr').setValue(r.RubberNr);
                    if(r.Clove=='1') Ext.getCmp('Clove').setValue(true);
                    Ext.getCmp('CloveNr').setValue(r.CloveNr);
                    if(r.Cashew=='1') Ext.getCmp('Cashew').setValue(true);
                    Ext.getCmp('CashewNr').setValue(r.CashewNr);
                    if(r.OilPalm=='1') Ext.getCmp('OilPalm').setValue(true);
                    Ext.getCmp('OilPalmNr').setValue(r.OilPalmNr);
                    if(r.SugarPalm=='1') Ext.getCmp('SugarPalm').setValue(true);
                    Ext.getCmp('SugarPalmNr').setValue(r.SugarPalmNr);
                    if(r.Nutmeg=='1') Ext.getCmp('Nutmeg').setValue(true);
                    Ext.getCmp('NutmegNr').setValue(r.NutmegNr);
                    if(r.Hazelnut=='1') Ext.getCmp('Hazelnut').setValue(true);
                    Ext.getCmp('HazelnutNr').setValue(r.HazelnutNr);
                    if(r.Kapok=='1') Ext.getCmp('FCGKapok').setValue(true);
                    Ext.getCmp('FCGKapokNr').setValue(r.KapokNr);

                    if(r.Mahagony=='1') Ext.getCmp('Mahagony').setValue(true);
                    Ext.getCmp('MahagonyNr').setValue(r.MahagonyNr);
                    if(r.Teak=='1') Ext.getCmp('Teak').setValue(true);
                    Ext.getCmp('TeakNr').setValue(r.TeakNr);
                    if(r.Vitex=='1') Ext.getCmp('Vitex').setValue(true);
                    Ext.getCmp('VitexNr').setValue(r.VitexNr);
                    if(r.Ermerilla=='1') Ext.getCmp('Ermerilla').setValue(true);
                    Ext.getCmp('ErmerillaNr').setValue(r.ErmerillaNr);
                    if(r.Anthocephalus=='1') Ext.getCmp('Anthocephalus').setValue(true);
                    Ext.getCmp('AnthocephalusNr').setValue(r.AnthocephalusNr);
                    if(r.Albizia=='1') Ext.getCmp('Albizia').setValue(true);
                    Ext.getCmp('AlbiziaNr').setValue(r.AlbiziaNr);

                    if(r.Jackfruit=='1') Ext.getCmp('Jackfruit').setValue(true);
                    Ext.getCmp('JackfruitNr').setValue(r.JackfruitNr);
                    if(r.Banana=='1') Ext.getCmp('Banana').setValue(true);
                    Ext.getCmp('BananaNr').setValue(r.BananaNr);
                    if(r.Rambutan=='1') Ext.getCmp('FCGRambutan').setValue(true);
                    Ext.getCmp('FCGRambutanNr').setValue(r.RambutanNr);
                    if(r.Mango=='1') Ext.getCmp('Mango').setValue(true);
                    Ext.getCmp('MangoNr').setValue(r.MangoNr);
                    if(r.SpondiasDulcis=='1') Ext.getCmp('SpondiasDulcis').setValue(true);
                    Ext.getCmp('SpondiasDulcisNr').setValue(r.SpondiasDulcisNr);
                    if(r.Langsat=='1') Ext.getCmp('FCGLangsat').setValue(true);
                    Ext.getCmp('FCGLangsatNr').setValue(r.LangsatNr);
                    if(r.Durian=='1') Ext.getCmp('FCGDurian').setValue(true);
                    Ext.getCmp('FCGDurianNr').setValue(r.DurianNr);
                    if(r.Guava=='1') Ext.getCmp('Guava').setValue(true);
                    Ext.getCmp('GuavaNr').setValue(r.GuavaNr);
                    if(r.Avocado=='1') Ext.getCmp('Avocado').setValue(true);
                    Ext.getCmp('AvocadoNr').setValue(r.AvocadoNr);
                    if(r.Cempedak=='1') Ext.getCmp('FCGCempedak').setValue(true);
                    Ext.getCmp('FCGCempedakNr').setValue(r.CempedakNr);
                    if(r.Breadfruit=='1') Ext.getCmp('Breadfruit').setValue(true);
                    Ext.getCmp('BreadfruitNr').setValue(r.BreadfruitNr);
                    if(r.Papaya=='1') Ext.getCmp('Papaya').setValue(true);
                    Ext.getCmp('PapayaNr').setValue(r.PapayaNr);
                    if(r.Mangosteen=='1') Ext.getCmp('Mangosteen').setValue(true);
                    Ext.getCmp('MangosteenNr').setValue(r.MangosteenNr);
                    if(r.Citrus=='1') Ext.getCmp('Citrus').setValue(true);
                    Ext.getCmp('CitrusNr').setValue(r.CitrusNr);

                    if(r.Gliricidia=='1') Ext.getCmp('Gliricidia').setValue(true);
                    Ext.getCmp('GliricidiaNr').setValue(r.GliricidiaNr);
                    if(r.Leucaena=='1') Ext.getCmp('Leucaena').setValue(true);
                    Ext.getCmp('LeucaenaNr').setValue(r.LeucaenaNr);
                    if(r.Parkia=='1') Ext.getCmp('Parkia').setValue(true);
                    Ext.getCmp('ParkiaNr').setValue(r.ParkiaNr);
                    if(r.Archidendron=='1') Ext.getCmp('Archidendron').setValue(true);
                    Ext.getCmp('ArchidendronNr').setValue(r.ArchidendronNr);

                    //load store transaksi here
                    store_clonal_penjualan.load();
                    store_clonal_monitoring.load();
                },
                failure: function(fp, o) {
                    var obj = Ext.decode(response.responseText);
                    Ext.MessageBox.alert('Warning', 'Data not found');
                }
            });

        }else{
            //update (ambil data dari clonal garden)
            console.log('update');

            Ext.Ajax.request({
                url: m_api + '/prog_sce/clonal_get_clonal_garden',
                method: 'GET',
                params: {
                    GardenNr : GardenNr,
                    ClonalID : ClonalID
                },
                success: function(fp, o) {
                    var r = Ext.decode(fp.responseText);
                    //console.log(r);

                    Ext.getCmp('ClonalID').setValue(r.ClonalID);

                    Ext.getCmp('EstablishedYear').setValue(r.EstablishedYear);
                    if(r.CertificationStatus == 'Yes'){
                        Ext.getCmp('CertificationStatus1').setValue(true);
                    }else{
                        Ext.getCmp('CertificationStatus2').setValue(true);
                    }

                    Ext.getCmp('DateAppliedCertification').setValue(r.DateAppliedCertification);
                    Ext.getCmp('DateReceivedCertification').setValue(r.DateReceivedCertification);

                    if(r.LandCertificate=='1') Ext.getCmp('LandCertificate1').setValue(true);
                    if(r.LandCertificate=='2') Ext.getCmp('LandCertificate2').setValue(true);
                    if(r.LandCertificate=='3') Ext.getCmp('LandCertificate3').setValue(true);
                    if(r.LandCertificate=='4') Ext.getCmp('LandCertificate4').setValue(true);
                    if(r.LandCertificate=='5') Ext.getCmp('LandCertificate5').setValue(true);

                    Ext.getCmp('Area').setValue(r.Area);
                    Ext.getCmp('ClonalGardenLatitude').setValue(r.Latitude);
                    Ext.getCmp('ClonalGardenLongitude').setValue(r.Longitude);

                    if(r.TSH858=='1') Ext.getCmp('FCGTSH858').setValue(true);
                    Ext.getCmp('FCGTSH858Nr').setValue(r.TSH858Nr);
                    if(r.RCC70=='1') Ext.getCmp('FCGRCC70').setValue(true);
                    Ext.getCmp('FCGRCC70Nr').setValue(r.RCC70Nr);
                    if(r.RCC71=='1') Ext.getCmp('FCGRCC71').setValue(true);
                    Ext.getCmp('FCGRCC71Nr').setValue(r.RCC71Nr);
                    if(r.RCC72=='1') Ext.getCmp('FCGRCC72').setValue(true);
                    Ext.getCmp('FCGRCC72Nr').setValue(r.RCC72Nr);
                    if(r.RCC73=='1') Ext.getCmp('FCGRCC73').setValue(true);
                    Ext.getCmp('FCGRCC73Nr').setValue(r.RCC73Nr);
                    if(r.Local=='1') Ext.getCmp('FCGLocal').setValue(true);
                    Ext.getCmp('FCGLocalNr').setValue(r.LocalNr);
                    if(r.S1=='1') Ext.getCmp('FCGS1').setValue(true);
                    Ext.getCmp('FCGS1Nr').setValue(r.S1Nr);
                    if(r.S2=='1') Ext.getCmp('FCGS2').setValue(true);
                    Ext.getCmp('FCGS2Nr').setValue(r.S2Nr);
                    if(r.ICCRI3=='1') Ext.getCmp('FCGICCRI3').setValue(true);
                    Ext.getCmp('FCGICCRI3Nr').setValue(r.ICCRI3Nr);
                    if(r.ICCRI4=='1') Ext.getCmp('FCGICCRI4').setValue(true);
                    Ext.getCmp('FCGICCRI4Nr').setValue(r.ICCRI4Nr);
                    if(r.ICCRI5=='1') Ext.getCmp('FCGICCRI5').setValue(true);
                    Ext.getCmp('FCGICCRI5Nr').setValue(r.ICCRI5Nr);
                    if(r.RCL=='1') Ext.getCmp('FCGRCL').setValue(true);
                    Ext.getCmp('FCGRCLNr').setValue(r.RCLNr);
                    if(r.M01=='1') Ext.getCmp('FCGM01').setValue(true);
                    Ext.getCmp('FCGM01Nr').setValue(r.M01Nr);
                    if(r.M06=='1') Ext.getCmp('FCGM06').setValue(true);
                    Ext.getCmp('FCGM06Nr').setValue(r.M06Nr);
                    if(r.THR=='1') Ext.getCmp('FCGTHR').setValue(true);
                    Ext.getCmp('FCGTHRNr').setValue(r.THRNr);
                    if(r.CG45=='1') Ext.getCmp('FCGCG45').setValue(true);
                    Ext.getCmp('FCGCG45Nr').setValue(r.CG45Nr);
                    if(r.Scavina=='1') Ext.getCmp('FCGScavina').setValue(true);
                    Ext.getCmp('FCGScavinaNr').setValue(r.ScavinaNr);
                    if(r.BLB=='1') Ext.getCmp('FCGBLB').setValue(true);
                    Ext.getCmp('FCGBLBNr').setValue(r.BLBNr);
                    if(r.M04=='1') Ext.getCmp('FCGM04').setValue(true);
                    Ext.getCmp('FCGM04Nr').setValue(r.M04Nr);
                    if(r.MT=='1') Ext.getCmp('FCGMT').setValue(true);
                    Ext.getCmp('FCGMTNr').setValue(r.MTNr);
                    if(r.M02=='1') Ext.getCmp('FCGM02').setValue(true);
                    Ext.getCmp('FCGM02Nr').setValue(r.M02Nr);
                    if(r.AP=='1') Ext.getCmp('FCGAP').setValue(true);
                    Ext.getCmp('FCGAPNr').setValue(r.APNr);
                    if(r.PR=='1') Ext.getCmp('FCGPR').setValue(true);
                    Ext.getCmp('FCGPRNr').setValue(r.PRNr);
                    if(r.BRT=='1') Ext.getCmp('FCGBRT').setValue(true);
                    Ext.getCmp('FCGBRTNr').setValue(r.BRTNr);
                    if(r.MHP03=='1') Ext.getCmp('FCGMHP03').setValue(true);
                    Ext.getCmp('FCGMHP03Nr').setValue(r.MHP03Nr);
                    if(r.MHP04=='1') Ext.getCmp('FCGMHP04').setValue(true);
                    Ext.getCmp('FCGMHP04Nr').setValue(r.MHP04Nr);
                    if(r.BB01=='1') Ext.getCmp('FCGBB01').setValue(true);
                    Ext.getCmp('FCGBB01Nr').setValue(r.BB01Nr);

                    Ext.getCmp('FCGOtherClones').setValue(r.OtherClones);
                    Ext.getCmp('FCGOtherClonesNr').setValue(r.OtherClonesNr);

                    if(r.Coconut=='1') Ext.getCmp('Coconut').setValue(true);
                    Ext.getCmp('CoconutNr').setValue(r.CoconutNr);
                    if(r.ArecaPalm=='1') Ext.getCmp('ArecaPalm').setValue(true);
                    Ext.getCmp('ArecaPalmNr').setValue(r.ArecaPalmNr);
                    if(r.Rubber=='1') Ext.getCmp('Rubber').setValue(true);
                    Ext.getCmp('RubberNr').setValue(r.RubberNr);
                    if(r.Clove=='1') Ext.getCmp('Clove').setValue(true);
                    Ext.getCmp('CloveNr').setValue(r.CloveNr);
                    if(r.Cashew=='1') Ext.getCmp('Cashew').setValue(true);
                    Ext.getCmp('CashewNr').setValue(r.CashewNr);
                    if(r.OilPalm=='1') Ext.getCmp('OilPalm').setValue(true);
                    Ext.getCmp('OilPalmNr').setValue(r.OilPalmNr);
                    if(r.SugarPalm=='1') Ext.getCmp('SugarPalm').setValue(true);
                    Ext.getCmp('SugarPalmNr').setValue(r.SugarPalmNr);
                    if(r.Nutmeg=='1') Ext.getCmp('Nutmeg').setValue(true);
                    Ext.getCmp('NutmegNr').setValue(r.NutmegNr);
                    if(r.Hazelnut=='1') Ext.getCmp('Hazelnut').setValue(true);
                    Ext.getCmp('HazelnutNr').setValue(r.HazelnutNr);
                    if(r.Kapok=='1') Ext.getCmp('FCGKapok').setValue(true);
                    Ext.getCmp('FCGKapokNr').setValue(r.KapokNr);

                    if(r.Mahagony=='1') Ext.getCmp('Mahagony').setValue(true);
                    Ext.getCmp('MahagonyNr').setValue(r.MahagonyNr);
                    if(r.Teak=='1') Ext.getCmp('Teak').setValue(true);
                    Ext.getCmp('TeakNr').setValue(r.TeakNr);
                    if(r.Vitex=='1') Ext.getCmp('Vitex').setValue(true);
                    Ext.getCmp('VitexNr').setValue(r.VitexNr);
                    if(r.Ermerilla=='1') Ext.getCmp('Ermerilla').setValue(true);
                    Ext.getCmp('ErmerillaNr').setValue(r.ErmerillaNr);
                    if(r.Anthocephalus=='1') Ext.getCmp('Anthocephalus').setValue(true);
                    Ext.getCmp('AnthocephalusNr').setValue(r.AnthocephalusNr);
                    if(r.Albizia=='1') Ext.getCmp('Albizia').setValue(true);
                    Ext.getCmp('AlbiziaNr').setValue(r.AlbiziaNr);

                    if(r.Jackfruit=='1') Ext.getCmp('Jackfruit').setValue(true);
                    Ext.getCmp('JackfruitNr').setValue(r.JackfruitNr);
                    if(r.Banana=='1') Ext.getCmp('Banana').setValue(true);
                    Ext.getCmp('BananaNr').setValue(r.BananaNr);
                    if(r.Rambutan=='1') Ext.getCmp('FCGRambutan').setValue(true);
                    Ext.getCmp('FCGRambutanNr').setValue(r.RambutanNr);
                    if(r.Mango=='1') Ext.getCmp('Mango').setValue(true);
                    Ext.getCmp('MangoNr').setValue(r.MangoNr);
                    if(r.SpondiasDulcis=='1') Ext.getCmp('SpondiasDulcis').setValue(true);
                    Ext.getCmp('SpondiasDulcisNr').setValue(r.SpondiasDulcisNr);
                    if(r.Langsat=='1') Ext.getCmp('FCGLangsat').setValue(true);
                    Ext.getCmp('FCGLangsatNr').setValue(r.LangsatNr);
                    if(r.Durian=='1') Ext.getCmp('FCGDurian').setValue(true);
                    Ext.getCmp('FCGDurianNr').setValue(r.DurianNr);
                    if(r.Guava=='1') Ext.getCmp('Guava').setValue(true);
                    Ext.getCmp('GuavaNr').setValue(r.GuavaNr);
                    if(r.Avocado=='1') Ext.getCmp('Avocado').setValue(true);
                    Ext.getCmp('AvocadoNr').setValue(r.AvocadoNr);
                    if(r.Cempedak=='1') Ext.getCmp('FCGCempedak').setValue(true);
                    Ext.getCmp('FCGCempedakNr').setValue(r.CempedakNr);
                    if(r.Breadfruit=='1') Ext.getCmp('Breadfruit').setValue(true);
                    Ext.getCmp('BreadfruitNr').setValue(r.BreadfruitNr);
                    if(r.Papaya=='1') Ext.getCmp('Papaya').setValue(true);
                    Ext.getCmp('PapayaNr').setValue(r.PapayaNr);
                    if(r.Mangosteen=='1') Ext.getCmp('Mangosteen').setValue(true);
                    Ext.getCmp('MangosteenNr').setValue(r.MangosteenNr);
                    if(r.Citrus=='1') Ext.getCmp('Citrus').setValue(true);
                    Ext.getCmp('CitrusNr').setValue(r.CitrusNr);

                    if(r.Gliricidia=='1') Ext.getCmp('Gliricidia').setValue(true);
                    Ext.getCmp('GliricidiaNr').setValue(r.GliricidiaNr);
                    if(r.Leucaena=='1') Ext.getCmp('Leucaena').setValue(true);
                    Ext.getCmp('LeucaenaNr').setValue(r.LeucaenaNr);
                    if(r.Parkia=='1') Ext.getCmp('Parkia').setValue(true);
                    Ext.getCmp('ParkiaNr').setValue(r.ParkiaNr);
                    if(r.Archidendron=='1') Ext.getCmp('Archidendron').setValue(true);
                    Ext.getCmp('ArchidendronNr').setValue(r.ArchidendronNr);

                    //load store transaksi here
                    store_clonal_penjualan.load();
                    store_clonal_monitoring.load();
                },
                failure: function(fp, o) {
                    Ext.MessageBox.alert('Warning', 'Data not found');
                }
            });

        }
    }

    function hitung_area(){
        Ext.Ajax.request({
            //url: m_coop + '_clonal_garden_area',
            url: m_api + '/prog_sce/update_clonal_garden_area',
            method: 'GET',
            params: {
                ClonalID: Ext.getCmp('ClonalID').getValue(),
            },
            success: function(fp, o) {
                var r = Ext.decode(fp.responseText);
                Ext.getCmp('Area').setValue(r.Area);
                Ext.getCmp('ClonalGardenLatitude').setValue(r.Latitude);
                Ext.getCmp('ClonalGardenLongitude').setValue(r.Longitude);
            }
       })
    }

    function display_area(comboLabel){
        var strTemp = comboLabel.split("@");
        var GardenNr = strTemp[0];
        var ClonalID = strTemp[1];

        var areawindow = Ext.create('widget.window', {
            id : 'areawindow',
            title: lang('Clonal Garden Polygon'),
            closable: true,
            modal:true,
            layout : 'fit',
            closeAction: 'destroy',
            width: '75%',
            height: 550,
            bodyPadding: 5,
            listeners: {
                close: function(cb, nv, ov) {
                    hitung_area();
                }
            }
        });
        areawindow.show();

        Ext.Ajax.request({
            //url: m_cpg_clonal + '_polygon',
            url: m_api + '/prog_sce/clonal_garden_polygon',
            method: 'GET',
            params: {
                ClonalID: ClonalID,
                GardenNr: GardenNr,
                lati: Ext.getCmp('ClonalGardenLatitude').getValue(),
                longi: Ext.getCmp('ClonalGardenLongitude').getValue(),
                hakAksesPolygon: m_hakakses_polygon
            },
            success: function(response){
                var htmlText = response.responseText;
                areawindow.update(htmlText, true);
            }
        });
    }

    var DataPanel = Ext.create('Ext.form.Panel', {
        title: 'Clonal Garden',
        padding: 0,
        margin: 15,
        height: 2000,
        frame: true,
        bodyStyle: {
            "background-color": "#F0F0F0"
        },
        style: 'background-color:#F0F0F0;',
        bodyPadding: 5,
        id: 'mainPanel',
        fieldDefaults: {
            labelAlign: 'left',
            labelWidth: 220,
            anchor: '95%'
        },
        items:[{
            layout: 'column',
            border: false,
            items: [{
                columnWidth: .5,
                layout: 'form',
                border: false,
                padding: 5,
                items: [{
                    xtype: 'textfield',
                    id: 'ClonalID',
                    name: 'ClonalID',
                    hidden: true
                },{
                    xtype: 'textfield',
                    id: 'SurveyNr',
                    name: 'SurveyNr',
                    hidden: true
                },{
                    fieldLabel: lang('Garden Nr'),
                    id: 'GardenNr',
                    name: 'GardenNr',
                    xtype: 'combo',
                    store: mc_combo_gardennr,
                    displayField: 'label',
                    valueField: 'id',
                    queryMode: 'local',
                    allowBlank: false,
                    listeners: {
                        change: function(cb, nv, ov) {
                            if (this.value != '-1') {
                                setFillForm(nv,this.up('form'));
                            }else{
                                //proses insert

                                //reset all form but GardenNr ============================================
                                var fields = this.up('form').query('[isFormField][name!="GardenNr"]');
                                for (var i = 0, len = fields.length; i < len; i++) {
                                    fields[i].reset();
                                }
                                //reset all form but GardenNr ============================================

                                //reset store transaksi here
                                store_clonal_penjualan.load();
                                store_clonal_monitoring.load();
                            }
                        }
                    }
                },{
                    xtype: 'textfield',
                    fieldLabel: lang('Year Established'),
                    id: 'EstablishedYear',
                    name: 'EstablishedYear',
                    allowBlank:false
                },{
                    xtype: 'radiogroup',
                    fieldLabel: lang('Clonal Garden Certification Status'),
                    items: [{
                        name: 'CertificationStatus',
                        id: 'CertificationStatus1',
                        boxLabel: lang('Yes, BP2MB'),
                        inputValue: 'Yes'
                    }, {
                        name: 'CertificationStatus',
                        id: 'CertificationStatus2',
                        boxLabel: lang('Tidak'),
                        inputValue: 'No',
                        checked: true,
                    }],
                    listeners: {
                        change: function(cb, nv, ov) {
                            if (Ext.getCmp('CertificationStatus1').getValue() == true) {
                                Ext.getCmp('DateAppliedCertification').setDisabled(false);
                                Ext.getCmp('DateReceivedCertification').setDisabled(false);
                            } else {
                                Ext.getCmp('DateAppliedCertification').setDisabled(true);
                                Ext.getCmp('DateReceivedCertification').setDisabled(true);
                            }
                        }
                    }
                },{
                    xtype: 'datefield',
                    disabled: true,
                    fieldLabel: lang('Date Applied for Certification'),
                    id: 'DateAppliedCertification',
                    name: 'DateAppliedCertification',
                    format: 'Y-m-d'
                }, {
                    xtype: 'datefield',
                    disabled: true,
                    fieldLabel: lang('Date Received for Certification'),
                    id: 'DateReceivedCertification',
                    name: 'DateReceivedCertification',
                    format: 'Y-m-d'
                },{
                    xtype: 'radiogroup',
                    id: 'LandCertificate',
                    columns: 1,
                    fieldLabel: lang('Land Ownership'),
                    items: [{
                        name: 'LandCertificate',
                        id: 'LandCertificate1',
                        boxLabel: lang('None'),
                        inputValue: '1'
                    }, {
                        name: 'LandCertificate',
                        id: 'LandCertificate2',
                        boxLabel: lang('Notary Deed/BPN'),
                        inputValue: '2'
                    }, {
                        name: 'LandCertificate',
                        id: 'LandCertificate3',
                        boxLabel: lang('Sub District'),
                        inputValue: '3'
                    }, {
                        name: 'LandCertificate',
                        id: 'LandCertificate4',
                        boxLabel: lang('Village/ward'),
                        inputValue: '4'
                    }, {
                        name: 'LandCertificate',
                        id: 'LandCertificate5',
                        boxLabel: lang('Do not know'),
                        inputValue: '5'
                    }],
                    listeners: {}
                }]
            },{
                columnWidth: .5,
                layout: 'form',
                border: false,
                padding: 5,
                items: [{
                    xtype: 'textfield',
                    fieldLabel: lang('Area (Ha)'),
                    id: 'Area',
                    name: 'Area',
                    labelWidth: 180,
                    maskRe: /[0-9.]/,
                    readOnly: true
                },{
                    xtype: 'textfield',
                    fieldLabel: lang('Latitude (Dec)'),
                    id: 'ClonalGardenLatitude',
                    name: 'Latitude',
                    readOnly: m_hakakses_lat_long
                }, {
                    xtype: 'textfield',
                    fieldLabel: lang('Longitude (Dec)'),
                    id: 'ClonalGardenLongitude',
                    name: 'Longitude',
                    readOnly: m_hakakses_long_long
                },{
                    items: [{
                        layout: 'column',
                        labelWidth: 500,
                        items: [{
                            html: lang('Map Area'),
                        }, {
                            items: [{
                                xtype: 'button',
                                margin: '0 0 0 168',
                                width:'100px',
                                id: 'buttonShowPolygon',
                                text: lang('Show Polygon'),
                                handler: function() {
                                    if (Ext.getCmp('ClonalID').getValue() == '') {
                                        Ext.MessageBox.alert('Warning', 'Please save clonal garden first!');
                                    } else {
                                        display_area(Ext.getCmp('GardenNr').getValue());
                                    }
                                }
                            }]
                        }]
                    }]
                }]
            }]
        },{
            html : '<b> &nbsp; Cocoa Clone</b>',
        },{
            xtype: 'fieldset',
            margin : '0 0 0 0',
            padding : '0 0 0 1',
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
                },{
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
                                    name: 'FCGS1',
                                    inputValue: '1',
                                    id: 'FCGS1',
                                    listeners: {
                                        change: function() {
                                            CheckClonalGarden(this)
                                        }
                                    }
                                }, {
                                    boxLabel: lang('S2'),
                                    name: 'FCGS2',
                                    inputValue: '1',
                                    id: 'FCGS2',
                                    listeners: {
                                        change: function() {
                                            CheckClonalGarden(this)
                                        }
                                    }
                                }, {
                                    boxLabel: lang('45/MCC02'),
                                    name: 'FCGCG45',
                                    inputValue: '1',
                                    id: 'FCGCG45',
                                    listeners: {
                                        change: function() {
                                            CheckClonalGarden(this)
                                        }
                                    }
                                }, {
                                    boxLabel: lang('M01/MCC01'),
                                    name: 'FCGM01',
                                    inputValue: '1',
                                    id: 'FCGM01',
                                    listeners: {
                                        change: function() {
                                            CheckClonalGarden(this)
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
                                    id: 'FCGS1Nr',
                                    name: 'FCGS1Nr',
                                    disabled: true,
                                    maskRe: /[0-9.]/,
                                    listeners: {
                                        change: function() {
                                            JumlahClonalGarden()
                                        }
                                    }
                                }, {
                                    id: 'FCGS2Nr',
                                    name: 'FCGS2Nr',
                                    disabled: true,
                                    maskRe: /[0-9.]/,
                                    listeners: {
                                        change: function() {
                                            JumlahClonalGarden()
                                        }
                                    }
                                }, {
                                    id: 'FCGCG45Nr',
                                    name: 'FCGCG45Nr',
                                    disabled: true,
                                    maskRe: /[0-9.]/,
                                    listeners: {
                                        change: function() {
                                            JumlahClonalGarden()
                                        }
                                    }
                                }, {
                                    id: 'FCGM01Nr',
                                    name: 'FCGM01Nr',
                                    disabled: true,
                                    maskRe: /[0-9.]/,
                                    listeners: {
                                        change: function() {
                                            JumlahClonalGarden()
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
                                    name: 'FCGTSH858',
                                    inputValue: '1',
                                    id: 'FCGTSH858',
                                    listeners: {
                                        change: function() {
                                            CheckClonalGarden(this)
                                        }
                                    }
                                }, {
                                    boxLabel: lang('ICCRI3'),
                                    name: 'FCGICCRI3',
                                    inputValue: '1',
                                    id: 'FCGICCRI3',
                                    listeners: {
                                        change: function() {
                                            CheckClonalGarden(this)
                                        }
                                    }
                                }, {
                                    boxLabel: lang('ICCRI4'),
                                    name: 'FCGICCRI4',
                                    inputValue: '1',
                                    id: 'FCGICCRI4',
                                    listeners: {
                                        change: function() {
                                            CheckClonalGarden(this)
                                        }
                                    }
                                }, {
                                    boxLabel: lang('ICCRI5'),
                                    name: 'FCGICCRI5',
                                    inputValue: '1',
                                    id: 'FCGICCRI5',
                                    listeners: {
                                        change: function() {
                                            CheckClonalGarden(this)
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
                                    id: 'FCGTSH858Nr',
                                    name: 'FCGTSH858Nr',
                                    disabled: true,
                                    maskRe: /[0-9.]/,
                                    listeners: {
                                        change: function() {
                                            JumlahClonalGarden()
                                        }
                                    }
                                }, {
                                    id: 'FCGICCRI3Nr',
                                    name: 'FCGICCRI3Nr',
                                    disabled: true,
                                    maskRe: /[0-9.]/,
                                    listeners: {
                                        change: function() {
                                            JumlahClonalGarden()
                                        }
                                    }
                                }, {
                                    id: 'FCGICCRI4Nr',
                                    name: 'FCGICCRI4Nr',
                                    disabled: true,
                                    maskRe: /[0-9.]/,
                                    listeners: {
                                        change: function() {
                                            JumlahClonalGarden()
                                        }
                                    }
                                }, {
                                    id: 'FCGICCRI5Nr',
                                    name: 'FCGICCRI5Nr',
                                    disabled: true,
                                    maskRe: /[0-9.]/,
                                    listeners: {
                                        change: function() {
                                            JumlahClonalGarden()
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
                                    name: 'FCGRCC70',
                                    inputValue: '1',
                                    id: 'FCGRCC70',
                                    listeners: {
                                        change: function() {
                                            CheckClonalGarden(this)
                                        }
                                    }
                                }, {
                                    boxLabel: lang('RCC71'),
                                    name: 'FCGRCC71',
                                    inputValue: '1',
                                    id: 'FCGRCC71',
                                    listeners: {
                                        change: function() {
                                            CheckClonalGarden(this)
                                        }
                                    }
                                }, {
                                    boxLabel: lang('RCC72'),
                                    name: 'FCGRCC72',
                                    inputValue: '1',
                                    id: 'FCGRCC72',
                                    listeners: {
                                        change: function() {
                                            CheckClonalGarden(this)
                                        }
                                    }
                                }, {
                                    boxLabel: lang('RCC73'),
                                    name: 'FCGRCC73',
                                    inputValue: '1',
                                    id: 'FCGRCC73',
                                    listeners: {
                                        change: function() {
                                            CheckClonalGarden(this)
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
                                    id: 'FCGRCC70Nr',
                                    name: 'FCGRCC70Nr',
                                    disabled: true,
                                    maskRe: /[0-9.]/,
                                    listeners: {
                                        change: function() {
                                            JumlahClonalGarden()
                                        }
                                    }
                                }, {
                                    id: 'FCGRCC71Nr',
                                    name: 'FCGRCC71Nr',
                                    disabled: true,
                                    maskRe: /[0-9.]/,
                                    listeners: {
                                        change: function() {
                                            JumlahClonalGarden()
                                        }
                                    }
                                }, {
                                    id: 'FCGRCC72Nr',
                                    name: 'FCGRCC72Nr',
                                    disabled: true,
                                    maskRe: /[0-9.]/,
                                    listeners: {
                                        change: function() {
                                            JumlahClonalGarden()
                                        }
                                    }
                                }, {
                                    id: 'FCGRCC73Nr',
                                    name: 'FCGRCC73Nr',
                                    disabled: true,
                                    maskRe: /[0-9.]/,
                                    listeners: {
                                        change: function() {
                                            JumlahClonalGarden()
                                        }
                                    }
                                }]
                            }]
                        }]
                    },{
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
                                    name: 'FCGLocal',
                                    inputValue: '1',
                                    id: 'FCGLocal',
                                    listeners: {
                                        change: function() {
                                            CheckClonalGarden(this)
                                        }
                                    }
                                }, {
                                    boxLabel: lang('RCL'),
                                    name: 'FCGRCL',
                                    inputValue: '1',
                                    id: 'FCGRCL',
                                    listeners: {
                                        change: function() {
                                            CheckClonalGarden(this)
                                        }
                                    }
                                }, {
                                    boxLabel: lang('THR'),
                                    name: 'FCGTHR',
                                    inputValue: '1',
                                    id: 'FCGTHR',
                                    listeners: {
                                        change: function() {
                                            CheckClonalGarden(this)
                                        }
                                    }
                                }, {
                                    boxLabel: lang('AP'),
                                    name: 'FCGAP',
                                    inputValue: '1',
                                    id: 'FCGAP',
                                    listeners: {
                                        change: function() {
                                            CheckClonalGarden(this)
                                        }
                                    }
                                }, {
                                    boxLabel: lang('PR'),
                                    name: 'FCGPR',
                                    inputValue: '1',
                                    id: 'FCGPR',
                                    listeners: {
                                        change: function() {
                                            CheckClonalGarden(this)
                                        }
                                    }
                                }, {
                                    boxLabel: lang('Scavina'),
                                    name: 'FCGScavina',
                                    inputValue: '1',
                                    id: 'FCGScavina',
                                    listeners: {
                                        change: function() {
                                            CheckClonalGarden(this)
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
                                    id: 'FCGLocalNr',
                                    name: 'FCGLocalNr',
                                    disabled: true,
                                    maskRe: /[0-9.]/,
                                    listeners: {
                                        change: function() {
                                            JumlahClonalGarden()
                                        }
                                    }
                                }, {
                                    id: 'FCGRCLNr',
                                    name: 'FCGRCLNr',
                                    disabled: true,
                                    maskRe: /[0-9.]/,
                                    listeners: {
                                        change: function() {
                                            JumlahClonalGarden()
                                        }
                                    }
                                }, {
                                    id: 'FCGTHRNr',
                                    name: 'FCGTHRNr',
                                    disabled: true,
                                    maskRe: /[0-9.]/,
                                    listeners: {
                                        change: function() {
                                            JumlahClonalGarden()
                                        }
                                    }
                                }, {
                                    id: 'FCGAPNr',
                                    name: 'FCGAPNr',
                                    disabled: true,
                                    maskRe: /[0-9.]/,
                                    listeners: {
                                        change: function() {
                                            JumlahClonalGarden()
                                        }
                                    }
                                }, {
                                    id: 'FCGPRNr',
                                    name: 'FCGPRNr',
                                    disabled: true,
                                    maskRe: /[0-9.]/,
                                    listeners: {
                                        change: function() {
                                            JumlahClonalGarden()
                                        }
                                    }
                                }, {
                                    id: 'FCGScavinaNr',
                                    name: 'FCGScavinaNr',
                                    disabled: true,
                                    maskRe: /[0-9.]/,
                                    listeners: {
                                        change: function() {
                                            JumlahClonalGarden()
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
                                    name: 'FCGMT',
                                    inputValue: '1',
                                    id: 'FCGMT',
                                    listeners: {
                                        change: function() {
                                            CheckClonalGarden(this)
                                        }
                                    }
                                }, {
                                    boxLabel: lang('M02'),
                                    name: 'FCGM02',
                                    inputValue: '1',
                                    id: 'FCGM02',
                                    listeners: {
                                        change: function() {
                                            CheckClonalGarden(this)
                                        }
                                    }
                                }, {
                                    boxLabel: lang('M04'),
                                    name: 'FCGM04',
                                    inputValue: '1',
                                    id: 'FCGM04',
                                    listeners: {
                                        change: function() {
                                            CheckClonalGarden(this)
                                        }
                                    }
                                }, {
                                    boxLabel: lang('M06'),
                                    name: 'FCGM06',
                                    inputValue: '1',
                                    id: 'FCGM06',
                                    listeners: {
                                        change: function() {
                                            CheckClonalGarden(this)
                                        }
                                    }
                                }, {
                                    boxLabel: lang('MHP03'),
                                    name: 'FCGMHP03',
                                    inputValue: '1',
                                    id: 'FCGMHP03',
                                    listeners: {
                                        change: function() {
                                            CheckClonalGarden(this)
                                        }
                                    }
                                }, {
                                    boxLabel: lang('MHP04'),
                                    name: 'FCGMHP04',
                                    inputValue: '1',
                                    id: 'FCGMHP04',
                                    listeners: {
                                        change: function() {
                                            CheckClonalGarden(this)
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
                                    id: 'FCGMTNr',
                                    name: 'FCGMTNr',
                                    disabled: true,
                                    maskRe: /[0-9.]/,
                                    listeners: {
                                        change: function() {
                                            JumlahClonalGarden()
                                        }
                                    }
                                }, {
                                    id: 'FCGM02Nr',
                                    name: 'FCGM02Nr',
                                    disabled: true,
                                    maskRe: /[0-9.]/,
                                    listeners: {
                                        change: function() {
                                            JumlahClonalGarden()
                                        }
                                    }
                                }, {
                                    id: 'FCGM04Nr',
                                    name: 'FCGM04Nr',
                                    disabled: true,
                                    maskRe: /[0-9.]/,
                                    listeners: {
                                        change: function() {
                                            JumlahClonalGarden()
                                        }
                                    }
                                }, {
                                    id: 'FCGM06Nr',
                                    name: 'FCGM06Nr',
                                    disabled: true,
                                    maskRe: /[0-9.]/,
                                    listeners: {
                                        change: function() {
                                            JumlahClonalGarden()
                                        }
                                    }
                                }, {
                                    id: 'FCGMHP03Nr',
                                    name: 'FCGMHP03Nr',
                                    disabled: true,
                                    maskRe: /[0-9.]/,
                                    listeners: {
                                        change: function() {
                                            JumlahClonalGarden()
                                        }
                                    }
                                }, {
                                    id: 'FCGMHP04Nr',
                                    name: 'FCGMHP04Nr',
                                    disabled: true,
                                    maskRe: /[0-9.]/,
                                    listeners: {
                                        change: function() {
                                            JumlahClonalGarden()
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
                                    name: 'FCGBB01',
                                    inputValue: '1',
                                    id: 'FCGBB01',
                                    listeners: {
                                        change: function() {
                                            CheckClonalGarden(this)
                                        }
                                    }
                                }, {
                                    boxLabel: lang('BLB'),
                                    name: 'FCGBLB',
                                    inputValue: '1',
                                    id: 'FCGBLB',
                                    listeners: {
                                        change: function() {
                                            CheckClonalGarden(this)
                                        }
                                    }
                                }, {
                                    boxLabel: lang('BRT'),
                                    name: 'FCGBRT',
                                    inputValue: '1',
                                    id: 'FCGBRT',
                                    listeners: {
                                        change: function() {
                                            CheckClonalGarden(this)
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
                                    id: 'FCGBB01Nr',
                                    name: 'FCGBB01Nr',
                                    disabled: true,
                                    maskRe: /[0-9.]/,
                                    listeners: {
                                        change: function() {
                                            JumlahClonalGarden()
                                        }
                                    }
                                }, {
                                    id: 'FCGBLBNr',
                                    name: 'FCGBLBNr',
                                    disabled: true,
                                    maskRe: /[0-9.]/,
                                    listeners: {
                                        change: function() {
                                            JumlahClonalGarden()
                                        }
                                    }
                                }, {
                                    id: 'FCGBRTNr',
                                    name: 'FCGBRTNr',
                                    disabled: true,
                                    maskRe: /[0-9.]/,
                                    listeners: {
                                        change: function() {
                                            JumlahClonalGarden()
                                        }
                                    }
                                }]
                            }]
                        }]
                    }]
                }]
            }]
        },{
            layout: 'column',
            border: false,
            items: [{
                columnWidth: .5,
                layout: 'form',
                border: false,
                padding: 5,
                items:[{
                    xtype: 'textfield',
                    fieldLabel: lang('Others'),
                    hidden: true,
                    id: 'FCGOtherClones',
                    name: 'FCGOtherClones'
                }]
            }, {
                columnWidth: .5,
                layout: 'form',
                border: false,
                padding: 5,
                items: [{
                    xtype: 'textfield',
                    fieldLabel: lang('Total'),
                    id: 'FCGOtherClonesNr',
                    hidden: true,
                    name: 'FCGOtherClonesNr',
                    maskRe: /[0-9.]/,
                    listeners: {
                        change: function() {
                            JumlahClonalGarden()
                        }
                    }
                }, {
                    xtype: 'textfield',
                    fieldLabel: lang('Cocoa Clone Total'),
                    id: 'FCGTotalClonesNr',
                    name: 'FCGTotalClonesNr',
                    readOnly: true
                }]
            }]
        },{
            html : '<b> &nbsp; Shade</b>',
        },{
            xtype: 'fieldset',
            margin : '0 0 0 0',
            padding : '0 0 0 1',
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
                                    id: 'Coconut',
                                    listeners: {
                                        change: function() {
                                            CheckClonalGarden(this)
                                        }
                                    }
                                }, {
                                    boxLabel: lang('ArecaPalm'),
                                    name: 'ArecaPalm',
                                    inputValue: '1',
                                    id: 'ArecaPalm',
                                    listeners: {
                                        change: function() {
                                            CheckClonalGarden(this)
                                        }
                                    }
                                }, {
                                    boxLabel: lang('Rubber'),
                                    name: 'Rubber',
                                    inputValue: '1',
                                    id: 'Rubber',
                                    listeners: {
                                        change: function() {
                                            CheckClonalGarden(this)
                                        }
                                    }
                                }, {
                                    boxLabel: lang('Clove'),
                                    name: 'Clove',
                                    inputValue: '1',
                                    id: 'Clove',
                                    listeners: {
                                        change: function() {
                                            CheckClonalGarden(this)
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
                                    id: 'CoconutNr',
                                    name: 'CoconutNr',
                                    disabled: true,
                                    maskRe: /[0-9.]/,
                                    listeners: {
                                        change: function() {
                                            JumlahShadeTrees()
                                        }
                                    }
                                }, {
                                    id: 'ArecaPalmNr',
                                    name: 'ArecaPalmNr',
                                    disabled: true,
                                    maskRe: /[0-9.]/,
                                    listeners: {
                                        change: function() {
                                            JumlahShadeTrees()
                                        }
                                    }
                                }, {
                                    id: 'RubberNr',
                                    name: 'RubberNr',
                                    disabled: true,
                                    maskRe: /[0-9.]/,
                                    listeners: {
                                        change: function() {
                                            JumlahShadeTrees()
                                        }
                                    }
                                }, {
                                    id: 'CloveNr',
                                    name: 'CloveNr',
                                    disabled: true,
                                    maskRe: /[0-9.]/,
                                    listeners: {
                                        change: function() {
                                            JumlahShadeTrees()
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
                                    id: 'Cashew',
                                    listeners: {
                                        change: function() {
                                            CheckClonalGarden(this)
                                        }
                                    }
                                }, {
                                    boxLabel: lang('OilPalm'),
                                    name: 'OilPalm',
                                    inputValue: '1',
                                    id: 'OilPalm',
                                    listeners: {
                                        change: function() {
                                            CheckClonalGarden(this)
                                        }
                                    }
                                }, {
                                    boxLabel: lang('SugarPalm'),
                                    name: 'SugarPalm',
                                    inputValue: '1',
                                    id: 'SugarPalm',
                                    listeners: {
                                        change: function() {
                                            CheckClonalGarden(this)
                                        }
                                    }
                                }, {
                                    boxLabel: lang('Nutmeg'),
                                    name: 'Nutmeg',
                                    inputValue: '1',
                                    id: 'Nutmeg',
                                    listeners: {
                                        change: function() {
                                            CheckClonalGarden(this)
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
                                    id: 'CashewNr',
                                    name: 'CashewNr',
                                    disabled: true,
                                    maskRe: /[0-9.]/,
                                    listeners: {
                                        change: function() {
                                            JumlahShadeTrees()
                                        }
                                    }
                                }, {
                                    id: 'OilPalmNr',
                                    name: 'OilPalmNr',
                                    disabled: true,
                                    maskRe: /[0-9.]/,
                                    listeners: {
                                        change: function() {
                                            JumlahShadeTrees()
                                        }
                                    }
                                }, {
                                    id: 'SugarPalmNr',
                                    name: 'SugarPalmNr',
                                    disabled: true,
                                    maskRe: /[0-9.]/,
                                    listeners: {
                                        change: function() {
                                            JumlahShadeTrees()
                                        }
                                    }
                                }, {
                                    id: 'NutmegNr',
                                    name: 'NutmegNr',
                                    disabled: true,
                                    maskRe: /[0-9.]/,
                                    listeners: {
                                        change: function() {
                                            JumlahShadeTrees()
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
                                    id: 'Hazelnut',
                                    listeners: {
                                        change: function() {
                                            CheckClonalGarden(this)
                                        }
                                    }
                                }, {
                                    boxLabel: lang('Kapok'),
                                    name: 'FCGKapok',
                                    inputValue: '1',
                                    id: 'FCGKapok',
                                    listeners: {
                                        change: function() {
                                            CheckClonalGarden(this)
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
                                    id: 'HazelnutNr',
                                    name: 'HazelnutNr',
                                    disabled: true,
                                    maskRe: /[0-9.]/,
                                    listeners: {
                                        change: function() {
                                            JumlahShadeTrees()
                                        }
                                    }
                                }, {
                                    id: 'FCGKapokNr',
                                    name: 'FCGKapokNr',
                                    disabled: true,
                                    maskRe: /[0-9.]/,
                                    listeners: {
                                        change: function() {
                                            JumlahShadeTrees()
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
                                    id: 'Mahagony',
                                    listeners: {
                                        change: function() {
                                            CheckClonalGarden(this)
                                        }
                                    }
                                }, {
                                    boxLabel: lang('Teak'),
                                    name: 'Teak',
                                    inputValue: '1',
                                    id: 'Teak',
                                    listeners: {
                                        change: function() {
                                            CheckClonalGarden(this)
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
                                    id: 'MahagonyNr',
                                    name: 'MahagonyNr',
                                    disabled: true,
                                    maskRe: /[0-9.]/,
                                    listeners: {
                                        change: function() {
                                            JumlahShadeTrees()
                                        }
                                    }
                                }, {
                                    id: 'TeakNr',
                                    name: 'TeakNr',
                                    disabled: true,
                                    maskRe: /[0-9.]/,
                                    listeners: {
                                        change: function() {
                                            JumlahShadeTrees()
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
                                    id: 'Vitex',
                                    listeners: {
                                        change: function() {
                                            CheckClonalGarden(this)
                                        }
                                    }
                                }, {
                                    boxLabel: lang('Ermerilla'),
                                    name: 'Ermerilla',
                                    inputValue: '1',
                                    id: 'Ermerilla',
                                    listeners: {
                                        change: function() {
                                            CheckClonalGarden(this)
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
                                    id: 'VitexNr',
                                    name: 'VitexNr',
                                    disabled: true,
                                    maskRe: /[0-9.]/,
                                    listeners: {
                                        change: function() {
                                            JumlahShadeTrees()
                                        }
                                    }
                                }, {
                                    id: 'ErmerillaNr',
                                    name: 'ErmerillaNr',
                                    disabled: true,
                                    maskRe: /[0-9.]/,
                                    listeners: {
                                        change: function() {
                                            JumlahShadeTrees()
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
                                    id: 'Anthocephalus',
                                    listeners: {
                                        change: function() {
                                            CheckClonalGarden(this)
                                        }
                                    }
                                }, {
                                    boxLabel: lang('Albizia'),
                                    name: 'Albizia',
                                    inputValue: '1',
                                    id: 'Albizia',
                                    listeners: {
                                        change: function() {
                                            CheckClonalGarden(this)
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
                                    id: 'AnthocephalusNr',
                                    name: 'AnthocephalusNr',
                                    disabled: true,
                                    maskRe: /[0-9.]/,
                                    listeners: {
                                        change: function() {
                                            JumlahShadeTrees()
                                        }
                                    }
                                }, {
                                    id: 'AlbiziaNr',
                                    name: 'AlbiziaNr',
                                    disabled: true,
                                    maskRe: /[0-9.]/,
                                    listeners: {
                                        change: function() {
                                            JumlahShadeTrees()
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
                                    id: 'Jackfruit',
                                    listeners: {
                                        change: function() {
                                            CheckClonalGarden(this)
                                        }
                                    }
                                }, {
                                    boxLabel: lang('Banana'),
                                    name: 'Banana',
                                    inputValue: '1',
                                    id: 'Banana',
                                    listeners: {
                                        change: function() {
                                            CheckClonalGarden(this)
                                        }
                                    }
                                }, {
                                    boxLabel: lang('Rambutan'),
                                    name: 'FCGRambutan',
                                    inputValue: '1',
                                    id: 'FCGRambutan',
                                    listeners: {
                                        change: function() {
                                            CheckClonalGarden(this)
                                        }
                                    }
                                }, {
                                    boxLabel: lang('Mango'),
                                    name: 'Mango',
                                    inputValue: '1',
                                    id: 'Mango',
                                    listeners: {
                                        change: function() {
                                            CheckClonalGarden(this)
                                        }
                                    }
                                }, {
                                    boxLabel: lang('SpondiasDulcis'),
                                    name: 'SpondiasDulcis',
                                    inputValue: '1',
                                    id: 'SpondiasDulcis',
                                    listeners: {
                                        change: function() {
                                            CheckClonalGarden(this)
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
                                    id: 'JackfruitNr',
                                    name: 'JackfruitNr',
                                    disabled: true,
                                    maskRe: /[0-9.]/,
                                    listeners: {
                                        change: function() {
                                            JumlahShadeTrees()
                                        }
                                    }
                                }, {
                                    id: 'BananaNr',
                                    name: 'BananaNr',
                                    disabled: true,
                                    maskRe: /[0-9.]/,
                                    listeners: {
                                        change: function() {
                                            JumlahShadeTrees()
                                        }
                                    }
                                }, {
                                    id: 'FCGRambutanNr',
                                    name: 'FCGRambutanNr',
                                    disabled: true,
                                    maskRe: /[0-9.]/,
                                    listeners: {
                                        change: function() {
                                            JumlahShadeTrees()
                                        }
                                    }
                                }, {
                                    id: 'MangoNr',
                                    name: 'MangoNr',
                                    disabled: true,
                                    maskRe: /[0-9.]/,
                                    listeners: {
                                        change: function() {
                                            JumlahShadeTrees()
                                        }
                                    }
                                }, {
                                    id: 'SpondiasDulcisNr',
                                    name: 'SpondiasDulcisNr',
                                    disabled: true,
                                    maskRe: /[0-9.]/,
                                    listeners: {
                                        change: function() {
                                            JumlahShadeTrees()
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
                                    name: 'FCGLangsat',
                                    inputValue: '1',
                                    id: 'FCGLangsat',
                                    listeners: {
                                        change: function() {
                                            CheckClonalGarden(this)
                                        }
                                    }
                                }, {
                                    boxLabel: lang('Durian'),
                                    name: 'FCGDurian',
                                    inputValue: '1',
                                    id: 'FCGDurian',
                                    listeners: {
                                        change: function() {
                                            CheckClonalGarden(this)
                                        }
                                    }
                                }, {
                                    boxLabel: lang('Guava'),
                                    name: 'Guava',
                                    inputValue: '1',
                                    id: 'Guava',
                                    listeners: {
                                        change: function() {
                                            CheckClonalGarden(this)
                                        }
                                    }
                                }, {
                                    boxLabel: lang('Avocado'),
                                    name: 'Avocado',
                                    inputValue: '1',
                                    id: 'Avocado',
                                    listeners: {
                                        change: function() {
                                            CheckClonalGarden(this)
                                        }
                                    }
                                }, {
                                    boxLabel: lang('Cempedak'),
                                    name: 'FCGCempedak',
                                    inputValue: '1',
                                    id: 'FCGCempedak',
                                    listeners: {
                                        change: function() {
                                            CheckClonalGarden(this)
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
                                    id: 'FCGLangsatNr',
                                    name: 'FCGLangsatNr',
                                    disabled: true,
                                    maskRe: /[0-9.]/,
                                    listeners: {
                                        change: function() {
                                            JumlahShadeTrees()
                                        }
                                    }
                                }, {
                                    id: 'FCGDurianNr',
                                    name: 'FCGDurianNr',
                                    disabled: true,
                                    maskRe: /[0-9.]/,
                                    listeners: {
                                        change: function() {
                                            JumlahShadeTrees()
                                        }
                                    }
                                }, {
                                    id: 'GuavaNr',
                                    name: 'GuavaNr',
                                    disabled: true,
                                    maskRe: /[0-9.]/,
                                    listeners: {
                                        change: function() {
                                            JumlahShadeTrees()
                                        }
                                    }
                                }, {
                                    id: 'AvocadoNr',
                                    name: 'AvocadoNr',
                                    disabled: true,
                                    maskRe: /[0-9.]/,
                                    listeners: {
                                        change: function() {
                                            JumlahShadeTrees()
                                        }
                                    }
                                }, {
                                    id: 'FCGCempedakNr',
                                    name: 'FCGCempedakNr',
                                    disabled: true,
                                    maskRe: /[0-9.]/,
                                    listeners: {
                                        change: function() {
                                            JumlahShadeTrees()
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
                                    id: 'Breadfruit',
                                    listeners: {
                                        change: function() {
                                            CheckClonalGarden(this)
                                        }
                                    }
                                }, {
                                    boxLabel: lang('Papaya'),
                                    name: 'Papaya',
                                    inputValue: '1',
                                    id: 'Papaya',
                                    listeners: {
                                        change: function() {
                                            CheckClonalGarden(this)
                                        }
                                    }
                                }, {
                                    boxLabel: lang('Mangosteen'),
                                    name: 'Mangosteen',
                                    inputValue: '1',
                                    id: 'Mangosteen',
                                    listeners: {
                                        change: function() {
                                            CheckClonalGarden(this)
                                        }
                                    }
                                }, {
                                    boxLabel: lang('Citrus'),
                                    name: 'Citrus',
                                    inputValue: '1',
                                    id: 'Citrus',
                                    listeners: {
                                        change: function() {
                                            CheckClonalGarden(this)
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
                                    id: 'BreadfruitNr',
                                    name: 'BreadfruitNr',
                                    disabled: true,
                                    maskRe: /[0-9.]/,
                                    listeners: {
                                        change: function() {
                                            JumlahShadeTrees()
                                        }
                                    }
                                }, {
                                    id: 'PapayaNr',
                                    name: 'PapayaNr',
                                    disabled: true,
                                    maskRe: /[0-9.]/,
                                    listeners: {
                                        change: function() {
                                            JumlahShadeTrees()
                                        }
                                    }
                                }, {
                                    id: 'MangosteenNr',
                                    name: 'MangosteenNr',
                                    disabled: true,
                                    maskRe: /[0-9.]/,
                                    listeners: {
                                        change: function() {
                                            JumlahShadeTrees()
                                        }
                                    }
                                }, {
                                    id: 'CitrusNr',
                                    name: 'CitrusNr',
                                    disabled: true,
                                    maskRe: /[0-9.]/,
                                    listeners: {
                                        change: function() {
                                            JumlahShadeTrees()
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
                                    id: 'Gliricidia',
                                    listeners: {
                                        change: function() {
                                            CheckClonalGarden(this)
                                        }
                                    }
                                }, {
                                    boxLabel: lang('Leucaena'),
                                    name: 'Leucaena',
                                    inputValue: '1',
                                    id: 'Leucaena',
                                    listeners: {
                                        change: function() {
                                            CheckClonalGarden(this)
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
                                    id: 'GliricidiaNr',
                                    name: 'GliricidiaNr',
                                    disabled: true,
                                    maskRe: /[0-9.]/,
                                    listeners: {
                                        change: function() {
                                            JumlahShadeTrees()
                                        }
                                    }
                                }, {
                                    id: 'LeucaenaNr',
                                    name: 'LeucaenaNr',
                                    disabled: true,
                                    maskRe: /[0-9.]/,
                                    listeners: {
                                        change: function() {
                                            JumlahShadeTrees()
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
                                    id: 'Parkia',
                                    listeners: {
                                        change: function() {
                                            CheckClonalGarden(this)
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
                                    id: 'ParkiaNr',
                                    name: 'ParkiaNr',
                                    disabled: true,
                                    listeners: {
                                        change: function() {
                                            JumlahShadeTrees()
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
                                    id: 'Archidendron',
                                    listeners: {
                                        change: function() {
                                            CheckClonalGarden(this)
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
                                    id: 'ArchidendronNr',
                                    name: 'ArchidendronNr',
                                    disabled: true,
                                    maskRe: /[0-9.]/,
                                    listeners: {
                                        change: function() {
                                            JumlahShadeTrees()
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
                    id: 'TotalShadeTreesNr',
                    name: 'TotalShadeTreesNr',
                    maskRe: /[0-9.]/,
                    readOnly: true
                }]
            }]
        },{
            xtype: 'tabpanel',
            flex: 1,
            margin: 2,
            activeTab: 0,
            plain: true,
            cls:'tabSce',
            items: [{
                xtype: 'gridpanel',
                title: lang('Penjualan'),
                id: 'gClonalPenjualan',
                style: 'border:1px solid #CCC;',
                store: store_clonal_penjualan,
                width: '100%',
                loadMask: true,
                selType: 'rowmodel',
                height:475,
                dockedItems: [{
                    xtype: 'pagingtoolbar',
                    store: store_clonal_penjualan,
                    dock: 'bottom',
                    displayInfo: true
                },{
                    xtype: 'toolbar',
                    items: [{
                        icon: varjs.config.base_url + 'images/icons/new/add.png', cls:'Sfr_BtnGridGreen', overCls:'Sfr_BtnGridGreen-Hover',
                        //cls: m_act_save,
                        hidden: m_act_add,
                        text: lang('Add'),
                        scope: this,
                        handler: function() {
                            var cek = cekGardenNrSel();
                            if(cek == true){

                                //cek save
                                var cekClonal = cekClonalSave();
                                if(cekClonal == true){
                                    cRowEditing.cancelEdit();
                                    var r = Ext.create('penjualan.Model', {
                                        id: '',
                                        Buyer: '',
                                        Volume: '',
                                        Price: '',
                                        Total: '',
                                        DateTransaction: ''
                                    });
                                    store_clonal_penjualan.insert(0, r);
                                    cRowEditing.startEdit(0, 0);
                                }
                            }
                        }
                    },{
                        icon: varjs.config.base_url + 'images/icons/new/update.png',
                        //cls: m_act_save,
                        hidden: m_act_update,
                        text: lang('Update'),
                        scope: this,
                        handler: function() {
                            var cek = cekGardenNrSel();
                            if(cek == true){

                                //cek save
                                var cekClonal = cekClonalSave();
                                if(cekClonal == true){
                                    cRowEditing.cancelEdit();
                                    var sm = Ext.getCmp('gClonalPenjualan').getSelectionModel().getSelection();
                                    cRowEditing.startEdit(sm[0].index, 0);
                                }
                            }
                        }
                    },{
                        itemId: 'remove',
                        icon: varjs.config.base_url + 'images/icons/new/delete.png',
                        //cls: m_act_save,
                        text: lang('Delete'),
                        hidden: m_act_delete,
                        scope: this,
                        handler: function() {
                            var cek = cekGardenNrSel();
                            if(cek == true){
                                //cek save
                                var cekClonal = cekClonalSave();
                                if(cekClonal == true){
                                    var smb = Ext.getCmp('gClonalPenjualan').getSelectionModel().getSelection()[0];
                                    cRowEditing.cancelEdit();

                                    Ext.MessageBox.confirm('Message', 'Do you want to delete this data ?', function(btn) {
                                        if (btn == 'yes') {
                                            Ext.Ajax.request({
                                                waitMsg: 'Please Wait',
                                                url: m_api + '/prog_sce/clonal_penjualan',
                                                method: 'DELETE',
                                                params: {
                                                    id: smb.raw.id
                                                },
                                                success: function(response, opts) {
                                                    var obj = Ext.decode(response.responseText);
                                                    switch (obj.success) {
                                                        case true:
                                                            Ext.MessageBox.alert('Success', obj.message);
                                                            store_clonal_penjualan.load();
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
                        }
                    }]
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
                },{
                    text: lang('Bibit Dijual'),
                    dataIndex: 'Volume',
                    xtype: 'numbercolumn',
                    format:'0,000',
                    width: '15%',
                    editor: {
                        xtype: 'numericfield',
                        id: 'nvol',
                        allowBlank: false,
                        listeners: {
                            change: function() {
                                Ext.getCmp('ntot').setValue(Ext.getCmp('nvol').getValue() * Ext.getCmp('npri').getValue());
                            }
                        }
                    }
                },{
                    text: lang('Clone Type'),
                    dataIndex: 'CloneTypeName',
                    width: '15%',
                    editor: {
                        xtype: 'combo',
                        store: mc_clone_type_combo,
                        name: 'CloneTypeID',
                        displayField: 'label',
                        valueField: 'id',
                        queryMode: 'local',
                        allowBlank: false
                    }
                },{
                    text: lang('Harga Satuan'),
                    dataIndex: 'Price',
                    xtype: 'numbercolumn',
                    format:'0,000',
                    width: '15%',
                    editor: {
                        xtype: 'numericfield',
                        id: 'npri',
                        allowBlank: false,
                        listeners: {
                            change: function() {
                                Ext.getCmp('ntot').setValue(Ext.getCmp('nvol').getValue() * Ext.getCmp('npri').getValue());
                            }
                        }
                    }
                },{
                    text: lang('Total'),
                    dataIndex: 'Total',
                    width: '15%',
                    xtype: 'numbercolumn',
                    format:'0,000',
                    editor: {
                        xtype: 'numericfield',
                        allowBlank: false,
                        id: 'ntot',
                        readOnly: true
                    }
                },{
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
                plugins: [cRowEditing],
                listeners: {
                    'canceledit': function(editor, e, eOpts) {
                        store_clonal_penjualan.load();
                    },
                    'edit': function(editor, e) {
                        if (e.record.data.id == '') {
                            //insert
                            Ext.Ajax.request({
                                waitMsg: 'Please wait...',
                                url: m_api + '/prog_sce/clonal_penjualan',
                                method: 'POST',
                                params: {
                                    ClonalID: Ext.getCmp('ClonalID').getValue(),
                                    Buyer: e.record.data.Buyer,
                                    Volume: e.record.data.Volume,
                                    Price: e.record.data.Price,
                                    Total: e.record.data.Totel,
                                    DateTransaction: e.record.data.DateTransaction,
                                    CloneTypeName: e.record.data.CloneTypeName,
                                    CloneTypeID: e.record.data.CloneTypeID
                                },
                                success: function(response, opts) {
                                    var obj = Ext.decode(response.responseText);
                                    switch (obj.success) {
                                        case true:
                                            Ext.MessageBox.alert('Success', obj.message);
                                            store_clonal_penjualan.load();
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
                        }else{
                            //update
                            Ext.Ajax.request({
                                waitMsg: 'Please wait...',
                                url: m_api + '/prog_sce/clonal_penjualan',
                                method: 'PUT',
                                params: {
                                    id: e.record.data.id,
                                    Buyer: e.record.data.Buyer,
                                    Volume: e.record.data.Volume,
                                    Price: e.record.data.Price,
                                    Total: e.record.data.Totel,
                                    DateTransaction: e.record.data.DateTransaction,
                                    CloneTypeName: e.record.data.CloneTypeName,
                                    CloneTypeID: e.record.data.CloneTypeID
                                },
                                success: function(response, opts) {
                                    var obj = Ext.decode(response.responseText);
                                    switch (obj.success) {
                                        case true:
                                            Ext.MessageBox.alert('Success', obj.message);
                                            store_clonal_penjualan.load();
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
                    }
                }
            },{
                xtype: 'gridpanel',
                title: lang('Monitoring'),
                id: 'gClonalMonitoring',
                style: 'border:1px solid #CCC;',
                store: store_clonal_monitoring,
                width: '100%',
                loadMask: true,
                selType: 'rowmodel',
                height:475,
                dockedItems: [{
                    xtype: 'pagingtoolbar',
                    store: store_clonal_monitoring,
                    dock: 'bottom',
                    displayInfo: true
                },{
                    xtype: 'toolbar',
                    items: [{
                        icon: varjs.config.base_url + 'images/icons/new/add.png', cls:'Sfr_BtnGridGreen', overCls:'Sfr_BtnGridGreen-Hover',
                        //cls: m_act_save,
                        hidden: m_act_add,
                        text: lang('Add'),
                        scope: this,
                        handler: function() {
                            var cek = cekGardenNrSel();
                            if(cek == true){

                                //cek save
                                var cekClonal = cekClonalSave();
                                if(cekClonal == true){
                                    mRowEditing.cancelEdit();
                                    var r = Ext.create('monitoring.Model', {
                                        id: '',
                                        MonitoringDate:'',
                                        MonitoringStatus:'',
                                        Description:''
                                    });
                                    store_clonal_monitoring.insert(0, r);
                                    mRowEditing.startEdit(0, 0);
                                }
                            }
                        }
                    },{
                        icon: varjs.config.base_url + 'images/icons/new/update.png',
                        //cls: m_act_save,
                        hidden: m_act_update,
                        text: lang('Update'),
                        scope: this,
                        handler: function() {
                            var cek = cekGardenNrSel();
                            if(cek == true){
                                //cek save
                                var cekClonal = cekClonalSave();
                                if(cekClonal == true){
                                    mRowEditing.cancelEdit();
                                    var sm = Ext.getCmp('gClonalMonitoring').getSelectionModel().getSelection();
                                    mRowEditing.startEdit(sm[0].index, 0);
                                    act_clonal_status(Ext.getCmp('mStatus').getValue());
                                }
                            }
                        }
                    },{
                        itemId: 'remove',
                        icon: varjs.config.base_url + 'images/icons/new/delete.png',
                        //cls: m_act_save,
                        hidden: m_act_delete,
                        text: lang('Delete'),
                        scope: this,
                        handler: function() {
                            var cek = cekGardenNrSel();
                            if(cek == true){
                                //cek save
                                var cekClonal = cekClonalSave();
                                if(cekClonal == true){
                                    var smb = Ext.getCmp('gClonalMonitoring').getSelectionModel().getSelection()[0];
                                    mRowEditing.cancelEdit();
                                    Ext.MessageBox.confirm('Message', 'Do you want to delete this data ?', function(btn) {
                                        if (btn == 'yes') {
                                            Ext.Ajax.request({
                                                waitMsg: 'Please Wait',
                                                url: m_api + '/prog_sce/clonal_monitoring',
                                                method: 'DELETE',
                                                params: {
                                                    id: smb.raw.id
                                                },
                                                success: function(response, opts) {
                                                    var obj = Ext.decode(response.responseText);
                                                    switch (obj.success) {
                                                        case true:
                                                            Ext.MessageBox.alert('Success', obj.message);
                                                            store_clonal_monitoring.load();
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
                        }
                    }]
                }],
                columns:[{
                    text: lang('ID'),
                    dataIndex: 'id',
                    hidden: true
                },{
                    text: lang('No'),
                    xtype: 'rownumberer',
                    width: '5%'
                },{
                    text: lang('Tanggal Kedatangan'),
                    dataIndex: 'MonitoringDate',
                    width: '15%',
                    editor: {
                        xtype: 'datefield',
                        id: 'mDate',
                        format: 'Y-m-d',
                        allowBlank: false
                    }
                },{
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
                                act_clonal_status(Ext.getCmp('mStatus').getValue());
                            }
                        }
                    }
                },{
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
                        store_clonal_monitoring.load();
                    },
                    'edit': function(editor, e) {
                        if (e.record.data.id == '') {
                            //insert
                            Ext.Ajax.request({
                                waitMsg: 'Please wait...',
                                url: m_api + '/prog_sce/clonal_monitoring',
                                method: 'POST',
                                params: {
                                    ClonalID: Ext.getCmp('ClonalID').getValue(),
                                    MonitoringDate: e.record.data.MonitoringDate,
                                    MonitoringStatus: e.record.data.MonitoringStatus,
                                    Description: e.record.data.Description
                                },
                                success: function(response, opts) {
                                    var obj = Ext.decode(response.responseText);
                                    switch (obj.success) {
                                        case true:
                                            Ext.MessageBox.alert('Success', obj.message);
                                            store_clonal_monitoring.load();
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
                        }else{
                            //update
                            Ext.Ajax.request({
                                waitMsg: 'Please wait...',
                                url: m_api + '/prog_sce/clonal_monitoring',
                                method: 'PUT',
                                params: {
                                    id: e.record.data.id,
                                    MonitoringDate: e.record.data.MonitoringDate,
                                    MonitoringStatus: e.record.data.MonitoringStatus,
                                    Description: e.record.data.Description
                                },
                                success: function(response, opts) {
                                    var obj = Ext.decode(response.responseText);
                                    switch (obj.success) {
                                        case true:
                                            Ext.MessageBox.alert('Success', obj.message);
                                            store_clonal_monitoring.load();
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
                    }
                }
            }]
        }],
        buttons: [{
            id: 'csaveButton',
            text: lang('Save'),
            margin: '5px',
            scale: 'large',
            ui: 's-button',
            cls: 's-blue',
            hidden: m_act_add,
            handler: function() {
                //Garden Nr harus terselect dulu
                var cek = cekGardenNrSel();
                if(cek == true){
                    var form = Ext.getCmp('mainPanel').getForm();
                    form.submit({
                        url: m_api + '/prog_sce/clonal_garden',
                        method: 'POST',
                        waitMsg: lang('Sending data...'),
                        success: function(fp, o) {
                            var jsonResp = o.result;
                            //console.log(jsonResp);

                            if (jsonResp.prosesnya == 'insert') {
                                //load combo NurseryNr
                                mc_combo_gardennr.load();
                                Ext.getCmp('GardenNr').setValue(jsonResp.comboGardenNr);
                            }
                            Ext.MessageBox.alert('Success', 'Data saved.');
                        },
                        failure: function(fp, o) {
                            var jsonResp = o.result;
                            if(jsonResp.message != undefined){
                                var msgNotif = jsonResp.message;
                            }else{
                                var msgNotif = "Please flll all the input";
                            }

                            Ext.MessageBox.show({
                                title: 'Failed',
                                msg: msgNotif,
                                buttons: Ext.MessageBox.OK,
                                animateTarget: 'mb9',
                                icon: 'ext-mb-info'
                            });
                        }
                    });
                }
            }
        },{
            text: lang('Delete'),
            margin: '5px',
            scale: 'large',
            ui: 's-button',
            cls: 's-red',
            hidden: m_act_delete,
            handler: function() {
                //Garden Nr harus terselect dulu
                var cek = cekGardenNrSel();
                if(cek == true){
                    if (Ext.getCmp('ClonalID').getValue() == '') {
                        Ext.MessageBox.alert('Warning', 'Please save clonal garden first!');
                    } else {
                        //proses hapus mulai
                        //console.log('hapus lah');

                        Ext.MessageBox.confirm('Message', 'Do you want to delete this data ?', function(btn) {
                            if (btn == 'yes') {
                                Ext.Ajax.request({
                                    waitMsg: 'Please Wait',
                                    url: m_api + '/prog_sce/clonal_garden',
                                    method: 'DELETE',
                                    params: {
                                        ClonalID: Ext.getCmp('ClonalID').getValue()
                                    },
                                    success: function(response, opts) {
                                        var obj = Ext.decode(response.responseText);

                                        //reset all form but GardenNr ============================================
                                        /*
                                        var fields = Ext.getCmp('mainPanel').query('[isFormField][name!="GardenNr"]');
                                        for (var i = 0, len = fields.length; i < len; i++) {
                                            fields[i].reset();
                                        }
                                        */
                                        //reset all form but GardenNr ============================================

                                        mc_combo_gardennr.load();
                                        Ext.getCmp('GardenNr').setValue('-1');

                                        Ext.MessageBox.alert('Notification', obj.message);
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
            }
        }],
        renderTo: 'ext-content'
    });

    //============================================ STUFF to do After Loading ========================================================
    //set auto select ke item pertama
    Ext.getCmp('GardenNr').setValue('-1');
});