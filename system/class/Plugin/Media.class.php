<?
namespace Plugin;

class Media extends Plugin
{
	const TAG = 'media';

	protected static $imageLinkAttr = array();
	protected static $imageZoomAttr = array();

	public function getHTML()
	{
		try
		{
			switch($this->type)
			{
				case 'image':
					return $this->getImageHTML($this->hash, $this->text, $this->href);
				break;

				case 'video':
					return $this->getVideoHTML($this->hash, $this->text, $this->href, $this->preview);
				break;
			}
		}
		catch(\Exception $e)
		{
			if( isDebug() ) return '[Plugin Media Parse Failed: ' . $e->getMessage() . ']';
		}

		return '';
	}

	public function getMedia()
	{
		return \Manager\Media::loadOneFromDB($this->mediaID);
	}


	protected function getImageHTML($hash, $text = '', $href = '', $isZoomable = true)
	{
		$inlineWidth 	= defined('STORY_PLUGIN_IMAGE_INLINE_WIDHT') 	? constant('STORY_PLUGIN_IMAGE_INLINE_WIDHT') 	: 600;
		$inlineHeight 	= defined('STORY_PLUGIN_IMAGE_INLINE_HEIGHT') 	? constant('STORY_PLUGIN_IMAGE_INLINE_HEIGHT') 	: 400;

		$zoomedWidth 	= defined('STORY_PLUGIN_IMAGE_ZOOMED_WIDHT') 	? constant('STORY_PLUGIN_IMAGE_ZOOMED_WIDHT') 	: 1024;
		$zoomedHeight 	= defined('STORY_PLUGIN_IMAGE_ZOOMED_HEIGHT') 	? constant('STORY_PLUGIN_IMAGE_ZOOMED_HEIGHT') 	: 1024;


		$Preset = new \Media\Generator\Preset\AspectThumb($hash, $inlineWidth, $inlineHeight);
		$imageInlineURL = $Preset->getURL();

		$textHTML = htmlspecialchars($text);

		$elementHTML = '<div class="media image">';

		$imageHTML .= sprintf('<img src="%1$s" alt="%2$s" title="%2$s" />', $imageInlineURL, $textHTML);

		if( strlen($href) > 0 )
		{
			$elementHTML.= '<a href="' . htmlspecialchars($href). '" ' . join(' ', static::$imageLinkAttr) . '>' . $imageHTML . '</a>';
		}
		elseif( $isZoomable )
		{
			$imageZoomedURL = sprintf('/AlbumImage.php?mediaHash=%s', $hash);
			$elementHTML.= '<a href="' . $imageZoomedURL . '" ' . join(' ', static::$imageZoomAttr) . '>' . $imageHTML . '</a>';
		}
		else
		{
			$elementHTML.= $imageHTML;
		}

		if( strlen($textHTML) > 0 ) $elementHTML .= '<div class="text">' . $textHTML . '</div>';

		$elementHTML .= '</div>';

		return $elementHTML;
	}

	protected function getVideoHTML($hash, $text = '', $href = '', $preview = 50)
	{
		#$imageURL = GraphicsHandler::createFromHash($hash)->setGeneratorArgs($preview)->getInlineImage();

		$textHTML = htmlspecialchars($text);

		$elementHTML = '<div class="media image">';

		$imageHTML .= sprintf('<img src="%1$s" alt="%2$s" title="%2$s" />', $imageURL, $textHTML);

		if( strlen($href) > 0 )
		{
			$imageHTML = sprintf('<a href="%s">%s</a>', $href, $imageHTML);
		}

		$elementHTML .= $imageHTML;

		if( strlen($textHTML) > 0 ) $elementHTML .= '<div class="text">' . $textHTML . '</div>';

		$elementHTML .= '</div>';

		return $elementHTML;
	}
}
