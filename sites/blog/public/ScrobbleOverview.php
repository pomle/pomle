<?
require '../Init.inc.php';

$rssHref = 'http://ws.audioscrobbler.com/1.0/user/pomle/recenttracks.rss';
$pageLen = 24;

$pageCaption = 'Scrobbles';

$css[] = '/css/Scrobble.css';

$js[] = '/js/LastFM.js';
$js[] = '/js/ScrobbleOverview.js';

$images = array
(
	'/img/BrickTile_Fallback_Blue.png',
	'/img/BrickTile_Fallback_Green.png',
	'/img/BrickTile_Fallback_Orange.png',
	'/img/BrickTile_Fallback_Purple.png',
	'/img/BrickTile_Fallback_Swamp.png',
);

$BrickTile = new \Element\BrickTile('recentScrobbles');

$i = 0;
while($i++ < $pageLen)
	$BrickTile->addItem($images[$i % count($images)], '...', '...');

$pageTitle = $pageTitle . ': Senast spelade låtar';

require HEADER;

echo $BrickTile;

#echo '<a style="float: right;" href="http://www.last.fm/user/pomle">Hela listan &raquo;</a>';

#$Paginator = new \Element\Paginator($page, max(1, $page - 4), $page + 4);
#echo $Paginator;
?>
<a class="moreScrobbles" href="http://www.last.fm/user/pomle">Fler spelade låtar &raquo;</a>
<?
require FOOTER;