Ext.define('Koltiva.store.Basic.MillRefinery.List',{
    extend: 'Ext.data.TreeStore',
    storeId:'koltiva-Basic-MillRefinery-List',
    model: Koltiva.model.Basic.MillRefinery.List,
    autoLoad: true,
    pageSize: 50,
    proxy: {
        type: 'ajax',
        url: m_crud,
    },
    listeners: {
        append: function( thisNode, newChildNode, index, eOpts ) {
            newChildNode.set('icon', '../images/icons/silk/user_star.png');
        }
    },
    folderSort: true
});
