<h1><? echo htmlspecialchars(_('Artists')); ?></h1>

<?
$query = \DB::prepareQuery("SELECT artist AS name, COUNT(*) AS trackCount FROM Music_UserTracks WHERE userID = %u GROUP BY artist ORDER BY artist ASC", $User->userID);
$Result = \DB::queryAndFetchResult($query);

echo '<ul>';

while($artist = $Result->fetch_assoc())
	printf('<li>%s (%d)</li>', libraryLink(htmlspecialchars($artist['name']), 'ListArtist', 'artist=' . urlencode($artist['name'])), $artist['trackCount']);

echo '</ul>';