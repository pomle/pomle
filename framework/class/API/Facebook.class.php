<?
namespace API;

class FacebookException extends \Exception
{}

class Facebook
{
	public function __construct($accessToken = null)
	{
		$this->graphURL = 'https://graph.facebook.com/%s/%s?access_token=%s';
		$this->accessToken = $accessToken;
	}


	public function decodeURL($graphURL)
	{
		if( !$returnString = @file_get_contents($graphURL) )
			throw New FacebookException("Could not fetch contents for \"$graphURL\". Bad access token?");

		if( !$returnData = json_decode($returnString) )
			throw New FacebookException("Decoding failed: $graphURL");

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