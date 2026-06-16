
                    <div class="btn-group btn-hspace pull-right" style="display:none;">
                        <button class="btn btn-default dropdown-toggle" data-toggle="dropdown" type="button" aria-expanded="false"><span id="filter_partner"></span>&nbsp;<span class="caret"></span></button>
                        <ul class="dropdown-menu" role="menu" id="filter_partner_list">
                        </ul>
                    </div>
                    <div class="btn-group btn-hspace pull-right">
                        <button class="btn btn-default dropdown-toggle" data-toggle="dropdown" type="button" aria-expanded="false"><span id="filter_region"></span>&nbsp;<span class="caret"></span></button>
                        <ul class="dropdown-menu" role="menu" id="filter_list_region">
                        </ul>
                    </div>

<script type="text/javascript">
    // $(function(){
        $.ajax({
            url: '<?php echo $api ?>/dashboard/region_traceability',
            // type: 'default GET (Other values: POST)',
            // dataType: 'default: Intelligent Guess (Other values: xml, json, script, or html)',
            data: {
                prov: m_prov,
                kab: m_kab,
                kec: m_kec,
                desa: m_desa,
                daer: m_daer
            }
        })
        .done(function(data) {
            // console.log(data);
            current = '';
            current_id = null;
            second_id = null;
            second = null;
            third_id = null;
            third = null;
            fourth_id = null;
            fourth = null;
            url = '';
            add_path = '?_search=';
            // if (typeof(m_petani) != 'undefined') {add_path += '&petani='+m_petani;}
            // if (typeof(m_tahun) != 'undefined') {add_path += '&tahun='+m_tahun;}
            // if (typeof(m_survey) != 'undefined') {add_path += '&survey='+m_survey;}
            //if (typeof(m_training) != 'undefined') {add_path += '&training='+m_training;}
            //if (typeof(m_startdate) != 'undefined') {add_path += '&startdate='+m_startdate;}
            //if (typeof(m_enddate) != 'undefined') {add_path += '&enddate='+m_enddate;}
            if (typeof(m_awal) != 'undefined') {add_path += '&awal='+m_awal;}
            if (typeof(m_akhir) != 'undefined') {add_path += '&akhir='+m_akhir;}
            if (!m_prov && !m_kab && !m_priv) {
                current = lang('All Provinces');
                first = '';
                url = m_path;
            } else if(m_prov && !m_kab && !m_kec && !m_desa && !m_priv){
                current = lang(data.province.name);
                first = '<li><a href="'+m_path+add_path+'" onclick="link(this.href); return false">'+lang('All Provinces')+'</a></li><li class="divider"></li>';
                url = m_path+m_prov+'/';
                if (!m_daer) {
                    url += '/';
                }
            } else if(m_prov && m_kab && !m_kec && !m_desa && !m_priv){
                current = lang(data.district.name);
                current_id = m_kab;
                first = '<li><a href="'+m_path+'" onclick="link(this.href); return false">'+lang('Seluruh Provinsi')+'</a></li><li class="divider"></li>';
                second = '<li><a href="'+m_path+data.province.id+add_path+'" onclick="link(this.href); return false">'+lang(data.province.name)+'</a></li><li class="divider"></li>';
                url = m_path+m_prov+'/'+m_kab+'/';
                if (!m_daer) {
                    url += '/';
                }
            } else if(m_prov && m_kab && m_kec && !m_desa && !m_priv){
                current = lang(data.subdistrict.name);
                current_id = m_kec;
                first = '<li><a href="'+m_path+'" onclick="link(this.href); return false">'+lang('Seluruh Provinsi')+'</a></li><li class="divider"></li>';
                second = '<li><a href="'+m_path+data.province.id+add_path+'" onclick="link(this.href); return false">'+lang(data.province.name)+'</a></li><li class="divider"></li>';
                third = '<li><a href="'+m_path+'/'+data.province.id+'/'+data.district.id+add_path+'" onclick="link(this.href); return false">'+lang(data.district.name)+'</a></li><li class="divider"></li>';
                url = m_path+m_prov+'/'+m_kab+'/'+m_kec;
                if (!m_daer) {
                    url += '/';
                }
            } else if(m_prov && m_kab && m_kec && m_desa && !m_priv){
                current = lang(data.village.name);
                current_id = m_desa;
                first = '<li><a href="'+m_path+'" onclick="link(this.href); return false">'+lang('Seluruh Provinsi')+'</a></li><li class="divider"></li>';
                second = '<li><a href="'+m_path+data.province.id+add_path+'" onclick="link(this.href); return false">'+lang(data.province.name)+'</a></li><li class="divider"></li>';
                third = '<li><a href="'+m_path+'/'+data.province.id+'/'+data.district.id+add_path+'" onclick="link(this.href); return false">'+lang(data.district.name)+'</a></li><li class="divider"></li>';
                fourth = '<li><a href="'+m_path+'/'+data.province.id+'/'+data.district.id+'/'+data.subdistrict.id+add_path+'" onclick="link(this.href); return false">'+lang(data.subdistrict.name)+'</a></li><li class="divider"></li>';
                url = m_path+m_prov+'/'+m_kab+'/'+m_kec;
                if (!m_daer) {
                    url += '/';
                }
            } else if(m_priv){
                current_id = m_priv;
                first = '<li><a href="'+m_path+'" onclick="link(this.href); return false">'+lang('Seluruh Provinsi')+'</a></li><li class="divider"></li>';
                second = '<li><a href="'+m_path+data.province.id+add_path+'" onclick="link(this.href); return false">'+lang(data.province.name)+'</a></li><li class="divider"></li>';         
                url = m_path+m_prov+'/';
                if (!m_daer) {
                    url += '/';
                }
            }
            var list = [];
            $.each(data.data, function(index, val) {
                var elm = '<li><a href="'+url+val.id+add_path+'" onclick="link(this.href); return false">'+lang(val.name)+'</a></li>';
                list.push(elm);
                if (current_id && current_id == val.id) {
                    current = lang(val.name);
                }
                if (second_id && second_id == val.id) {
                    second = '<li><a href="'+m_path+val.id+add_path+'" onclick="link(this.href); return false">'+lang(val.name)+'</a></li>';
                }
            });
            if (first) {
                $('#filter_list_region').append(first);
            }
            if (second) {
                $('#filter_list_region').append(second);
            }
            if (third) {
                $('#filter_list_region').append(third);
            }
            if (fourth) {
                $('#filter_list_region').append(fourth);
            }
            $.each(list, function(index, val) {
                $('#filter_list_region').append(val);
            });
            $('#filter_region').text(current);

            if (m_kab) {url += m_kab;}
            if (m_kec) {url += m_kec;}
            if (m_desa) {url += m_desa;}
            //if (m_priv) {url += m_priv;}
            
            // combo partner
            $.ajax({
                url: '<?php echo $api ?>/dashboard/traceability_partner',
                // type: 'default GET (Other values: POST)',
                // dataType: 'default: Intelligent Guess (Other values: xml, json, script, or html)',
                data: {
                    prov: m_prov,
                    kab: m_kab,
                    kec: m_kec,
                    desa: m_desa,
                    daer: m_daer,
                    start: m_awal,
                    end: m_akhir,
                    traceability_partner: m_traceability_partner,
                },
            })
            .done(function(data) {
                first = '';
                // console.log("success");
                current_partner_id = '';
                if (!m_traceability_partner) {
                    current_partner = lang('All Partners');
                } else {
                    current_partner = lang(data.partner.name);
                    current_partner_id = m_traceability_partner;
                    first = '<li><a href="'+url+'" onclick="link(this.href); return false">'+lang('All Partners')+'</a></li><li class="divider"></li>';
                }
                var list_partner = [];
                $.each(data.data, function(index, val) {
                    var elm = '<li><a href="'+url+add_path+'&traceability_partner='+val.id+'" onclick="link(this.href); return false">'+lang(val.name)+'</a></li>';
                    list_partner.push(elm);
                    if (current_partner_id && current_partner_id == val.id) {
                        current_partner = lang(val.name);
                    }
                });                
                if (first) {
                    $('#filter_partner_list').append(first);
                }
                $.each(list_partner, function(index, val) {
                    $('#filter_partner_list').append(val);
                });
                $('#filter_partner').text(current_partner);
            })
            .fail(function() {
                // console.log("error");
            })
            .always(function() {
                // console.log("complete");
            });
        })
        .fail(function() {
            // console.log("error");
        })
        .always(function() {
            // console.log("complete");
        });

       
        

    // })
</script>