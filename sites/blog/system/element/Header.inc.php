<!DOCTYPE html>
<html lang="sv">
<head>
	<meta charset="utf-8">
	<?
	if( isset($pageTitle) ) printf('<title>%s</title>', htmlspecialchars($pageTitle));

	foreach($css as $path)
		printf('<link rel="stylesheet" type="text/css" href="%s">', $path);

	if( isset($pageImageURL) )
	{
		#printf('<meta property="og:image" content="%s">', $previewURL);
		printf('<link rel="image_src" href="%s">', $pageImageURL);
	}
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

			<div class="range">
				<a href="/" class="logo"></a>

				<nav>
					<a class="item" href="/DiaryOverview.php"><img src="/img/Menu_Item_Diary.png" alt="Dagbok (numera känt som blogg) - här skriver saker med debaterbar intressehalt."></a>
					<a class="item" href="/AlbumOverview.php"><img src="/img/Menu_Item_Album.png" alt="Fotoalbum - innehåller förstås bilder på skit jag fotograferat och tycker om"></a>
					<a class="item" href="/TrackOverview.php"><img src="/img/Menu_Item_Track.png" alt="Hitlista - låtar jag fullkomligen älskar"></a>
					<a class="item" href="/ScrobbleOverview.php"><img src="/img/Menu_Item_Scrobble.png" alt="Spellista - det jag spelar just nu, mest i månaden och sånt"></a>
				</nav>
			</div>

			<!--<div class="caption"><? if(isset($pageCaption)) echo htmlspecialchars($pageCaption); ?></div>-->
		</header>
		<section class="pageContent">