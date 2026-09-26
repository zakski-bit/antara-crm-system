<?php
if (session_status() === PHP_SESSION_NONE) {
	session_start();
}
?>
<!DOCTYPE html>
<html 
<?php if(isset($rtl_mode) && !empty($rtl_mode)) { ?>dir="rtl"<?php } ?> 
lang="en"
<?php if(isset($dark_mode) && !empty($dark_mode)) { ?> data-tm-layout="dark"<?php } ?>
>
<head>
	<meta charset="utf-8">
	<title><?php echo $head_title;?></title>
	<link href="css/bootstrap.min.css" rel="stylesheet">
	<?php if(isset($rtl_mode) && !empty($rtl_mode)) { ?>
		<link href="css/style-rtl.css" rel="stylesheet">
	<?php } else { ?>
		<link href="css/style.css" rel="stylesheet">
	<?php } ?>
	<?php if(isset($dark_mode) && !empty($dark_mode)) { ?>
		<link href="css/style-dark.css" rel="stylesheet">
	<?php } ?>
	<link rel="icon" type="image/x-icon" href="/favicon.ico?v=20260304-antara-fix2">
	<link rel="icon" type="image/png" sizes="32x32" href="/favicon.png?v=20260304-antara-fix2">
	<link rel="icon" type="image/png" sizes="192x192" href="/images/logotab.png?v=20260304-antara-fix2">
	<link rel="shortcut icon" type="image/x-icon" href="/favicon.ico?v=20260304-antara-fix2">
	<script>
	(function () {
		var ver = "20260304-antara-fix2";
		var icons = [
			{ rel: "icon", type: "image/x-icon", href: "/favicon.ico?v=" + ver },
			{ rel: "shortcut icon", type: "image/x-icon", href: "/favicon.ico?v=" + ver },
			{ rel: "icon", type: "image/png", sizes: "32x32", href: "/favicon.png?v=" + ver },
			{ rel: "icon", type: "image/png", sizes: "192x192", href: "/images/logotab.png?v=" + ver }
		];
		var head = document.head || document.getElementsByTagName("head")[0];
		icons.forEach(function (meta) {
			var link = document.createElement("link");
			link.rel = meta.rel;
			link.href = meta.href;
			if (meta.type) link.type = meta.type;
			if (meta.sizes) link.sizes = meta.sizes;
			head.appendChild(link);
		});
	})();
	</script>
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
</head>
<body
<?php if(isset($body_class) && !empty($body_class)) { ?>
	class="<?php echo $body_class;?>"
<?php } ?>
<?php if(isset($body_bg_image) && !empty($body_bg_image)) { ?>
	data-tm-bg-img="<?php echo $body_bg_image;?>"
<?php } ?>
>
