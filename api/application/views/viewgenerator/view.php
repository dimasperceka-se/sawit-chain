
/**
* 
* @author AlfathDirk
* Created on 2016
* This File Auto Generated
*
*/

Ext.define('Koltiva.view.Generator.<?php echo str_replace(' ', '',$programName) ?>', {
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


<?php foreach ($data as $sectionLabel => $value) : ?>
        {
          xtype: 'panel',
          autoScroll: true,
          title: '<?php  echo $sectionLabel;  ?>', //echo id
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

<?php for ($i=0; $i < count($value) ; $i++) : ?>    
              {

<?php switch ($value[$i]['xtype']) {
  case "LONG_TEXT":
    $type = 'textarea';
    break;
  case "FILE_RESOURCE":
    $type = 'fileuploadfield';
    break;
  case "DATE":
    $type = 'datefield';
    break;
  default:
    $type = 'textfield';
    break;
} ?>
                xtype: <?php echo $type ?>,
                labelAlign: 'top',
                fieldLabel: '<?php echo $value[$i]['elementName'];  ?>',
                name: 'taroName',
                allowBlank: false
              },
<?php endfor; ?>

              ]
            }]
          }],
        },

 <?php endforeach; ?>
        //end


        ]
      }];

    }
    this.callParent(arguments);
  }
});

