<?
require '../Init.inc.php';

$css[] = '/css/Diary.css';

if( !$Diary = \Diary::loadOneFromDB($_GET['diaryID']) )
	die('Not Found');

require HEADER;
?>
<div class="diary">
	<div class="header">
		<h1><? echo htmlspecialchars($Diary->title); ?></h1>
		<ul class="details">
			<li><span class="timestamp"><? echo htmlspecialchars(\Format::timestamp($Diary->timePublished)); ?></span></li>
		</ul>
	</div>
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
require FOOTER;