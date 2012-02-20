<?
namespace Event;

class Artist
{
	public static function createFromName($name)
	{
		if( !$Artist = \Music\Artist::loadByName($name) )
		{
			$Artist = new \Music\Artist($name);

			if( $Image = Artwork::createFromArtist($Artist) )
				$Artist->setImage($Image);

			\Music\Artist::saveToDB($Artist);
		}

		return $Artist;
	}
}