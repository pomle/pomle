<?
interface IPostIO
{
	public function loadPost($postID);
	public function savePost(\Post $Post);
}

abstract class PostIO extends AjaxIO
{
	final public function delete()
	{
		$this->importArgs('postID');

		$query = \DB::prepareQuery("DELETE FROM Posts WHERE ID = %u", $this->postID);
		if( !\DB::queryAndCountAffected($query) )
			throw New \Exception(MESSAGE_ROW_MISSING);

		Message::addNotice(MESSAGE_ROW_DELETED);
	}

	final public function load()
	{
		global $result;
		$this->importArgs('postID');

		if( !$Post = $this->loadPost($this->postID) )
			throw New \Exception(MESSAGE_ROW_MISSING);

		$Post->timePublished = \Format::timestamp($Post->timePublished, true);

		$result = $Post;
	}

	final public function save()
	{
		$this->importArgs('postID', 'previewMediaID', 'isPublished', 'timePublished', 'title', 'uri');

		if( !$Post = $this->loadPost($this->postID) )
			throw New \Exception(MESSAGE_ROW_MISSING);

		$Post->isPublished = (bool)$this->isPublished;
		$Post->timePublished = strtotime($this->timePublished) ?: time();
		$Post->title = $this->title;
		$Post->uri = $this->uri ?: null;

		if( $this->previewMediaID )
			if( $Media = \Manager\Media::loadOneFromDB($this->previewMediaID) )
				$Post->setPreviewMedia($Media);
		else
			$Post->setPreviewMedia(null);


		$this->savePost($Post);

		Message::addNotice(MESSAGE_ROW_UPDATED);

		$this->load();
	}
}