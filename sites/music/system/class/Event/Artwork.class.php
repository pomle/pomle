<?
namespace Event;

class Artwork
{
	public static function createFromArtist(\Music\Artist $Artist)
	{
		$LastFM = new \API\LastFM(LAST_FM_API_KEY);

		$Image = $LastFM->getArtistImage($Artist->name);

		return $Image;
	}
}