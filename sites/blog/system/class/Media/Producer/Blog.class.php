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

	public function getPagePreview()
	{
		return $this->getCustom(300, 300, true);
	}

	public function getRSSPreview()
	{
		return $this->getCustom(150, 150, true);
	}

	public function getTrackImage()
	{
		return $this->getCustom(300, 300, false);
	}
}