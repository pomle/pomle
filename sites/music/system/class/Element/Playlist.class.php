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
				<a href="#" class="clear"><img src="/img/PlayQueue_Icon_Clear.png" title="<? echo _('Clear'); ?>"></a>
				<a href="#" class="shuffle"><img src="/img/PlayQueue_Icon_Shuffle.png" title="<? echo _('Shuffle'); ?>"></a>
			</div>

			<?
			echo $this->getItemsHTML($this->userTrackItems);
			?>

		</div>
		<?
		return ob_get_clean();
	}
}