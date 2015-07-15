<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="utf-8">
    <title>接口调试</title>
    <link href="http://cdn.bootcss.com/bootstrap/3.3.5/css/bootstrap.css" rel="stylesheet">
    <style type="text/css">
        body {
            font-family: "Helvetica Neue", Helvetica, Microsoft Yahei, Hiragino Sans GB, WenQuanYi Micro Hei, sans-serif;
        }
    </style>
</head>
<body>
<div class="container">
    <h3>腾讯企业邮件OpenAPI接口</h3>
    <div class="alert alert-danger hidden">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <span class="msg"></span>
    </div>
    <form id="form1">
        <div class="form-group">
            <label for="exampleInputEmail1">Key值：</label>
            <input type="text" name="Key" class="form-control" id="Key" placeholder="如：06510e2e8557a8cd4a9e882b95947dwe">
        </div>
        <div class="form-group">
            <label for="exampleInputEmail1">管理员帐号：<span class="label label-success">指管理企业邮件的帐号</span></label>
            <input type="text" name="ClientId" class="form-control" id="ClientId" placeholder="如：henrick">
        </div>
        <button type="button" id="getToken" class="btn btn-info">获取Token</button>
    </form>
    <form id="form2" class="hidden">
        <div class="form-group">
            <label for="exampleInputEmail1">登录邮箱：<span class="label label-success">邮件帐号如me@hejinmin.cn</span></label>
            <input type="text" name="Mail" class="form-control" id="Mail" placeholder="如：me@hejinmin.cn">
        </div>
        <button type="button" id="getExmail" class="btn btn-info">同步登录邮件系统</button>
        <div class="form-group">
            <label for="exampleInputEmail1">管理员帐号：<span class="label label-success">指管理企业邮件的帐号</span></label>
            <input type="text" name="ClientId" class="form-control" id="ClientId" placeholder="如：henrick">
        </div>
        <button type="button" class="btn btn-info">获取Token</button>
    </form>
</div>
<script src="http://cdn.bootcss.com/jquery/2.1.4/jquery.min.js"></script>
<script src="http://cdn.bootcss.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
<script>
    $(function(){
        $("#getToken").click(function(){
            getToken(); //检测权限
        });

        //直接进入登录系统
        $("#getExmail").click(function(){
            getExmail();
        });
    });

    //检测是否有权限使用接口
    function getToken(){
        var key = $("#Key").val();
        var clientId = $("#ClientId").val();
        if(!key || !clientId){
            showMsg("Key或管理员帐号不能为空！");
            return false;
        }
        $.post('api.php?opt=getAccessToken',{Key:key,ClientId:clientId},function(data){
            if(data.err){
                $(".alert span.msg").html(data.msg);
                return false;
            }
            $("#form1").hide();
            $("#form2").removeClass('hidden');
        },'json');
    }

    //单点登录
    function getExmail(){
        $.post("api.php?opt=goExmail",{Mail:$("#Mail").val()},function(data){
            if(data.err){
                $(".alert span.msg").html(data.msg);
                return false;
            }
            window.open(data.url);
        },'json');
    }

    //错误消息显示
    function showMsg(msg){
        $(".alert").removeClass("hidden");
        $(".alert span.msg").html(msg);
    }
</script>
</body>
</html>