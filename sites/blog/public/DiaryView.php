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
		<ul>
			<li><span class="timestamp"><? echo htmlspecialchars(\Format::timestamp($Diary->timePublished)); ?></span></li>
		</ul>
	</div>
	<?
	echo $Diary->getHTMLContent();
	?>
</div>
<?
require FOOTER;