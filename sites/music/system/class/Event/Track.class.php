<?
namespace Event;

class Track
{
	public static function createFromAudio(\Media\Audio $Audio)
	{
		$ID3 = new \ID3($Audio->getFilePath());

		$tinfo['artist'] = $ID3->getArtist();
		$tinfo['title'] = $ID3->getTitle();
		$tinfo['album'] = $ID3->getAlbum();
		$tinfo['year'] = $ID3->getYear();
		$tinfo['position'] = $ID3->getTrackNumber();


		if( strlen($tinfo['title']) == 0 )
			throw New \Exception(_('Track title could not be extracted'));

		if( strlen($tinfo['artist']) == 0 )
			throw New \Exception(_('Artist name could not be extracted'));


		return self::createManual($Audio, $tinfo['artist'], $tinfo['title'], $tinfo['album'], $tinfo['year'], $tinfo['position']);
	}

	public static function createManual(\Media\Audio $Audio, $artist, $title, $album = null, $year = null, $trackNo = null)
	{
		$Artist = Artist::createFromName($artist);

		$Track = new \Music\Track($title);

		$Track->setAudio($Audio);
		$Track->addArtist($Artist);


		if( $Track_Existing = \Music\Track::loadByTrack($Track) )
		{
			$Track = $Track_Existing;
		}
		else
		{
			$Track->timeReleased = $year ? mktime(0, 0, 0, 1, 1, (int)$year) : null;
			\Music\Track::saveToDB($Track);
		}

		$Track->trackNo = (int)$trackNo;

		### If album is defined, add Track to album
		if( $album && ($Album = Album::createFromInfo($album, $year)) )
		{
			while(true)
			{
				foreach($Album->tracks as $AlbumTrack)
					if( $AlbumTrack->trackID == $Track->trackID ) break 2;

				$Album->addTrack($Track);

				break;
			}

			\Music\Album::saveToDB($Album);
		}

		return $Track;
	}
}