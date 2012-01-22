<?
namespace Element;

class MediaScrubberItem
{
	protected
		$Media;

	public
		$mediaHash,
		$caption,
		$url;

	public function __construct(\Media $Media, $caption = null)
	{
		$this->Media = $Media;
		$this->url = null;

		if( isset($Media->postAlbumMediaID) )
			$this->postAlbumMediaID = $Media->postAlbumMediaID;
		elseif( isset($Media->mediaID) )
			$this->mediaID = $Media->mediaID;

		$this->mediaHash = $Media->mediaHash;

		$this->caption = $caption ?: $Media->comment;
	}

	public function getURL()
	{
		if( !isset($this->url) )
			$this->url = \Media\Producer\MediaScrubber::createFromMedia($this->Media)->getImage();

		return $this->url;
	}
}