/******************************************
 *  Author : n1colius.lau@gmail.com   
 *  Created On : Thu Apr 25 2019
 *  File : CmbMembershipStatus.js
 *******************************************/

Ext.define('Koltiva.store.Grower.CmbMembershipStatus', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.Grower.CmbMembershipStatus',
    fields: ['id', 'label'],
    data: [{
        "id": "1",
        "label": lang('Potential')
    },{
        "id": "2",
        "label": lang('Provisional')
    },{
        "id": "3",
        "label": lang('Member')
    },{
        "id": "4",
        "label": lang('Member - Certified')
    },{
        "id": "5",
        "label": lang('Member - Suspended')
    },{
        "id": "6",
        "label": lang('Resigned')
    },{
        "id": "7",
        "label": lang('Withdrawn')
    }]
});