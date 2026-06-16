/*
* @Author: nikolius
* @Date:   2016-08-22 10:57:51
* @Last Modified by:   nikolius
* @Last Modified time: 2016-08-22 11:26:50
*/
Ext.onReady(function(){
    Ext.tip.QuickTipManager.init();

    //store staff =============== (begin)
    Ext.define('staff.Model', {
        extend: 'Ext.data.Model',
        fields: ['SceID', 'StaffID', 'UserId', 'StaffName', 'Phone', 'Email', 'StaffBirthday', 'StaffGender', 'StaffGende', 'Position'],
    });
    var store_staff = Ext.create('Ext.data.Store', {
        model: 'staff.Model',
        autoLoad: true,
        pageSize: 25,
        proxy: {
            type: 'ajax',
            url: m_get_staff,
            reader: {
                type: 'json',
                root: 'data',
                totalProperty: 'total'
            }
        }
    });
    //store staff =============== (end)

    var grid = Ext.create('Ext.grid.Panel', {
        store: store_staff,
        width: '100%',
        id:'grid',
        style: 'border:1px solid #CCC;',
        loadMask: true,
        selType: 'rowmodel',
        dockedItems: [{
            xtype: 'pagingtoolbar',
            store: store_staff,   // same store GridPanel is using
            dock: 'bottom',
            displayInfo: true
        }],
        columns: [{
            hidden: true,
            dataIndex: 'UserId'
        }, {
            text: lang('ID'),
            dataIndex: 'StaffID',
            width: '5%'
        }, {
            text: lang('Nama Staff'),
            dataIndex: 'StaffName',
            width: '30%'
        }, {
            text: lang('Position'),
            dataIndex: 'Position',
            width: '10%'
        }, {
            text: lang('Phone'),
            dataIndex: 'Phone',
            width: '15%'
        }, {
            text: lang('Email'),
            dataIndex: 'Email',
            width: '20%'
        }, {
            text: lang('Birthday'),
            dataIndex: 'StaffBirthday',
            width: '10%'
        }, {
            text: lang('Kelamin'),
            dataIndex: 'StaffGende',
            width: '10%'
        }],
        renderTo: 'ext-content'
    });

});