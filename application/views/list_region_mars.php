                    <div class="btn-group btn-hspace pull-right">
                        <button class="btn btn-default dropdown-toggle" data-toggle="dropdown" type="button" aria-expanded="false"><span id="judul"></span>&nbsp;<span class="caret"></span></button>
                        <ul class="dropdown-menu" role="menu" id="dLabeli">
                        </ul>
                    </div>

<script type="text/javascript">
    // $(function(){
        $.ajax({
            url: '<?php echo $api ?>/dashboard/region_mars',
            // type: 'default GET (Other values: POST)',
            // dataType: 'default: Intelligent Guess (Other values: xml, json, script, or html)',
            data: {
                prov: m_prov,
                kab: m_kab,
                daer: m_daer,
            },
        })
        .done(function(data) {
            // console.log(data);
            current = '';
            current_id = null;
            second_id = null;
            second = null;
            url = '';
            add_path = '?_search=';
            if (typeof(m_petani) != 'undefined') {add_path += '&petani='+m_petani};
            if (typeof(m_tahun) != 'undefined') {add_path += '&tahun='+m_tahun};
            if (typeof(m_survey) != 'undefined') {add_path += '&survey='+m_survey};
            if (typeof(m_training) != 'undefined') {add_path += '&training='+m_training};
            if (typeof(m_startdate) != 'undefined') {add_path += '&startdate='+m_startdate};
            if (typeof(m_enddate) != 'undefined') {add_path += '&enddate='+m_enddate};
            if (typeof(m_awal) != 'undefined') {add_path += '&awal='+m_awal};
            if (typeof(m_akhir) != 'undefined') {add_path += '&akhir='+m_akhir};
            if (typeof(m_cert_holder) != 'undefined') {add_path += '&cert_holder='+m_cert_holder};
            if (!m_prov && !m_kab && !m_priv) {
                current = lang('Seluruh Provinsi');
                first = '';
                url = m_path;
            } else if(m_prov && !m_kab && !m_priv){
                current = lang(data.province.name);
                first = '<li><a href="'+m_path+add_path+'" onclick="link(this.href); return false">'+lang('Seluruh Provinsi')+'</a></li><li class="divider"></li>';
                url = m_path+m_prov+'/';
                if (!m_daer) {
                    url += 'null/';
                };
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
                if (second_id && second_id == val.id) {
                    second = '<li><a href="'+m_path+val.id+add_path+'" onclick="link(this.href); return false">'+lang(val.name)+'</a></li>';
                };
            });
            if (first) {
                $('#dLabeli').append(first);
            };
            if (second) {
                $('#dLabeli').append(second);
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