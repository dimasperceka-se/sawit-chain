Ext.define('Ext.ux.ktv.BasicData', {
    extend: 'Ext.form.field.Trigger',
    
    alias: 'widget.ktvfarmer',
    
    trigger1Cls: Ext.baseCSSPrefix + 'form-clear-trigger',
    
    trigger2Cls: Ext.baseCSSPrefix + 'form-search-trigger',
    
    readOnly:false,
    
    hasSearch : false,
    paramName : 'query',
    
    initComponent: function(){
        
        this.store = Ext.create('Ext.data.Store', {
            fields: ['loanTypeID', 'loanTypeName'],
            autoLoad: true,
            proxy: {
                type: 'rest',
                url: window.location.origin + '/api/index.php/common/getcombo', // url that will load data with respect to start and limit params
                extraParams: {
                    table: 'coop_loan_type',
                    name: 'loanTypeID',
                    id: 'loanTypeName'
                },
                reader: {
                    type: 'json',
                    root: 'data',
                    totalProperty: 'total'
                }
            }
        });
        this.on('specialkey', function(f, e){
            if(e.getKey() == e.ENTER){
                this.onTrigger2Click();
            }
        }, this);
        this.on('click', function(f, e){
            this.onClick();
        }, this);
        this.callParent(arguments);
    },
    
    afterRender: function(){
        this.callParent();
        this.triggerCell.item(0).setDisplayed(false);
    },
    
    onClick: function() {
        var me = this;
        me.showGrid();
    },
    
    onTriggerClick: function() {
        var me = this;
        me.showGrid();
    },
    
    showGrid: function() {
        var me = this;
        var store_farmer = Ext.create('Ext.data.Store', {
            fields: ['FarmerID', 'FarmerName', 'VillageID', 'Village', 'SubDistrict', 'District'],
            autoLoad: true,
            pageSize: 10,
            proxy: {
                type: 'ajax',
                url: m_api + "/member/coop_farmers",
                reader: {
                    type: 'json',
                    root: 'data',
                    totalProperty: 'total'
                }
            }
        });
       
        var grid = Ext.create('Ext.grid.Panel', {
            store: store_farmer,
            style: 'border:1px solid #CCC;',
            width: '100%',
            minHeight: 350,
            loadMask: true,
            selType: 'rowmodel',
            dockedItems: [{
                xtype: 'pagingtoolbar',
                store: store_farmer, // same store GridPanel is using
                dock: 'bottom',
                displayInfo: true
            }, {
                xtype: 'toolbar',
                items: [{
                    xtype: 'textfield',
                    fieldLabel: lang('Key'),
                    name: 'farmerKey',
                    id: 'farmerKey'
                }, {
                    xtype: 'button',
                    margin: '0px 0px 0px 6px',
                    text: 'Search',
                    handler: function() {
                        store_farmer.load({
                            params: {
                                key: Ext.getCmp('farmerKey').getValue()
                            }
                        });
                    }
                }]
            }],
            columns: [{
                text: 'No',
                xtype: 'rownumberer',
                align: 'center',
                width: '5%'
            }, {
                text: 'Farmer ID',
                width: '15%',
                dataIndex: 'FarmerID'
            }, {
                text: 'Name',
                width: '25%',
                dataIndex: 'FarmerName'
            }, {
                text: 'District',
                width: '15%',
                dataIndex: 'District'
            }, {
                text: 'SubDistrict',
                width: '15%',
                dataIndex: 'SubDistrict'
            }, {
                text: 'Village',
                width: '15%',
                dataIndex: 'Village'
            }, {
                menuDisabled: true,
                sortable: false,
                xtype: 'actioncolumn',
                width: '7%',
                align: 'center',
                items: [{
                    icon: m_url + '/images/icons/silk/page_white_edit.png',
                    tooltip: lang('Copy'),
                    handler: function(grid, rowIndex, colIndex) {
                        var rec = grid.getStore().getAt(rowIndex);
                        var farmerID = rec.get('farmerID');
                        var farmerName = rec.get('farmerName');
                        
                        me.setValue(farmerID);
                        me.setRawValue(farmerName);
                    }
                }]
            }]
        });
        
        Ext.create('Ext.window.Window', {
            title: 'Hello',
            width: 800,
            height: 500,
            layout: 'fit',
            items:[grid]
        }).show();
    }
});