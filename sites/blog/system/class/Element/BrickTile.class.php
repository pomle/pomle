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
		global $UserSettings;

		$this->layout = isset($UserSettings->brickTileLayout) ? $UserSettings->brickTileLayout : 'matrix';

		$this->items = array();

		$this->fallbackImageURLs = array(
			POST_TYPE_DIARY => array
			(
				'/img/BrickTile_Fallback_Diary_Blue.png',
				'/img/BrickTile_Fallback_Diary_Green.png',
				'/img/BrickTile_Fallback_Diary_Orange.png',
				'/img/BrickTile_Fallback_Diary_Purple.png',
				'/img/BrickTile_Fallback_Diary_Swamp.png',
			),
			POST_TYPE_TRACK => array('/img/BrickTile_Fallback_Track.png')
		);
	}

	public function __toString()
	{
		ob_start();
		?>
		<div class="brickTile <? echo $this->layout; ?>">
			<a href="/ajax/BrickTile.php" title="Växla vy" class="layoutSwitch"></a>
			<div class="items">
				<?
				foreach($this->items as $item)
					echo call_user_func_array(array($this, 'getTileHTML'), $item);
				?>
			</div>
		</div>
		<div class="brickTileCap"></div>
		<?
		return ob_get_clean();
	}

	public function addItem($imageURL, $href = null, $mainText = null, $smallText = null, $mediaHashs = null, $description = null, $class = null)
	{
		$this->items[] = func_get_args();
		return $this;
	}

	public function addPost(\Post $Post)
	{
		static $i;

		$mediaPool = array();

		if( in_array($Post::TYPE, array(POST_TYPE_DIARY, POST_TYPE_ALBUM)) )
		{
			foreach($Post->media as $Media)
				$mediaPool[] = $Media->mediaHash;
		}

		$description = $Post->getSummary();


		$previewMediaHash = (string)(isset($Post->PreviewMedia) ? $Post->PreviewMedia : reset($mediaPool));

		shuffle($mediaPool);
		$mediaPool = array_slice($mediaPool, 0, 10);

		if( $previewMediaHash )
			$imageURL = \Media\Producer\BrickTile::createFromHash($previewMediaHash)->getTile();
		elseif( isset($this->fallbackImageURLs[$Post::TYPE]) )
		{
			$imageURLs = $this->fallbackImageURLs[$Post::TYPE];
			$imageURL = $imageURLs[$i++ % count($imageURLs)];
		}
		else
			$imageURL = false;

		return $this->addItem($imageURL, $Post->getURL(), htmlspecialchars($Post->title), htmlspecialchars(\Format::date($Post->timePublished)), $mediaPool, $description, $Post::TYPE);
	}

	public function getTileHTML($imageURL, $href = null, $mainText = null, $smallText = null, $mediaHashs = null, $description = null, $class = null)
	{
		ob_start();
		?>
		<div class="item <? echo $class; ?>" <? if( is_array($mediaHashs) && count($mediaHashs) ) printf('data-mediapool="%s"', htmlspecialchars(json_encode($mediaHashs))); ?>>
			<div class="content">
				<a href="<? echo $href; ?>" class="image"><? if( $imageURL ) printf('<img src="%s" alt="">', $imageURL); ?></a>
				<a href="<? echo $href; ?>" class="mainText"><? echo $mainText; ?></a>
				<div class="smallText"><? echo $smallText; ?></div>
				<div class="badge"></div>
				<div class="description"><? if( $description ) echo $description; ?></div>
			</div>
		</div>
		<?
		return ob_get_clean();
	}
}