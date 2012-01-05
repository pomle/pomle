<?
require '../Init.inc.php';

$js[] = '/js/BrickTile.RandomizedUpdate.js';

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

postSort($postIDs, $posts);

$BrickTile = \Element\BrickTile::createFromPosts(array_slice($posts, 0, $pageLen), null);

if( count($posts) > $pageLen )
{
	$mediaURL = \Media\Producer\BrickTile::createFromHash('6ba739cd51f91b5e7b8c6e2877d81d60')->getTile();
	$BrickTile->addItem(sprintf('/index.php?page=%u', $page+1), 'Go Deeper »', $mediaURL);
}

require HEADER;

echo
	$BrickTile
	;

/*echo '<ul class="pageIndex">';
if( $page > 0 )
{
	$i = 0;
	while($i++<$page)
		printf('<li class="pageNumber"><a class="transition fast" href="/index.php?page=%u">%u</a></li>', $i-1, $i);
}
echo '</ul>';*/

require FOOTER;