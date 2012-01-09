<?
class TrackIO extends AjaxIO
{
	public function delete()
	{
		$query = \DB::prepareQuery("DELETE FROM Posts WHERE ID = %u", $this->postID);
		\DB::query($query);

		Message::addNotice(MESSAGE_ROW_DELETED);
	}

	public function import()
	{
		/*try
		{
			$count = 250;

			$url = sprintf('http://ws.audioscrobbler.com/2.0/?method=user.getlovedtracks&user=%s&api_key=%s&limit=%u', urlencode('pomle'), urlencode(LAST_FM_API_KEY), $count);
			echo $url;

			$loveXML = file_get_contents($url);

			$LoveXML = new SimpleXMLElement($loveXML);

			foreach($LoveXML->xpath('/lfm/lovedtracks/track') as $track)
			{
				$lovedUTS = (int)$track->date['uts'];
				$lastFmID = $lovedUTS;

				$query = \DB::prepareQuery("SELECT postID FROM PostTracks WHERE lastFmID = %u", $lastFmID);
				$postID = \DB::queryAndFetchOne($query);

				$Post = $postID ? \Track::loadOneFromDB($postID) : \Track::addToDB();

				if( !isset($Post->isPublished) ) $Post->isPublished = true;

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
						foreach( as $imageURL)
						{
							$imageURL = (string)$imageURL;
							if( $Media = \Operation\Media::downloadFileToLibrary($imageURL, MEDIA_TYPE_IMAGE) )
								$Post->setPreviewMedia($Media);

							break;
						}
					}
				}

				\Track::saveToDB($Post);
			}
		}
		catch(\FileException $e)
		{
			printf("Could not read \"%s\" Last FM Service probably stopped responding\n", $imageURL);
		}
		catch(\Exception $e)
		{
			print_r($e);
			die("Error: " . $e->getMessage());
		}*/
	}

	public function load()
	{
		global $result;
		$Post = \Track::loadOneFromDB($this->postID);
		$Post->timePublished = \Format::timestamp($Post->timePublished, true);
		$result = $Post;
	}

	public function save()
	{
		$this->importArgs('isPublished', 'timePublished', 'title', 'uri',  'previewMediaID', 'artist', 'track', 'artistURL', 'trackURL', 'spotifyURI');

		$Post = \Track::loadOneFromDB($this->postID);

		$Post->isPublished = (bool)$this->isPublished;
		$Post->timePublished = strtotime($this->timePublished);
		$Post->title = $this->title;
		$Post->uri = $this->uri;

		$Post->artist = $this->artist;
		$Post->track = $this->track;
		$Post->artistURL = $this->artistURL;
		$Post->trackURL = $this->trackURL;
		$Post->spotifyURI = $this->spotifyURI;

		if( $this->previewMediaID )
			if( $Media = \Manager\Media::loadOneFromDB($this->previewMediaID) )
				$Post->setPreviewMedia($Media);


		/*if( !$Post->PreviewMedia )
		{
			if( ($mediaIDs = $Post->getContentMediaIDs()) && ($Media = \Manager\Media::loadOneFromDB(reset($mediaIDs))) )
			{
				$Post->setPreviewMedia($Media);
			}
			else
			{
				### Break on first successful import
				foreach($Post->getPlugins() as $Plugin)
				{
					if( $Plugin::TAG == 'embed' )
					{
						if( $Media = $Plugin->getPreviewMedia() )
						{
							$Post->setPreviewMedia($Media);
							break;
						}
					}
				}
			}
		}*/

		\Track::saveToDB($Post);

		Message::addNotice(MESSAGE_ROW_UPDATED);

		$this->load();
	}
}

$AjaxIO = new TrackIO($action, array('postID'));