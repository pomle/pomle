<?
class Album extends Post
{
	const TYPE = POST_TYPE_ALBUM;

	protected
		$PreviewMedia,
		$media;


	public static function addToDB()
	{
		$Post = parent::addToDB();
		$query = \DB::prepareQuery("INSERT INTO PostAlbums (postID) VALUES(%u)", $Post->postID);
		\DB::query($query);
		return $Post;
	}

	public static function loadFromDB($postIDs, $skipMedia = false)
	{
		$albums = parent::loadFromDB($postIDs);

		$albumIDs = array_keys($albums);


		$query = \DB::prepareQuery("SELECT pa.postID, m.ID AS mediaID FROM PostAlbums pa JOIN Media m ON m.ID = pa.previewMediaID AND pa.postID IN %a", $albumIDs);
		$previewMediaIDs = \DB::queryAndFetchArray($query);

		$media = \Manager\Media::loadFromDB($previewMediaIDs);

		foreach($previewMediaIDs as $postID => $mediaID)
		{
			if( !isset($media[$mediaID]) || !isset($albums[$postID]) ) continue;

			$albums[$postID]->PreviewMedia = $media[$mediaID];
		}


		if( $skipMedia === false )
		{
			$query = \DB::prepareQuery("SELECT
					mediaID,
					postID,
					isVisible,
					comment,
					tags
				FROM
					PostAlbumMedia
				WHERE
					isVisible = 1
					AND postID IN %a
				ORDER BY
					sortOrder ASC",
				$albumIDs);

			$albumMedias = \DB::queryAndFetchArray($query);

			$mediaIDs = array_keys($albumMedias);

			$media = \Manager\Media::loadFromDB($mediaIDs);

			foreach($albumMedias as $mediaID => $albumMedia)
			{
				if( !isset($media[$mediaID]) ) continue;

				$postID = (int)$albumMedia['postID'];
				$Album = $albums[$postID];
				$Media = $media[$mediaID];

				$Media->isVisible = (bool)$albumMedia['isVisible'];
				$Media->comment = $albumMedia['comment'];
				$Media->tags = $albumMedia['tags'];

				$Album->addMedia($Media);
			}
		}

		return $albums;
	}


	public function __construct()
	{
		parent::__construct();
		$this->media = array();
	}


	public function addMedia(\Media\Common\_Root $Media)
	{
		$this->media[] = $Media;
		return $this;
	}
}