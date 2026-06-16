/*
* @Author: nikolius
* @Date:   2017-05-18 18:48:33
* @Last Modified by:   nikolius
* @Last Modified time: 2017-05-18 18:52:49
*/
Ext.define('Koltiva.store.Grower.CmbEducation', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.Grower.CmbEducation',
    fields: ['id', 'label'],
    data: [{
        "id": "1",
        "label": lang('No Education')
    },{
        "id": "2",
        "label": lang('Primary School Not Completed')
    },{
        "id": "3",
        "label": lang('Primary School Completed')
    },{
        "id": "4",
        "label": lang('Graduated Middle School')
    },{
        "id": "5",
        "label": lang('Graduated High School')
    },{
        "id": "6",
        "label": lang('Graduated College')
    },{
        "id": "7",
        "label": lang(' Magister/S2')
    },{
        "id": "8",
        "label": lang('Doctor/S3')
    }]
});