/*
* @Author: nikolius
* @Date:   2017-07-28 11:18:25
* @Last Modified by:   nikolius
* @Last Modified time: 2017-07-28 14:48:36
*/

/*
    Param2 yg diperlukan ketika load View ini
    - MemberID
    - PlotNr
    - SurveyNr
    - DateCollection
*/

// Define Variabel2 / Object2 / Fungsi yg diperlukan oleh view ini (begin)
// Define Variabel2 / Object2 / Fungsi yg diperlukan oleh view ini (end)

Ext.define('Koltiva.view.PlotPolygon.WinPlotPolygon' ,{
    extend: 'Ext.window.Window',
    id: 'Koltiva.view.PlotPolygon.WinPlotPolygon',
    title: lang('Garden Plot Polygon'),
    closable: true,
    modal: true,
    closeAction: 'destroy',
    width: '65%',
    height: '600px',
    overflowY: 'auto',
    viewVar: false,
    setViewVar: function(value){
        this.viewVar = value;
    },
    initComponent: function() {
        var thisObj = this;

        this.callParent(arguments);
    },
    listeners: {
        afterRender: function(){
            var thisObj = this;

            Ext.Ajax.request({
                url: m_api + '/plot_survey/plot_polygon',
                method: 'GET',
                params: {
                    MemberID:thisObj.viewVar.MemberID,
                    PlotNr: thisObj.viewVar.PlotNr,
                    SurveyNr: thisObj.viewVar.SurveyNr,
                    DateCollection: thisObj.viewVar.DateCollection,
                    CallFrom: thisObj.viewVar.CallFrom
                },
                success: function(response){
                    var htmlText = response.responseText;
                    thisObj.update(htmlText, true);
                },
                failure: function(response){
                    Ext.MessageBox.show({
                        title: 'Failed',
                        msg: 'Failed to render polygon',
                        buttons: Ext.MessageBox.OK,
                        animateTarget: 'mb9',
                        icon: 'ext-mb-error'
                    });

                    //tutup popup
                    thisObj.close();
                }
            });
        }
    }
});