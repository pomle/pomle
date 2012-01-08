<?
define('ACCESS_POLICY', 'AllowViewAlbum');

require '../Init.inc.php';

if( !isset($_GET['postID']) )
{
	$Post = \Diary::addToDB();
	header(sprintf('Location: /DiaryEdit.php?postID=%u', $Post->postID));
	exit();
}

$Post = \Diary::loadOneFromDB($_GET['postID']);

if( !$Post )
	echo \Element\Page::error(MESSAGE_ROW_MISSING);

$permParams = array('postID' => $Post->postID);

$pageTitle = _('Dagbok');
$pageSubtitle = $Post->postID;


require HEADER;

$IOCall = new \Element\IOCall('Diary', $permParams);

echo $IOCall->getHead();
?>

<fieldset>
	<legend><? echo \Element\Tag::legend('application_double', _('Egenskaper')); ?></legend>

	<?
	echo new \Element\Form\Post($Post);
	?>

	<fieldset>
		<legend><? echo \Element\Tag::legend('page_white', _('Text')); ?></legend>

		<?
		echo new \Element\TextArea('content', $Post->content, 120, 30);
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