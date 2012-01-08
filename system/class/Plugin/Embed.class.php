<?
namespace Plugin;

class Embed extends Plugin
{
	const TAG = 'embed';


	public function getHTML()
	{
		switch($this->type)
		{
			case 'vimeo':
				return $this->getVimeoHTML($this->id);
			break;

			case 'youtube':
				return $this->getYouTubeHTML($this->id);
			break;
		}
	}

	public function getPreviewImageURL()
	{
		$pageHTML = null;

		if( $this->type == 'youtube' )
		{
			$pageURL = sprintf('http://www.youtube.com/watch?v=%s', $this->id);
			$pageHTML = file_get_contents($pageURL);
		}

		if( $this->type == 'vimeo' )
		{
			$pageURL = sprintf('http://vimeo.com/%s', $this->id);
			$pageHTML = file_get_contents($pageURL);
		}

		if( preg_match('/<meta property="og:image" content="(.*)"/U', $pageHTML, $match) )
			return $match[1];

		return false;
	}

	public function getPreviewMedia()
	{
		if( $imageURL = $this->getPreviewImageURL() )
			if( $Media = \Operation\Media::downloadFileToLibrary($imageURL, MEDIA_TYPE_IMAGE) )
				return $Media;

		return false;
	}

	private function getVimeoHTML($id)
	{
		return sprintf('<iframe class="videoPlayer vimeo" src="http://player.vimeo.com/video/%s" frameborder="0"></iframe>', $id);
	}

	private function getYouTubeHTML($id)
	{
		return sprintf('<iframe class="videoPlayer vimeo" type="text/html" src="http://www.youtube.com/embed/%s" frameborder="0"></iframe>', $id);
	}
}