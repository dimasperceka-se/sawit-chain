/******************************************
 *  Author : n1colius.lau@gmail.com   
 *  Created On : Wed Feb 12 2020
 *  File : CmbAutoFarmer.js
 *******************************************/
Ext.define('Koltiva.store.ComboGeneral.CmbAutoFarmer', {
	extend: 'Ext.data.Store',
    id: 'Koltiva.store.ComboGeneral.CmbAutoFarmer',
    pageSize: 10,
    model: 'Koltiva.store.ComboGeneral.CmbAutoFarmerModel'
});