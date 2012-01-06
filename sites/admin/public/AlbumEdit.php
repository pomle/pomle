<?
define('ACCESS_POLICY', 'AllowViewAlbum');

require '../Init.inc.php';

if( !isset($_GET['postID']) )
{
	$Album = \Album::addToDB();
	header(sprintf('Location: /AlbumEdit.php?postID=%u', $Album->postID));
	exit();
}

$Album = \Album::loadOneFromDB($_GET['postID']);

if( !$Album )
	echo \Element\Page::error(MESSAGE_ROW_MISSING);

$permParams = array('postID' => $Album->postID);

$pageTitle = _('Album');
$pageSubtitle = $Album->title;


require HEADER;

$IOCall = new \Element\IOCall('Album', $permParams);

echo $IOCall->getHead();
?>
<fieldset>
	<legend><? echo \Element\Tag::legend('application_double', _('Egenskaper')); ?></legend>
	<?
	$size = 40;
	echo \Element\Table::inputs()
		->addRow(_('Aktiv'), \Element\Input::checkbox('isPublished', $Album->isPublished))
		->addRow(_('Titel'), \Element\Input::text('title', $Album->title)->size($size))
		->addRow(_('Datum'), \Element\Input::text('timePublished', \Format::timestamp($Album->timePublished))->size($size))
		#->addRow(_('Grafik'), '<input disabled="disabled" style="width: 960px; height: 320px;" name="thumb" class="image pinky">')
		;
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
			<legend><? echo \Element\Tag::legend('page_edit', _('Funktion')); ?></legend>
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
	?>

</fieldset>

<fieldset>
	<legend><? echo \Element\Tag::legend('page_add', _('Ladda upp')); ?></legend>

	<?
	$Upload = new \Element\FileUpload($IOCall, 'upload', $permParams);
	echo $Upload;
	?>

</fieldset>

<?
echo $IOCall->getHead();
$IOControl = new \Element\IOControl($IOCall);
?>
<fieldset>
	<legend><? echo \Element\Tag::legend('world_link', _('Ladda ner')); ?></legend>

	<?
	echo \Element\Table::inputs()
		->addRow(_('URL'), \Element\Input::text('url')->size(100))
		;

	echo $IOControl->setButtons(\Element\Button::IO('download', 'world_add', _('Hämta')));
	?>
</fieldset>

<fieldset>
	<legend><? echo \Element\Tag::legend('world_link', _('Ladda ner')); ?></legend>

	<?
	echo \Element\Table::inputs()
		->addRow(_('Media ID'), \Element\Input::text('mediaID'))
		;

	echo $IOControl->setButtons(\Element\Button::IO('importMedia', 'add', 'Importera'));
	?>
</fieldset>
<?
echo $IOCall->getFoot();

require FOOTER;