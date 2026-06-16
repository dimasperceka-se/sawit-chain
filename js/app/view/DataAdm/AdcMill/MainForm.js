/*
* @Author: nikolius
* @Date:   2017-10-11 16:36:21
* @Last Modified by:   nikolius
* @Last Modified time: 2017-10-11 18:17:33
*/

Ext.define('Koltiva.view.DataAdm.AdcMill.MainForm' ,{
    extend: 'Ext.panel.Panel',
    id: 'Koltiva.view.DataAdm.AdcMill.MainForm',
    style:'padding:0 15px 15px 15px;margin:12px 0 0 0;',
    viewVar: false,
    setViewVar: function(value){
        this.viewVar = value;
    },
    renderTo: 'ext-content',
    initComponent: function() {
        var thisObj = this;

        //Panel "Set by Mill" ================================================================================= (begin)
        thisObj.objPanelSetByMill = Ext.create('Koltiva.view.DataAdm.AdcMill.PanelSetByMill');
        //Panel "Set by Mill" ================================================================================= (end)

        //Panel "Set by Region" ================================================================================= (begin)
        thisObj.objPanelSetByRegion = Ext.create('Koltiva.view.DataAdm.AdcMill.PanelSetByRegion');
        //Panel "Set by Region" ================================================================================= (end)

        //Panel Member Not Assign Yet =================================================================== (begin)
        thisObj.objPanelMillNotAssignYet = Ext.create('Koltiva.view.DataAdm.AdcMill.PanelMillNotAssignYet');
        //Panel Member Not Assign Yet =================================================================== (end)

        // ==================================================== MAIN PANEL =============================================//
        thisObj.items = [{
            layout: 'column',
            border: false,
            items: [{
                columnWidth: 1,
                items:[
                    thisObj.objPanelSetByMill
                ]
            },{
                columnWidth: 1,
                items:[{
                    html: '<br /><div style="height:10px;border-bottom:1px dashed gray;"></div><br />'
                }]
            },{
                columnWidth: 1,
                items:[
                    thisObj.objPanelSetByRegion
                ]
            },{
                columnWidth: 1,
                items:[{
                    html: '<br /><div style="height:10px;border-bottom:1px dashed gray;"></div><br />'
                }]
            },{
                columnWidth: 1,
                items:[thisObj.objPanelMillNotAssignYet]
            }]
        }];

        this.callParent(arguments);
    }
});