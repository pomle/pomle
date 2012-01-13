<?
require '../Init.inc.php';

$css[] = '/css/Track.css';
$js[] = '/js/LastFM.js';
$js[] = '/js/TrackView.js';

if( !$Track = \Post\Track::loadOneFromDB($_GET['trackID']) )
{
	$Track = new \Post\Track();
	$Track->title = '404';
	$Track->timestamp = 'Sidan kunde inte hittas';
	header('HTTP/1.0 404 Not Found');
}
else
{
	$Track->timestamp = \Format::timestamp($Track->timePublished);
}

$pageTitle = $Track->title;

if( $Track->PreviewMedia )
	$imageURL = \Media\Producer\Blog::createFromMedia($Track->PreviewMedia)->getTrackImage();
else
	$imageURL = '';

require HEADER;
?>
<div class="track" data-artist="<? echo htmlspecialchars($Track->artist); ?>" data-track="<? echo htmlspecialchars($Track->track); ?>">
	<a href="<? echo $Track->artistURL; ?>" class="canvas">
		<div class="image" style="background-image: url('<? echo $imageURL; ?>');">

		</div>
	</a>

	<ul class="resources">
		<li><a href="<? echo $Track->getSpotifyURI(); ?>">Spotify</a></li>
		<li><a href="<? echo $Track->artistURL; ?>">Last.FM</a></li>
	</ul>

	<div class="header">
		<h1><a href="<? echo $Track->artistURL; ?>"><? echo htmlspecialchars($Track->artist); ?></a> - <a href="<? echo $Track->trackURL; ?>"><? echo htmlspecialchars($Track->track); ?></a></h1>
		<ul class="details">
			<li><span class="timestamp">Älskad <? echo htmlspecialchars($Track->timestamp); ?></span></li>
			<li><span class="playcount_artist">Artist spelad: <span class="count">-</span> gånger</span></li>
			<li><span class="playcount_track">Låt spelad: <span class="count">-</span> gånger</span></li>
		</ul>
	</div>

	<div class="description">
		<div class="bio"></div>
	</div>
</div>
<?
require FOOTER;