
<nav class="breadcrumb"><i class="Hui-iconfont">&#xe67f;</i> 首页 <span class="c-gray en">&gt;</span> 市场推广 <span class="c-gray en">&gt;</span> 带参数二维码场景统计 <span class="c-gray en">&gt;</span> 管理场景  <a class="btn btn-success btn-refresh radius r" style="line-height:1.6em;margin-top:3px" href="javascript:location.replace(location.href);" title="刷新" ><i class="Hui-iconfont">&#xe68f;</i></a></nav>

<div class="page-container">
    <div class="cl pd-5 bg-1 bk-gray mt-20">
        <span class="l">
            <a href="javascript:;" onclick="scene_add('添加场景','<?php echo $this->createUrl("scene/sceneAdd") ?>','800','500')" class="btn btn-primary radius"><i class="Hui-iconfont">&#xe600;</i> 添加场景</a>
        </span> <span class="r">共有数据：<strong><?php echo $scene_count ?></strong> 条</span>
    </div>

    <table class="table table-border table-bordered table-bg">
        <thead>
        <tr>
            <th scope="col" colspan="9">场景值列表</th>
        </tr>
        <tr class="text-c">
            <th>序号</th>
            <th>场景id</th>
            <th>场景备注</th>
            <th>二维码链接 <a href="http://cli.im/" target="_blank" style="color: #06c">(生成二维码)</a></th>
            <th>二维码类型</th>
            <th>修改时间</th>
            <th>操作</th>
        </tr>
        </thead>
        <tbody>

        <?php if (is_array($scene_data) && !empty($scene_data)) {
                foreach ($scene_data as $k => $v) {
            ?>
        <tr class="text-c">
            <td><?php echo $k + 1; ?></td>
            <td><?php echo $v['scene_id'] ?></td>
            <td><?php echo $v['scene_remark'] ?></td>
            <td><?php echo $v['url'] ?></td>
            <td>
                <?php
                switch ($v['type']) {
                    case 0:
                        $type = '临时二维码(30天)';
                        break;
                    case 1:
                        $type = '永久二维码';
                        break;
                    default:
                        $type = '其他';
                        break;
                }
                echo $type;
                ?>
            </td>
            <td><?php echo date('Y-m-d H:i:s', $v['timeint']) ?></td>
            <td class="td-manage">
                <a title="编辑" href="javascript:;" onclick="scene_edit('场景修改','<?php echo $this->createUrl("scene/sceneEdit?id=" . $v['id']) ?>','800','500')" class="ml-5" style="text-decoration:none"><i class="Hui-iconfont">&#xe6df;</i></a>
                <a title="删除" href="javascript:;" onclick="scene_del(this, <?php echo $v['id'] ?>, <?php echo $v['scene_id'] ?>)" class="ml-5" style="text-decoration:none"><i class="Hui-iconfont">&#xe6e2;</i></a>
            </td>
        </tr>
        <?php }} ?>

        </tbody>
    </table>
</div>

<script type="text/javascript">
    /*
     参数解释：
     title	标题
     url	请求的url
     id		需要操作的数据id
     w		弹出层宽度（缺省调默认值）
     h		弹出层高度（缺省调默认值）
     */
    /*管理员-增加*/
    function scene_add(title,url,w,h){
        layer_show(title,url,w,h);
    }
    /*管理员-删除*/
    function scene_del(obj, id, scene_id){
        layer.confirm('确认要删除吗？',function(index){

            $.ajax({
                type: "POST",
                dataType: "json",
                url: "/admin/scene/sceneDel",
                data: {id: id, scene_id: scene_id},
                success: function (data) {

                    if (data.error.error_id == 0) {
                        $(obj).parents("tr").remove();
                        layer.msg('删除成功',{icon:1,time:1000});
                        window.setTimeout(function(){
                            location.replace(location.href);
                        }, 1000);

                    } else {
                        console.log('111');
                        layer.msg('删除失败',{icon:2,time:1000});
                    }
                },
                error: function () {
                    console.log('222');
                    layer.msg('删除失败',{icon:2,time:1000});
                }
            });


        });
    }
    /*管理员-编辑*/
    function scene_edit(title,url,w,h){
        layer_show(title,url,w,h);
    }

</script>