<?
define('ACCESS_POLICY', 'AllowViewAlbum');

require '../Init.inc.php';

$Post = \Post\Album::loadOneFromDB($_GET['postID'], false, false);

if( !$Post )
	echo \Element\Page::error(MESSAGE_ROW_MISSING);


$previewMediaID = null;
if( isset($Post->PreviewMedia) )
	$previewMediaID = $Post->PreviewMedia->mediaID;

$pageTitle = _('Album');

require HEADER;

$IOCall = new \Element\IOCall('AlbumMedia', array('postID' => $Post->postID));

echo $IOCall->getHead();
?>
<fieldset>
	<legend><? echo \Element\Tag::legend('text_list_numbers', _('Sorting & Visibility')); ?></legend>
	<?
	$BlockSort = new \Element\BlockSort();
	foreach($Post->media as $Media)
	{
		$Item = new \Element\MediaItem($Media);
		$Item
			->addContent( \Element\Input::checkbox('isVisible[]', $Media->isVisible) )
			->addContent( \Element\Input::radio('previewMediaID', ($Media->mediaID == $previewMediaID), $Media->mediaID) )
			;

		$BlockSort->addItem($Item, $Media->postAlbumMediaID);
	}

	echo $BlockSort;


	$Control = new \Element\IOControl($IOCall);
	$Control
		->addButton(new \Element\Button\Save('saveLayout'))
		;
	echo $Control;
	?>
</fieldset>
<?
echo $IOCall->getFoot();

require FOOTER;