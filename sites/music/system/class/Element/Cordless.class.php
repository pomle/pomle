<?
namespace Element;

class Cordless
{
	public function __construct()
	{
		$this->playlist = array();
	}

	public function __toString()
	{
		ob_start();
		?>
		<section class="player cordless">

			<div class="time">
				<span class="current">0:00</span> /
				<span class="total">0:00</span>
			</div>

			<div class="controls">
				<a href="#" class="play_pause">Play/Pause</a>
				<a href="#" class="prev">Prev</a>
				<a href="#" class="next">Next</a>
			</div>

			<a href="#" class="scrubber">
				<div class="scrubArea">
					<div class="progress"></div>
				</div>
			</a>

			<div class="trackinfo">
				<div class="artist"></div>
				<div class="title"></div>
			</div>

		</section>
		<?
		return ob_get_clean();
	}

	public function addUserTrackItem(UserTrackItem $UserTrackItem)
	{
		$this->playlist[] = $UserTrackItem;
		return $this;
	}

	public function addUserTrackItems(Array $userTrackItems)
	{
		foreach($userTrackItems as $UserTrackItem)
			$this->addUserTrack($UserTrackItem);

		return $this;
	}

	public function getPlaylist()
	{
		ob_start();
		?>
		<div class="items">
			<?
			foreach($this->playlist as $UserTrackItem)
				echo $UserTrackItem;
			?>
		</div>
		<?
		return ob_get_clean();
	}
}