
<div class="title">
    <div class="row-fluid">
        <div class="span4 right_offset" style="float: right;height: 45px; text-align: right; width: inherit">
            <h5 style="width:calc(100% - 32px);margin-top: 2px;">
              <input type="text" id="datepicker1" size="10" style="width: 80px" class="row-fluid" value="<?=$tgl['awal']?>"> s.d. 
              <input type="text" id="datepicker2" size="10" style="width: 80px" class="row-fluid" value="<?=$tgl['akhir']?>"> 
              <button class="btn btn-primary" data-original-title=".btn .btn-info" data-placement="top" rel="tooltip" 
                  style="height: 23px;padding:0px 12px;" onClick="setRange()"><?php echo lang('Cari') ?></button>
              &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                <span id="judul">
                    
                </span>
            </h5>
            <div class="options_arrow pull-right" style="margin-top: -26px;">
                <div class="dropdown pull-right">
                    <a class="dropdown-toggle " id="dLabel" role="button" data-toggle="dropdown" data-target="#" href="/page.html">
                        <i class=" icon-caret-down"></i>
                    </a>
                    <ul class="dropdown-menu " role="menu" aria-labelledby="dLabel" id="dLabeli">
                        
                    </ul>
                </div>
            </div>
        </div>
        <!-- End .span6 -->
    </div>
    <!-- End .row-fluid -->
</div>
<!-- End .title --> 

<script type="text/javascript">
    $(function(){
        $.ajax({
            url: '<?php echo $api ?>/dashboard/region_traceability',
            // type: 'default GET (Other values: POST)',
            // dataType: 'default: Intelligent Guess (Other values: xml, json, script, or html)',
            data: {
                prov: m_prov,
                kab: m_kab,
                kec: m_kec,
                desa: m_desa,
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
            if (!m_prov && !m_kab && !m_priv) {
                current = lang('Seluruh Provinsi');
                first = '';
                url = m_path;
            } else if(m_prov && !m_kab && !m_priv){
                current = data.province.name;
                first = '<li><a href="'+m_path+'" onclick="link(this.href); return false">'+lang('Seluruh Provinsi')+'</a></li><li class="divider"></li>';
                url = m_path+m_prov+'/';
                if (!m_daer) {
                    url += 'null/';
                };
            } else if(m_kab){
                current_id = m_kab;
                first = '<li><a href="'+m_path+'" onclick="link(this.href); return false">'+lang('Seluruh Provinsi')+'</a></li><li class="divider"></li>';
                second = '<li><a href="'+m_path+data.province.id+'" onclick="link(this.href); return false">'+lang(data.province.name)+'</a></li><li class="divider"></li>';
                url = m_path+m_prov+'/';
                if (!m_daer) {
                    url += 'null/';
                };
            } else if(m_kec){
                current_id = m_kec;
                first = '<li><a href="'+m_path+'" onclick="link(this.href); return false">'+lang('Seluruh Provinsi')+'</a></li><li class="divider"></li>';
                second = '<li><a href="'+m_path+data.province.id+'" onclick="link(this.href); return false">'+lang(data.province.name)+'</a></li><li class="divider"></li>';
                url = m_path+m_prov+'/'+m_kab;
                if (!m_daer) {
                    url += 'null/';
                };
            } else if(m_desa){
                current_id = m_desa;
                first = '<li><a href="'+m_path+'" onclick="link(this.href); return false">'+lang('Seluruh Provinsi')+'</a></li><li class="divider"></li>';
                second = '<li><a href="'+m_path+data.province.id+'" onclick="link(this.href); return false">'+lang(data.province.name)+'</a></li><li class="divider"></li>';
                url = m_path+m_prov+'/'+m_kab+'/'+m_kec;
                if (!m_daer) {
                    url += 'null/';
                };
            } else if(m_priv){
                current_id = m_priv;
                first = '<li><a href="'+m_path+'" onclick="link(this.href); return false">'+lang('Seluruh Provinsi')+'</a></li><li class="divider"></li>';
                second = '<li><a href="'+m_path+data.province.id+'" onclick="link(this.href); return false">'+lang(data.province.name)+'</a></li><li class="divider"></li>';         
                url = m_path+m_prov+'/';
                if (!m_daer) {
                    url += 'null/';
                };
            }
            var list = [];
            $.each(data.data, function(index, val) {
                var elm = '<li><a href="'+url+val.id+'" onclick="link(this.href); return false">'+lang(val.name)+'</a></li>';
                list.push(elm);
                if (current_id && current_id == val.id) {
                    current = lang(val.name);
                };
                if (second_id && second_id == val.id) {
                    second = '<li><a href="'+m_path+val.id+'" onclick="link(this.href); return false">'+lang(val.name)+'</a></li>';
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
            console.log("error");
        })
        .always(function() {
            console.log("complete");
        });

    })
</script>