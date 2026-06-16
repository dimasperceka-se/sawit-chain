Ext.onReady(function(){
    var store,grid,height,panelchart,chartTraining,chartcert,chartnutsum;
    var mc_Provinsi = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id','label'],
        autoLoad: true,
        pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_Provinsi,
            reader: {
                type: 'json',
                    root: 'data'
            }
        }
    });

    var mc_Year = Ext.create('Ext.data.Store',{
       extend:'Ext.data.Model',
       fields:['label'],
       autoLoad:true,
       proxy:{
           type: 'ajax',
           url: m_year,
           reader:{
               type:'json',
               root:'data'
           }
       }
    });

    var mc_Kabupaten = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id','label'],
        autoLoad: true,
        pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_Kabupaten,
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });

   var mc_CertificationType = Ext.create('Ext.data.Store',{
      fields:['id','label'],
      data: [
          {'id':'1','label':'UTZ'},
          {'id':'2','label':'Rainforest'},
          {'id':'3','label':'Fairtrade'},
          {'id':'4','label':'Organic'}
      ]
   });

    var mc_Survey = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['id','label'],
        autoLoad: true,
        pageSize: 10,
        proxy: {
            type: 'ajax',
            url: m_Survey,
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });

    /* store menu summary
    var storeSummary2 = Ext.create('Ext.data.Store', {
        extend: 'Ext.data.Model',
        fields: ['col_left','col_right'],
        autoLoad: true,
        proxy: {
            type: 'ajax',
            url: m_menu,
            extraParams: {kategori: 1},
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });
    */

    // store main grid -----------------------
    var storeSummary = Ext.create('Ext.data.Store',{
        extend: 'Ext.data.Model',
        fields: ['col_left','col_right'],
        autoLoad: true,
        proxy: {
            type: 'ajax',
            url: m_menu,
            extraParams: {kategori: 1},
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });

    var storeFarmer = Ext.create('Ext.data.Store',{
        extend: 'Ext.data.Model',
        fields: ['col_left','col_right'],
        autoLoad: true,
        proxy: {
            type: 'ajax',
            url: m_menu,
            extraParams: {kategori: 2},
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });

    var storeGarden = Ext.create('Ext.data.Store',{
        extend: 'Ext.data.Model',
        fields: ['col_left','col_right'],
        autoLoad: true,
        proxy: {
            type: 'ajax',
            url: m_menu,
            extraParams: {kategori: 3},
            reader: {
                type: 'json',
                root: 'data'
            }
        }
    });

    function showWinReport(val){
        if(!winReport.isVisible()){
            Ext.getCmp('Provinsi').setValue(' -- All --')
            Ext.getCmp('Kabupaten').setValue(' -- All --')
            panelReport.remove(grid);
            panelReport.remove(panelchart);
            panelReport.remove(chartTraining);
            panelReport.remove(chartcert);
            panelReport.remove(chartnutsum);
            winReport.setTitle(val);
            Ext.getCmp("jenis").setValue(val);
            winReport.show();
            if(val=='Farmer Detail Data' ||
               val=='Summary Garden Data' ||
               val=='Nutrisi' || val=='PPI') {
                Ext.getCmp('Survey').show();
                Ext.getCmp('Survey').setValue('0');
                Ext.getCmp('LatestSurvey').show();
                Ext.getCmp('LatestSurvey').setValue('0');
                Ext.getCmp('trainingYear').hide();
                Ext.getCmp('CertificationType').hide();
            }else if(val == 'Certification'||val == 'Certification Summary'){
                Ext.getCmp('trainingYear').show();
                Ext.getCmp('CertificationType').show();
                Ext.getCmp('Survey').hide();
                Ext.getCmp('LatestSurvey').hide();
            }else if(val == 'GAP Participants' ||
                    val == 'GFP Participants' ||
                    val =='GNP Participants' ||
                    val =='Cumulative GAP Participants' ||
                    val =='Cumulative GFP Participants' ||
                    val =='Cumulative GNP Participants'){
                Ext.getCmp('Survey').hide();
                Ext.getCmp('LatestSurvey').hide();
                Ext.getCmp('CertificationType').hide();
                Ext.getCmp('trainingYear').show();
            }else {
                Ext.getCmp('Survey').hide();
                Ext.getCmp('LatestSurvey').hide();
                Ext.getCmp('trainingYear').hide();
                Ext.getCmp('CertificationType').hide();
            }
        } else {
            winReport.hide(this, function() {});
            Ext.getCmp("jenis").setValue();
            winReport.toFront();
        }
    }

    function renderMenu(value, p, record) {
        if(value != ""){
            return Ext.String.format(
                '<img src="/images/bullet_star.gif" /><a href="#" style="margin:10px 0 0 7px !important;">{0}</a>',
                lang(value)
            );
        }
    }

    function getKabupaten(){
        if(Ext.getCmp('Kabupaten').getValue()[0].trim()=='-- All --'){
            var ar = [];
            mc_Kabupaten.data.each(function(item, index, totalItems ) {
                if(item.data['label'] != ' -- All --'){
                    ar.push(item.data['label']);
                }
            });
            ar = ar.join();
        }else{
            var ar = Ext.getCmp('Kabupaten').getValue().join();
        }
        return ar;
    }

    var tab = Ext.create('Ext.Panel', {
        renderTo: 'ext-content',
        height : 500,
        frame: false,
        layout: {
            type: 'hbox',
            align: 'stretch'
        },
        items: [{
            xtype: 'gridpanel',
            padding:15,
            cls:'custom-grid',
            title: lang('Summary Report'),
            width: '48%',
            store: storeSummary,
            hideHeaders: true,
            columns: [
                { text: '', dataIndex: 'col_left', flex: 1, renderer: renderMenu},
                { text: '', dataIndex: 'col_right', flex: 1, renderer: renderMenu}
            ],
            height: 90,
            viewConfig: {
                listeners: {
                    cellclick : function(view, cell, cellIndex, record,row, rowIndex, e) {
                        var clickedCell = view.panel.headerCt.getHeaderAtIndex(cellIndex).dataIndex;
                        var clickedCellValue = record.get(clickedCell);
                        if(clickedCellValue !== ""){
                            showWinReport(clickedCellValue);
                        }
                    }
                }
            }
        },{
            xtype: 'gridpanel',
            padding:15,
            cls:'custom-grid',
            title: 'Detail Report',
            width: '48%',
            store: storeFarmer,
            hideHeaders: true,
            columns: [
                { text: '', dataIndex: 'col_left', flex: 1, renderer: renderMenu },
                { text: '', dataIndex: 'col_right', flex: 1, renderer: renderMenu }
            ],
            height: 500,
            viewConfig: {
                listeners: {
                    cellclick : function(view, cell, cellIndex, record,row, rowIndex, e) {
                        var clickedCell = view.panel.headerCt.getHeaderAtIndex(cellIndex).dataIndex;
                        var clickedCellValue = record.get(clickedCell);
                        if(clickedCellValue !== ""){
                            showWinReport(clickedCellValue);
                        }
                    }
                }
            }
        }/*,{
            xtype: 'gridpanel',
            padding:10,
            cls:'custom-grid',
            title: 'Garden Report',
            width: '35%',
            store: storeGarden,
            hideHeaders: true,
            columns: [
                { text: '', dataIndex: 'col_left', flex: 1, renderer: renderMenu },
                { text: '', dataIndex: 'col_right', flex: 1, renderer: renderMenu }
            ],
            height: 500,
            viewConfig: {
                listeners: {
                    cellclick : function(view, cell, cellIndex, record,row, rowIndex, e) {
                        var clickedCell = view.panel.headerCt.getHeaderAtIndex(cellIndex).dataIndex;
                        var clickedCellValue = record.get(clickedCell);
                        if(clickedCellValue !== ""){
                            showWinReport(clickedCellValue);
                        }
                    }
                }
            }
        }*/]
    });

    // panel report
    var panelReport = Ext.create('Ext.Panel', {
        height : 180,
        frame: false,
        autoScroll: true,
        tbar:{
            //cls: 'custom-tbar',
            items:[' ',{
                xtype:'form',
                cls: 'custom-tbar',
                style:'margin-top:10px',
                items:[{
                    xtype:'textfield',
                    id:'jenis',
                    name:'jenis',
                    hidden:true
                },{
                    layout:'column',
                    items: [{
                        columnWidth: .3,
                        padding:3,
                        border: false,
                        items:[{
                            id: 'Provinsi',
                            name: 'Provinsi',
                            xtype: 'combo',
                            labelWidth: 60,
                            fieldLabel: 'Provinsi',
                            store:mc_Provinsi,
                            displayField: 'label',
                            valueField: 'label',
                            queryMode: 'local',
                            listeners: {
                                change: function (cb, nv, ov) {
                                    mc_Kabupaten.load({
                                        params: {
                                            key: Ext.getCmp('Provinsi').getValue()
                                        }
                                    });
                                }
                            }
                        }]
                    },{
                        columnWidth: .4,
                        border:false,
                        padding:3,
                        style:'margin:0 0 0 5px',
                        items:[{
                            id: 'Kabupaten',
                            name: 'Kabupaten[]',
                            xtype: 'combo',
                            labelWidth: 70,
                            fieldLabel: 'Kabupaten',
                            store:mc_Kabupaten,
                            displayField: 'label',
                            valueField: 'label',
                            queryMode: 'local',
                            multiSelect: true,
                            forceSelection: true
                        }]
                    },{
                        border:false,
                        padding:3,
                        style:'margin:0 0 0 5px',
                        items:[{
                            id: 'trainingYear',
                            name: 'trainingYear',
                            xtype: 'combo',
                            labelWidth: 40,
                            fieldLabel: 'Year',
                            store:mc_Year,
                            displayField: 'label',
                            valueField: 'label',
                            queryMode: 'local',
                            forceSelection: true
                        }]
                    },{
                        border:false,
                        padding:3,
                        style:'margin:0 0 0 5px',
                        items:[{
                            id: 'CertificationType',
                            name: 'CertificationType',
                            xtype: 'combo',
                            labelWidth: 75,
                            fieldLabel: 'Certification',
                            width:190,
                            store:mc_CertificationType,
                            displayField: 'label',
                            valueField: 'id',
                            queryMode: 'local'
                        }]
                    },{
                        //columnWidth: .4,
                        border:false,
                        padding:3,
                        style:'margin:0 0 0 5px',
                        items:[{
                            id: 'Survey',
                            name: 'Survey',
                            xtype: 'combo',
                            labelWidth: 60,
                            fieldLabel: 'Survey',
                            width:320,
                            store:mc_Survey,
                            displayField: 'label',
                            valueField: 'id',
                            queryMode: 'local',
                            hidden:true
                        }]
                    },{
                        border:false,
                        padding:3,
                        style:'margin:2px 0 0 3px',
                        items:[{
                            xtype: 'fieldcontainer',
                            defaultType: 'checkboxfield',
                            items: [{
                                boxLabel: lang('Latest Survey'),
                                name: 'LatestSurvey',
                                inputValue: '1',
                                id: 'LatestSurvey'
                            }]
                        }]
                    }]
                }]
            },'-',{
                text: 'Preview',
                handler: function () {
                    if((Ext.getCmp('jenis').getValue()=='Farmer Detail Data' ||
                        Ext.getCmp('jenis').getValue()=='Nutrisi' ||
                        Ext.getCmp('jenis').getValue()=='PPI') &&
                        Ext.getCmp('Kabupaten').getValue()[0].trim()=='-- All --') {
                            Ext.MessageBox.alert('Warning','Silahkan pilih salah satu Kabupaten');
                            return;
                    }
                    if((Ext.getCmp('jenis').getValue()=='GAP Participants' ||
                        Ext.getCmp('jenis').getValue()=='GFP Participants' ||
                        Ext.getCmp('jenis').getValue()=='GNP Participants' ||
                        Ext.getCmp('jenis').getValue()=='Cumulative GAP Participants' ||
                        Ext.getCmp('jenis').getValue()=='Cumulative GFP Participants' ||
                        Ext.getCmp('jenis').getValue()=='Cumulative GNP Participants') &&
                        (Ext.getCmp('trainingYear').getValue()==null)){
                            Ext.Msg.alert('Warning','Please select training year');
                            return;
                    }

                    panelReport.remove(grid);
                    panelReport.remove(panelchart);
                    panelReport.remove(chartTraining);
                    panelReport.remove(chartcert);
                    panelReport.remove(chartnutsum);
                    var title = Ext.getCmp('jenis').getValue();
                    if(title == "GAP Participants" ||
                       title == "GFP Participants" ||
                       title == "GNP Participants" ||
                       title == "Cumulative GAP Participants" ||
                       title == "Cumulative GFP Participants" ||
                       title == "Cumulative GNP Participants"){
                         createChartTraining(title);
                    }else if(title == "Summary Garden Data"){
                        createChart(title);
                    }else if(title == "Certification Summary"){
                        createChartSumCert(title);
                    }else if (title == 'Nutrition Summary'){
                        createChartNutSum(title);
                    }else{
                        createGrid(title);
                    }

                }
            },' ',{
                id:'excel',
                text: 'Export Data',
                handler: function() {
                    //Ext.getCmp('jenis').getValue()=='Summary Garden Data' ||
                    if((Ext.getCmp('jenis').getValue()=='Farmer Detail Data' ||
                    Ext.getCmp('jenis').getValue()=='Nutrisi' || Ext.getCmp('jenis').getValue()=='PPI') &&
                    Ext.getCmp('Kabupaten').getValue()=='') {
                        Ext.MessageBox.alert('Warning','Silahkan pilih Kabupaten');
                        return
                    }
                    if(Ext.getCmp('Kabupaten').getValue()[0].trim()=='-- All --'){
                        var ar = [];
                        mc_Kabupaten.data.each(function(item, index, totalItems ) {
                            if(item.data['label'] != ' -- All --'){
                                ar.push(item.data['label']);
                            }
                        });
                        Ext.getCmp('Kabupaten').setValue(ar);
                    }

                    var form =this.ownerCt.down('form').getForm();
                    form.submit({
                        url: m_crud,
                        method : 'POST',
                        waitMsg: 'Wait...',
                        timeout : 300000,
                        success: function(fp, o) {
                            window.location = o.result.file;
                        }
                    });

                }
            }]
        }
   });

    // Window
    var winReport = Ext.create('widget.window', {
        title: '',
        id:'win-report',
        closable: true,
        modal:true,
        closeAction: 'show',
        width: '100%',
        autoScroll: true,
        //minWidth: 570,
        height: '100%',
        layout: {
            type: 'fit'
        },
        items: [panelReport]
    });

    Ext.define('GridLaporan', {
        extend: 'Ext.grid.Panel',
        alias: 'widget.gridlaporan',
        title: '',
        columns: [],
        initComponent: function() {
            var me = this;
            me.store = Ext.create('Ext.data.Store', {
                autoLoad: true,
                pageSize: 50,
                proxy: {
                    type: 'ajax',
                    url: m_crud,
                    waitMsg: lang('Please Wait'),
                    extraParams: {
                        //Kabupaten : Ext.getCmp('Kabupaten').getValue().join(),
                        Kabupaten :getKabupaten(),
                        Provinsi : Ext.getCmp('Provinsi').getValue(),
                        Survey : Ext.getCmp('Survey').getValue(),
                        jenis : Ext.getCmp('jenis').getValue(),
                        trainingDate : Ext.getCmp('trainingYear').getValue(),
                        LatestSurvey : Ext.getCmp('LatestSurvey').getValue(),
                        CertificationType : Ext.getCmp('CertificationType').getValue()
                    },
                    reader: {
                        type: 'json',
                        root: 'data',
                        totalProperty: 'count'
                    }
                },
                fields: [],
                listeners: {
                    metachange: function(store, meta) {
                        me.reconfigure(null, meta.columns);
                    }
                }
            });
            me.bbar = Ext.create('Ext.PagingToolbar',{
                store: me.getStore(),
                displayInfo:true,
                displayMsg: '{0} - {1} of {2}',
                emptyMsg: "No data to display"
            });
            me.callParent(arguments);
        }
    });

    Ext.define('GridSummary', {
        extend: 'Ext.grid.Panel',
        alias: 'widget.gridsummary',
        title: '',
        columns: [],
        features:[{ftype:"summary"}],
        initComponent: function() {
            var me = this;
            me.store = Ext.create('Ext.data.Store', {
                autoLoad: true,
                proxy: {
                    type: 'ajax',
                    url: m_crud,
                    waitMsg: lang('Please Wait'),
                    extraParams: {
                        //Kabupaten : Ext.getCmp('Kabupaten').getValue().join(),
                        Kabupaten :getKabupaten(),
                        Provinsi : Ext.getCmp('Provinsi').getValue(),
                        Survey : Ext.getCmp('Survey').getValue(),
                        jenis : Ext.getCmp('jenis').getValue(),
                        trainingDate : Ext.getCmp('trainingYear').getValue(),
                        LatestSurvey : Ext.getCmp('LatestSurvey').getValue(),
                        CertificationType : Ext.getCmp('CertificationType').getValue()
                    },
                    reader: {
                        type: 'json',
                        root: 'data'
                    }
                },
                fields: [],
                listeners: {
                    metachange: function(store, meta) {
                        me.reconfigure(null, meta.columns);
                    }
                }
            });
            /*
            me.bbar = Ext.create('Ext.PagingToolbar',{
                store: me.getStore(),
                displayInfo:true,
                displayMsg: '{0} - {1} of {2}',
                emptyMsg: "No data to display"
            });
            */
            me.callParent(arguments);
        }
    });

    // create chart summary garden
    var createChart = function(title){
        panelchart = Ext.create('Ext.Panel', {
           title:title,
           id:'panel-chart',
           frame:false,
           border:false,
           items:[{
                layout:{
                    type:'hbox',
                    align: 'stretch'
                },
                items:[{
                    xtype: 'panel',
                    //frame:true,
                    style:'margin:10px',
                    //title: 'Total Farmer',
                    width:650,
                    height:500,
                    items:[{
                        xtype: 'chart',
                        width: 650,
                        id:'pie-garden-farmers',
                        height: 450,
                        //padding: '10 0 0 40',
                        margin: "5 10 0 10",
                        animate: true,
                        store : Ext.create('Ext.data.Store', {
                            fields: [
                                {name:'name',type:'string'},
                                {name:'data',type:'int'}
                            ],
                            autoLoad: true,
                            waitMsg: lang('Please Wait'),
                            proxy: {
                                type: 'ajax',
                                url: m_piestore,
                                extraParams: {
                                    //Kabupaten : Ext.getCmp('Kabupaten').getValue().join(),
                                    Kabupaten :getKabupaten(),
                                    Provinsi : Ext.getCmp('Provinsi').getValue(),
                                    Survey : Ext.getCmp('Survey').getValue(),
                                    jenis : Ext.getCmp('jenis').getValue(),
                                    trainingDate : Ext.getCmp('trainingYear').getValue(),
                                    type: "farmer",
                                    LatestSurvey : Ext.getCmp('LatestSurvey').getValue(),
                                    CertificationType : Ext.getCmp('CertificationType').getValue()
                                },
                                reader: {
                                    type: 'json',
                                    root: 'data'
                                }
                            }
                        }),
                        insetPadding: 90,
                        legend: {
                            field: 'name',
                            position: 'right',
                            style:'margin:30px',
                            boxStrokeWidth: 0,
                            labelFont:'10px Helvetica'
                        },
                        items: [{
                            type  : 'text',
                            text  : 'Total Farmer',
                            font  : '18px Helvetica',
                            width : 100,
                            height: 30,
                            x : 200, //the sprite x position
                            y : 12  //the sprite y position
                        }],
                        series: [{
                            type: 'pie',
                            getLegendColor: function(index) {
                                return ['#3B5323','#446B1E','#4E8419','#589C14','#61B50F','#6BCD0A','#75E605','#7FFF00'][index%4];
                            },
                            renderer: function(sprite, record, attr, index, store) {
                                return Ext.apply(attr, {
                                    fill: ['#3B5323','#446B1E','#4E8419','#589C14','#61B50F','#6BCD0A','#75E605','#7FFF00'][index%4]
                                });
                            },
                            angleField: 'data',
                            label: {
                                field: 'name',
                                display: 'outside',
                                calloutLine: true
                            },
                            tips: {
                                trackMouse: true,
                                width: 220,
                                height: 28,
                                renderer: function (storeItem, item) {
                                    var totalFarmer = 0;
                                    var strPieFarmer = Ext.getCmp('pie-garden-farmers').getStore();
                                    strPieFarmer.each(function(rec) {
                                        totalFarmer += rec.get('data');
                                    });
                                    this.setTitle(storeItem.get('name') + ': '
                                            + Math.round(parseInt(storeItem.get('data')) / totalFarmer * 100) + '% ('
                                            + Ext.util.Format.number(parseInt(storeItem.get('data')), "0,0")+')');

                                }
                            },
                            showInLegend: true,
                            highlight: true
                        }]
                    }]
                },{
                    xtype: 'panel',
                    //frame:true,
                    style:'margin:10px',
                    //title: 'Total Production',
                    padding :20,
                    width:650,
                    height:450,
                    items:[{
                        xtype: 'chart',
                        width: 650,
                        id:'pie-garden-production',
                        height: 450,
                        //padding: '10 0 0 40',
                        margin: "5 10 0 10",
                        animate: true,
                        store : Ext.create('Ext.data.Store', {
                            fields: [
                                {name:'name',type:'string'},
                                {name:'data',type:'int'}
                            ],
                            autoLoad: true,
                            waitMsg: lang('Please Wait'),
                            proxy: {
                                type: 'ajax',
                                url: m_piestore,
                                extraParams: {
                                    //Kabupaten : Ext.getCmp('Kabupaten').getValue().join(),
                                    Kabupaten :getKabupaten(),
                                    Provinsi : Ext.getCmp('Provinsi').getValue(),
                                    Survey : Ext.getCmp('Survey').getValue(),
                                    jenis : Ext.getCmp('jenis').getValue(),
                                    trainingDate : Ext.getCmp('trainingYear').getValue(),
                                    type: "production",
                                    LatestSurvey : Ext.getCmp('LatestSurvey').getValue(),
                                    CertificationType : Ext.getCmp('CertificationType').getValue()
                                },
                                reader: {
                                    type: 'json',
                                    root: 'data'
                                }
                            }
                        }),
                        insetPadding: 90,
                        legend: {
                            field: 'name',
                            position: 'right',
                            style:'margin:30px',
                            boxStrokeWidth: 0,
                            labelFont:'10px Helvetica'
                        },
                        items: [{
                            type  : 'text',
                            text  : 'Total Production',
                            font  : '18px Helvetica',
                            width : 100,
                            height: 30,
                            x : 200, //the sprite x position
                            y : 12  //the sprite y position
                        }],
                        series: [{
                            type: 'pie',
                            getLegendColor: function(index) {
                                return ['#3B5323','#446B1E','#4E8419','#589C14','#61B50F','#6BCD0A','#75E605','#7FFF00'][index%4];
                            },
                            renderer: function(sprite, record, attr, index, store) {
                                return Ext.apply(attr, {
                                    fill: ['#3B5323','#446B1E','#4E8419','#589C14','#61B50F','#6BCD0A','#75E605','#7FFF00'][index%4]
                                });
                            },
                            angleField: 'data',
                            label: {
                                field: 'name',
                                display: 'outside',
                                calloutLine: true
                            },
                            tips: {
                                trackMouse: true,
                                width: 220,
                                height: 28,
                                renderer: function (storeItem, item) {
                                    var totalProduction = 0;
                                    var strPieProduction = Ext.getCmp('pie-garden-production').getStore();
                                    strPieProduction.each(function(rec) {
                                        totalProduction += rec.get('data');
                                    });
                                    this.setTitle(storeItem.get('name') + ': '
                                            + Math.round(parseInt(storeItem.get('data')) / totalProduction * 100) + '% ('
                                            + Ext.util.Format.number(parseInt(storeItem.get('data')), "0,0")+')');

                                }
                            },
                            showInLegend: true,
                            highlight: true
                        }]
                    }]
                }]
           }]
        });
        grid = Ext.widget("gridlaporan");
        grid.setTitle("Details Report");
        panelchart.add(grid);
        panelReport.setHeight(220+(height*34));
        panelReport.add(panelchart);
        panelReport.doLayout();
    };

    // create chart summary certification
    var createChartSumCert = function(title){
        chartcert = Ext.create('Ext.Panel', {
           title:title,
           id:'panel-chart-sumcert',
           frame:false,
           border:false,
           items:[{
                layout:{
                    type:'hbox',
                    align: 'stretch'
                },
                items:[{
                    xtype: 'panel',
                    style:'margin:10px',
                    width:650,
                    height:500,
                    items:[{
                        xtype: 'chart',
                        width: 650,
                        id:'pie-cert-farmers',
                        height: 450,
                        margin: "5 10 0 10",
                        animate: true,
                        store : Ext.create('Ext.data.Store', {
                            fields: [
                                {name:'name',type:'string'},
                                {name:'data',type:'int'}
                            ],
                            autoLoad: true,
                            waitMsg: lang('Please Wait'),
                            proxy: {
                                type: 'ajax',
                                url: m_piestore,
                                extraParams: {
                                    //Kabupaten : Ext.getCmp('Kabupaten').getValue().join(),
                                    Kabupaten :getKabupaten(),
                                    Provinsi : Ext.getCmp('Provinsi').getValue(),
                                    Survey : Ext.getCmp('Survey').getValue(),
                                    jenis : Ext.getCmp('jenis').getValue(),
                                    trainingDate : Ext.getCmp('trainingYear').getValue(),
                                    type: "totalcert",
                                    LatestSurvey : Ext.getCmp('LatestSurvey').getValue(),
                                    CertificationType : Ext.getCmp('CertificationType').getValue()
                                },
                                reader: {
                                    type: 'json',
                                    root: 'data'
                                }
                            }
                        }),
                        insetPadding: 90,
                        legend: {
                            field: 'name',
                            position: 'right',
                            style:'margin:30px',
                            boxStrokeWidth: 0,
                            labelFont:'10px Helvetica'
                        },
                        items: [{
                            type  : 'text',
                            text  : 'Total Certification',
                            font  : '18px Helvetica',
                            width : 100,
                            height: 30,
                            x : 200, //the sprite x position
                            y : 12  //the sprite y position
                        }],
                        series: [{
                            type: 'pie',
                            getLegendColor: function(index) {
                                return ['#3B5323','#446B1E','#4E8419','#589C14','#61B50F','#6BCD0A','#75E605','#7FFF00'][index%4];
                            },
                            renderer: function(sprite, record, attr, index, store) {
                                return Ext.apply(attr, {
                                    fill: ['#3B5323','#446B1E','#4E8419','#589C14','#61B50F','#6BCD0A','#75E605','#7FFF00'][index%4]
                                });
                            },
                            angleField: 'data',
                            label: {
                                field: 'name',
                                display: 'outside',
                                calloutLine: true
                            },
                            tips: {
                                trackMouse: true,
                                width: 220,
                                height: 28,
                                renderer: function (storeItem, item) {
                                    var totalFarmer = 0;
                                    var strPieFarmer = Ext.getCmp('pie-cert-farmers').getStore();
                                    strPieFarmer.each(function(rec) {
                                        totalFarmer += rec.get('data');
                                    });
                                    this.setTitle(storeItem.get('name') + ': '
                                            + Math.round(parseInt(storeItem.get('data')) / totalFarmer * 100) + '% ('
                                            + Ext.util.Format.number(parseInt(storeItem.get('data')), "0,0")+')');

                                }
                            },
                            showInLegend: true,
                            highlight: true
                        }]
                    }]
                },{
                    xtype: 'panel',
                    //frame:true,
                    style:'margin:10px',
                    //title: 'Total Production',
                    padding :20,
                    width:650,
                    height:450,
                    items:[{
                        xtype: 'chart',
                        width: 650,
                        id:'pie-cert-gender',
                        height: 450,
                        //padding: '10 0 0 40',
                        margin: "5 10 0 10",
                        animate: true,
                        store : Ext.create('Ext.data.Store', {
                            fields: [
                                {name:'name',type:'string'},
                                {name:'data',type:'int'}
                            ],
                            autoLoad: true,
                            waitMsg: lang('Please Wait'),
                            proxy: {
                                type: 'ajax',
                                url: m_piestore,
                                extraParams: {
                                    //Kabupaten : Ext.getCmp('Kabupaten').getValue().join(),
                                    Kabupaten :getKabupaten(),
                                    Provinsi : Ext.getCmp('Provinsi').getValue(),
                                    Survey : Ext.getCmp('Survey').getValue(),
                                    jenis : Ext.getCmp('jenis').getValue(),
                                    trainingDate : Ext.getCmp('trainingYear').getValue(),
                                    type: "gendercert",
                                    LatestSurvey : Ext.getCmp('LatestSurvey').getValue(),
                                    CertificationType : Ext.getCmp('CertificationType').getValue()
                                },
                                reader: {
                                    type: 'json',
                                    root: 'data'
                                }
                            }
                        }),
                        insetPadding: 90,
                        legend: {
                            field: 'name',
                            position: 'right',
                            style:'margin:30px',
                            boxStrokeWidth: 0,
                            labelFont:'10px Helvetica'
                        },
                        items: [{
                            type  : 'text',
                            text  : 'Certification / Gender',
                            font  : '18px Helvetica',
                            width : 100,
                            height: 30,
                            x : 200, //the sprite x position
                            y : 12  //the sprite y position
                        }],
                        series: [{
                            type: 'pie',
                            getLegendColor: function(index) {
                                return ['#3B5323','#446B1E','#4E8419','#589C14','#61B50F','#6BCD0A','#75E605','#7FFF00'][index%4];
                            },
                            renderer: function(sprite, record, attr, index, store) {
                                return Ext.apply(attr, {
                                    fill: ['#3B5323','#446B1E','#4E8419','#589C14','#61B50F','#6BCD0A','#75E605','#7FFF00'][index%4]
                                });
                            },
                            angleField: 'data',
                            label: {
                                field: 'name',
                                display: 'outside',
                                calloutLine: true
                            },
                            tips: {
                                trackMouse: true,
                                width: 220,
                                height: 28,
                                renderer: function (storeItem, item) {
                                    var totalProduction = 0;
                                    var strPieProduction = Ext.getCmp('pie-cert-gender').getStore();
                                    strPieProduction.each(function(rec) {
                                        totalProduction += rec.get('data');
                                    });
                                    this.setTitle(storeItem.get('name') + ': '
                                            + Math.round(parseInt(storeItem.get('data')) / totalProduction * 100) + '% ('
                                            + Ext.util.Format.number(parseInt(storeItem.get('data')), "0,0")+')');

                                }
                            },
                            showInLegend: true,
                            highlight: true
                        }]
                    }]
                }]
           }]
        });
        grid = Ext.widget("gridlaporan");
        grid.setTitle("Details Report");
        chartcert.add(grid);
        panelReport.setHeight(220+(height*34));
        panelReport.add(chartcert);
        panelReport.doLayout();
    };

    // create chart training
    var createChartTraining = function(title){
        chartTraining = Ext.create('Ext.Panel', {
           title:'Summary',
           id:'chart-training',
           frame:false,
           border:false,
           items:[{
                layout:{
                    type:'hbox',
                    align: 'stretch'
                },
                items:[{ // farmer per province
                    xtype: 'panel',
                    style:'margin:10px',
                    width:650,
                    height:500,
                    items:[{
                        xtype: 'chart',
                        id:'bar-training',
                        width: 550,
                        height: 470,
                        animate: true,
                        store : Ext.create('Ext.data.Store', {
                            fields: ['name', 'data'],
                            autoLoad: true,
                            waitMsg: lang('Please Wait'),
                            proxy: {
                                type: 'ajax',
                                url: m_piestore,
                                extraParams: {
                                    //Kabupaten : Ext.getCmp('Kabupaten').getValue().join(),
                                    Kabupaten :getKabupaten(),
                                    Provinsi : Ext.getCmp('Provinsi').getValue(),
                                    Survey : Ext.getCmp('Survey').getValue(),
                                    jenis : Ext.getCmp('jenis').getValue(),
                                    trainingDate : Ext.getCmp('trainingYear').getValue(),
                                    type: "province",
                                    LatestSurvey : Ext.getCmp('LatestSurvey').getValue(),
                                    CertificationType : Ext.getCmp('CertificationType').getValue()
                                },
                                reader: {
                                    type: 'json',
                                    root: 'data'
                                }
                            }
                        }),
                        axes: [{
                            type: 'Numeric',
                            position: 'left',
                            fields: ['data'],
                            label: {
                                renderer: Ext.util.Format.numberRenderer('0,0')
                            },
                            title: 'Participants',
                            grid: true,
                            minimum: 0
                        }, {
                            type: 'Category',
                            position: 'bottom',
                            fields: ['name'],
                            title: '',
                            label: {
                                rotate: { degrees: -50 }
                            }
                        }],
                        series: [{
                            type: 'column',
                            axis: 'left',
                            highlight: true,
                            tips: {
                                trackMouse: true,
                                width: 190,
                                height: 28,
                                renderer: function (storeItem, item) {
                                    var total = 0;
                                    var strBar = Ext.getCmp('bar-training').getStore();
                                    strBar.each(function(rec) {
                                        total += parseInt(rec.get('data'));
                                    });
                                    //this.setTitle(storeItem.get('name') + ': ' + storeItem.get('data') + ' %');
                                    this.setTitle(storeItem.get('name') + ': '
                                            + Math.round(storeItem.get('data') / total * 100) + '% ('
                                            + Ext.util.Format.number(storeItem.get('data'), "0,0")+')');
                                }
                            },
                            label: {
                                display: 'outside',
                                'text-anchor': 'middle',
                                field: 'data',
                                renderer: Ext.util.Format.numberRenderer('0,0'),
                                orientation: 'horizontal',
                                font: 'bold 12px Arial',
                                style: 'margin-left:20px',
                                color: '#333'
                            },
                            getLegendColor: function(index) {
                                return ['#446B1E'];
                            },
                            renderer: function(sprite, record, attr, index, store) {
                                return Ext.apply(attr, {
                                    fill: ['#446B1E']
                                });
                            },
                            xField: 'name',
                            yField: 'data'
                        }]
                    }]
                },{ // farmer per gender
                    xtype: 'panel',
                    style:'margin:10px',
                    padding :20,
                    width:650,
                    height:500,
                    items:[{
                        xtype: 'chart',
                        id:'pie-training',
                        width: 650,
                        height: 420,
                        //padding: '10 0 0 40',
                        margin: "5 10 0 10",
                        animate: true,
                        store : Ext.create('Ext.data.Store', {
                            fields: ['name', 'data'],
                            autoLoad: true,
                            waitMsg: lang('Please Wait'),
                            proxy: {
                                type: 'ajax',
                                url: m_piestore,
                                extraParams: {
                                    //Kabupaten : Ext.getCmp('Kabupaten').getValue().join(),
                                    Kabupaten :getKabupaten(),
                                    Provinsi : Ext.getCmp('Provinsi').getValue(),
                                    Survey : Ext.getCmp('Survey').getValue(),
                                    jenis : Ext.getCmp('jenis').getValue(),
                                    trainingDate : Ext.getCmp('trainingYear').getValue(),
                                    type: "gender",
                                    LatestSurvey : Ext.getCmp('LatestSurvey').getValue(),
                                    CertificationType : Ext.getCmp('CertificationType').getValue()
                                },
                                reader: {
                                    type: 'json',
                                    root: 'data'
                                }
                            }
                        }),
                        insetPadding: 90,
                        legend: {
                            field: 'name',
                            position: 'right',
                            style:'margin:30px',
                            boxStrokeWidth: 0,
                            labelFont:'10px Helvetica'
                        },
                        items: [{
                            type  : 'text',
                            text  : 'Participants / Gender',
                            font  : '18px Helvetica',
                            width : 100,
                            height: 30,
                            x : 150, //the sprite x position
                            y : 10  //the sprite y position
                        }],
                        series: [{
                            type: 'pie',
                            getLegendColor: function(index) {
                                return ['#3B5323','#446B1E','#4E8419','#589C14','#61B50F','#6BCD0A','#75E605','#7FFF00'][index%4];
                            },
                            renderer: function(sprite, record, attr, index, store) {
                                return Ext.apply(attr, {
                                    fill: ['#3B5323','#446B1E','#4E8419','#589C14','#61B50F','#6BCD0A','#75E605','#7FFF00'][index%4]
                                });
                            },
                            angleField: 'data',
                            label: {
                                field: 'name',
                                display: 'outside',
                                calloutLine: true
                            },
                            tips: {
                                trackMouse: true,
                                width: 140,
                                height: 28,
                                renderer: function (storeItem, item) {
                                    var total = 0;
                                    var strPie = Ext.getCmp('pie-training').getStore();
                                    strPie.each(function(rec) {
                                        total += rec.get('data');
                                    });
                                    this.setTitle(storeItem.get('name') + ': '
                                            + Math.round(storeItem.get('data') / total * 100) + '% ('
                                            + Ext.util.Format.number(parseInt(storeItem.get('data')), "0,0")+')');
                                }
                            },
                            showInLegend: true,
                            highlight: true
                        }]
                    }]
                }]
           }]
        });
        grid = Ext.widget("gridsummary");
        grid.setTitle("Details Report");
        chartTraining.add(grid);
        panelReport.setHeight(220+(height*34));
        panelReport.add(chartTraining);
        panelReport.doLayout();
    };

    // create chart nutrition summary
    var createChartNutSum = function(title){
        chartnutsum = Ext.create('Ext.Panel', {
           title:title,
           id:'panel-chart-nutsum',
           frame:false,
           border:false,
           items:[{
                layout:{
                    type:'hbox',
                    align: 'stretch'
                },
                items:[{
                    xtype: 'panel',
                    style:'margin:10px',
                    width:650,
                    height:500,
                    items:[{
                        xtype: 'chart',
                        width: 650,
                        id:'pie-nutsum-gnp',
                        height: 450,
                        margin: "5 10 0 10",
                        animate: true,
                        store : Ext.create('Ext.data.Store', {
                            fields: [
                                {name:'name',type:'string'},
                                {name:'data',type:'int'}
                            ],
                            autoLoad: true,
                            waitMsg: lang('Please Wait'),
                            proxy: {
                                type: 'ajax',
                                url: m_piestore,
                                extraParams: {
                                    //Kabupaten : Ext.getCmp('Kabupaten').getValue().join(),
                                    Kabupaten :getKabupaten(),
                                    Provinsi : Ext.getCmp('Provinsi').getValue(),
                                    Survey : Ext.getCmp('Survey').getValue(),
                                    jenis : Ext.getCmp('jenis').getValue(),
                                    trainingDate : Ext.getCmp('trainingYear').getValue(),
                                    type: "gnpnutsum",
                                    LatestSurvey : Ext.getCmp('LatestSurvey').getValue(),
                                    CertificationType : Ext.getCmp('CertificationType').getValue()
                                },
                                reader: {
                                    type: 'json',
                                    root: 'data'
                                }
                            }
                        }),
                        insetPadding: 90,
                        legend: {
                            field: 'name',
                            position: 'right',
                            style:'margin:30px',
                            boxStrokeWidth: 0,
                            labelFont:'10px Helvetica'
                        },
                        items: [{
                            type  : 'text',
                            text  : 'GNP Participants',
                            font  : '18px Helvetica',
                            width : 100,
                            height: 30,
                            x : 200, //the sprite x position
                            y : 12  //the sprite y position
                        }],
                        series: [{
                            type: 'pie',
                            getLegendColor: function(index) {
                                return ['#3B5323','#446B1E','#4E8419','#589C14','#61B50F','#6BCD0A','#75E605','#7FFF00'][index%4];
                            },
                            renderer: function(sprite, record, attr, index, store) {
                                return Ext.apply(attr, {
                                    fill: ['#3B5323','#446B1E','#4E8419','#589C14','#61B50F','#6BCD0A','#75E605','#7FFF00'][index%4]
                                });
                            },
                            angleField: 'data',
                            label: {
                                field: 'name',
                                display: 'outside',
                                calloutLine: true
                            },
                            tips: {
                                trackMouse: true,
                                width: 220,
                                height: 28,
                                renderer: function (storeItem, item) {
                                    var totalFarmer = 0;
                                    var strPieFarmer = Ext.getCmp('pie-nutsum-gnp').getStore();
                                    strPieFarmer.each(function(rec) {
                                        totalFarmer += rec.get('data');
                                    });
                                    this.setTitle(storeItem.get('name') + ': '
                                            + Math.round(parseInt(storeItem.get('data')) / totalFarmer * 100) + '% ('
                                            + Ext.util.Format.number(parseInt(storeItem.get('data')), "0,0")+')');

                                }
                            },
                            showInLegend: true,
                            highlight: true
                        }]
                    }]
                },{
                    xtype: 'panel',
                    //frame:true,
                    style:'margin:10px',
                    //title: 'Total Production',
                    padding :20,
                    width:650,
                    height:450,
                    items:[{
                        xtype: 'chart',
                        width: 650,
                        id:'pie-nutsum-gender',
                        height: 450,
                        //padding: '10 0 0 40',
                        margin: "5 10 0 10",
                        animate: true,
                        store : Ext.create('Ext.data.Store', {
                            fields: [
                                {name:'name',type:'string'},
                                {name:'data',type:'int'}
                            ],
                            autoLoad: true,
                            waitMsg: lang('Please Wait'),
                            proxy: {
                                type: 'ajax',
                                url: m_piestore,
                                extraParams: {
                                    //Kabupaten : Ext.getCmp('Kabupaten').getValue().join(),
                                    Kabupaten :getKabupaten(),
                                    Provinsi : Ext.getCmp('Provinsi').getValue(),
                                    Survey : Ext.getCmp('Survey').getValue(),
                                    jenis : Ext.getCmp('jenis').getValue(),
                                    trainingDate : Ext.getCmp('trainingYear').getValue(),
                                    type: "gendernutsum",
                                    LatestSurvey : Ext.getCmp('LatestSurvey').getValue(),
                                    CertificationType : Ext.getCmp('CertificationType').getValue()
                                },
                                reader: {
                                    type: 'json',
                                    root: 'data'
                                }
                            }
                        }),
                        insetPadding: 90,
                        legend: {
                            field: 'name',
                            position: 'right',
                            style:'margin:30px',
                            boxStrokeWidth: 0,
                            labelFont:'10px Helvetica'
                        },
                        items: [{
                            type  : 'text',
                            text  : 'Participants / Gender',
                            font  : '18px Helvetica',
                            width : 100,
                            height: 30,
                            x : 200, //the sprite x position
                            y : 12  //the sprite y position
                        }],
                        series: [{
                            type: 'pie',
                            getLegendColor: function(index) {
                                return ['#3B5323','#446B1E','#4E8419','#589C14','#61B50F','#6BCD0A','#75E605','#7FFF00'][index%4];
                            },
                            renderer: function(sprite, record, attr, index, store) {
                                return Ext.apply(attr, {
                                    fill: ['#3B5323','#446B1E','#4E8419','#589C14','#61B50F','#6BCD0A','#75E605','#7FFF00'][index%4]
                                });
                            },
                            angleField: 'data',
                            label: {
                                field: 'name',
                                display: 'outside',
                                calloutLine: true
                            },
                            tips: {
                                trackMouse: true,
                                width: 220,
                                height: 28,
                                renderer: function (storeItem, item) {
                                    var totalProduction = 0;
                                    var strPieProduction = Ext.getCmp('pie-nutsum-gender').getStore();
                                    strPieProduction.each(function(rec) {
                                        totalProduction += rec.get('data');
                                    });
                                    this.setTitle(storeItem.get('name') + ': '
                                            + Math.round(parseInt(storeItem.get('data')) / totalProduction * 100) + '% ('
                                            + Ext.util.Format.number(parseInt(storeItem.get('data')), "0,0")+')');

                                }
                            },
                            showInLegend: true,
                            highlight: true
                        }]
                    }]
                }]
           }]
        });
        grid = Ext.widget("gridlaporan");
        grid.setTitle("Details Report");
        chartnutsum.add(grid);
        panelReport.setHeight(220+(height*34));
        panelReport.add(chartnutsum);
        panelReport.doLayout();
    };

    //var createGrid = function(columndata,title) {
    var createGrid = function(title) {
        grid = Ext.widget("gridlaporan");
        grid.setTitle(title);
        panelReport.setHeight(220+(height*34));
        panelReport.add(grid);
        panelReport.doLayout();
    };

});
