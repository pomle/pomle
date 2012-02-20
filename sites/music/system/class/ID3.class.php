<?
require DIR_SITE_SYSTEM . 'libs/getid3/getid3.php';

class ID3
{
	public function __construct($filename)
	{
		$getID3 = new getID3();

		$this->id3 = $getID3->analyze($filename);
	}

	public function extractPreferred($key)
	{
		$keys = func_get_args();

		foreach($keys as $key)
		{
			if( isset($this->id3['tags']['id3v2'][$key]) )
				return reset($this->id3['tags']['id3v2'][$key]);

			elseif( isset($this->id3['tags']['id3v1'][$key]) )
				return reset($this->id3['tags']['id3v1'][$key]);
		}

		return false;
	}


	public function getAlbum()
	{
		return $this->extractPreferred('album');
	}

	public function getArtist()
	{
		return $this->extractPreferred('artist');
	}

	public function getTitle()
	{
		return $this->extractPreferred('title');
	}

	public function getTrackNumber()
	{
		return $this->extractPreferred('track_number', 'track');
	}

	public function getYear()
	{
		return $this->extractPreferred('year');
	}
}