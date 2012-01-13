<?
require '../Init.inc.php';

$css[] = '/css/Album.css';

if( !$Album = \Post\Album::loadOneFromDB($_GET['albumID']) )
{
	$Album = new \Post\Album();
	$Album->title = '404';
	$Album->timestamp = 'Sidan kunde inte hittas';
	$Album->description = 'Men här är några helt slumpvist utvalda bilder.';

	$query = "SELECT mediaID FROM PostAlbumMedia WHERE isVisible = 1 ORDER BY RAND() LIMIT 10";
	$mediaIDs = \DB::queryAndFetchArray($query);

	$media = \Manager\Media::loadFromDB($mediaIDs);

	foreach($media as $Media)
		$Album->addMedia($Media);

	header('HTTP/1.0 404 Not Found');
}
else
{
	$Album->timestamp = \Format::timestamp($Album->timePublished);

	if( $Album->PreviewMedia )
		$previewMediaHash = $Album->PreviewMedia->mediaHash;
}

$pageTitle = $Album->title;

require HEADER;
?>
<div class="album clearfix">
	<div class="header">
		<?
		if( $Album->title )
			printf('<h1>%s</h1>', htmlspecialchars($Album->title));

		if( $Album->timestamp || false )
		{
			echo '<ul class="details">';
			if( $Album->timestamp )
				printf('<li><span class="timestamp">%s</span></li>', htmlspecialchars($Album->timestamp));
			echo '</ul>';
		}

		if( $Album->description )
			printf('<p>%s</p>', htmlspecialchars($Album->description));
		?>
	</div>

	<div class="thumbnails">
		<?
		foreach($Album->media as $Media)
		{
			$mediaURL = \Media\Producer\Blog::createFromMedia($Media)->getAlbumThumb();
			printf('<a href="%s" class="image border" style="background-image: url(\'%s\');"><div class="overlay"></div></a>', sprintf('/Media.php?mediaID=%u&albumID=%u', $Media->mediaID, $Album->postID), $mediaURL);
		}
		?>
	</div>
</div>
<?
require FOOTER;