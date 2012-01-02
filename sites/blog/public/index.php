<?
require '../Init.inc.php';

$query = "SELECT ID FROM Posts JOIN PostAlbums ON postID = ID WHERE isPublished = 1 ORDER BY timePublished DESC LIMIT 20";
$postIDs = \DB::queryAndFetchArray($query);

$posts = \Album::loadFromDB($postIDs);

$BrickTile = new \Element\BrickTile();

foreach($postIDs as $postID)
{
	if( !isset($posts[$postID]) ) continue;

	$Post = $posts[$postID];

	$mediaPool = array();

	if( $Post::TYPE == 'album' )
	{
		foreach($Post->media as $Media)
			$mediaPool[] = $Media->mediaHash;
	}

	$mediaHash = reset($mediaPool);

	shuffle($mediaPool);

	$mediaPool = array_slice($mediaPool, 0, 10);

	$mediaURL = \Media\Producer\BrickTile::createFromHash($mediaHash)->getTile();

	$BrickTile->addItem(\URL::album($Post), $Post->title, $mediaURL, $mediaPool, \Format::date($Post->timePublished));
}


require HEADER;

echo $BrickTile;
?>
<?
require FOOTER;