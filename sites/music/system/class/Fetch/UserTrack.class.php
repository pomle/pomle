<?
namespace Fetch;

class UserTrack
{
	public function __construct(\User $User)
	{
		$this->userID = $User->userID;
	}

	public function getLastPlaylist()
	{
		$query = \DB::prepareQuery("SELECT ID FROM Music_UserTracks WHERE userID = %u ORDER BY timeCreated DESC LIMIT 20", $this->userID);
		$userTrackIDs = \DB::queryAndFetchArray($query);

		$userTracks = \Music\UserTrack::loadFromDB($userTrackIDs);

		return $userTracks;
	}

	public function getRecent()
	{
		$query = \DB::prepareQuery("SELECT ID FROM Music_UserTracks WHERE userID = %u ORDER BY timeCreated DESC LIMIT 20", $this->userID);
		$userTrackIDs = \DB::queryAndFetchArray($query);

		$userTracks = \Music\UserTrack::loadFromDB($userTrackIDs);

		return $userTracks;
	}
}