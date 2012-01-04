<?
namespace Element;

global $js;
$js[] = '/js/BrickTiles.js';

class BrickTile
{
	public function __construct()
	{
		$this->items = array();
	}

	public function __toString()
	{
		ob_start();
		?>
		<div class="bricktiles posts clearfix">
			<?
			foreach($this->items as $item)
			{
				?>
				<a href="<? echo $item['href']; ?>" class="tile" data-mediapool="<? echo htmlspecialchars(json_encode($item['mediaHashPool'])); ?>">
					<div class="content transition fast">
						<div class="image">
							<div class="timestamp darkened medium"><? echo htmlspecialchars($item['timestamp']); ?></div>
							<h1 class="caption darkened medium"><? echo htmlspecialchars($item['caption']); ?></h1>
							<img src="<? echo $item['imageURL']; ?>" alt="">
						</div>
					</div>
				</a>
				<?
			}
			?>
		</div>
		<?
		return ob_get_clean();
	}


	public function addItem($href, $caption, $imageURL, $mediaHashPool = null, $timestamp = null)
	{
		if( !is_array($mediaHashPool) ) $mediaHashPool = array();

		$this->items[] = array(
			'href' => $href,
			'caption' => $caption,
			'timestamp' => $timestamp,
			'imageURL' => $imageURL,
			'mediaHashPool' => $mediaHashPool
		);

		return $this;
	}
}