<?
require '../Init.inc.php';

$js[] = '/js/BrickTile.RandomizedUpdate.js';

$question = $_GET['q'];

### Search Diary
$query = \DB::prepareQuery("SELECT
		p.ID,
		p.timePublished
	FROM
		PostDiaries pd
		JOIN Posts p ON p.ID = pd.postID
	WHERE
		p.isPublished = 1
		AND (p.title LIKE %S OR pd.content LIKE %S)
	ORDER BY
		p.timePublished DESC",
	$question,
	$question);

$diaryIDs = \DB::queryAndFetchArray($query);

### Search Album
$query = \DB::prepareQuery("SELECT
		p.ID,
		p.timePublished
	FROM
		PostAlbums pa
		JOIN Posts p ON p.ID = pa.postID
		LEFT JOIN PostAlbumMedia pam ON pam.postID = pa.postID
	WHERE
		p.isPublished = 1
		AND pam.isVisible = 1
		AND (p.title LIKE %S OR pam.comment LIKE %S OR pam.tags LIKE %S)
	GROUP BY
		p.ID
	ORDER BY
		p.timePublished DESC",
	$question,
	$question,
	$question);

$albumIDs = \DB::queryAndFetchArray($query);


$postIDs = $diaryIDs + $albumIDs;

$posts = \Post::loadAutoTypedFromDB(array_keys($postIDs));

arsort($postIDs, SORT_NUMERIC);

#header("Content-type: text/plain");
#print_r($postIDs);
#die();

$BrickTile = new \Element\BrickTile();
foreach($postIDs as $postID => $timestamp)
	if( isset($posts[$postID]) )
		$BrickTile->addPost($posts[$postID]);

$pageCaption = 'Sök';

require HEADER;

echo
	$BrickTile
	;

require FOOTER;