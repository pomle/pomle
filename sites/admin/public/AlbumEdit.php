<?
define('ACCESS_POLICY', 'AllowViewAlbum');

require '../Init.inc.php';

if( !isset($_GET['postID']) )
{
	$Post = \Post\Album::addToDB();
	header(sprintf('Location: /AlbumEdit.php?postID=%u', $Post->postID));
	exit();
}

$Post = \Post\Album::loadOneFromDB($_GET['postID']);

if( !$Post )
	echo \Element\Page::error(MESSAGE_ROW_MISSING);

$permParams = array('postID' => $Post->postID);

$pageTitle = _('Album');
$pageSubtitle = $Post->postID;


require HEADER;

$SubLinks = new \Element\SubLinks();
$SubLinks
	->addLink(sprintf('/AlbumMediaEdit.php?postID=%u', $Post->postID), 'images', _('Redigera Media'))
	;

echo $SubLinks;


$IOCall = new \Element\IOCall('Album', $permParams);

echo $IOCall->getHead();
?>
<fieldset>
	<legend><? echo \Element\Tag::legend('application_double', _('Egenskaper')); ?></legend>
	<?
	echo new \Element\Form\Post($Post);
	?>
	<fieldset>
		<legend><? echo \Element\Tag::legend('text_align_left', _('Beskrivning')); ?></legend>
		<?
		echo new \Element\TextArea('description', $Post->description, 60, 5);
		?>
	</fieldset>
	<?
	$Control = new \Element\IOControl($IOCall);
	$Control
		->addButton(new \Element\Button\Save())
		->addButton(new \Element\Button\Delete())
		->addButton(\Element\Button::IO('purge', 'bin', _('Rensa'), _('Är du säker på att du vill tömma alla bilder från detta album?')))
		;
	echo $Control;
	?>
</fieldset>
<?
echo $IOCall->getFoot();
?>
<fieldset>
	<legend><? echo \Element\Tag::legend('images', _('Media')); ?></legend>

	<?
	$MediaList = \Element\Antiloop::getAsDomObject('AlbumMedia', null, $permParams)->setID('antiloopAlbumMedia');
	echo $MediaList;
	?>

	<?
	$IOCall = new \Element\IOCall('AlbumMedia', $permParams);
	echo $IOCall->getHead();
	?>
	<div class="ajaxEdit">
		<input type="hidden" name="postAlbumMediaID">

		<fieldset>
			<legend><? echo \Element\Tag::legend('page_edit', _('Redigera')); ?></legend>
			<?
			$size = 40;
			echo \Element\Table::inputs()
				->addRow(_('Visa'), \Element\Input::checkbox('isVisible'))
				->addRow(_('Framsidebild'), \Element\Input::checkbox('isAlbumPreview'))
				->addRow(_('Kommentar'), \Element\TextArea::small('comment'))
				->addRow(_('Taggar'), \Element\Input::text('tags'))
				;

			$Control = new \Element\IOControl($IOCall);
			$Control
				->addButton(new \Element\Button\Save())
				->addButton(new \Element\Button\Delete())
				;
			echo $Control;
			?>
		</fieldset>
	</div>
	<?
	echo $IOCall->getFoot();

	echo $IOCall->getHead();
	?>
	<fieldset>
		<legend><? echo \Element\Tag::legend('table_row_insert', _('Import')); ?></legend>

		<?
		echo \Element\Table::inputs()
			->addRow(_('Insert'), \Element\SelectBox::keyPair('insertOrder', 'last', array('first' => _('First'), 'last' => _('Last'))));
		?>

		<fieldset>
			<legend><? echo \Element\Tag::legend('page_add', _('Upload')); ?></legend>

			<?
			$Upload = new \Element\FileUpload($IOCall, 'upload', $permParams);
			echo $Upload;
			?>

		</fieldset>

		<?
		$IOControl = new \Element\IOControl($IOCall);
		?>
		<fieldset>
			<legend><? echo \Element\Tag::legend('world_link', _('Download')); ?></legend>

			<?
			echo \Element\Table::inputs()
				->addRow(_('URL'), \Element\Input::text('url')->size(100))
				;

			echo $IOControl->setButtons(\Element\Button::IO('download', 'world_add', _('Hämta')));
			?>
		</fieldset>

		<fieldset>
			<legend><? echo \Element\Tag::legend('world_link', _('Connect')); ?></legend>

			<?
			echo \Element\Table::inputs()
				->addRow(_('Media ID'), \Element\Input::text('mediaID'))
				;

			echo $IOControl->setButtons(\Element\Button::IO('importMedia', 'add', 'Importera'));
			?>
		</fieldset>

	</fieldset>
	<?
	echo $IOCall->getFoot();
	?>
</fieldset>
<?
require FOOTER;