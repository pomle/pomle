<?
class AlbumIO extends AjaxIO
{
	public function delete()
	{
		$query = \DB::prepareQuery("DELETE FROM Posts WHERE ID = %u", $this->postID);
		\DB::query($query);

		Message::addNotice(MESSAGE_ROW_DELETED);
	}

	public function purge()
	{
		$query = \DB::prepareQuery("DELETE FROM PostAlbumMedia WHERE postID = %u", $this->postID);
		\DB::query($query);

		Message::addCall('$("#antiloopAlbumMedia").trigger("reload");');
	}

	public function save()
	{
		$this->importArgs('isPublished', 'timePublished', 'title');

		$query = \DB::prepareQuery("UPDATE
				Posts
			SET
				isPublished = %u,
				timeModified = UNIX_TIMESTAMP(),
				timePublished = %u,
				title = %s
			WHERE
				ID = %u",
			$this->isPublished,
			strtotime($this->timePublished),
			$this->title,
			$this->postID);

		\DB::query($query);

		Message::addNotice(MESSAGE_ROW_UPDATED);
	}
}

$AjaxIO = new AlbumIO($action, array('postID'));