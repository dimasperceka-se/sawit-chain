/******************************************
 *  Author : hasbycs@gmail.com
 *  Created On : 2021-10-06
 *  File : CoachingTask.js
 *******************************************/
Ext.define('Koltiva.store.Coaching.CoachingTask', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.Coaching.CoachingTask',
    storeId: 'Koltiva.store.Coaching.CoachingTask',
    fields: ['ActivityNCID', 'CoachingTopicID', 'Subtopic', 'CoachingTopic', 'UrgentlyStatus', 'Finding', 'ActivityType', 'Recommendation', 'Target', 'Deadline', 'Status', 'Remark'],
    pageSize: 20,
    autoLoad: true,
    storeVar: false,
    setStoreVar: function (value) {
        this.storeVar = value;
    },
    remoteSort: true,
    proxy: {
        type: 'ajax',
        url: m_api + '/coaching/coaching_task_grid',
        reader: {
            type: 'json',
            root: 'data',
            totalProperty: 'total'
        }
    },
    listeners: {
        beforeload: function (store, operation, options) {
            store.proxy.extraParams.CoachingID = this.storeVar.CoachingID;
        }
    }
});