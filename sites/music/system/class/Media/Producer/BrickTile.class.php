<?
namespace Media\Producer;

class BrickTile extends CrossSite
{
	public function getTile()
	{
		return $this->getCustom(300, 200, true);
	}

	public function getAlbumImage()
	{
		return $this->getCustom(199, 199, true);
	}
}