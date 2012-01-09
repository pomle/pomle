<?
namespace API;
require '_Debug.inc.php';

class Facebook
{
	public function __construct($accessToken = null)
	{
		$this->graphURL = 'https://graph.facebook.com/%s/%s?access_token=%s';
		$this->accessToken = $accessToken;
	}


	public function decodeURL($graphURL)
	{
		echo "Fetching URL: $graphURL\n";

		$returnString = file_get_contents($graphURL);

		$returnData = json_decode($returnString);

		if( !$returnData )
			throw New Exception("Decoding failed: $graphURL");

		return $returnData;
	}

	public function fetchObject($facebookID, $type = null)
	{
		$url = sprintf($this->graphURL, $facebookID, $type, $this->accessToken);
		return $this->decodeURL($url);
	}

	public function getInfo($facebookID)
	{
		return $this->fetchObject($facebookID, null);
	}

	public function getPhotos($facebookID, $length = 25, $offset = 0, $afterID = null)
	{
		$url = sprintf($this->graphURL, $facebookID, 'photos', $this->accessToken) . sprintf('&limit=%u&offset=%u&__after_id=%s', $length, $offset, $afterID);
		return $this->decodeURL($url);
	}
}

$albumID = 10150223316092304;

$FB = new Facebook('AAACEdEose0cBAOgfZB6kE15VZBpHQMkZBksuF5tjJWZBZB5PoMZBxNqJytJZAlQvmr6TLEfUTUjJo346jc7XN32LMj9e5JZCS9KVZCt9BqZA9ZCEQZDZD');

$Album = new \Album();

if( $FBAlbum = $FB->getInfo($albumID) )
{
	print_r($FBAlbum);

	$Album->timePublished = strtotime($FBAlbum->created_time);
	$Album->timeCreated = time();
	$Album->title = $FBAlbum->name;
	$Album->description = $FBAlbum->description;

	$pageLen = 25;
	$page = 0;
	$afterID = null;
	$coverPhotoFacebookID = $FBAlbum->cover_photo;

	while($FBPhotos = $FB->getPhotos($FBAlbum->id, $pageLen, $pageLen * $page++, $afterID))
	{
		if( count($FBPhotos->data) == 0 ) break;

		foreach($FBPhotos->data as $FBPhoto)
		{
			$photoURL = 'http://a1.sphotos.ak.fbcdn.net/hphotos-ak-ash4/' . basename($FBPhoto->source);

			if( $Media = \Operation\Media::downloadFileToLibrary($photoURL, MEDIA_TYPE_IMAGE) )
			{
				$Media->isVisible = true;
				$Media->sortOrder = $FBPhoto->position;
				$Media->comment = $FBPhoto->name;

				$Album->addMedia($Media);

				if( $coverPhotoFacebookID == $FBPhoto->id )
					$Album->setPreviewMedia($Media);
			}

			$afterID = $FBPhoto->id;
		}
	}
}

\Album::saveToDB($Album);

print_r($Album);

