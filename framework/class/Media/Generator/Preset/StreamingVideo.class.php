<?
namespace Media\Generator\Preset;

class StreamingVideo extends _Preset
{
	const NAME = 'streamingVideo';

	public function __construct($mediaHash, $x, $y)
	{
		$this->mediaHash = $mediaHash;
		$this->x = abs($x);
		$this->y = abs($y);
		$this->subPath = sprintf('%ux%u/', $this->x, $this->y);
		$this->ext = '.mp4';
	}


	public function createFile()
	{
		if( !$Media = \Manager\Media::loadByHash($this->mediaHash) ) return false;

		if( !$Media instanceof \Media\Video ) return false;

		$Factory = new \Media\Generator\x264($Media, $this->x, $this->y);

		return $Factory->saveToFile(DIR_MEDIA . $this->getFilePath());
	}
}