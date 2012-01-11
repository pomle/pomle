<?
namespace Post;

class Album extends \Post
{
	const TYPE = \POST_TYPE_ALBUM;


	public
		$description;


	public static function loadFromDB($postIDs, $skipMedia = false)
	{
		$posts = parent::loadFromDB($postIDs);

		$postIDs = array_keys($posts);

		$query = \DB::prepareQuery("SELECT
				postID,
				description
			FROM
				PostAlbums
			WHERE
				postID IN %a",
			$postIDs);

		$result = \DB::queryAndFetchResult($query);

		while($post = \DB::assoc($result))
		{
			$postID = (int)$post['postID'];
			$Post = $posts[$postID];
			$Post->description = $post['description'];
			$Post->skipMedia = $skipMedia;
		}

		if( $skipMedia === false )
		{
			$query = \DB::prepareQuery("SELECT
					ID,
					postID
				FROM
					PostAlbumMedia
				WHERE
					isVisible = 1
					AND postID IN %a
				ORDER BY
					sortOrder ASC",
				$postIDs);

			$albumMedias = \DB::queryAndFetchArray($query);

			$postAlbumMedias = Album\Media::loadFromDB(array_keys($albumMedias));

			foreach($albumMedias as $postAlbumMediaID => $postID)
			{
				if( isset($postAlbumMedias[$postAlbumMediaID]) )
				{
					$postID = (int)$postID;
					$posts[$postID]->addMedia($postAlbumMedias[$postAlbumMediaID]);
				}
			}
		}

		return $posts;
	}

	public static function saveToDB(Album $Post)
	{
		parent::saveToDB($Post);

		$query = \DB::prepareQuery("INSERT INTO
			PostAlbums (
				postID,
				description
			) VALUES(
				%u,
				NULLIF(%s, '')
			) ON DUPLICATE KEY UPDATE
				description = VALUES(description)",
			$Post->postID,
			$Post->description);

		#throw New \Exception($query);

		\DB::query($query);

		if( $Post->skipMedia === false )
		{
			$sortOrder = 0;
			foreach($Post->media as $Media)
			{
				if( !isset($Media->sortOrder) ) $Media->sortOrder = $sortOrder++;
				self::saveMediaToDB($Post->postID, $Media);
			}
		}

		return true;
	}

	public static function saveMediaToDB($postID, \Media\Common\_Root $Media)
	{
		$query = \DB::prepareQuery("INSERT INTO
				PostAlbumMedia (
					ID,
					postID,
					mediaID,
					timeCreated,
					isVisible,
					sortOrder,
					comment,
					tags
				) VALUES(
					NULLIF(%u, 0),
					%u,
					%u,
					UNIX_TIMESTAMP(),
					%u,
					%u,
					NULLIF(%s, ''),
					NULLIF(%s, '')
				) ON DUPLICATE KEY UPDATE
					isVisible = VALUES(isVisible),
					sortOrder = VALUES(sortOrder),
					comment = VALUES(comment),
					tags = VALUES(tags)",
				$Media->postAlbumMediaID,
				$postID,
				$Media->mediaID,
				$Media->isVisible,
				$Media->sortOrder,
				$Media->comment,
				$Media->tags);

		if( $rowID = \DB::queryAndGetID($query) )
			$Media->postAlbumMediaID = (int)$rowID;

		return true;
	}


	public function __construct()
	{
		$this->skipMedia = false;
		parent::__construct();
	}


	public function getSummary()
	{
		return $this->description;
	}

	public function getURL()
	{
		return sprintf('/AlbumView.php?albumID=%u', $this->postID);
	}
}