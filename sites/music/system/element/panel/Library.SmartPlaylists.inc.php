<h1><? echo htmlspecialchars(_('Smart Playlists')); ?></h1>

<?
$query = \DB::prepareQuery("SELECT artist AS name, COUNT(*) AS trackCount FROM Music_UserTracks WHERE userID = %u GROUP BY artist ORDER BY artist ASC", $User->userID);
$Result = \DB::queryAndFetchResult($query);

echo '<ul>';

while($artist = $Result->fetch_assoc())
	printf('<li><a class="panelLibrary" href="/ajax/Panel.php?type=Library&amp;name=ListArtist&amp;artist=%s">%s</a> (%d)</li>', htmlspecialchars(urlencode($artist['name'])), htmlspecialchars($artist['name']), $artist['trackCount']);

echo '</ul>';