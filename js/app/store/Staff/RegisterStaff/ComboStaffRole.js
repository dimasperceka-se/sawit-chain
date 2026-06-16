/*
* @Author: nikolius
* @Date:   2017-10-13 14:41:09
* @Last Modified by:   nikolius
* @Last Modified time: 2017-10-13 14:43:12
*/

Ext.define('Koltiva.store.Staff.RegisterStaff.ComboStaffRole', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.Staff.RegisterStaff.ComboStaffRole',
    fields: ['id', 'label'],
    data: [{
        "id": "program",
        "label": "Program"
    }, {
        "id": "private",
        "label": "Private"
    }, {
        "id": "service",
        "label": "Service Provider"
    }, {
        "id": "mill",
        "label": "Mill"
    },{
        "id": "agent",
        "label": "Agent"
    }]
});