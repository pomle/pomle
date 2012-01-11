<?
require_once DIR_AJAX_IO . 'common/Post.io.php';

class AlbumIO extends PostIO
{
	final public function loadPost($postID)
	{
		return \Post\Album::loadOneFromDB($postID);
	}

	final public function importFacebook()
	{
		$this->importArgs('accessToken', 'facebookID');

		if( !$this->facebookID )
			throw New Exception("Facebook ID missing");

		if( !$this->accessToken )
			throw New Exception("Access Token missing");

		$FB = new \API\Facebook($this->accessToken);

		if( !$FBAlbum = $FB->getInfo($this->facebookID) )
			throw New Exception('Could not get info on ID');

		$Album = new \Post\Album();
		$Album->timePublished = strtotime($FBAlbum->created_time);
		$Album->timeCreated = time();
		$Album->title = $FBAlbum->name;
		$Album->description = $FBAlbum->description;

		$pageLen = 25;
		$page = 0;
		$afterID = null;
		$coverPhotoFacebookID = $FBAlbum->cover_photo;

		$hasPhotos = false;

		while($FBPhotos = $FB->getPhotos($FBAlbum->id, $pageLen, $pageLen * $page++, $afterID))
		{
			if( count($FBPhotos->data) == 0 ) break;

			foreach($FBPhotos->data as $FBPhoto)
			{
				$photoURL = $FBPhoto->source;

				$photoURL = 'http://a1.sphotos.ak.fbcdn.net/hphotos-ak-ash4/' . basename($photoURL);

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

		if( count($Album->media) == 0 )
			throw New Exception("No photos fetched");

		\Post\Album::saveToDB($Album);

		Message::addNotice(sprintf('Album "%s" Imported as ID: %s', htmlspecialchars($Album->title), sprintf('<a href="/AlbumEdit.php?postID=%1$u">%1$u</a>', $Album->postID)));
	}

	final public function purge()
	{
		$query = \DB::prepareQuery("DELETE FROM PostAlbumMedia WHERE postID = %u", $this->postID);
		\DB::query($query);

		Message::addCall('$("#antiloopAlbumMedia").trigger("reload");');
	}

	final public function savePost(\Post $Post)
	{
		$this->importArgs('description');

		$Post->description = $this->description;

		$Post->skipMedia = true;

		\Post\Album::saveToDB($Post);
	}
}

$AjaxIO = new AlbumIO($action, array('postID'));