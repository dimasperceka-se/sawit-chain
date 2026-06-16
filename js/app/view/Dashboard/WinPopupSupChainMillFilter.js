/******************************************
 *  Author : n1colius.lau@gmail.com   
 *  Created On : Fri Aug 16 2019
 *  File : WinPopupSupChainMillFilter.js
 *******************************************/
/*
    Param2 yg diperlukan ketika load View ini
    - PartnerID
*/

Ext.define('Koltiva.view.Dashboard.WinPopupSupChainMillFilter' ,{
    extend: 'Ext.window.Window',
    id: 'Koltiva.view.Dashboard.WinPopupSupChainMillFilter',
    title: lang('Filter Partner'),
    closable: true,
    modal: true,
    closeAction: 'destroy',
    width: '40%',
    height: '80%',
    overflowY: 'auto',
    viewVar: false,
    setViewVar: function(value){
        this.viewVar = value;
    },
    listeners: {
        afterRender: function(){
            var thisObj = this;
        }
    },
    initComponent: function() {
        var thisObj = this;

        var PanelGridPartnerHirar = Ext.create('Koltiva.view.Dashboard.PanelGridPartnerHirar', {
            viewVar: {
                PartnerID: thisObj.viewVar.PartnerID
            }
        });

        thisObj.items = [PanelGridPartnerHirar];

        //buttons -------------------------------------------------------------- (begin)
        thisObj.buttons = [{
            text: lang('Search'),
            margin: '5 15 5 5',
            scale: 'large',
            ui: 's-button',
            cls: 's-blue',
            id: 'Koltiva.view.Dashboard.WinPopupSupChainMillFilter-BtnFilter',
            handler: function () {
                //document.getElementById("SupChainMillPartnerIDFilter").value = "1,2,3";
                //runFilter();

                var gridSelected = Ext.getCmp('Koltiva.view.Dashboard.PanelGridPartnerHirar').getSelectionModel().getSelection();
                var IdSelectedArr = [];
                for (var i = gridSelected.length - 1; i >= 0; i--) {
                    IdSelectedArr.push(gridSelected[i].get('PartnerID'));
                }
                console.log(IdSelectedArr);

                if(IdSelectedArr.length > 0){
                    document.getElementById("SupChainMillPartnerIDFilter").value = IdSelectedArr.join();
                    document.getElementById("SupChainMillFirstLoad").value = "2";
                    runFilter();

                    thisObj.close();
                }else{
                    Ext.MessageBox.show({
                        title: 'Information',
                        msg: lang('No data selected'),
                        buttons: Ext.MessageBox.OK,
                        animateTarget: 'mb9',
                        icon: 'ext-mb-info'
                    });
                }
            }
        }];
        //buttons -------------------------------------------------------------- (end)

        this.callParent(arguments);
    }
});