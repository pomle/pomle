<?
try
{
	$y = isset($_GET['y']) ? $_GET['y'] : date('Y', time());
	$m = isset($_GET['m']) ? $_GET['m'] : date('n', time());

	$timeStart = mktime(0, 0, 0, $m, 1, $y);
	$timeEnd = mktime(0, 0, 0, $m+1, 1, $y);

	printf('<h1>%s</h1>', sprintf(_("Added %s"), strftime('%B %Y', $timeStart)));

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
		throw New \Exception(sprintf(_("No tracks found between %s and %s"), \Format::timestamp($timeStart), \Format::timestamp($timeEnd)));

	$userTracks = \Music\UserTrack::loadFromDB($userTrackIDs);

	echo \Element\Tracklist::createFromUserTracks($userTracks);
}
catch(\Exception $e)
{
	echo \Element\Message::error($e->getMessage());
}