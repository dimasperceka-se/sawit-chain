Ext.define('Koltiva.view.Grower.PanelTrainingCoachingGrid', {
    extend: 'Ext.form.Panel',
    id: 'Koltiva.view.Grower.PanelTrainingCoachingGrid',
    cls: 'Sfr_PanelSubLayoutForm',
    viewConfig: {
        deferEmptyText: false,
        emptyText: GetDefaultContentNoData()
    },
    viewVar: false,
    setViewVar: function (value) {
        this.viewVar = value;
    },
    listeners: {
        afterRender: function () {
            var thisObj = this;

            thisObj.StoreTrainingGrid.storeVar.MemberID = thisObj.viewVar.MemberID;
            thisObj.StoreTrainingGrid.load();
            thisObj.StoreCoachingGrid.storeVar.MemberID = thisObj.viewVar.MemberID;
            thisObj.StoreCoachingGrid.load();
        }
    },
    initComponent: function () {
        var thisObj = this;

        thisObj.StoreTrainingGrid = Ext.create('Koltiva.store.Grower.MainGridTraining', {
            storeVar: {
                MemberID: thisObj.viewVar.MemberID
            }
        });

        thisObj.StoreCoachingGrid = Ext.create('Koltiva.store.Grower.MainGridCoaching', {
            storeVar: {
                MemberID: thisObj.viewVar.MemberID
            }
        });

        thisObj.items = [{
                layout: 'column',
                border: false,
                style: 'padding-top:10px;padding-bottom:10px;',
                items: [{
                        columnWidth: 1,
                        layout: 'form',
                        cls: 'Sfr_PanelLayoutFormContainer',
                        items: [{
                                xtype: 'panel',
                                title: lang('Training'),
                                frame: false,
                                id: 'Koltiva.view.Grower.PanelTrainingCoachingGrid-SectionTraining',
                                style: 'margin-top:10px;',
                                cls: 'Sfr_PanelSubLayoutFormRoundedGray',
                                items: [{
                                        xtype: 'grid',
                                        id: 'Koltiva.view.Grower.PanelTrainingCoachingGrid-PanelTrainingGrid',
                                        cls: 'Sfr_GridNew',
                                        loadMask: true,
                                        selType: 'rowmodel',
                                        store: thisObj.StoreTrainingGrid,
                                        enableColumnHide: false,
                                        viewConfig: {
                                            deferEmptyText: false,
                                            emptyText: GetDefaultContentNoData()
                                        },
                                        columns: [{
                                                text: 'No',
                                                width: '5%',
                                                xtype: 'rownumberer'
                                            },{
                                                text: lang('Batch'),
                                                dataIndex: 'BatchNumber',
                                                flex: 10
                                            },{
                                                text: lang('Training'),
                                                dataIndex: 'CpgTrainings',
                                                flex: 10
                                            },{
                                                text: lang('Topic'),
                                                dataIndex: 'sub_topic',
                                                flex: 10
                                            }, {
                                                text: lang('Start'),
                                                dataIndex: 'TrainingStart',
                                                flex: 10
                                            }, {
                                                text: lang('End'),
                                                dataIndex: 'TrainingEnd',
                                                flex: 10
                                            },{
                                                text: lang('Training Status'),
                                                dataIndex: 'TrainingStatus',
                                                flex: 10
                                            }]
                                    }]
                            }, {
                                xtype: 'panel',
                                title: lang('Coaching'),
                                frame: false,
                                id: 'Koltiva.view.Grower.PanelTrainingCoachingGrid-SectionCoaching',
                                style: 'margin-top:20px;',
                                cls: 'Sfr_PanelSubLayoutFormRoundedGray',
                                items: [{
                                        xtype: 'grid',
                                        id: 'Koltiva.view.Grower.PanelTrainingCoachingGrid-PanelCoachingGrid',
                                        cls: 'Sfr_GridNew',
                                        loadMask: true,
                                        selType: 'rowmodel',
                                        store: thisObj.StoreCoachingGrid,
                                        enableColumnHide: false,
                                        viewConfig: {
                                            deferEmptyText: false,
                                            emptyText: GetDefaultContentNoData()
                                        },
                                        columns: [{
                                                text: 'No',
                                                width: '5%',
                                                xtype: 'rownumberer'
                                            }, {
                                                text: lang('Coaching Recipient'),
                                                dataIndex: 'CoachingRecipient',
                                                flex: 15
                                            }, {
                                                text: lang('Coaching Recipient Name'),
                                                dataIndex: 'CoachingRecipientName',
                                                flex: 20
                                            }, {
                                                text: lang('Coaching Date'),
                                                dataIndex: 'CoachingDate',
                                                flex: 15
                                            }, {
                                                text: lang('Time Start'),
                                                dataIndex: 'TimeStart',
                                                flex: 10
                                            }, {
                                                text: lang('Time End'),
                                                dataIndex: 'TimeEnd',
                                                flex: 10
                                            }]
                                    }]
                            }]
                    }]
        }];
        
        this.callParent(arguments);
    }
});