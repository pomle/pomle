<?
namespace Media\Generator\Preset;

class Copy extends _Preset
{
	const NAME = 'copy';

	public function __construct($mediaHash, $ext)
	{
		$this->mediaHash = $mediaHash;
		$this->ext = '.' . $ext;
	}

	public function createFile()
	{
		if( !$Media = \Manager\Media::loadByHash($this->mediaHash) ) return false;

		$sourceFile = $Media->getFilePath();
		$destinationFile = DIR_MEDIA . $this->getFilePath();

		return copy($sourceFile, $destinationFile);
	}
}