<?
define('ACCESS_POLICY', 'AllowViewAlbum');

require '../Init.inc.php';

$Post = \Post\Album::loadOneFromDB($_GET['postID']);

if( !$Post )
	echo \Element\Page::error(MESSAGE_ROW_MISSING);

$pageTitle = _('Album');

require HEADER;

$BlockSort = new \Element\BlockSort();
foreach($Post->media as $Media)
	$BlockSort->addItem(new \Element\MediaItem($Media));

echo $BlockSort;

require FOOTER;