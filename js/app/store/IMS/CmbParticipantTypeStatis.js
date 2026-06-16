/******************************************
 *  Author : n1colius.lau@gmail.com   
 *  Created On : Tue Dec 04 2018
 *  File : CmbParticipantTypeStatis.js
 *******************************************/

Ext.define('Koltiva.store.IMS.CmbParticipantTypeStatis', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.IMS.CmbParticipantTypeStatis',
    fields: ['id', 'label'],
    data: [{
        "id": "Applicant",
        "label": lang('Applicant')
    },{
        "id": "Existing Farmer",
        "label": lang('Existing Farmer')
    },{
        "id": "3",
        "label": lang('Existing Certified Farmer')
    }]
});