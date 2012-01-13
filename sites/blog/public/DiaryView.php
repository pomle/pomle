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
	<div class="page">
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

		<div class="pagination">
			<?
			$query = \DB::prepareQuery("SELECT ID FROM Posts WHERE type = %s AND isPublished = 1 AND timePublished > %u ORDER BY timePublished ASC LIMIT 1", POST_TYPE_DIARY, $Diary->timePublished);
			if( ($postID = \DB::queryAndFetchOne($query)) && ($Diary_Later = \Post\Diary::loadOneFromDB($postID, true)) )
			{
				printf('<a class="next" href="%s" title="Nyare">%s &raquo;</a>', $Diary_Later->getURL(), htmlspecialchars($Diary_Later->title));
			}

			$query = \DB::prepareQuery("SELECT ID FROM Posts WHERE type = %s AND isPublished = 1 AND timePublished < %u ORDER BY timePublished DESC LIMIT 1", POST_TYPE_DIARY, $Diary->timePublished);
			if( ($postID = \DB::queryAndFetchOne($query)) && ($Diary_Earlier = \Post\Diary::loadOneFromDB($postID, true)) )
			{
				printf('<a class="prev" href="%s" title="Äldre">&laquo; %s</a>', $Diary_Earlier->getURL(), htmlspecialchars($Diary_Earlier->title));
			}
			?>
		</div>
	</div>
</div>
<?
require FOOTER;