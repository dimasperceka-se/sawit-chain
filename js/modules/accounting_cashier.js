Ext.onReady(function() {
    Ext.tip.QuickTipManager.init();
    
    function displayFormWindow() {
        if (!win.isVisible()) {
//            resetForm();
            DataForm.getForm().reset();
            win.show();
        } else {
            win.hide(this, function() {
            });
            win.toFront();
        }
    }

    function submitOnEnter(field, event) {
        if (event.getKey() == event.ENTER) {
            store.load({
                params: {
                    key: Ext.getCmp('key').getValue(),
                    status: Ext.getCmp('filterStatus').getValue()
                }});
        }
    }

    function generateSavingType() {
        Ext.Ajax.request({
            url: m_crud + '_savingtype',
            method: 'GET',
            params: {id: Ext.getCmp('savingTypeID').getValue()},
            success: function(fp, o) {
                var r = Ext.decode(fp.responseText);
                Ext.getCmp('savingTypeInterestRate').setValue(r.savingTypeInterestRate);
                Ext.getCmp('savingTypeMinAmount').setValue(r.savingTypeMinAmount);
                Ext.getCmp('savingTypeMinTrans').setValue(r.savingTypeMinTrans);
            }
        });
    }

    function generateMemberData() {
        Ext.Ajax.request({
            url: m_crud + '_member',
            method: 'GET',
            params: {id: Ext.getCmp('memberID').getValue()},
            success: function(fp, o) {
                var r = Ext.decode(fp.responseText);
                Ext.getCmp('name').setValue(r.name);
                Ext.getCmp('address').setValue(r.address);
            }
        });
    }
    
    var form_teller = Ext.create('Ext.Container',{
        width: '100%',
        height: '100%',
        renderTo:'ext-content',
        listeners:{
            'render':function(){
               Ext.Ajax.request({
                    waitMsg: 'Please Wait',
                    url: m_api+'/transaction/actualbalance',
                    method: 'GET',
                    params: {
                        userid: Ext.getCmp('useridCashCount').getValue(),
                        date: Ext.getCmp('dateCashCount').getSubmitValue()
                    },
                    success: function(response, opts) {
                        var r = Ext.decode(response.responseText);
                        // console.log(r);
                        Ext.getCmp('ActualBalance').setValue(number_format2(r.total));

                    },
                    failure: function(response, opts) {

                    }
                });
            }
        },
        items:[
            {
                xtype:'container',
                id:'pnl-card-coop-transaction',
                layout:{
                    type:'card'
                },
                items:[
                    {
                        xtype:'panel',
                        frame:true,
                        hidden:true,
                        id:'pnl-add-deposit',
                        style:'border:6px solid #799143',
                        bodyStyle:'background:#799143;',
                        header:{
                            style:'background:#799143;border-color:#799143;text-align:center; font-size:25px'
                        },
                        title:'C A S H - C O U N T',
                        items:[
                            Ext.create('Ext.form.Panel', {
                                bodyPadding: 5,
                                id: 'frm-add-deposit',
                                fieldDefaults: {
                                    labelAlign: 'left',
                                    labelWidth: 120
                                },
                                layout: {
                                    type:'column'
                                },
                                getInvalidFields: function() {
                                    var invalidFields = [];
                                    Ext.suspendLayouts();
                                    this.form.getFields().filterBy(function(field) {
                                        if (field.validate()) return;
                                        invalidFields.push(field);
                                    });
                                    Ext.resumeLayouts(true);
                                    return invalidFields;
                                },
                                items: [
                                {
                                    xtype:'hiddenfield',
                                    value:m_userid,
                                    name:'userid',
                                    id:'useridCashCount'
                                },
                                    {
                                        xtype:'panel',
                                        columnWidth: .5,
                                        layout:{
                                            type:'fit'
                                        },
                                        // height:350,
                                        items:[
                                            {
                                                xtype:'fieldset',
                                                width:400,
                                                title:'Cash Box',
                                                style:'padding-bottom:2px',
                                                items:[
                                                    {
                                                        xtype:'displayfield',
                                                        id:'nameCashier',
                                                        fieldLabel:'Cashier Name',
                                                        // allowBlank:false,
                                                        value:m_realname,
                                                        width:400,
                                                        name:'name'
                                                    },
                                                    {
                                                        xtype:'numericfield',
                                                        id:'cashInHand',
                                                        hidden:true,
                                                        fieldLabel:'Cash In Hand',
                                                        // allowBlank:false,
                                                        // width:400,
                                                        hideTrigger:true,
                                                        name:'name',
                                                        listeners: {
                                                            'render': function (c) {
                                                                c.getEl().on('keyup', function () {
                                                                    var cashInHand = Ext.getCmp('cashInHand').getValue();
                                                                    var ActualBalanceCmp = Ext.getCmp('ActualBalance').getValue();
                                                                    var ActualBalance = ActualBalanceCmp.replace(',','');
                                                                    Ext.getCmp('balanceCashCount').setValue(ActualBalance*1-cashInHand*1);


                                                                    // setNilaiEmEp();
                                                                }, c);
                                                            }
                                                        }
                                                    },
                                                    /////////////
                                                    {
                                                        xtype: 'fieldcontainer',
                                                        fieldLabel: "Lembar 100.000",
                                                        layout: 'hbox',
                                                        items: [{
                                                                xtype: 'displayfield',
                                                                value: ' x '
                                                            }, {
                                                                xtype: 'numberfield',
                                                                hideTrigger:true,
                                                                margin: '0 0 0 50',
                                                                width:70,
                                                                // allowBlank: false,
                                                                id: 'Lembar100',
                                                                listeners: {
                                                                    'change':function(){
                                                                        hitungCashBox();
                                                                    },
                                                                    'render': function (c) {
                                                                        c.getEl().on('keyup', function () {
                                                                             hitungCashBox();
                                                                        }, c);
                                                                    }
                                                                }
                                                        }]
                                                    },
                                                    {
                                                        xtype: 'fieldcontainer',
                                                        fieldLabel: "Lembar 50.000",
                                                        layout: 'hbox',
                                                        items: [{
                                                                xtype: 'displayfield',
                                                                value: ' x '
                                                            }, {
                                                                xtype: 'numberfield',
                                                                hideTrigger:true,
                                                                margin: '0 0 0 50',
                                                                width:70,
                                                                // allowBlank: false,
                                                                id: 'Lembar50',
                                                                listeners: {
                                                                    'change':function(){
                                                                        hitungCashBox();
                                                                    },
                                                                    'render': function (c) {
                                                                        c.getEl().on('keyup', function () {
                                                                             hitungCashBox();
                                                                        }, c);
                                                                    }
                                                                }

                                                        }]
                                                    },
                                                    {
                                                        xtype: 'fieldcontainer',
                                                        fieldLabel: "Lembar 20.000",
                                                        layout: 'hbox',
                                                        items: [{
                                                                xtype: 'displayfield',
                                                                value: ' x '
                                                            }, {
                                                                xtype: 'numberfield',
                                                                hideTrigger:true,
                                                               margin: '0 0 0 50',
                                                                width:70,
                                                                // allowBlank: false,
                                                                id: 'Lembar20',
                                                                listeners: {
                                                                    'change':function(){
                                                                        hitungCashBox();
                                                                    },
                                                                    'render': function (c) {
                                                                        c.getEl().on('keyup', function () {
                                                                             hitungCashBox();
                                                                        }, c);
                                                                    }
                                                                }
                                                        }]
                                                    },
                                                    {
                                                        xtype: 'fieldcontainer',
                                                        fieldLabel: "Lembar 10.000",
                                                        layout: 'hbox',
                                                        items: [{
                                                                xtype: 'displayfield',
                                                                value: ' x '
                                                            }, {
                                                                xtype: 'numberfield',
                                                                hideTrigger:true,
                                                               margin: '0 0 0 50',
                                                                width:70,
                                                                // allowBlank: false,
                                                                id: 'Lembar10',
                                                                listeners: {
                                                                    'change':function(){
                                                                        hitungCashBox();
                                                                    },
                                                                    'render': function (c) {
                                                                        c.getEl().on('keyup', function () {
                                                                             hitungCashBox();
                                                                        }, c);
                                                                    }
                                                                }
                                                        }]
                                                    },
                                                    {
                                                        xtype: 'fieldcontainer',
                                                        fieldLabel: "Lembar 5.000",
                                                        layout: 'hbox',
                                                        items: [{
                                                                xtype: 'displayfield',
                                                                value: ' x '
                                                            }, {
                                                                xtype: 'numberfield',
                                                                hideTrigger:true,
                                                               margin: '0 0 0 50',
                                                                width:70,
                                                                // allowBlank: false,
                                                                id: 'Lembar5',
                                                                listeners: {
                                                                    'change':function(){
                                                                        hitungCashBox();
                                                                    },
                                                                    'render': function (c) {
                                                                        c.getEl().on('keyup', function () {
                                                                             hitungCashBox();
                                                                        }, c);
                                                                    }
                                                                }
                                                        }]
                                                    },
                                                    {
                                                        xtype: 'fieldcontainer',
                                                        fieldLabel: "Lembar 2.000",
                                                        layout: 'hbox',
                                                        items: [{
                                                                xtype: 'displayfield',
                                                                value: ' x '
                                                            }, {
                                                                xtype: 'numberfield',
                                                                hideTrigger:true,
                                                                margin: '0 0 0 50',
                                                                width:70,
                                                                // allowBlank: false,
                                                                id: 'Lembar2',
                                                                listeners: {
                                                                    'change':function(){
                                                                        hitungCashBox();
                                                                    },
                                                                    'render': function (c) {
                                                                        c.getEl().on('keyup', function () {
                                                                             hitungCashBox();
                                                                        }, c);
                                                                    }
                                                                }
                                                        }]
                                                    },
                                                    {
                                                        xtype: 'fieldcontainer',
                                                        hidden:true,
                                                        fieldLabel: "Koin 2.000",
                                                        layout: 'hbox',
                                                        items: [{
                                                                xtype: 'displayfield',
                                                                value: ' x '
                                                            }, {
                                                                xtype: 'numberfield',
                                                                hideTrigger:true,
                                                               margin: '0 0 0 50',
                                                                width:70,
                                                                // allowBlank: false,
                                                                id: 'Koin2',
                                                                listeners: {
                                                                    'change':function(){
                                                                        hitungCashBox();
                                                                    },
                                                                    'render': function (c) {
                                                                        c.getEl().on('keyup', function () {
                                                                             hitungCashBox();
                                                                        }, c);
                                                                    }
                                                                }
                                                        }]
                                                    },
                                                    {
                                                        xtype: 'fieldcontainer',
                                                        fieldLabel: "Lembar 1.000",
                                                        layout: 'hbox',
                                                        items: [{
                                                                xtype: 'displayfield',
                                                                value: ' x '
                                                            }, {
                                                                xtype: 'numberfield',
                                                                hideTrigger:true,
                                                               margin: '0 0 0 50',
                                                                width:70,
                                                                // allowBlank: false,
                                                                id: 'Lembar1',
                                                                listeners: {
                                                                    'change':function(){
                                                                        hitungCashBox();
                                                                    },
                                                                    'render': function (c) {
                                                                        c.getEl().on('keyup', function () {
                                                                             hitungCashBox();
                                                                        }, c);
                                                                    }
                                                                }
                                                        }]
                                                    },
                                                    {
                                                        xtype: 'fieldcontainer',
                                                        fieldLabel: "Koin 1.000",
                                                        layout: 'hbox',
                                                        items: [{
                                                                xtype: 'displayfield',
                                                                value: ' x '
                                                            }, {
                                                                xtype: 'numberfield',
                                                                hideTrigger:true,
                                                                margin: '0 0 0 50',
                                                                width:70,
                                                                // allowBlank: false,
                                                                id: 'Koin1',
                                                                listeners: {
                                                                    'change':function(){
                                                                        hitungCashBox();
                                                                    },
                                                                    'render': function (c) {
                                                                        c.getEl().on('keyup', function () {
                                                                             hitungCashBox();
                                                                        }, c);
                                                                    }
                                                                }
                                                        }]
                                                    },
                                                    {
                                                        xtype: 'fieldcontainer',
                                                        fieldLabel: "Koin 500",
                                                        layout: 'hbox',
                                                        items: [{
                                                                xtype: 'displayfield',
                                                                value: ' x '
                                                            }, {
                                                                xtype: 'numberfield',
                                                                hideTrigger:true,
                                                               margin: '0 0 0 50',
                                                                width:70,
                                                                // allowBlank: false,
                                                                id: 'Koin5rts',
                                                                listeners: {
                                                                    'change':function(){
                                                                        hitungCashBox();
                                                                    },
                                                                    'render': function (c) {
                                                                        c.getEl().on('keyup', function () {
                                                                             hitungCashBox();
                                                                        }, c);
                                                                    }
                                                                }
                                                        }]
                                                    },
                                                    {
                                                        xtype: 'fieldcontainer',
                                                        fieldLabel: "Koin 200",
                                                        layout: 'hbox',
                                                        items: [{
                                                                xtype: 'displayfield',
                                                                value: ' x '
                                                            }, {
                                                                xtype: 'numberfield',
                                                                hideTrigger:true,
                                                               margin: '0 0 0 50',
                                                                width:70,
                                                                // allowBlank: false,
                                                                id: 'Koin2rts',
                                                                listeners: {
                                                                    'change':function(){
                                                                        hitungCashBox();
                                                                    },
                                                                    'render': function (c) {
                                                                        c.getEl().on('keyup', function () {
                                                                             hitungCashBox();
                                                                        }, c);
                                                                    }
                                                                }
                                                        }]
                                                    },
                                                    {
                                                        xtype: 'fieldcontainer',
                                                        fieldLabel: "Koin 100",
                                                        layout: 'hbox',
                                                        items: [{
                                                                xtype: 'displayfield',
                                                                value: ' x '
                                                            }, {
                                                                xtype: 'numberfield',
                                                                hideTrigger:true,
                                                                margin: '0 0 0 50',
                                                                width:70,
                                                                // allowBlank: false,
                                                                id: 'Koin1rts',
                                                                listeners: {
                                                                    'change':function(){
                                                                        hitungCashBox();
                                                                    },
                                                                    'render': function (c) {
                                                                        c.getEl().on('keyup', function () {
                                                                             hitungCashBox();
                                                                        }, c);
                                                                    }
                                                                }
                                                        }]
                                                    },
                                                    {
                                                        xtype: 'fieldcontainer',
                                                        fieldLabel: "Koin 50",
                                                        layout: 'hbox',
                                                        items: [{
                                                                xtype: 'displayfield',
                                                                value: ' x '
                                                            }, {
                                                                xtype: 'numberfield',
                                                                hideTrigger:true,
                                                                margin: '0 0 0 50',
                                                                width:70,
                                                                // allowBlank: false,
                                                                id: 'Koin50',
                                                                listeners: {
                                                                    'change':function(){
                                                                        hitungCashBox();
                                                                    },
                                                                    'render': function (c) {
                                                                        c.getEl().on('keyup', function () {
                                                                             hitungCashBox();
                                                                        }, c);
                                                                    }
                                                                }
                                                        }]
                                                    },
                                                    {
                                                        xtype:'displayfield',
                                                        fieldLabel:'<b>TOTAL</b>',
                                                        fieldStyle:'text-align:right;',
                                                        id:'TotalCashBox',
                                                        // value:12345678,
                                                        allowBlank:false,
                                                        width:250,
                                                        name:'name'
                                                    }
                                                ]
                                            }
                                        ]
                                    },
                                    {
                                        xtype:'fieldset',
                                        title:'Cash Info',
                                        style:'padding-bottom:2px; margin-left:20px;',
                                        items:[
                                             {
                                                    xtype:'datefield',
                                                    format: 'd-m-Y',
                                                    width:230,
                                                    id:'dateCashCount',
                                                    fieldLabel:'Date',
                                                    value: new Date()
                                                },
                                            {
                                                xtype:'displayfield',
                                                fieldLabel:'Money which should be in your cash box',
                                                labelWidth:250,
                                                fieldStyle:'text-align:right;',
                                                id:'ActualBalance',
                                                allowBlank:false,
                                                width:350,
                                                name:'name'
                                            },
                                            {
                                                xtype:'displayfield',
                                                fieldLabel:'Total money in your cash box',
                                                labelWidth:250,
                                                fieldStyle:'text-align:right;',
                                                id:'TotalCashBoxRight',
                                                allowBlank:false,
                                                width:350,
                                                name:'name'
                                            },
                                            {
                                                xtype:'displayfield',
                                                fieldLabel:'Difference',
                                                fieldStyle:'text-align:right;',
                                                id:'balanceCashCount',
                                                allowBlank:false,
                                                width:350,
                                                name:'name'
                                            },
                                            {
                                                xtype:'textarea',
                                                width:350,
                                                id:'remarks',
                                                name:'remarks',
                                                fieldLabel:'Remarks'
                                            }
                                            // {
                                            //     xtype:'displayfield',
                                            //     fieldStyle:'text-align:right;',
                                            //     value:12313,
                                            //     id:'balanceCashCount',
                                            //     fieldLabel:'Balance'
                                            // }
                                        ]
                                    }
                                ],
                                        dockedItems:[
                                            {
                                                xtype:'toolbar',
                                                dock:'bottom',
                                                items: ['->',{
                                                    id: 'saveButtonConfirm',
                                                    text: 'Print',
                                                    margin: '5px',
                                                    padding:'10px',
                                                    scale: 'medium',
                                                    ui: 's-button',
                                                    cls: 's-blue',
                                                    handler: function() {
                                                        var form = Ext.getCmp('frm-add-deposit').getForm();
                                                        if(form.isValid()){
                                                            var nama = Ext.getCmp('nameCashier').getValue();
                                                            window.open(m_base_url+'/accounting/cashier_print/cetak/'+Ext.getCmp('Lembar100').getValue()+'.'+Ext.getCmp('Lembar50').getValue()+'.'+Ext.getCmp('Lembar20').getValue()+'.'+Ext.getCmp('Lembar10').getValue()+'.'+Ext.getCmp('Lembar5').getValue()+'.'+Ext.getCmp('Lembar2').getValue()+'.'+Ext.getCmp('Lembar1').getValue()+'.'+Ext.getCmp('Koin1').getValue()+'.'+Ext.getCmp('Koin5rts').getValue()+'.'+Ext.getCmp('Koin2rts').getValue()+'.'+Ext.getCmp('Koin1rts').getValue()+'.'+Ext.getCmp('Koin50').getValue()+'/'+Ext.getCmp('TotalCashBox').getValue()+'/'+Ext.getCmp('dateCashCount').getSubmitValue()+'/'+Ext.getCmp('ActualBalance').getValue()+'/'+Ext.getCmp('TotalCashBoxRight').getValue()+'/'+Ext.getCmp('balanceCashCount').getValue()+'/'+Ext.getCmp('remarks').getValue()+'/'+ window.btoa(nama),'_blank')
                                                            // window.open("http://philmontscoutranch.org/Camping/75.aspx", "_blank");
                                                            // form.submit({
                                                            //     url: m_api + '/transaction/add_deposit_recbook',
                                                            //     method:'POST',
                                                            //     waitMsg: 'Sending data...',
                                                            //     success: function(fp, o) {
                                                            //         Ext.MessageBox.alert('Success', 'Data saved.');
                                                            //         form.reset();
                                                            //         Ext.getCmp('grid-add-deposit-saving-trans').store.load();
                                                            //     }
                                                            // });
                                                        } else {
                                                            var fieldNames = [];                
                                                            var fields = Ext.getCmp('frm-add-deposit').getInvalidFields();
                                                            for(var i=0; i <  fields.length; i++){
                                                                var field = fields[i];
                                                                fieldNames.push(field.getName());
                                                            }
                                                            Ext.MessageBox.alert('Invalid Fields', 'The following fields are invalid: ' + fieldNames.join(', '));
                                                        }
                                                    }
                                                }]
                                            }
                                        ]
                            })
                        ]
                    }
                ]
            }
        ]
    });

    
});

