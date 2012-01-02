<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>Pomle.com</title>
	<?
	foreach($css as $path)
		printf('<link rel="stylesheet" type="text/css" href="%s">', $path);
	?>
	<!--[if IE]>
		<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
	<![endif]-->
</head>

<body>
	<div class="header pageContainer">
		<a href="/"><img src="/img/SiteLogo.png" alt=""></a>
		&emsp; <a href="/DiaryOverview.php">Blog</a> | <a href="/AlbumOverview.php">Foto</a>
	</div>
	<div class="pageContent pageContainer">