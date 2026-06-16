/*
* @Author: nikolius
* @Date:   2017-10-10 10:10:44
* @Last Modified by:   nikolius
* @Last Modified time: 2017-10-11 14:52:55
*/

/*
    Param2 yg diperlukan ketika load View ini

*/

// Define Variabel2 / Object2 / Fungsi yg diperlukan oleh view ini (begin)

// Define Variabel2 / Object2 / Fungsi yg diperlukan oleh view ini (end)

Ext.define('Koltiva.view.DataAdm.AdcMember.MainForm' ,{
    extend: 'Ext.panel.Panel',
    id: 'Koltiva.view.DataAdm.AdcMember.MainForm',
    style:'padding:0 15px 15px 15px;margin:12px 0 0 0;',
    viewVar: false,
    setViewVar: function(value){
        this.viewVar = value;
    },
    renderTo: 'ext-content',
    initComponent: function() {
        var thisObj = this;

        //Panel "Set by Member" ================================================================================= (begin)
        thisObj.objPanelSetByMember = Ext.create('Koltiva.view.DataAdm.AdcMember.PanelSetByMember');
        //Panel "Set by Member" ================================================================================= (end)

        //Panel "Set by Region" ================================================================================= (begin)
        thisObj.objPanelSetByRegion = Ext.create('Koltiva.view.DataAdm.AdcMember.PanelSetByRegion');
        //Panel "Set by Region" ================================================================================= (end)

        //Panel Member Not Assign Yet =================================================================== (begin)
        thisObj.objPanelMemberNotAssignYet = Ext.create('Koltiva.view.DataAdm.AdcMember.PanelMemberNotAssignYet');
        //Panel Member Not Assign Yet =================================================================== (end)

        // ==================================================== MAIN PANEL =============================================//
        thisObj.items = [{
            layout: 'column',
            border: false,
            items: [{
                columnWidth: 1,
                items:[
                    thisObj.objPanelSetByMember
                ]
            },{
                columnWidth: 1,
                items:[{
                    html: '<br /><div style="height:10px;border-bottom:1px dashed gray;"></div><br />'
                }]
            },{
                columnWidth: 1,
                items:[thisObj.objPanelSetByRegion]
            },{
                columnWidth: 1,
                items:[{
                    html: '<br /><div style="height:10px;border-bottom:1px dashed gray;"></div><br />'
                }]
            },{
                columnWidth: 1,
                items:[thisObj.objPanelMemberNotAssignYet]
            }]
        }];

        this.callParent(arguments);
    }
});