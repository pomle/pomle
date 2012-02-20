<?
require '../../Init.inc.php';

$messages = array();

try
{
	if( !isset($_FILES) || !is_array($_FILES) || !count($_FILES) )
		throw New \Exception(_("No files received"));


	foreach($_FILES as $file)
	{
		try
		{
			if( !\Media\Audio::canHandleFile($file['tmp_name']) )
				throw New Exception(_("Unsupported File =/"));

			$Audio = \Media\Audio::createFromFile($file['tmp_name']);

			$Audio = \Manager\Media::integrateIntoLibrary($Audio, $file['name']);

			$Track = \Event\Track::createFromAudio($Audio);

			$query = \DB::prepareQuery("SELECT ID FROM Music_UserTracks WHERE userID = %u AND trackID = %u", $User->userID, $Track->trackID);
			if( !$userTrackID = (int)\DB::queryAndFetchOne($query) )
			{
				$UserTrack = new \Music\UserTrack($User->userID);
				$UserTrack->setTrack($Track);

				$ID3 = new \ID3($Audio->getFilePath());

				$UserTrack->artist = $ID3->getArtist();
				$UserTrack->title = $ID3->getTitle();

				\Music\UserTrack::saveToDB($UserTrack);

				$userTrackID = $UserTrack->userTrackID;
			}

			$messages[] = \Element\Message::notice(sprintf(_("\"%s\" identified as %s"), $file['name'], $Track->getName()));
		}
		catch(\Exception $e)
		{
			$messages[] = \Element\Message::error(sprintf("%s: %s", $file['name'], $e->getMessage()));
		}
	}
}
catch(\Exception $e)
{
	$messages[] = \Element\Message::error($e->getMessage());
}

echo json_encode($messages);
