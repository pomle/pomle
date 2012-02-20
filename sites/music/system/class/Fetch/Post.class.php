<?
namespace Fetch;

class Post
{
	public function __construct($limit, $offset = 0, $sort = 'timePublished')
	{
		$this->limit = min($limit, 100);
		$this->offset = $offset;
		$this->sort = in_array($sort, array('title', 'timePublished')) ? $sort : 'timePublished';
	}


	public function getAlbums()
	{
		$query = \DB::prepareQuery("SELECT
				p.ID
			FROM
				Posts p
				JOIN PostAlbums pa ON pa.postID = p.ID
			WHERE
				p.isPublished = 1
				AND p.type = %s
			ORDER BY
				{$this->sort} DESC
			LIMIT %u, %u",
			POST_TYPE_ALBUM,
			$this->offset,
			$this->limit);

		$postIDs = $this->queryAndFetchArrayAndTotal($query);

		$posts = \Post\Album::loadFromDB($postIDs);

		return $posts;
	}

	public function getDiaries()
	{
		$query = \DB::prepareQuery("SELECT
				p.ID
			FROM
				Posts p
				JOIN PostDiaries pd ON pd.postID = p.ID
			WHERE
				p.isPublished = 1
				AND p.type = %s
			ORDER BY
				{$this->sort} DESC
			LIMIT %u, %u",
			POST_TYPE_DIARY,
			$this->offset,
			$this->limit);

		$postIDs = $this->queryAndFetchArrayAndTotal($query);

		$posts = \Post\Diary::loadFromDB($postIDs);

		return $posts;
	}

	public function getSearch($question)
	{
		### Search Diary
		$query = \DB::prepareQuery("SELECT
				p.ID,
				p.timePublished
			FROM
				PostDiaries pd
				JOIN Posts p ON p.ID = pd.postID
			WHERE
				p.isPublished = 1
				AND (p.title LIKE %S OR pd.content LIKE %S)
			ORDER BY
				{$this->sort} DESC",
			$question,
			$question);

		$diaryIDs = \DB::queryAndFetchArray($query);

		### Search Album
		$query = \DB::prepareQuery("SELECT
				p.ID,
				p.timePublished
			FROM
				PostAlbums pa
				JOIN Posts p ON p.ID = pa.postID
				LEFT JOIN PostAlbumMedia pam ON pam.postID = pa.postID
			WHERE
				p.isPublished = 1
				AND pam.isVisible = 1
				AND (p.title LIKE %S OR pam.comment LIKE %S OR pam.tags LIKE %S)
			GROUP BY
				p.ID
			ORDER BY
				{$this->sort} DESC",
			$question,
			$question,
			$question);

		$albumIDs = \DB::queryAndFetchArray($query);

		### Search Tracks
		$query = \DB::prepareQuery("SELECT
				p.ID,
				p.timePublished
			FROM
				PostTracks plt
				JOIN Posts p ON p.ID = plt.postID
			WHERE
				p.isPublished = 1
				AND (plt.artist LIKE %S OR plt.track LIKE %S)
			ORDER BY
				{$this->sort} DESC",
			$question,
			$question);

		$trackIDs = \DB::queryAndFetchArray($query);


		$postIDs = $diaryIDs + $albumIDs + $trackIDs;


		arsort($postIDs, SORT_NUMERIC);

		$posts = \Post::loadAutoTypedFromDB(array_slice(array_keys($postIDs), $this->offset, $this->limit));

		return $posts;
	}

	public function getTimeline()
	{
		$query = \DB::prepareQuery("SELECT
				p.ID,
				COALESCE(MAX(pam.timeCreated), p.timePublished) AS timePublished
			FROM
				Posts p
				LEFT JOIN PostDiaries pd ON pd.postID = p.ID
				LEFT JOIN PostAlbumMedia pam ON pam.postID = p.ID
			WHERE
				p.isPublished = 1
				AND p.type IN %A
			GROUP BY
				p.ID
			ORDER BY
				{$this->sort} DESC
			LIMIT %u, %u",
			array(POST_TYPE_ALBUM, POST_TYPE_DIARY, POST_TYPE_TRACK),
			$this->offset,
			$this->limit);

		$postIDs = \DB::queryAndFetchArray($query);

		$posts = \Post::loadAutoTypedFromDB(array_keys($postIDs));

		foreach($postIDs as $postID => $timePublished)
		{
			if( !isset($posts[$postID]) ) continue;
			$Post = $posts[$postID];
			$Post->timePublished = $timePublished;
		}

		return $posts;
	}

	public function getTracks()
	{
		$query = \DB::prepareQuery("SELECT
				p.ID
			FROM
				Posts p
				JOIN PostTracks plt ON plt.postID = p.ID
			WHERE
				p.isPublished = 1
				AND p.type = %s
			ORDER BY
				{$this->sort} DESC
			LIMIT %u, %u",
			POST_TYPE_TRACK,
			$this->offset,
			$this->limit);

		$postIDs = $this->queryAndFetchArrayAndTotal($query);

		return \Post\Track::loadFromDB($postIDs);
	}

	public function queryAndFetchArrayAndTotal($query)
	{
		/*if( preg_match('/(FROM.+)(ORDER|LIMIT)/smU', $query, $match) )
		{
			$this->rowTotal = \DB::queryAndFetchOne("SELECT COUNT(*) " . $match[1]);
		}*/

		return \DB::queryAndFetchArray($query);
	}
}