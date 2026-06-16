Ext.define('Koltiva.view.UserAffiliate.AffiliateWindow' ,{
    extend: 'Ext.window.Window',
    id: 'Koltiva.view.UserAffiliate.AffiliateWindow',
    title: lang('User'),
    closable: true,
    modal: true,
    closeAction: 'destroy',
    width: '88%',
    height: '86%',
    overflowY: 'auto',
    style:'padding:2px;',
    viewVar: false,
    setViewVar: function(value){
        this.viewVar = value;
    },
    initComponent: function() {
        var thisObj = this;

        thisObj.MainGrid = Ext.create('Koltiva.store.UserAffiliate.AffiliateWindow',{
        	storeVar: {
                UserId: thisObj.viewVar.UserId,
                key: null
            }
        });

        thisObj.items = [{
            xtype: 'gridpanel',
            id: 'Koltiva.view.UserAffiliate.AffiliateWindow-MainGrid',
            style: 'border:1px solid #CCC;',
            store: thisObj.MainGrid,
            width: '100%',
            loadMask: true,
            selType: 'checkboxmodel',
            viewConfig: {
                deferEmptyText: false,
                emptyText: lang('No Data Available')
            },
            dockedItems: [{
                xtype: 'pagingtoolbar',
                store: thisObj.MainGrid,
                dock: 'bottom',
                displayInfo: true,
                style:'padding-right:12px;'
            },{
                xtype: 'toolbar',
                items: [{
                    name: 'Koltiva.view.UserAffiliate.AffiliateWindow-SearchName',
                    id: 'Koltiva.view.UserAffiliate.AffiliateWindow-SearchName',
                    xtype: 'textfield',
                    width: 200,
                    emptyText: lang('Real Name/Username')
                },{
                    xtype: 'button',
                    icon: varjs.config.base_url + 'images/icons/silk/search.png',
                    margin: '0px 0px 0px 6px',
                    text: lang('Search'),
                    handler: function() {
                        thisObj.MainGrid.storeVar.key = Ext.getCmp('Koltiva.view.UserAffiliate.AffiliateWindow-SearchName').getValue();
                        thisObj.MainGrid.storeVar.UserId = thisObj.viewVar.UserId;

                        thisObj.MainGrid.load();
                    }
                }]
            }],
            columns: [{
                dataIndex: 'UserId',
                hidden: true
            },{
                HeaderCheckbox: true,
                dataIndex : 'UserId',
                flex: 0.05,
                renderer: function(){
                    return '';
                }
            },{
                text: lang('Real Name'),
                dataIndex: 'UserRealName',
                flex: 3
            },{
                text: lang('Username'),
                dataIndex: 'UserName',
                flex: 1
            },{
                text: lang('Active'),
                dataIndex: 'UserActive',
                flex: 2
            }]
        }]

        thisObj.buttons = [{
            text: 'Save',
            margin: '5 15 5 5',
            scale: 'large',
            ui: 's-button',
            cls: 's-blue',
            handler: function () {
                var gridSelected = Ext.getCmp('Koltiva.view.UserAffiliate.AffiliateWindow-MainGrid').getSelectionModel().getSelection();

                var IdSelectedArr = [];
                for (var i = gridSelected.length - 1; i >= 0; i--) {
                    IdSelectedArr.push(gridSelected[i].get('UserId'));
                }

                if(IdSelectedArr.length > 0){
                    thisObj.viewVar.Caller.AddAffiliates(IdSelectedArr);
                    thisObj.close();
                }else{
                    Ext.MessageBox.show({
                        title: 'Information',
                        msg: lang('No data selected'),
                        buttons: Ext.MessageBox.OK,
                        animateTarget: 'mb9',
                        icon: 'ext-mb-info'
                    });
                }
            }
        },{
            text: lang('Close'),
            margin: '5px',
            scale: 'large',
            ui: 's-button',
            cls: 's-grey',
            handler: function() {
                thisObj.close();
            }
        }];

        this.callParent(arguments);
    }
});