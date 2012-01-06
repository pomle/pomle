<?
require '../Init.inc.php';

$js[] = '/js/BrickTile.RandomizedUpdate.js';

$page = (int)abs($_GET['page']);
$pageLen = 11;
$pageStart = $pageLen * $page;

$query = \DB::prepareQuery("SELECT
		p.ID
	FROM
		Posts p
		JOIN PostAlbums pa ON pa.postID = p.ID
	WHERE
		p.isPublished = 1
		AND p.type = 'album'
	ORDER BY
		p.timePublished DESC
	LIMIT %u, %u",
	$pageStart,
	$pageLen + 1);

$postIDs = \DB::queryAndFetchArray($query);

$posts = \Album::loadFromDB($postIDs);

$BrickTile = new \Element\BrickTile();
foreach(array_slice($postIDs, 0, $pageLen) as $postID)
	if( isset($posts[$postID]) )
		$BrickTile->addPost($posts[$postID]);


if( count($posts) > $pageLen )
{
	$mediaURL = \Media\Producer\BrickTile::createFromHash('6ba739cd51f91b5e7b8c6e2877d81d60')->getTile();
	$BrickTile->addItem(sprintf('/AlbumOverview.php?page=%u', $page+1), 'Nästa sida »', $mediaURL);
}

$pageCaption = 'Fotoalbum';

require HEADER;

echo
	$BrickTile
	;

require FOOTER;