<?
require '../Init.inc.php';

$pageIndex = 0;
$primaryMediaID = null;

$MediaScrubber = new \Element\MediaScrubber();

### mediaHash has precedence of mediaID
if( isset($_GET['mediaHash']) && ($Media = \Manager\Media::loadByHash($_GET['mediaHash'])) )
{
	$media = array($Media);
	$primaryMediaID = $Media->mediaID;
}
elseif( isset($_GET['albumID']) && ($Album = \Post\Album::loadOneFromDB($_GET['albumID'])) )
{
	$media = $Album->media;
	$primaryMediaID = (int)$_GET['mediaID'];
}
elseif( isset($_GET['mediaID']) ) ### Can be array and will be handled by Media::loadFromDB as if it is
{
	$media = \Manager\Media::loadFromDB((array)$_GET['mediaID']);
	$primaryMediaID = is_array($_GET['mediaID']) ? reset($_GET['mediaID']) : $_GET['mediaID'];
}
else ### Fallback if none is found
{
	$media = array(\Manager\Media::integrateIntoLibrary(\Media\Image::createFromFile(DIR_SITE_RESOURCE . 'FileNotFound.jpg')));
	header('HTTP/1.0 404 Not Found');
}

### If we have an array of extra media to be browseable, prepare it
if( isset($media) && is_array($media) )
{
	$i = 0;
	foreach($media as $Media)
	{
		if( $Media->mediaID == $primaryMediaID )
		{
			$Primary_Media = $Media;
			$page = ($MediaScrubber->index = $i) + 1;
		}

		$MediaScrubber->addItem($Media);

		$i++;
	}
}

if( $Primary_Media )
	$pageImageURL = '/helpers/mediaGen/PagePreview.php?mediaHash=' . $Primary_Media->mediaHash;

require HEADER;

echo $MediaScrubber;

require FOOTER;