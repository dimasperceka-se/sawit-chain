<div class="btn-group btn-hspace pull-right">
<a class="btn btn-default dropdown-toggle" id="judul" role="button" data-toggle="dropdown" data-target="#" href="/page.html">
        <i class=" icon-caret-down"></i>
    </a>
    <ul class="dropdown-menu " role="menu" aria-labelledby="dLabel" id="dLabeli">

    </ul>
</div>

<script type="text/javascript">
    // $(function(){
        $.ajax({
            url: '<?php echo $api ?>/dashboard/coop',
            // type: 'default GET (Other values: POST)',
            // dataType: 'default: Intelligent Guess (Other values: xml, json, script, or html)',
            data: {
                coop_id: m_coop_id
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
            if (!m_coop_id) {
                current = lang('Seluruh Koperasi');
                first = '';
                url = m_path;
            } else if(m_coop_id){
                current = lang(data.province.name);
                first = '<li><a href="'+m_path+add_path+'" onclick="link(this.href); return false">'+lang('Seluruh Koperasi')+'</a></li><li class="divider"></li>';
                url = m_path+'/';
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
            // console.log("error");
        })
        .always(function() {
            // console.log("complete");
        });

    // })
</script>