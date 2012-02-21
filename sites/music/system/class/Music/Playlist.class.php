<?
namespace Music;

class PlaylistException extends \Exception
{}

class Playlist
{
	protected
		$playlistID,
		$Image,
		$timeCreated,
		$userTracks;

	public
		$title;


	public static function addUserTracksToDB($playlistID, Array $userTrackIDs, Array $userIDs)
	{
		if( count($userTrackIDs) != count($userIDs) )
			throw New PlaylistException('userTrackID array and userID array must have equal length');

		$timeCreated = time();

		$track_Insert = "INSERT INTO
			Music_PlaylistUserTracks
			(
				ID,
				playlistID,
				userTrackID,
				adder_userID,
				timeCreated
			) VALUES";

		$track_Value = "(
				NULL,
				NULLIF(%u, 0),
				NULLIF(%u, 0),
				NULLIF(%u, 0),
				NULLIF(%u, 0)
			)";

		$track_Values = '';

		### Prepares the first userID from array
		$userID = reset($userIDs);

		foreach($userTrackIDs as $userTrackID)
		{
			$track_Values .= \DB::prepareQuery($tracks_Value,
				$playlistID,
				$userTrackID,
				$userID,
				$timeCreated
			) . ",";

			$userID = next($userIDs);
		}

		$track_Query = $track_Insert . rtrim($track_Values, ',');

		return \DB::queryAndCountAffected($track_Query);
	}


	public static function createInDB(self $Playlist)
	{
		$timeCreated = time();

		$query = \DB::prepareQuery("INSERT INTO
			Music_Playlists
			(
				ID,
				timeCreated,
				title
			) VALUES(
				NULL,
				NULLIF(%u, 0),
				NULLIF(%s, '')
			)",
			$timeCreated,
			$Playlist->title);

		if( $playlistID = \DB::queryAndGetID($query) )
		{
			$Playlist->playlistID = $playlistID;
			$Playlist->timeCreated = $timeCreated;
		}

		return true;
	}


	public static function loadFromDB($playlistIDs, $skipTracks = false)
	{
		if( !($returnArray = is_array($playlistIDs)) )
			$playlistIDs = array($playlistIDs);

		$playlists = array_fill_keys($playlistIDs, false);

		$query = \DB::prepareQuery("SELECT
				p.ID AS playlistID,
				p.image_mediaID,
				p.timeCreated,
				p.title
			FROM
				Music_Playlists p
			WHERE
				p.ID IN %a",
			$playlistIDs);

		$Result = \DB::queryAndFetchResult($query);

		$image_mediaIDs = array();

		while($playlist = $Result->fetch_assoc())
		{
			$Playlist = new self();

			$Playlist->title = $playlist['title'];

			$Playlist->playlistID = (int)$playlist['playlistID'];
			$Playlist->image_mediaID = $playlist['image_mediaID'];

			$Playlist->timeCreated = (int)$playlist['timeCreated'] ?: null;

			$playlists[$Playlist->playlistID] = $Playlist;

			if( $Playlist->image_mediaID )
				$image_mediaIDs[] = $Playlist->image_mediaID;
		}

		$playlists = array_filter($playlists);
		$playlistIDs = array_keys($playlists);

		if( count($image_mediaIDs) )
		{
			$images = \Manager\Media::loadFromDB($image_mediaIDs);
			foreach($playlists as $Playlist)
			{
				if( isset($images[$Playlist->image_mediaID]) ) $Playlist->setImage($images[$Playlist->image_mediaID]);
				unset($Playlist->image_mediaID);
			}
			unset($images);
		}


		if( $skipTracks !== true )
		{
			$query = \DB::prepareQuery("SELECT
					put.playlistID,
					put.userTrackID,
					put.adder_userID,
					put.timeCreated
				FROM
					Music_PlaylistUserTracks put
				WHERE
					put.playlistID IN %a",
				$playlistIDs);

			$Result = \DB::queryAndFetchResult($query);

			$playlistIDs = array();
			$userTrackIDs = array();

			while($row = $Result->fetch_assoc())
			{
				$playlistID = (int)array_shift($row);
				$userTrackID = (int)array_shift($row);

				$userTrackIDs[] = $userTrackID;
				$playlistIDs[$playlistID][$userTrackID] = $row;
			}


			$userTracks = UserTrack::loadFromDB($userTrackIDs);

			foreach($playlistIDs as $playlistID => $userTrackIDs)
			{
				if( !isset($playlists[$playlistID]) ) continue;

				$Playlist = $playlists[$playlistID];

				foreach($userTrackIDs as $userTrackID => $meta)
				{
					if( !isset($userTracks[$userTrackID]) ) continue;

					$UserTrack = $userTracks[$userTrackID];
					$UserTrack->adder_userID = $meta['adder_userID'];
					$UserTrack->timeAdded = (int)$meta['timeCreated'] ?: null;

					$Playlist->addUserTrack($UserTrack);
				}
			}
		}

		return $returnArray ? $playlists : reset($playlists);
	}

	public static function removeUserTracksFromDB($playlistUserTrackIDs)
	{
		$query = \DB::prepareQuery("DELETE FROM Music_PlaylistUserTracks WHERE ID = %a", $playlistUserTrackIDs);

		return \DB::queryAndCountAffected($track_Query);
	}

	public static function saveToDB($playlists)
	{
		if( !is_array($playlists) )
			$playlists = array($playlists);

		$timeModified = time();

		$playlist_Insert = "INSERT INTO Music_Playlists (
			ID,
			image_mediaID,
			title
		) VALUES";

		$playlist_Value = "(
			%u,
			NULLIF(%u, 0),
			NULLIF(%s, '')
		)";

		$playlist_Update = " ON DUPLICATE KEY UPDATE
			image_mediaID = VALUES(image_mediaID),
			title = VALUES(title)";

		$playlist_Values = '';


		foreach($playlists as $index => $Playlist)
		{
			if( !$Playlist instanceof self )
				throw New TrackException(sprintf('Playlist array index %s must be instance of %s', $index, __CLASS__));

			if( !isset($Playlist->playlistID) )
				self::createInDB($Playlist);

			$playlist_Values .= \DB::prepareQuery($playlist_Value,
				$Playlist->playlistID,
				isset($Playlist->Image) ? $Playlist->Image->mediaID : 0,
				$Playlist->title
			) . ",";
		}

		$playlist_InsertQuery = $playlist_Insert . rtrim($playlist_Values, ',') . $playlist_Update;
		\DB::query($playlist_InsertQuery);

		return true;
	}


	public function __construct()
	{
		$this->userTracks = array();
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
		return $this->title;
	}

	public function addUserTrack(UserTrack $UserTrack)
	{
		$this->userTracks[] = $UserTrack;
		return $this;
	}

	public function appendTrack($userTrackID, $userID)
	{
		return (self::addUserTracksToDB($this->playlistID, array($userTrackID), array($userID)) > 0);
	}

	public function dropTrack($playlistUserTrackID, $userID)
	{
		### Check to see if this track is actually on this playlist and is owned by the user before triggering remove
		$query = \DB::prepareQuery("SELECT
				ID
			FROM
				Music_PlaylistUserTracks
			WHERE
				AND playlistID = %u
				AND adder_userID = %u
				ID = %u",
			$this->playlistID,
			$userID,
			$playlistUserTrackID);

		if( $playlistUserTrackIDs = \DB::queryAndFetchArray($query) )
			return (self::removeUserTracksFromDB($playlistUserTrackIDs) > 0);

		return false;
	}

	public function setImage(\Media\Image $Image)
	{
		$this->Image = $Image;
		return $this;
	}
}