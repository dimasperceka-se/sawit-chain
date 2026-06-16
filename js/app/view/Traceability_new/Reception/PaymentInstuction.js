Ext.define('Koltiva.view.Traceability_new.Reception.PaymentInstuction' ,{
    extend: 'Ext.window.Window',
    id: 'Koltiva.view.Traceability_new.Reception.PaymentInstuction',
    title: lang('Payment Instruction'),
    frame: false,
    closable: true,
    modal: true,
    closeAction: 'destroy',
    width: '60%',
    minWidth: 350,
    height: 500,
    maxHeight: 400,
    layout: 'fit',
    opsiDisplay: false,
    style:'padding:margin:12px 0 0 0;',
    setOpsiDisplay: function(value){
        this.opsiDisplay = value;
    },
    formVar: false,
    setFormVar: function(value){
        this.formVar = value;
    },
    renderTo: 'ext-content',
    initComponent: function() {
        var thisObj = this;
        thisObj.PrePaymentID = thisObj.viewVar.PrePaymentID;

        //ajax kelalen

        $(document).on('click', '.btn_copy_va', function() {
             var nomor_va = $('#nomor_va_ori').val();
             var aux = document.createElement("div");
             aux.setAttribute("contentEditable", true);
             aux.innerHTML = nomor_va;
             aux.setAttribute("onfocus", "document.execCommand('selectAll',false,null)"); 
             document.body.appendChild(aux);
             aux.focus();
             document.execCommand("copy");
             document.body.removeChild(aux);
             Ext.MessageBox.show({
                title: 'Information',
                msg: "Copied: " + nomor_va,
                buttons: Ext.MessageBox.OK,
                animateTarget: 'mb9',
                icon: 'ext-mb-success',
            });
        });

        $(document).on('click', '.btn_copy_va_name', function() {
            var va_name = $('#va_name_ori').val();
            var aux = document.createElement("div");
            aux.setAttribute("contentEditable", true);
            aux.innerHTML = va_name;
            aux.setAttribute("onfocus", "document.execCommand('selectAll',false,null)"); 
            document.body.appendChild(aux);
            aux.focus();
            document.execCommand("copy");
            document.body.removeChild(aux);
            Ext.MessageBox.show({
               title: 'Information',
               msg: "Copied: " + va_name,
               buttons: Ext.MessageBox.OK,
               animateTarget: 'mb9',
               icon: 'ext-mb-success',
               
           });
       });

        $(document).on('click', '.btn_copy_amount', function() {
            var amount = $('#amount_ori').val();
            var aux = document.createElement("div");
            aux.setAttribute("contentEditable", true);
            aux.innerHTML = amount;
            aux.setAttribute("onfocus", "document.execCommand('selectAll',false,null)"); 
            document.body.appendChild(aux);
            aux.focus();
            document.execCommand("copy");
            document.body.removeChild(aux);
            Ext.MessageBox.show({
                title: 'Information',
                msg: "Copied: " + amount,
                buttons: Ext.MessageBox.OK,
                animateTarget: 'mb9',
                icon: 'ext-mb-success',
                
            });
          });
        

        thisObj.items = [
            {
            xtype: 'form',
            frame: true,
            id: 'Koltiva.view.Traceability_new.Reception.PaymentInstuction-form',
            frame: false,
            width: '60%',
            height: 450,
            autoScroll:true,
            bodyPadding: 10,
            fieldDefaults: {
                labelAlign: 'left',
                padding: 10
            },
            items: [
                {
                    xtype: 'panel',
                    autoScroll: true,
                    items: [
                        {
                            xtype: 'panel',
                            width: '100%',
                            html: '<div class="main-content" >'
                                + '<div class="row">'
                    
                                + '<div class="col-md-12">'
                                    + '<div style="border-bottom:1px solid #cccc;padding-bottom:3px">'
                                        
                                        +'<table width="100%"><tr><td>'
                                            + '<div style="font-weight:bold;font-size:large" id="PaymentVia"></div>'
                                        +'</td><td align="right">'
                                            +'<div id="PaymentViaLogo"></div>'
                                        +'</td></tr></table>'
                                        +'<br><br>'
                                    + '</div>'
                                + '</div>'

                                + '</div>'
                                + '</div>'
                        },
                        {
                            xtype: 'panel',
                            width: '100%',
                            html: '<div class="main-content" >'
                                + '<div class="row">'

                                
                                + '<div class="col-md-12">'
                                    + '<div style="border-bottom:1px solid #cccc;padding-bottom:3px">'
                                        + '<div class="desc">' + lang('Transaction ID') + '</div>'
                                        + '<div style="padding-top:7px;font-weight:bold;" class="value" id="nomor_po">0</div>'
                                    + '</div>'
                                + '</div>'

                                
                                + '<div id="PanelVirtualAccount" class="col-md-12" style="display:none">'
                                    + '<div style="border-bottom:1px solid #cccc;padding-bottom:3px;padding-top:3px">'
                                        +'<table width="100%"><tr><td>'
                                            + '<div class="desc">' + lang('Virtual Account Number') + '</div>'
                                            + '<div style="padding-top:7px;font-weight:bold;" class="value" id="nomor_va">0</div>'
                                            + '<input type="hidden" id="nomor_va_ori">'
                                        +'</td><td align="right">'
                                            +'<div class="btn_copy_va" style="color:#589C14;cursor:copy"><b>Copy</b></div>'
                                        +'</td></tr></table>'
                                    + '</div>'
                                + '</div>'

                                + '<div class="col-md-12">'
                                    + '<div style="border-bottom:1px solid #cccc;padding-bottom:3px;padding-top:3px">'
                                        +'<table width="100%"><tr><td>'
                                            + '<div class="desc">' + lang('Virtual Account Name') + '</div>'
                                            + '<div style="padding-top:7px;font-weight:bold;" class="value" id="va_name">0</div>'
                                            + '<input type="hidden" id="va_name_ori">'
                                        +'</td><td align="right">'
                                            +'<div class="btn_copy_va_name" style="color:#589C14;cursor:copy"><b>Copy</b></div>'
                                        +'</td></tr></table>'
                                    + '</div>'
                                + '</div>'

                                + '<div class="col-md-12">'
                                    + '<div style="border-bottom:1px solid #cccc;padding-bottom:3px;padding-top:3px">'
                                       
                                        +'<table width="100%"><tr><td>'
                                            + '<div class="desc">' + lang('Transaction Amount') + '</div>'
                                            + '<div style="padding-top:7px;font-weight:bold;font-size:large;color:#32d412" class="value" id="TotalTagihan">0</div>'
                                            + '<input type="hidden" id="amount_ori">'
                                        +'</td><td align="right">'
                                            +'<div class="btn_copy_amount" style="color:#589C14;cursor:copy"><b>Copy</b></div>'
                                        +'</td></tr></table>'
                                    + '</div>'
                                + '</div>'
                                 
                                + '</div>'
                                + '</div>'
                        },
                        {
                            xtype: 'panel',
                            title: lang("Payment Instruction"),
                            width: '100%',
                            hidden:true,
                            id:"PanelCP0",
                            height:'auto',
                            html: '<div class="main-content" >'
                                + '<div class="row">'

                                + '<div class="col-md-12">'
                                    + '<div style="border-bottom:1px solid #cccc;padding-bottom:3px">'
                                        + '<div style="font-weight:bold" id="AccordTitle0"></div>'
                                        + '<div style="padding-top:7px;" class="value" id="Accord0"></div>'
                                    + '</div>'
                                + '</div>'

                                + '</div>'
                                + '</div>'
                        },
                        {
                            xtype: 'panel',
                            width: '100%',
                            id:"PanelCP1",
                            hidden:true,
                            height:'auto',
                            html: '<div class="main-content" >'
                                + '<div class="row">'

                                + '<div class="col-md-12">'
                                    + '<div style="border-bottom:1px solid #cccc;padding-bottom:3px">'
                                        + '<div style="font-weight:bold" id="AccordTitle1"></div>'
                                        + '<div style="padding-top:7px;" class="value" id="Accord1"></div>'
                                    + '</div>'
                                + '</div>'

                                + '</div>'
                                + '</div>'
                        },
                        {
                            xtype: 'panel',
                            width: '100%',
                            id:"PanelCP2",
                            height:'auto',
                            hidden:true,
                            html: '<div class="main-content" >'
                                + '<div class="row">'

                                + '<div class="col-md-12">'
                                    + '<div style="border-bottom:1px solid #cccc;padding-bottom:3px">'
                                        + '<div style="font-weight:bold" id="AccordTitle2"></div>'
                                        + '<div style="padding-top:7px;" class="value" id="Accord2"></div>'
                                    + '</div>'
                                + '</div>'

                                + '</div>'
                                + '</div>'
                        },
                        {
                            xtype: 'panel',
                            width: '100%',
                            id:"PanelCP3",
                            height:'auto',
                            hidden:true,
                            html: '<div class="main-content" >'
                                + '<div class="row">'

                                + '<div class="col-md-12">'
                                    + '<div style="border-bottom:1px solid #cccc;padding-bottom:3px">'
                                        + '<div style="font-weight:bold" id="AccordTitle3"></div>'
                                        + '<div style="padding-top:7px;" class="value" id="Accord3"></div>'
                                    + '</div>'
                                + '</div>'

                                + '</div>'
                                + '</div>'
                        },
                        {
                            xtype: 'panel',
                            width: '100%',
                            id:"PanelCP4",
                            height:'auto',
                            hidden:true,
                            html: '<div class="main-content" >'
                                + '<div class="row">'

                                + '<div class="col-md-12">'
                                    + '<div style="border-bottom:1px solid #cccc;padding-bottom:3px">'
                                        + '<div style="font-weight:bold" id="AccordTitle4"></div>'
                                        + '<div style="padding-top:7px;" class="value" id="Accord4"></div>'
                                    + '</div>'
                                + '</div>'

                                + '</div>'
                                + '</div>'
                        },
                        {
                            xtype: 'panel',
                            width: '100%',
                            id:"PanelCP5",
                            height:'auto',
                            hidden:true,
                            html: '<div class="main-content" >'
                                + '<div class="row">'

                                + '<div class="col-md-12">'
                                    + '<div style="border-bottom:1px solid #cccc;padding-bottom:3px">'
                                        + '<div style="font-weight:bold" id="AccordTitle5"></div>'
                                        + '<div style="padding-top:7px;" class="value" id="Accord5"></div>'
                                    + '</div>'
                                + '</div>'

                                + '</div>'
                                + '</div>'
                        },
                        {
                            xtype: 'panel',
                            width: '100%',
                            height:10,
                            html: '<div class="main-content" >'
                                 +'<div class="row">'
                                 + '<div class="col-md-12">'
                                 + '</div>'
                                 + '</div>'
                                + '</div>'
                                + '</div>'

                        },
                        /*{
                            xtype:'panel',
                            hidden:true,
                            //cls: 'Sfr_PanelSubLayoutFormRoundedGray',
                            title: lang('Cara Pembayaran'),
                            width: '98%',
                            height: 300,
                            defaults: {
                                // applied to each contained panel
                                bodyStyle: 'padding:15px'
                            },
                            layout: {
                                // layout-specific configs go here
                                type: 'accordion',
                                titleCollapse: false,
                                animate: true,
                                activeOnTop: true
                            },
                            items: [
                            {
                                title: 'Panel 1',
                               
                                html: 'Panel content!'
                            },{
                                title: 'Panel 2',
                              
                                html: '<ol>\n\t<li>Open the BRI internet banking website</li>\n\t<li>Login by entering your User ID and Password</li>\n\t<li>Select on the &quot;Payment&quot; menu</li>\n\t<li>Then select the &quot;BRIVA&quot; menu</li>\n\t<li>Then select Payer&#39;s account</li>\n\t<li>Enter your BRI Virtual Account Number (Example 26215-1234567890)</li>\n\t<li>Enter the nominal to be paid</li>\n\t<li>Enter your password and Mtoken</li>\n\t<li>Transaction was successful</li>\n</ol>\n'
                            },{
                                title: 'Panel 3',
                              
                                html: 'Panel content!'
                            }
                            ],
                        }*/
                    ],
                }
            ],
            buttons: [
            {
                text: 'Close',
                margin: '5px',
                scale: 'large',
                ui: 's-button',
                cls: 's-grey',
                disabled: false,
                handler: function () {
                    Ext.getCmp('Koltiva.view.Traceability_new.Reception.PaymentInstuction').destroy();
                }
            }],
            listeners: {
                afterrender: function(c){
                    // var SupplyTransID = thisObj.viewVar.SupplyTransID;
                    // var Tipe = thisObj.viewVar.Tipe;
                            
                    Ext.Ajax.request({
                        url: m_api + '/traceability_api/web_transaction/payment_instruction',
                        method: 'GET',
                        params: {
                            SupplyTransID : Object.is(sessionStorage.getItem('setSupplyTransID'), null) == false ? sessionStorage.getItem('setSupplyTransID') : thisObj.viewVar.SupplyTransID
                        },
                        success: function(fp, o){
                            var r = Ext.decode(fp.responseText);
                            var logo = '<img src="'+r.PaymentViaLogo+'" width="100px">';
                            $('#nomor_va').html(r.VirtualAccount);
                            $('#nomor_va_ori').val(r.VirtualAccount);
                            $('#va_name').html(r.VirtualAccountName);
                            $('#va_name_ori').val(r.VirtualAccountName);
                            $('#nomor_po').html(r.TransactionNumber);
                            $('#TotalTagihan').html(r.TotalPayment);
                            $('#amount_ori').val(r.TotalPaymentOri);
                            $('#PaymentVia').html(lang(r.PaymentVia));
                            $('#PaymentViaLogo').html(logo);
                            if(r.PaymentMethodID=='2' || r.PaymentMethodID=='3'){
                                $('#PanelVirtualAccount').show();
                            }

                            var total = r.data.length-1;
                            $.each(r.data, function(idx, key) {
                                Ext.getCmp('PanelCP'+idx).show();
                                if(total == idx){
                                    Ext.getCmp('PanelCP'+idx).setHeight(250);
                                }

                                $('#AccordTitle'+idx).html(key.Name);
                                $('#Accord'+idx).html(key.Content);
                            });
                            
                        }
                    });
                }
            }
        }];
        //========================================================== LAYOUT UTAMA (END) ========================================//
        this.callParent(arguments);
    },
    listeners: {
        afterrender: function(){
            
        }
    }
});