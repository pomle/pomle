<?
require '../Init.inc.php';

$js[] = '/js/BrickTile.RandomizedUpdate.js';

$page = (int)abs($_GET['page']);
$pageLen = 11;
$pageStart = $pageLen * $page;

$query = \DB::prepareQuery("SELECT
		p.ID,
		COALESCE(MAX(pam.timeCreated), p.timePublished) AS timeFresh
	FROM
		Posts p
		LEFT JOIN PostDiaries pd ON pd.postID = p.ID
		LEFT JOIN PostAlbumMedia pam ON pam.postID = p.ID
	WHERE
		p.isPublished = 1
		AND p.type IN %A
	GROUP BY
		p.ID
	ORDER BY
		timeFresh DESC
	LIMIT %u, %u",
	array(POST_TYPE_ALBUM, POST_TYPE_DIARY, POST_TYPE_TRACK),
	$pageStart,
	$pageLen + 1);

$postIDs = \DB::queryAndFetchArray($query);

$posts = \Post::loadAutoTypedFromDB(array_slice(array_keys($postIDs), 0, $pageLen));

$BrickTile = new \Element\BrickTile();
foreach($postIDs as $postID => $timeFresh)
{
	if( !isset($posts[$postID]) ) continue;
	$Post = $posts[$postID];
	$Post->timePublished = $timeFresh;
	$BrickTile->addPost($Post);
}

if( count($postIDs) > $pageLen )
	$BrickTile->addItem('/img/NextPage_Timeline.jpg', sprintf('/index.php?page=%u', $page+1), 'Go Deeper »');

$pageCaption = 'Timeline';

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