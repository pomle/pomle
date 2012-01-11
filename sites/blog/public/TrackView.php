<?
require '../Init.inc.php';

$css[] = '/css/Track.css';

if( !$Track = \Post\Track::loadOneFromDB($_GET['diaryID']) )
{
	$Track = new \Post\Track();
	$Track->title = '404';
	$Track->timestamp = 'Sidan kunde inte hittas';
	header('HTTP/1.0 404 Not Found');
}
else
{
	$Track->timestamp = \Format::timestamp($Track->timePublished);
}

$pageTitle = $Track->title;

require HEADER;
?>
<div class="track">
	<div class="header">
		<h1><? echo htmlspecialchars($Track->title); ?></h1>
		<ul class="details">
			<li><span class="timestamp"><? echo htmlspecialchars($Track->timestamp); ?></span></li>
		</ul>
	</div>
</div>
<?
require FOOTER;