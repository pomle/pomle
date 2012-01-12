<?
require_once DIR_AJAX_IO . 'common/Post.io.php';

class TrackIO extends PostIO
{
	public function importLastFM()
	{
		$this->importArgs('username');

		try
		{
			$count = 250;

			$url = sprintf('http://ws.audioscrobbler.com/2.0/?method=user.getlovedtracks&user=%s&api_key=%s&limit=%u', urlencode($this->username), urlencode(LAST_FM_API_KEY), $count);
			#echo $url;

			$loveXML = file_get_contents($url);

			$LoveXML = new SimpleXMLElement($loveXML);

			$importCount = 0;

			foreach($LoveXML->xpath('/lfm/lovedtracks/track') as $track)
			{
				$lovedUTS = (int)$track->date['uts'];
				$lastFmID = $lovedUTS;

				$query = \DB::prepareQuery("SELECT postID FROM PostTracks WHERE lastFmID = %u", $lastFmID);
				$postID = \DB::queryAndFetchOne($query);

				if( $postID )
					break;

				$Post = new \Post\Track();

				$Post->isPublished = true;

				$Post->timePublished = $lovedUTS;

				$Post->artist = (string)$track->artist->name;
				$Post->track = (string)$track->name;

				$Post->artistURL = (string)$track->artist->url;
				$Post->trackURL = (string)$track->url;

				$Post->title = sprintf('%s - %s', $Post->artist, $Post->track);

				$Post->lastFmID = $lastFmID;

				$imageURL = null;
				foreach($track->xpath('image[@size="extralarge"]') as $image)
				{
					$imageURL = (string)$image;
					if( $Media = \Operation\Media::downloadFileToLibrary($imageURL, MEDIA_TYPE_IMAGE) )
						$Post->setPreviewMedia($Media);
				}

				if( !$imageURL ) ### If no default ImageURL was supplied, fetch by artist name
				{
					$url = sprintf('http://ws.audioscrobbler.com/2.0/?method=artist.getimages&artist=%s&api_key=%s', urlencode($Post->artist), urlencode(LAST_FM_API_KEY));

					#echo $url, "\n";

					$imageXML = file_get_contents($url);
					$ImageXML = new SimpleXMLElement($imageXML);

					if( $images = $ImageXML->xpath('/lfm/images/image/sizes/size[@name="original"]') )
					{
						foreach($images as $imageURL)
						{
							$imageURL = (string)$imageURL;
							if( $Media = \Operation\Media::downloadFileToLibrary($imageURL, MEDIA_TYPE_IMAGE) )
								$Post->setPreviewMedia($Media);

							break;
						}
					}
				}

				\Post\Track::saveToDB($Post);

				$importCount++;
			}

			#throw New Exception((string)$importCount);
		}
		catch(\FileException $e)
		{
			throw New Exception("Could not read \"%s\" Last FM Service probably stopped responding\n", $imageURL);
		}
		catch(\Exception $e)
		{
			throw New Exception("Error: " . $e->getMessage());
		}

		Message::addNotice(sprintf('%d new tracks imported', $importCount));
	}

	final public function loadPost($postID)
	{
		return \Post\Track::loadOneFromDB($postID);
	}

	final public function savePost(\Post\Track $Post)
	{
		$this->importArgs('artist', 'track', 'artistURL', 'trackURL', 'spotifyURI');

		$Post->artist = $this->artist;
		$Post->track = $this->track;
		$Post->artistURL = $this->artistURL;
		$Post->trackURL = $this->trackURL;
		$Post->spotifyURI = $this->spotifyURI;

		\Post\Track::saveToDB($Post);
	}
}

$AjaxIO = new TrackIO($action, array('postID'));