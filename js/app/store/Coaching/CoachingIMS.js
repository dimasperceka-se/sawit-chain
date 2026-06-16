/******************************************
 *  Author : hasbycs@gmail.com
 *  Created On : 2021-10-06
 *  File : CoachingTask.js
 *******************************************/
Ext.define('Koltiva.store.Coaching.CoachingIMS', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.Coaching.CoachingIMS',
    storeId: 'Koltiva.store.Coaching.CoachingIMS',
    fields: ['id', 'label'],
    pageSize: 20,
    autoLoad: true,
    storeVar: false,
    setStoreVar: function (value) {
        this.storeVar = value;
    },
    remoteSort: true,
    proxy: {
        type: 'ajax',
        url: m_api + '/coaching/coaching_ims',
        reader: {
            type: 'json',
            root: 'data',
            totalProperty: 'total'
        }
    }
});