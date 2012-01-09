<?
require '../Init.inc.php';

$css[] = '/css/Album.css';
$js[] = '/js/MediaScrubber.js';

$pageIndex = 0;
$mediaHashs = array();

### mediaHash has precedence of mediaID
if( isset($_GET['mediaHash']) )
	$Media_Primary = \Manager\Media::loadByHash($_GET['mediaHash']);
elseif( isset($_GET['mediaID']) )
	$Media_Primary = \Manager\Media::loadOneFromDB(is_array($_GET['mediaID']) ? reset($_GET['mediaID']) : $_GET['mediaID']);


if( $Media_Primary )
{
	### If this image is to be displayed as a browseable group of images, get them
	if( isset($_GET['albumID']) && ($Album = \Album::loadOneFromDB($_GET['albumID'])) )
		$media = $Album->media;
	elseif( isset($_GET['mediaID']) && is_array($_GET['mediaID']) )
		$media = \Manager\Media::loadFromDB($_GET['mediaID']);


	### If we have an array of extra media to be browseable, prepare it
	if( isset($media) && is_array($media) )
	{
		$i = 0;
		foreach($media as $Media)
		{
			if( $Media->mediaID == $Media_Primary->mediaID )
				$page = ($pageIndex = $i) + 1;

			$mediaHashs[$i] = $Media->mediaHash;

			$i++;
		}
		$pageLen = $i;
	}
}
else ### Fallback if none is found
{
	$Media_Primary = \Manager\Media::integrateIntoLibrary(\Media\Image::createFromFile(DIR_SITE_RESOURCE . 'FileNotFound.jpg'));
	header('HTTP/1.0 404 Not Found');
}


$mediaURL = \Media\Producer\Blog::createFromMedia($Media_Primary)->getAlbumImage();

require HEADER;
?>
<div class="mediaDock frame" data-pageIndex="<? echo htmlspecialchars(json_encode($pageIndex)); ?>" data-mediaHashs="<? echo htmlspecialchars(json_encode($mediaHashs)); ?>">
	<div class="image" style="background-image: url('<? echo $mediaURL; ?>');">
		<div class="overlay">
			<div class="busy"></div>
			<?
			if( $pageLen )
			{
				?>
				<div class="control">
					<div class="pageIndex"><? printf('%d / %d', $page, $pageLen); ?></div>
					<a href="#" class="prev" rel="-1"></a>
					<a href="#" class="next" rel="1"></a>
					<div class="info">
						<div class="content"></div>
						<a href="<? echo $mediaURL; ?>" class="mediaURL">Direktlänk</a>
					</div>
				</div>
				<?
			}
			?>
		</div>
	</div>
</div>
<?
require FOOTER;