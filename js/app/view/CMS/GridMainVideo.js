/******************************************
 *  Author : n1colius.lau@gmail.com   
 *  Created On : Wed Sep 12 2018
 *  File : GridMainVideo.js
 *******************************************/

Ext.define('Koltiva.view.CMS.GridMainVideo' ,{
    extend: 'Ext.container.Container',
    id: 'Koltiva.view.CMS.GridMainVideo',
    margin: '15px 15px 15px 15px',        
    renderTo: 'ext-content',    
    viewVar: false,
    setViewVar: function(value){
        this.viewVar = value;
	},	
	LimitItem: 6,
	html:'',
    listeners: {
        afterRender: function(){
            var thisObj = this;			
			thisObj.LoadVideoContent(1);			
        }
	},	
	LoadVideoContent: function(PageInfo){
		var thisObj = this;

		//Get Load Content
		Ext.Ajax.request({
			waitMsg: lang('Please Wait'),
			url: m_api + '/cms/video_content',
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
					var ElBtnAdd = document.getElementsByClassName('Koltiva.view.CMS.GridMainVideo-BtnAdd');
					for (i = 0; i < ElBtnAdd.length; i ++) {
						ElBtnAdd[i].style.display = 'block';
					}
				}
				if(m_act_update == true) {
					var ElBtnUpdate = document.getElementsByClassName('Koltiva.view.CMS.GridMainVideo-BtnUpdate');
					for (i = 0; i < ElBtnUpdate.length; i ++) {
						ElBtnUpdate[i].style.display = 'block';
					}
				}
				if(m_act_delete == true) {
					var ElBtnDelete = document.getElementsByClassName('Koltiva.view.CMS.GridMainVideo-BtnDelete');
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
	NextVideo: function(){
		var thisObj = this;
		var PageInfo = document.getElementById('Koltiva.view.CMS.GridMainVideo-NextPageInfo').value;
		thisObj.LoadVideoContent(PageInfo);		
	},
	PrevVideo: function(){
		var thisObj = this;
		var PageInfo = document.getElementById('Koltiva.view.CMS.GridMainVideo-PrevPageInfo').value;
		thisObj.LoadVideoContent(PageInfo);		
	},
	PlayVideo: function(VidID){
		var WinFormVideoPlay = Ext.create('Koltiva.view.CMS.WinFormVideoPlay', {
            viewVar: {
                VidID: VidID
            }
        });
        if (!WinFormVideoPlay.isVisible()) {
            WinFormVideoPlay.center();
            WinFormVideoPlay.show();
        } else {
            WinFormVideoPlay.close();
        }
	},
	NewVideo: function(){
		var thisObj = this;
		
		var WinFormVideo = Ext.create('Koltiva.view.CMS.WinFormVideo', {
            viewVar: {
				OpsiDisplay: 'insert',
				VidID: null
            }
        });
        if (!WinFormVideo.isVisible()) {
            WinFormVideo.center();
            WinFormVideo.show();
        } else {
            WinFormVideo.close();
        }
	},
	UpdateVideo: function(VidID){
		var thisObj = this;
		
		var WinFormVideo = Ext.create('Koltiva.view.CMS.WinFormVideo', {
            viewVar: {
				OpsiDisplay: 'update',
				VidID: VidID
            }
        });
        if (!WinFormVideo.isVisible()) {
            WinFormVideo.center();
            WinFormVideo.show();
        } else {
            WinFormVideo.close();
        }
	},
	DeleteVideo: function(VidID){
		var thisObj = this;

		Ext.MessageBox.confirm('Message', 'Do you want to delete this data ?', function(btn) {
			if (btn == 'yes') {
				Ext.Ajax.request({
					waitMsg: 'Please Wait',
					url: m_api + '/cms/video',
					method: 'DELETE',
					params: {
						VidID: VidID
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
						thisObj.LoadVideoContent(1);
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