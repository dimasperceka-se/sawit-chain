/******************************************
 *  Author : n1colius.lau@gmail.com   
 *  Created On : Fri Sep 18 2020
 *  File : MainGrid.js
 *******************************************/
Ext.define('Koltiva.store.System.Transman.MainGrid', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.System.Transman.MainGrid',
    storeId: 'Koltiva.store.System.Transman.MainGrid',
    fields: ['TransManID','ModuleName','ModuleDescription','FilesCount','KeysCount','ProgramMobile'],
    autoLoad: true,
    storeVar: false,
    setStoreVar: function(value){
        this.storeVar = value;
    },
    proxy: {
        type: 'ajax',
        url: m_api + '/transman/main_grid',
        reader: {
            type: 'json',
            root: 'data'
        }
    }
});