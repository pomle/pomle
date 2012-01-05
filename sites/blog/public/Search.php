<?
require '../Init.inc.php';

$js[] = '/js/BrickTile.RandomizedUpdate.js';

$question = $_GET['q'];

$query = \DB::prepareQuery("SELECT
		p.ID
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

$postIDs = \DB::queryAndFetchArray($query);

$posts = \Album::loadFromDB($postIDs);

postSort($postIDs, $posts);

$BrickTile = \Element\BrickTile::createFromPosts($posts, null);

require HEADER;

echo
	$BrickTile
	;

require FOOTER;