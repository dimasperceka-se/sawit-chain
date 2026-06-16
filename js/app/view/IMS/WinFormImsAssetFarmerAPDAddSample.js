/******************************************
 *  Author : n1colius.lau@gmail.com   
 *  Created On : Fri Oct 05 2018
 *  File : WinFormImsAssetFarmerAPDAddSample.js
 *******************************************/

/*
    Param2 yg diperlukan ketika load View ini    
    - IMSID
    - CallerStore
*/

Ext.define('Koltiva.view.IMS.WinFormImsAssetFarmerAPDAddSample' ,{
    extend: 'Ext.window.Window',
    id: 'Koltiva.view.IMS.WinFormImsAssetFarmerAPDAddSample',
    title: lang('IMS - Farmer APD (Add Farmer Sample)'),
    closable: true,
    modal: true,
    closeAction: 'destroy',
    width: '52%',
    //height: '18%',
    overflowY: 'auto',
    viewVar: false,
    setViewVar: function(value){
        this.viewVar = value;
    },
    initComponent: function() {
        var thisObj = this;

        //Store ===================================== (Begin)
        thisObj.store_farmer_search = Ext.create('Koltiva.store.IMS.ImsAssetRcpGridFarmerAPDFarmerSearch',{
        	storeVar: {
                IMSID: thisObj.viewVar.IMSID
            }
        });
        //Store ===================================== (End)

        //items ---------------------------------------------------------------------------------------------------------------------------- (Begin)
        thisObj.items = [{
            xtype: 'form',
            id: 'Koltiva.view.IMS.WinFormImsAssetFarmerAPDAddSample-Form',
            fileUpload: true,
            padding:'5 25 5 8',
            items:[{
                layout: 'column',
                border: false,
                items:[{
                    columnWidth: 0.75,
                    layout:'form',
                    items:[{
                        xtype: 'combo',
                        id: 'Koltiva.view.IMS.WinFormImsAssetFarmerAPDAddSample-Form-FarmerSearch',
                        name: 'Koltiva.view.IMS.WinFormImsAssetFarmerAPDAddSample-Form-FarmerSearch',
                        fieldLabel: lang('Farmer Search'),
                        store: thisObj.store_farmer_search,
                        displayField: 'DisplayField',
                        typeAhead: false,
                        hideTrigger: true,
                        anchor: '100%',
                        listConfig: {
                            loadingText: lang('Searching...'),
                            emptyText: lang('No matching farmer found'),
                            getInnerTpl: function() {
                                return '<div class="search-item">' + '{FarmerID} - {FarmerName} ({FarmerGroup}) </div>';
                            }
                        },
                        pageSize: 10,
                        listeners: {
                            select: function(combo, selection) {
                                var post = selection[0];
                                //console.log(post);
                                Ext.getCmp('Koltiva.view.IMS.WinFormImsAssetFarmerAPDAddSample-Form-FarmerID').setValue(post.data.FarmerID);
                            }
                        }
                    },{
                        html:'<div style="font-size:11px;font-style:italic;color:green;margin-top:-6px;">* '+lang('Type FarmerID / Farmer Name to search')+'</div>'
                    },{
                        xtype: 'hiddenfield',
                        id: 'Koltiva.view.IMS.WinFormImsAssetFarmerAPDAddSample-Form-FarmerID',
                        name: 'Koltiva.view.IMS.WinFormImsAssetFarmerAPDAddSample-Form-FarmerID'
                    }]
                },{
                    columnWidth: 0.25,
                    layout:'column',
                    style:'margin-left:25px;margin-top:5px;',
                    items:[{
                        columnWidth: 1,
                        border: false,
                        layout:{
                            type:'hbox',
                            pack:'left',
                            align: 'middle'
                        },
                        items:[{
                            xtype: 'button',
                            icon: varjs.config.base_url + 'images/icons/new/add.png',
                            text: lang('Add Farmer Sample'),
                            cls: 'Sfr_BtnFormGreen',
                            overCls: 'Sfr_BtnFormGreen-Hover',
                            handler: function() {
                                var FarmerID = Ext.getCmp('Koltiva.view.IMS.WinFormImsAssetFarmerAPDAddSample-Form-FarmerID').getValue();
                                if(FarmerID != ""){
                                    Ext.Ajax.request({
                                        url: m_api + '/ims_asset_rcp/insert_farmer_sample',
                                        method: 'POST',
                                        params: {
                                            IMSID: thisObj.viewVar.IMSID,
                                            FarmerID: FarmerID
                                        },
                                        success: function(response, action) {
                                            var rp = Ext.decode(response.responseText);

                                            Ext.MessageBox.show({
                                                title: 'Information',
                                                msg: rp.message,
                                                buttons: Ext.MessageBox.OK,
                                                animateTarget: 'mb9',
                                                icon: 'ext-mb-success'
                                            });

                                            thisObj.viewVar.CallerStore.load();
                                            Ext.getCmp('Koltiva.view.IMS.WinFormImsAssetFarmerAPDAddSample-Form-FarmerID').setValue('');
                                            Ext.getCmp('Koltiva.view.IMS.WinFormImsAssetFarmerAPDAddSample-Form-FarmerSearch').setValue('');
                                        },
                                        failure: function(response, action){
                                            var rp = Ext.decode(response.responseText);

                                            Ext.MessageBox.show({
                                                title: lang('Attention'),
                                                msg: rp.message,
                                                buttons: Ext.MessageBox.OK,
                                                animateTarget: 'mb9',
                                                icon: 'ext-mb-error'
                                            });
                                        }
                                    });
                                }else{
                                    Ext.MessageBox.show({
                                        title: 'Attention',
                                        msg: lang('No Farmer selected'),
                                        buttons: Ext.MessageBox.OK,
                                        animateTarget: 'mb9',
                                        icon: 'ext-mb-info'
                                    });
                                }
                            }
                        }]
                    }]
                }]
            }]
        }];
        //items ---------------------------------------------------------------------------------------------------------------------------- (End)

        this.callParent(arguments);
    }
});