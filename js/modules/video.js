Ext.onReady(function() {
    Ext.tip.QuickTipManager.init();
    Ext.define('Scpp.Model', {
        extend: 'Ext.data.Model',
        fields: ['VideoId', 'VideoFile', 'VideoTitle', 'VideoDescription', 'VideoThumbnail', 'DateCreated', 'UserName'],
    });
    var store = Ext.create('Ext.data.Store', {
        model: 'Scpp.Model',
        autoLoad: true,
        pageSize: 50,
        proxy: {
            type: 'ajax',
            url: m_crud + 's',
            params: {
                'X-API-KEY': '030584'
            },
            reader: {
                type: 'json',
                root: 'data',
                totalProperty: 'total'
            }
        }
    });
    function submitOnEnter(field, event) {
        if (event.getKey() == event.ENTER) {
            store.load({
                params: {
                    key: Ext.getCmp('key').getValue()
                }});
        }
    }

    function resetForm() {
        Ext.getCmp('id').setValue('');
        Ext.getCmp('current_file').setValue('');
        Ext.getCmp('current_file_size').setValue('');
        Ext.getCmp('current_file_label').setValue('');
        Ext.getCmp('current_thumbnail').setValue('');
        Ext.getCmp('current_thumbnail_label').setValue('');
        Ext.getCmp('file').setValue('');
        Ext.getCmp('title').setValue('');
        Ext.getCmp('desc').setValue('');
    }

    Ext.create('Ext.Panel', {
        store: store,
        width: '100%',
        id: 'images-view',
        minHeight: 500,
        renderTo: 'ext-content',
        frame: false,
        loadMask: true,
        style: 'border:1px solid #999999;',
        dockedItems: [{
                xtype: 'pagingtoolbar',
                store: store, // same store GridPanel is using
                dock: 'bottom',
                hidden: true,
                displayInfo: true
            }, {
                xtype: 'toolbar',
                items: [{
                        xtype: 'button',
                        icon: varjs.config.base_url + 'images/icons/silk/disk_upload.png',
                        text: 'Upload',
                        handler: function() {
                            displayFormWindow();
                            Ext.getCmp('current_thumbnail').hide();
                            Ext.getCmp('current_thumbnail_label').hide();
                            Ext.getCmp('current_file').hide();
                            Ext.getCmp('current_file_label').hide();
                        }
                    }, {
                        name: 'key', baseCls:'Sfr_TxtfieldSearchGrid',
                        id: 'key',
                        xtype: 'textfield',
                        listeners: {
                            specialkey: submitOnEnter
                        }
                    }, {
                        xtype: 'button',
                        icon: varjs.config.base_url + 'images/icons/silk/search.png',
                        margin: '0px 0px 0px -10px',
                        text: 'Search',
                        handler: function() {
                            store.load({
                                params: {
                                    key: Ext.getCmp('key').getValue()
                                }});
                        }
                    }]
            }, {
                xtype: 'toolbar',
                id: 'toolbar_dua',
                hidden: true,
                items: [{
                        xtype: 'textfield',
                        hidden: true,
                        id: 'video_id'
                    }, {
                        xtype: 'textfield',
                        hidden: true,
                        id: 'video_name'
                    }, {
                        xtype: 'textfield',
                        hidden: true,
                        id: 'video_thumb'
                    }, {
                        xtype: 'textfield',
                        hidden: true,
                        id: 'video_title'
                    }, {
                        xtype: 'textfield',
                        hidden: true,
                        id: 'video_oleh'
                    }, {
                        xtype: 'textfield',
                        hidden: true,
                        id: 'video_tanggal'
                    }, {
                        xtype: 'button',
                        icon: varjs.config.base_url + 'images/icons/silk/play_blue.png',
                        text: 'Play',
                        handler: function() {
                            //console.log(m_api+'files/playvideo/vid/'+Ext.getCmp('video_name').getValue());
                            preview_video(m_api + 'files/video/' + Ext.getCmp('video_name').getValue());
                            //playFormWindow();
                            //Ext.getCmp('panelVideo').update('<div class="flowplayer" data-swf="flowplayer.swf" data-ratio="0.4167"><video><source type="video/webm" src="http://stream.flowplayer.org/bauhaus/624x260.webm"><source type="video/mp4" src="http://stream.flowplayer.org/bauhaus/624x260.mp4"><source type="video/ogv" src="http://stream.flowplayer.org/bauhaus/624x260.ogv"></video></div>');
                            //Ext.getCmp('label').setText(Ext.getCmp('video_title').getValue()+' diupload oleh '+
                            //Ext.getCmp('video_oleh').getValue()+' pada '+Ext.getCmp('video_tanggal').getValue());
                        }
                    }, {
                        itemId: 'download',
                        icon: varjs.config.base_url + 'images/icons/silk/disk_download.png',
                        text: 'Download',
                        scope: this,
                        hidden: true,
                        handler: function() {
                            window.location = m_api + 'files/video/' + Ext.getCmp('video_name').getValue();
                        }
                    }, {
                        itemId: 'edit',
                        icon: varjs.config.base_url + 'images/icons/new/update.png',
                        text: lang('Update'),
                        cls: m_act_update,
                        method: 'GET',
                        scope: this,
                        handler: function() {
                            displayFormWindow();
                            Ext.Ajax.request({
                                url: m_crud,
                                method: 'GET',
                                params: {
                                    id: Ext.getCmp('video_id').getValue()
                                },
                                success: function(fp, o) {
                                    var r = Ext.decode(fp.responseText);
                                    var id = Ext.getCmp('video_id').getValue();

                                    Ext.getCmp('current_thumbnail').show();
                                    Ext.getCmp('current_thumbnail_label').show();
                                    Ext.getCmp('current_file').show();
                                    Ext.getCmp('current_file_label').show();

                                    Ext.getCmp('id').setValue(id);

                                    Ext.getCmp('current_file').setValue(r.VideoFile);
                                    Ext.getCmp('current_file_size').setValue(r.VideoSize);
                                    Ext.getCmp('current_file_label').setValue(r.VideoFile);
                                    Ext.getCmp('title').setValue(r.VideoTitle);
                                    Ext.getCmp('desc').setValue(r.VideoDescription);
                                    Ext.getCmp('current_thumbnail').setValue(r.VideoThumbnail);
                                    Ext.getCmp('current_thumbnail_label').setValue(r.VideoThumbnail);
                                }
                            });
                        }
                    }, {
                        itemId: 'detail',
                        icon: varjs.config.base_url + 'images/icons/silk/detail.png',
                        text: lang('Detail'),
//                        cls: m_act_update,
                        method: 'GET',
                        scope: this,
                        handler: function() {
                            detailFormWindow();
                            Ext.Ajax.request({
                                url: m_crud,
                                method: 'GET',
                                params: {
                                    id: Ext.getCmp('video_id').getValue()
                                },
                                success: function(fp, o) {
                                    var r = Ext.decode(fp.responseText);
                                    var id = Ext.getCmp('video_id').getValue();

                                    Ext.getCmp('file_detail').setValue(r.VideoFile);
                                    Ext.getCmp('title_detail').setValue(r.VideoTitle);
                                    Ext.getCmp('desc_detail').setValue(r.VideoDescription);
                                    Ext.getCmp('thumbnail_detail').setValue(r.VideoThumbnail);
                                }
                            });
                        }
                    }, {
                        itemId: 'remove',
                        icon: varjs.config.base_url + 'images/icons/new/delete.png',
                        cls: m_act_delete,
                        text: 'Hapus',
                        scope: this,
                        handler: function() {
                            Ext.MessageBox.confirm('Message', 'Apakah anda mau menghapus data ini ?', function(btn) {
                                if (btn == 'yes') {
                                    Ext.Ajax.request({
                                        waitMsg: 'Please Wait',
                                        url: m_crud,
                                        method: 'DELETE',
                                        params: {id: Ext.getCmp('video_id').getValue(), name: Ext.getCmp('video_name').getValue(),
                                            thumb: Ext.getCmp('video_thumb').getValue()},
                                        success: function(response, opts) {
                                            var obj = Ext.decode(response.responseText);
                                            switch (obj.success) {
                                                case true:
                                                    store.load();
                                                    break;
                                                default:
                                                    Ext.MessageBox.alert('Warning', obj.message);
                                                    break;
                                            }
                                        },
                                        failure: function(response, opts) {
                                            var obj = Ext.decode(response.responseText);
                                            Ext.MessageBox.alert('error', 'Could not connect to the database. Retry later');
                                        }
                                    });
                                }
                            });
                        }
                    }]
            }],
        items: Ext.create('Ext.view.View', {
            store: store,
            tpl: [
                '<ul>',
                '<tpl for=".">',
                '<li class="thumbnail-style thumbnail-kenburn" style="font-size:14px;position:relative;margin:10px;width:250px;height:300px;overflow:hidden;float:left">',
                '<div class="thumb-wrap" id="{VideoId}" style="width:250px;height:300px;">',
                '<div class="overflow-hidden">',
                '<img alt="{VideoTitle:htmlEncode}" src="' + m_api + 'files/video/{VideoThumbnail}" title="{VideoTitle:htmlEncode}">',
                '</div>',
                '<h3 style="line-height:1;margin-bottom:5px"><a style="line-height:1" href="#" class="hover-effect">{VideoTitle:htmlEncode}</a></h3>',
                '<ul style="font-size:11px;margin-bottom:3px" class="unstyled inline blog-info">',
                '<li style="padding:0"><i class="icon-calendar"></i> {DateCreated}&nbsp;&nbsp;&nbsp;<i class="icon-edit"></i> {UserName}</li>',
                '</ul>',
                '<p>{VideoDescription:htmlEncode}</p>',
                '</div>',
                '</li>',
                '</tpl>',
                '</ul>',
                '<div class="x-clear"></div>'
            ],
            multiSelect: false,
            minHeight: 500,
            trackOver: true,
            overItemCls: 'x-item-over',
            itemSelector: 'div.thumb-wrap',
            emptyText: 'No images to display',
            plugins: [
                Ext.create('Ext.ux.DataView.DragSelector', {}),
                Ext.create('Ext.ux.DataView.LabelEditor', {dataIndex: 'name'})
            ],
            prepareData: function(data) {
                Ext.apply(data, {
                    shortName: Ext.util.Format.ellipsis(data.name, 15),
                    sizeString: Ext.util.Format.fileSize(data.size),
                    dateString: Ext.util.Format.date(data.lastmod, "m/d/Y g:i a")
                });
                return data;
            },
            listeners: {
                selectionchange: function(dv, nodes) {
                    if (nodes.length != 1)
                        Ext.getCmp('toolbar_dua').setVisible(false);
                    else {
                        Ext.getCmp('toolbar_dua').setVisible(true);
                        Ext.getCmp('video_id').setValue(nodes[0].data.VideoId);
                        Ext.getCmp('video_name').setValue(nodes[0].data.VideoFile);
                        Ext.getCmp('video_thumb').setValue(nodes[0].data.VideoThumb);
                        Ext.getCmp('video_title').setValue(nodes[0].data.VideoTitle);
                        Ext.getCmp('video_oleh').setValue(nodes[0].data.UserName);
                        Ext.getCmp('video_tanggal').setValue(nodes[0].data.DateCreated);
                    }
                }
            }
        })
    });

    function displayFormWindow() {
        if (!win.isVisible()) {
            resetForm();
            win.show();
        } else {
            win.hide(this, function() {
            });
            win.toFront();
        }
    }

    var DataForm = Ext.create('Ext.form.Panel', {
        frame: false,
        height: 250,
        autoScroll: true,
        width: 580,
        bodyPadding: 5,
        id: 'dataForm',
        fileUpload: true,
        enctype: 'multipart/form-data',
        id:'upload',
                fieldDefaults: {
            labelAlign: 'left',
            labelWidth: 100,
            anchor: '100%'
        },
        items: [
            {
                xtype: 'textfield',
                id: 'id',
                name: 'id',
                inputType: 'hidden'
            }, {
                xtype: 'displayfield',
                fieldLabel: 'Current File',
                id: 'current_file_label',
                name: 'current_file_label'
            }, {
                xtype: 'textfield',
                id: 'current_file',
                name: 'current_file',
                inputType: 'hidden'
            }, {
                xtype: 'textfield',
                id: 'current_file_size',
                name: 'current_file_size',
                inputType: 'hidden'
            }, {
                xtype: 'fileuploadfield',
                fieldLabel: 'Video',
                id: 'file',
                name: 'file',
                buttonText: 'Browse'
            }, {
                xtype: 'textfield',
                fieldLabel: 'Title',
                id: 'title',
                name: 'title'
            }, {
                xtype: 'textareafield',
                fieldLabel: 'Description',
                id: 'desc',
                name: 'desc'
            }, {
                xtype: 'displayfield',
                fieldLabel: 'Current Thumbnail',
                id: 'current_thumbnail_label',
                name: 'current_thumbnail_label'
            }, {
                xtype: 'textfield',
                id: 'current_thumbnail',
                name: 'current_thumbnail',
                inputType: 'hidden'
            }, {
                xtype: 'fileuploadfield',
                fieldLabel: 'Thumbnail',
                id: 'thumb',
                name: 'thumb',
                buttonText: 'Browse'
            }],
        buttons: [{
                id: 'saveButton',
                text: 'Save',
                margin: '5px',
                scale: 'large',
                ui: 's-button',
                cls: 's-blue',
                handler: function() {
                    var form = Ext.getCmp('upload').getForm();
                    var methode;
                    if (Ext.getCmp('id').getValue() == '')
                        methode = 'POST';
                    else
                        methode = 'POST';
                    form.submit({
                        url: m_crud,
                        method: 'POST',
                        waitMsg: 'Sending files....',
                        success: function(fp, o) {
                            Ext.MessageBox.alert('Success', 'Data saved.');
                            store.load();
                        },
                        failure: function(fp, o) {
                            Ext.MessageBox.show({
                                title: 'Failed',
                                msg: lang('Upload failed, make sure the file type is valid'),
                                buttons: Ext.MessageBox.OK,
                                animateTarget: 'mb9',
                                icon: 'ext-mb-error'
                            });
                        }
                    });
                    win.hide(this, function() {
                        store.load();
                    });
                }
            }, {
                text: 'Close',
                margin: '5px',
                scale: 'large',
                ui: 's-button',
                cls: 's-grey',
                disabled: false,
                handler: function() {
                    win.hide();
                }
            }]
    });
    var win = Ext.create('widget.window', {
        title: 'Upload Video',
        id: 'win',
        closable: true,
        modal: true,
        closeAction: 'hide',
        width: 600,
        height: 300,
        layout: {
            type: 'border',
            padding: 5
        },
        items: [DataForm]
    });

    var DataDetail = Ext.create('Ext.form.Panel', {
        frame: false,
        height: 250,
        autoScroll: true,
        width: 580,
        bodyPadding: 5,
        id: 'dataDetail',
        items: [{
                xtype: 'displayfield',
                fieldLabel: 'Current File',
                id: 'file_detail',
                name: 'file_detail'
            }, {
                xtype: 'displayfield',
                fieldLabel: 'Title',
                id: 'title_detail',
                name: 'title_detail'
            }, {
                xtype: 'displayfield',
                fieldLabel: 'Description',
                id: 'desc_detail',
                name: 'desc_detail'
            }, {
                xtype: 'displayfield',
                fieldLabel: 'Thumbnail',
                id: 'thumbnail_detail',
                name: 'thumbnail_detail'
            }],
        buttons: [{
                text: 'Close',
                margin: '5px',
                scale: 'large',
                ui: 's-button',
                cls: 's-grey',
                disabled: false,
                handler: function() {
                    windetail.hide();
                }
            }]
    });

    var windetail = Ext.create('widget.window', {
        title: 'Detail Video',
        id: 'windetail',
        closable: true,
        modal: true,
        closeAction: 'hide',
        width: 600,
        height: 300,
        layout: {
            type: 'border',
            padding: 5
        },
        items: [DataDetail]
    });

    function detailFormWindow() {
        if (!windetail.isVisible()) {
            windetail.show();
        } else {
            windetail.hide(this, function() {
            });
            windetail.toFront();
        }
    }

    function playFormWindow() {
        if (!winplay.isVisible()) {
            winplay.show();
        } else {
            winplay.hide(this, function() {
            });
            winplay.toFront();
        }
    }

    var PlayForm = Ext.create('Ext.Panel', {
        frame: false,
        height: 250,
        autoScroll: true,
        width: 550,
        bodyPadding: 5,
        id: 'playForm',
        layout: 'fit',
        items: [{
                xtype: 'container',
                id: 'panelVideo',
                flex: 1,
                html: '<div class="flowplayer" data-swf="flowplayer.swf" data-ratio="0.4167"><video><source type="video/webm" src="http://stream.flowplayer.org/bauhaus/624x260.webm"><source type="video/mp4" src="http://stream.flowplayer.org/bauhaus/624x260.mp4"><source type="video/ogv" src="http://stream.flowplayer.org/bauhaus/624x260.ogv"></video></div>'
            }]
                // ,
                // buttons: [{
                //      text: 'Close',
                //      margin: '0px 0px 0px 6px',
                //      scale: 'large',
                //      ui: 's-button',
                //      cls: 's-grey',
                //      disabled: false,
                //      handler: function() {
                //          winplay.hide();
                //      }
                // }]
    });
    var winplay = Ext.create('widget.window', {
        title: 'Play Video',
        id: 'winplay',
        closable: true,
        modal: true,
        closeAction: 'hide',
        width: 650,
        height: 350,
        layout: 'fit',
        items: [PlayForm]
    });
});
