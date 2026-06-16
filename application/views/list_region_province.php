
                    <ul class="dropdown-menu " role="menu" aria-labelledby="dLabel" id="dLabeli">
                        
                    </ul>

<script type="text/javascript">
    // $(function(){
        prov_name = '';
        if (m_prov) {
            $.get('<?php echo $api ?>/dashboard/region?prov='+m_prov, function(data) {
                 prov_name = data.province.name;
             }); 
        };
        $.ajax({
            url: '<?php echo $api ?>/dashboard/region',
            // type: 'default GET (Other values: POST)',
            // dataType: 'default: Intelligent Guess (Other values: xml, json, script, or html)',
            data: {
                prov: '',
                kab: '',
                daer: m_daer,
            },
        })
        .done(function(data) {
            // console.log(data);
            current     = '';
            current_id  = null;
            second_id   = null;
            second      = null;
            url         = '';
            add_path    = '?_search=';
            if (typeof(m_petani) != 'undefined') {add_path += '&petani='+m_petani};
            if (typeof(m_tahun) != 'undefined') {add_path += '&tahun='+m_tahun};
            if (typeof(m_survey) != 'undefined') {add_path += '&survey='+m_survey};
            if (!m_prov && !m_kab && !m_priv) {
                current = lang('Seluruh Provinsi');
                first = '';
                url = m_path;
            } else if(m_prov && !m_kab && !m_priv){
                current = prov_name;
                first = '<li><a href="'+m_path+add_path+'" onclick="link(this.href); return false">'+lang('Seluruh Provinsi')+'</a></li><li class="divider"></li>';
                
                url = m_path;
            } else if(m_kab){
                current_id = m_kab;
                first = '<li><a href="'+m_path+'" onclick="link(this.href); return false">'+lang('Seluruh Provinsi')+'</a></li><li class="divider"></li>';
                second = '<li><a href="'+m_path+data.province.id+add_path+'" onclick="link(this.href); return false">'+lang(data.province.name)+'</a></li><li class="divider"></li>';
                url = m_path+m_prov+'/';
                if (!m_daer) {
                    url += 'null/';
                };
            } else if(m_priv){
                current_id = m_priv;
                first = '<li><a href="'+m_path+'" onclick="link(this.href); return false">'+lang('Seluruh Provinsi')+'</a></li><li class="divider"></li>';
                second = '<li><a href="'+m_path+data.province.id+add_path+'" onclick="link(this.href); return false">'+lang(data.province.name)+'</a></li><li class="divider"></li>';         
                url = m_path+m_prov+'/';
                if (!m_daer) {
                    url += 'null/';
                };
            }
            var list = [];
            $.each(data.data, function(index, val) {
                var elm = '<li><a href="'+url+val.id+add_path+'" onclick="link(this.href); return false">'+lang(val.name)+'</a></li>';
                list.push(elm);
                if (current_id && current_id == val.id) {
                    current = lang(val.name);
                };
            });
            if (first) {
                $('#dLabeli').append(first);
            };
            $.each(list, function(index, val) {
                $('#dLabeli').append(val);
            });
            
            $('#judul').text(current);
        })
        .fail(function() {
            // console.log("error");
        })
        .always(function() {
            // console.log("complete");
        });

    // })
</script>