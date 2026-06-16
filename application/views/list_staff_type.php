    <div class="pull-right xs-mr-50">&nbsp;</div>
    <div class="btn-group btn-hspace pull-right">
        <button class="btn btn-default dropdown-toggle" data-toggle="dropdown" type="button" aria-expanded="false"><span id="staffTypeTitle"><?php echo lang('All Participant') ?></span>&nbsp;<span class="caret"></span></button>
        <ul class="dropdown-menu" role="menu" id="dLabelp">
            <li><a onClick="changeStaffType(''); setStaffType(); return false" ><?php echo lang('All Participant') ?></a></li>
            <?php foreach ($staff_type as $key => $value): ?>
                <li><a onClick="changeStaffType('<?php echo $value['id'] ?>'); setStaffType(); return false" ><?php echo lang('Staff '.$value['name']) ?></a></li>
            <?php endforeach ?>
        </ul>
    </div>

    <script type="text/javascript">
        function changeStaffType (staff_type) {
            switch(staff_type){
                <?php foreach ($staff_type as $key => $value): ?>
                case '<?php echo $value['id'] ?>':
                    $('#staffTypeTitle').text(lang('Staff '+'<?php echo $value['name'] ?>'));
                    break;    
                <?php endforeach ?>
            }
            m_staff_type = staff_type;
        } 
        function setStaffType() {
            link('<?php echo current_url() ?>?_search='+'&staff_type='+m_staff_type);
        }  
        $(function() {                                    
            changeStaffType(m_staff_type);
        });
    </script>