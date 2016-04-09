
<article class="page-container">
    <form class="form form-horizontal" id="form-scene-add">
        <div class="row cl">
            <label class="form-label col-xs-4 col-sm-3"><span class="c-red">*</span>二维码类型：</label>
            <div class="formControls col-xs-8 col-sm-9">
                <span class="select-box" style="width:200px;">
                    <select class="select" name="type" id="type">
                        <option value="0" selected>临时二维码(30天)</option>
                        <option value="1">永久二维码</option>
                    </select>
                </span>
            </div>
        </div>
        <div class="row cl">
            <label class="form-label col-xs-4 col-sm-3"><span class="c-red">*</span>场景id：</label>
            <div class="formControls col-xs-8 col-sm-9">
                <input type="text" class="input-text" value="" placeholder="必填 唯一属性 数字范围(1-100000)" id="scene_id" name="scene_id">
            </div>
        </div>
        <div class="row cl">
            <label class="form-label col-xs-4 col-sm-3"><span class="c-red">*</span>场景备注：</label>
            <div class="formControls col-xs-8 col-sm-9">
                <input type="text" class="input-text" value="" placeholder="必填" id="scene_remark" name="scene_remark">
            </div>
        </div>

        <div class="row cl">
            <div class="col-xs-8 col-sm-9 col-xs-offset-4 col-sm-offset-3">
                <input class="btn btn-primary radius" type="submit" value="&nbsp;&nbsp;添 加&nbsp;&nbsp;">
            </div>
        </div>
    </form>
</article>

<script type="text/javascript" src="/resource/admin/lib/jquery.validation/1.14.0/jquery.validate.min.js"></script>
<script type="text/javascript" src="/resource/admin/lib/jquery.validation/1.14.0/validate-methods.js"></script>
<script type="text/javascript" src="/resource/admin/lib/jquery.validation/1.14.0/messages_zh.min.js"></script>


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
                var type = $('#type').val();
                var scene_id = $('#scene_id').val();
                var scene_remark = $('#scene_remark').val();

                $.ajax({
                    type: "POST",
                    dataType: "json",
                    url: "/admin/promote/sceneAddOrEdit",
                    data: {code_type:type, scene_id:scene_id, scene_remark:scene_remark},
                    success: function (data) {
                        //console.log('success');

                        if (data.error.error_id == 0) {
                            var index = parent.layer.getFrameIndex(window.name);
                            parent.layer.msg('添加成功');
                            window.setTimeout(function(){
                                parent.location.replace(parent.location.href);
                                parent.layer.close(index);
                            }, 1000);

                        } else {
                            parent.layer.msg('添加失败');
                        }
                    },
                    error: function () {
                        parent.layer.msg('添加失败');
                    }
                });

            }
        });

    });
</script>
