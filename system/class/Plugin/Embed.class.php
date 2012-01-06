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
		switch($attr['type']) {
			case 'youtube':
				return $this->getYouTubeHTML($attr['id'], $attr['text']);
				break;
		}
	}

	private function getYouTubeHTML($id, $text = '', $href = '')
	{
		$url = sprintf('http://www.youtube.com/v/%s&=en_US&fs=1', $id, static::YOUTUBE_COLOR1, static::YOUTUBE_COLOR2, static::YOUTUBE_BORDER);

		if( static::YOUTUBE_COLOR1 || static::YOUTUBE_COLOR2 ) $url.= sprintf('&color1=0x%s&color2=0x%s', static::YOUTUBE_COLOR1, static::YOUTUBE_COLOR2);
		if( static::YOUTUBE_BORDER ) $url.= sprintf('&border=%u', static::YOUTUBE_BORDER);

		$w = static::YOUTUBE_WIDTH;
		$h = static::YOUTUBE_HEIGHT;

		ob_start();
		?>
		<div class="embed youtube">
			<object width="<? echo $w; ?>" height="<? echo $h; ?>">
				<param name="movie" value="<? echo $url; ?>"></param>
				<param name="allowFullScreen" value="true"></param>
				<param name="allowscriptaccess" value="always"></param>
				<embed src="<? echo $url; ?>" type="application/x-shockwave-flash" allowscriptaccess="always" allowfullscreen="true" width="<? echo $w; ?>" height="<? echo $h; ?>"></embed>
			</object>
		</div>
		<?

		return ob_get_clean();
	}
}