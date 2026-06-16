
    <div class="pull-right xs-mr-50">&nbsp;</div>
    <div class="btn-group btn-hspace pull-right">
        <button class="btn btn-default dropdown-toggle" data-toggle="dropdown" type="button" aria-expanded="false"><span id="cert_holderTitle"></span>&nbsp;<span class="caret"></span></button>
        <ul class="dropdown-menu" role="menu" id="list_ch">
                <li id="ch_all"><a onClick="changeCH('all'); setCH(); return false" ><?php echo lang('All Certificate Holder') ?></a></li>
                <li><a onClick="changeCH('Private_9'); setCH(); return false" ><?php echo lang('Mars') ?></a></li>
                <li><a onClick="changeCH('Organisasi Petani_23'); setCH(); return false" ><?php echo lang('Koperasi Payung Bersama') ?></a></li>
                <li><a onClick="changeCH('Organisasi Petani_26'); setCH(); return false" ><?php echo lang('Koperasi Tani Masagena') ?></a></li>
                <li><a onClick="changeCH('Organisasi Petani_24'); setCH(); return false" ><?php echo lang('Koperasi Cahaya Sehati') ?></a></li>
        </ul>
    </div>
    <script type="text/javascript">
        function changeCH (cert_holder) {
            switch(cert_holder){
                case 'Private_9':
                    $('#cert_holderTitle').text(lang('Mars'));
                    break;
                case 'Organisasi Petani_23':
                    $('#cert_holderTitle').text(lang('Koperasi Payung Bersama'));
                    break;
                case 'Organisasi Petani_26':
                    $('#cert_holderTitle').text(lang('Koperasi Tani Masagena'));
                    break;
                case 'Organisasi Petani_24':
                    $('#cert_holderTitle').text(lang('Koperasi Cahaya Sehati'));
                    break;
                default :
                    $('#cert_holderTitle').text(lang('All Certificate Holder'));
                    $('#list_ch li#ch_all').hide();
                    break;
            }
            m_cert_holder = cert_holder;
        } 
        function setCH() {
            link('<?php echo current_url() ?>?_search='+'&cert_holder='+m_cert_holder);
        }  
        $(function() {                                    
            changeCH(m_cert_holder);
        });
    </script>