<?
namespace Element;

class UserTrackItem
{
	public static function fromUserTrack(\Music\UserTrack $UserTrack)
	{
		$UTI = new self($UserTrack->userTrackID, $UserTrack->title, $UserTrack->artist);

		if( isset($UserTrack->Track->artists[0]) )
		{
			$Artist = $UserTrack->Track->artists[0];
			$UTI->artistID = $Artist->artistID;
		}

		if( isset($UserTrack->Track->Image) )
			$UTI->imageURL = getUserTrackItemImageURL($UserTrack->Track->Image->mediaHash);

		elseif( isset($Artist->Image) )
			$UTI->imageURL = getUserTrackItemImageURL($Artist->Image->mediaHash);

		return $UTI;
	}

	public function __construct($userTrackID, $title, $artist, $imageURL = null)
	{
		$this->userTrackID = $userTrackID;
		$this->title = $title;
		$this->artist = $artist;

		$this->imageURL = $imageURL;
	}
}