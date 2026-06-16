Ext.onReady(function(){
    
    //Ext.get('et-content').hide();
    
    // document.getElementById('titlet').innerHTML = '<div><span id="jasper-menu-controller"></span><span style="margin-left: 10px; font-weight: bold; font-family: Arial; color: rgb(227, 227, 227);">Palmoiltrace Advanced Report</span></div>';
    /*
    Ext.create('Ext.Button',{
        xtype:'button',
        renderTo:'jasper-menu-controller',
        style:'background-image:url('+varjs.config.base_url+'images/menu-bars.png);background-size:cover;border:none; width:24px;',
        handler: function() {
            showHideMenu();
        }
    });
    */
    Ext.create('Ext.Panel',{
        renderTo:'ext-content',
        id:'panel-jasper-content',
        height:1145,
        loader: {
            url: m_report,
            autoLoad: true
        }
    });
    
    //showHideMenu();
    
    function showHideMenu() {
        // var pnl = Ext.getCmp('panel-jasper-content');
        //     document.getElementById('sidebar').classList.toggle('closed');
        //     document.getElementById('main').setAttribute('style','padding: 0 10px 0 10px;');
        //     if(document.getElementById('sidebar').classList.contains('closed')){
        //         pnl.setWidth(1329);
        //         document.getElementById('main').setAttribute('style','padding: 0 10px 0 10px;');
        //     } else {
        //         document.getElementById('main').removeAttribute("style");
        //         pnl.setWidth(1129);
        //     }
    }
    
});
