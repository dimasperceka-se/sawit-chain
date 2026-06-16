/******************************************
 *  Author : fikrifauzul@gmail.com
 *  Created On : 2020-11-18
 *  File : ViewDashboard.js
 *******************************************/
/*
 Param2 yg diperlukan ketika load View ini
 - DashID
 */

Ext.define('Koltiva.view.UserDashboard.ViewDashboard', {
    extend: 'Ext.panel.Panel',
    id: 'Koltiva.view.UserDashboard.ViewDashboard',
    style: 'padding:0 15px 15px 15px;margin:5px 0 0 0;',
    viewVar: false,
    setViewVar: function (value) {
        this.viewVar = value;
    },
    renderTo: 'ext-content',
    listeners: {
        afterRender: function () {
            var thisObj = this;
            document.getElementById('divCommonContentRegion').style.display = 'none';

            var tinggi = 150;
            var height = 0;
            if (screenfull.isFullscreen) {
                height = screen.height;
            } else {
                height = window.innerHeight - tinggi;
            }
            $('#wrapper').addClass('cover');
            $('#frame').hide();
            //load dashboard

            Ext.Ajax.request({
                waitMsg: lang('Please Wait'),
                url: m_api + '/user_dashboard/view_dashboard/' + thisObj.viewVar.DashID,
                method: 'GET',
//                params: {
//                    DashID: thisObj.viewVar.DashID
//                },
                success: function (response, opts) {
                    var r = Ext.decode(response.responseText);
//                    console.log(r);

                    Ext.getCmp('Koltiva.view.UserDashboard.ViewDashboard-PanelDashboard').setHeight(height);
                    Ext.getCmp('Koltiva.view.UserDashboard.ViewDashboard-PanelDashboard').update('<iframe id="frame" allowtransparency="" frameborder="0" src="' + r.url + '" style="display: none;background-color:white;width:100%;min-height: 600px;overflow-y: auto;margin: 0px;position: absolute;height:' + height + 'px"></iframe>');
                    Ext.getCmp('Koltiva.view.UserDashboard.ViewDashboard-PanelDashboard').doLayout();
                    $('#frame').show();
                    $('#frame').load(function () {
                        $('#wrapper').removeClass('cover');
                    });
                },
                failure: function (response, opts) {
                    Ext.MessageBox.show({
                        title: 'Failed',
                        msg: 'Failed to retrieve data',
                        buttons: Ext.MessageBox.OK,
                        animateTarget: 'mb9',
                        icon: 'ext-mb-error'
                    });
                }
            });
        }
    },
    initComponent: function () {
        var thisObj = this;
        var labelWidth = 250;

        thisObj.ObjPanelMain = Ext.create('Ext.panel.Panel', {
            frame: false,
            style: 'margin-top:0px;padding-top:0px;',
            items: [{
                    id: 'Koltiva.view.UserDashboard.ViewDashboard-PanelDashboard',
                    html: '<iframe id="frame" allowtransparency="" frameborder="0" src="" style="display: none;background-color:white;width:100%;min-height: 600px;overflow-y: auto;margin: 0px;position: absolute;"></iframe>'
                }]
        });

        //========================================================== LAYOUT UTAMA (Begin) ========================================//
        thisObj.items = [{
                xtype: 'panel',
                border: false,
                layout: {
                    type: 'hbox'
                },
                items: [{
//                        id: 'Koltiva.view.UserDashboard.ViewDashboard-labelInfoInsert',
//                        html: '<div id="header_title_farmer">' + lang('View Dashboard') + '</div>'
                    }]
            }, {
                items: [{
                        id: 'Koltiva.view.UserDashboard.ViewDashboard-LinkBackToList',
                        html: '<div id="Sfr_IdBoxInfoDataGrid" class="Sfr_BoxInfoDataGrid"><ul class="Sft_UlListInfoDataGrid"><li class="Sft_ListInfoDataGrid"><a href="javascript:Ext.getCmp(\'Koltiva.view.UserDashboard.ViewDashboard\').BackToList()"><img class="Sft_ListIconInfoDataGrid" src="' + varjs.config.base_url + 'images/icons/new/back.png" width="20" />&nbsp;&nbsp;' + lang('Back to List') + '</a></li></div>'
                    }]
            }, {
                html: '<br />'
            }, {
                layout: 'column',
                border: false,
                items: [{
                        //LEFT CONTENT
                        columnWidth: 1,
                        items: [
                            thisObj.ObjPanelMain
                        ]
                    }]
            }];
        //========================================================== LAYOUT UTAMA (End) ========================================//

        this.callParent(arguments);
    },
    BackToList: function () {
        Ext.getCmp('Koltiva.view.UserDashboard.ViewDashboard').destroy(); //destory current view
        var GridMainGrower = [];
        if (Ext.getCmp('Koltiva.view.UserDashboard.MainGrid') == undefined) {
            GridMainGrower = Ext.create('Koltiva.view.UserDashboard.MainGrid');
        } else {
            //destroy, create ulang
            Ext.getCmp('Koltiva.view.UserDashboard.MainGrid').destroy();
            GridMainGrower = Ext.create('Koltiva.view.UserDashboard.MainGrid');
        }
    }
});
