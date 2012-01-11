<?
namespace Post\Album;

class Media
{
	public static function loadFromDB($postAlbumMediaIDs)
	{
		$postAlbumMedias = array_fill_keys($postAlbumMediaIDs, false);

		$query = \DB::prepareQuery("SELECT
				ID AS postAlbumMediaID,
				mediaID,
				postID,
				isVisible,
				sortOrder,
				comment,
				tags
			FROM
				PostAlbumMedia
			WHERE
				ID IN %a",
			$postAlbumMediaIDs);

		$result = \DB::queryAndFetchResult($query);

		$albumMedias = $mediaIDs = array();

		while($row = \DB::assoc($result))
		{
			$mediaIDs[] = (int)$row['mediaID'];
			$albumMedias[] = $row;
		}

		$medias = \Manager\Media::loadFromDB($mediaIDs);

		foreach($albumMedias as $albumMedia)
		{
			$mediaID = (int)$albumMedia['mediaID'];

			if( isset($medias[$mediaID]) )
			{
				$Media = clone $medias[$mediaID];

				$Media->postAlbumMediaID = (int)$albumMedia['postAlbumMediaID'];
				$Media->isVisible = (bool)$albumMedia['isVisible'];
				$Media->sortOrder = (int)$albumMedia['sortOrder'];
				$Media->comment = $albumMedia['comment'];
				$Media->tags = $albumMedia['tags'];

				$postAlbumMedias[$Media->postAlbumMediaID] = $Media;
			}
		}

		$postAlbumMedias = array_filter($postAlbumMedias);

		return $postAlbumMedias;
	}

	public static function saveToDB(\Post\Album $Post, \Media\Common\_Root $Media)
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
				$Post->postID,
				$Media->mediaID,
				$Media->isVisible,
				$Media->sortOrder,
				$Media->comment,
				$Media->tags);

		if( $rowID = \DB::queryAndGetID($query) )
			$Media->postAlbumMediaID = (int)$rowID;

		return true;
	}
}