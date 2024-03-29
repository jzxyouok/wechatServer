
<article class="page-container">
    <?php if (is_array($scene_data) && !empty($scene_data)) { ?>
    <form class="form form-horizontal" id="form-scene-add">
        <div class="row cl">
            <label class="form-label col-xs-4 col-sm-3"><span class="c-red">*</span>二维码类型：</label>
            <div class="formControls col-xs-8 col-sm-9">
                <span class="select-box" style="width:200px;">
                    <select class="select" name="type" id="type">
                        <option value="0" <?php echo $scene_data['type'] == 0 ? 'selected' : '' ?>>临时二维码(30天)</option>
                        <option value="1" <?php echo $scene_data['type'] == 1 ? 'selected' : '' ?>>永久二维码</option>
                    </select>
                </span>
            </div>
        </div>
        <div class="row cl">
            <label class="form-label col-xs-4 col-sm-3"><span class="c-red">*</span>场景id：</label>
            <div class="formControls col-xs-8 col-sm-9">
                <input type="text" class="input-text disabled" value="<?php echo $scene_data['scene_id'] ?>" placeholder="必填 唯一属性 数字范围(1-100000)" id="scene_id" name="scene_id" disabled="disabled">
            </div>
        </div>
        <div class="row cl">
            <label class="form-label col-xs-4 col-sm-3"><span class="c-red">*</span>场景备注：</label>
            <div class="formControls col-xs-8 col-sm-9">
                <input type="text" class="input-text" value="<?php echo $scene_data['scene_remark'] ?>" placeholder="必填" id="scene_remark" name="scene_remark">
            </div>
        </div>

        <input type="hidden" value="<?php echo $scene_data['id'] ?>" name="id" id="id">
        <div class="row cl">
            <div class="col-xs-8 col-sm-9 col-xs-offset-4 col-sm-offset-3">
                <input class="btn btn-primary radius" type="submit" value="&nbsp;&nbsp;修 改&nbsp;&nbsp;">
            </div>
        </div>
    </form>
    <?php } else { ?>
    <h4>数据有误</h4>
    <?php } ?>
</article>

<script type="text/javascript">
    $(function(){
        $("#form-scene-add").validate({
            rules:{
                type:{
                    required:true,
                },
                scene_id:{
                    required:true,
                    digits:true,
                    min:1,
                    max:100000
                },
                scene_remark:{
                    required:true,
                    //minlength:4,
                    //maxlength:16
                },
            },
            onkeyup:false,
            focusCleanup:true,
            success:"valid",
            submitHandler:function(form){
                var id = $('#id').val();
                var type = $('#type').val();
                var scene_id = $('#scene_id').val();
                var scene_remark = $('#scene_remark').val();

                $.ajax({
                    type: "POST",
                    dataType: "json",
                    url: "/admin/promote/sceneAddOrEdit",
                    data: {id: id, code_type:type, scene_id:scene_id, scene_remark:scene_remark},
                    success: function (data) {

                        if (data.error.error_id == 0) {
                            var index = parent.layer.getFrameIndex(window.name);
                            parent.layer.msg('修改成功');
                            window.setTimeout(function(){
                                parent.location.replace(parent.location.href);
                                parent.layer.close(index);
                            }, 1000);

                        } else {
                            parent.layer.msg('修改失败');
                        }
                    },
                    error: function () {
                        parent.layer.msg('修改失败');
                    }
                });

            }
        });

    });
</script>