function hitungCashBox()
{
    var Lembar100 = 100000 * Ext.getCmp('Lembar100').getValue()*1;
    var Lembar50 = 50000 * Ext.getCmp('Lembar50').getValue()*1;
    var Lembar20 = 20000 * Ext.getCmp('Lembar20').getValue()*1;
    var Lembar10 = 10000 * Ext.getCmp('Lembar10').getValue()*1;
    var Lembar5 = 5000 * Ext.getCmp('Lembar5').getValue()*1;
    var Lembar2 = 2000 * Ext.getCmp('Lembar2').getValue()*1;
    var Koin2 = 2000 * Ext.getCmp('Koin2').getValue()*1;
    var Lembar1 = 1000 * Ext.getCmp('Lembar1').getValue()*1;
    var Koin1 = 1000 * Ext.getCmp('Koin1').getValue()*1;
    var Koin5rts = 500 * Ext.getCmp('Koin5rts').getValue()*1;
    var Koin2rts = 200 * Ext.getCmp('Koin2rts').getValue()*1;
    var Koin1rts = 100 * Ext.getCmp('Koin1rts').getValue()*1;
    var Koin50 = 50 * Ext.getCmp('Koin50').getValue()*1;
    var total = Lembar100+Lembar50+Lembar20+Lembar10+Lembar5+Lembar2+Koin2+Lembar1+Koin1+Koin5rts+Koin2rts+Koin1rts+Koin50;
    Ext.getCmp('TotalCashBox').setValue(number_format2(total));
    Ext.getCmp('TotalCashBoxRight').setValue(number_format2(total));

    var ActualBalanceCmp = Ext.getCmp('ActualBalance').getValue();
    if(ActualBalanceCmp==0)
    {
        var ActualBalance = 0;
    } else {
        var ActualBalance = ActualBalanceCmp.split('.').join('');
    }
    
    console.log(total+' '+ActualBalance)
    var diff = total*1-ActualBalance*1;
    if(diff*1!=0)
    {
        Ext.getCmp('saveButtonConfirm').setDisabled(true);
    } else {
        Ext.getCmp('saveButtonConfirm').setDisabled(false);
    }
    Ext.getCmp('balanceCashCount').setValue(number_format2(diff));
}

function number_format2(number, decimals, dec_point, thousands_sep) {
  number = (number + '').replace(/[^0-9+\-Ee.]/g, '');
  var n = !isFinite(+number) ? 0 : +number,
    prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
    sep = (typeof thousands_sep === 'undefined') ? '.' : thousands_sep,
    dec = (typeof dec_point === 'undefined') ? '.' : dec_point,
    s = '',
    toFixedFix = function (n, prec) {
      var k = Math.pow(10, prec);
      return '' + Math.round(n * k) / k;
    };
  // Fix for IE parseFloat(0.55).toFixed(0) = 0;
  s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
  if (s[0].length > 3) {
    s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
  }
  if ((s[1] || '').length < prec) {
    s[1] = s[1] || '';
    s[1] += new Array(prec - s[1].length + 1).join('0');
  }
  return s.join(dec);
}

