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
		JOIN PostDiaries pd ON pd.postID = p.ID
	WHERE
		p.isPublished = 1
		AND p.type = 'diary'
	ORDER BY
		p.timePublished DESC
	LIMIT %u, %u",
	$pageStart,
	$pageLen + 1);

$postIDs = \DB::queryAndFetchArray($query);

$posts = \Diary::loadFromDB($postIDs);

$BrickTile = new \Element\BrickTile();
foreach(array_slice($postIDs, 0, $pageLen) as $postID)
	if( isset($posts[$postID]) )
		$BrickTile->addPost($posts[$postID]);


if( count($posts) > $pageLen )
	$BrickTile->addItem('/img/NextPage_Diary.jpg', sprintf('/DiaryOverview.php?page=%u', $page+1), 'Nästa sida »');

$pageCaption = 'Blogg';

require HEADER;

echo
	$BrickTile
	;

require FOOTER;