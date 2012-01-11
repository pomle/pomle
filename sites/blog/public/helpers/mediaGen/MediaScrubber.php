<?
require '../../../Init.inc.php';

try
{
	if( isset($_GET['postAlbumMediaID']) )
	{
		if( !$media = \Post\Album\Media::loadFromDB((array)$_GET['postAlbumMediaID']) )
			throw New Exception('No postAlbumMediaIDs found');
	}
	elseif( isset($_GET['mediaID']) )
	{
		if( !$media = \Manager\Media::loadFromDB((array)$_GET['mediaID']) )
			throw New Exception('No mediaIDs found');
	}
	elseif( isset($_GET['mediaHash']) )
	{
		if( !$Media = \Manager\Media::loadByHash($_GET['mediaHash']) )
			throw New Exception('mediaHash not found');

		$media = array($Media);
	}
	else
	{
		throw New Exception("Nothing to do...");
	}

	$mediaPool = array();

	foreach($media as $Media)
		$mediaPool[] = new \Element\MediaScrubberItem($Media);

	foreach($mediaPool as $MediaScrubberItem)
		$MediaScrubberItem->getURL();

	echo json_encode(count($mediaPool) == 1 ? reset($mediaPool) : $mediaPool);
	die(0);
}
catch(Exception $e)
{
	echo json_encode(array('result' => 'error', 'message' => $e->getMessage()));
	die(1);
}
