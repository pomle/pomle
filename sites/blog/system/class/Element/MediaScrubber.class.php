<?
namespace Element;

global $css, $js;
$css[] = '/css/MediaScrubber.css';
$js[] = '/js/MediaScrubber.js';

class MediaScrubber
{
	protected
		$items;

	public
		$index;


	public function __construct()
	{
		$this->index = 0;
		$this->items = array();
	}

	public function __toString()
	{
		ob_start();

		if( $Item = $this->getItem($this->index) )
		{
			$url = $Item->getURL();
			$caption = $Item->caption;
		}
		?>
		<div class="mediaScrubber" data-pageIndex="<? echo htmlspecialchars(json_encode($this->index)); ?>" data-mediaPool="<? echo htmlspecialchars($this->getItemsAsJSON()); ?>">
			<div class="image" style="background-image: url('<? echo $url; ?>');"></div>
			<div class="overlay">
				<div class="busy"></div>
				<div class="control">
					<div style="height: 0;"><!-- I need to be here because box object model is a piece of shitfest //-->&nbsp;</div>
					<?
					if( count($this->items) > 1 )
					{
						?>
						<div class="header">
							<span class="pageIndex"><? printf('%d / %d', $this->index + 1, count($this->items)); ?></span>
							<a href="#" class="slideshowToggle"></a>
						</div>
						<a href="#" class="prev" rel="-1"></a>
						<a href="#" class="next" rel="1"></a>
						<?
					}
					?>
					<div class="info">
						<div class="caption"><? echo htmlspecialchars($caption); ?></div>
						<a href="<? echo $url; ?>" class="mediaURL">Direktlänk</a>
					</div>
				</div>
			</div>
		</div>
		<?
		return ob_get_clean();
	}


	public function addItem(\Media $Media, $caption = null)
	{
		$this->items[] = new MediaScrubberItem($Media, $caption);
		return $this;
	}

	public function getItem($index)
	{
		return isset($this->items[$index]) ? $this->items[$index] : null;
	}

	public function getItemsAsJSON()
	{
		return json_encode($this->items);
	}
}
