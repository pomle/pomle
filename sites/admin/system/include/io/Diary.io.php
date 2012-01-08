<?
class DiaryIO extends AjaxIO
{
	public function delete()
	{
		$query = \DB::prepareQuery("DELETE FROM Posts WHERE ID = %u", $this->postID);
		\DB::query($query);

		Message::addNotice(MESSAGE_ROW_DELETED);
	}

	public function load()
	{
		global $result;
		$Post = \Diary::loadOneFromDB($this->postID);
		$Post->timePublished = \Format::timestamp($Post->timePublished, true);
		$result = $Post;
	}

	public function save()
	{
		$this->importArgs('isPublished', 'timePublished', 'title', 'uri', 'content', 'previewMediaID');

		$Post = \Diary::loadOneFromDB($this->postID);

		$Post->isPublished = (bool)$this->isPublished;
		$Post->timePublished = strtotime($this->timePublished);
		$Post->title = $this->title;
		$Post->uri = $this->uri;
		$Post->content = $this->content;

		if( $this->previewMediaID )
			if( $Media = \Manager\Media::loadOneFromDB($this->previewMediaID) )
				$Post->setPreviewMedia($Media);


		if( !$Post->PreviewMedia )
		{
			if( ($mediaIDs = $Post->getContentMediaIDs()) && ($Media = \Manager\Media::loadOneFromDB(reset($mediaIDs))) )
			{
				$Post->setPreviewMedia($Media);
			}
			else
			{
				### Break on first successful import
				foreach($Post->getPlugins() as $Plugin)
				{
					if( $Plugin::TAG == 'embed' )
					{
						if( $Media = $Plugin->getPreviewMedia() )
						{
							$Post->setPreviewMedia($Media);
							break;
						}
					}
				}
			}
		}

		\Diary::saveToDB($Post);

		Message::addNotice(MESSAGE_ROW_UPDATED);

		$this->load();
	}
}

$AjaxIO = new DiaryIO($action, array('postID'));