<?
require_once DIR_AJAX_IO . 'common/Post.io.php';

class DiaryIO extends PostIO
{
	final public function loadPost($postID)
	{
		return \Post\Diary::loadOneFromDB($postID);
	}

	final public function savePost(\Post $Post)
	{
		$this->importArgs('content');

		$Post->content = $this->content;

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

		\Post\Diary::saveToDB($Post);
	}
}

$AjaxIO = new DiaryIO($action, array('postID'));