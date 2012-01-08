<?
require '../Init.inc.php';

$js[] = '/js/BrickTile.RandomizedUpdate.js';

$pageLen = 12;

$Fetcher = new \Fetch\Post($pageLen + 1, $pageLen * $pageIndex);
$posts = $Fetcher->getDiaries();

$BrickTile = new \Element\BrickTile();

$i = 0;
$hasMore = false;
foreach($posts as $postID => $Post)
{
	if( $hasMore = (++$i > $pageLen) ) break;
	$BrickTile->addPost($Post);
}

$pageCaption = 'Blogg';

require HEADER;

echo
	$BrickTile
	;

$Paginator = new \Element\Paginator($page, max(1, $page - 4), min($page + 4, ceil($Fetcher->rowTotal / $pageLen)));
echo $Paginator;

require FOOTER;