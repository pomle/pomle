<?
try
{
	if( !isset($_GET['artist']) )
		throw New \Exception('No artist name given');

	$artist = $_GET['artist'];

	printf('<h1>%s</h1>', htmlspecialchars($artist));

	$query = \DB::prepareQuery("SELECT
			ut.ID
		FROM
			Music_UserTracks ut
		WHERE
			ut.userID = %u
			AND
			(
				ut.artist = %s
			)
		ORDER BY
			ut.title ASC",
		$User->userID,
		$artist);

	$userTrackIDs = \DB::queryAndFetchArray($query);

	if( count($userTrackIDs) == 0 )
		throw New \Exception(sprintf(_("No matches found for \"%s\""), htmlspecialchars($artist)));

	$userTracks = \Music\UserTrack::loadFromDB($userTrackIDs);

	echo \Element\UserTrackList::createFromUserTracks($userTracks);
}
catch(\Exception $e)
{
	echo \Element\Message::error($e->getMessage());
}