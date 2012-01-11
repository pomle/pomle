<?
require '../Init.inc.php';

$rssHref = '/RSSFeed.php?type=' . POST_TYPE_TRACK;
$pageLen = 12;
$pageCaption = 'Loved tracks';


$Fetcher = new \Fetch\Post($pageLen + 1, $pageLen * $pageIndex);
$posts = $Fetcher->getTracks();

$BrickTile = new \Element\BrickTile();
$i = 0;
$hasMore = false;
foreach($posts as $postID => $Post)
{
	if( $hasMore = (++$i > $pageLen) ) break;
	$BrickTile->addPost($Post);
}

require HEADER;

$pageTitle = $pageTitle . ': Älskade låtar';

echo $BrickTile;

$Paginator = new \Element\Paginator($page, max(1, $page - 4), $page + 4);
echo $Paginator;

require FOOTER;