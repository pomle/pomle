<?
require '../Init.inc.php';

$css[] = '/css/Diary.css';

if( !$Diary = \Post\Diary::loadOneFromDB($_GET['diaryID']) )
{
	$Diary = new \Post\Diary();
	$Diary->title = '404';
	$Diary->timestamp = 'Sidan kunde inte hittas';
	header('HTTP/1.0 404 Not Found');
}
else
{
	$Diary->timestamp = \Format::timestamp($Diary->timePublished);

	if( $Diary->PreviewMedia )
		$previewMediaHash = $Diary->PreviewMedia->mediaHash;
}

$pageTitle = $Diary->title;

require HEADER;
?>
<div class="diary">
	<div class="header">
		<h1><? echo htmlspecialchars($Diary->title); ?></h1>
		<ul class="details">
			<li><span class="timestamp"><? echo htmlspecialchars($Diary->timestamp); ?></span></li>
		</ul>
	</div>
	<?
	if( $Diary->content )
	{
		?>
		<div class="content">
			<?
			if( preg_match('/(<p>)/', $Diary->content) ) ### If we find a <p> tag we assume the post is modern and therefore HTML-aware
			{
				printf('<!-- %s -->', 'RENDER TYPE: MODERN');
				echo $Diary->getHTMLContent();
			}
			else ### Or we add simple, old-school line breaks
			{
				printf('<!-- %s -->', 'RENDER TYPE: OLDSCHOOL');
				echo nl2br($Diary->getHTMLContent());
			}
			?>
		</div>
		<?
	}
	?>
</div>
<?
require FOOTER;