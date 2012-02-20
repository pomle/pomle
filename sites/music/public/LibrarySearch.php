<?
require '../Init.inc.php';

$search = str_replace(' ', '%', $_GET['q']);

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
	$search,
	$search);

$userTrackIDs = \DB::queryAndFetchArray($query);

$userTracks = \Music\UserTrack::loadFromDB($userTrackIDs);

require HEADER;

echo \Element\UserTrackList::createFromUserTracks($userTracks);

require FOOTER;
