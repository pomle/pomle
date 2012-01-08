<?
require '../Init.inc.php';

$js[] = '/js/BrickTile.RandomizedUpdate.js';

$pageLen = 12;

$Fetcher = new \Fetch\Post($pageLen + 1, $pageLen * $pageIndex);

$posts = $Fetcher->getTimeline();

$BrickTile = new \Element\BrickTile();

$i = 0;
$hasMore = false;
foreach($posts as $postID => $Post)
{
	if( $hasMore = (++$i > $pageLen) ) break;
	$BrickTile->addPost($Post);
}

$pageCaption = 'Timeline';

require HEADER;

echo
	$BrickTile
	;


$Paginator = new \Element\Paginator($page, max(1, $page - 4), $page + 4);
echo $Paginator;

require FOOTER;