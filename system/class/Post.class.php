<?
class Post extends \Manager\Common\DB
{
	public
		$postID,
		$isPublished,
		$timeCreated,
		$timeModified,
		$timePublished,
		$title,
		$uri;

	protected
		$PreviewMedia,
		$media;


	final public static function addToDB()
	{
		$Post = new static();
		static::saveToDB($Post);
		return $Post;
	}

	public static function loadAutoTypedFromDB($postIDs)
	{
		$query = \DB::prepareQuery("SELECT ID, type FROM Posts WHERE ID IN %a ORDER BY type", $postIDs);
		$result = \DB::queryAndFetchResult($query);

		$types = array();
		while($row = \DB::assoc($result))
			$types[$row['type']][] = (int)$row['ID'];

		$posts = array_fill_keys($postIDs, false);

		foreach($types as $type => $ids)
		{
			switch($type)
			{
				case POST_TYPE_ALBUM:
					$posts = array_replace($posts, \Album::loadFromDB($ids));
				break;

				case POST_TYPE_DIARY:
					$posts = array_replace($posts, \Diary::loadFromDB($ids));
				break;

				case POST_TYPE_TRACK:
					$posts = array_replace($posts, \Track::loadFromDB($ids));
				break;
			}
		}

		$posts = array_filter($posts);

		return $posts;
	}

	public static function loadFromDB($postIDs)
	{
		$posts = array_fill_keys($postIDs, false);

		$query = \DB::prepareQuery("SELECT
				p.ID AS postID,
				p.previewMediaID,
				p.isPublished,
				p.timeCreated,
				p.timeModified,
				p.timePublished,
				p.title,
				p.uri
			FROM
				Posts p
			WHERE
				p.ID IN %a", $postIDs);

		$result = \DB::queryAndFetchResult($query);

		$mediaIDs = array();

		while($post = \DB::assoc($result))
		{
			$Post = new static();

			$Post->postID = (int)$post['postID'];

			$Post->isPublished = (bool)$post['isPublished'];

			$Post->timeCreated = (int)$post['timeCreated'] ?: null;
			$Post->timeModified = (int)$post['timeModified'] ?: null;
			$Post->timePublished = (int)$post['timePublished'] ?: null;

			$Post->title = $post['title'];
			$Post->uri = $post['uri'];

			$mediaIDs[$Post->postID] = $Post->previewMediaID = (int)$post['previewMediaID'];

			$posts[$Post->postID] = $Post;
		}

		if( count($mediaIDs) )
		{
			$media = \Manager\Media::loadFromDB($mediaIDs);
			foreach($mediaIDs as $postID => $mediaID)
				if( isset($media[$mediaID]) )
					$posts[$postID]->setPreviewMedia($media[$mediaID]);
		}

		$posts = array_filter($posts);

		return $posts;
	}

	public static function saveToDB(\Post $Post)
	{
		if( !isset($Post->timeCreated) ) $Post->timeCreated = time();
		if( !isset($Post->uri) ) $Post->uri = $Post->title;

		$Post->uri = niceurl($Post->uri);

		$query = \DB::prepareQuery("INSERT INTO
			Posts (
				ID,
				previewMediaID,
				type,
				isPublished,
				timeCreated,
				timeModified,
				timePublished,
				title,
				uri
			) VALUES(
				NULLIF(%u, 0),
				NULLIF(%u, 0),
				NULLIF(%s, ''),
				%u,
				%u,
				%u,
				NULLIF(%u, 0),
				NULLIF(%s, ''),
				NULLIF(%s, '')
			) ON DUPLICATE KEY UPDATE
				previewMediaID = VALUES(previewMediaID),
				isPublished = VALUES(isPublished),
				timeModified = VALUES(timeModified),
				timePublished = VALUES(timePublished),
				title = VALUES(title),
				uri = VALUES(uri)",
			$Post->postID,
			isset($Post->PreviewMedia) ? $Post->PreviewMedia->mediaID : null,
			$Post::TYPE,
			$Post->isPublished,
			$Post->timeCreated,
			$Post->timeModified = time(),
			$Post->timePublished,
			$Post->title,
			$Post->uri);

		#throw New Exception($query);

		if( $postID = \DB::queryAndGetID($query) )
			$Post->postID = (int)$postID;

		return true;
	}


	public function __construct()
	{
		$this->isPublished = false;
		$this->media = array();
	}

	public function __get($key)
	{
		return $this->$key;
	}

	public function __isset($key)
	{
		return isset($this->$key);
	}


	public function addMedia(\Media\Common\_Root $Media)
	{
		$this->media[] = $Media;
		return $this;
	}

	public function getSummary()
	{
		return false;
	}

	public function getURL()
	{
		return false;
	}

	public function setPreviewMedia(\Media\Common\_Root $Media)
	{
		$this->PreviewMedia = $Media;
		return $this;
	}
}