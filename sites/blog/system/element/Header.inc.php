<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>Pomle.com</title>
	<?
	foreach($css as $path)
		printf('<link rel="stylesheet" type="text/css" href="%s">', $path);
	?>
	<meta name="viewport" content="width=820">
	<!--[if IE]>
		<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
	<![endif]-->
</head>

<body>
	<div class="pageContainer">
		<header class="header">
			<form action="/Search.php" method="get">
			<div class="search">
				<input type="text" name="q" value="<? if( isset($_GET['q']) ) echo htmlspecialchars($_GET['q']); ?>">
			</div>
			</form>

			<a href="/"><img src="/img/SiteLogo.png" alt="" height="38" width="159"></a>
			<? if(isset($pageCaption)) printf('<span class="caption">%s</span>', htmlspecialchars($pageCaption)); ?>
		</header>
		<section class="pageContent">