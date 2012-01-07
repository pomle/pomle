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
	<link rel="alternate" type="application/rss+xml" title="<? echo htmlspecialchars($rssTitle); ?>" href="<? echo $rssHref; ?>">
	<!--[if IE]>
		<script src="//html5shiv.googlecode.com/svn/trunk/html5.js"></script>
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

			<div>
				<a href="/"><img src="/img/SiteLogo.png" alt="" height="38" width="159"></a>
				<? if(isset($pageCaption)) printf('<span class="caption">%s</span>', htmlspecialchars($pageCaption)); ?>
			</div>
			<nav>
				<a href="/DiaryOverview.php"><img src="/img/Menu_Item_Diary.png" alt="Dagbok (numera känt som blogg) - här skriver saker med debaterbar intressehalt."></a>
				<a href="/AlbumOverview.php"><img src="/img/Menu_Item_Album.png" alt="Fotoalbum - innehåller förstås bilder på skit jag fotograferat och tycker om"></a>
				<a href="/TrackOverview.php"><img src="/img/Menu_Item_Track.png" alt="Musik - Låtar jag älskar och vad som spelas just nu hos mig"></a>
			</nav>
		</header>
		<section class="pageContent">