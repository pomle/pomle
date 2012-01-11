<?
namespace Media\Producer;

class MediaScrubber extends CrossSite
{
	public function getImage()
	{
		return $this->getCustom(1000, 750, false);
	}
}