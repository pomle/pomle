<?
namespace Media\Producer;

class Blog extends CrossSite
{
	public function getAlbumImage()
	{
		return $this->getCustom(1000, 750, false);
	}

	public function getAlbumThumb()
	{
		return $this->getCustom(200, 200, true);
	}

	public function getRSSPreview()
	{
		return $this->getCustom(150, 150, true);
	}
}