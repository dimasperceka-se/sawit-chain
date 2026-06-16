
    <div class="pull-right xs-mr-50">&nbsp;</div>
    <div class="btn-group btn-hspace pull-right">
        <button class="btn btn-default dropdown-toggle" data-toggle="dropdown" type="button" aria-expanded="false"><span id="trainingTitle"></span>&nbsp;<span class="caret"></span></button>
        <ul class="dropdown-menu" role="menu" id="dLabelp">
                <li><a onClick="changeTraining('kader'); setTraining(); return false" ><?php echo lang('TOT Key Farmers') ?></a></li>
                <li><a onClick="changeTraining('farmer'); setTraining(); return false" ><?php echo lang('FFS Participants') ?></a></li>
                <li><a onClick="changeTraining('all'); setTraining(); return false" ><?php echo lang('All Participants') ?></a></li>
        </ul>
    </div>
    <script type="text/javascript">
        function changeTraining (training) {
            switch(training){
                case 'kader':
                    $('#trainingTitle').text(lang('TOT Key Farmers'));
                    break;
                case 'farmer':
                    $('#trainingTitle').text(lang('FFS Participants'));
                    break;
                case 'all':
                    $('#trainingTitle').text(lang('All Participants'));
                    break;
            }
            m_training = training;
        } 
        function setTraining() {
            link('<?php echo current_url() ?>?_search='+'&training='+m_training);
        }  
        $(function() {                                    
            changeTraining(m_training);
        });
    </script>