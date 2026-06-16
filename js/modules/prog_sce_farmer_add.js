/*
* @Author: nikolius
* @Date:   2016-10-27 13:47:25
* @Last Modified by:   nikolius
* @Last Modified time: 2016-10-27 14:58:08
*/
Ext.onReady(function(){
    Ext.tip.QuickTipManager.init();

    //Autocomplete Search Farmer ====================== (begin)
    Ext.define("Post", {
        extend: 'Ext.data.Model',
        proxy: {
            type: 'ajax',
            url: m_api + '/sce/farmer_sce_farmers',
            extraParams: {
                prov: ''
            },
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
            name: 'grup',
            mapping: 'grup'
        }, {
            name: 'sub_district',
            mapping: 'sub_district'
        }, {
            name: 'district',
            mapping: 'district'
        }, {
            name: 'province',
            mapping: 'province'
        }, {
            name: 'village',
            mapping: 'village'
        }, {
            name: 'photo',
            mapping: 'photo'
        }, {
            name: 'displayField',
            mapping: 'displayField'
        }, {
            name: 'address',
            mapping: 'address'
        }, {
            name: 'handphone',
            mapping: 'handphone'
        }, {
            name: 'status',
            mapping: 'status'
        }]
    });
    var ds = Ext.create('Ext.data.Store', {
        pageSize: 10,
        model: 'Post'
    });
    //Autocomplete Search Farmer ====================== (end)

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
            url: m_api + '/farmer/farmerl_garden_status',
            reader: {
                type: 'json'
            }
        }
    });
    //store garden_status =============== (end)

    var DataPanel = Ext.create('Ext.form.Panel', {
        title: lang('Add Farmer'),
        padding: 0,
        margin:15,
        id:'dataForm',
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
                    id: 'FarmerID',
                    name: 'FarmerID',
                    inputType: 'hidden'
                },{
                    xtype: 'textfield',
                    id: 'formFrom',
                    name: 'formFrom',
                    inputType: 'hidden',
                    value: 'sce'
                },{
                    xtype: 'combo',
                    store: ds,
                    id: 'FarmerName',
                    name: 'FarmerName',
                    displayField: 'displayField',
                    fieldLabel: lang('Farmer'),
                    typeAhead: false,
                    hideTrigger: true,
                    anchor: '100%',
                    listConfig: {
                        loadingText: 'Searching...',
                        emptyText: 'No matching farmer found.',
                        getInnerTpl: function() {
                            return '<div class="search-item">' + '{id} - {name} ({grup})' + '{excerpt}' + '</div>';
                        }
                    },
                    pageSize: 10,
                    listeners: {
                        select: function(combo, selection) {
                            var post = selection[0];
                            if (post) {
                                Ext.getCmp('FarmerID').setValue(post.get('id'));
                                Ext.getCmp('FarmerName').setValue(post.get('displayField'));
                                Ext.getCmp('FarmerGroup').setValue(post.get('grup'));
                                Ext.getCmp('SubDistrict').setValue(post.get('sub_district'));
                                Ext.getCmp('District').setValue(post.get('district'));
                                Ext.getCmp('Province').setValue(post.get('province'));
                                Ext.getCmp('Village').setValue(post.get('village'));
                                Ext.getCmp('Address').setValue(post.get('address'));
                                Ext.getCmp('Handphone').setValue(post.get('handphone'));
                                Ext.getCmp('Status').setValue(post.get('status'));
                                Ext.getCmp('ilogo').setSrc(m_photo + post.get('photo'));
                                //photo
                                var fotoFarmer = m_photo + post.get('photo');
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
                                        id: post.get('id')
                                    }
                                });
                            }
                        }
                    }
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
                    readOnly: m_hakakses_lat_short
                }, {
                    xtype: 'textfield',
                    fieldLabel: lang('Longitude'),
                    id: 'Longitude',
                    name: 'Longitude',
                    anchor: '100%',
                    readOnly: m_hakakses_long_short
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
        buttons: [{
            id: 'saveButton',
            text: lang('Save'),
            margin: '5px',
            scale: 'large',
            ui: 's-button',
            cls: 's-blue',
            handler: function() {
                //event click
                var form = this.up('form').getForm();
                var methode;
                methode = 'POST';
                form.submit({
                    url: m_api + '/sce/farmer_sce',
                    method: methode,
                    waitMsg: 'Sending data...',
                    success: function(fp, o) {
                        Ext.MessageBox.show({
                            title: 'Success',
                            msg: lang('Data saved'),
                            buttons: Ext.MessageBox.OK,
                            animateTarget: 'mb9',
                            icon: 'ext-mb-success'
                        });
                        window.location = m_baseUrlNya+'/prog_sce/profile';
                    },
                    failure: function(response, opts) {
                        Ext.MessageBox.show({
                            title: 'Failed',
                            msg: lang('Saved failed'),
                            buttons: Ext.MessageBox.OK,
                            animateTarget: 'mb9',
                            icon: 'ext-mb-error'
                        });
                    }
                });
            }
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