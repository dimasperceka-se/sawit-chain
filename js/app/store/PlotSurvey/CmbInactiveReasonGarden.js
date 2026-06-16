/******************************************
 *  Author : n1colius.lau@gmail.com   
 *  Created On : Tue May 07 2019
 *  File : CmbInactiveReasonGarden.js
 *******************************************/
Ext.define('Koltiva.store.PlotSurvey.CmbInactiveReasonGarden', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.PlotSurvey.CmbInactiveReasonGarden',
    fields: ['id', 'label'],
    data: [{
        "id": "1",
        "label": lang('Died')
    },{
    	"id": "2",
        "label": lang('Moved/left the area')
    },{
    	"id": "3",
        "label": lang('Switched to other crop')
    },{
    	"id": "4",
        "label": lang('Sold the land')
    },{
    	"id": "5",
        "label": lang('Gave the land to family member')
    },{
    	"id": "6",
        "label": lang('Force Major')
    }]
});