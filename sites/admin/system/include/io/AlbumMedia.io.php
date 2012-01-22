<?
class AlbumMediaIO extends AjaxIO
{
	private function addMediaToAlbum(\Media $Media, $albumID)
	{
		$this->importArgs('insertOrder');

		if( $this->insertOrder == 'first' )
		{
			$query = \DB::prepareQuery("UPDATE PostAlbumMedia SET sortOrder = sortOrder + 1 WHERE postID = %u", $albumID);
			\DB::query($query);

			$query = \DB::prepareQuery("SELECT MIN(sortOrder) FROM PostAlbumMedia WHERE postID = %u", $albumID);
			$sortOrder = -1 + \DB::queryAndFetchOne($query);
		}
		else
		{
			$query = \DB::prepareQuery("SELECT MAX(sortOrder) FROM PostAlbumMedia WHERE postID = %u", $albumID);
			$sortOrder = 1 + \DB::queryAndFetchOne($query);
		}

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
				%d,
				null,
				null
			)",
			null,
			$albumID,
			$Media->mediaID,
			true,
			$sortOrder);

		#throw New Exception($query);

		$rowID = \DB::queryAndGetID($query);

		return $rowID;
	}

	public function delete()
	{
		$query = \DB::prepareQuery("DELETE FROM PostAlbumMedia WHERE ID = %u", $this->postAlbumMediaID);
		\DB::query($query);

		Message::addNotice(MESSAGE_ROW_DELETED);
	}

	public function download()
	{
		try
		{
			$url = $_POST['url'];

			$Media = \Operation\Media::downloadFileToLibrary($url);

			$result['postAlbumMediaID'] = $this->addMediaToAlbum($Media, $this->postID);

			Message::addNotice(sprintf('"%s" OK', $url));
		}
		catch(Exception $e)
		{
			Message::addError(sprintf('"%s" misslyckades: %s', $url, $e->getMessage()));
		}
	}

	public function importMedia()
	{
		$this->importArgs('mediaID');

		if( !$Media = \Manager\Media::loadOneFromDB($this->mediaID) )
			throw New Exception('Invalid Media ID');

		$this->addMediaToAlbum($Media, $this->postID);

		Message::addNotice(sprintf('Media #%u imported', $Media->mediaID));
	}

	public function load()
	{
		global $result;
		$query = \DB::prepareQuery("SELECT *, ID AS postAlbumMediaID FROM PostAlbumMedia WHERE ID = %u", $this->postAlbumMediaID);
		$result = \DB::queryAndFetchOne($query);
	}

	public function save()
	{
		$this->importArgs('isVisible', 'isAlbumPreview', 'comment', 'tags');

		$query = \DB::prepareQuery("UPDATE
				PostAlbumMedia
			SET
				isVisible = %u,
				comment = NULLIF(%s, ''),
				tags = NULLIF(%s, '')
			WHERE
				ID = %u",
			$this->isVisible,
			$this->comment,
			$this->tags,
			$this->postAlbumMediaID);

		\DB::query($query);

		if( $this->isAlbumPreview )
		{
			$query = \DB::prepareQuery("UPDATE Posts SET previewMediaID = (SELECT mediaID FROM PostAlbumMedia WHERE ID = %u) WHERE ID = %u", $this->postAlbumMediaID, $this->postID);
			\DB::query($query);
		}

		Message::addNotice(MESSAGE_ROW_UPDATED);
	}

	public function saveLayout()
	{
		$this->importArgs('sortOrder', 'isVisible', 'mediaID', 'postID', 'previewMediaID');

		### Delete IDs that are not in sortOrder list. Non-existing will obviously be skipped
		$query = \DB::prepareQuery("DELETE FROM PostAlbumMedia WHERE postID = %u AND NOT ID IN %a", $this->postID, $this->sortOrder);
		$affectedRows = \DB::queryAndCountAffected($query);

		if( $this->sortOrder )
		{
			$query = "INSERT INTO PostAlbumMedia (ID, postID, mediaID, isVisible, timeCreated, sortOrder) VALUES";

			foreach($this->sortOrder as $index => $postAlbumMediaID)
			{
				$sortOrder = $index;
				$query .= \DB::prepareQuery("(NULLIF(%u, 0), %u, %u, %u, UNIX_TIMESTAMP(), %u),",
					$postAlbumMediaID,
					$this->postID,
					$this->mediaID[$index],
					isset($this->isVisible[$index]),
					$sortOrder
				);
			}

			$query = trim($query, ',') . " ON DUPLICATE KEY UPDATE isVisible = VALUES(isVisible), sortOrder = VALUES(sortOrder)";
			$affectedRows = \DB::queryAndCountAffected($query);
		}


		$query = \DB::prepareQuery("UPDATE Posts SET previewMediaID = NULLIF(%u, 0) WHERE ID = %u", $this->previewMediaID, $this->postID);
		$affectedRows = \DB::queryAndCountAffected($query);


		Message::addNotice(MESSAGE_ROW_UPDATED);
	}

	public function saveMeta()
	{

	}

	public function upload()
	{
		if( !isset($_FILES) || !is_array($_FILES) )
			throw New Exception(_('Inga filer hittades i begäran'));

		$preferredMediaType = $_POST['preferredMediaType'] ?: null;

		foreach($_FILES as $file)
		{
			try
			{
				$Media = \Operation\Media::importFileToLibrary($file['tmp_name'], $file['name'], $preferredMediaType);

				$result['postAlbumMediaID'] = $this->addMediaToAlbum($Media, $this->postID);

				Message::addNotice(sprintf('"%s" OK', $file['name']));
			}
			catch(Exception $e)
			{
				Message::addAlert(sprintf('"%s" misslyckades: %s', $file['name'], $e->getMessage()));
			}
		}
	}
}

$AjaxIO = new AlbumMediaIO($action, array('postID', 'postAlbumMediaID'));