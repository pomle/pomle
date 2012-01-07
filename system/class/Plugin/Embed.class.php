<?
namespace Plugin;

class Embed
{
	const YOUTUBE_WIDTH = 700;
	const YOUTUBE_HEIGHT = 400;
	const YOUTUBE_COLOR1 = '';
	const YOUTUBE_COLOR2 = '';
	const YOUTUBE_BORDER = 0;


	public function __construct()
	{}


	public function getHTML($attr)
	{
		switch($attr['type'])
		{
			case 'youtube':
				return $this->getYouTubeHTML($attr['id'], $attr['text']);
			break;
		}
	}

	private function getYouTubeHTML($id, $text = '', $href = '')
	{
		$w = static::YOUTUBE_WIDTH;
		$h = static::YOUTUBE_HEIGHT;

		return sprintf('<iframe class="youtube-player" type="text/html" src="http://www.youtube.com/embed/%s" frameborder="0"></iframe>', $id, $w, $h);
	}
}