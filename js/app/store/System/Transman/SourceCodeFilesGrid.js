/******************************************
 *  Author : n1colius.lau@gmail.com   
 *  Created On : Mon Sep 21 2020
 *  File : SourceCodeFilesGrid.js
 *******************************************/
Ext.define('Koltiva.store.System.Transman.SourceCodeFilesGrid', {
    extend: 'Ext.data.Store',
    id: 'Koltiva.store.System.Transman.SourceCodeFilesGrid',
    storeId: 'Koltiva.store.System.Transman.SourceCodeFilesGrid',
    fields: ['TransManID','FilePath','IsFileExist','OptionInput'],
    autoLoad: true,
    storeVar: false,
    setStoreVar: function(value){
        this.storeVar = value;
    },
    proxy: {
        type: 'ajax',
        url: m_api + '/transman/source_code_files_grid',
        reader: {
            type: 'json',
            root: 'data'
        }
    },
    listeners: {
        beforeload: function(store, operation, options) {
            store.proxy.extraParams.TransManID = this.storeVar.TransManID;
        }
    }
});