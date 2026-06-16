Ext.Loader.setConfig({enabled: true});
Ext.Loader.setPath('Ext.ux', varjs.config.base_url+'js/'+varjs.config.extjs_version+'/ux');
Ext.require([
    'Ext.grid.*',
    'Ext.data.*',
    'Ext.panel.*',
    'Ext.ux.grid.FiltersFeature',
    'Ext.form.Panel',
    'Ext.tab.*',
    'Ext.window.*',
    'Ext.tip.*',
    'Ext.layout.container.Border'
]);
var crud_action='update';
Ext.onReady(function(){
    Ext.define('CategoryModel', {
        extend: 'Ext.data.Model',
        fields: ['RegionID', 'RegionName', 'ProvinceCode', 'Status']
    });
    var store = Ext.create('Ext.data.JsonStore', {
        model: 'CategoryModel',
        proxy: {
            type: 'ajax',
            url: m_crud,
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });
    store.load();

    var win;
    var LocationForm;
    LocationForm = Ext.create('Ext.tab.Panel', {
        width: '100%',
        title: 'Daftar Kategori Lokasi',
        renderTo: 'ext-content',
        height: 332,
        activeTab: 2,
        items: [
            {
                xtype: 'panel',
                title: 'Tab 1',
                items: [
                    {
                        xtype: 'fieldset',
                        title: 'Personal Info',
                        items: [
                            {
                                xtype: 'textfield',
                                fieldLabel: 'Name',
                                anchor: '100%'
                            },
                            {
                                xtype: 'textfield',
                                fieldLabel: 'Birth Place',
                                anchor: '100%'
                            },
                            {
                                xtype: 'datefield',
                                fieldLabel: 'Birth Date',
                                anchor: '100%'
                            },
                            {
                                xtype: 'textfield',
                                fieldLabel: 'Phone',
                                anchor: '100%'
                            },
                            {
                                xtype: 'textareafield',
                                fieldLabel: 'Address',
                                anchor: '100%'
                            }
                        ]
                    },
                    {
                        xtype: 'fieldset',
                        title: 'Additional Info',
                        items: [
                            {
                                xtype: 'textfield',
                                fieldLabel: 'Work',
                                anchor: '100%'
                            },
                            {
                                xtype: 'textfield',
                                fieldLabel: 'Work2',
                                anchor: '100%'
                            }
                        ]
                    }
                ]
            },
            {
                xtype: 'panel',
                title: 'Tab 2',
                items: [
                    {
                        xtype: 'gridpanel',
                        title: 'My Grid Panel',
                        columns: [
                            {
                                xtype: 'gridcolumn',
                                dataIndex: 'string',
                                text: 'String'
                            },
                            {
                                xtype: 'numbercolumn',
                                dataIndex: 'number',
                                text: 'Number'
                            },
                            {
                                xtype: 'datecolumn',
                                dataIndex: 'date',
                                text: 'Date'
                            },
                            {
                                xtype: 'booleancolumn',
                                dataIndex: 'bool',
                                text: 'Boolean'
                            }
                        ],
                        viewConfig: {

                        }
                    }
                ]
            },
            {
                xtype: 'panel',
                title: 'Tab 3',
                items: [
                    {
                        xtype: 'tabpanel',
                        activeTab: 0,
                        plain: true,
                        items: [
                            {
                                xtype: 'panel',
                                title: 'Tab 31'
                            },
                            {
                                xtype: 'panel',
                                title: 'Tab 32'
                            },
                            {
                                xtype: 'panel',
                                title: 'Tab 33'
                            }
                        ]
                    },
                    {
                        xtype: 'textfield',
                        fieldLabel: 'Label'
                    },
                    {
                        xtype: 'textfield',
                        fieldLabel: 'Label'
                    }
                ]
            }
        ]
    });

    win = Ext.create('widget.window', {
        title: 'Data Kategori',
        closable: true,
        closeAction: 'hide',
        width: 400,
        minWidth: 370,
        height: 160,
        layout: {
            type: 'border',
            padding: 5
        },
        items: [LocationForm]
    });

    function displayFormWindow(){
        if(!win.isVisible()){

            win.show();
            //	storeName.load();
        } else {
            win.hide(this, function() {

            });
            win.toFront();
        }
    }


});
