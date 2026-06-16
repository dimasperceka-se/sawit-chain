/******************************************
 *  Author : n1colius.lau@gmail.com   
 *  Created On : Fri Sep 14 2018
 *  File : GridMainNews.js
 *******************************************/

Ext.define('Koltiva.view.CMS.GridMainNews' ,{
    extend: 'Ext.container.Container',
    id: 'Koltiva.view.CMS.GridMainNews',
    margin: '15px 15px 15px 15px',        
    renderTo: 'ext-content',    
    viewVar: false,
    setViewVar: function(value){
        this.viewVar = value;
	},	
	LimitItem: 5,
    html:'',
    listeners: {
        afterRender: function(){
            var thisObj = this;
            thisObj.LoadNewsContent(1);            
        }
    },
    LoadNewsContent: function(PageInfo){
        var thisObj = this;

        //Get Load Content
		Ext.Ajax.request({
			waitMsg: lang('Please Wait'),
			url: m_api + '/cms/news_content',
			method : 'GET',
			params: {
				page:  PageInfo,
				limit: thisObj.LimitItem
			},
			success: function(response, opts){
				var r = response.responseText, i;				
				//console.log(r);

				thisObj.update(r);
				thisObj.doComponentLayout();

                //Cek Hak Akses
                if(m_act_add == true) {
					var ElBtnAdd = document.getElementsByClassName('Koltiva.view.CMS.GridMainNews-BtnAdd');
					for (i = 0; i < ElBtnAdd.length; i ++) {
						ElBtnAdd[i].style.display = 'block';
					}
				}
				if(m_act_update == true) {
					var ElBtnUpdate = document.getElementsByClassName('Koltiva.view.CMS.GridMainNews-BtnUpdate');
					for (i = 0; i < ElBtnUpdate.length; i ++) {
						ElBtnUpdate[i].style.display = 'block';
					}
				}
				if(m_act_delete == true) {
					var ElBtnDelete = document.getElementsByClassName('Koltiva.view.CMS.GridMainNews-BtnDelete');
					for (i = 0; i < ElBtnDelete.length; i ++) {
						ElBtnDelete[i].style.display = 'block';						
					}
				}
			},
			failure: function(response, opts){
				Ext.MessageBox.show({
					title: 'Failed',
					msg: 'Failed to retrieve data',
					buttons: Ext.MessageBox.OK,
					animateTarget: 'mb9',
					icon: 'ext-mb-error'
				});
			}
		});
    },
    NextNews: function(){
        var thisObj = this;
		var PageInfo = document.getElementById('Koltiva.view.CMS.GridMainNews-NextPageInfo').value;
		thisObj.LoadNewsContent(PageInfo);
    },
    PrevNews: function(){
        var thisObj = this;
		var PageInfo = document.getElementById('Koltiva.view.CMS.GridMainNews-PrevPageInfo').value;
		thisObj.LoadNewsContent(PageInfo);
    },
    NewNews: function(){
        var thisObj = this;		
		var WinFormNews = Ext.create('Koltiva.view.CMS.WinFormNews', {
            viewVar: {
				OpsiDisplay: 'insert',
				NewsID: null
            }
        });
        if (!WinFormNews.isVisible()) {
            WinFormNews.center();
            WinFormNews.show();
        } else {
            WinFormNews.close();
        }
    },
    UpdateNews: function(NewsID){
        var thisObj = this;		
		var WinFormNews = Ext.create('Koltiva.view.CMS.WinFormNews', {
            viewVar: {
				OpsiDisplay: 'update',
				NewsID: NewsID
            }
        });
        if (!WinFormNews.isVisible()) {
            WinFormNews.center();
            WinFormNews.show();
        } else {
            WinFormNews.close();
        }
    },
    DeleteNews: function(NewsID){
        var thisObj = this;

        Ext.MessageBox.confirm('Message', 'Do you want to delete this data ?', function(btn) {
			if (btn == 'yes') {
				Ext.Ajax.request({
					waitMsg: 'Please Wait',
					url: m_api + '/cms/news',
					method: 'DELETE',
					params: {
						NewsID: NewsID
					},
					success: function(response, opts) {
						Ext.MessageBox.show({
							title: 'Information',
							msg: lang('Data deleted'),
							buttons: Ext.MessageBox.OK,
							animateTarget: 'mb9',
							icon: 'ext-mb-success'
						});

						//refresh store
						thisObj.LoadNewsContent(1);
					},
					failure: function(response, o) {
						var pesanNya;
						if(o.result.message != undefined){
							pesanNya = o.result.message;
						}else{
							pesanNya = lang('Connection error');
						}
						Ext.MessageBox.show({
							title: 'Error',
							msg: pesanNya,
							buttons: Ext.MessageBox.OK,
							animateTarget: 'mb9',
							icon: 'ext-mb-error'
						});
					}
				});
			}
		});
    }
});