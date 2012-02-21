<?
try
{
	$timeStart = isset($_GET['uts_f']) ? $_GET['uts_f'] : time() - 60*60;
	$timeEnd = isset($_GET['uts_t']) ? $_GET['uts_t'] : time();

	printf('<h1>%s</h1>', sprintf(_("Added between %s - %s"), \Format::timestamp($timeStart, true), \Format::timestamp($timeEnd, true)));

	$query = \DB::prepareQuery("SELECT
			ut.ID
		FROM
			Music_UserTracks ut
		WHERE
			ut.userID = %u
			AND ut.timeCreated BETWEEN %d AND %d
		ORDER BY
			ut.timeCreated DESC",
		$User->userID,
		$timeStart,
		$timeEnd);

	$userTrackIDs = \DB::queryAndFetchArray($query);

	if( count($userTrackIDs) == 0 )
		throw New \Exception(sprintf(_("No tracks found between %s and %s"), \Format::timestamp($timeStart, true), \Format::timestamp($timeEnd, true)));

	$userTracks = \Music\UserTrack::loadFromDB($userTrackIDs);

	echo \Element\Tracklist::createFromUserTracks($userTracks);
}
catch(\Exception $e)
{
	echo \Element\Message::error($e->getMessage());
}