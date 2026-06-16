Ext.Loader.setConfig({enabled: true});
Ext.Loader.setPath('js/ext-4.2.0.663', '../ux');
//Ext.Loader.setPath('js/ext-4.2.0.663/ux/form');
Ext.require([
    //'Ext.form.Panel',
    //'Ext.ux.form.MultiSelect',
    'Ext.ux.form.ItemSelector'
]);

var form_data = {};
Ext.onReady(function () {
    Ext.tip.QuickTipManager.init();
    var selected_role = null;
    var store = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['UserId', 'UserRealName', 'UserName', 'UserActive', 'RoleName', 'GroupName'],
        autoLoad: true,
        pageSize: 50,
        proxy: {
            type: 'ajax',
            url: m_crud + 's',
            reader: {
                type: 'json',
                root: 'data',
                totalProperty: 'total'
            }
        },
        listeners: {
            beforeload: function(store, operation) {
                store.proxy.extraParams.key = Ext.getCmp('key').getValue();
                store.proxy.extraParams.RoleId = Ext.getCmp('filter-RoleId').getValue();
                store.proxy.extraParams.GroupId = Ext.getCmp('filter-GroupId').getValue();
                store.proxy.extraParams.Status = Ext.getCmp('filter-Status').getValue();
            }
        }
    });
    var groups = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['GroupId', 'GroupName'],
        autoLoad: false,
        proxy: {
            type: 'ajax',
            url: m_group,
            reader: {
                type: 'json',
                root: 'data'
            }
        },
        // listeners: {
        //     afterload: function (store, operation) {
        //         Ext.getCmp('GroupIds').reset()
        //     }
        // }
    });
    var access_staffs = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'name'],
        autoLoad: true,
        proxy: {
            type: 'ajax',
            url: m_access_staffs,
            reader: {
                type: 'json',
                root: 'data'
            }
        },
    });
    var selected_groups = Ext.create('Ext.data.ArrayStore', {
        fields: ['GroupId', 'GroupName'],
        autoLoad: false
    });
    var langs = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'name'],
        autoLoad: true,
        proxy: {
            type: 'ajax',
            url: m_lang_list,
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });
    var roles = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id', 'name', 'object'],
        autoLoad: true,
        proxy: {
            type: 'ajax',
            url: m_role_list,
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });

    var partners = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id','label'],
        autoLoad: true,
        proxy: {
            type: 'ajax',
            url: m_partner_list,
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });

    var provinces = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id','name'],
        autoLoad: true,
        pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_province_list,
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });
    var districts = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id','name'],
        autoLoad: false,
        // pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_district_list,
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });
    var person_workareas = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id','name'],
        autoLoad: false,
        // pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_work_area_list,
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });
    var person_districts = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id','name'],
        autoLoad: false,
        pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_district_list,
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });
    var subdistricts = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id','name'],
        autoLoad: false,
        pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_subdistrict_list,
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });
    var villages = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id','name'],
        autoLoad: false,
        pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_village_list,
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });
    var person_subdistricts = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id','name'],
        autoLoad: false,
        pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_subdistrict_list,
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });
    var cpgs = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id','name'],
        autoLoad: false,
        pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_cpg_list,
            reader: {
                type: 'json',
                root: 'data'
            }
        },
    });
    var cooperatives = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id','name'],
        autoLoad: false,
        pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_cooperative_list,
            reader: {
                type: 'json',
                root: 'data'
            }
        },
    });
    var sces = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id','name'],
        autoLoad: false,
        pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_sce_list,
            reader: {
                type: 'json',
                root: 'data'
            }
        },
    });
    var traders = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id','name'],
        autoLoad: false,
        pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_trader_list,
            reader: {
                type: 'json',
                root: 'data'
            }
        },
    });
    var warehouses = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id','name'],
        autoLoad: true,
        pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_warehouse_list,
            reader: {
                type: 'json',
                root: 'data'
            }
        },
    });
    var banks = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id','name'],
        autoLoad: true,
        pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_bank_list,
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });
    var bank_branchs = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id','name'],
        autoLoad: false,
        pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_bank_branch_list,
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });
    var cooperative_farmers = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id','name'],
        autoLoad: false,
        pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_farmer_list,
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });
    var sce_farmers = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id','name'],
        autoLoad: false,
        pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_farmer_list,
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });
    var cpg_farmers = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id','name'],
        autoLoad: false,
        pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_farmer_list,
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });

    var status = Ext.create('Ext.data.Store', {
        fields: ['id', 'name'],
        data: [{
            "id": "Yes",
            "name": lang("Yes")
        }, {
            "id": "No",
            "name": lang("No")
        },
        ]
    });

    var status_code = Ext.create('Ext.data.Store', {
        fields: ['id', 'name'],
        data: [{
            "id": "active",
            "name": lang("Active")
        }, {
            "id": "inactive",
            "name": lang("Inactive")
        }, {
            "id": "nullified",
            "name": lang("Nullified")
        },
        ]
    });

    var gender = Ext.create('Ext.data.Store', {
        fields: ['id', 'name'],
        data: [{
            "id": "m",
            "name": lang("Laki-laki")
        }, {
            "id": "f",
            "name": lang("Perempuan")
        },]
    });

    var cooperative_types = Ext.create('Ext.data.Store', {
        fields: ['id', 'name'],
        data: [{
            "id": "farmer",
            "name": lang("Farmer")
        }, {
            "id": "non-farmer",
            "name": lang("Non Farmer")
        },]
    });

    var educations = Ext.create('Ext.data.Store', {
        fields: ['id', 'name'],
        data: [
        {
            "id": "1",
            "name": lang('Belum pernah sekolah'),
        },{
            "id": "2",
            "name": lang('Tidak tamat SD'),
        },{
            "id": "3",
            "name": lang('Tamat SD, tidak melanjutkan'),
        },{
            "id": "4",
            "name": lang('Tamat SMP'),
        },{
            "id": "5",
            "name": lang('Tamat SMA/SMK'),
        },{
            "id": "6",
            "name": lang('Tamat perguruan tinggi'),
        },
        ]
    });

    function displayFormWindow(editable) {
        if (editable===false) {            
            DataForm.query('.textfield, .checkboxfield, .datefield, .combobox, .radiogroup').forEach(function(c){c.setReadOnly(true);});
            DataForm.query('.itemselector').forEach(function(c){c.setDisabled(true);});
            Ext.getCmp('Photo').hide();
            Ext.getCmp('saveButton').hide();
        } else {
            DataForm.query('.textfield, .checkboxfield, .datefield, .combobox, .radiogroup').forEach(function(c){c.setReadOnly(false);});
            DataForm.query('.itemselector').forEach(function(c){c.setDisabled(false);});
            Ext.getCmp('Photo').show();
            Ext.getCmp('saveButton').show();
        }
        if (!win.isVisible()) {
            // resetForm();
            win.show();
        } else {
            win.hide(this, function () {
            });
            win.toFront();
        }
    }

    function set_form_value(data) {
        form_data = data;
        Ext.getCmp('dataForm').getForm().reset();
        if(data) {
            Ext.getCmp('UserId').setValue(data.UserId);
            // Ext.getCmp('UserPassword').setDisabled(true);
            // Ext.getCmp('UserPasswordRe').setDisabled(true);
            Ext.getCmp('UserName').setValue(data.UserName);
            Ext.getCmp('UserIsAdmin').setValue(data.UserIsAdmin);
            Ext.getCmp('UserLanguage').setValue(data.UserLanguage);
            Ext.getCmp('RoleId').setValue(data.RoleId);
            setTimeout(function() {
                Ext.getCmp('GroupIds').setValue(data.groups);
                set_selected_groups ();
                Ext.getCmp('UserGroupIsDefault').setValue(data.UserGroupIsDefault);
            }, 500);
            Ext.getCmp('AccessStaff').setValue(data.access);
            Ext.getCmp('PersonID').setValue(data.PersonID);
            Ext.getCmp('PersonNm').setValue(data.PersonNm);
            Ext.getCmp('Ssn').setValue(data.Ssn);
            Ext.getCmp('EmpNr').setValue(data.EmpNr);
            Ext.getCmp('BirthDate').setValue(data.BirthDate);
            Ext.getCmp('BirthPlace').setValue(data.BirthPlace);
            Ext.getCmp('Gender').setValue(data.Gender);
            Ext.getCmp('Address').setValue(data.Address);
            // Ext.getCmp('ZipCd').setValue(data.ZipCd);
            Ext.getCmp('PersonProvince').setValue(data.ProvinceID);
            // setTimeout(function () {
            //     Ext.getCmp('WorkAreaID').setValue(data.WorkAreaID);
            //     // Ext.getCmp('PersonDistrict').setValue(data.DistrictID);
            //     // setTimeout(function () {
            //     //     Ext.getCmp('PersonSubDistrict').setValue(data.SubDistrictID);
            //     //     setTimeout(function () {
            //     //         Ext.getCmp('VillageID').setValue(data.VillageID);
            //     //     }, 100);
            //     // }, 100);
            // }, 100);
            
            if (parseInt(data.MaritalSt) > 0) { 
                Ext.getCmp('MaritalSt'+data.MaritalSt).setValue(true); 
            }
            // Ext.getCmp('Education').setValue(data.Education);
            if (Ext.getCmp('NationalityNm_'+data.NationalityNm)) Ext.getCmp('NationalityNm_'+data.NationalityNm).setValue(true);
            // if (Ext.getCmp('StatusCd_'+data.StatusCd)) Ext.getCmp('StatusCd_'+data.StatusCd).setValue(true);
            Ext.getCmp('StatusCd').setValue(data.StatusCd);
            Ext.getCmp('WorkPhone').setValue(data.WorkPhone);
            Ext.getCmp('PrivateCellPhone').setValue(data.PrivateCellPhone);
            Ext.getCmp('OfficialCellPhone').setValue(data.OfficialCellPhone);
            Ext.getCmp('PrivateEmail').setValue(data.PrivateEmail);
            Ext.getCmp('OfficialEmail').setValue(data.OfficialEmail);

            if (data.RoleId > 0) {
                var role = Ext.getCmp('RoleId').getStore().getById(data.RoleId).getData();
                selected_role = role.object;
                // console.log(selected_role);
                if (typeof(selected_role) !== 'undefined') {
                    switch (selected_role) {
                        case 'Bank':
                            // bank
                            Ext.getCmp('BankStaffID').setValue(data.BankStaffID);
                            Ext.getCmp('BankBankID').setValue(data.BankBankID);
                            // setTimeout(function(){
                            //     Ext.getCmp('BankBranchID').setValue(data.BankBranchID);
                            // }, 200);
                            break;
                        case 'Cooperative':
                            // cooperative
                            Ext.getCmp('CooperativeStaffID').setValue(data.CooperativeStaffID);
                            Ext.getCmp('CooperativeProvinceID').setValue(data.CooperativeProvinceID);
                            // setTimeout(function () {
                            //     Ext.getCmp('CooperativeDistrictID').setValue(data.CooperativeDistrictID);
                            //     setTimeout(function () {
                            //         Ext.getCmp('CooperativeSubDistrictID').setValue(data.CooperativeSubDistrictID);
                            //         setTimeout(function() {
                            //             Ext.getCmp('CooperativeCoopID').setValue(data.CooperativeCoopID);
                            //             if (data.CooperativeFarmerID) {
                            //                 Ext.getCmp('CooperativeType').setValue('farmer');
                            //                 Ext.getCmp('CooperativeFarmerID').setValue(data.CooperativeFarmerID);
                            //             }
                            //         }, 200)
                            //     }, 200);
                            // }, 200);

                            switch (data.CooperativePosition) {
                                case 'Ketua Badan Pengawas':
                                    Ext.getCmp('CooperativePosition1').setValue(true);
                                    break;
                                case 'Ketua':
                                    Ext.getCmp('CooperativePosition2').setValue(true);
                                    break;
                                case 'Wakil Ketua':
                                    Ext.getCmp('CooperativePosition3').setValue(true);
                                    break;
                                case 'Sekretaris':
                                    Ext.getCmp('CooperativePosition4').setValue(true);
                                    break;
                                case 'Wakil Sekretaris':
                                    Ext.getCmp('CooperativePosition5').setValue(true);
                                    break;
                                case 'Bendahara':
                                    Ext.getCmp('CooperativePosition6').setValue(true);
                                    break;
                                case 'Wakil Bendahara':
                                    Ext.getCmp('CooperativePosition7').setValue(true);
                                    break;
                            }
                            switch (data.CooperativeStaffStatus) {
                                case 'Full-Time':
                                    Ext.getCmp('CooperativeStaffStatus1').setValue(true);
                                    break;
                                case 'Part-Time':
                                    Ext.getCmp('CooperativeStaffStatus2').setValue(true);
                                    break;
                            }

                            if (Ext.getCmp('CooperativePaymentStatus_'+data.CooperativePaymentStatus)) Ext.getCmp('CooperativePaymentStatus_'+data.CooperativePaymentStatus).setValue(true);
                            break;
                        case 'CPG':
                            // CPG
                            Ext.getCmp('CPGStaffID').setValue(data.CPGStaffID);
                            Ext.getCmp('CPGProvinceID').setValue(data.CPGProvinceID);
                            // setTimeout(function () {
                            //     Ext.getCmp('CPGDistrictID').setValue(data.CPGDistrictID);
                            //     setTimeout(function () {
                            //         Ext.getCmp('CPGSubDistrictID').setValue(data.CPGSubDistrictID);
                            //         setTimeout(function(){
                            //             Ext.getCmp('CPGCPGid').setValue(data.CPGCPGid);
                            //             Ext.getCmp('CPGFarmerID').setValue(data.CPGFarmerID);
                            //         }, 200);
                            //     }, 200);
                            // }, 200);

                            switch (data.CPGPosition) {
                                case 'Ketua Badan Pengawas':
                                    Ext.getCmp('CPGPosition1').setValue(true);
                                    break;
                                case 'Ketua':
                                    Ext.getCmp('CPGPosition2').setValue(true);
                                    break;
                                case 'Wakil Ketua':
                                    Ext.getCmp('CPGPosition3').setValue(true);
                                    break;
                                case 'Sekretaris':
                                    Ext.getCmp('CPGPosition4').setValue(true);
                                    break;
                                case 'Wakil Sekretaris':
                                    Ext.getCmp('CPGPosition5').setValue(true);
                                    break;
                                case 'Bendahara':
                                    Ext.getCmp('CPGPosition6').setValue(true);
                                    break;
                                case 'Wakil Bendahara':
                                    Ext.getCmp('CPGPosition7').setValue(true);
                                    break;
                            }
                            break;
                        case 'Extension':
                            // extension
                            Ext.getCmp('ExtensionExtensionID').setValue(data.ExtensionExtensionID);
                            if (Ext.getCmp('ExtensionStaffPosition'+data.ExtensionStaffPosition)) Ext.getCmp('ExtensionStaffPosition'+data.ExtensionStaffPosition).setValue(true);
                            if (Ext.getCmp('ExtensionGovInstitute'+data.ExtensionGovInstitute)) Ext.getCmp('ExtensionGovInstitute'+data.ExtensionGovInstitute).setValue(true);
                            break;
                        case 'Private':
                            // private
                            Ext.getCmp('PrivatePrivateStaffID').setValue(data.PrivatePrivateStaffID);
                            Ext.getCmp('PrivatePartnerID').setValue(data.PrivatePartnerID);
                            break;
                        case 'Program':
                            // program
                            Ext.getCmp('ProgramStaffID').setValue(data.ProgramStaffID);
                            Ext.getCmp('ProgramPartnerID').setValue(data.ProgramPartnerID);
                            if (Ext.getCmp('ProgramPosition'+data.ProgramPosition)) Ext.getCmp('ProgramPosition'+data.ProgramPosition).setValue(true);
                            // Ext.getCmp('ProgramProvinceID').setValue(data.ProgramProvinceID);
                            // setTimeout(function () {
                            //     Ext.getCmp('ProgramWorkArea').setValue(data.ProgramWorkArea);
                            // }, 100);
                            break;
                        case 'SCE':
                            // sce
                            Ext.getCmp('SCEStaffID').setValue(data.SCEStaffID);

                            Ext.getCmp('SCEProvinceID').setValue(data.SCEProvinceID);
                            // setTimeout(function () {
                            //     Ext.getCmp('SCEDistrictID').setValue(data.SCEDistrictID);
                            //     setTimeout(function () {
                            //         Ext.getCmp('SCESubDistrictID').setValue(data.SCESubDistrictID);
                            //         setTimeout(function(){
                            //             Ext.getCmp('SCESceID').setValue(data.SCESceID);
                            //             Ext.getCmp('SCEFarmerID').setValue(data.SCEFarmerID);
                            //         }, 200);
                            //     }, 200);
                            // }, 200);

                            switch (data.SCEPosition) {
                                case 'Ketua Badan Pengawas':
                                    Ext.getCmp('SCEPosition1').setValue(true);
                                    break;
                                case 'Ketua':
                                    Ext.getCmp('SCEPosition2').setValue(true);
                                    break;
                                case 'Wakil Ketua':
                                    Ext.getCmp('SCEPosition3').setValue(true);
                                    break;
                                case 'Sekretaris':
                                    Ext.getCmp('SCEPosition4').setValue(true);
                                    break;
                                case 'Wakil Sekretaris':
                                    Ext.getCmp('SCEPosition5').setValue(true);
                                    break;
                                case 'Bendahara':
                                    Ext.getCmp('SCEPosition6').setValue(true);
                                    break;
                                case 'Wakil Bendahara':
                                    Ext.getCmp('SCEPosition7').setValue(true);
                                    break;
                            }
                            break;
                        case 'Trader':
                            // trader
                            Ext.getCmp('TraderTraderStaffID').setValue(data.TraderTraderStaffID);
                            Ext.getCmp('TraderProvinceID').setValue(data.TraderProvinceID);
                            setTimeout(function () {
                                Ext.getCmp('TraderDistrictID').setValue(data.TraderDistrictID);
                                setTimeout(function () {
                                    Ext.getCmp('TraderSubDistrictID').setValue(data.TraderSubDistrictID);
                                    setTimeout(function(){
                                        Ext.getCmp('TraderTraderID').setValue(data.TraderTraderID);
                                    }, 200);
                                }, 200);
                            }, 200);

                            if (Ext.getCmp('TraderPosition_'+data.TraderPosition)) Ext.getCmp('TraderPosition_'+data.TraderPosition).setValue(true);
                            break;
                        case 'Warehouse':
                            // warehouse
                            Ext.getCmp('WarehouseStaffID').setValue(data.WarehouseStaffID);
                            Ext.getCmp('WarehouseWarehouseID').setValue(data.WarehouseWarehouseID);
                            if (Ext.getCmp('WarehousePosition_'+data.WarehousePosition)) Ext.getCmp('WarehousePosition_'+data.WarehousePosition).setValue(true);
                            break;
                    }
                }
            }
        } else {            
            Ext.getCmp('NationalityNm_local').setValue(true);
            Ext.getCmp('StatusCd').setValue('active');
        }
    }

    function change_panel_staff (role) {
        hide_all_panel();
        if (role) {
            var data = Ext.getCmp('RoleId').getStore().getById(role).getData();
            selected_role = data.object;
            var panel = 'panel_'+data.object;
            if (typeof(Ext.getCmp(panel)) !== 'undefined') {
                Ext.getCmp(panel).show();
            }
        }
    }

    function hide_all_panel () {
        Ext.getCmp('panel_Bank').hide();
        Ext.getCmp('panel_Cooperative').hide();
        Ext.getCmp('panel_CPG').hide();
        Ext.getCmp('panel_Extension').hide();
        Ext.getCmp('panel_Private').hide();
        Ext.getCmp('panel_Program').hide();
        Ext.getCmp('panel_SCE').hide();
        Ext.getCmp('panel_Trader').hide();
        Ext.getCmp('panel_Warehouse').hide();
    }

    function set_selected_groups () {
        var itemSelectorField   = Ext.getCmp('GroupIds');
        var fieldList           = itemSelectorField.toField.store.getRange();
        var value = Ext.getCmp('UserGroupIsDefault').getValue();
        var exist = false;
        selected_groups.removeAll();
        $.each(fieldList, function(index, val) {
            if (value == val.data.GroupId) {
                exist = true;
            }
            selected_groups.add({
                GroupId: val.data.GroupId,
                GroupName: val.data.GroupName,
            });
        });
        if (!exist) {
            Ext.getCmp('UserGroupIsDefault').setValue('');
        }
    }

    var DataForm = Ext.create('Ext.form.Panel', {
        id: 'dataForm',
        frame: false,
        width: 900,
        height: 600,
        autoScroll:true,
        fileUpload: true,
        enctype:'multipart/form-data',
        bodyPadding: 10,
        fieldDefaults: {
            labelAlign: 'left',
            labelWidth: 120,
            padding: 10,
            // anchor: '100%'
        },
        items: [
            {
                xtype: 'panel',
                autoScroll: true,
                items: [
                    {
                        layout: 'column',
                        border: false,
                        items: [
                            {
                                columnWidth: 0.5,
                                layout: 'form',
                                items: [
                                    {
                                        xtype: 'hiddenfield',
                                        id: 'UserId',
                                        name: 'UserId',
                                    },
                                    {
                                        xtype: 'textfield',
                                        fieldLabel: lang('User Name'),
                                        labelWidth: 120,
                                        allowBlank: false,
                                        id: 'UserName',
                                        name: 'UserName'
                                    },
                                    {
                                        xtype: 'textfield',
                                        inputType: 'password',
                                        fieldLabel: lang('Password'),
                                        id: 'UserPassword',
                                        name: 'UserPassword',
                                        validator: function(value){
                                            if (!Ext.getCmp('UserId').getValue() && value === '') {
                                                return lang('Please input password');
                                            }
                                            return true;
                                        }
                                    },
                                    {
                                        xtype: 'textfield',
                                        inputType: 'password',
                                        fieldLabel: lang('Re Type Password'),
                                        id: 'UserPasswordRe',
                                        name: 'UserPasswordRe',
                                        validator: function(value){
                                            if (Ext.getCmp('UserPassword').getValue() !== value) {
                                                return lang('Password confirmation doesn\'t match');
                                            }
                                            return true;
                                        }
                                    },
                                    {
                                        xtype: 'fieldcontainer',
                                        defaultType: 'checkboxfield',
                                        fieldLabel: lang('Is Admin'),
                                        width: '100%',
                                        padding: 0,
                                        hidden: true,
                                        items: [
                                            {
                                                boxLabel: lang('Yes'),
                                                name: 'UserIsAdmin',
                                                inputValue: '1',
                                                id: 'UserIsAdmin'
                                            }
                                        ]
                                    },
                                    {
                                        xtype: 'combobox',
                                        fieldLabel: lang('Interface Language'),
                                        store: langs,
                                        queryMode: 'local',
                                        displayField: 'name',
                                        valueField: 'id',
                                        allowBlank: false,
                                        id: 'UserLanguage',
                                        name: 'UserLanguage'
                                    },
                                    {
                                        xtype: 'hiddenfield',
                                        id: 'PersonID',
                                        name: 'PersonID'
                                    },
                                    {
                                        xtype: 'textfield',
                                        fieldLabel: lang('Full Name'),
                                        allowBlank: false,
                                        id: 'PersonNm',
                                        name: 'PersonNm'
                                    },
                                    {
                                        xtype: 'textfield',
                                        fieldLabel: lang('SSN'),
                                        id: 'Ssn',
                                        name: 'Ssn'
                                    },
                                    {
                                        xtype: 'textfield',
                                        fieldLabel: lang('Employee Number'),
                                        id: 'EmpNr',
                                        name: 'EmpNr'
                                    },
                                    {
                                        xtype: 'datefield',
                                        fieldLabel: lang('Birth Date'),
                                        id: 'BirthDate',
                                        name: 'BirthDate',
                                        format: 'Y-m-d'
                                    },
                                    {
                                        xtype: 'textfield',
                                        fieldLabel: lang('Birth Place'),
                                        id: 'BirthPlace',
                                        name: 'BirthPlace'
                                    },
                                    {
                                        xtype: 'combobox',
                                        fieldLabel: lang('Gender'),
                                        allowBlank: false,
                                        store: gender,
                                        queryMode: 'local',
                                        displayField: 'name',
                                        valueField: 'id',
                                        id: 'Gender',
                                        name: 'Gender'
                                    },
                                    {
                                        xtype: 'textfield',
                                        fieldLabel: lang('Address'),
                                        id: 'Address',
                                        name: 'Address'
                                    },
                                    {
                                        id: 'PersonProvince',
                                        name: 'PersonProvince',
                                        xtype: 'combobox',
                                        fieldLabel: lang('Province'),
                                        store: provinces,
                                        displayField: 'name',
                                        valueField: 'id',
                                        allowBlank: false,
                                        queryMode: 'local',
                                        listeners: {
                                            change: function (cb, nv, ov) {
                                                Ext.getCmp('WorkAreaID').setValue('');
                                                person_workareas.load({
                                                    params: {
                                                        ProvinceID: Ext.getCmp('PersonProvince').getValue()
                                                    }
                                                });

                                                if (typeof(form_data) !== 'undefined')
                                                if (typeof(form_data.WorkAreaID) !== 'undefined') {
                                                    Ext.getCmp('WorkAreaID').setValue(form_data.WorkAreaID);
                                                }
                                                // Ext.getCmp('PersonDistrict').setValue('');
                                                // person_districts.load({
                                                //     params: {
                                                //         ProvinceID: Ext.getCmp('PersonProvince').getValue()
                                                //     }
                                                // });
                                            }
                                        }
                                    },
                                    {
                                        id: 'WorkAreaID',
                                        name: 'WorkAreaID',
                                        xtype: 'combobox',
                                        fieldLabel: lang('Work Area'),
                                        store: person_workareas,
                                        displayField: 'name',
                                        valueField: 'id',
                                        allowBlank: false,
                                        queryMode: 'local',
                                        listeners: {
                                            // change: function (cb, nv, ov) {
                                            //     Ext.getCmp('PersonSubDistrict').setValue('');
                                            //     person_subdistricts.load({
                                            //         params: {
                                            //             DistrictID: Ext.getCmp('WorkAreaID').getValue()
                                            //         }
                                            //     });
                                            // }
                                        }
                                    },
                                    // {
                                    //     id: 'PersonDistrict',
                                    //     name: 'PersonDistrict',
                                    //     xtype: 'combobox',
                                    //     fieldLabel: lang('District'),
                                    //     store: person_districts,
                                    //     displayField: 'name',
                                    //     valueField: 'id',
                                    //     queryMode: 'local',
                                    //     listeners: {
                                    //         change: function (cb, nv, ov) {
                                    //             Ext.getCmp('PersonSubDistrict').setValue('');
                                    //             person_subdistricts.load({
                                    //                 params: {
                                    //                     DistrictID: Ext.getCmp('PersonDistrict').getValue()
                                    //                 }
                                    //             });
                                    //         }
                                    //     }
                                    // },
                                    // {
                                    //     id: 'PersonSubDistrict',
                                    //     name: 'PersonSubDistrict',
                                    //     xtype: 'combobox',
                                    //     fieldLabel: lang('Sub District'),
                                    //     store: person_subdistricts,
                                    //     displayField: 'name',
                                    //     valueField: 'id',
                                    //     queryMode: 'local',
                                    //     listeners: {
                                    //         change: function (cb, nv, ov) {
                                    //             Ext.getCmp('VillageID').setValue('');
                                    //             villages.load({
                                    //                 params: {
                                    //                     SubDistrictID: Ext.getCmp('PersonSubDistrict').getValue()
                                    //                 }
                                    //             });
                                    //         }
                                    //     }
                                    // },
                                    // {
                                    //     id: 'VillageID',
                                    //     name: 'VillageID',
                                    //     xtype: 'combobox',
                                    //     fieldLabel: lang('Village'),
                                    //     allowBlank: false,
                                    //     store: villages,
                                    //     displayField: 'name',
                                    //     valueField: 'id',
                                    //     queryMode: 'local',
                                    // },
                                    // {
                                    //     xtype: 'textfield',
                                    //     fieldLabel: lang('Zip Code'),
                                    //     id: 'ZipCd',
                                    //     name: 'ZipCd'
                                    // },
                                    {
                                        fieldLabel: lang('Marital Status'),
                                        xtype: 'radiogroup',
                                        width: '100%',
                                        columns: 2,
                                        items: [
                                            {
                                                boxLabel: lang('Single'),
                                                id: 'MaritalSt2',
                                                name: 'MaritalSt',
                                                inputValue: '2',
                                            },
                                            {
                                                boxLabel: lang('Menikah'),
                                                id: 'MaritalSt1',
                                                name: 'MaritalSt',
                                                inputValue: '1',
                                            },
                                            {
                                                boxLabel: lang('Janda/Duda'),
                                                id: 'MaritalSt3',
                                                name: 'MaritalSt',
                                                inputValue: '3',
                                            },
                                        ]
                                    },
                                ]
                            },
                            {
                                columnWidth: 0.5,
                                padding: '0 0 0 10',
                                layout: 'form',
                                items: [
                                    {
                                        xtype: 'image',
                                        id: 'iphoto',
                                        height: '180px'
                                    },
                                    {
                                        xtype: 'fileuploadfield',
                                        fieldLabel: lang('Photo'),
                                        labelWidth: 120,
                                        id: 'Photo',
                                        padding: 5,
                                        name: 'Photo',
                                        buttonText: 'Browse',
                                        listeners: {
                                            'change': function (fb, v) {
                                                // do something
                                            }
                                        }
                                    },
                                    // {
                                    //     xtype: 'combobox',
                                    //     fieldLabel: lang('Education'),
                                    //     store: educations,
                                    //     queryMode: 'local',
                                    //     displayField: 'name',
                                    //     valueField: 'id',
                                    //     id: 'Education',
                                    //     name: 'Education',
                                    // },
                                    {
                                        fieldLabel: lang('Nationality'),
                                        xtype: 'radiogroup',
                                        allowBlank: false,
                                        width: '100%',
                                        columns: 2,
                                        items: [
                                            {
                                                boxLabel: lang('Local'),
                                                name: 'NationalityNm',
                                                inputValue: 'local',
                                                id: 'NationalityNm_local'
                                            }, {
                                                boxLabel: lang('Expat'),
                                                name: 'NationalityNm',
                                                inputValue: 'expat',
                                                id: 'NationalityNm_expat'
                                            },
                                        ]
                                    },
                                    {
                                        id: 'StatusCd',
                                        name: 'StatusCd',
                                        xtype: 'combobox',
                                        fieldLabel: lang('Status'),
                                        store: status_code,
                                        displayField: 'name',
                                        allowBlank: false,
                                        valueField: 'id',
                                        queryMode: 'local',
                                    },
                                    // {
                                    //     fieldLabel: lang('Status'),
                                    //     xtype: 'radiogroup',
                                    //     allowBlank: false,
                                    //     width: '100%',
                                    //     columns: 2,
                                    //     items: [
                                    //         {
                                    //             boxLabel: lang('New'),
                                    //             name: 'StatusCd',
                                    //             inputValue: 'new',
                                    //             id: 'StatusCd_new'
                                    //         }, {
                                    //             boxLabel: lang('Active'),
                                    //             name: 'StatusCd',
                                    //             inputValue: 'active',
                                    //             id: 'StatusCd_active'
                                    //         }, {
                                    //             boxLabel: lang('Inactive'),
                                    //             name: 'StatusCd',
                                    //             inputValue: 'inactive',
                                    //             id: 'StatusCd_inactive'
                                    //         }, {
                                    //             boxLabel: lang('Nullified'),
                                    //             name: 'StatusCd',
                                    //             inputValue: 'nullified',
                                    //             id: 'StatusCd_nullified'
                                    //         },
                                    //     ]
                                    // },
                                    {
                                        xtype: 'textfield',
                                        fieldLabel: lang('Work Phone'),
                                        id: 'WorkPhone',
                                        name: 'WorkPhone'
                                    },
                                    {
                                        xtype: 'textfield',
                                        fieldLabel: lang('Private Cell Phone'),
                                        id: 'PrivateCellPhone',
                                        name: 'PrivateCellPhone'
                                    },
                                    {
                                        xtype: 'textfield',
                                        fieldLabel: lang('Official Cell Phone'),
                                        allowBlank: false,
                                        id: 'OfficialCellPhone',
                                        name: 'OfficialCellPhone'
                                    },
                                    {
                                        xtype: 'textfield',
                                        fieldLabel: lang('Private Email'),
                                        vtype: 'email',
                                        id: 'PrivateEmail',
                                        name: 'PrivateEmail',
                                    },
                                    {
                                        xtype: 'textfield',
                                        fieldLabel: lang('Official Email'),
                                        vtype: 'email',
                                        allowBlank: false,
                                        id: 'OfficialEmail',
                                        name: 'OfficialEmail'
                                    },
                                ]
                            }
                        ]
                    },
                ],
            },
            {
                xtype: 'panel',
                items: [
                    {
                        xtype: 'combobox',
                        fieldLabel: lang('Role'),
                        store: roles,
                        queryMode: 'local',
                        displayField: 'name',
                        valueField: 'id',
                        id: 'RoleId',
                        name: 'RoleId',
                        listeners: {
                            change: function(cb, nv, ov) {
                                change_panel_staff(nv);
                                groups.load({
                                    params: {
                                        RoleId: nv,
                                    },
                                    callback: function(records, operation, success) {
                                        Ext.getCmp('GroupIds').reset();
                                        Ext.getCmp('UserGroupIsDefault').reset();
                                        selected_groups.removeAll();
                                    }
                                });
                            }
                        }
                    },
                ]
            },
            // panel bank
            {
                xtype: 'panel',
                id: 'panel_Bank',
                hidden: true,
                border: false,
                bodyPadding: 10,
                items: [
                    {
                        xtype: 'hiddenfield',
                        id: 'BankStaffID',
                        name: 'BankStaffID',
                    },
                    {
                        xtype: 'combobox',
                        fieldLabel: lang('Bank'),
                        store: banks,
                        queryMode: 'local',
                        displayField: 'name',
                        valueField: 'id',
                        id: 'BankBankID',
                        name: 'BankBankID',
                        listeners: {
                            change: function (cb, nv, ov) {
                                Ext.getCmp('BankBranchID').setValue();
                                bank_branchs.load({
                                    params: {
                                        bank: Ext.getCmp('BankBankID').getValue(),
                                    }
                                });
                                if (typeof(form_data) !== 'undefined')
                                if (typeof(form_data.BankBranchID) !== 'undefined') {
                                    Ext.getCmp('BankBranchID').setValue(form_data.BankBranchID);
                                }
                            }
                        }
                    },
                    {
                        xtype: 'combobox',
                        fieldLabel: lang('Branch'),
                        store: bank_branchs,
                        queryMode: 'local',
                        displayField: 'name',
                        valueField: 'id',
                        id: 'BankBranchID',
                        name: 'BankBranchID',
                        validator: function(value){
                            if (selected_role === 'Bank' && value === '') {
                                return lang('Please select branch');
                            }
                            return true;
                        }
                    },
                ]
            },
            // panel cooperative
            {
                xtype: 'panel',
                id: 'panel_Cooperative',
                hidden: true,
                border: false,
                bodyPadding: 10,
                fieldDefaults: {
                    width: 400
                },
                items: [
                    {
                        xtype: 'hiddenfield',
                        id: 'CooperativeStaffID',
                        name: 'CooperativeStaffID',
                    },
                    {
                        id: 'CooperativeProvinceID',
                        name: 'CooperativeProvinceID',
                        xtype: 'combobox',
                        fieldLabel: lang('Province'),
                        store: provinces,
                        displayField: 'name',
                        valueField: 'id',
                        queryMode: 'local',
                        listeners: {
                            change: function (cb, nv, ov) {
                                Ext.getCmp('CooperativeDistrictID').setValue('');
                                districts.load({
                                    params: {
                                        ProvinceID: Ext.getCmp('CooperativeProvinceID').getValue()
                                    }
                                });
                                if (typeof(form_data) !== 'undefined')
                                if (typeof(form_data.CooperativeDistrictID) !== 'undefined') {
                                    Ext.getCmp('CooperativeDistrictID').setValue(form_data.CooperativeDistrictID);
                                }
                            }
                        }
                    },
                    {
                        id: 'CooperativeDistrictID',
                        name: 'CooperativeDistrictID',
                        xtype: 'combobox',
                        fieldLabel: lang('District'),
                        store: districts,
                        displayField: 'name',
                        valueField: 'id',
                        queryMode: 'local',
                        listeners: {
                            change: function (cb, nv, ov) {
                                Ext.getCmp('CooperativeSubDistrictID').setValue('');
                                subdistricts.load({
                                    params: {
                                        DistrictID: Ext.getCmp('CooperativeDistrictID').getValue()
                                    }
                                });
                                if (typeof(form_data) !== 'undefined')
                                if (typeof(form_data.CooperativeSubDistrictID) !== 'undefined') {
                                    Ext.getCmp('CooperativeSubDistrictID').setValue(form_data.CooperativeSubDistrictID);
                                }
                            }
                        }
                    },
                    {
                        id: 'CooperativeSubDistrictID',
                        name: 'CooperativeSubDistrictID',
                        xtype: 'combobox',
                        fieldLabel: lang('Sub District'),
                        store: subdistricts,
                        displayField: 'name',
                        valueField: 'id',
                        queryMode: 'local',
                        listeners: {
                            change: function (cb, nv, ov) {
                                Ext.getCmp('CooperativeCoopID').setValue();
                                Ext.getCmp('CooperativeFarmerID').setValue();
                                cooperatives.load({
                                    params: {
                                        SubDistrictID: Ext.getCmp('CooperativeSubDistrictID').getValue()
                                    }
                                });
                                if (typeof(form_data) !== 'undefined')
                                if (typeof(form_data.CooperativeCoopID) !== 'undefined') {
                                    Ext.getCmp('CooperativeCoopID').setValue(form_data.CooperativeCoopID);
                                }
                                cooperative_farmers.load({
                                    params: {
                                        SubDistrictID: Ext.getCmp('CooperativeSubDistrictID').getValue()
                                    }
                                });
                                if (typeof(form_data) !== 'undefined')
                                if (typeof(form_data.CooperativeFarmerID) !== 'undefined') {
                                    Ext.getCmp('CooperativeFarmerID').setValue(form_data.CooperativeFarmerID);
                                }
                            }
                        }
                    },
                    {
                        xtype: 'combobox',
                        fieldLabel: lang('Cooperative'),
                        store: cooperatives,
                        queryMode: 'local',
                        displayField: 'name',
                        valueField: 'id',
                        id: 'CooperativeCoopID',
                        name: 'CooperativeCoopID',
                        validator: function(value){
                            if (selected_role === 'Cooperative' && value === '') {
                                return lang('Please select cooperative');
                            }
                            return true;
                        }
                    },
                    {
                        xtype: 'combobox',
                        fieldLabel: lang('Type'),
                        store: cooperative_types,
                        queryMode: 'local',
                        displayField: 'name',
                        valueField: 'id',
                        id: 'CooperativeType',
                        name: 'CooperativeType',
                        listeners: {
                            change: function (cb, nv, ov) {
                                if (nv == 'farmer') {
                                    Ext.getCmp('CooperativeFarmerID').show();
                                } else {
                                    Ext.getCmp('CooperativeFarmerID').hide();
                                }
                            }
                        }
                    },
                    {
                        xtype: 'combobox',
                        fieldLabel: lang('Farmer'),
                        store: cooperative_farmers,
                        typeAhead: true,
                        queryMode: 'local',
                        displayField: 'name',
                        valueField: 'id',
                        id: 'CooperativeFarmerID',
                        name: 'CooperativeFarmerID',
                        hidden: true
                    },
                    {
                        xtype: 'radiogroup',
                        fieldLabel: lang('Position'),
                        columns: 3,
                        width: 800,
                        items: [{
                            boxLabel: lang('Ketua Badan Pengawas'),
                            id: 'CooperativePosition1',
                            name: 'CooperativePosition',
                            inputValue:'Ketua Badan Pengawas'
                        },{
                            boxLabel: lang('Ketua'),
                            id: 'CooperativePosition2',
                            name: 'CooperativePosition',
                            inputValue:'Ketua'
                        },{
                            boxLabel: lang('Wakil Ketua'),
                            id: 'CooperativePosition3',
                            name: 'CooperativePosition',
                            inputValue:'Wakil Ketua'
                        },{
                            boxLabel: lang('Sekretaris'),
                            id: 'CooperativePosition4',
                            name: 'CooperativePosition',
                            inputValue:'Sekretaris'
                        },{
                            boxLabel: lang('Wakil Sekretaris'),
                            id: 'CooperativePosition5',
                            name: 'CooperativePosition',
                            inputValue:'Wakil Sekretaris'
                        },{
                            boxLabel: lang('Bendahara'),
                            id: 'CooperativePosition6',
                            name: 'CooperativePosition',
                            inputValue:'Bendahara'
                        },{
                            boxLabel: lang('Wakil Bendahara'),
                            id: 'CooperativePosition7',
                            name: 'CooperativePosition',
                            inputValue:'Wakil Bendahara'
                        }
                        ]
                    },
                    {
                        xtype: 'radiogroup',
                        fieldLabel: lang('Status'),
                        columns: 3,
                        width: 800,
                        items: [{
                            boxLabel: lang('Full-Time'),
                            id: 'CooperativeStaffStatus1',
                            name: 'CooperativeStaffStatus',
                            inputValue:'Full-Time'
                        },{
                            boxLabel: lang('Part-Time'),
                            id: 'CooperativeStaffStatus2',
                            name: 'CooperativeStaffStatus',
                            inputValue:'Part-Time'
                        }
                        ]
                    },
                    {
                        xtype: 'radiogroup',
                        fieldLabel: lang('Payment'),
                        columns: 3,
                        width: 800,
                        items: [{
                            boxLabel: lang('Paid'),
                            id: 'CooperativePaymentStatus_Paid',
                            name: 'CooperativePaymentStatus',
                            inputValue:'Paid'
                        },{
                            boxLabel: lang('Unpaid'),
                            id: 'CooperativePaymentStatus_Unpaid',
                            name: 'CooperativePaymentStatus',
                            inputValue:'Unpaid'
                        }
                        ]
                    },
                ]
            },
            // panel cpg
            {
                xtype: 'panel',
                id: 'panel_CPG',
                hidden: true,
                border: false,
                bodyPadding: 10,
                items: [
                    {
                        xtype: 'hiddenfield',
                        id: 'CPGStaffID',
                        name: 'CPGStaffID',
                    },
                    {
                        id: 'CPGProvinceID',
                        name: 'CPGProvinceID',
                        xtype: 'combobox',
                        fieldLabel: lang('Province'),
                        store: provinces,
                        displayField: 'name',
                        valueField: 'id',
                        queryMode: 'local',
                        listeners: {
                            change: function (cb, nv, ov) {
                                Ext.getCmp('CPGDistrictID').setValue('');
                                districts.load({
                                    params: {
                                        ProvinceID: Ext.getCmp('CPGProvinceID').getValue()
                                    }
                                });
                                if (typeof(form_data) !== 'undefined')
                                if (typeof(form_data.CPGDistrictID) !== 'undefined') {
                                    Ext.getCmp('CPGDistrictID').setValue(form_data.CPGDistrictID);
                                }
                            }
                        }
                    },
                    {
                        id: 'CPGDistrictID',
                        name: 'CPGDistrictID',
                        xtype: 'combobox',
                        fieldLabel: lang('District'),
                        store: districts,
                        displayField: 'name',
                        valueField: 'id',
                        queryMode: 'local',
                        listeners: {
                            change: function (cb, nv, ov) {
                                Ext.getCmp('CPGSubDistrictID').setValue('');
                                subdistricts.load({
                                    params: {
                                        DistrictID: Ext.getCmp('CPGDistrictID').getValue()
                                    }
                                });
                                if (typeof(form_data) !== 'undefined')
                                if (typeof(form_data.CPGSubDistrictID) !== 'undefined') {
                                    Ext.getCmp('CPGSubDistrictID').setValue(form_data.CPGSubDistrictID);
                                }
                            }
                        }
                    },
                    {
                        id: 'CPGSubDistrictID',
                        name: 'CPGSubDistrictID',
                        xtype: 'combobox',
                        fieldLabel: lang('Sub District'),
                        store: subdistricts,
                        displayField: 'name',
                        valueField: 'id',
                        queryMode: 'local',
                        listeners: {
                            change: function (cb, nv, ov) {
                                Ext.getCmp('CPGCPGid').setValue();
                                Ext.getCmp('CPGFarmerID').setValue();
                                cpgs.load({
                                    params: {
                                        SubDistrictID: Ext.getCmp('CPGSubDistrictID').getValue()
                                    }
                                });
                                if (typeof(form_data) !== 'undefined')
                                if (typeof(form_data.CPGCPGid) !== 'undefined') {
                                    Ext.getCmp('CPGCPGid').setValue(form_data.CPGCPGid);
                                }
                                cpg_farmers.load({
                                    params: {
                                        SubDistrictID: Ext.getCmp('CPGSubDistrictID').getValue()
                                    }
                                });
                                if (typeof(form_data) !== 'undefined')
                                if (typeof(form_data.CPGFarmerID) !== 'undefined') {
                                    Ext.getCmp('CPGFarmerID').setValue(form_data.CPGFarmerID);
                                }
                            }
                        }
                    },
                    {
                        xtype: 'combobox',
                        fieldLabel: lang('CPG'),
                        store: cpgs,
                        queryMode: 'local',
                        displayField: 'name',
                        valueField: 'id',
                        id: 'CPGCPGid',
                        name: 'CPGCPGid',
                        validator: function(value){
                            if (selected_role === 'CPG' && value === '') {
                                return lang('Please select CPG');
                            }
                            return true;
                        }
                    },
                    {
                        xtype: 'combobox',
                        fieldLabel: lang('Farmer'),
                        store: cpg_farmers,
                        queryMode: 'local',
                        displayField: 'name',
                        valueField: 'id',
                        id: 'CPGFarmerID',
                        name: 'CPGFarmerID',
                    },
                    {
                        xtype: 'radiogroup',
                        fieldLabel: lang('Position'),
                        columns: 3,
                        width: 800,
                        items: [{
                            boxLabel: lang('Ketua Badan Pengawas'),
                            id: 'CPGPosition1',
                            name: 'CPGPosition',
                            inputValue:'Ketua Badan Pengawas'
                        },{
                            boxLabel: lang('Ketua'),
                            id: 'CPGPosition2',
                            name: 'CPGPosition',
                            inputValue:'Ketua'
                        },{
                            boxLabel: lang('Wakil Ketua'),
                            id: 'CPGPosition3',
                            name: 'CPGPosition',
                            inputValue:'Wakil Ketua'
                        },{
                            boxLabel: lang('Sekretaris'),
                            id: 'CPGPosition4',
                            name: 'CPGPosition',
                            inputValue:'Sekretaris'
                        },{
                            boxLabel: lang('Wakil Sekretaris'),
                            id: 'CPGPosition5',
                            name: 'CPGPosition',
                            inputValue:'Wakil Sekretaris'
                        },{
                            boxLabel: lang('Bendahara'),
                            id: 'CPGPosition6',
                            name: 'CPGPosition',
                            inputValue:'Bendahara'
                        },{
                            boxLabel: lang('Wakil Bendahara'),
                            id: 'CPGPosition7',
                            name: 'CPGPosition',
                            inputValue:'Wakil Bendahara'
                        }
                        ]
                    },
                ]
            },
            // panel extension
            {
                xtype: 'panel',
                id: 'panel_Extension',
                hidden: true,
                border: false,
                bodyPadding: 10,
                items: [
                    {
                        xtype: 'hiddenfield',
                        id: 'ExtensionExtensionID',
                        name: 'ExtensionExtensionID',
                    },
                    {
                        xtype: 'radiogroup',
                        fieldLabel: lang('Institution'),
                        columns: 3,
                        width: 800,
                        items: [{
                            boxLabel: lang('Dinas Perkebunan dan Kehutanan'),
                            id: 'ExtensionGovInstitute1',
                            name: 'ExtensionGovInstitute',
                            inputValue:'1'
                        },{
                            boxLabel: lang('Dinas Kesehatan'),
                            id: 'ExtensionGovInstitute2',
                            name: 'ExtensionGovInstitute',
                            inputValue:'2'
                        },{
                            boxLabel: lang('Dinas Koperasi'),
                            id: 'ExtensionGovInstitute3',
                            name: 'ExtensionGovInstitute',
                            inputValue:'3'
                        },{
                            boxLabel: lang('Badan Penyuluhan'),
                            id: 'ExtensionGovInstitute4',
                            name: 'ExtensionGovInstitute',
                            inputValue:'4'
                        },{
                            boxLabel: lang('Balai Proteksi Tanaman'),
                            id: 'ExtensionGovInstitute5',
                            name: 'ExtensionGovInstitute',
                            inputValue:'5'
                        }],
                        validator: function(value){
                            if (selected_role === 'Extension' && value === '') {
                                return lang('Please select institution');
                            }
                            return true;
                        }
                    },
                    {
                        xtype: 'radiogroup',
                        fieldLabel: lang('Position'),
                        columns: 3,
                        width: 800,
                        items: [{
                            boxLabel: lang('Penyuluh'),
                            id: 'ExtensionStaffPosition1',
                            name: 'ExtensionStaffPosition',
                            inputValue:'1'
                        },{
                            boxLabel: lang('Petugas Teknis'),
                            id: 'ExtensionStaffPosition2',
                            name: 'ExtensionStaffPosition',
                            inputValue:'2'
                        },{
                            boxLabel: lang('Petugas Administratif'),
                            id: 'ExtensionStaffPosition3',
                            name: 'ExtensionStaffPosition',
                            inputValue:'3'
                        },{
                            boxLabel: lang('Kepala Balai/unit/Dinas'),
                            id: 'ExtensionStaffPosition4',
                            name: 'ExtensionStaffPosition',
                            inputValue:'4'
                        }]
                    },
                ]
            },
            // panel private
            {
                xtype: 'panel',
                id: 'panel_Private',
                hidden: true,
                border: false,
                bodyPadding: 10,
                items: [
                    {
                        xtype: 'hiddenfield',
                        id: 'PrivatePrivateStaffID',
                        name: 'PrivatePrivateStaffID',
                    },
                    {
                        xtype: 'combobox',
                        fieldLabel: lang('Partner'),
                        store: partners,
                        queryMode: 'local',
                        displayField: 'label',
                        valueField: 'id',
                        id: 'PrivatePartnerID',
                        name: 'PrivatePartnerID',
                        validator: function(value){
                            if (selected_role === 'Private' && value === '') {
                                return lang('Please select partner');
                            }
                            return true;
                        }
                    },
                ]
            },
            // panel program
            {
                xtype: 'panel',
                id: 'panel_Program',
                hidden: true,
                border: false,
                bodyPadding: 10,
                items: [
                    {
                        xtype: 'hiddenfield',
                        id: 'ProgramStaffID',
                        name: 'ProgramStaffID',
                    },
                    {
                        xtype: 'combobox',
                        fieldLabel: lang('Partner'),
                        store: partners,
                        queryMode: 'local',
                        displayField: 'label',
                        valueField: 'id',
                        id: 'ProgramPartnerID',
                        name: 'ProgramPartnerID',
                        validator: function(value){
                            if (selected_role === 'Program' && value === '') {
                                return lang('Please select partner');
                            }
                            return true;
                        }
                    },
                    {
                        xtype: 'radiogroup',
                        fieldLabel: lang('Position'),
                        width: 800,
                        columns:3,
                        items: [{
                            boxLabel  : lang('Field Fasilitator'),
                            labelWidth: 200,
                            name      : 'ProgramPosition',
                            inputValue: '1',
                            id        : 'ProgramPosition1'
                        },{
                            boxLabel  : lang('District Coordinator'),
                            name      : 'ProgramPosition',
                            inputValue: '2',
                            id        : 'ProgramPosition2'
                        },{
                            boxLabel  : lang('Program Ofiicer'),
                            name      : 'ProgramPosition',
                            inputValue: '3',
                            id        : 'ProgramPosition3'
                        },{
                            boxLabel  : lang('Area Manager'),
                            name      : 'ProgramPosition',
                            inputValue: '4',
                            id        : 'ProgramPosition4'
                        },{
                            boxLabel  : lang('GIS Officer'),
                            name      : 'ProgramPosition',
                            inputValue: '5',
                            id        : 'ProgramPosition5'
                        },{
                            boxLabel  : lang('Monitoring and Evaluation'),
                            name      : 'ProgramPosition',
                            inputValue: '6',
                            id        : 'ProgramPosition6'
                        }]
                    },
                    // {
                    //     layout: 'hbox',
                    //     width: 800,
                    //     items: [
                    //         {
                    //             id: 'ProgramProvinceID',
                    //             name: 'ProgramProvinceID',
                    //             xtype: 'combobox',
                    //             fieldLabel: lang('Work Area'),
                    //             store: provinces,
                    //             displayField: 'name',
                    //             valueField: 'id',
                    //             queryMode: 'local',
                    //             listeners: {
                    //                 change: function (cb, nv, ov) {
                    //                     Ext.getCmp('ProgramWorkArea').setValue('');
                    //                     districts.load({
                    //                         params: {
                    //                             ProvinceID: Ext.getCmp('ProgramProvinceID').getValue()
                    //                         }
                    //                     });
                    //                 }
                    //             }
                    //         },
                    //         {
                    //             id: 'ProgramWorkArea',
                    //             name: 'ProgramWorkArea',
                    //             xtype: 'combobox',
                    //             fieldLabel: ' ',
                    //             labelWidth: 20,
                    //             store: districts,
                    //             displayField: 'name',
                    //             valueField: 'id',
                    //             queryMode: 'local',
                    //             validator: function(value){
                    //                 if (selected_role === 'Program' && value === '') {
                    //                     return lang('Please select work area');
                    //                 }
                    //                 return true;
                    //             }
                    //         }
                    //     ]
                    // },

                ]
            },
            // panel sce
            {
                xtype: 'panel',
                id: 'panel_SCE',
                hidden: true,
                border: false,
                bodyPadding: 10,
                items: [
                    {
                        xtype: 'hiddenfield',
                        id: 'SCEStaffID',
                        name: 'SCEStaffID',
                    },
                    {
                        id: 'SCEProvinceID',
                        name: 'SCEProvinceID',
                        xtype: 'combobox',
                        fieldLabel: lang('Province'),
                        store: provinces,
                        displayField: 'name',
                        valueField: 'id',
                        queryMode: 'local',
                        listeners: {
                            change: function (cb, nv, ov) {
                                Ext.getCmp('SCEDistrictID').setValue('');
                                districts.load({
                                    params: {
                                        ProvinceID: Ext.getCmp('SCEProvinceID').getValue()
                                    }
                                });
                                if (typeof(form_data) !== 'undefined')
                                if (typeof(form_data.SCEDistrictID) !== 'undefined') {
                                    console.log('Set SCE District');
                                    Ext.getCmp('SCEDistrictID').setValue(form_data.SCEDistrictID);
                                }
                            }
                        }
                    },
                    {
                        id: 'SCEDistrictID',
                        name: 'SCEDistrictID',
                        xtype: 'combobox',
                        fieldLabel: lang('District'),
                        store: districts,
                        displayField: 'name',
                        valueField: 'id',
                        queryMode: 'local',
                        listeners: {
                            change: function (cb, nv, ov) {
                                Ext.getCmp('SCESubDistrictID').setValue('');
                                subdistricts.load({
                                    params: {
                                        DistrictID: Ext.getCmp('SCEDistrictID').getValue()
                                    }
                                });
                                if (typeof(form_data) !== 'undefined')
                                if (typeof(form_data.SCESubDistrictID) !== 'undefined') {
                                    Ext.getCmp('SCESubDistrictID').setValue(form_data.SCESubDistrictID);
                                }
                            }
                        }
                    },
                    {
                        id: 'SCESubDistrictID',
                        name: 'SCESubDistrictID',
                        xtype: 'combobox',
                        fieldLabel: lang('Sub District'),
                        store: subdistricts,
                        displayField: 'name',
                        valueField: 'id',
                        queryMode: 'local',
                        listeners: {
                            change: function (cb, nv, ov) {
                                Ext.getCmp('SCESceID').setValue();
                                Ext.getCmp('SCEFarmerID').setValue();
                                sces.load({
                                    params: {
                                        SubDistrictID: Ext.getCmp('SCESubDistrictID').getValue()
                                    }
                                });
                                if (typeof(form_data) !== 'undefined')
                                if (typeof(form_data.SCESceID) !== 'undefined') {
                                    Ext.getCmp('SCESceID').setValue(form_data.SCESceID);
                                }
                                sce_farmers.load({
                                    params: {
                                        SubDistrictID: Ext.getCmp('SCESubDistrictID').getValue()
                                    }
                                });
                                if (typeof(form_data) !== 'undefined')
                                if (typeof(form_data.SCEFarmerID) !== 'undefined') {
                                    Ext.getCmp('SCEFarmerID').setValue(form_data.SCEFarmerID);
                                }
                            }
                        }
                    },
                    {
                        xtype: 'combobox',
                        fieldLabel: lang('SCE'),
                        store: sces,
                        queryMode: 'local',
                        displayField: 'name',
                        valueField: 'id',
                        id: 'SCESceID',
                        name: 'SCESceID',
                        validator: function(value){
                            if (selected_role === 'SCE' && value === '') {
                                return lang('Please select SCE');
                            }
                            return true;
                        }
                    },
                    {
                        xtype: 'combobox',
                        fieldLabel: lang('Farmer'),
                        store: sce_farmers,
                        typeAhead: true,
                        width: 400,
                        queryMode: 'local',
                        displayField: 'name',
                        valueField: 'id',
                        id: 'SCEFarmerID',
                        name: 'SCEFarmerID',
                        validator: function(value){
                            if (selected_role === 'SCE' && value === '') {
                                return lang('Please select farmer');
                            }
                            return true;
                        }
                    },
                    {
                        xtype: 'radiogroup',
                        fieldLabel: lang('Position'),
                        columns: 3,
                        width: 800,
                        items: [{
                            boxLabel: lang('Ketua Badan Pengawas'),
                            id: 'SCEPosition1',
                            name: 'SCEPosition',
                            inputValue:'Ketua Badan Pengawas'
                        },{
                            boxLabel: lang('Ketua'),
                            id: 'SCEPosition2',
                            name: 'SCEPosition',
                            inputValue:'Ketua'
                        },{
                            boxLabel: lang('Wakil Ketua'),
                            id: 'SCEPosition3',
                            name: 'SCEPosition',
                            inputValue:'Wakil Ketua'
                        },{
                            boxLabel: lang('Sekretaris'),
                            id: 'SCEPosition4',
                            name: 'SCEPosition',
                            inputValue:'Sekretaris'
                        },{
                            boxLabel: lang('Wakil Sekretaris'),
                            id: 'SCEPosition5',
                            name: 'SCEPosition',
                            inputValue:'Wakil Sekretaris'
                        },{
                            boxLabel: lang('Bendahara'),
                            id: 'SCEPosition6',
                            name: 'SCEPosition',
                            inputValue:'Bendahara'
                        },{
                            boxLabel: lang('Wakil Bendahara'),
                            id: 'SCEPosition7',
                            name: 'SCEPosition',
                            inputValue:'Wakil Bendahara'
                        }
                        ]
                    },
                ]
            },
            // panel trader
            {
                xtype: 'panel',
                id: 'panel_Trader',
                hidden: true,
                border: false,
                bodyPadding: 10,
                items: [
                    {
                        xtype: 'hiddenfield',
                        id: 'TraderTraderStaffID',
                        name: 'TraderTraderStaffID',
                    },
                    {
                        id: 'TraderProvinceID',
                        name: 'TraderProvinceID',
                        xtype: 'combobox',
                        fieldLabel: lang('Province'),
                        store: provinces,
                        displayField: 'name',
                        valueField: 'id',
                        queryMode: 'local',
                        listeners: {
                            change: function (cb, nv, ov) {
                                Ext.getCmp('TraderDistrictID').setValue('');
                                districts.load({
                                    params: {
                                        ProvinceID: Ext.getCmp('TraderProvinceID').getValue()
                                    }
                                });
                            }
                        }
                    },
                    {
                        id: 'TraderDistrictID',
                        name: 'TraderDistrictID',
                        xtype: 'combobox',
                        fieldLabel: lang('District'),
                        store: districts,
                        displayField: 'name',
                        valueField: 'id',
                        queryMode: 'local',
                        listeners: {
                            change: function (cb, nv, ov) {
                                Ext.getCmp('TraderSubDistrictID').setValue('');
                                subdistricts.load({
                                    params: {
                                        DistrictID: Ext.getCmp('TraderDistrictID').getValue()
                                    }
                                });
                            }
                        }
                    },
                    {
                        id: 'TraderSubDistrictID',
                        name: 'TraderSubDistrictID',
                        xtype: 'combobox',
                        fieldLabel: lang('Sub District'),
                        store: subdistricts,
                        displayField: 'name',
                        valueField: 'id',
                        queryMode: 'local',
                        listeners: {
                            change: function (cb, nv, ov) {
                                Ext.getCmp('TraderTraderID').setValue();
                                traders.load({
                                    params: {
                                        SubDistrictID: Ext.getCmp('TraderSubDistrictID').getValue()
                                    }
                                });
                            }
                        }
                    },
                    {
                        xtype: 'combobox',
                        fieldLabel: lang('Trader'),
                        store: traders,
                        queryMode: 'local',
                        displayField: 'name',
                        valueField: 'id',
                        id: 'TraderTraderID',
                        name: 'TraderTraderID',
                        validator: function(value){
                            if (selected_role === 'Trader' && value === '') {
                                return lang('Please select trader');
                            }
                            return true;
                        }
                    },
                    {
                        xtype: 'radiogroup',
                        fieldLabel: lang('Position'),
                        columns: 3,
                        width: 800,
                        items: [{
                            boxLabel: lang('pemilik'),
                            id: 'TraderPosition_pemilik',
                            name: 'TraderPosition',
                            inputValue:'pemilik'
                        },{
                            boxLabel: lang('staff'),
                            id: 'TraderPosition_staff',
                            name: 'TraderPosition',
                            inputValue:'staff'
                        },{
                            boxLabel: lang('coordinator'),
                            id: 'TraderPosition_coordinator',
                            name: 'TraderPosition',
                            inputValue:'coordinator'
                        }
                        ]
                    },
                    ]
            },
            // panel warehouse
            {
                xtype: 'panel',
                id: 'panel_Warehouse',
                hidden: true,
                border: false,
                bodyPadding: 10,
                items: [
                    {
                        xtype: 'hiddenfield',
                        id: 'WarehouseStaffID',
                        name: 'WarehouseStaffID',
                    },
                    {
                        xtype: 'combobox',
                        fieldLabel: lang('Warehouse'),
                        store: warehouses,
                        queryMode: 'local',
                        displayField: 'name',
                        valueField: 'id',
                        id: 'WarehouseWarehouseID',
                        name: 'WarehouseWarehouseID',
                        validator: function(value){
                            if (selected_role === 'Warehouse' && value === '') {
                                return lang('Please select warehouse');
                            }
                            return true;
                        }
                    },
                    {
                        xtype: 'radiogroup',
                        fieldLabel: lang('Position'),
                        columns: 3,
                        width: 800,
                        items: [{
                            boxLabel: lang('pemilik'),
                            id: 'WarehousePosition_pemilik',
                            name: 'WarehousePosition',
                            inputValue:'pemilik'
                        },{
                            boxLabel: lang('staff'),
                            id: 'WarehousePosition_staff',
                            name: 'WarehousePosition',
                            inputValue:'staff'
                        },{
                            boxLabel: lang('coordinator'),
                            id: 'WarehousePosition_coordinator',
                            name: 'WarehousePosition',
                            inputValue:'coordinator'
                        }
                        ]
                    },
                ]
            },
            {
                xtype: 'itemselector',
                id: 'GroupIds',
                name: 'GroupIds',
                fieldLabel: lang('Group'),
                fromTitle: lang('Available'),
                toTitle: lang('Selected'),
                anchor: '100%',
                height:320,
                store: groups,
                displayField: 'GroupName',
                valueField: 'GroupId',
                value: [],
                allowBlank: false,
                msgTarget: 'side',
                listeners: {
                    change: function() {
                        set_selected_groups();
                    }
                }
            },
            {
                id: 'UserGroupIsDefault',
                name: 'UserGroupIsDefault',
                xtype: 'combobox',
                fieldLabel: lang('Default Group'),
                width: 400,
                store: selected_groups,
                displayField: 'GroupName',
                valueField: 'GroupId',
                queryMode:'local',
            },
            {
                xtype: 'itemselector',
                id: 'AccessStaff',
                name: 'AccessStaff',
                fieldLabel: lang('Access Area'),
                fromTitle: lang('Available'),
                toTitle: lang('Selected'),
                anchor: '100%',
                height:320,
                store: access_staffs,
                displayField: 'name',
                valueField: 'id',
                value: [],
                // allowBlank: false,
                msgTarget: 'side',
            },
        ],
        buttons: [{
            id: 'saveButton',
            text: 'Save',
            margin: '5px',
            scale: 'large',
            ui: 's-button',
            cls: 's-blue',
            handler: function () {
                var form = this.up('form').getForm();
                var methode;
                if (Ext.getCmp('UserId').getValue() === '') methode = 'POST'; else methode = 'PUT';
                if (form.isValid()) {
                    form.submit({
                        url: m_crud,
                        method: methode,
                        waitMsg: 'Sending data...',
                        success: function (fp, o) {
                            Ext.MessageBox.alert('Success', 'Data saved.');
                        }
                    });
                    win.hide(this, function () {
                        store.load({
                            params: {
                                key: Ext.getCmp('key').getValue()
                            }
                        });
                    });
                }
            }
        }, {
            text: 'Close',
            margin: '5px',
            scale: 'large',
            ui: 's-button',
            cls: 's-grey',
            disabled: false,
            handler: function () {
                win.hide();
            }
        }]
    });
    var win = Ext.create('widget.window', {
        title: 'Data User',
        frame: false,
        closable: true,
        id: 'win',
        modal: true,
        closeAction: 'show',
        width: 900,
        minWidth: 570,
        height: 600,
        layout: 'fit',
        items: [DataForm]
    });

    function submitOnEnter(field, event) {
        if (event.getKey() == event.ENTER) {
            filterRecord();
        }
    }

    function filterRecord() {
        store.load({
            params: {
                start: 0,
                key: Ext.getCmp('key').getValue(),
                RoleId: Ext.getCmp('filter-RoleId').getValue(),
                GroupId: Ext.getCmp('filter-GroupId').getValue(),
                Status: Ext.getCmp('filter-Status').getValue(),
            }
        });
    }
    var contextMenuGrid = Ext.create('Ext.menu.Menu',{
        items: [
        {
            icon: varjs.config.base_url + 'images/icons/new/view.png',
            text: lang('View'),
            hidden: false,
            handler: function() {
                var sm = Ext.getCmp('grid').getSelectionModel().getSelection()[0];
                displayFormWindow(false);
                Ext.Ajax.request({
                    url: m_crud,
                    method: 'GET',
                    params: {UserId: sm.get('UserId')},
                    success: function (fp, o) {
                        var data = Ext.decode(fp.responseText);
                        set_form_value(data);
                    }
                });
            }
        },
        {
            icon: varjs.config.base_url + 'images/icons/new/update.png',
            text: lang('Update'),
            hidden: !m_act_update,
            handler: function(){
                var sm = Ext.getCmp('grid').getSelectionModel().getSelection()[0];
                displayFormWindow(true);
                Ext.Ajax.request({
                    url: m_crud,
                    method: 'GET',
                    params: {UserId: sm.get('UserId')},
                    success: function (fp, o) {
                        var data = Ext.decode(fp.responseText);
                        set_form_value(data);
                    }
                });
            }
        },
        {
            icon: varjs.config.base_url + 'images/icons/new/delete.png',
            text: lang('Delete'),
            hidden: !m_act_delete,
            handler: function() {
                var smb = Ext.getCmp('grid').getSelectionModel().getSelection()[0];
                Ext.MessageBox.confirm('Message', lang('Apakah anda mau menghapus data ini ?'), function (btn) {
                    if (btn == 'yes') {
                        Ext.Ajax.request({
                            waitMsg: lang('Please Wait'),
                            url: m_crud,
                            method: 'DELETE',
                            params: {UserId: smb.raw.UserId},
                            success: function (response, opts) {
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
                            failure: function (response, opts) {
                                var obj = Ext.decode(response.responseText);
                                Ext.MessageBox.alert('error', 'Could not connect to the database. Retry later');
                            }
                        });
                    }
                });
            }
        }
        ]
    });

    var grid = Ext.create('Ext.grid.Panel', {
        store: store,
        width: '100%',
        id: 'grid',
        minHeight: 250,
        //title: 'User List',
        style: 'border:1px solid #CCC;',
        renderTo: 'ext-content',
        loadMask: true,
        selType: 'rowmodel',
        listeners: {
            itemclick: function(view, record, item, index, e){
               contextMenuGrid.showAt(e.getXY());
            }
            // itemdblclick: function (dv, record, item, index, e) {
            //     displayFormWindow();
            //     var sm = record;
            //     Ext.Ajax.request({
            //         url: m_crud,
            //         method: 'GET',
            //         params: {UserId: sm.get('UserId')},
            //         success: function (fp, o) {
            //             var data = Ext.decode(fp.responseText);
            //             set_form_value(data);
            //         }
            //     });
            // }
        },
        dockedItems: [{
            xtype: 'pagingtoolbar',
            store: store,   // same store GridPanel is using
            dock: 'bottom',
            displayInfo: true
        }, {
            xtype: 'toolbar',
            items: [
                {
                    icon: varjs.config.base_url + 'images/icons/new/add.png', cls:'Sfr_BtnGridGreen', overCls:'Sfr_BtnGridGreen-Hover',
                    text: 'Add',
                    scope: this,
                    handler: function() {
                        displayFormWindow(true);
                        set_form_value();
                    },
                    cls: m_act_add?'':'hidden'
                }, 
                // {
                //     itemId: 'remove',
                //     icon: varjs.config.base_url + 'images/icons/new/delete.png',
                //     cls: m_act_delete,
                //     text: 'Hapus',
                //     scope: this,
                //     handler: function () {
                //         var smb = Ext.getCmp('grid').getSelectionModel().getSelection()[0];
                //         Ext.MessageBox.confirm('Message', lang('Apakah anda mau menghapus data ini ?'), function (btn) {
                //             if (btn == 'yes') {
                //                 Ext.Ajax.request({
                //                     waitMsg: lang('Please Wait'),
                //                     url: m_crud,
                //                     method: 'DELETE',
                //                     params: {UserId: smb.raw.UserId},
                //                     success: function (response, opts) {
                //                         var obj = Ext.decode(response.responseText);
                //                         switch (obj.success) {
                //                             case true:
                //                                 store.load();
                //                                 break;
                //                             default:
                //                                 Ext.MessageBox.alert('Warning', obj.message);
                //                                 break;
                //                         }
                //                     },
                //                     failure: function (response, opts) {
                //                         var obj = Ext.decode(response.responseText);
                //                         Ext.MessageBox.alert('error', 'Could not connect to the database. Retry later');
                //                     }
                //                 });
                //             }
                //         });
                //     }
                // },                 
                {
                    xtype: 'combobox',
                    id: 'filter-RoleId',
                    // fieldLabel: lang('Interface Language'),
                    emptyText: lang('Role'),
                    store: roles,
                    queryMode: 'local',
                    displayField: 'name',
                    valueField: 'id',
                    listeners: {
                        change: function (cb, nv, ov) {
                            Ext.getCmp('filter-GroupId').setValue('');
                            groups.load({
                                params: {
                                    RoleId: nv
                                }
                            });
                        }
                    }
                },
                {
                    xtype: 'combobox',
                    id: 'filter-GroupId',
                    // fieldLabel: lang('Interface Language'),
                    emptyText: lang('Group'),
                    store: groups,
                    queryMode: 'local',
                    displayField: 'GroupName',
                    valueField: 'GroupId',
                    width: 400,
                },
                {
                    xtype: 'combobox',
                    id: 'filter-Status',
                    // fieldLabel: lang('Interface Language'),
                    emptyText: lang('Active'),
                    store: status,
                    queryMode: 'local',
                    displayField: 'name',
                    valueField: 'id',
                    width: 100,
                },
                {
                    xtype: 'textfield',
                    emptyText: lang('Username'),
                    name: 'key', baseCls:'Sfr_TxtfieldSearchGrid',
                    id: 'key',
                    listeners: {
                        specialkey: submitOnEnter
                    }
                }, 
                {
                    xtype: 'button',
                    margin: '0px 0px 0px 6px',
                    text: 'Search',
                    handler: function () {
                        filterRecord();
                    }
                }]
        }],
        columns: [
            {
                text: 'ID',
                dataIndex: 'UserId',
                hidden: true
            },
            {
                text: 'No',
                xtype: 'rownumberer',
                align: 'center',
                width: 50,
            },
            {
                text: lang('Real Name'),
                flex: 2,
                dataIndex: 'UserRealName'
            },
            {
                text: lang('User Name'),
                flex: 2,
                dataIndex: 'UserName'
            },
            {
                text: lang('Active'),
                flex: 1,
                dataIndex: 'UserActive'
            },
            {
                text: lang('Role'),
                flex: 1,
                dataIndex: 'RoleName'
            },
            {
                text: lang('Group'),
                flex: 4,
                dataIndex: 'GroupName'
            }]
    });
});
