<?
require '../Init.inc.php';


?><!DOCTYPE html>
<html lang="en">
<head>
	<?
	if( isset($pageTitle) ) printf('<title>%s</title>', htmlspecialchars($pageTitle));

	foreach($css as $path)
		printf('<link rel="stylesheet" type="text/css" href="%s">', $path);

	?>
	<meta name="viewport" content="width=400">
</head>

<body>
	<header id="control">

		<div class="inner">

			<form action="/ajax/Panel.php?type=Library&amp;name=Search" method="get" id="search">
				<div class="search">
					<input type="text" name="q" value="<? if( isset($_COOKIE['searchQuery']) ) echo htmlspecialchars($_GET['searchQuery']); ?>">
				</div>
			</form>

			<div class="torus">
				<a href="#Home" class="panelLibrary"><img src="/img/Cordless_Logo.png" alt="Cordless"></a>

				<nav>
					<ul>
						<li><a href="#Home" class="panelLibrary"><? echo _("Home"); ?></a></li>
					</ul>
				</nav>
			</div>

			<?
			$Cordless = new \Element\Cordless();
			echo $Cordless;
			?>

		</div>

	</header>

	<section id="upload" class="sidebar">
		<div class="control">
			<div class="icon logo"></div>

			<a href="#" class="icon lock"><? echo _('Lock'); ?></a>
		</div>

		<div class="content">
s			<?
			$UploadForm = new \Element\Upload('/ajax/ReceiveFile.php');
			echo $UploadForm;
			?>
		</div>
	</section>

	<section id="playqueue" class="sidebar">
		<div class="control">
			<div class="icon logo"></div>

			<a href="#" class="icon lock"><? echo _('Lock'); ?></a>
		</div>

		<div class="content">
			<?
			$Fetch = new \Fetch\UserTrack($User);

			$userTracks = $Fetch->getLastPlaylist();

			$Playlist = \Element\Playlist::createFromUserTracks($userTracks);

			echo $Playlist;
			?>
		</div>
	</section>

	<section id="library">
		<?
		include DIR_ELEMENT_PANEL . 'Library.Home.inc.php';
		?>
	</section>

	<footer class="footer">

	</footer>
	<?
	foreach($js as $path)
		printf('<script type="text/javascript" src="%s"></script>', $path);
	?>
</body>
</html>