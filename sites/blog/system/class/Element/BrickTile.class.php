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
		$this->layout = 'matrix';

		$this->items = array();

		$this->fallbackImageURLs = array(
			POST_TYPE_DIARY => '/img/BrickTile_Fallback_Diary.jpg',
			POST_TYPE_TRACK => '/img/BrickTile_Fallback_Track.png'
		);
	}

	public function __toString()
	{
		ob_start();
		?>
		<div class="brickTile <? echo $this->layout; ?>">
			<?
			foreach($this->items as $item)
			{
				list($imageURL, $href, $mainText, $smallText, $mediaHashs, $class) = $item;
				echo $this->getTileHTML($imageURL, $href, $mainText, $smallText, $mediaHashs, $class);
			}
			?>
		</div>
		<?
		return ob_get_clean();
	}

	public function addItem($imageURL, $href = null, $mainText = null, $smallText = null, $mediaHashs = null, $class = null)
	{
		$this->items[] = array($imageURL, $href, $mainText, $smallText, $mediaHashs, $class);
		return $this;
	}

	public function addPost(\Post $Post)
	{
		$mediaPool = array();

		if( in_array($Post::TYPE, array(POST_TYPE_DIARY, POST_TYPE_ALBUM)) )
		{
			foreach($Post->media as $Media)
				$mediaPool[] = $Media->mediaHash;
		}

		$previewMediaHash = (string)(isset($Post->PreviewMedia) ? $Post->PreviewMedia : reset($mediaPool));

		shuffle($mediaPool);
		$mediaPool = array_slice($mediaPool, 0, 10);

		if( $previewMediaHash )
			$imageURL = \Media\Producer\BrickTile::createFromHash($previewMediaHash)->getTile();
		elseif( isset($this->fallbackImageURLs[$Post::TYPE]) )
			$imageURL = $this->fallbackImageURLs[$Post::TYPE];
		else
			$imageURL = false;

		return $this->addItem($imageURL, $Post->getURL(), $Post->title, \Format::date($Post->timePublished), $mediaPool, $Post::TYPE);
	}

	public function getTileHTML($imageURL, $href = null, $mainText = null, $smallText = null, $mediaHashs = null, $class = null)
	{
		ob_start();
		?>
		<div class="item <? echo $class; ?>" <? if( is_array($mediaHashs) && count($mediaHashs) ) printf('data-mediapool="%s"', htmlspecialchars(json_encode($mediaHashs))); ?>>
			<div class="content">
				<a href="<? echo $href; ?>" class="image"><? if( $imageURL ) printf('<img src="%s" alt="">', $imageURL); ?></a>
				<a href="<? echo $href; ?>" class="mainText"><? echo htmlspecialchars($mainText); ?></a>
				<div class="smallText"><? echo htmlspecialchars($smallText); ?></div>
				<div class="badge"></div>
			</div>
		</div>
		<?
		return ob_get_clean();
	}
}