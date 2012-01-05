<?
namespace Element;

global $css, $js;
$css[] = '/css/BrickTile.css';
$js[] = '/js/BrickTile.js';

class BrickTile
{
	public static function createFromPosts(Array $posts)
	{
		$B = new self();

		foreach($posts as $Post)
			$B->addPost($Post);

		return $B;
	}

	public function __construct()
	{
		$this->items = array();
	}

	public function __toString()
	{
		ob_start();
		?>
		<div class="brickTile">
			<?
			foreach($this->items as $item)
				echo $this->getTileHTML($item);
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

	public function addPost(\Post $Post)
	{
		$mediaPool = array();

		if( $Post::TYPE == POST_TYPE_ALBUM )
		{
			foreach($Post->media as $Media)
				$mediaPool[] = $Media->mediaHash;
		}

		$previewMediaHash = (string)(isset($Post->PreviewMedia) ? $Post->PreviewMedia : reset($mediaPool));

		shuffle($mediaPool);

		$mediaPool = array_slice($mediaPool, 0, 10);

		$mediaURL = \Media\Producer\BrickTile::createFromHash($previewMediaHash)->getTile();

		return $this->addItem(\URL::album($Post), $Post->title, $mediaURL, $mediaPool, \Format::date($Post->timePublished));
	}

	public function getTileHTML($item)
	{
		ob_start();
		?>
		<a href="<? echo $item['href']; ?>" class="tile transition fast" data-mediapool="<? echo htmlspecialchars(json_encode($item['mediaHashPool'])); ?>">
			<div class="content transition fast">
				<div class="timestamp darkened medium"><? echo htmlspecialchars($item['timestamp']); ?></div>
				<h1 class="caption darkened medium"><? echo htmlspecialchars($item['caption']); ?></h1>
				<img src="<? echo $item['imageURL']; ?>" alt="">
			</div>
		</a>
		<?
		return ob_get_clean();
	}
}