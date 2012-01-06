<?
require '_Debug.inc.php';

$query = \DB::prepareQuery("SELECT
		p.ID,
		COALESCE(MAX(pam.timeCreated), p.timePublished) AS timeFresh
	FROM
		Posts p
		LEFT JOIN PostDiaries pd ON pd.postID = p.ID
		LEFT JOIN PostAlbumMedia pam ON pam.postID = p.ID
	WHERE
		p.isPublished = 1
	GROUP BY
		p.ID
	ORDER BY
		timeFresh DESC
	LIMIT %u, %u",
	0,
	10);

#echo $query;

$postIDs = \DB::queryAndFetchArray($query);

print_r($postIDs);

$posts = \Post::loadAutoTypedFromDB(array_slice(array_keys($postIDs), 0, $pageLen));

print_r(array_keys($posts));