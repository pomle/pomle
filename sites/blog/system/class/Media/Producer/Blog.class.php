<?
namespace Media\Producer;

class Blog extends CrossSite
{
	public function getAlbumImage()
	{
		return $this->getCustom(1000, 750, false);
	}
}