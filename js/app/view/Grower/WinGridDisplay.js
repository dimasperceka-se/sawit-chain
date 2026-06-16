/*
* @Author: nikolius
* @Date:   2017-05-17 13:15:55
* @Last Modified by:   nikolius
* @Last Modified time: 2017-09-29 15:59:34
*/
Ext.define('Koltiva.view.Grower.WinGridDisplay' ,{
    extend: 'Ext.window.Window',
    id: 'Koltiva.view.Grower.WinGridDisplay',
    title: lang('Field to Display'),
    closable: true,
    modal: true,
    closeAction: 'destroy',
    width: '40%',
    height: '55%',
    overflowY: 'auto',
    initComponent: function() {
        var thisObj = this;

        //store yg dipakai
        var combo_store_field_display = Ext.create('Koltiva.store.Grower.CmbGridFieldDisplay');

        //isi formnya
        thisObj.items = [{
            layout: 'column',
            border: false,
            padding:'5 20 5 5',
            items:[{
                columnWidth: 1,
                layout:{
                    type:'vbox',
                    align:'stretch'
                },
                items:[{
                    xtype: 'itemselector',
                    flex:true,
                    id: 'Koltiva.view.Grower.WinGridDisplay-fieldDisplay',
                    name: 'Koltiva.view.Grower.WinGridDisplay-fieldDisplay',
                    fieldLabel: '',
                    fromTitle: lang('Available Fields'),
                    toTitle: lang('Visible Fields'),
                    anchor: '100%',
                    store: combo_store_field_display,
                    displayField: 'label',
                    valueField: 'id',
                    value: [],
                    allowBlank: false,
                    msgTarget: 'side'
                }]
            }]
        }]

        this.callParent(arguments);
    },
    buttons: [{
        text: lang('Save'),
        icon: varjs.config.base_url + 'images/icons/new/save.png',
        cls: 'Sfr_BtnFormBlue',
        overCls: 'Sfr_BtnFormBlue-Hover',
        handler: function() {
            Ext.MessageBox.show({
                msg: 'Please wait...',
                progressText: 'Displaying...',
                width: 300,
                wait: true,
                waitConfig: {
                    interval: 200
                },
                animateTarget: 'mb9',
                icon: 'ext-mb-info'
            });

            //set ke hidden dl semuanya
            Ext.getCmp('view.Grower.GridMainGrower-colid').setVisible(false);
            Ext.getCmp('view.Grower.GridMainGrower-colFarmerName').setVisible(false);
            Ext.getCmp('view.Grower.GridMainGrower-colBirthdate').setVisible(false);
            Ext.getCmp('view.Grower.GridMainGrower-colAge').setVisible(false);
            Ext.getCmp('view.Grower.GridMainGrower-colHandphone').setVisible(false);
            Ext.getCmp('view.Grower.GridMainGrower-colMaritalStatus').setVisible(false);
            Ext.getCmp('view.Grower.GridMainGrower-colDateCollection').setVisible(false);
            Ext.getCmp('view.Grower.GridMainGrower-colDateCreated').setVisible(false);
            Ext.getCmp('view.Grower.GridMainGrower-colProvince').setVisible(false);
            Ext.getCmp('view.Grower.GridMainGrower-colDistrict').setVisible(false);
            Ext.getCmp('view.Grower.GridMainGrower-colDesa').setVisible(false);
            Ext.getCmp('view.Grower.GridMainGrower-colKecamatan').setVisible(false);
            Ext.getCmp('view.Grower.GridMainGrower-colNrOfPlantation').setVisible(false);
            Ext.getCmp('view.Grower.GridMainGrower-colTotalHectare').setVisible(false);
            Ext.getCmp('view.Grower.GridMainGrower-colTotalHectarePolygon').setVisible(false);
            Ext.getCmp('view.Grower.GridMainGrower-colEnumerator').setVisible(false);

            var visibleField = Ext.getCmp('Koltiva.view.Grower.WinGridDisplay-fieldDisplay').getValue();
            if(visibleField.length > 0){
                for (var i = 0; i < visibleField.length; i++) {
                    Ext.getCmp(visibleField[i]).setVisible(true);
                }
            }

            //tutup popup
            Ext.getCmp('Koltiva.view.Grower.WinGridDisplay').close();
            Ext.MessageBox.hide();
        }
    },{
        text: lang('Close'),
        icon: varjs.config.base_url + 'images/icons/new/close.png',
        cls: 'Sfr_BtnFormGrey',
        overCls: 'Sfr_BtnFormGrey-Hover',
        handler: function() {
            Ext.getCmp('Koltiva.view.Grower.WinGridDisplay').close();
        }
    }],
    listeners: {
        afterRender: function(component, eOpts){
            //set nilainya detek dari yg visible saja
            var arrDisplay = [];

            if(Ext.getCmp('view.Grower.GridMainGrower-colid').isVisible() == true) arrDisplay.push('view.Grower.GridMainGrower-colid');
            if(Ext.getCmp('view.Grower.GridMainGrower-colFarmerName').isVisible() == true) arrDisplay.push('view.Grower.GridMainGrower-colFarmerName');
            if(Ext.getCmp('view.Grower.GridMainGrower-colBirthdate').isVisible() == true) arrDisplay.push('view.Grower.GridMainGrower-colBirthdate');
            if(Ext.getCmp('view.Grower.GridMainGrower-colAge').isVisible() == true) arrDisplay.push('view.Grower.GridMainGrower-colAge');
            if(Ext.getCmp('view.Grower.GridMainGrower-colHandphone').isVisible() == true) arrDisplay.push('view.Grower.GridMainGrower-colHandphone');
            if(Ext.getCmp('view.Grower.GridMainGrower-colMaritalStatus').isVisible() == true) arrDisplay.push('view.Grower.GridMainGrower-colMaritalStatus');
            if(Ext.getCmp('view.Grower.GridMainGrower-colDateCollection').isVisible() == true) arrDisplay.push('view.Grower.GridMainGrower-colDateCollection');
            if(Ext.getCmp('view.Grower.GridMainGrower-colDateCreated').isVisible() == true) arrDisplay.push('view.Grower.GridMainGrower-colDateCreated');
            if(Ext.getCmp('view.Grower.GridMainGrower-colProvince').isVisible() == true) arrDisplay.push('view.Grower.GridMainGrower-colProvince');
            if(Ext.getCmp('view.Grower.GridMainGrower-colDistrict').isVisible() == true) arrDisplay.push('view.Grower.GridMainGrower-colDistrict');
            if(Ext.getCmp('view.Grower.GridMainGrower-colDesa').isVisible() == true) arrDisplay.push('view.Grower.GridMainGrower-colDesa');
            if(Ext.getCmp('view.Grower.GridMainGrower-colKecamatan').isVisible() == true) arrDisplay.push('view.Grower.GridMainGrower-colKecamatan');
            if(Ext.getCmp('view.Grower.GridMainGrower-colNrOfPlantation').isVisible() == true) arrDisplay.push('view.Grower.GridMainGrower-colNrOfPlantation');
            if(Ext.getCmp('view.Grower.GridMainGrower-colTotalHectare').isVisible() == true) arrDisplay.push('view.Grower.GridMainGrower-colTotalHectare');
            if(Ext.getCmp('view.Grower.GridMainGrower-colTotalHectarePolygon').isVisible() == true) arrDisplay.push('view.Grower.GridMainGrower-colTotalHectarePolygon');
            if(Ext.getCmp('view.Grower.GridMainGrower-colEnumerator').isVisible() == true) arrDisplay.push('view.Grower.GridMainGrower-colEnumerator');

            //set nilainya
            Ext.getCmp('Koltiva.view.Grower.WinGridDisplay-fieldDisplay').setValue(arrDisplay);
        }
    }
});