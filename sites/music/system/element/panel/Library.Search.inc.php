<h1><? echo htmlspecialchars(_('Search')); ?></h1>
<?
$queryMinLen = 2;
$wildcards = array(' ', '*', '%');

try
{
	$search = $_GET['q'];

	if( strlen(str_replace($wildcards, '', $search)) < $queryMinLen )
		throw New \Exception(sprintf(_("Query too short. Minimum allowed length is %d _real_ characters"), $queryMinLen));

	$query_search = str_replace($wildcards, '%', $search);


	$query = \DB::prepareQuery("SELECT
			ut.ID
		FROM
			Music_UserTracks ut
			JOIN Music_Tracks t ON t.ID = ut.trackID
			JOIN Music_TrackArtists ta ON ta.trackID = t.ID
			JOIN Music_Artists a ON a.ID = ta.artistID
		WHERE
			ut.userID = %u
			AND
			(
				IFNULL(ut.artist, a.name) LIKE %S
				OR IFNULL(ut.title, t.title) LIKE %S
			)
		ORDER BY
			ut.playcount DESC",
		$User->userID,
		$query_search,
		$query_search);

	$userTrackIDs = \DB::queryAndFetchArray($query);

	if( count($userTrackIDs) == 0 )
		throw New \Exception(sprintf(_("No matches found for \"%s\""), htmlspecialchars($search)));

	$userTracks = \Music\UserTrack::loadFromDB($userTrackIDs);

	echo \Element\UserTrackList::createFromUserTracks($userTracks);
}
catch(\Exception $e)
{
	echo \Element\Message::error($e->getMessage());
}