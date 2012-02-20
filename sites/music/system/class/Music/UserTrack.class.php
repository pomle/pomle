<?
namespace Music;

class UserTrackException extends \Exception
{}

class UserTrack
{
	protected
		$userTrackID,
		$playcount,
		$Track;

	public
		$userID,
		$trackID,
		$filename,
		$artist,
		$title;


	public static function changePlayCount($userTrackIDs, $diff)
	{
		$query = \DB::prepareQuery("UPDATE Music_UserTracks SET playCount = playCount + %d WHERE ID IN %a", $diff, $userTrackIDs);
		\DB::query($query);

		return true;
	}


	public static function createInDB(self $UserTrack)
	{
		$timeCreated = time();

		$query = \DB::prepareQuery("INSERT INTO
			Music_UserTracks
			(
				ID,
				userID,
				trackID,
				timeCreated
			) VALUES(
				NULL,
				%u,
				%u,
				NULLIF(%u, 0)
			)",
			$UserTrack->userID,
			$UserTrack->Track->trackID,
			$timeCreated);

		if( $userTrackID = \DB::queryAndGetID($query) )
		{
			$UserTrack->userTrackID = $userTrackID;
			$UserTrack->timeCreated = $timeCreated;
		}

		return true;
	}

	public static function loadFromDB($userTrackIDs)
	{
		if( !($returnArray = is_array($userTrackIDs)) )
			$userTrackIDs = array($userTrackIDs);

		$userTracks = array_fill_keys($userTrackIDs, false);

		$query = \DB::prepareQuery("SELECT
				ut.ID AS userTrackID,
				ut.userID,
				ut.trackID,
				ut.timeCreated,
				ut.playcount,
				ut.filename,
				ut.artist,
				ut.title
			FROM
				Music_UserTracks ut
			WHERE
				ut.ID IN %a",
			$userTrackIDs);

		$result = \DB::queryAndFetchResult($query);

		$trackIDs = array();

		while($userTrack = \DB::assoc($result))
		{
			$UserTrack = new self($userTrack['userID']);

			$UserTrack->userTrackID = (int)$userTrack['userTrackID'];
			$UserTrack->userID = (int)$userTrack['userID'];
			$UserTrack->trackID = (int)$userTrack['trackID'];

			$UserTrack->timeCreated = (int)$userTrack['timeCreated'] ?: null;
			$UserTrack->playcount = (int)$userTrack['playcount'];

			$UserTrack->filename = $userTrack['filename'];
			$UserTrack->artist = $userTrack['artist'];
			$UserTrack->title = $userTrack['title'];

			$userTracks[$UserTrack->userTrackID] = $UserTrack;

			$trackIDs[] = $userTrack['trackID'];
		}

		$tracks = Track::loadFromDB($trackIDs);

		foreach($userTracks as $UserTrack)
		{
			if( isset($tracks[$UserTrack->trackID]) )
			{
				$Track = $tracks[$UserTrack->trackID];

				$UserTrack->setTrack($Track);

				if( !isset($UserTrack->artist) )
					$UserTrack->artist = $Track->getArtist();

				if( !isset($UserTrack->title) )
					$UserTrack->title = (string)$Track;
			}
		}

		$userTracks = array_filter($userTracks);

		return $returnArray ? $userTracks : reset($userTracks);
	}

	public static function saveToDB($userTracks)
	{
		if( !is_array($userTracks) )
			$userTracks = array($userTracks);


		$timeModified = time();

		$userTrack_Insert = "INSERT INTO Music_UserTracks (
			ID,
			filename,
			artist,
			title
		) VALUES";

		$userTrack_Value = "(
			%u,
			NULLIF(%s, ''),
			NULLIF(%s, ''),
			NULLIF(%s, '')
		)";

		$userTrack_Update = " ON DUPLICATE KEY UPDATE
			filename = VALUES(filename),
			artist = VALUES(artist),
			title = VALUES(title)";


		$userTrack_Values = '';

		foreach($userTracks as $UserTrack)
		{
			if( !isset($UserTrack->userTrackID) )
				self::createInDB($UserTrack);

			$userTrack_Values .= \DB::prepareQuery($userTrack_Value,
				$UserTrack->userTrackID,
				$UserTrack->filename,
				$UserTrack->artist,
				$UserTrack->title) . ",";
		}

		$userTrack_InsertQuery = $userTrack_Insert . rtrim($userTrack_Values, ',') . $userTrack_Update;
		\DB::query($userTrack_InsertQuery);

		return true;
	}


	public function __construct($userID)
	{
		$this->userID = $userID;

		$this->playcount = 0;
	}

	public function __get($key)
	{
		return $this->$key;
	}

	public function __isset($key)
	{
		return isset($this->$key);
	}

	public function __toString()
	{
		return sprintf('%s - %s', $this->artist, $this->title);
	}


	public function registerPlay()
	{
		return self::changePlayCount($this->userTrackID, 1);
	}

	public function setTrack(Track $Track)
	{
		$this->Track = $Track;
		return $this;
	}
}