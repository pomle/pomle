<?
require '../Init.inc.php';

if( !$UserTrack = \Music\UserTrack::loadFromDB($_GET['userTrackID']) )
	die(MESSAGE_USERTRACK_MISSING);

require HEADER;
?>
<div class="userTrack">
	<div class="image">
	</div>

	<div class="info">
		<h1><? echo htmlspecialchars($UserTrack->title); ?></h1>
		<h2><? echo htmlspecialchars($UserTrack->artist); ?></h2>
	</div>

</div>
<?
require FOOTER;
