/*
* @Author: nikolius
* @Date:   2016-08-19 15:28:46
* @Last Modified by:   nikolius
* @Last Modified time: 2016-10-27 13:27:47
*/
Ext.onReady(function(){
    Ext.tip.QuickTipManager.init();

    //store garden_status =============== (begin)
    Ext.define('gardenStatus.Model', {
        extend: 'Ext.data.Model',
        fields: ['FarmerID', 'GardenNr', 'GardenStatus', 'GardenHaUnCertified', 'Commodity', 'Remarks']
    });
    var store_garden_status = Ext.create('Ext.data.Store', {
        model: 'gardenStatus.Model',
        autoLoad: false,
        proxy: {
            type: 'ajax',
            url: m_get_farmer_garden,
            reader: {
                type: 'json'
            }
        }
    });
    //store garden_status =============== (end)

    //get data profile by session
    Ext.Ajax.request({
        url: m_get_profile,
        method: 'GET',
        waitMsg: lang('Please Wait'),
        success: function(data) {
            var jsonResp = JSON.parse(data.responseText);

            Ext.getCmp('sce_id').setValue(jsonResp.sce_id);
            Ext.getCmp('FarmerName').setValue(jsonResp.displayField);
            Ext.getCmp('FarmerGroup').setValue(jsonResp.grup);
            Ext.getCmp('SubDistrict').setValue(jsonResp.sub_district);
            Ext.getCmp('District').setValue(jsonResp.district);
            Ext.getCmp('Province').setValue(jsonResp.province);
            Ext.getCmp('Village').setValue(jsonResp.village);
            Ext.getCmp('Address').setValue(jsonResp.address);
            Ext.getCmp('Handphone').setValue(jsonResp.handphone);
            Ext.getCmp('Status').setValue(jsonResp.status);
            Ext.getCmp('Latitude').setValue(jsonResp.Latitude);
            Ext.getCmp('Longitude').setValue(jsonResp.Longitude);

            //photo
            var fotoFarmer = m_photo + jsonResp.photo;
            checkImageExists(fotoFarmer, function(existsImage) {
                if (existsImage == true) {
                    Ext.getCmp('ilogo').setSrc(fotoFarmer);
                } else {
                    Ext.getCmp('ilogo').setSrc(m_photo + 'no-user.jpg');
                }
            });

            //load garden
            store_garden_status.load({
                params: {
                    id: jsonResp.id
                }
            });
        },
        failure: function() {
            Ext.MessageBox.show({
                title: 'Notifications',
                msg: 'Failed to get data. No Professional Farmer selected',
                buttons: Ext.MessageBox.OK,
                animateTarget: 'mb9',
                icon: 'ext-mb-info'
            });
        }
    });

    var DataPanel = Ext.create('Ext.Panel', {
        title:'Farmer Profile',
        padding: 0,
        margin:15,
        frame: true,
        bodyStyle:{"background-color":"#F0F0F0"},
        style:'background-color:#F0F0F0;',
        layout: 'column',
        items: [{
            columnWidth: .5,
            layout: 'form',
            padding: 10,
            items: [{
                xtype: 'fieldset',
                title: 'Basic Data',
                height: '100%',
                items: [{
                    xtype: 'textfield',
                    fieldLabel: lang('SCE ID'),
                    id: 'sce_id',
                    name: 'sce_id',
                    anchor: '100%',
                    readOnly: true
                }, {
                    xtype: 'textfield',
                    fieldLabel: lang('Farmer'),
                    id: 'FarmerName',
                    name: 'FarmerName',
                    anchor: '100%',
                    readOnly: true
                },{
                    xtype: 'textfield',
                    fieldLabel: lang('Province'),
                    id: 'Province',
                    name: 'Province',
                    readOnly: true,
                    anchor: '100%'
                }, {
                    xtype: 'textfield',
                    fieldLabel: lang('District'),
                    id: 'District',
                    name: 'District',
                    readOnly: true,
                    anchor: '100%'
                }, {
                    xtype: 'textfield',
                    fieldLabel: lang('Sub District'),
                    id: 'SubDistrict',
                    name: 'SubDistrict',
                    readOnly: true,
                    anchor: '100%'
                }, {
                    xtype: 'textfield',
                    fieldLabel: lang('Village'),
                    id: 'Village',
                    name: 'Village',
                    readOnly: true,
                    anchor: '100%'
                }, {
                    xtype: 'textfield',
                    fieldLabel: lang('Address'),
                    id: 'Address',
                    name: 'Address',
                    readOnly: true,
                    anchor: '100%'
                }, {
                    xtype: 'textfield',
                    fieldLabel: lang('Farmer Group'),
                    id: 'FarmerGroup',
                    name: 'FarmerGroup',
                    readOnly: true,
                    anchor: '100%'
                }, {
                    xtype: 'textfield',
                    fieldLabel: lang('Handphone'),
                    id: 'Handphone',
                    name: 'Handphone',
                    readOnly: true,
                    anchor: '100%'
                }, {
                    xtype: 'textfield',
                    fieldLabel: lang('Status'),
                    id: 'Status',
                    name: 'Status',
                    readOnly: true,
                    anchor: '100%'
                }]
            }, {
                xtype: 'fieldset',
                title: 'Buying Unit Location',
                height: '100%',
                items: [{
                    xtype: 'textfield',
                    fieldLabel: lang('Latitude'),
                    id: 'Latitude',
                    name: 'Latitude',
                    anchor: '100%',
                    readOnly: true
                }, {
                    xtype: 'textfield',
                    fieldLabel: lang('Longitude'),
                    id: 'Longitude',
                    name: 'Longitude',
                    anchor: '100%',
                    readOnly: true
                }]
            }]
        },{
            columnWidth: .5,
            layout: 'form',
            padding: 10,
            style: 'margin-left:12px',
            items: [{
                xtype: 'fieldset',
                title: 'Photo',
                height: '100%',
                width: '150px',
                id: 'panelFoto',
                items: [{
                    xtype: 'image',
                    id: 'ilogo',
                    width: '130px',
                    src: m_photo + 'no-user.jpg'
                }]
            }, {
                xtype: 'fieldset',
                title: 'Garden',
                height: '100%',
                width: '100%',
                items: [{
                    xtype: 'gridpanel',
                    id: 'gridGarden',
                    store: store_garden_status,
                    width: '100%',
                    minHeight: 150,
                    loadMask: true,
                    columns: [{
                        text: lang('GardenNr'),
                        dataIndex: 'GardenNr',
                        width: '15%'
                    }, {
                        text: lang('Ha'),
                        dataIndex: 'GardenHaUnCertified',
                        width: '15%'
                    }, {
                        text: lang('Garden Status'),
                        dataIndex: 'GardenStatus',
                        width: '30%'
                    }, {
                        text: lang('Remarks'),
                        dataIndex: 'Remarks',
                        width: '39%'
                    }]
                }]
            }]
        }],
        renderTo: 'ext-content'
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
});