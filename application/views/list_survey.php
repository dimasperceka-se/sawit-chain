
    <div class="pull-right xs-mr-50">&nbsp;</div>
    <div class="btn-group btn-hspace pull-right">
        <button class="btn btn-default dropdown-toggle" data-toggle="dropdown" type="button" aria-expanded="false"><span id="surveyTitle"></span>&nbsp;<span class="caret"></span></button>
        <ul class="dropdown-menu" role="menu" id="dLabeli">
            <li><a onClick="changeSurvey('0'); setSurvey(); return false" ><?php echo lang('Baseline') ?></a></li>
            <li><a onClick="changeSurvey('1'); setSurvey(); return false" ><?php echo lang('Postline') ?></a></li>
            <li><a onClick="changeSurvey('2'); setSurvey(); return false" ><?php echo lang('Latest Survey Mix') ?></a></li>
        </ul>
    </div>
    <script type="text/javascript">
        function changeSurvey (survey) {
            switch(survey){
                case '0':
                    $('#surveyTitle').text(lang('Baseline'));
                    break;
                case '1':
                    $('#surveyTitle').text(lang('Postline'));
                    break;
                case '2':
                    $('#surveyTitle').text(lang('Latest Survey Mix'));
                    break;
            }
            m_survey = survey;
        } 
        function setSurvey() {
            var tahun = $('#datepicker1').val();
            link('<?php echo current_url() ?>?petani='+m_petani+'&tahun='+tahun+'&survey='+m_survey);
        }  
        $(function() {                                    
            changeSurvey(m_survey);
        })
    </script>