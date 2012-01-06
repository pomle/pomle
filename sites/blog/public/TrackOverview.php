<?
require '../Init.inc.php';

#$js[] = '/js/BrickTile.RandomizedUpdate.js';

$page = (int)abs($_GET['page']);
$pageLen = 11;
$pageStart = $pageLen * $page;

$query = \DB::prepareQuery("SELECT
		p.ID
	FROM
		Posts p
		JOIN PostTracks plt ON plt.postID = p.ID
	WHERE
		p.isPublished = 1
		AND p.type = %s
	ORDER BY
		p.timePublished DESC
	LIMIT %u, %u",
	POST_TYPE_TRACK,
	$pageStart,
	$pageLen + 1);

$postIDs = \DB::queryAndFetchArray($query);

$posts = \Track::loadFromDB($postIDs);

$BrickTile = new \Element\BrickTile();
foreach(array_slice($postIDs, 0, $pageLen) as $postID)
	if( isset($posts[$postID]) )
		$BrickTile->addPost($posts[$postID]);


if( count($posts) > $pageLen )
	$BrickTile->addItem('/img/NextPage_Track.jpg', sprintf('/TrackOverview.php?page=%u', $page+1), 'Nästa sida »');

$pageCaption = 'Last.fm Loved Tracks';

require HEADER;

echo
	$BrickTile
	;

require FOOTER;