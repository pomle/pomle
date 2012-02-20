<?
namespace Element;

class Playlist extends UserTrackList
{
	public function __toString()
	{
		ob_start();
		?>
		<div class="userTrackList playlist">

			<div class="control">

			</div>

			<div class="items">
				<?
				foreach($this->userTrackItems as $UserTrackItem)
					echo $this->getItemHTML($UserTrackItem);
				?>
			</div>

		</div>
		<?
		return ob_get_clean();
	}
}