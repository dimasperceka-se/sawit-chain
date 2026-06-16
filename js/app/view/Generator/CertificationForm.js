
/**
* 
* @author AlfathDirk
* Created on 2016
* This File Auto Generated
*
*/

Ext.define('Koltiva.view.Generator.CertificationForm', {
  extend: 'Ext.form.Panel',
  grid: false,
  layout: {
    type: 'fit',
    columns: 1
  },
  initComponent: function() {

    var _that = this;

    if (_that.grid) {
      _that.items = [{
        xtype: 'tabpanel',
        flex: 1,
        margin: 2,
        activeTab: 0,
        plain: true,
        cls: 'tabSce',
        id: 'sectionTab',
        listeners: {
          'tabchange': function(tabPanel, tab) {

          }
        },
        items: [


        {
          xtype: 'panel',
          autoScroll: true,
          title: 'Certification', //echo id
          padding: 5,
          style: 'border:2px solid #D6EDA4',
          items: [{
            layout: 'column',
            border: false,
            items: [{
              columnWidth: .5,
              layout: 'form',
              padding: 3,
              border: false,
              items: [

    
              {
                xtype: 'textfield',
                labelAlign: 'top',
                fieldLabel: 'Farmer Name',
                name: 'taroName',
                allowBlank: false
              },
    
              {
                xtype: 'textfield',
                labelAlign: 'top',
                fieldLabel: 'FarmerID',
                name: 'taroName',
                allowBlank: false
              },
    
              {
                xtype: 'textfield',
                labelAlign: 'top',
                fieldLabel: 'GardenNr',
                name: 'taroName',
                allowBlank: false
              },
    
              {
                xtype: 'textfield',
                labelAlign: 'top',
                fieldLabel: 'SurveyNr',
                name: 'taroName',
                allowBlank: false
              },
    
              {
                xtype: 'textfield',
                labelAlign: 'top',
                fieldLabel: 'Candidate Selection',
                name: 'taroName',
                allowBlank: false
              },
    
              {
                xtype: 'textfield',
                labelAlign: 'top',
                fieldLabel: 'Certification Start',
                name: 'taroName',
                allowBlank: false
              },
    
              {
                xtype: 'textfield',
                labelAlign: 'top',
                fieldLabel: 'Certification End',
                name: 'taroName',
                allowBlank: false
              },
    
              {
                xtype: 'textfield',
                labelAlign: 'top',
                fieldLabel: 'Certification Year',
                name: 'taroName',
                allowBlank: false
              },
    
              {
                xtype: 'textfield',
                labelAlign: 'top',
                fieldLabel: 'Type of Certification',
                name: 'taroName',
                allowBlank: false
              },
    
              {
                xtype: 'textfield',
                labelAlign: 'top',
                fieldLabel: 'Signature',
                name: 'taroName',
                allowBlank: false
              },
    
              {
                xtype: 'textfield',
                labelAlign: 'top',
                fieldLabel: 'External Date',
                name: 'taroName',
                allowBlank: false
              },

              ]
            }]
          }],
        },

         //end


        ]
      }];

    }
    this.callParent(arguments);
  }
});

