<?
define('ACCESS_POLICY', 'AllowViewTrack');

require '../Init.inc.php';

if( !isset($_GET['postID']) )
{
	$Post = \Track::addToDB();
	header(sprintf('Location: /TrackEdit.php?postID=%u', $Post->postID));
	exit();
}

$Post = \Track::loadOneFromDB($_GET['postID']);

if( !$Post )
	echo \Element\Page::error(MESSAGE_ROW_MISSING);

$permParams = array('postID' => $Post->postID);

$pageTitle = _('Hit');
$pageSubtitle = $Post->postID;


require HEADER;

$IOCall = new \Element\IOCall('Track', $permParams);

echo $IOCall->getHead();
?>

<fieldset>
	<legend><? echo \Element\Tag::legend('application_double', _('Egenskaper')); ?></legend>

	<?
	echo new \Element\Form\Post($Post);
	?>

	<fieldset>
		<legend><? echo \Element\Tag::legend('sound', _('Hit')); ?></legend>

		<?
		$size = 60;

		echo \Element\Table::inputs()
			->addRow(_('Last FM ID'), \Element\Input::text('lastFmID', $Post->lastFmID))
			->addRow(_('Artist'), \Element\Input::text('artist', $Post->artist)->size($size))
			->addRow(_('Låt'), \Element\Input::text('track', $Post->track)->size($size))
			->addRow(_('URL Artist'), \Element\Input::text('artistURL', $Post->artistURL)->size($size))
			->addRow(_('URL Låt'), \Element\Input::text('trackURL', $Post->trackURL)->size($size))
			->addRow(_('Spotify URI'), \Element\Input::text('spotifyURI', $Post->spotifyURI)->size($size))
			;
		?>

	</fieldset>

	<?
	$Control = new \Element\IOControl($IOCall);
	$Control
		->addButton(new \Element\Button\Save())
		->addButton(new \Element\Button\Delete())
		;
	echo $Control;
	?>

</fieldset>

<?
echo $IOCall->getFoot();

require FOOTER;