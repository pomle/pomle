<?
namespace Plugin;

class External
{
	public function __construct()
	{}


	public function getHTML($attr)
	{
		switch($attr['type'])
		{
			case 'image':
				return $this->getImageHTML($attr['src'], $attr['text'], $attr['href']);
			break;
		}
	}

	private function getImageHTML($imageURL, $text = '', $href = '')
	{
		$textHTML = htmlspecialchars($text);

		$elementHTML = '<div class="external image">';

		$imageHTML .= sprintf('<img src="%1$s" alt="%2$s" title="%2$s" />', $imageURL, $textHTML);

		if( strlen($href) > 0 )
			$imageHTML = sprintf('<a href="%s">%s</a>', $href, $imageHTML);

		$elementHTML .= $imageHTML;

		if( strlen($textHTML) > 0 ) $elementHTML .= '<div class="text">' . $textHTML . '</div>';

		$elementHTML .= '</div>';

		return $elementHTML;
	}
}