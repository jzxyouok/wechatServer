<!DOCTYPE HTML>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="renderer" content="webkit|ie-comp|ie-stand">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<meta name="viewport" content="width=device-width,initial-scale=1,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no" />
	<meta http-equiv="Cache-Control" content="no-siteapp" />

	<link rel="stylesheet" type="text/css" href="/resource/admin/static/h-ui/css/H-ui.min.css" />
	<link rel="stylesheet" type="text/css" href="/resource/admin/static/h-ui/css/H-ui.admin.css" />
	<link rel="stylesheet" type="text/css" href="/resource/admin/lib/Hui-iconfont/1.0.7/iconfont.css" />
	<link rel="stylesheet" type="text/css" href="/resource/admin/lib/icheck/icheck.css" />
	<link rel="stylesheet" type="text/css" href="/resource/admin/static/h-ui/skin/default/skin.css" id="skin" />
	<link rel="stylesheet" type="text/css" href="/resource/admin/static/h-ui/css/style.css" />

	<script type="text/javascript" src="/resource/admin/lib/jquery/1.9.1/jquery.min.js"></script>
	<script type="text/javascript" src="/resource/admin/lib/layer/2.1/layer.js"></script>
	<script type="text/javascript" src="/resource/admin/static/h-ui/js/H-ui.js"></script>
	<script type="text/javascript" src="/resource/admin/static/h-ui/js/H-ui.admin.js"></script>
	<script type="text/javascript" src="/resource/admin/lib/juicer/juicer-min.js"></script>
	<script type="text/javascript" src="/resource/admin/lib/jquery.validation/1.14.0/jquery.validate.min.js"></script>
	<script type="text/javascript" src="/resource/admin/lib/jquery.validation/1.14.0/validate-methods.js"></script>
	<script type="text/javascript" src="/resource/admin/lib/jquery.validation/1.14.0/messages_zh.min.js"></script>

	<title><?php echo CHtml::encode($this->pageTitle) ?></title>
</head>

<body>
<?php echo $content; ?>

<script>
	var _hmt = _hmt || [];
	(function() {
		var hm = document.createElement("script");
		hm.src = "//hm.baidu.com/hm.js?080836300300be57b7f34f4b3e97d911";
		var s = document.getElementsByTagName("script")[0];
		s.parentNode.insertBefore(hm, s);
	})();
	var _bdhmProtocol = (("https:" == document.location.protocol) ? " https://" : " http://");
	document.write(unescape("%3Cscript src='" + _bdhmProtocol + "hm.baidu.com/h.js%3F080836300300be57b7f34f4b3e97d911' type='text/javascript'%3E%3C/script%3E"));
</script>

</body>
</html>