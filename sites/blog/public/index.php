<?
require '../Init.inc.php';

$page = (int)abs($_GET['page']);
$pageLen = 11;
$pageStart = $pageLen * $page;

$query = \DB::prepareQuery("SELECT
		ID
	FROM
		Posts
		JOIN PostAlbums ON postID = ID
	WHERE
		isPublished = 1
	ORDER BY
		timePublished DESC
	LIMIT %u, %u",
	$pageStart,
	$pageLen + 1);

$postIDs = \DB::queryAndFetchArray($query);

$posts = \Album::loadFromDB($postIDs);

$BrickTile = new \Element\BrickTile();

$i = 0;
$hasMore = false;
foreach($postIDs as $postID)
{
	if( !isset($posts[$postID]) ) continue;

	if( ++$i > $pageLen )
	{
		$mediaURL = \Media\Producer\BrickTile::createFromHash('6ba739cd51f91b5e7b8c6e2877d81d60')->getTile();
		$BrickTile->addItem(sprintf('/index.php?page=%u', $page+1), 'Go Deeper »', $mediaURL);
		break;
	}

	$Post = $posts[$postID];

	$mediaPool = array();

	if( $Post::TYPE == 'album' )
	{
		foreach($Post->media as $Media)
			$mediaPool[] = $Media->mediaHash;
	}

	$mediaHash = (string)(isset($Post->PreviewMedia) ? $Post->PreviewMedia : reset($mediaPool));

	shuffle($mediaPool);

	$mediaPool = array_slice($mediaPool, 0, 10);

	$mediaURL = \Media\Producer\BrickTile::createFromHash($mediaHash)->getTile();

	$BrickTile->addItem(\URL::album($Post), $Post->title, $mediaURL, $mediaPool, \Format::date($Post->timePublished));
}

require HEADER;

echo $BrickTile;

if( $page > 0 )
{
	echo '<ul class="pageIndex clearfix">';
	$i = 0;
	while($i++<$page)
		printf('<li class="pageNumber"><a class="transition fast" href="/index.php?page=%u">%u</a></li>', $i-1, $i);

	echo '</ul>';
}


require FOOTER;