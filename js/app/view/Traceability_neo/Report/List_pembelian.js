var cmbSupplyType = Ext.create('Koltiva.store.Traceability_new.Transaction.ComboSupplyType');
var cmbSupplyStatus = Ext.create('Koltiva.store.Traceability_new.Transaction.ComboSupplyStatus');
var storeGridMainRecapPurchase = Ext.create('Koltiva.store.Traceability_new.Report.MainGridRecapPurchase');

Ext.define('Koltiva.view.Traceability_neo.Report.List_pembelian' ,{
    extend: 'Ext.panel.Panel',
    id: 'Koltiva.view.Traceability_neo.Report.List_pembelian', 
    viewVar: false,
    setViewVar: function(value){
        this.viewVar = value;
    },
    frame: false, 
    collapsible:false, 
    margin:'0 0 0 0',
    initComponent: function() {
        var thisObj = this;

        thisObj.items = [{
                            xtype: 'grid',
                            id: 'Koltiva.view.Traceability_new.Transaction.MainGrid-gridTransaction',
                            style: 'border:1px solid #CCC;margin-top:4px;',
                            loadMask: true,
                            selType: 'rowmodel',
                            store: storeGridMainRecapPurchase,
                            viewConfig: {
                                deferEmptyText: false,
                                emptyText: lang('No data Available')
                            },
                            dockedItems: [{
                                xtype: 'pagingtoolbar',
                                id: 'Koltiva.view.Traceability_new.Transaction.MainGrid-gridToolbar',
                                store: storeGridMainRecapPurchase,
                                dock: 'bottom',
                                displayInfo: true,
                                hidden: (m_pid == '14' || m_pid == '194') ? true : false, // khusus WAGS & mill perak tidak ada paging
                            },{
                                xtype: 'toolbar',
                                dock:'top',
                                items: [
                                {
                                    xtype: 'button',
                                    id: 'Koltiva.view.Traceability_new.Transaction.MainGrid-gridToolbar-BtnExport',
                                    icon: varjs.config.base_url + 'images/icons/new/export.png', cls:'Sfr_BtnGridPaleBlue',
                                    margin: '0px 10px 0px 6px',
                                    text: lang('Export'),
                                    cls:'Sfr_BtnGridGreen',
                                    overCls:'Sfr_BtnGridGreen-Hover',
                                    handler: function() {

                                        Ext.MessageBox.show({
                                            msg: 'Please wait...',
                                            progressText: 'Exporting...',
                                            width: 300,
                                            wait: true,
                                            waitConfig: {
                                                interval: 200
                                            },
                                            icon: 'ext-mb-download', //custom class in msg-box.html
                                            animateTarget: 'mb7'
                                        });

                                        var param_string    = '?sid='+m_sid;
        
                                        try {
                                            Ext.destroy(Ext.get('downloadIframe'));
                                        }
                                        catch(e) {}
        
                                        Ext.Ajax.request({
                                            url: m_api+'/traceability_api/web_transaction/export_excel_report/'+param_string,
                                        
                                            method: 'GET',
                                            waitMsg: lang('Please Wait'),
                                            timeout: 360000,
                                            success: function(data) {
                                                Ext.MessageBox.hide();
                                                var jsonResp = JSON.parse(data.responseText);
                                                window.location = jsonResp.filenya;
                                            },
                                            failure: function() {
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
                                }, {
                                    xtype: 'container',
                                    flex: 1
                                }]
                            }],
                            columns: [
                            {
                                text: lang('Date'),
                                dataIndex: 'Tanggal_transaksi',
                                renderer: Ext.util.Format.dateRenderer('d-m-Y'),
                                width:100,
                            },
                            {
                                text: lang('Supplier ID'),
                                dataIndex: 'ID_pemasok',
                                width:100,
                            },
                            {
                                text: lang('Supplier Name'),
                                dataIndex: 'Nama_Pemasok',
                                flex:1
                            },
                            {
                                text: lang('Bunches'),
                                dataIndex: 'Janjang',
                                width:90
                            },{
                                text: lang('Gross'),
                                dataIndex: 'Berat_Kotor',
                                width:100
                            },{
                                text: lang('Deduction (%)'),
                                dataIndex: 'Presentase_pemotongan',
                                width:100
                            },
                            {
                                text: lang('Netto'),
                                dataIndex: 'Berat_bersih',
                                width:150
                            },
                            {
                                text: lang('Price Per Kilo'),
                                dataIndex: 'Harga_per_kilo',
                                width:150
                            },
                            {
                                text: lang('Total Price'),
                                dataIndex: 'Total',
                                width:150,
                                renderer: Ext.util.Format.numberRenderer('0,000,000')
                            },
                            {
                                text: lang('Payment Reduction'),
                                dataIndex: 'Pengurangan_pembayaran',
                                width:150,
                                renderer: Ext.util.Format.numberRenderer('0,000,000')
                            },
                            {
                                text: lang('Payment Amount'),
                                dataIndex: 'Jumlah_pembayaran',
                                width:150,
                                renderer: Ext.util.Format.numberRenderer('0,000,000')
                            },
                            {
                                text: lang('Traceable Status'),
                                dataIndex: 'Ketelusuran',
                                width:150
                            }] 
				}];
				this.callParent(arguments);
    }
});

function SetFormTransaction(SupplyTransID) { 
		Ext.Ajax.request({
			waitMsg: 'Please Wait',
			url: m_api + '/web-traceability/main-grid',
			method: 'get',
			params: {
				STID : SupplyTransID,
				SID : m_sid,
				page : 1,
				start : 0,
				limit :1
			},
			success: function(response, opts) {
				var r = Ext.decode(response.responseText);
				Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormTransaction-DateTransaction').setValue(r.data[0].DateTransaction);
				Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormTransaction-STID').setValue(r.data[0].SupplyTransID);
				var ComboFarmer = Ext.create('Koltiva.store.Traceability_new.Transaction.ComboFarmer');
				ComboFarmer.load();
				Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormTransaction-FarmerID').setValue(r.data[0].SupplyID); 
				 
                var PlantationNr =  r.data[0].PlantationNr;
                
                if(r.data[0].SupplyStatus != 'Open'){
                    Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormTransaction-DateTransaction').setDisabled(true);
                }
				 
				Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormTransaction-PlantationNr').setValue(PlantationNr); 
				Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormTransaction-Certified').setValue(r.data[0].Certified); 
                Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormTransaction-ContractPrice').setValue(r.data[0].ContractPrice);
                Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormTransaction-FFBCount').setValue(r.data[0].FFBCount); 
                Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormTransaction-VolumeBruto').setValue(r.data[0].VolumeBruto);
                Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormTransaction-VolumeNetto').setValue(r.data[0].VolumeNetto);
				Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormTransaction-InvoiceNumber').setValue(r.data[0].InvoiceNumber);
                Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormTransaction-TotalPayment').setValue(r.data[0].TotalPayment);
                
                if(r.data[0].SalesType == "1"){
                    Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormTransaction-FarmerTC').setValue(true);
                    Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormTransaction-FarmerTC').setVisible(true);
                    Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormTransaction-NonFarmerTC').setValue(false);
                    Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormTransaction-NonFarmerTC').setVisible(false);
                    Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormTransaction-batchTC').setValue(false);
                    Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormTransaction-batchTC').setVisible(false);
                }
                if(r.data[0].SalesType == "2"){
                    Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormTransaction-NonFarmerTC').setValue(true);
                    Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormTransaction-NonFarmerTC').setVisible(true);
                    Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormTransaction-FarmerTC').setValue(false);
                    Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormTransaction-FarmerTC').setVisible(false);
                    Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormTransaction-batchTC').setValue(false);
                    Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormTransaction-batchTC').setVisible(false);
                    Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormTransaction-PlantationNrNonFarmer').setValue(PlantationNr);
                }
                if(r.data[0].SalesType == "3"){
                    Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormTransaction-batchTC').setValue(true);
                    Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormTransaction-batchTC').setVisible(true);
                    Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormTransaction-NonFarmerTC').setValue(false);
                    Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormTransaction-NonFarmerTC').setVisible(false);
                    Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormTransaction-FarmerTC').setValue(false);
                    Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormTransaction-FarmerTC').setVisible(false);
                    Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormTransaction-SellerType').setValue(r.data[0].SellerType);
                    Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormTransaction-Mill').setValue(r.data[0].MillID);
                    if(r.data[0].OtherMill == "1"){
                        Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormTransaction-OtherMill').setValue(true);
                    }
                    Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormTransaction-OtherMillName').setValue(r.data[0].MillOther);
                    Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormTransaction-DO').setValue(r.data[0].DOID);
                    if(r.data[0].OtherDO == "1"){
                        Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormTransaction-OtherDO').setValue(true);
                    }
                    Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormTransaction-OtherDOName').setValue(r.data[0].DOOther);
                    Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormTransaction-Agent').setValue(r.data[0].AgentID);
                    if(r.data[0].OtherAgent == "1"){
                        Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormTransaction-OtherAgent').setValue(true);
                    }
                    Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormTransaction-OtherAgentName').setValue(r.data[0].AgentOther);
                    Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormTransaction-OtherAgentNin').setValue(r.data[0].AgentOtherNIK);
                    Ext.getCmp('Koltiva.view.Traceability_new.Transaction.FormTransaction-OtherAgentSurvey').setValue(r.data[0].AgentOtherSurvey);
                }
				
			}
		})
}
 