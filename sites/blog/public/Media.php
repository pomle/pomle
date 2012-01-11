<?
require '../Init.inc.php';

$pageIndex = 0;
$MediaScrubber = new \Element\MediaScrubber();

### mediaHash has precedence of mediaID
if( isset($_GET['mediaHash']) )
	$Media_Primary = \Manager\Media::loadByHash($_GET['mediaHash']);
elseif( isset($_GET['mediaID']) )
	$Media_Primary = \Manager\Media::loadOneFromDB(is_array($_GET['mediaID']) ? reset($_GET['mediaID']) : $_GET['mediaID']);


if( $Media_Primary )
{
	### If this image is to be displayed as a browseable group of images, get them
	if( isset($_GET['albumID']) && ($Album = \Post\Album::loadOneFromDB($_GET['albumID'])) )
		$media = $Album->media;
	elseif( isset($_GET['mediaID']) && is_array($_GET['mediaID']) )
		$media = \Manager\Media::loadFromDB($_GET['mediaID']);
	else
		$media = array($Media_Primary);


	### If we have an array of extra media to be browseable, prepare it
	if( isset($media) && is_array($media) )
	{
		$i = 0;
		foreach($media as $Media)
		{
			if( $Media->mediaID == $Media_Primary->mediaID )
				$page = ($MediaScrubber->index = $i) + 1;

			$MediaScrubber->addItem($Media);

			$i++;
		}
	}
}
else ### Fallback if none is found
{
	$Media_Primary = \Manager\Media::integrateIntoLibrary(\Media\Image::createFromFile(DIR_SITE_RESOURCE . 'FileNotFound.jpg'));
	$MediaScrubber->addItem($Media_Primary);
	header('HTTP/1.0 404 Not Found');
}

require HEADER;

echo $MediaScrubber;

require FOOTER;