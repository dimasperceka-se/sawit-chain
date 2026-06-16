Ext.onReady(function(){
   //grid standard
    //grid standard quality
   Ext.define('var_quality.Model', {
        extend: 'Ext.data.Model',
        fields: ['DetailID','StandardID','Name','Order','FAQFormula','FFFormula','FAQValue','FFValue'],
   });

    function displayAddArea() {
        if (!winAddArea.isVisible()) {
            winAddArea.show();
        } else {
            winAddArea.hide(this, function(){});
            winAddArea.toFront();
        }
    }
   var store_var_quality = Ext.create('Ext.data.Store', {
      model: 'var_quality.Model',
      autoLoad: false,
      pageSize: 10,
      proxy: {
         type: 'ajax',
         url: m_var_quality,
         reader: {
            type: 'json',
            root: 'data',
            totalProperty: 'total'
         }
      }
   });
   var vqRowEditing = Ext.create('Ext.grid.plugin.RowEditing', {
      id: 'vqRowEditing',
      clicksToMoveEditor: 0,
      autoCancel: false,
      errorSummary : false,
      clicksToEdit: 2
   });
    //end grid standard quality
    var DataFormStandard = Ext.create('Ext.form.Panel', {
        frame: false,
        height: 450,
        width: 800,
        bodyPadding: 5,
        id: 'DataFormStandard',
        fieldDefaults: {
            labelAlign: 'left',
            labelWidth: 100,
            anchor: '100%'
        },
        items: [{
         xtype: 'fieldset',
         items: [{
                xtype: 'textfield',
                id: 'VarStandardID',
                name: 'StandardID',
                inputType: 'hidden'
            },{
                xtype: 'textfield',
                id: 'VarSupplychainID',
                name: 'SupplychainID',
                inputType: 'hidden'
            },
            {
                xtype: 'textfield',
                fieldLabel: 'Standard Name',
                allowBlank: false,
                id: 'VarStandardName',
                name: 'StandardName'
            },{
                xtype: 'checkboxgroup',
                labelWidth: 270,
                fieldLabel: lang('Jenis Reward'),
                width:'100%',
                items: [{
                    boxLabel: lang('Reward'),
                    name: 'IsReward',
                    id: 'IsReward',
                    uncheckedValue:'0',
                    inputValue: '1'
                },{
                    boxLabel: lang('Claim'),
                    name: 'IsClaim',
                    id: 'IsClaim',
                    uncheckedValue:'0',
                    inputValue: '1'
                }]
            }]
         },{
               xtype: 'gridpanel',
               id:'grid_var_quality',
               store: store_var_quality,
               width: '100%',
               height:150,
               loadMask: true,
               selType: 'rowmodel',
               dockedItems: [{
                  xtype: 'toolbar',
                  id:'toolbar_var_quality',
                  items: [{
                     icon: varjs.config.base_url + 'images/icons/new/add.png', cls:'Sfr_BtnGridGreen', overCls:'Sfr_BtnGridGreen-Hover',
                     text: lang('Add'),
                     cls : m_act_save,
                     scope: this,
                     handler :function(){
                          vqRowEditing.cancelEdit();
                          var r = Ext.create('var_quality.Model', {
                              DetailID:'',StandardID:'',Name:'',Order:'',FAQFormula:'',FFFormula:'',FAQValue:'',FFValue:''});
                          store_var_quality.insert(0, r);
                          vqRowEditing.startEdit(0, 0);
                     }
                  },{
                     icon: varjs.config.base_url+'images/icons/silk/pencil.png',
                     cls:m_act_save,
                     text: lang('Edit'),
                     scope: this,
                     handler : function() {
                       vqRowEditing.cancelEdit();
                       var sm = Ext.getCmp('grid_var_quality').getSelectionModel().getSelection();
                       vqRowEditing.startEdit(sm[0].index, 0);
                     }
                  },{
                     itemId: 'remove',
                     icon: varjs.config.base_url+'images/icons/silk/delete.png',
                     text: lang('Hapus'),
                     scope: this,
                     handler : function(){
                       var smb = Ext.getCmp('grid_var_quality').getSelectionModel().getSelection()[0];
                       vqRowEditing.cancelEdit();
                       Ext.MessageBox.confirm('Message', lang('Apakah anda mau menghapus data ini ?') , function(btn){
                           if(btn == 'yes'){
                              Ext.Ajax.request({
                                 waitMsg: lang('Please Wait'),
                                 url: m_var_quality,
                                 method : 'DELETE',
                                 params: {
                                    id:  smb.raw.DetailID
                                 },
                                 success: function(response, opts){
                                    var obj = Ext.decode(response.responseText);
                                    switch(obj.success){
                                       case true:
                                          store_var_quality.load({
                                             params: {
                                                id: Ext.getCmp('VarStandardID').getValue()
                                          }});
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
               columns: [{
                 text: lang('No'),
                 xtype: 'rownumberer',
                 width:'5%'
               },{
                 text: lang('Nama'),
                 dataIndex: 'Name',
                 width:'20%',
                 editor: {
                    xtype:'textfield',
                    allowBlank:false
                 }
               },{
                 text: lang('Order'),
                 dataIndex: 'Order',
                 width:'10%',
                 editor: {
                    xtype:'textfield',
                    allowBlank:false
                 }
               },{
                 text: lang('FF Formula'),
                 dataIndex: 'FFFormula',
                 width:'20%',
                 editor: {
                    xtype:'textfield'
                 }
               },{
                 text: lang('FAQ Formula'),
                 dataIndex: 'FAQFormula',
                 width:'20%',
                 editor: {
                    xtype:'textfield'
                 }
               },{
                 text: lang('FF Value'),
                 dataIndex: 'FFValue',
                 width:'10%',
                 editor: {
                    xtype:'textfield'
                 }
               },{
                 text: lang('FAQ Value'),
                 dataIndex: 'FAQValue',
                 width:'10%',
                 editor: {
                    xtype:'textfield'
                 }
               }],
               plugins: [vqRowEditing],
               listeners: {
                   'canceledit':function(editor,e,eOpts){
                        store_var_quality.load({
                           params: {
                              id: Ext.getCmp('VarStandardID').getValue()
                        }});
                   },
                   'edit': function(editor, e) {
                     console.log(e.record.data)
                     if(e.record.data.DetailID==''){
                        Ext.Ajax.request({
                            waitMsg: lang('Please wait...'),
                            url: m_var_quality,
                            method : 'POST',
                            params: {
                               StandardID      : Ext.getCmp('VarStandardID').getValue(),
                               Name    : e.record.data.Name,
                               Order      : e.record.data.Order,
                               FAQFormula    : e.record.data.FAQFormula,
                               FFFormula    : e.record.data.FFFormula,
                               FAQValue    : e.record.data.FAQValue,
                               FFValue    : e.record.data.FFValue
                            },
                            success: function(response, opts){
                                var obj = Ext.decode(response.responseText);
                                switch(obj.success){
                                    case true:
                                       Ext.MessageBox.alert('Success',obj.message);
                                       store_var_quality.load({
                                          params: {
                                             id: Ext.getCmp('VarStandardID').getValue()
                                       }});
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
                        Ext.MessageBox.confirm('Message', lang('Update data variabel standard quality ini ?') , function(btn){
                           if(btn == 'yes') {
                              Ext.Ajax.request({
                                 waitMsg: lang('Please wait...'),
                                 url: m_var_quality,
                                 method : 'PUT',
                                 params: {
                                     DetailID    : e.record.data.DetailID,
                                     Name    : e.record.data.Name,
                                     Order      : e.record.data.Order,
                                     FAQFormula    : e.record.data.FAQFormula,
                                     FFFormula    : e.record.data.FFFormula,
                                     FAQValue    : e.record.data.FAQValue,
                                     FFValue    : e.record.data.FFValue
                                 },
                                 success: function(response, opts){
                                     var obj = Ext.decode(response.responseText);
                                     switch(obj.success){
                                         case true:
                                            Ext.MessageBox.alert('Success',obj.message);
                                            store_var_quality.load({
                                                params: {
                                                   id: Ext.getCmp('VarStandardID').getValue()
                                            }});
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
                  },
                  show: function(c){
                    store_var_quality.load({
                        params: {
                            id: Ext.getCmp('VarStandardID').getValue()
                    }});
                  }
               }
            }
        ],
        buttons: [{
            // id: 'saveButton',
            text: 'Save',
            margin: '5px',
            scale: 'large',
            ui: 's-button',
            cls: 's-blue',
            handler: function () {
                var form = this.up('form').getForm();
                if (form.isValid()) {
                    var methode;
                    if (Ext.getCmp('VarStandardID').getValue() == '') methode = 'POST'; else methode = 'PUT';
                    form.submit({
                        url: m_quality_standard,
                        method: methode,
                        waitMsg: 'Sending data...',
                        success: function (fp, o) {
                            Ext.getCmp('VarStandardID').setValue(o.result.id);
                            Ext.MessageBox.alert('Success', 'Data saved.');
                            Ext.getCmp('toolbar_var_quality').show();
                            //**//
                                 store_quality_standard.load({
                                    params: {
                                           id: Ext.getCmp('SupplychainID').getValue()
                                 }});
                            //**//
                        }
                    });
                } else {
                    Ext.MessageBox.alert('Warning', lang('Silahkan isi form dengan data yang benar'));
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
                winFormStandard.hide();
            }
        }]
    });
    var winFormStandard = Ext.create('widget.window', {
        title: 'Standard',
        frame: false,
        closable: true,
        id: 'winFormStandard',
        modal: true,
        closeAction: 'hide',
        width: 830,
        minWidth: 370,
        height: 420,
        layout: 'fit',
        listeners:{
            hide: function(){
                DataFormStandard.getForm().reset();
                store_var_quality.removeAll();
            }
        },
        items: [DataFormStandard]
    });
    function add_standard() {
        Ext.getCmp('DataFormStandard').getForm().reset();
        if (!winFormStandard.isVisible()) {
            winFormStandard.show();
        } else {
            winFormStandard.hide();
            winFormStandard.toFront();
        }
        Ext.getCmp('VarSupplychainID').setValue(Ext.getCmp('SupplychainID').getValue())
        Ext.getCmp('toolbar_var_quality').hide();
    }
    function update_standard () {
        add_standard();
        var sm = Ext.getCmp('grid_quality_standard').getSelectionModel().getSelection()[0];
        Ext.Ajax.request({
            url: m_quality_standard,
            method: 'GET',
            params: {StandardID: sm.get('StandardID')},
            success: function (fp, o) {
                var r = Ext.decode(fp.responseText);
                Ext.getCmp('VarStandardID').setValue(sm.get('StandardID'));
                Ext.getCmp('VarStandardName').setValue(r.StandardName);
                if(r.IsReward === '1') Ext.getCmp('IsReward').setValue(1);
                if(r.IsClaim === '1') Ext.getCmp('IsClaim').setValue(1);
            }
        });
         store_var_quality.load({params: {id: sm.get('StandardID')}});
         Ext.getCmp('toolbar_var_quality').show();
    }
   //end grid standard

   //update
   function update(ids) {
      displayFormWindow();
      Ext.getCmp('all_panel').show();
      mc_Kabupaten.load({params: {SupplychainID: ids}});
      Ext.Ajax.request({
         url: m_crud+'data',
         method: 'GET',
         params: {id: ids},
         success: function(fp, o){

            var r = Ext.decode(fp.responseText);
            Ext.getCmp('SupplychainID').setValue(ids);
            Ext.getCmp('ObjType').setValue(r.OrgType);
            Ext.getCmp('ObjID').setValue(r.OrgID);
            Ext.getCmp('Kab').setValue(r.kab);
            store_relasi.load({
               params: {
                  id: Ext.getCmp('SupplychainID').getValue()
            }});
            store_area.load({
               params: {
                  SupplychainID: Ext.getCmp('SupplychainID').getValue()
            }});
            store_perwakilan.load({
               params: {
                  id: Ext.getCmp('SupplychainID').getValue()
            }});
            store_quality_standard.load({
               params: {
                  id: Ext.getCmp('SupplychainID').getValue()
            }});
            store_quality.load({
               params: {
                  id: Ext.getCmp('SupplychainID').getValue()
            }});
            store_premium.load({
               params: {
                  id: Ext.getCmp('SupplychainID').getValue()
            }});
            store_kurs.load({
               params: {
                  id: Ext.getCmp('SupplychainID').getValue()
            }});
            store_price.load({
               params: {
                  id: Ext.getCmp('SupplychainID').getValue()
            }});
            store_package.load({
               params: {
                  id: Ext.getCmp('SupplychainID').getValue()
            }});
            mc_child.load({
                params: {
                    id: Ext.getCmp('SupplychainID').getValue()
                }
            });
            Ext.getCmp('PerwakilanKoperasi').setValue(r.PerwakilanKoperasi);
            if (r.IsFF == '1') Ext.getCmp('IsFF').setValue(true);
            if (r.IsFAQ == '1') Ext.getCmp('IsFAQ').setValue(true);
            if (r.PembelianNonFarmer == '1') Ext.getCmp('PembelianNonFarmer').setValue(true);
            if (r.PembelianFarmer == '1') Ext.getCmp('PembelianFarmer').setValue(true);
            if (r.PembelianFarmerCert == '1') Ext.getCmp('PembelianFarmerCert').setValue(true);
            if (r.PembelianBatch == '1') Ext.getCmp('PembelianBatch').setValue(true);
            if (r.KalkulasiPremium=='1') Ext.getCmp('KalkulasiPremium').setValue(true);
            else if (r.KalkulasiPremium=='2') Ext.getCmp('KalkulasiPremium2').setValue(true);
            else if (r.KalkulasiPremium=='3') Ext.getCmp('KalkulasiPremium3').setValue(true);
            if (r.LabelKarung=='1') Ext.getCmp('LabelKarung').setValue(true);
            else Ext.getCmp('LabelKarung2').setValue(true);
            if (r.IsFakturNumber=='1') Ext.getCmp('IsFakturNumber').setValue(true);
            else Ext.getCmp('IsFakturNumber2').setValue(true);
            if (r.IsMoistureKarung=='1') Ext.getCmp('IsMoistureKarung').setValue(true);
            else Ext.getCmp('IsMoistureKarung2').setValue(true);
            if (r.IsGeneratePacking=='1') Ext.getCmp('IsGeneratePacking').setValue(true);
            else Ext.getCmp('IsGeneratePacking2').setValue(true);
            if (r.GenerateWeightOrPackage=='Weight') Ext.getCmp('GenerateWeightOrPackage').setValue(true);
            else if (r.GenerateWeightOrPackage=='Package') Ext.getCmp('GenerateWeightOrPackage2').setValue(true);
            else if (r.GenerateWeightOrPackage=='Both') Ext.getCmp('GenerateWeightOrPackage3').setValue(true);
            else if (r.GenerateWeightOrPackage=='Transaction') Ext.getCmp('GenerateWeightOrPackageTransaction').setValue(true);

            if(r.IsGenerateBuyingUnitBatch=='1'){ Ext.getCmp('IsGenerateBuyingUnitBatch').setValue(true); } else { Ext.getCmp('IsGenerateBuyingUnitBatch2').setValue(true); }

            if (r.IsDriver=='1') Ext.getCmp('IsDriver').setValue(true);
            else Ext.getCmp('IsDriver2').setValue(true);
            if (r.IsPoliceNumber=='1') Ext.getCmp('IsPoliceNumber').setValue(true);
            else Ext.getCmp('IsPoliceNumber2').setValue(true);
            if (r.IsVehicleType=='1') Ext.getCmp('IsVehicleType').setValue(true);
            else Ext.getCmp('IsVehicleType2').setValue(true);
            if (r.IsDriverPosition=='1') Ext.getCmp('IsDriverPosition').setValue(true);
            else Ext.getCmp('IsDriverPosition2').setValue(true);
            if (r.IsDriverAddress=='1') Ext.getCmp('IsDriverAddress').setValue(true);
            else Ext.getCmp('IsDriverAddress2').setValue(true);
            if (r.IsSuratJalan=='1') Ext.getCmp('IsSuratJalan').setValue(true);
            else Ext.getCmp('IsSuratJalan2').setValue(true);
            if (r.PemisahanBatch=='1') Ext.getCmp('PemisahanBatch').setValue(true);
            else Ext.getCmp('PemisahanBatch2').setValue(true);
            if (r.IsGeneratePo=='1') Ext.getCmp('IsGeneratePo').setValue(true);
            else Ext.getCmp('IsGeneratePo2').setValue(true);
            if (r.IsLockDestWeigh=='1') Ext.getCmp('IsLockDestWeigh').setValue(true);
            else Ext.getCmp('IsLockDestWeigh2').setValue(true);
            if (r.IsAutoBatch=='1') Ext.getCmp('IsAutoBatch').setValue(true);
            else Ext.getCmp('IsAutoBatch2').setValue(true);
            Ext.getCmp('FormulaNettoKarung').setValue(r.FormulaNettoKarung);
            Ext.getCmp('StandardMoistureKarung').setValue(r.StandardMoistureKarung);
            Ext.getCmp('FormulaNettoPrice').setValue(r.FormulaNettoPrice);
            Ext.getCmp('FormulaNettoAkhir').setValue(r.FormulaNettoAkhir);
            Ext.getCmp('LabelFarmerCertified').setValue(r.LabelFarmerCertified);
            Ext.getCmp('LabelFarmerNonCertified').setValue(r.LabelFarmerNonCertified);
            Ext.getCmp('LabelNonFarmer').setValue(r.LabelNonFarmer);
            Ext.getCmp('LabelFAQ').setValue(r.LabelFAQ);
            Ext.getCmp('LabelFF').setValue(r.LabelFF);
            Ext.getCmp('SentEmail').setValue(r.SentEmail);
            mc_Desa.load({params: {SupplychainID: Ext.getCmp('SupplychainID').getValue()}});

         }
      });
   }
   //end update
   //grid & tambah
   var mc_type = Ext.create('Ext.data.Store', {
       fields: ['label'],
       data : [
           {"id":"warehouse","label":lang("Gudang")},
           {"id":"trader","label":lang("Pedagang")},
           {"id":"cpg","label":lang("Kelompok Petani")},
           {"id":"koperasi","label":lang("Organisasi Petani")},
           {"id":"sce","label":lang("Professional Farmer")},
       ]
   });
   var mc_Kabupaten = Ext.create('Ext.data.Store', {
      extend: 'Ext.data.Model',
      fields: ['id','label'],
      autoLoad: false,
      pageSize: 10,
      proxy: {
         type: 'ajax',
         url: m_Kabupaten,
         reader: {
            type: 'json',
            root: 'data'
         }
      }
   });
   //tambah
    //premium
   Ext.define('premium.Model', {
        extend: 'Ext.data.Model',
        fields: ['PremiumID', 'PremiumSupplychainID','PremiumDateStart','PremiumDateEnd','PersenPetani',
            'PersenBuyinUnit','PersenPerwakilan','USD','Kurs','Rupiah'],
   });
   var store_premium = Ext.create('Ext.data.Store', {
      model: 'premium.Model',
      autoLoad: false,
      pageSize: 10,
      proxy: {
         type: 'ajax',
         url: m_premium+'s',
         reader: {
            type: 'json',
            root: 'data',
            totalProperty: 'total'
         }
      }
   });
   var preRowEditing = Ext.create('Ext.grid.plugin.RowEditing', {
      id: 'preRowEditing',
      clicksToMoveEditor: 0,
      autoCancel: false,
      errorSummary : false,
      clicksToEdit: 2
   });
    //end premium
    //kurs
   Ext.define('kurs.Model', {
        extend: 'Ext.data.Model',
        fields: ['KursID','KursSupplychainID','KursDateStart','KursDateEnd','KursNominal'],
   });
   var store_kurs = Ext.create('Ext.data.Store', {
      model: 'kurs.Model',
      autoLoad: false,
      pageSize: 10,
      proxy: {
         type: 'ajax',
         url: m_kurs,
         reader: {
            type: 'json',
            root: 'data',
            totalProperty: 'total'
         }
      }
   });
   var kursRowEditing = Ext.create('Ext.grid.plugin.RowEditing', {
      id: 'kursRowEditing',
      clicksToMoveEditor: 0,
      autoCancel: false,
      errorSummary : false,
      clicksToEdit: 2
   });
   //kurs
    //package
   Ext.define('package.Model', {
        extend: 'Ext.data.Model',
        fields: ['PackageID','PackageSupplychainID','PackageType','PackageWeight','PackageCapasity'],
   });
   var store_package = Ext.create('Ext.data.Store', {
      model: 'package.Model',
      autoLoad: false,
      pageSize: 10,
      proxy: {
         type: 'ajax',
         url: m_package,
         reader: {
            type: 'json',
            root: 'data',
            totalProperty: 'total'
         }
      }
   });
   var paRowEditing = Ext.create('Ext.grid.plugin.RowEditing', {
      id: 'paRowEditing',
      clicksToMoveEditor: 0,
      autoCancel: false,
      errorSummary : false,
      clicksToEdit: 2
   });
    //end package
    //price
   Ext.define('price.Model', {
        extend: 'Ext.data.Model',
        fields: ['PriceID','PriceSupplychainID','PriceDateStart','PriceDateEnd','FFPrice','FAQPrice','District'],
   });
   var store_price = Ext.create('Ext.data.Store', {
      model: 'price.Model',
      autoLoad: false,
      pageSize: 10,
      proxy: {
         type: 'ajax',
         url: m_price,
         reader: {
            type: 'json',
            root: 'data',
            totalProperty: 'total'
         }
      }
   });
   var pRowEditing = Ext.create('Ext.grid.plugin.RowEditing', {
      id: 'pRowEditing',
      clicksToMoveEditor: 0,
      autoCancel: false,
      errorSummary : false,
      clicksToEdit: 2
   });
    //end price
    //quality standard
   Ext.define('quality_standard.Model', {
        extend: 'Ext.data.Model',
        fields: ['StandardID','StandardSupplychainID','StandardName','IsReward','IsClaim'],
   });
   var store_quality_standard = Ext.create('Ext.data.Store', {
      model: 'quality_standard.Model',
      autoLoad: false,
      pageSize: 10,
      proxy: {
         type: 'ajax',
         url: m_quality_standard+'s',
         reader: {
            type: 'json',
            root: 'data',
            totalProperty: 'total'
         }
      }
   });
    //end quality standard
    //quality
   Ext.define('quality.Model', {
        extend: 'Ext.data.Model',
        fields: ['QualityID','QualitySupplychainID','QualityDateStart','QualityDateEnd','StandardName','StandardID'],
   });
   var store_quality = Ext.create('Ext.data.Store', {
      model: 'quality.Model',
      autoLoad: false,
      pageSize: 10,
      proxy: {
         type: 'ajax',
         url: m_quality,
         reader: {
            type: 'json',
            root: 'data',
            totalProperty: 'total'
         }
      }
   });
   var qRowEditing = Ext.create('Ext.grid.plugin.RowEditing', {
      id: 'qRowEditing',
      clicksToMoveEditor: 0,
      autoCancel: false,
      errorSummary : false,
      clicksToEdit: 2
   });
    var store_standard = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id','label'],
        proxy: {
            type: 'ajax',
            url: m_standard,
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });
    //end quality
   var mc_child = Ext.create('Ext.data.Store', {
      extend: 'Ext.data.Model',
      fields: ['id','label'],
      autoLoad: false,
      pageSize: 10,
      proxy: {
         type: 'ajax',
         url: m_relasi+'_child',
         reader: {
            type: 'json',
            root: 'data'
         }
      }
   });
   var mc_Kecamatan = Ext.create('Ext.data.Store', {
      extend: 'Ext.data.Model',
      fields: ['id','label'],
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
   var mc_Desa = Ext.create('Ext.data.Store', {
      extend: 'Ext.data.Model',
      fields: ['label','id'],
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
    //relasi
   Ext.define('relasi.Model', {
        extend: 'Ext.data.Model',
        fields: ['RelId','NoKontrak','ParentOrgId','ChildOrgId','label','StartDate','EndDate','File','Description'],
   });
   var store_relasi = Ext.create('Ext.data.Store', {
      model: 'relasi.Model',
      autoLoad: false,
      pageSize: 10,
      proxy: {
         type: 'ajax',
         url: m_relasi+'s',
         reader: {
            type: 'json',
            root: 'data',
            totalProperty: 'total'
         }
      }
   });
    Ext.define('area.Model', {
        extend: 'Ext.data.Model',
        fields: ['AreaID','Province','District'],
    });
    var store_area = Ext.create('Ext.data.Store', {
        model: 'area.Model',
        autoLoad: false,
        pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_crud+'supplychain_area',
            reader: {
                type: 'json',
                root: 'data',
                totalProperty: 'total'
            }
        }
    });
    var province = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id','label'],
        autoLoad: true,
        // pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_crud + 'supplychain_province',
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });
    var district = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id','label'],
        autoLoad: false,
        // pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_crud + 'supplychain_district',
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });
   var rRowEditing = Ext.create('Ext.grid.plugin.RowEditing', {
      id: 'rRowEditing',
      clicksToMoveEditor: 0,
      autoCancel: false,
      errorSummary : false,
      clicksToEdit: 2
   });
    //end relasi
    //perwakilan
   Ext.define('perwakilan.Model', {
        extend: 'Ext.data.Model',
        fields: ['RelId','ParentOrgId','ChildOrgId','TraderID','TraderName','SubDistrict','SubDistrictID','Village',
            'VillageID','Address','Handphone'],
   });
   var store_perwakilan = Ext.create('Ext.data.Store', {
      model: 'perwakilan.Model',
      autoLoad: false,
      pageSize: 10,
      proxy: {
         type: 'ajax',
         url: m_perwakilan+'s',
         reader: {
            type: 'json',
            root: 'data',
            totalProperty: 'total'
         }
      }
   });
   var peRowEditing = Ext.create('Ext.grid.plugin.RowEditing', {
      id: 'peRowEditing',
      clicksToMoveEditor: 1,
      autoCancel: false,
      errorSummary : false,
      clicksToEdit: 2
   });
    //end perwakilan
   var mc_objid = Ext.create('Ext.data.Store', {
      extend: 'Ext.data.Model',
      fields: ['label','id'],
      autoLoad: false,
      pageSize: 10,
      proxy: {
         type: 'ajax',
         url: m_objid,
         reader: {
            type: 'json',
            root: 'data'
         }
      }
   });
    var DataForm = Ext.create('Ext.form.Panel', {
x        height: 500,
        autoScroll: true,
        width: 1010,
        bodyPadding: 5,
        id:'dataForm',
        fieldDefaults: {
            labelAlign: 'left',
            labelWidth: 170,
            anchor: '100%'
        },
        items: [{
            xtype: 'fieldset',
            title: lang('Unit Pembelian'),
            items: [{
                  xtype: 'textfield',
                  id: 'SupplychainID',
                  name: 'SupplychainID',
                  hidden:true
            },{
               id: 'ObjType',
               name: 'ObjType',
               xtype: 'combo',
               readOnly:!m_act_update,
               fieldLabel: lang('Type'),
               store:mc_type,
               displayField: 'label',
               valueField: 'id',
               queryMode: 'local',
               listeners: {
                  'change': function(fb, v){
                     if (Ext.getCmp('ObjType').getValue()=='koperasi') {
                        Ext.getCmp('setting_perwakilan').show();
                        Ext.getCmp('panel_relasi').disable()
                        Ext.getCmp('panel_perwakilan').disable()
                     } else {
                        Ext.getCmp('panel_perwakilan').disable()
                        Ext.getCmp('setting_perwakilan').hide();
                     }
                     if (Ext.getCmp('ObjType').getValue()=='warehouse') Ext.getCmp('setting_kalkulasi_premium').show();
                     else Ext.getCmp('setting_kalkulasi_premium').hide();
                     mc_objid.load({
                          params: {
                              type: Ext.getCmp('ObjType').getValue(),
                              id:Ext.getCmp('SupplychainID').getValue()
                          }
                      });
                  }
              }
            },{
               fieldLabel: lang('Nama'),
               id: 'ObjID',
               name: 'ObjID',
               xtype: 'combo',
               readOnly:!m_act_update,
               store:mc_objid,
               queryMode: 'local',
               displayField: 'label',
               valueField: 'id',
               /*listeners: {
                  'change': function(fb, v){
                     mc_Kabupaten.load({
                        params: {
                           key: this.value
                     }});
                  }
               }*/
            }]
         },{
            xtype: 'tabpanel',
            id:'all_panel',
            flex: 1,
            margin:2,
            activeTab: 0,
            plain: true,
            items: [{
             xtype: 'panel',
            autoScroll: true,
            id:'panel_setting',
            title: lang('Setting'),
            width:'100%',
            padding:5,
            style: 'border:2px solid #ADD2ED',
            hidden:m_act_setting,
            items: [{
               xtype: 'combo',
               hidden: true,
               labelWidth: 270,
               width:600,
               fieldLabel: 'Area',
               multiSelect: true,
               displayField: 'label',
               valueField: 'label',
               store: mc_Kabupaten,
               queryMode: 'local',
               id: 'Kab',
               name: 'Kab[]',
            },{
              xtype: 'radiogroup',
              labelWidth: 270,
              fieldLabel: lang('Perwakilan Koperasi'),
              id:'setting_perwakilan',
              width:'100%',
              columns: 3,
              items: [{
                  xtype: 'radiofield',
                  boxLabel: lang('Ya'),
                  id: 'PerwakilanKoperasi',
                  name: 'PerwakilanKoperasi',
                  inputValue: '1',
                  listeners: {
                      change: function() {
                         if (Ext.getCmp('PerwakilanKoperasi').getValue()) {
                           Ext.getCmp('panel_relasi').disable()
                           Ext.getCmp('panel_perwakilan').enable()
                         }
                      }
                  }
              },{
                  xtype: 'radiofield',
                  boxLabel: lang('Tidak'),
                  id: 'PerwakilanKoperasi2',
                  name: 'PerwakilanKoperasi',
                  inputValue: '2',
                  listeners: {
                      change: function() {
                         if (Ext.getCmp('PerwakilanKoperasi2').getValue()) {
                           Ext.getCmp('panel_relasi').enable()
                           Ext.getCmp('panel_perwakilan').disable()
                         }
                      }
                  }
              }]
            },{
                xtype: 'checkboxgroup',
                labelWidth: 270,
                fieldLabel: lang('Jenis'),
                width:'100%',
                items: [{
                    boxLabel: lang('Fermentasi'),
                    name: 'IsFF',
                    id: 'IsFF',
                    inputValue: 'FF'
                },{
                    boxLabel: lang('Non Fermentasi'),
                    name: 'IsFAQ',
                    id: 'IsFAQ',
                    inputValue: 'FAQ'
                }]
            },{
                xtype: 'checkboxgroup',
                labelWidth: 270,
                fieldLabel: lang('Pembelian dari'),
                width:'100%',
                items: [{
                    boxLabel: lang('Petani Sertifikasi'),
                    name: 'PembelianFarmer',
                    id: 'PembelianFarmer',
                    inputValue: '1'
                },{
                    boxLabel: lang('Petani Non Sertifikasi'),
                    name: 'PembelianFarmerCert',
                    id: 'PembelianFarmerCert',
                    inputValue: '1'
                },{
                    boxLabel: lang('Non Petani'),
                    name: 'PembelianNonFarmer',
                    id: 'PembelianNonFarmer',
                    inputValue: '1'
                },{
                    boxLabel: lang('Batch'),
                    name: 'PembelianBatch',
                    id: 'PembelianBatch',
                    inputValue: '1'
                }]
            },{
              xtype: 'radiogroup',
              id:'setting_kalkulasi_premium',
              labelWidth: 270,
              fieldLabel: lang('Kalkulasi Premium'),
              width:'100%',
              columns: 3,
              items: [{
                  xtype: 'radiofield',
                  boxLabel: lang('Bruto'),
                  id: 'KalkulasiPremium',
                  name: 'KalkulasiPremium',
                  inputValue: '1'
              },{
                  xtype: 'radiofield',
                  boxLabel: lang('Bruto & Moisture'),
                  id: 'KalkulasiPremium2',
                  name: 'KalkulasiPremium',
                  inputValue: '2'
              },{
                  xtype: 'radiofield',
                  boxLabel: lang('Bruto, Moisture & Harga'),
                  id: 'KalkulasiPremium3',
                  name: 'KalkulasiPremium',
                  inputValue: '3'
              }]
            },{
              xtype: 'radiogroup',
              labelWidth: 270,
              fieldLabel: lang('Nomer Faktur'),
              width:'100%',
              columns: 2,
              items: [{
                  xtype: 'radiofield',
                  boxLabel: lang('Ya'),
                  id: 'IsFakturNumber',
                  name: 'IsFakturNumber',
                  inputValue: '1'
              },{
                  xtype: 'radiofield',
                  boxLabel: lang('Tidak'),
                  id: 'IsFakturNumber2',
                  name: 'IsFakturNumber',
                  inputValue: '2'
              }]
            },{
              xtype: 'radiogroup',
              labelWidth: 270,
              fieldLabel: lang('Label Karung'),
              width:'100%',
              columns: 2,
              items: [{
                  xtype: 'radiofield',
                  boxLabel: lang('Ya'),
                  id: 'LabelKarung',
                  name: 'LabelKarung',
                  inputValue: '1'
              },{
                  xtype: 'radiofield',
                  boxLabel: lang('Tidak'),
                  id: 'LabelKarung2',
                  name: 'LabelKarung',
                  inputValue: '2'
              }]
            },{
              xtype: 'radiogroup',
              labelWidth: 270,
              fieldLabel: lang('Moisture Karung'),
              width:'100%',
              columns: 2,
              items: [{
                  xtype: 'radiofield',
                  boxLabel: lang('Ya'),
                  id: 'IsMoistureKarung',
                  name: 'IsMoistureKarung',
                  inputValue: '1'
              },{
                  xtype: 'radiofield',
                  boxLabel: lang('Tidak'),
                  id: 'IsMoistureKarung2',
                  name: 'IsMoistureKarung',
                  inputValue: '0'
              }]
            },{
               xtype: 'textfield',
               labelWidth: 270,
               width:700,
               fieldLabel: lang('Standard Moisture Karung'),
               id: 'StandardMoistureKarung',
               name: 'StandardMoistureKarung',
           },{
              xtype: 'radiogroup',
              labelWidth: 270,
              fieldLabel: lang('Tampilkan Generate Packaging'),
              width:'100%',
              columns: 2,
              items: [{
                  xtype: 'radiofield',
                  boxLabel: lang('Ya'),
                  id: 'IsGeneratePacking',
                  name: 'IsGeneratePacking',
                  inputValue: '1'
              },{
                  xtype: 'radiofield',
                  boxLabel: lang('Tidak'),
                  id: 'IsGeneratePacking2',
                  name: 'IsGeneratePacking',
                  inputValue: '0'
              }]
            },{
              xtype: 'radiogroup',
              labelWidth: 270,
              fieldLabel: lang('Tampilkan Generate Batch Buying Unit'),
              width:'100%',
              columns: 2,
              items: [{
                  xtype: 'radiofield',
                  boxLabel: lang('Ya'),
                  id: 'IsGenerateBuyingUnitBatch',
                  name: 'IsGenerateBuyingUnitBatch',
                  inputValue: '1'
              },{
                  xtype: 'radiofield',
                  boxLabel: lang('Tidak'),
                  id: 'IsGenerateBuyingUnitBatch2',
                  name: 'IsGenerateBuyingUnitBatch',
                  inputValue: '0'
              }]
            },{
              xtype: 'radiogroup',
              labelWidth: 270,
              fieldLabel: lang('Generate dengan Package, Berat atau Transaction'),
              width:'100%',
              columns: 4,
              items: [{
                  xtype: 'radiofield',
                  boxLabel: lang('Weight'),
                  id: 'GenerateWeightOrPackage',
                  name: 'GenerateWeightOrPackage',
                  inputValue: 'Weight'
              },{
                  xtype: 'radiofield',
                  boxLabel: lang('Package'),
                  id: 'GenerateWeightOrPackage2',
                  name: 'GenerateWeightOrPackage',
                  inputValue: 'Package'
              },{
                  xtype: 'radiofield',
                  boxLabel: lang('Package & Weight'),
                  id: 'GenerateWeightOrPackage3',
                  name: 'GenerateWeightOrPackage',
                  inputValue: 'Both'
              },{
                  xtype: 'radiofield',
                  boxLabel: lang('Transaction'),
                  id: 'GenerateWeightOrPackageTransaction',
                  name: 'GenerateWeightOrPackage',
                  inputValue: 'Transaction'
              }]
            },{
              xtype: 'radiogroup',
              labelWidth: 270,
              fieldLabel: lang('Cetak Surat Jalan'),
              width:'100%',
              columns: 2,
              items: [{
                  xtype: 'radiofield',
                  boxLabel: lang('Ya'),
                  id: 'IsSuratJalan',
                  name: 'IsSuratJalan',
                  inputValue: '1'
              },{
                  xtype: 'radiofield',
                  boxLabel: lang('Tidak'),
                  id: 'IsSuratJalan2',
                  name: 'IsSuratJalan',
                  inputValue: '0'
              }]
            },{
              xtype: 'radiogroup',
              labelWidth: 270,
              fieldLabel: lang('Mencatat Sopir'),
              width:'100%',
              columns: 2,
              items: [{
                  xtype: 'radiofield',
                  boxLabel: lang('Ya'),
                  id: 'IsDriver',
                  name: 'IsDriver',
                  inputValue: '1'
              },{
                  xtype: 'radiofield',
                  boxLabel: lang('Tidak'),
                  id: 'IsDriver2',
                  name: 'IsDriver',
                  inputValue: '0'
              }]
            },{
              xtype: 'radiogroup',
              labelWidth: 270,
              fieldLabel: lang('Mencatat Nomor Polisi'),
              width:'100%',
              columns: 2,
              items: [{
                  xtype: 'radiofield',
                  boxLabel: lang('Ya'),
                  id: 'IsPoliceNumber',
                  name: 'IsPoliceNumber',
                  inputValue: '1'
              },{
                  xtype: 'radiofield',
                  boxLabel: lang('Tidak'),
                  id: 'IsPoliceNumber2',
                  name: 'IsPoliceNumber',
                  inputValue: '0'
              }]
            }, {
                xtype: 'radiogroup',
                labelWidth: 270,
                fieldLabel: lang('Mencatat Tipe Kendaraan'),
                width:'100%',
                columns: 2,
                items: [{
                    xtype: 'radiofield',
                    boxLabel: lang('Ya'),
                    id: 'IsVehicleType',
                    name: 'IsVehicleType',
                    inputValue: '1'
                }, {
                    xtype: 'radiofield',
                    boxLabel: lang('Tidak'),
                    id: 'IsVehicleType2',
                    name: 'IsVehicleType',
                    inputValue: '0'
                }]
            }, {
                xtype: 'radiogroup',
                labelWidth: 270,
                fieldLabel: lang('Mencatat Jabatan Sopir'),
                width:'100%',
                columns: 2,
                items: [{
                    xtype: 'radiofield',
                    boxLabel: lang('Ya'),
                    id: 'IsDriverPosition',
                    name: 'IsDriverPosition',
                    inputValue: '1'
                }, {
                    xtype: 'radiofield',
                    boxLabel: lang('Tidak'),
                    id: 'IsDriverPosition2',
                    name: 'IsDriverPosition',
                    inputValue: '0'
                }]
            }, {
                xtype: 'radiogroup',
                labelWidth: 270,
                fieldLabel: lang('Mencatat Alamat Sopir'),
                width:'100%',
                columns: 2,
                items: [{
                    xtype: 'radiofield',
                    boxLabel: lang('Ya'),
                    id: 'IsDriverAddress',
                    name: 'IsDriverAddress',
                    inputValue: '1'
                }, {
                    xtype: 'radiofield',
                    boxLabel: lang('Tidak'),
                    id: 'IsDriverAddress2',
                    name: 'IsDriverAddress',
                    inputValue: '0'
                }]
            }, {
              xtype: 'radiogroup',
              labelWidth: 270,
              fieldLabel: lang('Pemisahan Batch Farmer Sertifikasi, Farmer Non Sertifikasi & Non Farmer'),
              width:'100%',
              columns: 2,
              items: [{
                  xtype: 'radiofield',
                  boxLabel: lang('Ya'),
                  id: 'PemisahanBatch',
                  name: 'PemisahanBatch',
                  inputValue: '1'
              },{
                  xtype: 'radiofield',
                  boxLabel: lang('Tidak'),
                  id: 'PemisahanBatch2',
                  name: 'PemisahanBatch',
                  inputValue: '2'
              }]
            }, {
                xtype: 'radiogroup',
                labelWidth: 270,
                fieldLabel: lang('Auto Generate PO Number'),
                width:'100%',
                columns: 2,
                items: [{
                    xtype: 'radiofield',
                    boxLabel: lang('Ya'),
                    id: 'IsGeneratePo',
                    name: 'IsGeneratePo',
                    inputValue: '1'
                }, {
                    xtype: 'radiofield',
                    boxLabel: lang('Tidak'),
                    id: 'IsGeneratePo2',
                    name: 'IsGeneratePo',
                    inputValue: '0'
                }]
            }, {
                xtype: 'radiogroup',
                labelWidth: 270,
                fieldLabel: lang('Lock Destination Weiight'),
                width:'100%',
                columns: 2,
                items: [{
                    xtype: 'radiofield',
                    boxLabel: lang('Ya'),
                    id: 'IsLockDestWeigh',
                    name: 'IsLockDestWeigh',
                    inputValue: '1'
                }, {
                    xtype: 'radiofield',
                    boxLabel: lang('Tidak'),
                    id: 'IsLockDestWeigh2',
                    name: 'IsLockDestWeigh',
                    inputValue: '0'
                }]
            }, {
                xtype: 'radiogroup',
                labelWidth: 270,
                fieldLabel: lang('Auto Generate Sent Batch'),
                width:'100%',
                columns: 2,
                items: [{
                    xtype: 'radiofield',
                    boxLabel: lang('Ya'),
                    id: 'IsAutoBatch',
                    name: 'IsAutoBatch',
                    inputValue: '1'
                }, {
                    xtype: 'radiofield',
                    boxLabel: lang('Tidak'),
                    id: 'IsAutoBatch2',
                    name: 'IsAutoBatch',
                    inputValue: '0'
                }]
            }, {
               xtype: 'textfield',
               labelWidth: 270,
               width:700,
               fieldLabel: lang('Formula Netto Karung <br>[B]:Bruto, [P]:Package, [M]:Moisture'),
               id: 'FormulaNettoKarung',
               name: 'FormulaNettoKarung',
           },{
               xtype: 'textfield',
               labelWidth: 270,
               width:700,
               fieldLabel: lang('Formula Net Price<br>[R]:Reward, [C]:Contract Price'),
               id: 'FormulaNettoPrice',
               name: 'FormulaNettoPrice'
           },{
               xtype: 'textfield',
               labelWidth: 270,
               width:700,
               fieldLabel: lang('Formula Netto Akhir<br>[R]:Reward, [N]:Netto'),
               id: 'FormulaNettoAkhir',
               name: 'FormulaNettoAkhir'
           },{
               xtype: 'textfield',
               labelWidth: 270,
               width:700,
               fieldLabel: lang('Label Petani Sertifikasi'),
               id: 'LabelFarmerCertified',
               name: 'LabelFarmerCertified'
           },{
               xtype: 'textfield',
               labelWidth: 270,
               width:700,
               fieldLabel: lang('Label Petani Non Sertifikasi'),
               id: 'LabelFarmerNonCertified',
               name: 'LabelFarmerNonCertified'
           },{
               xtype: 'textfield',
               labelWidth: 270,
               width:700,
               fieldLabel: lang('Label Non Farmer'),
               id: 'LabelNonFarmer',
               name: 'LabelNonFarmer'
           },{
               xtype: 'textfield',
               labelWidth: 270,
               width:700,
               fieldLabel: lang('Label FAQ'),
               id: 'LabelFAQ',
               name: 'LabelFAQ'
           },{
               xtype: 'textfield',
               labelWidth: 270,
               width:700,
               fieldLabel: lang('Label FF'),
               id: 'LabelFF',
               name: 'LabelFF'
            }, {
               xtype: 'textfield',
               labelWidth: 270,
               width:700,
               fieldLabel: lang('Sent Email'),
               id: 'SentEmail',
               name: 'SentEmail'
            }]
         },{
               xtype: 'panel',
               autoScroll: true,
               title: lang('Area'),
               id:'panel_area',
               padding:5,
               style: 'border:2px solid #ADD2ED',
               //hidden:m_act_relasi,
               items: [{
                  xtype: 'gridpanel',
                  id:'grid_area',
                  store: store_area,
                  width: '100%',
                  minHeight:400,
                  loadMask: true,
                  selType: 'rowmodel',
                  dockedItems: [{
                     xtype: 'toolbar',
                     items: [{
                        icon: varjs.config.base_url + 'images/icons/new/add.png', cls:'Sfr_BtnGridGreen', overCls:'Sfr_BtnGridGreen-Hover',
                        text: lang('Add'),
                        cls : m_act_save,
                        scope: this,
                        handler :function(){
                            displayAddArea(); 
                            Ext.getCmp('buProvinceID').setValue('');
                            Ext.getCmp('buDistrictID').setValue('');
                        }
                     },{
                        itemId: 'remove',
                        icon: varjs.config.base_url+'images/icons/silk/delete.png',
                        text: lang('Hapus'),
                        scope: this,
                        handler : function(){
                          var smb = Ext.getCmp('grid_area').getSelectionModel().getSelection()[0];
                          rRowEditing.cancelEdit();
                          Ext.MessageBox.confirm('Message', lang('Apakah anda mau menghapus data relasi ini ?') , function(btn){
                              if(btn == 'yes'){
                                 Ext.Ajax.request({
                                    waitMsg: lang('Please Wait'),
                                    url: m_crud+'supplychain_area',
                                    method : 'DELETE',
                                    params: {
                                       id:  smb.raw.AreaID
                                    },
                                    success: function(response, opts){
                                       var obj = Ext.decode(response.responseText);
                                       switch(obj.success){
                                          case true:
                                             store_area.load({
                                                params: {
                                                   SupplychainID: Ext.getCmp('SupplychainID').getValue()
                                             }});
                                          break;
                                          default:
                                              Ext.MessageBox.alert('Warning',obj.message);
                                          break;
                                       }
                                    },
                                    failure: function(response, opts){
                                       Ext.MessageBox.alert('error',lang('Could not connect to the database. Retry later'));
                                    }
                                 });
                              }
                          });
                        }
                     }]
                  }],
                  columns: [{
                    text: lang('No'),
                    xtype: 'rownumberer',
                    width:'5%'
                  },{
                    text: lang('Province'),
                    dataIndex: 'Province',
                    flex: 3
                  },{
                    text: lang('District'),
                    dataIndex: 'District',
                    flex: 3
                  }]
            }]
         },{
               xtype: 'panel',
               autoScroll: true,
               title: lang('Relasi'),
               id:'panel_relasi',
               padding:5,
               style: 'border:2px solid #ADD2ED',
               hidden:m_act_relasi,
               items: [{
                  xtype: 'gridpanel',
                  id:'grid_relasi',
                  store: store_relasi,
                  width: '100%',
                  minHeight:400,
                  loadMask: true,
                  selType: 'rowmodel',
                  dockedItems: [{
                     xtype: 'toolbar',
                     items: [{
                        icon: varjs.config.base_url + 'images/icons/new/add.png', cls:'Sfr_BtnGridGreen', overCls:'Sfr_BtnGridGreen-Hover',
                        text: lang('Add'),
                        cls : m_act_save,
                        scope: this,
                        handler :function(){
                          rRowEditing.cancelEdit();
                          var r = Ext.create('relasi.Model', {
                              RelId:'',NoKontrak:'',ParentOrgId:'',ChildOrgId:'',label:'',StartDate:'',EndDate:'',File:'',Description:''
                          });
                          store_relasi.insert(0, r);
                          rRowEditing.startEdit(0, 0);
                        }
                     },{
                        icon: varjs.config.base_url+'images/icons/silk/pencil.png',
                        cls:m_act_save,
                        text: lang('Edit'),
                        scope: this,
                        handler : function() {
                          rRowEditing.cancelEdit();
                          var sm = Ext.getCmp('grid_relasi').getSelectionModel().getSelection();
                          Ext.getCmp('ChildOrgId').setValue(sm[0].index.ChildOrgId);
                          rRowEditing.startEdit(sm[0].index, 0);
                        }
                     },{
                        itemId: 'remove',
                        icon: varjs.config.base_url+'images/icons/silk/delete.png',
                        text: lang('Hapus'),
                        scope: this,
                        handler : function(){
                          var smb = Ext.getCmp('grid_relasi').getSelectionModel().getSelection()[0];
                          rRowEditing.cancelEdit();
                          Ext.MessageBox.confirm('Message', lang('Apakah anda mau menghapus data relasi ini ?') , function(btn){
                              if(btn == 'yes'){
                                 Ext.Ajax.request({
                                    waitMsg: lang('Please Wait'),
                                    url: m_relasi,
                                    method : 'DELETE',
                                    params: {
                                       id:  smb.raw.RelId
                                    },
                                    success: function(response, opts){
                                       var obj = Ext.decode(response.responseText);
                                       switch(obj.success){
                                          case true:
                                             store_relasi.load({
                                                params: {
                                                   id: Ext.getCmp('SupplychainID').getValue()
                                             }});
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
                  columns: [{
                    text: lang('No'),
                    xtype: 'rownumberer',
                    width:'5%'
                  },{
                    text: lang('No Kontrak'),
                    dataIndex: 'NoKontrak',
                    width:'15%',
                    editor: {
                       xtype:'textfield',
                       allowBlank:false
                    }
                  },{
                    text: lang('Buying Unit'),
                    dataIndex: 'label',
                    width:'20%',
                    editor: {
                       xtype: 'combo',
                       store: mc_child,
                       name:'ChildOrgId',
                       id:'ChildOrgId',
                       displayField: 'label',
                       valueField: 'id',
                       queryMode: 'local'
                    }
                  },{
                    text: lang('Mulai'),
                    dataIndex: 'StartDate',
                    width:'15%',
                    editor: {
                       xtype: 'datefield',
                       format: 'Y-m-d'
                    }
                  },{
                    text: lang('Akhir'),
                    dataIndex: 'EndDate',
                    width:'15%',
                    editor: {
                       xtype: 'datefield',
                       format: 'Y-m-d'
                    }
                  },{
                    text: lang('Deskripsi'),
                    dataIndex: 'Description',
                    width:'20%',
                    editor: {
                       xtype:'textfield',
                       allowBlank:false
                    }
                  },{
                    text: lang('File'),
                    dataIndex: 'File',
                    width:'10%',
                    hidden:true,
                    editor: {
                      xtype: 'fileuploadfield',
                      padding: 5,
                      buttonText: 'Browse',
                    }
                  }],
                  plugins: [rRowEditing],
                  listeners: {
                   'canceledit':function(editor,e,eOpts){
                        store_relasi.load({
                           params: {
                              id: Ext.getCmp('SupplychainID').getValue()
                        }});
                   },
                   'edit': function(editor, e) {
                     if(e.record.data.RelId==''){
                        Ext.Ajax.request({
                            waitMsg: lang('Please wait...'),
                            url: m_relasi,
                            method : 'POST',
                            params: {
                               ParentOrgId      : Ext.getCmp('SupplychainID').getValue(),
                               NoKontrak    : e.record.data.NoKontrak,
                               ChildOrgId   : Ext.getCmp('ChildOrgId').getValue(),
                               StartDate   : e.record.data.StartDate,
                               EndDate   : e.record.data.EndDate,
                               File   : e.record.data.File,
                               Description   : e.record.data.Description
                            },
                            success: function(response, opts){
                                var obj = Ext.decode(response.responseText);
                                switch(obj.success){
                                    case true:
                                       Ext.MessageBox.alert('Success',obj.message);
                                       store_relasi.load({
                                          params: {
                                             id: Ext.getCmp('SupplychainID').getValue()
                                       }});
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
                        Ext.MessageBox.confirm('Message', lang('Update data relasi ini ?') , function(btn){
                           if(btn == 'yes') {
                              Ext.Ajax.request({
                                 waitMsg: lang('Please wait...'),
                                 url: m_relasi+'u',
                                 method : 'POST',
                                 params: {
                                     RelId    : e.record.data.RelId,
                                     ParentOrgId      : Ext.getCmp('SupplychainID').getValue(),
                                     NoKontrak    : e.record.data.NoKontrak,
                                     ChildOrgId   : Ext.getCmp('ChildOrgId').getValue(),
                                     StartDate   : e.record.data.StartDate,
                                     EndDate   : e.record.data.EndDate,
                                     File   : e.record.data.File,
                                     Description   : e.record.data.Description
                                 },
                                 success: function(response, opts){
                                     var obj = Ext.decode(response.responseText);
                                     switch(obj.success){
                                         case true:
                                            Ext.MessageBox.alert('Success',obj.message);
                                            store_relasi.load({
                                                params: {
                                                   id: Ext.getCmp('SupplychainID').getValue()
                                            }});
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
               }
            }]
         }, {
               xtype: 'panel',
               autoScroll: true,
               title: lang('Perwakilan'),
               id:'panel_perwakilan',
               padding:5,
               style: 'border:2px solid #ADD2ED',
               hidden:m_act_perwakilan,
               items: [{
                  xtype: 'gridpanel',
                  id:'grid_perwakilan',
                  store: store_perwakilan,
                  width: '100%',
                  loadMask: true,
                  selType: 'rowmodel',
                  dockedItems: [{
                     xtype: 'toolbar',
                     items: [{
                        icon: varjs.config.base_url + 'images/icons/new/add.png', cls:'Sfr_BtnGridGreen', overCls:'Sfr_BtnGridGreen-Hover',
                        text: lang('Add'),
                        cls : m_act_save,
                        scope: this,
                        handler :function(){
                          peRowEditing.cancelEdit();
                          var r = Ext.create('perwakilan.Model', {
                              RelId:'',ParentOrgId:'',ChildOrgId:'',TraderID:'',TraderName:'',SubDistrict:'',SubDistrictID:'',
                              Village:'',VillageID:'',Address:'',Handphone:''
                          });
                          store_perwakilan.insert(0, r);
                          peRowEditing.startEdit(0, 0);
                        }
                     },{
                        icon: varjs.config.base_url+'images/icons/silk/pencil.png',
                        cls:m_act_save,
                        text: lang('Edit'),
                        scope: this,
                        handler : function() {
                          peRowEditing.cancelEdit();
                          var sm = Ext.getCmp('grid_perwakilan').getSelectionModel().getSelection();
                          Ext.getCmp('pVillage').setValue(sm[0].index.VillageID);
                          peRowEditing.startEdit(sm[0].index, 0);
                        }
                     },{
                        itemId: 'remove',
                        icon: varjs.config.base_url+'images/icons/silk/delete.png',
                        text: lang('Hapus'),
                        scope: this,
                        handler : function(){
                          var smb = Ext.getCmp('grid_perwakilan').getSelectionModel().getSelection()[0];
                          peRowEditing.cancelEdit();
                          Ext.MessageBox.confirm('Message', lang('Apakah anda mau menghapus data perwakilan ini ?') , function(btn){
                              if(btn == 'yes'){
                                 Ext.Ajax.request({
                                    waitMsg: lang('Please Wait'),
                                    url: m_perwakilan,
                                    method : 'DELETE',
                                    params: {
                                       id:  smb.raw.RelId
                                    },
                                    success: function(response, opts){
                                       var obj = Ext.decode(response.responseText);
                                       switch(obj.success){
                                          case true:
                                             store_perwakilan.load({
                                                params: {
                                                   id: Ext.getCmp('SupplychainID').getValue()
                                             }});
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
                  columns: [{
                    text: lang('No'),
                    xtype: 'rownumberer',
                    width:'5%'
                  },{
                    text: lang('Nama'),
                    dataIndex: 'TraderName',
                    width:'20%',
                    editor: {
                       xtype:'textfield',
                       allowBlank:false
                    }
                  },{
                    text: lang('Kecamatan'),
                    dataIndex: 'SubDistrict',
                    width:'20%',
                    hidden:true,
                    editor: {
                       xtype: 'combo',
                       store: mc_Kecamatan,
                       name:'pSubDistrict',
                       id:'pSubDistrict',
                       displayField: 'label',
                       valueField: 'id',
                       queryMode: 'local',
                       listeners: {
                            edit: function(combo, records, eOpts) {
                              mc_Desa.load({
                                 params: {
                                    key: records[0].get('label')
                              }});
                            }
                        }
                    }
                  },{
                    text: lang('Desa'),
                    dataIndex: 'Village',
                    width:'40%',
                    editor: {
                       xtype: 'combo',
                       store: mc_Desa,
                       name:'pVillage',
                       id:'pVillage',
                       displayField: 'label',
                       valueField: 'id',
                       queryMode: 'local'
                    }
                  },{
                    text: lang('Address'),
                    dataIndex: 'Address',
                    width:'20%',
                    editor: {
                       xtype:'textfield',
                       allowBlank:false
                    }
                  },{
                    text: lang('Handphone'),
                    dataIndex: 'Handphone',
                    width:'15%',
                    editor: {
                       xtype:'textfield',
                       allowBlank:false
                    }
                  }],
                  plugins: [peRowEditing],
                  listeners: {
                   itemdblclick: function(dv, record, item, index, e) {
                     Ext.getCmp('pVillage').setValue(record.data.VillageID);
                   },
                   'canceledit':function(editor,e,eOpts){
                        store_perwakilan.load({
                           params: {
                              id: Ext.getCmp('id').getValue()
                        }});
                   },
                   'edit': function(editor, e) {
                     if(e.record.data.RelId==''){
                        Ext.Ajax.request({
                            waitMsg: lang('Please wait...'),
                            url: m_perwakilan,
                            method : 'POST',
                            params: {
                               ParentOrgId      : Ext.getCmp('SupplychainID').getValue(),
                               TraderName    : e.record.data.TraderName,
                               ChildOrgId   : e.record.data.ChildOrgId,
                               Village   : Ext.getCmp('pVillage').getValue(),
                               Address   : e.record.data.Address,
                               Handphone   : e.record.data.Handphone
                            },
                            success: function(response, opts){
                                var obj = Ext.decode(response.responseText);
                                switch(obj.success){
                                    case true:
                                       Ext.MessageBox.alert('Success',obj.message);
                                       store_perwakilan.load({
                                          params: {
                                             id: Ext.getCmp('SupplychainID').getValue()
                                       }});
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
                        Ext.MessageBox.confirm('Message', lang('Update data perwakilan ini ?') , function(btn){
                           if(btn == 'yes') {
                              Ext.Ajax.request({
                                 waitMsg: lang('Please wait...'),
                                 url: m_perwakilan+'u',
                                 method : 'POST',
                                 params: {
                                     RelId    : e.record.data.RelId,
                                     ParentOrgId      : Ext.getCmp('SupplychainID').getValue(),
                                     TraderID    : e.record.data.TraderID,
                                     TraderName    : e.record.data.TraderName,
                                     ChildOrgId   : e.record.data.ChildOrgId,
                                     Village   : Ext.getCmp('pVillage').getValue(),
                                     Address   : e.record.data.Address,
                                     Handphone   : e.record.data.Handphone
                                 },
                                 success: function(response, opts){
                                     var obj = Ext.decode(response.responseText);
                                     switch(obj.success){
                                         case true:
                                            Ext.MessageBox.alert('Success',obj.message);
                                            store_perwakilan.load({
                                                params: {
                                                   id: Ext.getCmp('SupplychainID').getValue()
                                            }});
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
               }
            }]
         },{
            xtype: 'panel',
            autoScroll: true,
            id:'panel_kualitas_standard',
            hidden:m_act_standard_quality,
            title: lang('Standar Kualitas'),
            padding:5,
            flex:1,
            border:false,
            items: [{
               xtype: 'gridpanel',
               id:'grid_quality_standard',
               store: store_quality_standard,
               width: '100%',
               border:false,
               flex:1,
               loadMask: true,
               selType: 'rowmodel',
               listeners: {
                   itemdblclick: function (dv, record, item, index, e) {
                       update_standard();
                   },
                   show: function(){
                        store_quality_standard.load({
                            params: {
                                id: Ext.getCmp('SupplychainID').getValue()
                        }});
                   }
               },
               dockedItems: [{
                  xtype: 'toolbar',
                  items: [{
                     icon: varjs.config.base_url + 'images/icons/new/add.png', cls:'Sfr_BtnGridGreen', overCls:'Sfr_BtnGridGreen-Hover',
                     text: lang('Add'),
                     cls : m_act_save,
                     scope: this,
                     handler :function(){
                          add_standard()
                     }
                  },{
                     icon: varjs.config.base_url+'images/icons/silk/pencil.png',
                     cls:m_act_save,
                     text: lang('Edit'),
                     scope: this,
                     handler : function() {
                        update_standard()
                     }
                  },{
                     itemId: 'remove',
                     icon: varjs.config.base_url+'images/icons/silk/delete.png',
                     text: lang('Hapus'),
                     scope: this,
                     handler : function(){
                        var smb = Ext.getCmp('grid_quality_standard').getSelectionModel().getSelection()[0];
                        Ext.MessageBox.confirm('Message', lang('Apakah anda mau menghapus data ini?'), function (btn) {
                            if (btn == 'yes') {
                                Ext.Ajax.request({
                                    waitMsg: lang('Please Wait'),
                                    url: m_quality_standard,
                                    method: 'DELETE',
                                    params: {id: smb.raw.StandardID},
                                    success: function (response, opts) {
                                        var obj = Ext.decode(response.responseText);
                                        switch (obj.success) {
                                            case true:
                                                store_quality_standard.load({
                                                   params: {
                                                      id: Ext.getCmp('SupplychainID').getValue()
                                                }});
                                                break;
                                            default:
                                                Ext.MessageBox.alert('Warning', obj.message);
                                                break;
                                        }
                                    },
                                    failure: function (response, opts) {
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
                 text: lang('No'),
                 xtype: 'rownumberer',
                 width:'5%'
               },{
                 text: lang('Nama'),
                 dataIndex: 'StandardName',
                 width:'90%',
                 editor: {
                    xtype:'textfield',
                    allowBlank:false
                 }
               }]
            }]
         },{
            xtype: 'panel',
            autoScroll: true,
            id:'panel_kualitas',
            hidden:m_act_quality,
            title: lang('Kualitas'),
            padding:5,
            flex:1,
            items: [{
               xtype: 'gridpanel',
               id:'grid_quality',
               store: store_quality,
               width: '100%',
               flex:1,
               minHeight:200,
               loadMask: true,
               selType: 'rowmodel',
               dockedItems: [{
                  xtype: 'toolbar',
                  items: [{
                     icon: varjs.config.base_url + 'images/icons/new/add.png', cls:'Sfr_BtnGridGreen', overCls:'Sfr_BtnGridGreen-Hover',
                     text: lang('Add'),
                     cls : m_act_save,
                     scope: this,
                     handler :function(){
                          qRowEditing.cancelEdit();
                          var r = Ext.create('quality.Model', {
                              QualityID:'', QualitySupplychainID:'', QualityDateStart:'',QualityDateEnd:'', StandardName:'',
                              StandardID:''});
                          store_quality.insert(0, r);
                          qRowEditing.startEdit(0, 0);
                           store_standard.load({
                              params: {
                                 id: Ext.getCmp('SupplychainID').getValue()
                           }});
                     }
                  },{
                     icon: varjs.config.base_url+'images/icons/silk/pencil.png',
                     cls:m_act_save,
                     text: lang('Edit'),
                     scope: this,
                     handler : function() {
                       qRowEditing.cancelEdit();
                       var sm = Ext.getCmp('grid_quality').getSelectionModel().getSelection();
                        store_standard.load({
                           params: {
                              id: Ext.getCmp('SupplychainID').getValue()
                        }});
                       qRowEditing.startEdit(sm[0].index, 0);
                     }
                  },{
                     itemId: 'remove',
                     icon: varjs.config.base_url+'images/icons/silk/delete.png',
                     text: lang('Hapus'),
                     scope: this,
                     handler : function(){
                       var smb = Ext.getCmp('grid_quality').getSelectionModel().getSelection()[0];
                       qRowEditing.cancelEdit();
                       Ext.MessageBox.confirm('Message', lang('Apakah anda mau menghapus data kualitas ini ?') , function(btn){
                           if(btn == 'yes'){
                              Ext.Ajax.request({
                                 waitMsg: lang('Please Wait'),
                                 url: m_quality,
                                 method : 'DELETE',
                                 params: {
                                    id:  smb.raw.QualityID
                                 },
                                 success: function(response, opts){
                                    var obj = Ext.decode(response.responseText);
                                    switch(obj.success){
                                       case true:
                                          store_quality.load({
                                             params: {
                                                id: Ext.getCmp('SupplychainID').getValue()
                                          }});
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
               columns: [{
                 text: lang('No'),
                 xtype: 'rownumberer',
                 width:'5%'
               },{
                 text: lang('Tanggal Awal'),
                 dataIndex: 'QualityDateStart',
                 width:'20%',
                 editor: {
                    xtype:'datefield',
                    allowBlank:false,
                    format:'Y-m-d'
                 }
               },{
                 text: lang('Tanggal Akhir'),
                 dataIndex: 'QualityDateEnd',
                 width:'20%',
                 editor: {
                    xtype:'datefield',
                    allowBlank:false,
                    format:'Y-m-d'
                 }
               },{
                 text: lang('Nama'),
                 dataIndex: 'StandardName',
                 flex:1,
                 editor: {
                     xtype      : 'combo',
                     store : store_standard,
                     id:'StandardID',
                     queryMode: 'local',
                     displayField: 'label',
                     valueField: 'id'
                 }
               }],
               plugins: [qRowEditing],
               listeners: {
                    show: function(){
                         store_standard.load({
                             params: {
                                 id: Ext.getCmp('SupplychainID').getValue()
                         }});
                    },
                    itemdblclick: function(dv, record, item, index, e) {
                       store_standard.load({
                           params: {
                              id: Ext.getCmp('SupplychainID').getValue()
                        }});
                   },
                   'canceledit':function(editor,e,eOpts){
                        store_quality.load({
                           params: {
                              id: Ext.getCmp('SupplychainID').getValue()
                        }});
                   },
                   'edit': function(editor, e) {
                     if(e.record.data.QualityID==''){
                        Ext.Ajax.request({
                            waitMsg: lang('Please wait...'),
                            url: m_quality,
                            method : 'POST',
                            params: {
                               QualitySupplychainID      : Ext.getCmp('SupplychainID').getValue(),
                               QualityDateStart    : e.record.data.QualityDateStart,
                               QualityDateEnd    : e.record.data.QualityDateEnd,
                               StandardID      : Ext.getCmp('StandardID').getValue()
                            },
                            success: function(response, opts){
                                var obj = Ext.decode(response.responseText);
                                switch(obj.success){
                                    case true:
                                       Ext.MessageBox.alert('Success',obj.message);
                                       store_quality.load({
                                          params: {
                                             id: Ext.getCmp('SupplychainID').getValue()
                                       }});
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
                        Ext.MessageBox.confirm('Message', lang('Update data quality ini ?') , function(btn){
                           if(btn == 'yes') {
                              Ext.Ajax.request({
                                 waitMsg: lang('Please wait...'),
                                 url: m_quality,
                                 method : 'PUT',
                                 params: {
                                     QualitySupplychainID      : Ext.getCmp('SupplychainID').getValue(),
                                     QualityID    : e.record.data.QualityID,
                                     QualityDateStart    : e.record.data.QualityDateStart,
                                     QualityDateEnd    : e.record.data.QualityDateEnd,
                                     StandardID      : Ext.getCmp('StandardID').getValue()
                                 },
                                 success: function(response, opts){
                                     var obj = Ext.decode(response.responseText);
                                     switch(obj.success){
                                         case true:
                                            Ext.MessageBox.alert('Success',obj.message);
                                            store_quality.load({
                                                params: {
                                                   id: Ext.getCmp('SupplychainID').getValue()
                                            }});
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
               }
            }]
         },{
            xtype: 'panel',
            autoScroll: true,
            id:'panel_harga',
            title: lang('Harga'),
            hidden:m_act_harga,
            padding:5,
            style: 'border:2px solid #ADD2ED',
            items: [{
               xtype: 'gridpanel',
               id:'grid_price',
               store: store_price,
               width: '100%',
               loadMask: true,
               selType: 'rowmodel',
               dockedItems: [{
                  xtype: 'toolbar',
                  items: [{
                     icon: varjs.config.base_url + 'images/icons/new/add.png', cls:'Sfr_BtnGridGreen', overCls:'Sfr_BtnGridGreen-Hover',
                     text: lang('Add'),
                     cls : m_act_save,
                     scope: this,
                     handler :function(){
                          pRowEditing.cancelEdit();
                          var r = Ext.create('price.Model', {
                              PriceID:'', PriceSupplychainID:'', PriceDateStart:'', PriceDateEnd:'', FFPrice:'',FAQPrice:'',
                                 District:Ext.getCmp('Kab').getValue()
                          });
                          store_price.insert(0, r);
                          pRowEditing.startEdit(0, 0);
                     }
                  },{
                     icon: varjs.config.base_url+'images/icons/silk/pencil.png',
                     cls:m_act_save,
                     text: lang('Edit'),
                     scope: this,
                     handler : function() {
                       pRowEditing.cancelEdit();
                       var sm = Ext.getCmp('grid_price').getSelectionModel().getSelection();
                       pRowEditing.startEdit(sm[0].index, 0);
                     }
                  },{
                     itemId: 'remove',
                     icon: varjs.config.base_url+'images/icons/silk/delete.png',
                     text: lang('Hapus'),
                     scope: this,
                     handler : function(){
                       var smb = Ext.getCmp('grid_price').getSelectionModel().getSelection()[0];
                       pRowEditing.cancelEdit();
                       Ext.MessageBox.confirm('Message', lang('Apakah anda mau menghapus harga ini ?') , function(btn){
                           if(btn == 'yes'){
                              Ext.Ajax.request({
                                 waitMsg: lang('Please Wait'),
                                 url: m_price,
                                 method : 'DELETE',
                                 params: {
                                    id:  smb.raw.PriceID
                                 },
                                 success: function(response, opts){
                                    var obj = Ext.decode(response.responseText);
                                    switch(obj.success){
                                       case true:
                                          store_price.load({
                                             params: {
                                                id: Ext.getCmp('SupplychainID').getValue()
                                          }});
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
               columns: [{
                 text: lang('No'),
                 xtype: 'rownumberer',
                 width:'5%'
               },{
                 text: lang('Tanggal Awal'),
                 dataIndex: 'PriceDateStart',
                 width:'20%',
                 editor: {
                    xtype:'datefield',
                    allowBlank:false,
                    format:'Y-m-d'
                 }
               },{
                 text: lang('Tanggal Akhir'),
                 dataIndex: 'PriceDateEnd',
                 width:'20%',
                 editor: {
                    xtype:'datefield',
                    allowBlank:false,
                    format:'Y-m-d'
                 }
               },{
                 text: lang('District'),
                 dataIndex: 'District',
                 width:'20%',
                 editor: {
                     xtype      : 'combo',
                     store : mc_Kabupaten,
                     id:'district_harga',
                     queryMode: 'local',
                     id: 'District',
                     displayField: 'label',
                     valueField: 'label'
                 }
               },{
                 text: lang('Harga FF'),
                 dataIndex: 'FFPrice',
                 width:'10%',
                 editor: {
                    xtype:'textfield',
                    allowBlank:false
                 }
               },{
                 text: lang('Harga FAQ'),
                 dataIndex: 'FAQPrice',
                 width:'10%',
                 editor: {
                    xtype:'textfield',
                    allowBlank:false
                 }
               }],
               plugins: [pRowEditing],
               listeners: {
                   'canceledit':function(editor,e,eOpts){
                        store_price.load({
                           params: {
                              id: Ext.getCmp('SupplychainID').getValue()
                        }});
                   },
                   'edit': function(editor, e) {
                     if(e.record.data.PriceID==''){
                        Ext.Ajax.request({
                            waitMsg: lang('Please wait...'),
                            url: m_price,
                            method : 'POST',
                            params: {
                               PriceSupplychainID      : Ext.getCmp('SupplychainID').getValue(),
                               PriceDateStart    : e.record.data.PriceDateStart,
                               PriceDateEnd    : e.record.data.PriceDateEnd,
                               FFPrice   : e.record.data.FFPrice,
                               FAQPrice   : e.record.data.FAQPrice,
                               District   : e.record.data.District
                            },
                            success: function(response, opts){
                                var obj = Ext.decode(response.responseText);
                                switch(obj.success){
                                    case true:
                                       Ext.MessageBox.alert('Success',obj.message);
                                       store_price.load({
                                          params: {
                                             id: Ext.getCmp('SupplychainID').getValue()
                                       }});
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
                        Ext.MessageBox.confirm('Message', lang('Update data price ini ?') , function(btn){
                           if(btn == 'yes') {
                              Ext.Ajax.request({
                                 waitMsg: lang('Please wait...'),
                                 url: m_price,
                                 method : 'PUT',
                                 params: {
                                     PriceSupplychainID      : Ext.getCmp('SupplychainID').getValue(),
                                     PriceID    : e.record.data.PriceID,
                                     PriceDateStart    : e.record.data.PriceDateStart,
                                     PriceDateEnd    : e.record.data.PriceDateEnd,
                                     FFPrice   : e.record.data.FFPrice,
                                     FAQPrice   : e.record.data.FAQPrice,
                                     District  : e.record.data.District
                                 },
                                 success: function(response, opts){
                                     var obj = Ext.decode(response.responseText);
                                     switch(obj.success){
                                         case true:
                                            Ext.MessageBox.alert('Success',obj.message);
                                            store_price.load({
                                                params: {
                                                   id: Ext.getCmp('SupplychainID').getValue()
                                            }});
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
               }
            }]
         },{
            xtype: 'panel',
            autoScroll: true,
            id:'panel_kemasan',
            hidden:m_act_kemasan,
            title: lang('Kemasan'),
            padding:5,
            style: 'border:2px solid #ADD2ED',
            items: [{
               xtype: 'gridpanel',
               id:'grid_package',
               store: store_package,
               width: '100%',
               loadMask: true,
               selType: 'rowmodel',
               dockedItems: [{
                  xtype: 'toolbar',
                  items: [{
                     icon: varjs.config.base_url + 'images/icons/new/add.png', cls:'Sfr_BtnGridGreen', overCls:'Sfr_BtnGridGreen-Hover',
                     text: lang('Add'),
                     cls : m_act_save,
                     scope: this,
                     handler :function(){
                          paRowEditing.cancelEdit();
                          var r = Ext.create('package.Model', {
                              PackageID:'', PackageSupplychainID:'', PackageType:'', PackageWeight:'',PackageCapasity:''
                          });
                          store_package.insert(0, r);
                          paRowEditing.startEdit(0, 0);
                     }
                  },{
                     icon: varjs.config.base_url+'images/icons/silk/pencil.png',
                     cls:m_act_save,
                     text: lang('Edit'),
                     scope: this,
                     handler : function() {
                       paRowEditing.cancelEdit();
                       var sm = Ext.getCmp('grid_package').getSelectionModel().getSelection();
                       paRowEditing.startEdit(sm[0].index, 0);
                     }
                  },{
                     itemId: 'remove',
                     icon: varjs.config.base_url+'images/icons/silk/delete.png',
                     text: lang('Hapus'),
                     scope: this,
                     handler : function(){
                       var smp = Ext.getCmp('grid_package').getSelectionModel().getSelection()[0];
                       paRowEditing.cancelEdit();
                       Ext.MessageBox.confirm('Message', lang('Apakah anda mau menghapus package ini ?') , function(btn){
                           if(btn == 'yes'){
                              Ext.Ajax.request({
                                 waitMsg: lang('Please Wait'),
                                 url: m_package,
                                 method : 'DELETE',
                                 params: {
                                    id:  smp.raw.PackageID
                                 },
                                 success: function(response, opts){
                                    var obj = Ext.decode(response.responseText);
                                    switch(obj.success){
                                       case true:
                                          store_package.load({
                                             params: {
                                                id: Ext.getCmp('SupplychainID').getValue()
                                          }});
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
               columns: [{
                 text: lang('No'),
                 xtype: 'rownumberer',
                 width:'5%'
               },{
                 text: lang('Nama'),
                 dataIndex: 'PackageType',
                 width:'65%',
                 editor: {
                    xtype:'textfield',
                    allowBlank:false
                 }
               },{
                 text: lang('Berat Pemotongan'),
                 dataIndex: 'PackageWeight',
                 width:'15%',
                 editor: {
                    xtype:'textfield',
                    allowBlank:false
                 }
               },{
                 text: lang('Kapasitas'),
                 dataIndex: 'PackageCapasity',
                 width:'15%',
                 editor: {
                    xtype:'textfield',
                    allowBlank:false
                 }
               }],
               plugins: [paRowEditing],
               listeners: {
                   'canceledit':function(editor,e,eOpts){
                        store_package.load({
                           params: {
                              id: Ext.getCmp('id').getValue()
                        }});
                   },
                   'edit': function(editor, e) {
                     if(e.record.data.PackageID==''){
                        Ext.Ajax.request({
                            waitMsg: lang('Please wait...'),
                            url: m_package,
                            method : 'POST',
                            params: {
                               PackageSupplychainID      : Ext.getCmp('SupplychainID').getValue(),
                               PackageType    : e.record.data.PackageType,
                               PackageWeight  : e.record.data.PackageWeight,
                               PackageCapasity  : e.record.data.PackageCapasity
                            },
                            success: function(response, opts){
                                var obj = Ext.decode(response.responseText);
                                switch(obj.success){
                                    case true:
                                       Ext.MessageBox.alert('Success',obj.message);
                                       store_package.load({
                                          params: {
                                             id: Ext.getCmp('SupplychainID').getValue()
                                       }});
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
                        Ext.MessageBox.confirm('Message', lang('Update data package ini ?') , function(btn){
                           if(btn == 'yes') {
                              Ext.Ajax.request({
                                 waitMsg: lang('Please wait...'),
                                 url: m_package,
                                 method : 'PUT',
                                 params: {
                                     PackageSupplychainID      : Ext.getCmp('SupplychainID').getValue(),
                                     PackageID    : e.record.data.PackageID,
                                     PackageType    : e.record.data.PackageType,
                                     PackageWeight  : e.record.data.PackageWeight,
                                     PackageCapasity  : e.record.data.PackageCapasity
                                 },
                                 success: function(response, opts){
                                     var obj = Ext.decode(response.responseText);
                                     switch(obj.success){
                                         case true:
                                            Ext.MessageBox.alert('Success',obj.message);
                                            store_package.load({
                                                params: {
                                                   id: Ext.getCmp('SupplychainID').getValue()
                                            }});
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
               }
            }]
         },{
            xtype: 'panel',
            autoScroll: true,
            id:'panel_kurs',
            title: lang('Kurs'),
            hidden:m_act_kurs,
            padding:5,
            style: 'border:2px solid #ADD2ED',
            items: [{
               xtype: 'gridpanel',
               id:'grid_kurs',
               store: store_kurs,
               width: '100%',
               loadMask: true,
               selType: 'rowmodel',
               dockedItems: [{
                  xtype: 'toolbar',
                  items: [{
                     icon: varjs.config.base_url + 'images/icons/new/add.png', cls:'Sfr_BtnGridGreen', overCls:'Sfr_BtnGridGreen-Hover',
                     text: lang('Add'),
                     cls : m_act_save,
                     scope: this,
                     handler :function(){
                          kursRowEditing.cancelEdit();
                          var r = Ext.create('kurs.Model', {
                              KursID:'',KursSupplychainID:'',KursDate:'',KursNominal:''
                          });
                          store_kurs.insert(0, r);
                          kursRowEditing.startEdit(0, 0);
                     }
                  },{
                     icon: varjs.config.base_url+'images/icons/silk/pencil.png',
                     cls:m_act_save,
                     text: lang('Edit'),
                     scope: this,
                     handler : function() {
                       kursRowEditing.cancelEdit();
                       var sm = Ext.getCmp('grid_kurs').getSelectionModel().getSelection();
                       kursRowEditing.startEdit(sm[0].index, 0);
                     }
                  },{
                     itemId: 'remove',
                     icon: varjs.config.base_url+'images/icons/silk/delete.png',
                     text: lang('Hapus'),
                     scope: this,
                     handler : function(){
                       var smp = Ext.getCmp('grid_kurs').getSelectionModel().getSelection()[0];
                       kursRowEditing.cancelEdit();
                       Ext.MessageBox.confirm('Message', lang('Apakah anda mau menghapus Kurs ini ?') , function(btn){
                           if(btn == 'yes'){
                              Ext.Ajax.request({
                                 waitMsg: lang('Please Wait'),
                                 url: m_kurs,
                                 method : 'DELETE',
                                 params: {
                                    id:  smp.raw.KursID
                                 },
                                 success: function(response, opts){
                                    var obj = Ext.decode(response.responseText);
                                    switch(obj.success){
                                       case true:
                                          store_kurs.load({
                                             params: {
                                                id: Ext.getCmp('SupplychainID').getValue()
                                          }});
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
               columns: [{
                 text: lang('No'),
                 xtype: 'rownumberer',
                 width:'6%'
               },{
                 text: lang('Tanggal Awal'),
                 dataIndex: 'KursDateStart',
                 width:'24%',
                 editor: {
                    xtype:'datefield',
                    allowBlank:false,
                    format:'Y-m-d'
                 }
               },{
                 text: lang('Tanggal Akhir'),
                 dataIndex: 'KursDateEnd',
                 width:'24%',
                 editor: {
                    xtype:'datefield',
                    allowBlank:false,
                    format:'Y-m-d'
                 }
               },{
                 text: lang('Nominal'),
                 dataIndex: 'KursNominal',
                 width:'43%',
                 editor: {
                    xtype:'textfield',
                    allowBlank:false
                 }
               }],
               plugins: [kursRowEditing],
               listeners: {
                   'canceledit':function(editor,e,eOpts){
                        store_kurs.load({
                           params: {
                              id: Ext.getCmp('SupplychainID').getValue()
                        }});
                   },
                   'edit': function(editor, e) {
                     if(e.record.data.KursID==''){
                        Ext.Ajax.request({
                            waitMsg: lang('Please wait...'),
                            url: m_kurs,
                            method : 'POST',
                            params: {
                               KursSupplychainID      : Ext.getCmp('SupplychainID').getValue(),
                               KursDateStart    : e.record.data.KursDateStart,
                               KursDateEnd    : e.record.data.KursDateEnd,
                               KursNominal   : e.record.data.KursNominal
                            },
                            success: function(response, opts){
                                var obj = Ext.decode(response.responseText);
                                switch(obj.success){
                                    case true:
                                       Ext.MessageBox.alert('Success',obj.message);
                                       store_kurs.load({
                                          params: {
                                             id: Ext.getCmp('SupplychainID').getValue()
                                       }});
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
                        Ext.MessageBox.confirm('Message', lang('Update data kurs ini ?') , function(btn){
                           if(btn == 'yes') {
                              Ext.Ajax.request({
                                 waitMsg: lang('Please wait...'),
                                 url: m_kurs,
                                 method : 'PUT',
                                 params: {
                                     KursID    : e.record.data.KursID,
                                     KursSupplychainID      : Ext.getCmp('SupplychainID').getValue(),
                                     KursDateStart    : e.record.data.KursDateStart,
                                     KursDateEnd    : e.record.data.KursDateEnd,
                                     KursNominal   : e.record.data.KursNominal
                                 },
                                 success: function(response, opts){
                                     var obj = Ext.decode(response.responseText);
                                     switch(obj.success){
                                         case true:
                                            Ext.MessageBox.alert('Success',obj.message);
                                            store_kurs.load({
                                                params: {
                                                   id: Ext.getCmp('SupplychainID').getValue()
                                            }});
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
               }
            }]
         },{
            xtype: 'panel',
            autoScroll: true,
            id:'panel_premium',
            title: lang('Premium'),
            hidden:m_act_premium,
            padding:5,
            style: 'border:2px solid #ADD2ED',
            items: [{
               xtype: 'gridpanel',
               id:'grid_premium',
               store: store_premium,
               width: '100%',
               loadMask: true,
               selType: 'rowmodel',
               dockedItems: [{
                  xtype: 'toolbar',
                  items: [{
                     icon: varjs.config.base_url + 'images/icons/new/add.png', cls:'Sfr_BtnGridGreen', overCls:'Sfr_BtnGridGreen-Hover',
                     text: lang('Add'),
                     cls : m_act_save,
                     scope: this,
                     handler :function(){
                          preRowEditing.cancelEdit();
                          var r = Ext.create('premium.Model', {
                              PremiumID:'', PremiumSupplychainID:'', PremiumDateStart:'', PremiumDateEnd:'', PersenPetani:'',
                           	PersenBuyinUnit:'', PersenPerwakilan:'',USD:'', Kurs:'', Rupiah:'', DateCreated:'', CreatedBy:'', DateUpdated:'',
                              LastModifiedBy:''
                          });
                          store_premium.insert(0, r);
                          preRowEditing.startEdit(0, 0);
                     }
                  },{
                     icon: varjs.config.base_url+'images/icons/silk/pencil.png',
                     cls:m_act_save,
                     text: lang('Edit'),
                     scope: this,
                     handler : function() {
                       preRowEditing.cancelEdit();
                       var sm = Ext.getCmp('grid_premium').getSelectionModel().getSelection();
                       preRowEditing.startEdit(sm[0].index, 0);
                     }
                  },{
                     itemId: 'remove',
                     icon: varjs.config.base_url+'images/icons/silk/delete.png',
                     text: lang('Hapus'),
                     scope: this,
                     handler : function(){
                       var smp = Ext.getCmp('grid_premium').getSelectionModel().getSelection()[0];
                       preRowEditing.cancelEdit();
                       Ext.MessageBox.confirm('Message', lang('Apakah anda mau menghapus Reward ini ?') , function(btn){
                           if(btn == 'yes'){
                              Ext.Ajax.request({
                                 waitMsg: lang('Please Wait'),
                                 url: m_premium,
                                 method : 'DELETE',
                                 params: {
                                    id:  smp.raw.PremiumID
                                 },
                                 success: function(response, opts){
                                    var obj = Ext.decode(response.responseText);
                                    switch(obj.success){
                                       case true:
                                          store_premium.load({
                                             params: {
                                                id: Ext.getCmp('SupplychainID').getValue()
                                          }});
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
               columns: [{
                 text: lang('No'),
                 xtype: 'rownumberer',
                 width:'5%'
               },{
                 text: lang('Awal'),
                 dataIndex: 'PremiumDateStart',
                 width:'12.5%',
                 editor: {
                    xtype:'datefield',
                    allowBlank:false,
                    format:'Y-m-d'
                 }
               },{
                 text: lang('Akhir'),
                 dataIndex: 'PremiumDateEnd',
                 width:'12.5%',
                 editor: {
                    xtype:'datefield',
                    allowBlank:false,
                    format:'Y-m-d'
                 }
               },{
                 text: lang('% Petani'),
                 dataIndex: 'PersenPetani',
                 width:'15%',
                 editor: {
                    xtype:'textfield',
                    allowBlank:false
                 }
               },{
                 text: lang('% Koperasi'),
                 dataIndex: 'PersenBuyinUnit',
                 width:'15%',
                 //hidden:true,
                 editor: {
                    xtype:'textfield',
                    allowBlank:false
                 }
               },{
                 text: lang('% Lain (Perwakilan, SCE, Trader)'),
                 id:'premium_perwakilan',
                 dataIndex: 'PersenPerwakilan',
                 //hidden:true,
                 width:'25%',
                 editor: {
                    xtype:'textfield',
                    //allowBlank:false
                 }
               },{
                 text: lang('USD'),
                 dataIndex: 'USD',
                 width:'14%',
                 //hidden:true,
                 editor: {
                    xtype:'textfield',
                    //allowBlank:false
                 }
               },{
                 text: lang('Kurs'),
                 dataIndex: 'Kurs',
                 width:'12%',
                 hidden:true,
                 editor: {
                    xtype:'textfield',
                    //allowBlank:false
                 }
               },{
                 text: lang('Rupiah'),
                 dataIndex: 'Rupiah',
                 hidden:true,
                 width:'23%',
                 editor: {
                    xtype:'textfield',
                    //allowBlank:false
                 }
               }],
               plugins: [preRowEditing],
               listeners: {
                   'canceledit':function(editor,e,eOpts){
                       console.log('canceledit');
                        store_premium.load({
                           params: {
                              id: Ext.getCmp('SupplychainID').getValue()
                        }});
                   },
                   'edit': function(editor, e) {
                     if(e.record.data.PremiumID==''){
                        Ext.Ajax.request({
                            waitMsg: lang('Please wait...'),
                            url: m_premium,
                            method : 'POST',
                            params: {
                               PremiumSupplychainID      : Ext.getCmp('SupplychainID').getValue(),
                               PremiumDateStart    : e.record.data.PremiumDateStart,
                               PremiumDateEnd   : e.record.data.PremiumDateEnd,
                               PersenPetani   : e.record.data.PersenPetani,
                               PersenBuyinUnit   : e.record.data.PersenBuyinUnit,
                               PersenPerwakilan   : e.record.data.PersenPerwakilan,
                               USD   : e.record.data.USD,
                               Kurs   : e.record.data.Kurs,
                               Rupiah   : e.record.data.Rupiah
                            },
                            success: function(response, opts){
                                var obj = Ext.decode(response.responseText);
                                switch(obj.success){
                                    case true:
                                       Ext.MessageBox.alert('Success',obj.message);
                                       store_premium.load({
                                          params: {
                                             id: Ext.getCmp('SupplychainID').getValue()
                                       }});
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
                        Ext.MessageBox.confirm('Message', lang('Update data reward ini ?') , function(btn){
                           if(btn == 'yes') {
                              Ext.Ajax.request({
                                 waitMsg: lang('Please wait...'),
                                 url: m_premium,
                                 method : 'PUT',
                                 params: {
                                     PremiumID    : e.record.data.PremiumID,
                                     PremiumSupplychainID      : Ext.getCmp('SupplychainID').getValue(),
                                     PremiumDateStart    : e.record.data.PremiumDateStart,
                                     PremiumDateEnd   : e.record.data.PremiumDateEnd,
                                     PersenPetani   : e.record.data.PersenPetani,
                                     PersenBuyinUnit   : e.record.data.PersenBuyinUnit,
                                     PersenPerwakilan   : e.record.data.PersenPerwakilan,
                                     USD   : e.record.data.USD,
                                     Kurs   : e.record.data.Kurs,
                                     Rupiah   : e.record.data.Rupiah
                                 },
                                 success: function(response, opts){
                                     var obj = Ext.decode(response.responseText);
                                     switch(obj.success){
                                         case true:
                                            Ext.MessageBox.alert('Success',obj.message);
                                            store_premium.load({
                                                params: {
                                                   id: Ext.getCmp('SupplychainID').getValue()
                                            }});
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
               }
            }]
         }]
        }],
        buttons: [{
            id:'saveButton',
            text: lang('Save'),
            margin: '5px',
            scale: 'large',
            ui: 's-button',
            cls: 's-blue',
            handler: function() {
                var form = this.up('form').getForm();
                var urle = m_crud+'data';
                form.submit({
                    url: urle,
                    method:'POST',
                    waitMsg: lang('Sending data...'),
                    success: function(fp, o) {
                        Ext.MessageBox.alert('Success', 'Data saved.');
                        //search();
                        //if (Ext.getCmp('SupplychainID').getValue()!='') win.hide();
                        Ext.getCmp('SupplychainID').setValue(o.result.sid);
                        Ext.getCmp('all_panel').show()
                        mc_Kabupaten.load({params: {SupplychainID: o.result.sid}});
			//update(o.result.id);
                    }
                });
            }
        },{
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
        title: m_title,
        id:'win',
        closable: true,
        modal:true,
        closeAction: 'hide',
        autoScroll: true,
        width: '90%',
        height: '90%',
        listeners:{
            hide: function(){
                //supaya di reset lg form + gridnya
                Ext.getCmp('ObjType').reset();
                Ext.getCmp('ObjID').reset();
                Ext.getCmp('dataForm').getForm().reset();
                store_perwakilan.removeAll();
                store_relasi.removeAll();
                store_quality_standard.removeAll();
                store_quality.removeAll();
                store_price.removeAll();
                store_package.removeAll();
                store_kurs.removeAll();
                store_premium.removeAll();
            }
        },
        layout: {
            type: 'fit'
        },
        items: [DataForm]
    });
    function displayFormWindow(){
        DataForm.getForm().reset();
        if(!win.isVisible()){
            win.show();
        } else {
            win.show();
        }
    }
    function tambah() {
        console.log('new buying unit...');
        Ext.getCmp('ObjType').reset();
        Ext.getCmp('ObjID').reset();
        Ext.getCmp('dataForm').getForm().reset();
        displayFormWindow();
        Ext.getCmp('all_panel').hide()
    }
   //grid
   function search() {
      store.load({
         params: {
             type: Ext.getCmp('sType').getValue(),
             key: Ext.getCmp('key').getValue(),
             kab: Ext.getCmp('sKabupaten').getValue(),
             prov: Ext.getCmp('sProvinsi').getValue()
         }
      });
   }
   function submitOnEnterCari(field, event) {
    	if (event.getKey() == event.ENTER) search()
   }
    var store = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['SupplychainID','OrgType','OrgID','Name','Address','District'],
        autoLoad: true,
        pageSize: 50,
        proxy: {
            type: 'ajax',
            url: m_crud+'data',
            reader: {
                type: 'json',
                root: 'data',
                totalProperty: 'total'
            }
        }
    });
   var mc_Provinsi = Ext.create('Ext.data.Store', {
      extend: 'Ext.data.Model',
      fields: ['id','label'],
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
   var grid = Ext.create('Ext.grid.Panel', {
      store: store,
      width: '100%',
      minHeight: 250,
      id:'grid',
      style: 'border:1px solid #CCC;',
      renderTo: 'ext-content',
      loadMask: true,
      selType: 'rowmodel',
      listeners : {
         itemdblclick: function(dv, record, item, index, e) {
            var sm = record;
            update(sm.get('SupplychainID'))
         }
      },
      dockedItems: [{
         xtype: 'pagingtoolbar',
         store: store,   // same store GridPanel is using
         dock: 'bottom',
         displayInfo: true
      },{
         xtype: 'toolbar',
         items: [{
            icon: varjs.config.base_url + 'images/icons/new/add.png', cls:'Sfr_BtnGridGreen', overCls:'Sfr_BtnGridGreen-Hover',
            text: lang('Add'),
            scope: this,
            handler : function(){
               tambah();
            },
            cls : m_act_add
         },{
            icon: varjs.config.base_url+'images/icons/silk/pencil.png',
            text: lang('Update'),
            scope: this,
            handler : function(){
               var sm = Ext.getCmp('grid').getSelectionModel().getSelection()[0];
               update(sm.get('SupplychainID'))
            },
            cls : (m_act_update?'':'hide-icon')
         },{
            itemId: 'remove',
            icon: varjs.config.base_url+'images/icons/silk/delete.png',
            cls:m_act_delete,
            text: lang('Hapus'),
            scope: this,
            handler : function(){
                var smb = Ext.getCmp('grid').getSelectionModel().getSelection();
                if(smb.length > 0){
                    var id = smb[0].get('SupplychainID');

                    Ext.MessageBox.confirm('Message', lang('Apakah anda mau menghapus data ini ?') , function(btn){
                        if(btn == 'yes'){
                           Ext.Ajax.request({
                              waitMsg: lang('Please Wait'),
                              url: m_crud + 'del' + '/' + id,
                              method : 'DELETE',
                              success: function(response, opts){
                                 Ext.getCmp('grid').getStore().load();
                              },
                              failure: function(response, opts){
                                 var obj = Ext.decode(response.responseText);
                                 Ext.MessageBox.alert('error',lang('Could not connect to the database. Retry later'));
                              }
                           });
                        }
                    });
                } else {
                    Ext.MessageBox.alert('error',lang('Please select the data to delete'));
                }
            }
         },{
            id: 'sType',
            name: 'sType',
            xtype: 'combo',
            store:mc_type,
            displayField: 'label',
            valueField: 'id',
            queryMode: 'local',
            listeners: {
               specialkey: submitOnEnterCari
           	}
         },{
            name: 'key', baseCls:'Sfr_TxtfieldSearchGrid',
            id: 'key',
            xtype:'textfield',
            emptyText: lang('Cari berdasar nama/ID'),
            listeners: {
               specialkey: submitOnEnterCari
           	}
         },{
            id: 'sProvinsi',
            name: 'sProvinsi',
            xtype: 'combo',
            store:mc_Provinsi,
            displayField: lang('label'),
            valueField: 'label',
            queryMode: 'local',
            listeners: {
               specialkey: submitOnEnterCari,
               change: function (cb, nv, ov) {
                  mc_Kabupaten.load({
                     params: {
                        key: Ext.getCmp('sProvinsi').getValue()
                  }});
                  Ext.getCmp('sKabupaten').enable();
               }
            }
         },{
            id: 'sKabupaten',
            name: 'sKabupaten',
            xtype: 'combo',
            store:mc_Kabupaten,
            displayField: 'label',
            valueField: 'label',
            queryMode: 'local',
            listeners: {
               specialkey: submitOnEnterCari
           	}
         },{
            xtype :'button',
            icon: varjs.config.base_url+'images/icons/silk/search.png',
            margin: '0px 0px 0px 6px',
            text: lang('Search'),
            handler: function() {
               search()
            }
         }]
      }],
      columns: [{
         text: lang('No'),
         xtype: 'rownumberer',
         width:'5%'
      },{
          text: lang('Type'),
          width: '15%',
          dataIndex: 'OrgType'
      },{
          text: lang('ID'),
          width: '10%',
          dataIndex: 'OrgID'
      },{
          text: lang('Nama'),
          width: '20%',
          dataIndex: 'Name'
      },{
          text: lang('Alamat'),
          width: '25%',
          dataIndex: 'Address'
      },{
          text: lang('District'),
          width: '24%',
          dataIndex: 'District'
      }]
    });

    var DataFormAddArea = Ext.create('Ext.form.Panel', {
        id: 'dataFormAddArea',
        frame: false,
        width: 450,
        height: 200,
        autoScroll:true,
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
                                columnWidth: 1,
                                layout: 'form',
                                items: [
                                    {
                                        xtype: 'combobox',
                                        id: 'buProvinceID',
                                        name: 'buProvinceID',
                                        emptyText: lang('Province'),
                                        store: province,
                                        allowBlank: false,
                                        queryMode: 'local',
                                        displayField: 'label',
                                        valueField: 'id',
                                        listeners: {
                                            change: function (cb, nv, ov) {
                                                Ext.getCmp('buDistrictID').setValue('');
                                                district.load({
                                                    params: {
                                                        SupplychainID: Ext.getCmp('SupplychainID').getValue(),
                                                        ProvinceID: Ext.getCmp('buProvinceID').getValue()
                                                    }
                                                });
                                            }
                                        }
                                    }, {
                                        xtype: 'combobox',
                                        id: 'buDistrictID',
                                        name: 'buDistrictID',
                                        emptyText: lang('District'),
                                        store: district,
                                        allowBlank: false,
                                        queryMode: 'local',
                                        displayField: 'label',
                                        valueField: 'id'
                                    }
                                ]
                            }
                        ]
                    },
                ],
            }
        ],
        buttons: [{
            id: 'busave_number',
            text: 'Save',
            margin: '5px',
            scale: 'large',
            ui: 's-button',
            cls: 's-blue',
            handler: function () {
                var form = this.up('form').getForm();
                if (form.isValid()) {
                    form.submit({
                        url: m_crud + 'supplychain_area',
                        method: 'POST',
                        waitMsg: lang('Sending data...'),
                        params: {
                            SupplychainID: Ext.getCmp('SupplychainID').getValue(),
                            DistrictID: Ext.getCmp('buDistrictID').getValue()
                        },
                        success: function(fp, o) {
                            switch (o.result.success) {
                                case true:
                                    Ext.MessageBox.alert('Success', o.result.message);
                                    store_area.load({
                                       params: {
                                          SupplychainID: Ext.getCmp('SupplychainID').getValue()
                                    }});
                                    winAddArea.hide();
                                    break;
                                default:
                                    Ext.MessageBox.alert('Warning', o.result.message);
                                    break;
                            }
                        },
                        failure: function (response, opts) {
                            Ext.MessageBox.alert('error', 'Could not connect to the database. Retry later');
                        }
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
                winAddArea.hide();
            }
        }]
    });

    var winAddArea = Ext.create('widget.window', {
        title: lang('Add Area'),
        frame: false,
        closable: true,
        id: 'winArea',
        modal: true,
        closeAction: 'show',
        width: 450,
        minWidth: 350,
        height: 220,
        layout: 'fit',
        items: [DataFormAddArea]
    });
});
//end grid
