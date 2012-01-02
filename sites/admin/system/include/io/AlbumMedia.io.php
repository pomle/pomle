<?
class AlbumMediaIO extends AjaxIO
{
	public function delete()
	{
		$query = \DB::prepareQuery("DELETE FROM PostAlbumMedia WHERE ID = %u", $this->postAlbumMediaID);
		\DB::query($query);

		Message::addNotice(MESSAGE_ROW_DELETED);
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
			$query = \DB::prepareQuery("UPDATE PostAlbums SET previewMediaID = (SELECT mediaID FROM PostAlbumMedia WHERE ID = %u) WHERE postID = %u", $this->postAlbumMediaID, $this->postID);
			\DB::query($query);
		}

		Message::addNotice(MESSAGE_ROW_UPDATED);
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

				$query = \DB::prepareQuery("INSERT INTO
					PostAlbumMedia (
						ID,
						postID,
						mediaID,
						isVisible,
						sortOrder,
						comment,
						tags
					) SELECT
						NULLIF(%u, 0),
						%u,
						%u,
						%u,
						COUNT(*),
						null,
						null
					FROM
						PostAlbumMedia
					WHERE
						postID = %u",
					null,
					$this->postID,
					$Media->mediaID,
					true,
					$this->postID);

				$rowID = \DB::queryAndGetID($query);

				Message::addNotice(sprintf('"%s" OK', $file['name']));

				$result['mediaID'] = $Media->mediaID;
			}
			catch(Exception $e)
			{
				Message::addAlert(sprintf('"%s" misslyckades: %s', $file['name'], $e->getMessage()));
			}
		}
	}
}

$AjaxIO = new AlbumMediaIO($action, array('postID', 'postAlbumMediaID'));