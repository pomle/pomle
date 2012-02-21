<?
namespace Element;

interface iUserTrackList
{
	public function __toString();
}

abstract class UserTrackList
{
	public static function createFromUserTracks(Array $userTracks)
	{
		$fallbackImageURLs = array('/img/UserTrackList_Blue.png', '/img/UserTrackList_Purple.png', '/img/UserTrackList_Swamp.png');
		$fallbackCount = count($fallbackImageURLs);

		$I = new static();
		$i = 0;
		foreach($userTracks as $UserTrack)
		{
			$UserTrackItem = UserTrackItem::fromUserTrack($UserTrack);

			if( !isset($UserTrackItem->imageURL) )
				$UserTrackItem->imageURL = $fallbackImageURLs[$i++ % $fallbackCount];

			$I->addUserTrackItem($UserTrackItem);
		}
		return $I;
	}

	public static function createFromUserTrackItems(Array $userTrackItems)
	{
		$I = new static();
		$I->addUserTrackItems($userTrackItems);
		return $I;
	}

	public function __construct()
	{
		$this->userTrackItems = array();
	}


	public function addUserTrack(\Music\UserTrack $UserTrack)
	{
		$UserTrackItem = UserTrackItem::fromUserTrack($UserTrack);
		$this->addUserTrackItem($UserTrackItem);
		return $this;
	}

	public function addUserTracks(Array $userTracks)
	{
		foreach($userTracks as $UserTrack)
			$this->addUserTrack($UserTrack);

		return $this;
	}

	public function addUserTrackItem(UserTrackItem $UserTrackItem)
	{
		$this->userTrackItems[] = $UserTrackItem;
		return $this;
	}

	public function addUserTrackItems(Array $userTrackItems)
	{
		foreach($userTrackItems as $UserTrackItem)
			$this->addUserTrackItem($UserTrackItem);

		return $this;
	}

	public function getItemsHTML($userTrackItems)
	{
		ob_start();
		?>
		<div class="userTrackItems items">
			<?
			foreach($userTrackItems as $UserTrackItem)
				echo $this->getItemHTML($UserTrackItem);
			?>
		</div>
		<?
		return ob_get_clean();
	}

	public function getItemHTML(UserTrackItem $UserTrackItem)
	{
		/*
		data-artist="<? echo htmlspecialchars($this->artist); ?>"
		data-title="<? echo htmlspecialchars($this->title); ?>"
		*/

		ob_start();
		?>
		<div class="item userTrack"
			data-usertrackid="<? echo $UserTrackItem->userTrackID; ?>"
			data-artist="<? echo htmlspecialchars($UserTrackItem->artist); ?>"
			data-title="<? echo htmlspecialchars($UserTrackItem->title); ?>"
			>

			<div class="image playStart">
				<img src="<? echo $UserTrackItem->imageURL; ?>">
			</div>

			<ul class="meta">
				<li>
					<a href="#" class="title playStart"><? echo htmlspecialchars($UserTrackItem->title); ?></a>
				</li>
				<li>
					<a href="#" class="artist" href="/Artist.php?artistID=<? echo $UserTrackItem->artistID; ?>"><? echo htmlspecialchars($UserTrackItem->artist); ?></a>
				</li>
			</ul>

		</div>
		<?
		return ob_get_clean();
	}
}