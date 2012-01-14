<?
namespace Post;

class Track extends \Post
{
	const TYPE = \POST_TYPE_TRACK;

	public
		$artist,
		$track,
		$artistURL,
		$trackURL,
		$spotifyURI;


	public static function loadFromDB($postIDs)
	{
		$posts = parent::loadFromDB($postIDs);

		$query = \DB::prepareQuery("SELECT * FROM PostTracks WHERE postID IN %a", array_keys($posts));

		$result = \DB::queryAndFetchResult($query);

		while($track = \DB::assoc($result))
		{
			$postID = (int)$track['postID'];
			if( isset($posts[$postID]) )
			{
				$Post = $posts[$postID];

				$Post->lastFmID = (int)$track['lastFmID'];
				$Post->artist = $track['artist'];
				$Post->track = $track['track'];
				$Post->artistURL = $track['artistURL'];
				$Post->trackURL = $track['trackURL'];
				$Post->spotifyURI = ($track['spotifyURI'] ?: ($Post->artist || $Post->track) ? 'spotify:search:' . urlencode(trim(preg_replace('/[-]/', ' ', $Post->artist . ' ' . $Post->track))) : null);
			}
		}

		return $posts;
	}

	public static function saveToDB(Track $Post)
	{
		parent::saveToDB($Post);

		$query = \DB::prepareQuery("REPLACE INTO
			PostTracks (
				postID,
				lastFmID,
				artist,
				track,
				artistURL,
				trackURL,
				spotifyURI
			) VALUES (
				%u,
				%u,
				%s,
				%s,
				NULLIF(%s, ''),
				NULLIF(%s, ''),
				NULLIF(%s, '')
			)",
			$Post->postID,
			$Post->lastFmID,
			$Post->artist,
			$Post->track,
			$Post->artistURL,
			$Post->trackURL,
			$Post->spotifyURI);

		#throw New Exception($query);

		\DB::query($query);

		return true;
	}



	public function getSpotifyURI()
	{
		$spotifyURI = null;

		$url = sprintf('http://ws.spotify.com/search/1/track?q=%s', urlencode(preg_replace('/[&-]/', ' ', $this->artist . ' ' . $this->track)));

		$XML = simplexml_load_file($url);
		$XML->registerXPathNamespace('s', 'http://www.spotify.com/ns/music/1');

		foreach($XML->xpath('/s:tracks/s:track[@href]') as $track)
			if( $spotifyURI = $track['href'] ) break;

		return $spotifyURI;
	}

	public function getSummary()
	{
		return sprintf(
			'<a href="%s" class="spotifySearch">Spotify</a>' .
			'<a href="%s" class="lastFMLookUp">LastFM Lookup</a>',
			$this->spotifyURI,
			$this->artistURL
		);
	}

	public function getURL()
	{
		return sprintf('/TrackView.php?trackID=%u', $this->postID);
	}
}