
<nav class="breadcrumb"><i class="Hui-iconfont">&#xe67f;</i> 首页 <span class="c-gray en">&gt;</span> 带参数二维码场景值统计  <a class="btn btn-success radius r" style="line-height:1.6em;margin-top:3px" href="javascript:location.replace(location.href);" title="刷新" ><i class="Hui-iconfont">&#xe68f;</i></a></nav>

<div class="page-container">
    <div>
        <a class="btn btn-primary radius" onclick="management_scene('管理场景','<?php echo $this->createUrl("scene/sceneManagement") ?>')" href="javascript:;"><i class="Hui-iconfont">&#xe6f5;</i> 管理场景</a>
    </div>

    <table class="table table-border table-bordered table-bg mt-20">
        <thead>
        <tr>
            <th colspan="5" scope="col">场景统计列表：</th>
        </tr>
        <tr class="text-c">
            <th>序号</th>
            <th>场景id</th>
            <th>场景备注</th>
            <th>关注次数</th>
            <th>时间</th>
        </tr>
        </thead>
        <tbody class="data_show">
            <!--#data_tpl-->
        </tbody>
    </table>
</div>

<script>
    function management_scene(title,url){
        var index = layer.open({
            type: 2,
            title: title,
            content: url
        });
        layer.full(index);
    }

    $(function(){
        $.ajax({
            type: 'POST',
            url: '/WechatApi0101',
            dataType: 'json',
            data: {method: 's001', query: ''},
            success: function(data){
                if (data.error.error_id == 0) {
                    var html = juicer($('#data_tpl').html(), data);
                    $('.data_show').html(html);
                } else {
                    console.log(data);
                }
            },
            error: function(){
                console.log('error');
            }
        });
    });
</script>

<script id="data_tpl" type="text/template">
    {@each data as item,index}
    <tr class="text-c">
        <td>${index}</td>
        <td>${item.scene_id}</td>
        <td>remark</td>
        <td>num</td>
        <td>${item.timeint}</td>
    </tr>
    {@/each}
</script>