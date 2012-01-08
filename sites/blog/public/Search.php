<?
require '../Init.inc.php';

$js[] = '/js/BrickTile.RandomizedUpdate.js';

$pageLen = 12;

$Fetcher = new \Fetch\Post($pageLen + 1, $pageLen * $pageIndex);
$posts = $Fetcher->getSearch($_GET['q']);


$i = 0;
$hasMore = false;
$BrickTile = new \Element\BrickTile();
foreach($posts as $postID => $Post)
{
	if( $hasMore = (++$i > $pageLen) ) break;
	$BrickTile->addPost($Post);
}


$pageCaption = 'Sök';

require HEADER;

echo
	$BrickTile
	;

$Paginator = new \Element\Paginator($page, max(1, $page - 4), $page + 4, sprintf('/Search.php?q=%s&amp;', htmlspecialchars(urlencode($_GET['q']))));
echo $Paginator;

require FOOTER;