<?
require '../../../Init.inc.php';

try
{
	if( isset($_GET['mediaHash']) )
	{
		if( !$Media = \Manager\Media::loadByHash($_GET['mediaHash']) )
			throw New Exception('mediaHash not found');

		$media = array($Media);
	}
	elseif( isset($_GET['mediaID']) )
	{
		if( !$media = \Manager\Media::loadFromDB((array)$_GET['mediaID']) )
			throw New Exception('No media IDs found');
	}
	else
	{
		throw New Exception("Nothing to do...");
	}

	$jsonObject = array();

	foreach($media as $Media)
	{
		$Media->mediaURL = \Media\Producer\Blog::createFromMedia($Media)->getAlbumImage();
		$jsonObject[$Media->mediaHash] = $Media;
	}

	exit(json_encode($jsonObject));
}
catch(Exception $e)
{
	die(json_encode(array('result' => 'error', 'message' => $e->getMessage())));
}
