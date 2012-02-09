<?
namespace Element;

global $css;
$css[] = '/css/Element/MediaItem.css';


class MediaItem extends Common\Root
{
	public static function check(\Media $Media, $id, $isChecked, $prefix = 'isEnabled')
	{
		$M = new self($Media);
		$M->addContent(Input::checkbox($prefix . '[]', $isChecked, $id));
		return $M;
	}

	public function __construct(\Media $Media)
	{
		$this->Media = $Media;
		$this->addContent(Input::hidden('mediaID[]', $Media->mediaID));
	}

	public function __toString()
	{
		ob_start();
		?>
		<div class="mediaItem" data-mediaid="<? echo $this->Media->mediaID; ?>" data-mediahash="<? echo $this->Media->mediaHash; ?>" style="background-image: url('<? echo getMediaURL($this->Media->mediaHash, 100, 100, true); ?>');">
			<?
			foreach($this->content as $Element)
				echo $Element;
			?>
		</div>
		<?
		return ob_get_clean();
	}


	public function addContent(Common\Root $Element)
	{
		$this->content[] = $Element;
		return $this;
	}
}