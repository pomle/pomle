<h1><? echo htmlspecialchars(str_replace('%USERNAME%', $User->username, _('Home of %USERNAME%'))); ?></h1>

<ul>
<?
$query = \DB::prepareQuery("SELECT COUNT(*) FROM Music_UserTracks ut WHERE ut.userID = %u", $User->userID);
$userTrackCount = \DB::queryAndFetchOne($query);

$query = \DB::prepareQuery("SELECT SUM(playcount) FROM Music_UserTracks ut WHERE ut.userID = %u", $User->userID);
$userPlayCountTotal = \DB::queryAndFetchOne($query);


?>
<ul>
	<li><? echo htmlspecialchars(sprintf(_("Tracks in Library: %d"), $userTrackCount)); ?></li>
	<li><? echo htmlspecialchars(sprintf(_("Total track playcount: %d"), $userPlayCountTotal)); ?></li>
</ul>


<ul>
	<li><a href="#SmartPlaylists" class="panelLibrary"><? echo _("SmartPlaylists"); ?></a></li>
</ul>
<?